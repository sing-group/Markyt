<?php

App::uses('AppController', 'Controller');

/**
 * Annotations Controller
 *
 * @property Annotation $Annotation
 */
class AnnotationsController extends AppController {

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    /*     * **************
      Este metdodo sera llamado desde el confrintation multiRound multiUSer para ver todas las anotaciones
     * ************* */
    public function index() {
        $data = $this->Session->read('confrontationSettingsData');
        $this->Annotation->recursive = 0;
        $this->Annotation->contain(false, array(
              'User' => array(
                    'username',
                    'id'
              ),
              'Round' => array(
                    'title',
                    'id'
              ),
              'Type' => array(
                    'id',
                    'name',
                    'colour'
              )
        ));
        $cond = array(
              'round_id' => $data['round'],
              'user_id' => $data['user']
        );
        if (!empty($data['Project']['type']))
            $cond['type_id'] = $data['Project']['type'];
        $this->paginate = array(
              'fields' => array(
                    'Annotation.id',
                    'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
              ),
              'limit' => 50
        );
        $this->set('redirect', $data['redirect']);
        $this->set('annotations', $this->paginate($cond));
        $this->set('project_id', $data['Project']['id']);
        $this->set('count', 0);
        $this->render('listAnnotation');
    }

    /*     * **************
      Este metdodo sera llamado desde el view de project
     * ************* */

    /**
     * allAnottationsInRound method
     * @param boolean $id
     * @param boolean $projectId
     * @return void
     */
    public function allAnottationsInRound($id = null, $projectId = null) {
        $this->Annotation->contain(false, array(
              'User' => array(
                    'username',
                    'id'
              ),
              'Round' => array(
                    'title',
                    'id'
              ),
              'Type' => array(
                    'id',
                    'name',
                    'colour'
              )
        ));
        $cond = array(
              'round_id' => $id
        );
        $redirect = $this->Session->write('redirect', array(
              'controller' => 'annotations',
              'action' => 'allAnottationsInRound',
              $id,
              $projectId
        ));
        $this->paginate = array(
              'fields' => array(
                    'Annotation.id',
                    'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
              ),
              'limit' => 100
        );
        $this->set('redirect', array(
              'controller' => 'projects',
              'action' => 'view',
              $projectId
        ));
        $this->set('annotations', $this->paginate($cond));
        $this->set('project_id', $projectId);
        //en este formato no necesitamos apguinaciones especiales
        $this->set('count', 0);
        $this->render('listAnnotation');
    }

