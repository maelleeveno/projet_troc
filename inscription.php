<?php

// fichier init : 
require_once('inc/init.inc.php');

// ----------------------- TRAITEMENTS PHP -----------------------

if(!empty($_POST['pseudo']) || !empty($_POST['mdp']) || !empty($_POST['nom']) || !empty($_POST['prenom']) || !empty($_POST['telephone']) || !empty($_POST['mail']) || !empty($_POST['civilite']) ) {	
	
	if(!isset($_POST['pseudo']) || strlen($_POST['pseudo']) < 4 || strlen($_POST['pseudo']) > 20) {
		$contenu .= '<div class="bg-danger text-center">Le pseudo est incorrect.</div>';	
	}
	if(!isset($_POST['mdp']) || strlen($_POST['mdp']) < 4 || strlen($_POST['mdp']) > 20) {
		$contenu .= '<div class="bg-danger text-center">Le mot de passe est incorrect.</div>';
	}
	if(!isset($_POST['nom']) || strlen($_POST['nom']) < 2 || strlen($_POST['nom']) > 20) {
		$contenu .= '<div class="bg-danger text-center">Le nom doit contenir  entre 2 et 20 caractères.</div>';
	}	
	if(!isset($_POST['prenom']) || strlen($_POST['prenom']) < 2 || strlen($_POST['prenom']) > 20) {
		$contenu .= '<div class="bg-danger text-center">Le prénom doit contenir  entre 2 et 20 caractères.</div>';
	}	

	// Vérification Email : 
	if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$contenu .= '<div class="bg-danger text-center">Email incorrect.</div>';
	}
	
	// Téléphone : 
	if(!isset($_POST['telephone']) || !preg_match('/^[0-9]{10}$/', $_POST['telephone']) ) {	
		$contenu .= '<div class="bg-danger text-center">Numéro de téléphone incorrect.</div>';
	}
	
	// Vérification de la civilité : 
	if(!isset($_POST['civilite']) || ($_POST['civilite'] != 'm' && $_POST['civilite'] != 'f')) {
		$contenu .= '<div class="bg-danger text-center">Civilité incorrecte.</div>';
	}

	
	// Si $contenu est vide, c'est qu'il n'y a pas d'erreur sur le formulaire :
	if (empty($contenu)) {
		$membre = executeReq("SELECT * FROM membre WHERE pseudo = :pseudo", array(':pseudo' => $_POST['pseudo'])); 
		
		if($membre->rowCount() > 0) {
			$contenu .= '<div class="bg-danger text-center">Pseudo indisponible, veuillez en choisir un autre.</div>';
		} else {
			
			$mdp = md5($_POST['mdp']);	
			executeReq("INSERT INTO membre (pseudo, mdp, nom, prenom, telephone, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :telephone, :email, :civilite, 0, NOW())", 
						   array(':pseudo' 		=> $_POST['pseudo'], 
								 ':mdp' 		=> $mdp,
								 ':nom' 		=> $_POST['nom'],
								 ':prenom'		=> $_POST['prenom'],
								 ':telephone'	=> $_POST['telephone'],
								 ':email' 		=> $_POST['email'],
								 ':civilite' 	=> $_POST['civilite']
						   ));
							$contenu .= '<div class="bg-success text-center">Vous êtes inscrit. Cliquez sur "Connexion" dans le menu ou <a href="index.php">revenir à l\'accueil pour se connecter.</a></div>';
							
		}
		
	} // fin du if (empty($contenu))

}	// fin du if(!emplty($_POST))


// -------------------------- AFFICHAGE --------------------------
require_once('inc/haut.inc.php');
echo $contenu; 	// pour afficher des messages

// Affichage du post :
// debug($_POST);
?>

<h2 class="text-center">Inscription</h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">
	
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
		<input type="text" name="pseudo" id="pseudo" class="form-control" placeholder="Votre pseudo" autofocus aria-describedby="basic-addon1" value="<?php echo $_POST['pseudo'] ?? ''; ?>">
	</div><br />

	<div class="input-group">
		<span class="input-group-addon" id="basic-addon2"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
		<input type="password" name="mdp" id="mdp" class="form-control" placeholder="Votre mot de passe" aria-describedby="basic-addon2" value="<?php echo $_POST['mdp'] ?? ''; ?>">
	</div><br />
	
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon3"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
		<input type="text" name="nom" id="nom" class="form-control" placeholder="Votre nom" aria-describedby="basic-addon3" value="<?php echo $_POST['nom'] ?? ''; ?>">
	</div><br />

	<div class="input-group">
		<span class="input-group-addon" id="basic-addon4"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
		<input type="text" name="prenom" id="prenom" class="form-control" placeholder="Votre prénom" aria-describedby="basic-addon4" value="<?php echo $_POST['prenom'] ?? ''; ?>">
	</div><br />
	
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon5"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></span>
		<input type="text" name="telephone" id="telephone" class="form-control" placeholder="Votre téléphone" aria-describedby="basic-addon5" value="<?php echo $_POST['telephone'] ?? ''; ?>">
	</div><br />

	<div class="input-group">
		<span class="input-group-addon" id="basic-addon6"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></span>
		<input type="text" name="email" id="email" class="form-control" placeholder="Votre email" aria-describedby="basic-addon6" value="<?php echo $_POST['email'] ?? ''; ?>">
	</div><br />
	
	<label>Civilité</label>
	<input type="radio" name="civilite" id="homme" value="m" checked /><label for="homme">Homme</label>
	<input type="radio" name="civilite" id="femme" value="f" <?php if(isset($_POST['civilite']) && $_POST['civilite'] == 'f') echo 'checked'; ?> /><label for="femme">Femme</label><br /><br />
	
	<input type="submit" value="S'inscrire" name="inscription" class="btn" />
</form>


<?php

// footer :
require_once('inc/bas.inc.php');


?>