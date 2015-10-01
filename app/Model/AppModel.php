<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    function validateNameJoke($field = array(), $name_field = null) {
        return $this -> data[$this -> name][$name_field] != 'Removing...';
    }

    function updateAll($fields, $conditions = true, $recursive = null) {
        if (!isset($recursive)) {
            $recursive = $this -> recursive;
        }

        if ($recursive == -1) {
            $this -> unbindModel(array('belongsTo' => array_keys($this -> belongsTo), 'hasOne' => array_keys($this -> hasOne)), true);
        }

        return parent::updateAll($fields, $conditions);
    }
    
   function deleteAll( $conditions, $cascade = true, $callbacks = false,$recursive=-1) {
        if (!isset($recursive)) {
            $recursive = $this -> recursive;
        }

        if ($recursive == -1) {
            $this -> unbindModel(array('belongsTo' => array_keys($this -> belongsTo), 'hasOne' => array_keys($this -> hasOne)), true);
        }

        return parent::deleteAll( $conditions, $cascade , $callbacks );
    }
   

    

}
