<?php

App::uses('AppController', 'Controller');

/**
 * Participants Controller
 *
 * @property Participant $Participant
 * @property PaginatorComponent $Paginator
 */
class ParticipantsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('analysis');
        $this->Auth->allow('results');
        $this->Auth->allow('getProjects');
        $this->Auth->allow('postAndRedirect');
        $this->Auth->allow('listGoldensHits');
        $this->Auth->allow('listFalsePositives');
        $this->Auth->allow('listFalseNegatives');
        $this->Auth->allow('downloadPredictions');
        $this->Auth->allow('downloadTopFalseNegatives');
        $this->Auth->allow('downloadTopFalsePositives');
        $this->Auth->allow('uploadFinalPredictions');
        $this->Auth->allow('getUsedRuns');
        $this->Auth->allow('compare');
        $this->Auth->allow('search');
        $this->Auth->allow('evaluationResults');
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->Participant->recursive = 0;
        $this->set('participants', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Participant->exists($id)) {
            throw new NotFoundException(__('Invalid participant'));
        }
        $options = array('conditions' => array('Participant.' . $this->Participant->primaryKey => $id));
        $this->set('participant', $this->Participant->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($project_id = null) {

        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        if ($this->request->is('post') || $this->request->is('put')) {
            if (isset($this->request->data['Participant']['File']) && $this->request->data['Participant']['File']['size'] > 0) {
                $fileName = $this->request->data['Participant']['File']['name'];
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $checkextension = array("tsv", "txt");
                if (in_array($extension, $checkextension)) {
                    //your code goes here.
                    $db = $this->Participant->getDataSource();
                    $timeLimit = Configure::read('scriptTimeLimit');
                    set_time_limit($timeLimit);
                    $db->begin();

//                    $project_id = $this->request->data['Participant']['Project'];

                    $file = new File($this->request->data['Participant']['File']['tmp_name']);
                    if ($file->readable()) {
                        $content = $file->read();
                        $file->close();
                        $lines = explode("\n", $content);
                        if (!empty($lines)) {
                            $size = count($lines);
                            for ($index = 0; $index < $size; $index++) {
                                if (strlen($lines[$index]) > 1) {
                                    $section = explode("\t", $lines[$index]);
                                    if (sizeof($section) == 3) {
                                        $team_id = trim($section[0]);
                                        $email = trim($section[1]);
                                        $code = trim($section[2]);

                                        $data = array(
                                            'team_id' => $team_id
                                        );
                                        if (!$this->Participant->hasAny($data)) {
                                            if ($this->Participant->hasAny(array(
                                                        'code' => $code
                                                    ))) {
                                                $db->rollback();
                                                $this->Session->setFlash(__("Line " . $index . " could not be save. Participant code repeated"));
//                                                throw new Exception("Line " . $index . " could not be save");
                                                return $this->redirect(array('controller' => 'participants',
                                                            'action' => 'add', $project_id));
                                            }
                                            $this->Participant->create();
                                            $data = array(
                                                'Project' => array('id' => $project_id),
                                                'Participant' => array(
                                                    'team_id' => $team_id,
                                                    'code' => $code,
                                                    'email' => $email
                                            ));

                                            if (!$this->Participant->save($data)) {
                                                $db->rollback();
                                                $this->Session->setFlash(__("Line " . $index + 1 . " could not be save " . json_encode($this->Participant->validationErrors)));
//                                                throw new Exception("Line " . $index . " could not be save");
                                                return $this->redirect(array('controller' => 'participants',
                                                            'action' => 'add', $project_id));
                                            }
                                        } else {


                                            $participant = $this->Participant->find('first', array(
                                                'recursive' => -1,
                                                'conditions' => $data,
                                                'fields' => array('id'))
                                            );
                                            $participant_id = $participant['Participant']['id'];

                                            $projects = $this->getParticipantProjects($email, $code, $project_id);
//
//                                            $projects = array_keys($projects);
//                                            $projects = array_push($projects, $project_id);

                                            $data = array(
                                                'project_id' => $project_id,
                                                'participant_id' => $participant_id,
                                            );


                                            if (empty($projects)) {
                                                $this->Participant->ProjectsParticipant->create();
                                                if (!$this->Participant->ProjectsParticipant->save($data)) {
                                                    $db->rollback();
                                                    $this->Session->setFlash(__("Line " . $index . " could not be save on this project" . json_encode($this->Participant->validationErrors)));
//                                                throw new Exception("Line " . $index . " could not be save");
                                                    return $this->redirect(array(
                                                                'controller' => 'participants',
                                                                'action' => 'add',
                                                                $project_id));
                                                }
                                            }
                                        }
                                    } else {
                                        $db->rollback();
                                        $index++;
                                        $this->Session->setFlash(__("Line " . $index . " is repeated"));
                                        return $this->redirect(array('controller' => 'participants',
                                                    'action' => 'add', $project_id));
                                    }
                                }
                            }

                            $this->Session->setFlash(__('All participants have been saved'), 'success');
                            $db->commit();
                            if ($project_id != 0) {
                                return $this->redirect(array('controller' => 'projects',
                                            'action' => 'view', $project_id));
                            } else {
                                return $this->redirect(array('controller' => 'participants',
                                            'action' => 'add', $project_id));
                            }
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The document could not be processed. Incorrect format or file empty (try txt or tsv file).'));
        }
//        $projects = $this->Participant->Project->find('list');
//        $this->set(compact('projects'));
//
//
//
//        if ($this->request->is('post')) {
//            $this->Participant->create();
//            if ($this->Participant->save($this->request->data)) {
//                $this->Session->setFlash(__('The participant has been saved.'));
//                return $this->redirect(array('action' => 'index'));
//            } else {
//                $this->Session->setFlash(__('The participant could not be saved. Please, try again.'));
//            }
//        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Participant->exists($id)) {
            throw new NotFoundException(__('Invalid participant'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Participant->save($this->request->data)) {
                $this->Session->setFlash(__('The participant has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The participant could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Participant.' . $this->Participant->primaryKey => $id));
            $this->request->data = $this->Participant->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null, $project_id = null) {
        if ($this->Participant->ProjectsParticipant->deleteAll(array('ProjectsParticipant.participant_id' => $id,
                    'ProjectsParticipant.project_id' => $project_id), false)) {
            if (!$this->Participant->ProjectsParticipant->hasAny(array('ProjectsParticipant.participant_id' => $id))) {
                $this->Participant->deleteAll(array('id' => $id), false);
            }

            return $this->correctResponseJson(array('success' => true,));
        } else {
            return $this->correctResponseJson(array('success' => false));
        }
    }

    /**
     * deleteAll method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteSelected($project_id = null) {
        $ids = json_decode($this->request->data['selected-items']);
        $project_id = $this->request->data['participants']['project_id'];
        if ($this->Participant->ProjectsParticipant->deleteAll(array('ProjectsParticipant.participant_id' => $ids,
                    'ProjectsParticipant.project_id' => $project_id), false)) {
            for ($index = 0; $index < count($ids); $index++) {
                if (!$this->Participant->ProjectsParticipant->hasAny(array('ProjectsParticipant.participant_id' => $ids[$index]))) {
                    $this->Participant->deleteAll(array('id' => $ids[$index]), false);
                }
            }

            return $this->correctResponseJson(array('success' => true,));
        } else {
            return $this->correctResponseJson(array('success' => false));
        }
    }

    public function deleteAll() {
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $project_id = $this->request->data['participants']['project_id'];
            if (!$this->Participant->Project->exists($project_id)) {
                throw new NotFoundException(__('Invalid project'));
            }

            $redirect = $this->Session->read('redirect');
            $deleteCascade = Configure::read('deleteCascade');
            $conditions = array('project_id' => $project_id);
            $ids = $this->Participant->ProjectsParticipant->find('list', array(
                'conditions' => array('ProjectsParticipant.project_id' => $project_id),
                'fields' => array('ProjectsParticipant.participant_id', 'ProjectsParticipant.participant_id')
            ));
            if ($this->Participant->ProjectsParticipant->deleteAll($conditions, $deleteCascade)) {
                foreach ($ids as $id) {
                    if (!$this->Participant->ProjectsParticipant->hasAny(array('ProjectsParticipant.participant_id' => $id))) {
                        $this->Participant->deleteAll(array('id' => $id), false);
                    }
                }
                $this->Session->setFlash(__('All participants  have been deleted'), 'success');
                return $this->redirect($redirect);
            }

            $this->Session->setFlash(__("participants haven't been deleted"));
            return $this->redirect($redirect);
        }
    }

    public function setGoldStandard() {
        if ($this->request->is(array('post', 'put'))) {
            $projectId = $this->request->data['Participant']['project_id'];
            $roundId = $this->request->data['Participant']['round_id'];
            $user_id = $this->request->data['Participant']['user_id'];
            $goldenAnnotation = $this->Participant->GoldenAnnotation;
            $this->Participant->Project->id = $projectId;
            $this->Participant->Project->Round->id = $roundId;
            if (!$this->Participant->Project->exists()) {
                throw new NotFoundException(__('Invalid proyect'));
            } //!$this->Project->exists()
            if (!$this->Participant->Project->Round->exists()) {
                throw new NotFoundException(__('Invalid Round'));
            } //!$this->Project->exists()
            $this->Participant->Project->User->Annotation->virtualFields = array(
                'project_id' => $projectId
            );

            $db = $this->Participant->Project->User->Annotation->getDataSource();
            $options = array(
                'table' => $db->fullTableName($this->Participant->Project->User->Annotation),
                'alias' => 'Annotation',
                'recursive' => -1,
                'fields' => array(
                    'Annotation.round_id',
                    'Annotation.user_id',
                    'Annotation.document_id',
                    'Annotation.type_id',
                    'Annotation.init',
                    'Annotation.end',
                    'Annotation.annotated_text',
                    'Annotation.section',
                ),
                'conditions' => array('Annotation.round_id' => $roundId, 'Annotation.user_id' => $user_id),
//                'conditions' => array('round_id' => $roundId,
//                    'Annotation.init IS NOT NULL',
//                    'Annotation.end IS NOT NULL'
//                ),
            );
//$this->ConsensusAnnotation->Annotation->find('all', $options);
            $commit = true;
            $db->begin();



            $commit = $commit & $this->Participant->GoldenProject->deleteAll(array(
                        'project_id' => $projectId));

            $this->Participant->GoldenProject->create();
            $commit = $commit & $this->Participant->GoldenProject->save(array(
                        'project_id' => $projectId,
                        'user_id' => $user_id,
                        'round_id' => $roundId
            ));




            $commit = $commit & $this->Participant->GoldenAnnotation->deleteAll(array(
                        'user_id' => $user_id,
                        'round_id' => $roundId
            ));


            $query = $db->buildStatement($options, $this->Participant->Project->User->Annotation);
            $db->query('INSERT INTO ' . $db->fullTableName($this->Participant->GoldenAnnotation) . ' (round_id,user_id,document_id,type_id,init,end,annotated_text,section) ' . $query);

            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__('Gold Standard have been created'), 'success');
                $this->redirect(array('controller' => 'rounds', 'action' => 'view',
                    $roundId));
            } else {
                $db->rollback();
                $this->Session->setFlash(__("Gold Standard haven't been created"));
                $this->redirect(array('controller' => 'rounds', 'action' => 'view',
                    $roundId));
            }
        } else {
            throw new NotFoundException(__('Invalid proyect'));
        }
    }

    public function deleteGoldStandard() {
        if ($this->request->is(array('post', 'put'))) {
            $projectId = $this->request->data['Participant']['project_id'];
            $roundId = $this->request->data['Participant']['round_id'];

            $this->Participant->Project->id = $projectId;
            $db = $this->Participant->getDataSource();
            $db->begin();

            if (!$this->Participant->Project->exists()) {
                throw new NotFoundException(__('Invalid proyect'));
            } //!$this->Project->exists()

            $this->Participant->Project->Round->id = $roundId;
            if (!$this->Participant->Project->Round->exists()) {
                throw new NotFoundException(__('Invalid Round'));
            } //!$this->Project->exists()
            $commit = true;
            $commit = $commit & $this->Participant->GoldenProject->deleteAll(array(
                        'project_id' => $projectId));



            $commit = $commit & $this->Participant->GoldenAnnotation->deleteAll(array(
                        'round_id' => $roundId));
            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__("Gold Standard have been deleted"), 'success');
                $this->redirect(array('controller' => 'rounds', 'action' => 'view',
                    $roundId));
            } else {
                $db->rollback();
                $this->Session->setFlash(__("Gold Standard haven't been deleted"));
                $this->redirect(array('controller' => 'rounds', 'action' => 'view',
                    $roundId));
            }
        }
    }

    public function analysis() {

        $this->Cookie = $this->Components->load('Cookie');
        $this->Cookie->type('rijndael');
        $projects = array();
//        $this->Cookie->time = '999 hours';

        if ($this->request->is(array('post', 'put'))) {
//            debug($this->request->data);

            if (isset($this->request->data['Participant']['email']))
                $email = $this->request->data['Participant']['email'];

            if (isset($this->request->data['Participant']['code']))
                $code = $this->request->data['Participant']['code'];

            $project_id = -1;


            if (isset($this->request->data['Project']['Project'])) {
                $project_id = $this->request->data['Project']['Project'];
            }

            if ($this->data['Participant']['remember_me'] && isset($code) && isset($email)) {
                $cookie = array();
                $cookie['email'] = $email;
                $cookie['code'] = $code;
                if (isset($project_id)) {
                    $cookie['project_id'] = $project_id;
                }

                $this->Cookie->write('participantData', $cookie, true, '+2 weeks');
            } else {
                if ($this->Cookie->check('participantData')) {
                    $this->Cookie->destroy('participantData');
                }
            }

            $projects = $this->getParticipantProjects($email, $code);


            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');

            if (isset($this->request->data['Participant']['analyze_File']) && $this->request->data['Participant']['analyze_File']['size'] > 0 && !empty($projects)) {

                /* ============================================= */
                /* ==============Load analysis=================== */
                /* ============================================= */
                if ($this->request->data['Participant']['analyze_File']['size'] > $this->filesize2bytes(Configure::read('max_file_size'))) {
                    $this->Session->setFlash("The file can not be more than " . Configure::read('max_file_size'));
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }

                $file = $this->request->data['Participant']['analyze_File']['name'];
                if (pathinfo($file, PATHINFO_EXTENSION) != 'tsv') {
                    $this->Session->setFlash("The file must be in TSV format");
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }

                $file = new File($this->request->data['Participant']['analyze_File']['tmp_name']);
                if ($file->readable()) {
                    $content = $file->read();
                    $file->close();

                    $lines = explode("\n", $content);

                    $incorrectFormat = empty($lines);
                    $count = 0;
                    $size = count($lines);

                    for ($index = 0; $index < $size; $index++) {
                        if (strlen(trim($lines[$index])) > 0) {
                            if (!$incorrectFormat) {
                                $columns = explode("\t", $lines[$index]);
                                for ($i = 0; $i < count($columns); $i++) {
                                    if (strlen(trim($columns[$i])) == 0) {
                                        $incorrectFormat = true;
                                    }
                                }
                                $incorrectFormat = $incorrectFormat || sizeof($columns) != 5;
                                $count++;
                            } else {
                                break;
                            }
                        }
                    }


//                   $correctFormat = $this->correctTsvFormat($file, 5);
                    if ($incorrectFormat) {

//                        $count=$this->incorrecLineTsvFormat($file);
                        $this->Session->setFlash("Incorrect content file. Line $count is incorrect. "
                                . "Content file must be in this format WO2009026621A1->A:12:24->1->0.99->paliperidone");
                        return $this->redirect(array('controller' => 'Participants',
                                    'action' => 'analysis'));
                    }


                    $participantID = $this->Participant->find('first', array(
                        "recursive" => -1,
                        "fields" => array("id"),
                        "conditions" => array(
                            'Participant.email' => $email,
                            'Participant.code' => $code,
                        ),
                    ));

                    $participantID = $participantID["Participant"]["id"];

                    $this->request->data['Participant']['id'] = $participantID;
                    $this->participantSaveConnection($this->request->data['Participant'], "uploadAnalysis");


//                    $this->Participant->UploadedAnnotation->deleteAll(array(
//                        "participant_id" => $participantID));
//
//                    $this->Participant->PredictionDocument->deleteAll(array(
//                        "participant_id" => $participantID,
//                        "project_id" => $project_id
//                    ));




                    $javaJarPath = Configure::read('javaJarPath');
                    $analyze_program = Configure::read('analyze_program');
                    $runJava = Configure::read('runJava');


                    $program = $javaJarPath . DS . $analyze_program;
                    $path = $this->request->data['Participant']['analyze_File']['tmp_name'];
                    $file = new File($path);
                    if ($file->readable()) {

                        $newPath = $file->Folder->path . DS . md5(date('Y-m-d H:i:s:u')) . $file->name() . "mtmp";
                        $file->copy($newPath);
                        $path = $newPath;
                    } else {
                        throw new Exception("Ops! file could not be readed");
                    }


                    $arguments = "$project_id\t$participantID\t$email\t$path";
//                        exec("nohup java -jar $program $arguments", $output);
                    $javaLog = ".." . DS . 'java_jars' . DS . "java.log";
                    $date = date('Y-m-d H:i:s');
                    exec("echo \"$date:$runJava $program $arguments\" >> $javaLog 2>&1 &");
                    exec("$runJava $program $arguments >> $javaLog 2>&1 &");

                    $this->Session->setFlash("Your predictions are being processed, we will send you an email when the analysis finish", 'success');
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }
            } else if (isset($this->request->data['Participant']['results_File']['size']) && $this->request->data['Participant']['results_File']['size'] > 0) {

                if ($this->request->data['Participant']['results_File']['size'] > $this->filesize2bytes(Configure::read('max_file_size'))) {
                    $this->Session->setFlash("The file can not be more than " . Configure::read('max_file_size'));
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }
                /* ============================================= */
                /* =====================Results================= */
                /* ============================================= */
                $file = new File($this->request->data['Participant']['results_File']['tmp_name']);
                if ($file->readable()) {
                    $content = $file->read();
                    $file->close();

                    $content = $this->decrypt($content);
                    $results = json_decode(trim($content), true);

//                        debug($results);
                    if (!empty($results)) {


                        $projects = $this->getParticipantProjects($email, $code, $results['project_id']);
                        if (!empty($projects)) {
                            $goldenSet = $this->Participant->GoldenProject->find('first', array(
                                'recursive' => -1,
                                'fields' => array('user_id', 'round_id'),
                                'conditions' => array('project_id' => $results['project_id'])));
                            if (!empty($goldenSet)) {

                                $isModified = !$this->Participant->PredictionFile->hasAny(array(
                                            'participant_id' => $results['Participant']['id'],
                                            'modified' => $results['date'],
//                                            'project_id' => $results['project_id']
                                ));
                                $results['Participant']['isModified'] = $isModified;
                                $results['Golden']['user_id'] = $goldenSet['GoldenProject']['user_id'];
                                $results['Golden']['round_id'] = $goldenSet['GoldenProject']['round_id'];

                                $this->Session->write("analysisResults", $results);
                                $this->redirect(array(
                                    'action' => 'results'));
                            } else {
                                $this->Session->setFlash("This golden set have been deleted");
                            }
                        } else {
                            $this->Session->setFlash("This file does not correspond to your credentials");
                        }
                    } else {
                        $this->Session->setFlash("This file is corrupted");
                    }
                } else {
                    $this->Session->setFlash("This file is corrupted");
                }
            } else {
                if (empty($projects)) {
                    $this->Session->setFlash("You are not in any project");
                } else {

                    $this->Session->setFlash("One team prediction file or one result file is needed");
                }
            }
        } else {
            $cookie = $this->Cookie->read('participantData');
            if (isset($cookie)) {
                if (isset($cookie['email']) && $cookie['code']) {
                    $this->request->data['Participant']['email'] = $cookie['email'];
                    $this->request->data['Participant']['code'] = $cookie['code'];
                    $this->request->data['Participant']['remember_me'] = true;
                    if (isset($cookie['project_id'])) {
                        $this->request->data['Project']['Project'] = $cookie['project_id'];
                    }
                    $projects = $this->getParticipantProjects($cookie['email'], $cookie['code']);
                    if (empty($projects)) {
                        $this->Cookie->destroy('participantData');
                    }
                } else {
                    $this->Cookie->destroy('participantData');
                }
            }
            $this->loadModel('Post');
            $this->Post->recursive = -1;
            $this->Post->contain(false, array('User' => array('username', 'surname',
                    'full_name', 'image', 'image_type',
                    'id')));
            $this->Post->paginate = array('limit' => 5, 'order' => array('modified' => 'DESC'));
            $this->set("posts", $this->paginate($this->Post));
        }
        $this->set('projects', $projects);
        App::uses('CakeTime', 'Utility');

        $finalDate = Configure::read('final_date_to_upload_tasks');
        $startDate = Configure::read('initial_date_to_upload_tasks');
        $isEnd = CakeTime::isPast($finalDate);
        $isFuture = CakeTime::isFuture($startDate);
        $isThisWeek = CakeTime::isThisWeek($finalDate);
        $isToday = CakeTime::isTomorrow(CakeTime::fromString($finalDate));
        $finalDate = CakeTime::format($finalDate, "%B %e, %Y");
        $startDate = CakeTime::format($startDate, "%B %e, %Y");

        $this->set('isEnd', $isEnd);
        $this->set('isFuture', $isFuture);
        $this->set('isThisWeek', $isThisWeek);
        $this->set('isToday', $isToday);
        $this->set('finalDate', $finalDate);
        $this->set('startDate', $startDate);



////        $key = "xVO5JLSLOTpKX4YRyFpJXNYb1STQK26mHAzgNK6bwS697XzK8ZE5kEA8R7gajaI9fE6HfemeLhg28nqbGTmh5Dv5uKydSOoM4BHlQ43mvH4h0Jl3xFDcv95fRnY9wYAluS1WFi9QOLc2JDUOsN3ggNzypHuZcPaAjBklfsNH98qkX5brqEnfMUubPOUCtpTEUmtvVNq2oTGKSArEuSuuKRnMHtlbKvl4XbaAUvSfajF4DtHwLa2qaWU6pNXLHf16";
////        $key = "FFF3454D1E9CCDE00101010101010101";
//        $value = "63612bb6b56ef964bc2a6add5e0697deadde735fd4ca966d7b762f61b2b4cb14a14500";
////        $value = base64_decode($value);
////        debug($value);
////        $result = Security::rijndael($value, $key, 'decrypt');
//        $result = $this->decrypt($value);
////        $resultE = Security::rijndael($value, $key, 'encrypt');
//        $this->set(compact('result', 'resultE'));
    }

    public function results() {


        $results = $this->Session->read("analysisResults");
//        debug($results);
        if (!empty($results)) {
            $project_id = $results['project_id'];

            $cond = array(
                'project_id' => $project_id
            );
            $types = $this->Participant->Project->Type->find('all', array(
                'fields' => array('id', 'name', 'colour'),
                'recursive' => -1,
                'conditions' => $cond
            ));

//        $results = array(
//            'Participant' => array(
//                'hits' => array('1' => 10, '2' => 100),
//                'only_me' => 150,
//                'total' => 1050,
//                'id' => 1,
//            ),
//            'Golden' => array(
//                'total' => 180,
//                'only_me' => array('4' => 50, '2' => 80),
//                'id'=>1,
//            ),
//            'Types' => $types,
//            'topErrorDocuments' => array('5' => 50, '2' => 50),
//            'project_id' => 1
//        );

            $results['Types'] = $types;


            $project = $this->Participant->Project->find('first', array(
                'recursive' => -1,
                'fields' => array('title'),
                'conditions' => array('id' => $results['project_id'])));

            $documentsIDs = array_merge(array_keys($results['topFalsePositivesDocuments']), array_keys($results['topFalseNegativesDocuments']));
            $documents = $this->Participant->Project->Document->find('list', array(
                'recursive' => -1,
                'fields' => array('external_id'),
                'conditions' => array('id' => $documentsIDs)));

            if (!$results['Participant']['isModified']) {
                $falsePositivesWords = $this->Participant->UploadedAnnotation->find('list', array(
                    'recursive' => -1,
                    'fields' => array('annotated_text'),
                    'conditions' => array('id' => array_keys($results['topFalsePositivesAnnotations']))));
                $falseNegativesWords = $this->Participant->GoldenAnnotation->find('list', array(
                    'recursive' => -1,
                    'fields' => array('annotated_text'),
                    'conditions' => array('id' => array_keys($results['topFalseNegativesAnnotations']))));
            } else {
                $falsePositivesWords = array();
                $falseNegativesWords = array();
            }

//            $prediction = $this->Participant->PredictionFile->find('list', array(
//                'recursive' => -1,
//                'fields' => array('id','modified'),
//                'conditions' => array('participant_id' => $project_id)));
//            $isModified = !$this->Participant->PredictionFile->hasAny(array('participant_id' => $project_id,'modified'=>$results['Participant']['date']));
            $this->set('date', $results['date']);
//            $this->set('date', date("Ymd"));
            $this->set('isModified', $results['Participant']['isModified']);
            $this->set('title', $project['Project']['title']);
            $this->set('documents', $documents);
            $this->set('falsePositivesWords', $falsePositivesWords);
            $this->set('falseNegativesWords', $falseNegativesWords);

            App::uses('File', 'Utility');
            $CEMPResultsFile = new File("files" . DS . "Results" . DS . "CEMPResults.json", 600);
            $GPROResultsFile = new File("files" . DS . "Results" . DS . "GPROResults.json", 600);
            $CEMPResults = "[]";
            $GPROResults = "[]";
            if ($CEMPResultsFile->exists()) {
                $CEMPResults = $CEMPResultsFile->read();
            }
            if ($GPROResultsFile->exists()) {
                $GPROResults = $GPROResultsFile->read();
            }
            $this->set('CEMPResults', json_decode($CEMPResults, true));
            $this->set('GPROResults', json_decode($GPROResults, true));

            $this->set('analysisResults', true);
            $this->set('results', $results);
        } else {
            $this->Session->setFlash("There is no data to show");
            $this->redirect(array('controller' => 'Participants',
                'action' => 'analysis'));
        }
    }

    public function getProjects() {
        if ($this->request->is(array('post', 'put'))) {
            $email = $this->request->data['email'];
            $code = $this->request->data['code'];

            $conditions = array(
                'email' => $email, 'code' => $code);
            $participant = $this->Participant->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'team_id'),
                'conditions' => $conditions));

            $projects = $this->getParticipantProjects($email, $code);
            return $this->correctResponseJson(json_encode(array('success' => true,
                        'projects' => $projects, 'team_id' => $participant['Participant']['team_id'])));
        }
    }

    public function getUsedRuns() {
        if ($this->request->is(array('post', 'put'))) {
            $email = $this->request->data['email'];
            $code = $this->request->data['code'];
            $task = $this->request->data['task'];
            $conditions = array(
                'email' => $email, 'code' => $code);
            $participant = $this->Participant->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'team_id'),
                'conditions' => $conditions));
            $runs = array();
            if (!empty($participant)) {
                $conditions = array(
                    'FinalPrediction.participant_id' => $participant['Participant']['id'],
                    'FinalPrediction.biocreative_task' => $task
                );
                $runs = $this->Participant->FinalPrediction->find('list', array(
                    'recursive' => -1,
                    'fields' => array('run', 'run'),
                    'conditions' => $conditions
                ));
                return $this->correctResponseJson(json_encode(array('success' => true,
                            'runs' => array_values($runs))));
            }
            return $this->correctResponseJson(json_encode(array('success' => false)));
        }
    }

    private function decrypt($code) {
        $iv = 'p8|NnN8w]g;hlSbK'; #Same as in JAVA
        $key = 'ra3(9wy`sV6dFto{'; #Same as in JAVA
        $output = "";
        $code = $this->hex2bin($code);

        $code = @gzinflate($code);
        if (strlen($code) > 1) {
            $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
            mcrypt_generic_init($td, $key, $iv);
            $decrypted = mdecrypt_generic($td, $code);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $output = utf8_encode(trim($decrypted));
        }
        return $output;
    }

    private function hex2bin($hexdata) {
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

    function postAndRedirect() {
        if ($this->request->is(array('post', 'put'))) {
            $this->Session->write('requestData', $this->request->data);

            return $this->redirect(array('controller' => 'Participants',
                        'action' => $this->request->data['Action']));
        } else {
//            exit();
        }
    }

    function listGoldensHits($typeId,$post = null) {
        $results = $this->Session->read('analysisResults');
//        debug($results);
//        debug($requestData);                
        $this->Participant->Project->Type->id = $typeId;
        if (!$this->Participant->Project->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        }
        if (!empty($results) && !$results['Participant']['isModified']) {
//            if(true){
            $goldenUser = $results['Golden']['user_id'];
            $goldenRound = $results['Golden']['round_id'];
            $participantId = $results['Participant']['id'];
            $goldenType = $typeId;
//            $goldenType = 1;
            $subQueryConditions = array(
                'Golden.round_id' => $goldenRound,
                'Golden.user_id' => $goldenUser,
                'Golden.document_id=UploadedAnnotation.document_id ',
                'Golden.type_id' => $goldenType,
                "Golden.init= UploadedAnnotation.init",
                "Golden.end=UploadedAnnotation.end",
                "Golden.section=UploadedAnnotation.section"
            );
            $this->Participant->contain('UploadedAnnotation', array(
                'Document' => array('title', 'external_id'),
            ));

            $searchQuery = $this->Session->read('data');
            $searchString = $this->Session->read('search');
            $conditions = array(
                'UploadedAnnotation.participant_id' => $participantId
            );
            if ($post == null) {
                $this->Session->delete('data');
                $this->Session->delete('search');
                $this->set('search', '');
            } else if (!empty($searchQuery)) {
                $conditions = array('OR' => $searchQuery,
                    'AND' => array(
                        'UploadedAnnotation.participant_id' => $participantId));
                $this->set('search', $searchString);
            }
            

            $this->Paginator->settings = array(
                'fields' => array(
                    'Golden.annotated_text',
                    'UploadedAnnotation.annotated_text',
                    'UploadedAnnotation.init',
                    'UploadedAnnotation.end',
                    'UploadedAnnotation.document_id',
                    'Golden.init',
                    'Golden.end',
                    'UploadedAnnotation.section',
                    'Golden.section',
                    'Golden.type_id',
//                    'Document.external_id',
//                    'Document.id',
                ),
                'limit' => 50,
                'joins' => array(
                    array(
                        'type' => 'inner',
                        'table' => 'golden_annotations',
                        'alias' => 'Golden',
                        'conditions' => $subQueryConditions
                    ),
                    array(
                        'type' => 'inner',
                        'table' => 'documents',
                        'alias' => 'Document',
                        'conditions' => array('Document.id=Golden.document_id'),
                    )
                ),
                'conditions' => $conditions
            );
            $project_id = $results['project_id'];

            $documentsList = $this->getDocumentsList($project_id);
            $this->set('typeId', $typeId);
            $this->set('types', $this->getTypesList($project_id));
            $this->set('documentsList', $documentsList);
            $this->set('annotations', $this->paginate('UploadedAnnotation'));
            $this->set('project_id', $results['project_id']);
            $this->set('analysisResults', true);
            
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    function search($isPositive = false) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $search = trim($this->request->data[$this->name]['search']);
            $cond = array();
            $cond['Document.external_id  LIKE'] = '%' . addslashes($search) . '%';
            //$cond['Document.html  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('search', $search);
            if (isset($this->request->data[$this->name]['type_id'])) {
                $cond['Golden.annotated_text  LIKE'] = '%' . addslashes($search) . '%';
                $this->Session->write('data', $cond);
                $type_id = $this->request->data[$this->name]['type_id'];
                $this->redirect(array('action' => $this->request->data[$this->name]['from'],
                    $type_id,
                    1));
            } else {
                throw new Exception;
                $cond['UploadedAnnotation.annotated_text  LIKE'] = '%' . addslashes($search) . '%';
                $this->Session->write('data', $cond);
                $this->redirect(array('action' => $this->request->data[$this->name]['from'],
                    1));
            }
        }
    }

    function listFalsePositives($post = null) {
        $results = $this->Session->read('analysisResults');
//        debug($results);
//        debug($requestData);                

        if (!empty($results) && !$results['Participant']['isModified']) {
            $goldenUser = $results['Golden']['user_id'];
            $goldenRound = $results['Golden']['round_id'];
            $participantId = $results['Participant']['id'];
            $falsePositivesIds = $results['Participant']['falsePostivesIds'];

//            $subQueryConditions = array(
//                'Golden.round_id' => $goldenRound,
//                'Golden.user_id' => $goldenUser,
//                'Golden.document_id=UploadedAnnotation.document_id ',
//                "Golden.init= UploadedAnnotation.init",
//                "Golden.end=UploadedAnnotation.end",
//                "Golden.section=UploadedAnnotation.section"
//            );
//            $this->Participant->contain('UploadedAnnotation', array(
//                'Document' => array('title', 'external_id'),
//            ));
//
//            $db = $this->Participant->getDataSource();
//            $subQuery = $db->buildStatement(array(
//                'fields' => array(
//                    'Distinct UploadedAnnotation.id'
//                ),
//                'table' => 'uploaded_annotations',
//                'alias' => 'UploadedAnnotation',
//                'limit' => null,
//                'offset' => null,
//                'joins' => array(
//                    array(
//                        'type' => 'inner',
//                        'table' => 'golden_annotations',
//                        'alias' => 'Golden',
//                        'conditions' => $subQueryConditions
//                    ),
//                ),
//                'conditions' => array("UploadedAnnotation.participant_id" => $participantId),
//                'order' => null,
//                'group' => null
//                    ), $this->Participant);

            $conditions = array(
                'UploadedAnnotation.id' => $falsePositivesIds,
                'UploadedAnnotation.participant_id' => $participantId
            );
            $searchQuery = $this->Session->read('data');
            $searchString = $this->Session->read('search');
            if ($post == null) {
                $this->Session->delete('data');
                $this->Session->delete('search');
                $this->set('search', '');
            } else if (!empty($searchQuery)) {
                $conditions = array('OR' => $searchQuery,
                    'AND' => array(
                        'UploadedAnnotation.id' => $falsePositivesIds,
                        'UploadedAnnotation.participant_id' => $participantId));
                $this->set('search', $searchString);
            }
            $this->Paginator->settings = array(
                'fields' => array(
//                    'Golden.annotated_text',
                    'UploadedAnnotation.annotated_text',
                    'UploadedAnnotation.init',
                    'UploadedAnnotation.end',
//                    'Golden.init',
//                    'Golden.end',
                    'UploadedAnnotation.section',
                    'UploadedAnnotation.document_id',
//                    'Golden.section',
//                    'Document.external_id',
//                    'Document.id',
                ),
                'limit' => 50,
                'joins' => array(
//                    array(
//                        'type' => 'inner',
//                        'table' => 'golden_annotations',
//                        'alias' => 'Golden',
//                        'conditions' => $subQueryConditions
//                    ),
                    array(
                        'type' => 'inner',
                        'table' => 'documents',
                        'alias' => 'Document',
                        'conditions' => array('Document.id=UploadedAnnotation.document_id'),
                    )
                ),
                'conditions' => $conditions
            );
            $project_id = $results['project_id'];
            $documentsList = $this->getDocumentsList($project_id);
            $this->set('documentsList', $documentsList);
            $this->set('annotations', $this->paginate('UploadedAnnotation'));
            $this->set('project_id', $results['project_id']);
            $this->set('analysisResults', true);
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    function listFalseNegatives($type_id = null, $post = null) {
        $results = $this->Session->read('analysisResults');
        $this->Participant->Project->Type->id = $type_id;
        if (!$this->Participant->Project->Type->exists()) {
            throw new NotFoundException(__('Invalid type'));
        }
        if (!empty($results) && !$results['Participant']['isModified']) {
            $goldenUser = $results['Golden']['user_id'];
            $goldenRound = $results['Golden']['round_id'];
            $participantId = $results['Participant']['id'];
            $goldenType = $type_id;
            $falseNegativesIds = $results['Participant']['falseNegativesIds'];
            $conditions = array(
                'GoldenAnnotation.id' => $falseNegativesIds,
                'GoldenAnnotation.user_id' => $goldenUser,
                'GoldenAnnotation.round_id' => $goldenRound,
                'GoldenAnnotation.type_id' => $goldenType,
            );
            $data = $this->Session->read('data');
            $busqueda = $this->Session->read('search');
            if ($post == null) {
                $this->Session->delete('data');
                $this->Session->delete('search');
                $this->set('search', '');
            } else if (!empty($data)) {
                $conditions = array('OR' => $data,
                    'AND' => $conditions);
                $this->set('search', $busqueda);
            }
            $this->Paginator->settings = array(
                'fields' => array(
                    'GoldenAnnotation.annotated_text',
                    'GoldenAnnotation.init',
                    'GoldenAnnotation.end',
                    'GoldenAnnotation.section',
                    'GoldenAnnotation.document_id',
                    'GoldenAnnotation.type_id',
//                    'Document.external_id',
//                    'Document.id',
                ),
                'limit' => 50,
                'joins' => array(
                    array(
                        'type' => 'inner',
                        'table' => 'documents',
                        'alias' => 'Document',
                        'conditions' => array('Document.id=GoldenAnnotation.document_id'),
                    )
                ),
                'conditions' => $conditions
            );
            $project_id = $results['project_id'];
            $documentsList = $this->getDocumentsList($project_id);
            $this->set('types', $this->getTypesList($project_id));
            $this->set('type_id', $type_id);
            $this->set('documentsList', $documentsList);

            $this->set('annotations', $this->paginate('GoldenAnnotation'));
            $this->set('project_id', $results['project_id']);
            $this->set('analysisResults', true);
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    function downloadPredictions() {
        $results = $this->Session->read('analysisResults');

        if (!empty($results) && !$results['Participant']['isModified']) {

            $project_id = $results['project_id'];
            $goldenUser = $results['Golden']['user_id'];
            $goldenRound = $results['Golden']['round_id'];
            $participantId = $results['Participant']['id'];
            $falsePositivesIds = $results['Participant']['falsePostivesIds'];
            $falseNegativesIds = $results['Participant']['falseNegativesIds'];




            $listFalsePositivesSize = sizeof($falsePositivesIds);

            $listFalseNegativesSize = sizeof($falseNegativesIds);




            $types = Cache::read('types-list-project_id-' . $project_id, 'short');
            if (!$types) {
                $types = $this->Participant->Project->Type->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'name'),
                    'conditions' => array(
                        'Type.project_id' => $project_id
                    )
                ));
                Cache::write('types-list-project_id-' . $project_id, $types, 'short');
            }


            $documentsList = $this->getDocumentsList($project_id);


            $downloadPath = Configure::read('downloadFolder');
            $annotationsBuffer = Configure::read('annotationsBuffer');

            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');

            $this->RequestHandler = $this->Components->load('RequestHandler');
            $downloadDir = new Folder($downloadPath, true, 0700);
            if ($downloadDir->create('')) {
                $tempPath = $downloadDir->pwd() . DS . uniqid();
                $tempFolder = new Folder($tempPath, true, 0700);
                if ($tempFolder->create('')) {
                    $tempFolderAbsolutePath = $tempFolder->path . DS;
                    $zip = new ZipArchive;
                    $packetName = "predictionsResults" . ".zip";

                    if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                        throw new Exception("Failed to create archive\n");
                    }


                    $index = 0;
                    $file = new File($tempFolder->pwd() . DS . "falsePositives.tsv", 600);
                    $content = "Document\tSection\tStarting_offset\tEnding_offset\tAnnotation_text\n";
                    while ($index < $listFalsePositivesSize) {
                        $annotations = $this->Participant->UploadedAnnotation->find('all', array(
                            'fields' => array(
                                'annotated_text',
                                'init',
                                'end',
                                'section',
                                'document_id'
                            ),
                            'recursive' => -1,
                            'conditions' => array(
                                'UploadedAnnotation.id' => $falsePositivesIds,
                                'UploadedAnnotation.participant_id' => $participantId
                            ),
                            'limit' => $annotationsBuffer, //int
                            'offset' => $index, //int
                        ));

                        $content.=$this->annotationsToTSV($annotations, $documentsList);
                        $index+=$annotationsBuffer;
                    }
                    if ($file->exists()) {
                        $file->append($content);
                    } else {
                        throw new Exception("Error creating files ");
                    }

                    $content = "";
                    $file->close();
                    $zip->addFile($file->path, ltrim($file->name() . ".tsv", '/'));


                    $index = 0;
                    $file = new File($tempFolder->pwd() . DS . "falseNegatives.tsv", 600);
                    $content = "Document\tSection\tStarting_offset\tEnding_offset\tAnnotation_text\n";
                    while ($index < $listFalseNegativesSize) {
                        $annotations = $this->Participant->GoldenAnnotation->find('all', array(
                            'fields' => array(
                                'annotated_text',
                                'init',
                                'end',
                                'section',
                                'document_id'
                            ),
                            'recursive' => -1,
                            'conditions' => array(
                                'GoldenAnnotation.id' => $falseNegativesIds,
                                'GoldenAnnotation.user_id' => $goldenUser,
                                'GoldenAnnotation.round_id' => $goldenRound,
                            ),
                            'limit' => $annotationsBuffer, //int
                            'offset' => $index, //int
                        ));

                        $content.=$this->annotationsToTSV($annotations, $documentsList);
                        $index+=$annotationsBuffer;
                    }


                    if ($file->exists()) {
                        $file->append($content);
                    } else {
                        throw new Exception("Error creating files ");
                    }


                    $file->close();
                    $zip->addFile($file->path, ltrim($file->name() . ".tsv", '/'));
                    if (!$zip->status == ZIPARCHIVE::ER_OK) {
                        throw new Exception("Error creating zip ");
                    }
                }


                $zip->close();
                $zipFolder = new File($tempFolder->pwd() . DS . $packetName);
                $packet = $zipFolder->read();
                $zipFolder->close();
                if (!$tempFolder->delete()) {
                    throw new Exception("Error delete zip ");
                }
                $mimeExtension = 'application/zip';
                $this->autoRender = false;
                $this->response->type($mimeExtension);
                $this->response->body($packet);
                $this->response->download($packetName);
                return $this->response;
            }
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    public function downloadTopFalsePositives() {

        $results = $this->Session->read("analysisResults");
        if (!empty($results)) {
            $predictions = $results['topFalsePositivesAnnotations'];
            $lines = $this->predictionsTopToTsv($predictions, true);
            $this->exportTsvDocument($lines, "topFalsePositivesPredictions.tsv");
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    public function downloadTopFalseNegatives() {
        $results = $this->Session->read("analysisResults");


        if (!empty($results)) {
            $predictions = $results['topFalseNegativesAnnotations'];
            $lines = $this->predictionsTopToTsv($predictions, false);
            $this->exportTsvDocument($lines, "topFalseNegativesPredictions.tsv");
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    public function compare($document_id = null) {
        $results = $this->Session->read("analysisResults");

        if (!empty($results) && !$results['Participant']['isModified']) {

            $this->AnnotatedDocument = $this->Participant->Project->Round->AnnotatedDocument;
            $this->Round = $this->Participant->Project->Round;


            $this->Participant->Project->Round->id = $results['Golden']['round_id'];
            if (!$this->Participant->Project->Round->exists()) {
                throw new NotFoundException(__('Invalid Golden'));
            } //!$this->UsersRound->exists()




            $types = Cache::read('comparator-types-' . $results['Golden']['round_id'], 'short');
            if (!$types) {
                $types = $this->Round->Type->find('all', array(
                    'recursive' => -1,
//                'contain' => array('Question',),
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
                        'TypesRound.round_id' => array($results['Golden']['round_id'])
                    ),
                    'group' => array('Type.id')
                ));
                Cache::write('comparator-types-' . $results['Golden']['round_id'], $types, 'short');
            }


            $golden_text = $this->AnnotatedDocument->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'AnnotatedDocument.round_id' => $results['Golden']['round_id'],
                    'AnnotatedDocument.user_id' => $results['Golden']['user_id'],
                    'AnnotatedDocument.document_id' => $document_id,
                ),
            ));


            $prediction_text = $this->Participant->PredictionDocument->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'participant_id' => $results['Participant']['id'],
                    'document_id' => $document_id,
                    'project_id' => $results['project_id'],
                ),
            ));


            if (empty($types) || empty($golden_text)) {
                throw new NotFoundException(__('Invalid Golden'));
            }



//            if (strlen(trim($userRound_A['UsersRound']['text_marked'])) == 0 && strlen(trim($userRound_B['UsersRound']['text_marked'])) == 0) {
//                throw new NotFoundException(__('Invalid Golden'));
//            } 
//            $golden_text['UsersRound']['text_marked']=utf8_encode($golden_text['UsersRound']['text_marked']);


            $this->set('golden_text', $golden_text['AnnotatedDocument']['text_marked']);
            if (empty($prediction_text['PredictionDocument']['text_marked'])) {
                $prediction_text['PredictionDocument']['text_marked'] = "";
            }
            $this->set('prediction_text', $prediction_text['PredictionDocument']['text_marked']);
            $this->set('types', $types);
            $this->set('analysisResults', true);
        } else {
            $this->Session->setFlash("There is no data to show");
            if (!empty($results)) {
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'results'));
            }
            return $this->redirect(array('controller' => 'Participants',
                        'action' => 'analysis'));
        }
    }

    private function getDocumentsList($project_id) {
        $documentsList = Cache::read('documents-list-project_id-' . $project_id, 'short');
        if (empty($documentsList)) {
            $documentsList = $this->Participant->Project->Document->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'external_id', 'title'),
                'joins' => array(
                    array(
                        'table' => 'documents_projects',
                        'alias' => 'DocumentsProject',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DocumentsProject.document_id = Document.id',
                        ))
                ),
                'conditions' => array(
                    'DocumentsProject.project_id' => $project_id,
                ),
                'order' => array('document_id Asc')
            ));

            $documentsListCopy = array();
            $size = count($documentsList);
            for ($index = 0; $index < $size; $index++) {
                $id = $documentsList[$index]['Document']['id'];
                $title = $documentsList[$index]['Document']['title'];
                $external_id = $documentsList[$index]['Document']['external_id'];
                $documentsListCopy[$id] = array('title' => $title, 'external_id' => $external_id);
            }
            $documentsList = $documentsListCopy;
            Cache::write('documents-list-project_id-' . $project_id, $documentsList, 'short');
        }

        return $documentsList;
    }

    private function getTypesList($project_id) {
        $types = Cache::read('types-all-project_id-' . $project_id, 'short');
        if (empty($types)) {
            $types = $this->Participant->Project->Type->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'name', 'colour'),
                'conditions' => array(
                    'Type.project_id' => $project_id
                )
            ));
            $copyTypes = array();
            $size = count($types);
            for ($index = 0; $index < $size; $index++) {
                $id = $types[$index]['Type']['id'];
                $name = $types[$index]['Type']['name'];
                $colour = $types[$index]['Type']['colour'];
                $copyTypes[$id] = array('name' => $name, 'colour' => $colour);
            }
            $types = $copyTypes;
            Cache::write('types-list-project_id-' . $project_id, $types, 'short');
        }
        return $types;
    }

    private function annotationsToTSV($annotations, $documentsList) {
        $content = "";
        $size = count($annotations);
        for ($i = 0; $i < $size; $i++) {
            if (isset($annotations[$i]['GoldenAnnotation'])) {
                $annotation = $annotations[$i]['GoldenAnnotation'];
            } else if (isset($annotations[$i]['UploadedAnnotation'])) {
                $annotation = $annotations[$i]['UploadedAnnotation'];
            } else {
                throw new Exception;
            }
            $document = $documentsList[$annotation['document_id']];
            $content .= $document['external_id'] . "\t";

            if ($annotation['section'] == 'A') {
                $titleSize = strlen($document['title']);
                $annotation['init'] -=$titleSize;
                $annotation['end']-=$titleSize;
            }
            $content.=$annotation['section'] . "\t";
            $content.=
                    $annotation['init'] . "\t" .
                    $annotation['end'] . "\t" .
                    $annotation['annotated_text'] . "\n";
        }

        return $content;
    }

    private function predictionsTopToTsv($predictions = array(), $falsePositives = false) {
        $iDs = array_keys($predictions);

        if ($falsePositives) {
            $words = $this->Participant->UploadedAnnotation->find('list', array(
                'recursive' => -1,
                'fields' => array('annotated_text'),
                'conditions' => array('id' => $iDs)));
        } else {
            $words = $this->Participant->GoldenAnnotation->find('list', array(
                'recursive' => -1,
                'fields' => array('annotated_text'),
                'conditions' => array('id' => $iDs)));
        }

        $lines = array();
        $cont = 0;
        foreach ($predictions as $key => $value) {
            $lines[$cont] = $words[$key] . "\t" . $value;
            $cont++;
            if ($cont > 9) {
                break;
            }
        }
        return $lines;
    }

    private function getParticipantProjects($email, $code, $project_id = null) {
        $conditions = array(
            'email' => $email, 'code' => $code);
        $participant = $this->Participant->find('first', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => $conditions));
        $projects = array();
        if (!empty($participant)) {

            $conditions = array('ProjectsParticipants.participant_id' => $participant['Participant']['id']);
            if (isset($project_id)) {
                $conditions = array(
                    'ProjectsParticipants.participant_id' => $participant['Participant']['id'],
                    'ProjectsParticipants.project_id' => $project_id,
                );
            }
            $projects = $this->Participant->Project->find('list', array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'projects_participants',
                        'alias' => 'ProjectsParticipants',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Project.id = ProjectsParticipants.project_id'
                        )
                    ),
                    array(
                        'table' => 'golden_projects',
                        'alias' => 'GoldenProjects',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Project.id = GoldenProjects.project_id'
                        )
                    ),
                ),
                'fields' => array('Project.id', 'Project.title'),
                'conditions' => $conditions
            ));
        }
        return $projects;
    }

    private function participantSaveConnection($request, $task) {
        App::uses('CakeTime', 'Utility');
        $connectionLog = Configure::read('connectionLog');
        if ($connectionLog) {
            $connectionLogProxy = Configure::read('connectionLogProxy');
            if ($connectionLogProxy && isset($request['connection-details'])) {
                $details = $request['connection-details'];
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
                'participant_id' => $request['id'],
                'ip' => $ip,
                'city' => $details['city'] . " ($region) ",
                'country' => $details['country'],
                'task' => $task,
            );
            $this->Participant->UploadLog->create();
            $this->Participant->UploadLog->save($data);
        }
    }

    function participantShowConnections() {
        $this->Participant->UploadLog->recursive = 0;
        $this->paginate = array(
            'order' => 'created DESC',
            'contain' => array('Participant'),
            'limit' => 50,
        );
        $this->set('connections', $this->Paginator->paginate($this->Participant->UploadLog));
    }

    function dowloadParticipantsUploadsLog() {

        $group_id = $this->Session->read('group_id');
        if ($group_id > 1 || !isset($group_id)) {
            throw new Exception;
        }
        $finalPredictions = $this->Participant->FinalPrediction->find('all', array(
//            'recursive' => -1,
            'fields' => array('Participant.team_id', 'run', 'biocreative_task', 'created',
                'modified'),
            'contain' => array('Participant' => array('team_id')),
            'order' => array('Participant.team_id' => 'DESC', 'biocreative_task' => 'DESC',
                'run' => 'ASC')
        ));
        $lines = array();
        array_push($lines, "team_id\tbiocreative_task\trun\tcreated\tmodified");
        foreach ($finalPredictions as $finalPrediction) {

            $team_id = $finalPrediction['Participant']['team_id'];
            $biocreative_task = $finalPrediction['FinalPrediction']['biocreative_task'];
            $run = $finalPrediction['FinalPrediction']['run'];
            $created = $finalPrediction['FinalPrediction']['created'];
            $modified = $finalPrediction['FinalPrediction']['modified'];
            array_push($lines, "$team_id\t$biocreative_task\t$run\t$created\t$modified");
        }

        return $this->exportTsvDocument($lines, "partcicipantsUploads.tsv");
    }

    function uploadFinalPredictions() {
        $this->Cookie = $this->Components->load('Cookie');
        $this->Cookie->type('rijndael');

//        $this->Cookie->time = '999 hours';

        if ($this->request->is(array('post', 'put'))) {
//            debug($this->request->data);
            $email = $this->request->data['Participant']['email'];
            $code = $this->request->data['Participant']['code'];
            $task = $this->request->data['Participant']["task"];
            $run = $this->request->data['Participant']["run"];

            $maxUploadTask = Configure::read('max_participant_task_upload');
            $tasks = Configure::read('biocreative_tasks');
            $finalDate = Configure::read('final_date_to_upload_tasks');
            $startDate = Configure::read('initial_date_to_upload_tasks');

            if (!in_array($task, $tasks) && $run < 0 && $run > 5) {
                $this->Session->setFlash("Incorrect data");
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'analysis'));
            }

            App::uses('CakeTime', 'Utility');
            $isEnd = CakeTime::isPast($finalDate);
            $isFuture = CakeTime::isFuture($startDate);
            if ($isEnd || $isFuture) {
                $this->Session->setFlash("The final delivery date has expired");
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'analysis'));
            }

            $project_id = -1;
            if ($this->data['Participant']['remember_me'] && isset($code) && isset($email)) {
                $cookie = array();
                $cookie['email'] = $email;
                $cookie['code'] = $code;

                $this->Cookie->write('participantData', $cookie, true, '+2 weeks');
            } else {
                if ($this->Cookie->check('participantData')) {
                    $this->Cookie->destroy('participantData');
                }
            }
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
//            debug($this->request->data);
            if ($this->request->data['Participant']['final_prediction']['size'] > 0) {

                /* ============================================= */
                /* ==============Load analysis=================== */
                /* ============================================= */
                if ($this->request->data['Participant']['final_prediction']['size'] > $this->filesize2bytes(Configure::read('max_file_size'))) {
                    $this->Session->setFlash("The file can not be more than " . Configure::read('max_file_size'));
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }

                $file = $this->request->data['Participant']['final_prediction']['name'];
                if (pathinfo($file, PATHINFO_EXTENSION) != 'tsv') {
                    $this->Session->setFlash("The file must be in TSV format");
                    return $this->redirect(array('controller' => 'Participants',
                                'action' => 'analysis'));
                }

                $file = new File($this->request->data['Participant']['final_prediction']['tmp_name']);
                if ($file->readable()) {
                    $content = $file->read();
                    $file->close();

                    $lines = explode("\n", $content);

                    $incorrectFormat = empty($lines);
                    $count = 0;
                    $size = count($lines);
                    $correctColumns = 5;
                    if ($task == "CPD") {
                        $correctColumns = 4;
                    }


                    for ($index = 0; $index < $size; $index++) {
                        if (strlen(trim($lines[$index])) > 0) {
                            if (!$incorrectFormat) {
                                $columns = explode("\t", $lines[$index]);
                                for ($i = 0; $i < count($columns); $i++) {
                                    if (strlen(trim($columns[$i])) == 0) {
                                        $incorrectFormat = true;
                                    }
                                }
                                $incorrectFormat = $incorrectFormat || sizeof($columns) != $correctColumns;
                                $count++;
                            } else {
                                break;
                            }
                        }
                    }
//                    $correctFormat = $this->correctTsvFormat($file, 5);
                    if ($incorrectFormat) {

//                        $count=$this->incorrecLineTsvFormat($file);
                        if ($task == "CPD") {
                            $this->Session->setFlash("Incorrect content file. Line $count is incorrect. "
                                    . "Content file must be contain 4 columns");
                        } else {
                            $this->Session->setFlash("Incorrect content file. Line $count is incorrect. "
                                    . "Content file must be in this format WO2009026621A1->A:12:24->1->0.99->paliperidone");
                        }
                        return $this->redirect(array('controller' => 'Participants',
                                    'action' => 'analysis'));
                    }


                    $participant = $this->Participant->find('first', array(
                        "recursive" => -1,
                        "fields" => array("id", "team_id"),
                        "conditions" => array(
                            'Participant.email' => $email,
                            'Participant.code' => $code,
                        ),
                    ));

                    $participantID = $participant["Participant"]["id"];
                    $team_id = $participant["Participant"]["team_id"];


                    $this->request->data['Participant']['id'] = $participantID;
                    $this->participantSaveConnection($this->request->data['Participant'], "uploadTeamPrediction");


                    $path = Configure::read('participantsPath');

                    $path = $path . DS . $task . DS . $team_id;
                    $dir = new Folder($path, true, 0755);


                    $tempPath = $this->request->data['Participant']['final_prediction']['tmp_name'];
                    $file = new File($tempPath);
                    if ($file->readable()) {
                        $content = $file->read();
                        $newPath = $dir->pwd() . DS . "$run.tsv";
                        $file->copy($newPath);
                        $path = $newPath;
                        chmod($newPath, 0200);

                        $data = array(
                            'participant_id' => $participantID,
                            'biocreative_task' => $task,
                            'run' => $run
                        );

                        $finalfile = $this->Participant->FinalPrediction->find('first', array(
                            "conditions" => $data,
                            'fields' => 'id'));


                        if (!empty($finalfile)) {
                            $this->Participant->FinalPrediction->id = $finalfile['FinalPrediction']['id'];
                            $this->Session->setFlash("The file has been updated successfully", "success");
                            $type = "updated";
                        } else {
                            $this->Participant->FinalPrediction->create();
                            $this->Session->setFlash("The file has been created successfully", "success");
                            $type = "uploaded";
                        }

                        $data = array(
                            'participant_id' => $participantID,
                            'run' => $run,
                            'email' => $email,
                            'biocreative_task' => $task,
                            'file' => $content,
                            'file_name' => $this->request->data['Participant']['final_prediction']['name'],
                        );

                        if (!$this->Participant->FinalPrediction->save($data)) {
                            $this->Session->setFlash("File could not be saved");
                            return $this->redirect(array('controller' => 'Participants',
                                        'action' => 'analysis'));
                        } else {
                            $this->sendMailWithAttachment("finalPredictionSuccess", $email, "[MARKYT - BIOCREATIVE V (CHEMDNER)] Final submision", array(
                                "run" => $run, "type" => $type, "task" => $task), array(
                                "$run.tsv" => $tempPath));
                        }

                        return $this->redirect(array('controller' => 'Participants',
                                    'action' => 'analysis'));
                    }
                } else {
                    throw new Exception("Ops! file could not be readed");
                }
            } else {
                $this->Session->setFlash("One team prediction file is needed");
                return $this->redirect(array('controller' => 'Participants',
                            'action' => 'analysis'));
            }
        }
    }

    public function deleteLog() {
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $redirect = array('controller' => 'participants', 'action' => 'participantShowConnections');
            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $conditions = array('1' => 1);
                if ($this->Participant->UploadLog->UpdateAll(array('title' => '\'Removing...\''), $conditions, -1)) {
                    $this->Session->setFlash(__('All connections are being deleted. Please be patient'), 'information');
                    $this->backGround($redirect);
                    $this->Participant->UploadLog->deleteAll($conditions, $deleteCascade);
                }
            } else {
                if ($this->Participant->UploadLog->deleteAll(array('1' => 1), $deleteCascade)) {
                    $this->Session->setFlash(__('All Connections  have been deleted'), 'success');
                    $this->redirect($redirect);
                }
                $this->Session->setFlash(__("Connections haven't been deleted"));
                $this->redirect($redirect);
            }
        }
    }

    function downloadFinalPrediction($participant_id) {
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1 || !isset($group_id)) {
            throw new Exception;
        }

        $participant = $this->Participant->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'team_id'),
            'conditions' => array('id' => $participant_id)));
        $finalPredictions = array();


        if (empty($participant)) {
            throw new NotFoundException(__('Participant not found'));
        } else {
            $conditions = array(
                'FinalPrediction.participant_id' => $participant['Participant']['id'],
            );
            $finalPredictions = $this->Participant->FinalPrediction->find('all', array(
                'recursive' => -1,
                'fields' => array('run', 'biocreative_task', 'file'),
                'conditions' => $conditions
            ));

            if (!empty($finalPredictions)) {
                $team_id = $participant['Participant']['team_id'];
                App::uses('Folder', 'Utility');
                App::uses('File', 'Utility');
                $downloadPath = Configure::read('downloadFolder');
                $this->RequestHandler = $this->Components->load('RequestHandler');
                $downloadDir = new Folder($downloadPath, true, 0700);
                if ($downloadDir->create('')) {
                    $tempPath = $downloadDir->pwd() . DS . $team_id;
                    $tempFolder = new Folder($tempPath, true, 0700);
                    if ($tempFolder->create('')) {
                        $tempFolderAbsolutePath = $tempFolder->path . DS;
                        $zip = new ZipArchive;
                        $packetName = "team_" . $team_id . ".zip";

                        if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                            throw new Exception("Failed to create archive\n");
                        }


                        for ($index = 0; $index < count($finalPredictions); $index++) {
                            $finalPrediction = $finalPredictions[$index];
                            $task = $finalPrediction['FinalPrediction']['biocreative_task'];
                            $run = $finalPrediction['FinalPrediction']['run'];
                            $content = $finalPrediction['FinalPrediction']['file'];
                            $taskFolder = new Folder($tempPath . DS . $task, true, 0700);
                            $file = new File($taskFolder->pwd() . DS . "run$run.tsv", 600);
                            if ($file->exists()) {
                                $file->append($content);
                            } else {
                                throw new Exception("Error creating files ");
                            }
                            $file->close();
                            $zip->addFile($file->path, ltrim($task . DS . $file->name() . ".tsv", '/'));
                            if (!$zip->status == ZIPARCHIVE::ER_OK) {
                                throw new Exception("Error creating zip ZIPARCHIVE::ER" . $zip->status);
                            }
                        }
                    }


                    $zip->close();
                    $zipFolder = new File($tempFolder->pwd() . DS . $packetName);
                    $packet = $zipFolder->read();
                    $zipFolder->close();
                    if (!$tempFolder->delete()) {
                        throw new Exception("Error delete zip ");
                    }
                    $mimeExtension = 'application/zip';
                    $this->autoRender = false;
                    $this->response->type($mimeExtension);
                    $this->response->body($packet);
                    $this->response->download($packetName);
                    return $this->response;
                }
            } else {
                $mimeExtension = 'text/plain';
                $this->autoRender = false;
                $this->response->type($mimeExtension);
                $this->response->body("This team has not uploaded any prediction");
                $this->response->download("Readme.txt");
                return $this->response;
            }
        }
    }

    function downloadAllFinalPredictions() {
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1 || !isset($group_id)) {
            throw new Exception;
        }

