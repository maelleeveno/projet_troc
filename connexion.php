<?php 
require_once('inc/init.inc.php');

// ----------- TRAITEMENT -------------

// 2. Déconnexion de l'internaute : 
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion' ) {
	// si l'internaute demande la déconnexion : 
	unset($_SESSION['membre']);	// supprime l'indice membre de $_SESSION avec toutes les infos du membre 
}

// 3. Cas de l'internaute déjà connecté :
if(isConnected()) {
	// si le membre est déjà connecté, on ne lui propose pas le formulaire de connexion mais on le redirige vers son profil : 
	header('location:mon_compte.php');	// redirection vers la page profil
	exit();	// on sort du script 
} 

// 1. Traitement du formulaire : 
if(!empty($_POST)) { // -> si le formulaire est posté
	// validation du formulaire :
	if(empty($_POST['pseudo'])) {
		// si pseudo vide OU non défini :
		$contenu .= '<div class="bg-danger">Le pseudo est requis</div>';
	}
	if(empty($_POST['mdp'])) {
		// si mot de passe vide OU non défini :
		$contenu .= '<div class="bg-danger">Le mot de passe est requis</div>';
	}
	
	if(empty($contenu)) {
		// si $contenu est vide, c'est qu'il n'y a pas d'erreurs : on peut rechercher le membre en BDD : 
		$mdp = md5($_POST['mdp']);	// Pour comparer deux clés identiques (car on a crypté le mot de passe à l'inscription).
		$resultat = executeReq("SELECT * FROM membre WHERE pseudo = :pseudo AND mdp = :mdp", 
								   array(':pseudo' => $_POST['pseudo'], 
										 ':mdp' 	 => $mdp ));
		
		if ($resultat->rowCount() == 0) {
			// si pas de lignes, il n'y a pas de correspondance entre le loggin et le mdp en BDD (car il y a un AND dans la requête) :
			$contenu .= '<div class="bg-danger">Erreur est sur les identifiants.</div>';	
		} else {
			// sinon (s'il y a une ligne) c'est qu'il y a correspondance entre les deux on va connecter le membre :
			$membre = $resultat->fetch(PDO::FETCH_ASSOC);	// On fait un fetch() car $resultat est un objet PDOSTATEMENT -> $membre devient un array associatif
															// On ne fait pas de boucle car on est certain d'avoir un seul membre
			$_SESSION['membre'] = $membre;	// Nous créons une session avec les infos du membre venant de la BDD 
			
			header('location:mon_compte.php');	// Une fois la session créée, on redirige l'internaute vers le script indiqué après location: -> sa page de profil
											// header() renvoie une entête au navigateur qui demande la page profil.php. Puis le serveur reçoit cette demande et lui envoie la page. 
			exit(); // On sort du script lorsque l'internaute est connecté et est redirigé.
			
		}
	} // fin du if(empty($contenu))
}




// -------------- AFFICHAGE ------------

require_once('inc/haut.inc.php');
echo $contenu;
?>

<h2 class="text-center">Connexion</h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
		<input type="text" name="pseudo" id="pseudo" class="form-control" placeholder="Votre pseudo" aria-describedby="basic-addon1">
	</div><br />

	<div class="input-group">
		<span class="input-group-addon" id="basic-addon2"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
		<input type="password" name="mdp" id="mdp" class="form-control" placeholder="Votre mot de passe" aria-describedby="basic-addon2">
	</div><br />
	
	<input type="submit" value="Se connecter" class="btn align-center" />
</form>



<?php 
require_once('inc/bas.inc.php');


?>