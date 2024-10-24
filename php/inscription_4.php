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
$errors = [];
if (isset($_POST['btnInscription'])) {
	$errors = traitementInscription();
}
affFormulaire($errors);

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();





//_______________________________________________________________
/**
 * Affichage du formulaire d'inscription
 *
 * @param array 	$err 	tableau des erreurs éventuelles
 *
 * @return void
 */
function affFormulaire(array $err):void {
	$post = ['login' => '', 'nom' => '', 'prenom' => '', 'email' => '', 'naissance' =>'' ];

	echo 
	'<section><h3>Formulaire d\'inscription</h3>',
		'<p>Pour vous inscrire, merci de fournir les informations suivantes.</p>';

	if (isset($_POST['btnInscription'])) {
		$post = $_POST;
		affErrors($err);
	}

	echo
		'<form action="inscription.php" method="POST">',
			'<table id="inscription">',
				'<tr>',
					'<td><label for="login">Votre login :</label></td>',
					'<td><input id="login" name="login" type="text" placeholder="4 à 8 lettres minuscules ou chiffres" required value="', $post['login'], '"></input></td>',
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
					'<td><input id="nom" name="nom" type="text" required value="', $post['nom'], '"></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="prenom">Votre prénom :</label></td>',
					'<td><input id="prenom" name="prenom" type="text" required value="', $post['prenom'], '"></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="email">Votre adresse email :</label></td>',
					'<td><input id="email" name="email" type="email" required value="', $post['email'], '"></input></td>',
				'</tr>',
				'<tr>',
					'<td><label for="naissance">Votre date de naissance :</label></td>',
					'<td><input id="naissance" name="naissance" type="date" required value="', $post['naissance'], '"></input></td>',
				'</tr>',
				'<tr><td colspan="2">',
					'<input type="submit" name="btnInscription" value="S\'inscrire">',
					'<input type="reset" value="Réinitialiser">',
				'</td></tr>',
			'</table>',
		'</form>',
	'</section>';
}





//_______________________________________________________________
/**
 * Réalise de traitement des données recues
 *
 * @return array 		le tableau des éventuelles erreurs
 */
