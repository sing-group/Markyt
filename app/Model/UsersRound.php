<?php

App::uses('AppModel', 'Model');

/**
 * UsersRound Model
 *
 * @property User $User
 * @property Round $Round
 * @property Document $Document
 * @property Annotation $Annotation
 */
class UsersRound extends AppModel {

    var $actsAs = array('Containable');

    public function beforeSave($options = array()) {
        //comprimimos el texto
        if (isset($this->data[$this->name]['text_marked'])) {
            /*App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
            $config = HTMLPurifier_Config::createDefault();
            $dirty_html = $this->data[$this->name]['text_marked'];
            $purifier = new HTMLPurifier($config);
            $clean_html = $purifier->purify($dirty_html); */          
            $this->data[$this->name]['text_marked'] = gzdeflate($this->data[$this->name]['text_marked'], 9);
        }
        return true;
    }

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['text_marked']) && $val[$name]['text_marked'] != '') {
                $results[$key][$name]['text_marked'] = @gzinflate($val[$name]['text_marked']);
                
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
        'text_marked' => array(
            'notempty' => array(
                'rule' => array('notempty'),
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
        ),
        'Document' => array(
            'className' => 'Document',
            'foreignKey' => 'document_id',
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
            'foreignKey' => 'users_round_id',
            'dependent' => TRUE,
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

}
