<?php
require_once('inc/init.inc.php');

// ------------- TRAITEMENT ---------------

$contenu_gauche .= '<form method="post" action="">';
	// Affichage du filtre des catégories : 
	$contenu_gauche .= '<label for="categorie_id">Catégories</label>';
	$contenu_gauche .= '<select class="form-control" name="categorie_id">';
		$contenu_gauche .= '<option value="all" class="list-group-item">Toutes les catégories</option>'; 
		$resultatCat = executeReq("SELECT * FROM categorie");
		while ($cat = $resultatCat->fetch(PDO::FETCH_ASSOC)) {
			// debug($cat);
			$contenu_gauche .= '<option value="'. $cat['id_categorie'] .'" class="list-group-item">'. $cat['titre'] .'</option>'; 
		}
	$contenu_gauche .= '</select><br />';
	// Affichage du filtre des régions : 
	$contenu_gauche .= '<label for="ville">Villes</label>';
	$contenu_gauche .= '<select class="form-control" name="ville">';
		$contenu_gauche .= '<option value="all" class="list-group-item">Toutes les villes</option>';
		$resultatVille = executeReq("SELECT DISTINCT(ville) FROM annonce ORDER BY ville");
		while ($ville = $resultatVille->fetch(PDO::FETCH_ASSOC)) {
			$contenu_gauche .= '<option value="'. $ville['ville'] .'" class="list-group-item">'. $ville['ville'] .'</option>';
		}
	$contenu_gauche .= '</select><br />';
	// Affichage du filtre des prix : 
	$contenu_gauche .= '<label for="prixMax">Prix maximum (en €)</label>';
		$resultatPrix = executeReq("SELECT MAX(prix) FROM annonce");
		$prixMax = $resultatPrix->fetch(PDO::FETCH_ASSOC);
		foreach($prixMax as $indice => $information) {
			$contenu_gauche .= '<input id="prixMax" class="range" name="prixMax" type="range" class="form-control" min="0" max="'. $information .'" step="50" /><output class = "price_output"></output><br />';
		}
	$contenu_gauche .= '<input type="submit" value="Rechercher" class="btn" />';
$contenu_gauche .= '</form><br />';

// Ajout d'un formulaire pour trier les annonces :  
$contenu_droite .= '<div class="row">';
$contenu_droite .= '<form method="get" action="" class="col-sm-offset-8 col-sm-4 form-tri">';
	$contenu_droite .= '<select class="form-control" name="tri">';
		$contenu_droite .= '<option value="croissant" class="list-group-item">Du - cher au + cher</option>';
		$contenu_droite .= '<option value="decroissant" class="list-group-item">Du + cher au - cher</option>';
		$contenu_droite .= '<option value="recent" class="list-group-item">Du plus récent au plus ancien</option>';
		$contenu_droite .= '<option value="ancien" class="list-group-item">Du plus ancien au plus récent</option>';
	$contenu_droite .= '</select><br />';
	$contenu_droite .= '<input type="submit" value="Trier" class="btn btn-tri" /><br /><br />';
$contenu_droite .= '</form>';
$contenu_droite .= '</div>';


if(isset($_GET['tri'])) { 
	if($_GET['tri'] == 'croissant') {
		$resultatTri = executeReq("SELECT * FROM annonce ORDER BY prix");
	}elseif($_GET['tri'] == 'decroissant') {
		$resultatTri = executeReq("SELECT * FROM annonce ORDER BY prix DESC");
	}elseif($_GET['tri'] == 'recent') {
		$resultatTri = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement DESC");
	}elseif($_GET['tri'] == 'ancien') {
		$resultatTri = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement");
	}
} else {
	$resultatTri = executeReq("SELECT * FROM annonce");
}

if(isset($_POST['categorie_id']) || isset($_POST['ville'])) {
	if(isset($_POST['prixMax'])) {
		$resultat = executeReq("SELECT * FROM annonce WHERE prix <= :prixMax", array(':prixMax' => $_POST['prixMax']));
	}
	if($_POST['categorie_id'] != 'all') {
		$resultat = executeReq("SELECT * FROM annonce WHERE categorie_id = :categorie_id AND prix <= :prixMax", array(':categorie_id' => $_POST['categorie_id'], ':prixMax' => $_POST['prixMax']));
	}elseif($_POST['ville'] != 'all') {
		$resultat = executeReq("SELECT * FROM annonce WHERE ville = :ville AND prix <= :prixMax", array(':ville' => $_POST['ville'], ':prixMax' => $_POST['prixMax']));
	}
}else {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement DESC");
}


// 2- Affichage des annonces en fonction de la catégorie choisie : 
while( ($annonce = $resultat->fetch(PDO::FETCH_ASSOC)) && ($tri = $resultatTri->fetch(PDO::FETCH_ASSOC)) ) {
	// mise en forme de l'annonce :
	$contenu_droite .= '<div class="col-sm-4">';
		$contenu_droite .= '<div class="thumbnail">';
			// image cliquable :
			$contenu_droite .= '<a href="fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"><img class="vignette" src="'. $annonce['photo'] .'" /></a>';
			
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
