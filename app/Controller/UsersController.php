<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

    var $helpers = array(
        'Session');

//    public $components = array('Cookie');

    public function beforeFilter() {
        $this->Auth->autoRedirect = false;
        parent::beforeFilter();
        $this->Auth->allow('sendFeedback');
    }

    /**
     * login method
     *
     * @return void
     */
    public function login() {
        $user_id = $this->Session->read('user_id');
        $group_id = $this->Auth->user('group_id');

        if (!isset($this->data['User']['username'])) {
            $this->Auth->authenticate['Form'] = array(
                'fields' => array(
                    'username' => 'email'));
        }

        if (!isset($user_id)) {
//            if (empty($this->data)) {
//                $cookie = $this->Cookie->read('Auth.User');
//                if (!is_null($cookie)) {
//                    if ($this->Auth->login($cookie)) {
//                        $id_user = $this->Auth->user('id');
//                        $this->Session->write('user_id', $id_user);
//                        $username = $this->Auth->user('full_name');
//                        $this->Session->write('username', $username);
//                        $email = $this->Auth->user('email');
//                        $this->Session->write('email', $email);
//                        $group_id = $this->Auth->user('group_id');
//                        $this->Session->write('group_id', $group_id);
//                        $image = $this->Auth->user('image');
//                        if (isset($image)) {
//                            $extension = $this->Auth->user('image_extension');
//                            $image = 'data:' . $extension . ';base64,' . base64_encode($this->Auth->user('image'));
//                        }
//                        $this->Session->write('image', $image);
//
//
//                        if ($group_id == 1)
//                            $this->redirect(array('controller' => 'Projects', 'action' => 'index'));
//                        else
//                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
//                    } else {// Delete invalid Cookie
//                        $this->Cookie->destroy('Auth.User');
//                        $this->Session->setFlash(__('Your username or pasword is incorrect'));
//                        $this->redirect(array('controller' => 'Posts', 'action' => 'publicIndex'));
//                    }
//                }
//
//                $this->Session->setFlash(__('You are not authorized to access this location (user Login)'));
//            }
            if ($this->request->is('post')) {
                if ($this->Auth->login()) {
                    $user_id = $this->Auth->user('id');
                    $this->Session->write('user_id', $user_id);
                    $username = $this->Auth->user('full_name');
                    $this->Session->write('username', $username);
                    $email = $this->Auth->user('email');
                    $this->Session->write('email', $email);
                    $group_id = $this->Auth->user('group_id');
                    $this->Session->write('group_id', $group_id);
                    $image = $this->Auth->user('image');
                    if (isset($image)) {
                        $extension = $this->Auth->user('image_type');
                        $image = 'data:' . $extension . ';base64,' . base64_encode($this->Auth->user('image'));
                    }
                    $this->Session->write('image', $image);

//                    if (!empty($this->data) && $this->data['User']['remember_me']) {
//                        $cookie = array();
//                        $cookie['username'] = $username;
//                        $cookie['password'] = $this->data['User']['password'];
//                        $cookie['user_id'] = $id_user;
//                        $cookie['group_id'] = $group_id;
//                        $this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
//                        unset($this->data['User']['remember_me']);
//                    }
                    //ver si el usuario esta ya logueado
                    App::uses('CakeTime', 'Utility');
                    $date = $this->Auth->user('logged_until');
                    $isPast = CakeTime::isPast($date);
                    if (!$isPast) {
                        return $this->redirect(array(
                                    'action' => 'logout',
                                    true));
                    }
                    $connectionLog = Configure::read('connectionLog');
                    if ($connectionLog) {
                        $ip = $this->request->clientIp();
//                        $ip = "4.4.4.4";


                        $connectionLogProxy = Configure::read('connectionLogProxy');
                        if ($connectionLogProxy && isset($this->request->data['User']['connection-details'])) {
                            $details = $this->request->data['User']['connection-details'];
                            $copyDetails = json_decode($details, true);
                            $ip = $copyDetails['ip'];
                        } else {
                            $details = @file_get_contents("http://ipinfo.io/$ip/json");
                        }
                        if ($details && $ip != '127.0.0.1') {
                            $details = json_decode($details, true);
                        } else {
                            $details = array();
                            $details['city'] = "unknown";
                            $details['country'] = "unknown";
                        }



                        $region = "";
                        if (isset($details['region'])) {
                            $region = $details['region'];
                        }
                        $data = array(
                            'user_id' => $user_id,
                            'ip' => $ip,
                            'city' => $details['city'] . " ($region) ",
                            'country' => $details['country'],
                            'session_time' => '00:00:00',
                        );
                        $this->User->Connection->create();
                        $this->User->Connection->save($data);
                        $this->Session->write('connection_id', $this->User->Connection->id);
                    }

                    if ($group_id == 1)
                        $this->redirect(array(
                            'controller' => 'Projects',
                            'action' => 'index'));
                    else
                        $this->redirect(array(
                            'controller' => 'rounds',
                            'action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The username or the password is incorrect.'));
                }
            }
            if (isset($this->request->data)) {
                $this->Session->write('loginIntent', $this->request->data);

                return $this->redirect(array(
                            'controller' => 'Posts',
                            'action' => 'publicIndex'));
            }
            $this->redirect(array(
                'controller' => 'Posts',
                'action' => 'publicIndex'));
        } else {
            $this->autoRender = false;
            $this->redirect(array(
                'action' => 'logout'));
        }
    }

    public function renewSession($id) {
        if ($this->request->is('ajax')) {
            $group_id = $this->Session->read('group_id');
            $user_id = $this->Session->read('user_id');
            if ($group_id > 1) {
                $id = $user_id;
            }

            $this->User->id = $id;
            if (!$this->User->exists()) {
                return $this->correctResponseJson(json_encode(array(
                            'success' => false)));
            }

            App::uses('CakeTime', 'Utility');
            $renewSessionMinutes = Configure::read('renewSessionMinutes');
            $renewSessionMinutes = ($renewSessionMinutes * 60);
            $date = CakeTime::format("+$renewSessionMinutes seconds", '%Y-%m-%d %H:%M:%S');

            $connectionLog = Configure::read('connectionLog');
            if ($connectionLog) {
                $this->User->Connection->id = $this->Session->read('connection_id');

                if ($this->User->Connection->id == 0) {
                    return $this->correctResponseJson(json_encode(array(
                                'success' => false)));
                }
                $start = $this->User->Connection->field('created');
                $now = new DateTime(); // current date/time
                $ref = new DateTime($start);
                $diff = $ref->diff($now);
//                debug($now);
//                debug($ref);
//                debug($diff);
//                debug($diff->format("%H:%I:%s"));
//                throw new Exception;
                $this->User->Connection->saveField('session_time', $diff->format("%H:%I:%s"));
            }


            if ($this->User->saveField('logged_until', $date)) {
                return $this->correctResponseJson(json_encode(array(
                            'success' => true)));
            } else {
                return $this->correctResponseJson(json_encode(array(
                            'success' => false)));
            }
        }
    }

    /**
     * login method
     *
     * @return void
     */
    public function logout($isLogin = false) {
        $user_id = $this->Session->read('user_id');
        if (isset($user_id) && !$isLogin) {
            $this->User->id = $user_id;
            App::uses('CakeTime', 'Utility');
            $date = CakeTime::format("-3 seconds", '%Y-%m-%d %H:%M:%S');
            $this->User->saveField('logged_until', $date);
        }
        $this->Session->destroy();
//        $this->Cookie->destroy();
        if ($isLogin) {
            $this->Session->setFlash('This user is already logged into the system. Please try again in a minute or contact with administrator');
        }
//        $this->redirect(array('controller' => 'Posts', 'action' => 'publicIndex'));
        $this->redirect('/');
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
                $userFind = $this->User->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'id',
                        'username',
                        'email'),
                    'conditions' => array(
                        'User.email' => $this->request->data['User']['email'])));
                
                
                if (isset($userFind)) {
                    $newPassword = $this->randomAlphaNum();
                    $emailProfile = Configure::read('emailProfile');
                    $Email = new CakeEmail($emailProfile);
                    $Email->from(array(
                        'Marky@webAannotation.com' => 'Marky'
                    ));
                    $Email->to($userFind['User']['email']);
                    $Email->subject('Marky recover account');
                    $Email->emailFormat('html');
                    $Email->template('recoverMarkyBootstrap');
                    $Email->viewVars(array(
                        'user' => $userFind['User']['username'],
                        'password' => $newPassword));
                    $send = $Email->send();
                    if ($send) {
                        $userFind['User']['password'] = $newPassword;
                        $this->User->id = $userFind['User']['id'];
                        if ($this->User->save($userFind)) {
                            $this->Session->setFlash(__('Your password has been changed. Please check your email'), 'success');
                            $this->redirect(array(
                                'controller' => 'posts',
                                'action' => 'publicIndex'));
                        }
                    } else {
                        CakeLog::write('MailLog', 'Error send  password to: ' . $sendByMail['email']);
                    }
                }
                $this->Session->setFlash(__('This email does not exist or failed to send the new password'));
                $this->redirect(array(
                    'controller' => 'posts',
                    'action' => 'publicIndex'));
            } else if ($this->request->is('ajax')) {
                $this->layout = false;
            }
        } else {
            $this->autoRender = false;
            $this->redirect(array(
                'action' => 'logout'));
        }
    }

    public function sendFeedback() {
        if ($this->request->is('post') || $this->request->is('put')) {

            $data = $this->request->data['User'];
            $emailProfile = Configure::read('emailProfile');
            $Email = new CakeEmail($emailProfile);
            $Email->from(array(
                $data['email'] => $data['email']
            ));
            $Email->to("mpperez3@esei.uvigo.es");
            $Email->cc(array(
                'analia@uvigo.es',
                'gprodriguez2@esei.uvigo.es'));
            $Email->subject('[MARKYT] ' . $data['subject']);

            $data["body"] = $data["body"] . "<p>==============================</p><h2>Please don't reply</h2>";

            App::import('Vendor', 'HTMLPurifier', array(
                'file' => 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php'));
            $config = HTMLPurifier_Config::createDefault();
            $dirty_html = $data["body"];
            $purifier = new HTMLPurifier($config);
            $data["body"] = $purifier->purify($dirty_html);

            $Email->replyTo($data['email']);
//            $this->Email->from = 'Cool Web App <app@ejemplo.com>';
            $Email->emailFormat('html');
//            $Email->template('recoverMarky');
//            $Email->viewVars(array('user' => $userFind['User']['username'],
//                'password' => $newPassword));

            $send = $Email->send($data["body"]);
            if ($send) {
                return $this->correctResponseJson(json_encode(array(
                            'success' => true)));
            } else {
                CakeLog::write('MailLog', 'Error send  password to: ' . $data['email']);
                return $this->correctResponseJson(json_encode(array(
                            'success' => false)));
            }
        }
    }

    /**
     * register method
     *
     * @return void
     */
    public function register() {

//        $oneUser = $this->User->find('count');
//        //si existe almenos un usuario creado lo mandamos a login
//        if ($oneUser == 0) {
//            if ($this->request->is('post') || $this->request->is('put')) {
//                $this->User->create();
//                $this->request->data['User']['group_id'] = 1;
//                if ($this->User->save($this->request->data)) {
//                    $this->Session->setFlash(__('You can now enter the application with your username and password'), 'success');
//                    $this->redirect(array('controller' => 'posts', 'action' => 'publicIndex'));
//                } else {
//                    $this->Session->setFlash(__('Error already exists a user with that name'));
//                }
//            }
//        } else {
//            $this->Session->setFlash(__('This option only works if there is no registered user. If you do not already have a user, contact your administrator'));
//            $this->redirect(array('action' => 'login'));
//        }
    }

    /**
     * index method
     * @param boolean $id
     * @return void
     */
    public function index($post = null) {

        $this->User->recursive = 0;
        $this->User->contain('Project.title', 'Project.id', 'Group');

        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } else if (!empty($data)) {
            $conditions = array(
                'conditions' => array(
                    'OR' => $data));
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
            $this->redirect(array(
                'action' => 'index',
                1));
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

        $group_id = $this->Session->read('group_id');
        $user_id = $this->Session->read('user_id');
        if ($group_id > 1) {
            $id = $user_id;
        }

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $contain = array(
            'Project' => array(
                'id',
                'title',
                'created',
                'modified'),
            'Group' => array(
                'name'));
        $this->set('user', $this->User->find('first', array(
                    'contain' => $contain,
                    'conditions' => array(
                        'User.id' => $id))));
        $this->set('user_id', $id);
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
                        $documentsId = $this->User->Project->DocumentsProject->find('first', array(
                            'recursive' => -1,
                            'fields' => 'DocumentsProject.document_id',
                            'order' => 'DocumentsProject.Document_id ASC',
                            'conditions' => array(
                                'DocumentsProject.project_id' => $projectId)));
                        if (!empty($documentsId)) {
                            $documentsId = $documentsId['DocumentsProject']['document_id'];
                            $roundsIds = $this->User->Project->Round->find('list', array(
                                'recursive' => -1,
                                'fields' => 'Round.id',
                                'conditions' => array(
                                    'Round.project_id' => $projectId)));
                            foreach ($roundsIds as $RId) { {
                                    if (!$this->User->UsersRound->hasAny(array('user_id' => $user_id,
                                                'round_id' => $RId))) {
                                        $this->User->UsersRound->create();
                                        $this->User->UsersRound->save(array(
                                            'user_id' => $user_id,
                                            'round_id' => $RId,
                                            'state' => 0));
                                    }
                                }
                            }
                        }
                    }

//                    if (!empty($allData)) {
//                        if (!$this->User->UsersRound->saveMany($allData))
//                            throw new NotFoundException(__('Unknown exception'));
//                    }
                }
                $this->Session->setFlash(__('User has been saved'), 'success');
                $this->redirect(array(
                    'action' => 'index'));
            } else {
                $this->Session->setFlash(__('User could not be saved. Please, try again.'));
            }
        }
        $groups = $this->User->Group->find('list');

        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array();
        if ($deleteCascade)
            $conditions = array(
                'title !=' => 'Removing...');
        $projects = $this->User->Project->find('list', array(
            'conditions' => $conditions));
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
        $user_id = $this->Session->read('user_id');
        if ($group != 1) {
            $id = $user_id;
            $admin = FALSE;
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if (isset($this->request->data['User']['group_id'])) {
                if ($group == 1) {
                    $numberOfAdmins = $this->User->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'group_id' => 1)));
                    $user = $this->User->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $id)));
                    if ($numberOfAdmins == 1 && $this->request->data['User']['group_id'] != 1 && $user['User']['group_id'] == 1) {
                        $this->Session->setFlash(__('You can not delete the only administrator who is in the application'));
                        $this->redirect(array(
                            'action' => 'index'));
                    }
                }
            } else {
                unset($this->request->data['User']['group_id']);
            }

            if ($this->User->save($this->request->data)) {
                if ($id == $user_id) {
                    $user = $this->User->find('first', array(
                        'fields' => array(
                            'image_type',
                            'image'),
                        'conditions' => array(
                            'User.id' => $id)));

                    $image = null;
                    if (isset($user['User']['image'])) {
                        $extension = $user['User']['image_type'];
                        $image = 'data:' . $extension . ';base64,' . base64_encode($user['User']['image']);
                    }
                    $this->Session->write('image', $image);
                }


                $this->Session->setFlash(__('User has been saved'), 'success');
                if ($admin)
                    $this->redirect(array(
                        'action' => 'index'));
                else
                    $this->redirect(array(
                        'controller' => 'rounds',
                        'action' => 'index'));
            } else {
                $this->request->data['User']['image'] = null;
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $contain = array(
                'Group');
            $this->request->data = $this->User->find('first', array(
                'contain' => $contain,
                'conditions' => array(
                    'User.id' => $id)));
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
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id);


