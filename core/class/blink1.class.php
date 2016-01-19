<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class blink1 extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Méthodes statiques*************************** */

	/*     * *********************Méthodes d'instance************************* */

	public function postSave() {
		$colorAll = $this->getCmd(null, 'colorAll');
		if (!is_object($colorAll)) {
			$colorAll = new blink1Cmd();
			$colorAll->setName(__('Couleur', __FILE__));
			$colorAll->setIsVisible(1);
			$colorAll->setOrder(-1);
		}
		$colorAll->setLogicalId('colorAll');
		$colorAll->setType('action');
		$colorAll->setSubType('color');
		$colorAll->setDisplay('generic_type', 'LIGHT_SET_COLOR');
		$colorAll->setEqlogic_id($this->getId());
		$colorAll->save();
	}

	public function syncPattern() {
		$url = 'http://' . $this->getConfiguration('address') . ':' . $this->getConfiguration('port') . '/blink1/pattern';
		$request_http = new com_http($url);
		$patterns = json_decode($request_http->exec(), true);

		if (is_array($patterns)) {
			foreach ($patterns['patterns'] as $pattern) {
				$find = false;
				foreach ($this->getCmd(null, 'pattern', null, true) as $cmd) {
					if ($cmd->getConfiguration('pattern') == $pattern['name']) {
						$find = true;
					}
				}
				if (!$find) {
					$patternCmd = new blink1Cmd();
					$patternCmd->setName(__($pattern['name'], __FILE__));
					$patternCmd->setIsVisible(1);
					$patternCmd->setLogicalId('pattern');
					$patternCmd->setType('action');
					$patternCmd->setSubType('other');
					$patternCmd->setEqlogic_id($this->getId());
					$patternCmd->setConfiguration('pattern', $pattern['name']);
					$patternCmd->save();
				}
			}
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}

class blink1Cmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Méthodes statiques*************************** */

	/*     * *********************Méthodes d'instance************************* */

	public function dontRemoveCmd() {
		if ($this->getLogicalId() == 'colorAll') {
			return true;
		}
	}

	public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		if ($eqLogic->getConfiguration('mode') == 'watch' || $eqLogic->getConfiguration('mode') == 'both') {
			$watchData = '';
			if ($this->getLogicalId() == 'colorAll') {
				$watchData = json_encode(array('color' => $_options['color']));
			}
			if ($this->getLogicalId() == 'pattern') {
				$watchData = json_encode(array('pattern' => $this->getConfiguration('pattern')));
			}
			$eqLogic->setConfiguration('watchData', $watchData);

		}

		if ($eqLogic->getConfiguration('mode') == 'internal' || $eqLogic->getConfiguration('mode') == 'both') {
			$url = 'http://' . $eqLogic->getConfiguration('address') . ':' . $eqLogic->getConfiguration('port');
			if ($this->getLogicalId() == 'colorAll') {
				$url .= '/blink1/fadeToRGB?rgb=' . urlencode($_options['color']);
			}
			if ($this->getLogicalId() == 'pattern') {
				$url .= '/blink1/pattern/play?pname=' . urlencode($this->getConfiguration('pattern'));
			}
			$request_http = new com_http($url);
			if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {
				$request_http->setNoReportError(true);
				$request_http->exec(0.1, 1);
			} else {
				$request_http->exec();
			}
			if ($eqLogic->getConfiguration('mode') == 'both' && $eqLogic->getConfiguration('doNoRepeatCommand') == 1) {
				$eqLogic->setConfiguration('watchData', '');
			}
		}

		if ($eqLogic->getConfiguration('mode') == 'watch' || $eqLogic->getConfiguration('mode') == 'both') {
			$eqLogic->save();
		}

		if ($eqLogic->getConfiguration('mode') == 'ssh') {
			$command = $eqLogic->getConfiguration('pathtoblink1', './blink1-tool ');
			if ($this->getLogicalId() == 'colorAll') {
				$command .= '--rgb ' . str_replace('#', '', $_options['color']);
			} else {
				$command .= $this->getConfiguration('pattern');
			}
			if ($eqLogic->getConfiguration('device') != '') {
				$command .= ' -d ' . $eqLogic->getConfiguration('device');
			}
			log::add('blink1', 'debug', 'ssh "' . $eqLogic->getConfiguration('username') . '"@"' . $eqLogic->getConfiguration('host') . '" sudo "' . $command . '" 2>&1');
			$request_shell = new com_shell('ssh "' . $eqLogic->getConfiguration('username') . '"@"' . $eqLogic->getConfiguration('host') . '" sudo "' . $command . '" 2>&1');
			if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {
				$request_shell->setBackground(true);
			}
			$result = $request_shell->exec();
			log::add('blink1', 'debug', $result);
		}

		if ($eqLogic->getConfiguration('mode') == 'local') {
			$command = dirname(__FILE__) . '/../../resources/blink1-tool-';
			$uname = posix_uname();
			if (!file_exists($command . $uname['machine'])) {
				throw new Exception(__('Aucun exécutable trouvé pour l\'architecture : ', __FILE__) . $command . $uname['machine']);
			}
			$command = $command . $uname['machine'] . ' ';
			if ($this->getLogicalId() == 'colorAll') {
				$command .= '--rgb ' . str_replace('#', '', $_options['color']);
			} else {
				$command .= $this->getConfiguration('pattern');
			}
			if ($eqLogic->getConfiguration('device') != '') {
				$command .= ' -d ' . $eqLogic->getConfiguration('device');
			}
			$request_shell = new com_shell('sudo ' . $command . ' 2>&1');
			if (isset($_options['speedAndNoErrorReport']) && $_options['speedAndNoErrorReport'] == true) {
				$request_shell->setBackground(true);
			}
			$request_shell->exec();
		}
	}

/*     * **********************Getteur Setteur*************************** */
}

?>
