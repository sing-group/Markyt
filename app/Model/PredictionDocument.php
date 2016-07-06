<?php

App::uses('AppModel', 'Model');

/**
 * Document Model
 *
 * @property UsersRound $UsersRound
 * @property Project $Project
 */
class PredictionDocument extends AppModel {

    var $actsAs = array('Containable');
//    public $virtualFields = array("full_name" => "CONCAT(COALESCE(external_id,''),' - ' ,COALESCE(title,''))");

//    public function beforeSave($options = array()) {
//        if (isset($this->data[$this->name]['title'])) {
//            $this->data[$this->name]['title'] = ucfirst($this->data[$this->name]['title']);
//        }
//        if (isset($this->data[$this->name]['html'])) {
//            //si los documentos ya estan limpios no se limpian
//            App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
//            $config = HTMLPurifier_Config::createDefault();
////            $dirty_html = htmlentities($this->data[$this->name]['html']);
//            $dirty_html = $this->data[$this->name]['html'];
//            $purifier = new HTMLPurifier($config);
//            $clean_html = $purifier->purify($dirty_html);
//            $this->data[$this->name]['html'] = gzdeflate($clean_html, 9);
//        }
//        return true;
//    }

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['text_marked'])) {
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
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
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
                'message' => 'This field cannot be empty',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

   
}
