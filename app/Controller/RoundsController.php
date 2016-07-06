<?php

App::uses('AppController', 'Controller');

/**
 * Rounds Controller
 *
 * @property Round $Round
 */
class RoundsController extends AppController {

    /**
     * RGBToHex method
     * @param array $colour
     * @return string
     */
    function RGBToHex($colour) {
        $colour = explode(',', $colour);
        //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
        $hex = "#";
        $hex .= str_pad(dechex($colour[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($colour[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($colour[2]), 2, "0", STR_PAD_LEFT);
        return $hex;
    }

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {

        $this->UsersRound = $this->Round->UsersRound;
        $this->User = $this->Round->UsersRound->User;
        $this->Job = $this->User->Job;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;


        $user_id = $this->Session->read('user_id');
        $this->Round->recursive = -1;

//        $this->Round->contain(false, array(
//            'Project','UsersRound'));
//        $rounds = $this->Round->UsersRound->find('list', array(
//            'recursive' => -1,
//            'fields' => 'UsersRound.round_id',
//            'conditions' => array(
//            )
//        ));





        App::uses('CakeTime', 'Utility');
        $date = CakeTime::format('+0 days', '%Y-%m-%d');
        $user_id = $this->Session->read('user_id');

        $db = $this->Round->getDataSource();
        $subQuery = $db->buildStatement(array(
            'fields' => array(
                'AnnotatedDocument.id',
            ),
            'table' => 'rounds',
            'alias' => 'Round',
            'joins' => array(
                array(
                    'alias' => 'AnnotatedDocument',
                    'table' => 'annotated_documents',
                    'type' => 'INNER',
                    'conditions' => array(
                        '`Round`.`id` = `AnnotatedDocument`.`round_id`',
                        'user_id' => $user_id)
                )
            ),
            'conditions' => array(
                'ends_in_date <' => $date,
                'text_marked IS NULL'),
                ), $this->Round);

        $this->AnnotatedDocument->deleteAll(array(
            "id IN( $subQuery)"));

//        $this->Round->find('list', array(
//            'fields' => array(
//                'AnnotatedDocument.id',
//            ),
//            'joins' => array(
//                array(
//                    'alias' => 'AnnotatedDocument',
//                    'table' => 'annotated_documents',
//                    'type' => 'INNER',
//                    'conditions' => array(
//                        '`Round`.`id` = `AnnotatedDocument`.`round_id`',
//                        'user_id' => $user_id)
//                )
//            ),
//            'conditions' => array(
//                'ends_in_date <' => $date,
//                'text_marked IS NULL'),
//        ));
//        throw new Exception;

        $conditions = array(
            'AND' => array(
//                'Round.id' => $rounds,
                'Round.ends_in_date IS NOT NULL',
                'Round.is_visible' => 1,
                'UsersRound.user_id' => $user_id,
            )
        );


        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } //$post == null
        else {
            $conditions['OR'] = $data;
            $this->set('search', $busqueda);
        }

        $this->paginate = array(
            'fields' => array(
                'Round.id',
                'Round.title',
                'Round.description',
                'Round.ends_in_date',
                'Round.project_id',
                'Project.title',
                'UsersRound.id',
                'UsersRound.user_id',
                'UsersRound.state',
            ),
            'joins' => array(
                array(
                    'alias' => 'UsersRound',
                    'table' => 'users_rounds',
                    'type' => 'INNER',
                    'conditions' => '`Round`.`id` = `UsersRound`.`round_id`'
                ),
//                 array(
//                    'alias' => 'Job',
//                    'table' => 'jobs',
//                    'type' => 'LEFT ',
//                    'conditions' => array('`Round`.`id` = `Job`.`round_id`' , 'Job.percentage IS NOT NULL', 'Job.percentage < 100' )
//                ),
            ),
            'conditions' => $conditions,
            'order' => array('ends_in_date' => 'DESC'),
            'contain' => array(
                'Project',
            )
        );

        $rounds = $this->paginate();
        $roundsIds = Set::classicExtract($rounds, '{n}.Round.id');

        $jobs = $this->Job->find('all', array(
            'recursive' => -1,
            'fields' => array('Job.round_id', 'Job.id', 'Job.percentage'),
            'conditions' => array(
                'round_id' => $roundsIds,
                'user_id' => $user_id,
                'percentage <>' => 100,
                'status NOT LIKE'=>'Starting...'
                ),
            'order' => array('modified' => 'DESC'),
//            'group'=>array('round_id','user_id'),
        ));
        
        $jobs = Set::combine($jobs, '{n}.Job.round_id', '{n}.Job');
        $name = strtolower($this->name);
        $this->set("user_id", $this->Session->read('user_id'));
        $this->set('jobs', $jobs);
        $this->set($name, $rounds);
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
            $cond['Round.title  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Round.ends_in_date  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Round.description  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array(
                'action' => 'index',
                1
            ));
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
        $this->UsersRound = $this->Round->UsersRound;
        $this->Annotation = $this->Round->Annotation;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->GoldenProject = $this->Round->Project->Participant->GoldenProject;
        $this->User = $this->Round->User;
        $this->Job = $this->User->Job;



        $this->Round->id = $id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->Round->exists()
        $contain = array(
            'Project' => array(
                'id',
                'title'
            ),
            'Type' => array(
                'id',
                'name',
                'colour'
            )
        );
        $round = $this->Round->find('first', array(
            'contain' => $contain,
            'conditions' => array(
                'Round.id' => $id
            )
        ));
        $this->set('round', $round);
        $users = $this->UsersRound->find('list', array(
            'recursive' => -1,
            'fields' => 'UsersRound.user_id',
            'conditions' => array(
                'UsersRound.round_id' => $id
            )
        ));
        
        $this->AnnotatedDocument->virtualFields['annotation_time'] = 'SUM(AnnotatedDocument.annotation_minutes)';
        $this->AnnotatedDocument->virtualFields['avg_annotation_time'] = 'AVG(AnnotatedDocument.annotation_minutes)';

        $annotationTime = $this->AnnotatedDocument->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'AnnotatedDocument.user_id',
                'annotation_time',
                'avg_annotation_time'),
            'group' => 'AnnotatedDocument.user_id',
            'conditions' => array(
                'AnnotatedDocument.round_id' => $id,
                'AnnotatedDocument.user_id' => $users,
                'AnnotatedDocument.annotation_minutes > 0'
            )
        ));



