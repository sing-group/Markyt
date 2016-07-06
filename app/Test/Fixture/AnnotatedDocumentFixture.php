<?php
/**
 * AnnotatedDocumentFixture
 *
 */
class AnnotatedDocumentFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'users_round_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'round_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'document_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'text_marked' => array('type' => 'binary', 'null' => true, 'default' => null),
		'annotation_minutes' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'users_round_id'), 'unique' => 1),
			'fk_documents_annotated_users_rounds1' => array('column' => 'users_round_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'users_round_id' => 1,
			'user_id' => 1,
			'round_id' => 1,
			'document_id' => 1,
			'text_marked' => 'Lorem ipsum dolor sit amet',
			'annotation_minutes' => 1
		),
	);

}
