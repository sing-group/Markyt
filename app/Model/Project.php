<?php

App::uses('AppModel', 'Model');

/**
 * Project Model
 *
 * @property Round $Round
 * @property Type $Type
 * @property Document $Document
 * @property User $User
 */
class Project extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['title'])) {
            $this->data[$this->name]['title'] = ucfirst($this->data[$this->name]['title']);
        }
        if (isset($this->data[$this->name]['description'])) {
            App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
            $html = $this->data[$this->name]['description'];
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $clean_html = $purifier->purify($html);
            $this->data[$this->name]['description'] = $clean_html;
        }
        return true;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
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
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Round' => array(
            'className' => 'Round',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
            'dependent' => true
        ),
        'Type' => array(
            'className' => 'Type',
            'foreignKey' => 'project_id',
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
        'Document' => array(
            'className' => 'Document',
            'joinTable' => 'documents_projects',
            'foreignKey' => 'project_id',
            'associationForeignKey' => 'document_id',
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
        'User' => array(
            'className' => 'User',
            'joinTable' => 'projects_users',
            'foreignKey' => 'project_id',
            'associationForeignKey' => 'user_id',
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
