<?php

App::uses('AppModel', 'Model');

/**
 * AnnotationsQuestion Model
 *
 * @property Annotation $Annotation
 * @property Question $Question
 */
class AnnotationsQuestion extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['answer'])) {
            $this->data[$this->name]['answer'] = ucfirst($this->data[$this->name]['answer']);
        }
        return true;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'annotation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'question_id' => array(
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
        'Annotation' => array(
            'className' => 'Annotation',
            'foreignKey' => 'annotation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Question' => array(
            'className' => 'Question',
            'foreignKey' => 'question_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
