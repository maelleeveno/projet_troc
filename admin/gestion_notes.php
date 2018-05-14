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
	$resultat = executeReq("SELECT * FROM note"); // sélectionne tous les produits 
	
	$contenu .= 'Nombre de notes enregistrées :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>id_note</th>';
			$contenu .= '<th>note</th>';
			$contenu .= '<th>avis</th>';
			$contenu .= '<th>date_enregistrement</th>';
			$contenu .= '<th>membre_id1</th>';
			$contenu .= '<th>membre_id2</th>';
			$contenu .= '<th>Action</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($note = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
                    foreach($note as $indice => $information) {
                     
                        $resultat1 = executeReq("SELECT * FROM membre WHERE id_membre IN (SELECT id_membre FROM note WHERE id_membre = membre_id1)");
                        $membre1 = $resultat1->fetch(PDO::FETCH_ASSOC);

                        $resultat2 = executeReq("SELECT * FROM membre WHERE id_membre IN (SELECT id_membre FROM note WHERE id_membre = membre_id2)");
                        $membre2 = $resultat2->fetch(PDO::FETCH_ASSOC);

                        if($indice == 'note') {	
                            $contenu .= '<td> '. $information .' / 5 </td>';
                        } elseif($indice == 'membre_id1') {
                            $contenu .= '<td>'. $membre1['pseudo'] .'</td>';
                        } elseif($indice == 'membre_id2') {
                            $contenu .= '<td>'. $membre2['pseudo'] . '</td>';
                        }else {
                            // pour les autres champs
                            $contenu .= '<td>'. $information .'</td>';
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