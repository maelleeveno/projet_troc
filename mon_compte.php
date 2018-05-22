<?php
require_once('inc/init.inc.php');

// 1- Cas du visiteur non connecté :
if (!isConnected()) {
	header('location:connexion.php');  // on redirige le visiteur vers la page de connexion
	exit();
}

// 1- Contrôle de l'existence du membre demandé :

if (isset($_GET['membre_id'])) {
	// si l'indice "id_membre" existe, je peux sélectionner le membre en BDD :
	$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", array(':id_membre' => $_GET['membre_id']));
		
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
								   array(':id_membre' => $_GET['membre_id']));
		$membre = $resultat->fetch(PDO::FETCH_ASSOC); 	
		
} else {
	header('location:index.php');	
	exit();
}

// 2- Suppresion du membre : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_SESSION['membre']['id_membre'])) {
	$resultat = executeReq("DELETE FROM membre WHERE id_membre = :id_membre", 
							   array(':id_annonce' => $_SESSION['membre']['id_membre']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success">Votre profil a bien été supprimé !</div>';
	}
	
	$_GET['action'] = 'deconnexion';
	header('location:index.php');	
	exit();
}

// 5- Modification du mmbre
// Traitement du formulaire 
if(!empty($_POST)) {	
	
	if(!isset($_POST['pseudo']) || strlen($_POST['pseudo']) < 4 || strlen($_POST['pseudo']) > 20) {
		$contenu .= '<div class="bg-danger">Le pseudo est incorrect.</div>';	
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


	
	// Si $contenu est vide, c'est qu'il n'y a pas d'erreur sur le formulaire :
	if (empty($contenu)) {
		$membre = executeReq("SELECT * FROM membre WHERE pseudo = :pseudo", array(':pseudo' => $_POST['pseudo'])); 

		
		if($membre->rowCount() > 0) {
			$contenu .= '<div class="bg-danger">Pseudo indisponible, veuillez en choisir un autre.</div>';
		}

		// Enregistrement des modifications du membre : 
		executeReq("REPLACE INTO membre
		VALUE(:id_membre, :pseudo, :mdp, :nom, :prenom, :telephone, :email, :civilite, :statut, :date_enregistrement)", 
		// on doit bien mettre dans le même ordre que la table "annonce" de la BDD car on n'a pas spécifié les champs concernés dans une première paire de parenthèses. 
		
		array( ':id_membre' 			=> $_SESSION['membre']['id_membre'], 
			   ':pseudo'				=> $_POST['pseudo'],
			   ':mdp'					=> $_SESSION['membre']['mdp'],
			   ':nom'					=> $_POST['nom'],
			   ':prenom'				=> $_POST['prenom'],
			   ':telephone'				=> $_POST['telephone'],
			   ':email'					=> $_POST['email'],
			   ':civilite'				=> $_SESSION['membre']['civilite'],
			   ':statut'				=> $_SESSION['membre']['statut'],
			   ':date_enregistrement'	=> $_SESSION['membre']['date_enregistrement']
		));

$contenu .= '<div class="bg-success">Vos modifications ont été enregistrées !</div>';

// header('location:mon_compte.php?membre_id=' . echo $_SESSION['membre']['id_membre'] .'');

	}
}

//debug($_POST);

// -------------- Affichage ----------

// 4- Préparation du profil à afficher :
// debug($_SESSION);
	
	// On récupère le membre : 
	$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", 
							array(':id_membre' => $_GET['membre_id']));
	$membre = $resultat->fetch(PDO::FETCH_ASSOC); 

	
	// On récupère la note moyenne du membre : 
	$resultat2 = executeReq("SELECT ROUND(AVG(note.note), 1) AS 'moyenne', COUNT(note.note) AS 'nbDeNotes' FROM note, membre WHERE note.membre_id2 = membre.id_membre AND membre.id_membre = :membre_id", array('membre_id' => $_GET['membre_id']));
	$noteMoyenne = $resultat2->fetch(PDO::FETCH_ASSOC);

	if(isConnected() && $_GET['membre_id'] == $_SESSION['membre']['id_membre']) {
		$contenu .= '<p class="bg-success" style="text-align: center;">Bonjour <strong>' . $_SESSION['membre']['pseudo'] . '</strong> ! </p>';
		$contenu .= '<div><h3>Voici vos informations de profil';
		$contenu .= '<a href="?action=modification&membre_id='. $membre['id_membre'] .'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
		$contenu .= '<a href="?action=suppression&membre_id='. $membre['id_membre'] .'"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
		$contenu .= '</h3><hr />';

		$contenu .= '<p>Pseudo : ' . $membre['pseudo'] . '</p>';
		$contenu .= '<p>Nom : ' . $membre['nom'] . '</p>';
		$contenu .= '<p>Prénom : ' . $membre['prenom'] . '</p>';
		$contenu .= '<p>Téléphone : ' . $membre['telephone'] . '</p>';
		$contenu .= '<p>Email : ' . $membre['email'] . '</p>';
		$contenu .= '</div><br />';



		$contenu .= '<p>Note moyenne : ' . $noteMoyenne['moyenne'] . ' / 5 (sur '. $noteMoyenne['nbDeNotes'] .' notes reçues)</p>';
		$contenu .= '<h4>Vos avis reçus : </h4><hr />';

		$resultat3 = executeReq("SELECT * FROM note WHERE membre_id2 = :membre_id", array('membre_id' => $_GET['membre_id'])); 
		while($avis = $resultat3->fetch(PDO::FETCH_ASSOC)) {
			$resultat = executeReq("SELECT * FROM membre WHERE id_membre IN (SELECT id_membre FROM note WHERE id_membre = membre_id1)");
			$membre1 = $resultat->fetch(PDO::FETCH_ASSOC);
	
			$contenu .= '<p><strong>Avis déposé par '. $membre1['pseudo'] . ' le ' . $avis['date_enregistrement'] .'</strong></p>';
			$contenu .= '<p>Note : '. $avis['note'] .' / 5</p>'; 
			$contenu .= '<p>'. $avis['avis'] .'</p><hr />'; 
		}


		
	} else{
		$contenu .= '<div><h3>Profil de '. $membre['pseudo'] .'</h3><br />';
		$contenu .= '<p>Pseudo : ' . $membre['pseudo'] . '</p>';
		$contenu .= '<p>Nom : ' . $membre['nom'] . '</p>';
		$contenu .= '<p>Prénom : ' . $membre['prenom'] . '</p>';
		$contenu .= '<p>Note moyenne : ' . $noteMoyenne['moyenne'] . ' / 5 (sur '. $noteMoyenne['nbDeNotes'] .' notes reçues)</p>';
		$contenu .= '</div><br />';

		$contenu .= '<button class="btn"><a href="avis.php?membre_id='. $membre['id_membre'] .'">Laisser un avis à '. $membre['pseudo'] .' </a></button><br /><br />';

		$contenu .= '<h4>Les avis reçus par '. $membre['pseudo'] .' : </h4><hr />';

		$resultat3 = executeReq("SELECT * FROM note WHERE membre_id2 = :membre_id", array('membre_id' => $_GET['membre_id'])); 
		while($avis = $resultat3->fetch(PDO::FETCH_ASSOC)) {
			$resultat = executeReq("SELECT * FROM membre WHERE id_membre IN (SELECT id_membre FROM note WHERE id_membre = membre_id1)");
			$membre1 = $resultat->fetch(PDO::FETCH_ASSOC);
	
			$contenu .= '<p><strong>Avis déposé par '. $membre1['pseudo'] . ' le ' . $avis['date_enregistrement'] .'</strong></p>';
			$contenu .= '<p>Note : '. $avis['note'] .' / 5</p>'; 
			$contenu .= '<p>'. $avis['avis'] .'</p><hr />'; 
			
		}

	} 