    public function annotationsDocumentStatistics($round_id, $user_id) {
        $data = $this->Session->read('documentsStatisticsData');
        $round = $this->Annotation->Round->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $round_id,
              ),
              'fields' => array('id', 'project_id', 'title'),
        ));
        $project_id = $round['Round']['project_id'];
        $types = $this->Annotation->Type->find('all', array(
              'recursive' => -1,
              'conditions' => array(
                    'project_id' => $project_id,
              ),
              'fields' => array('id', 'colour', 'name'),
              'order' => array('Type.id ASC')
        ));
        $documents = $this->Annotation->Document->find('all', array(
              'recursive' => -1,
              'joins' => array(
                    array(
                          'type' => 'inner',
                          'table' => 'documents_projects',
                          'alias' => 'DocumentsProject',
                          'conditions' => array('Document.id=DocumentsProject.document_id')
                    )
              ),
              'conditions' => array(
                    'project_id' => $project_id,
              ),
              'fields' => array('Document.id', 'external_id', 'title'),
              'order' => array('id ASC')
        ));
        $documents = Set::combine($documents, '{n}.Document.id', '{n}.Document');
        $this->Annotation->virtualFields['totalAnnotations'] = 'COUNT(Annotation.id)';
        $this->Annotation->virtualFields['sumTotalAnnotations'] = 'SUM(totalAnnotations)';
        $annotations = $this->Annotation->find('all', array(
              'recursive' => -1,
              'conditions' => array(
                    'round_id' => $round_id,
                    'user_id' => $user_id
              ),
              'fields' => array('type_id', 'document_id', 'totalAnnotations'),
              'group' => array('document_id', 'type_id'),
              'order' => array('type_id ASC', 'document_id ASC')
        ));
        $annotations = Set::combine($annotations, '{n}.Annotation.type_id', '{n}.Annotation', '{n}.Annotation.document_id');
        $typeColors = Set::combine($types, '{n}.Type.id', '{n}.Type.colour');
        $this->set('types', $types);
        $this->set('typeColors', $typeColors);
        $this->set('documents', $documents);
        $this->set('annotations', $annotations);
        $this->set('round_id', $round_id);
        $this->set('project_id', $project_id);
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Annotation->id = $id;
        if (!$this->Annotation->exists()) {
            throw new NotFoundException(__('Invalid annotation'));
        }
        if ($this->Annotation->delete()) {
            $this->Session->setFlash(__('Annotation deleted'));
            $this->redirect(array(
                  'action' => 'index'
            ));
        }
        $this->Session->setFlash(__('Annotation was not deleted'));
        $this->redirect(array(
              'action' => 'index'
        ));
    }

    function redirectToAnnotatedDocument($round_id = null, $user_id = null, $document_id = null) {
        $this->DocumentsProject = $this->Annotation->Document->DocumentsProject;
        $round = $this->Annotation->Round->find('first', array(
              'recursive' => -1,
              'fields' => array('project_id', 'id'),
              'conditions' => array(
                    'Round.id' => $round_id,
              ),
        ));
        if (empty($round)) {
            throw new NotFoundException(__('Invalid round'));
        }
        $page = $this->DocumentsProject->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'DocumentsProject.project_id' => $round['Round']['project_id'],
                    'DocumentsProject.document_id <=' => $document_id,
              ),
              'order' => array('document_id' => 'ASC')
        ));
        $this->redirect(array('controller' => 'annotatedDocuments', 'action' => 'start',
              $round_id, $user_id, "find", 'page' => $page));
    }

    /**
     * listAnnotation method
     *
     * @param string $fragment
     * @return void
     */
    function listAnnotationsHits() {
        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        session_write_close();
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }

        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
        //evitar sql injection
            throw new Exception("Error Processing Request", 1);
        //para la opcion de naotaciones relacionadas, hits
        $type_A = $annotationData['Annotation']['typeCol'];
        $type_B = $annotationData['Annotation']['typeFil'];

        $subQueryConditions = array(
              'Annotation_B.round_id' => $round_id_B,
              'Annotation_B.user_id' => $user_id_B,
              'Annotation.document_id = Annotation_B.document_id ',
//              'Annotation.section = Annotation_B.section OR (Annotation.section = NULL AND Annotation_B.section = NULL)',
        );
        if ($margin != 0) {
            $subQueryConditions = array_merge($subQueryConditions, array(
                  "Annotation.end between Annotation_B.end - $margin AND Annotation_B.end + $margin",
                  "Annotation.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
            ));
        } else {
            $subQueryConditions = array_merge($subQueryConditions, array(
                  "Annotation.init = Annotation_B.init",
                  "Annotation.end = Annotation_B.end"
            ));
        }
        $this->Annotation->contain(false, array(
              'Document' => array('title', 'external_id'),
        ));
        $this->paginate = array(
              'fields' => array(
                    'Annotation.id',
                    'Annotation.type_id',
                    'Annotation.round_id',
                    'Annotation.user_id',
                    'Annotation.annotated_text',
                    'Annotation_B.id',
                    'Annotation_B.type_id',
                    'Annotation_B.round_id',
                    'Annotation_B.user_id',
              ),
              'limit' => 50,
              'joins' => array(
                    array(
                          'type' => 'inner',
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'conditions' => $subQueryConditions
                    )
              ),
              'order' => array("document_id" => "ASC"),
              'conditions' => array(
                    'Annotation.type_id' => $type_A,
                    'Annotation_B.type_id' => $type_B,
                    'Annotation.round_id' => $round_id_A,
                    'Annotation.user_id' => $user_id_A
              )
        );
        $rounds = $this->Annotation->Round->find('list', array('recursive' => -1,
              'conditions' => array('id' => array($round_id_A, $round_id_B))));
        $types = $this->Annotation->Type->find('all', array('fields' => array('id',
                    'name', 'colour'), 'recursive' => -1, 'conditions' => array(
                    'id' => array(
                          $type_A, $type_B))));
        $types = Hash::combine($types, '{n}.Type.id', '{n}.Type');
        $users = $this->Annotation->User->find('all', array('fields' => array('id',
                    'full_name', 'image', 'image_type'), 'recursive' => -1, 'conditions' => array(
                    'id' => array($user_id_A, $user_id_B))));
        $users = Hash::combine($users, '{n}.User.id', '{n}.User');
        $this->set(compact('rounds', 'users', 'types'));
        $this->set('annotations', $this->paginate());
        $this->set('project_id', $data['Project']['id']);
        $this->render('listAnnotation');
    }

    public function listAnnotationsNone() {
        $this->Annotation->recursive = -1;
        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }
        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
        //evitar sql injection
            throw new Exception("Error Processing Request", 1);
        $byRound = $annotationData['Annotation']['byRound'];
        $id_user_or_round = $annotationData['Annotation']['id_none'];
        $typeNone = $annotationData['Annotation']['typeNone'];
        if (($id_user_or_round == $user_id_A && !$byRound) || ($id_user_or_round == $round_id_A && $byRound)) {
            $id_roundSeach = $round_id_A;
            $id_roundNone = $round_id_B;
            $id_userSeach = $user_id_A;
            $id_userNone = $user_id_B;
        } else {
            $id_roundSeach = $round_id_B;
            $id_roundNone = $round_id_A;
            $id_userSeach = $user_id_B;
            $id_userNone = $user_id_A;
        }
        $subQueryConditions = array(
              'Annotation_B.round_id' => $id_roundNone,
              'Annotation_B.user_id' => $id_userNone,
              'Annotation_A.document_id = Annotation_B.document_id ',
              'Annotation_A.round_id' => $id_roundSeach,
              'Annotation_A.user_id' => $id_userSeach
        );
        if ($margin != 0) {
            array_push($subQueryConditions, array(
                  "Annotation_A.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
                  "Annotation_A.end between Annotation_B.end - $margin AND Annotation_B.end + $margin"
            ));
        } else {
            array_push($subQueryConditions, array(
                  "Annotation_A.init= Annotation_B.init",
                  "Annotation_A.end=Annotation_B.end"
            ));
        }
        $db = $this->Annotation->getDataSource();
        $subQuery = $db->buildStatement(array(
              'fields' => array(
                    'Distinct Annotation_A.id'
              ),
              'table' => 'annotations',
              'alias' => 'Annotation_A',
              'limit' => null,
              'offset' => null,
              'joins' => array(
                    array(
                          'type' => 'INNER',
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'conditions' => $subQueryConditions
                    )
              ),
              'conditions' => array(''),
              'order' => null,
              'group' => null
            ), $this->Annotation);
        /* print_r($subQuery);
          throw new Exception("Error Processing Request", 1); */
        $this->Annotation->contain(false, array(
              'Document' => array('title', 'external_id'),
        ));
        $this->paginate = array(
              'fields' => array(
                    'Annotation.id',
                    'Annotation.type_id',
                    'Annotation.round_id',
                    'Annotation.user_id',
                    'Annotation.annotated_text'
              ),
              'limit' => 50,
              'conditions' => array(
                    'Annotation.id NOT IN(' . $subQuery . ')',
                    'Annotation.type_id' => $typeNone,
                    'Annotation.round_id' => $id_roundSeach,
                    'Annotation.user_id' => $id_userSeach
              )
        );
        $rounds = $this->Annotation->Round->find('list', array('recursive' => -1,
              'conditions' => array('id' => array($id_roundSeach))));



        $types = $this->Annotation->Type->find('all', array('fields' => array('id',
                    'name', 'colour'), 'recursive' => -1, 'conditions' => array(
                    'id' => array(
                          $typeNone))));
        $types = Hash::combine($types, '{n}.Type.id', '{n}.Type');
        $users = $this->Annotation->User->find('all', array('fields' => array('id',
                    'full_name', 'image', 'image_type'), 'recursive' => -1, 'conditions' => array(
                    'id' => array($id_userSeach))));



        $users = Hash::combine($users, '{n}.User.id', '{n}.User');
        $this->set(compact('rounds', 'users', 'types'));

        $this->set('userId', $id_userSeach);
        $this->set('roundId', $id_roundSeach);

        $this->set('annotations', $this->paginate());
        $this->set('project_id', $data['Project']['id']);
        $this->render('listAnnotation');
    }

    function downloadAnnotationsHits($toRound_id = null, $toUser_id = null, $toType_id = null) {
        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }
        //para la opcion de naotaciones relacionadas, hits
        $type_A = $annotationData['Annotation']['typeCol'];
        $type_B = $annotationData['Annotation']['typeFil'];
        if ($toRound_id != $round_id_A) {
            $aux = $round_id_B;
            $round_id_B = $round_id_A;
            $round_id_A = $aux;
        }
        if ($toUser_id != $user_id_A) {
            $aux = $user_id_B;
            $user_id_B = $user_id_A;
            $user_id_A = $aux;
        }
        if ($toType_id != $type_A) {
            $aux = $type_B;
            $type_B = $type_A;
            $type_A = $aux;
        }
        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
        //evitar sql injection
            throw new Exception("Error Proc
            essing Request", 1);
        $subQueryConditions = array(
              'Annotation_B.round_id' => $round_id_B,
              'Annotation_B.user_id' => $user_id_B,
              'Annotation.document_id = Annotation_B.document_id ',
              'Annotation_B.type_id' => $type_B
        );
        if ($margin != 0) {
            array_push($subQueryConditions, array(
                  "Annotation.end between Annotation_B.end - $margin AND Annotation_B.end + $margin",
                  "Annotation.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
            ));
        } else {
            array_push($subQueryConditions, array(
                  "Annotation.init= Annotation_B.init",
                  "Annotation.end=Annotation_B.end"
            ));
        }
        $annotationsSize = $this->Annotation->find('count', array(
              'recursive' => -1,
              'joins' => array(
                    array(
                          'type' => 'inner',
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'conditions' => $subQueryConditions
                    )
              ),
              'conditions' => array(
                    'Annotation.type_id' => $type_A,
                    'Annotation.round_id' => $round_id_A,
                    'Annotation.user_id' => $user_id_A
              ),
              'group' => array('Annotation.annotated_text')
        ));
        $types = $this->Annotation->Type->find('all', array('fields' => array('id',
                    'name', 'colour'), 'recursive' => -1, 'conditions' => array(
                    'id' => array(
                          $type_A, $type_B))));
        $types = Hash::combine($types, '{n}.Type.id', '{n}.Type');
        $downloadPath = Configure::read('downloadFolder');
        $documentsBuffer = Configure::read('documentsBuffer');
        $annotationsBuffer = Configure::read('annotationsBuffer');
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        $downloadDir = new Folder($downloadPath, true, 0700);
        if ($downloadDir->create('')) {
            //si se puede crear la carpeta
            //creamos una carpeta temporal
            $tempPath = $downloadDir->pwd() . DS . uniqid();
            $tempFolder = new Folder($tempPath, true, 0700);
            if ($tempFolder->create('')) {
                $file = new File($tempFolder->pwd() . DS . "annotations.tsv", 600);
                $content = "Type\tAnnotation_text\n";
                $index = 0;
                while ($index < $annotationsSize) {
                    $annotations = $this->Annotation->find('all', array(
                          'fields' => array(
                                'Type.name',
                                'Annotation.annotated_text',
                          ),
                          'joins' => array(
                                array(
                                      'type' => 'inner',
                                      'table' => 'annotations',
                                      'alias' => 'Annotation_B',
                                      'conditions' => $subQueryConditions
                                )
                          ),
                          'conditions' => array(
                                'Annotation.type_id' => $type_A,
                                'Annotation.round_id' => $round_id_A,
                                'Annotation.user_id' => $user_id_A
                          ),
                          'limit' => $annotationsBuffer, //int
                          'offset' => $index, //int
                          'group' => array('Annotation.annotated_text')
                    ));
                    foreach ($annotations as $annotation) {
                        $content .= $annotation['Type']['name'] . "\t" .
                            $annotation['Annotation']['annotated_text'] . "\n";
                    }
                    //throw new Exception;
                    if ($file->exists()) {
                        $file->append($content);
                    } else {
                        throw new Exception("Error creating files ");
                        if (!$tempFolder->delete()) {
                            throw new Exception("Error delete zip ");
                        }
                    }
                    $content = '';
                    $index += $annotationsBuffer;
                }
                $file->close();
                $content = $file->read();
                if (!$tempFolder->delete()) {
                    throw new Exception("Error delete zip ");
                }
                $mimeExtension = 'text/tab-separated-values';
                $this->autoRender = false;
                $this->response->type($mimeExtension);
                $this->response->body($content);
                $this->response->download($file->name() . "." . $file->ext());
                return $this->response;
            }
        }
    }

    public function downloadAnnotationsNone() {
        $this->Annotation->recursive = -1;
        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }
        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
            throw new Exception("Error Processing Request", 1);
        $this->Annotation->Round->recursive = -1;
        $this->Annotation->Round->id = $round_id_A;
        if (!$this->Annotation->Round->exists()) {
            throw new NotFoundException(__('Invalid Round ' . $round_id_A));
        } //!$this->Project->exists()
        $round_A_name = $this->Annotation->Round->read('title');
        $round_A_name = $round_A_name['Round']['title'];
        $this->Annotation->Round->id = $round_id_B;
        if (!$this->Annotation->Round->exists()) {
            throw new NotFoundException(__('Invalid Round ' . $round_id_B));
        } //!$this->Project->exists()
        $round_B_name = $this->Annotation->Round->read('title');
        $round_B_name = $round_B_name['Round']['title'];
        $this->Annotation->User->recursive = -1;
        $this->Annotation->User->id = $user_id_A;
        if (!$this->Annotation->User->exists()) {
            throw new NotFoundException(__('Invalid User ' . $user_id_A));
        } //!$this->Project->exists()
        $user_A_name = $this->Annotation->User->read('full_name');
        $user_A_name = $user_A_name['User']['full_name'];
        $this->Annotation->User->id = $user_id_A;
        if (!$this->Annotation->User->exists()) {
            throw new NotFoundException(__('Invalid User ' . $user_id_A));
        } //!$this->Project->exists()
        $user_B_name = $this->Annotation->User->read('full_name');
        $user_B_name = $user_B_name['User']['full_name'];
        $subQueryConditions = array(
              'Annotation_B.round_id' => $round_id_B,
              'Annotation_B.user_id' => $user_id_B,
              'Annotation_A.document_id = Annotation_B.document_id ',
              'Annotation_A.round_id' => $round_id_A,
              'Annotation_A.user_id' => $user_id_A
        );
        if ($margin != 0) {
            array_push($subQueryConditions, array(
                  "Annotation_A.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
                  "Annotation_A.end between Annotation_B.end - $margin AND Annotation_B.end + $margin"
            ));
        } else {
            array_push($subQueryConditions, array(
                  "Annotation_A.init = Annotation_B.init",
                  "Annotation_A.end = Annotation_B.end"
            ));
        }
        $db = $this->Annotation->getDataSource();
        $subQuery_A = $db->buildStatement(array(
              'fields' => array(
                    'Distinct Annotation_A.id'
              ),
              'table' => 'annotations',
              'alias' => 'Annotation_A',
              'joins' => array(
                    array(
                          'type' => 'INNER',
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'conditions' => $subQueryConditions
                    )
              ),
            ), $this->Annotation);
        $subQuery_B = $db->buildStatement(array(
              'fields' => array(
                    'Distinct Annotation_B.id'
              ),
              'table' => 'annotations',
              'alias' => 'Annotation_A',
              'joins' => array(
                    array(
                          'type' => 'INNER',
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'conditions' => $subQueryConditions
                    )
              ),
            ), $this->Annotation);
        $annotations_A = $this->Annotation->find('all', array(
              'contain' => array(
                    'Type' => array(
                          'name',
                    )),
              'fields' => array(
                    'annotated_text',
                    'Type.name'
              ),
              'conditions' => array(
                    'Annotation.id NOT IN(' . $subQuery_A . ')',
                    'Annotation.round_id' => $round_id_A,
                    'Annotation.user_id' => $user_id_A
              ),
              'group' => array('annotated_text')
        ));
        $annotations_B = $this->Annotation->find('all', array(
              'contain' => array(
                    'Type' => array(
                          'name',
                    )),
              'fields' => array(
                    'annotated_text',
                    'Type.name'
              ),
              'conditions' => array(
                    'Annotation.id NOT IN(' . $subQuery_B . ')',
                    'Annotation.round_id' => $round_id_B,
                    'Annotation.user_id' => $user_id_B
              ),
              'group' => array('annotated_text')
        ));
        $downloadPath = Configure::read('downloadFolder');
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        //$this->RequestHandler = $this->Components->load('RequestHandler');
        $this->Annotation->Round->Project->recursive = -1;
        $this->Annotation->Round->Project->id = $data['Project']['id'];
        $projectTitle = $this->Annotation->Round->Project->read('title');
        $projectTitle = "Marky#noneHits";
        $downloadDir = new Folder($downloadPath, true, 0700);
        if ($downloadDir->create('')) {
            //si se puede crear la carpeta
            //creamos una carpeta temporal
            $tempPath = $downloadDir->pwd() . DS . uniqid();
            $tempFolder = new Folder($tempPath, true, 0700);
            if ($tempFolder->create('')) {
                //se le añaden permisos
                $tempFolderAbsolutePath = $tempFolder->path . DS;
                // Initialize archive object
                $zip = new ZipArchive;
                $packetName = $projectTitle . ".zip";
                if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                    $tempFolder->delete();
                    throw new Exception("Failed to create Zip file");
                }
                $content = "";
                $fileName = $user_A_name . ".txt";
                $round_A_folder = new Folder($tempFolder->pwd() . DS . $round_A_name, true, 0700);
                if ($round_A_folder->create('')) {
                    $file = new File($tempFolder->pwd() . DS . $round_A_name . DS . $fileName, 600);
                    if ($file->exists()) {
                        foreach ($annotations_A as $annotation) {
                            $content .= $annotation['Type']['name'] . "\t" . $annotation['Annotation']['annotated_text'] . "\n";
                        }
                        $file->write($content);
                        $file->close();
                        $zip->addFile($file->path, $round_A_name . DS . ltrim($fileName, '/'));
                    } else {
                        $tempFolder->delete();
                        throw new Exception("Error creating file A " . $fileName);
                    }
                } else {
                    $tempFolder->delete();
                    throw new Exception("Error creating folder A " . $round_A_name);
                }
                $content = "";
                $fileName = $user_B_name . ".txt";
                $round_B_folder = new Folder($tempFolder->pwd() . DS . $round_B_name, true, 0700);
                if ($round_B_folder->create('')) {
                    $file = new File($tempFolder->pwd() . DS . $round_B_name . DS . $fileName, 600);
                    if ($file->exists()) {
                        foreach ($annotations_B as $annotation) {
                            $content .= $annotation['Type']['name'] . "\t" . $annotation['Annotation']['annotated_text'] . "\n";
                        }
                        $file->write($content);
                        $file->close();
                        $zip->addFile($file->path, $round_B_name . DS . ltrim($fileName, '/'));
                    } else {
                        $tempFolder->delete();
                        throw new Exception("Error creating file B " . $fileName);
                    }
                } else {
                    $tempFolder->delete();
                    throw new Exception("Error creating folder B " . $round_B_name);
                }
                $zip->close();
                if (!$zip->status == ZIPARCHIVE::ER_OK) {
                    $tempFolder->delete();
                    throw new Exception("Error creating zip ");
                }
                $zipFolder = new File($tempFolder->pwd() . DS . $packetName);
                $packet = $zipFolder->read();
                $zipFolder->close();
                if (!$tempFolder->delete()) {
                    throw new Exception("Error delete zip ");
                }
                $mimeExtension = 'application/zip';
                $this->autoRender = false;
                $this->response->type($mimeExtension);
                $this->response->body($packet);
                $this->response->download($packetName);
                return $this->response;
            }
        }
    }

    public function generateConsensus($projectId = null, $roundId = null) {
        ini_set('memory_limit', Configure::read('scriptMemoryLimit'));
        $this->Annotation->Round->Project->id = $projectId;
        $this->Annotation->Round->id = $roundId;
        if (!$this->Annotation->Round->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if (!$this->Annotation->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        $this->Annotation->recursive = 0;
        $users = $this->Annotation->User->find('all', array(
              'recursive' => -1,
              'joins' => array(
                    array(
                          'type' => 'inner',
                          'table' => 'users_rounds',
                          'alias' => 'UsersRound',
                          'conditions' => array(
                                'UsersRound.round_id' => $roundId,
                                'User.id = UsersRound.user_id'
                          )
                    ),
              ),
        ));
        $this->Annotation->virtualFields['users'] = "GROUP_CONCAT(DISTINCT Annotation.user_id SEPARATOR ', ')";
        $this->Annotation->virtualFields['total'] = "COUNT(DISTINCT User.id)";
        $this->paginate = array(
              'fields' => array(
                    'Annotation.id',
                    'Annotation.total',
                    'Annotation.type_id',
                    'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text',
                    'Annotation.users',
                    'ConsensusAnnotation.id',
              ),
              'joins' => array(
                    array(
                          'type' => 'LEFT',
                          'table' => 'consensus_annotations',
                          'alias' => 'ConsensusAnnotation',
                          'conditions' => array(
                                'ConsensusAnnotation.init = Annotation.init',
                                'ConsensusAnnotation.end = Annotation.end',
                                'ConsensusAnnotation.round_id = Annotation.round_id',
                                'ConsensusAnnotation.document_id = Annotation.document_id',
                          )
                    ),
              ),
              'conditions' => array(
                    'Annotation.init IS NOT NULL',
                    'Annotation.end IS NOT NULL',
                    'Annotation.round_id' => $roundId,
              ),
              'limit' => 100,
              'group' => array('Annotation.init', 'Annotation.end',
                    'Annotation.section', 'Annotation.document_id',
                    'ConsensusAnnotation.id'),
              'contain' => array(
                    'User' => array(
                          'username',
                          'id'
                    ),
                    'Document' => array(
                          'title',
                          'id'
                    ),
                    'Type' => array(
                          'id',
                          'name',
                          'colour'
                    )
              ),
              'order' => array('Annotation.init', 'Annotation.end'),
        );
        $this->set('annotations', $this->paginate());
        $this->set('users', $users);
        $this->set('project_id', $projectId);
        $this->set('round_id', $roundId);
        $this->set('count', 0);
    }

    function export($roundId = null, $userId = null) {
        $this->Round = $this->Annotation->Round;
        $this->Project = $this->Round->Project;
        $this->User = $this->Project->User;
        $this->Type = $this->Project->Type;
        $this->User->id = $userId;
        $this->Round->id = $roundId;
        if (!$this->Round->exists() && $this->User->exists()) {
            throw new NotFoundException(__('Invalid Round or User'));
        } //!$this->Project->exists()
        else {
            $projectId = $this->Round->field('project_id');
            $this->Project->id = $projectId;
            if (!$this->Project->exists()) {
                throw new NotFoundException(__('Invalid proyect'));
            } //!$this->Project->exists()
            $types = $this->Round->Project->Type->find('list', array(
                  'recursive' => -1,
                  'fields' => array('id', 'name'),
                  'conditions' => array(
                        'Type.project_id' => $projectId
                  )
            ));
            $annotationsBuffer = Configure::read('annotationsBuffer');
            $conditions = array(
                  'Annotation.round_id' => $roundId,
                  'Annotation.user_id' => $userId,
                  'Annotation.init IS NOT NULL',
                  'Annotation.end IS NOT NULL',
            );
            $annotationsSize = $this->Annotation->find('count', array(
                  'recursive' => -1,
                  'conditions' => $conditions,
            ));
            $index = 0;
            $content = "Document\tSection\tStarting_offset\tEnding_offset\tAnnotation_text\tType\n";
            $lines = array();
            while ($index < $annotationsSize) {
                $annotations = $this->Annotation->find('all', array(
                      'recursive' => -1,
                      'contain' => array('Document' => array('external_id', 'title')),
                      'conditions' => $conditions,
                      'fields' => array('Document.external_id',
                            'init',
                            'end',
                            'annotated_text',
                            'document_id',
                            'type_id',
                            'section'),
                      'order' => array('Document.external_id ASC', 'section ASC',
                            'init DESC'),
                      'limit' => $annotationsBuffer, //int
                      'offset' => $index, //int
                ));
                foreach ($annotations as $annotation) {
                    $content .= $annotation['Document']['external_id'] . "\t";
                    $content .= $annotation['Annotation']['section'] . "\t" .
                        $annotation['Annotation']['init'] . "\t" .
                        $annotation['Annotation']['end'] . "\t" .
                        $annotation['Annotation']['annotated_text'] . "\t" .
                        $types[$annotation['Annotation']['type_id']] . "\n";
                }
                array_push($lines, $content);
                $content = '';
                $index += $annotationsBuffer;
            }
            $project = $this->Project->field('title');
            $user = $this->User->field('username');
            return $this->exportTsvDocument($lines, $project . "_" . "entities" . ".tsv");
            //$this->render('confrontationMulti');
        }
    }

    function exportAnnotations($roundId = null, $userId = null) {
        $this->Round = $this->Annotation->Round;
        $this->Project = $this->Round->Project;
        $this->Document = $this->Project->Document;
        $this->User = $this->Project->User;
        $this->Type = $this->Project->Type;
        $this->Relation = $this->Project->Relation;
        $this->Round->id = $roundId;
        $projectId = $this->Round->field('project_id');
        $this->Project->id = $projectId;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $relationLevel = $this->Project->field('relation_level');
        ini_set('memory_limit', Configure::read('scriptMemoryLimit'));
        set_time_limit(Configure::read('scriptTimeLimit'));
        $format = $this->params['named']['format'];
        if (!isset($format)) {
            $format = "BIOC";
        }
        $format = strtoupper($format);
        $types = $this->Round->Project->Type->find('list', array(
              'recursive' => -1,
              'fields' => array('id', 'name'),
              'conditions' => array(
                    'Type.project_id' => $projectId
              )
        ));
        $conditions = array(
              'Annotation.round_id' => $roundId,
              'Annotation.user_id' => $userId,
        );
        $this->Annotation->virtualFields["offset"] = "Annotation.init";
        $this->Annotation->virtualFields["text"] = "Annotation.annotated_text";
        $this->Annotation->virtualFields['text'] = 'annotated_text';
        $this->Annotation->virtualFields['offset'] = 'init';
        $this->Annotation->virtualFields['length'] = 'end - init';
        $fields = array('id',
              'annotated_text',
              'init',
              'end',
              'length',
              'document_id',
              'section',
              'type_id');
        if ($format == "BIOC" || $format == "BIOCJSON" || $format == "PUB") {
            $fields = array_merge($fields, array(
                  'text',
                  'offset'
            ));
        }
        $annotations = $this->Annotation->find('all', array(
              'recursive' => -1,
              'conditions' => $conditions,
              'fields' => $fields,
              'order' => array('section ASC', 'init DESC'),
        ));
        $fields = array(
              'Relation.Rid',
              'Relation.name',
              'Relation.is_directed',
              'Annotation_A.id',
              'Annotation_A.annotated_text',
              'Annotation_A.init',
              'Annotation_A.end',
              'Annotation_A.section',
              'Annotation_A.document_id',
              'Annotation_B.id',
              'Annotation_B.annotated_text',
              'Annotation_B.init',
              'Annotation_B.end',
              'Annotation_B.section',
              'Annotation_A.type_id',
              'Annotation_B.type_id',
              'Annotation_B.document_id',
        );
        if ($relationLevel == 2) {
            $fields = array(
                  'Relation.Rid',
                  'Relation.name',
                  'Relation.is_directed',
                  'Annotation_A.id',
                  'Annotation_A.document_id',
                  'Annotation_A.annotated_text',
                  'Annotation_A.type_id',
                  'Annotation_B.id',
                  'Annotation_B.document_id',
                  'Annotation_B.annotated_text',
                  'Annotation_B.type_id',
            );
        }
        $this->Relation->virtualFields["Rid"] = "AnnotationsInterRelations.id";
        $annotationsRelations = $this->Relation->find('all', array(
              'recursive' => -1,
              'fields' => $fields,
              'conditions' => array('Relation.project_id' => $projectId),
              'joins' => array(
                    array(
                          'table' => 'annotations_inter_relations',
                          'alias' => 'AnnotationsInterRelations',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Relation.id = AnnotationsInterRelations.relation_id'
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation_A',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Annotation_A.id = AnnotationsInterRelations.annotation_a_id',
                                'Annotation_A.round_id' => $roundId,
                                'Annotation_A.user_id' => $userId,
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Annotation_B.id = AnnotationsInterRelations.annotation_b_id',
                                'Annotation_B.round_id' => $roundId,
                                'Annotation_B.user_id' => $userId,
                          )
                    ),
              ),
        ));
        $this->Document->virtualFields["abstract"] = "Document.raw";
        if ($format != "BIOC" && $format != "BIOCJSON" && $format != "PUB") {
            $documentsList = Cache::read('documents-list-project-' . $projectId, 'long');
            if (empty($documentsList)) {
                $documentsList = $this->Document->find('list', array(
                      'recursive' => -1,
                      'fields' => array('id', 'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'documents_projects',
                                  'alias' => 'DocumentProjects',
                                  'type' => 'INNER',
                                  'conditions' => array(
                                        'Document.id = DocumentProjects.document_id',
                                        'DocumentProjects.project_id' => $projectId,
                                  )
                            ),
                      ),
                      'order' => array('id ASC')
                ));
                Cache::write('documents-list-project-' . $projectId, $documentsList, 'long');
            }
        } else {
            $documentsList = $this->Document->find('all', array(
                  'recursive' => -1,
                  'fields' => array('id', 'external_id', 'title', 'abstract'),
                  'joins' => array(
                        array(
                              'table' => 'documents_projects',
                              'alias' => 'DocumentProjects',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'Document.id = DocumentProjects.document_id',
                                    'DocumentProjects.project_id' => $projectId,
                              )
                        ),
                  ),
                  "order" => array('Document.id'),
            ));
            $documentsList = Set::combine($documentsList, '{n}.Document.id', '{n}.Document');
            $annotationsRelations = Set::combine($annotationsRelations, '{n}.Relation.Rid', '{n}', '{n}.Annotation_A.document_id');
        }
        /* ========================== */
        $this->AnotationsExporter = $this->Components->load('AnotationsExporter');
        $this->AnotationsExporter->format($format);
        if ($format == "PUB") {
            $format = "JSON";
        }
        $content = $this->AnotationsExporter->export($annotations, $documentsList, $types, $annotationsRelations);
        $this->response->body($content);
        $this->response->download("annotations." . strtolower($format));
        $this->autoRender = false;
        return $this->response;
    }

}
