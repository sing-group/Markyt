<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppController', 'Controller');

/**
 * CakePHP ProjectsNetworksController
 * @author Sing-pc
 */
class ProjectNetworksController extends AppController {

    public function getProgress($id = false) {
        $this->Project = $this->ProjectNetwork->Project;
        $this->Job = $this->Project->User->Job;
        $this->autoRender = false;
        $job = $this->Job->find('first', array(
              'recursive' => -1, //int
              //array of field names
              'fields' => array('Job.percentage', 'Job.status', 'Job.exception'),
              'conditions' => array('Job.id' => $id), //array of conditions
        ));
        return $this->correctResponseJson($job);
    }

    public function add($projectId = null) {
        $this->NetworkEdge = $this->ProjectNetwork->NetworkEdge;
        $this->Project = $this->ProjectNetwork->Project;
        $this->Round = $this->Project->Round;
        $this->User = $this->Project->User;
        $this->Relation = $this->Project->Relation;
        $this->Type = $this->Project->Type;
        $this->Job = $this->Round->User->Job;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data['ProjectNetwork']['Round'])) {
                $oldRoundId = $this->request->data['ProjectNetwork']['Round'];
                $round = $this->Round->find('first', array(
                      'recursive' => -1,
                      'conditions' => array(
                            'Round.id' => $oldRoundId
                      )
                ));
                if (empty($this->request->data['ProjectNetwork']['User'])) {
                    $this->Session->setFlash('You must choose at least one user');
                    $this->redirect(array(
                          'controller' => 'ProjectNetworks',
                          'action' => 'add',
                          $projectId
                    ));
                } else {
                    $rounds = $this->request->data['ProjectNetwork']['Round'];
                    $users = $this->request->data['ProjectNetwork']['User'];
                    $name = "\"" . $this->request->data['ProjectNetwork']['name'] . "\"";
                    $operation = $this->request->data['ProjectNetwork']['operation'];
                    $users = json_encode($users);
                    $rounds = json_encode($rounds);
                    $user_id = $this->Session->read('user_id');
                    $this->Job->create();
                    $data = array('user_id' => $user_id,
                          'percentage' => 0,
                          'status' => 'Starting...');
                    if ($this->Job->save($data)) {
                        $programName = "Creating $name network";
                        $id = $this->Job->id;
                        $operationId = 11;
                        $arguments = "$operationId\t$id\t$user_id\t$projectId\t$rounds\t$users\t$operation\t$name";
                        $this->sendJob($id, $programName, $arguments, false);
                        $this->Session->setFlash('We are creating the new network. Please be patient', 'information');
                        return $this->redirect(array(
                                  'controller' => 'jobs',
                        ));
                    }
                } //$this->Round->save($this->request->data)
            } else {
                $this->Session->setFlash('There are no data to be copied!');
                $this->redirect(array(
                      'controller' => 'ProjectNetworks',
                      'action' => 'add',
                      $projectId
                ));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->Project->id = $projectId;
            if (!$this->Round->Project->exists()) {
                throw new NotFoundException(__('Invalid round'));
            } //!$this->Round->exists()
            $rounds = $this->Round->find('list', array(
                  'conditions' => array('project_id' => $projectId)
            ));
            if (empty($rounds)) {
                $this->Session->setFlash('There is not rounds to select');
                $this->redirect(array(
                      'controller' => 'projects',
                      'action' => 'view',
                      $projectId
                ));
            } else {
                $users = $this->User->find('list', array(
                      'recursive' => -1, //int
                      'fields' => array('id', 'full_name'),
                      'joins' => array(
                            array(
                                  'table' => 'projects_users',
                                  'alias' => 'ProjectsUser',
                                  'type' => 'INNER',
                                  'conditions' => array(
                                        'user_id = User.id',
                                        'project_id' => $projectId
                                  )
                            ),
                      ),
                ));
            }
            $this->set('rounds', $rounds);
            $this->set('users', $users);
            $this->set("project_id", $projectId);
            $this->set('projectId', $projectId);
        }
    }

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

    public function getMultiResults($id) {
        $this->NetworkEdge = $this->ProjectNetwork->NetworkEdge;
        $this->Project = $this->ProjectNetwork->Project;
        $this->User = $this->Project->User;
        $this->Round = $this->Project->Round;
        $this->Relation = $this->Project->Relation;
        $this->Type = $this->Project->Type;
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        if (!$this->ProjectNetwork->hasAny(array("name" => $id))) {
            throw new NotFoundException(__('Invalid relation agreement result'));
        }
        $network = $this->ProjectNetwork->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'name' => $id
              )
        ));
        $results = json_decode($network["ProjectNetwork"]["JSON"], true);
        $operation = json_decode($network["ProjectNetwork"]["operation"], true);
        $rounds = $this->Round->find('list', array(
              'recursive' => -1,
              'conditions' => array(
                    'project_id' => $network["ProjectNetwork"]["project_id"],
                    'id' => $operation[0]
              )
        ));
        $users = $this->User->find('list', array(
              'recursive' => -1, //int
              'fields' => array('User.id', 'User.full_name'),
              'joins' => array(
                    array(
                          'table' => 'projects_users',
                          'alias' => 'ProjectsUsers',
                          'type' => 'INNER',
                          'conditions' => array(
                                'ProjectsUsers.user_id = User.id',
                                'ProjectsUsers.project_id' => $network["ProjectNetwork"]["project_id"]
                          )
                    ),
              ),
              'conditions' => array('id' => $operation[1]), //array of conditions
        ));
        $round_name = null;
        $user_name = null;
        $keys = array();
        if (count(($operation[0])) == 1) {
            $round_name = $rounds[$operation[0][0]];
            $sprint = "%s:" . $operation[0][0];
            $this->model = $this->User;
        } else {
            $user_name = $users[$operation[1][0]];
            $sprint = $operation[1][0] . ":%s";
            $this->model = $this->Round;
        }
        $this->model->virtualFields["total"] = "COUNT(AnnotationsInterRelations.id)";
        $relationsSummary = $this->model->find('list', array(
              'recursive' => -1, //int
              //array of field names
              'fields' => array($this->model->name . '.id', 'total'),
              'joins' => array(
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationA',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.' . strtolower($this->model->name) . '_id =' . $this->model->name . '.id'
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'AnnotationB',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.' . strtolower($this->model->name) . '_id =' . $this->model->name . '.id'
                          )
                    ),
                    array(
                          'table' => 'annotations_inter_relations',
                          'alias' => 'AnnotationsInterRelations',
                          'type' => 'INNER',
                          'conditions' => array(
                                'AnnotationA.id = AnnotationsInterRelations.annotation_a_id',
                                'AnnotationB.id = AnnotationsInterRelations.annotation_b_id',
                          )
                    ),
              ),
              'conditions' => array(
                    "AnnotationA.round_id" => $operation[0], "AnnotationA.user_id" => $operation[1],
                    "AnnotationB.round_id" => $operation[0], "AnnotationB.user_id" => $operation[1]
              ),
              'group' => array($this->model->name . '.id')
        ));
        $this->set(compact('users', 'rounds', 'results', 'round_name', 'user_name', 'sprint', 'relationsSummary', "id"));
    }

    public function confrontationDual($id = null) {
        $this->NetworkEdge = $this->ProjectNetwork->NetworkEdge;
        $this->Project = $this->ProjectNetwork->Project;
        $this->User = $this->Project->User;
        $this->Round = $this->Project->Round;
        $this->Relation = $this->Project->Relation;
        $this->Type = $this->Project->Type;
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        if (!$this->ProjectNetwork->hasAny(array("name" => $id))) {
            throw new NotFoundException(__('Invalid relation agreement result'));
        }
        $network = $this->ProjectNetwork->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'name' => $id
              )
        ));
        $results = json_decode($network["ProjectNetwork"]["JSON"], true);
        $operation = json_decode($network["ProjectNetwork"]["operation"], true);
        $rounds = $this->Round->find('list', array(
              'recursive' => -1,
              'conditions' => array(
                    'project_id' => $network["ProjectNetwork"]["project_id"],
                    'id' => $operation[0]
              )
        ));
        $users = $this->User->find('list', array(
              'recursive' => -1, //int
              'fields' => array('User.id', 'User.full_name'),
              'joins' => array(
                    array(
                          'table' => 'projects_users',
                          'alias' => 'ProjectsUsers',
                          'type' => 'INNER',
                          'conditions' => array(
                                'ProjectsUsers.user_id = User.id',
                                'ProjectsUsers.project_id' => $network["ProjectNetwork"]["project_id"]
                          )
                    ),
              ),
              'conditions' => array('id' => $operation[1]), //array of conditions
        ));
        $differentRelations = $this->Relation->find('all', array(
              'recursive' => -1, //int
              'conditions' => array('id' => $operation[2]), //array of conditions
              'order' => 'name ASC'
        ));
        $round_name = null;
        $user_name = null;
        $keys = array();
        if (count(($operation[0])) == 1) {
            $round_name = $rounds[$operation[0][0]];
            $sprint = "%s:" . $operation[0][0];
            $this->model = $this->User;
        } else {
            $user_name = $users[$operation[1][0]];
            $sprint = $operation[1][0] . ":%s";
            $this->model = $this->Round;
        }
        $section = $this->request->query["section"];
        $sections = explode("-VS-", $section);
        $section1 = explode(":", $sections[0]);
        $section2 = explode(":", $sections[1]);
        $results = $results[$section];
        $rowName = $users[$section1[0]] . ' in round ' . $rounds[$section1[1]];
        $colName = $users[$section2[0]] . ' in round ' . $rounds[$section2[1]];
        $this->set(compact('users', 'rounds', 'results', 'rowName', 'colName', 'differentRelations', "id", "section"));
    }

    /* ============================= */
    /* ============================= */
    /* ============================= */
    /* ============================= */

    public function confrontationSettingMultiUser($id = null) {
        $this->Project = $this->ProjectNetwork->Project;
        $this->Job = $this->Project->User->Job;
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            $data = $this->request->data;
            if (!empty($data['user']) && !empty($data['ProjectNetworks']['type'])) {
                $rounds = array($data['round']);
                $users = $data['user'];
                $edges = $data['ProjectNetworks']['type'];
                $operation = json_encode(array($rounds, $users, $edges));
                $name = md5($operation);
                $confrontationResult = $this->ProjectNetwork->find('first', array(
                      'fields' => array("name"),
                      'recursive' => -1, //int
                      //array of field names
                      'conditions' => array('ProjectNetwork.name' => $name, 'ProjectNetwork.created >=' => date("Y-m-d H:i:s", strtotime('-10 minutes'))), //array of conditions;
                ));
                $this->ProjectNetwork->deleteAll(array('ProjectNetwork.name' => $name), false);
                $this->Project->recursive = -1;
                $relationLevel = $this->Project->field('relation_level');
                if (!isset($relationLevel)) {
                    $relationLevel = 1;
                }
                $user_id = $this->Session->read('user_id');
                $this->Job->create();
                $programName = "NetworkAgreement";
                $data = array('user_id' => $user_id,
                      'percentage' => 0, '' => $programName,
                      'status' => 'Starting...');
                if ($this->Job->save($data)) {
                    $id = $this->Job->id;
                    $project_id = $this->Project->id;
                    $operationId = 11;
                    $arguments = "$operationId\t$id\t$user_id\t$project_id\t" . json_encode($rounds) . "\t" . json_encode($users) . "\t" . json_encode($edges) . "\t" . json_encode($operation) . "\t" . $name . "\t" . $relationLevel;
                    $this->sendJob($id, $programName, $arguments, false);
                    return $this->correctResponseJson(array("job" => $id, "key" => $name));
                }
            } else {
                $this->layout = false;
                $this->autoRender = false;
                return $this->Session->setFlash(__('You must choose more than one user and at least one relation type'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        $this->getConfrontationData($users, $rounds, $this->Project->id);
        $cond = array(
              'project_id' => $this->Project->id
        );
        $types = $this->Project->Relation->find('list', array(
              'conditions' => $cond
        ));
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
        $this->set('types', $types);
    }

    public function confrontationSettingMultiRound($id = null) {
        $this->Project = $this->ProjectNetwork->Project;
        $this->Job = $this->Project->User->Job;
        $this->Project->id = $id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->Session->write('start', microtime(true));
            $data = $this->request->data;
            if (!empty($data['round']) && !empty($data['ProjectNetworks']['type'])) {
                $rounds = $data['round'];
                $users = array($data['user']);
                $edges = $data['ProjectNetworks']['type'];
                $operation = json_encode(array($rounds, $users, $edges));
                $name = md5($operation);
                $confrontationResult = $this->ProjectNetwork->find('first', array(
                      'fields' => array("name"),
                      'recursive' => -1, //int
                      //array of field names
                      'conditions' => array('ProjectNetwork.name' => $name, 'ProjectNetwork.created >=' => date("Y-m-d H:i:s", strtotime('-10 minutes'))), //array of conditions;
                ));
                $this->ProjectNetwork->deleteAll(array('ProjectNetwork.name' => $name), false);
                $this->Project->recursive = -1;
                $relationLevel = $this->Project->field('relation_level');
                if (!isset($relationLevel)) {
                    $relationLevel = 1;
                }
                $user_id = $this->Session->read('user_id');
                $this->Job->create();
                $programName = "NetworkAgreement";
                $data = array('user_id' => $user_id,
                      'percentage' => 0, '' => $programName,
                      'status' => 'Starting...');
                if ($this->Job->save($data)) {
                    $id = $this->Job->id;
                    $project_id = $this->Project->id;
                    $operationId = 11;
                    $arguments = "$operationId\t$id\t$user_id\t$project_id\t" . json_encode($rounds) . "\t" . json_encode($users) . "\t" . json_encode($edges) . "\t" . json_encode($operation) . "\t" . $name . "\t" . $relationLevel;
                    $this->sendJob($id, $programName, $arguments, false);
                    return $this->correctResponseJson(array("job" => $id, "key" => $name));
                }
            } else {
                $this->layout = false;
                $this->autoRender = false;
                return $this->Session->setFlash(__('You must choose more than one user and at least one relation type'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        $this->getConfrontationData($users, $rounds, $this->Project->id);
        $cond = array(
              'project_id' => $this->Project->id
        );
        $types = $this->Project->Relation->find('list', array(
              'conditions' => $cond
        ));
        $this->set(compact('users', 'rounds'));
        $this->set('project_id', $this->Project->id);
        $this->set('types', $types);
    }

    public function viewRelationsTable($id) {
        $this->NetworkEdge = $this->ProjectNetwork->NetworkEdge;
        $this->Project = $this->ProjectNetwork->Project;
        $this->User = $this->Project->User;
        $this->Round = $this->Project->Round;
        $this->Relation = $this->Project->Relation;
        $this->Type = $this->Project->Type;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotationsInterRelation = $this->Annotation->AnnotationsInterRelation;
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        if (!$this->ProjectNetwork->hasAny(array("name" => $id))) {
            throw new NotFoundException(__('Invalid relation agreement result'));
        }
        $network = $this->ProjectNetwork->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'name' => $id
              )
        ));
        $results = json_decode($network["ProjectNetwork"]["JSON"], true);
        $operation = json_decode($network["ProjectNetwork"]["operation"], true);
        $rounds = $this->Round->find('list', array(
              'recursive' => -1,
              'conditions' => array(
                    'project_id' => $network["ProjectNetwork"]["project_id"],
                    'id' => $operation[0]
              )
        ));
        $users = $this->User->find('list', array(
              'recursive' => -1, //int
              'fields' => array('User.id', 'User.full_name'),
              'joins' => array(
                    array(
                          'table' => 'projects_users',
                          'alias' => 'ProjectsUsers',
                          'type' => 'INNER',
                          'conditions' => array(
                                'ProjectsUsers.user_id = User.id',
                                'ProjectsUsers.project_id' => $network["ProjectNetwork"]["project_id"]
                          )
                    ),
              ),
              'conditions' => array('id' => $operation[1]), //array of conditions
        ));
        $differentRelations = $this->Relation->find('all', array(
              'recursive' => -1, //int
              'conditions' => array('id' => $operation[2]), //array of conditions
        ));
        $network = $this->ProjectNetwork->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'name' => $id
              )
        ));
        $project_id = $network['ProjectNetwork']['project_id'];
        $edges = $this->Relation->find('all', array(
              'recursive' => -1,
              'conditions' => array('Relation.project_id' => $project_id),
        ));
        $edges = Set::combine($edges, '{n}.Relation.id', '{n}.Relation');
        $types = $this->Type->find("all", array(
              "recursive" => -1,
              "fieds" => array("id", "name", "colour"),
              "conditions" => array("project_id" => $project_id),
        ));
        $types = Set::combine($types, '{n}.Type.id', '{n}.Type');
        $round_name = null;
        $user_name = null;
        $section = $this->request->query["section"];
        $section = $this->request->query["section"];
        $sections = explode("-VS-", $section);
        $section1 = explode(":", $sections[0]);
        $section2 = explode(":", $sections[1]);
        $subsection = $this->request->query["subSection"];
        $typeA = (isset($this->request->query["relatlionTypeA"])) ? $this->request->query["relatlionTypeA"] : null;
        $typeB = (isset($this->request->query["relatlionTypeB"])) ? $this->request->query["relatlionTypeB"] : null;
        $normalQuery = true;
        $conditions = array();
        switch ($subsection) {
            case "TP":
                if ($typeA == $typeB) {
                    $results = $results[$section]["TPEdgesSameType"];
                    $relations = array_merge($results[$typeA], $results[$typeB]);
                    $ids = Set::combine($relations, '{n}.id', '{n}.id');
                    $conditions = array("AnnotationsInterRelation.id" => $ids);
                } else {
                    $normalQuery = false;
                }
                break;
            case "FP":
                $results = $results[$section]["FPEdges"];
                $relations = $results[$typeA];
                $ids = Set::combine($relations, '{n}.id', '{n}.id');
                $conditions = array("AnnotationsInterRelation.id" => $ids);
                $user_name = $users[$section1[0]];
                $round_name = $rounds[$section1[1]];
                break;
            case "FN":
                $results = $results[$section]["FNEdges"];
                $relations = $results[$typeA];
                $ids = Set::combine($relations, '{n}.id', '{n}.id');
                $conditions = array("AnnotationsInterRelation.id" => $ids);
                $user_name = $users[$section2[0]];
                $round_name = $rounds[$section2[1]];
                break;
            default:
                break;
        }
        if ($normalQuery) {
            $fields = array(
                  'AnnotationsInterRelation.relation_id',
                  'AnnotationA.id',
                  'AnnotationA.annotated_text',
                  'AnnotationA.init',
                  'AnnotationA.end',
                  'AnnotationA.section',
                  'AnnotationA.type_id',
                  'AnnotationA.user_id',
                  'AnnotationA.round_id',
                  'AnnotationB.id',
                  'AnnotationB.annotated_text',
                  'AnnotationB.init',
                  'AnnotationB.end',
                  'AnnotationB.section',
                  'AnnotationB.type_id',
                  'AnnotationB.user_id',
                  'AnnotationB.round_id',
                  'Document.id',
                  'Document.external_id',
                  'Document.title',
            );
            $annotationsInterRelations = $this->AnnotationsInterRelation->find('all', array(
                  'recursive' => -1,
                  'fields' => $fields,
                  'conditions' => $conditions,
                  'joins' => array(
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationA',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'AnnotationA.id = AnnotationsInterRelation.annotation_a_id',
                              )
                        ),
                        array(
                              'table' => 'documents',
                              'alias' => 'Document',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'Document.id = AnnotationA.document_id'
                              )
                        ),
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationB',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'AnnotationB.id = AnnotationsInterRelation.annotation_b_id',
                              )
                        ),
                  ),
                  "order" => array("AnnotationA.annotated_text" => "ASC", "AnnotationB.annotated_text" => "ASC")
            ));
        } else {
            $fields = array(
                  'AnnotationsInterRelation.relation_id',
                  'AnnotationsInterRelation.id',
                  'AnnotationsInterRelation.annotation_a_id',
                  'AnnotationsInterRelation.annotation_B_id',
                  'AnnotationsInterRelationB.annotation_a_id',
                  'AnnotationsInterRelationB.annotation_B_id',
                  'AnnotationsInterRelationB.relation_id',
                  'AnnotationsInterRelationB.id',
                  'AnnotationA.id',
                  'AnnotationA.annotated_text',
                  'AnnotationA.init',
                  'AnnotationA.end',
                  'AnnotationA.section',
                  'AnnotationA.type_id',
                  'AnnotationA.user_id',
                  'AnnotationA.round_id',
                  'AnnotationB.id',
                  'AnnotationB.annotated_text',
                  'AnnotationB.init',
                  'AnnotationB.end',
                  'AnnotationB.section',
                  'AnnotationB.type_id',
                  'AnnotationB.user_id',
                  'AnnotationB.round_id',
                  'AnnotationC.user_id',
                  'AnnotationC.round_id',
                  'AnnotationD.user_id',
                  'AnnotationD.round_id',
                  'Document.id',
                  'Document.external_id',
                  'Document.title',
            );
            $annotationsInterRelationId = Set::classicExtract($results[$section]["TPEdges1"][$typeA . ":" . $typeB], '{n}.id');
            $annotationsInterRelationBId = Set::classicExtract($results[$section]["TPEdges2"][$typeB . ":" . $typeA], '{n}.id');
            $orCoditions = array();
            $total = count($annotationsInterRelationId);
            for ($index = 0; $index < $total; $index++) {
                array_push($orCoditions, array(
                      "AnnotationsInterRelation.id" => $annotationsInterRelationId[$index],
                      "AnnotationsInterRelationB.id" => $annotationsInterRelationBId[$index],
                ));
            }
            $conditions = array(
                  'OR' => $orCoditions
            );
            $annotationsInterRelations = $this->AnnotationsInterRelation->find('all', array(
                  'recursive' => -1,
                  'fields' => $fields,
                  'conditions' => $conditions,
                  'joins' => array(
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationA',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'AnnotationA.id = AnnotationsInterRelation.annotation_a_id',
                              )
                        ),
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationB',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'AnnotationB.id = AnnotationsInterRelation.annotation_b_id',
                              )
                        ),
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationC',
                              'type' => 'INNER',
                              'conditions' => array(
                              )
                        ),
                        array(
                              'table' => 'annotations',
                              'alias' => 'AnnotationD',
                              'type' => 'INNER',
                              'conditions' => array(
                              )
                        ),
                        array(
                              'table' => 'annotations_inter_relations',
                              'alias' => 'AnnotationsInterRelationB',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'AnnotationC.id = AnnotationsInterRelationB.annotation_a_id',
                                    'AnnotationD.id = AnnotationsInterRelationB.annotation_b_id',
                              )
                        ),
                        array(
                              'table' => 'documents',
                              'alias' => 'Document',
                              'type' => 'INNER',
                              'conditions' => array(
                                    'Document.id = AnnotationA.document_id'
                              )
                        ),
                  ),
                  "order" => array("AnnotationA.annotated_text" => "ASC", "AnnotationB.annotated_text" => "ASC")
            ));
        }
        $this->set("relationsMap", $edges);
        $this->set("typesMap", $types);
        $this->set(compact('annotationsInterRelations', 'project_id', 'round_name', "user_name", "normalQuery", "rounds", "users"));
    }

    public function export($id) {
        if (!$this->ProjectNetwork->exists($id)) {
            throw new NotFoundException(__('Invalid network'));
        }
        $this->Project = $this->ProjectNetwork->Project;
        $this->Type = $this->Project->Type;
        $network = $this->ProjectNetwork->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $id
              )
        ));
        $project_id = $network['ProjectNetwork']['project_id'];
        $types = $this->Type->find("list", array(
              "recursive" => -1,
              "fieds" => array("id", "name"),
              "conditions" => array("project_id" => $project_id),
        ));
        $annotationsInterRelations = $this->getRelationsOfNetwork($id);
        if (!empty($annotationsInterRelations)) {
            $header = array('document_id',
                  'AnnotationA_text',
                  'AnnotationA_section',
                  'AnnotationA_init',
                  'AnnotationA_end',
                  'AnnotationA_type',
                  'Relation',
                  'AnnotationB_text',
                  'AnnotationB_section',
                  'AnnotationB_init',
                  'AnnotationB_end',
                  'AnnotationB_type');
            $textToExport = array(implode("\t", $header));
            foreach ($annotationsInterRelations as $annotationsRelation) {
                if (isset($annotationsRelation["Document"]["external_id"])) {
                    $document = $annotationsRelation["Document"]["external_id"];
                } else {
                    $document = $annotationsRelation["Document"]["title"];
                }
                $typeA = $types[$annotationsRelation["AnnotationA"]["type_id"]];
                $typeB = $types[$annotationsRelation["AnnotationB"]["type_id"]];
                array_push($textToExport, $document . "\t" .
                    $annotationsRelation["AnnotationA"]["annotated_text"] . "\t" .
                    $annotationsRelation["AnnotationA"]["section"] . "\t" .
                    $annotationsRelation["AnnotationA"]["init"] . "\t" .
                    $annotationsRelation["AnnotationA"]["end"] . "\t" .
                    $typeA . "\t" .
                    $annotationsRelation["Relation"]["name"] . "\t" .
                    $annotationsRelation["AnnotationB"]["annotated_text"] . "\t" .
                    $annotationsRelation["AnnotationB"]["section"] . "\t" .
                    $annotationsRelation["AnnotationB"]["init"] . "\t" .
                    $annotationsRelation["AnnotationB"]["end"] . "\t" .
                    $typeB);
            }
            $name = $network['ProjectNetwork']['name'];
            $name = ltrim(substr($name, 0, 20) . '_relations');
            return $this->exportTsvDocument($textToExport, $name . ".tsv");
        } else {
            $this->Session->setFlash(__('There is no data to be exported'));
            return $this->redirect(array('controller' => 'rounds', 'action' => 'view',
                      $round_id));
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
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id, 'name');
    }

    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('name');
    }

}
