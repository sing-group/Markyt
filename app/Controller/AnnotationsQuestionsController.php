<?php

App::uses('AppController', 'Controller');

/**
 * AnnotationsQuestions Controller
 *
 * @property AnnotationsQuestion $AnnotationsQuestion
 */
class AnnotationsQuestionsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('view');
    }

    public function find() {
        if ($this->request->is('get')) {
            $this->Annotation = $this->AnnotationsQuestion->Annotation;
            $this->Document = $this->Annotation->Document;
            $documents = array();
            $type_id = $this->request->query['type_id'];
            $query = $this->request->query['query'];
            $triada = $this->Session->read('triada');
            $only_review = $this->request->query['only_review'];
            $conditions = array(
                  'user_id' => $triada['user_id'],
                  'round_id' => $triada['round_id'],
                  'UPPER(annotated_text) LIKE' => "%" . strtoupper(trim($query)) . "%",
            );
            if (isset($type_id) && $type_id != -1) {
                $conditions['type_id'] = $type_id;
            }
            if ($only_review) {
                $conditions['mode'] = 2;
            }
            $this->Annotation->virtualFields['total'] = 'COUNT(Annotation.id)';
            $annotations = $this->Annotation->find('all', array(
                  'recursive' => -1, //int
                  'fields' => array('Annotation.id', 'Annotation.document_id'),
                  'conditions' => $conditions, //array of conditions
                  'order' => array('Annotation.document_id ASC'),
            ));
            return $this->correctResponseJson(json_encode(array(
                      'success' => true,
                      'annotations' => $annotations,
                      'query' => $query,
                      'ocurrences' => count($annotations),
            )));
        }
    }

    public function view($id = null) {
        $this->Question = $this->AnnotationsQuestion->Question;
        $this->Type = $this->Question->Type;
        $this->Participant = $this->AnnotationsQuestion->Annotation->Round->Project->Participant;
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->GoldenProject = $this->Participant->GoldenProject;
        //si la anotacion no existe
        if (!isset($id)) {
            $id = $this->request->query['type_id'];
            $this->Type->id = $id;
            if (!$this->Type->exists()) {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "This type doesnt exist")));
            } else {
                $questions = $this->Question->find('all', array(
                      'recursive' => -1,
                      'contain' => array(
                            'Question'),
                      'fields' => array(
                            'id as question_id',
                            'question'),
                      'order' => 'id ASC',
                      'conditions' => array(
                            "type_id" => $id,
                      )
                ));
                $lastAnnotation = $this->getTypeOfWord($this->request->query['text']);
                return $this->correctResponseJson(json_encode(array(
                          'success' => true,
                          'AnnotationsQuestion' => $questions,
                          'lastAnnotation' => $lastAnnotation)));
            }
        } else {
            //si la anotacion existe
            $group_id = $this->Session->read('group_id');
            $conditions = array(
                  'id' => $id);
            $openAnnotatedDocs = Configure::read('openAnnotatedDocs');
            if (isset($group_id)) {
                if ($group_id > 1) {
                    $triada = $this->Session->read('triada');
                    $conditions = array(
                          'id' => $id,
                          'user_id' => $triada['user_id'],
                          'round_id' => $triada['round_id'],
                          'document_id' => $triada['document_id'],
                    );
                } else {
                    $conditions = array(
                          'id' => $id,
                    );
                }
                $documentsPerPage = Configure::read('documentsPerPage');
                if ($documentsPerPage > 1) {
                    $conditions['document_id'] = $this->request->query['document_id'];
                }
            } else if ($openAnnotatedDocs) {
                //poder ver respuestas en la parte publica
                $goldenRounds = $this->GoldenProject->find('list', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id',
                            'round_id'),
                ));
                $goldenUsers = $this->GoldenProject->find('list', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id',
                            'user_id'),
                ));
                if (!empty($goldenUsers) && !empty($goldenRounds)) {
                    $conditions = array(
                          'id' => $id,
                          'user_id' => $goldenUsers,
                          'round_id' => $goldenRounds,
                    );
                } else {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "This annotation doesnt exist in database")));
                }
            } else {
                //si no esta resgistrado y no es un round abierto ¿Qué hace aquí?
                exit();
            }
            if (!$this->Annotation->hasAny($conditions)) {
                //do something
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "This annotation doesnt exist in database")));
            } else {
                $typeId = $this->request->query['type_id'];
                $this->Type->id = $typeId;
                if (!$this->Type->exists()) {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "Type $typeId doesnt exist")));
                }
                $answers = $this->AnnotationsQuestion->find('all', array(
                      'recursive' => -1,
                      'contain' => array(
                            'Question'),
                      'fields' => array(
                            'id',
                            'Question.id as question_id',
                            'AnnotationsQuestion.answer',
                            'Question.question'
                      ),
                      'order' => 'AnnotationsQuestion.question_id ASC',
                      'conditions' => array(
                            'OR' => array(
                                  "AnnotationsQuestion.annotation_id" => $id,
                            ),
                      )
                ));
                $questionList = Hash::combine($answers, '{n}.Question.id', '{n}.Question.id');
                $answers = Hash::combine($answers, '{n}.Question.id', '{n}');
                $questions = $this->Question->find('all', array(
                      'recursive' => -1,
                      'fields' => array(
                            'id as question_id',
                            'question'),
                      'order' => 'id ASC',
                      'conditions' => array(
                            'NOT' => array(
                                  'id' => $questionList),
                            "type_id" => $typeId,
                      )
                ));
                for ($index = 0; $index < count($questions); $index++) {
                    $questions[$index]['AnnotationsQuestion'] = array();
                }
                $questions = Hash::combine($questions, '{n}.Question.question_id', '{n}');
                $answers = Hash::merge($answers, $questions);
                return $this->correctResponseJson(json_encode(array(
                          'success' => true,
                          'AnnotationsQuestion' => $answers,
                          'lastAnnotation' => array())));
            }
        }
    }

    /**
     * add method
     * @return void
     */
    public function addAnnotation() {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->Round = $this->Annotation->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->Type = $this->Annotation->Type;
        $this->Document = $this->Annotation->Document;
        $this->User = $this->Annotation->User;
        if ($this->request->is('post') || $this->request->is('put')) {
            //variable que contiene User.round.Document.user_round_id
            $triada = $this->Session->read('triada');
            $modes = Configure::read('annotationsModes');
            $newAnnotation = array(
                  'annotated_text' => $this->request->data['text'],
                  'document_id' => $triada['document_id'],
                  'type_id' => $this->request->data['type_id'],
                  'round_id' => $triada['round_id'],
                  'user_id' => $triada['user_id'],
                  'mode' => $modes["MANUAL"],
            );
            $documentsPerPage = Configure::read('documentsPerPage');
            if (isset($documentsPerPage) && $documentsPerPage > 1) {
                if (!isset($this->request->data['document_id'])) {
                    $this->request->data['document_id'] = -1;
                }
                $newAnnotation['document_id'] = $this->request->data['document_id'];
                $triada['document_id'] = $this->request->data['document_id'];
                $conditions = array(
                      'id' => $this->request->data['document_annotated_id'],
                      'document_id' => $this->request->data['document_id'],
                      'user_id' => $triada['user_id'],
                      'round_id' => $triada['round_id']);
                if (!isset($this->request->data['document_annotated_id']) && !isset($this->request->data['document_id'])) {
                    $this->log("[ANNOTATION] Request data not received in annotation mode " .
                        ' document_annotated_id =>' . $this->request->data['document_annotated_id'] .
                        ', document_id =>' . $this->request->data['document_id']
                    );
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "Ops! This annotation could not be saved. Request data not received "
                    )));
                }
                if (!$this->AnnotatedDocument->hasAny($conditions)) {
                    $this->log("[ANNOTATION]  404 Annotatd document not found in annotation mode " .
                        ' id =>' . $this->request->data['document_annotated_id'] .
                        ', document_id =>' . $this->request->data['document_id'] .
                        ', user_id =>' . $triada['user_id'] .
                        ', round_id=>' . $triada['round_id']);
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "Ops! This annotation could not be saved. 404 Annotatd document not found"
                    )));
                }
            } else {
                $conditions = array(
                      'id' => $this->request->data['document_annotated_id'],
                      'document_id' => $triada['document_id'],
                      'round_id' => $triada['round_id'],
                      'user_id' => $triada['user_id'],
                );
            }
            if (isset($this->request->data['section']) && $this->request->data['section'] !== '') {
                $newAnnotation['section'] = $this->request->data['section'];
            } else {
                $newAnnotation['section'] = null;
            }
            $this->Type->id = $this->request->data['type_id'];
            $this->Round->id = $triada['round_id'];
            $this->Document->id = $triada['document_id'];
            $this->User->id = $triada['user_id'];
            if ($this->Type->exists() && $this->Round->exists() && $this->Document->exists() && $this->User->exists()) {
                $this->Annotation->annotated_text = $this->request->data['text'];
                $lastAnnotation = $this->getTypeOfWord($this->request->data['text']);
                if ($this->Annotation->save($newAnnotation)) {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => true,
                              'id' => $this->Annotation->id,
                              'lastAnnotation' => $lastAnnotation,)));
                }
            }
            $this->log("[ANNOTATION] annotation could not be saved. " .
                ' Type =>' . $this->Type->exists() .
                ', Round =>' . $this->Round->exists() .
                ', Document =>' . $this->Document->exists() .
                ', User =>' . $this->User->exists()
            );
            return $this->correctResponseJson(json_encode(array(
                      'success' => false,
                      'message' => "This annotation could not be saved. On save error")));
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "GET???")));
    }

    /*
     * Guarda las preguntas de una anotacion
     * en este punto la anotacion ya esta creada 
     * */

    public function save() {
        $isEnd = $this->Session->read('isEnd');
        if (!$isEnd) {
            if ($this->request->is('post')) {
                $this->request->data['answers'];
                $answers = json_decode($this->request->data['answers'], true);
                $isEnableAnswerPropagation = Configure::read('enableAnswerPropagation');
                if ($isEnableAnswerPropagation) {
                    App::uses('Sanitize', 'Utility');
                    $answersCopy = array();
                    foreach ($answers as $answer) {
                        $question_id = $answer["question_id"];
                        $answersCopy[$question_id] = $answer["answer"];
                    }
                    $annotation_id = $this->request->data["annotation_id"];
                    $triada = $this->Session->read('triada');
                    $this->Annotation = $this->AnnotationsQuestion->Annotation;
                    $db = $this->AnnotationsQuestion->getDataSource();
                    $alias = $this->Annotation->table . "_A";
                    $this->Annotation->id = $annotation_id;
                    $annotated_text = $this->Annotation->field('annotated_text');
                    $options = array(
                          'fields' => array(
                                "$alias.id"
                          ),
                          'table' => $this->Annotation->table,
                          'alias' => $alias,
                          'conditions' => array(
                                "$alias.annotated_text" => $annotated_text,
                                "$alias.round_id" => $triada['round_id'],
                                "$alias.user_id" => $triada['user_id'],),
                    );
                    $subQuery = $db->buildStatement($options, $this->Annotation);
                    $ids = $this->Annotation->find('list', array(
                          'fields' => array(
                                "id", "id"
                          ),
                          'recursive' => -1,
                          'conditions' => array(
                                "annotated_text" => $annotated_text,
                                "round_id" => $triada['round_id'],
                                "user_id" => $triada['user_id'],
                          )
                    ));
                    $annotationsWithAnswers = $this->AnnotationsQuestion->find('list', array(
                          'fields' => array(
                                "annotation_id", "annotation_id"
                          ),
                          'recursive' => -1,
                          'conditions' => array(
                                "annotation_id IN ($subQuery)"
                          )
                    ));
                    $annotationsWithoutAnswers = array_diff($ids, $annotationsWithAnswers);
                    $db->begin();
                    $this->AnnotationsQuestion->recursive = -1;
                    $size = count($annotationsWithoutAnswers);
                    $insert = "INSERT INTO " . $this->AnnotationsQuestion->table . " (annotation_id, question_id, answer) VALUES ";
                    foreach ($answersCopy as $question_id => $answer) {
                        if (!empty($annotationsWithAnswers) && !$this->AnnotationsQuestion->updateAll(array(
                                  "answer" => "'" . Sanitize::paranoid($answer) . "'"), array(
                                  "annotation_id" => $annotationsWithAnswers, "question_id" => $question_id))) {
                            $db->rollback();
                            $this->log("[ANNOTATION] Update all answers error ");
                            return $this->correctResponseJson(json_encode(array(
                                      'success' => false,
                                      'message' => "Ops! Annotation could not be saved. [Update all answers error]")));
                        }
                        if ($size > 0) {
                            foreach ($annotationsWithoutAnswers as $id) {
                                $insert .= "(" . $id . "," . $question_id . ",'" . Sanitize::paranoid($answer) . "'),";
                            }
                        }
                    }
                    if ($size > 0) {
                        $insert = substr($insert, 0, -1) . ";";
                        $result = $this->AnnotationsQuestion->query($insert);
                        if ($result === false) {
                            $db->rollback();
                            $this->log("[ANNOTATION] Annotation could not be saved. [insert all answers fail");
                            return $this->correctResponseJson(json_encode(array(
                                      'success' => false,
                                      'message' => "Ops! Annotation could not be saved. [insert all answers error]")));
                        }
                    }
                    $db->commit();
                    return $this->correctResponseJson(json_encode(array(
                              'success' => true)));
                }
                if (!empty($answers)) {
                    if ($this->AnnotationsQuestion->saveAll($answers)) {
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => true)));
                    } else {
                        $this->log("[ANNOTATION] Annotation could not be saved. [insert answers (simple) error]");
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "Ops! Annotation could not be saved. [insert answers (simple) error]")));
                    }
                } else {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => true)));
                }
                //devolvemos el id de la nueva anotacion
            }
        }
    }

    function questionsAnswersView($id = null) {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->Question = $this->Annotation->Question;
        $this->Annotation->id = $id;
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $data = $this->Session->read('confrontationPostedData');
            $this->Annotation->id = $id;
            if ($this->Annotation->exists()) {
                $user_id = $this->Session->read('user_id');
                $group_id = $this->Session->read('group_id');
                $annotation = $this->Annotation->find('first', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'Annotation.id' => $id
                      )
                ));
                if ($group_id != 1 && $annotation['Annotation']['user_id'] != $user_id) {
                    $this->Session->setFlash(__('Do not try to spy on the responses of other scorers. Not a good practice'));
                    $redirect = $this->Session->read('redirect');
                    $this->redirect($redirect);
                }
                $AnnotationsQuestions = $this->AnnotationsQuestion->find('all', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'AnnotationsQuestion.annotation_id' => $id
                      )
                ));
                $question_id = array();
                $question_answer = array();
                foreach ($AnnotationsQuestions as $AnnotationsQuestion) {
                    array_push($question_id, $AnnotationsQuestion['AnnotationsQuestion']['question_id']);
                    $question_answer[$AnnotationsQuestion['AnnotationsQuestion']['question_id']] = $AnnotationsQuestion['AnnotationsQuestion']['answer'];
                }
                $questions = $this->Question->find('all', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'Question.id' => $question_id
                      )
                ));
                $this->set('questions', $questions);
                $this->set('question_answer', $question_answer);
                $this->set('annotation', $annotation);
                $this->set('project_id', $data['Project']['id']);
            } else {
                $this->Session->setFlash(__('This annotation does not exist or it was deleted'));
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            }
        }
    }

    function updateSection($id = null) {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $debug = Configure::read('debug');
        if ($debug == 2) {
            $id = $this->request->data['id'];
            $this->Annotation->id = $id;
            if (!$this->request->is('post')) {
                throw new MethodNotAllowedException();
            } else {
                $this->Annotation->id = $id;
                if ($this->Annotation->exists()) {
                    $user_id = $this->Session->read('user_id');
                    $group_id = $this->Session->read('group_id');
                    $this->Annotation->saveField('section', trim($this->request->data['section']));
                    return $this->correctResponseJson(json_encode(array(
                              'success' => true)));
                }
            }
        }
    }

    /**
     * ajaxMultiAdd method
     * @return void
     * @deprecated
     */
    public function ajaxMultiAdd() {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->Question = $this->Annotation->Question;
        //no mostrar vista
        $this->autoRender = false;
        $isEnd = $this->Session->read('isEnd');
        $commit = true;
        $annotationIds = array();
        if (!$isEnd) {
            if ($this->request->is('post')) {
                //variable que contiene User.round.Document.user_round_id
                $triada = $this->Session->read('triada');
                $answers = $this->request->data['answers'];
                $newAnnotation = array(
                      'annotated_text' => $this->request->data['text'],
                      'document_id' => $this->request->data['document_id'],
                      'users_round_id' => $triada['users_round_id'],
                      'type_id' => $this->request->data['type_id'],
                      'round_id' => $triada['round_id'],
                      'user_id' => $triada['user_id']
                );
                $actualNumQuestions = $this->Question->find('count', array(
                      'recursive' => -1,
                      'conditions' => array(
                            "Question.type_id" => $this->request->data['type_id']
                      )
                ));
                if ($this->request->data['numberOfQuestions'] != $actualNumQuestions) {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "alert questions")));
                    //se produce cuando el numero de preguntas es distinto al numero de respuestas
                    //generalmente cuando se borra o se crea una pregunta mientras estas en la ejecucion de un round
                } else {
                    $db = $this->AnnotationsQuestion->getDataSource();
                    $db->begin();
                    //si se produce un error en alguna parte se producira un reload de la pagina y la anotacion sera borada al no tener
                    //inicio ni final
                    $numberOfAnnotations = $this->request->data['numberOfAnnotations'];
                    for ($index = 0; $index < $numberOfAnnotations; $index++) {
                        $this->Annotation->create();
                        $commit = $commit & $this->Annotation->save($newAnnotation);
                        array_push($annotationIds, $this->Annotation->getLastInsertID());
                    }
                    if ($answers != 'empty') {
                        foreach ($annotationIds as $id) {
                            $answersCopy = str_replace('#M#', $id, $answers);
                            $answersCopy = json_decode($answersCopy, true);
                            //substituimos todos los identificadores de Id_annotation del patron introducido
                            //al id_annotation real,antes de json_decode
                            $commit = $commit & $this->AnnotationsQuestion->saveAll($answersCopy);
                            //throw new Exception;
                            if (!empty($answers) && !$commit) {
                                return $this->correctResponseJson(json_encode(array(
                                          'success' => false,
                                          'message' => "ErrorSave. Because it has one error in Answer save ")));
                                break;
                            }
                        }
                    }
                    if ($commit) {
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'annotation_ids' => $annotationIds)));
                        $db->commit();
                    } else {
                        $db->rollback();
                    }
                    //devolvemos el id de la nueva anotacion
                }
            } else {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "Error in Post Marky ")));
            }
        }
    }

    public function multiAdd() {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->Question = $this->Annotation->Question;
        $this->Round = $this->Annotation->Round;
        $this->Type = $this->Annotation->Type;
        $this->User = $this->Annotation->User;
        $this->UsersRound = $this->Round->UsersRound;
        $this->Document = $this->UsersRound->Document;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        //no mostrar vista
        $isEnd = $this->Session->read('isEnd');
        $commit = true;
        $annotationIds = array();
        $annotationIdsMap = array(
              'A' => array(),
              'T' => array());
        $isMultiDocument = Configure::read('documentsPerPage') > 1;
        if (!$isEnd) {
            if ($this->request->is('post') || $this->request->is('put')) {
                //variable que contiene User.round.Document.user_round_id
                $triada = $this->Session->read('triada');
                $newAnnotation = array(
                      'annotated_text' => $this->request->data['text'],
                      'type_id' => $this->request->data['type_id'],
                      'round_id' => $triada['round_id'],
                      'user_id' => $triada['user_id'],
                );
                $this->Type->id = $this->request->data['type_id'];
                $this->Round->id = $triada['round_id'];
                $this->User->id = $triada['user_id'];
                if ($isMultiDocument || $this->Type->exists() && $this->Round->exists() && $this->UsersRound->exists() && $this->User->exists()) {
                    $db = $this->AnnotationsQuestion->getDataSource();
                    $db->begin();
                    //si se produce un error en alguna parte se producira un reload de la pagina y la anotacion sera borada al no tener
                    //inicio ni final
                    $numberOfAnnotations = $this->request->data['numberOfAnnotations'];
                    if (isset($this->request->data['annotationsMap'])) {
                        $anntationsMap = $this->request->data['annotationsMap'];
                        $usersRounds = array();
                        if ($isMultiDocument) {
                            $annotationIdsMap = array();
                            $total = sizeof($anntationsMap);
                            for ($index = 0; $index < $total; $index++) {
                                $this->AnnotationsQuestion->Annotation->create();
                                if (isset($anntationsMap[$index]['section'])) {
                                    $newAnnotation['section'] = $anntationsMap[$index]['section'];
                                }
                                $newAnnotation['document_id'] = $anntationsMap[$index]['document_id'];
                                $conditions = array(
                                      'id' => $anntationsMap[$index]['document_annotated_id'],
                                      'document_id' => $newAnnotation['document_id'],
                                      'user_id' => $newAnnotation['user_id'],
                                      'round_id' => $newAnnotation['round_id']);
                                if (!$this->AnnotatedDocument->hasAny($conditions)) {
                                    $this->log("[ANNOTATION Multiannotation] This Annotated Document doesnt exist in database " .
                                        ' id =>' . $this->request->data['document_annotated_id'] .
                                        ', document_id =>' . $this->request->data['document_id'] .
                                        ', user_id =>' . $triada['user_id'] .
                                        ', round_id=>' . $triada['round_id']);
                                    return $this->correctResponseJson(json_encode(array(
                                              'success' => false,
                                              'message' => "Ops! This annotation could not be saved. Multiannotation 404, annotated document error " . json_encode($conditions))));
                                }
                                $commit = $commit & $this->Annotation->save($newAnnotation);
                                array_push($annotationIds, $this->Annotation->id);
                                array_push($annotationIdsMap, array(
                                      'document_id' => $this->Annotation->field('document_id'),
                                      'id' => $this->AnnotationsQuestion->Annotation->id));
                            }
                        } else {
                            $total = $anntationsMap['T'];
                            for ($index = 0; $index < $total; $index++) {
                                $this->Annotation->create();
                                $newAnnotation['section'] = 'T';
                                $commit = $commit & $this->Annotation->save($newAnnotation);
                                array_push($annotationIds, $this->Annotation->id);
                                array_push($annotationIdsMap['T'], $this->Annotation->id);
                            }
                            $total = $anntationsMap['A'];
                            for ($index = 0; $index < $total; $index++) {
                                $this->Annotation->create();
                                $newAnnotation['section'] = 'A';
                                $commit = $commit & $this->Annotation->save($newAnnotation);
                                array_push($annotationIds, $this->Annotation->id);
                                array_push($annotationIdsMap['A'], $this->Annotation->getLastInsertID());
                            }
                        }
                    } else {
                        for ($index = 0; $index < $numberOfAnnotations; $index++) {
                            $this->Annotation->create();
                            $commit = $commit & $this->Annotation->save($newAnnotation);
                            array_push($annotationIds, $this->Annotation->id);
                        }
                    }
                    if (!empty($this->request->data['answers'])) {
                        $answers = $this->request->data['answers'];
                        $answersSize = count($answers);
                        foreach ($annotationIds as $id) {
                            for ($index = 0; $index < $answersSize; $index++) {
                                $answers[$index]['annotation_id'] = $id;
                            }
                            $commit = $commit & $this->AnnotationsQuestion->saveAll($answers);
                            if (!empty($answers) && !$commit) {
                                return $this->correctResponseJson(json_encode(array(
                                          'success' => false,
                                          'message' => "Ops! This annotation could not be saved.")));
                            }
                        }
                    }
                    if ($commit) {
                        $db->commit();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => true,
                                  'annotation_ids' => $annotationIds,
                                  'annotationIdsMap' => $annotationIdsMap)));
                    } else {
                        $db->rollback();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "Ops! This annotation could not be saved. No commit")));
                    }
                } else {
                    return $this->correctResponseJson(json_encode(array(
                              'success' => false,
                              'message' => "Ops! This annotation could not be saved")));
                }
            }
        }
    }

    function getPrediction() {
        $isEnd = $this->Session->read('isEnd');
        $triada = $this->Session->read('triada');
        if (!$isEnd) {
            if ($this->request->is('get') && $this->request->is('ajax')) {
                $query = "";
                if (strlen($this->request->query['q']) > 2) {
                    $query = $this->request->query['q'];
                }
                $conditions = array(
                      'upper(answer) LIKE' => '%' . strtolower($query) . '%',
                      'user_id' => $triada['user_id'],
                      'round_id' => $triada['round_id'],
                      'annotated_text' => $this->request->query['selectedText']
                );
                $answers = $this->AnnotationsQuestion->find('list', array(
                      'recursive' => -1,
                      'conditions' => $conditions,
                      'contain' => 'Annotation',
                      'fields' => array(
                            'AnnotationsQuestion.id',
                            'AnnotationsQuestion.answer'),
                      'group' => 'answer',
                      'order' => 'answer ASC'
                ));
                return $this->correctResponseJson(json_encode(array(
                          'lastAnswers' => array_values($answers),
                          'success' => true)));
            }
        }
    }

    function getTypeOfSelection() {
        $isEnd = $this->Session->read('isEnd');
        if (!$isEnd) {
            if ($this->request->is('get') && $this->request->is('ajax')) {
                $lastAnnotation = $this->getTypeOfWord($this->request->query['selectedText']);
                return $this->correctResponseJson(json_encode(array(
                          'lastAnnotation' => $lastAnnotation,
                          'success' => true)));
            }
        }
    }

    private function getTypeOfWord($selectedText) {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $triada = $this->Session->read('triada');
        if (strlen($selectedText) < 2) {
            return array();
        } else {
            $conditions = array(
                  'upper(annotated_text) LIKE' => strtolower($selectedText),
                  'user_id' => $triada['user_id'],
                  'init is NOT NULL',
                  'end is NOT NULL',
                  'round_id' => $triada['round_id'],
            );
            $answers = $this->Annotation->find('list', array(
                  'recursive' => -1,
                  'conditions' => $conditions,
                  'fields' => array(
                        'Annotation.type_id',
                        'Annotation.type_id'),
                  'group' => array(
                        'Annotation.type_id'),
            ));
            return array_keys($answers);
        }
    }

    function changeType() {
        $this->Annotation = $this->AnnotationsQuestion->Annotation;
        $this->Round = $this->Annotation->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        if ($this->request->is('post') || $this->request->is('put')) {
            //variable que contiene User.round.Document.user_round_id
            $triada = $this->Session->read('triada');
            $isEnd = $this->Session->read('isEnd');
            $this->Annotation->id = $this->request->data['id'];
            if ($this->Annotation->exists() && !$isEnd) {
                $annotation = array(
                      'id' => $this->Annotation->id,
                      'round_id' => $triada['round_id'],
                      'user_id' => $triada['user_id'],
                      'document_id' => $this->request->data['document_id']
                );
                if ($this->Annotation->hasAny($annotation)) {
                    $db = $this->UsersRound->getDataSource();
                    $db->begin();
                    $annotation['type_id'] = $this->request->data['to_type'];
                    $commit = $this->Annotation->save($annotation);
                    $conditions = array(
                          'id' => $this->request->data['annotated_document_id'],
                          'document_id' => $this->request->data['document_id'],
                          'user_id' => $triada['user_id'],
                          'round_id' => $triada['round_id'],
                    );
                    if ($commit)
                        $commit = $commit && $this->AnnotatedDocument->hasAny($conditions);
                    if ($commit) {
                        $this->AnnotatedDocument->id = $this->request->data['annotated_document_id'];
                        $commit = $commit && $this->AnnotatedDocument->save(array(
                                  'text_marked' => trim($this->request->data['html'])));
                    }
                    if ($commit) {
                        $db->commit();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => true)));
                    } else {
                        $db->rollback();
                    }
                }
            }
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "This type doesnt changed")));
    }

    function javaAction() {
        $triada = $this->Session->read('triada');
        $isEnd = $this->Session->read('isEnd');
        $this->Job = $this->AnnotationsQuestion->Annotation->User->Job;
        $this->Round = $this->AnnotationsQuestion->Annotation->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $enableJavaActions = Configure::read('enableJavaActions');
        if ($this->request->is('post') && $enableJavaActions) {
            $this->UsersRound->id = $triada['users_round_id'];
            $operation = "";
            if (isset($this->request->data['operation'])) {
                $operation = $this->request->data['operation'];
                if ($operation == -1) {
                    $id = $this->request->data['job_id'];
                    return $this->killJob($id);
                } else {
                    if (!$isEnd && $this->UsersRound->field('state') == 0) {
                        $user_id = $this->Session->read('user_id');
                        $round_id = $triada['round_id'];
                        $this->Job->create();
                        $data = array('user_id' => $user_id, 'round_id' => $round_id,
                              'percentage' => 0,
                              'status' => 'Starting...');
                        if ($this->Job->save($data)) {
                            $id = $this->Job->id;
                            switch ($operation) {
                                // Kill process
                                case -1:
                                    break;
                                // Cambiar tipo de palabras ya anotadas
                                case "changeType":
                                    $programName = "Change type";
                                    $operationId = 1;
                                    $type_id = $this->request->data['newType'];
                                    $term = $this->request->data['term'];
                                    $term = "\"$term\"";
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$term\t$type_id";
                                    break;
                                // Borrar tipo de palabras ya anotadas
                                case "deleteAllTerms":
                                    $programName = "Delete terms";
                                    $operationId = 2;
                                    $term = $this->request->data['term'];
                                    $term = "\"$term\"";
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$term";
                                    break;
                                // Borrar un tipo que este siendo utilizado
                                case "deleteAlltypes":
                                    $programName = "Delete type";
                                    $operationId = 3;
                                    $type_id = $this->request->data['type_id'];
                                    $term = $this->request->data['term'];
                                    $term = "\"$term\"";
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$type_id";
                                    break;
                                // Borrar un tipo de un termino especificado
                                case "deleteAllTermWithType":
                                    $programName = "Delete term with id";
                                    $operationId = 4;
                                    $type_id = $this->request->data['type_id'];
                                    $term = $this->request->data['term'];
                                    $term = "\"$term\"";
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$term\t$type_id";
                                    break;
                                case "automaticAnnotateTerm":
                                    $programName = "Automatic annotate term";
                                    $operationId = 5;
                                    $type_id = $this->request->data['type_id'];
                                    $term = $this->request->data['term'];
                                    $term = "\"$term\"";
                                    if (isset($this->request->data['answers'])) {
                                        $answers = json_encode($answers = $this->request->data['answers']);
                                    } else {
                                        $answers = "[]";
                                    }
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$term\t$type_id\t$answers";
                                    break;
                                case "automaticAnnotation":
                                    //In round Controller!!
                                    break;
                                default:
                                    return $this->correctResponseJson(json_encode(array(
                                              'success' => false,
                                              'message' => "Undefined operation")));
                                    break;
                            }
                            return $this->sendJob($id, $programName, $arguments);
                        }
                    }
                }
            } else {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "The task could not be performed successfully. Other operation is in progress. ")));
            }
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "The task could not be performed successfully ")));
    }

    function getJavaState() {
        $this->Job = $this->AnnotationsQuestion->Annotation->User->Job;
        if ($this->request->is('post') || $this->request->is('put')) {
            $user_id = $this->Session->read('user_id');
            $this->Job->create();
            $this->Job->id = $this->request->data['id'];
            $percent = $this->Job->field('percentage');
            $percent = round($percent, 2);
            $message = null;
            $action = null;
            if ($percent == 100) {
                $json = $this->Job->field('comments');
                $json = json_decode($json, true);
                if (isset($json["totalNewAnnotations"])) {
                    $message = $json["totalNewAnnotations"] . " terms annotated!";
                    $action = "create";
                }
                if (isset($json["totalDeleteAnnotations"])) {
                    $message = $json["totalDeleteAnnotations"] . " terms removed!";
                    $action = "remove";
                }
            }
            return $this->correctResponseJson(json_encode(array(
                      'success' => true,
                      'percent' => $percent,
                      'message' => $message,
                      'action' => $action,
            )));
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "The task could not be performed successfully")));
    }

}
