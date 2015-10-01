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
class Round extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {

        if (isset($this->data[$this->name]['description'])) {
            App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
            $html = $this->data[$this->name]['description'];
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $clean_html = $purifier->purify($html);
            $this->data[$this->name]['description'] = $clean_html;
        }

        if (isset($this->data[$this->name]['title'])) {
            $this->data[$this->name]['title'] = ucfirst($this->data[$this->name]['title']);
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
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'this field cannot be empty',
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
        'ends_in_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'this field cannot be empty',
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
        'UsersRound' => array(
            'className' => 'UsersRound',
            'foreignKey' => 'round_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => '',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Type' => array(
            'className' => 'Type',
            'joinTable' => 'types_rounds',
            'foreignKey' => 'round_id',
            'associationForeignKey' => 'type_id',
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
