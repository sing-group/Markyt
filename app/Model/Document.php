<?php

App::uses('AppModel', 'Model');

/**
 * Document Model
 *
 * @property UsersRound $UsersRound
 * @property Project $Project
 */
class Document extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->name]['title'])) {
            $this->data[$this->name]['title'] = ucfirst($this->data[$this->name]['title']);
        }
        if (isset($this->data[$this->name]['html'])) {
            //si los documentos ya estan limpios no se limpian
            App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
            $config = HTMLPurifier_Config::createDefault();
            $dirty_html = $this->data[$this->name]['html'];
            $purifier = new HTMLPurifier($config);
            $clean_html = $purifier->purify($dirty_html);
            $this->data[$this->name]['html'] = gzdeflate($clean_html, 9);
        }
        return true;
    }

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['html'])) {
                $results[$key][$name]['html'] = @gzinflate($val[$name]['html']);
            }
        }
        return $results;
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
        'html' => array(
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
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'UsersRound' => array(
            'className' => 'UsersRound',
            'foreignKey' => 'document_id',
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
        'Project' => array(
            'className' => 'Project',
            'joinTable' => 'documents_projects',
            'foreignKey' => 'document_id',
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
        )
    );

}
