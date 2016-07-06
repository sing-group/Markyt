<?php

App::uses('AppModel', 'Model');

/**
 * Type Model
 *
 * @property Project $Project
 * @property Annotation $Annotation
 * @property Question $Question
 * @property Round $Round
 */
class Type extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['name'])) {
            $name = str_replace(' ', '_', $this->data[$this->name]['name']);
            $this->data[$this->name]['name'] = str_replace("'", '´', strtoupper($name));
        }
        if (isset($this->data[$this->name]['description'])) {
            $this->data[$this->name]['description'] = str_replace("'", '´', ucfirst($this->data[$this->name]['description']));
        }
        if (isset($this->data[$this->name]['colour'])) {
            $colour=$this->data[$this->name]['colour'];
            $colour=str_replace("rgba(", '',$colour);
            $colour=str_replace(")", '',$colour);        
            $this->data[$this->name]['colour'] = $colour;
        }
        
        return true;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'project_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'duplicatesCreate' => array(
                'rule' => array('limitDuplicates', 0),
                'message' => 'Sorry but there is already a name for this type in this project . Please change the name of this type',
                'on' => 'create'
            ),
            'duplicatesUpdate' => array(
                'rule' => array('limitDuplicates', 1),
                'message' => 'Sorry but there is already a name for this type in this project . Please change the name of this type',
                'on' => 'update'
            ),
        ),
        'colour' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );

    function limitDuplicates($data, $num) {
        $cond = array('Type.name' => ucfirst($this->data['Type']['name']), 'Type.project_id' => $this->data['Type']['project_id']);
        $numName = $this->find('count', array('recursive' => -1, 'conditions' => $cond));
        return $numName <= $num;
    }

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

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Annotation' => array(
            'className' => 'Annotation',
            'foreignKey' => 'type_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
            'dependent' => true,
        ),
        'Question' => array(
            'className' => 'Question',
            'foreignKey' => 'type_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
            'dependent' => true
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Round' => array(
            'className' => 'Round',
            'joinTable' => 'types_rounds',
            'foreignKey' => 'type_id',
            'associationForeignKey' => 'round_id',
            'unique' => 'keepExisting',
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
