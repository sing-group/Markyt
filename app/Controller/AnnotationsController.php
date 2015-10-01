<?php

App::uses('AppController', 'Controller');

/**
 * Annotations Controller
 *
 * @property Annotation $Annotation
 */
class AnnotationsController extends AppController {

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    /*     * **************
      Este metdodo sera llamado desde el confrintation multiRound multiUSer para ver todas las anotaciones
     * ************* */
    public function index() {
        $data = $this->Session->read('confrontationSettingsData');
        $this->Annotation->recursive = 0;
        $this->Annotation->contain(false, array(
            'User' => array(
                'username',
                'id'
            ),
            'Round' => array(
                'title',
                'id'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        ));
        $cond = array(
            'round_id' => $data['round'],
            'user_id' => $data['user']
        );
        if (!empty($data['Project']['type']))
            $cond['type_id'] = $data['Project']['type'];
        $this->paginate = array(
            'fields' => array(
                'Annotation.id',
                'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
            ),
            'limit' => 100
        );
        $this->set('redirect', $data['redirect']);
        $this->set('annotations', $this->paginate($cond));
        $this->set('project_id', $data['Project']['id']);
        $this->set('count', 0);
        $this->render('listAnnotation');
    }

    /*     * **************
      Este metdodo sera llamado desde el view de project
     * ************* */

    /**
     * allAnottationsInRound method
     * @param boolean $id
     * @param boolean $projectId
     * @return void
     */
    public function allAnottationsInRound($id = null, $projectId = null) {
        $this->Annotation->contain(false, array(
            'User' => array(
                'username',
                'id'
            ),
            'Round' => array(
                'title',
                'id'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        ));
        $cond = array(
            'round_id' => $id
        );
        $redirect = $this->Session->write('redirect', array(
            'controller' => 'annotations',
            'action' => 'allAnottationsInRound',
            $id,
            $projectId
        ));
        $this->paginate = array(
            'fields' => array(
                'Annotation.id',
                'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
            ),
            'limit' => 100
        );
        $this->set('redirect', array(
            'controller' => 'projects',
            'action' => 'view',
            $projectId
        ));
        $this->set('annotations', $this->paginate($cond));
        $this->set('project_id', $projectId);
        //en este formato no necesitamos apguinaciones especiales
        $this->set('count', 0);
        $this->render('listAnnotation');
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
        $this->Annotation->id = $id;
        if (!$this->Annotation->exists()) {
            throw new NotFoundException(__('Invalid annotation'));
        }
        if ($this->Annotation->delete()) {
            $this->Session->setFlash(__('Annotation deleted'));
            $this->redirect(array(
                'action' => 'index'
            ));
        }
        $this->Session->setFlash(__('Annotation was not deleted'));
        $this->redirect(array(
            'action' => 'index'
        ));
    }


    /**
     * listAnnotation method
     *
     * @param string $fragment
     * @return void
     */
    
    function listAnnotationsHits() {
        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }
        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
        //evitar sql injection
            throw new Exception("Error Processing Request", 1);
        //para la opcion de naotaciones relacionadas, hits
        $type_A = $annotationData['Annotation']['typeCol'];
        $type_B = $annotationData['Annotation']['typeFil'];
        $subQueryConditions = array(
            'Annotation_B.round_id' => $round_id_B,
            'Annotation_B.user_id' => $user_id_B,
            'Annotation.document_id=Annotation_B.document_id ',
            'Annotation_B.type_id' => $type_B
        );
        if ($margin != 0) {
            array_push($subQueryConditions, array(
                "Annotation.end between Annotation_B.end - $margin AND Annotation_B.end + $margin",
                "Annotation.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
            ));
        } else {
            array_push($subQueryConditions, array(
                "Annotation.init= Annotation_B.init",
                "Annotation.end=Annotation_B.end"
            ));
        }
        $this->Annotation->contain(false, array(
            'User' => array(
                'username',
                'id'
            ),
            'Round' => array(
                'title',
                'id'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        ));
        $this->paginate = array(
            'fields' => array(
                'Annotation.id',
                'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
            ),
            'limit' => 50,
            'joins' => array(
                array(
                    'type' => 'inner',
                    'table' => 'annotations',
                    'alias' => 'Annotation_B',
                    'conditions' => $subQueryConditions
                )
            ),
            'conditions' => array(
                'Annotation.type_id' => $type_A,
                'Annotation.round_id' => $round_id_A,
                'Annotation.user_id' => $user_id_A
            )
        );
        $this->set('annotations', $this->paginate());
        $this->set('project_id', $data['Project']['id']);
        $this->render('listAnnotation');
    }

    public function listAnnotationsNone() {
        $this->Annotation->recursive = -1;


        //proviene de la pagina de confrontar rounds
        $data = $this->Session->read('confrontationPostedData');
        //proviene de este mismo controlador para ahorra llamar a la BD
        $annotationData = $this->Session->read('SettingsAnnotation');
        $round_id_A = $data['round_A'];
        $round_id_B = $data['round_B'];
        if (!is_array($data['user_A'])) {
            $user_id_A = $data['user_A'];
            $user_id_B = $data['user_B'];
        } else {
            $user_id_A = $data['user_A'][0];
            $user_id_B = $data['user_B'][0];
        }
        $margin = $data['Project']['margin'];
        if (!is_numeric($margin))
        //evitar sql injection
            throw new Exception("Error Processing Request", 1);

        $byRound = $annotationData['Annotation']['byRound'];
        $id_user_or_round = $annotationData['Annotation']['id_none'];
        $typeNone = $annotationData['Annotation']['typeNone'];

        if (($id_user_or_round == $user_id_A && !$byRound) || ($id_user_or_round == $round_id_A && $byRound)) {
            $id_roundSeach = $round_id_A;
            $id_roundNone = $round_id_B;
            $id_userSeach = $user_id_A;
            $id_userNone = $user_id_B;
        } else {
            $id_roundSeach = $round_id_B;
            $id_roundNone = $round_id_A;
            $id_userSeach = $user_id_B;
            $id_userNone = $user_id_A;
        }

        $subQueryConditions = array(
            'Annotation_B.round_id' => $id_roundNone,
            'Annotation_B.user_id' => $id_userNone,
            'Annotation_A.document_id=Annotation_B.document_id ',
            'Annotation_A.round_id' => $id_roundSeach,
            'Annotation_A.user_id' => $id_userSeach
        );
        if ($margin != 0) {
            array_push($subQueryConditions, array(
                "Annotation_A.init between Annotation_B.init - $margin AND  Annotation_B.init + $margin",
                "Annotation_A.end between Annotation_B.end - $margin AND Annotation_B.end + $margin"
            ));
        } else {
            array_push($subQueryConditions, array(
                "Annotation_A.init= Annotation_B.init",
                "Annotation_A.end=Annotation_B.end"
            ));
        }


        $db = $this->Annotation->getDataSource();
        $subQuery = $db->buildStatement(array(
            'fields' => array(
                'Distinct Annotation_A.id'
            ),
            'table' => 'annotations',
            'alias' => 'Annotation_A',
            'limit' => null,
            'offset' => null,
            'joins' => array(
                array(
                    'type' => 'INNER',
                    'table' => 'annotations',
                    'alias' => 'Annotation_B',
                    'conditions' => $subQueryConditions
                )
            ),
            'conditions' => array(''),
            'order' => null,
            'group' => null
                ), $this->Annotation);

        /* print_r($subQuery);
          throw new Exception("Error Processing Request", 1); */


        $this->Annotation->contain(false, array(
            'User' => array(
                'username',
                'id'
            ),
            'Round' => array(
                'title',
                'id'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        ));



        $this->paginate = array(
            'fields' => array(
                'Annotation.id',
                'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text'
            ),
            'limit' => 50,
            'conditions' => array(
                'Annotation.id NOT IN (' . $subQuery . ')',
                'Annotation.type_id' => $typeNone,
                'Annotation.round_id' => $id_roundSeach,
                'Annotation.user_id' => $id_userSeach
            )
        );
        $this->set('annotations', $this->paginate());
        $this->set('project_id', $data['Project']['id']);
        $this->render('listAnnotation');
    }

    public function randomize() {
//        set_time_limit(0);
//        $annotations = $this->Annotation->find('all', array('recursive' => -1, 'conditions' => array('round_id' => 1, 'user_id' => 1,'type_id'=>6)));
//        $tam = count($annotations);
//        for ($index = 0; $index < $tam; $index++) {
//
//            $annotation = $annotations[$index];
//            // $annotation['Annotation']['init']=$annotation['Annotation']['init']+10;
//            //$annotation['Annotation']['end']=$annotation['Annotation']['end']+10;
//            unset($annotation['Annotation']['id']);
//            $annotation['Annotation']['user_id'] = 2;
//            $this->Annotation->create();
//            //$this->Annotation->id = $annotation['Annotation']['id'];
//            $this->Annotation->save($annotation);
//            //$this->Annotation->delete();                    
//        }
        /* $annotations = $this->Annotation->find('all', array('recursive' => -1, 'conditions' => array('round_id' => 6, 'user_id' => 2)));
          $tam = count($annotations);
          for ($index = 0; $index < $tam; $index++) {

          $annotation = $annotations[$index];
          // $annotation['Annotation']['init']=$annotation['Annotation']['init']+10;
          //$annotation['Annotation']['end']=$annotation['Annotation']['end']+10;
          //$annotation['Annotation']['type_id'] = rand(1, 9);
          $this->Annotation->id = $annotation['Annotation']['id'];
          // $this->Annotation->save($annotation);
          $this->Annotation->delete();
          if ($index > $tam / 2) {
          break;
          }
          } */
    }

    public function generateConsensus($projectId = null, $roundId = null) {

        $this->Annotation->Round->Project->id = $projectId;
        $this->Annotation->Round->id = $roundId;
        if (!$this->Annotation->Round->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if (!$this->Annotation->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()

        $this->Annotation->recursive = 0;
        $users = $this->Annotation->User->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'type' => 'inner',
                    'table' => 'projects_users',
                    'alias' => 'ProjectsUser',
                    'conditions' => array(
                        'ProjectsUser.project_id' => $projectId,
                        'User.id = ProjectsUser.user_id'
                    )
                ),                
            ),
        ));

        $this->Annotation->virtualFields['users'] = "GROUP_CONCAT(DISTINCT User.id SEPARATOR ',')";
        $this->Annotation->contain(false, array(
            'User' => array(
                'username',
                'id'
            ),
            'Document' => array(
                'title',
                'id'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        ));
        $cond = array(
            'Annotation.round_id' => $roundId,
        );
        $this->paginate = array(
            'fields' => array(
                'Annotation.id',                
                'SUBSTR(Annotation.annotated_text, 1, 100) as annotated_text',
                'Annotation.users',
                'ConsensusAnnotation.id'
            ),
            'joins' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'consensusAnnotations',
                    'alias' => 'ConsensusAnnotation',
                    'conditions' => array(
                        'ConsensusAnnotation.init = Annotation.init',
                        'ConsensusAnnotation.end = Annotation.end'
                    )
                ),
            ),
            'limit' => 100,
            'group' => array('Annotation.init', 'Annotation.end'),
        );
        $this->set('annotations', $this->paginate($cond));
        $this->set('users', $users);
        $this->set('project_id', $projectId);
        $this->set('round_id', $roundId);
        $this->set('count', 0);
    }

}
