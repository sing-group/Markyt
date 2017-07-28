<?php

App::uses('AppController', 'Controller');

/**
 * Questions Controller
 *
 * @property Question $Question
 */
class QuestionsController extends AppController {

    /**
     * index method
     * @param string $id
     * @return void
     */
    public function index($post = null) {
        $this->Question->recursive = 0;
        $this->paginate = array('fields' => array(' `Question`.`id`, `Question`.`question`, `Type`.`id`, `Type`.`name`'));
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } else {
            $conditions = array('conditions' => $data);
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
            $cond['Question.question  LIKE'] = '%' . addslashes($search) . '%';
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
        $this->Question->id = $id;
        if (!$this->Question->exists()) {
            throw new NotFoundException(__('Invalid question'));
        }
        $this->set('question', $this->Question->read(null, $id));
        $answers = $this->Question->AnnotationsQuestion->find('all', array('order' => 'AnnotationsQuestion.annotation_id ASC',
              'conditions' => array('AnnotationsQuestion.question_id' => $this->Question->id)));
        $this->set('question', $this->Question->find('first', array('contain' => 'Question',
                  'conditions' => array('User.id' => $id))));
        $answersMap = NULL;
        foreach ($answers as $answer) {
            $answersMap[$answer['AnnotationsQuestion']['annotation_id']] = $answer['AnnotationsQuestion']['answer'];
        }
        $this->set('answers', $answersMap);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $this->Question->create();
            if ($this->Question->save($this->request->data)) {
                return $this->correctResponseJson(array('success' => true, 'id' => $this->Question->id));
            } else {
                return $this->correctResponseJson(array('success' => false));
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit() {
        $this->autoRender = false;
        $this->Question->id = $this->request->data['id'];
        if (!$this->Question->exists()) {
            return $this->correctResponseJson(array('success' => false));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Question->save($this->request->data)) {
                return $this->correctResponseJson(array('success' => true));
            } else {
                return $this->correctResponseJson(array('success' => false));
            }
        }
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id) {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id);
    }

    /**
     * deleteAll method
     *
     * @throws MethodNotAllowedException
     * @return void
     */
    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('question');
    }

}
