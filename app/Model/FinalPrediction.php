<?php

App::uses('AppModel', 'Model');

/**
 * FinalPrediction Model
 *
 * @property Participant $Participant
 */
class FinalPrediction extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'file_name';

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['file'])) {
            $this->data[$this->name]['file'] = gzdeflate($this->data[$this->name]['file'], 9);
        }
        return true;
    }

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['file'])) {
                $results[$key][$name]['file'] = @gzinflate($val[$name]['file']);
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
          'participant_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
          'Participant' => array(
                'className' => 'Participant',
                'foreignKey' => 'participant_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          )
    );
}
