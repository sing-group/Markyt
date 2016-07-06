<?php
App::uses('UploadLog', 'Model');

/**
 * UploadLog Test Case
 *
 */
class UploadLogTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.upload_log',
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
		'app.prediction_document'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UploadLog = ClassRegistry::init('UploadLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UploadLog);

		parent::tearDown();
	}

}
