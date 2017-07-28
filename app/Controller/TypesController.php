<?php

App::uses('AppController', 'Controller');

/**
 * Types Controller
 *
 * @property Type $Type
 */
class TypesController extends AppController {

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {
        $this->Type->recursive = 0;
        $this->paginate = array('fields' => array(' `Type`.`id`, `Type`.`project_id`, `Type`.`name`, `Type`.`colour`, `Project`.`id`, `Project`.`title`'));
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } else {
            $conditions = array('conditions' => array('OR' => $data));
            $this->paginate = $conditions;
            $this->set('search', $busqueda);
        }
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
            $cond['Type.name  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Type.description LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array('action' => 'index', 1));
        }
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        $this->Type->id = $id;
        if (!$this->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        }
        $contain = array('Question' => array('id', 'question'), 'Project' => array(
                    'id', 'title'));
        $this->set('type', $this->Type->find('first', array('contain' => $contain,
                  'conditions' => array('Type.id' => $id))));
        $this->set('type_id', $id);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($projectId = null) {
        if ($this->request->is('post')) {
            if (isset($projectId)) {
                $this->request->data['Type']['project_id'] = $projectId;
            }
            $this->Type->create();
            $roundsIds = $this->Type->Round->find('list', array('recursive' => -1,
                  'fields' => 'Round.id', 'conditions' => array('Round.project_id' => $this->request->data['Type']['project_id'])));
            //remove data for update in round view
            foreach ($roundsIds as $id) {
                $this->Session->delete('data' . $id);
            }
            if ($this->request->data['Type']['allRounds']) {
                $this->request->data['Round'] = array('Round' => $roundsIds);
            }
            if ($this->Type->save($this->request->data)) {
                $this->Session->setFlash(__('Type has been saved'), 'success');
                $this->redirect(array('action' => 'view', $this->Type->id));
            } else {
                $this->Session->setFlash(__('Type could not be saved. Please, try again.'));
            }
        }
        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array();
        if ($deleteCascade)
            $conditions = array('title !=' => 'Removing...');
        $projects = $this->Type->Project->find('list', array('recursive' => -1, 'conditions' => $conditions));
        if (empty($projects)) {
            $this->Session->setFlash(__('There are currently no project created to include some type. Please create a project.'));
            $redirect = $this->Session->read('redirect');
            $this->redirect($redirect);
        }
        $this->set('projects', $projects);
        $this->set('projectId', $projectId);
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->Type->id = $id;
        if (!$this->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Type->save($this->request->data)) {
                $roundsIds = $this->Type->Round->find('list', array('recursive' => -1,
                      'fields' => 'Round.id', 'conditions' => array('Round.project_id' => $this->request->data['Type']['project_id'])));
                //remove data for update in round view
                foreach ($roundsIds as $id) {
                    $this->Session->delete('data' . $id);
                }
                $this->Session->setFlash(__('Type has been saved'), 'success');
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            } else {
                $this->Session->setFlash(__('Type could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Type->find('first', array('recursive' => -1,
                  'conditions' => array('Type.id' => $id)));
        }
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->Annotation = $this->Type->Annotation;
        $this->TypesRound = $this->Type->TypesRound;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Type->id = $id;
        $enableJavaActions = Configure::read('enableJavaActions');
        if (!$this->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        } else {
            $redirect = $this->Session->read('redirect');
            $annotation = $this->Annotation->find('first', array('recursive' => -1,
                  'conditions' => array('Annotation.type_id' => $id)));
            if (empty($annotation)) {
                $this->CommonFunctions = $this->Components->load('CommonFunctions');
                $this->CommonFunctions->delete($id);
            } else if ($enableJavaActions) {
                $rounds = $this->TypesRound->find('all', array(
                      'fields' => array('TypesRound.round_id'),
                      'recursive' => -1,
                      'conditions' => array(
                            'type_id' => $id,
                      ),
                ));
                $rounds = Set::classicExtract($rounds, '{n}.TypesRound.round_id');
                return $this->deleteTypesScript(array($id), $rounds);
            }
            $this->Session->setFlash(__('Can not delete this type while there is some annotation of this type. Dispose of the all rounds and wait for all users open their round. Please try after'));
            $this->redirect($redirect);
        }
    }

