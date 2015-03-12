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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function blink1_install() {
	$command = dirname(__FILE__) . '/../resources/blink1-tool-x86_64';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
	$command = dirname(__FILE__) . '/../resources/blink1-tool-armv6l';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
	$command = dirname(__FILE__) . '/../resources/blink1-tool-armv7l';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
}

function blink1_update() {
	$command = dirname(__FILE__) . '/../resources/blink1-tool-x86_64';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
	$command = dirname(__FILE__) . '/../resources/blink1-tool-armv6l';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
	$command = dirname(__FILE__) . '/../resources/blink1-tool-armv7l';
	if (file_exists($command)) {
		shell_exec('chmod +x ' . $command);
	}
}

?>