<?php
App::uses('DocumentsAssessment', 'Model');

/**
 * DocumentsAssessment Test Case
 *
 */
class DocumentsAssessmentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.documents_assessment',
		'app.document',
		'app.users_round',
		'app.user',
		'app.group',
		'app.annotation',
		'app.type',
		'app.project',
		'app.round',
		'app.types_round',
		'app.documents_project',
		'app.projects_user',
		'app.question',
		'app.annotations_question',
		'app.post'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DocumentsAssessment = ClassRegistry::init('DocumentsAssessment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DocumentsAssessment);

		parent::tearDown();
	}

}
