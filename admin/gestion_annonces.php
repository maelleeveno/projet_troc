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
	
	$contenu .= 'Nombre d\'annonces publiées :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>N° de l\'annonce</th>';
			$contenu .= '<th>Titre</th>';
			$contenu .= '<th>Description courte</th>';
			$contenu .= '<th>Description longue</th>';
			$contenu .= '<th>Prix</th>';
			$contenu .= '<th>Photo</th>';
			$contenu .= '<th>Pays</th>';
			$contenu .= '<th>Ville</th>';
			$contenu .= '<th>Adresse</th>';
			$contenu .= '<th>Code postal</th>';
			$contenu .= '<th>Date de publication</th>';
			$contenu .= '<th>Membre</th>';
			$contenu .= '<th>Catégorie</th>';
			$contenu .= '<th>Action</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($annonce = $resultat->fetch(PDO::FETCH_ASSOC)) {
			
			$contenu .= '<tr>';
				// on parcourt les informations du tableau associatif $annonce : 
				foreach($annonce as $indice => $information) {
					if($indice =='description_longue' && strlen($information) >= 70) {
						$contenu .= '<td>' .substr($information, 0, 70) . ' [...]</td>';
					}elseif($indice == 'photo') {	// on met une balise <img /> pour la photo
						$contenu .= '<td><img src="../'. $information .'" width="90" height="90"/></td>';
					}elseif($indice == 'membre_id') {
						$resultatMembre = executeReq("SELECT * FROM membre WHERE id_membre = $information");
						$membre = $resultatMembre->fetch(PDO::FETCH_ASSOC);
						$contenu .= '<td><a href="../mon_compte.php?membre_id='. $membre['id_membre'] .'">' . $membre['pseudo'] . '</a></td>';
					}elseif($indice == 'categorie_id') {
						$resultatCateg = executeReq("SELECT * FROM categorie WHERE id_categorie = $information");
						$categ = $resultatCateg->fetch(PDO::FETCH_ASSOC);
						$contenu .= '<td>' . $categ['titre'] . '</td>';
					}elseif($indice == 'date_enregistrement') {
						$dateFr = new DateTime($information);
						$contenu .= '<td>' . $dateFr->format('d/m/Y à H:i:s') . '</td>';
					}else {
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
