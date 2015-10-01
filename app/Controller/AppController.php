<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $helpers = array('Session');
    public $components = array('Session', 'Auth' => array('loginRedirect' => array('controller' => 'posts', 'action' => 'publicIndex'),
            //'loginAction' => array('controller' => 'posts', 'action' => 'publicIndex'),
            'authError' => 'You are not authorized to access this location'));

    //aplana un array recursivamente
    public function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    /**
     * randomAlphaNum method
     * @param int $length
     * @return String
     */
    function randomAlphaNum($length = null) {
        if ($length == null)
            $length = rand(8, 15);
        $a_z = "!$&/()=?:;-*+";
        $int = rand(0, 12);
        $unique_key = substr(md5(rand(0, 1000000)), 0, $length) . $a_z[$int] . substr(md5(rand(0, 1000000)), 0, rand(0, 3));
        return $unique_key;
    }

    /**
     * backGround method
     * @param Array $location
     * @return void
     */
    public function backGround($location = null) {
        $redirect = $this->Session->read('redirect');
        $scriptTimeLimit = Configure::read('scriptTimeLimit');
        set_time_limit($scriptTimeLimit);

        //cortamos la ejecucion parab el usuario pero el script sigue en ejecucion
        //de esta forma el usuario puedeseguir navegando
        //sino estamos en la vista de un proyecto nos dirigimos a donde nos manden
        //en caso contrario volvemos a la vista
        //en caso de estar en la vista de un proyecto y querer eliminarlo nos vamos a donde nos manden
        if (isset($redirect) && is_array($redirect)) {
            if ($redirect['action'] == 'view' && $redirect['controller'] != 'projects') {
                $redirect['action'] = 'index';
                unset($redirect[0]);
            }
            header("Location: " . Router::url(($redirect)), true);
        } else if (isset($location)) {
            header("Location: " . $location);
        }
        //Erase the output buffer
        ob_end_clean();
        //Tell the browser that the connection's closed
        header("Connection: close");
        //Ignore the user's abort (which we caused with the redirect).
        ignore_user_abort(true);
        //Start output buffering again
        ob_start();
        //Tell the browser we're serious... there's really
        //nothing else to receive from this page.
        header("Content-Length: 0");
        //Send the output buffer and turn output buffering off.
        ob_end_flush();
        //Yes... flush again.
        flush();
        //Close the session.
        session_write_close();
    }
    
    
      public function correctResponseJson($response) {
        $this->autoRender = false;
        if (isset($response)) {
            if (is_array($response)) {
                $response = json_encode($response);
            }

            $this->response->body($response);
        } else {
            $this->response->body('');
        }
        $this->response->type('json');
        return $this->response;
    }

    public function beforeFilter() {
        /* $this->Auth->allow(array('forward', 'processUrl')); */
        $this->Auth->allow(array('controller' => 'pages', 'action' => 'display', 'markyInformation'));
        $this->Auth->allow('postsSearch', 'publicIndex', 'recoverAccount');
        $this->Auth->allow('login', 'register', 'Logout');
        //cambiar aqui a que paginas se puede  ir sin registrarse
        $group = $this->Session->read('group_id');
        $controller = $this->request->params['controller'];
        //en minuculas dado que en mayusculas daba algunos errores
        $action = $this->request->params['action'];

        //dado a la dificultad de cakephp y sus permisos para acceder a las paginas
        //se ha optado por hacer nuestra propia forma de permiso a la hora de acceder a las paginas
        $controller = strtolower($controller);
        //en minuculas dado que en mayusculas daba algunos errores
        if (isset($group) && $group != 1) {
            switch ($controller) {
                case 'pages' :
                    break;
//                case 'annotations' :
//                    break;
                case 'annotationsquestions' :
                    break;
                case 'annotations_questions' :
                    break;
                case 'rounds' :
                    switch ($action) {
                        case 'user_view' :
                        case 'userView' :
                        case 'backgroundCopyRound' :
                            break;
                        default :
                            $this->Session->setFlash(__('You not authorized to enter this area, your action has been reported'));
                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                            break;
                    }
                    break;
                case 'users' :
                    switch ($action) {
                        case 'edit' :
                            break;
                        case 'login' :
                            break;
                        case 'logout' :
                            break;
                        default :
                            $this->Session->setFlash(__('You not authorized to enter this area, your action has been reported'));
                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                            break;
                    }
                    break;
                case 'users_rounds' :
                case 'usersrounds' :
                    switch ($action) {
                        case 'start' :
                            break;
                        case 'save' :
                            break;
                        case 'index' :
                            break;
                        default :
                            $this->Session->setFlash(__('You not authorized to enter this area, your action has been reported'));
                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                            break;
                    }
                    break;
                 case 'documentsassessments' :
                      switch ($action) {
                        case 'view' :
                            break;
                        case 'save' :
                            break;                       
                    }
                    break;
                case 'projects' :
                    switch ($action) {
                        case 'userIndex' :
                            break;
                        case 'userView' :
                            break;
                        case 'statisticsForUser' :
                            break;
                        default :
                            $this->Session->setFlash(__('You not authorized to enter this area, your action has been reported'));
                            $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));
                            break;
                    }
                    break;
                default :
                    //print_r($controller.' '.$action);

                    $this->Session->setFlash(__('You not authorized to enter this area, your action has been reported(Control not permited) '.$controller));
                    $this->redirect(array('controller' => 'usersRounds', 'action' => 'index'));

                    break;
            }
        } elseif (isset($group) && $group == 1) {
            $action = $this->request->params['action'];
            if ($action == 'view') {
                $redirect = array('controller' => $controller, 'action' => $action);
                if (!empty($this->request->params['pass'][0]))
                //print_r($this->request->params);
                    $redirect = array('controller' => $controller, 'action' => $action, $this->request->params['pass'][0]);
                $this->Session->write('redirect', $redirect);
            }
            if ($action == 'index') {
                //print_r($this->request->params);
                $redirect = array('action' => 'index');
                $this->Session->write('redirect', $redirect);
                $this->Session->write('comesFrom', $redirect);
            }
        }
    }

}
