<?php

// chargement des bibliothèques de fonctions
require_once 'bibli_erestou.php';
require_once 'bibli_generale.php';

// bufferisation des sorties
ob_start();

// affichage de l'entête
affEntete('Accès restreint');

// affichage de la barre de navigation
affNav();

// contenu de la page
affInfos('1');

// affichage du pied de page
affPiedDePage();

// fin du script --> envoi de la page 
ob_end_flush();





function affInfos(string $id):void {
	$bd = bdConnect();
	$sql = 'SELECT * FROM usager
		WHERE usID=\'' . $id . '\'';

	$res = bdSendRequest($bd, $sql);

	echo '<section>',
		'<h3>Accès restreint aux utilisateurs authentifiés</h3>',
		'<ul id="user_infos" style="list-style-type: disc;">',
			'<li><span>ID : ', $id, '</span></li>',
			'<li>SID : ...</li>';

	while ($tab = mysqli_fetch_assoc($res)) {
		foreach ($tab as $key => $value) {
			echo '<li>', $key, ' : ', $value, '</li>';
		}
	}

	echo '</ul></section>';

	mysqli_free_result($res);
	mysqli_close($bd);
}