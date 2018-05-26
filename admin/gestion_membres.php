<?php
//-------------------------------------------
// EXERCICE
//-------------------------------------------

/*   Vous allez créer la page de gestion des membres dans le back-office :
	 1- Seul l’administrateur doit avoir accès à cette page. Les membres classiques seront redirigés vers la page connexion.php  
	 2- Afficher dans cette page tous les membres inscrits sur le site sous forme de table HTML, avec toutes les infos du membre sauf son mot de passe.  
     3- Dans cette même page, ajoutez la possibilité à l’administrateur de pouvoir supprimer un membre inscrit au site, sauf lui-même ! 
	 4- Donner la possibilité à l'administrateur de modifier le statut des membres pour en faire un admin ou un membre, sauf lui-même.
*/	

require_once("../inc/init.inc.php");

// 1- Vérification si Admin :
if(!isConnectedAndAdmin())
{
	header("location:../connexion.php");
	exit();
}

// 3- Suppression d'un membre :
if(isset($_GET['action']) && $_GET['action'] == "supprimer_membre" && isset($_GET['id_membre']))
{	// on ne peut pas supprimer son propre profil :
	if ($_SESSION['membre']['id_membre'] != $_GET['id_membre']) {
		executeReq("DELETE FROM membre WHERE id_membre=:id_membre", array(':id_membre' => $_GET['id_membre']));
	} else {
		$contenu .= '<div class="bg-danger">Vous ne pouvez pas supprimer votre propre profil ! </div>';
	}
	
}

// 4- Modification statut membre :
if(isset($_GET['action']) && $_GET['action'] == "modifier_statut" && isset($_GET['id_membre']) && isset($_GET['statut']))
{
	if ($_GET['id_membre'] != $_SESSION['membre']['id_membre']) {
		$statut = ($_GET['statut'] == 0) ? 1 : 0;	// si statut = 0 alors il devient 1 sinon devient 0
		executeReq("UPDATE membre SET statut = '$statut' WHERE id_membre=:id_membre", array(':id_membre' => $_GET['id_membre']));
	} else {
		$contenu .= '<div class="bg-danger">Vous ne pouvez pas modifier votre propre profil ! </div>';	
	}
}


// 2- Préparation de l'affichage :
$resultat = executeReq("SELECT id_membre, pseudo, nom, prenom, telephone, email, date_enregistrement, civilite, statut FROM membre");
$contenu .= '<h3> Membres inscrits </h3>';
$contenu .=  "Nombre de membre(s) : " . $resultat->rowCount();

$contenu .=  '<table class="table"> <tr>';
		// Affichage des entêtes :
		$contenu .=  '<th> id_membre </th>';
		$contenu .=  '<th> pseudo </th>';
		$contenu .=  '<th> nom </th>';
		$contenu .=  '<th> prenom </th>';
		$contenu .=  '<th> telephone </th>';
		$contenu .=  '<th> email </th>';
		$contenu .=  '<th> Date d\'inscription </th>';
		$contenu .=  '<th> civilite </th>';
		$contenu .=  '<th> statut </th>';
				
		$contenu .=  '<th> Supprimer </th>';
		$contenu .=  '<th> Modifier Statut </th>';
		$contenu .=  '<th>Consulter le profil</th>';
		$contenu .=  '</tr>';

		// Affichage des lignes :
		while ($membre = $resultat->fetch(PDO::FETCH_ASSOC)){
			$contenu .=  '<tr>';
				foreach ($membre as $indice => $information){
					if($indice == 'date_enregistrement') {
						$dateFr = new DateTime($information);
						$contenu .= '<td>' . $dateFr->format('d/m/Y à H:i:s') . '</td>';
					} else {
						$contenu .=  '<td>' . $information . '</td>';
					}	
				}
				$contenu .=  '<td><a href="?action=supprimer_membre&id_membre=' . $membre['id_membre'] . '" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer ce membre?\'));"> <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
				$contenu .=  '<td><a href="?action=modifier_statut&id_membre=' . $membre['id_membre'] . '&statut='. $membre['statut'] .'"> <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> </a></td>';
				$contenu .= '<td><a href="../mon_compte.php?membre_id='. $membre['id_membre'] .'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></td>';

			$contenu .=  '</tr>';
		}
$contenu .=  '</table>';


//--------------------------- Affichage ---------------------
require_once("../inc/haut.inc.php");
echo $contenu;
require_once("../inc/bas.inc.php");



