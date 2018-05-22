<?php 

require_once('inc/init.inc.php');

// ----------- TRAITEMENT ------------

// 1- Vérification que le membre est connecté : 
    if(!isConnected()) {
        header('location:connexion.php');
        exit();	// on quitte le script.
    }

if(!empty($_POST)) {
    if( !isset($_POST['note']) || !preg_match('/^[1-5]{1}$/', $_POST['note']) ) {
        $contenu .= '<div class="bg-danger">Veuillez laisser une note.</div>';
    }
    if(!isset($_POST['avis']) || strlen($_POST['avis']) < 2 || strlen($_POST['avis']) > 150 ) {
        $contenu .= '<div class="bg-danger">Veuillez laisser un avis (entre 2 et 150 caractères).</div>';
    }

    if (empty($contenu)) {
        
        $resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre",
        array(':id_membre' => $_GET['membre_id']));
        $membre2 = $resultat->fetch(PDO::FETCH_ASSOC);
        
        executeReq("INSERT INTO note (note, avis, date_enregistrement, membre_id1, membre_id2) VALUES (:note, :avis, NOW(), :membre_id1, :membre_id2)", 
                        array(':note'  		=> $_POST['note'],
                              ':avis'       => $_POST['avis'],
                              ':membre_id1' => $_SESSION['membre']['id_membre'],
                              ':membre_id2' => $membre2['id_membre']
                        ));
                        $contenu .= '<div class="bg-success">Votre avis a bien été enregistré. Retournez sur le <a href="mon_compte.php?membre_id='. $_GET['membre_id'] .'">profil du membre.</a></div>';
	}

}

debug($_POST);

$resultat = executeReq("SELECT * FROM membre WHERE id_membre = :id_membre",
array(':id_membre' => $_GET['membre_id']));
$membre2 = $resultat->fetch(PDO::FETCH_ASSOC);

// ----------- AFFICHAGE -------------
require_once('inc/haut.inc.php');
echo $contenu;

?>

<h2>Laissez un avis à <?php echo $membre2['pseudo'] ?></h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">


    <label for="avis">Laissez un avis à <?php echo $membre2['pseudo'] ?> </label>
    <textarea name="avis" id="avis" class="form-control" rows="5" value="" required></textarea><br />

    
    <div>Donnez une note à <?php echo $membre2['pseudo'] ?></div>
	<div class="rating">
        <input name="note" id="e5" type="radio" value="5"><label for="e5"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></label>
		<input name="note" id="e4" type="radio" value="4"><label for="e4"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></label>
		<input name="note" id="e3" type="radio" value="3"><label for="e3"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></label>
		<input name="note" id="e2" type="radio" value="2"><label for="e2"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></label>
		<input name="note" id="e1" type="radio" value="1"><label for="e1"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></label>
    </div>

    <input type="submit" value="Enregistrer" class="btn" />
</form>

<?php 

require_once('inc/bas.inc.php');

?>