        $annotationTimePerDocument = $this->AnnotatedDocument->find('all', array(
            'recursive' => -1,
            'contain' => array(
                'Document'),
            'fields' => array(
                'Document.id',
                'Document.title',
                'Document.external_id',
                'avg_annotation_time'),
            'group' => 'AnnotatedDocument.document_id',
            'conditions' => array(
                'AnnotatedDocument.round_id' => $id,
                'AnnotatedDocument.user_id' => $users,
                'AnnotatedDocument.annotation_minutes > 0'
            ),
            'order' => 'avg_annotation_time DESC',
            'limit' => 5,
        ));


        $annotationSumTime = Set::combine($annotationTime, '{n}.AnnotatedDocument.user_id', '{n}.AnnotatedDocument.annotation_time');
        $annotationAvgTime = Set::combine($annotationTime, '{n}.AnnotatedDocument.user_id', '{n}.AnnotatedDocument.avg_annotation_time');



        $goldUserId = $this->GoldenProject->find('first', array(
            'recursive' => -1,
            'fields' => array(
                'user_id'),
            'conditions' => array(
                'round_id' => $id,
                'project_id' => $round['Round']['project_id'],
            ),
        ));

        if (!empty($goldUserId)) {
            $goldUserId = $goldUserId['GoldenProject']['user_id'];
        } else {
            $goldUserId = -1;
        }

        /* ============Jobs============ */
        $userRoundsMap = $this->UsersRound->find('list', array(
            'recursive' => -1, //int
            'fields' => array('user_id', 'state'),
            'conditions' => array('round_id' => $this->Round->id), //array of conditions
        ));



//        $users = $this->flatten($users);
        $this->set('users', $this->User->find('all', array(
                    'recursive' => -1,
                    'fields' => array(
                        'User.id',
                        'User.username',
                        'User.surname',
                        'User.email',
                        'User.image',
                        'User.image_type'
                    ),
                    'conditions' => array(
                        'User.id' => $users
                    )
        )));


        $annotations = $this->Annotation->find('count', array(
            'recursive' => -1,
            'conditions' => array("round_id" => $this->Round->id),
        ));

