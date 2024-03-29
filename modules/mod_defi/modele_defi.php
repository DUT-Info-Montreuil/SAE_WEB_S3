<?php

require_once 'Connexion.php';

class ModeleDefi extends Connexion {

    private $connexion;

    function __construct() {
        $this->connexion = new Connexion();
        $this->connexion::initConnexion();
    }

    function getListe() {
        $requete = $this->connexion->getBdd()->query("SELECT * from defi");
        $tableau = $requete->fetchAll();
        return $tableau;
    }

    function ajouterErreurReponse($defiId, $id_utilisateur) {
        $requete = $this->connexion->getBdd()->prepare("UPDATE joueurDefi SET repondu = repondu + 1 WHERE id_defi = ? AND id_joueur = ?");
        $requete->execute([$defiId, $id_utilisateur]);
    }

    function bonneReponse($defiId, $id_utilisateur) {
        $requete = $this->connexion->getBdd()->prepare("UPDATE joueurDefi SET repondu = 4 WHERE id_defi = ? AND id_joueur = ?");
        $requete->execute([$defiId, $id_utilisateur]);
    }


    function aDejaReponduCorrectement($defiId, $id_utilisateur) {
        $requete = $this->connexion->getBdd()->prepare("SELECT repondu FROM joueurDefi WHERE id_defi = ? AND id_joueur = ?");
        $requete->execute([$defiId, $id_utilisateur]);
        $resultat = $requete->fetch();
        if (isset($resultat["repondu"])) {
            return $resultat;
        } else {
            return null; 
        }

    }

    function enregistrerReponse($defiId, $id_utilisateur, $numero) {
        $requete = $this->connexion->getBdd()->prepare("INSERT INTO joueurDefi (id_joueur, id_defi, repondu) VALUES (?, ?, ?)");
        $requete->execute([$id_utilisateur, $defiId, $numero]);
    }

    function verifierReponse($defiId, $reponse) {
        $requete = $this->connexion->getBdd()->prepare("SELECT solution FROM defi WHERE id_defi = ?");
        $requete->execute([$defiId]);
        $resultat = $requete->fetch();
        
        if ($resultat && strtolower($resultat['solution']) == strtolower($reponse)) {
            return true; 
        } else {
            return false; 
        }
    }

    function ajouterJetonUtilisateur($id_utilisateur) {
        $requete = $this->connexion->getBdd()->prepare("UPDATE joueur SET jeton = jeton + 2 WHERE id_joueur = ?");
        $requete->execute([$id_utilisateur]);
    }

    public function creerDefi($defi, $reponse) {
        try {
            $requete = $this->connexion->getBdd()->prepare("INSERT INTO defi (defi, Solution) VALUES (?, ?)");
            $requete->execute([$defi, $reponse]);
            $resultat = $requete->fetch();

            return $resultat;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; 
        }
    }

    function enleverDefiJoueurs($id) {
        $requete = $this->connexion->getBdd()->prepare("DELETE FROM joueurDefi WHERE id_defi = ?");
        $requete->execute([$id]);
    }

    function suppDefi($id) {
        $requete = $this->connexion->getBdd()->prepare("DELETE FROM defi WHERE id_defi = ? ");
        $requete->execute([$id]);
    }

}

?>