function traitementInscription():array {
	$errors = [];

	if (!parametresControle('post', ['login', 'passe1', 'passe2', 'nom', 'prenom', 'email', 'naissance', 'btnInscription'])) {
		$errors[] = 'Problème avec avec le tableau global post.';
		return $errors;
	}
	applyTrimToPostValues();

	// Optionnel puisque les champs sont en required
	foreach ($_POST as $value) {
		if (mb_strlen($value) == 0) {
			$errors[] = 'Un ou plusieurs champs sont vides.';
			affErrors($errors);
			return $errors;
		}
	}

	// 
	$_POST['email'] = strtolower($_POST['email']);

	// login
	if (!preg_match('/^[a-z]{1}[a-z0-9]{3,7}$/u', $_POST['login']))
		$errors[] = 'Le login doit contenir entre 4 et 8 lettres minuscules sans accents, ou chiffres, et commencer par une lettre.';

	// password
	if (strcmp($_POST['passe1'], $_POST['passe2']))
		$errors[] = 'Les mots de passe doivent être identiques.';
	$len = mb_strlen($_POST['passe1']);
	if ($len < 4 || $len > 20)
		$errors[] = 'Le mot de passe doit être consitué de 4 à 20 caractères.';

	// lastname firstname
	if (!preg_match('/^[[:alpha:]\s\-\']+$/u', $_POST['nom']))
		$errors[] = 'Le nom ne doit pas contenir de tags HTML.';
	if (mb_strlen($_POST['nom']) > 50)
		$errors[] = 'Le nom ne doit pas dépasser 50 caractères.';
	if (!preg_match('/^[[:alpha:]\s\-\']+$/u', $_POST['prenom']))
		$errors[] = 'Le prénom ne doit pas contenir de tags HTML.';
	if (mb_strlen($_POST['prenom']) > 80)
		$errors[] = 'Le prénom ne doit pas dépasser 80 caractères.';

	// email
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$errors[] = 'L\'adresse email n\'est pas valide.';
	if (mb_strlen($_POST['email']) > 80)
		$errors[] = 'L\'adresse email ne doit pas dépasser 80 caractères.';

	// naissance + age
	if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/u', $_POST['naissance'])) {
		$errors[] = 'La date de naissance n\'est pas valide.';
	} else {
		$b = preg_split('/-/', $_POST['naissance']);
		$is_valid = checkdate($b[1], $b[2], $b[0]);
		if (!checkdate($b[1], $b[2], $b[0])) {
			$errors[] = 'La date de naissance n\'est pas valide.';
		} else {
			if (getAge($_POST['naissance']) < 16) {
				$errors[] = 'Vous devez avoir au moins 16 ans pour vous inscrire.';
			}
		}
	}



	if (count($errors) == 0) {
		$bd = bdConnect();
		$sql = 'SELECT usLogin, usMail FROM usager
			WHERE usLogin=\'' . $_POST['login'] . '\' OR usMail=\'' . $_POST['email'] . '\'';

		$res = bdSendRequest($bd, $sql);

		while ($tab = mysqli_fetch_assoc($res)) {
			if (strcmp($tab['usLogin'], $_POST['login']) == 0)
				$errors[] = 'Le login est déjà utilisé.';
			if (strcmp($tab['usMail'], $_POST['email']) == 0)
				$errors[] = 'L\'adresse email est déjà utilisée.';
		}

		mysqli_free_result($res);
		mysqli_close($bd);
	}

	if (count($errors) == 0) {
		$date = str_replace('-', '', $_POST['naissance']); // cette étape devrait être faite plus tot pour que les vérifications soient avec le bon format des le début
		$passe = password_hash($_POST['passe1'], PASSWORD_DEFAULT);
		addUser($_POST['nom'], $_POST['prenom'], $date, $_POST['login'], $passe, $_POST['email']);

		header('Location: protegee.php');
		exit;
	}

	return $errors;
}





//_______________________________________________________________
/**
 * Display input errors
 *
 * @param array 	$errors 	...
 *
 * @return void
 */
function affErrors(array $errors) {
	echo '<div id="erreurs">',
	'<p>Les erreurs suivantes ont été relevées lors de votre inscription :</p>';
	echo '<ul>';
	foreach ($errors as $key => $value) {
		echo '<li>', $value, '</li>';
	}
	echo '</ul></div>';
}

//_______________________________________________________________
/**
 * Add user to database
 *
 * @param string 	$nom 			...
 * @param string 	$prenom 		...
 * @param string 	$naissance 		...
 * @param string 	$login 			...
 * @param string 	$password 		...
 * @param string 	$email 			...
 *
 * @return void
 */
function addUser(string $nom, string $prenom, int $naissance, string $login, string $password, string $email) {

	$bd = bdConnect();
	$sql = 'INSERT INTO usager (usNom, usPrenom, usDateNaissance, usLogin, usPasse, usMail)
		VALUES (\'' . $nom
		 . '\', \'' . $prenom
		 . '\', ' . $naissance
		 . ', \'' . $login
		 . '\', \'' . $password
		 . '\', \'' . $email . '\')';

	bdSendRequest($bd, $sql);
	mysqli_close($bd);

	echo '<section>',
	'<p>Un nouvel usager a bien été enregistré.</p>',
	'</section>';
}

//_______________________________________________________________
/**
 * Apply trim function to each value of _POST
 *
 * @return  void
 */
function applyTrimToPostValues():void {
	foreach ($_POST as &$value) {
		$value = trim($value);
	}
}

//_______________________________________________________________
/**
 * Calculate age with birthdate
 *
 * @param string 	$birthdate		...
 *
 * @return int 		age
 */
function getAge(string $birthdate):int {
	if (strtotime($birthdate) > time()) {
		return 0;
	}
	$today = new DateTime();
	$birthdate = new DateTime($birthdate);
	$age = $today->diff($birthdate)->y;
	return $age;
}