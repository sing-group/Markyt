<?php
App::uses('AnnotationsRelation', 'Model');

/**
 * AnnotationsRelation Test Case
 *
 */
class AnnotationsRelationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.annotations_relation',
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
		'app.projects_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AnnotationsRelation = ClassRegistry::init('AnnotationsRelation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AnnotationsRelation);

		parent::tearDown();
	}

}
