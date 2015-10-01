<?php
App::uses('AppModel', 'Model');
/**
 * TypesRound Model
 *
 * @property Type $Type
 * @property Round $Round
 */
class TypesRound extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'type_id' => array(
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
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Type' => array(
			'className' => 'Type',
			'foreignKey' => 'type_id',
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
		)
	);
}