        $this->set('enableJavaActions', Configure::read('enableJavaActions'));
        $this->set('goldUserId', $goldUserId);
        $this->set('annotations', $annotations);
        $this->set('userRoundsMap', $userRoundsMap);
        $this->set('annotationSumTime', $annotationSumTime);
        $this->set('annotationAvgTime', $annotationAvgTime);
        $this->set('annotationTimePerDocument', $annotationTimePerDocument);
        $this->set('round_id', $this->Round->id);
    }

    /**
     * userView method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function userView($id = null) {
        $this->Round->id = $id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->Round->exists()
        $contain = array(
            'Project' => array(
                'id',
                'title'
            ),
            'Type' => array(
                'id',
                'name',
                'colour',
                'description'
            )
        );
        $this->set('round', $this->Round->find('first', array(
                    'contain' => $contain,
                    'conditions' => array(
                        'Round.id' => $id
                    )
        )));
    }

    /**
     * monitorizing method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function monitorizing($id = null) {
        $this->Round->id = $id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->Round->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $round = $this->Round->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id
                )
            ));

            if (preg_match('/.*\-\[([0-9]*)\%\]/', $round['Round']['title'], $progres)) {
                return $this->correctResponseJson(array(
                            'end' => false,
                            'progres' => $progres[1]));
            } else {
                return $this->correctResponseJson(array(
                            'end' => true));
            }
        } else {
            $contain = array(
                'Project' => array(
                    'id',
                    'title'
                )
            );
            $this->set('round', $this->Round->find('first', array(
                        'contain' => $contain,
                        'conditions' => array(
                            'Round.id' => $id
                        )
            )));
        }
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($projectId = null) {
        $cond = array(
            "DocumentsProject.project_id" => $projectId
        );
        $document = $this->Round->Project->DocumentsProject->find('first', array(
            'recursive' => -1,
            'order' => 'DocumentsProject.document_id ASC',
            'conditions' => $cond
        ));

        $max = $this->Round->Project->DocumentsProject->find('count', array(
            'recursive' => -1,
            'order' => 'DocumentsProject.document_id ASC',
            'conditions' => array(
                "project_id" => $projectId)
        ));

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Round->create();
            $users = $this->request->data['Round']['User'];
            $this->request->data['Round']['User'] = null;

            if (isset($this->request->data['Round']['interval'])) {
                $interval = explode(",", $this->request->data['Round']['interval']);
                if ($interval[0] != $interval[1]) {
                    $this->request->data['Round']['start_document'] = $interval[0];
                    $this->request->data['Round']['end_document'] = $interval[1];
                } else {
                    $this->request->data['Round']['start_document'] = 1;
                    $this->request->data['Round']['end_document'] = $max;
                }
            }

            if ($this->Round->save($this->request->data)) {
                $allData = array();
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $data = array(
                            'round_id' => $this->Round->id,
                            'user_id' => $user,
//                            'state' => $this->request->data['Round']['state']
                        );
                        array_push($allData, $data);
                    }
                    $this->Round->UsersRound->saveMany($allData);
                }
                $this->Session->setFlash(__('Round has been saved'), 'success');


                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $this->request->data['Round']['project_id']
                ));
            } //$this->Round->save($this->request->data)
            else {
                $this->Session->setFlash(__('Round could not be saved. Please, try again.'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        $types = $this->Round->Type->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'Type.project_id' => $projectId
            )
        ));
        $users = $this->Round->Project->ProjectsUser->find('list', array(
            'recursive' => -1,
            'fields' => array(
                'user_id'),
            'conditions' => array(
                'project_id' => $projectId
            )
        ));
        if (empty($types) || empty($users) || empty($document)) {
            $this->Session->setFlash(__('You can not create a round without having at least one user,one document and one type in the project'));
            $this->redirect(array(
                'controller' => 'projects',
                'action' => 'view',
                $projectId
            ));
        } //empty($types) || empty($users) || empty($document)        

        $deleteCascade = Configure::read('deleteCascade');
        $userConditions = array(
            'group_id' => 2,
            'id' => $users
        );

        if ($deleteCascade) {
            $userConditions = array(
                'username !=' => 'Removing...',
                'group_id' => 2,
                'id' => $users
            );
        }
        $users = $this->Round->User->find('list', array(
            'recursive' => -1,
            'conditions' => $userConditions
        ));



        $this->set('max', $max);
        $this->set(compact('types', 'users'));
        $this->set('projectId', $projectId);
    }

    /**
     * copyRound method
     *
     * @throws NotFoundException
     * @param string $projectId
     * @return void
     * @deprecated since version number
     */
    //esta funcion puede ser muy muy pesada
    public function oldCopyRound($projectId = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this -> autoRender = false;
            if (!empty($this->request->data['Round']['Round'])) {
                $oldRoundId = $this->request->data['Round']['Round'];
                $round = $this->Round->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Round.id' => $oldRoundId
                    )
                ));

                if (trim($this->request->data['Round']['title']) == '' || !isset($this->request->data['Round']['title'])) {
                    $this->Session->setFlash('Please select one Title');
                    $this->redirect(array(
                        'controller' => 'rounds',
                        'action' => 'copyRound',
                        $projectId
                    ));
                }
                App::uses('CakeTime', 'Utility');

                if (CakeTime::isFuture($round['Round']['ends_in_date'])) {
                    $this->Round->id = $round['Round']['id'];
                    $date = CakeTime::format('-1 days', '%Y-%m-%d');
                    $this->Round->saveField('ends_in_date', $date);
                }


                if (empty($this->request->data['Round']['User'])) {
                    $this->Session->setFlash('You must choose at least one user');
                    $this->redirect(array(
                        'controller' => 'rounds',
                        'action' => 'copyRound',
                        $projectId
                    ));
                } else {
                    $oldRoundId = $this->request->data['Round']['Round'];
                    $this->Round->create();
                    $this->request->data['Round']['ends_in_date'] = NULL;
                    $users = $this->request->data['Round']['User'];
                    $conditions_userRounds = array(
                        'UsersRound.round_id' => $oldRoundId,
                        'UsersRound.user_id' => $users
                    );
                    //guardamos los usuarios en condiciones
                    unset($this->request->data['Round']['User']);
                    $title = $this->request->data['Round']['title'];
                    $this->request->data['Round']['title'] = $title . '-[0%]';
                    $errors = "";
                    //$db = $this->Round->getDataSource();
                    //$db->begin();
                    if ($this->Round->save($this->request->data, false)) {
                        //se deshabilita el save para poder guardar rounds con ends_in_date = NULL
                        //esta bandera marcara que un round esta en estado de copia
                        $this->Session->setFlash('We are creating a new version of the round. Please be patient', 'information');
                        //cortamos la ejecucion parab el usuario pero el script sigue en ejecucion
                        //de esta forma el usuario puedeseguir navegando
                        $this->backGround(array(
                            'controller' => 'projects',
                            'action' => 'view',
                            $round['Round']['project_id']
                        ));
                        $round_id = $this->Round->id;
                        $newRoundId = $this->Round->id;
                        $size_userRounds = $this->Round->UsersRound->find('count', array(
                            'recursive' => -1,
                            'conditions' => $conditions_userRounds
                        ));
                        /**
                         * tamaño de particiones
                         * Haremos una particion de los rounds para no sobrecargar la memoria
                         * ** */
                        if ($size_userRounds > 0) {
                            //hacemos partioces de user_rounds de 100
                            $particiones_userRounds = 100;
                            $size_userRounds_total = $size_userRounds;
                            //si el tamaño es mayor que la particion calculamos cuantas veces vamos a tener que hacer
                            if ($size_userRounds > $particiones_userRounds) {
                                $fin_userRounds = $size_userRounds / $particiones_userRounds;
                                //calculamos el numero de beces a la baja
                                // por ejemplo 2.5 se haces dos veces
                                $fin_userRounds = floor($fin_userRounds);
                                //si existe resto se hace una vez mas
                                if ($size_userRounds % $particiones_userRounds != 0)
                                    $fin_userRounds++;
                            } else {
                                // si no la particion es = al tamaño
                                $particiones_userRounds = $size_userRounds;
                                $fin_userRounds = 1;
                            }
                            $contador_userRounds = 0;
                            $procces_userRounds = 0;
                            $time = date('Y-m-d H:i:s');
                            //variables para monotorizar la copia
                            $worked = 0;
                            $procces = 0;
                            while ($contador_userRounds < $fin_userRounds) {
                                $usersRounds = $this->Round->UsersRound->find('all', array(
                                    'offset' => $procces_userRounds,
                                    'limit' => $particiones_userRounds,
                                    'recursive' => -1,
                                    'fields' => array(
                                        'UsersRound.id',
                                        'UsersRound.user_id',
                                        'UsersRound.document_id',
                                        'UsersRound.text_marked'
                                    ),
                                    'conditions' => $conditions_userRounds
                                ));
                                //se eligen bucles for en vez de bucles foreach dado que son mas rapidos si se van a modificar datos
                                //http://www.phpbench.com/
                                $usersRoundTam = sizeof($usersRounds);
                                for ($i = 0; $i < $usersRoundTam; $i++) {
                                    $procces++;
                                    $worked = (($procces / $size_userRounds_total) * 100);
                                    if (round($worked) % 10 == 0) {
                                        $data = array(
                                            'Round' => array(
                                                'title' => $title . '-[' . round($worked) . '%]'
                                            )
                                        );
                                        $this->Round->save($data);
                                    }
                                    $usersRounds[$i]['UsersRound']['created'] = $time;
                                    $conditions = array(
                                        'Annotation.round_id' => $oldRoundId,
                                        'Annotation.document_id' => $usersRounds[$i]['UsersRound']['document_id'],
                                        'Annotation.user_id' => $usersRounds[$i]['UsersRound']['user_id'],
                                        'Annotation.users_round_id' => $usersRounds[$i]['UsersRound']['id'],
                                    );
                                    $textoForMatches = $usersRounds[$i]['UsersRound']['text_marked'];
                                    $usersRounds[$i]['UsersRound']['round_id'] = $newRoundId;
                                    unset($usersRounds[$i]['UsersRound']['id']);
                                    unset($usersRounds[$i]['UsersRound']['text_marked']);

                                    $this->Round->UsersRound->create();
                                    if ($this->Round->UsersRound->save($usersRounds[$i])) {
                                        $size_annotations = $this->Round->UsersRound->Annotation->find('count', array(
                                            'recursive' => -1,
                                            'conditions' => $conditions
                                        ));
                                        if ($size_annotations > 0) {
                                            $parseKey = Configure::read('parseKey');
                                            $parseIdAttr = Configure::read('parseIdAttr');
                                            /**
                                             * Particiones de anotaciones
                                             */
                                            $particiones_annotations = 400;
                                            if ($size_annotations > $particiones_annotations) {
                                                $fin_annotations = $size_annotations / $particiones_annotations;
                                                $fin_annotations = floor($fin_annotations);
                                                if ($size_annotations % $particiones_annotations != 0)
                                                    $fin_annotations++;
                                            } else {
                                                $particiones_annotations = $size_annotations;
                                                $fin_annotations = 1;
                                            }
                                            $contador_annotations = 0;
                                            $procces_annotations = 0;
                                            $annotations_id = array();
                                            while ($contador_annotations < $fin_annotations) {
                                                $annotations = $this->Round->UsersRound->Annotation->find('all', array(
                                                    'offset' => $procces_annotations,
                                                    'limit' => $particiones_annotations,
                                                    'recursive' => -1,
                                                    'conditions' => $conditions
                                                ));
                                                $annotationTam = sizeof($annotations);
                                                for ($j = 0; $j < $annotationTam; $j++) {
                                                    $oldId = $annotations[$j]['Annotation']['id'];
                                                    unset($annotations[$j]['Annotation']['id']);
                                                    //insertamos nuevo user round
                                                    $annotations[$j]['Annotation']['users_round_id'] = $this->Round->UsersRound->id;
                                                    //insertamos nuevo round
                                                    $annotations[$j]['Annotation']['round_id'] = $newRoundId;
                                                    if (empty($annotations[$j]['Annotation']['annotated_text']))
                                                        $annotations[$j]['Annotation']['annotated_text'] = 'empty?';
                                                    $this->Round->UsersRound->Annotation->create();
                                                    if ($this->Round->UsersRound->Annotation->save($annotations[$j])) {
                                                        $newId = $this->Round->UsersRound->Annotation->id;
                                                        array_push($annotations_id, $newId);
                                                        $this->Round->UsersRound->Annotation->query("insert into annotations_questions ( annotation_id,question_id,answer)
                                                     SELECT " . $newId . ",question_id,answer FROM annotations_questions where annotation_id = $oldId");
                                                    }
                                                } //$i = 0; $i < $annotationTam; $i++
                                                if ($procces_annotations + $particiones_annotations * 2 > $size_annotations) {
                                                    $procces_annotations += $particiones_annotations;
                                                    $particiones_annotations = $size_annotations - $procces_annotations;
                                                } else {
                                                    $procces_annotations += $particiones_annotations;
                                                }
                                                $contador_annotations++;
                                            }
                                            /*
                                             * A partir de este punto machearemos las anotaciones en el documento actualizando su ID
                                             * */
                                            preg_match_all("/<mark[^>]*" . $parseKey . "[^>]*[^>]*>/", $textoForMatches, $matches);
                                            $numberOfMaches = sizeof($matches[0]);
                                            // el sistema es flexible frente a contratiempos imprevistos como que el umero de anotaciones parseadas sean distintos a la base de datos
                                            $pos = -1;
                                            $k = 0;
                                            $ultimoId = -1;
                                            while ($k < $numberOfMaches) {
                                                preg_match('/[^>]*' . $parseIdAttr . '=.?(\\d+).?[^>]/', $matches[0][$k], $id);
                                                if ($ultimoId != $id) {
                                                    $ultimoId = $id;
                                                    $pos++;
                                                }
//                                                debug($annotations_id[$pos]);
                                                $search = $matches[0][$k];
//                                                if(!isset($annotations_id[$pos])) {
//                                                    debug($id);
//                                                }

                                                $replace = preg_replace('/' . $parseIdAttr . '=.(\\d+)./', $parseIdAttr . '="' . $annotations_id[$pos] . '"', $search);
                                                $textoForMatches = str_replace($search, $replace, $textoForMatches);
                                                $k++;
                                            }
                                            if ($pos + 1 != sizeof($annotations_id)) {

//                                                debug(sizeof($annotations_id));
//                                                debug($numberOfMaches);
//                                                debug($pos);
//                                                debug($usersRounds[$i]['UsersRound']['document_id']);
                                                $errors = "This round is courrupted.  Unexpected error has occurred. Do not worry this is not a fatal error. But please contact the administrator. Error: 'other number annotations database VS parse'";
                                            }
                                        }
                                        //guardamos el documento con el texto anotado actualizado
                                        $usersRounds[$i]['UsersRound']['text_marked'] = $textoForMatches;
                                        unset($usersRounds[$i]['UsersRound']['id']);
                                        $this->Round->UsersRound->save($usersRounds[$i]);
                                    } //$this->Round->UsersRound->save($usersRounds[$i])
                                } //$i = 0; $i < $usersRoundTam; $i++
                                if ($procces_userRounds + $particiones_userRounds * 2 >= $size_userRounds) {
                                    $procces_userRounds += $particiones_userRounds;
                                    $particiones_userRounds = $size_userRounds - $procces_userRounds;
                                } else {
                                    $procces_userRounds += $particiones_userRounds;
                                }
                                $contador_userRounds++;
                            }
                        }
                        //finalmente actualizamos los datos del round con los datos que metio el usuario e introducimos los tipos de anotacion
                        //del round origen

                        $date = CakeTime::format('+30 days', '%Y-%m-%d');
                        $data = array(
                            'Round' => array(
                                'description' => $errors,
                                'ends_in_date' => $date,
                                'title' => $title
                            ),
                            //creamos para el resto de usuarios que no tuvieran ningun documento del round
                            'User' => array(
                                'User' => $users
                            )
                        );
                        if ($this->Round->save($data)) {
                            //$this->Round->commit();
                            $round_id = $this->Round->id;
                            $this->Round->query("insert into types_rounds ( round_id,type_id) select $round_id,type_id
                                                                                                          from types_rounds 
                                                                                                            where round_id = $oldRoundId");
                            //$db->commit();
                            //$db->close();
                        } else {
                            //$db->rollback();
                            //$db->close();
                        }
                    } //$this->Round->save($this->request->data)
                    else {
                        //$db->rollback();
                        //$db->close();
                        $this->Session->setFlash('Unexpected error ocurs, error Id: roundCopySave');
                        $this->redirect(array(
                            'controller' => 'rounds',
                            'action' => 'copyRound',
                            $projectId
                        ));
                    }
                }
            } else {
                $this->Session->setFlash('There are no data to be copied!');
                $this->redirect(array(
                    'controller' => 'rounds',
                    'action' => 'copyRound',
                    $projectId
                ));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->Round->Project->id = $projectId;
            if (!$this->Round->Project->exists()) {
                throw new NotFoundException(__('Invalid round'));
            } //!$this->Round->exists()
            $deleteCascade = Configure::read('deleteCascade');
            $conditions = array(
                'project_id' => $projectId,
                'ends_in_date IS NOT NULL'
            );
            if ($deleteCascade) {
                $conditions = array(
                    'project_id' => $projectId,
                    'ends_in_date IS NOT NULL',
                    'title!=\'Removing...\''
                );
            }
            $rounds = $this->Round->find('list', array(
                'conditions' => $conditions
            ));
            if (empty($rounds)) {
                $this->Session->setFlash('Insufficient data for this operation');
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $projectId
                ));
            } else {
                $cond = array(
                    'project_id' => $projectId
                );
                $users = $this->Round->Project->ProjectsUser->find('all', array(
                    'fields' => 'user_id',
                    'conditions' => $cond,
                    'recursive' => -1
                ));
                $users = $this->flatten($users);
                $userConditions = array(
                    'id' => $users
                );
                if ($deleteCascade)
                    $userConditions = array(
                        'username !=' => 'Removing...',
                        'id' => $users
                    );
                $users = $this->Round->User->find('list', array(
                    'conditions' => $userConditions
                ));
            }
            $this->set('rounds', $rounds);
            $this->set('users', $users);
            $this->set('projectId', $projectId);
        }
    }

    /**
     * copyRound method
     *
     * @throws NotFoundException
     * @param string $projectId
     * @return void
     * @deprecated since version number
     */
    //esta funcion puede ser muy muy pesada
    public function copyRound($projectId = null) {
        $this->Job = $this->Round->User->Job;

        if ($this->request->is('post') || $this->request->is('put')) {
            //$this -> autoRender = false;
            if (!empty($this->request->data['Round']['Round'])) {
                $oldRoundId = $this->request->data['Round']['Round'];
                $round = $this->Round->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Round.id' => $oldRoundId
                    )
                ));

                if (trim($this->request->data['Round']['title']) == '' || !isset($this->request->data['Round']['title'])) {
                    $this->Session->setFlash('Please select one Title');
                    $this->redirect(array(
                        'controller' => 'rounds',
                        'action' => 'copyRound',
                        $projectId
                    ));
                }

                if (empty($this->request->data['User']['User'])) {
                    $this->Session->setFlash('You must choose at least one user');
                    $this->redirect(array(
                        'controller' => 'rounds',
                        'action' => 'copyRound',
                        $projectId
                    ));
                } else {
                    $round_id = $this->request->data['Round']['Round'];
                    $this->Round->create();
                    $this->request->data['Round']['ends_in_date'] = NULL;
                    $users = $this->request->data['User']['User'];
                    $title = "\"" . $this->request->data['Round']['title'] . "\"";
                    $users = json_encode($users);
                    $user_id = $this->Session->read('user_id');

                    //se deshabilita el save para poder guardar rounds con ends_in_date = NULL
                    //esta bandera marcara que un round esta en estado de copia
                    $this->Job->create();
                    $data = array('user_id' => $user_id, 'round_id' => $round_id,
                        'percentage' => 0, '' => "",
                        'status' => 'Starting...');

                    if ($this->Job->save($data)) {
                        $programName = "Copy round";
                        $id = $this->Job->id;
                        $operationId = 8;
                        $arguments = "$operationId\t$id\t$user_id\t$round_id\t$users\t$title";
                        $this->sendJob($id, $programName, $arguments, false);
                        $this->Session->setFlash('We are creating a new version of the round. Please be patient', 'information');
                        return $this->redirect(array(
                                    'controller' => 'jobs',
                        ));
                    }
                } //$this->Round->save($this->request->data)
            } else {
                $this->Session->setFlash('There are no data to be copied!');
                $this->redirect(array(
                    'controller' => 'rounds',
                    'action' => 'copyRound',
                    $projectId
                ));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->Round->Project->id = $projectId;
            if (!$this->Round->Project->exists()) {
                throw new NotFoundException(__('Invalid round'));
            } //!$this->Round->exists()
            $deleteCascade = Configure::read('deleteCascade');
            $conditions = array(
                'project_id' => $projectId,
                'ends_in_date IS NOT NULL'
            );
            if ($deleteCascade) {
                $conditions = array(
                    'project_id' => $projectId,
                    'ends_in_date IS NOT NULL',
                    'title!=\'Removing...\''
                );
            }
            $rounds = $this->Round->find('list', array(
                'conditions' => $conditions
            ));
            if (empty($rounds)) {
                $this->Session->setFlash('Insufficient data for this operation');
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $projectId
                ));
            } else {
                $cond = array(
                    'project_id' => $projectId
                );
                $users = $this->Round->Project->ProjectsUser->find('all', array(
                    'fields' => 'user_id',
                    'conditions' => $cond,
                    'recursive' => -1
                ));
                $users = $this->flatten($users);
                $userConditions = array(
                    'id' => $users
                );
                if ($deleteCascade)
                    $userConditions = array(
                        'username !=' => 'Removing...',
                        'id' => $users
                    );
                $users = $this->Round->User->find('list', array(
                    'conditions' => $userConditions
                ));
            }
            $this->set('rounds', $rounds);
            $this->set('users', $users);
            $this->set('projectId', $projectId);
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
        $deleteCascade = Configure::read('deleteCascade');
        $this->Round->id = $id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->Round->exists()


        $projectId = $this->Round->field('project_id');

        $max = $this->Round->Project->DocumentsProject->find('count', array(
            'recursive' => -1,
            'order' => 'DocumentsProject.document_id ASC',
            'conditions' => array(
                "project_id" => $projectId)
        ));

        if ($this->request->is('post') || $this->request->is('put')) {
            if (date('Y-m-d') > date(strtotime($this->request->data['Round']['ends_in_date']))) {
                $roundInCopy = $this->Round->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            'ends_in_date' => NULL,
                            'title' => 'Removing...'
                        ),
                        'project_id' => $this->request->data['Round']['project_id']
                    )
                ));
                if (!empty($roundInCopy)) {
                    $this->Session->setFlash(__('you can not modify rounds to date before today while another round is being copied or deleted'));
                    $this->redirect(array(
                        'controller' => 'projects',
                        'action' => 'view',
                        $this->request->data['Round']['project_id']
                    ));
                }
            }
            //Dado que la tabla user_rounds es algo especial
            //buscamos el primer documento para constatar si existe un user round para cada usuario
            $document = $this->Round->Project->DocumentsProject->find('first', array(
                'recursive' => -1,
                'order' => 'DocumentsProject.document_id ASC',
                'conditions' => array(
                    "project_id" => $this->request->data['Round']['project_id']
                )
            ));
            

            $users = $this->request->data['User']['User'];



            //buscamos los usuarios  a ser eliminados que son aquellos que no se han escogido
            $deleteUsers = array();
            if (!empty($users)) {
                $deleteUsers = $this->Round->Project->ProjectsUser->find('list', array(
                    'recursive' => -1,
                    'fields' => 'user_id',
                    'conditions' => array(
                        "project_id" => $this->request->data['Round']['project_id'],
                        "NOT" => array(
                            'user_id' => $users
                        )
                    )
                ));
            }

