<?php

App::uses('Component', 'Controller');

class CommonFunctionsComponent extends Component {

    public $components = array('Session');
    private $Model;
    private $Controller;
    private $request;
    private $modelAlias;

    public function __construct(ComponentCollection $collection, $settings = array()) {
        $settings = array_merge($this->settings, (array) $settings);
        $this->Controller = $collection->getController();
        $this->Model = $this->Controller->{$this->Controller->modelClass};
        $this->modelAlias = $this->Model->alias;
        parent::__construct($collection, $settings);
    }

//    public function initialize(Controller $controller) {
//        $this->Controller = $controller;
//        $this->Model = $this->Controller->{$this->Controller->modelClass};
//        $this->modelAlias = $this->Model->alias;
//        parent::initialize($controller);
//    }
//
//    function startup(Controller $controller) {
//        $this->Controller = $controller;
//        $this->Model = $this->Controller->{$this->Controller->modelClass};
//        $this->modelAlias = $this->Model->alias;
//        parent::initialize($controller);
//    }
//    public function initialize(&$controller, $settings = array()) {
//        $this->controller = $controller;
//    }



    public function delete($id = null,$column = '', $messageError = "Unknown error") {
        $redirect = $this->Session->read('redirect');
        $this->request = $this->Controller->request;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Model->id = $id;
        if (!$this->Model->exists()) {
            throw new NotFoundException(__('Invalid ') . $this->modelAlias);
        }
        $deleteCascade = Configure::read('deleteCascade');
        $redirect = $this->Session->read('redirect');

        if ($deleteCascade) {
            if ($this->Model->save(array($column => 'Removing...'), false)) {
                if (!$this->request->is('ajax')) {
                    $this->Session->setFlash(__('Selected ') . $this->modelAlias . __('is being deleted. Please be patient'), 'information');
                    $this->Controller->backGround($redirect);
                    $this->Model->delete($id, $deleteCascade);
                }
            }
        } else {
            if ($this->Model->delete($id, $deleteCascade)) {
                if (!$this->request->is('ajax')) {
                    $this->Session->setFlash($this->modelAlias . __(' selected has been deleted'), 'success');
//                    if ($redirect['controller'] == 'documents') {
//                        $redirect = array('controller' => 'documents', 'action' => 'index');
//                    }
                    $this->Controller->redirect($redirect);
                } else {
                    return $this->Controller->correctResponseJson(array('success' => true));
                }
            }
        }
        if (!$this->request->is('ajax')) {
            $this->Session->setFlash($this->modelAlias . __(" hasn't been deleted"));
            $this->Controller->redirect($redirect);
        } else {

            return $this->Controller->correctResponseJson(array('success' => false, 'error' => $messageError));
        }
        return;
    }

    public function deleteSelected($column = '', $messageError = "Unknown error") {
        $redirect = $this->Session->read('redirect');
        $this->request = $this->Controller->request;

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $ids = json_decode($this->request->data['selected-items']);
            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $conditions = array($this->modelAlias . '.id' => $ids);
                if ($this->Model->UpdateAll(array($title => '\'Removing...\''), $conditions, -1)) {
                    if (!$this->request->is('ajax')) {
                        $this->Session->setFlash(__('Selected ') . $this->modelAlias . __('s are being deleted. Please be patient'), 'information');
                        $this->Controller->backGround($redirect);
                        $this->Model->deleteAll($conditions, $deleteCascade);
                    } else {
                        return $this->Controller->correctResponseJson(array('success' => true));
                    }
                }
            } else {

                if ($this->Model->deleteAll(array($this->modelAlias . '.id' => $ids), $deleteCascade)) {
                    if (!$this->request->is('ajax')) {
                        $this->Session->setFlash($this->modelAlias . __('s selected have been deleted'), 'success');
                        $this->Controller->redirect($redirect);
                    } else {
                        return $this->Controller->correctResponseJson(array('success' => true));
                    }
                }
                if (!$this->request->is('ajax')) {
                    $this->Session->setFlash(__($this->modelAlias . "s selected haven't been deleted"));
                    $this->Controller->redirect($redirect);
                } else {
                    return $this->Controller->correctResponseJson(array('success' => false, 'error' => $messageError));
                }
            }
        }
        return;
    }

}
