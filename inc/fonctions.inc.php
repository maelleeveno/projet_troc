<?php

// Fonction de debug : 
function debug($param) {
	echo '<div id="debug" style="margin-top: 70px; border: red 1px solid;">';
		echo '<pre>'; 
			print_r($param);
		echo '</pre>';
	echo '</div>';
}


function isConnected() {
	// Cette fonction indique si le membre est connecté
	if(isset($_SESSION['membre'])) {
		return true;
	} else {
		return false;
	}
}

// Fonction indiquant que l'internaute est connecté ET qu'il est admin : 
function isConnectedAndAdmin() {
	if ( isConnected() && $_SESSION['membre']['statut'] == 1 ) {
		return TRUE;
	} else {
		return FALSE;
	}
}

// Fonction pour exécuter des requêtes : 
function executeReq($req, $param = array()) {	// $req -> sous forme de "string" --- $param -> marqueurs associés sous forme d'ARRAY
	
	// si $param n'est pas vide, on échappe les caractères sépciaux : 
	if(!empty($param)) {
		foreach ($param as $indice => $valeur) {
			$param[$indice] = htmlspecialchars($valeur, ENT_QUOTES);
		}
	}

	// Permet d'avoir accès à la variable $pdo globale (qui permet la connexion à la BDD) dans l'environnement local de la fonction executeReq :
	global $pdo;	
	$resultat = $pdo->prepare($req);
	$resultat->execute($param);
	return($resultat);	
}

?>




