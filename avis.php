<?php 

require_once('inc/init.inc.php');

// ----------- TRAITEMENT ------------
if(!empty($_POST)) {
    if(!isset($_POST['note'])) {
        $contenu .= '<div class="bg-danger">Veuillez laisser une note.</div>';
    }
    if(!isset($_POST['avis'])) {
        $contenu .= '<div class="bg-danger">Veuillez laisser un avis.</div>';
    }
}






// ----------- AFFICHAGE -------------
require_once('inc/haut.inc.php');
echo $contenu;

// debug($_POST);

?>

<h2>Laissez un avis à </h2>

<form method="post" action="" class="col-lg-offset-4 col-lg-4">
    
    <div>Donnez une note à </div>
	<div class="rating">
        <input name="stars" id="e5" type="radio"></a><label for="e5">☆</label>
		<input name="stars" id="e4" type="radio"></a><label for="e4">☆</label>
		<input name="stars" id="e3" type="radio"></a><label for="e3">☆</label>
		<input name="stars" id="e2" type="radio"></a><label for="e2">☆</label>
		<input name="stars" id="e1" type="radio"></a><label for="e1">☆</label>
    </div>

    <label for="avis">Laissez un avis à </label>
    <input type="text" name="avis" id="avis" class="form-control" value="" required /><br />



    <input type="submit" value="Enregistrer" class="btn" />
</form>

<?php 

require_once('inc/bas.inc.php');

?>