require_once('inc/haut.inc.php');
echo $contenu;

// 2- Formulaire HTML d'une annonce :
if (isset($_GET['action']) && ($_GET['action'] == 'modification')) :	// Les deux points ':' et le 'endif' permettent d'enlever les accolades.
	// si je suis en ajout ou en modification d'annonce, j'affiche le formulaire. 
	
		// 8- Formulaire de modification d'un annonce avec présaisie des valeurs :
		if(isset($_SESSION['membre']['id_membre'])) {
			// si existe l'id_membre, je peux sélectionner l'annonce en BDD : 
			$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre", 
									   array(':id_membre' => $_SESSION['membre']['id_membre']));
			$membre_actuel = $resultat->fetch(PDO::FETCH_ASSOC); 	 
		}
?>

<h2>Complétez le formulaire pour modifier votre profil</h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">
	<label for="pseudo">Pseudo</label>
	<input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $membre_actuel['pseudo']; ?>" /><br />
	
	<label for="nom">Nom</label>
	<input type="text" name="nom" id="nom" class="form-control" value="<?php echo $membre_actuel['nom']; ?>" /><br />
	
	<label for="prenom">Prénom</label>
	<input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $membre_actuel['prenom']; ?>" /><br />
	
	<label for="telephone">Téléphone</label>
	<input type="text" name="telephone" id="telephone" class="form-control" value="<?php echo $membre_actuel['telephone']; ?>" /><br />
	
	<label for="email">Email</label>
	<input type="text" name="email" id="email" class="form-control" value="<?php echo $membre_actuel['email']; ?>" /><br />
	
	<input type="submit" value="Enregistrer" name="inscription" class="btn" />
</form>

<?php
endif;
require_once('inc/bas.inc.php');
?>