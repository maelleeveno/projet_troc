<?php

// fichier init : 
require_once('inc/init.inc.php');

// ----------------------- TRAITEMENTS PHP -----------------------

if(!empty($_POST)) {	
	
	if(!isset($_POST['pseudo']) || strlen($_POST['pseudo']) < 4 || strlen($_POST['pseudo']) > 20) {
		$contenu .= '<div class="bg-danger">Le pseudo est incorrect.</div>';	
	}
	if(!isset($_POST['mdp']) || strlen($_POST['mdp']) < 4 || strlen($_POST['mdp']) > 20) {
		$contenu .= '<div class="bg-danger">Le mot de passe est incorrect.</div>';
	}
	if(!isset($_POST['nom']) || strlen($_POST['nom']) < 2 || strlen($_POST['nom']) > 20) {
		$contenu .= '<div class="bg-danger">Le nom doit contenir  entre 2 et 20 caractères.</div>';
	}	
	if(!isset($_POST['prenom']) || strlen($_POST['prenom']) < 2 || strlen($_POST['prenom']) > 20) {
		$contenu .= '<div class="bg-danger">Le prénom doit contenir  entre 2 et 20 caractères.</div>';
	}	

	// Vérification Email : 
	if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$contenu .= '<div class="bg-danger">Email incorrect.</div>';
	}
	
	// Téléphone : 
	if(!isset($_POST['telephone']) || !preg_match('/^[0-9]{10}$/', $_POST['telephone']) ) {	
		$contenu .= '<div class="bg-danger">Numéro de téléphone incorrect.</div>';
	}
	
	// Vérification de la civilité : 
	if(!isset($_POST['civilite']) || ($_POST['civilite'] != 'm' && $_POST['civilite'] != 'f')) {
		$contenu .= '<div class="bg-danger">Civilité incorrecte.</div>';
	}

	
	// Si $contenu est vide, c'est qu'il n'y a pas d'erreur sur le formulaire :
	if (empty($contenu)) {
		$membre = executeReq("SELECT * FROM membre WHERE pseudo = :pseudo", array(':pseudo' => $_POST['pseudo'])); 
		
		if($membre->rowCount() > 0) {
			$contenu .= '<div class="bg-danger">Pseudo indisponible, veuillez en choisir un autre.</div>';
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
							$contenu .= '<div class="bg-success">Vous êtes inscrit. <a href="connexion.php">Cliquez ici pour vous connecter.</a></div>';
		}
		
	} // fin du if (empty($contenu))

}	// fin du if(!emplty($_POST))


// -------------------------- AFFICHAGE --------------------------
require_once('inc/haut.inc.php');
echo $contenu; 	// pour afficher des messages

// Affichage du post :
// debug($_POST);
?>

<h2>Complétez le formulaire pour vous inscrire</h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">
	<label for="pseudo">Pseudo</label>
	<input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $_POST['pseudo'] ?? ''; ?>" /><br />
	
	<label for="mdp">Mot de passe</label>
	<input type="password" name="mdp" id="mdp" class="form-control" value="<?php echo $_POST['mdp'] ?? ''; ?>" /><br />
	
	<label for="nom">Nom</label>
	<input type="text" name="nom" id="nom" class="form-control" value="<?php echo $_POST['nom'] ?? ''; ?>" /><br />
	
	<label for="prenom">Prénom</label>
	<input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $_POST['prenom'] ?? ''; ?>" /><br />
	
	<label for="telephone">Téléphone</label>
	<input type="text" name="telephone" id="telephone" class="form-control" value="<?php echo $_POST['telephone'] ?? ''; ?>" /><br />
	
	<label for="email">Email</label>
	<input type="text" name="email" id="email" class="form-control" value="<?php echo $_POST['email'] ?? ''; ?>" /><br />
	
	<label>Civilité</label>
	<input type="radio" name="civilite" id="homme" value="m" checked /><label for="homme">Homme</label>
	<input type="radio" name="civilite" id="femme" value="f" <?php if(isset($_POST['civilite']) && $_POST['civilite'] == 'f') echo 'checked'; ?> /><label for="femme">Femme</label><br />	
	
	<input type="submit" value="S'inscrire" name="inscription" class="btn" />
</form>


<?php

// footer :
require_once('inc/bas.inc.php');


?>