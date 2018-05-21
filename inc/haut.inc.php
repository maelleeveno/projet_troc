<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>LE BON TROC</title>
		<meta type="description" content="Site de petite annonces en ligne de particulier à particulier" />

		<!-- Favicon -->

		
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
						<li class="active nav-item"><a href="<?php echo RACINE_SITE . 'index.php'; ?>">Accueil</a></li>
						<li class="nav-item"><a href="<?php RACINE_SITE . 'contact.php'; ?>">Contact</a></li>

						<?php 
						
						// Menu du membre connecté
						if (isConnected()) {
							echo '<li class="nav-item dropdown">';
								echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Mon compte <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></a>';
								echo '<div class="dropdown-menu">';
									echo '<a href="' . RACINE_SITE . 'mon_compte.php">Mon profil</a><br />';
									echo '<a href="' . RACINE_SITE . 'mes_annonces.php">Mes annonces</a>';
								echo '</div>';
							echo '</li>';
							echo '<li class="nav-item"><a href="' . RACINE_SITE . 'connexion.php?action=deconnexion">Déconnexion</a></li>';
						} else {
							echo '<li class="nav-item"><a href="' . RACINE_SITE . 'inscription.php">Inscription</a></li>';			
							echo '<li class="nav-item"><a href="' . RACINE_SITE . 'connexion.php">Connexion</a></li>';			
						}

						// Menu de l'admin connecté
						if (isConnectedAndAdmin()) {
							echo '<li class="nav-item dropdown">';
								echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Outils de gestion <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></a>';
								echo '<div class="dropdown-menu">';
									echo '<a href="' . RACINE_SITE . 'admin/gestion_membres.php" class="dropdown-item">Gestion des membres</a><br />';
									echo '<a href="' . RACINE_SITE . 'admin/gestion_annonces.php" class="dropdown-item">Gestion des annonces</a><br />';
									echo '<a href="' . RACINE_SITE . 'admin/gestion_categories.php" class="dropdown-item">Gestion des catégories</a><br />';
									echo '<a href="' . RACINE_SITE . 'admin/gestion_notes.php" class="dropdown-item">Gestion des notes</a><br />';
									echo '<a href="' . RACINE_SITE . 'admin/gestion_commentaires.php" class="dropdown-item">Gestion des commentaires</a><br />';
									echo '<a href="' . RACINE_SITE . 'admin/statistiques.php" class="dropdown-item">Statistiques</a><br/>';
								echo '</div>';
							echo '</li>';
						} 
						
						?>
						
						<form class="navbar-form navbar-right inline-form">
							<div class="form-group">
								<input type="search" class="input-sm form-control" placeholder="Recherche">
								<button type="submit" class="btn btn-sm"><span class="glyphicon glyphicon-search"></span></button>
							</div>
						</form>

					</ul>
				</div>
			</div>
		</nav>

		<!-- Page Content -->
		<div class="container" style="min-height: 80vh; margin-top: 70px;">



