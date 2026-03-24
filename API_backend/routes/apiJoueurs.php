<?php 
use R301\Controleur\JoueurControleur;

$ctrl = JoueurControleur::getInstance();
        if ($method === 'GET' && $id == null){
            //alors c'est la liste de tout les joueurs
            $joueurs = $ctrl->listerTousLesJoueurs();
            deliver_response(200,'Succes',$joueurs);
        }elseif ($method == 'GET' && $id !== null) {
        // GET /joueurs/5 → obtenir un joueur précis
            $joueurs = $ctrl->getJoueurById($id);
            if($joueurs == null){
                deliver_response(404,'Joueur non trouvé',null);
            }else{
                deliver_response(200,'Succes',$joueurs);
            }
        } elseif ($method === 'POST') {
            // POST /joueurs → créer un joueur
            //on verifie que l'utilisateur connecté est l'entraineur
            if ($role !== 'directeur'){
                 deliver_response(403,'Acces interdit',null);
                exit;
            }
            //on recupere le contenue du json pour creer le joueur en appellant le controleur adapté
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $ctrl->ajouterJoueur(
                $data['numero_licence'],
                $data['nom'],
                $data['prenom'],
                new DateTime($data['date_naissance']),
                $data['taille'],
                $data['poids'],
                $data['statut']
            );
            deliver_response(201, 'Joueur créé', $result);
            } elseif ($method === 'PUT' && $id !== null) {
            // PUT /joueurs/5 → modifier un joueur
            //on verifie encore que l'utilisateur est directeur 
            if ($role !== 'directeur') {
                deliver_response(403, 'Accès interdit', null);
                exit;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $ctrl->modifierJoueur(
                $id,
                $data['numero_licence'],
                $data['nom'],
                $data['prenom'],
                new DateTime($data['date_naissance']),
                $data['taille'],
                $data['poids'],
                $data['statut']
            );
            deliver_response(200, 'Joueur modifié avec succès', $result);
        } elseif ($method === 'DELETE' && $id !== null) {
             // DELETE /joueurs/5 → supprimer un joueur
        // Seulement le directeur
        if ($role !== 'directeur') {
            deliver_response(403, 'Accès interdit', null);
            exit;
        }
        $result = $ctrl->supprimerJoueur($id);
        deliver_response(200, 'Joueur supprimé', $result);

        } else {
            // Méthode non supportée sur cette route
            deliver_response(405, 'Méthode non autorisée', null);
        }


?>