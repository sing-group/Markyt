<?php

App::uses('AppModel', 'Model');

/**
 * Job Model
 *
 * @property User $User
 */
class Job extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'program';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
          'user_id' => array(
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
          'User' => array(
                'className' => 'User',
                'foreignKey' => 'user_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          ),
          'Round' => array(
                'className' => 'Round',
                'foreignKey' => 'round_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          )
    );
}
