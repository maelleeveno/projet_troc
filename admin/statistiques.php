<?php 
require_once('../inc/init.inc.php');

// TRAITEMENT 

// 1- Vérification que le membre est admin et est connecté : 
if(!isConnectedAndAdmin()) {
    header('location:../connexion.php');
    exit();	
}

// AFFICHAGE 

require_once('../inc/haut.inc.php'); 
echo $contenu ;

?>

<h2>Statistiques</h2><br />

<h4><a data-toggle="collapse" href="#top5notes" aria-expanded="false" aria-controls="top5notes"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Top 5 des membres les mieux notés</a></h4><br />

<?php 
if(isConnectedAndAdmin()) {
    $resultat1 = executeReq("SELECT DISTINCT(membre.pseudo) as 'membre', ROUND(AVG(note.note),1) as 'note'
                             FROM membre
                             LEFT JOIN note ON membre.id_membre = note.membre_id2
                             GROUP BY membre.pseudo
                             ORDER BY AVG(note.note) DESC
                             LIMIT 5");

    echo '<div class="collapse" id="top5notes">';

    echo '<table class="table">';
        echo '<tr>';
            echo '<th>Membre</th>';
            echo '<th>Note moyenne</th>';
        echo '</tr>';

        while($bestNote = $resultat1->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
            foreach($bestNote as $indice => $information) {

                $resultat1bis = executeReq("SELECT COUNT(note.note) AS 'total' FROM note, membre WHERE note.membre_id2 = membre.id_membre");
                $nbNotes = $resultat1bis->fetch(PDO::FETCH_ASSOC);
    
                if($indice == 'note') {
                    echo '<td>' . $information . ' / 5 (basé sur '. $nbNotes['total'] .' avis)</td>'; 
                } elseif($indice == 'note' && $indice == NULL) {
                    echo '<td>Aucune note.</td>';
                } 
                else {
                    echo '<td>'. $information .'</td>';
                }
            }
        echo '</tr>';
        }    		
    echo '</table>';

    echo '</div>';
}
?>

<h4><a data-toggle="collapse" href="#top5membres" aria-expanded="false" aria-controls="top5membres"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Top 5 des membres les plus actifs</a></h4><br />

<?php 
if(isConnectedAndAdmin()) {
    $resultat2 = executeReq("SELECT membre.pseudo, COUNT(note.avis) 
	                         FROM membre
	                         LEFT JOIN note ON membre.id_membre = note.membre_id1
	                         GROUP BY membre.pseudo
	                         LIMIT 5;");

    echo '<div class="collapse" id="top5membres">';

    echo '<table class="table">';

    echo '<tr>';
       echo '<th>membre</th>';
       echo '<th>Nombre d\'avis laissés</th>';
    echo '</tr>';

    while($activeMember = $resultat2->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
        foreach($activeMember as $indice => $information) {
            echo '<td>'. $information .'</td>';
        }
    }
        echo '</tr>';    		
    echo '</table>';

    echo '</div>';
}
?>


<h4><a data-toggle="collapse" href="#top5annonces" aria-expanded="false" aria-controls="top5annonces"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Top 5 des annonces les plus anciennes</a></h4><br />

<?php 
if(isConnectedAndAdmin()) {
    $resultat3 = executeReq("SELECT titre, date_enregistrement
	                         FROM annonce
	                         ORDER BY date_enregistrement
	                         LIMIT 5;");

    echo '<div class="collapse" id="top5annonces">';

    echo '<table class="table">';

    echo '<tr>';
       echo '<th>Annonce</th>';
       echo '<th>Date d\'enregistrement</th>';
    echo '</tr>';

    while($oldestPost = $resultat3->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
        foreach($oldestPost as $indice => $information) {
            echo '<td>'. $information .'</td>';
        }
    }
        echo '</tr>';    		
    echo '</table>';

    echo '</div>';
}
?>


<h4><a data-toggle="collapse" href="#top5categ" aria-expanded="false" aria-controls="top5categ"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Top 5 des catégories contenant le plus d'annonces</a></h4><br />

<?php 
if(isConnectedAndAdmin()) {
    $resultat4 = executeReq("SELECT categorie.titre, COUNT(annonce.titre)
	                         FROM categorie
	                         LEFT JOIN annonce ON categorie.id_categorie = annonce.categorie_id
	                         GROUP BY categorie.titre
	                         ORDER BY COUNT(annonce.titre) DESC
	                         LIMIT 5;");

    echo '<div class="collapse" id="top5categ">';

    echo '<table class="table">';

    echo '<tr>';
       echo '<th>Catégorie</th>';
       echo '<th>Nombre d\'annonce(s)</th>';
    echo '</tr>';

    while($popularCateg = $resultat4->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
        foreach($popularCateg as $indice => $information) {
            echo '<td>'. $information .'</td>';
        }
    }
        echo '</tr>';    		
    echo '</table>';

    echo '</div>';
}
?>


<?php 
require_once('../inc/bas.inc.php');

?>