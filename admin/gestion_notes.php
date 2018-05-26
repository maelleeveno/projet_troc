<?php 

require_once('../inc/init.inc.php');

// ------------- TRAITEMENT ------------

// 1- Vérification que le membre est admin et est connecté : 
if(!isConnectedAndAdmin()) {
	// Si membre non connecté ou non admin, on le redirige vers la page de connexion :
	header('location:../connexion.php');	// on demande la page de connexion
	exit();	// on quitte le script.
} 


// 3- Suppresion de la note : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_note'])) {
	$resultat = executeReq("DELETE FROM note WHERE id_note = :id_note", 
							   array(':id_note' => $_GET['id_note']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success">La note a bien été supprimée !</div>';
	}
}

// 2- Affichage des notes
if(isConnectedAndAdmin()) {

	// $resultat = executeReq("SELECT * FROM note");
	$resultat = executeReq("SELECT * FROM note");

	$contenu .= '<h3>Gestion des notes et avis </h3>';
	
	$contenu .= 'Nombre de notes enregistrées :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>id_note</th>';
			$contenu .= '<th>note</th>';
			$contenu .= '<th>avis</th>';
			$contenu .= '<th>date_enregistrement</th>';
			$contenu .= '<th>membre acheteur</th>';
			$contenu .= '<th>membre vendeur</th>';
			$contenu .= '<th>Action</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($note = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
                    foreach($note as $indice => $information) {

                        if(empty($information)) {
							$contenu .= '<td>Membre supprimé</td>';
						}elseif($indice == 'id_note') {
							$contenu .= '<td> '. $information .' </td>';
						}elseif($indice == 'note') {
							$contenu .= '<td> '. $information .' / 5 </td>';
						}elseif($indice == 'avis') {
							$contenu .= '<td> '. $information .' </td>';
						}elseif($indice == 'date_enregistrement') {
							$dateFr = new DateTime($information);
							$contenu .= '<td>' . $dateFr->format('d/m/Y à H:i:s') . '</td>';
						}elseif($indice == 'membre_id1') {
							$resultat1 = executeReq("SELECT pseudo FROM membre WHERE id_membre = $information");
							$acheteur = $resultat1->fetch(PDO::FETCH_ASSOC);
							$contenu .= '<td>' . $acheteur['pseudo'] . '</td>';
						}elseif($indice == 'membre_id2') {
							$resultat2 = executeReq("SELECT pseudo FROM membre WHERE id_membre = $information");
							$vendeur = $resultat2->fetch(PDO::FETCH_ASSOC);
							$contenu .= '<td>' . $vendeur['pseudo'] . '</td>';
						}
                    }
							
						// Voir - modifier - suprimer 
						// glyphicon-eye-open pour voir
			$contenu .= '<td>
							<a href="?action=suppression&id_note='. $note['id_note'] .'"  onclick="return(confirm(\'Êtes-vous certain de vouloir supprimer cette annonce ?\'));" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
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