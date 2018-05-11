<?php 

// Connexion à la BDD : 
$pdo = new PDO (
				'mysql:host=localhost;dbname=projet_troc',
				'root',
				'',	// mettre '' sur windows
				array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
					  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
				);

// Création ou ouverture d'une session : 
session_start();

// Création d'une constante qui contient le chemin du site : 
define('RACINE_SITE', '/projet_back/');

// Variables d'affichage du HTML : 
$contenu = '';
$contenu_gauche = '';
$contenu_droite = '';

// Inclusion des fonctions : 
require_once('fonctions.inc.php');
?>