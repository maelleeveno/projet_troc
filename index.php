<?php
require_once('inc/init.inc.php');
// ------------- TRAITEMENT ---------------

$contenu_gauche .= '<form method="post" action="">';

	// Affichage du filtre des catégories : 
	$contenu_gauche .= '<label for="categorie_id">Catégories</label>';
	$contenu_gauche .= '<select class="form-control" name="categorie_id">';
		$contenu_gauche .= '<option value="all" class="list-group-item">Toutes les catégories</option>'; 
		$resultat = executeReq("SELECT * FROM categorie");
		while ($cat = $resultat->fetch(PDO::FETCH_ASSOC)) {
			// debug($cat);
			$contenu_gauche .= '<option value="'. $cat['id_categorie'] .'" class="list-group-item">'. $cat['titre'] .'</option>'; 
		}
	$contenu_gauche .= '</select><br />';

	// Affichage du filtre des régions : 
	$contenu_gauche .= '<label for="ville">Villes</label>';
	$contenu_gauche .= '<select class="form-control" name="ville">';
		$contenu_gauche .= '<option value="all" class="list-group-item">Toutes les villes</option>';
		$resultat = executeReq("SELECT DISTINCT(ville) FROM annonce ORDER BY ville");
		while ($ville = $resultat->fetch(PDO::FETCH_ASSOC)) {
			$contenu_gauche .= '<option value="'. $ville['ville'] .'" class="list-group-item">'. $ville['ville'] .'</option>';
		}
	$contenu_gauche .= '</select><br />';

	// Affichage du filtre des prix : 
	$contenu_gauche .= '<label for="prixMax">Prix maximum</label>';
	$contenu_gauche .= '<input id="prixMax" class="range" name="prixMax" type="range" class="form-control" min="0" max="10000" step="100" /><output class = "price_output"></output><br />';



	$contenu_gauche .= '<input type="submit" value="Rechercher" class="btn" />';

$contenu_gauche .= '</form>';
debug($_POST);


// 2- Affichage des annonces en fonction de la catégorie choisie : 

if(isset($_POST['categorie_id']) && $_POST['categorie_id'] != 'all'){
	$donnees = executeReq("SELECT id_annonce, titre, description_courte, prix, photo, pays, ville, cp, categorie_id FROM annonce WHERE categorie_id = :categorie_id", 
							array(':categorie_id' => $_POST['categorie_id']));
} elseif(!empty($_POST['prixMax'])) {
	$donnees = executeReq("SELECT id_annonce, titre, description_courte, prix, photo, pays, ville, cp, categorie_id FROM annonce WHERE prix <= :prix", 
							array(':prix' => $_POST['prixMax']));
} elseif(isset($_POST['ville']) && $_POST['ville'] != 'all') {
	$donnees = executeReq("SELECT id_annonce, titre, description_courte, prix, photo, pays, ville, cp, categorie_id FROM annonce WHERE categorie_id = :categorie_id AND prix <= :prix AND ville = :ville", 
							array(':ville' => $_POST['ville']));
} else {
	$donnees = executeReq("SELECT id_annonce, titre, description_courte, prix, photo, pays, ville, cp, categorie_id FROM annonce");
} 

while($annonce = $donnees->fetch(PDO::FETCH_ASSOC)) {
	// mise en forme de l'annonce :
	$contenu_droite .= '<div class="col-sm-4">';
		$contenu_droite .= '<div class="thumbnail">';
			// image cliquable :
			$contenu_droite .= '<a href="fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"><img src="'. $annonce['photo'] .'" width="130" height="130" /></a>';
			
			// les infos de l'annonce :
			$contenu_droite .= '<div class="caption">';
				$contenu_droite .= '<h4 class="pull-right">'. $annonce['prix'] . ' € </h4>';
				$contenu_droite .= '<h4>' . $annonce['titre'] . '</h4>';
				$contenu_droite .= '<p>' . $annonce['description_courte'] . '</p>';
			$contenu_droite .= '</div>';
			
		$contenu_droite .= '</div>';
	$contenu_droite .= '</div>';	
}



// -------------- AFFICHAGE ---------------
require_once('inc/haut.inc.php');
?>

	<div class="row">
		<div class="col-md-3">
			<?php echo $contenu_gauche; ?>
		</div>
		<div class="col-md-9">
			<div class="row">
				<?php echo $contenu_droite; ?>
			</div>
		</div>
	</div>



<?php 
require_once('inc/bas.inc.php');
?>
<script>
$(function() {
	$('.price_output').text('--'); // Valeur par défaut
	$('.range').on('input', function() {
		var $set = $(this).val();
		$('.price_output').text($set);
	});
});

</script>
