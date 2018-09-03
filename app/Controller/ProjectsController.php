<?php

/* __  __            _          	 © 
  |  \/  |          | |
  | \  / | __ _ _ __| | ___   _
  | |\/| |/ _` | '__| |/ / | | |
  | |  | | (_| | |  |   <| |_| |
  |_|  |_|\__,_|_|  |_|\_\\__, |
  __/ |
  |___/
  _____ ______ _      _____
  |  __ \| ___ \ |    |____ |
  | |  \/| |_/ / |        / /
  | | __ |  __/| |        \ \
  | |_\ \| |   | |____.___/ /
  \____/\_|   \_____/\____/
  Copyright (C) 2013-2014 Martín Pérez Pérez.
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  For any doubt consult the official website: http://sing.ei.uvigo.es/marky/
  For any doubt Marky's license  send email to mpperez3@esei.uvigo.es.
 */
App::uses('AppController', 'Controller');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');

/**
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {
        $this->Project->recursive = -1;
        $this->paginate = array(
              'fields' => array(
                    '`Project`.`id`, `Project`.`title`, `Project`.`created`, `Project`.`modified`'
              )
        );
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        $conditions = array(
              'conditions' => array());
        $group_id = $this->Session->read('group_id');
        $user_id = $this->Session->read('user_id');
        if ($group_id > 1) {
            $projects = $this->Project->ProjectsUser->find('list', array(
                  'recursive' => -1,
                  'fields' => 'ProjectsUser.project_id',
                  'ProjectsUser.project_id',
                  'conditions' => array(
                        'ProjectsUser.user_id' => $user_id
            )));
            $conditions['conditions']['AND'] = array(
                  'id' => $projects);
        }
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } //$post == null
        else if (!empty($data)) {
            $conditions['conditions']['OR'] = $data;
            $this->set('search', $busqueda);
        }
        $this->paginate = $conditions;
        $name = strtolower($this->name);
        $this->set($name, $this->paginate());
        if ($group_id > 1) {
            $this->render('userIndex');
        }
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
            $cond['Project.title  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Project.created  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Project.modified  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Project.description  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array(
                  'action' => 'index',
                  1
            ));
        } //$this->request->is('post') || $this->request->is('put')
    }

    /**
     * userIndex method
     * @return void
     */

    /**
     * user_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function userView($id = null) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        } //!$this->Project->exists()
        $this->Project->recursive = 1;
        $contain = array(
              'Type' => array(
                    'id',
                    'name',
                    'colour',
                    'description'
              )
        );
        $user_id = $this->Session->read('user_id');
        $this->set('project', $this->Project->find('first', array(
                  'contain' => $contain,
                  'conditions' => array(
                        'Project.id' => $id
                  )
        )));
        $user_id = $this->Session->read('user_id');
        $documents = $this->Project->DocumentsProject->find('count', array(
              'conditions' => array(
                    'project_id' => $id,
              )
        ));
        $rounds = $this->Project->Round->find('list', array(
              'fields' => array(
                    'id',
                    'id'),
              'conditions' => array(
                    'project_id' => $id,
              )
        ));
        $annotations = $this->Project->Type->Annotation->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'Annotation.user_id' => $user_id,
                    'Annotation.round_id' => $rounds
              )
        ));
        $this->set('user_id', $user_id);
        $this->set(compact('documents', 'rounds', 'annotations'));
        $this->set('user_id', $user_id);
        $this->set('project_id', $id);
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        $this->Project->id = $id;
        $this->Round = $this->Project->Round;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        } //!$this->Project->exists()
        //si existen rounds sin existeir ningun documento se borran dado que no tiene sentido tener rounds sin documentos.
        $numberOfDocuments = $this->Project->DocumentsProject->find('first', array(
              'conditions' => array(
                    'DocumentsProject.project_id' => $this->Project->id
              )
        ));
        if (empty($numberOfDocuments)) {
            $rounds = $this->Project->Round->find('list', array(
                  'fields' => 'Round.id',
                  'recursive' => -1,
                  'conditions' => array(
                        'Round.project_id' => $this->Project->id
                  )
            ));
            $deleteCascade = Configure::read('deleteCascade');
            $this->Project->Round->deleteAll(array(
                  'Round.id' => $rounds
                ), $deleteCascade);
        }
        $cond = array(
              'Project.id' => $id
        );
        $contain = array(
              'Type' => array(
                    'id',
                    'name',
                    'colour'
              ),
              'Relation' => array(
                    'id',
                    'name',
                    'colour'
              ),
              'Round' => array(
                    'id',
                    'title',
                    'ends_in_date'
              ),
              'User' => array(
                    'id',
                    'username',
                    'surname',
                    'email',
                    'image_type',
                    'image',
              ),
              'ProjectNetworks',
        );
        $project = $this->Project->find('first', array(
              'contain' => $contain,
              'conditions' => array(
                    'Project.id' => $id
              )
        ));
        $this->Project->virtualFields['negatives'] = 'SUM(DocumentsAssessment.negative)';
        $this->Project->virtualFields['neutral'] = 'SUM(DocumentsAssessment.neutral)';
        $this->Project->virtualFields['positives'] = 'SUM(DocumentsAssessment.positive)';
        $fields = array(
              ' `Document`.`id`, `Document`.`title`, `Document`.`created`, `DocumentsProject`.`document_id`, `DocumentsProject`.`project_id`',
              'positives',
              'neutral',
              'negatives'
        );
        $this->paginate = array(
              'fields' => $fields,
              'joins' => array(
                    array(
                          'table' => 'documents_projects',
                          'alias' => 'DocumentsProject',
                          'type' => 'Inner',
                          'conditions' => array(
                                'DocumentsProject.project_id=Project.id'
                          )
                    ),
                    array(
                          'table' => 'documents',
                          'alias' => 'Document',
                          'type' => 'Inner',
                          'conditions' => array(
                                'DocumentsProject.document_id = Document.id'
                          )
                    ),
                    array(
                          'table' => 'documents_assessments',
                          'alias' => 'DocumentsAssessment',
                          'type' => 'LEFT',
                          'conditions' => array(
                                'DocumentsAssessment.document_id = Document.id',
                                'DocumentsAssessment.project_id=Project.id'
                          )
                    )
              ),
              'conditions' => $cond,
              'group' => array(
                    'Document`.`id`'),
              'recursive' => 0
        );
        /* =========statistisc=============== */
        $types = $project['Type'];
        $rounds = Set::classicExtract($project['Round'], '{n}.id');
        $users = Set::classicExtract($project['User'], '{n}.id');
        $statisticsData = array();
        $totalAnnotations = 0;
        foreach ($types as $type) {
            $count = $this->Project->Type->Annotation->find('count', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Annotation.type_id' => $type['id'],
                        'Annotation.user_id' => $users,
                        'Annotation.round_id' => $rounds
                  )
            ));
            array_push($statisticsData, array(
                  'GraficColumns' => $type['name'],
                  'Colour' => $this->RGBToHex($type['colour']),
                  'value' => $count
            ));
            $totalAnnotations += $count;
        }
        $totalRelations = $this->Project->Relation->find('count', array(
              'recursive' => -1,
              'conditions' => array(
                    'project_id' => $id,
              )
        ));
        /* =================== */
        $this->set('totalAnnotations', $totalAnnotations);
        $this->set('totalRelations', $totalRelations);
        $this->set('statisticsData', $statisticsData);
        $this->set('documents', $this->paginate());
        $this->set('project_id', $id);
        $this->set('project', $project);
        $this->set('projects', $this->Project->find('list', array(
                  'conditions' => array(
                        'NOT' => array(
                              'id' => $id)))));
        $this->AnnotationsInterRelation->virtualFields['total'] = 'COUNT(AnnotationsInterRelation.id)';
        $relationsByType = $this->AnnotationsInterRelation->find('all', array(
              'recursive' => -1, //int
              'fields' => array('total', 'relation_id', 'Relation.name', 'Relation.colour'),
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationsInterRelation.annotation_a_id = Annotation.id'
                          )
                    ),
                    array(
                          'table' => 'relations',
                          'alias' => 'Relation',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Relation.project_id' => $id,
                                'Relation.id = AnnotationsInterRelation.relation_id'
                          )
                    ),
              ),
              'group' => array('AnnotationsInterRelation.relation_id'), //fields to GROUP BY
        ));
        /* ================================ */
        /*         Relations chart          */
        /* ================================ */
        /* PieChart chart */
        App::import('Vendor', '/Charts/PieChart');
        $chart = new pieChart();
        $data = array();
        $i = 0;
        $totalRelations = 0;
        foreach ($relationsByType as $type) {
            $totalRelations += $type["AnnotationsInterRelation"]["total"];
            $data[$i] = array(
                  'category' => $type["Relation"]['name'],
                  'column-1' => $type["AnnotationsInterRelation"]["total"]
            );
            $chart->configuration['colors'][$i] = $type["Relation"]['colour'];
            $i++;
        }
        $chart->configuration["titles"] = array(array("text" => "% of annotations by type",
                    "size" => 16, 'color' => '#000'));
        $chart->configuration["dataProvider"] = $data;
        $this->set('relationsDistribution', json_encode($chart->configuration));
        $this->set('totalRelations', $totalRelations);
        /* ==================================== */
        /*             projects networks        */
        /* ==================================== */
        /*
          $contain = array(
          'Document' => array('id', 'tittle')
          );
          $documents = $this->Project->Document->find('list', array(
          'joins' => array(
          array(
          'table' => 'documents_projects',
          'alias' => 'DocumentsProject',
          'type' => 'Inner',
          'conditions' => array(
          'DocumentsProject.document_id=Document.id'
          )
          )),
          'conditions' => array('project_id' => $id)));
          $this->set('documents', $documents); */
        $this->Session->write('comesFrom', array(
              'controller' => 'Projects',
              'action' => 'view',
              $this->Project->id
        ));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Project->create();
            if ($this->Project->save($this->request->data)) {
                $this->Session->setFlash(__('Project has been saved'), 'success');
                $this->redirect(array(
                      'action' => 'index'
                ));
            } //$this->Project->save($this->request->data)
            else {
                $this->Session->setFlash(__('Project could not be saved. Please, try again.'));
            }
        } //$this->request->is('post')
        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array();
        $userConditions = array(
              'group_id' => 2
        );
        if ($deleteCascade) {
            $conditions = array(
                  'title !=' => 'Removing...'
            );
            $userConditions = array(
                  'username !=' => 'Removing...',
                  'group_id' => 2
            );
        }
        $documents = $this->Project->Document->find('list', array(
              'conditions' => $conditions
        ));
        $users = $this->Project->User->find('list', array(
              'conditions' => $userConditions
        ));
        $this->set(compact('documents', 'users'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->Project->id = $id;
        $this->Round = $this->Project->Round;
        $this->UsersRound = $this->Round->UsersRound;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->Relation = $this->Project->Relation;
        $this->AnnotationsInterRelations = $this->Relation->AnnotationsInterRelations;
        $project = $this->Project->find('first', array(
              "recursive" => -1,
              'contain' => array(
                    'User' => array(
                          'id',
                          'username',
                          'surname',
                          'email'
                    )
              ),
              'conditions' => array(
                    'Project.id' => $id
              )
        ));
        $deleteCascade = Configure::read('deleteCascade');
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Project->save($this->request->data)) {
                $redirect = array("controller" => "Projects", "action" => "view",
                      $id);
                if ($deleteCascade) {
                    $this->Session->setFlash(__('Project has been saved'), 'success');
                    $this->backGround($redirect);
                }
                $rounds = $this->Round->find('list', array(
                      'fields' => 'id',
                      'recursive' => -1,
                      'conditions' => array(
                            'Round.project_id' => $this->Project->id
                      )
                ));
                if (isset($project["Project"]["relation_level"]) && $project["Project"]["relation_level"] != $this->request->data['Project']['relation_level']) {
                    $db = $this->Project->getDataSource();
                    $db->begin();
                    $ids = $this->Relation->find("list", array(
                          'fields' => 'id',
                          "conditions" => array(
                                "project_id" => $id
                          ),
                          "recursive" => -1)
                    );
                    $this->AnnotationsInterRelations->deleteAll(array("relation_id" => $ids), Configure::read("deleteCascade"));
                    $db->commit();
                }
                $this->UsersRound->deleteAll(array(
                      'AND' => array(
                            'UsersRound.round_id' => $rounds,
                      ),
                      'NOT' => array(
                            'UsersRound.user_id' => $this->request->data['User']['User']
                      )
                    ), $deleteCascade);
                $this->Session->setFlash(__('Project has been saved'), 'success');
                $this->redirect($redirect);
            } //$this->Project->save($this->request->data)
            else {
                $this->Session->setFlash(__('Project could not be saved. Please, try again.'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->request->data = $project;
        }
        $conditions = array();
        $userConditions = array(
              'group_id' => 2
        );
        if ($deleteCascade) {
            $conditions = array(
                  'title !=' => 'Removing...'
            );
            $userConditions = array(
                  'username !=' => 'Removing...',
                  'group_id' => 2
            );
        }
        $users = $this->Project->User->find('list', array(
              "recursive" => -1,
              'conditions' => $userConditions
        ));
        $project_id = $id;
        $this->set(compact('documents', 'users', 'project_id'));
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
        session_write_close();
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } //!$this->request->is('post')
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        } //!$this->Project->exists()
        $redirect = $this->Session->read('redirect');
        $deleteCascade = Configure::read('deleteCascade');
        if ($deleteCascade) {
            $data = array(
                  'title' => 'Removing...'
            );
            if ($this->Project->save($data, false)) {
                $this->Session->setFlash('We are removing this project. Please be patient', 'information');
                $this->backGround(array(
                      'controller' => 'projects',
                      'action' => 'index'
                ));
                $this->Project->delete($id, $deleteCascade);
            }
        } else {
            $db = $this->Project->getDataSource();
            $conditions = array('Project.id' => $id);
            if ($db->delete($this->Project, $conditions)) {
                $this->Session->setFlash(__('Project has been deleted'), 'success');
                $this->redirect(array(
                      'controller' => 'projects',
                      'action' => 'index'
                ));
            } //$this->Project->delete()
        }
        $this->Session->setFlash(__("Project hasn't been deleted"));
        $this->redirect(array(
              'action' => 'index'));
    }

    /*     * *************
      Este metodo ha sido deprecated devido a que borrar muchos proyectos a la vez puede ser demasiado costoso
     * ************** */
    /* public function deleteAll($id = null) {
      $this->autoRender = false;
      if (!$this->request->is('post')) {
      throw new MethodNotAllowedException();
      } //!$this->request->is('post')
      else {
      $ids = json_decode($this->request->data['allProjects']);
      $this->Project->deleteAll(array(
      'Project.id' => $ids
      ), true);
      $this->Session->setFlash(__('Project selected are deleted'));
      $redirect = $this->Session->read('redirect');
      $this->redirect($redirect);
      }
      } */

    /**
     * msort method
     * @param array $array
     * @param string $key
     * @param string $sort_flags
     * @return Array
     */
    function msort($array, $key, $sort_flags = SORT_REGULAR) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    /**
     * confrontationSettingDual method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function confrontationSettingDual($id = null) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            ////$this->Session->write('start', microtime(true));
            $this->render(false);
            $this->Session->delete('confrontationResult');
            $this->Session->delete('confrontationDualResult');
            $this->request->data['redirect'] = 'confrontationSettingDual';
            $this->Session->write('confrontationPostedData', $this->request->data);
            //se mandan los dos igual debido a que confrontation dual se puede llegar desde dos funciones distintas
            $this->Session->write('confrontationSettingsData', $this->request->data);
            $this->redirect(array(
                  'action' => 'confrontationDual',
                  true
            ));
        } //$this->request->is('post') || $this->request->is('put')
        $cond = array(
              'project_id' => $this->Project->id
        );
        $types = $this->Project->Type->find('list', array(
              'conditions' => $cond
        ));
        $this->set('types', $types);
        $this->getConfrontationData($users, $rounds, $this->Project->id);
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
    }

    /**
     * confrontationDual method
     *
     * @throws NotFoundException
     * @return void
     */
    public function confrontationDual($comesSettings = false) {
        // top of file
        $time_start = microtime(true);
        $this->Session->write('start', $time_start);
        $data = $this->Session->read('confrontationPostedData');
        $settingData = $this->Session->read('confrontationSettingsData');
        $this->Project->id = $data['Project']['id'];
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->autoRender = false;
                //machacamos la cache de busqueda de annoataciones los datos por si el usuaruario quiere mirar nones y hits
                //para entender mejor ver la funcion listAnnotation,donde se implementa una pequenha cache con la variable annotations
                $this->Session->write('SettingsAnnotation', $this->request->data);
                if (isset($this->request->data['Annotation']['typeNone']))
                    $action = 'listAnnotationsNone';
                else
                    $action = 'listAnnotationsHits';
                $this->redirect(array(
                      'controller' => 'annotations',
                      'action' => $action
                ));
            } //$this->request->is('post') || $this->request->is('put')
            else {
                if (empty($data['Project']['type']) && !empty($settingData['Project']['type'])) {
                    $data['Project']['type'] = $settingData['Project']['type'];
                } else {
                    $data['Project']['type'] = $data['Project']['type'];
                }
                if (!($data['user_A'] == $data['user_B'] && $data['round_A'] == $data['round_B']) && !empty($data['Project']['type'])) {
                    $sendMail = false;
                    ignore_user_abort(true);
                    if (isset($data['Project']['sendEmail']) && $data['Project']['sendEmail'] == true) {
                        $userInSession = $this->Project->User->find('first', array(
                              'conditions' => array(
                                    'id' => $this->Auth->user('id')
                              ),
                              'recursive' => -1
                        ));
                        $sendMail = true;
                        $userMail = $userInSession['User']['email'];
                        $this->Session->setFlash("we are getting the data, be patient, we will send you an email (To:$userMail ) when operations are completed.", 'information');
                        $this->autoRender = false;
                        echo "MAIL";
                        unset($data['Project']['sendEmail']);
                        session_write_close();
                        header("Content-Encoding: none");
                        header("Content-Length: " . ob_get_length());
                        header("Connection: close");
                        ob_end_flush();
                        flush();
                    }
                    if (empty($data['Project']['margin']))
                        $data['Project']['margin'] = 0;
                    $differentTypes = $this->Project->Type->find('all', array(
                          'fields' => array(
                                'id',
                                'name',
                                'colour'
                          ),
                          'conditions' => array(
                                'Type.id' => $settingData['Project']['type']
                          ),
                          'order' => 'Type.name ASC',
                          'recursive' => -1
                    ));
                    $types = $settingData['Project']['type'];
                    $round_id_A = $data['round_A'];
                    $round_id_B = $data['round_B'];
                    $user_id_A = $data['user_A'];
                    $user_id_B = $data['user_B'];
                    $cond = array(
                          'id' => $round_id_A
                    );
                    $round_A = $this->Project->Round->find('first', array(
                          'conditions' => $cond,
                          'recursive' => -1
                    ));
                    $cond = array(
                          'id' => $round_id_B
                    );
                    $round_B = $this->Project->Round->find('first', array(
                          'conditions' => $cond,
                          'recursive' => -1
                    ));
                    $cond = array(
                          'id' => $user_id_A
                    );
                    $user_A = $this->Project->User->find('first', array(
                          'conditions' => $cond,
                          'recursive' => -1
                    ));
                    $cond = array(
                          'id' => $user_id_B
                    );
                    $user_B = $this->Project->User->find('first', array(
                          'conditions' => $cond,
                          'recursive' => -1
                    ));
                    $field_A = $user_A['User']['username'] . ' in round ' . $round_A['Round']['title'];
                    $field_B = $user_B['User']['username'] . ' in round ' . $round_B['Round']['title'];
                    //si el usuario es el mismo marca que estamos haciendo las cosas por round
                    $byRound = FALSE;
                    if ($user_id_A != $user_id_B) {
                        $group_A = $user_id_A;
                        $group_B = $user_id_B;
                    }
                    //$user_id_A != $user_id_B
                    else {
                        $group_A = $round_id_A;
                        $group_B = $round_id_B;
                        $byRound = TRUE;
                    }
                    $confrontationDualResult = $this->Session->read('confrontationDualResult');
                    if (empty($confrontationDualResult)) {
                        $relationTypes = array();
                        $noneRelation_aux = array();
                        $noneRelation_B = array();
                        $noneRelation_A = array();
                        $tam = sizeof($types);
                        $margin = $data['Project']['margin'];
                        $hitsAcumulate = 0;
                        $noneHits = 0;
                        foreach ($types as $type) {
                            $noneRelation_aux[$type] = 0;
                        }
                        $scriptTimeLimit = Configure::read('scriptTimeLimit');
                        set_time_limit($scriptTimeLimit);
                        error_reporting(E_ERROR | E_PARSE);
                        $progress = 0;
                        $_SESSION['progress'] = 0;
                        if (!$sendMail) {
                            session_write_close();
                            header("Content-Encoding: none");
                            header("Content-Length: " . 0);
                            header("Connection: close");
                            ob_end_flush();
                            flush();
                        }
                        $db = $this->Project->getDataSource();
                        //el array que viene de la BD trae como claves los ids de los tipos
                        for ($i = 0; $i < $tam; $i++) {
                            $progress = round(($i / $tam) * 90);
                            //las siguientes lineas son debidas a causa de la barra de progreso
                            $_SESSION['progress'] = $progress;
                            session_write_close();
                            session_start();
                            $hitsAcumulate = 0;
                            for ($j = 0; $j < $tam; $j++) {
                                $hits = $db->fetchAll("select count(a.id) as hits from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
							where a.round_id =:round_a and b.round_id =:round_b and a.user_id =:user_a and b.user_id =:user_b 
							and a.document_id=b.document_id  and a.type_id = :type_a  and b.type_id = :type_b
							and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                                      'round_a' => $round_id_A,
                                      'round_b' => $round_id_B,
                                      'user_a' => $user_id_A,
                                      'user_b' => $user_id_B,
                                      'type_a' => $types[$i],
                                      'type_b' => $types[$j],
                                      'margin' => $margin
                                ));
                                $hits = $hits[0][0]['hits'];
                                $hitsAcumulate += $hits;
                                if ($hits != 0) {
                                    //													
                                    array_push($relationTypes, array(
                                          'type_fil' => $types[$j],
                                          'group_id' => $group_A,
                                          'type_col' => $types[$i],
                                          'Hits' => (string) $hits
                                    ));
                                }
                                //vamos sumando los hits para despues restarselos al numero total y tener el numero de desaciertos
                                $noneRelation_aux[$types[$j]] = $noneRelation_aux[$types[$j]] + $hits;
                            }
                            $total = $this->Project->Type->Annotation->find('count', array(
                                  'recursive' => -1,
                                  'conditions' => array(
                                        'user_id' => $user_id_A,
                                        'round_id' => $round_id_A,
                                        'type_id' => $types[$i]
                                  )
                            ));
                            $noneHits = $total - $hitsAcumulate;
                            if ($noneHits > 0) {
                                //se pone $round_id_A no por que sean los errores de A, si no por que se deben meter en las columnas de A
                                array_push($noneRelation_B, array(
                                      'group_id' => $group_A,
                                      'type_id' => $types[$i],
                                      'Discordances' => (string) $noneHits
                                ));
                            }
                            /*
                              else if($noneHits < 0)
                              {
                              $noneRelation_aux[$types[$i]] = $noneRelation_aux[$types[$i]] + $noneHits;
                              } */
                        }
                        $i = 0;
                        $tam = sizeof($noneRelation_aux);
                        foreach ($noneRelation_aux as $key => $val) {
                            $progress = ceil(90 + ($i / $tam) * 10);
                            $i++;
                            $_SESSION['progress'] = $progress;
                            session_write_close();
                            session_start();
                            $total = $this->Project->Type->Annotation->find('count', array(
                                  'recursive' => -1,
                                  'conditions' => array(
                                        'user_id' => $user_id_B,
                                        'round_id' => $round_id_B,
                                        'type_id' => $key
                                  )
                            ));
                            $noneHits = $total - $val;
                            if ($noneHits > 0) {
                                //se pone $round_id_A no por que sean los errores de A si no por que se debenmeter en las columnas de A
                                array_push($noneRelation_A, array(
                                      'group_id' => $group_B,
                                      'type_id' => $key,
                                      'Discordances' => (string) $noneHits
                                ));
                            }
                        }
                        $relationTypes = Set::combine($relationTypes, '{n}.type_fil', '{n}.Hits', '{n}.type_col');
                        $noneRelation_aux = array_merge($noneRelation_A, $noneRelation_B);
                        $this->Session->write('confrontationDualResult', array(
                              'noneRelation' => $noneRelation_aux,
                              'relationTypes' => $relationTypes
                        ));
                        $_SESSION['progress'] = 100;
                        session_write_close();
                        session_start();
                    } else {
                        $relationTypes = $confrontationDualResult['relationTypes'];
                        $noneRelation_aux = $confrontationDualResult['noneRelation'];
                    }
                    if ($sendMail) {
                        $this->autoRender = false;
                        $this->Session->write('confrontationPostedData', $data);
                        $userMail = $userInSession['User']['email'];
                        $userName = $userInSession['User']['full_name'];
                        $this->downloadConfrontationData("confrontationDual", array(
                              'username' => $userName,
                              'email' => $userMail
                        ));
                    } else {
                        $this->set('differentTypes', $differentTypes);
                        $this->set('id_group_files', $group_B);
                        $this->set('id_group_cols', $group_A);

                        $this->set('colName', $field_B);
                        $this->set('rowName', $field_A);
                        $this->set('byRound', $byRound);
                        $this->set('noneRelation', $noneRelation_aux);
                        $this->set('relationTypes', $relationTypes);
                        $this->set('project_id', $this->Project->id);
                        $this->set('redirect', $data['redirect']);
                        $this->Session->write('progress', 100);
                    }
                } //isset($data['round_A']) && isset($data['round_A']) && isset($data['user_B'][0]) && isset($data['round_B'])
                else {
                    $this->autoRender = false;
                    if ($comesSettings == true) {
                        $this->Session->setFlash(__('You must choose different rounds or different users and at least one type of annotation'));
                    } else {
                        $this->Session->setFlash(__('Incorrect operation with these data'));
                    }
                    if ($this->request->is('ajax')) {
                        echo "ERROR";
                    } else {
                        $this->redirect(array(
                              'action' => 'confrontationSettingDual',
                              $this->Project->id
                        ));
                    }
                }
            }
        }
    }

    /**
     * confrontationSettingMultiRound method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function confrontationSettingMultiRound($id = null) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            $this->render(false);
            $this->Session->delete('confrontationResult');
            $this->Session->delete('confrontationDualResult');
            $this->request->data['redirect'] = 'confrontationMultiRound';
            $this->Session->write('confrontationSettingsData', $this->request->data);
            $this->redirect(array(
                  'action' => 'confrontationMultiRound'
            ));
        } //$this->request->is('post') || $this->request->is('put')
        $this->getConfrontationData($users, $rounds, $this->Project->id);
        $cond = array(
              'project_id' => $this->Project->id
        );
        $types = $this->Project->Type->find('list', array(
              'conditions' => $cond
        ));
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
        $this->set('types', $types);
    }

    /**
     * confrontationMultiRound method
     * @throws NotFoundException
     * @return void
     */
    public function confrontationMultiRound() {
        // top of file
        $this->User = $this->Project->User;
        $this->Round = $this->Project->Round;
        $time_start = microtime(true);
        $this->Session->write('start', $time_start);
        $data = $this->Session->read('confrontationSettingsData');
        $this->Project->id = $data['Project']['id'];
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->render(false);
                $this->request->data['redirect'] = 'confrontationMultiRound';
                $this->Session->delete('confrontationDualResult');
                $this->Session->write('confrontationPostedData', $this->request->data);
                $this->redirect(array(
                      'action' => 'confrontationDual'
                ));
            } //$this->request->is('post') || $this->request->is('put')
            else {
                if (sizeof($data['round']) > 1 && sizeof($data['Project']['type']) > 0) {
                    if (empty($data['Project']['margin']))
                        $data['Project']['margin'] = 0;
                    $sendMail = false;
                    ignore_user_abort(true);
                    if (isset($data['Project']['sendEmail']) && $data['Project']['sendEmail'] == true) {
                        $userInSession = $this->Project->User->find('first', array(
                              'conditions' => array(
                                    'id' => $this->Auth->user('id')
                              ),
                              'recursive' => -1
                        ));
                        $sendMail = true;
                        $userMail = $userInSession['User']['email'];
                        $this->Session->setFlash("we are getting the data, be patient, we will send you an email (To:$userMail ) when operations are completed.", 'information');
                        $this->autoRender = false;
                        echo "MAIL";
                        unset($data['Project']['sendEmail']);
                        session_write_close();
                        header("Content-Encoding: none");
                        header("Content-Length: " . ob_get_length());
                        header("Connection: close");
                        ob_end_flush();
                        flush();
                    }
                    $differentRounds = $this->Project->Round->find('all', array(
                          'fields' => 'Round.* ,Round.title as name',
                          'conditions' => array(
                                'Round.project_id' => $this->Project->id,
                                'Round.id' => $data['round']
                          ),
                          'order' => 'Round.id ASC',
                          'recursive' => -1
                    ));
                    $margin = $data['Project']['margin'];
                    $rounds = $data['round'];
                    $user = $data['user'];
                    $this->User->id = $user;
                    $types_id = $data['Project']['type'];
                    $user_name = $this->User->field('full_name');
                    $confrontationResult = $this->Session->read('confrontationResult');
                    if (empty($confrontationResult)) {
                        $relationRounds = array();
                        $tam = sizeof($rounds);
                        $types = implode(",", $types_id);
                        $db = $this->Project->getDataSource();
                        $scriptTimeLimit = Configure::read('scriptTimeLimit');
                        set_time_limit($scriptTimeLimit);
                        error_reporting(E_ERROR | E_PARSE);
                        $progress = 0;
                        $_SESSION['progress'] = 0;
                        if (!$sendMail) {
                            session_write_close();
                            header("Content-Encoding: none");
                            header("Content-Length: " . 0);
                            header("Connection: close");
                            ob_end_flush();
                            flush();
                        }
                        $db = $this->Project->getDataSource();
                        //el array que viene de la BD trae como claves los ids de los tipos
                        for ($i = 0; $i < $tam - 1; $i++) {
                            $progress = round(($i / ($tam - 1)) * 90);
                            //las siguientes lineas son debidas a causa de la barra de progreso
                            $_SESSION['progress'] = $progress;
                            session_write_close();
                            session_start();
                            for ($j = $i + 1; $j < $tam; $j++) {
                                $hits = $db->fetchAll("select count(a.id) as hits from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
								where a.round_id =:round_a and b.round_id =:round_b and a.user_id =:user_a and b.user_id =:user_b 
								and a.document_id=b.document_id  and a.type_id=b.type_id and a.type_id in ($types) 
								and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                                      'round_a' => $rounds[$i],
                                      'round_b' => $rounds[$j],
                                      'user_a' => $user,
                                      'user_b' => $user,
                                      'margin' => $margin
                                ));
                                $hits = $hits[0][0]['hits'];
                                array_push($relationRounds, array(
                                      'fila' => $rounds[$i],
                                      'columna' => $rounds[$j],
                                      'hits' => $hits
                                ));
                            }
                        }
                        $this->Session->write('confrontationResult', $relationRounds);
                        $_SESSION['progress'] = 100;
                        session_write_close();
                        session_start();
                    } else {
                        $relationRounds = $confrontationResult;
                    }
                    if ($sendMail) {
                        $this->autoRender = false;
                        $this->Session->write('confrontationSettingsData', $data);
                        $userMail = $userInSession['User']['email'];
                        $userName = $userInSession['User']['full_name'];
                        $this->downloadConfrontationData("confrontationMultiRound", array(
                              'username' => $userName,
                              'email' => $userMail
                        ));
                    } else {
                        $cond = array(
                              'Annotation.round_id' => $data['round'],
                              'Annotation.user_id' => $data['user']
                        );
                        $cont = $this->Project->Type->Annotation->find('count', array(
                              'recursive' => -1,
                              'conditions' => $cond
                        ));
                        $this->Round->virtualFields["total"] = "COUNT(Annotation.id)";
                        $annotationsSummary = $this->Round->find('list', array(
                              'recursive' => -1, //int
                              //array of field names
                              'fields' => array('Round.title', 'total'),
                              'conditions' => array(
                                    'Round.id' => $data['round'],
                              ), //array of conditions
                              'joins' => array(
                                    array(
                                          'table' => 'annotations',
                                          'alias' => 'Annotation',
                                          'type' => 'INNER',
                                          'conditions' => array(
                                                'Annotation.round_id = Round.id',
                                                'Annotation.user_id' => $data['user'],
                                          )
                                    ),
                              ),
                              'group' => array('Round.title')
                        ));
                        $relationRounds = Set::combine($relationRounds, '{n}.columna', '{n}.hits', '{n}.fila');
                        $this->set('annotationsSummary', $annotationsSummary);
                        $this->set('numAnnotations', $cont);
                        $this->set('differentElements', $differentRounds);
                        $this->set('elementName', 'Round');
                        $this->set('margin', $margin);
                        $this->set('user', $user);
                        $this->set('project_id', $this->Project->id);
                        $this->set('user_name', $user_name);
                        $this->set('relationElements', $relationRounds);
                        $this->set('redirect', 'confrontationSettingMultiRound');
                        $this->render('confrontationMulti');
                    }
                } //sizeof($data['round']) > 1
                else {
                    $this->Session->setFlash(__('You must choose more than one round and at least one type of annotation'));
                    $this->autoRender = false;
                    if ($this->request->is('ajax')) {
                        echo "ERROR";
                    } else {
                        $this->redirect(array(
                              'action' => 'confrontationSettingMultiRound',
                              $this->Project->id
                        ));
                    }
                }
            }
        }
    }

    /**
     * confrontationSettingMultiUser method
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function confrontationSettingMultiUser($id = null) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            $this->render(false);
            $this->Session->delete('confrontationResult');
            $this->Session->delete('confrontationDualResult');
            $this->request->data['redirect'] = 'confrontationMultiUser';
            $this->Session->write('confrontationSettingsData', $this->request->data);
            $this->redirect(array(
                  'action' => 'confrontationMultiUser'
            ));
        } //$this->request->is('post') || $this->request->is('put')
        $this->getConfrontationData($users, $rounds, $this->Project->id);
        $cond = array(
              'project_id' => $this->Project->id
        );
        $types = $this->Project->Type->find('list', array(
              'conditions' => $cond
        ));
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
        $this->set('types', $types);
    }

    /**
     * confrontationMultiUser method
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function confrontationMultiUser() {
        // top of file
        $this->Session->write('start', microtime(true));
        $this->User = $this->Project->User;
        $data = $this->Session->read('confrontationSettingsData');
        $this->Project->id = $data['Project']['id'];
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->render(false);
                $this->request->data['redirect'] = 'confrontationMultiUser';
                $this->Session->delete('confrontationDualResult');
                $this->Session->write('confrontationPostedData', $this->request->data);
                $this->redirect(array(
                      'action' => 'confrontationDual'
                ));
            } //$this->request->is('post') || $this->request->is('put')
            if (sizeof($data['user']) > 1 && sizeof($data['Project']['type']) > 0) {
                if (empty($data['Project']['margin']))
                    $data['Project']['margin'] = 0;
                $sendMail = false;
                ignore_user_abort(true);
                if (isset($data['Project']['sendEmail']) && $data['Project']['sendEmail'] == true) {
                    $userInSession = $this->Project->User->find('first', array(
                          'conditions' => array(
                                'id' => $this->Auth->user('id')
                          ),
                          'recursive' => -1
                    ));
                    $sendMail = true;
                    $userMail = $userInSession['User']['email'];
                    $this->Session->setFlash("we are getting the data, be patient, we will send you an email (To:$userMail ) when operations are completed.", 'information');
                    $this->autoRender = false;
                    echo "MAIL";
                    unset($data['Project']['sendEmail']);
                    session_write_close();
                    header("Content-Encoding: none");
                    header("Content-Length: " . ob_get_length());
                    header("Connection: close");
                    ob_end_flush();
                    flush();
                }
                $differentUsers = $this->Project->User->find('all', array(
                      'fields' => array('id', 'User.username as name'),
                      'conditions' => array(
                            'User.id' => $data['user']
                      ),
                      'order' => 'User.id ASC',
                      'recursive' => -1
                ));
                $margin = $data['Project']['margin'];
                if ($data['Project']['margin'] > 100)
                    $margin = 100;
                $rounds = $data['round'];
                $users = $data['user'];
                $types_id = $data['Project']['type'];
                $round_name = $data['round_name_A'];
                $confrontationResult = $this->Session->read('confrontationResult');
                if (empty($confrontationResult)) {
                    $relationUsers = array();
                    $tam = sizeof($users);
                    $db = $this->Project->getDataSource();
                    $types = implode(",", $types_id);
                    $scriptTimeLimit = Configure::read('scriptTimeLimit');
                    set_time_limit($scriptTimeLimit);
                    error_reporting(E_ERROR | E_PARSE);
                    $progress = 0;
                    $_SESSION['progress'] = 0;
                    if (!$sendMail) {
                        session_write_close();
                        header("Content-Encoding: none");
                        header("Content-Length: " . 0);
                        header("Connection: close");
                        ob_end_flush();
                        flush();
                    }
                    //el array que viene de la BD trae como claves los ids de los tipos
                    for ($i = 0; $i < $tam - 1; $i++) {
                        $progress = round(($i / $tam - 1) * 90);
                        //las siguientes lineas son debidas a causa de la barra de progreso
                        $_SESSION['progress'] = $progress;
                        session_write_close();
                        session_start();
                        for ($j = $i + 1; $j < $tam; $j++) {
                            $hits = $db->fetchAll("select count(a.id) as hits from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
							where a.round_id =:round_a and b.round_id =:round_b and a.user_id =:user_a and b.user_id =:user_b 
							and a.document_id=b.document_id  and a.type_id=b.type_id and a.type_id in ($types) 
							and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                                  'round_a' => $rounds,
                                  'round_b' => $rounds,
                                  'user_a' => $users[$i],
                                  'user_b' => $users[$j],
                                  'margin' => $margin
                            ));
                            array_push($relationUsers, array(
                                  'fila' => $users[$i],
                                  'columna' => $users[$j],
                                  'hits' => $hits[0][0]['hits']
                            ));
                        }
                    }
                    $this->Session->write('confrontationResult', $relationUsers);
                    $_SESSION['progress'] = 100;
                    session_write_close();
                    session_start();
                } else {
                    $relationUsers = $confrontationResult;
                }
                if ($sendMail) {
                    $this->autoRender = false;
                    $this->Session->write('confrontationSettingsData', $data);
                    $userMail = $userInSession['User']['email'];
                    $userName = $userInSession['User']['full_name'];
                    $this->downloadConfrontationData("confrontationMultiUser", array(
                          'username' => $userName,
                          'email' => $userMail
                    ));
                } else {
                    $cond = array(
                          'Annotation.round_id' => $data['round'],
                          'Annotation.user_id' => $data['user']
                    );
                    $cont = $this->Project->Type->Annotation->find('count', array(
                          'conditions' => $cond
                    ));
                    $this->User->virtualFields["total"] = "COUNT(Annotation.id)";
                    $annotationsSummary = $this->User->find('list', array(
                          'recursive' => -1, //int
                          //array of field names
                          'fields' => array('User.full_name', 'total'),
                          'conditions' => array(
                                'User.id' => $data['user'],
                          ), //array of conditions
                          'joins' => array(
                                array(
                                      'table' => 'annotations',
                                      'alias' => 'Annotation',
                                      'type' => 'INNER',
                                      'conditions' => array(
                                            'Annotation.user_id = User.id',
                                            'Annotation.round_id' => $data['round'],
                                      )
                                ),
                          ),
                          'group' => array('User.full_name')
                    ));
                    $relationUsers = Set::combine($relationUsers, '{n}.columna', '{n}.hits', '{n}.fila');
                    $this->set('annotationsSummary', $annotationsSummary);
                    $this->set('numAnnotations', $cont);
                    $this->set('differentElements', $differentUsers);
                    $this->set('elementName', 'User');
                    $this->set('margin', $margin);
                    $this->set('round', $rounds);
                    $this->set('project_id', $this->Project->id);
                    $this->set('round_name', $round_name);
                    $this->set('relationElements', $relationUsers);
                    $this->set('redirect', 'confrontationSettingMultiUser');
                    $this->render('confrontationMulti');
                }
            } //sizeof($data['user']) > 1
            else {
                $this->Session->setFlash(__('You must choose more than one User at least one type of annotation'));
                $this->autoRender = false;
                if ($this->request->is('ajax')) {
                    echo "ERROR";
                } else {
                    $this->redirect(array(
                          'action' => 'confrontationSettingMultiUser',
                          $this->Project->id
                    ));
                }
            }
        }
    }

    public function confrontationSettingFscoreUsers($id = null) {
        $this->Session->write('start', microtime(true));
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            //place this before any script you want to calculate time
            ////$this->Session->write('start', microtime(true));
            $this->render(false);
            $this->request->data['redirect'] = 'confrontationFscoreUsers';
            $this->request->data['user'] = array(
                  $this->request->data['user_A'][0],
                  $this->request->data['user_B'][0]
            );
            $this->Session->write('confrontationSettingsData', $this->request->data);
            $this->Session->delete('confrontationResult');
            $this->redirect(array(
                  'action' => 'confrontationFscoreUsers'
            ));
        } //$this->request->is('post') || $this->request->is('put')
        $cond = array(
              'project_id' => $this->Project->id
        );
        $users = $this->Project->ProjectsUser->find('all', array(
              'fields' => 'user_id',
              'conditions' => $cond,
              'recursive' => -1
        ));
        $users = $this->flatten($users);
        $users = $this->Project->User->find('list', array(
              'conditions' => array(
                    'id' => $users
              )
        ));
        $cond = array(
              'project_id' => $this->Project->id,
              'ends_in_date IS NOT NULL',
              'title!=\'Removing...\''
        );
        $rounds = $this->Project->Round->find('list', array(
              'conditions' => $cond
        ));
        if (sizeof($users) < 2 || sizeof($rounds) < 1) {
            $this->Session->setFlash(__('This project must have at least two users and one round for this functionality'));
            $this->redirect(array(
                  'action' => 'view',
                  $id
            ));
        }
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
    }

    /*
     * Este metodo esta deprecado dado que tarda excesivamente con gran numero de anotaciones
     *
     * */

    public function confrontationFscoreUsers() {
        // top of file
        $time_start = microtime(true);
        $this->Session->write('start', $time_start);
        $data = $this->Session->read('confrontationSettingsData');
        $this->Project->id = $data['Project']['id'];
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->request->data['redirect'] = 'confrontationFscoreUsers';
                $this->Session->write('confrontationSettingsData', $this->request->data);
                $this->redirect(array(
                      'controller' => 'annotations',
                      'action' => 'index'
                ));
            } //$this->request->is('post') || $this->request->is('put')
            else {
                if (isset($data['user_A']) && isset($data['user_B']) && !empty($data['round']) && $data['user_A'] != $data['user_B']) {
                    if (empty($data['Project']['margin']))
                        $data['Project']['margin'] = 0;
                    $sendMail = false;
                    ignore_user_abort(true);
                    if (isset($data['Project']['sendEmail']) && $data['Project']['sendEmail'] == true) {
                        $userInSession = $this->Project->User->find('first', array(
                              'conditions' => array(
                                    'id' => $this->Auth->user('id')
                              ),
                              'recursive' => -1
                        ));
                        $sendMail = true;
                        $userMail = $userInSession['User']['email'];
                        $this->Session->setFlash("we are getting the data, be patient, we will send you an email (To:$userMail ) when operations are completed.", 'information');
                        $this->autoRender = false;
                        echo "MAIL";
                        unset($data['Project']['sendEmail']);
                        session_write_close();
                        header("Content-Encoding: none");
                        header("Content-Length: " . ob_get_length());
                        header("Connection: close");
                        ob_end_flush();
                        flush();
                    }
                    //hallamos el numero total de typos en el proeycto y recogemos su id
                    $types_id = $this->Project->Type->find('all', array(
                          'fields' => 'id',
                          'conditions' => array(
                                'Type.project_id' => $data['Project']['id']
                          ),
                          'order' => 'Type.id ASC',
                          'recursive' => -1
                    ));
                    $types_id = $this->flatten($types_id);
                    $margin = $data['Project']['margin'];
                    if ($data['Project']['margin'] > 100)
                        $margin = 100;
                    //miramos si esta guardado en sesion 
                    $confrontationResult = $this->Session->read('confrontationResult');
                    if (empty($confrontationResult)) {
                        $rounds = $data['round'];
                        $f_scores = array();
                        $tamRounds = sizeof($rounds);
                        $tamTypes = sizeof($types_id);
                        $user_A = $data['user_A'];
                        $user_B = $data['user_B'];
                        $scriptTimeLimit = Configure::read('scriptTimeLimit');
                        set_time_limit($scriptTimeLimit);
                        error_reporting(E_ERROR | E_PARSE);
                        $progress = 0;
                        $_SESSION['progress'] = 0;
                        if (!$sendMail) {
                            session_write_close();
                            header("Content-Encoding: none");
                            header("Content-Length: " . 0);
                            header("Connection: close");
                            ob_end_flush();
                            flush();
                        }
                        $db = $this->Project->getDataSource();
                        //el array que viene de la BD trae como claves los ids de los tipos
                        for ($i = 0; $i < $tamRounds; $i++) {
                            $progress = round(($i / $tamRounds) * 90);
                            //las siguientes lineas son debidas a causa de la barra de progreso
                            $_SESSION['progress'] = $progress;
                            session_write_close();
                            session_start();
                            $f_scores[$rounds[$i]] = array();
                            for ($j = 0; $j < $tamTypes; $j++) {
                                //hallamos el numero total de las anotaciones de cada usuario de un tipo para un round
                                //y a continuacion hallamos las anotaciones comunes a ambos
                                $hits = 0;
                                $cond = array(
                                      'Annotation.round_id' => $rounds[$i],
                                      'Annotation.user_id' => $user_A,
                                      'Annotation.type_id' => $types_id[$j]
                                );
                                $num_set_A = $this->Project->Type->Annotation->find('count', array(
                                      'conditions' => $cond,
                                      'recursive' => -1
                                ));
                                $cond = array(
                                      'Annotation.round_id' => $rounds[$i],
                                      'Annotation.user_id' => $user_B,
                                      'Annotation.type_id' => $types_id[$j]
                                );
                                $num_set_B = $this->Project->Type->Annotation->find('count', array(
                                      'conditions' => $cond,
                                      'recursive' => -1
                                ));
                                if ($num_set_A > 0 && $num_set_B > 0) {
                                    $hits = $db->fetchAll("select count(a.id) as hits  from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
									where a.round_id =:round and b.round_id = a.round_id and a.user_id =:user_a and b.user_id =:user_b 
									and a.document_id=b.document_id  and a.type_id=b.type_id and a.type_id =:type_id 
									and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                                          'round' => $rounds[$i],
                                          'user_a' => $user_A,
                                          'user_b' => $user_B,
                                          'margin' => $margin,
                                          'type_id' => $types_id[$j]
                                    ));
                                }
                                if ($hits > 0) {
                                    $precision = $hits[0][0]['hits'] / $num_set_A;
                                    $recall = $hits[0][0]['hits'] / $num_set_B;
                                    $f_score = $precision * $recall;
                                    $f_score = 100 * 2 * $f_score / ($precision + $recall);
                                } else {
                                    $f_score = 0;
                                }
                                $f_scores[$rounds[$i]][$types_id[$j]] = array(
                                      'f-score' => round($f_score, 2)
                                );
                            }
                        }
                        $types = $this->Project->Type->find('all', array(
                              'fields' => array(
                                    'id',
                                    'name',
                                    'colour'
                              ),
                              'conditions' => array(
                                    'Type.project_id' => $data['Project']['id']
                              ),
                              'order' => 'Type.id ASC',
                              'recursive' => -1
                        ));
                        //elements en este caso sera usado por los rounds
                        $elements = $this->Project->Round->find('all', array(
                              'fields' => array(
                                    'id',
                                    'title as name'
                              ),
                              'conditions' => array(
                                    'Round.id' => $rounds
                              ),
                              'recursive' => -1
                        ));
                        $user_A = $this->Project->User->find('first', array(
                              'fields' => array(
                                    'full_name',
                                    'id'
                              ),
                              'conditions' => array(
                                    'User.id' => $user_A
                              ),
                              'recursive' => -1
                        ));
                        $user_B = $this->Project->User->find('first', array(
                              'fields' => array(
                                    'full_name',
                                    'id'
                              ),
                              'conditions' => array(
                                    'User.id' => $user_B
                              ),
                              'recursive' => -1
                        ));
                        $this->Session->write('confrontationResult', array(
                              'f_scores' => $f_scores,
                              'user_A' => $user_A,
                              'user_B' => $user_B,
                              'types' => $types,
                              'elements' => $elements
                        ));
                        $_SESSION['progress'] = 100;
                        session_write_close();
                        session_start();
                    } else {
                        $user_A = $confrontationResult['user_A'];
                        $user_B = $confrontationResult['user_B'];
                        $f_scores = $confrontationResult['f_scores'];
                        $types = $confrontationResult['types'];
                        $elements = $confrontationResult['elements'];
                    }
                    if ($sendMail) {
                        $this->autoRender = false;
                        $this->Session->write('confrontationSettingsData', $data);
                        $userMail = $userInSession['User']['email'];
                        $userName = $userInSession['User']['full_name'];
                        $this->downloadConfrontationData("FScore2Users", array(
                              'username' => $userName,
                              'email' => $userMail
                        ));
                    } else {
                        $this->set('user_A', $user_A['User']['id']);
                        $this->set('user_B', $user_B['User']['id']);
                        $this->set('key', 'Round');
                        $this->set('margin', $margin);
                        $this->set('name_A', $user_A['User']['full_name']);
                        $this->set('name_B', $user_B['User']['full_name']);
                        $this->set('f_scores', $f_scores);
                        $this->set('types', $types);
                        $this->set('elements', $elements);
                        $this->set('project_id', $this->Project->id);
                        $this->set('redirect', 'confrontationSettingFscoreUsers');
                        $this->render('confrontationFscore');
                    }
                } else {
                    $this->Session->setFlash(__('You must choose different users and at least 1 Round'));
                    $this->autoRender = false;
                    if ($this->request->is('ajax')) {
                        echo "ERROR";
                    } else {
                        $this->redirect(array(
                              'action' => 'confrontationSettingFscoreUsers',
                              $this->Project->id
                        ));
                    }
                }
            }
        }
    }

    public function confrontationSettingFscoreRounds($id = null) {
        $this->Session->write('start', microtime(true));
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            $this->render(false);
            $this->request->data['redirect'] = 'confrontationFscoreRounds';
            debug($this->request->data);
            $this->request->data['round'] = array(
                  $this->request->data['round_A'],
                  $this->request->data['round_B']
            );
            $this->Session->write('confrontationSettingsData', $this->request->data);
            $this->Session->delete('confrontationResult');
            $this->redirect(array(
                  'action' => 'confrontationFscoreRounds'
            ));
        } //$this->request->is('post') || $this->request->is('put')
        $cond = array(
              'project_id' => $this->Project->id
        );
        $users = $this->Project->ProjectsUser->find('all', array(
              'fields' => 'user_id',
              'conditions' => $cond,
              'recursive' => -1
        ));
        $users = $this->flatten($users);
        $users = $this->Project->User->find('list', array(
              'conditions' => array(
                    'id' => $users
              )
        ));
        $cond = array(
              'project_id' => $this->Project->id,
              'ends_in_date IS NOT NULL',
              'title!=\'Removing...\''
        );
        $rounds = $this->Project->Round->find('list', array(
              'conditions' => $cond
        ));
        if (sizeof($users) < 1 || sizeof($rounds) < 2) {
            $this->Session->setFlash(__('This project must have at least two rounds and one user for this functionality'));
            $this->redirect(array(
                  'action' => 'view',
                  $id
            ));
        }
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
    }

    /*
     * Este metodo esta deprecado dado que tarda excesivamente con gran numero de anotaciones
     *
     * */

    public function confrontationFscoreRounds() {
        // top of file
        $time_start = microtime(true);
        $this->Session->write('start', $time_start);
        $data = $this->Session->read('confrontationSettingsData');
        $this->Project->id = $data['Project']['id'];
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->request->data['redirect'] = 'confrontationFscoreUsers';
                $this->Session->write('confrontationSettingsData', $this->request->data);
                $this->redirect(array(
                      'controller' => 'annotations',
                      'action' => 'index'
                ));
            } //$this->request->is('post') || $this->request->is('put')
            else {
                if (isset($data['round_A']) && isset($data['round_B']) && !empty($data['user']) && $data['round_A'] != $data['round_B']) {
                    if (empty($data['Project']['margin']))
                        $data['Project']['margin'] = 0;
                    $sendMail = false;
                    ignore_user_abort(true);
                    if (isset($data['Project']['sendEmail']) && $data['Project']['sendEmail'] == true) {
                        $userInSession = $this->Project->User->find('first', array(
                              'conditions' => array(
                                    'id' => $this->Auth->user('id')
                              ),
                              'recursive' => -1
                        ));
                        $sendMail = true;
                        $userMail = $userInSession['User']['email'];
                        $this->Session->setFlash("we are getting the data, be patient, we will send you an email (To:$userMail ) when operations are completed.", 'information');
                        $this->autoRender = false;
                        echo "MAIL";
                        unset($data['Project']['sendEmail']);
                        session_write_close();
                        header("Content-Encoding: none");
                        header("Content-Length: " . ob_get_length());
                        header("Connection: close");
                        ob_end_flush();
                        flush();
                    }
                    //hallamos el numero total de typos en el proeycto y recogemos su id
                    $types_id = $this->Project->Type->find('all', array(
                          'fields' => 'id',
                          'conditions' => array(
                                'Type.project_id' => $data['Project']['id']
                          ),
                          'order' => 'Type.id ASC',
                          'recursive' => -1
                    ));
                    $types_id = $this->flatten($types_id);
                    $margin = $data['Project']['margin'];
                    if ($data['Project']['margin'] > 100)
                        $margin = 100;
                    //miramos si esta guardado en sesion 
                    $confrontationResult = $this->Session->read('confrontationResult');
                    if (empty($confrontationResult)) {
                        $users = $data['user'];
                        $f_scores = array();
                        $tamUsers = sizeof($users);
                        $tamTypes = sizeof($types_id);
                        $round_A = $data['round_A'];
                        $round_B = $data['round_B'];
                        $scriptTimeLimit = Configure::read('scriptTimeLimit');
                        set_time_limit($scriptTimeLimit);
                        error_reporting(E_ERROR | E_PARSE);
                        $progress = 0;
                        $_SESSION['progress'] = 0;
                        if (!$sendMail) {
                            session_write_close();
                            header("Content-Encoding: none");
                            header("Content-Length: " . 0);
                            header("Connection: close");
                            ob_end_flush();
                            flush();
                        }
                        $db = $this->Project->getDataSource();
                        //el array que viene de la BD trae como claves los ids de los tipos
                        for ($i = 0; $i < $tamUsers; $i++) {
                            $progress = round(($i / $tamUsers) * 90);
                            //las siguientes lineas son debidas a causa de la barra de progreso
                            $_SESSION['progress'] = $progress;
                            session_write_close();
                            session_start();
                            $f_scores[$users[$i]] = array();
                            for ($j = 0; $j < $tamTypes; $j++) {
                                //hallamos el numero total de las anotaciones de cada usuario de un tipo para un round
                                //y a continuacion hallamos las anotaciones comunes a ambos
                                $hits = 0;
                                $cond = array(
                                      'Annotation.round_id' => $round_A,
                                      'Annotation.user_id' => $users[$i],
                                      'Annotation.type_id' => $types_id[$j]
                                );
                                $num_set_A = $this->Project->Type->Annotation->find('count', array(
                                      'conditions' => $cond,
                                      'recursive' => -1
                                ));
                                $cond = array(
                                      'Annotation.round_id' => $round_B,
                                      'Annotation.user_id' => $users[$i],
                                      'Annotation.type_id' => $types_id[$j]
                                );
                                $num_set_B = $this->Project->Type->Annotation->find('count', array(
                                      'conditions' => $cond,
                                      'recursive' => -1
                                ));
                                if ($num_set_A > 0 && $num_set_B > 0)
                                    $hits = $db->fetchAll("select count(a.id) as hits  from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
								where a.round_id =:round_a and b.round_id =:round_b  and a.user_id =:user and b.user_id =:user 
								and a.document_id=b.document_id  and a.type_id=b.type_id and a.type_id =:type_id 
								and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                                          'round_a' => $round_A,
                                          'round_b' => $round_B,
                                          'user' => $users[$i],
                                          'margin' => $margin,
                                          'type_id' => $types_id[$j]
                                    ));
                                if ($hits > 0) {
                                    $precision = $hits[0][0]['hits'] / $num_set_A;
                                    $recall = $hits[0][0]['hits'] / $num_set_B;
                                    $f_score = $precision * $recall;
                                    $f_score = 100 * 2 * $f_score / ($precision + $recall);
                                } else {
                                    $f_score = 0;
                                }
                                $f_scores[$users[$i]][$types_id[$j]] = array(
                                      'f-score' => round($f_score, 2)
                                );
                            }
                        }
                        $types = $this->Project->Type->find('all', array(
                              'fields' => array(
                                    'id',
                                    'name'
                              ),
                              'conditions' => array(
                                    'Type.project_id' => $data['Project']['id']
                              ),
                              'order' => 'Type.id ASC',
                              'recursive' => -1
                        ));
                        //elements en este caso sera usado por los rounds
                        $elements = $this->Project->User->find('all', array(
                              'fields' => array(
                                    'id',
                                    'username as name'
                              ),
                              'conditions' => array(
                                    'User.id' => $users
                              ),
                              'recursive' => -1
                        ));
                        $round_A = $this->Project->Round->find('first', array(
                              'fields' => array(
                                    'title',
                                    'id'
                              ),
                              'conditions' => array(
                                    'id' => $round_A
                              ),
                              'recursive' => -1
                        ));
                        $round_B = $this->Project->Round->find('first', array(
                              'fields' => array(
                                    'title',
                                    'id'
                              ),
                              'conditions' => array(
                                    'id' => $round_B
                              ),
                              'recursive' => -1
                        ));
                        $this->Session->write('confrontationResult', array(
                              'f_scores' => $f_scores,
                              'round_A' => $round_A,
                              'round_B' => $round_B,
                              'types' => $types,
                              'elements' => $elements
                        ));
                        $_SESSION['progress'] = 100;
                        session_write_close();
                        session_start();
                    } else {
                        $round_A = $confrontationResult['round_A'];
                        $round_B = $confrontationResult['round_B'];
                        $f_scores = $confrontationResult['f_scores'];
                        $types = $confrontationResult['types'];
                        $elements = $confrontationResult['elements'];
                    }
                    if ($sendMail) {
                        $this->autoRender = false;
                        $this->Session->write('confrontationSettingsData', $data);
                        $userMail = $userInSession['User']['email'];
                        $userName = $userInSession['User']['full_name'];
                        $this->downloadConfrontationData("FScore2Rounds", array(
                              'username' => $userName,
                              'email' => $userMail
                        ));
                    } else {
                        $this->set('A', $round_A['Round']['id']);
                        $this->set('B', $round_A['Round']['id']);
                        $this->set('key', 'User');
                        $this->set('margin', $margin);
                        $this->set('name_A', $round_A['Round']['title']);
                        $this->set('name_B', $round_B['Round']['title']);
                        $this->set('f_scores', $f_scores);
                        $this->set('types', $types);
                        $this->set('elements', $elements);
                        $this->set('project_id', $this->Project->id);
                        $this->set('redirect', 'confrontationSettingFscoreRounds', $this->Project->id);
                        $this->render('confrontationFscore');
                    }
                } else {
                    $this->Session->setFlash(__('You must choose different rounds and at least 1 user'));
                    $this->autoRender = false;
                    if ($this->request->is('ajax')) {
                        echo "ERROR";
                    } else {
                        $this->redirect(array(
                              'action' => 'confrontationSettingFscoreRounds',
                              $this->Project->id
                        ));
                    }
                }
            }
        }
    }

    /**
     * RGBToHex method
     * @param array $colour
     * @return string
     */
    function RGBToHex($colour) {
        $colour = explode(',', $colour);
        //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
        $hex = "#";
        $hex .= str_pad(dechex($colour[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($colour[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($colour[2]), 2, "0", STR_PAD_LEFT);
        return $hex;
    }

    /**
     * RGBToHex method
     * @param string $project_id
     * @param string $user_id
     * @return void
     */
    public function statisticsForUser($project_id = null, $user_id = null) {
        $this->Project->id = $project_id;
        $this->Project->User->id = $user_id;
        $group_id = $this->Session->read('group_id');
        if ($group_id != 1) {
            $user_id = $this->Session->read('user_id');
            $this->Project->User->id = $this->Session->read('user_id');
            $redirect = array(
                  'controller' => 'Projects',
                  'action' => 'userView',
                  $project_id
            );
        }
        if (!$this->Project->exists() || !$this->Project->User->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $redirect = array(
              'controller' => 'Projects',
              'action' => 'view',
              $project_id
        );
        $haveThisProject = $this->Project->ProjectsUser->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'ProjectsUser.user_id' => $user_id,
                    'ProjectsUser.project_id' => $this->Project->id
              )
        ));
        if (empty($haveThisProject)) {
            $this->Session->setFlash('You should review  projects  that have assigned you ');
            $this->redirect(array(
                  'controller' => 'projects',
                  'action' => 'userIndex'
            ));
        } //empty($haveThisProject)
        $types = $this->Project->Type->find('all', array(
              'recursive' => -1,
              'conditions' => array(
                    'Type.project_id' => $project_id
              )
        ));
        $user = $this->Project->User->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'User.id' => $user_id
              )
        ));
        $project = $this->Project->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'Project.id' => $project_id
              )
        ));
        $rounds = $this->Project->Round->find('list', array(
              'recursive' => -1,
              'fields' => array(
                    'id'
              ),
              'conditions' => array(
                    'Round.project_id' => $project_id
              )
        ));
        $totalTypeData = array();
        $totalAnnotations = 0;
        foreach ($types as $type) {
            $count = $this->Project->Type->Annotation->find('count', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'Annotation.type_id' => $type['Type']['id'],
                        'Annotation.user_id' => $user_id,
                        'Annotation.round_id' => $rounds
                  )
            ));
            array_push($totalTypeData, array(
                  'GraficColumns' => $type['Type']['name'],
                  'Colour' => $this->RGBToHex($type['Type']['colour']),
                  'value' => $count
            ));
            $totalAnnotations += $count;
        }
        $this->set('totalTypeData', $totalTypeData);
        $this->set('projectName', $project['Project']['title']);
        $this->set('userName', $user['User']['full_name']);
        $this->set('totalAnnotations', $totalAnnotations);
        $this->set('redirect', $redirect);
    }

    /**
     * getConfrontationData method
     * @param Array &$users
     * @param Array &$rounds
     * @param string $project_id
     * @return void
     */
    public function getConfrontationData(&$users = array(), &$rounds = array(), $project_id = null) {
        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array(
              'project_id' => $project_id,
              'ends_in_date IS NOT NULL'
        );
        if ($deleteCascade) {
            $conditions = array(
                  'project_id' => $project_id,
                  'ends_in_date IS NOT NULL',
                  'title!=\'Removing...\''
            );
        }
        $rounds = $this->Project->Round->find('list', array(
              'recursive' => -1,
              'conditions' => $conditions
        ));
        if (empty($rounds)) {
            $this->Session->setFlash('Insufficient data for this operation. This project must have at least two users and one round for this functionality');
            $this->redirect(array(
                  'controller' => 'projects',
                  'action' => 'view',
                  $this->Project->id
            ));
        } else {
            $cond = array(
                  'project_id' => $this->Project->id
            );
            $users = $this->Project->ProjectsUser->find('all', array(
                  'fields' => 'user_id',
                  'conditions' => $cond,
                  'recursive' => -1
            ));
            $users = $this->flatten($users);
            $userConditions = array(
                  'id' => $users
            );
            if ($deleteCascade)
                $userConditions = array(
                      'username !=' => 'Removing...',
                      'id' => $users
                );
            $users = $this->Project->User->find('list', array(
                  'conditions' => $userConditions
            ));
        }
    }

    public function getProgress($get = false) {
        $this->autoRender = false;
        //session_start();
        if ($get) {
            if (isset($_SESSION['progress'])) {
                echo $_SESSION['progress'];
                session_write_close();
                header("Content-Encoding: none");
                header("Content-Length: " . ob_get_length());
                header("Connection: close");
                ob_end_flush();
                flush();
            }
            //session_write_close();
        }
    }

    function downloadConfrontationData($tableName = null, $sendByMail = null) {
        $this->autoRender = false;
        $confrontationResult = $this->Session->read('confrontationResult');
        $confrontationSettingsData = $this->Session->read('confrontationSettingsData');
        $confrontationDualResult = $this->Session->read('confrontationDualResult');
        $confrontationData = $this->Session->read('confrontationPostedData');
        /* aunque no haya nada ponemos valores igualmente */
        if (empty($confrontationResult))
            $confrontationResult = 'null';
        if (empty($confrontationSettingsData))
            $confrontationSettingsData = 'null';
        if (empty($confrontationDualResult))
            $confrontationDualResult = 'null';
        if (empty($confrontationData))
            $confrontationData = 'null';
        $dataText = json_encode(array(
              "confrontationResult" => $confrontationResult,
              "confrontationSettingsData" => $confrontationSettingsData,
              "confrontationDualResult" => $confrontationDualResult,
              "confrontationPostedData" => $confrontationData,
              "tableToLoad" => $tableName
        ));
        $dataText = Security::cipher($dataText, Configure::read('Security.salt'));
        $fileName = "MarkyData-" . $tableName . "__" . date("Y-m-d") . ".json";
        if ($sendByMail != null) {
            $emailProfile = Configure::read('emailProfile');
            $Email = new CakeEmail($emailProfile);
            $Email->from(array(
                  'Marky@webAannotation.com' => 'Marky'
            ));
            $userMail = $sendByMail['email'];
            $userName = $sendByMail['username'];
            $Email->to($userMail);
            $Email->subject('Marky agreement statistics');
            $Email->emailFormat('html');
            $Email->template('sendData');
            $Email->viewVars(array(
                  'userName' => $userName));
            $tmpfname = tempnam(sys_get_temp_dir(), "sendData");
            $file = new File($tmpfname, true, 0777);
            if ($file->append($dataText)) {
                $Email->attachments(array(
                      $fileName => array(
                            'file' => $file->pwd()
                      )
                ));
                $file->close();
                if (!$Email->send("")) {
                    CakeLog::write('MailLog', 'Error send  statistics to: ' . $sendByMail['email']);
                }
            } else {
                CakeLog::write('MailLog', 'CRASH:Append statistics to send to: ' . $sendByMail['email']);
                $file->delete();
                throw new Exception("Append data to send -> crash", 1);
            }
            $file->delete();
        } else {
            $this->response->type('txt');
            $this->response->body($dataText);
            $this->response->download($fileName);
        }
    }

    function importData($id) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            if ($this->request->data['Project']['File']['size'] > 0) {
                $file = new File($this->request->data['Project']['File']['tmp_name']);
                $contents = $file->read();
                $contents = Security::cipher($contents, Configure::read('Security.salt'));
                $data = json_decode($contents, true);
                if (!empty($data)) {
                    $this->Session->delete('confrontationResult');
                    $this->Session->delete('confrontationSettingsData');
                    $this->Session->delete('confrontationDualResult');
                    $this->Session->delete('confrontationPostedData');
                    $this->Session->write('confrontationResult', $data['confrontationResult']);
                    $this->Session->write('confrontationSettingsData', $data['confrontationSettingsData']);
                    $this->Session->write('confrontationDualResult', $data['confrontationDualResult']);
                    $this->Session->write('confrontationPostedData', $data['confrontationPostedData']);
                    $redirect = null;
                    switch ($data['tableToLoad']) {
                        case 'confrontationMultiRound':
                            $redirect = "confrontationMultiRound";
                            break;
                        case 'confrontationMultiUser':
                            $redirect = "confrontationMultiUser";
                            break;
                        case 'confrontationDual':
                            $redirect = "confrontationDual";
                            break;
                        case 'FScore2Users':
                            $redirect = "confrontationFscoreUsers";
                            break;
                        case 'FScore2Rounds':
                            $redirect = "confrontationFscoreRounds";
                            break;
                        default:
                            throw new Exception("Error Processing Request, error: " . $data['tableToLoad'], 1);
                    }
                    if ($redirect == null) {
                        $this->Session->setFlash('This file is to load the table: ' . $data['tableToLoad']);
                        $this->redirect(array(
                              'controller' => 'projects',
                              'action' => 'loadTable',
                              $this->Project->id
                        ));
                    } else {
                        $this->redirect(array(
                              'controller' => 'projects',
                              'action' => $redirect
                        ));
                    }
                }
            } else {
                $this->Session->setFlash('Please select file');
                $this->redirect(array(
                      'controller' => 'projects',
                      'action' => 'importData',
                      $this->Project->id
                ));
            }
            $this->Session->setFlash('This file is corrupted');
            $this->redirect(array(
                  'controller' => 'projects',
                  'action' => 'index'
            ));
        }
        $this->set('project_id', $this->Project->id);
    }

    public function importTypes($id = null) {
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        } //!$this->Project->exists()
        $redirect = $this->Session->read('redirect');
        if ($this->request->is('post') || $this->request->is('put')) {
            $types = json_decode($this->request->data['allTypes']);
            if (!empty($types)) {
                $db = $this->Project->getDataSource();
                $db->begin();
                $types = $this->Project->Type->find('all', array(
                      'conditions' => array(
                            'Type.id' => $types),
                      'recursive' => -1
                ));
                $problems = array();
                $commit = true;
                foreach ($types as $type) {
                    $type_id = $type['Type']['id'];
                    $type['Type']['project_id'] = $id;
                    unset($type['Type']['id']);
                    $questions = $this->Project->Type->Question->find('all', array(
                          'conditions' => array(
                                'type_id' => $type_id),
                          'recursive' => -1,
                          'fields' => 'question'));
                    $type['Question'] = $questions;
                    $commit = $commit & $this->Project->Type->saveAssociated($type);
                    if ($commit == FALSE) {
                        array_push($problems, $type['Type']['name']);
                    }
                }
                if ($commit) {
                    $db->commit();
                    $this->Session->setFlash(__('The chosen types have been successfully imported'), 'success');
                    $this->redirect($redirect);
                } //$this->Project->save($this->request->data)
                else {
                    $db->rollback();
                    $this->Session->setFlash(__('The chosen types havent been successfully imported, types: ' . implode(',', $problems) . " appear to be repeated or incorrect "));
                }
            } else {
                $this->redirect($redirect);
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $types = $this->Project->Type->find('all', array(
                  'recursive' => -1,
                  //'conditions' => array('project_id !=' => $id)
                  'group' => array(
                        'Type.description',
                        'Type.name')
            ));
            $project = $this->Project->find('first', array(
                  'recursive' => -1,
                  'conditions' => array(
                        'id' => $id)
            ));
        }
        $project = $this->Project->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $id)
        ));
        $this->set(compact('types', 'project'));
    }

    function importFulltext() {
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        App::uses('CakeTime', 'Utility');
        App::import('Vendor', 'htmLawed', array(
              'file' => 'htmLawed' . DS . 'htmLawed.php'));
        $this->autoRender = false;
        $dir = new Folder('/home/server/Desktop/fulltexts/', false, 0777);
        $files = $dir->find('.*');
        $this->count = 0;
        $this->UsersRound = $this->Project->User->UsersRound;
        $config = array(
              'cdata' => 1,
              'safe' => 1,
              'keep_bad' => 6,
              'no_deprecated_attr' => 2,
              'valid_xhtml' => 1,
              'abs_url' => 1);
        foreach ($files as $file) {
            //sleep(5);
            $fileName = substr($file, 0, strrpos($file, '.'));
            $file = new File($dir->pwd() . DS . $file);
            if ($file->readable()) {
                $content = '<p>' . $file->read();
                $content = mb_convert_encoding($content, "ISO-8859-1", "UTF-8");
                $content = preg_replace("/\.\s*\n/", '.</p><p>', $content);
                $content = $content . '</p>';
                $content = str_replace("\n", '', $content);
                $content = str_replace('<?xml version="1.0" encoding="ISO-8859-1"?><?xml-stylesheet type="text/css" href="..\..\default.css"?>', "", $content);
                $file->close();
                $document = $this->Project->Document->find('first', array(
                      'conditions' => array(
                            'title' => $fileName),
                      'fields' => 'id'));
                if (isset($document['Document']['id'])) {
                    $date = date("Y-m-d H:i:s");
                    $data = array(
                          'title' => $fileName,
                          'html' => $content,
                          'id' => $document['Document']['id'],
                          'created' => $date);
                    $this->Project->Document->save($data);
                    $results = htmLawed($content, $config);
                    $results = preg_replace_callback('/class="[^>]*/', array(
                          $this,
                          "callbackImportFulltextClass"), $results);
                    $cond = array(
                          'document_id' => $document['Document']['id'],
                          'round_id' => '1');
                    $results = gzdeflate($results, 9);
                    $ids = $this->UsersRound->find('list', array(
                          'conditions' => $cond,
                          'recursive' => -1));
                    foreach ($ids as $id) {
                        $this->UsersRound->id = $id;
                        if (!$this->UsersRound->save(array(
                                  'text_marked' => $results,
                                  'created' => $date))) {
                            print('user_rounds_error_' . $fileName . '<br>');
                        }
                    }
                } else {
                    print('error_' . $fileName . '<br>');
                }
            }
        }
        print('hecho' . '<br>');
    }

    function callbackImportFulltextClass($match) {
        $result = substr($match[0], 7);
        $title = substr($result, 0, -1);
        $result = ucfirst($result);
        $result = 'class="myMark' . $result;
        $result = $result . " id='Marky$this->count' name='annotation' value='$this->count' title='$title' ";
        $this->count++;
        return $result;
    }

    function import($id = null) {
        $this->User = $this->Project->User;
        $users = $this->User->find('list', array(
              'conditions' => array(
                    'group_id' => 2
              ),
              'recursive' => -1
        ));
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($users)) {
                $this->Session->setFlash("You need select almost one user");
                $this->redirect(array(
                      'action' => 'view', $this->Project->id
                ));
            }
            $this->Job = $this->Project->User->Job;
            App::uses('Folder', 'Utility');
            //App::import('Vendor', 'htmLawed', array('file' => 'htmLawed' . DS . 'htmLawed.php'));
            $zipFile = new File($this->request->data['Project']['File']['tmp_name'], false, 0777);
            $newPath = sys_get_temp_dir() . DS . uniqid() . "." . $zipFile->ext();
            $zipFile->copy($newPath);
            $user_id = $this->Session->read('user_id');
            $data = $this->request->data;
            if (!empty($data['User']['User'])) {
                if ($data['Project']['project_name'] != '') {
                    $file = new File($data['Project']['File']['tmp_name']);
                    if ($file->exists()) {
                        $dir = new Folder(sys_get_temp_dir() . DS . uniqid(), true);
                        $file->copy($dir->path . DS . $data['Project']['File']['name']);
                        $params = array(
                              'project_name' => $data['Project']["project_name"],
                              'file_type' => $data['Project']["file_type"],
                              'zero_start' => true,
                              'allow_overlaps' => false,
                              'twitter_project' => false,
                              'has_title' => true,
                              'bioc_byte_level' => isset($data['Project']['bioc_default_options']),
                              'bioc_annotation_key' => isset($data['Project']['bioc_annotation_key']) ? $data['Project']['bioc_annotation_key'] : '',
                              'bioc_relation_key' => isset($data['Project']['bioc_relation_key']) ? $data['Project']['bioc_relation_key'] : '',
                              'file' => $dir->path . DS . $data['Project']['File']['name'],
                              'user' => $data['User']["User"]
                        );
                        if (!empty($data['Project']['default_options'])) {
                            foreach ($data['Project']['default_options'] as $option) {
                                if ($option == 'zero_start') {
                                    $params[$option] = false;
                                } else if ($option == 'has_title') {
                                    $params[$option] = false;
                                } else {
                                    $params[$option] = true;
                                }
                            }
                        }
                        $this->Job->create();
                        $programName = "importation.jar";
                        $data = array('user_id' => $user_id,
                              'percentage' => 0, '' => $programName,
                              'status' => 'Starting...');
                        if ($this->Job->save($data)) {
                            $id = $this->Job->id;
                            $params["job_id"] = $id;
                            $project_id = $this->Project->id;
                            $operationId = 0;
                            $javaJarPath = Configure::read('javaJarPath');
                            $filePath = $javaJarPath . DS . "job$id.log";
                            $jsonArguments = new File($dir->pwd() . DS . "arguments.json");
                            $jsonArguments->write(json_encode($params));
                            $this->sendJob($id, $programName, $jsonArguments->pwd(), false, $filePath);
                            $this->redirect(array(
                                  'controller' => 'jobs',
                                  'action' => 'importProgress',
                                  $id
                            ));
                        }
                    } else {
                        $this->Session->setFlash(__('You must upload almost one zip file with your archives'));
                    }
                } else {
                    $this->Session->setFlash(__('You must indicate a project name'));
                }
            } else {
                $this->Session->setFlash(__('You must select almost one user'));
            }
        }
        $this->set(compact('users'));
    }

    function importAnnotationsAndDocuments() {
        $this->Round = $this->Project->Round;
        $this->Document = $this->Project->Document;
        $this->DocumentsProject = $this->Project->DocumentsProject;
        $this->UsersRound = $this->Project->User->UsersRound;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->Type = $this->Project->Type;
        $this->Relation = $this->Project->Relation;
        $this->AnnotationsInterRelations = $this->Relation->AnnotationsInterRelations;
        $this->Annotation = $this->Type->Annotation;
        $this->Question = $this->Type->Question;
        $this->AnnotationsQuestion = $this->Annotation->AnnotationsQuestion;
        $this->DocumentsAssessment = $this->Document->DocumentsAssessment;
        $maxAnnotation = $this->Annotation->find('first', array(
              'recursive' => -1,
              'fields' => ('id'),
              'order' => 'id DESC'));
        if (isset($maxAnnotation['Annotation']['id'])) {
            $maxAnnotation = $maxAnnotation['Annotation']['id'];
        } else {
            $maxAnnotation = 0;
        }
        $maxType = $this->Type->find('first', array(
              'recursive' => -1,
              'fields' => ('id'),
              'order' => 'id DESC'));
        if (isset($maxType['Type']['id'])) {
            $maxType = $maxType['Type']['id'];
        } else {
            $maxType = 0;
        }
        $maxRelation = $this->Relation->find('first', array(
              'recursive' => -1,
              'fields' => ('id'),
              'order' => 'id DESC'));
        if (isset($maxRelation['Relation']['id'])) {
            $maxRelation = $maxRelation['Relation']['id'];
        } else {
            $maxRelation = 0;
        }
        $maxInterRelations = $this->AnnotationsInterRelations->find('first', array(
              'recursive' => -1,
              'fields' => ('id'),
              'order' => 'id DESC'));
        if (isset($maxInterRelations['AnnotationsInterRelations']['id'])) {
            $maxInterRelations = $maxInterRelations['AnnotationsInterRelations']['id'];
        } else {
            $maxInterRelations = 0;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $maxAnnotation++;
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
            App::uses('CakeTime', 'Utility');
            $scriptTimeLimit = Configure::read('scriptTimeLimit');
            set_time_limit($scriptTimeLimit);
            $users = $this->request->data['User']['User'];
            //App::import('Vendor', 'htmLawed', array('file' => 'htmLawed' . DS . 'htmLawed.php'));
            $zipFile = new File($this->request->data['Project']['File']['tmp_name'], false, 0777);
            $projectName = ucwords(pathinfo($this->request->data['Project']['File']['name'], PATHINFO_FILENAME));
            if ($zipFile->exists() && !empty($users)) {
                $filesAllowed = Configure::read('filesAllowed');
                $zip = new ZipArchive;
                if (!$zip->open($zipFile->pwd(), ZipArchive::CREATE)) {
                    $zipFile->remove();
                    throw new Exception("Failed to open Zip archive\n");
                }
                $path = sys_get_temp_dir() . DS;
                $path = $path . uniqid();
                //creamos la carpeta de descarga para el usuario si no existe
                $extractFolder = new Folder($path, true, 0777);
                $zip->extractTo($extractFolder->pwd());
                $zip->close();
                $zipFile->delete(); // I am deleting this file
                $rounds = array();
                $db = $this->Project->getDataSource();
                $db->execute("UNLOCK TABLES");
                $db->query("ALTER TABLE annotations AUTO_INCREMENT = $maxAnnotation");
                $db->execute("LOCK TABLES " .
                    $this->Annotation->table
                    . " WRITE, " .
                    $this->Type->table
                    . " WRITE, " .
                    $this->Relation->table
                    . " WRITE, " .
                    $this->AnnotationsInterRelations->table
                    . " WRITE;"
                );
                $db->begin();
                $db->useNestedTransactions = false;
                $this->Project->create();
                if ($this->Project->save(array(
                          'Project' => array(
                                "title" => $projectName,
                                "description" => ""),
                          "User" => array(
                                'User' => $users)))) {
                    //creamos round de anotacion automatica
                    $this->Round->create();
                    if (!$this->Round->save(array(
                              'Round' => array(
                                    "title" => "Round for job " . date("Y/m/d"),
                                    "project_id" => $this->Project->id,
                                    "description" => "-",
                                    'ends_in_date' => date("Y-m-d", strtotime("+30 day")),
                                    'trim_helper' => true,
                                    'whole_word_helper' => true,
                                    'punctuation_helper' => true,
                                    'start_document' => 0,
                                    'end_document' => 0,
                                    'is_visible' => 0,
                                    'highlight' => 0,
                              ),
                        ))
                    ) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("Round work can not be saved");
                    }
                    array_push($rounds, $this->Round->id);
                    foreach ($users as $user) {
                        foreach ($rounds as $round) {
                            $this->UsersRound->create();
                            if (!$this->UsersRound->save(array(
                                      'state' => 0,
                                      'round_id' => $round,
                                      'user_id' => $user))) {
                                $extractFolder->delete();
                                $db->rollback();
                                $db->execute("UNLOCK TABLES");
                                throw new Exception("This UsersRound can not be save:" . $fileName);
                            }
                        }
                    }
                    $typesText = new File($extractFolder->pwd() . DS . 'types.txt', false, 0644);
                    if (!$typesText->exists()) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("types.txt not exist in zip file");
                    }
                    $count = new File($extractFolder->pwd() . DS . 'count.txt', false, 0644);
                    if (!$count->exists()) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("count.txt not exist in zip filer");
                    }
                    $count = $count->read();
                    $count = intval($count) + 1;
                    //buscamos la anotacion final con dicho Id si ya existe no se puede crear el proyecto 
                    $existLastId = $this->Annotation->find('count', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'id' => $count)));
                    $existInitialId = $this->Annotation->find('count', array(
                          'recursive' => -1,
                          'conditions' => array(
                                'id' => $count - ($count - $maxAnnotation))));
                    if ($existInitialId > 0) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("the first id already exist in Marky");
                    }
                    if ($existLastId > 0) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("the last id already exist in Marky");
                    }
                    $typesText = $typesText->read();
                    $types = preg_split("/[\r\n]+/", trim($typesText));
                    $typesMap = array();
                    $defaultColors = Configure::read('defaultColors');
                    $colorCont = 0;
                    foreach ($types as $type) {
                        //debug($types);
                        $type = explode("\t", $type);
                        if (sizeof($type) > 1) {
                            $typeName = $type[0];
                            $typeId = $type[1];
                            if (!empty($defaultColors[$colorCont])) {
                                $color = $defaultColors[$colorCont];
                                $colorCont++;
                            } else {
                                $color = array(
                                      rand(50, 255),
                                      rand(50, 255),
                                      rand(50, 255));
                            }
                            $this->Type->create();
                            if (!$this->Type->hasAny(array('id' => $typeId)) && $this->Type->save(
                                    array(
                                          'Type' =>
                                          array(
                                                'id' => $typeId,
                                                'name' => $typeName,
                                                'project_id' => $this->Project->id,
                                                'colour' => $color[0] . ',' . $color[1] . ',' . $color[2] . ',1'),
                                          'Round' => array(
                                                'Round' => $rounds)
                                    )
                                )
                            ) {
                                $typesMap[$typeName] = $this->Type->id;
                            } else {
                                $extractFolder->delete();
                                $db->rollback();
                                $db->execute("UNLOCK TABLES");
                                throw new Exception("This type can not be save" . json_encode($type));
                            }
                        } else {
                            $extractFolder->delete();
                            $db->rollback();
                            $db->execute("UNLOCK TABLES");
                            throw new Exception("This type can not be save" . $type);
                        }
                    }
                    unset($typesText);
                    /* ==================Find rates================== */
                    $rateFile = new File($extractFolder->pwd() . DS . 'stars.txt', false, 0644);
                    $rateMap = array();
                    if ($rateFile->exists()) {
                        $content = $rateFile->read();
                        $tsvLines = array_map(function($string) {
                            return str_getcsv($string, "\t");
                        }, explode("\n", $content));
                        foreach ($tsvLines as $line) {
                            if (isset($line[1]) && $line[1] == "1") {
                                $rateMap[$line[0]] = array(
                                      "positive" => true,
                                      "neutral" => false,
                                      "negative" => false,
                                      'project_id' => $this->Project->id,
                                      'note' => $line[2],
                                );
                            }
                        }
                        unset($content);
                    }
                    /* ============================================== */
                    $files = $extractFolder->find('.*.html', true);
                    if (empty($files)) {
                        $extractFolder->delete();
                        $db->rollback();
                        $db->execute("UNLOCK TABLES");
                        throw new Exception("The html documents can not be read");
                    }
                    //$config = array('cdata' => 1, 'safe' => 1, 'keep_bad' => 6, 'no_depreca ted_attr' => 2, 'valid_xhtml' => 1, 'abs_url' => 1);
                    $contAnnotations = 0;
                    $documentsMap = array();
                    $annotationsAcumulate = array();
                    foreach ($files as $file) {
                        //sleep(5);
                        $fileName = substr($file, 0, strrpos($file, '.'));
                        $file = new File($extractFolder->pwd() . DS . $file);
                        if ($file->readable()) {
                            //$content = '<p>' . preg_replace("/\.\s*\n/", '.</p><p>', $file->read()) . '</p>';
                            $content = $file->read();
                            if (strlen($content) > 1) {
                                $file->close();
                                $date = date("Y-m-d H:i:s");
                                $document = $this->Document->find('first', array(
                                      'recursive' => -1,
                                      'conditions' => array('external_id' => $fileName)
                                ));
                                $title = $fileName;
                                $raw = $content;
                                $sections = explode("</h3>", $content);
                                if (count($sections) > 1) {
                                    $title = $this->parseHtmlToGetAnnotations($sections[0] . "</h3></div>");
                                    $raw = $title . "\t" . $this->parseHtmlToGetAnnotations($sections[1]);
                                }
                                if (empty($document)) {
                                    $this->Document->create();
                                    if (!$this->Document->save(array(
                                              'Document' => array(
                                                    'external_id' => $fileName,
                                                    'title' => $fileName,
                                                    'html' => $content,
                                                    'raw' => $raw,
                                                    'created' => $date),
                                              'Project' => array(
                                                    'Project' => $this->Project->id)
                                        ))) {
                                        $extractFolder->delete();
                                        $db->rollback();
                                        $db->execute("UNLOCK TABLES");
                                        throw new Exception("This Document can not be save:" . $fileName);
                                    }
                                } else {
                                    $this->Document->id = $document['Document']['id'];
                                    $this->DocumentsProject->create();
                                    if (!$this->DocumentsProject->save(array(
                                              'document_id' => $this->Document->id,
                                              'project_id' => $this->Project->id)
                                        )) {
                                        $extractFolder->delete();
                                        $db->rollback();
                                        $db->execute("UNLOCK TABLES");
                                        throw new Exception("This Document - Project can not be save");
                                    }
                                }
                                $documentsMap[$fileName] = $this->Document->id;
                                $annotations = $this->getAnnotations($content, $this->Document->id);
                                $tam = count($annotations);
                                $modes = Configure::read('annotationsModes');
                                $insertAssesment = isset($rateMap[$fileName]);
                                foreach ($users as $user) {
                                    if ($insertAssesment) {
                                        $rateMap[$fileName]["user_id"] = $user;
                                        $rateMap[$fileName]["document_id"] = $this->Document->id;
                                        $this->DocumentsAssessment->create();
                                        if (!$this->DocumentsAssessment->save($rateMap[$fileName])) {
                                            $db->rollback();
                                            $db->execute("UNLOCK TABLES");
                                            throw new Exception("This documents assessments can not be save:" . $fileName);
                                        }
                                    }
                                    foreach ($rounds as $round) {
                                        $this->AnnotatedDocument->create();
                                        if (!$this->AnnotatedDocument->save(array(
                                                  'text_marked' => $content,
                                                  'document_id' => $this->Document->id,
                                                  'round_id' => $round,
                                                  'user_id' => $user))) {
                                            $extractFolder->delete();
                                            $db->rollback();
                                            $db->execute("UNLOCK TABLES");
                                            throw new Exception("This AnnotatedDocument can not be save:" . $fileName);
                                        }
                                        if (!empty($annotations)) {
                                            foreach ($annotations as $index => $annotation) {
                                                $annotations[$index]['user_id'] = $user;
                                                $annotations[$index]['round_id'] = $round;
                                                $annotations[$index]['mode'] = $modes["AUTOMATIC"];
                                                $contAnnotations++;
                                            }
                                            $annotationsAcumulate = array_merge($annotationsAcumulate, $annotations);
                                        }
                                    }
                                }
                                if (count($annotationsAcumulate) > 500) {
                                    try {
                                        if (in_array(false, $this->Annotation->saveMany($annotationsAcumulate, array(
                                                      "validate" => true,
                                                      "atomic" => false,
                                                      "callbacks" => false,
                                                      "counterCache" => true,
                                                    )
                                            ))) {
                                            debug($this->Annotation->validationErrors);
                                            debug($fileName);
                                            $extractFolder->delete();
                                            $db->rollback();
                                            $db->execute("UNLOCK TABLES");
                                            throw new Exception("Annotations can not be save. Validation Error");
                                        } else {
                                            
                                        }
                                    } catch (Exception $e) {
                                        throw new Exception("Annotations can not be save. Validation Error on save");
                                    }
                                    $annotationsAcumulate = array();
                                }
                            }
                        }
                    }
                    if (count($annotationsAcumulate) > 0) {
                        if (in_array(false, $this->Annotation->saveMany($annotationsAcumulate, array(
                                      "validate" => true,
                                      "atomic" => false,
                                      "callbacks" => false,
                                      "counterCache" => true,
                                    )
                            ))) {
                            debug($annotationsAcumulate);
                            debug(array_pop($this->Project->Type->Annotation->validationErrors));
                            debug($fileName);
                            debug($raw);
                            $extractFolder->delete();
                            $db->rollback();
                            $db->execute("UNLOCK TABLES");
                            throw new Exception("Annotations can not be save. Validation Error on save");
                        } else {
                            
                        }
                    }
                    /* ========================================= */
                    /*            create relations             */
                    /* ========================================= */
                    $annotationsFile = new File($extractFolder->pwd() . DS . 'annotations.tsv', false, 0644);
                    if ($annotationsFile->exists() && $annotationsFile->size() != 0) {
                        $annotationsText = $annotationsFile->read();
                        $annotations = preg_split("/[\r\n]+/", trim($annotationsText));
                        $questionMap = array();
                        $commentaryFlag = false;
                        array_shift($annotations);
                        $cont = 0;
                        foreach ($annotations as $annotation) {
                            //debug($types);
                            $annotationSections = explode("\t", $annotation);
                            if (sizeof($annotationSections) > 6) {
                                $commentary = trim($annotationSections[6]);
                                $document = $annotationSections[0];
                                $section = $annotationSections[1];
                                $init = $annotationSections[2];
                                $end = $annotationSections[3];
                                $typeName = $annotationSections[5];
                                if ($commentary != '' && $commentary != '-') {
                                    $typeId = $typesMap[$typeName];
                                    if (isset($typeId) && !isset($questionMap[$typeId])) {
                                        $this->Question->create();
                                        if ($this->Question->save(
                                                array(
                                                      'type_id' => $typeId,
                                                      'question' => "database id:",
                                                )
                                            )
                                        ) {
                                            $questionMap[$typeId] = $this->Question->id;
                                        } else {
                                            $extractFolder->delete();
                                            debug($this->Question->validationErrors);
                                            $db->rollback();
                                            $db->execute("UNLOCK TABLES");
                                            throw new Exception("This question can not be save, line : " . $annotation);
                                        }
                                    }
                                    $annotationInDb = $this->Annotation->find('first', array(
                                          'recursive' => -1, //int
                                          'fields' => array('Annotation.id'),
                                          'conditions' => array(
                                                'Annotation.init BETWEEN ? AND ?' => array(
                                                      $init - 2, $init + 2),
                                                'Annotation.end BETWEEN ? AND ?' => array(
                                                      $end - 2, $end + 2),
                                                'Annotation.section' => $section,
                                                'Annotation.document_id' => $documentsMap[$document],
                                                'Annotation.round_id' => $round,
                                                'Annotation.user_id' => $user,
                                                'Annotation.type_id' => $typeId,
                                          ), //array of conditions
                                    ));
                                    $this->AnnotationsQuestion->create();
                                    if (!empty($annotationInDb) && $this->AnnotationsQuestion->save(
                                            array(
                                                  'annotation_id' => $annotationInDb["Annotation"]["id"],
                                                  'question_id' => $questionMap[$typeId],
                                                  'answer' => $commentary,
                                            )
                                        )
                                    ) {
                                        $cont++;
                                    } else {
                                        debug($annotationInDb);
                                        debug($document);
                                        debug($this->AnnotationsQuestion->validationErrors);
                                        $this->recursive = -1;
                                        $this->Document->id = $documentsMap[$document];
                                        debug($this->Document->find('first', array(
                                                  'recursive' => -1, //int
                                                  //array of field names
                                                  'conditions' => array('Document.id' => $documentsMap[$document]), //array of conditions
                                                )
                                        ));
                                        debug($this->Annotation->find('all', array(
                                                  'recursive' => -1, //int
                                                  //array of field names
                                                  'fields' => array('Annotation.*'),
                                                  'conditions' => array(
                                                        'Annotation.document_id' => $documentsMap[$document],
                                                        'Annotation.round_id' => $round,
                                                        'Annotation.user_id' => $user,
                                                  ), //array of conditions
                                            ))
                                        );
                                        $extractFolder->delete();
                                        $db->rollback();
                                        $db->execute("UNLOCK TABLES");
                                        throw new Exception("This annotationsQuestion can not be save, line : " . $annotation);
                                    }
                                }
                            }
                        }
                    }
                    /* ========================================= */
                    /*            create relations             */
                    /* ========================================= */
                    $relationsFile = new File($extractFolder->pwd() . DS . 'relations.txt', false, 0644);
                    if ($relationsFile->exists() && $relationsFile->size() != 0) {
                        $relationsText = $relationsFile->read();
                        $relations = preg_split("/[\r\n]+/", trim($relationsText));
                        $relationsMap = array();
                        $defaultColors = Configure::read('defaultColors');
                        $colorCont = 0;
                        foreach ($relations as $relation) {
                            //debug($types);
                            $relationSections = explode("\t", $relation);
                            if (sizeof($relationSections) > 1) {
                                $relationName = $relationSections[0];
                                $relationId = $relationSections[1];
                                $relationDirection = $relationSections[2];
                                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                                $this->Relation->create();
                                if ($this->Relation->save(
                                        array(
                                              'id' => $relationId,
                                              'name' => $relationName,
                                              'is_directed' => (strtolower($relationDirection) == "directed"),
                                              'marker' => (strtolower($relationDirection) == "directed") ? "arrow" : null,
                                              'project_id' => $this->Project->id,
                                              'colour' => $color
                                        )
                                    )
                                ) {
                                    
                                } else {
                                    $extractFolder->delete();
                                    debug($this->Relation->validationErrors);
                                    $db->rollback();
                                    $db->execute("UNLOCK TABLES");
                                    throw new Exception("This relation can not be save, line : " . $relation);
                                }
                            } else {
                                $extractFolder->delete();
                                $db->rollback();
                                $db->execute("UNLOCK TABLES");
                                debug($relationSections);
                                throw new Exception("This relation can not be save by incompatible sections , line: " . $relation);
                            }
                        }
                    }
                    $interRelationsFile = new File($extractFolder->pwd() . DS . 'interRelations.txt', false, 0644);
                    if ($interRelationsFile->exists() && $interRelationsFile->size() != 0) {
                        $interRelationsText = $interRelationsFile->read();
                        $interRelations = preg_split("/[\r\n]+/", trim($interRelationsText));
                        foreach ($interRelations as $interRelation) {
                            //debug($types);
                            $interRelationSections = explode("\t", $interRelation);
                            if (sizeof($interRelationSections) > 1) {
                                $annotationA = $interRelationSections[0];
                                $relationId = $interRelationSections[1];
                                $annotationB = $interRelationSections[2];
                                $this->AnnotationsInterRelations->create();
                                if (!$this->AnnotationsInterRelations->save(
                                        array(
                                              'annotation_a_id' => $annotationA,
                                              'relation_id' => $relationId,
                                              'annotation_b_id' => $annotationB,
                                        )
                                    )
                                ) {
                                    $extractFolder->delete();
                                    $db->rollback();
                                    $db->execute("UNLOCK TABLES");
                                    debug($this->AnnotationsInterRelations->validationErrors);
                                    throw new Exception("This inter-relation can not be save" . $interRelation);
                                }
                            } else {
                                $extractFolder->delete();
                                $db->rollback();
                                $db->execute("UNLOCK TABLES");
                                throw new Exception("This inter-relation can not be save" . $interRelation);
                            }
                        }
                    }
                    $extractFolder->delete();
                    $db->commit();
                    $db->execute("UNLOCK TABLES");
                    $this->Session->setFlash(__('Project has been created with documents imported'), 'success');
                    return $this->redirect(array(
                              'controller' => 'projects',
                              'action' => 'index'
                    ));
                }
                $extractFolder->delete();
            } else {
                $this->Session->setFlash(__('Choose almost one user and one file'));
            }
        }
        $userConditions = array(
              'group_id' => 2
        );
        $users = $this->Project->User->find('list', array(
              'conditions' => $userConditions
        ));
        $this->set(compact('users', 'maxAnnotation', 'maxType', 'maxRelation', 'maxInterRelations'));
    }

}
