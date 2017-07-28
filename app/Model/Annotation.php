<?php

App::uses('AppModel', 'Model');

/**
 * Annotation Model
 *
 * @property Type $Type
 * @property Document $Document
 * @property Round $Round
 * @property User $User
 * @property Question $Question
 */
class Annotation extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['annotated_text'])) {
            $this->data[$this->name]['annotated_text'] = $this->encodeToUTF8($this->data[$this->name]['annotated_text']);
        }
        return true;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
          'type_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'document_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
          'round_id' => array(
                'numeric' => array(
                      'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
          ),
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
          'annotated_text' => array(
                'notBlank' => array(
                      'rule' => array('notBlank'),
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
          'Type' => array(
                'className' => 'Type',
                'foreignKey' => 'type_id',
                'conditions' => '',
                'fields' => '',
                'order' => '',
          ),
          'Document' => array(
                'className' => 'Document',
                'foreignKey' => 'document_id',
                'conditions' => '',
                'fields' => '',
                'order' => '',
          ),
          'Round' => array(
                'className' => 'Round',
                'foreignKey' => 'round_id',
                'conditions' => '',
                'fields' => '',
                'order' => '',
          ),
          'User' => array(
                'className' => 'User',
                'foreignKey' => 'user_id',
                'conditions' => '',
                'fields' => '',
                'order' => '',
          )
    );
    public $hasMany = array(
          'AnnotationsQuestion' => array(
                'className' => 'AnnotationsQuestion',
                'foreignKey' => 'annotation_id',
                'dependent' => false,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'exclusive' => '',
                'finderQuery' => '',
                'counterQuery' => ''
          ),
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
          'Question' => array(
                'className' => 'Question',
                'joinTable' => 'annotations_questions',
                'foreignKey' => 'annotation_id',
                'associationForeignKey' => 'question_id',
                'unique' => 'keepExisting',
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'deleteQuery' => '',
                'insertQuery' => ''
          ),
          'AnnotationsInterRelation' => array(
                'className' => 'AnnotationsInterRelation',
                'joinTable' => 'annotations_inter_relations',
                'foreignKey' => '',
                'associationForeignKey' => '',
                'unique' => '',
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'deleteQuery' => '',
                'insertQuery' => ''
          )
    );
}
