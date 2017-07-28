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
    public function view($project_id = null, $user_id = null, $document_id = null) {
        if ($this->request->is(array('ajax'))) {
            $group_id = $this->Session->read('group_id');
            if ($group_id != 1) {
                $user_id = $this->Session->read('user_id');
            }
            $documentAssessment = $this->DocumentsAssessment->find('first', array(
                  'recursive' => -1,
                  'conditions' => array('user_id' => $user_id, 'project_id' => $project_id,
                        'document_id' => $document_id)));
            return $this->correctResponseJson(json_encode(array('success' => true,
                      "data" => $documentAssessment)));
        } else {
            $this->DocumentsAssessment->recursive = 0;
            $this->paginate = array(
                  'fields' => array('document_id', 'User.id', 'User.username', 'User.surname',
                        'User.image', 'User.email',
                        'User.image_type', 'project_id', 'positive', 'neutral', 'negative',
                        'about_author', 'topic', 'note', 'Document.title', 'Document.id',
                        'Project.id', 'Project.title'),
                  'limit' => 50,
                  'conditions' => array('project_id' => $project_id, 'document_id' => $document_id)
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
                    $data = array_merge($data, array('positive' => 1, 'neutral' => 0,
                          'negative' => 0));
                    break;
                case 'neutral' :
                    $data = array_merge($data, array('positive' => 0, 'neutral' => 1,
                          'negative' => 0));
                    break;
                case 'negative' :
                    $data = array_merge($data, array('positive' => 0, 'neutral' => 0,
                          'negative' => 1));
                    break;
            }
            if (isset($data['id']) && $data['id'] != '-1') {
                $id = $data['id'];
                if (!$this->DocumentsAssessment->exists($id)) {
                    return $this->correctResponseJson(json_encode(array('success' => false)));
                }
                $this->DocumentsAssessment->id = $id;
            } else {
                unset($data['id']);
                $this->DocumentsAssessment->create();
            }

            $data['user_id'] = $this->Session->read('user_id');
            $isEnd = $this->Session->read('isEnd');
            if ($this->DocumentsAssessment->save($data) && !$isEnd) {
                return $this->correctResponseJson(json_encode(array('success' => true)));
            } else {
                return $this->correctResponseJson(json_encode(array('success' => false,
                          "message" => "Ops! The document assessment could not be saved.")));
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

    public function export($project_id = null, $user_id = null) {
        $group_id = $this->Session->read('group_id');
        if ($group_id != 1) {
            throw new NotFoundException(__('Invalid group'));
        }
        $this->DocumentsAssessment->Project->id = $project_id;
        if (!$this->DocumentsAssessment->Project->exists()) {
            throw new NotFoundException(__('Invalid project'));
        }
        $assesments = $this->DocumentsAssessment->find('all', array(
              'contain' => array('Document' => array('external_id', 'title')),
              'recursive' => -1,
              'conditions' => array('user_id' => $user_id, 'project_id' => $project_id)
            )
        );
        $project = $this->DocumentsAssessment->Project->find('first', array(
              'fields' => array('title'),
              'recursive' => -1,
              'conditions' => array('id' => $project_id)
            )
        );
        $title = $project['Project']['title'];
        $title = $project['Project']['title'];
        $title = ltrim(substr($title, 0, 20) . '_assessment');
        if (!empty($assesments)) {
            $size = count($assesments);
            $lines = array();
            array_push($lines, "id\trate\tabout_author\ttopic\tnote");
            for ($index = 0; $index < $size; $index++) {
                $assesment = $assesments[$index]['DocumentsAssessment'];
                $document = $assesments[$index]['Document'];
                $line = "";
                if ($document['external_id'] != '') {
                    $line .= $document['external_id'];
                } else {
                    $line .= $document['title'];
                }
                if ($assesment['positive'] == 1) {
                    $line .= "\tRelevant document";
                } elseif ($assesment['neutral'] == 1) {
                    $line .= "\tNeutral document";
                } else if ($assesment['negative'] == 1) {
                    $line .= "\tIrrelevant document";
                } else {
                    $line .= "\tNULL";
                }
                $line .= "\t" . $assesment['about_author'];
                $line .= "\t" . $assesment['topic'];
                $line .= "\t" . $assesment['note'];
                array_push($lines, $line);
            }
            return $this->exportTsvDocument($lines, $title . ".tsv");
        } else {
            $this->Session->setFlash(__('There is no data to be exported'));
            return $this->redirect(array('controller' => 'projects', 'action' => 'view',
                      $project_id));
        }
    }

}
