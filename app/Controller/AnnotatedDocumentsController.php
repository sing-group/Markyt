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
//        $openAnnotatedDocs = Configure::read('openAnnotatedDocs');
//        if (!empty($openAnnotatedDocs)) {
        $this->Auth->allow('publicView');
        $this->Auth->allow('changeNabPosition');
//        }
    }

    /**
     * index method
     * @throws NotFoundException
     * @return void
     */
    public function fixDocumentID() {
        $db = $this->AnnotatedDocument->getDataSource();
        $db->begin();

        $this->Document = $this->AnnotatedDocument->Document;
        $this->DocumentsProject = $this->Document->DocumentsProject;
        $this->Document->virtualFields['TOTAL'] = "COUNT(id)";
//        debug($this->AnnotatedDocument->find('count', array(
//                    'recursive' => -1,
//        )));
        $all = $this->Document->find('all', array(
            'fields' => array('TOTAL', 'external_id', 'id'),
            'recursive' => -1,
            'group' => 'external_id',
            'order' => array('TOTAL DESC'),
        ));


        $repeated = array();
        foreach ($all as $document) {
            if ($document['Document']['TOTAL'] == 1) {
                break;
            }
            array_push($repeated, $document['Document']['external_id']);
        }

        $ids = $this->Document->find('all', array(
            'fields' => array('id', 'external_id'),
            'recursive' => -1,
            'conditions' => array('external_id' => $repeated),
            'order' => array('external_id DESC', 'id DESC'),
        ));

        $last = "";
        $currentId = "";
        $final = array();
        foreach ($ids as $document) {
            if ($document['Document']['external_id'] != $last) {
                $last = $document['Document']['external_id'];
                $currentId = $document['Document']['id'];
                $final[$currentId] = array();
            }
            if ($currentId != $document['Document']['id'])
                array_push($final[$currentId], $document['Document']['id']);
        }

        $this->AnnotatedDocument->recursive = -1;
        foreach ($final as $id => $find) {
            $this->AnnotatedDocument->updateAll(
                    array('document_id' => "$id"), array('document_id' => $find)
            );
            $this->DocumentsProject->updateAll(
                    array('document_id' => "$id"), array('document_id' => $find)
            );


            $this->Document->deleteAll(array("id" => $find), false);
        }


        $documentsProject = $this->AnnotatedDocument->find('all', array(
            'fields' => array('document_id', 'Round.project_id'),
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'rounds',
                    'alias' => 'Round',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Round.id = AnnotatedDocument.round_id'
                    )
                )
            ),
                )
        );


        foreach ($documentsProject as $document) {
            $document_id = $document["AnnotatedDocument"]["document_id"];
            $project_id = $document["Round"]["project_id"];
            if (!isset($project_id)) {
                throw new Exception;
            }
            if (!$this->DocumentsProject->hasAny(array("project_id" => $project_id, "document_id" => $document_id))) {
                $db->fetchAll("INSERT INTO `documents_projects` (`document_id`, `project_id`) VALUES (:document_id,:project_id);", array(
                    'document_id' => $document_id,
                    'project_id' => $project_id,
                ));
            }
        }
        $db->commit();

        debug($this->AnnotatedDocument->find('count', array(
                    'recursive' => -1,
        )));

        throw new Exception;
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

    public function start($round_id = null, $user_id = null, $operation = null) {

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

//        $round = Cache::read('round-id-' . $round_id, 'short');
//        if (!$round) {
        //buscamos el round para saber la fecha de finalizacion
        $round = $this->Round->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Round.id' => $round_id
            )
        ));
//            Cache::write('round-id-' . $round_id, $round, 'short');
//        }



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
        //buscamos todos los documentos del proyecto para el selector

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

