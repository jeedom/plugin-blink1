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



  /*     * ***********************Methode static*************************** */



  /*     * *********************Méthodes d'instance************************* */

  public function postSave() {
    $colorAll = $this->getCmd(null,'colorAll');
    if(!is_object($colorAll)){
      $colorAll = new blink1Cmd();
      $colorAll->setName(__('Couleur',__FILE__));
      $colorAll->setIsVisible(1);
    }
    $colorAll->setLogicalId('colorAll');
    $colorAll->setType('action');
    $colorAll->setSubType('color');
    $colorAll->setEqlogic_id($this->getId());
    $colorAll->save();


  }

  /*     * **********************Getteur Setteur*************************** */
}

class blink1Cmd extends cmd {
  /*     * *************************Attributs****************************** */


  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  public function dontRemoveCmd() {
    if($this->getLogicalId() == 'colorAll'){
      return true;
    }
  }

  public function execute($_options = array()) {
    $eqLogic = $this->getEqLogic();
    if($eqLogic->getConfiguration('mode') == 'watch' || $eqLogic->getConfiguration('mode') == 'both'){
      $watchData = '';
      if($this->getLogicalId() == 'colorAll'){
        $watchData =  json_encode(array('color' => $_options['color']));;
      }
      if($this->getLogicalId() == 'patern'){
        $watchData = json_encode(array('pattern' => $this->getConfiguration('patern')));
      }
      $eqLogic->setConfiguration('watchData',$watchData);
      $eqLogic->save();
    }

    if($eqLogic->getConfiguration('mode') == 'internal' || $eqLogic->getConfiguration('mode') == 'both'){
      $url = 'http://'.$eqLogic->getConfiguration('address').':'.$eqLogic->getConfiguration('port');
      if($this->getLogicalId() == 'colorAll'){
        $url.='/blink1/fadeToRGB?rgb='.urlencode($_options['color']);
      }
      if($this->getLogicalId() == 'patern'){
        $url.='/blink1/pattern/play?pname='.urlencode($this->getConfiguration('patern'));
      }
      $request_http = new com_http($url);
      $request_http->exec();
    }
  }

  /*     * **********************Getteur Setteur*************************** */
}

?>
