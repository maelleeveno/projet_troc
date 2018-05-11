<?php

require_once('inc/init.inc.php');

// ------------- TRAITEMENT ------------

// 1- Vérification que le membre est connecté : 
if(!isConnected()) {
	// Si membre non connecté ou non admin, on le redirige vers la page de connexion :
	header('location:connexion.php');	// on demande la page de connexion
	exit();	// on quitte le script.
}



// 7- Suppresion de l'annonce : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_annonce'])) {
	// si l'indice 'action' existe et qu'il vaut 'suppression' et qu'existe l'indice 'id_annonce', on va supprimer l'annonce : 
	$resultat = executeReq("DELETE FROM annonce WHERE id_annonce = :id_annonce", 
							   array(':id_annonce' => $_GET['id_annonce']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success">L\'annonce a bien été supprimée !</div>';
	}
	
	$_GET['action'] = 'affichage';	// permet de lancer l'affichage des annonces (cf. chapitre 6 ci-dessous).
}



// 4- Enregistrement de l'annonce en BDD 
if(!empty($_POST)) {	// si le formulaire est soumis
	// debug($_POST);	
	
		// Titre : 
		if(!isset($_POST['titre']) || strlen($_POST['titre']) < 4 || strlen($_POST['titre']) > 25) {
			$contenu .= '<div class="bg-danger">Le titre doit être compris entre 4 et 25 caractères. Veuillez saisir un titre correct.</div>';	
		}
		
		// Descriptions :
		if(!isset($_POST['description_courte']) || strlen($_POST['description_courte']) < 10 || strlen($_POST['description_courte']) > 50) {
			$contenu .= '<div class="bg-danger">La description doit comprendre entre 10 et 50 caractères. Veuillez recommencer votre saisie.</div>';
		}
		if(!isset($_POST['description_longue']) || strlen($_POST['description_longue']) < 10 || strlen($_POST['description_longue']) > 300) {
			$contenu .= '<div class="bg-danger">La description doit comprendre entre 10 et 300 caractères. Veuillez recommencer votre saisie.</div>';
		}
		
		// Code postal : 
		if(!isset($_POST['cp']) || !preg_match('/^[0-9]{5}$/', $_POST['cp']) ) {	
			$contenu .= '<div class="bg-danger">Code postal incorrect.</div>';
		}
		
		// Adresse :
		if(!isset($_POST['adresse']) || strlen($_POST['adresse']) < 5 || strlen($_POST['adresse']) > 50) {
			$contenu .= '<div class="bg-danger">L\'adresse doit comprendre entre 5 et 40 caractères. Veuillez recommencer votre saisie.</div>';
		}
	
	// Variable qui contiendra le chemin de la photo à insérer (cf. ci-dessous) 
	$photo_bdd = '';
	
	// 9- Fin du traitement de la photo :
	if(isset($_POST['photo_actuelle'])) {	// si exsite, c'est que nous sommes en train de modifier la photo : on la remet donc en BDD pour ne pas l'effacer :
		$photo_bdd = $_POST['photo_actuelle']; 
		
	}

	// 5- Traitement de la photo : 
	// debug($_FILES);
	if(!empty($_FILES['photo']['name'])) {	// Si une photo est uploadée, la valeur name n'est pas vide
	
		// On crée une variable pour le nom du fichier photo :
		$nom_photo = $_POST['id_annonce'] . '_' . $_FILES['photo']['name'];	// on crée un nom de fichier unique par référence de l'annonce
		
		// On crée une variable pour le chemin de la photo à mettre en BDD : 
		$photo_bdd = 'photo/' . $nom_photo;	// chemin relatif de la photo qui est enregistrée en BDD nécessaire aux balises <img> : "photo/nom_fichier.jpg"
		
		// On crée une variable pour le chemin absolu de la phot physique enregistrée sur notre serveur : 
		$photo_physique = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . $photo_bdd; // Chemin absolu complet depuis la racine du serveur pour enregistrer le fichier physique -> C:/wamp64/www/projet_back/photo/nomfichier.jpg (correspond pour nous en local à localhost + la constante créée dans 'init.inc.php' + le chemin de la photo dans la BDD). 
		// $_SERVER['DOCUMENT_ROOT'] est une superglobale qui fournit ici la racine du serveur sur lequel se trouve le site. 
		
		copy($_FILES['photo']['tmp_name'], $photo_physique);	// copie le fichier qui est temporairement dans $_FILES['photo']['tmp_name'] vers l'emplacement $photo_physique
		
	}
	
	debug($_POST);
	
	// Enregistrement de l'annonce : 
	executeReq("REPLACE INTO annonce
					VALUE(:id_annonce, :titre, :description_courte, :description_longue, :prix, :photo_bdd, :pays, :ville, :adresse, :cp, NOW(), :membre_id, :categorie_id)", 
					// on doit bien mettre dans le même ordre que la table "annonce" de la BDD car on n'a pas spécifié les champs concernés dans une première paire de parenthèses. 
					
					array( ':id_annonce' 			=> $_POST['id_annonce'], 
						   ':titre'					=> $_POST['titre'],
						   ':description_courte'	=> $_POST['description_courte'],
						   ':description_longue'	=> $_POST['description_longue'],
						   ':prix'					=> $_POST['prix'],
						   ':photo_bdd'				=> $photo_bdd,
						   ':pays'					=> $_POST['pays'],
						   ':ville'					=> $_POST['ville'],
						   ':adresse'				=> $_POST['adresse'],
						   ':cp'					=> $_POST['cp'],
						   ':membre_id'				=> $_SESSION['membre']['id_membre'],
						   ':categorie_id'			=> $_POST['categorie_id']
					));
	
	$contenu .= '<div class="bg-success">L\'annonce a bien été enregistrée.</div>';
	
	$_GET['action'] = 'affichage';	// Pour déclencher l'affichage de la table HTML avec tous les annoncess (cf. étape 6 ci-dessous)
	
}	// fin du if(!empty($_POST))

	
	
