<?php

// chargement des bibliothèques de fonctions
require_once 'bibli_erestou.php';
require_once 'bibli_generale.php';

// bufferisation des sorties
ob_start();

// affichage de l'entête
affEntete('Inscription');

// affichage de la barre de navigation
affNav();

// contenu de la page 
affForm();

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();

//_______________________________________________________________
/**
 * Affichage du formulaire d'inscription
 *
 * @return void
 */
function affForm():void {
	echo 
	'<section><h3>Formulaire d\'inscription</h3>',
		'<p>Pour vous inscrire, merci de fournir les informations suivantes.</p>',
		'<form action="inscription_3.php" method="POST">',
			'<table id="inscription">',
				'<tr>',
					'<td><label for="login">Votre login :</label></td>',
					'<td><input id="login" name="login" type="text" placeholder="4 à 8 lettres minuscules ou chiffres" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="passe1">Votre mot de passe :</label></td>',
					'<td><input id="passe1" name="passe1" type="password" placeholder="4 caractères minimum" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="passe2">Répétez le mot de passe :</label></td>',
					'<td><input id="passe2" name="passe2" type="password" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="nom">Votre nom :</label></td>',
					'<td><input id="nom" name="nom" type="text" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="prenom">Votre prénom :</label></td>',
					'<td><input id="prenom" name="prenom" type="text" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="email">Votre adresse email :</label></td>',
					'<td><input id="email" name="email" type="email" required></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="naissance">Votre date de naissance :</label></td>',
					'<td><input id="naissance" name="naissance" type="date" required></input></td>',
				'</tr>',
				'<tr><td colspan="2">',
					'<input type="submit" name="btnInscription" value="S\'inscrire">',
					'<input type="reset" value="Réinitialiser">',
				'</td></tr>',
			'</table>',
		'</form>',
	'</section>';
}