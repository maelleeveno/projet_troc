<?php
require_once('inc/init.inc.php');

//-------------------- TRAITEMENT --------------------
// 1- Contrôle de l'existence du membre demandé :

if (isset($_GET['id_membre'])) {
	// si l'indice "id_membre" existe, je peux sélectionner le membre en BDD :
	$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", array(':id_membre' => $_GET['id_membre']));
		
	if ($resultat->rowCount() == 0) {
		// s'il n'y a pas de ligne dans le jeu de résultat, c'est que le membre n'est pas ou plus en BDD :
        header('location:index.php');
        $contenu .= '<div class="bg-danger">Le membre recherché n\'existe plus.</div>';	
		exit();
	}
	
	// 2- préparation des variables d'affichage des infos du membre :
	$membre = $resultat->fetch(PDO::FETCH_ASSOC);
	// debug($membre);
	extract($membre);  // crée des variables nommées comme les indices de l'array et qui prennent pour valeur les valeurs correspondantes dans l'array. On peut faire extract car on n'est pas dans une boucle.

		// si existe l'id_membre, je peux sélectionner le membre : 
		$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", 
								   array(':id_membre' => $_GET['id_membre']));
		$membre = $resultat->fetch(PDO::FETCH_ASSOC); 	
		
} else {
	// l'indice "id_produit" n'existant pas, je redirige l'internaute vers la boutique :
	header('location:index.php');	
	exit();
}


// -------------- Affichage ----------

	// On récupère le membre : 
	$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", 
							array(':id_membre' => $_GET['id_membre']));
	$membre = $resultat->fetch(PDO::FETCH_ASSOC); 
	
	// On récupère la note moyenne du membre : 
	$resultat2 = executeReq("SELECT ROUND(AVG(note.note), 1) AS 'moyenne', COUNT(note.note) AS 'nbDeNotes' FROM note, membre WHERE note.membre_id2 = membre.id_membre ");
	$noteMoyenne = $resultat2->fetch(PDO::FETCH_ASSOC);

	$contenu .= '<div><h3>Profil de '. $membre['pseudo'] .'</h3><br />';

    $contenu .= '<p>Pseudo : ' . $membre['pseudo'] . '</p>';
	$contenu .= '<p>Nom : ' . $membre['nom'] . '</p>';
    $contenu .= '<p>Prénom : ' . $membre['prenom'] . '</p>';
    $contenu .= '<p>Note moyenne : ' . $noteMoyenne['moyenne'] . ' / 5 (sur '. $noteMoyenne['nbDeNotes'] .' notes reçues)</p>';
    $contenu .= '</div><br />';
    
    if(isConnected()) {
        $contenu .= '<button><a href="avis.php?id_membre='. $membre['id_membre'] .'">Laisser un avis à '. $membre['pseudo'] .' </a></button><br /><br />';
    }

	$contenu .= '<h4>Les avis reçus par '. $membre['pseudo'] .' : </h4><hr />';
	

		$resultat3 = executeReq("SELECT * FROM note"); 
		while($avis = $resultat3->fetch(PDO::FETCH_ASSOC)) {

			$resultat1 = executeReq("SELECT * FROM membre WHERE id_membre IN (SELECT id_membre FROM note WHERE id_membre = membre_id1)");
			$membre1 = $resultat1->fetch(PDO::FETCH_ASSOC);

			$contenu .= '<p><strong>Avis déposé par '. $membre1['pseudo'] . ' le ' . $avis['date_enregistrement'] .'</strong></p>';
			$contenu .= '<p>Note : '. $avis['note'] .' / 5</p>'; 
			$contenu .= '<p>'. $avis['avis'] .'</p><hr />'; 
		}


		



require_once('inc/haut.inc.php');
echo $contenu;


require_once('inc/bas.inc.php');
?>