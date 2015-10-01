<?php

App::uses('AppController', 'Controller');

/**
 * Rounds Controller
 *
 * @property Round $Round
 */
class RoundsController extends AppController {

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
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
        $this->set('round', $this->Round->find('first', array(
                    'contain' => $contain,
                    'conditions' => array(
                        'Round.id' => $id
                    )
        )));
        $users = $this->Round->UsersRound->find('all', array(
            'recursive' => -1,
            'fields' => 'UsersRound.user_id',
            'group' => 'UsersRound.user_id',
            'conditions' => array(
                'UsersRound.round_id' => $id
            )
        ));
        $users = $this->flatten($users);
        $this->set('users', $this->Round->User->find('all', array(
                    'recursive' => -1,
                    'fields' => array(
                        'User.id',
                        'User.username',
                        'User.surname',
                        'User.email'
                    ),
                    'conditions' => array(
                        'User.id' => $users
                    )
        )));
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
            echo $round['Round']['title'];
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
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Round->create();
            $users = $this->request->data['Round']['User'];
            $this->request->data['Round']['User'] = null;
            if ($this->Round->save($this->request->data)) {
                $allData = array();
                if (!empty($document) && !empty($users)) {
                    foreach ($users as $user) {
                        $data = array(
                            'round_id' => $this->Round->id,
                            'user_id' => $user,
                            'document_id' => $document['DocumentsProject']['document_id']
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
        $users = $this->Round->Project->ProjectsUser->find('all', array(
            'recursive' => -1,
            'fields' => 'user_id',
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
        $users = $this->flatten($users);
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
        $this->set(compact('types', 'users'));
        $this->set('projectId', $projectId);
    }

    /**
     * copyRound method
     *
     * @throws NotFoundException
     * @param string $projectId
     * @return void
     */
    //esta funcion puede ser muy muy pesada
    public function copyRound($projectId = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this -> autoRender = false;
            if (!empty($this->request->data['Round']['Round'])) {
                $oldRoundId = $this->request->data['Round']['Round'][0];
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
                if (strtotime($round['Round']['ends_in_date']) > strtotime('today') || empty($this->request->data['Round']['User'])) {
                    $this->Session->setFlash('You should close the round origin before try copying it.Please put a date prior 
                    to today.Also you must choose at least one user');
                    $this->redirect(array(
                        'controller' => 'projects',
                        'action' => 'view',
                        $projectId
                    ));
                } else {
                    $oldRoundId = $this->request->data['Round']['Round'][0];
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
                                                        $this->Round->UsersRound->Annotation->query("insert into annotations_questions ( `annotation_id`,`question_id`,`answer`)
                                                     SELECT `annotation_id`,`question_id`,`answer` FROM annotations_questions where annotation_id = $oldId");
                                                    } else {
                                                        
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
                                            preg_match_all("/<mark[^>]*Marky[^>]*.value=.([0-9]*)[^>]*>/", $textoForMatches, $matches);
                                            $numberOfMaches = sizeof($matches[0]);
                                            // el sistema es flexible frente a contratiempos imprevistos como que el umero de anotaciones parseadas sean distintos a la base de datos
                                            $pos = -1;
                                            $k = 0;
                                            $ultimoId = -1;
                                            while ($k < $numberOfMaches) {
                                                if ($ultimoId != $matches[1][$k]) {
                                                    $ultimoId = $matches[1][$k];
                                                    $pos++;
                                                }
                                                $search = $matches[0][$k];
                                                $replace = preg_replace('/value=.(\\d+)./', 'value="' . $annotations_id[$pos] . '"', $search);
                                                $textoForMatches = str_replace($search, $replace, $textoForMatches);
                                                $k++;
                                            }
                                            if ($pos != sizeof($annotations_id)) {
                                                //$errors = "Unexpected error has occurred. Do not worry this is not a fatal error. But please contact the administrator. Error: 'other number annotations-parse'";
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
                        $data = array(
                            'Round' => array(
                                'description' => $errors,
                                'ends_in_date' => date('Y-m-d'),
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
            $cond = array(
                "project_id" => $this->request->data['Round']['project_id']
            );
            $document = $this->Round->Project->DocumentsProject->find('first', array(
                'recursive' => -1,
                'order' => 'DocumentsProject.document_id ASC',
                'conditions' => $cond
            ));
            $users = $this->request->data['Round']['User'];
            $document_id = $document['DocumentsProject']['document_id'];
            $allData = array();
            //buscamos los usuarios  a ser eliminados que son aquellos que no se han escogido
            $cond = array(
                "project_id" => $this->request->data['Round']['project_id'],
                "NOT" => array(
                    'user_id' => $users
                )
            );
            $deleteUsers = $this->Round->Project->ProjectsUser->find('all', array(
                'recursive' => -1,
                'fields' => 'user_id',
                'conditions' => $cond
            ));
            $deleteUsers = $this->flatten($deleteUsers);
            if (!empty($document) && !empty($users)) {
                //rrecorremos los usuarios para escoger aquellos a los que se le va a crear un round
                foreach ($users as $key => $user) {
                    //buscamos si existe algun user round
                    $conditions = array(
                        'round_id' => $id,
                        'user_id' => $user,
                        'document_id' => $document_id
                    );
                    $userRound = $this->Round->UsersRound->find('first', array(
                        'recursive' => -1,
                        'conditions' => $conditions
                    ));
                    //si no existe lo guardamos para crearlo posteriormente
                    if (empty($userRound)) {
                        array_push($allData, $conditions);
                    } else {
                        unset($users[$key]);
                    }
                }
            }
            $this->Round->begin();
            if ($this->Round->save($this->request->data)) {
                //creamos los rounds para los que no lo tengan
                $existError = false;
                if (!empty($users)) {
                    if (!$this->Round->UsersRound->saveMany($allData)) {
                        $existError = true;
                    }
                }
                //borramos para el resto
                if (!$this->Round->UsersRound->deleteAll(array(
                            'UsersRound.round_id' => $this->Round->id,
                            'UsersRound.user_id' => $deleteUsers
                        ))) {
                    $existError = true;
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
            $users = $this->Round->Project->ProjectsUser->find('all', array(
                'fields' => 'ProjectsUser.user_id',
                'conditions' => array(
                    'project_id' => $projectId
                ),
                'recursive' => -1
            ));
            $selectedUsers = $this->Round->UsersRound->find('all', array(
                'fields' => 'DISTINCT  user_id',
                'conditions' => array(
                    'round_id' => $id
                ),
                'recursive' => -1
            ));
            $selectedUsers = $this->flatten($selectedUsers);
            $users = $this->flatten($users);
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
        $deleteCascade = Configure::read('deleteCascade');
        if ($deleteCascade) {
            $data = array(
                'Round' => array(
                    'title' => 'Removing...'
                )
            );
            if ($this->Round->save($data, false)) {
                $this->Session->setFlash(__('Round is being erased. Please be patient'), 'information');
                $this->backGround(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $round['Round']['project_id']
                ));
                $this->Round->delete($id, $deleteCascade);
            }
        } else {
            if ($this->Round->delete($id, $deleteCascade)) {
                $this->Session->setFlash(__('Round has been deleted'), 'success');
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $round['Round']['project_id']
                ));
            }
        }
        $this->Session->setFlash(__("Round hasn't been deleted"));
        $this->redirect(array(
            'controller' => 'projects',
            'action' => 'view',
            $round['Round']['project_id']
        ));
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteAll() {
        //$this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } //!$this->request->is('post')
        else {
            $redirect = $this->Session->read('redirect');
            $projectId = $this->request->data['rounds']['project_id'];
            $roundInCopy = $this->Round->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ends_in_date IS NULL',
                    'project_id' => $projectId
                )
            ));
            if (!empty($roundInCopy)) {
                $this->Session->setFlash(__('You can not use this feature while another round is being copied. Use simple delete option '));
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $projectId
                ));
            } else {
                $ids = json_decode($this->request->data['allRounds']);
                $deleteCascade = Configure::read('deleteCascade');
                if ($deleteCascade) {
                    if ($this->Round->UpdateAll(array(
                                'Round.title' => '\'Removing...\''
                                    ), array(
                                'Round.id' => $ids
                                    ), -1)) {
                        $this->Session->setFlash(__('Selected rounds are being deleted. Please be patient'), 'information');
                        $this->backGround(array(
                            'controller' => 'projects',
                            'action' => 'view',
                            $projectId
                        ));
                        $this->Round->deleteAll(array(
                            'Round.id' => $ids,
                            'Round.ends_in_date IS NOT NULL',
                            'Round.project_id' => $projectId
                                ), $deleteCascade);
                    }
                } else {
                    if ($this->Round->deleteAll(array(
                                'Round.id' => $ids
                                    ), $deleteCascade)) {
                        $this->Session->setFlash(__('Rounds selected have been deleted'), 'success');
                        $this->redirect($redirect);
                    }
                }
                $this->Session->setFlash(__("Rounds selected haven't been deleted"));
                $this->redirect(array(
                    'controller' => 'projects',
                    'action' => 'view',
                    $projectId
                ));
            }
        }
    }

}
