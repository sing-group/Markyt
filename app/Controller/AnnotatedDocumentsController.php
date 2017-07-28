<?php

App::uses('AppController', 'Controller');

/**
 * AnnotatedDocuments Controller
 *
 * @property AnnotatedDocument $AnnotatedDocument
 * @property PaginatorComponent $Paginator
 */
class AnnotatedDocumentsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('publicView');
        $this->Auth->allow('changeNabPosition');
    }

    private function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array(
              "\\",
              "/",
              "\"",
              "\n",
              "\r",
              "\t",
              "\x08",
              "\x0c");
        $replacements = array(
              "\\\\",
              "\\/",
              "\\\"",
              "\\n",
              "\\r",
              "\\t",
              "\\f",
              "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    public function changeNabPosition() {
        $changeBar = $this->Session->read("changeBar");
        if ($changeBar) {
            $this->Session->write("changeBar", false);
        } else {
            $this->Session->write("changeBar", true);
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => true)));
    }

    public function findDocuments($round_id = null, $user_id = null) {
        return $this->redirect(array(
                  'controller' => 'annotatedDocuments',
                  'action' => 'start',
                  $round_id,
                  $user_id,
                  true
        ));
    }

    public function viewRedirect($round_id = null, $user_id = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->UsersRound = $this->Round->UsersRound;
    }

    public function start($round_id = null, $user_id = null, $operation = null, $page = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->UsersRound = $this->Round->UsersRound;
        $this->Project = $this->Round->Project;
        $this->Document = $this->Project->Document;
        $this->Type = $this->Round->Type;
        $this->Relation = $this->Project->Relation;
        $this->DocumentsProject = $this->Project->DocumentsProject;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        $this->Job = $this->User->Job;
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        $find = false;
        $isReviewAutomaticAnnotation = false;
        switch ($operation) {
            case "find":
                $find = true;
                break;
            case "lastAutomatic":
                $isReviewAutomaticAnnotation = true;
                break;
        }
        $this->Round->id = $round_id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            $redirect = array(
                  'controller' => 'rounds',
                  'action' => 'index'
            );
            $user_id = $this->Session->read('user_id');
        } else {
            $redirect = array(
                  'controller' => 'rounds',
                  'action' => 'view',
                  $round_id
            );
        }
        if ($isReviewAutomaticAnnotation) {
            $documents = $this->Job->find('first', array(
                  'recursive' => -1, //int
                  'fields' => array("comments"),
                  'conditions' => array('Job.comments IS NOT NULL', 'Job.exception IS NULL',
                        'Job.user_id' => $user_id, 'Job.round_id' => $round_id, 'program' => 'Automatic_Dictionary_Annotation'), //array of conditions
                  'order' => array('Job.modified DESC'),
            ));
            if (!empty($documents)) {
                $documents = json_decode($documents['Job']['comments'], true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $documents = array_keys($documents["documentsWithAnnotations"]);
                    if (empty($documents)) {
                        $this->Session->setFlash(__('There are no recommendations for you [JSON empty]'));
                        $this->redirect($redirect);
                    }
                } else {
                    $this->Session->setFlash(__('There are no recommendations for you [JSON parser error]'));
                    $this->redirect($redirect);
                }
            } else {
                $this->Session->setFlash(__('There are no recommendations for you'));
                $this->redirect($redirect);
            }
        }
        $this->User->id = $user_id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //
        $userRound = $this->UsersRound->find('first', array(
              'recursive' => -1,
              'fields' => 'id',
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id)));
        $users_round_id = $userRound['UsersRound']['id'];
        $this->UsersRound->id = $userRound['UsersRound']['id'];
        if ($this->UsersRound->field('state') != 0) {
            $this->Session->setFlash(__('There are one process working in this documents'));
            $this->redirect($redirect);
        }
        $isMultiDocument = Configure::read('documentsPerPage') > 1;
        if ($find && $group_id == 1) {
            $isMultiDocument = false;
        } else {
            $find = false;
        }
        $round_id = $this->UsersRound->field('round_id');


        $round = $this->Round->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'Round.id' => $round_id
              )
        ));
        $this->Session->write('round_id', $round_id);




        if (empty($round)) {
            throw new NotFoundException(__('Empty round data'));
        }
        App::uses('CakeTime', 'Utility');
        $isEnd = CakeTime::isPast($round['Round']['ends_in_date']);
        if ($group_id > 1) {
            if (isset($round['Round']['start_document'])) {
                $offset = $round['Round']['start_document'] - 1;
                if ($offset < 0) {
                    $offset = 0;
                }
                $limit = $round['Round']['end_document'];
            } else {
                $offset = 0;
                $limit = 0;
            }
        } else {
            $offset = 0;
            $limit = 0;
            $isEnd = true;
        }
        $projectId = $round['Round']['project_id'];
        $onlyAnnotated = false;
        if ($group_id == 1 || $isEnd) {
            $onlyAnnotated = true;
        }


        if ($isReviewAutomaticAnnotation) {
            $setDocumentsToAnnotateByRound = Cache::read('documents-automatic-of-user' . $round_id . $user_id . count($documents), 'short');
            $documentsListByRound = Cache::read('documents-list-by-round' . $round_id . $user_id . count($documents), 'short');
        } else if ($onlyAnnotated) {
            $setDocumentsToAnnotateByRound = Cache::read('documents-annotated-of-user' . $round_id . $user_id . $offset . $limit, 'short');
            $documentsListByRound = Cache::read('documents-list-by-round' . $round_id . $user_id . $offset . $limit, 'short');
        } else {
            $setDocumentsToAnnotateByRound = Cache::read('documents-to-annotate-round' . $round_id . $offset . $limit, 'short');
            $documentsListByRound = Cache::read('documents-list-by-round' . $round_id . $offset . $limit, 'short');
        }
        if (empty($setDocumentsToAnnotateByRound)) {
            if ($isReviewAutomaticAnnotation) {
                $setDocumentsToAnnotateByRound = $this->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id',
                            'title',
                            'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'annotated_documents',
                                  'alias' => 'AnnotatedDocument',
                                  'type' => 'LEFT',
                                  'conditions' => array(
                                        'AnnotatedDocument.document_id = Document.id',
                                        'AnnotatedDocument.text_marked IS NOT NULL',
                                  ))
                      ),
                      'conditions' => array(
                            'AnnotatedDocument.round_id' => $round_id,
                            'AnnotatedDocument.user_id' => $user_id,
                            'AnnotatedDocument.document_id' => $documents,
                      ),
                      'order' => array(
                            'document_id ASC')
                ));
            } else if ($onlyAnnotated) {
                $setDocumentsToAnnotateByRound = $this->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id',
                            'title',
                            'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'annotated_documents',
                                  'alias' => 'AnnotatedDocument',
                                  'type' => 'LEFT',
                                  'conditions' => array(
                                        'AnnotatedDocument.document_id = Document.id',
                                        'AnnotatedDocument.text_marked IS NOT NULL',
                                  ))
                      ),
                      'conditions' => array(
                            'AnnotatedDocument.round_id' => $round_id,
                            'AnnotatedDocument.user_id' => $user_id,
                      ),
                      'limit' => $limit, //int
                      'offset' => $offset, //int
                      'order' => array(
                            'document_id ASC')
                ));
            } else {
                $setDocumentsToAnnotateByRound = $this->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id',
                            'title',
                            'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'documents_projects',
                                  'alias' => 'DocumentsProject',
                                  'type' => 'INNER',
                                  'conditions' => array(
                                        'DocumentsProject.document_id = Document.id',
                                  ))
                      ),
                      'conditions' => array(
                            'DocumentsProject.project_id' => $projectId,
                      ),
                      'limit' => $limit, //int
                      'offset' => $offset, //int
                      'order' => array(
                            'document_id ASC')
                ));
            }
            $documentsListByRound = array();








            $documentsListByRound = array();
            $copySetDocumentsToAnnotateByRound = array();
            $cont = 0;
            foreach ($setDocumentsToAnnotateByRound as $document) {
                $id = $document['Document']['id'];
                $copySetDocumentsToAnnotateByRound[$id] = $document['Document'];
                array_push($documentsListByRound, array('name' => $document['Document']['external_id'] . " - " . $document['Document']['title'],
                      'value' => $cont, 'data-document-id' => $id));
                $cont++;
            }
            $setDocumentsToAnnotateByRound = $copySetDocumentsToAnnotateByRound;
            if ($isReviewAutomaticAnnotation) {
                Cache::write('documents-automatic-of-user' . $round_id . $user_id . count($documents), $setDocumentsToAnnotateByRound, 'short');
                Cache::write('documents-list-by-round' . $round_id . $user_id . count($documents), $documentsListByRound, 'short');
            } else if ($onlyAnnotated) {
                Cache::write('documents-annotated-of-user' . $round_id . $user_id . $offset . $limit, $setDocumentsToAnnotateByRound, 'short');
                Cache::write('documents-list-by-round' . $round_id . $user_id . $offset . $limit, $documentsListByRound, 'short');
            } else {
                Cache::write('documents-to-annotate-round' . $projectId . $offset . $limit, $setDocumentsToAnnotateByRound, 'short');
                Cache::write('documents-list-by-round' . $round_id . $offset . $limit, $documentsListByRound, 'short');
            }
        }
        $idsDocumentsToAnnotateByRound = array_keys($setDocumentsToAnnotateByRound);
        if (isset($this->params['named']['page'])) {
            $page = $this->params['named']['page'];
            if ($page <= 0)
                $page = 1;
            if (!$isMultiDocument) {
                $document_id = $idsDocumentsToAnnotateByRound[$page - 1];
            }
        } else {
            $page = 1;
            if (!$isMultiDocument) {
                $document_id = $idsDocumentsToAnnotateByRound[0];
            }
        }
        if ($isMultiDocument) {
            /* ================================================================================= */
            /* ==================================Multidocument================================== */
            /* ================================================================================= */
            $this->paginate = array(
                  'recursive' => -1,
                  'order' => array(
                        'DocumentsProject.document_id' => 'asc'
                  ),
                  'conditions' => array(
                        'DocumentsProject.document_id' => $idsDocumentsToAnnotateByRound,
                        'DocumentsProject.project_id' => $projectId
                  ),
                  'limit' => Configure::read('documentsPerPage'),
                  'order' => 'document_id ASC',
            );
            $documentsProject = $this->paginate($this->DocumentsProject);
            $idsDocumentsOfPage = Hash::extract($documentsProject, '{n}.DocumentsProject.document_id');
            if (empty($idsDocumentsOfPage)) {
                $this->Session->setFlash(__('There are no documents annotated'));
                $this->redirect($redirect);
            }
            if ($group_id > 1) {


                $deleteCascade = Configure::read('deleteCascade');
                $this->Annotation->deleteAll(array(
                      'Annotation.round_id' => $round_id,
                      'Annotation.user_id' => $user_id,
                      'Annotation.document_id' => $idsDocumentsOfPage,
                      'Annotation.init IS NULL',
                      'Annotation.end IS NULL'
                    ), $deleteCascade);
            }
            $annotatedDocuments = $this->AnnotatedDocument->find('all', array(
                  'fields' => array(
                        'id',
                        'document_id',
                        'text_marked'),
                  'recursive' => -1,
                  'conditions' => array(
                        'round_id' => $round_id,
                        'user_id' => $user_id,
                        'document_id' => $idsDocumentsOfPage,
                  ),
                  'order' => array(
                        'AnnotatedDocument.document_id Asc')
            ));
            $documentsAnnotatedIds = array();
            $annotatedDocumentsCopy = array();




            foreach ($annotatedDocuments as $annotatedDocument) {
                $documentsAnnotatedIds[] = $annotatedDocument["AnnotatedDocument"]["document_id"];
                $annotatedDocumentsCopy[$annotatedDocument["AnnotatedDocument"]["document_id"]] = $annotatedDocument["AnnotatedDocument"];
            }
            $annotatedDocuments = $annotatedDocumentsCopy;
            unset($annotatedDocumentsCopy);


            $documentsOfPage = Cache::read('documents-page' . $page, 'short');
            if (!$documentsOfPage) {
                $documentsOfPage = $this->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array(
                            'html',
                            'id',
                            'title',
                            'external_id'),
                      'conditions' => array(
                            'Document.id' => $idsDocumentsOfPage,
                      )
                ));
                $documentsOfPage = Set::combine($documentsOfPage, '{n}.Document.id', '{n}.Document');
                Cache::write('documents-page' . $page . $user_id, $documentsOfPage, 'short');
            }


























            $size = count($idsDocumentsOfPage);
            for ($i = 0; $i < $size; $i++) {
                $document_id = intval($idsDocumentsOfPage[$i]);
                if (!empty($annotatedDocuments[$document_id])) {
                    if (strlen(trim($annotatedDocuments[$document_id]['text_marked'])) == 0) {
                        $annotatedDocuments[$document_id]['text_marked'] = $documentsOfPage[$document_id]['html'];
                    }
                } else if (strlen(trim($documentsOfPage[$document_id]['html'])) !== 0) {
                    $annotatedDocuments[$document_id] = array(
                          'user_id' => $user_id,
                          'round_id' => $round_id,
                          'document_id' => $document_id,
                          'annotation_minutes' => 0
                    );
                    if ($group_id > 1 && !$isEnd) {
                        if (empty($annotatedDocuments[$document_id]['html'])) {


                            $this->AnnotatedDocument->create();
                            if (!$this->AnnotatedDocument->save($annotatedDocuments[$document_id])) {
                                debug($annotatedDocuments);
                                debug($this->AnnotatedDocument->validationErrors);
                                $this->Session->setFlash(__('ops! error creating AnnotatedDocument '));
                                throw new Exception;
                            }
                            $annotatedDocuments[$document_id]['id'] = $this->AnnotatedDocument->id;
                        }
                    } else {
                        $annotatedDocuments[$document_id]['id'] = -1;
                    }
                    $annotatedDocuments[$document_id]['text_marked'] = $documentsOfPage[$document_id]['html'];
                    $annotatedDocuments[$document_id]['document_id'] = $document_id;
                }
                $title = "Title: " . $setDocumentsToAnnotateByRound[$document_id]['title'];
                if (isset($setDocumentsToAnnotateByRound[$document_id]['external_id'])) {
                    $title = "ID: " . $setDocumentsToAnnotateByRound[$document_id]['external_id'];
                }
                $annotatedDocuments[$document_id]['title'] = $title;
            }
            $documentAssessment = $this->Document->DocumentsAssessment->find('list', array(
                  'recursive' => -1,
                  'fields' => array(
                        'document_id',
                        'document_id'),
                  'conditions' => array(
                        'user_id' => $user_id,
                        'project_id' => $projectId,
                        'document_id' => $idsDocumentsOfPage)));
            $this->set('documentAssessments', $documentAssessment);
            $this->set('annotatedDocuments', $annotatedDocuments);
            $triada = array(
                  'user_id' => $user_id,
                  'round_id' => $round_id,
                  'document_id' => -1,
                  'users_round_id' => $users_round_id);
            $this->set('DocumentsProject', $documentsProject);
        } else {
            /* ================================================================================= */
            /* ==================================No multidocument=============================== */
            /* ================================================================================= */
            $document = Cache::read('document-id-' . $document_id, 'short');
            if (!$document) {
                $document = $this->Document->find('first', array(
                      'recursive' => -1,
                      'fields' => array(
                            'html',
                            'title',
                            'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'documents_projects',
                                  'alias' => 'DocumentsProject',
                                  'type' => 'INNER',
                                  'conditions' => array(
                                        'DocumentsProject.document_id = Document.id',
                                  ))
                      ),
                      'order' => array('document_id' => 'ASC'),
                      'conditions' => array(
                            'DocumentsProject.project_id' => $projectId,
                            'DocumentsProject.document_id' => $document_id,
                      )
                ));
                Cache::write('first-doc-project-id-' . $document_id, $document, 'short');
            }
            $annotatedDocuments = $this->AnnotatedDocument->find('first', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'round_id' => $round_id,
                        'user_id' => $user_id,
                        'document_id' => $document_id,
                  ),
                  'order' => array(
                        'AnnotatedDocument.document_id Asc')
            ));
            if (!empty($annotatedDocuments)) {
                if (strlen(trim($annotatedDocuments['AnnotatedDocument']['text_marked'])) !== 0) {
                    $this->set('text', $annotatedDocuments['AnnotatedDocument']['text_marked']);
                    $this->set('document_annotated_id', $annotatedDocuments['AnnotatedDocument']['id']);
                } else {
                    $this->set('document_annotated_id', -1);
                    $this->set('text', $document['Document']['html']);
                }
            } else if (!empty($document) && strlen(trim($document['Document']['html'])) !== 0) {
                $annotatedDocuments = array(
                      'AnnotatedDocument' => array(
                            'user_id' => $user_id,
                            'round_id' => $round_id,
                            'document_id' => $document_id,
                            'text_marked' => $document['Document']['html'],
                            'annotation_minutes' => 0
                ));
                if ($group_id > 1 && !$isEnd) {
                    if (!$this->AnnotatedDocument->save($annotatedDocuments)) {
                        debug($this->AnnotatedDocument->validationErrors);
                        $this->Session->setFlash(__('ops! error creating AnnotatedDocument '));
                        $this->redirect($redirect);
                    }
                    $annotatedDocuments['AnnotatedDocument']['id'] = $this->AnnotatedDocument->id;
                } else {
                    $annotatedDocuments['AnnotatedDocument']['id'] = -1;
                }
                $title = "Title: " . $setDocumentsToAnnotateByRound[$document_id]['title'];
                if (isset($setDocumentsToAnnotateByRound[$document_id]['external_id'])) {
                    $title = "ID: " . $setDocumentsToAnnotateByRound[$document_id]['external_id'];
                }
                $annotatedDocuments[$document_id]['title'] = $title;
                $this->set('document_annotated_id', $annotatedDocuments['AnnotatedDocument']['id']);
                $this->set('text', $document['Document']['html']);
            } else {
                $this->Session->setFlash(__('There are no documents associated with this project'));
                $this->redirect($redirect);
            }
            if ($group_id > 1) {


                $deleteCascade = Configure::read('deleteCascade');
                $this->Annotation->deleteAll(array(
                      'Annotation.round_id' => $round_id,
                      'Annotation.user_id' => $user_id,
                      'Annotation.init IS NULL',
                      'Annotation.end IS NULL'
                    ), $deleteCascade);
            }
            $this->paginate = array(
                  'recursive' => -1,
                  'order' => array(
                        'DocumentsProject.document_id' => 'ASC'
                  ),
                  'conditions' => array(
                        'document_id' => array_keys($setDocumentsToAnnotateByRound),
                        'DocumentsProject.project_id' => $projectId
                  ),
                  'limit' => 1,
            );
            $this->set('DocumentsProject', $this->paginate($this->DocumentsProject));
            $title = "Title: " . $document['Document']['title'];
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }
            $this->set('title', $title);


            $triada = array(
                  'user_id' => $user_id,
                  'round_id' => $round_id,
                  'document_id' => $document_id,
                  'users_round_id' => $users_round_id,
                  'document_annotated_id' => $annotatedDocuments['AnnotatedDocument']['id']
            );
        }
        /* ======================================= */
        $highlight = 2;
        if (isset($round['Round']['highlight']))
            $highlight = $round['Round']['highlight'];
        $trim_helper = $round['Round']['trim_helper'];
        $whole_word_helper = $round['Round']['whole_word_helper'];
        $punctuation_helper = $round['Round']['punctuation_helper'];


        $this->Session->write('isEnd', $isEnd);


        $this->Session->write('triada', $triada);




        $relations = Cache::read('relations-project-id-' . $projectId, 'short');
        if (!$relations) {
            $relations = $this->Relation->find('all', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Relation.project_id' => $projectId,
                  )
            ));
            Cache::write('relations-project-id-' . $projectId, $relations, 'short');
        }
        $types = Cache::read('usersRoundTypes-round-id-' . $round_id, 'short');
        if (!$types) {
            $types = $this->Type->find('all', array(
                  'recursive' => -1,
                  'contain' => array(
                        'Question',
                  ),
                  'joins' => array(
                        array(
                              'table' => 'types_rounds',
                              'alias' => 'TypesRound',
                              'type' => 'LEFT',
                              'conditions' => array(
                                    'TypesRound.type_id = Type.id',
                              ))
                  ),
                  'conditions' => array(
                        'TypesRound.round_id' => $round_id
                  )
            ));
            Cache::write('usersRoundTypes-round-id-' . $round_id, $types, 'short');
        }
        $relationIds = Hash::extract($relations, '{n}.Relation.id');
        if (!$find) {
            $this->set('documentsMap', $documentsOfPage);
            $this->set('isReviewAutomaticAnnotation', $isReviewAutomaticAnnotation);
        } else {
            $idsDocumentsOfPage = $document_id;
        }
        if (!$this->Session->check('start_step') && $group_id > 1) {
            $this->Session->write('start_step', new DateTime(''));
        }
        $annotationsInterRelations = $this->AnnotationsInterRelation->find('all', array(
              'recursive' => -1, //int
              'fields' => array(
                    'id',
                    'relation_id',
                    'AnnotationB.annotated_text',
                    'AnnotationA.annotated_text',
                    'AnnotationB.type_id',
                    'AnnotationA.type_id',
                    'AnnotationB.document_id',
                    'AnnotationA.document_id',
                    'AnnotationB.id',
                    'AnnotationA.id',
              ),
              'conditions' => array(
                    'AnnotationA.user_id' => $user_id,
                    'AnnotationA.round_id' => $round_id,
                    'AnnotationB.user_id' => $user_id,
                    'AnnotationB.round_id' => $round_id,
              ), //array of conditions
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationA',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.id = AnnotationsInterRelation.annotation_a_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationA.document_id' => $idsDocumentsOfPage,
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationB',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationB.id = AnnotationsInterRelation.annotation_b_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationB.document_id' => $idsDocumentsOfPage,
                          )
                    ),
              ),
              'order' => array('AnnotationA.document_id' => 'ASC')
        ));
        $interRelationsMap = array();
        foreach ($annotationsInterRelations as $annotationsInterRelation) {
            $annotationA = $annotationsInterRelation["AnnotationA"]["id"];
            $annotationB = $annotationsInterRelation["AnnotationB"]["id"];
            $relationId = $annotationsInterRelation["AnnotationsInterRelation"]["relation_id"];
            $inteRelationId = $annotationsInterRelation["AnnotationsInterRelation"]["id"];










            $interRelationsMap[$annotationA][$annotationB][$relationId]["relationId"] = $relationId;
            $interRelationsMap[$annotationA][$annotationB][$relationId]["directedTo"] = $annotationB;
            $interRelationsMap[$annotationA][$annotationB][$relationId]["interRelationId"] = $inteRelationId;
            $interRelationsMap[$annotationB][$annotationA][$relationId]["relationId"] = $relationId;
            $interRelationsMap[$annotationB][$annotationA][$relationId]["directedTo"] = $annotationB;
            $interRelationsMap[$annotationB][$annotationA][$relationId]["interRelationId"] = $inteRelationId;
        }
        $this->set('annotationsInterRelations', $annotationsInterRelations);
        $this->set('relationsMap', Set::combine($relations, '{n}.Relation.id', '{n}.Relation'));
        $this->set('interRelationsMap', $interRelationsMap);
        $this->set('document_id', $document_id);
        $this->set('findMode', $find);
        $this->set('isMultiDocument', $isMultiDocument);
        $this->set('typesMap', Set::combine($types, '{n}.Type.id', '{n}.Type'));
        /* ================================================================================= */
        /* ==================================No Ajax================================== */
        /* ================================================================================= */
        if (!$this->request->is('ajax')) {
            if (empty($types)) {
                $this->Session->setFlash(__('There are no types associated with this round'));
                return $this->redirect($redirect);
            }


            $nonTypes = Cache::read('nonTypes-round-id-' . $round_id, 'short');
            if (!$nonTypes) {
                $listTypes = Set::classicExtract($types, '{n}.Type.id');
                $nonTypes = $this->Type->find('list', array(
                      'fields' => 'Type.name',
                      'recursive' => -1,
                      'conditions' => array(
                            'Type.project_id' => $projectId,
                            "NOT" => array(
                                  'Type.id' => $listTypes
                            ),
                      )
                ));
                Cache::write('nonTypes-round-id-' . $round_id, $nonTypes, 'short');
            }






            $this->set('documentList', $documentsListByRound);
            if ($page > 0) {
                $page--;
            }
            $this->set('documentsPerPage', Configure::read('documentsPerPage'));
            $this->set('documentsMap', array_flip($idsDocumentsToAnnotateByRound));
            $this->set('relations', $relations);
            $this->set('operation', $operation);
            $this->set('page', $page);
            $this->set('round_id', $round_id);
            $this->set('project_id', $projectId);
            $this->set('types', $types);


            $this->set('nonTypes', $nonTypes);
            $this->set('isEnd', $isEnd);
            $this->set('user_id', $user_id);
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }
            $this->set('title', $title);
            $this->set('trim_helper', $trim_helper);
            $this->set('highlight', $highlight);
            $this->set('whole_word_helper', $whole_word_helper);
            $this->set('punctuation_helper', $punctuation_helper);
            $this->set('annotationMenu', true);
            $this->render("start");
        } else {
            $this->layout = 'ajax';








            $this->response->header(array(
                  "Pragma" => "no-cache",
            ));
            $this->response->expires(0);
            $this->response->disableCache();
            $this->set('title', $title);
            $this->set('isEnd', $isEnd);
            $this->render("ajax");
        }
    }

    public function simpleSave() {
        $userId = $this->Session->read("user_id");
        $roundId = $this->Session->read("round_id");
        $isEnd = $this->Session->read('isEnd');
        if (isset($this->request->data['documents']) && !$isEnd) {
            $db = $this->AnnotatedDocument->getDataSource();
            $db->begin();
            $documents = $this->request->data['documents'];
            $size = count($documents);
            for ($index = 0; $index < $size; $index++) {
                $document = $documents[$index];
                if (!isset($document['id'])) {
                    $document['id'] = -1;
                }
                if (!isset($document['document_id'])) {
                    $document['document_id'] = -1;
                }
                $annotatedDocument = array();
                $annotatedDocument['document_id'] = $document['document_id'];
                $annotatedDocument['text_marked'] = $document['text_marked'];
                $annotatedDocument['id'] = $document['id'];
                $conditions = array(
                      'id' => $annotatedDocument['id'],
                      'document_id' => $annotatedDocument['document_id'],
                      'user_id' => $userId,
                      'round_id' => $roundId,
                );
                if (!$this->AnnotatedDocument->hasAny($conditions)) {
                    $db->rollback();
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "Ops! This documents could not be saved. 404 error")));
                } else {
                    if ($this->AnnotatedDocument->save($annotatedDocument)) {
                        $db->commit();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => true)));
                    } else {
                        $db->rollback();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "The round could not be saved. Info: Error document." . $annotatedDocument['id'])));
                    }
                }
            }
        }
    }

    /**
     * edit method
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function save() {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $this->Annotation = $this->Round->Annotation;
        $this->autoRender = false;
        $triada = $this->Session->read('triada');
        $this->AnnotatedDocument->id = $triada['document_annotated_id'];
        if (!$this->AnnotatedDocument->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $pos = $this->request->data['AnnotatedDocument']['page'];
            $isEnd = $this->Session->read('isEnd');
            $this->UsersRound->id = $triada['users_round_id'];
            $state = $this->UsersRound->field('state');
            if (strlen(trim($this->request->data['AnnotatedDocument']['text_marked'])) !== 0 && !$isEnd && $state == 0) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                $textoMarky = trim($this->request->data['AnnotatedDocument']['text_marked']);
                $textoForMatches = $this->parseHtmlToGetAnnotations($textoMarky);
                debug($this->request->data);
                throw new Exception;
                $annotations = $this->getAnnotations($textoMarky);
                debug($annotations);
                throw new Exception;
                $parseKey = Configure::read('parseKey');
                $parseIdAttr = Configure::read('parseIdAttr');
                preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                $allAnnotations = array();
                $db = $this->AnnotatedDocument->getDataSource();
                $db->begin();
                if (sizeof($matches[1]) != 0) {
                    $this->recursive = -1;
                    $accum = 0;
                    preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/u', $matches[1][0][0], $value);
                    $insertID = $value[1];
                    $lastID = -1;
                    $original_start = mb_strlen(substr($textoForMatches, 0, $matches[0][$i][1]));
                    $textAcummulate = 0;
                    $text = "";
                    for ($i = 0; $i < sizeof($matches[1]); $i++) {
                        preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                        if (!isset($value[1])) {
                            $db->rollback();
                            throw new Exception("Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0]);
                        }
                        if ($insertID != $value[1]) {
                            $lastID = $insertID;
                            $this->Annotation->id = $insertID;
                            if ($this->Annotation->exists() && $this->Annotation->save(array(
                                      'init' => $original_start,
                                      'end' => $original_start + $textAcummulate
                                ))) {
                                array_push($allAnnotations, $insertID);
                            } else {
                                $this->Session->setFlash(__("This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text));
                                $db->rollback();
                                return $this->redirect(array(
                                          'controller' => 'annotatedDocuments',
                                          'action' => 'start',
                                          $triada['round_id'], $triada['user_id'],
                                          'page' => $this->request->data['AnnotatedDocument']['page']
                                ));
                            }
                            $text = "";
                            $insertID = $value[1];
                            $original_start = $matches[1][$i][1] - $accum;
                        } //$insertID != $value[1]
                        $textAcummulate += mb_strlen(strip_tags($matches[0][$i][0]));
                        $text .= $matches[0][$i][0];
                        $accum = $accum + mb_strlen($matches[1][$i][0]) + mb_strlen("</mark>");
                    } //$i = 0; $i < sizeof($matches[1]); $i++
                    if ($lastID != $insertID) {
                        $this->Annotation->id = $insertID;
                        if ($this->Annotation->exists() && $this->Annotation->save(array(
                                  'init' => $original_start,
                                  'end' => $original_start + $textAcummulate
                            ))) {
                            array_push($allAnnotations, $insertID);
                        } else {
                            $this->Session->setFlash(__("This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text));
                            $db->rollback();
                            return $this->redirect(array(
                                      'controller' => 'annotatedDocuments',
                                      'action' => 'start',
                                      $triada['round_id'], $triada['user_id'],
                                      'page' => $this->request->data['AnnotatedDocument']['page']
                            ));
                        }
                    } //$lastID != $insertID
                } //sizeof($matches[1]) != 0
                $this->request->data['AnnotatedDocument']['text_marked'] = $textoMarky;
                $deleteCascade = Configure::read('deleteCascade');
                $this->Annotation->deleteAll(array(
                      'not' => array(
                            'Annotation.id' => $allAnnotations
                      ),
                      'Annotation.round_id' => $triada['round_id'],
                      'Annotation.user_id' => $triada['user_id'],
                      'Annotation.document_id' => $triada['document_id'],
                    ), $deleteCascade);
                if ($this->AnnotatedDocument->save($this->request->data)) {
                    $db->commit();
                    $this->Session->setFlash(__('Changes has been saved'), 'success');
                } //$this->UsersRound->save($this->request->data)
                else {
                    $db->rollback();
                    $this->Session->setFlash(__('The round could not be saved. Info: save Annoted text. Please, try again.'));
                    return $this->redirect(array(
                              'controller' => 'annotatedDocuments',
                              'action' => 'start',
                              $triada['round_id'], $triada['user_id'],
                              'page' => $this->request->data['AnnotatedDocument']['page']
                    ));
                }
            }
            return $this->redirect(array(
                      'controller' => 'annotatedDocuments',
                      'action' => 'start',
                      $triada['round_id'], $triada['user_id'],
                      'page' => $this->request->data['AnnotatedDocument']['page']
            ));
        }
    }

    public function saveAjax() {
        $this->autoRender = false;
        $triada = $this->Session->read('triada');
        $this->Round = $this->AnnotatedDocument->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $this->Annotation = $this->Round->Annotation;
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->UsersRound->id = $triada['users_round_id'];
            $state = $this->UsersRound->field('state');
            $isEnd = $this->Session->read('isEnd');
            if (isset($this->request->data['documents']) && $state == 0 && !$isEnd) {
                $db = $this->UsersRound->getDataSource();
                $db->begin();
                $commit = true;
                $documents = $this->request->data['documents'];
                $size = count($documents);
                $annotationsToSave = array();
                for ($index = 0; $index < $size; $index++) {
                    $document = $documents[$index];
                    if (!isset($document['id'])) {
                        $document['id'] = -1;
                    }
                    if (!isset($document['document_id'])) {
                        $document['document_id'] = -1;
                    }
                    $annotatedDocument = array();
                    $annotatedDocument['document_id'] = $document['document_id'];
                    $annotatedDocument['id'] = $document['id'];
                    $conditions = array(
                          'id' => $annotatedDocument['id'],
                          'document_id' => $annotatedDocument['document_id'],
                          'user_id' => $triada['user_id'],
                          'round_id' => $triada['round_id'],
                    );
                    if (!$this->AnnotatedDocument->hasAny($conditions)) {
                        $db->rollback();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "Ops! This documents could not be saved. 404 error")));
                    }
                    if (strlen(trim($document['text_marked'])) !== 0) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                        $textoMarky = $document['text_marked'];
                        $annotations = $this->getAnnotations($textoMarky, $annotatedDocument['document_id']);
                        foreach ($annotations as $annotation) {
                            $text = $annotation["annotated_text"];
                            $this->Annotation->id = $annotation["id"];
                            if ($this->Annotation->exists() && $this->Annotation->save(array(
                                      'init' => $annotation["init"],
                                      'end' => $annotation["end"]
                                ))) {
                                array_push($annotationsToSave, $this->Annotation->id);
                            } else {
                                $db->rollback();
                                return $this->correctResponseJson(json_encode(array(
                                          'success' => false,
                                          'message' => "This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text)));
                            }
                        }
                        $document['text_marked'] = $textoMarky;
                        $deleteCascade = Configure::read('deleteCascade');
                        $this->Annotation->deleteAll(array(
                              'not' => array(
                                    'Annotation.id' => $annotationsToSave
                              ),
                              'Annotation.round_id' => $triada['round_id'],
                              'Annotation.user_id' => $triada['user_id'],
                              'Annotation.document_id' => $annotatedDocument['document_id'],
                            ), $deleteCascade);
                        if (!$this->AnnotatedDocument->save($document)) {
                            $db->rollback();
                            return $this->correctResponseJson(json_encode(array(
                                      'success' => false,
                                      'message' => "The round could not be saved. Info: Error document." . $annotatedDocument['id'])));
                        }
                    }
                }
                if ($commit) {
                    $cond = array(
                          'round_id' => $triada['round_id'],
                          'user_id' => $triada['user_id'],
                    );
                    $this->UsersRound->recursive = -1;
                    $this->UsersRound->UpdateAll(array(
                          'modified' => 'NOW()'
                        ), $cond);
                    $db->commit();
                    return $this->correctResponseJson(json_encode(array(
                              'success' => true,
                    )));
                } //$this->UsersRound->save($this->request->data)
                else {
                    $db->rollback();
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "The round could not be saved. Info: save Annoted text. Please, try again.")));
                }
            }
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "Ops! This documents could not be saved. Final return")));
    }

    public function getAnnotatedDocument() {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $roundId = $this->request->query["roundId"];
        $userId = $this->request->query["userId"];
        $documentId = $this->request->query["documentId"];
        $this->Round->id = $roundId;
        $this->User->id = $userId;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $document = $this->AnnotatedDocument->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $userId,
                    'round_id' => $roundId,
                    'document_id' => $documentId,
        )));
        return $this->correctResponseJson(
                array(
                      'success' => true,
                      'html' => $document["AnnotatedDocument"]['text_marked'])
        );
    }

    public function getDocumentOfAnntation() {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->Annotation = $this->Round->Annotation;
        $roundId = $this->request->query["roundId"];
        $userId = $this->request->query["userId"];
        $annotationId = $this->request->query["annotationId"];
        $this->Round->id = $roundId;
        $this->User->id = $userId;
        $this->Annotation->id = $annotationId;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        if (!$this->Annotation->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $annotation = $this->Annotation->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $annotationId,
        )));
        $documents = $this->Annotation->find('list', array(
              'fields' => array('id', 'document_id'),
              'recursive' => -1,
              'conditions' => array(
                    'annotated_text' => $annotation['Annotation']['annotated_text'],
                    'user_id' => $userId,
                    'round_id' => $roundId,
        )));
        $documents = $this->AnnotatedDocument->find('all', array(
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $userId,
                    'round_id' => $roundId,
                    'document_id' => $documents,
              )
            )
        );
        $html = "";
        foreach ($documents as $document) {
            $html .= "<div class='col-md-12'>" . $document["AnnotatedDocument"]['text_marked'] . "</div>";
        }
        return $this->correctResponseJson(
                array(
                      'success' => true,
                      'html' => $html
                )
        );
    }

    /**
     * start method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($round_id = null, $user_id = null) {
        $this->Project = $this->Round->Project;
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->Document = $this->Project->Document;
        $this->Type = $this->Round->Type;
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            $user_id = $this->Session->read('user_id');
            $hasAnnotatedDocument = $this->AnnotatedDocument->hasAny(array(
                  'round_id' => $round_id,
                  'user_id' => $user_id));
            if (!$hasAnnotatedDocument) {
                throw new NotFoundException(__('Invalid round'));
            }
        }
        $this->Round->id = $round_id;
        $this->User->id = $user_id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $count = $this->AnnotatedDocument->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id,
                    array(
                          'NOT' => array(
                                'text_marked' => NULL))
        )));
        if ($count == 0) {
            $this->Session->setFlash(__('This user has not annotated any document'));
            $this->redirect(array(
                  'controller' => 'rounds',
                  'action' => 'view',
                  $round_id
            ));
        } //!
        $data = $this->Session->read('data' . $round_id);
        if (!empty($data) && !isset($data['nonTypes'])) {
            $this->Session->delete('data' . $round_id);
            $data = null;
        }
        if (empty($data)) {
            $data = array();
            $cond = array(
                  'TypesRound.Round_id' => $round_id
            );
            $typesId = $this->Round->TypesRound->find('list', array(
                  'fields' => 'TypesRound.type_id', 'TypesRound.type_id',
                  'conditions' => $cond,
                  'recursive' => -1
            ));
            $types = $this->Type->find('all', array(
                  'contain' => array(
                        'Question'),
                  'recursive' => -1,
                  'conditions' => array(
                        'Type.id' => $typesId
                  )
            ));
            $projectId = $types[0]['Type']['project_id'];
            $data['projectId'] = $projectId;
            $nonTypes = $this->Type->find('all', array(
                  'fields' => 'Type.name',
                  'recursive' => -1,
                  'conditions' => array(
                        "NOT" => array(
                              'Type.id' => $typesId
                        ),
                        'Type.project_id' => $projectId
                  )
            ));
            $nonTypes = $this->flatten($nonTypes);
            foreach ($types as &$type):
                $type['Type']['description'] = str_replace("'", '"', $type['Type']['description']);
            endforeach;
            $documents = $this->Document->find('list', array(
                  'joins' => array(
                        array(
                              'table' => 'documents_projects',
                              'alias' => 'DocumentsProject',
                              'type' => 'Inner',
                              'conditions' => array(
                                    'DocumentsProject.document_id=Document.id'
                              )
                        ),
                        array(
                              'table' => 'annotated_document',
                              'alias' => 'AnnotatedDocument',
                              'type' => 'Inner',
                              'conditions' => array(
                                    'AnnotatedDocument.document_id=Document.id'
                              )
                        )),
                  'conditions' => array(
                        'project_id' => $projectId,
                        array(
                              'NOT' => array(
                                    'AnnotatedDocument.text_marked' => NULL))),
                  'order' => array(
                        'id' => 'asc'
                  )
            ));
            $data['types'] = $types;
            $data['nonTypes'] = $nonTypes;
            $data['projectId'] = $projectId;
            $data['documents'] = $documents;
            $this->Session->write('data' . $round_id, $data);
        } //empty($data)
        else {
            $types = $data['types'];
            $nonTypes = $data['nonTypes'];
            $projectId = $data['projectId'];
            $documents = $data['documents'];
        }
        $user = $this->UsersRound->User->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $user_id)
        ));
        $this->set('types', $types);
        $this->set('nonTypes', $nonTypes);
        $this->set('round_id', $round_id);
        $this->set('user_id', $user_id);
        $this->set('project_id', $projectId);
        $this->set('isEnd', true);
        $this->set('documents', $documents);
        $this->set('fullName', $user['User']['full_name']);
        $this->paginate = array(
              'recursive' => -1,
              'order' => array(
                    'AnnotatedDocument.document_id' => 'asc'
              ),
              'limit' => 1,
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id,
                    array(
                          'NOT' => array(
                                'text_marked' => NULL)))
        );
        $this->set('UsersRound', $this->paginate());
    }

    /**
     * externalView method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function publicView($external_id = null, $project_id = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->Type = $this->Round->Type;
        $this->Project = $this->Round->Project;
        $this->Document = $this->Project->Document;
        $this->Relation = $this->Project->Relation;
        $this->GoldenProject = $this->Project->GoldenProject;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        if (!isset($external_id)) {
            throw new NotFoundException(__('Invalid document_id'));
        }
        $this->Project->id = $project_id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        $openAnnotatedDocs = Configure::read('openAnnotatedDocs');
        if (!$openAnnotatedDocs) {
            throw new NotFoundException(__('No open docs'));
        } else {
            $goldenProject = $this->GoldenProject->find('first', array(
                  'recursive' => -1,
                  'fields' => array(
                        'user_id',
                        'round_id'),
                  'conditions' => array(
                        'project_id' => $project_id),
            ));
            if (!empty($goldenProject)) {
                $user_id = $goldenProject['GoldenProject']['user_id'];
                $round_id = $goldenProject['GoldenProject']['round_id'];
            } else {
                throw new NotFoundException(__('Invalid golden project'));
            }
        }
        $hasAnyAnnotatedDocument = $this->AnnotatedDocument->hasAny(array(
              'round_id' => $round_id,
              'user_id' => $user_id));
        if (!$hasAnyAnnotatedDocument) {
            throw new NotFoundException(__('Invalid round'));
        }
        $this->Round->id = $round_id;
        $this->User->id = $user_id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $count = $this->AnnotatedDocument->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id,
                    array(
                          'NOT' => array(
                                'text_marked' => NULL))
        )));
        if ($count == 0) {
            throw new NotFoundException(__('This user has not annotated any document'));
        } //!
        $round = Cache::read('round-id-' . $round_id, 'short');
        if (!$round) {
            $round = $this->Round->find('first', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Round.id' => $round_id
                  )
            ));
            Cache::write('round-id-' . $round_id, $round, 'short');
        }
        if (empty($round)) {
            throw new NotFoundException(__('Invalid round'));
        }
        Cache::write('round-id-' . $round_id, $round, 'short');
        $isEnd = true;
        $projectId = $round['Round']['project_id'];
        $onlyAnnotated = true;
        $document = Cache::read('annotated_document' . $round_id . '-' . $user_id . '-' . $external_id, 'short');
        if (!$document) {
            $document = $this->Document->find('first', array(
                  'recursive' => -1,
                  'fields' => array(
                        'id',
                        'title',
                        'external_id'),
                  'joins' => array(
                        array(
                              'table' => 'documents_projects',
                              'alias' => 'DocumentsProject',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'DocumentsProject.document_id = Document.id',
                              ))
                  ),
                  'conditions' => array(
                        'Document.external_id' => $external_id,
                  ),
            ));
            Cache::write('annotated_document' . $round_id . '-' . $user_id . '-' . $external_id, $document, 'short');
        }
        if (empty($document)) {
            $this->redirect('http://www.ncbi.nlm.nih.gov/pubmed/' . $external_id);
        }
        $document_id = $document['Document']['id'];
        $types = Cache::read('usersRoundTypes-round-id-' . $round_id, 'short');
        if (!$types) {
            $types = $this->Type->find('all', array(
                  'recursive' => -1,
                  'contain' => array(
                        'Question',
                  ),
                  'joins' => array(
                        array(
                              'table' => 'types_rounds',
                              'alias' => 'TypesRound',
                              'type' => 'LEFT',
                              'conditions' => array(
                                    'TypesRound.type_id = Type.id',
                              ))
                  ),
                  'conditions' => array(
                        'TypesRound.round_id' => $round_id
                  )
            ));
            Cache::write('usersRoundTypes-round-id-' . $round_id, $types, 'short');
        }
        if (empty($types)) {
            throw new NotFoundException(__('Invalid types'));
        }
        $nonTypes = Cache::read('nonTypes-round-id-' . $round_id, 'short');
        if (!$nonTypes) {
            $listTypes = Set::classicExtract($types, '{n}.Type.id');
            $nonTypes = $this->Type->find('list', array(
                  'fields' => 'Type.name',
                  'recursive' => -1,
                  'conditions' => array(
                        'Type.project_id' => $projectId,
                        "NOT" => array(
                              'Type.id' => $listTypes
                        ),
                  )
            ));
            Cache::write('nonTypes-round-id-' . $round_id, $nonTypes, 'short');
        }
        $relations = Cache::read('relations-project-id-' . $projectId, 'short');
        if (!$relations) {
            $relations = $this->Relation->find('all', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Relation.project_id' => $projectId,
                  )
            ));
            Cache::write('relations-project-id-' . $projectId, $relations, 'short');
        }
        $relationIds = Hash::extract($relations, '{n}.Relation.id');
        $annotationsInterRelations = $this->AnnotationsInterRelation->find('all', array(
              'recursive' => -1, //int
              'fields' => array('id',
                    'relation_id',
                    'AnnotationB.annotated_text',
                    'AnnotationA.annotated_text',
                    'AnnotationB.type_id',
                    'AnnotationA.type_id',
                    'AnnotationB.document_id',
                    'AnnotationA.document_id',
                    'AnnotationB.id',
                    'AnnotationA.id',
              ),
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationA',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.id = AnnotationsInterRelation.annotation_a_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationA.document_id' => $document_id,
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationB',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationB.id = AnnotationsInterRelation.annotation_b_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationB.document_id' => $document_id,
                          )
                    ),
              ),
        ));
        $AnnotatedDocument = $this->AnnotatedDocument->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'round_id' => $round_id,
                    'user_id' => $user_id,
                    'document_id' => $document_id),
              'order' => array(
                    'document_id Asc')
        ));
        if (!empty($AnnotatedDocument)) {
            if (strlen(trim($AnnotatedDocument['AnnotatedDocument']['text_marked'])) !== 0) {
                $this->set('text', $AnnotatedDocument['AnnotatedDocument']['text_marked']);
            } else {
                throw new NotFoundException(__('Invalid user_round'));
            }
        } else {
            throw new NotFoundException(__('Invalid user_round'));
        }
        $deleteCascade = Configure::read('deleteCascade');
        $this->Annotation->deleteAll(array(
              'Annotation.round_id' => $this->Round->id,
              'Annotation.user_id' => $user_id,
              'Annotation.init IS NULL',
              'Annotation.end IS NULL'
            ), $deleteCascade);
        $this->Session->write('isEnd', $isEnd);
        $triada = array(
              'user_id' => $user_id,
              'round_id' => $round_id,
              'document_id' => $document_id,
        );
        $this->Session->write('triada', $triada);
        $this->set('isReviewAutomaticAnnotation', false);
        $this->set('highlight', 0);
        $this->set('documentsMap', array());
        $this->set('operation', "");
        $this->set('document_id', $document_id);
        $this->set('document_annotated_id', $AnnotatedDocument['AnnotatedDocument']['id']);
        $this->set('documentsPerPage', 1);
        $this->set('isMultiDocument', false);
        $this->set('round_id', $round_id);
        $this->set('project_id', $projectId);
        $this->set('user_id', $user_id);
        $this->set('findMode', false);
        $this->set('types', $types);
        $this->set('relations', $relations);
        $this->set('annotationsInterRelations', $annotationsInterRelations);
        $this->set('relationsMap', Set::combine($relations, '{n}.Relation.id', '{n}.Relation'));
        $this->set('typesMap', Set::combine($types, '{n}.Type.id', '{n}.Type'));
        $this->set('nonTypes', $nonTypes);
        $this->set('isEnd', $isEnd);
        $this->set('annotationMenu', true);
        $title = "Title: " . $document['Document']['title'];
        if (isset($document['Document']['external_id'])) {
            $title = "ID: " . $document['Document']['external_id'];
        }
        $this->set('title', $title);
        $this->render("start");
    }

    public function compare($user_id_A = null, $user_id_B = null, $round_id_A = null, $round_id_B = null, $document_id = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->User = $this->Round->User;
        $this->Document = $this->Round->Project->Document;
        $this->Type = $this->Round->Type;
        if (!$this->User->exists($user_id_A)) {
            throw new NotFoundException(__('Invalid first user'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists($user_id_B)) {
            throw new NotFoundException(__('Invalid second user'));
        } //!$this->UsersRound->exists()
        if (!$this->Round->exists($round_id_A)) {
            throw new NotFoundException(__('Invalid first round'));
        } //!$this->UsersRound->exists()
        if (!$this->Round->exists($round_id_B)) {
            throw new NotFoundException(__('Invalid second round'));
        } //!$this->UsersRound->exists()
        if (!$this->Document->exists($document_id)) {
            throw new NotFoundException(__('Invalid document'));
        }
        $annotatedDocument_A = $this->AnnotatedDocument->find('first', array(
              'contain' => array(
                    'User' => array(
                          'full_name',
                          'image',
                          'image_type'),
                    'Round' => ('title'),
                    'Document' => ('title'),
              ),
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $user_id_A,
                    'round_id' => $round_id_A,
                    'document_id' => $document_id,
              ),
        ));
        $annotatedDocument_B = $this->AnnotatedDocument->find('first', array(
              'contain' => array(
                    'User' => array(
                          'full_name',
                          'image',
                          'image_type'),
                    'Round' => ('title'),
                    'Document' => ('title'),
              ),
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $user_id_B,
                    'round_id' => $round_id_B,
                    'document_id' => $document_id,
              ),
        ));
        $round_id_A = $annotatedDocument_A['AnnotatedDocument']['round_id'];
        $round_id_B = $annotatedDocument_B['AnnotatedDocument']['round_id'];
        $types = Cache::read('types-round-id-compare-' . $round_id_A . '_' . $round_id_B, 'short');
        if (!$types) {
            $types = $this->Type->find('all', array(
                  'recursive' => -1,
                  'joins' => array(
                        array(
                              'table' => 'types_rounds',
                              'alias' => 'TypesRound',
                              'type' => 'LEFT',
                              'conditions' => array(
                                    'TypesRound.type_id = Type.id',
                              ))
                  ),
                  'conditions' => array(
                        'TypesRound.round_id' => array(
                              $round_id_A,
                              $round_id_B)
                  ),
                  'group' => array(
                        'Type.id')
            ));
            Cache::write('types-round-id-compare-' . $round_id_A . '_' . $round_id_B, $types, 'short');
        }
        if (empty($types)) {
            $this->Session->setFlash(__('There are no types associated with this round'));
            $this->redirect(array(
                  'controller' => 'annotations',
                  'action' => 'listAnnotationsHits'));
        }
        if (strlen(trim($annotatedDocument_A['AnnotatedDocument']['text_marked'])) !== 0 && strlen(trim($annotatedDocument_B['AnnotatedDocument']['text_marked'])) !== 0) {
            
        } else {
            $this->Session->setFlash(__('There are no text annotated'));
            $this->redirect(array(
                  'controller' => 'annotations',
                  'action' => 'listAnnotationsHits'));
        }
        $this->set('annotatedDocument_A', $annotatedDocument_A);
        $this->set('annotatedDocument_B', $annotatedDocument_B);
        $this->set('types', $types);
        $this->set('isEnd', true);
    }

    public function tabularPerspective($round_id = null, $user_id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $types = array_diff(array_values($this->request->data["Type"]), array(
                  0));
            $this->Session->write("selectedTypes." . $round_id, $types);
            return $this->redirect($this->request->here(false));
        }
        $this->Round = $this->AnnotatedDocument->Round;
        $this->TypesRound = $this->Round->TypesRound;
        $this->Project = $this->Round->Project;
        $this->Relation = $this->Project->Relation;
        $this->User = $this->Round->User;
        $this->Document = $this->Project->Document;
        $this->Type = $this->Round->Type;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            $user_id = $this->Session->read('user_id');
            $hasAnnotatedDocument = $this->AnnotatedDocument->hasAny(array(
                  'round_id' => $round_id,
                  'user_id' => $user_id));
            if (!$hasAnnotatedDocument) {
                throw new NotFoundException(__('Invalid round'));
            }
        }
        $this->Round->id = $round_id;
        $this->User->id = $user_id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        App::uses('CakeTime', 'Utility');
        $round = $this->Round->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'Round.id' => $round_id
              )
        ));
        $isEnd = CakeTime::isPast($round['Round']['ends_in_date']);
        $count = $this->AnnotatedDocument->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id,
                    array(
                          'NOT' => array(
                                'text_marked' => NULL)
                    )
        )));
        if ($count == 0) {
            $this->Session->setFlash(__('This user has not any annotated document'));
            $this->redirect(array(
                  'controller' => 'rounds',
                  'action' => 'view',
                  $round_id
            ));
        } //!
        $data = $this->Session->read('data' . $round_id);
        if (!empty($data) && !isset($data['nonTypes'])) {
            $this->Session->delete('data' . $round_id);
            $data = null;
        }
        $types = Cache::read('types-round-' . $round_id, 'short');
        if (empty($types)) {
            $types = $this->Type->find('all', array(
                  'contain' => array(
                        'Question'),
                  'recursive' => -1,
                  'conditions' => array(
                        'TypesRound.round_id' => $round_id
                  ),
                  'joins' => array(
                        array(
                              'table' => 'types_rounds',
                              'alias' => 'TypesRound',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'Type.id = TypesRound.type_id'
                              )
                        ),
                  ),
            ));
            $types = Set::combine($types, '{n}.Type.id', '{n}.Type');
            Cache::write('types-round-' . $round_id, $types, 'short');
        }
        $typeIds = Set::classicExtract($types, '{n}.id');
        if (empty($types)) {
            $this->Session->setFlash(__('In this round there is not any annotation types'));
            $this->redirect(array(
                  'controller' => 'rounds',
                  'action' => 'view',
                  $round_id
            ));
        } //!
        $projectId = $round['Round']['project_id'];
        $nonTypes = Cache::read('non-types-round-' . $round_id, 'short');
        if (empty($nonTypes)) {
            $data['projectId'] = $projectId;
            $nonTypes = $this->Type->find('list', array(
                  'fields' => array('Type.id', 'Type.id'),
                  'recursive' => -1,
                  'conditions' => array(
                        "NOT" => array(
                              'Type.id' => $typeIds
                        ),
                        'Type.project_id' => $projectId
                  )
            ));
            Cache::write('non-types-round-' . $round_id, $types, 'short');
        }
        $documents = Cache::read('documents-project-' . $projectId, 'short');
        if (empty($documents)) {
            $documents = $this->Document->find('list', array(
                  'recursive' => -1,
                  'joins' => array(
                        array(
                              'table' => 'documents_projects',
                              'alias' => 'DocumentsProject',
                              'type' => 'Inner',
                              'conditions' => array(
                                    'DocumentsProject.document_id=Document.id'
                              )
                        ),
                        array(
                              'table' => 'annotated_documents',
                              'alias' => 'AnnotatedDocument',
                              'type' => 'Inner',
                              'conditions' => array(
                                    'AnnotatedDocument.document_id = Document.id'
                              )
                        )
                  ),
                  'conditions' => array(
                        'project_id' => $projectId,
                        array(
                              'NOT' => array(
                                    'AnnotatedDocument.text_marked' => NULL)
                        )
                  ),
                  'order' => array(
                        'id' => 'ASC'
                  )
            ));
            Cache::write('documents-project-' . $projectId, $documents, 'short');
        }
        $user = $this->User->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $user_id)
        ));
        $relations = Cache::read('relations-project-id-' . $projectId, 'short');
        if (!$relations) {
            $relations = $this->Relation->find('all', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Relation.project_id' => $projectId,
                  )
            ));
            $relations = Set::combine($relations, '{n}.Relation.id', '{n}');
            Cache::write('relations-project-id-' . $projectId, $relations, 'short');
        }
        $relationIds = array_keys($relations);
        $this->paginate = array(
              'recursive' => -1,
              'order' => array(
                    'AnnotatedDocument.document_id' => 'asc'
              ),
              'limit' => Configure::read('documentsPerPage'),
              'conditions' => array(
                    'user_id' => $user_id,
                    'round_id' => $round_id,
                    array(
                          'NOT' => array(
                                'text_marked' => NULL)
                    )
              )
        );
        $annotatedDocuments = $this->paginate();
        $this->set('annotatedDocuments', $annotatedDocuments);
        $idsDocumentsOfPage = Hash::extract($annotatedDocuments, '{n}.AnnotatedDocument.document_id');
        $selectedTypes = array();
        if ($this->Session->check("selectedTypes." . $round_id)) {
            $selectedTypes = $this->Session->read("selectedTypes." . $round_id);
        } else {
            $selectedTypes = array(182, 183, 187);
        }
        $annotationsInterRelations = $this->AnnotationsInterRelation->find('all', array(
              'recursive' => -1, //int
              'fields' => array(
                    'id',
                    'relation_id',
                    'AnnotationB.annotated_text',
                    'AnnotationA.annotated_text',
                    'AnnotationB.type_id',
                    'AnnotationA.type_id',
                    'AnnotationB.document_id',
                    'AnnotationA.document_id',
                    'AnnotationB.id',
                    'AnnotationA.id',
              ),
              'conditions' => array(
                    'AnnotationA.user_id' => $user_id,
                    'AnnotationA.round_id' => $round_id,
                    'AnnotationB.user_id' => $user_id,
                    'AnnotationB.round_id' => $round_id,
                    'AnnotationA.type_id' => $selectedTypes,
                    'AnnotationB.type_id' => $selectedTypes,
              ), //array of conditions
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationA',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.id = AnnotationsInterRelation.annotation_a_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationA.document_id' => $idsDocumentsOfPage,
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationB',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationB.id = AnnotationsInterRelation.annotation_b_id',
                                'AnnotationsInterRelation.relation_id' => $relationIds,
                                'AnnotationB.document_id' => $idsDocumentsOfPage,
                          )
                    ),
              ),
              'order' => array('AnnotationA.document_id' => 'ASC')
        ));
        $interRelationsMap = array();
        foreach ($annotationsInterRelations as $annotationsInterRelation) {
            $annotationA = $annotationsInterRelation["AnnotationA"]["id"];
            $annotationB = $annotationsInterRelation["AnnotationB"]["id"];
            $relationId = $annotationsInterRelation["AnnotationsInterRelation"]["relation_id"];
            $inteRelationId = $annotationsInterRelation["AnnotationsInterRelation"]["id"];
            $interRelationsMap[$annotationA][$annotationB]["relationId"] = $relationId;
            $interRelationsMap[$annotationA][$annotationB]["directedTo"] = $annotationB;
            $interRelationsMap[$annotationA][$annotationB]["interRelationId"] = $inteRelationId;
            $interRelationsMap[$annotationB][$annotationA]["relationId"] = $relationId;
            $interRelationsMap[$annotationB][$annotationA]["directedTo"] = $annotationB;
            $interRelationsMap[$annotationB][$annotationA]["interRelationId"] = $inteRelationId;
        }
        $this->Annotation->virtualFields['id'] = "MAX(id)";
        $annotations = $this->Annotation->find('all', array(
              'recursive' => -1, //int
              'fields' => array(
                    'id',
                    'Annotation.type_id',
                    'Annotation.annotated_text',
                    'Annotation.document_id',
                    'Annotation.section',
                    'Annotation.init',
                    'Annotation.end',
              ),
              'conditions' => array(
                    'Annotation.document_id' => $idsDocumentsOfPage,
                    'Annotation.user_id' => $user_id,
                    'Annotation.round_id' => $round_id,
                    'Annotation.type_id' => $selectedTypes,
              ), //array of conditions
              'order' => array('Annotation.document_id' => "ASC",
                    'Annotation.type_id' => "ASC", "Annotation.section" => "DESC"),
              'group' => array('annotated_text', 'type_id', 'document_id', 'section'),
        ));
        $annotations = Set::combine($annotations, '{n}.Annotation.id', '{n}.Annotation', '{n}.Annotation.document_id');
        /*  =======================================  */
        $highlight = 2;
        if (isset($round['Round']['highlight']))
            $highlight = $round['Round']['highlight'];
        $this->Session->write('round_id', $round_id);
        $this->set('selectedTypes', $selectedTypes);
        $this->set('highlight', $highlight);
        $this->set('documents', $documents);
        $this->set('findMode', false);
        $this->set('annotations', $annotations);
        $this->set('annotationsInterRelations', $annotationsInterRelations);
        $this->set('interRelationsMap', $interRelationsMap);
        $this->set('relations', $relations);
        $this->set('relationsMap', Set::combine($relations, '{n}.Relation.id', '{n}.Relation'));
        $this->set('types', $types);
        $this->set('nonTypes', $nonTypes);
        $this->set('round_id', $round_id);
        $this->set('user_id', $user_id);
        $this->set('isEnd', $isEnd);
        $this->set('project_id', $projectId);
        $this->set('fullName', $user['User']['full_name']);
        $this->set('relationalMenu', true);
    }

    public function exportAnnotatedCorpora($roundId = null, $userId = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->TypesRound = $this->Round->TypesRound;
        $this->Project = $this->Round->Project;
        $this->Relation = $this->Project->Relation;
        $this->User = $this->Round->User;
        $this->Document = $this->Project->Document;
        $this->Type = $this->Round->Type;
        $this->Annotation = $this->Round->Annotation;
        $this->User->id = $userId;
        $this->Round->id = $roundId;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->Project->exists()
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        $annotatedDocuments = $this->Annotation->find('list', array(
              'recursive' => -1, //int
              'fields' => array('document_id', 'document_id'),
              'conditions' => array('Annotation.user_id' => $userId, 'Annotation.round_id' => $roundId), //array of conditions
              'group' => array('document_id')
        ));
        $documents = $this->Document->find('all', array(
              'recursive' => -1, //int
              //array of field names
              'fields' => array('Document.external_id', 'Document.title', 'Document.html',
                    'Document.raw'),
              'conditions' => array('Document.id' => $annotatedDocuments), //array of conditions
        ));
        $tsv = "ID\tTITLE\tABSTRACT\n";
        foreach ($documents as $document) {
            $tsv .= $document["Document"]["external_id"] . "\t" .
                str_replace("â‰º", "<", $document["Document"]["raw"]) . "\n";
        }
        $mimeExtension = 'application/zip';
        $this->autoRender = false;
        $this->response->type("text/tab-separated-values");
        $this->response->body($tsv);
        $this->response->download("annotatedCorpora.tsv");
        return $this->response;
    }

    public function exportRelationsCorpora($roundId = null, $userId = null) {
        $this->Round = $this->AnnotatedDocument->Round;
        $this->TypesRound = $this->Round->TypesRound;
        $this->Project = $this->Round->Project;
        $this->Relation = $this->Project->Relation;
        $this->User = $this->Round->User;
        $this->Document = $this->Project->Document;
        $this->Type = $this->Round->Type;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        $this->User->id = $userId;
        $this->Round->id = $roundId;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->Project->exists()
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        $annotatedDocuments = $this->AnnotationsInterRelation->find('list', array(
              'recursive' => -1, //int
              'fields' => array('Annotation.document_id', 'Annotation.document_id'),
              'conditions' => array('Annotation.user_id' => $userId, 'Annotation.round_id' => $roundId), //array of conditions
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationsInterRelation.annotation_a_id = Annotation.id'
                          )
                    ),
              ),
              'group' => array('document_id')
        ));
        $documents = $this->Document->find('all', array(
              'recursive' => -1, //int
              //array of field names
              'fields' => array('Document.external_id', 'Document.title', 'Document.html',
                    'Document.raw'),
              'conditions' => array('Document.id' => $annotatedDocuments), //array of conditions
        ));
        $tsv = "ID\tTITLE\tABSTRACT\n";
        foreach ($documents as $document) {
            $tsv .= $document["Document"]["external_id"] . "\t" .
                str_replace("â‰º", "<", $document["Document"]["raw"]) . "\n";
        }
        $mimeExtension = 'application/zip';
        $this->autoRender = false;
        $this->response->type("text/tab-separated-values");
        $this->response->body($tsv);
        $this->response->download("relationsCorpora.tsv");
        return $this->response;
    }

}
