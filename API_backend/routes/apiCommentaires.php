<?php
use R301\Controleur\CommentaireControleur;

$ctrl = CommentaireControleur::getInstance();

// DELETE /commentaires/{id}
if ($method === 'DELETE' && $id !== null) {
    $result = $ctrl->supprimerCommentaire($id);
    if ($result) {
        deliver_response(200, 'Commentaire supprimé', null);
    } else {
        deliver_response(500, 'Erreur lors de la suppression du commentaire', null);
    }
} else {
    deliver_response(405, 'Méthode non autorisée', null);
}