// 6- Affichage des annonces dans une table HTML : 
if(isset($_GET['action']) && $_GET['action'] == 'affichage') {
	// si on a demandé l'affichage de la table HTML 
	
	$resultat = executeReq("SELECT * FROM annonce WHERE membre_id = :membre_id", 
				array (':membre_id' => $_SESSION['membre']['id_membre'])); // sélectionne tous les annonces 
	
	$contenu .= 'Mes annonces publiées :  ' . $resultat->rowCount();
	$contenu .= '<table class="table">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th>id_annonce</th>';
			$contenu .= '<th>titre</th>';
			$contenu .= '<th>description_courte</th>';
			$contenu .= '<th>description_longue</th>';
			$contenu .= '<th>prix</th>';
			$contenu .= '<th>photo</th>';
			$contenu .= '<th>pays</th>';
			$contenu .= '<th>ville</th>';
			$contenu .= '<th>adresse</th>';
			$contenu .= '<th>cp</th>';
			$contenu .= '<th>date_enregistrement</th>';
			$contenu .= '<th>membre_id</th>';
			$contenu .= '<th>categorie</th>';
			$contenu .= '<th>Modifier</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($annonce = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
				// on parcourt les informations du tableau associatif $annonce : 
				foreach($annonce as $indice => $information) {

					if($indice == 'prix') {
						$contenu .= '<td>' . $information . ' €</td>';
					} elseif($indice == 'photo') {	// on met une balise <img /> pour la photo
						$contenu .= '<td><img src="'. $information .'" width="90" height="90"/></td>';
					} else {
						// pour les autres champs
						$contenu .= '<td>'. $information .'</td>';
					}

				}
						
			$contenu .= '<td>
							<a href="fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
							
							<a href="?action=modification&id_annonce='. $annonce['id_annonce'] .'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
							
							<a href="?action=suppression&id_annonce='. $annonce['id_annonce'] .'"  onclick="return(confirm(\'Êtes-vous certain de vouloir supprimer cette annonce ?\'));" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
						</td>';
			$contenu .= '</tr>';
		}			
	$contenu .= '</table>';
} // fin du if(isset($_GET['action']) && $_GET['action'] == 'affichage')
	


// ------------- AFFICHAGE ------------

require_once('inc/haut.inc.php');

// 2- Onglets 'ajout' et 'affichage des produits' :
echo 	'<ul class="nav nav-tabs">
			<li><a href="?action=affichage">Afficher mes annonces</a></li>
			<li><a href="?action=ajout">Ajouter une annonce</a></li>
		</ul>';

echo $contenu; // pour afficher les messages

