<?php
App::uses('Relation', 'Model');

/**
 * Relation Test Case
 *
 */
class RelationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.relation',
		'app.project',
		'app.round',
		'app.users_round',
		'app.user',
		'app.group',
		'app.annotation',
		'app.type',
		'app.question',
		'app.annotations_question',
		'app.types_round',
		'app.document',
		'app.documents_project',
		'app.post',
		'app.projects_user',
		'app.annotations_inter_relations'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Relation = ClassRegistry::init('Relation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Relation);

		parent::tearDown();
	}

}
