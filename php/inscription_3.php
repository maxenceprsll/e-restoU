<?php

// chargement des bibliothèques de fonctions
require_once 'bibli_erestou.php';
require_once 'bibli_generale.php';

// bufferisation des sorties
ob_start();

// affichage de l'entête
affEntete('Vérification des données et enregistrement du nouvel utilisateur dans la base de données');

// affichage de la barre de navigation
affNav();

// contenu de la page

if (parametresControle('post', ['login', 'passe1', 'passe2', 'nom', 'prenom', 'email', 'naissance', 'btnInscription'])) {
	applyTrimToPostValues();

	if (verifications() == 0) {
		$date = str_replace('-', '', $_POST['naissance']);
		$passe = password_hash($_POST['passe1'], PASSWORD_DEFAULT);
		addUser($_POST['nom'], $_POST['prenom'], $date, $_POST['login'], $passe, $_POST['email']);
	}
}

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();

//_______________________________________________________________
/**
 * Verify each input value
 *
 * @return int 		1 si au moins une erreur est détectée, 0 sinon
 */
function verifications():int {
	$errors = [];

	foreach ($_POST as $value) {
		if (mb_strlen($value) == 0) {
			$errors['empty_field'] = 'Un ou plusieurs champs sont vides';
			affErrors($errors);
			return 1;
		}
	}

	$_POST['email'] = strtolower($_POST['email']);

	// login
	if (!preg_match('/^[a-z]{1}[a-z0-9]{3,7}$/u', $_POST['login']))
		$errors['login'] = 'Le login doit contenir entre 4 et 8 lettres minuscules sans accents, ou chiffres, et commencer par une lettre.';

	// password
	if (strcmp($_POST['passe1'], $_POST['passe2']))
		$errors['same_pass'] = 'Les mots de passe doivent être identiques.';
	$len = mb_strlen($_POST['passe1']);
	if ($len < 4 || $len > 20)
		$errors['pass'] = 'Le mot de passe doit être consitué de 4 à 20 caractères.';

	// firstname lastname
	if (!preg_match('/^[[:alpha:]\s\-\']+$/u', $_POST['prenom']))
		$errors['firstname'] = 'Le prénom ne doit pas contenir de tags HTML.';
	if (mb_strlen($_POST['prenom']) > 80)
		$errors['firstname_len'] = 'Le prénom ne doit pas dépasser 80 caractères.';
	if (!preg_match('/^[[:alpha:]\s\-\']+$/u', $_POST['nom']))
		$errors['lastname'] = 'Le nom ne doit pas contenir de tags HTML.';
	if (mb_strlen($_POST['nom']) > 50)
		$errors['lastname_len'] = 'Le nom ne doit pas dépasser 50 caractères.';

	// email
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		$errors['lastname'] = 'L\'adresse email n\'est pas valide.';
	if (mb_strlen($_POST['email']) > 80)
		$errors['email_len'] = 'L\'adresse email ne doit pas dépasser 80 caractères.';

	// naissance + age
	if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/u', $_POST['naissance'])) {
		$errors['birthdate'] = 'La date de naissance n\'est pas valide.';
	} else {
		$b = preg_split('/-/', $_POST['naissance']);
		$is_valid = checkdate($b[1], $b[2], $b[0]);
		if (!checkdate($b[1], $b[2], $b[0])) {
			$errors['birthdate'] = 'La date de naissance n\'est pas valide.';
		} else {
			if (getAge($_POST['naissance']) < 16) {
				$errors['age'] = 'Vous devez avoir au moins 16 ans pour vous inscrire.';
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
				$errors['login_exists'] = 'Le login est déjà utilisé.';
			if (strcmp($tab['usMail'], $_POST['email']) == 0)
				$errors['email_exists'] = 'L\'adresse email est déjà utilisée.';
		}

		mysqli_free_result($res);
		mysqli_close($bd);


	}

	if (count($errors) != 0) {
		affErrors($errors);
		return 1;
	}
	return 0;
}



//_______________________________________________________________
/**
 * Display input errors
 *
 * @return void
 */
function affErrors(array $errors) {
	echo '<section>',
	'<p>Les erreurs suivantes ont été relevées lors de votre inscription :</p>';
	echo '<ul style="list-style-type: disc;">';
	foreach ($errors as $key => $value) {
		echo '<li>', $value, '</li>';
	}
	echo '</ul></section>';
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
 * @return int
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
