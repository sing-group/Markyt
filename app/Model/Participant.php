<?php

App::uses('AppModel', 'Model');

/**
 * Participant Model
 *
 * @property GoldenAnnotation $GoldenAnnotation
 * @property UploadedAnnotation $UploadedAnnotation
 */
class Participant extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    var $actsAs = array('Containable');
    public $displayField = 'email';

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['code']) && strlen(trim($this->data[$this->alias]['code'])) != 0) {
            $this->data[$this->alias]['code'] = AuthComponent::password($this->data[$this->alias]['code']);
        } else {
            unset($this->data[$this->alias]['code']);
        }
        return true;
    }

    public function beforeFind($queryData) {
        if (isset($queryData['conditions'][$this->alias . '.code'])) {
            $queryData['conditions'][$this->alias . '.code'] = AuthComponent::password($queryData['conditions'][$this->alias . '.code']);
        } elseif (isset($queryData['conditions']['code'])) {
            $queryData['conditions']['code'] = AuthComponent::password($queryData['conditions']['code']);
        }
        return $queryData;
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $result) {
            if (isset($results[$key][$this->alias]['code']) && strlen(trim($results[$key][$this->alias]['code'])) != 0) {
                $results[$key][$this->alias]['code'] = AuthComponent::password($results[$key][$this->alias]['code']);
            }
        }
        return $results;
    }

    public $validate = array(
        'email' => array(
//            'email' => array(
////                'rule' => '^.+@ (?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil| biz|info|mobi|name|aero|asia|jobs|museum)$',
//                'rule' => 'email',
//                'message' => 'This email is incorrect Example:user@mail.com',
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
//            ),
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'This field cannot be empty',
                'allowEmpty' => false,
                'required' => true,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
//            'inUse' => array(
//                'rule' => array('isUnique', 'email'),
//                'message' => 'This email is already in use please choose another')
        ),
        'code' => array(
//            'length' => array(
////                'rule' => array('code', '4'),
//                'required' => true,
//                'allowEmpty' => false,
//                'message' => 'Code must have at least 6 characters'),
            'inUse' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This code is already in use please choose another')
        ),
    );

    /**
     * hasOne associations
     *
     * @var array
     */
    public $hasOne = array(
        'GoldenAnnotation' => array(
            'className' => 'GoldenAnnotation',
            'foreignKey' => 'id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'GoldenProject' => array(
            'className' => 'GoldenProject',
            'foreignKey' => 'id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
//        'Document' => array(
//            'className' => 'Document',
//            'foreignKey' => 'document_id',
//            'conditions' => '',
//            'fields' => '',
//            'order' => ''
//        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'UploadedAnnotation' => array(
            'className' => 'UploadedAnnotation',
            'foreignKey' => 'participant_id',
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
        'PredictionFile' => array(
            'className' => 'PredictionFile',
            'foreignKey' => 'participant_id',
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
        'PredictionDocument' => array(
            'className' => 'PredictionDocument',
            'foreignKey' => 'participant_id',
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
        'ProjectsParticipant' => array(
            'className' => 'ProjectsParticipant',
            'foreignKey' => 'participant_id',
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
        'UploadLog' => array(
            'className' => 'UploadLog',
            'foreignKey' => 'participant_id',
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
        'FinalPrediction' => array(
            'className' => 'FinalPrediction',
            'foreignKey' => 'participant_id',
            'dependent' => false,
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
    public $hasAndBelongsToMany = array(
        'Project' => array(
            'className' => 'Project',
            'joinTable' => 'projects_participants',
            'foreignKey' => 'participant_id',
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
    );

}
