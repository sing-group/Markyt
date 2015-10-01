<?php
App::uses('ConsensusAnnotation', 'Model');

/**
 * ConsensusAnnotation Test Case
 *
 */
class ConsensusAnnotationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.consensus_annotation',
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
		$this->ConsensusAnnotation = ClassRegistry::init('ConsensusAnnotation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ConsensusAnnotation);

		parent::tearDown();
	}

}
