<?php
App::uses('ConsensusAnnotationsController', 'Controller');

/**
 * ConsensusAnnotationsController Test Case
 *
 */
class ConsensusAnnotationsControllerTest extends ControllerTestCase {

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

}
