<?php

// chargement des bibliothèques de fonctions
require_once 'bibli_erestou.php';
require_once 'bibli_generale.php';

// bufferisation des sorties
ob_start();

// affichage de l'entête
affEntete('Connexion');

// affichage de la barre de navigation
affNav();

// contenu de la page
echo '<a href="inscription.php">S\'inscrire</a>';

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();
