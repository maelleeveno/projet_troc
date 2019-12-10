<?php
require_once('inc/init.inc.php');

// Déclaration de variable :
$suggestion = '';
$commentaire = '';

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
	$resultatPseudo = executeReq("SELECT * FROM membre, annonce WHERE membre.id_membre = annonce.membre_id AND annonce.id_annonce = :id_annonce", 
									array(':id_annonce' => $_GET['id_annonce']));
	$membre_actuel = $resultatPseudo->fetch(PDO::FETCH_ASSOC);
		
} else {
	// l'indice "id_produit" n'existant pas, je redirige l'internaute vers la boutique :
	header('location:index.php');	
	exit();
}



// Enregistrement du commentaire
if(isset($_POST['validation']) && !empty($_POST)) {	

	if(!isset($_POST['commentaire'])){
		$contenu .= '<div class="bg-danger text-center">Pas de commentaire</div>';
	}
	
	if(empty($contenu)){
		$membreConnecte = $_SESSION['membre'];
		executeReq("INSERT INTO commentaire (membre_id, annonce_id, commentaire, date_enregistrement) VALUES (:membre, :annonce, :commentaire, NOW())",
			array(	':membre'				=> $membreConnecte['id_membre'],
							':annonce'		=> $_GET['id_annonce'],
							':commentaire' 	=> $_POST['commentaire']
			));
			// debug($membreConnecte);
	}
	
}



// Affichage des suggestions 
$resultat = executeReq("SELECT * 
			FROM annonce 
			WHERE categorie_id = :categorie_id 
			AND id_annonce <> :id_annonce 
			ORDER BY RAND() 
			LIMIT 4", 
			array(':categorie_id' => $annonce['categorie_id'], 
				  ':id_annonce' => $_GET['id_annonce']));


while ($vignette = $resultat->fetch(PDO::FETCH_ASSOC)) {
	$suggestion .= 	'<div class="col-md-2">
				<h4>' . $vignette['titre'] . '</h4>	
				<a href="fiche_annonce.php?id_annonce= ' . $vignette['id_annonce'] . '">
					<img style ="width:200px" class="img-responsive" src="' . $vignette['photo'] . '">
				</a>
			</div>';
}

