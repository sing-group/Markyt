<?php

App::uses('AppModel', 'Model');

/**
 * Round Model
 *
 * @property Project $Project
 * @property Annotation $Annotation
 * @property Type $Type
 * @property User $User
 */
class ProjectNetwork extends AppModel {

    var $actsAs = array('Containable');

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
          'name' => array(
                'notBlank' => array(
                      'rule' => array('notBlank'),
                      'message' => 'This field cannot be empty',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'operation' => array(
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
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
          'Project' => array(
                'className' => 'Project',
                'foreignKey' => 'project_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          )
    );
    public $hasMany = array(
          'NetworkEdge' => array(
                'className' => 'NetworkEdge',
                'foreignKey' => 'project_network_id',
                'conditions' => '',
                'order' => '',
                'limit' => '',
                'dependent' => true
          ),
    );
}
