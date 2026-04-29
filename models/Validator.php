<?php

/**
 * Classe Validator
 * Valide les données des formulaires avant insertion/modification.
 * Retourne un tableau d'erreurs si validation échouée.
 */
class Validator
{
    private $errors = [];

    /**
     * Récupère les erreurs de validation
     * @return array Tableau des erreurs
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Vérifie s'il y a des erreurs
     * @return bool true si erreurs, false sinon
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Ajoute une erreur
     * @param string $field Nom du champ
     * @param string $message Message d'erreur
     */
    public function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }

    // ==================== VALIDATIONS ====================

    /**
     * Valide qu'un champ n'est pas vide
     * @param string $value Valeur à vérifier
     * @param string $fieldName Nom du champ
     * @return bool true si valide
     */
    public function required($value, $fieldName)
    {
        if (empty(trim($value))) {
            $this->addError($fieldName, "{$fieldName} est obligatoire.");
            return false;
        }
        return true;
    }

    /**
     * Valide la longueur minimale
     * @param string $value Valeur à vérifier
     * @param int $min Longueur minimale
     * @param string $fieldName Nom du champ
     * @return bool true si valide
     */
    public function minLength($value, $min, $fieldName)
    {
        if (strlen(trim($value)) < $min) {
            $this->addError($fieldName, "{$fieldName} doit contenir au moins {$min} caractères.");
            return false;
        }
        return true;
    }

    /**
     * Valide la longueur maximale
     * @param string $value Valeur à vérifier
     * @param int $max Longueur maximale
     * @param string $fieldName Nom du champ
     * @return bool true si valide
     */
    public function maxLength($value, $max, $fieldName)
    {
        if (strlen(trim($value)) > $max) {
            $this->addError($fieldName, "{$fieldName} ne doit pas dépasser {$max} caractères.");
            return false;
        }
        return true;
    }

    /**
     * Valide une adresse email
     * @param string $email Email à vérifier
     * @param string $fieldName Nom du champ
     * @return bool true si valide
     */
    public function email($email, $fieldName = "Email")
    {
        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            $this->addError($fieldName, "{$fieldName} n'est pas valide.");
            return false;
        }
        return true;
    }

    /**
     * Valide qu'un champ est un nombre
     * @param mixed $value Valeur à vérifier
     * @param string $fieldName Nom du champ
     * @return bool true si valide
     */
    public function isNumeric($value, $fieldName)
    {
        if (!is_numeric($value) || $value <= 0) {
            $this->addError($fieldName, "{$fieldName} doit être un nombre valide.");
            return false;
        }
        return true;
    }

    /**
     * Valide un contrat complet
     * @param array $data Données du formulaire
     * @return bool true si tout est valide
     */
    public function validateContrat($data)
    {
        $this->errors = [];

        // Validation du titre
        $this->required($data['titre'] ?? '', 'Titre');
        $this->minLength($data['titre'] ?? '', 3, 'Titre');
        $this->maxLength($data['titre'] ?? '', 255, 'Titre');

        // Validation du contenu
        $this->required($data['contenu'] ?? '', 'Contenu');
        $this->minLength($data['contenu'] ?? '', 10, 'Contenu');

        // Validation de l'auteur
        if (empty($data['auteur_id'])) {
            $this->addError('auteur_id', 'Veuillez sélectionner un auteur.');
        } else {
            $this->isNumeric($data['auteur_id'], 'Auteur');
        }

        return !$this->hasErrors();
    }
}
?>
