<?php
require_once 'models/Contrat.php';
require_once 'models/Auteur.php';

class ContratController {
    private $contratModel;
    private $auteurModel;

    public function __construct() {
        $this->contratModel = new Contrat();
        $this->auteurModel = new Auteur();
    }

    // Affichage FrontOffice
    public function list() {
        $contrats = $this->contratModel->getAll();
        require 'views/front.php';
    }

    // Affichage BackOffice
    public function admin() {
        $contrats = $this->contratModel->getAll();
        require 'views/back.php';
    }

    public function detail($id) {
        $contrat = $this->contratModel->getById($id);
        if (!$contrat) {
            die("Contrat introuvable.");
        }
        require 'views/detail.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $date = $_POST['date'];
            $auteur_id = $_POST['auteur_id'];

            // Validation simple en PHP car HTML5 strict interdit
            if (!empty($titre) && !empty($contenu) && !empty($date) && !empty($auteur_id)) {
                $this->contratModel->add($titre, $contenu, $date, $auteur_id);
                header('Location: index.php?action=admin');
                exit();
            } else {
                $erreur = "Erreur de validation : Veuillez remplir tous les champs manquants.";
            }
        }
        $auteurs = $this->auteurModel->getAll();
        require 'views/add.php';
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $contenu = $_POST['contenu'];
            $date = $_POST['date'];
            $auteur_id = $_POST['auteur_id'];

            if (!empty($titre) && !empty($contenu) && !empty($date) && !empty($auteur_id)) {
                $this->contratModel->update($id, $titre, $contenu, $date, $auteur_id);
                header('Location: index.php?action=admin');
                exit();
            } else {
                $erreur = "Erreur de validation : Veuillez remplir tous les champs manquants.";
            }
        }
        $contrat = $this->contratModel->getById($id);
        $auteurs = $this->auteurModel->getAll();
        require 'views/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
            $this->contratModel->delete($id);
            header('Location: index.php?action=admin');
            exit();
        }
        $contrat = $this->contratModel->getById($id);
        require 'views/delete.php';
    }
}
?>
