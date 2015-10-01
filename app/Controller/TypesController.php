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
        $contain = array('Question' => array('id', 'question'), 'Project' => array('id', 'title'));
        $this->set('type', $this->Type->find('first', array('contain' => $contain, 'conditions' => array('Type.id' => $id))));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Type->create();
            $this->request->data['Type']['name'] = str_replace(' ', '_', $this->request->data['Type']['name']);
            if ($this->request->data['Type']['allRounds']) {
                $roundsIds = $this->Type->Round->find('list', array('recursive' => -1, 'fields' => 'Round.id', 'conditions' => array('Round.project_id' => $this->request->data['Type']['project_id'])));
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
            $this->request->data['Type']['name'] = str_replace(' ', '_', $this->request->data['Type']['name']);
            if ($this->Type->save($this->request->data)) {
                $this->Session->setFlash(__('Type has been saved'), 'success');
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            } else {
                $this->Session->setFlash(__('Type could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Type->find('first', array('recursive' => -1, 'conditions' => array('Type.id' => $id)));
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
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Type->id = $id;
        if (!$this->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        } else {
            $redirect = $this->Session->read('redirect');
            $annotation = $this->Type->Annotation->find('first', array('recursive' => -1, 'conditions' => array('Annotation.type_id' => $id)));
            if (empty($annotation)) {
                $deleteCascade = Configure::read('deleteCascade');
                if ($this->Type->delete($id, $deleteCascade)) {
                    $this->Session->setFlash(__('Type has been deleted', 'success'));
                    if ($redirect['controller'] == 'types') {
                        $redirect = array('controller' => 'types', 'action' => 'index');
                    }
                    $this->redirect($redirect);
                }
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
    public function deleteAll() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $deleteCascade = Configure::read('deleteCascade');
            $ids = json_decode($this->request->data['allTypes']);
            $tam = sizeof($ids);
            for ($i = 0; $i < $tam; $i++) {
                $annotation = $this->Type->Annotation->find('count', array('recursive' => -1, 'conditions' => array('type_id' => $ids[$i])));
                if ($annotation != 0) {
                    unset($ids[$i]);
                }
            }
            $redirect = $this->Session->read('redirect');
            if (!empty($ids) &&  $this->Type->deleteAll(array('Type.id' => $ids), $deleteCascade)) {
                $this->Session->setFlash(__('Types selected without annotations have been deleted'), 'success');
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            } else {
                $this->Session->setFlash(__("Types selected haven't been deleted. Can not delete this type while there is some annotation of this type."));
                $this->redirect(array('controller' => 'types', 'action' => 'index'));
            }
        }
    }

}