// 3- Formulaire HTML d'une annonce :
if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :	// Les deux points ':' et le 'endif' permettent d'enlever les accolades.
// si je suis en ajout ou en modification d'annonce, j'affiche le formulaire. 

	// 8- Formulaire de modification d'un annonce avec présaisie des valeurs :
	if(isset($_GET['id_annonce'])) {
		// si existe l'id_annonce, je peux sélectionner l'annonce en BDD : 
		$resultat = executeReq("SELECT * FROM annonce WHERE id_annonce = :id_annonce", 
								   array(':id_annonce' => $_GET['id_annonce']));
		$annonce_actuelle = $resultat->fetch(PDO::FETCH_ASSOC); 	// Pas de boucle sur ce fetch car il n'y a qu'une seule annonce par id. 
																// 'annonce_actuelle' est un array qui contient toutes les infos de l'annonce à mettre dans le formulaire. 
		
	}
		
	
?>	

	<h3>Ajouter une annonce</h3>
	
	<form method="post" action="" enctype="multipart/form-data" class="col-lg-offset-4 col-lg-4">	<!-- 'multipart/form-data' spécifie que ce formulaire envoie des données binaires (=photos) et du texte (=champs du form) : permet d'uploader une photo --> 
		
		<input type="hidden" id="id_annonce" name="id_annonce" value="<?php echo $annonce_actuelle['id_annonce'] ?? 0; ?>" /> <!-- Nécessaire pour la phase de modification d'un annonce. Champ caché pour ne pas pouvoir être modifiable -->	
		
		<label for="titre">Titre</label><br />
		<input type="texte" id="titre" name="titre" class="form-control" value="<?php echo $annonce_actuelle['titre'] ?? ''; ?>" /><br />		

		<label for="description_courte">Description courte</label><br />
		<textarea id="description_courte" name="description_courte" class="form-control"><?php echo $annonce_actuelle['description_courte'] ?? ''; ?></textarea><br />
		
		<label for="description_longue">Description longue</label><br />
		<textarea id="description_longue" name="description_longue" class="form-control"><?php echo $annonce_actuelle['description_longue'] ?? ''; ?></textarea><br />		

		<label for="categorie_id">Catégorie</label><br />
		<select name="categorie_id" id="categorie_id" class="form-control"><br />
		
		<?php 
			$resultat = executeReq("SELECT * FROM categorie");
		
			// affichage des autres catégories : 
			while ($cat = $resultat->fetch(PDO::FETCH_ASSOC)) {
				echo '<option value="'. $cat['id_categorie'] .'">' . $cat['titre'] . '</option>'; 
			}
		?>
		</select><br />

		<label for="prix">Prix</label><br />
		<input type="texte" id="prix" name="prix" class="form-control" value="<?php echo $annonce_actuelle['prix'] ?? 0; ?>" /><br />
		

		<label for="photo">Photo</label><br />		
		<!-- 5- UPLOAD DE LA PHOTO -->
		<input type="file" id="photo" name="photo" class="form-control" /><br /> <!-- Le type="file" fonctionne en parallèle du "enctype="multipart/form-data" dans la balise <form> -->
		
		<!-- 9- Modification de la photo --> 
		<?php if(isset($annonce_actuelle['photo'])) {	// si existe, alors on est en train de modifier un annonce. On affiche alors la vignette de la photo actuelle :
			echo '<i>Vous pouvez uploader une nouvelle photo</i>';
			echo '<p>Photo actuelle :</p>';
			echo '<img src="'. $annonce_actuelle['photo'] .'" name="photo_actuelle" width="90" height="90" /><br />';
			
			echo '<input type="hidden" name="photo_actuelle" value="'. $annonce_actuelle['photo'] .'" /><br />';	// on a besoin de mettre dans le formulaire le chemin de la photo qui vient de la BDD. Cet input complète le $_POST['photo_actuelle'] qui va en base : on prend la photo de la BDD, puis on la met dans le formulaire, puis elle va dans le $_POST, puis on la remet dans la BDD. 
			
		}  ?>
		
		<label for="pays">Pays</label><br />
		<input type="texte" id="pays" name="pays" class="form-control" value="<?php echo $annonce_actuelle['pays'] ?? ''; ?>" /><br />		

		<label for="ville">Ville</label><br />
		<input type="texte" id="ville" name="ville" class="form-control" value="<?php echo $annonce_actuelle['ville'] ?? ''; ?>" /><br />		

		<label for="cp">Code postal</label><br />
		<input type="text" name="cp" id="cp" class="form-control" value="<?php echo $annonce_actuelle['cp'] ?? ''; ?>" /><br />	
		
		<label for="adresse">Adresse</label><br />
		<textarea id="adresse" name="adresse" class="form-control"><?php echo $annonce_actuelle['adresse'] ?? ''; ?></textarea><br />
	
			
		<input type="submit" value="Enregistrer" class="btn" />
		
	</form>
	

<?php
endif;  
require_once('inc/bas.inc.php');

?>