//        $this->Participant->Project->id = $project_id;
//        if (!$this->Participant->Project->exists()) {
//            throw new NotFoundException(__('Invalid project'));
//        } else {
//            $conditions = array(
//                'ProjectsParticipants.project_id' => $project_id,
//            );

        $participants = $this->Participant->find('list', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects_participants',
                    'alias' => 'ProjectsParticipants',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Participant.id = ProjectsParticipants.participant_id',
//                            'ProjectsParticipants.project_id' => $project_id,
                    )
                ),
            ),
            'fields' => array('Participant.id', 'Participant.team_id'),
            'group' => array('Participant.id'),
//                'conditions' => $conditions
        ));


        if (empty($participants)) {
            $mimeExtension = 'text/plain';
            $this->autoRender = false;
            $this->response->type($mimeExtension);
            $this->response->body("There have not predictions uploaded");
            $this->response->download("Readme.txt");
            return $this->response;
        } else {
            $conditions = array(
                'FinalPrediction.participant_id' => array_keys($participants),
            );
            $finalPredictions = $this->Participant->FinalPrediction->find('all', array(
                'recursive' => -1,
                'fields' => array('run', 'biocreative_task', 'file', 'participant_id'),
                'conditions' => $conditions
            ));


            if (!empty($finalPredictions)) {
                App::uses('Folder', 'Utility');
                App::uses('File', 'Utility');
                $downloadPath = Configure::read('downloadFolder');
                $this->RequestHandler = $this->Components->load('RequestHandler');
                $downloadDir = new Folder($downloadPath, true, 0700);
                if ($downloadDir->create('')) {
                    $tempFolderAbsolutePath = $downloadDir->path . DS . uniqid();
                    $tempFolder = new Folder($tempFolderAbsolutePath, true, 0700);

                    if ($tempFolder->create('')) {
                        $zip = new ZipArchive;
                        $packetName = "finalPredictions.zip";

                        if (!$zip->open($tempFolder->pwd() . DS . $packetName, ZipArchive::CREATE)) {

                            throw new Exception("Failed to create archive\n");
                        }
                        for ($index = 0; $index < count($finalPredictions); $index++) {
                            $finalPrediction = $finalPredictions[$index];
                            $task = $finalPrediction['FinalPrediction']['biocreative_task'];
                            $run = $finalPrediction['FinalPrediction']['run'];
                            $content = $finalPrediction['FinalPrediction']['file'];
                            $participant_id = $finalPrediction['FinalPrediction']['participant_id'];
                            $team_id = $participants[$participant_id];


                            $tempPath = $tempFolder->pwd() . DS . $task;
                            $tempTeamFolder = new Folder($tempPath, true, 0700);
                            $taskFolder = new Folder($tempPath . DS . $team_id, true, 0700);
                            $file = new File($taskFolder->pwd() . DS . "run$run.tsv", 600);
                            if ($file->exists()) {
                                $file->append($content);
                            } else {
                                throw new Exception("Error creating files ");
                            }
                            $file->close();
                            $zip->addFile($file->path, ltrim($task . DS . "team_" . $team_id . DS . $file->name() . ".tsv", '/'));
                            if (!$zip->status == ZIPARCHIVE::ER_OK) {
                                throw new Exception("Error creating zip ZIPARCHIVE::ER" . $zip->status);
                            }
                        }
                    } else {
                        throw new Exception("Error creating temp folder");
                    }


                    $zip->close();
                    $zipFolder = new File($tempFolder->pwd() . DS . $packetName);
                    $packet = $zipFolder->read();
                    $zipFolder->close();
                    if (!$tempFolder->delete()) {
                        throw new Exception("Error delete zip ");
                    }
                    $mimeExtension = 'application/zip';
                    $this->autoRender = false;
                    $this->response->type($mimeExtension);
                    $this->response->body($packet);
                    $this->response->download($packetName);
                    return $this->response;
                }
            } else {
                $mimeExtension = 'text/plain';
                $this->autoRender = false;
                $this->response->type($mimeExtension);
                $this->response->body("There have not predictions uploaded");
                $this->response->download("Readme.txt");
                return $this->response;
            }
        }
    }

    function evaluationResults() {
        
    }

//    }
}