//            $documentsListByRound = Set::format($setDocumentsToAnnotateByRound, '{1} - {0}', array(
//                        '{n}.Document.external_id', '{n}.Document.title'));
//
//            $setDocumentsToAnnotateByRound = Set::combine($setDocumentsToAnnotateByRound, '{n}.Document.id', '{n}.Document');
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
//                'offset' => $offset, //int
                'order' => 'document_id ASC',
            );

            $documentsProject = $this->paginate($this->DocumentsProject);


            $idsDocumentsOfPage = Hash::extract($documentsProject, '{n}.DocumentsProject.document_id');
            if (empty($idsDocumentsOfPage)) {
                $this->Session->setFlash(__('There are no documents annotated'));
                $this->redirect($redirect);
            }

            if ($group_id > 1) {
                //delete annotation
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


            $documentsAnnotatedIds = Hash::extract($annotatedDocuments, '{n}.AnnotatedDocument.document_id');
            $annotatedDocuments = Set::combine($annotatedDocuments, '{n}.AnnotatedDocument.document_id', '{n}.AnnotatedDocument');

//            $diff = array_diff($documentsAnnotatedIds, $documentsIds);

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
//
//            $documentsTitles = Cache::read('documents-titles', 'short');
//            if (!$documentsTitles) {
//                $documentsTitles = $this->UsersRound->Round->Project->Document->find('all', array(
//                    'recursive' => -1,
//                    'fields' => array('id', 'title', 'external_id'),
//                    'conditions' => array(
//                        'Document.id' => $documentsIds,
//                    )
//                ));
//                $documentsTitles = Set::combine($documentsTitles, '{n}.Document.id', '{n}.Document.external_idDocument.title');
//                Cache::write('documents-titles', $documentsTitles, 'short');
//            }

            $size = count($idsDocumentsOfPage);
            for ($i = 0; $i < $size; $i++) {
                $document_id = intval($idsDocumentsOfPage[$i]);
                if (!empty($annotatedDocuments[$document_id])) {
                    if (strlen(trim($annotatedDocuments[$document_id]['text_marked'])) == 0) {
                        $annotatedDocuments[$document_id]['text_marked'] = $documentsOfPage[$document_id]['html'];
                    }
//                    debug(strlen(trim($userRounds[$document_id]['text_marked'])));
//                    debug($documents[$document_id]['html']);
                } else if (strlen(trim($documentsOfPage[$document_id]['html'])) !== 0) {
                    $annotatedDocuments[$document_id] = array(
                        'user_id' => $user_id,
                        'round_id' => $round_id,
                        'document_id' => $document_id,
//                        'text_marked' => $documents[$document_id]['html'],
                        'annotation_minutes' => 0
                    );
                    if ($group_id > 1 && !$isEnd) {
                        if (empty($annotatedDocuments[$document_id]['html'])) {
//                        $this->AnnotatedDocument->id = $annotatedDocuments[$document_id]['id'];
                            $this->AnnotatedDocument->create();
                            if (!$this->AnnotatedDocument->save($annotatedDocuments[$document_id])) {
                                debug($annotatedDocuments);
                                debug($this->AnnotatedDocument->validationErrors);
                                $this->Session->setFlash(__('ops! error creating AnnotatedDocument '));
                                throw new Exception;
//                            $this->redirect($redirect);
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
                //delete annotation
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
                    'DocumentsProject.document_id' => 'asc'
                ),
                'conditions' => array(
                    'document_id' => array_keys($setDocumentsToAnnotateByRound)),
                'limit' => 1,
                'offset' => $offset, //int
            );

            $this->set('DocumentsProject', $this->paginate($this->DocumentsProject, array(
                        'DocumentsProject.project_id' => $projectId)));

            $title = "Title: " . $document['Document']['title'];
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }


            $this->set('title', $title);
            //variable que contiene User.round.Document.user_round_id
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


        //escribimos la variable en una variable de session puesto que nos sera util a la hora de verificar la fecha cuando se intente crear anotaciones o editarlas
        $this->Session->write('isEnd', $isEnd);

        //esta variable sera usada para constatar que no se intentan modificar dichas variables
        $this->Session->write('triada', $triada);
//        App::uses('CakeTime', 'Utility');
//        $date = CakeTime::format('+0 seconds', '%Y-%m-%d %H:M:%S');


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

        if (!$find) {
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
//            'conditions' => array('AnnotatedDocument.field' => $thisValue), //array of conditions
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
            ));
            $this->set('relationsMap', Set::combine($relations, '{n}.Relation.id', '{n}.Relation'));
            $this->set('typesMap', Set::combine($types, '{n}.Type.id', '{n}.Type'));
            $this->set('documentsMap', $documentsOfPage);
            $this->set('annotationsInterRelations', $annotationsInterRelations);
            $this->set('isReviewAutomaticAnnotation', $isReviewAutomaticAnnotation);
        }


        if (!$this->Session->check('start_step') && $group_id > 1) {
            $this->Session->write('start_step', new DateTime(''));
        }

        $this->set('document_id', $document_id);
        $this->set('findMode', $find);
        $this->set('isMultiDocument', $isMultiDocument);









        /* ================================================================================= */
        /* ==================================No Ajax================================== */
        /* ================================================================================= */
        if (!$this->request->is('ajax')) {



            if (empty($types)) {
                $this->Session->setFlash(__('There are no types associated with this round'));
                return $this->redirect($redirect);
            }


            //buscamos el primer documento del proyecto
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


//            debug($nonTypes);
//            throw new Exception;
//            debug($setDocumentsToAnnotateByRound);



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
            //lo utilizaremos para eliminar las anotaciones de un tipo eliminado
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
//            $title = "Title: " . $document['Document']['title'];
//            if (isset($document['Document']['external_id'])) {
//                $title = "ID: " . $document['Document']['external_id'];
//            }


            $this->set('title', $title);
            $this->set('isEnd', $isEnd);
            $this->render("ajax");
        }
//            $this->autoRender = false;
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

            //usado para paginacion
            if (strlen(trim($this->request->data['AnnotatedDocument']['text_marked'])) !== 0 && !$isEnd && $state == 0) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                $textoMarky = trim($this->request->data['AnnotatedDocument']['text_marked']);


                $textoForMatches = $this->parseHtmlToGetAnnotations($textoMarky);
//                $textoMarky = preg_replace('/\s+/', ' ', $textoMarky);
//                //las siguientes lineas son necesarias dado que cada navegador hace lo  que le da la gana con el DOM con respecto a la gramatica,
//                //no hay un estandar asi por ejemplo en crhome existe Style:valor y en Explorer Style :valor,etc
//                $textoForMatches = str_replace(array(
//                    "\n",
//                    "\t",
//                    "\r"
//                        ), '', $textoMarky);
//                //$textoForMatches = str_replace('> <', '><', $textoForMatches);
//                $textoForMatches = strip_tags($textoForMatches, '<mark>');
//                $textoForMatches = utf8_decode(htmlspecialchars_decode($textoForMatches));
////                debug(strlen(strip_tags($textoForMatches)));
////                
////                
                //buscamnos el comienzo por ello devemos volver a machear todos los span
                $parseKey = Configure::read('parseKey');
                $parseIdAttr = Configure::read('parseIdAttr');

                preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);

                //array donde guardaremos las nuevas anotaciones
                $allAnnotations = array();
                $db = $this->AnnotatedDocument->getDataSource();
                $db->begin();
                if (sizeof($matches[1]) != 0) {
                    $this->recursive = -1;
                    //ahora comenzaremos a guardarlas en la BD
                    $accum = 0;
                    //guarda el acumulado de los spans creados                    
                    preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][0][0], $value);
                    //se corresponde con el ultimo id inserado /[^>]*value=.?(\w*).?[^>]/
                    $insertID = $value[1];
                    $lastID = -1;
                    //se corresponde al identificador del ultimo id del final del array, sirve para saber si la ultima anotacion se ha insertado
                    $original_start = $matches[1][0][1];
                    //variable que guarda el tamanho else texto acumulado empieza en -1 debido a que match empieza en la posicion 0 y length empieza uno
                    //si esto no fuera asi tendriamos una anotacion que termina en 59 y otra que empieza en 59
                    $textAcummulate = 0;
                    $text = "";
