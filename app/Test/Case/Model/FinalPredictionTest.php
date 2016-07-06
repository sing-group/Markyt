<?php
App::uses('FinalPrediction', 'Model');

/**
 * FinalPrediction Test Case
 *
 */
class FinalPredictionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.final_prediction',
		'app.participant',
		'app.golden_annotation',
		'app.golden_project',
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
		'app.connection',
		'app.post',
		'app.projects_user',
		'app.relation',
		'app.annotations_inter_relations',
		'app.projects_participant',
		'app.uploaded_annotation',
		'app.prediction_file',
		'app.prediction_document',
		'app.upload_log',
		'app.participants_final_prediction'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->FinalPrediction = ClassRegistry::init('FinalPrediction');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->FinalPrediction);

		parent::tearDown();
	}

}
