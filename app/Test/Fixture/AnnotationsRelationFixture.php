<?php
/**
 * AnnotationsRelationFixture
 *
 */
class AnnotationsRelationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'relation_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'annotation_a_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'annotation_b_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'relation_id', 'annotation_a_id'), 'unique' => 1),
			'fk_annotations_has_relations_relations1_idx' => array('column' => 'relation_id', 'unique' => 0),
			'fk_annotations_has_relations_annotations1_idx' => array('column' => 'annotation_a_id', 'unique' => 0)
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
			'relation_id' => 1,
			'annotation_a_id' => 1,
			'annotation_b_id' => 'Lorem ipsum dolor sit amet'
		),
	);

}