//                   debug($matches);

                    for ($i = 0; $i < sizeof($matches[1]); $i++) {
                        preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                        if (!isset($value[1])) {
                            $db->rollback();
                            throw new Exception("Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0]);
                        }
                        //es necesario hacer esto debido a que pude darse el caso de que tengamos <mark> entre tags html anidadas con el mismo id
                        if ($insertID != $value[1]) {
                            $lastID = $insertID;
//                            debug($insertID);

                            $this->Annotation->id = $insertID;
                            if ($this->Annotation->exists() && $this->Annotation->save(array(
                                        'init' => $original_start,
                                        'end' => $original_start + $textAcummulate
                                    ))) {
                                array_push($allAnnotations, $insertID);
                            } else {
//                                throw new Exception("Annotation not exist loop: " . $lastID . "  " . $texto);
                                $this->Session->setFlash(__("This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text));
                                $db->rollback();
                                return $this->redirect(array(
                                            'controller' => 'annotatedDocuments',
                                            'action' => 'start',
                                            $triada['round_id'], $triada['user_id'],
                                            'page' => $this->request->data['AnnotatedDocument']['page']
                                ));
                            }
                            //actualizamos las variables para la proxima anotacion
                            $textAcummulate = 0;
                            $text = "";
                            $insertID = $value[1];
                            $original_start = $matches[1][$i][1] - $accum;
                        } //$insertID != $value[1]
                        $textAcummulate += strlen(strip_tags($matches[0][$i][0]));
//                        debug(strip_tags($matches[0][$i][0]));
//                        debug(strlen(strip_tags($matches[0][$i][0])));
//                        throw new Exception;
                        $text .= $matches[0][$i][0];
                        $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                    } //$i = 0; $i < sizeof($matches[1]); $i++
                    //introducimos la ultima anotacion dado que ha quedado sin introducir
                    //LENGTH (Annotation.annotated_text)-1 tamanho del texto original -1 dado que preg match empieza en 0



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
                //$textoMarky=str_ireplace("id=\"Marky","onmousedown='unHiglight(event);' id=\"Marky",$textoMarky); //for much browsers

                $this->request->data['AnnotatedDocument']['text_marked'] = $textoMarky;


                //borramos todas las anotaciones que no tengan inicio ni final
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

                    //modificamos todos los rounds dado que todos ellos forman un nico round
                    $this->updateElapsedTime($this->AnnotatedDocument->id);
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
                    //$this->redirect(array('action' => 'start', $this->UsersRound->id));
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
                        $textoMarky = trim($document['text_marked']);

                        $textoForMatches = $this->parseHtmlToGetAnnotations($textoMarky);


                        $parseKey = Configure::read('parseKey');
                        $parseIdAttr = Configure::read('parseIdAttr');
                        preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                        $allAnnotations = array();

                        if (sizeof($matches[1]) != 0) {
                            $this->recursive = -1;
                            $accum = 0;
                            preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][0][0], $value);
                            $insertID = $value[1];
                            $lastID = -1;
                            $original_start = $matches[1][0][1];
                            $textAcummulate = 0;
                            $text = "";
                            for ($i = 0; $i < sizeof($matches[1]); $i++) {
                                preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                                if (!isset($value[1])) {
                                    $db->rollback();

                                    return $this->correctResponseJson(json_encode(array(
                                                'success' => false,
                                                'message' => "Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0])));
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
                                        $db->rollback();
                                        return $this->correctResponseJson(json_encode(array(
                                                    'success' => false,
                                                    'message' => "This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text)));
                                    }
                                    $textAcummulate = 0;
                                    $text = "";
                                    $insertID = $value[1];
                                    $original_start = $matches[1][$i][1] - $accum;
                                } //$insertID != $value[1]
                                $textAcummulate += strlen(strip_tags($matches[0][$i][0]));
                                $text .= $matches[0][$i][0];
                                $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                            } //$i = 0; $i < sizeof($matches[1]); $i++
                            if ($lastID != $insertID) {
                                $this->Annotation->id = $insertID;
                                if ($this->Annotation->exists() && $this->Annotation->save(array(
                                            'init' => $original_start,
                                            'end' => $original_start + $textAcummulate
                                        ))) {

                                    array_push($allAnnotations, $insertID);
                                } else {
                                    $db->rollback();
                                    return $this->correctResponseJson(json_encode(array(
                                                'success' => false,
                                                'message' => "This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text)));
                                }
                            }
                        } //$lastID != $insertID
                    } //sizeof($matches[1]) != 0
                    $document['text_marked'] = $textoMarky;
                    $deleteCascade = Configure::read('deleteCascade');


                    $this->Annotation->deleteAll(array(
                        'not' => array(
                            'Annotation.id' => $allAnnotations
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
                        //$this->redirect(array('action' => 'start', $this->UsersRound->id));
                    }

                    $this->updateElapsedTime($annotatedDocument['id']);
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

    private function reparseAnnotations() {
        set_time_limit(false);
//        $group_id = $this->Session->read('group_id');
//        if ($group_id > 1) {
//            throw new Exception;
//        }
        $this->Round = $this->AnnotatedDocument->Round;

        $this->Annotation = $this->Round->Annotation;
        $db = $this->AnnotatedDocument->getDataSource();
        $db->begin();
        $commit = true;
        $reparseadas = 0;
        $maxDocument = $this->AnnotatedDocument->find('first', array(
            'recursive' => -1,
            'fields' => 'id',
            'order' => 'id DESC'));

        for ($index = $maxDocument["AnnotatedDocument"]["id"]; $index > 0; $index--) {
            $document = $this->AnnotatedDocument->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $index),
                'order' => 'id DESC'));


            if (!empty($document)) {
                $document = $document["AnnotatedDocument"];
                if (strlen(trim($document['text_marked'])) !== 0) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                    $textoMarky = trim($document['text_marked']);

                    $ancle = "#===#";
                    $textoMarky = str_replace("</h3>", $ancle, $textoMarky);
                    $textoForMatches = $this->parseHtmlToGetAnnotations($textoMarky);


                    $sectionLimit = strpos($textoForMatches, $ancle);
                    $textoForMatches = str_replace($ancle, "", $textoForMatches);


                    $parseKey = Configure::read('parseKey');
                    $parseIdAttr = Configure::read('parseIdAttr');
                    preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                    $allAnnotations = array();

                    if (sizeof($matches[1]) != 0) {
                        $this->recursive = -1;
                        $accum = 0;
                        preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][0][0], $value);
                        preg_match('/[^>]*data-type-id=.?(\w*).?[^>]/', $matches[1][0][0], $type);

                        $isAutomatic = strpos($matches[1][0][0], "automatic");
                        $mode = 0;
                        if ($isAutomatic) {
                            $mode = 2;
                        }


                        $insertID = $value[1];
                        $lastID = -1;
                        $lastType = $type[1];
                        $original_start = $matches[1][0][1];



                        $acumulatedTextSize = 0;
                        $acumulatedText = "";
                        for ($i = 0; $i < sizeof($matches[1]); $i++) {
                            preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                            preg_match('/[^>]*data-type-id=.?(\w*).?[^>]/', $matches[1][$i][0], $type);




                            if (!isset($value[1])) {
                                $db->rollback();

                                return debug(array(
                                    'success' => false,
                                    'message' => "Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0]));
                            }

                            if ($insertID != $value[1]) {
                                $lastID = $insertID;
                                if (!$this->Annotation->hasAny(array('id' => $insertID))) {
                                    if ($original_start > $sectionLimit) {
                                        $section = "A";
                                    } else {
                                        $section = "T";
                                    }


                                    if ($this->Annotation->save(array(
                                                'id' => $insertID,
                                                'type_id' => $lastType,
                                                'user_id' => $document["user_id"],
                                                'round_id' => $document["round_id"],
                                                'document_id' => $document["document_id"],
                                                'init' => $original_start,
                                                'end' => $original_start + $acumulatedTextSize,
                                                'mode' => $mode,
                                                'section' => $section,
                                                'annotated_text' => preg_replace('/\s(?=\S*$)/', '', $acumulatedText)
                                            ))) {


                                        $reparseadas++;
                                    } else {
                                        $db->rollback();
                                        return debug(array(
                                            'success' => false,
                                            'message' => "This Document could not be saved. Annotation $acumulatedText does not exist in database. Please, delete annotation with text:" . $acumulatedText));
                                    }
                                } else {
//                                    if(!$this->Annotation->hasAny(array('id' => $insertID,'section' => $section)))
//                                    {
//                                        throw new Exception;
//                                    }
                                }

                                $acumulatedTextSize = 0;
                                $acumulatedText = "";
                                $insertID = $value[1];
                                $original_start = $matches[1][$i][1] - $accum;
                                $lastType = $type[1];
                                $isAutomatic = strpos($matches[1][$i][0], "automatic");
                                $mode = 0;
                                if ($isAutomatic) {
                                    $mode = 2;
                                }
                            } //$insertID != $value[1]




                            $acumulatedTextSize += strlen(strip_tags($matches[0][$i][0]));
                            $acumulatedText .= $matches[2][$i][0] . " ";
                            $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                        } //$i = 0; $i < sizeof($matches[1]); $i++


                        if ($lastID != $insertID && isset($type[1])) {


                            if (!$this->Annotation->hasAny(array('id' => $insertID))) {
                                if ($original_start > $sectionLimit) {
                                    $section = "A";
                                } else {
                                    $section = "T";
                                }
                                if ($this->Annotation->save(array(
                                            'id' => $insertID,
                                            'type_id' => $type[1],
                                            'user_id' => $document["user_id"],
                                            'round_id' => $document["round_id"],
                                            'document_id' => $document["document_id"],
                                            'init' => $original_start,
                                            'end' => $original_start + $acumulatedTextSize,
                                            'mode' => $mode,
                                            'section' => $section,
                                            'annotated_text' => preg_replace('/\s(?=\S*$)/', '', $acumulatedText)
                                        ))) {
                                    $reparseadas++;
                                } else {
                                    $db->rollback();
                                    return debug(array(
                                        'success' => false,
                                        'message' => "This Document could not be saved. Annotation $acumulatedText does not exist in database. Please, delete annotation with text:" . $acumulatedText));
                                }
                            }
                        }
                    } //$lastID != $insertID
                } //sizeof($matches[1]) != 0
            }
        }
        if ($commit) {
            $this->reparseRelations();
            $db->commit();
            debug("hecho! anotaciones Arregladas: " . $reparseadas);
        } //$this->UsersRound->save($this->request->data)
        else {
            $db->rollback();
            debug("Error!");
        }
    }

    private function reparseRelations() {
        set_time_limit(false);
//        $group_id = $this->Session->read('group_id');
//        if ($group_id > 1) {
//            throw new Exception;
//        }
        $this->Round = $this->AnnotatedDocument->Round;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;


        $db = $this->AnnotatedDocument->getDataSource();
        $db->begin();
        $commit = true;
        $reparseadas = 0;
        $maxDocument = $this->AnnotatedDocument->find('first', array(
            'recursive' => -1,
            'fields' => 'id',
            'order' => 'id DESC'));

        for ($index = $maxDocument["AnnotatedDocument"]["id"]; $index > 0; $index--) {
            $document = $this->AnnotatedDocument->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $index),
                'order' => 'id DESC'));


            if (!empty($document)) {
                $document = $document["AnnotatedDocument"];
                if (strlen(trim($document['text_marked'])) !== 0) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                    $textoForMatches = $this->parseHtmlToGetAnnotations(trim($document['text_marked']));
                    $parseKey = Configure::read('parseKey');
                    $parseIdAttr = Configure::read('parseIdAttr');
                    preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                    $allAnnotations = array();

                    if (sizeof($matches[1]) != 0) {
                        $this->recursive = -1;
                        $accum = 0;
                        preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][0][0], $value);
                        $relationsToSave = array();

                        for ($i = 0; $i < sizeof($matches[1]); $i++) {
                            preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                            preg_match('/[^>]*data-annotation-relation-ids-json=.?(\[[^\]]*\]).?[^>]/', $matches[1][$i][0], $relations);
                            preg_match('/[^>]*data-relation-id=.?(\w*).?[^>]/', $matches[1][$i][0], $relationId);




                            if (!isset($value[1])) {
                                $db->rollback();
                                return debug(array(
                                    'success' => false,
                                    'message' => "Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0]));
                            }

                            if (!empty($relations))
                                if (isset($relations[1]) && strlen($relations[1]) > 0) {
                                    $relations = json_decode($relations[1]);
                                    $annotationId = $value[1];
                                    foreach ($relations as $id) {
                                        if (!$this->AnnotationsInterRelation->hasAny(array('id' => $id))) {
                                            if (!isset($relationId[1])) {
                                                preg_match('/[^>]*data-annotation-relation-id=.?(\w*).?[^>]/', $matches[1][$i][0], $relationId);
                                            }
                                            if (empty($relationsToSave[$id])) {
                                                $relationsToSave[$id] = array();
                                                $relationsToSave[$id] = array("id" => $id, "annotation_a_id" => $annotationId, "relation_id" => $relationId[1]);
                                            } else {
                                                $relationsToSave[$id]["annotation_b_id"] = $annotationId;
                                            }
                                        }
                                    }
                                }
                        }

                        if (!empty($relationsToSave)) {
                            foreach ($relationsToSave as $relation) {
                                $this->AnnotationsInterRelation->save($relation);
                                $reparseadas++;
                            }
                        }
                    }
                }
            }
        }
        if ($commit) {
            $db->commit();
            debug("hecho! Relaciones Arregladas: " . $reparseadas);
        } //$this->UsersRound->save($this->request->data)
        else {
            $db->rollback();
            debug("Error!");
        }
    }

    private function updateElapsedTime($annotatedDocumentId) {
        $this->AnnotatedDocument->recursive = -1;
        $lastDate = $this->Session->read('start_step');
        $elapsed = $lastDate->diff(new DateTime(''));
        $elapsed_seconds = $elapsed->s;
        $elapsed_minutes = round($elapsed_seconds / 60, 2);
        $this->AnnotatedDocument->id = $annotatedDocumentId;
        $databaseTime = $this->AnnotatedDocument->field('annotation_minutes');
        $total = $elapsed_minutes + $databaseTime;
        $this->AnnotatedDocument->saveField('annotation_minutes', $total);
        $this->Session->write('start_step', new DateTime(''));
    }

    /**
     * start method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($round_id = null, $user_id = null) {

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
            $typesId = $this->Round->TypesRound->find('all', array(
                'fields' => 'TypesRound.type_id',
                'conditions' => $cond,
                'recursive' => -1
            ));
            $typesId = $this->flatten($typesId);
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
//cogemos el nombre para poder borrar los tipos que ya no necesitamos o ya no estan en el round
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



//a partir de este momento, en el array, project_id pasara a tener la lista de questions de cada type
//dado que este atributo ya no es necesari
            foreach ($types as &$type):
//se modifocan las comillas simples para que no haya errores
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

//guardamos todos los datos importantes en sesion para no tener que hacer re-busquedas ineficientes
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

//$this->set('text', ));
        $this->set('types', $types);

//lo utilizaremos para eliminar las anotaciones de un tipo eliminado
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
//!empty($document)
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
//        $this->Session->write('group_id', 99);

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
//buscamos el round para saber la fecha de finalizacion
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
//                    'DocumentsProject.project_id' => $projectId,
                    'Document.external_id' => $external_id,
                ),
//                    'limit' => $limit, //int
//                    'offset' => $offset, //int
//                    'order' => array('document_id Asc')
            ));

            Cache::write('annotated_document' . $round_id . '-' . $user_id . '-' . $external_id, $document, 'short');
        }
//        throw new Exception;  

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
//buscamos el primer documento del proyecto
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
//            'conditions' => array('AnnotatedDocument.field' => $thisValue), //array of conditions
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
                //para el primer user round vacio
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
        //esta variable sera usada para constatar que no se intentan modificar dichas variables
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

//!empty($document)
    }

//    public function rate($id = null) {
////$this->autoRender = false;
//        $this->UsersRound->id = $id;
//        if (!$this->UsersRound->exists()) {
//            throw new NotFoundException(__('Invalid round'));
//        } //!$this->UsersRound->exists()
//        else {
//            $userRound = $this->UsersRound->find('first', array(
//                'fields' => array(
//                    'user_id',
//                    'document_id'),
//                'conditions' => array(
//                    'id' => $id),
//                'recursive' => -1));
//            $user_id = $this->Session->read('user_id');
//            if ($userRound['UsersRound']['user_id'] == $user_id) {
//                if ($this->UsersRound->updateAll(array(
//                            'rate' => $this->request->data['rate']), array(
//                            'user_id' => $user_id,
//                            'document_id' => $userRound['UsersRound']['document_id']))) {
//                    return $this->correctResponseJson(json_encode(array(
//                                'success' => true)));
//                }
//            }
//            return $this->correctResponseJson(json_encode(array(
//                        'success' => false)));
//        }
//    }

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

//
//        $round_A= Cache::read('round-id-' . $round_id_A, 'short');
//        if (!$round_A) {
//            //buscamos el round para saber la fecha de finalizacion
//            $round = $this->UsersRound->Round->find('first', array(
//                'recursive' => -1,
//                'conditions' => array(
//                    'Round.id' => $round_id
//                )
//            ));
//            Cache::write('round-id-' . $round_id_A, $round_A, 'short');
//        }
//
//        if (empty($round_A)) {
//            throw new NotFoundException(__('Invalid round'));
//        }
//        $projectId = $round['Round']['project_id'];



        $round_id_A = $annotatedDocument_A['AnnotatedDocument']['round_id'];
        $round_id_B = $annotatedDocument_B['AnnotatedDocument']['round_id'];

        $types = Cache::read('types-round-id-compare-' . $round_id_A . '_' . $round_id_B, 'short');
        if (!$types) {
            $types = $this->Type->find('all', array(
                'recursive' => -1,
//                'contain' => array('Question',),
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

}
