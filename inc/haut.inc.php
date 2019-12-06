<?php 

// ----------- TRAITEMENT -------------

// 2. Déconnexion de l'internaute : 
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion' ) {
	// si l'internaute demande la déconnexion : 
	unset($_SESSION['membre']);	// supprime l'indice membre de $_SESSION avec toutes les infos du membre 
}

// 3. Cas de l'internaute déjà connecté :
/* if(isConnected()) {
	// si le membre est déjà connecté, on ne lui propose pas le formulaire de connexion mais on le redirige vers son profil : 
	header('location:mon_compte.php?membre_id='. $_SESSION['membre']['id_membre'] .'');	// redirection vers la page profil
	exit();	// on sort du script 
} */

// 1. Traitement du formulaire : 
if(isset($_POST['connexion'])) { // -> si le formulaire est posté
	// validation du formulaire :
	if(empty($_POST['pseudo1'])) {
		// si pseudo vide OU non défini :
		$contenu .= '<div class="bg-danger text-center">Le pseudo est requis</div>';
	}
	if(empty($_POST['mdp1'])) {
		// si mot de passe vide OU non défini :
		$contenu .= '<div class="bg-danger text-center">Le mot de passe est requis</div>';
	}
	
	if(empty($contenu)) {
		// si $contenu est vide, c'est qu'il n'y a pas d'erreurs : on peut rechercher le membre en BDD : 
		$mdp1 = md5($_POST['mdp1']);	// Pour comparer deux clés identiques (car on a crypté le mot de passe à l'inscription).
		$resultat = executeReq("SELECT * FROM membre WHERE pseudo = :pseudo AND mdp = :mdp", 
								   array(':pseudo' => $_POST['pseudo1'], 
										 ':mdp' 	 => $mdp1 ));
		
		if ($resultat->rowCount() == 0) {
			// si pas de lignes, il n'y a pas de correspondance entre le loggin et le mdp en BDD (car il y a un AND dans la requête) :
			$contenu .= '<div class="bg-danger text-center">Erreur est sur les identifiants.</div>';	
		} else {
			// sinon (s'il y a une ligne) c'est qu'il y a correspondance entre les deux on va connecter le membre :
			$membre = $resultat->fetch(PDO::FETCH_ASSOC);	// On fait un fetch() car $resultat est un objet PDOSTATEMENT -> $membre devient un array associatif
															// On ne fait pas de boucle car on est certain d'avoir un seul membre
			$_SESSION['membre'] = $membre;	// Nous créons une session avec les infos du membre venant de la BDD 
			
			header('location:mon_compte.php?membre_id='. $membre['id_membre'] .'');	// Une fois la session créée, on redirige l'internaute vers le script indiqué après location: -> sa page de profil
											// header() renvoie une entête au navigateur qui demande la page profil.php. Puis le serveur reçoit cette demande et lui envoie la page. 
			exit(); // On sort du script lorsque l'internaute est connecté et est redirigé.
			
		}
	} // fin du if(empty($contenu))
}




?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>LE BON TROC</title>
		<meta type="description" content="Site de petite annonces en ligne de particulier à particulier" />

		<!-- Favicon -->
		<link rel="shortcut icon" href="<?php echo RACINE_SITE . 'inc/img/favicon_lbt.ico'; ?>">
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Bootstrap -->
		<link rel="stylesheet" href="<?php echo RACINE_SITE . 'bootstrap/css/bootstrap.css'; ?>" />

		<!-- Fichier style -->
		<link rel="stylesheet" href="<?php echo RACINE_SITE . 'inc/css/style.css'; ?>" /> 

		<!-- Fichier Js -->
		<script src="<?php echo RACINE_SITE . 'inc/js/script.js'; ?>"></script>

		<!--[if lt IE 9]> -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>	
		
		<!-- JQuery -->
		<script src="<?php echo RACINE_SITE . 'bootstrap/js/jquery-3.3.1.js'; ?>"></script>
		<script src="<?php echo RACINE_SITE . 'bootstrap/js/bootstrap.js'; ?>"></script>

	</head>


	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					
					<!-- Nom du site -->
					<a class="navbar-brand" href="<?php echo RACINE_SITE . 'index.php'; ?>">LE BON TROC</a>
				</div>
				
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav nav-fill">
						<li class="nav-item"><a href="<?php echo RACINE_SITE . 'index.php'; ?>">Accueil</a></li>
						<li class="nav-item"><a href="<?php echo RACINE_SITE . 'contact.php'; ?>">Contact</a></li>

						<?php 
						
						// Menu du membre connecté
						if (isConnected()) {
							echo '<li class="nav-item dropdown">';
								echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Mon compte <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></a>';
								echo '<ul class="dropdown-menu">';
									echo '<li><a href="' . RACINE_SITE . 'mon_compte.php?membre_id='. $_SESSION['membre']['id_membre'] .'">Mon profil</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'mes_annonces.php">Mes annonces</a></li>';
								echo '</ul>';
							echo '</li>';
							echo '<li class="nav-item"><a href="' . RACINE_SITE . 'index.php?action=deconnexion">Déconnexion</a></li>';
						} else {
							echo '<li class="nav-item"><a href="' . RACINE_SITE . 'inscription.php">Inscription</a></li>';			
							echo '<li class="nav-item"><a href="" data-toggle="modal" data-target="#modalConnexion">Connexion</a></li>';			
						}

						

						// Menu de l'admin connecté
						if (isConnectedAndAdmin()) {
							echo '<li class="nav-item dropdown">';
								echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Outils de gestion <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></a>';
								echo '<ul class="dropdown-menu">';
									echo '<li><a href="' . RACINE_SITE . 'admin/gestion_membres.php" class="dropdown-item">Gestion des membres</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'admin/gestion_annonces.php" class="dropdown-item">Gestion des annonces</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'admin/gestion_categories.php" class="dropdown-item">Gestion des catégories</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'admin/gestion_notes.php" class="dropdown-item">Gestion des notes et avis</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'admin/gestion_commentaires.php" class="dropdown-item">Gestion des commentaires</a></li>';
									echo '<li><a href="' . RACINE_SITE . 'admin/statistiques.php" class="dropdown-item">Statistiques</a></li>';
								echo '</ul>';
							echo '</li>';
						} 
						
						?>


					</ul>
				</div>
			</div>
		</nav>


		<!-- Page Content -->
		<div class="container" style="min-height: 80vh; margin-top: 70px;">

	
		<!-- Modal -->
		<div id="modalConnexion" class="modal fade" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title text-center">Connexion</h2>
					</div>

					<div class="modal-body">
						<form method="post" action="">
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
								<input type="text" name="pseudo1" id="pseudo1" class="form-control" autofocus placeholder="Votre pseudo" aria-describedby="basic-addon1">
							</div><br />

							<div class="input-group">
								<span class="input-group-addon" id="basic-addon2"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
								<input type="password" name="mdp1" id="mdp1" class="form-control" placeholder="Votre mot de passe" aria-describedby="basic-addon2">
							</div><br />

							<input type="submit" value="Se connecter" name="connexion" class="btn align-center" />
						</form>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>



