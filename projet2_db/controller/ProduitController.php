<?php
// /controller/ProduitController.php
// No session check — open access as required.
// No HTML. No SQL. Only: receive request → call Model → pass to View.

require_once __DIR__ . '/../model/ProduitModel.php';

class ProduitController {
    private $model;

    public function __construct() {
        $this->model = new ProduitModel();
    }

    public function handle() {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'index'  : $this->index();  break;
            case 'create' : $this->create(); break;
            case 'store'  : $this->store();  break;
            case 'edit'   : $this->edit();   break;
            case 'update' : $this->update(); break;
            case 'delete' : $this->delete(); break;
            case 'show'   : $this->show();   break;
            default       : $this->index();  break;
        }
    }

    // LIST — backoffice
    private function index() {
        $data = $this->model->getAll();
        include __DIR__ . '/../view/backoffice/produits/list.php';
    }

    // SHOW FORM ADD
    private function create() {
        $errors = [];
        include __DIR__ . '/../view/backoffice/produits/form_add.php';
    }

    // STORE — process POST from form_add
    private function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=produit&action=index');
            exit;
        }
        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            include __DIR__ . '/../view/backoffice/produits/form_add.php';
            return;
        }
        $this->model->insert($_POST);
        header('Location: index.php?controller=produit&action=index&success=ajout');
        exit;
    }

    // SHOW FORM EDIT
    private function edit() {
        $id   = (int)($_GET['id'] ?? 0);
        $item = $this->model->getById($id);
        if (!$item) {
            header('Location: index.php?controller=produit&action=index');
            exit;
        }
        $errors = [];
        include __DIR__ . '/../view/backoffice/produits/form_edit.php';
    }

    // UPDATE — process POST from form_edit
    private function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=produit&action=index');
            exit;
        }
        $id     = (int)($_GET['id'] ?? 0);
        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $item = array_merge($this->model->getById($id) ?? [], $_POST);
            include __DIR__ . '/../view/backoffice/produits/form_edit.php';
            return;
        }
        $this->model->update($id, $_POST);
        header('Location: index.php?controller=produit&action=index&success=modif');
        exit;
    }

    // DELETE
    private function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) $this->model->delete($id);
        header('Location: index.php?controller=produit&action=index&success=suppression');
        exit;
    }

    // SHOW — frontoffice detail
    private function show() {
        $id   = (int)($_GET['id'] ?? 0);
        $item = $this->model->getById($id);
        if (!$item) {
            header('Location: index.php?controller=produit&action=front');
            exit;
        }
        include __DIR__ . '/../view/frontoffice/produits/detail.php';
    }

    // FRONT INDEX — public list
    public function front() {
        $data = $this->model->getAll();
        include __DIR__ . '/../view/frontoffice/produits/index.php';
    }

    // VALIDATION — no HTML5, PHP only
    private function validate(array $data): array {
        $errors = [];
        $nom    = trim($data['nom']         ?? '');
        $desc   = trim($data['description'] ?? '');
        $prix   = trim($data['prix']        ?? '');
        $stock  = trim($data['stock']       ?? '');

        if ($nom   === '') $errors[] = "Le nom est obligatoire.";
        if ($desc  === '') $errors[] = "La description est obligatoire.";
        if ($prix  === '') $errors[] = "Le prix est obligatoire.";
        if ($stock === '') $errors[] = "Le stock est obligatoire.";

        if ($prix !== '' && (!is_numeric($prix) || (float)$prix < 0))
            $errors[] = "Le prix doit être un nombre positif.";
        if ($stock !== '' && (!ctype_digit($stock) || (int)$stock < 0))
            $errors[] = "Le stock doit être un entier positif.";

        return $errors;
    }
}
