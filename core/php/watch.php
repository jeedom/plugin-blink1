<?php

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";


if (trim(config::byKey('api')) == '') {
	echo 'Vous n\'avez aucune clé API configurée, veuillez d\'abord en générer une (Page Générale -> Administration -> Configuration';
		log::add('jeeEvent', 'error', 'Vous n\'avez aucune clé API configurée, veuillez d\'abord en générer une (Page Générale -> Administration -> Configuration');
			die();
		}


		if (config::byKey('api') != init('apikey')) {
			connection::failed();
			throw new Exception('Clef API non valide, vous n\'êtes pas autorisé à effectuer cette action (jeeApi). Demande venant de :' . getClientIp() . 'Clef API : ' . init('apikey') . init('api'));
		}
		connection::success('blink1');


		$blink1 = blink1::byId(init('id'));

		if(!is_object($blink1)){
			throw new Exception('Aucun équipement correspondant');
		}

		echo $blink1->getConfiguration('watchData');

		if($blink1->getConfiguration('doNoRepeatCommand') == 1){
			$blink1->setConfiguration('watchData','');
			$blink1->save();
		}
		?>