//-------------------- AFFICHAGE ----------------------
require_once('inc/haut.inc.php');
echo $contenu;
?>	

	<div class="row">
		<div class="col-sm-3">
			<br><a href="index.php"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Retour vers les annonces</a>
		</div>

		<div class="col-sm-offset-2 col-sm-2 text-center">
			<button class="btn" data-toggle="modal" data-target="#ModalContact">Contacter <?php echo $membre_actuel['pseudo']; ?></button>
		</div>
		
		<?php if(isConnected()) : ?>
		<div class="col-sm-3 text-center">
			<button class="btn"><a href="mon_compte.php?membre_id=<?php echo $membre_actuel['id_membre']; ?>">Voir le profil de <?php echo $membre_actuel['pseudo']; ?></a></button>
		</div>
		<?php endif; ?>

		<div class="col-sm-2 text-center">
			<button class="btn" type="button" data-toggle="modal" data-target="#ModalCommentaire">Laisser un commentaire</button>
		</div>
	</div>	
	
	<div class="row">
		<div class="col-sm-12">
			<h1 class="page-header"><?php echo $annonce['titre']; ?></h1>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6">
			<img class="img-responsive" src="<?php echo $annonce['photo']; ?>">
		</div>
		
		<div class="col-md-6">
			<h3>Détails de l'annonce</h3><br />			
			<p><?php echo $annonce['description_courte']; ?></p><br />

			<a data-toggle="collapse" href="#infos" aria-expanded="false" aria-controls="infos"><h5><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Plus de renseignements</h5></a><br />
			<div class="collapse" id="infos"><?php echo $annonce['description_longue']; ?></div><hr />

			<p class="align-bottom">Mots-clés :
			<?php
				$resultatCat = executeReq("SELECT * FROM categorie, annonce WHERE categorie.id_categorie = annonce.categorie_id AND annonce.id_annonce = :id_annonce", array(':id_annonce' => $_GET['id_annonce']));
				$motscles = $resultatCat->fetch(PDO::FETCH_ASSOC);
				echo ''. $motscles['motscles'] .'</p><br />'; 

			?>

			<p><a href="#commentaires">Voir les commentaires</a></p>
		</div>
		
	</div><!-- .row -->
	
	<!-- Modal -->
	<div id="ModalContact" class="modal fade" role="dialog">
		<div class="modal-dialog">

		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Votre contact</h4>
				</div>

				<div class="modal-body">

					<?php if(isConnected()) : ?>
					<p><span class="glyphicon glyphicon-user" aria-hidden="true"></span> : <?php echo '<a href="mon_compte.php?membre_id='. $membre_actuel['id_membre'] .'">' . $membre_actuel['pseudo'] . '</a>'; ?><br>
					<span class="glyphicon glyphicon-phone-alt"></span> : <?php echo $membre_actuel['telephone']; ?><br>
					<span class="glyphicon glyphicon-envelope"></span> : <a href="mailto:<?php echo $membre_actuel['email']; ?>"><?php echo $membre_actuel['email']; ?></a></p>
					<?php endif; ?>
		  
					<?php if(!isConnected()) : ?>
					<div class="bg-alert text-center">Vous n'êtes pas connecté. Veuillez vous connecter pour afficher les informations de contact du membre.</div>
					<?php endif; ?>

				</div>
		
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
	
		</div>
	</div>	
	
	<!-- Modal -->
	<div id="ModalCommentaire" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Laissez un commentaire à <?php echo $membre_actuel['pseudo']; ?> à propos de l'annonce " <?php echo $annonce['titre']; ?> "</h4>
				</div>

				<div class="modal-body">

					<?php if(isConnected()) : ?>
					<form method="post" action="">
						<textarea name="commentaire" id="commentaire" class="form-control" rows="7" cols="60" autofocus></textarea><br /><br />
						<input type="submit" value="Publier" name="validation" class="btn" />
					</form>
					<?php endif; ?>

					<?php if(!isConnected()) : ?>
					<div class="bg-alerte text-center">Vous n'êtes pas connecté. Veuillez vous connecter pour laisser un commentaire.</div>
					<?php endif; ?>					

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
			<p><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Publiée le <?php $dateFr = new DateTime($annonce['date_enregistrement']); echo $dateFr->format('d/m/Y à H:i:s'); ?></p>
		</div>
		
		<div class="col-lg-3">
			<?php if(isConnected()) : ?>
			<p><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <a href="mon_compte.php?membre_id= <?php echo $annonce['membre_id']; ?> "> <?php echo $membre_actuel['pseudo'];?></a></p>
			<? endif; ?>

			<?php if(!isConnected()) : ?>
			<p><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo $membre_actuel['pseudo'];?></p>
			<? endif; ?>
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

	<!-- suggestions de produits -->
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">Produits similaires :</h3>
		</div>
		<?php echo $suggestion; ?>
	</div><br />

	<div class="row">
		<div class="col-lg-12">
			<h3 id="commentaires">Commentaires :</h3><hr />
			<?php 

			$resultatCom = executeReq("SELECT commentaire.*, membre.id_membre, membre.pseudo 
									   FROM commentaire, membre 
									   WHERE commentaire.annonce_id = :id_annonce 
									   AND commentaire.membre_id = membre.id_membre
									   ORDER BY commentaire.date_enregistrement DESC", 
									   array(':id_annonce' => $_GET['id_annonce'])); 
			while($affichage = $resultatCom->fetch(PDO::FETCH_ASSOC)) {
				$dateFr = new DateTime($affichage['date_enregistrement']);
				echo '<div class="thumbnail">';
					echo '<p><strong>Avis déposé par <a href="mon_compte.php?membre_id='. $affichage['id_membre'] .'">'. $affichage['pseudo'] . '</a> le ' . $dateFr->format('d/m/Y à H:i:s') .'</strong></p><br />';
					echo '<p>'. $affichage['commentaire'] .'</p>'; 
				echo '</div>';
			}

			?>
		</div>
	</div>
		
	<script>
	// jQuery qui permet d'afficher la modale de $contenu si elle existe :
		$(function(){
			$("#myModal").modal("show");			
		});
	</script>
	
<?php
require_once('inc/bas.inc.php');
