<?php

App::uses('AppModel', 'Model');

/**
 * AnnotationsInterRelation Model
 *
 * @property Relation $Relation
 */
class NetworkEdge extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
          'relation_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'annotation_a_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'annotation_b_id' => array(
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
          'Relation' => array(
                'className' => 'Relation',
                'foreignKey' => 'relation_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          ),
          'ProjectsNetwork' => array(
                'className' => 'ProjectsNetwork',
                'foreignKey' => 'project_network_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
          )
    );
}
