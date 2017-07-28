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
            $text = $this->data[$this->name]['title'];
            $text = $this->encodeToUTF8($text);
            $this->data[$this->name]['title'] = $this->cleanText($text);
        }
        App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $deflateRate = 9;
        $debug = Configure::read('debug');
        if ($debug != 0) {
            $deflateRate = 0;
        }
        if (isset($this->data[$this->name]['html'])) {
            $dirty_html = $this->data[$this->name]['html'];
            $clean_html = $purifier->purify($dirty_html);
            $clean_html = $this->encodeToUTF8($clean_html);
            $this->data[$this->name]['html'] = gzdeflate($clean_html, $deflateRate);
        }
        if (!isset($this->data[$this->name]['raw'])) {
            $this->data[$this->name]['raw'] = strip_tags($this->data[$this->name]['html']);
        }
        if (isset($this->data[$this->name]['raw'])) {
            $text = $this->data[$this->name]['raw'];
            $text = $this->encodeToUTF8($text);
            $this->data[$this->name]['raw'] = gzdeflate($this->cleanText($text), $deflateRate);
        }
        if (!isset($this->data[$this->name]["id"]) && (!isset($this->data[$this->name]['external_id']) || trim($this->data[$this->name]['external_id']) == '')) {
            $this->data[$this->name]['external_id'] = "MID" . date("dmyhms");
        }
        return true;
    }

    public function cleanText(&$content) {
        $end = false;
        $regex = "/<(\d)/miu";
        $content = preg_replace($regex, "≺$1", trim($content));
        $regex = "/<([^>]*(<|$))/miu";
        while (!$end) {
            preg_match($regex, $content, $matches);
            $end = empty($matches);
            $content = preg_replace($regex, "≺$1", trim($content));
        }
        $content = strip_tags($content);
        return str_replace("≺", "<", $content);
    }

    public function afterFind($results, $primary = false) {
        $name = $this->name;
        foreach ($results as $key => $val) {
            if (isset($val[$name]['html'])) {
                $results[$key][$name]['html'] = @gzinflate($val[$name]['html']);
            }
            if (isset($val[$name]['raw'])) {
                $results[$key][$name]['raw'] = @gzinflate($val[$name]['raw']);
            }
            if (isset($val[$name]['abstract'])) {
                $results[$key][$name]['abstract'] = @gzinflate($val[$name]['abstract']);
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
                'notBlank' => array(
                      'rule' => array('notBlank'),
                      'message' => 'This field cannot be empty',
                //'allowEmpty' => false,
                ),
                'noJokeName' => array(
                      'rule' => array('validateNameJoke', 'title'),
                      'message' => 'Invalid title',
                //'allowEmpty' => false,
                ),
          ),
          'html' => array(
                'notBlank' => array(
                      'rule' => array('notBlank'),
                      'message' => 'This field cannot be empty',
                //'allowEmpty' => false,
                ),
          ),
    );

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
          ),
          'DocumentsAssessment' => array(
                'className' => 'DocumentsAssessment',
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
