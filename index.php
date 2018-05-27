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
			if(isset($_POST['categorie_id']) && ($cat['id_categorie'] == $_POST['categorie_id'])) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$contenu_gauche .= '<option value="'. $cat['id_categorie'] .'" class="list-group-item"'. $selected .'>'. $cat['titre'] .'</option>'; 
		}
	$contenu_gauche .= '</select><br />';
	// Affichage du filtre des régions : 
	$contenu_gauche .= '<label for="ville">Villes</label>';
	$contenu_gauche .= '<select class="form-control" name="ville">';
		$contenu_gauche .= '<option value="all" class="list-group-item">Toutes les villes</option>';
		$resultatVille = executeReq("SELECT DISTINCT(ville) FROM annonce ORDER BY ville");
		while ($ville = $resultatVille->fetch(PDO::FETCH_ASSOC)) {
			if(isset($_POST['ville']) && ($ville['ville'] == $_POST['ville'])) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$contenu_gauche .= '<option value="'. $ville['ville'] .'" class="list-group-item"'. $selected .'>'. $ville['ville'] .'</option>';
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
 


// Pagination

$annoncesParPage = 12;
$annoncesTotalesReq = $pdo->query('SELECT * FROM annonce');
$annoncesTotales = $annoncesTotalesReq->rowCount();
$pagesTotales = ceil($annoncesTotales/$annoncesParPage);

if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0) {
	$_GET['page'] = intval($_GET['page']);
	$pageActuelle = $_GET['page'];
} else {
	$pageActuelle = 1;
}

$depart = ($pageActuelle-1)*$annoncesParPage;


// 2- Affichage des annonces en fonction de la catégorie choisie : 
if(isset($_POST['categorie_id']) || isset($_POST['ville'])) {
	if(isset($_POST['prixMax'])) {
		$resultat = executeReq("SELECT * FROM annonce WHERE prix <= :prixMax LIMIT ".$depart.", ".$annoncesParPage."", array(':prixMax' => $_POST['prixMax']));
	}
	if($_POST['categorie_id'] != 'all') {
		$resultat = executeReq("SELECT * FROM annonce WHERE categorie_id = :categorie_id AND prix <= :prixMax LIMIT ".$depart.", ".$annoncesParPage."", array(':categorie_id' => $_POST['categorie_id'], ':prixMax' => $_POST['prixMax']));
	}elseif($_POST['ville'] != 'all') {
		$resultat = executeReq("SELECT * FROM annonce WHERE ville = :ville AND prix <= :prixMax LIMIT ".$depart.", ".$annoncesParPage."", array(':ville' => $_POST['ville'], ':prixMax' => $_POST['prixMax']));
	}
}elseif(isset($_GET['tri']) && ($_GET['tri'] == 'croissant')) {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY prix LIMIT ".$depart.", ".$annoncesParPage."");
}elseif(isset($_GET['tri']) && ($_GET['tri'] == 'decroissant')) {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY prix DESC LIMIT ".$depart.", ".$annoncesParPage."");
}elseif(isset($_GET['tri']) && ($_GET['tri'] == 'recent')) {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement DESC LIMIT ".$depart.", ".$annoncesParPage."");
}elseif(isset($_GET['tri']) && ($_GET['tri'] == 'ancien')) {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement LIMIT ".$depart.", ".$annoncesParPage."");
}else {
	$resultat = executeReq("SELECT * FROM annonce ORDER BY date_enregistrement DESC LIMIT ".$depart.", ".$annoncesParPage."");
}


while($annonce = $resultat->fetch(PDO::FETCH_ASSOC)) {
	// mise en forme de l'annonce :
	$contenu_droite .= '<div class="affichage">';
		$contenu_droite .= '<div class="thumbnail col-xs-12">';
			// image cliquable :
			$contenu_droite .= '<div class="image"><a href="fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"><img class="vignette pull-left" src="'. $annonce['photo'] .'" /></a></div>';
			
			// les infos de l'annonce :
			$contenu_droite .= '<div class="caption infos">';
				$contenu_droite .= '<h4 class="pull-right">'. $annonce['prix'] . ' € </h4>';
				$contenu_droite .= '<h4>' . $annonce['titre'] . '</h4>';
				$contenu_droite .= '<p>' . $annonce['description_courte'] . '</p>';

				$resultatMembre = executeReq("SELECT * FROM membre WHERE id_membre = :membre_id", array(':membre_id' => $annonce['membre_id']));
				$membre = $resultatMembre->fetch(PDO::FETCH_ASSOC);
				$contenu_droite .= '<p>Annonce publiée par <a href="mon_compte.php?membre_id='. $membre['id_membre'] .'">'. $membre['pseudo'] .' </a></p>';

				$contenu_droite .= '<button class="btn btnVoirAnnonce pull-right"><a href="fiche_annonce.php?id_annonce='. $annonce['id_annonce'] .'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Voir l\'annonce</a></button>';
			$contenu_droite .= '</div>';

			
		$contenu_droite .= '</div>';
	$contenu_droite .= '</div>';
}

// -------------- AFFICHAGE ---------------
require_once('inc/haut.inc.php');
?>

	<div class="row">
		<div class="col-xs-3">
			<?php echo $contenu_gauche; ?>
		</div>
		<div class="col-xs-9">
			<div class="row text-center">
				<span id="mosaique" class="list-group-item glyphicon glyphicon-th col-sm-offset-7 col-sm-1 btn-affichage" aria-hidden="true"></span>

				<form method="get" action="" class="col-sm-4 form-tri">
					<select class="form-control" name="tri">
						<option value="croissant" class="list-group-item">Du - cher au + cher</option>
						<option value="decroissant" class="list-group-item">Du + cher au - cher</option>
						<option value="recent" class="list-group-item">Du plus récent au plus ancien</option>
						<option value="ancien" class="list-group-item">Du plus ancien au plus récent</option>
					</select><br />
					<input type="submit" value="Trier" class="btn btn-tri" /><br /><br />
				</form>
			</div>
			<div class="row">
				<?php echo $contenu_droite; ?>
			</div>
			<div class="row text-center">
				<ul class="pagination">
					<li><a href="index.php?page=1" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>

					<?php

					for($i=1; $i <= $pagesTotales; $i++) {
						if($i == $pageActuelle) {
							echo '<li class="active"><a href="index.php?page='. $i .'">'. $i .'</a></li>';
						} else {
							echo '<li><a href="index.php?page='. $i .'" aria-label="Previous"><span aria-hidden="true">'. $i .'</span></a></li>';
						}
					}
					?>
					<li><a href="index.php?page=<?php echo $pagesTotales ?>" aria-label="Previous"><span aria-hidden="true">&raquo;</span></a></li>
				</ul>
			</div>
		</div>
	</div>



<?php 
require_once('inc/bas.inc.php');
?>
<script>
  $("#mosaique").click(function () {
    $(".affichage").toggleClass("col-lg-4");
    $(".thumbnail").toggleClass("cadreAnnonce");	
    $(".vignette").toggleClass("pull-left");
    $(".image").toggleClass("image");
    $(".caption").toggleClass("infos");
    $(".btnVoirAnnonce").toggleClass("pull-right");
    $(this).toggleClass("glyphicon-th-list");
  });

  
	$(function() {
		$('.price_output').text('--'); // Valeur par défaut
		$('.range').on('input', function() {
			var $set = $(this).val();
			$('.price_output').text($set);
		});
	});

</script>