//        if (!$this->request->is('post')) {
//            throw new MethodNotAllowedException();
//        }
//        $this->User->id = $id;
//        if (!$this->User->exists()) {
//            throw new NotFoundException(__('Invalid user'));
//        }
//        $numberOfAdmins = $this->User->find('count', array('recursive' => -1, 'conditions' => array('group_id' => 1)));
//        if ($numberOfAdmins == 1) {
//            $isAdmin = $this->User->find('first', array('recursive' => -1, 'conditions' => array('id' => $id)));
//            if ($isAdmin['User']['group_id'] == 1) {
//                $this->Session->setFlash(__('You can not delete the only administrator who is in the application'));
//                $this->redirect(array('action' => 'index'));
//            }
//        }
//
//        $deleteCascade = Configure::read('deleteCascade');
//        if ($deleteCascade) {
//            if ($this->User->save(array('username' => 'Removing...'), false)) {
//                $this->Session->setFlash(__('Selected user is being deleted. Please be patient'), 'information');
//                $this->backGround(array('controller' => 'users', 'action' => 'index'));
//                $this->User->delete($id, $deleteCascade);
//            }
//        } else {
//            if ($this->User->delete($id, $deleteCascade)) {
//                $this->Session->setFlash(__('User has been deleted'), 'success');
//                $this->redirect(array('controller' => 'users', 'action' => 'index'));
//            }
//        }
//        $this->Session->setFlash(__("User hasn't been deleted"));
//        $this->redirect(array('controller' => 'users', 'action' => 'index'));
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
            $conditions = array(
                'User.id' => $ids);
            $numberOfAdmins = $this->User->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'group_id' => 1)));
            if ($numberOfAdmins == 1)
                $conditions['group_id'] = 2;
            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $this->Session->setFlash(__('Selected users are being deleted. Please be patient'), 'information');
                if ($this->User->UpdateAll(array(
                            'User.username' => '\'Removing...\''), $conditions, -1)) {
                    $this->backGround(array(
                        'controller' => 'users',
                        'action' => 'index'));
                    $this->User->deleteAll($conditions, $deleteCascade);
                }
            } else {
                if ($this->User->deleteAll($conditions, $deleteCascade)) {
                    $this->Session->setFlash(__('Users selected have been deleted'), 'success');
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'index'));
                }
            }
            $this->Session->setFlash(__("Users selected haven't been deleted"));
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'index'));
        }
    }

}
