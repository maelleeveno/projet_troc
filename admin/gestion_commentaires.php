<?php 

require_once('../inc/init.inc.php');

// ------------- TRAITEMENT ------------

// 1- Vérification que le membre est admin et est connecté : 
if(!isConnectedAndAdmin()) {
	// Si membre non connecté ou non admin, on le redirige vers la page de connexion :
	header('location:../connexion.php');	// on demande la page de connexion
	exit();	// on quitte le script.
} 


// 7- Suppresion du commentaire : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_commentaire'])) {
	// si l'indice 'action' existe et qu'il vaut 'suppression' et qu'existe l'indice 'id_categorie', on va supprimer la catégorie : 
	$resultat = executeReq("DELETE FROM commentaire WHERE id_commentaire = :id_commentaire", 
							   array(':id_commentaire' => $_GET['id_commentaire']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success">Le commentaire a bien été supprimé !</div>';
	}
	
	$_GET['action'] = 'affichage';	// permet de lancer l'affichage des catégories (cf. chapitre 6 ci-dessous).
}


// 2- Affichage des commentaires
if(isConnectedAndAdmin()) {
	$resultat = executeReq("SELECT commentaire.id_commentaire, commentaire.commentaire, commentaire.date_enregistrement, membre.pseudo, commentaire.annonce_id 
						    FROM annonce 
							LEFT JOIN membre ON annonce.membre_id = membre.id_membre
							RIGHT JOIN commentaire ON annonce.id_annonce = commentaire.annonce_id");

	$contenu .= 'Nombre de commentaire :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>N° du commentaire</th>';
			$contenu .= '<th>Commentaire</th>';
			$contenu .= '<th>Publié le</th>';
			$contenu .= '<th>Membre</th>';
			$contenu .= '<th>Annonce</th>';
			$contenu .= '<th>Supprimer</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($commentaire = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
				// on parcourt les informations du tableau associatif $categorie : 
				foreach($commentaire as $indice => $information) {
					if($indice == 'pseudo' && empty($information)) {
						$contenu .= '<td><i>Membre supprimé</i></td>';
					}elseif($indice == 'annonce_id' && empty($information)) {
						$contenu .= '<td><i>Annonce supprimée</i></td>';
					}elseif($indice == 'annonce_id') {
						$resultatAnnonce = executeReq("SELECT * FROM annonce WHERE id_annonce = $information");
						$idAnnonce = $resultatAnnonce->fetch(PDO::FETCH_ASSOC);
						$contenu .= '<td><a href="../fiche_annonce.php?id_annonce='. $idAnnonce['id_annonce'] .'">' . $idAnnonce['titre'] . '</a></td>'; 
					}elseif($indice == 'date_enregistrement') {
						$dateFr = new DateTime($information);
						$contenu .= '<td>' . $dateFr->format('d/m/Y à H:i:s') . '</td>';
					}else {
						$contenu .= '<td>' . $information . '</td>';
					}
				}
						
                $contenu .= '<td>
                                <a href="?action=suppression&id_commentaire='. $commentaire['id_commentaire'] .'"  onclick="return(confirm(\'Êtes-vous certain de vouloir supprimer ce commentaire ?\'));" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            </td>';
                $contenu .= '</tr>';
		}			
	$contenu .= '</table>';
}


        
        

// ------------- AFFICHAGE ------------

require_once('../inc/haut.inc.php');
    
echo '<h3>Gestion des commentaires </h3>';

echo $contenu; // pour afficher les messages

// 3- Formulaire HTML d'une catégorie :
if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :	// Les deux points ':' et le 'endif' permettent d'enlever les accolades.
    // si je suis en ajout ou en modification d'une catégorie, j'affiche le formulaire. 
    
        // 8- Formulaire de modification d'une catégorie avec présaisie des valeurs :
        if(isset($_GET['id_commentaire'])) {
            // si existe l'id_categorie, je peux sélectionner la catégorie en BDD : 
            $resultat = executeReq("SELECT * FROM commentaire WHERE id_commentaire = :id_commentaire", 
                                       array(':id_commentaire' => $_GET['id_commentaire']));
            $commentaire_actuelle = $resultat->fetch(PDO::FETCH_ASSOC); 	// Pas de boucle sur ce fetch car il n'y a qu'une seule catégorie par id. 
                                                                    // 'categorie_actuelle' est un array qui contient toutes les infos de la catégorie à mettre dans le formulaire. 
            
        }

?>



<?php 
endif;  
require_once('../inc/bas.inc.php');

?>