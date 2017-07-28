<?php

App::uses('AppController', 'Controller');

/**
 * UsersRounds Controller
 *
 * @property UsersRound $UsersRound
 */
class UsersRoundsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('externalView');
        $this->Auth->allow('changeNabPosition');
    }

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {
        $user_id = $this->Session->read('user_id');
        $this->Round->recursive = -1;
        $this->paginate = array(
              'fields' => array(
                    'Round.id',
                    'Round.title',
                    'Round.description',
                    'Round.ends_in_date',
                    'Project.title',
                    'Project.id',
              ),
              'contain' => array('Project')
        );
        $rounds = $this->Round->UsersRound->find('list', array(
              'recursive' => -1,
              'fields' => 'UsersRound.round_id',
              'group' => 'UsersRound.round_id',
              'conditions' => array(
                    'UsersRound.user_id' => $user_id,
              )
        ));
        $this->Round->contain(false, array('Project'));
        App::uses('CakeTime', 'Utility');
        $date = CakeTime::format('+1 days', '%Y-%m-%d');
        $conditions = array('conditions' => array('AND' => array('Round.id' => $rounds,
                          'ends_in_date IS NOT NULL', "ends_in_date >= $date")));
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } //$post == null
        else {
            $conditions['conditions']['OR'] = $data;
            $this->set('search', $busqueda);
        }
        $this->paginate = $conditions;
        $name = strtolower($this->name);
        $this->set($name, $this->paginate());
    }

    /**
     * search method
     * @return void
     */
    function search() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $search = trim($this->request->data[$this->name]['search']);
            $cond = array();
            $cond['Round.title  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Round.ends_in_date  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Round.description  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array(
                  'action' => 'index',
                  1
            ));
        } //$this->request->is('post') || $this->request->is('put')
    }

    /**
     * start method
     * @deprecated
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function start($id = null, $isRound = false) {
        $user_id = $this->Session->read('user_id');
        if ($isRound) {
            $usersRound = $this->UsersRound->find('first', array(
                  'fields' => 'id',
                  'recursive' => -1,
                  'conditions' => array('round_id' => $id, 'user_id' => $user_id),
                  'order' => array('UsersRound.id Asc')
            ));
            if (isset($usersRound['UsersRound']['id']))
                $this->UsersRound->id = $usersRound['UsersRound']['id'];
        }
        else {
            $this->UsersRound->id = $id;
        }
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->request->is('post') || !$this->request->is('put')) {
            $cond = array(
                  'UsersRound.id' => $this->UsersRound->id
            );
            $this->request->data = $this->UsersRound->find('first', array(
                  'recursive' => -1,
                  'conditions' => $cond
            ));
            $UsersRound = $this->request->data;
            $data = $this->Session->read('data');
            if (!empty($data) && $data['roundId'] != $UsersRound['UsersRound']['round_id']) {
                $this->Session->delete('data');
                $data = null;
            }
            if (empty($data)) {
                $data = array();
                $cond = array(
                      'TypesRound.Round_id' => $UsersRound['UsersRound']['round_id']
                );
                $typesId = $this->UsersRound->Round->TypesRound->find('all', array(
                      'fields' => 'TypesRound.type_id',
                      'conditions' => $cond,
                      'recursive' => -1
                ));
                $typesId = $this->flatten($typesId);
                $types = $this->UsersRound->Round->Project->Type->find('all', array(
                      'contain' => array('Question'),
                      'recursive' => -1,
                      'conditions' => array(
                            'Type.id' => $typesId
                      )
                ));
                if (!empty($types) > 0) {
                    $projectId = $types[0]['Type']['project_id'];
                    $document = $this->UsersRound->Round->Project->DocumentsProject->find('first', array(
                          'fields' => 'DocumentsProject.document_id',
                          'recursive' => -1,
                          'conditions' => array(
                                'DocumentsProject.project_id' => $projectId
                          ),
                          'order' => 'DocumentsProject.document_id ASC'
                    ));
                } //!empty($types) > 0
            } //empty($data)
            else {
                $data = $this->Session->read('data');
                $document = $data['document'];
            }
            if (!empty($document)) {
                if (empty($data)) {
                    $data['document'] = $document;
                    $data['projectId'] = $projectId;
                    $nonTypes = $this->UsersRound->Round->Type->find('list', array(
                          'fields' => 'Type.name',
                          'recursive' => -1,
                          'conditions' => array(
                                "NOT" => array(
                                      'Type.id' => $typesId
                                ),
                                'Type.project_id' => $projectId
                          )
                    ));
                    foreach ($types as &$type):
                        $type['Type']['description'] = str_replace("'", '"', $type['Type']['description']);
                    endforeach;
                    $projectDocuments = $this->UsersRound->Round->Project->DocumentsProject->find('all', array(
                          'fields' => 'DocumentsProject.document_id',
                          'recursive' => -1,
                          'conditions' => array(
                                'DocumentsProject.project_id' => $projectId
                          ),
                          'order' => 'DocumentsProject.document_id ASC'
                    ));
                    $projectDocuments = $this->flatten($projectDocuments);
                    $projectDocuments = $this->UsersRound->Round->Project->Document->find('all', array(
                          'fields' => 'Document.title',
                          'recursive' => -1,
                          'conditions' => array(
                                'Document.id' => $projectDocuments
                          ),
                          'order' => 'Document.id ASC'
                    ));
                    $round = $this->UsersRound->Round->find('first', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'Round.id' => $UsersRound['UsersRound']['round_id']
                          )
                    ));
                    $isEnd = (time() > strtotime($round['Round']['ends_in_date']));
                    $data['types'] = $types;
                    $data['nonTypes'] = $nonTypes;
                    $data['projectDocuments'] = $projectDocuments;
                    $data['roundId'] = $round['Round']['id'];
                    $trim_helper = $data['trim_helper'] = $round['Round']['trim_helper'];
                    $whole_word_helper = $data['whole_word_helper'] = $round['Round']['whole_word_helper'];
                    $punctuation_helper = $data['punctuation_helper'] = $round['Round']['punctuation_helper'];
                    $this->Session->write('data', $data);
                } //empty($data)
                else {
                    $types = $data['types'];
                    $nonTypes = $data['nonTypes'];
                    $projectDocuments = $data['projectDocuments'];
                    $projectId = $data['projectId'];
                    $trim_helper = $data['trim_helper'];
                    $whole_word_helper = $data['whole_word_helper'];
                    $punctuation_helper = $data['punctuation_helper'];
                    $isEnd = $this->Session->read('isEnd');
                }
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                      'Annotation.round_id' => $UsersRound['UsersRound']['round_id'],
                      'Annotation.user_id' => $user_id,
                      'Annotation.init IS NULL',
                      'Annotation.end IS NULL'
                    ), $deleteCascade);
                $page = 1;
                if (trim($UsersRound['UsersRound']['text_marked']) != '') {
                    $page = $this->UsersRound->find('count', array(
                          'fields' => 'id',
                          'recursive' => -1,
                          'conditions' => array(
                                'UsersRound.user_id' => $UsersRound['UsersRound']['user_id'],
                                'UsersRound.round_id' => $UsersRound['UsersRound']['round_id'],
                                'UsersRound.document_id <=' => $UsersRound['UsersRound']['document_id']
                          )
                    ));
                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'Document.id' => $UsersRound['UsersRound']['document_id']
                          )
                    ));
                    $this->set('text', $UsersRound['UsersRound']['text_marked']);
                } //trim($UsersRound['UsersRound']['text_marked']) != ''
                else {
                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'Document.id' => $document['DocumentsProject']['document_id']
                          )
                    ));
                    if (trim($document['Document']['html']) == '') {
                        $this->Session->setFlash(__('This round contains empty documents'));
                        $this->redirect(array(
                              'controller' => 'rounds',
                              'action' => 'index'
                        ));
                    }
                    $this->set('text', $document['Document']['html']);
                }
                $this->set('projectDocuments', $projectDocuments);
                $this->set('title', $document['Document']['title']);
                $this->set('document_id', $document['Document']['id']);
                $this->set('project_id', $projectId);
                $this->set('types', $types);
                $this->set('nonTypes', $nonTypes);
                $this->set('round_id', $UsersRound['UsersRound']['round_id']);
                $this->set('user_id', $user_id);
                $this->set('users_round_id', $this->UsersRound->id);
                $this->set('isEnd', $isEnd);
                $this->set('trim_helper', $trim_helper);
                $this->set('whole_word_helper', $whole_word_helper);
                $this->set('punctuation_helper', $punctuation_helper);
                $this->Session->write('isEnd', $isEnd);
                $triada = array('user_id' => $user_id, 'round_id' => $UsersRound['UsersRound']['round_id'],
                      'document_id' => $document['Document']['id'], 'users_round_id' => $UsersRound['UsersRound']['id']);
                $this->Session->write('triada', $triada);
                $this->paginate = array(
                      'recursive' => -1,
                      'order' => array(
                            'DocumentsProject.document_id' => 'asc'
                      ),
                      'limit' => 1,
                      'page' => $page
                );
                $this->set('DocumentsProject', $this->paginate($this->UsersRound->Round->Project->DocumentsProject, array(
                          'DocumentsProject.project_id' => $projectId
                )));
            } //!empty($document)
            else {
                $this->Session->setFlash(__('There are no documents associated with this project or this round does not have any type associated'));
                $this->redirect(array(
                      'controller' => 'rounds',
                      'action' => 'index'
                ));
            }
        } //!$this->request->is('post') || !$this->request->is('put')
    }

    private function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f",
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
        return $this->correctResponseJson(json_encode(array('success' => true)));
    }

    public function findDocuments($round_id = null, $user_id = null) {
        $this->redirect(array(
              'controller' => 'usersRounds',
              'action' => 'start2',
              $round_id,
              $user_id,
              true
        ));
    }

    public function start2($round_id = null, $user_id = null, $find = null) {
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            $redirect = array(
                  'controller' => 'rounds',
                  'action' => 'index'
            );
            $user_id = $this->Session->read('user_id');
        } else {
            if (!$this->Session->check('users_round_id')) {
                $this->Session->write('users_round_id', $user_id);
            } else {
                $last_user_id = $this->Session->read('users_round_id');
                if (isset($user_id) && $user_id != $last_user_id) {
                    $this->Session->write('users_round_id', $user_id);
                }
                $user_id = $this->Session->read('users_round_id');
            }
            $redirect = array(
                  'controller' => 'rounds',
                  'action' => 'view', $round_id
            );
        }
        $multiDocument = Configure::read('documentsPerPage') > 1;
        if ($find && $group_id == 1) {
            $multiDocument = false;
        } else {
            $find = false;
        }
        $round = Cache::read('round-id-' . $round_id, 'short');
        if (!$round) {
            //buscamos el round para saber la fecha de finalizacion
            $round = $this->UsersRound->Round->find('first', array(
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
        if ($onlyAnnotated) {
            $documents = Cache::read('documents-list-project-id-onlyAnnotated' . $round_id . '-' . $user_id, 'short');
        } else {
            $documents = Cache::read('documents-list-project-id-' . $projectId, 'short');
        }
        if (!$documents) {
            if ($onlyAnnotated) {
                $documentsAll = $this->UsersRound->Round->Project->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array('id', 'title', 'external_id'),
                      'joins' => array(
                            array(
                                  'table' => 'users_rounds',
                                  'alias' => 'UsersRounds',
                                  'type' => 'LEFT',
                                  'conditions' => array(
                                        'UsersRounds.document_id = Document.id',
                                        'UsersRounds.text_marked IS NOT NULL',
                                  ))
                      ),
                      'conditions' => array(
                            'UsersRounds.round_id' => $round_id,
                            'UsersRounds.user_id' => $user_id,
                      ),
                      'limit' => $limit, //int
                      'offset' => $offset, //int
                      'order' => array('document_id Asc')
                ));
            } else {
                $documentsAll = $this->UsersRound->Round->Project->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array('id', 'title', 'external_id'),
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
                      'order' => array('document_id Asc')
                ));
            }
            $documents = array();
            $tam = count($documentsAll);
            for ($index = 0; $index < $tam; $index++) {
                $id = $documentsAll[$index]['Document']['id'];
                $title = "";
                if (isset($documentsAll[$index]['Document']['external_id'])) {
                    $title .= $documentsAll[$index]['Document']['external_id'] . " - ";
                }
                $title .= $documentsAll[$index]['Document']['title'];
                $documents[$id] = $title;
            }
            if ($onlyAnnotated) {
                Cache::write('documents-list-project-id-onlyAnnotated' . $round_id . '-' . $user_id, $documents, 'short');
            } else {
                Cache::write('documents-list-project-id-' . $projectId, $documents, 'short');
            }
        }
        $ids = array_keys($documents);
        if (isset($this->params['named']['page'])) {
            $page = $this->params['named']['page'];
            if ($page <= 0)
                $page = 1;
            if (!$multiDocument) {
                $document_id = $ids[$page - 1];
            }
        } else {
            $page = 1;
            if (!$multiDocument) {
                $document_id = $ids[0];
            }
        }
        /* ===============No Ajax=================== */
        if (!$this->request->is('ajax')) {
            $types = Cache::read('usersRoundTypes-round-id-' . $round_id, 'short');
            if (!$types) {
                $types = $this->UsersRound->Round->Type->find('all', array(
                      'recursive' => -1,
                      'contain' => array('Question',
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
                $this->Session->setFlash(__('There are no types associated with this round'));
                return $this->redirect($redirect);
            }
            //buscamos el primer documento del proyecto
            $nonTypes = Cache::read('nonTypes-round-id-' . $round_id, 'short');
            if (!$nonTypes) {
                $listTypes = Set::classicExtract($types, '{n}.Type.id');
                $nonTypes = $this->UsersRound->Round->Type->find('list', array(
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
                $relations = $this->UsersRound->Round->Project->Relation->find('all', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'Relation.project_id' => $projectId,
                      )
                ));
                Cache::write('relations-project-id-' . $projectId, $relations, 'short');
            }
        }
        if ($multiDocument) {
            /* ================================================================================= */
            /* ==================================Multidocument================================== */
            /* ================================================================================= */
            $this->paginate = array(
                  'recursive' => -1,
                  'order' => array(
                        'DocumentsProject.document_id' => 'asc'
                  ),
                  'conditions' => array('document_id' => array_keys($documents)),
                  'limit' => Configure::read('documentsPerPage'),
                  'offset' => $offset, //int
            );
            $documentsProject = $this->paginate($this->UsersRound->Round->Project->DocumentsProject, array(
                  'DocumentsProject.project_id' => $projectId));
            $documentsIds = Hash::extract($documentsProject, '{n}.DocumentsProject.document_id');
            if (empty($documentsIds)) {
                $this->Session->setFlash(__('There are no documents associated with this project'));
                $this->redirect($redirect);
            }
            if ($group_id > 1) {
                //delete annotation
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                      'Annotation.round_id' => $round_id,
                      'Annotation.user_id' => $user_id,
                      'Annotation.document_id' => $documentsIds,
                      'Annotation.init IS NULL',
                      'Annotation.end IS NULL'
                    ), $deleteCascade);
            }
            $userRounds = $this->UsersRound->find('all', array(
                  'fields' => array('id', 'document_id', 'text_marked'),
                  'recursive' => -1,
                  'conditions' => array('round_id' => $round_id, 'user_id' => $user_id,
                        'document_id' => $documentsIds),
                  'order' => array('UsersRound.document_id Asc')
            ));
            $documentsAnnotatedIds = Hash::extract($userRounds, '{n}.UsersRound.document_id');
            $userRounds = Set::combine($userRounds, '{n}.UsersRound.document_id', '{n}.UsersRound');
            $documents = Cache::read('documents-page' . $page, 'short');
            if (!$documents) {
                $documents = $this->UsersRound->Round->Project->Document->find('all', array(
                      'recursive' => -1,
                      'fields' => array('html', 'id', 'title', 'external_id'),
                      'conditions' => array(
                            'Document.id' => $documentsIds,
                      )
                ));
                $documents = Set::combine($documents, '{n}.Document.id', '{n}.Document');
                Cache::write('documents-page' . $page, $documents, 'short');
            }
            $size = count($documentsIds);
            for ($i = 0; $i < $size; $i++) {
                $document_id = intval($documentsIds[$i]);
                if (!empty($userRounds[$document_id])) {
                    if (strlen(trim($userRounds[$document_id]['text_marked'])) == 0) {
                        $userRounds[$document_id]['text_marked'] = $documents[$document_id]['html'];
                    }
                } else if (strlen(trim($documents[$document_id]['html'])) !== 0) {
                    $userRounds[$document_id] = array(
                          'user_id' => $user_id,
                          'round_id' => $round_id,
                          'document_id' => $document_id,
                          'text_marked' => $documents[$document_id]['html']
                    );
                    if ($group_id > 1) {
                        $this->UsersRound->create();
                        if (!$this->UsersRound->save($userRounds[$document_id])) {
                            debug($this->UsersRound->validationErrors);
                            $this->Session->setFlash(__('ops! error creating user round ' . $this->UsersRound->validationErrors));
                            $this->redirect($redirect);
                        }
                        $userRounds[$document_id]['id'] = $this->UsersRound->id;
                    }
                    $userRounds[$document_id]['text_marked'] = $documents[$document_id]['html'];
                    $userRounds[$document_id]['document_id'] = $document_id;
                }
                $title = "Title: " . $documents[$document_id]['title'];
                if (isset($documents[$document_id]['external_id'])) {
                    $title = "ID: " . $documents[$document_id]['external_id'];
                }
                $userRounds[$document_id]['title'] = $title;
            }
            $documentAssessment = $this->UsersRound->Round->Project->Document->DocumentsAssessment->find('list', array(
                  'recursive' => -1,
                  'fields' => array('document_id', 'document_id'),
                  'conditions' => array('user_id' => $user_id, 'project_id' => $projectId,
                        'document_id' => $ids)));
            $this->set('documentAssessments', $documentAssessment);
            $this->set('userRounds', $userRounds);
            $triada = array('user_id' => $user_id, 'round_id' => $round_id,
                  'document_id' => -1, 'users_round_id' => -1);
            $this->set('DocumentsProject', $documentsProject);
        } else {
            /* ================================================================================= */
            /* ==================================No multidocument=============================== */
            /* ================================================================================= */
            $document = Cache::read('document-id-' . $document_id, 'short');
            if (!$document) {
                $document = $this->UsersRound->Round->Project->Document->find('first', array(
                      'recursive' => -1,
                      'fields' => array('html', 'title', 'external_id'),
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
            $userRound = $this->UsersRound->find('first', array(
                  'recursive' => -1,
                  'conditions' => array('round_id' => $round_id, 'user_id' => $user_id,
                        'document_id' => $document_id),
                  'order' => array('UsersRound.document_id Asc')
            ));
            if (!empty($userRound)) {
                if (strlen(trim($userRound['UsersRound']['text_marked'])) !== 0) {
                    $this->set('text', $userRound['UsersRound']['text_marked']);
                } else {
                    $this->set('text', $document['Document']['html']);
                }
            } else if (!empty($document) && strlen(trim($document['Document']['html'])) !== 0) {
                $userRound = array(
                      'UsersRound' => array(
                            'user_id' => $user_id,
                            'round_id' => $round_id,
                            'document_id' => $document_id,
                            'text_marked' => $document['Document']['html']
                ));
                if ($group_id > 1) {
                    if (!$this->UsersRound->save($userRound)) {
                        debug($this->UsersRound->validationErrors);
                        $this->Session->setFlash(__('ops! error creating user round ' . $this->UsersRound->validationErrors));
                        $this->redirect($redirect);
                    }
                }
                $userRound['UsersRound']['id'] = $this->UsersRound->id;
                $this->set('text', $document['Document']['html']);
            } else {
                $this->Session->setFlash(__('There are no documents associated with this project'));
                $this->redirect($redirect);
            }
            if ($group_id > 1) {
                //delete annotation
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                      'Annotation.round_id' => $userRound['UsersRound']['round_id'],
                      'Annotation.user_id' => $user_id,
                      'Annotation.users_round_id' => $userRound['UsersRound']['id'],
                      'Annotation.init IS NULL',
                      'Annotation.end IS NULL'
                    ), $deleteCascade);
            }
            $this->paginate = array(
                  'recursive' => -1,
                  'order' => array(
                        'DocumentsProject.document_id' => 'asc'
                  ),
                  'conditions' => array('document_id' => array_keys($documents)),
                  'limit' => 1,
                  'offset' => $offset, //int
            );
            $this->set('DocumentsProject', $this->paginate($this->UsersRound->Round->Project->DocumentsProject, array(
                      'DocumentsProject.project_id' => $projectId)));
            $title = "Title: " . $document['Document']['title'];
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }
            $this->set('title', $title);
            $this->set('users_round_id', $userRound['UsersRound']['id']);
            //variable que contiene User.round.Document.user_round_id
            $triada = array('user_id' => $user_id, 'round_id' => $round_id,
                  'document_id' => $document_id, 'users_round_id' => $userRound['UsersRound']['id']);
        }
        $trim_helper = $round['Round']['trim_helper'];
        $whole_word_helper = $round['Round']['whole_word_helper'];
        $punctuation_helper = $round['Round']['punctuation_helper'];
        //escribimos la variable en una variable de session puesto que nos sera util a la hora de verificar la fecha cuando se intente crear anotaciones o editarlas
        $this->Session->write('isEnd', $isEnd);
        //esta variable sera usada para constatar que no se intentan modificar dichas variables
        $this->Session->write('triada', $triada);
        if (!$this->Session->check('start_step') && $group_id > 1) {
            $this->Session->write('start_step', new DateTime(''));
        }
        $this->set('document_id', $document_id);
        $this->set('multiDocument', $multiDocument);
        $this->set('findMode', $find);
        if (!$this->request->is('ajax')) {
            $this->set('documents', array_values($documents));
            if ($page > 0) {
                $page--;
            }
            $this->set('page', $page);
            $this->set('round_id', $round_id);
            $this->set('project_id', $projectId);
            $this->set('types', $types);
            $this->set('relations', $relations);
            //lo utilizaremos para eliminar las anotaciones de un tipo eliminado
            $this->set('nonTypes', $nonTypes);
            $this->set('isEnd', $isEnd);
            $this->set('user_id', $user_id);
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }
            $this->set('title', $title);
            $this->set('trim_helper', $trim_helper);
            $this->set('whole_word_helper', $whole_word_helper);
            $this->set('punctuation_helper', $punctuation_helper);
            $this->set('annotationMenu', true);
            $this->render("start");
        } else {
            $this->layout = 'ajax';
            $title = "Title: " . $document['Document']['title'];
            if (isset($document['Document']['external_id'])) {
                $title = "ID: " . $document['Document']['external_id'];
            }
            $this->set('title', $title);
            $this->set('isEnd', $isEnd);
            $this->render("ajax");
        }
    }

    /**
     * edit method
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function save2() {
        $this->autoRender = false;
        $triada = $this->Session->read('triada');
        $this->UsersRound->id = $triada['users_round_id'];
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $pos = $this->request->data['UsersRound']['page'];
            $isEnd = $this->Session->read('isEnd');
            if (strlen(trim($this->request->data['UsersRound']['text_marked'])) !== 0 && !$isEnd) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                $textoMarky = trim($this->request->data['UsersRound']['text_marked']);
                $textoForMatches = $this->parseHtmlToGetAnnotations($textoMarky);
                $parseKey = Configure::read('parseKey');
                $parseIdAttr = Configure::read('parseIdAttr');
                preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                $allAnnotations = array();
                $db = $this->UsersRound->getDataSource();
                $db->begin();
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
                            throw new Exception("Annotation mark error, id not found document_id: " . $this->request->data['UsersRound']['document_id'] . " mark " . $matches[0][$i][0]);
                        }
                        if ($insertID != $value[1]) {
                            $lastID = $insertID;
                            $this->UsersRound->Annotation->id = $insertID;
                            if ($this->UsersRound->Annotation->exists() && $this->UsersRound->Annotation->save(array(
                                      'init' => $original_start,
                                      'end' => $original_start + $textAcummulate
                                ))) {
                                array_push($allAnnotations, $insertID);
                            } else {
                                $this->Session->setFlash(__("This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text));
                                $db->rollback();
                                return $this->redirect(array(
                                          'controller' => 'annotations',
                                          'action' => 'redirectToAnnotatedDocument',
                                          $triada['round_id'],
                                          $triada['user_id'],
                                          $triada['document_id'],
                                ));
                            }
                            $textAcummulate = 0;
                            $text = "";
                            $insertID = $value[1];
                            $original_start = $matches[1][$i][1] - $accum;
                        } //$insertID != $value[1]
                        $textAcummulate += strlen(strip_tags($matches[0][$i][0]));
                        debug(strip_tags($matches[0][$i][0]));
                        debug(strlen(strip_tags($matches[0][$i][0])));
                        throw new Exception;
                        $text .= $matches[0][$i][0];
                        $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                    } //$i = 0; $i < sizeof($matches[1]); $i++
                    if ($lastID != $insertID) {
                        $this->UsersRound->Annotation->id = $insertID;
                        if ($this->UsersRound->Annotation->exists() && $this->UsersRound->Annotation->save(array(
                                  'init' => $original_start,
                                  'end' => $original_start + $textAcummulate
                            ))) {
                            array_push($allAnnotations, $insertID);
                        } else {
                            $this->Session->setFlash(__("This Document could not be saved. Annotation $text does not exist in database. Please, delete annotation with text:" . $text));
                            $db->rollback();
                            return $this->redirect(array(
                                      'controller' => 'annotations',
                                      'action' => 'redirectToAnnotatedDocument',
                                      $triada['round_id'],
                                      $triada['user_id'],
                                      $triada['document_id'],
                            ));
                        }
                    } //$lastID != $insertID
                } //sizeof($matches[1]) != 0
                $this->request->data['UsersRound']['text_marked'] = $textoMarky;
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                      'not' => array(
                            'Annotation.id' => $allAnnotations
                      ),
                      'Annotation.round_id' => $triada['round_id'],
                      'Annotation.user_id' => $triada['user_id'],
                      'Annotation.document_id' => $triada['document_id'],
                    ), $deleteCascade);
                if ($this->UsersRound->save($this->request->data)) {
                    $cond = array(
                          'round_id' => $triada['round_id'],
                          'user_id' => $triada['user_id'],
                    );
                    $this->UsersRound->UpdateAll(array(
                          'modified' => 'NOW()'
                        ), $cond);
                    $db->commit();
                    $this->Session->setFlash(__('Changes has been saved'), 'success');
                } //$this->UsersRound->save($this->request->data)
                else {
                    $db->rollback();
                    $this->Session->setFlash(__('The round could not be saved. Info: save Annoted text. Please, try again.'));
                    return $this->redirect(array(
                              'controller' => 'annotations',
                              'action' => 'redirectToAnnotatedDocument',
                              $triada['round_id'],
                              $triada['user_id'],
                              $triada['document_id'],
                    ));
                }
            }
            return $this->redirect(array(
                      'controller' => 'usersRounds',
                      'action' => 'start2',
                      $triada['round_id'],
                      'page' => $this->request->data['UsersRound']['page']
            ));
        }
    }

    public function saveAjax() {
        $this->autoRender = false;
        $triada = $this->Session->read('triada');
        if ($this->request->is('post') || $this->request->is('put')) {
            if (isset($this->request->data['documents'])) {
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
                    $usersRound = array();
                    $usersRound['document_id'] = $document['document_id'];
                    $usersRound['id'] = $document['id'];
                    $conditions = array('id' => $usersRound['id'], 'document_id' => $usersRound['document_id'],
                          'user_id' => $triada['user_id'], 'round_id' => $triada['round_id']);
                    if (!$this->UsersRound->hasAny($conditions)) {
                        $db->rollback();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "Ops! This documents could not be saved. 404 error")));
                    }
                    $isEnd = $this->Session->read('isEnd');
                    if (strlen(trim($document['text_marked'])) !== 0 && !$isEnd) { // nos ahorramos transacciones si se le da a borra sin modificar nada
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
                                    $this->UsersRound->Annotation->id = $insertID;
                                    if ($this->UsersRound->Annotation->exists() && $this->UsersRound->Annotation->save(array(
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
                                $this->UsersRound->Annotation->id = $insertID;
                                if ($this->UsersRound->Annotation->exists() && $this->UsersRound->Annotation->save(array(
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
                    $this->UsersRound->Annotation->deleteAll(array(
                          'not' => array(
                                'Annotation.id' => $allAnnotations
                          ),
                          'Annotation.round_id' => $triada['round_id'],
                          'Annotation.user_id' => $triada['user_id'],
                          'Annotation.document_id' => $usersRound['document_id'],
                          'Annotation.users_round_id' => $usersRound['id']
                        ), $deleteCascade);
                    if (!$this->UsersRound->save($document)) {
                        $db->rollback();
                        return $this->correctResponseJson(json_encode(array(
                                  'success' => false,
                                  'message' => "The round could not be saved. Info: Error document." . $usersRound['id'])));
                        //$this->redirect(array('action' => 'start', $this->UsersRound->id));
                    }
                }
                if ($commit) {
                    $cond = array(
                          'round_id' => $triada['round_id'],
                          'user_id' => $triada['user_id'],
                    );
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

    /**
     * edit method
     * @deprecated
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function save($id = null) {
        throw new Exception;
        $this->autoRender = false;
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $pos = $this->request->data['UsersRound']['page'];
            if ($this->request->data['UsersRound']['deleteSessionData'])
                $this->Session->delete('data');
            //borarmos esta variable de session para que se actualicen los satos del round
            $isEnd = $this->Session->read('isEnd');
            //usado para paginacion
            if ($this->request->data['UsersRound']['text_marked'] != 'empty' && !$isEnd) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                $textoMarky = trim($this->request->data['UsersRound']['text_marked']);
                $textoMarky = preg_replace('/\s+/', ' ', $textoMarky);
                //las siguientes lineas son necesarias dado que cada navegador hace lo  que le da la gana con el DOM con respecto a la gramatica,
                //no hay un estandar asi por ejemplo en crhome existe Style:valor y en Explorer Style :valor,etc
                $textoForMatches = str_replace(array(
                      "\n",
                      "\t",
                      "\r"
                    ), '', $textoMarky);
                //$textoForMatches = str_replace('> <', '><', $textoForMatches);
                $textoForMatches = strip_tags($textoForMatches, '<mark>');
                $textoForMatches = utf8_decode(html_entity_decode($textoForMatches));
                //buscamnos el comienzo por ello devemos volver a machear todos los span
                $parseKey = Configure::read('parseKey');
                $parseIdAttr = Configure::read('parseIdAttr');
                preg_match_all("/(<mark[^>]*" . $parseKey . "[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                //array donde guardaremos las nuevas anotaciones
                $allAnnotations = array();
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
                    $textAcummulate = -1;
                    for ($i = 0; $i < sizeof($matches[1]); $i++) {
                        preg_match('/[^>]*' . $parseIdAttr . '=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                        //es necesario hacer esto debido a que pude darse el caso de que tengamos <mark> entre tags html anidadas con el mismo id
                        if ($insertID != $value[1]) {
                            $lastID = $insertID;
                            if ($this->UsersRound->Annotation->updateAll(array(
                                      'Annotation.init' => $original_start,
                                      'Annotation.end' => $original_start + strlen($texto)
                                    ), array(
                                      'Annotation.id' => $insertID
                                    ), -1)) {
                                array_push($allAnnotations, $insertID);
                            }
                            //actualizamos las variables para la proxima anotacion
                            $textAcummulate = -1;
                            $texto = "";
                            $insertID = $value[1];
                            $original_start = $matches[1][$i][1] - $accum;
                        } //$insertID != $value[1]
                        $texto = strip_tags($matches[0][$i][0]);
                        $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                    } //$i = 0; $i < sizeof($matches[1]); $i++
                    //introducimos la ultima anotacion dado que ha quedado sin introducir
                    //LENGTH (Annotation.annotated_text)-1 tamanho del texto original -1 dado que preg match empieza en 0
                    if ($lastID != $insertID) {
                        if ($this->UsersRound->Annotation->updateAll(array(
                                  'Annotation.init' => $original_start,
                                  'Annotation.end' => $original_start + strlen($matches[2][$i - 1][0])
                                ), array(
                                  'Annotation.id' => $insertID
                                ), -1)) {
                            array_push($allAnnotations, $insertID);
                        }
                    } //$lastID != $insertID
                } //sizeof($matches[1]) != 0
                //$textoMarky=str_ireplace("id=\"Marky","onmousedown='unHiglight(event);' id=\"Marky",$textoMarky); //for much browsers
                $this->request->data['UsersRound']['text_marked'] = $textoMarky;
                unset($this->request->data['UsersRound']['page']);
                //borramos todas las anotaciones que no tengan inicio ni final
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                      'not' => array(
                            'Annotation.id' => $allAnnotations
                      ),
                      'Annotation.users_round_id' => $this->UsersRound->id
                    ), $deleteCascade);
                if ($this->UsersRound->save($this->request->data)) {
                    $cond = array(
                          'round_id' => $this->request->data['UsersRound']['round_id'],
                          'user_id' => $this->request->data['UsersRound']['user_id']
                    );
                    $this->UsersRound->UpdateAll(array(
                          'modified' => 'NOW()'
                        ), $cond);
                    //modificamos todos los rounds dado que todos ellos forman un nico round
                    if ($pos != '#')
                        $this->Session->setFlash(__('The previous document has been saved'), 'success');
                    else
                        $this->Session->setFlash(__('Document has been saved', 'success'));
                } //$this->UsersRound->save($this->request->data)
                else {
                    $this->Session->setFlash(__('The round could not be saved.Info: save Annoted text. Please, try again.'));
                    //$this->redirect(array('action' => 'start', $this->UsersRound->id));
                }
            } //$this->request->data['UsersRound']['text_marked'] != 'empty' && !$isEnd
            //$this->Session->setFlash(__('the request text is empty. A herror has occurred or the text was already empty. Therefore, the round will not be saved.'));
            if ($pos != '#') {
                $pos = $pos - 1;
                //buscamos el round para obtener el proyecto_id
                $triada = $this->Session->read('triada');
                $round = $this->UsersRound->Round->find('first', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'Round.id' => $triada['round_id']
                      )
                ));
                $documents = $this->UsersRound->Round->Project->DocumentsProject->find('all', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'DocumentsProject.project_id' => $round['Round']['project_id']
                      ),
                      'order' => 'DocumentsProject.Document_id ASC',
                      'offset' => $pos,
                      'limit' => $pos
                ));
                $UsersRound = $this->UsersRound->find('first', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'UsersRound.user_id' => $this->request->data['UsersRound']['user_id'],
                            'UsersRound.round_id' => $round['Round']['id'],
                            'UsersRound.document_id' => $documents[0]['DocumentsProject']['document_id']
                      )
                ));
                if (empty($UsersRound)) {
                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'Document.id' => $documents[0]['DocumentsProject']['document_id']
                          )
                    ));
                    $data = array(
                          'user_id' => $this->request->data['UsersRound']['user_id'],
                          'round_id' => $round['Round']['id'],
                          'document_id' => $documents[0]['DocumentsProject']['document_id'],
                          'text_marked' => $document['Document']['html']
                    );
                    $this->UsersRound->id = null;
                    $this->UsersRound->create();
                    if ($this->UsersRound->save($data)) {
                        $sigId = $this->UsersRound->id;
                    } //$this->UsersRound->save($data)
                    else {
                        $this->Session->setFlash(__('The users round could not be saved. Uncknow server error. Please, try again.'));
                        $sigId = $id;
                    }
                } //empty($UsersRound)
                else {
                    $sigId = $UsersRound['UsersRound']['id'];
                }
                $this->redirect(array(
                      'controller' => 'usersRounds',
                      'action' => 'start',
                      $sigId,
                      "page" => $pos + 1
                ));
            } //$pos != '#'
            else {
                $this->Session->setFlash(__('Document has been saved'), 'success');
                $this->redirect(array(
                      'controller' => 'usersRounds',
                      'action' => 'start',
                      $this->UsersRound->id
                ));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->request->data = $this->UsersRound->read(null, $id);
        }
    }

    /**
     * start method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($round_id = null, $user_id = null) {
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            $user_id = $this->Session->read('user_id');
            $hasAnyUsersRound = $this->UsersRound->hasAny(array('round_id' => $round_id,
                  'user_id' => $user_id));
            if (!$hasAnyUsersRound) {
                throw new NotFoundException(__('Invalid round'));
            }
        }
        $this->UsersRound->Round->id = $round_id;
        $this->UsersRound->User->id = $user_id;
        if (!$this->UsersRound->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->UsersRound->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $count = $this->UsersRound->find('count', array(
              'recursive' => -1,
              'conditions' => array('user_id' => $user_id, 'round_id' => $round_id,
                    array('NOT' => array('text_marked' => NULL))
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
            $typesId = $this->UsersRound->Round->TypesRound->find('all', array(
                  'fields' => 'TypesRound.type_id',
                  'conditions' => $cond,
                  'recursive' => -1
            ));
            $typesId = $this->flatten($typesId);
            $types = $this->UsersRound->Round->Project->Type->find('all', array(
                  'contain' => array('Question'),
                  'recursive' => -1,
                  'conditions' => array(
                        'Type.id' => $typesId
                  )
            ));
            $projectId = $types[0]['Type']['project_id'];
            $data['projectId'] = $projectId;
            $nonTypes = $this->UsersRound->Round->Type->find('all', array(
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
            $documents = $this->UsersRound->Round->Project->Document->find('list', array(
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
                              'table' => 'users_rounds',
                              'alias' => 'UsersRound',
                              'type' => 'Inner',
                              'conditions' => array(
                                    'UsersRound.document_id=Document.id'
                              )
                        )),
                  'conditions' => array('project_id' => $projectId, array('NOT' => array(
                                    'UsersRound.text_marked' => NULL))),
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
              'conditions' => array('id' => $user_id)
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
                    'UsersRound.document_id' => 'asc'
              ),
              'limit' => 1,
              'conditions' => array('user_id' => $user_id, 'round_id' => $round_id,
                    array('NOT' => array('text_marked' => NULL)))
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
    public function externalView($external_id = null, $project_id = null) {
        if (!isset($external_id)) {
            throw new NotFoundException(__('Invalid document_id'));
        }
        $openAnnotatedDocs = Configure::read('openAnnotatedDocs');
        if (!empty($openAnnotatedDocs)) {
            $user_id = $openAnnotatedDocs['user_id'];
            $round_id = $openAnnotatedDocs['round_id'];
        } else {
            $this->Participant = $this->UsersRound->Round->Project->Participant;
            $goldenProject = $this->Participant->GoldenProject->find('first', array(
                  'recursive' => -1,
                  'fields' => array('user_id', 'round_id'),
                  'conditions' => array('project_id' => $project_id),
            ));
            if (!empty($goldenProject)) {
                $user_id = $goldenProject['GoldenProject']['user_id'];
                $round_id = $goldenProject['GoldenProject']['round_id'];
            } else {
                throw new NotFoundException(__('Invalid project_id'));
            }
        }
        $hasAnyUsersRound = $this->UsersRound->hasAny(array('round_id' => $round_id,
              'user_id' => $user_id));
        if (!$hasAnyUsersRound) {
            throw new NotFoundException(__('Invalid round'));
        }
        $this->UsersRound->Round->id = $round_id;
        $this->UsersRound->User->id = $user_id;
        if (!$this->UsersRound->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->UsersRound->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()
        $count = $this->UsersRound->find('count', array(
              'recursive' => -1,
              'conditions' => array('user_id' => $user_id, 'round_id' => $round_id,
                    array('NOT' => array('text_marked' => NULL))
        )));
        if ($count == 0) {
            throw new NotFoundException(__('This user has not annotated any document'));
        } //!
        $round = Cache::read('round-id-' . $round_id, 'short');
        if (!$round) {
            $round = $this->UsersRound->Round->find('first', array(
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
            $document = $this->UsersRound->Round->Project->Document->find('first', array(
                  'recursive' => -1,
                  'fields' => array('id', 'title', 'external_id'),
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
                        'Document.external_id' => $external_id,
                  ),
            ));
            Cache::write('annotated_document' . $round_id . '-' . $user_id . '-' . $external_id, $document, 'short');
        }
        if (empty($document)) {
            $this->redirect('http://www.ncbi.nlm.nih.gov/pubmed/' . $external_id);
        }
        $types = Cache::read('usersRoundTypes-round-id-' . $round_id, 'short');
        if (!$types) {
            $types = $this->UsersRound->Round->Type->find('all', array(
                  'recursive' => -1,
                  'contain' => array('Question',
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
            $nonTypes = $this->UsersRound->Round->Type->find('list', array(
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
            $relations = $this->UsersRound->Round->Project->Relation->find('all', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Relation.project_id' => $projectId,
                  )
            ));
            Cache::write('relations-project-id-' . $projectId, $relations, 'short');
        }
        $document_id = $document['Document']['id'];
        $userRound = $this->UsersRound->find('first', array(
              'recursive' => -1,
              'conditions' => array('round_id' => $round_id, 'user_id' => $user_id,
                    'document_id' => $document_id),
              'order' => array('UsersRound.document_id Asc')
        ));
        if (!empty($userRound)) {
            if (strlen(trim($userRound['UsersRound']['text_marked'])) !== 0) {
                $this->set('text', $userRound['UsersRound']['text_marked']);
            } else {
                //para el primer user round vacio
                throw new NotFoundException(__('Invalid user_round'));
            }
        } else {
            throw new NotFoundException(__('Invalid user_round'));
        }
        $deleteCascade = Configure::read('deleteCascade');
        $this->UsersRound->Annotation->deleteAll(array(
              'Annotation.round_id' => $userRound['UsersRound']['round_id'],
              'Annotation.user_id' => $user_id,
              'Annotation.users_round_id' => $userRound['UsersRound']['id'],
              'Annotation.init IS NULL',
              'Annotation.end IS NULL'
            ), $deleteCascade);
        $this->Session->write('isEnd', $isEnd);
        //variable que contiene User.round.Document.users_round_id
        $triada = array('user_id' => $user_id, 'round_id' => $round_id,
              'document_id' => $document_id, 'users_round_id' => $userRound['UsersRound']['id']);
        //esta variable sera usada para constatar que no se intentan modificar dichas variables
        $this->Session->write('triada', $triada);
        $this->set('document_id', $document_id);
        $this->set('multiDocument', false);
        $this->set('round_id', $round_id);
        $this->set('project_id', $projectId);
        $this->set('user_id', $userRound['UsersRound']['user_id']);
        $this->set('users_round_id', $userRound['UsersRound']['id']);
        $this->set('types', $types);
        $this->set('relations', $relations);
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

    public function rate($id = null) {
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        else {
            $userRound = $this->UsersRound->find('first', array('fields' => array(
                        'user_id', 'document_id'), 'conditions' => array('id' => $id),
                  'recursive' => -1));
            $user_id = $this->Session->read('user_id');
            if ($userRound['UsersRound']['user_id'] == $user_id) {
                if ($this->UsersRound->updateAll(array('rate' => $this->request->data['rate']), array(
                          'user_id' => $user_id, 'document_id' => $userRound['UsersRound']['document_id']))) {
                    return $this->correctResponseJson(json_encode(array('success' => true)));
                }
            }
            return $this->correctResponseJson(json_encode(array('success' => false)));
        }
    }

    public function compare($users_round_id_A = null, $users_round_id_B = null) {
        $this->UsersRound->id = $users_round_id_A;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round A'));
        } //!$this->UsersRound->exists()
        $this->UsersRound->id = $users_round_id_B;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round B'));
        } //!$this->UsersRound->exists()
        $userRound_A = $this->UsersRound->find('first', array(
              'contain' => array(
                    'User' => array('full_name', 'image', 'image_type'),
                    'Round' => ('title'),
                    'Document' => ('title'),
              ),
              'recursive' => -1,
              'conditions' => array('UsersRound.id' => $users_round_id_A),
        ));
        $userRound_B = $this->UsersRound->find('first', array(
              'contain' => array(
                    'User' => array('full_name', 'image', 'image_type'),
                    'Round' => ('title'),
                    'Document' => ('title'),
              ),
              'recursive' => -1,
              'conditions' => array('UsersRound.id' => $users_round_id_B),
        ));
        $round_id_A = $userRound_A['UsersRound']['round_id'];
        $round_id_B = $userRound_B['UsersRound']['round_id'];
        $types = Cache::read('usersRoundTypes-round-id-compare-' . $round_id_A . '_' . $round_id_B, 'short');
        if (!$types) {
            $types = $this->UsersRound->Round->Type->find('all', array(
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
                        'TypesRound.round_id' => array($round_id_A, $round_id_B)
                  ),
                  'group' => array('Type.id')
            ));
            Cache::write('usersRoundTypes-round-id-compare-' . $round_id_A . '/' . $round_id_B, $types, 'short');
        }
        if (empty($types)) {
            $this->Session->setFlash(__('There are no types associated with this round'));
            $this->redirect(array('controller' => 'annotations', 'action' => 'listAnnotationsHits'));
        }
        if (strlen(trim($userRound_A['UsersRound']['text_marked'])) !== 0 && strlen(trim($userRound_B['UsersRound']['text_marked'])) !== 0) {
            
        } else {
            $this->Session->setFlash(__('There are no text annotated'));
            $this->redirect(array('controller' => 'annotations', 'action' => 'listAnnotationsHits'));
        }
        $this->set('userRound_A', $userRound_A);
        $this->set('userRound_B', $userRound_B);
        $this->set('types', $types);
        $this->set('isEnd', true);
    }

}
