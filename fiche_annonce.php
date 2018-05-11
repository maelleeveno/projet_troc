<?php
require_once('inc/init.inc.php');

//-------------------- TRAITEMENT --------------------
// 1- Contrôle de l'existence du produit demandé (un produit mis en favoris a pu être supprimé de la boutique) :

if (isset($_GET['id_annonce'])) {
	// si l'indice "id_produit" existe, je peux sélectionner le produit en BDD :
	$resultat = executeReq("SELECT * FROM annonce WHERE id_annonce = :id_annonce", array(':id_annonce' => $_GET['id_annonce']));
		
	if ($resultat->rowCount() == 0) {
		// s'il n'y a pas de ligne dans le jeu de résultat, c'est que le produit n'est pas ou plus en BDD :
		header('location:index.php');	
		exit();
	}
	
	// 2- préparation des variables d'affichage des infos du produits :
	$annonce = $resultat->fetch(PDO::FETCH_ASSOC);
	// debug($produit);
	extract($annonce);  // crée des variables nommées comme les indices de l'array et qui prennent pour valeur les valeurs correspondantes dans l'array. On peut faire extract car on n'est pas dans une boucle.

		// si existe l'id_annonce, je peux sélectionner le membre : 
		$resultatPseudo = executeReq("SELECT * FROM membre, annonce WHERE membre.id_membre = annonce.membre_id AND id_annonce = :id_annonce", 
								   array(':id_annonce' => $_GET['id_annonce']));
		$membre_actuel = $resultatPseudo->fetch(PDO::FETCH_ASSOC); 	
		
} else {
	// l'indice "id_produit" n'existant pas, je redirige l'internaute vers la boutique :
	header('location:index.php');	
	exit();
}

//-------------------- AFFICHAGE ----------------------
require_once('inc/haut.inc.php');
echo $contenu;
?>

	<div class="row">
		
		<div class="col-lg-10">
			<h1 class="page-header"><?php echo $annonce['titre']; ?></h1>
		</div>
		<div class="col-lg-2">
			<input class="" type="button" value="Contacter <?php echo $membre_actuel['pseudo']; ?>" data-toggle="modal" data-target="#ModalContact">
		</div>
	
		<div class="col-md-6">
			<img class="img-responsive" src="<?php echo $annonce['photo']; ?>">
		</div>
		
		<div class="col-md-6">
			<h3>Description</h3>			
			<p><?php echo $annonce['description_longue']; ?></p>
		</div>
		
	</div><!-- .row -->
	
	<!-- Modal -->
	<div id="ModalContact" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Modal Header</h4>
		  </div>
		  <div class="modal-body">
			<p>M admin <br>
			<span>glyphicon glyphicon-phone-alt</span> : 0123456789<br>
			<span>glyphicon glyphicon-envelope</span> : truc@mail.com</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
	
	<br><br>
	<div class="row">
	
		<div class="col-lg-3">
			<p><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Date de publication : <?php echo $annonce['date_enregistrement']; ?></p>
		</div>
		
		<div class="col-lg-3">
			<p><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo $membre_actuel['pseudo'];?></p>
		</div>
		
		<div class="col-lg-3">
			<p><span class="glyphicon glyphicon-euro" aria-hidden="true"></span> Prix : <?php echo $annonce['prix']; ?>€</p>
		</div>
		
		<div class="col-lg-3">
			<p><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> Adresse : <?php echo $annonce['adresse'] . ', ' . $annonce['cp'] . ', ' . $annonce['ville']; ?></p>
		</div>
	
	</div><!-- .row -->
	
	<div class="row">
		<div class="col-lg-12">
			<!-- plan -->
		</div>
	</div><!-- .row -->

	<!-- Exercice : suggestions de produits -->
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">Suggestions de produits</h3>
		</div>
		
		<?php echo $suggestion; ?>
		
	</div>
	
		
	<script>
	<!-- jQuery qui permet d'afficher la modale de $contenu si elle existe : -->
		$(function(){
			$("#myModal").modal("show");			
		});
	</script>
	
<?php
require_once('inc/bas.inc.php');