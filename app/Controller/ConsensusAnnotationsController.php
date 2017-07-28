<?php

App::uses('AppController', 'Controller');

/**
 * ConsensusAnnotations Controller
 *
 * @property ConsensusAnnotation $ConsensusAnnotation
 * @property PaginatorComponent $Paginator
 */
class ConsensusAnnotationsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array(
          'Paginator');

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $this->ConsensusAnnotation->create();
            $id = $this->request->data['consensusAnnotation']['id'];
            $round_id = $this->request->data['consensusAnnotation']['round_id'];
            $project_id = $this->request->data['consensusAnnotation']['project_id'];
            $data = $this->ConsensusAnnotation->Annotation->find('first', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'id' => $id)));
            $newConsensus = array(
                  'round_id' => $round_id,
                  'project_id' => $project_id,
                  'document_id' => $data['Annotation']['document_id'],
                  'type_id' => $data['Annotation']['type_id'],
                  'annotation' => $data['Annotation']['annotated_text'],
                  'init' => $data['Annotation']['init'],
                  'end' => $data['Annotation']['end'],
                  'section' => $data['Annotation']['section'],
            );
            if ($this->ConsensusAnnotation->save($newConsensus)) {
                return $this->correctResponseJson(json_encode(array(
                          'success' => true)));
            } else {
                $this->Session->setFlash(__('The condition could not be saved. Please, try again.'));
                return $this->correctResponseJson(json_encode(array(
                          'success' => false)));
            }
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->ConsensusAnnotation->id = $id;
        if (!$this->ConsensusAnnotation->exists()) {
            throw new NotFoundException(__('Invalid condition'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->ConsensusAnnotation->delete()) {
            return $this->correctResponseJson(json_encode(array(
                      'success' => true)));
        } else {
            $this->Session->setFlash(__('The condition could not be saved. Please, try again.'));
            return $this->correctResponseJson(json_encode(array(
                      'success' => false)));
        }
        return $this->redirect(array(
                  'action' => 'index'));
    }

    public function automatic() {
        if ($this->request->is(array(
                  'post',
                  'put'))) {
            $projectId = $this->request->data['consensusAnnotation']['project_id'];
            $roundId = $this->request->data['consensusAnnotation']['round_id'];
            $percent = $this->request->data['consensusAnnotation']['percent'];
            $this->ConsensusRelation = $this->ConsensusAnnotation->ConsensusRelation;
            $this->ConsensusAnnotation->Project->id = $projectId;
            $this->ConsensusAnnotation->Project->Round->id = $roundId;
            if (!$this->ConsensusAnnotation->Project->exists()) {
                throw new NotFoundException(__('Invalid proyect'));
            } //!$this->Project->exists()
            if (!$this->ConsensusAnnotation->Project->Round->exists()) {
                throw new NotFoundException(__('Invalid Round'));
            } //!$this->Project->exists()
            $count = $this->ConsensusAnnotation->Project->User->find('count', array(
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
            if ($percent == '') {
                $percent = 100;
            } else {
                $percent = intval($percent);
                if ($percent == 0) {
                    $this->ConsensusAnnotation->deleteAll(array(
                          'round_id' => $roundId));
                    $this->Session->setFlash(__('Deleted consensus has had success'), 'success');
                    return $this->redirect(array(
                              'controller' => 'annotations',
                              'action' => 'generateConsensus',
                              $projectId,
                              $roundId));
                }
            }
            $percent /= 100;
            $this->ConsensusAnnotation->Annotation->virtualFields = array(
                  'project_id' => $projectId
            );
            $db = $this->ConsensusAnnotation->Annotation->getDataSource();
            $options = array(
                  'table' => $db->fullTableName($this->ConsensusAnnotation->Annotation),
                  'alias' => 'Annotation',
                  'recursive' => -1,
                  'fields' => array(
                        $projectId,
                        $roundId,
                        'Annotation.id',
                        'Annotation.document_id',
                        'Annotation.type_id',
                        'Annotation.init',
                        'Annotation.end',
                        'Annotation.annotated_text',
                        'Annotation.section',
                  ),
                  'group' => array(
                        'init',
                        'document_id',
                        'round_id',
                        'type_id',
                        'annotated_text',
                        'section',
                        'end HAVING (COUNT(DISTINCT Annotation.user_id)/' . $count . ') >= ' . $percent
                  ),
                  'conditions' => array(
                        'round_id' => $roundId,
                        'Annotation.init IS NOT NULL',
                        'Annotation.end IS NOT NULL'
                  ),
            );
            //$this->ConsensusAnnotation->Annotation->find('all', $options);
            $commit = true;
            $db->begin();
            $commit = $commit & $this->ConsensusAnnotation->deleteAll(array(
                      'project_id' => $projectId));
            $query = $db->buildStatement($options, $this->ConsensusAnnotation->Annotation);
            $db->query('INSERT INTO ' . $db->fullTableName($this->ConsensusAnnotation) . '(
                  project_id, round_id, id, document_id, type_id, init, end, annotation, section) ' . $query);
            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__('Automatic consensus has had success'), 'success');
                $this->redirect(array(
                      'controller' => 'annotations',
                      'action' => 'generateConsensus',
                      $projectId,
                      $roundId));
            } else {
                $db->rollback();
                $this->Session->setFlash(__('Automatic consensus has not had success'));
                $this->redirect(array(
                      'controller' => 'annotations',
                      'action' => 'generateConsensus',
                      $projectId,
                      $roundId));
            }
        } else {
            throw new NotFoundException(__('Invalid proyect'));
        }
    }

    function download($projectId = null, $roundId = null, $diferenceSection = false) {
        $scriptTimeLimit = Configure::read('scriptTimeLimit');
        set_time_limit($scriptTimeLimit);
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        $this->Project = $this->ConsensusAnnotation->Project;
        $this->ConsensusRelation = $this->ConsensusAnnotation->ConsensusRelation;
        $this->Round = $this->Project->Round;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->Document = $this->Project->Document;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        $this->Relation = $this->Project->Relation;
        //$this->autoRender = false;
        $this->Project->id = $projectId;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $this->Round->id = $roundId;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        else {
            $downloadPath = Configure::read('downloadFolder');
            $documentsBuffer = Configure::read('documentsBuffer');
            $annotationsBuffer = Configure::read('annotationsBuffer');
            $user = $this->Session->read('email');
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
            session_write_close();
            $this->RequestHandler = $this->Components->load('RequestHandler');
            if (!isset($user)) {
                print("A joker!!");
                exit();
            } else {
                $this->Project->recursive = -1;
                $projectTitle = $this->Project->read('title');
                $projectTitle = ltrim($projectTitle['Project']['title'], '/');
                $projectTitle = str_replace(' ', '_', $projectTitle);
                $projectTitle = "Marky_#" . substr($projectTitle, 0, 20);
                $downloadDir = new Folder($downloadPath, true, 0700);
                if ($downloadDir->create('')) {
                    //si se puede crear la carpeta
                    //creamos una carpeta temporal
                    $tempPath = $downloadDir->pwd() . DS . uniqid();
                    $tempFolder = new Folder($tempPath, true, 0700);
                    if ($tempFolder->create('')) {
                        //se le añaden permisos
                        $tempFolderAbsolutePath = $tempFolder->path . DS;
                        $this->AnnotatedDocument->virtualFields['count'] = 'COUNT(DISTINCT(AnnotatedDocument.document_id))';
                        //no tiene por que corresponderse el numero de documentos con los documentos
                        //realmente anotados
                        $annotatedDocumentsSize = $this->AnnotatedDocument->find('count', array(
                              'recursive' => -1,
                              'joins' => array(
                                    array(
                                          'type' => 'inner',
                                          'table' => 'documents_projects',
                                          'alias' => 'DocumentsProject',
                                          'conditions' => array(
                                                'DocumentsProject.project_id' => $projectId,
                                          )
                                    ),
                                    array(
                                          'type' => 'inner',
                                          'table' => 'documents',
                                          'alias' => 'Document',
                                          'conditions' => array(
                                                'Document.id = DocumentsProject.document_id',
                                          )
                                    ),
                              ),
                              'conditions' => array(
                                    'AnnotatedDocument.document_id = DocumentsProject.document_id',
                                    'AnnotatedDocument.round_id' => $roundId,
                                    'NOT' => array(
                                          'text_marked' => 'NULL')),
                              'fields' => array(
                                    'count'),
                        ));
                        if ($annotatedDocumentsSize == 0) {
                            $this->Session->setFlash(__('This round has not annotated documents'));
                            $this->redirect(array(
                                  'controller' => 'projects',
                                  'action' => 'view',
                                  $projectId));
                        }
                        // Initialize archive object
                        $zip = new ZipArchive;
                        $packetName = $projectTitle . ".zip";
                        if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                            die("Failed to create archive\n");
                        }
                        $index = 0;
                        while ($index < $annotatedDocumentsSize) {
                            $annotatedDocuments = $this->AnnotatedDocument->find('all', array(
                                  'recursive' => -1,
                                  'joins' => array(
                                        array(
                                              'type' => 'inner',
                                              'table' => 'documents_projects',
                                              'alias' => 'DocumentsProject',
                                              'conditions' => array(
                                                    'DocumentsProject.project_id' => $projectId,
                                              )
                                        ),
                                        array(
                                              'type' => 'inner',
                                              'table' => 'documents',
                                              'alias' => 'Document',
                                              'conditions' => array(
                                                    'Document.id = DocumentsProject.document_id',
                                              )
                                        ),
                                  ),
                                  'conditions' => array(
                                        'AnnotatedDocument.document_id = DocumentsProject.document_id',
                                        'AnnotatedDocument.round_id' => $roundId,
                                        'NOT' => array(
                                              'text_marked' => 'NULL')),
                                  'fields' => array(
                                        'AnnotatedDocument.text_marked',
                                        'Document.external_id',
                                        'Document.title',
                                        'AnnotatedDocument.user_id',
                                        'Document.id'),
                                  'group' => array(
                                        'AnnotatedDocument.user_id',
                                        'AnnotatedDocument.round_id',
                                        'AnnotatedDocument.document_id'),
                                  'limit' => $documentsBuffer, //int
                                  'offset' => $index, //int
                            ));
                            foreach ($annotatedDocuments as $document) {
                                $id = $document['Document']['external_id'];
                                $fileName = $id . ".txt";
                                $file = new File($tempFolder->pwd() . DS . $fileName, 600);
                                if ($file->exists()) {
                                    $content = $document['AnnotatedDocument']['text_marked'];
                                    $content = preg_replace('/\s+/', ' ', $content);
                                    //las siguientes lineas son necesarias dado que cada navegador hace lo  que le da la gana con el DOM con respecto a la gramatica,
                                    //no hay un estandar asi por ejemplo en crhome existe Style:valor y en Explorer Style :valor,etc
                                    $content = str_replace(array(
                                          "\n",
                                          "\t",
                                          "\r"), '', $content);
                                    $content = str_replace('> <', '><', $content);
                                    $content = strip_tags($content);
                                    $content = html_entity_decode($content, ENT_QUOTES, "UTF-8");
                                    //echo $content;
                                    //throw new Exception;
                                    $file->write($content);
                                    $file->close();
                                    $zip->addFile($file->path, ltrim($fileName, '/'));
                                } else {
                                    throw new Exception("Error creating files ");
                                }
                            }
                            $index += $documentsBuffer;
                        }
                        $this->Document->virtualFields["abstract"] = "Document.raw";
                        $documentsList = $this->Document->find('all', array(
                              'recursive' => -1,
                              'fields' => array('id', 'external_id', 'title', 'abstract'),
                              'joins' => array(
                                    array(
                                          'type' => 'inner',
                                          'table' => 'documents_projects',
                                          'alias' => 'DocumentsProject',
                                          'conditions' => array(
                                                'DocumentsProject.project_id' => $projectId,
                                                'DocumentsProject.document_id = Document.id',
                                          )
                                    ),
                              ),
                        ));
                        $documentsList = Set::combine($documentsList, '{n}.Document.id', '{n}.Document');
                        $types = $this->Round->Project->Type->find('list', array(
                              'recursive' => -1,
                              'fields' => array('id', 'name'),
                              'conditions' => array(
                                    'Type.project_id' => $projectId
                              )
                        ));
                        $this->ConsensusAnnotation->virtualFields['text'] = 'annotation';
                        $this->ConsensusAnnotation->virtualFields['annotated_text'] = 'annotation';
                        $this->ConsensusAnnotation->virtualFields['offset'] = 'init';
                        $this->ConsensusAnnotation->virtualFields['length'] = 'end - init';
                        $fields = array('id',
                              'annotated_text',
                              'init',
                              'end',
                              'length',
                              'document_id',
                              'section',
                              'type_id',
                              'text',
                              'offset',
                              'length'
                        );
                        $annotations = $this->ConsensusAnnotation->find('all', array(
                              'recursive' => -1,
                              'conditions' => array(
                                    'ConsensusAnnotation.round_id' => $roundId,
                              ),
                              'fields' => $fields,
                        ));
                        $this->AnotationsExporter = $this->Components->load('AnotationsExporter');
                        $this->AnotationsExporter->format("tsv");
                        $content = $this->AnotationsExporter->export($annotations, $documentsList, $types);
                        $file = new File($tempFolder->pwd() . DS . "corpus.tsv", 600);
                        $file->append($content);
                        if ($file->exists()) {
                            $file->append($content);
                        } else {
                            throw new Exception("Error creating files ");
                        } $file->close();
                        $zip->addFile($file->path, ltrim("corpus.tsv", '/'));
                        if (!$zip->status == ZIPARCHIVE::ER_OK) {
                            throw new Exception("Error creating zip ");
                        }
                        /* ========================================== */
                        /* ====== relations consensus =============== */
                        /* ========================================== */
                        $this->Project->$projectId;
                        $relationLevel = $this->Project->field('relation_level');
                        /*
                          if ($relationLevel == 2) {
                          $conditions = array(
                          array(
                          'Annotation.annotated_text = ConsensusAnnotations.annotated_text',
                          'Annotation.document_id = ConsensusAnnotations.document_id',
                          'Annotation.type_id = ConsensusAnnotations.type_id',
                          'Annotation.round_id' => $roundId,
                          ),
                          );
                          } else {
                          $conditions = array(
                          array(
                          'Annotation.init = ConsensusAnnotations.init',
                          'Annotation.end = ConsensusAnnotations.end',
                          'Annotation.section = ConsensusAnnotations.section',
                          'Annotation.document_id = ConsensusAnnotations.document_id',
                          'Annotation.type_id = ConsensusAnnotations.type_id',
                          'Annotation.round_id' => $roundId,
                          ),
                          );
                          }
                          $db = $this->ConsensusAnnotation->Annotation->getDataSource();
                          $options = array(
                          'table' => $db->fullTableName($this->ConsensusAnnotation->Annotation),
                          'alias' => 'Annotation` USE INDEX (complex_index)`',
                          'fields' => array("Annotation.id"),
                          'joins' => array(
                          array(
                          'table' => 'consensus_annotations',
                          'alias' => 'ConsensusAnnotations',
                          'type' => 'INNER',
                          'conditions' => array(
                          'Annotation.round_id' => $roundId,
                          )
                          ),
                          ),
                          'recursive' => -1,
                          'conditions' => $conditions,
                          );
                          $query = $db->buildStatement($options, $this->ConsensusAnnotation->Annotation);
                          $query = str_replace("``", "", $query);
                          $ids = $db->query($query);
                          $ids = Set::classicExtract($ids, '{n}.Annotation.id'); */
                        $ids = $this->ConsensusAnnotation->find('list', array(
                              'recursive' => -1, //int
                              //array of field names
                              'fields' => array('ConsensusAnnotation.id', 'ConsensusAnnotation.id'),
                              'conditions' => array('ConsensusAnnotation.round_id' => $roundId), //array of conditions
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
                                                "Annotation_A.id = AnnotationsInterRelations.annotation_a_id",
                                                "Annotation_A.id" => $ids,
                                                'Annotation_A.round_id' => $roundId,
                                          )
                                    ),
                                    array(
                                          'table' => 'annotations',
                                          'alias' => 'Annotation_B',
                                          'type' => 'INNER',
                                          'conditions' => array(
                                                "Annotation_B.id  =AnnotationsInterRelations.annotation_b_id",
                                                "Annotation_B.id" => $ids,
                                                'Annotation_B.round_id' => $roundId,
                                          )
                                    ),
                              ),
                        ));
                        $annotationsRelations = Set::combine($annotationsRelations, '{n}.Relation.Rid', '{n}', '{n}.Annotation_A.document_id');
                        $this->AnotationsExporter->format("bioc");
                        $content = $this->AnotationsExporter->export($annotations, $documentsList, $types, $annotationsRelations);
                        $file = new File($tempFolder->pwd() . DS . "corpus.bioc", 600);
                        if ($file->exists()) {
                            $file->append($content);
                        } else {
                            throw new Exception("Error creating files ");
                        }
                        $file->close();
                        $zip->addFile($file->path, ltrim("corpus.bioc", '/'));
                        if (!$zip->status == ZIPARCHIVE::ER_OK) {
                            throw new Exception("Error creating zip ");
                        }
                        $zip->close();
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
        }
    }

}
