<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Controllers/PdfRenderer.php';
require_once ROOT . '/Controllers/BadwordsFilter.php';
require_once ROOT . '/Controllers/Translator.php';
require_once ROOT . '/Controllers/Mailer.php';
require_once ROOT . '/Models/ContratModel.php';
require_once ROOT . '/Models/RuleModel.php';
require_once ROOT . '/Models/HistoryModel.php';
require_once ROOT . '/Models/UserModel.php';

class ContratController extends Controller {

    private ContratModel $contratModel;
    private RuleModel    $ruleModel;
    private HistoryModel $history;
    private UserModel    $userModel;

    public function __construct() {
        $this->contratModel = new ContratModel();
        $this->ruleModel    = new RuleModel();
        $this->history      = new HistoryModel();
        $this->userModel    = new UserModel();
    }

    /* ── Liste ── */
    public function index(): void {
        $this->requireAuth();
        $contrats = $this->isAdmin()
            ? $this->contratModel->getAll()
            : $this->contratModel->getByOwner($this->currentUserId());
        $stats = [
            'total'    => count($contrats),
            'cdi'      => count(array_filter($contrats, fn($c) => $c['type'] === 'CDI')),
            'cdd'      => count(array_filter($contrats, fn($c) => $c['type'] === 'CDD')),
            'cdiv'     => count(array_filter($contrats, fn($c) => $c['type'] === 'CDIV')),
            'actif'    => count(array_filter($contrats, fn($c) => $c['statut'] === 'actif')),
            'brouillon'=> count(array_filter($contrats, fn($c) => $c['statut'] === 'brouillon')),
        ];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        // Pagination (10 par page)
        $pagination = new Pagination(count($contrats), 10);
        $contrats   = $pagination->slice($contrats);

        $this->render('contrats/index', compact('contrats', 'stats', 'success', 'pagination'));
    }