//            $data = array();
//            if (!empty($users)) {
//                //rrecorremos los usuarios para escoger aquellos a los que se le va a crear un round
//                foreach ($users as $key => $user) {
//                    //buscamos si existe algun user round
//                    $condition = array(
//                        'round_id' => $id,
//                        'user_id' => $user,
//                    );
//                    $userRound = $this->Round->UsersRound->find('first', array(
//                        'recursive' => -1,
//                        'fields' => 'id',
//                        'conditions' => $condition
//                    ));
//                    //si no existe lo guardamos para crearlo posteriormente
//                    if (empty($userRound)) {
//                        array_push($data, array(
//                            'round_id' => $id,
//                            'user_id' => $user,
//                            'state' => $this->request->data['Round']['state']));
//                    } else {
//                        array_push($data, array(
//                            'id' => $userRound['UsersRound']['id'],
//                            'state' => $this->request->data['Round']['state']));
//                    }
//                }
//            }

            if (isset($this->request->data['Round']['interval'])) {
                $interval = explode(",", $this->request->data['Round']['interval']);

                if ($interval[0] != $interval[1]) {
                    $this->request->data['Round']['start_document'] = $interval[0];
                    $this->request->data['Round']['end_document'] = $interval[1];
                } else {
                    $this->request->data['Round']['start_document'] = 1;
                    $this->request->data['Round']['end_document'] = $max;
                }
            }

            $this->Round->begin();
            if ($this->Round->save($this->request->data)) {
                //creamos los rounds para los que no lo tengan
                $existError = false;
//                if (!empty($users)) {
//                    if (!$this->Round->UsersRound->saveMany($data)) {
//                        $existError = true;
//                    }
//                }
                //borramos para el resto
                if (!empty($deleteUsers)) {
                    if (!$this->Round->UsersRound->deleteAll(array(
                                'UsersRound.round_id' => $this->Round->id,
                                'UsersRound.user_id' => $deleteUsers
                            ))) {
                        $existError = true;
                    }
                }


                if ($existError)
                    $this->Round->rollback();
                else
                    $this->Round->commit();
                $this->Session->setFlash(__('Round has been saved'), 'success');
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $this->request->data['Round']['project_id']
                ));
            } //$this->Round->save($this->request->data)
            else {
                $this->Session->setFlash(__('Round could not be saved. Please, try again.'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $contain = array(
                'Type' => array(
                    'id',
                    'name'
                ),
                'User' => array(
                    'id',
                    'username',
                    'surname'
                )
            );
            $this->request->data = $this->Round->find('first', array(
                'contain' => $contain,
                'conditions' => array(
                    'Round.id' => $id
                )
            ));
            $cond = array(
                "DocumentsProject.project_id" => $this->request->data['Round']['project_id']
            );
            $document = $this->Round->Project->DocumentsProject->find('first', array(
                'recursive' => -1,
                'order' => 'DocumentsProject.document_id ASC',
                'conditions' => $cond
            ));
            if (empty($document)) {
                $this->Session->setFlash(__('You can not edit a round without having at least one document in the project'));
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $this->request->data['Round']['project_id']
                ));
            } //empty($document)
            $projectId = $this->request->data['Round']['project_id'];
            $types = $this->Round->Type->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Type.project_id' => $projectId
                )
            ));
            $users = $this->Round->Project->ProjectsUser->find('list', array(
                'fields' => array(
                    'ProjectsUser.user_id',
                    'ProjectsUser.project_id'),
                'conditions' => array(
                    'project_id' => $projectId
                ),
                'group' => 'user_id',
                'recursive' => -1
            ));


            $selectedUsers = $this->Round->UsersRound->find('list', array(
                'fields' => 'user_id',
                'conditions' => array(
                    'round_id' => $id
                ),
                'group' => 'user_id',
                'recursive' => -1
            ));

            $userConditions = array(
                'group_id' => 2,
                'id' => array_keys($users)
            );

            if ($deleteCascade) {
                $userConditions = array(
                    'username !=' => 'Removing...',
                    'group_id' => 2,
                    'id' => array_keys($users)
                );
            }

            $users = $this->Round->User->find('list', array(
                'recursive' => -1,
                'conditions' => $userConditions
            ));


            $this->set('max', $max);

            $this->set('types', $types);
            $this->set('users', $users);
            $this->set('selectedUsers', $selectedUsers);
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
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } //!$this->request->is('post')
        //$this->autoRender = false;
        $this->Round->id = $id;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        }
        $round = $this->Round->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id
            )
        ));
        if (date('Y-m-d') > date(strtotime($round['Round']['ends_in_date']))) {
            $roundInCopy = $this->Round->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ends_in_date is NULL',
                    'project_id' => $round['Round']['project_id']
                )
            ));
            if (!empty($roundInCopy)) {
                $this->Session->setFlash(__('you can not delete rounds to date before today while another is being copied'));
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $round['Round']['project_id']
                ));
            }
        }

        $this->Session->write('redirect', array(
            'controller' => 'projects',
            'action' => 'view',
            $round['Round']['project_id']
        ));
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id, 'title');


