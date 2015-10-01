<?php

App::uses('AppController', 'Controller');

/**
 * DocumentsAssessments Controller
 *
 * @property DocumentsAssessment $DocumentsAssessment
 * @property PaginatorComponent $Paginator
 */
class DocumentsAssessmentsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->DocumentsAssessment->recursive = 0;
        $this->set('documentsAssessments', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($project_id = null, $document_id = null, $user_id = null) {

        if ($this->request->is(array('ajax'))) {
            $group_id = $this->Session->read('group_id');
            if ($group_id != 1) {
                $user_id = $this->Session->read('user_id');
            }
            
            $documentAssessment = $this->DocumentsAssessment->find('first', array('recursive' => -1,
                'conditions' => array('user_id' => $user_id, 'project_id' => $project_id, 'document_id' => $document_id)));
            return $this->correctResponseJson(json_encode($documentAssessment));
        } else {
            $this->DocumentsAssessment->recursive = 0;
            $this->paginate = array(
                'fields' => array('document_id', 'User.id', 'User.username', 'User.image', 'User.image_type', 'project_id', 'positive', 'neutral', 'negative',
                    'about_author', 'topic', 'note', 'Document.title', 'Document.id', 'Project.id', 'Project.title'),
                'limit' => 50,
                'conditions' => array('project_id' => $project_id, 'document_id' => $document_id)
//                'order' => array(
//                    'Post.title' => 'asc'
//                )
            );
            $this->set('documentsAssessments', $this->Paginator->paginate());
            $this->set('project_id', $project_id);
        }
    }

    /**
     * add method
     *
     * @return void
     */
    public function save() {
        if ($this->request->is(array('post', 'put'))) {
            $data = $this->request->data['DocumentsAssessments'];
            switch ($data['rate']) {
                case 'positive' :
                    $data = array_merge($data, array('positive' => 1, 'neutral' => 0, 'negative' => 0));
                    break;
                case 'neutral' :
                    $data = array_merge($data, array('positive' => 0, 'neutral' => 1, 'negative' => 0));
                    break;
                case 'negative' :
                    $data = array_merge($data, array('positive' => 0, 'neutral' => 0, 'negative' => 1));
                    break;
            }
            if (isset($data['id']) && $data['id'] != '') {
                $id = $data['id'];
                if (!$this->DocumentsAssessment->exists($id)) {
                    return $this->correctResponseJson(json_encode(array('success' => false)));
                }
            } else {

                $this->DocumentsAssessment->create();
            }
            $data['user_id'] = $this->Session->read('user_id');

            if ($this->DocumentsAssessment->save($data)) {
                return $this->correctResponseJson(json_encode(array('success' => true)));
            } else {
                return $this->correctResponseJson(json_encode(array('success' => false)));
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
    public function edit($id = null) {
        if (!$this->DocumentsAssessment->exists($id)) {
            throw new NotFoundException(__('Invalid documents assessment'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->DocumentsAssessment->save($this->request->data)) {
                $this->Session->setFlash(__('The documents assessment has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The documents assessment could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('DocumentsAssessment.' . $this->DocumentsAssessment->primaryKey => $id));
            $this->request->data = $this->DocumentsAssessment->find('first', $options);
        }
        $documents = $this->DocumentsAssessment->Document->find('list');
        $users = $this->DocumentsAssessment->User->find('list');
        $projects = $this->DocumentsAssessment->Project->find('list');
        $this->set(compact('documents', 'users', 'projects'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->DocumentsAssessment->id = $id;
        if (!$this->DocumentsAssessment->exists()) {
            throw new NotFoundException(__('Invalid documents assessment'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->DocumentsAssessment->delete()) {
            $this->Session->setFlash(__('The documents assessment has been deleted.'));
        } else {
            $this->Session->setFlash(__('The documents assessment could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

}
