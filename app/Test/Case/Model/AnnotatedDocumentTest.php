<?php
App::uses('AnnotatedDocument', 'Model');

/**
 * AnnotatedDocument Test Case
 *
 */
class AnnotatedDocumentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.annotated_document',
		'app.users_round',
		'app.user',
		'app.group',
		'app.annotation',
		'app.type',
		'app.project',
		'app.round',
		'app.types_round',
		'app.relation',
		'app.annotations_inter_relations',
		'app.document',
		'app.documents_assessment',
		'app.documents_project',
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
		'app.post',
		'app.annotated_documents'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AnnotatedDocument = ClassRegistry::init('AnnotatedDocument');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AnnotatedDocument);

		parent::tearDown();
	}

}
