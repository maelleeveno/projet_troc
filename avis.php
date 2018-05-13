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
        <input type="hidden" name="note" value="" id="note"/>
            <img src="inc/img/star_clear.gif" id="clear_stars" title="Sans intérêt c'est trop nul">
            <img src="inc/img/star_out.gif" id="star_1" class="star"/>
            <img src="inc/img/star_out.gif" id="star_2" class="star"/>
            <img src="inc/img/star_out.gif" id="star_3" class="star"/>
            <img src="inc/img/star_out.gif" id="star_4" class="star"/>
            <img src="inc/img/star_out.gif" id="star_5" class="star"/>

    <label for="avis">Laissez un avis à </label>
    <input type="text" name="avis" id="avis" class="form-control" value="" required /><br />



    <input type="submit" value="Enregistrer" class="btn" />
</form>

<?php 

require_once('inc/bas.inc.php');

?>
