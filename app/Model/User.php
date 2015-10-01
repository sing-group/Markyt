<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 * @property Group $Group
 * @property Annotation $Annotation
 * @property Project $Project
 * @property Round $Round
 */
class User extends AppModel {

    var $actsAs = array('Containable');
    public $virtualFields = array("full_name" => "CONCAT(username,' ' ,surname)");
    public $displayField = 'full_name';

    public function beforeSave($options = array()) {
        if (!empty($this->data['User']['image']) && $this->data['User']['image']['size'] != 0) {
            $fileData = fread(fopen($this->data['User']['image']['tmp_name'], 'r'), $this->data['User']['image']['size']);
            $this->data['User']['image_type'] = $this->data['User']['image']['type'];
            $this->data['User']['image'] = $fileData;
        } else {
            unset($this->data['User']['image']);
            unset($this->data['User']['image_extension']);
        }

        if (isset($this->data[$this->alias]['password']) && strlen(trim($this->data[$this->alias]['password'])) != 0) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data['User']['password']);
        } else {
            unset($this->data['User']['password']);
        }
        return true;
    }

    public function isImage($field = array(), $name_field = null) {
        if ((empty($this->data['User']['image']['type']) || strpos($this->data['User']['image']['type'], 'image') !== false ) && (number_format($this->data['User']['image']['size'] / 1048576, 2) < 1 || $this->data['User']['image']['size'] == 0 )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'group_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'username' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'this field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'noJokeName' => array(
                'rule' => array('validateNameJoke', 'username'),
                'message' => 'Invalid username',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'inUse' => array('rule' => array('isUnique', 'username'), 'message' => 'This user name is already in use please choose another')
        ),
        'surname' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'this field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'email' => array(
            'email' => array(
                'rule' => array('email'),
                'message' => 'This email is incorrect Example:user@mail.com'
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'inUse' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This email is already in use please choose another')
        ),
        'password' => array(
            'length' => array(
                'rule' => array('minLength', '6'),
                'required' => true,
                'allowEmpty' => true,
                'message' => 'Password must have at least 6 characters'),
        ),
        'image' => array(
            'rule' => array('isImage'),
            'allowEmpty' => true,
            'message' => 'Image extension is not valid. Try with images gif, jpeg, png, jpg'),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
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
            'foreignKey' => 'user_id',
            'dependent' => TRUE,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Post' => array(
            'className' => 'Post',
            'foreignKey' => 'user_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Project' => array(
            'className' => 'Project',
            'joinTable' => 'projects_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'project_id',
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
        'Round' => array(
            'className' => 'Round',
            'joinTable' => 'users_rounds',
            'foreignKey' => 'user_id',
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
