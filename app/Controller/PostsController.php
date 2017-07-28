<?php

App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController {

    public $components = array('Cookie');

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {
        $this->Post->recursive = -1;
        $this->Post->contain(false, array('User' => array('username', 'surname',
                    'id')));
        $this->paginate = array('fields' => array(' `Post`.`id`, `Post`.`title`, `Post`.`created`, `Post`.`modified`'));
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        }//$post == null
        else if ($post == 1 && !empty($data)) {
            $data = $this->Session->read('data');
            $busqueda = $this->Session->read('search');
            $conditions = array('conditions' => array('OR' => $data));
            $this->paginate = $conditions;
            $this->set('search', $busqueda);
        }//$post == 1
        else {
            $user_id = $this->Session->read('user_id');
            if (isset($user_id)) {
                $conditions = array('conditions' => array('user_id' => $user_id));
                $this->paginate = $conditions;
                $this->set('search', '');
            }//isset($user_id)
            else {
                $this->redirect(array('action' => 'index'));
            }
        }
        $name = strtolower($this->name);
        $this->set($name, $this->paginate());
    }

    /**
     * publicIndex method
     * @param boolean $post
     * @return void
     */
    public function publicIndex() {
        $id_user = $this->Session->read('user_id');
        if (!isset($id_user)) {
            $user = $this->Post->User->find('first', array('recursive' => -1));
            if (empty($user)) {
                $this->Session->setFlash(__('Not yet  created a user in the application. Please create your user.'));
                $this->redirect(array('controller' => 'users', 'action' => 'register'));
            }
            $this->Post->recursive = -1;
            $this->Post->contain(false, array('User' => array('username', 'surname',
                        'full_name', 'image', 'image_type',
                        'id')));
            $this->paginate = array('limit' => 5, 'order' => array('modified' => 'DESC'));
            //$post
            $name = strtolower($this->name);
            $this->set("login", true);
            $this->set($name, $this->paginate());
            $loginIntent = $this->Session->read('loginIntent');
            if (isset($loginIntent)) {
                $this->request->data = $loginIntent;
            }
        }//!isset($id_user)
        else {
            $this->redirect(array('controller' => 'users', 'action' => 'logout'));
        }
    }

    /**
     * postsSearch method
     * @return void
     */
    public function search() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $search = trim($this->request->data[$this->name]['search']);
            $cond = array();
            $cond['Post.title  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Post.body  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Post.modified  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Post.created  LIKE'] = '%' . addslashes($search) . '%';
            $user_id = $this->Session->read('user_id');
            if (!isset($user_id)) {
                $cookie = array();
                $cookie['data'] = $cond;
                $cookie['search'] = $search;
                $this->Cookie->write('Post.search', $cookie, true, '300');
                $this->redirect(array('action' => 'publicIndex', 1));
            }//!isset($user_id)
            else {
                $this->Session->write('data', $cond);
                $this->Session->write('search', $search);
                $this->redirect(array('action' => 'index', 1));
            }
        } //$this->request->is('post') || $this->request->is('put')
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Post->exists($id)) {
            throw new NotFoundException(__('Invalid post'));
        }//!$this->Post->exists($id)
        $contain = array('User' => array('username', 'surname', 'id'));
        $options = array('recursive' => 0, 'contain' => $contain, 'conditions' => array(
                    'Post.' . $this->Post->primaryKey => $id));
        $this->set('post', $this->Post->find('first', $options));
        $this->set('post_id', $id);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create();
            $user_id = $this->Session->read('user_id');
            $this->request->data['Post']['user_id'] = $user_id;
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('Post has been saved'), 'success');
                $this->redirect(array('action' => 'index'));
            }//$this->Post->save($this->request->data)
            else {
                $this->Session->setFlash(__('Post could not be saved. Please, try again.'));
            }
        }//$this->request->is('post')
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Post->exists($id)) {
            throw new NotFoundException(__('Invalid post'));
        }//!$this->Post->exists($id)
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('Post has been saved'), 'success');
                $this->redirect(array('action' => 'index'));
            }//$this->Post->save($this->request->data)
            else {
                $this->Session->setFlash(__('Post could not be saved. Please, try again.'));
            }
        }//$this->request->is('post') || $this->request->is('put')
        else {
            $options = array('conditions' => array('Post.' . $this->Post->primaryKey => $id));
            $this->request->data = $this->Post->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id, 'title');
    }

    /**
     * deleteAll method
     *
     * @throws NotFoundException
     * @return void
     */
    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('title');
    }

}
