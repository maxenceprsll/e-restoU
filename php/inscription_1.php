<?php

// chargement des bibliothèques de fonctions
require_once 'bibli_erestou.php';
require_once 'bibli_generale.php';

// bufferisation des sorties
ob_start();

// affichage de l'entête
affEntete('Réception des données saisies');

// affichage de la barre de navigation
affNav();

// contenu de la page 
affValues();

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();

//_______________________________________________________________
/**
 * Affichage les valeurs recues depuis le formulaire d'inscription
 *
 * @return void
 */
function affValues():void {
	echo
	'<section><h3>Avec une boucle foreach</h3><ul style="list-style-type: disc;">';

	foreach ($_POST as $key => $value) {
		echo '<li>clé : ', $key, ', valeur = ', $value, '</li>';
	}
	echo '</ul>
		<h3>Avec var_dump()</h3>',
		'<pre>',
			var_dump($_POST),
		'</pre>',

		'<h3>Avec print_r()</h3>',
		'<pre>',
			print_r($_POST, true),
		'</pre>',
	'<section>';
}