<?php
App::uses('Round', 'Model');

/**
 * Round Test Case
 *
 */
class RoundTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.round',
		'app.project',
		'app.type',
		'app.annotation',
		'app.document',
		'app.users_round',
		'app.user',
		'app.group',
		'app.post',
		'app.projects_user',
		'app.documents_project',
		'app.question',
		'app.annotations_question',
		'app.types_round'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Round = ClassRegistry::init('Round');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Round);

		parent::tearDown();
	}

}