    /* ── Détail + ses règles ── */
    public function show(string $id): void {
        $this->requireAuth();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);
        $rules = $this->ruleModel->getByContrat((int)$id);
        $this->render('contrats/show', compact('contrat', 'rules'));
    }

    /* ── Formulaire création ── */
    public function create(): void {
        $this->requireAuth();
        $users  = $this->userModel->getAll();
        $errors = $_SESSION['form_errors'] ?? [];
        $old    = $_SESSION['form_old']    ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->render('contrats/form', compact('users', 'errors', 'old'));
    }

    /* ── Enregistrement ── */
    public function store(): void {
        $this->requireAuth();
        $this->csrfCheck();

        $errors = $this->validateContrat($_POST);

        $data = [
            'titre'       => trim($_POST['titre']       ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'type'        => trim($_POST['type']        ?? ''),
            'signature'   => trim($_POST['signature']   ?? ''),
            'signed_by'   => !empty($_POST['signed_by']) ? (int)$_POST['signed_by'] : null,
            'statut'      => trim($_POST['statut']      ?? 'brouillon'),
            'created_by'  => (int)$_SESSION['user_id'],
        ];

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            $this->redirect('/contrats/create');
            return;
        }

        $id = $this->contratModel->create($data);
        $this->history->log($_SESSION['user_id'], 'CONTRAT_CREATE', "Contrat #$id créé : {$data['titre']}");

        $_SESSION['success'] = 'Contrat créé avec succès.';
        $this->redirect('/contrats/show/' . $id);
    }

    /* ── Formulaire édition ── */
    public function edit(string $id): void {
        $this->requireAuth();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);
        $users  = $this->userModel->getAll();
        $errors = $_SESSION['form_errors'] ?? [];
        $old    = $_SESSION['form_old']    ?? $contrat;
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->render('contrats/form', compact('contrat', 'users', 'errors', 'old'));
    }

    /* ── Mise à jour ── */
    public function update(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $existing = $this->contratModel->findById((int)$id);
        if (!$existing) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($existing);

        $errors = $this->validateContrat($_POST);

        $data = [
            'titre'       => trim($_POST['titre']       ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'type'        => trim($_POST['type']        ?? ''),
            'signature'   => trim($_POST['signature']   ?? ''),
            'signed_by'   => !empty($_POST['signed_by']) ? (int)$_POST['signed_by'] : null,
            'statut'      => trim($_POST['statut']      ?? 'brouillon'),
        ];

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            $this->redirect('/contrats/edit/' . $id);
            return;
        }

        $this->contratModel->update((int)$id, $data);
        $this->history->log($_SESSION['user_id'], 'CONTRAT_UPDATE', "Contrat #$id modifié");

        $_SESSION['success'] = 'Contrat mis à jour.';
        $this->redirect('/contrats/show/' . $id);
    }

    /* ── Suppression : owner ou admin ── */
    public function delete(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);
        $this->contratModel->delete((int)$id);
        $this->history->log($_SESSION['user_id'], 'CONTRAT_DELETE', "Contrat #$id supprimé");
        $_SESSION['success'] = 'Contrat supprimé.';
        $this->redirect('/contrats');
    }

    /* ── Validation centralisée — nettoie les badwords automatiquement ── */
    private function validateContrat(array &$input): array {
        // Nettoie les insultes (remplace par ***) avant validation
        $bw = new BadwordsFilter();
        foreach (['titre', 'description', 'signature'] as $f) {
            if (!empty($input[$f])) $input[$f] = $bw->clean((string)$input[$f]);
        }

        $v = (new Validator($input))
            ->labels([
                'titre'       => 'Titre',
                'description' => 'Description',
                'type'        => 'Type',
                'signature'   => 'Signature',
                'statut'      => 'Statut',
            ])
            ->rules([
                'titre'       => 'required|string|min:3|max:200',
                'description' => 'max:5000',
                'type'        => 'required|in:CDI,CDD,CDIV',
                'signature'   => 'max:255',
                'statut'      => 'required|in:brouillon,actif,archive',
            ]);

        $flat = [];
        foreach ($v->errors() as $field => $msgs) $flat[$field] = $msgs[0] ?? 'Invalide';
        return $flat;
    }

    /* ── Construit un PdfRenderer pour le contrat (réutilisé par pdf + email) ── */
    private function buildPdf(array $contrat, array $rules): PdfRenderer {
        $pdf = new PdfRenderer();
        $pdf->title('Contrat #' . $contrat['id'] . ' — ' . $contrat['titre']);
        $pdf->h2('Informations');
        $pdf->p('Type      : ' . $contrat['type']);
        $pdf->p('Statut    : ' . $contrat['statut']);
        $pdf->p('Signature : ' . ($contrat['signature'] ?? '—'));
        $sig = trim(($contrat['signataire_prenom'] ?? '') . ' ' . ($contrat['signataire_nom'] ?? ''));
        $pdf->p('Signataire: ' . ($sig !== '' ? $sig : '—'));
        $pdf->p('Créé le   : ' . substr($contrat['created_at'] ?? '', 0, 10));
        if (!empty($contrat['description'])) {
            $pdf->h2('Description');
            $pdf->p($contrat['description']);
        }
        $pdf->h2('Règles (' . count($rules) . ')');
        if (empty($rules)) {
            $pdf->p('Aucune règle définie.');
        } else {
            foreach ($rules as $r) {
                $pdf->li(($r['position'] ?: '•') . '. ' . $r['titre']);
                if (!empty($r['description'])) $pdf->p('   ' . $r['description']);
            }
        }
        return $pdf;
    }

    /* ── Export PDF (téléchargement direct) ── */
    public function pdf(string $id): void {
        $this->requireAuth();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);
        $rules = $this->ruleModel->getByContrat((int)$id);

        $pdf = $this->buildPdf($contrat, $rules);
        $this->history->log($_SESSION['user_id'], 'CONTRAT_PDF', "Export PDF du contrat #$id");
        $pdf->stream('contrat-' . $contrat['id'] . '.pdf');
    }

    /* ── Traduction du contrat (description) via LibreTranslate ── */
    public function translate(string $id): void {
        $this->requireAuth();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);

        $target = trim($_GET['to'] ?? $_POST['to'] ?? 'en');
        $allowed = ['en','fr','es','de','it','pt','ar','ru','zh','ja'];
        if (!in_array($target, $allowed, true)) $target = 'en';

        $tr = new Translator();
        $source = $tr->detect($contrat['description'] ?? $contrat['titre']) ?? 'fr';

        $contrat['titre']       = $tr->translate($contrat['titre'], $source, $target);
        $contrat['description'] = $tr->translate($contrat['description'] ?? '', $source, $target);

        $rules = $this->ruleModel->getByContrat((int)$id);
        foreach ($rules as &$r) {
            $r['titre']       = $tr->translate($r['titre'], $source, $target);
            $r['description'] = $tr->translate($r['description'] ?? '', $source, $target);
        }
        unset($r);

        $this->history->log($_SESSION['user_id'], 'CONTRAT_TRANSLATE', "Contrat #$id traduit $source → $target");
        $_SESSION['success'] = "Traduction $source → $target appliquée (vue temporaire).";
        $this->render('contrats/show', compact('contrat', 'rules'));
    }

    /* ── Envoi du PDF par email au signataire ── */
    public function email(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);

        $to = trim($_POST['to'] ?? $contrat['signataire_email'] ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_errors'] = ['email' => 'Adresse email destinataire invalide.'];
            $this->redirect('/contrats/show/' . $id);
            return;
        }

        if (!defined('SMTP_HOST') || SMTP_HOST === '') {
            $_SESSION['form_errors'] = ['email' => 'SMTP non configuré (config/database.php).'];
            $this->redirect('/contrats/show/' . $id);
            return;
        }

        $rules = $this->ruleModel->getByContrat((int)$id);
        $pdfBin = $this->buildPdf($contrat, $rules)->output();

        $name  = htmlspecialchars($contrat['titre']);
        $html  = "<h2>Contrat : $name</h2>
                  <p>Bonjour,</p>
                  <p>Veuillez trouver ci-joint le contrat <strong>$name</strong> au format PDF.</p>
                  <p style='color:#888;font-size:12px;'>Envoyé depuis CreatorSpace.</p>";

        try {
            $mailer = new Mailer();
            $mailer->send($to, 'Contrat : ' . $contrat['titre'], $html, null, [[
                'name' => 'contrat-' . $contrat['id'] . '.pdf',
                'mime' => 'application/pdf',
                'data' => $pdfBin,
            ]]);
            $this->history->log($_SESSION['user_id'], 'CONTRAT_EMAIL', "PDF du contrat #$id envoyé à $to");
            $_SESSION['success'] = "PDF envoyé à $to.";
        } catch (\Throwable $e) {
            error_log('[Mailer] ' . $e->getMessage());
            $_SESSION['form_errors'] = ['email' => 'Échec de l\'envoi : ' . $e->getMessage()];
        }
        $this->redirect('/contrats/show/' . $id);
    }

    /* ── Changer statut rapide ── */
    public function statut(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $contrat = $this->contratModel->findById((int)$id);
        if (!$contrat) { $this->redirect('/contrats'); return; }
        $this->requireOwnership($contrat);
        $statut = trim($_POST['statut'] ?? 'brouillon');
        $allowed = ['brouillon', 'actif', 'archive'];
        if (!in_array($statut, $allowed, true)) {
            $this->redirect('/contrats');
            return;
        }
        $this->contratModel->updateStatut((int)$id, $statut);
        $this->history->log($_SESSION['user_id'], 'CONTRAT_STATUT', "Contrat #$id → $statut");
        $_SESSION['success'] = 'Statut mis à jour.';
        $this->redirect('/contrats/show/' . $id);
    }
}
