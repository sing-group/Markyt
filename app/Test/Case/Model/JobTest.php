<?php
App::uses('Job', 'Model');

/**
 * Job Test Case
 *
 */
class JobTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.job',
		'app.user',
		'app.group',
		'app.annotation',
		'app.type',
		'app.project',
		'app.round',
		'app.users_round',
		'app.document',
		'app.documents_assessment',
		'app.documents_project',
		'app.annotated_document',
		'app.types_round',
		'app.relation',
		'app.annotations_inter_relations',
		'app.projects_user',
		'app.participant',
		'app.golden_annotation',
		'app.golden_project',
		'app.uploaded_annotation',
		'app.prediction_file',
		'app.prediction_document',
		'app.projects_participant',
		'app.upload_log',
		'app.final_prediction',
		'app.question',
		'app.annotations_question',
		'app.connection',
		'app.post'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Job = ClassRegistry::init('Job');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Job);

		parent::tearDown();
	}

}
