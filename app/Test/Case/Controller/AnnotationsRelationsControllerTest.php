<?php
App::uses('AnnotationsRelationsController', 'Controller');

/**
 * AnnotationsRelationsController Test Case
 *
 */
class AnnotationsRelationsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.annotations_relation',
		'app.annotation',
		'app.type',
		'app.project',
		'app.round',
		'app.users_round',
		'app.user',
		'app.group',
		'app.post',
		'app.projects_user',
		'app.document',
		'app.documents_project',
		'app.types_round',
		'app.question',
		'app.annotations_question',
		'app.relation'
	);

}
