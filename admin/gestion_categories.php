<?php 

require_once('../inc/init.inc.php');

// ------------- TRAITEMENT ------------

// 1- Vérification que le membre est admin et est connecté : 
if(!isConnectedAndAdmin()) {
	// Si membre non connecté ou non admin, on le redirige vers la page de connexion :
	header('location:../index.php');	// on demande la page de connexion
	exit();	// on quitte le script.
} 


// 7- Suppresion de la catégorie : 
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['id_categorie'])) {
	// si l'indice 'action' existe et qu'il vaut 'suppression' et qu'existe l'indice 'id_categorie', on va supprimer la catégorie : 
	$resultat = executeReq("DELETE FROM categorie WHERE id_categorie = :id_categorie", 
							   array(':id_categorie' => $_GET['id_categorie']));
							   
	if($resultat->rowCount() > 0) {
		$contenu .= '<div class="bg-success text-center">La catégorie a bien été supprimée !</div>';
	}
	
	$_GET['action'] = 'affichage';	// permet de lancer l'affichage des catégories (cf. chapitre 6 ci-dessous).
}

// 4- Enregistrement de la catégorie en BDD 
if(!empty($_POST)) {	// si le formulaire est soumis
	// debug($_POST);	
    
        // traitement du formulaire : 
		// Titre : 
		if(!isset($_POST['titre']) || strlen($_POST['titre']) < 4 || strlen($_POST['titre']) > 15) {
			$contenu .= '<div class="bg-danger text-center">Le titre doit être compris entre 4 et 15 caractères. Veuillez saisir un titre correct.</div>';	
		}
		
		// mots-clés :
		if(!isset($_POST['motscles']) || strlen($_POST['motscles']) < 5 || strlen($_POST['motscles']) > 300) {
			$contenu .= '<div class="bg-danger text-center">La description doit comprendre entre 5 et 300 caractères. Veuillez recommencer votre saisie.</div>';
		}
	
	// Enregistrement de la catégorie : 
	executeReq("UPDATE categorie SET id_categorie = :id_categorie, titre = :titre, motscles = :motscles WHERE id_categorie = :id_categorie", 
					array( ':id_categorie'	=> $_POST['id_categorie'],
						   ':titre'			=> $_POST['titre'],
						   ':motscles'	    => $_POST['motscles']
					));
	
	$contenu .= '<div class="bg-success text-center">La catégorie a bien été enregistrée.</div>';
	
	$_GET['action'] = 'affichage';	// Pour déclencher l'affichage de la table HTML avec tous les catégories (cf. étape 6 ci-dessous)
	
}	// fin du if(!empty($_POST))


// 2- Affichage des catégories
if(isConnectedAndAdmin()) {
	$resultat = executeReq("SELECT * FROM categorie"); // sélectionne tous les produits 

	$contenu .= 'Nombre de catégories :  ' . $resultat->rowCount();
	$contenu .= '<table class="table table-striped text-center">';
		// Affichage des entêtes du tableau :
		$contenu .= '<tr>';
			$contenu .= '<th scope="col">N° de la catégorie</th>';
			$contenu .= '<th scope="col">Nom de la catégorie</th>';
			$contenu .= '<th scope="col">Mots-clés</th>';
			$contenu .= '<th scope="col">Gestion</th>';
		$contenu .= '</tr>';
		
		// affichage des lignes du tableau : 
		while($categorie = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu .= '<tr>';
				// on parcourt les informations du tableau associatif $categorie : 
				foreach($categorie as $indice => $information) {
					$contenu .= '<td>'. $information .'</td>';
				}
						
                $contenu .= '<td>
                                <a href="?action=modification&id_categorie='. $categorie['id_categorie'] .'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                                <a href="?action=suppression&id_categorie='. $categorie['id_categorie'] .'"  onclick="return(confirm(\'Êtes-vous certain de vouloir supprimer cette catégorie ?\'));" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            </td>';
                $contenu .= '</tr>';
		}			
	$contenu .= '</table>';
}


        
        

// ------------- AFFICHAGE ------------

require_once('../inc/haut.inc.php');

echo '<h4 class="pull-right"><a href="?action=ajout">Ajouter une catégorie <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></a></h4>';   
echo '<h3>Gestion des catégories </h3>';

echo $contenu; // pour afficher les messages

// 3- Formulaire HTML d'une catégorie :
if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :	// Les deux points ':' et le 'endif' permettent d'enlever les accolades.
    // si je suis en ajout ou en modification d'une catégorie, j'affiche le formulaire. 
    
        // 8- Formulaire de modification d'une catégorie avec présaisie des valeurs :
        if(isset($_GET['id_categorie'])) {
            // si existe l'id_categorie, je peux sélectionner la catégorie en BDD : 
            $resultat = executeReq("SELECT * FROM categorie WHERE id_categorie = :id_categorie", 
                                       array(':id_categorie' => $_GET['id_categorie']));
            $categorie_actuelle = $resultat->fetch(PDO::FETCH_ASSOC); 	// Pas de boucle sur ce fetch car il n'y a qu'une seule catégorie par id. 
                                                                    // 'categorie_actuelle' est un array qui contient toutes les infos de la catégorie à mettre dans le formulaire. 
            
        }

?>

	<h3>Ajouter une catégorie</h3>
	
	<form method="post" action="" class="col-lg-offset-4 col-lg-4">	
		
		<input type="hidden" id="id_categorie" name="id_categorie" value="<?php echo $categorie_actuelle['id_categorie']; ?>" /> <!-- Nécessaire pour la phase de modification d'une catégorie. Champ caché pour ne pas pouvoir être modifiable -->	
		
		<label for="titre">Titre</label><br />
		<input type="texte" id="titre" name="titre" class="form-control" value="<?php echo $categorie_actuelle['titre']; ?>" /><br />		

		<label for="motscles">Mots-clés</label><br />
		<textarea id="motscles" name="motscles" class="form-control"><?php echo $categorie_actuelle['motscles']; ?></textarea><br />
			
		<input type="submit" value="Enregistrer" class="btn" />
		
	</form>
    annonce

<?php 
endif;  
require_once('../inc/bas.inc.php');

?>