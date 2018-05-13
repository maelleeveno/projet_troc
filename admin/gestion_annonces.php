<?php 

require_once('../inc/init.inc.php');

// ------------- TRAITEMENT ------------

// 1- Vérification que le membre est admin et est connecté : 
if(!isConnectedAndAdmin()) {
	// Si membre non connecté ou non admin, on le redirige vers la page de connexion :
	header('location:../connexion.php');	// on demande la page de connexion
	exit();	// on quitte le script.
} 

// 3- Suppresion de l'annonce : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_annonce'])) {
	// si l'indice 'action' existe et qu'il vaut 'suppression' et qu'existe l'indice 'id_annonce', on va supprimer l'annonce : 
	$resultat = executeReq("DELETE FROM annonce WHERE id_annonce = :id_annonce", 
							   array(':id_annonce' => $_GET['id_annonce']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success">L\'annonce a bien été supprimée !</div>';
	}
	
	$_GET['action'] = 'affichage';
}

// 2- Affichage des annonces
if(isConnectedAndAdmin()) {
	$resultat = executeReq("SELECT * FROM annonce"); // sélectionne tous les produits 
	$membre = executeReq("SELECT prenom, nom FROM membre WHERE id_membre IN (SELECT id_membre FROM annonce WHERE id_membre = membre_id)");
	
	$contenu .= 'Nombre d\'annonces publiées :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>id_annonce</th>';
			$contenu .= '<th>titre</th>';
			$contenu .= '<th>description_courte</th>';
			$contenu .= '<th>description_longue</th>';
			$contenu .= '<th>prix</th>';
			$contenu .= '<th>photo</th>';
			$contenu .= '<th>pays</th>';
			$contenu .= '<th>ville</th>';
			$contenu .= '<th>adresse</th>';
			$contenu .= '<th>cp</th>';
			$contenu .= '<th>date_enregistrement</th>';
			$contenu .= '<th>membre_id</th>';
			$contenu .= '<th>categorie_id</th>';
			$contenu .= '<th>Action</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($annonce = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
				// on parcourt les informations du tableau associatif $annonce : 
				foreach($annonce as $indice => $information) {
					if($indice == 'photo') {	// on met une balise <img /> pour la photo
						$contenu .= '<td><img src="../'. $information .'" width="90" height="90"/></td>';
					} else {
						// pour les autres champs
						$contenu .= '<td>'. $information .'</td>';
					}
				}
						
						// Voir - modifier - suprimer 
						// glyphicon-eye-open pour voir
			$contenu .= '<td>
							<a href="../fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
							
							<a href="?action=suppression&id_annonce='. $annonce['id_annonce'] .'"  onclick="return(confirm(\'Êtes-vous certain de vouloir supprimer cette annonce ?\'));" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
						</td>';
			$contenu .= '</tr>';
		}			
	$contenu .= '</table>';
}


// ------------- AFFICHAGE ------------

require_once('../inc/haut.inc.php');

echo $contenu; // pour afficher les messages


require_once('../inc/bas.inc.php');

?>
