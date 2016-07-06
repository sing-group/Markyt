<?php
App::uses('JobsController', 'Controller');

/**
 * JobsController Test Case
 *
 */
class JobsControllerTest extends ControllerTestCase {

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
		'app.golden_project',
		'app.projects_user',
		'app.participant',
		'app.golden_annotation',
		'app.uploaded_annotation',
		'app.prediction_file',
		'app.prediction_document',
		'app.projects_participant',
		'app.upload_log',
		'app.final_prediction',
		'app.question',
		'app.annotations_question',
		'app.annotations_inter_relation',
		'app.connection',
		'app.post'
	);

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
	}

/**
 * testView method
 *
 * @return void
 */
	public function testView() {
	}

/**
 * testAdd method
 *
 * @return void
 */
	public function testAdd() {
	}

/**
 * testEdit method
 *
 * @return void
 */
	public function testEdit() {
	}

/**
 * testDelete method
 *
 * @return void
 */
	public function testDelete() {
	}

}