//        $deleteCascade = Configure::read('deleteCascade');
//        if ($deleteCascade) {
//            $data = array(
//                'Round' => array(
//                    'title' => 'Removing...'
//                )
//            );
//            if ($this->Round->save($data, false)) {
//                $this->Session->setFlash(__('Round is being erased. Please be patient'), 'information');
//                $this->backGround(array(
//                    'controller' => 'projects',
//                    'action' => 'view',
//                    $round['Round']['project_id']
//                ));
//                $this->Round->delete($id, $deleteCascade);
//            }
//        } else {
//            if ($this->Round->delete($id, $deleteCascade)) {
//                $this->Session->setFlash(__('Round has been deleted'), 'success');
//                $this->redirect(array(
//                    'controller' => 'projects',
//                    'action' => 'view',
//                    $round['Round']['project_id']
//                ));
//            }
//        }
//        $this->Session->setFlash(__("Round hasn't been deleted"));
//        $this->redirect(array(
//            'controller' => 'projects',
//            'action' => 'view',
//            $round['Round']['project_id']
//        ));
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteSelected() {
        //$this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } //!$this->request->is('post')
        else {
//            $redirect = $this->Session->read('redirect');
            $projectId = $this->request->data['rounds']['project_id'];
            $roundInCopy = $this->Round->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ends_in_date IS NULL',
                    'project_id' => $projectId
                )
            ));
            if (!empty($roundInCopy)) {
                if (!$this->request->is('ajax')) {
                    $this->Session->setFlash(__('You can not use this feature while another round is being copied. Use simple delete option '));
                    $this->redirect(array(
                        'controller' => 'projects',
                        'action' => 'view',
                        $projectId
                    ));
                } else {
                    return $this->correctResponseJson(array(
                                'success' => false,
                                'error' => 'You can not use this feature while another round is being copied. Use simple delete optio'));
                }
            } else {
                $this->CommonFunctions = $this->Components->load('CommonFunctions');
                $this->CommonFunctions->deleteSelected('title');
//                $ids = json_decode($this->request->data['allRounds']);
//                $deleteCascade = Configure::read('deleteCascade');
//                if ($deleteCascade) {
//                    if ($this->Round->UpdateAll(array(
//                                'Round.title' => '\'Removing...\''
//                                    ), array(
//                                'Round.id' => $ids
//                                    ), -1)) {
//                        $this->Session->setFlash(__('Selected rounds are being deleted. Please be patient'), 'information');
//                        $this->backGround(array(
//                            'controller' => 'projects',
//                            'action' => 'view',
//                            $projectId
//                        ));
//                        $this->Round->deleteAll(array(
//                            'Round.id' => $ids,
//                            'Round.ends_in_date IS NOT NULL',
//                            'Round.project_id' => $projectId
//                                ), $deleteCascade);
//                    }
//                } else {
//                    if (!$this->request->is('ajax')) {
//                        if ($this->Round->deleteAll(array(
//                                    'Round.id' => $ids
//                                        ), $deleteCascade)) {
//                            $this->Session->setFlash(__('Rounds selected have been deleted'), 'success');
//                            $this->redirect($redirect);
//                        }
//                    } else {
//                        return $this->correctResponseJson(array('success' => true));
//                    }
//                }
//                if (!$this->request->is('ajax')) {
//                    $this->Session->setFlash(__("Rounds selected haven't been deleted"));
//                    $this->redirect(array(
//                        'controller' => 'projects',
//                        'action' => 'view',
//                        $projectId
//                    ));
//                } else {
//                    return $this->correctResponseJson(array('success' => false, 'error' => 'Unknown error'));
//                }
            }
        }
    }

    function exportDataStatistics($projectId = null, $roundId = null) {

        $this->Round->Project->id = $projectId;
        $this->Round->id = $roundId;
        if (!$this->Round->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        else {
            $margin = 0;
            $project = $this->Round->Project->find('first', array(
                'recursive' => -1,
                array(
                    'conditions' => array(
                        'id' => $projectId))));
            $users = $this->Round->Project->User->find('all', array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'type' => 'inner',
                        'table' => 'projects_users',
                        'alias' => 'ProjectsUser',
                        'conditions' => array(
                            'ProjectsUser.user_id = User.id',
                            'ProjectsUser.project_id' => $projectId)
                    )
                ),
            ));


            $types = $this->Round->Project->Type->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Type.project_id' => $projectId
                )
            ));

            $round = $this->Round->find('first', array(
                'recursive' => -1,
                array(
                    'conditions' => array(
                        'id' => $roundId))));
            $exportData = $this->Session->read('exportData');
            if (empty($exportData)) {
                $typeIds = $this->Round->Project->Type->find('list', array(
                    'recursive' => -1,
                    'fields' => 'Type.id',
                    'conditions' => array(
                        'Type.project_id' => $projectId
                    )
                ));

                $relationUsers = array();
                $tam = sizeof($users);
                $typeIdsString = implode(",", $typeIds);
                $db = $this->Round->Project->getDataSource();
                $totalUsersAnnotation = array();

                if ($this->request->is('post') || $this->request->is('put')) {
                    $scriptTimeLimit = Configure::read('scriptTimeLimit');
                    set_time_limit($scriptTimeLimit);
                    error_reporting(E_ERROR | E_PARSE);
                    $_SESSION['progress'] = 0;
                    header("Content-Encoding: none");
                    header("Content-Length: " . 0);
                    header("Connection: close");
                    ob_end_flush();
                    flush();
                }


                //el array que viene de la BD trae como claves los ids de los tipos
                for ($i = 0; $i < $tam - 1; $i++) {
                    $userA = $users[$i]['User'];
                    //las siguientes lineas son debidas a causa de la barra de progreso
                    for ($j = $i + 1; $j < $tam; $j++) {
                        $userB = $users[$j]['User'];
                        $hits = $db->fetchAll("select count(a.id) as hits from  annotations a FORCE INDEX (complex_index_2), annotations b FORCE INDEX (complex_index_2) 
							where a.round_id =:round_a and b.round_id =:round_b and a.user_id =:user_a and b.user_id =:user_b 
							and a.document_id=b.document_id  and a.type_id=b.type_id and a.type_id in ($typeIdsString) 
							and a.init between b.init - :margin and b.init + :margin and a.end between b.end - :margin and b.end + :margin", array(
                            'round_a' => $roundId,
                            'round_b' => $roundId,
                            'user_a' => $userA['id'],
                            'user_b' => $userB['id'],
                            'margin' => 0
                        ));

                        array_push($relationUsers, array(
                            'fila' => $userA['id'],
                            'columna' => $userB['id'],
                            'hits' => $hits[0][0]['hits']
                        ));
                    }
                }

                foreach ($users as $user) {
                    $totalTypeData = array();
                    foreach ($types as $type) {
                        $count = $this->Round->Project->Type->Annotation->find('count', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'Annotation.type_id' => $type['Type']['id'],
                                'Annotation.user_id' => $user['User']['id'],
                                'Annotation.round_id' => $roundId
                            )
                        ));

                        array_push($totalTypeData, array(
                            'GraficColumns' => $type['Type']['name'],
                            'Colour' => $this->RGBToHex($type['Type']['colour']),
                            'value' => $count
                        ));
                    }

                    $totalUsersAnnotation[$user['User']['id']] = $totalTypeData;
                }

                $this->Session->write('exportData', array(
                    'annotationsConfrontation' => $relationUsers,
                    'totalUsersAnnotation' => $totalUsersAnnotation));
                $_SESSION['progress'] = 100;
                session_write_close();
            } else {
                $relationUsers = $exportData['annotationsConfrontation'];
                $totalUsersAnnotation = $exportData['totalUsersAnnotation'];
                $_SESSION['progress'] = 100;
                session_write_close();
                session_start();
            }
            $cond = array(
                'Annotation.round_id' => $roundId,
            );
            $cont = $this->Round->Project->Type->Annotation->find('count', array(
                'conditions' => $cond
            ));
            $this->set('NumAnnotations', $cont);
            $this->set('users', $users);
            $this->set('project', $project);
            $this->set('round', $round);
            $this->set('hitsArray', $relationUsers);
            $this->set('project_id', $this->Round->Project->id);
            $this->set('round_id', $this->Round->id);
            $this->set('types', $types);
            $this->set('totalUsersAnnotation', $totalUsersAnnotation);
            $this->set('redirect', 'confrontationSettingMultiRound');
            //$this->render('confrontationMulti');
        }
    }

    function getTypes($roundId) {

        $this->Round->id = $roundId;
        $this->Type = $this->Round->Type;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        }
