<?php
/**
 * DocumentsAssessmentFixture
 *
 */
class DocumentsAssessmentFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'document_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'positive' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'neutral' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'negative' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'about_author' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'topic' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'document_id', 'user_id', 'project_id'), 'unique' => 1),
			'fk_documents_has_users_users1_idx' => array('column' => 'user_id', 'unique' => 0),
			'fk_documents_has_users_documents1_idx' => array('column' => 'document_id', 'unique' => 0),
			'fk_documents_rates_projects1_idx' => array('column' => 'project_id', 'unique' => 0)
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
			'document_id' => 1,
			'user_id' => 1,
			'project_id' => 1,
			'positive' => 1,
			'neutral' => 1,
			'negative' => 1,
			'about_author' => 'Lorem ipsum dolor sit amet',
			'topic' => 'Lorem ipsum dolor sit amet',
			'note' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		),
	);

}
