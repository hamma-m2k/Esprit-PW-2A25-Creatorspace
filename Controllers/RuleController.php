<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Controllers/BadwordsFilter.php';
require_once ROOT . '/Controllers/Translator.php';
require_once ROOT . '/Models/RuleModel.php';
require_once ROOT . '/Models/ContratModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class RuleController extends Controller {

    private RuleModel    $ruleModel;
    private ContratModel $contratModel;
    private HistoryModel $history;

    public function __construct() {
        $this->ruleModel    = new RuleModel();
        $this->contratModel = new ContratModel();
        $this->history      = new HistoryModel();
    }

    /** Vérifie que le contrat parent appartient à l'utilisateur (ou admin). */
    private function assertCanAccessContrat(int $contratId): array {
        $contrat = $this->contratModel->findById($contratId);
        if (!$contrat) { $this->redirect('/contrats'); exit; }
        $this->requireOwnership($contrat);
        return $contrat;
    }

    /* ── Liste globale des règles ── */
    public function index(): void {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $rules    = $this->ruleModel->getAll();
            $contrats = $this->contratModel->getAll();
        } else {
            $contrats = $this->contratModel->getByOwner($this->currentUserId());
            $ids      = array_column($contrats, 'id');
            $rules    = array_filter(
                $this->ruleModel->getAll(),
                fn($r) => in_array((int)$r['contrat_id'], array_map('intval', $ids), true)
            );
        }
        $success  = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $this->render('rules/index', compact('rules', 'contrats', 'success'));
    }

    /* ── Formulaire ajout d'une règle ── */
    public function create(): void {
        $this->requireAuth();
        $contratId  = (int)($_GET['contrat_id'] ?? 0);
        $contrat    = $contratId ? $this->assertCanAccessContrat($contratId) : null;
        $contrats   = $this->isAdmin()
            ? $this->contratModel->getAll()
            : $this->contratModel->getByOwner($this->currentUserId());
        $errors     = $_SESSION['form_errors'] ?? [];
        $old        = $_SESSION['form_old']    ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->render('rules/form', compact('contrat', 'contrats', 'errors', 'old'));
    }

    /* ── Enregistrement d'une règle ── */
    public function store(): void {
        $this->requireAuth();
        $this->csrfCheck();
        $this->assertCanAccessContrat((int)($_POST['contrat_id'] ?? 0));

        $data = [
            'contrat_id'  => (int)($_POST['contrat_id'] ?? 0),
            'titre'       => trim($_POST['titre']       ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'position'    => trim($_POST['position']    ?? ''),
            'source'      => 'manuel',
            'created_by'  => (int)$_SESSION['user_id'],
        ];

        // Nettoyage des badwords (remplace par ***) au lieu de bloquer
        $bw = new BadwordsFilter();
        $data['titre']       = $bw->clean($data['titre']);
        $data['description'] = $bw->clean($data['description']);

        $errors = $this->ruleModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            $this->redirect('/rules/create?contrat_id=' . $data['contrat_id']);
            return;
        }

        $id = $this->ruleModel->create($data);
        $this->history->log($_SESSION['user_id'], 'RULE_CREATE', "Règle #$id ajoutée au contrat #{$data['contrat_id']}");

        $_SESSION['success'] = 'Règle ajoutée avec succès.';
        $this->redirect('/contrats/show/' . $data['contrat_id']);
    }

    /* ── Page d'import / ajout multiple ── */
    public function addPage(): void {
        $this->requireAuth();
        $contratId = (int)($_GET['contrat_id'] ?? 0);
        $contrat   = $contratId ? $this->assertCanAccessContrat($contratId) : null;
        $contrats  = $this->isAdmin()
            ? $this->contratModel->getAll()
            : $this->contratModel->getByOwner($this->currentUserId());
        $rules     = $contratId ? $this->ruleModel->getByContrat($contratId) : [];
        $success   = $_SESSION['success'] ?? null;
        $errors    = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['success'], $_SESSION['form_errors']);
        $this->render('rules/add_page', compact('contrat', 'contrats', 'rules', 'success', 'errors'));
    }

    /* ── Sauvegarde depuis la page d'ajout multiple ── */
    public function saveBatch(): void {
        $this->requireAuth();
        $this->csrfCheck();

        $contratId = (int)($_POST['contrat_id'] ?? 0);
        $this->assertCanAccessContrat($contratId);
        if ($contratId <= 0) {
            $_SESSION['form_errors'] = ['contrat_id' => 'Contrat invalide.'];
            $this->redirect('/rules/add');
            return;
        }

        $titres      = $_POST['titres']       ?? [];
        $descriptions= $_POST['descriptions'] ?? [];
        $positions   = $_POST['positions']    ?? [];
        $errors      = [];
        $saved       = 0;
        $bw          = new BadwordsFilter();

        foreach ($titres as $i => $titre) {
            $titre = trim($titre);
            if (empty($titre)) continue;

            $row = [
                'contrat_id'  => $contratId,
                'titre'       => $bw->clean($titre),
                'description' => $bw->clean(trim($descriptions[$i] ?? '')),
                'position'    => trim($positions[$i]    ?? ''),
                'source'      => 'manuel',
                'created_by'  => (int)$_SESSION['user_id'],
            ];

            $rowErrors = $this->ruleModel->validate($row);
            if (!empty($rowErrors)) {
                $errors["ligne_$i"] = $rowErrors;
                continue;
            }

            $this->ruleModel->create($row);
            $saved++;
        }

        $this->history->log(
            $_SESSION['user_id'],
            'RULES_BATCH_SAVE',
            "$saved règle(s) ajoutée(s) au contrat #$contratId"
        );

        $_SESSION['success'] = "$saved règle(s) sauvegardée(s) avec succès.";
        $this->redirect('/contrats/show/' . $contratId);
    }

    /* ── Import depuis JSON / texte ── */
    public function import(): void {
        $this->requireAuth();
        $this->csrfCheck();

        $contratId = (int)($_POST['contrat_id'] ?? 0);
        $this->assertCanAccessContrat($contratId);
        $rawJson   = trim($_POST['import_json'] ?? '');

        if ($contratId <= 0) {
            $_SESSION['form_errors'] = ['contrat_id' => 'Sélectionnez un contrat.'];
            $this->redirect('/rules/add?contrat_id=' . $contratId);
            return;
        }

        if (empty($rawJson)) {
            $_SESSION['form_errors'] = ['import' => 'Aucune donnée à importer.'];
            $this->redirect('/rules/add?contrat_id=' . $contratId);
            return;
        }

        $decoded = json_decode($rawJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $_SESSION['form_errors'] = ['import' => 'Format JSON invalide : ' . json_last_error_msg()];
            $this->redirect('/rules/add?contrat_id=' . $contratId);
            return;
        }

        if (!is_array($decoded)) {
            $_SESSION['form_errors'] = ['import' => 'Le JSON doit contenir un tableau de règles.'];
            $this->redirect('/rules/add?contrat_id=' . $contratId);
            return;
        }

        $this->ruleModel->importBatch($contratId, $decoded, (int)$_SESSION['user_id']);
        $this->history->log($_SESSION['user_id'], 'RULES_IMPORT', count($decoded) . " règle(s) importée(s) dans le contrat #$contratId");

        $_SESSION['success'] = count($decoded) . ' règle(s) importée(s) avec succès.';
        $this->redirect('/rules/add?contrat_id=' . $contratId);
    }

    /* ── Édition ── */
    public function edit(string $id): void {
        $this->requireAuth();
        $rule     = $this->ruleModel->findById((int)$id);
        if (!$rule) { $this->redirect('/rules'); return; }
        $this->assertCanAccessContrat((int)$rule['contrat_id']);
        $contrats = $this->isAdmin()
            ? $this->contratModel->getAll()
            : $this->contratModel->getByOwner($this->currentUserId());
        $errors   = $_SESSION['form_errors'] ?? [];
        $old      = $_SESSION['form_old']    ?? $rule;
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->render('rules/form', compact('rule', 'contrats', 'errors', 'old'));
    }

    /* ── Mise à jour ── */
    public function update(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $existing = $this->ruleModel->findById((int)$id);
        if (!$existing) { $this->redirect('/rules'); return; }
        $this->assertCanAccessContrat((int)$existing['contrat_id']);
        $this->assertCanAccessContrat((int)($_POST['contrat_id'] ?? 0));

        $data = [
            'contrat_id'  => (int)($_POST['contrat_id'] ?? 0),
            'titre'       => trim($_POST['titre']       ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'position'    => trim($_POST['position']    ?? ''),
        ];

        $errors = $this->ruleModel->validate($data);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            $this->redirect('/rules/edit/' . $id);
            return;
        }

        $this->ruleModel->update((int)$id, $data);
        $this->history->log($_SESSION['user_id'], 'RULE_UPDATE', "Règle #$id modifiée");

        $_SESSION['success'] = 'Règle mise à jour.';
        $this->redirect('/contrats/show/' . $data['contrat_id']);
    }

    /* ── Traduction d'une règle (titre + description) ── */
    public function translate(string $id): void {
        $this->requireAuth();
        $rule = $this->ruleModel->findById((int)$id);
        if (!$rule) { $this->redirect('/rules'); return; }
        $this->assertCanAccessContrat((int)$rule['contrat_id']);

        $target = trim($_GET['to'] ?? 'en');
        $allowed = ['en','fr','es','de','it','pt','ar','ru','zh','ja'];
        if (!in_array($target, $allowed, true)) $target = 'en';

        $tr     = new Translator();
        $source = $tr->detect($rule['description'] ?? $rule['titre']) ?? 'fr';

        $_SESSION['translated_rule'] = [
            'id'          => $rule['id'],
            'source'      => $source,
            'target'      => $target,
            'titre'       => $tr->translate($rule['titre'], $source, $target),
            'description' => $tr->translate($rule['description'] ?? '', $source, $target),
        ];
        $this->history->log($_SESSION['user_id'], 'RULE_TRANSLATE', "Règle #$id traduite $source → $target");
        $_SESSION['success'] = "Règle traduite $source → $target.";
        $this->redirect('/contrats/show/' . $rule['contrat_id']);
    }

    /* ── Suppression ── */
    public function delete(string $id): void {
        $this->requireAuth();
        $this->csrfCheck();
        $rule = $this->ruleModel->findById((int)$id);
        if (!$rule) { $this->redirect('/contrats'); return; }
        $this->assertCanAccessContrat((int)$rule['contrat_id']);
        $contratId = $rule['contrat_id'] ?? 0;
        $this->ruleModel->delete((int)$id);
        $this->history->log($_SESSION['user_id'], 'RULE_DELETE', "Règle #$id supprimée");
        $_SESSION['success'] = 'Règle supprimée.';
        $this->redirect('/contrats/show/' . $contratId);
    }
}
