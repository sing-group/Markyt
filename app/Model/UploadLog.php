<?php

App::uses('AppModel', 'Model');

/**
 * UploadLog Model
 *
 * @property Participant $Participant
 */
class UploadLog extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'id';

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
