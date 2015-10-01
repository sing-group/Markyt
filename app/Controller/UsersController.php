<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

    var $helpers = array('Session');
    public $components = array('Cookie');

    public function beforeFilter() {
        $this->Auth->autoRedirect = false;
        parent::beforeFilter();
    }

    /**
     * login method
     *
     * @return void
     */
    public function login() {
        $id_user = $this->Session->read('user_id');
        $group_id = $this->Auth->user('group_id');
        if (!isset($id_user)) {
            if (empty($this->data)) {
                $cookie = $this->Cookie->read('Auth.User');
                if (!is_null($cookie)) {
                    if ($this->Auth->login($cookie)) {
                        $id_user = $this->Auth->user('id');
                        $this->Session->write('user_id', $id_user);
                        $username = $this->Auth->user('username');
                        $this->Session->write('username', $username);
                        $email = $this->Auth->user('email');
                        $this->Session->write('email', $email);
                        $group_id = $this->Auth->user('group_id');
                        $this->Session->write('group_id', $group_id);
                        if ($group_id == 1)
                            $this->redirect(array('controller' => 'Projects', 'action' => 'index'));
                        else
                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                    } else {// Delete invalid Cookie
                        $this->Cookie->destroy('Auth.User');
                        $this->Session->setFlash(__('Your username or pasword is incorrect'));
                        $this->redirect(array('controller' => 'Posts', 'action' => 'publicIndex'));
                    }
                }

                $this->Session->setFlash(__('You are not authorized to access this location (user Login)'));
            }
            if ($this->request->is('post')) {
                if ($this->Auth->login()) {
                    $id_user = $this->Auth->user('id');
                    $this->Session->write('user_id', $id_user);
                    $username = $this->Auth->user('username');
                    $this->Session->write('username', $username);
                    $email = $this->Auth->user('email');
                    $this->Session->write('email', $email);
                    $group_id = $this->Auth->user('group_id');
                    $this->Session->write('group_id', $group_id);
                    if (!empty($this->data) && $this->data['User']['remember_me']) {
                        $cookie = array();
                        $cookie['username'] = $username;
                        $cookie['password'] = $this->data['User']['password'];
                        $cookie['user_id'] = $id_user;
                        $cookie['group_id'] = $group_id;
                        $this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
                        unset($this->data['User']['remember_me']);
                    }
                    if ($group_id == 1)
                        $this->redirect(array('controller' => 'Projects', 'action' => 'index'));
                    else
                        $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Your username or pasword is incorrect'));
                }
            }
            $this->redirect(array('controller' => 'Posts', 'action' => 'publicIndex'));
        } else {
            $this->autoRender = false;
            $this->redirect(array('action' => 'logout'));
        }
    }

    /**
     * login method
     *
     * @return void
     */
    public function logout() {
        $this->Session->destroy();
        $this->Cookie->destroy();
        $this->redirect(array('controller' => 'Posts', 'action' => 'publicIndex'));
    }

    /**
     * recoveryAccount method
     *
     * @return void
     */
    public function recoverAccount() {
        $id_user = $this->Session->read('user_id');
        if (!isset($id_user)) {
            if ($this->request->is('post') || $this->request->is('put')) {
                $userFind = $this->User->find('first', array('recursive' => -1, 'fields' => array('id', 'username', 'email'), 'conditions' => array('User.email' => $this->request->data['User']['email'])));
                if (isset($userFind)) {
                    $newPassword = $this->randomAlphaNum();
                    $emailProfile = Configure::read('emailProfile');
                    $Email = new CakeEmail($emailProfile);
                    $Email->from(array(
                        'Marky@webAannotation.com' => 'Marky'
                    ));
                    $Email->to($userFind['User']['email']);
                    $Email->subject('Marky recover account');
                    $Email->emailFormat('both');
                    $Email->template('recoverMarky');
                    $Email->viewVars(array('user' => $userFind['User']['username'], 'password' => $newPassword));
                    $send = $Email->send();
                    if ($send) {
                        $userFind['User']['password'] = $newPassword;
                        $this->User->id = $userFind['User']['id'];
                        if ($this->User->save($userFind)) {
                            $this->Session->setFlash(__('Your password has been changed. Please check your email'), 'success');
                            $this->redirect(array('controller' => 'posts', 'action' => 'publicIndex'));
                        }
                    } else {
                        CakeLog::write('MailLog', 'Error send  password to: ' . $sendByMail['email']);
                    }
                }
                $this->Session->setFlash(__('This email does not exist or failed to send the new password'));
                $this->redirect(array('controller' => 'posts', 'action' => 'publicIndex'));
            }
        } else {
            $this->autoRender = false;
            $this->redirect(array('action' => 'logout'));
        }
    }

    /**
     * register method
     *
     * @return void
     */
    public function register() {

        $oneUser = $this->User->find('count');
        //si existe almenos un usuario creado lo mandamos a login
        if ($oneUser == 0) {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->User->create();
                $this->request->data['User']['group_id'] = 1;
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('You can now enter the application with your username and password'), 'success');
                    $this->redirect(array('controller' => 'posts', 'action' => 'publicIndex'));
                } else {
                    $this->Session->setFlash(__('Error already exists a user with that name'));
                }
            }
        } else {
            $this->Session->setFlash(__('This option only works if there is no registered user. If you do not already have a user, contact your administrator'));
            $this->redirect(array('action' => 'login'));
        }
    }

    /**
     * index method
     * @param boolean $id
     * @return void
     */
    public function index($post = null) {

        $this->User->recursive = 0;
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
     *
     * @return void
     */
    public function search() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $search = trim($this->request->data[$this->name]['search']);
            $cond = array();
            $cond['User.username  LIKE'] = '%' . addslashes($search) . '%';
            $cond['User.surname  LIKE'] = '%' . addslashes($search) . '%';
            $cond['User.email  LIKE'] = '%' . addslashes($search) . '%';
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
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $contain = array('Project' => array('id', 'title', 'created', 'modified'), 'Group' => array('name'));
        $this->set('user', $this->User->find('first', array('contain' => $contain, 'conditions' => array('User.id' => $id))));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->User->create();
            //limpiamos las variables para que sea imposible un admin con proyectos asociados
            if ($this->request->data['User']['group_id'] == 1) {
                unset($this->request->data['User']['allRounds']);
                unset($this->request->data['Project']);
            }
            if ($this->User->save($this->request->data)) {
                if ($this->request->data['User']['allRounds']) {
                    $allData = array();
                    $user_id = $this->User->id;
                    foreach ($this->request->data['Project']['Project'] as $projectId) {
                        //dado que en la funcion start Round se ha arreglado para insertar un nuevo round por documento si este no existe
                        //nos podemos ahorrar otro bucle For para cada documento lo que agiliza bastante este proceso.
                        $documentsId = $this->User->Project->DocumentsProject->find('first', array('recursive' => -1, 'fields' => 'DocumentsProject.document_id', 'order' => 'DocumentsProject.Document_id ASC', 'conditions' => array('DocumentsProject.project_id' => $projectId)));
                        if (!empty($documentsId)) {
                            $documentsId = $documentsId['DocumentsProject']['document_id'];
                            $roundsIds = $this->User->Project->Round->find('list', array('recursive' => -1, 'fields' => 'Round.id', 'conditions' => array('Round.project_id' => $projectId)));
                            foreach ($roundsIds as $RId) {
                                array_push($allData, array('user_id' => $user_id, 'document_id' => $documentsId, 'round_id' => $RId));
                            }
                        }
                    }
                    if (!empty($allData)) {
                        if (!$this->User->UsersRound->saveMany($allData))
                            throw new NotFoundException(__('Unknown exception'));
                    }
                }
                $this->Session->setFlash(__('User has been saved'), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('User could not be saved. Please, try again.'));
            }
        }
        $groups = $this->User->Group->find('list');

        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array();
        if ($deleteCascade)
            $conditions = array('title !=' => 'Removing...');
        $projects = $this->User->Project->find('list', array('conditions' => $conditions));
        //$rounds   = $this->User->Round->find('list');
        $this->set(compact('groups', 'projects'
        ));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $group = $this->Session->read('group_id');
        $admin = TRUE;
        if ($group != 1) {
            $id = $this->Session->read('user_id');
            $admin = FALSE;
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if (isset($this->request->data['User']['group_id'])) {
                if ($group == 1) {
                    $numberOfAdmins = $this->User->find('count', array('recursive' => -1, 'conditions' => array('group_id' => 1)));
                    $user = $this->User->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));
                    if ($numberOfAdmins == 1 && $this->request->data['User']['group_id'] != 1 && $user['User']['group_id']==1) {
                        $this->Session->setFlash(__('You can not delete the only administrator who is in the application'));
                        $this->redirect(array('action' => 'index'));
                    }
                }
            } else {
                unset($this->request->data['User']['group_id']);
            }

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('User has been saved'), 'success');
                if ($admin)
                    $this->redirect(array('action' => 'index'));
                else
                    $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
            } else {
                $this->request->data['User']['image'] = null;
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $contain = array('Group');
            $this->request->data = $this->User->find('first', array('contain' => $contain, 'conditions' => array('User.id' => $id)));
        }
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
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
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $numberOfAdmins = $this->User->find('count', array('recursive' => -1, 'conditions' => array('group_id' => 1)));
        if ($numberOfAdmins == 1) {
            $isAdmin = $this->User->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));
            if ($isAdmin['User']['group_id'] == 1) {
                $this->Session->setFlash(__('You can not delete the only administrator who is in the application'));
                $this->redirect(array('action' => 'index'));
            }
        }

        $deleteCascade = Configure::read('deleteCascade');
        if ($deleteCascade) {
            if ($this->User->save(array('username' => 'Removing...'), false)) {
                $this->Session->setFlash(__('Selected user is being deleted. Please be patient'), 'information');
                $this->backGround(array('controller' => 'users', 'action' => 'index'));
                $this->User->delete($id, $deleteCascade);
            }
        } else {
            if ($this->User->delete($id, $deleteCascade)) {
                $this->Session->setFlash(__('User has been deleted'), 'success');
                $this->redirect(array('controller' => 'users', 'action' => 'index'));
            }
        }
        $this->Session->setFlash(__("User hasn't been deleted"));
        $this->redirect(array('controller' => 'users', 'action' => 'index'));
    }

    /**
     * deleteAll method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteAll() {
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $ids = json_decode($this->request->data['allUsers']);
            $conditions = array('User.id' => $ids);
            $numberOfAdmins = $this->User->find('count', array('recursive' => -1, 'conditions' => array('group_id' => 1)));
            if ($numberOfAdmins == 1)
                $conditions['group_id'] = 2;
            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $this->Session->setFlash(__('Selected users are being deleted. Please be patient'), 'information');
                if ($this->User->UpdateAll(array('User.username' => '\'Removing...\''), $conditions, -1)) {
                    $this->backGround(array('controller' => 'users', 'action' => 'index'));
                    $this->User->deleteAll($conditions, $deleteCascade);
                }
            } else {
                if ($this->User->deleteAll($conditions, $deleteCascade)) {
                    $this->Session->setFlash(__('Users selected have been deleted'), 'success');
                    $this->redirect(array('controller' => 'users', 'action' => 'index'));
                }
            }
            $this->Session->setFlash(__("Users selected haven't been deleted"));
            $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }
    }

}