//        if ($this->request->is('ajax')) {
        $types = Cache::read('usersRoundTypes-round-id-' . $roundId, 'short');
        if (!$types) {
            $types = $this->Type->find('all', array(
                'recursive' => -1,
                'contain' => array(
                    'Question',
                ),
                'joins' => array(
                    array(
                        'table' => 'types_rounds',
                        'alias' => 'TypesRound',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TypesRound.type_id = Type.id',
                        ))
                ),
                'conditions' => array(
                    'TypesRound.round_id' => $roundId
                )
            ));
            Cache::write('usersRoundTypes-round-id-' . $roundId, $types, 'short');
        }

        $this->set('types', $types);
//        }

        $this->layout = false;
    }

    function uploadDictionary($round_id, $user_id) {
        $group_id = $this->Session->read('group_id');

        if ($group_id > 1) {
            throw new NotFoundException(__('Invalid User'));
        } else {
            
        }
        $this->set('round_id', $round_id);
        $this->set('user_id', $user_id);
        if ($this->request->is('ajax')) {
//            $this->autoRender = false;
            $this->layout = false;
        }
    }

    function automaticAnnotation() {
        $this->Job = $this->Round->User->Job;
        $this->UsersRound = $this->Round->UsersRound;
        $this->User = $this->Round->User;
        $enableJavaActions = Configure::read('enableJavaActions');

        if ($this->request->is('post') && $enableJavaActions) {

            $user_id = $this->request->data['user_id'];
            $round_id = $this->request->data['round_id'];
            $group_id = $this->Session->read('group_id');
            if ($group_id > 1) {
                $user_id = $this->Session->read('user_id');
                $this->User->id = $user_id;
            }
            $this->Round->id = $this->request->data['round_id'];

            $userRound = $this->UsersRound->find('first', array(
                'recursive' => -1, //int
                'fields' => array('id', 'state'),
                'conditions' => array('user_id' => $user_id, 'round_id' => $round_id), //array of conditions
            ));


            if (empty($userRound)) {
                throw new NotFoundException(__('Invalid Round'));
            }

            $operation = $this->request->data['operation'];
            App::uses('CakeTime', 'Utility');
            $group_id = $this->Session->read('group_id');
            $isEnd = false;
            if ($group_id > 1) {
                $isEnd = CakeTime::isPast($this->Round->field('ends_in_date'));
            }

//            $this->Job->find->hasAny(array('user_id'))


            if ($operation == -1) {
                $this->killJob($this->request->data['job_id']);
            } else {
                $programName = "Automatic_Dictionary_Annotation";

                if (!$isEnd && $userRound['UsersRound']['state'] == 0) {
                    $this->Job->create();
                    $data = array('user_id' => $user_id, 'round_id' => $round_id,
                        'percentage' => 0, '' => $programName,
                        'status' => 'Starting...');
                    if ($this->Job->save($data)) {
                        $id = $this->Job->id;
                        switch ($operation) {
                            // Kill process
                            case -1:
                                break;
                            // Anotacion en base a las anotaciones del usuario.
                            case 1 :
                                $operationId = 6;
                                $types = json_encode($this->request->data['types']);
                                if (!empty($types)) {
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$types";
                                    return $this->sendJob($id, $programName, $arguments);
                                } else {
                                    return $this->correctResponseJson(json_encode(array(
                                                'success' => false,
                                                'message' => "The task could not be performed successfully. Other operation is in progress. Please select at least one type")));
                                }
                                break;
                            case 2:
                                $operationId = 9;
                                App::uses('Folder', 'Utility');
                                $dictionary = new File($this->request->data['File']['tmp_name'], false, 0777);
                                if ($dictionary->exists()) {
                                    $path = Configure::read('uploadFolder');
                                    $newPath = $path . uniqid();
                                    $dictionary->copy($newPath);
                                    $project_id = $this->Round->field('project_id');
                                    $arguments = "$operationId\t$id\t$user_id\t$round_id\t$project_id\t$newPath";
                                    $this->sendJob($id, $programName, $arguments, false);

                                    if ($this->request->is("ajax")) {
                                        return $this->correctResponseJson(json_encode(array(
                                                    'success' => true,
                                        )));
                                    } else {
                                        return $this->redirect(array(
                                                    'controller' => 'jobs',
                                                    'action' => 'index',
                                        ));
                                    }
                                    break;
                                }
                        }
                    }
                }

                if ($this->request->is("ajax")) {
                    return $this->correctResponseJson(json_encode(array(
                                'success' => false,
                                'message' => "The task could not be performed successfully. ")));
                } else {

                    $this->Session->setFlash(__('The task could not be performed successfully'));
                    return $this->redirect(array(
                                'controller' => 'jobs',
                                'action' => 'index',
                    ));
                }
            }
        }
    }

}
