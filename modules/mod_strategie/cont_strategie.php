<?php
require_once 'modele_strategie.php';
require_once 'vue_strategie.php';

class ContStrategie {

    private $vue;
    private $modele;
    private $action;

    function __construct() {
        $this->vue = new VueStrategie();
        $this->modele = new ModeleStrategie();
        $this->action = isset($_GET['action']) ? $_GET['action'] : "strategie" ;
    }

    function tour() {
        $this->vue->affiche_listeDefense($this->modele->getListeDefense());
    }

    function ennemi() {
        $this->vue->affiche_listeEnnemi($this->modele->getListeAttaqueEtDeplacement());
    }

    function envoiSuggestion($utilisateur) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $choix = isset($_POST['choix']) ? $_POST['choix'] : '';
		    $sug = isset($_POST['sug']) ? $_POST['sug'] : '';
            $date = $_POST["date"];
            $id_utilisateur = $this->getIdUtilisateur();

            if (!empty($choix) && !empty($sug) && !empty($date)) {
                
                if ($this->modele->verifJeton($id_utilisateur)) {
                    if($this->modele->ajouterSuggestion($id_utilisateur, $choix, $sug, $date)) {
                        $_SESSION["msg"] ="Suggestion envoyé.";
                        $this->modele->ajouterJetonUtilisateur($id_utilisateur);
                        $this->vue->affiche_suggestion();
                    }else {
                        $_SESSION["erreur"] = "Erreur lors de l'envoie'.";
                    }
                } else {
                    $_SESSION["erreur"] = "Pas assez de jeton.";
                }
        
            } else {
                $_SESSION["erreur"] = "Veuillez remplir tous les champs du formulaire.";
            }
	    }else {
            $this->vue->affiche_suggestion();
        }
        if(isset($_SESSION["erreur"])){
            $this->vue->affiche_suggestion();
        }
    }

    private function getIdUtilisateur() {
        return isset($_SESSION['user']['id_joueur']) ? $_SESSION['user']['id_joueur'] : null;
    }

    function afficheSuggestion() {
        $this->vue->affiche_sugg($this->modele->getListeSugg());
    }

    function exec(){

        switch ($this->action){
            case "strategie":
                $this->vue->bienvenue();
                break;
            case "tour":
                $this->vue->menu();
                $this->tour();
                break;
            case "ennemi":
                $this->vue->menu();
                $this->ennemi();
                break;    
            case "suggestion":
                $this->vue->menu();
                $utilisateur = $_SESSION['user'];
                $this->envoiSuggestion($utilisateur);
                break;
            case "afficheSuggestion":
                $this->vue->menu();
                $this->afficheSuggestion();
                break;
            case "supprimerSugg":
                $id = isset($_GET['id']) ? $_GET['id'] : "Error" ;
                $_SESSION["msg"] = "Défi supprimé !";
                $this->vue->menu();
                $this->modele->suppSugg($id);
                $this->afficheSuggestion();
                break;

            default:
                $_SESSION["erreur"] = "Erreur action incorrecte.";
                $this->vue->bienvenue();
                break;
        }

    }

    public function getAffichage() {
        return $this->vue->getAffichage();
     }
}
?>