    /**
     * deleteAll method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteSelected() {
        $this->Annotation = $this->Type->Annotation;
        $this->TypesRound = $this->Type->TypesRound;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $enableJavaActions = Configure::read('enableJavaActions');
            $deleteCascade = Configure::read('deleteCascade');
            $ids = json_decode($this->request->data['selected-items']);
            if ($enableJavaActions) {
                $tam = sizeof($ids);
                for ($i = 0; $i < $tam; $i++) {
                    $annotation = $this->Annotation->find('count', array('recursive' => -1,
                          'conditions' => array('type_id' => $ids[$i])));
                    if ($annotation == 0) {
                        $this->Type->delete($ids[$i], false);
                        unset($ids[$i]);
                    }
                }
                if (!empty($ids)) {
                    $rounds = $this->TypesRound->find('all', array(
                          'fields' => array('TypesRound.round_id'),
                          'recursive' => -1,
                          'conditions' => array(
                                'type_id' => $ids,
                          ),
                    ));
                    $rounds = Set::classicExtract($rounds, '{n}.TypesRound.round_id');
                    return $this->deleteTypesScript($ids, $rounds);
                } else {
                    return $this->correctResponseJson(array('success' => true));
                }
            } else {
                $tam = sizeof($ids);
                for ($i = 0; $i < $tam; $i++) {
                    $annotation = $this->Annotation->find('count', array('recursive' => -1,
                          'conditions' => array('type_id' => $ids[$i])));
                    if ($annotation != 0) {
                        unset($ids[$i]);
                    }
                }
                if (!empty($ids) && $this->Type->deleteAll(array('Type.id' => $ids), $deleteCascade)) {
                    $redirect = $this->Session->read('redirect');
                    if (!$this->request->is('ajax')) {
                        $this->Session->setFlash(__('Types selected without annotations have been deleted', "success"));
                        $redirect = $this->Session->read('redirect');
                        $this->redirect($redirect);
                    } else {
                        return $this->correctResponseJson(array('success' => true));
                    }
                } else {
                    if (!$this->request->is('ajax')) {
                        $this->Session->setFlash(__("Types selected haven't been deleted. Can not delete this type while there is some annotation of this type."));
                        $this->redirect(array('controller' => 'types', 'action' => 'index'));
                    } else {
                        return $this->correctResponseJson(array('success' => false,
                                  'error' => 'Not empty'));
                    }
                }
            }
        }
    }

    private function deleteTypesScript($types, $rounds) {
        $this->Project = $this->Type->Project;
        $this->Job = $this->Project->User->Job;
        $this->User = $this->Project->User;
        $group_id = $this->Session->read('group_id');
        if ($group_id == 1) {
            $user_id = $this->Session->read('user_id');
            $this->User->id = $user_id;
        } else {
            throw new Exception("Incorrect user");
        }
        $programName = "Automatic_Types_Remove";
        $this->Job->create();
        $data = array('user_id' => $user_id, 'round_id' => $rounds[0],
              'percentage' => 0, '' => $programName,
              'status' => 'Starting...');
        if ($this->Job->save($data)) {
            $this->Type->recursive = -1;
            $this->Type->updateAll(
                array('name' => "'Removing...'"), array('id' => $types)
            );
            $id = $this->Job->id;
            if (!empty($types) && !empty($rounds)) {
                $types = json_encode($types);
                $rounds = json_encode($rounds);
                $operationId = 10;
                $arguments = "$operationId\t$id\t$user_id\t$rounds\t$types";
                return $this->sendJob($id, $programName, $arguments);
            } else {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "The task could not be performed successfully.Empty rounds or empty types")));
            }
        }
    }

}
