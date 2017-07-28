<?php

App::uses('AppModel', 'Model');

/**
 * Document Model
 *
 * @property UsersRound $UsersRound
 * @property Project $Project
 */
class PredictionDocument extends AppModel {

    var $actsAs = array('Containable');

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['text_marked'])) {
                $results[$key][$name]['text_marked'] = @gzinflate($val[$name]['text_marked']);
            }
        }
        return $results;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
          'title' => array(
                'notBlank' => array(
                      'rule' => array('notBlank'),
                      'message' => 'This field cannot be empty',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
                'noJokeName' => array(
                      'rule' => array('validateNameJoke', 'title'),
                      'message' => 'Invalid title',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'html' => array(
                'notBlank' => array(
                      'rule' => array('notBlank'),
                      'message' => 'This field cannot be empty',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed
}
