<?php

App::uses('AppController', 'Controller');

/**
 * AnnotationsQuestions Controller
 *
 * @property AnnotationsQuestion $AnnotationsQuestion
 */
class AnnotationsQuestionsController extends AppController {

    /**
     * add method
     *
     * @return void
     */
    public function ajaxAdd() {
        //no mostrar vista
        $this->autoRender = false;
        $isEnd = $this->Session->read('isEnd');
        if (!$isEnd) {
            if ($this->request->is('post')) {
                //variable que contiene User.round.Document.user_round_id
                $triada = $this->Session->read('triada');
                $isTriada = $this->request->data['user_id'] . $this->request->data['round_id'] . $this->request->data['document_id'] . $this->request->data['user_round_id'];
                //si el id del usuario se ha modificado, se trata de un hacker o un chistoso
                //por ello abortamos la ejecucion
                if ($triada != $isTriada) {
                    print_r('ErrorAbort');
                    exit();
                }
                
                
                $answers = $this->request->data['answers'];
                $newAnnotation = array(
                    'annotated_text' => $this->request->data['text'],
                    'document_id' => $this->request->data['document_id'],
                    'users_round_id' => $this->request->data['user_round_id'],
                    'type_id' => $this->request->data['type_id'],
                    'round_id' => $this->request->data['round_id'],
                    'user_id' => $this->request->data['user_id']
                );
                $actualNumQuestions = $this->AnnotationsQuestion->Question->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "Question.type_id" => $this->request->data['type_id']
                    )
                ));

                if ($this->request->data['numberOfQuestions'] != $actualNumQuestions) {
                    print_r("alert questions");
                    //se produce cuando el numero de preguntas es distinto al numero de respuestas
                    //generalmente cuando se borra o se crea una pregunta mientras estas en la ejecucion de un round
                } else {
                    //si se produce un error en alguna parte se producira un reload de la pagina y la anotacion sera borada al no tener
                    //inicio ni final
                    $this->AnnotationsQuestion->Annotation->save($newAnnotation);
                    if ($answers != 'empty') {
                        $answers = str_replace('#M#', $this->AnnotationsQuestion->Annotation->id, $answers);
                        //substituimos todos los identificadores de Id_annotation del patron introducido
                        //al id_annotation real,antes de json_decode
                        $answers = json_decode($answers, true);
                        if (!empty($answers) && !$this->AnnotationsQuestion->saveAll($answers)) {
                            Print("ErrorSave. Because it has one error in Answer save  annotation number: ");
                        }
                    }
                    print($this->AnnotationsQuestion->Annotation->id);
                    //devolvemos el id de la nueva anotacion
                }
            } else {
                print("Error Post Marky");
            }
        }
    }

    /**
     * edit method
     *
     * @return void
     */
    public function ajaxEdit() {

            //no mostrar vista
        $this->autoRender = false;
        $group_id = $this->Session->read('group_id');
        if ($group_id > 1) {
            //variable que contiene User.round.Document.user_round_id
            $triada = $this->Session->read('triada');
            $isTriada = $this->request->data['user_id'] . $this->request->data['round_id'] . $this->request->data['document_id'] . $this->request->data['user_round_id'];
            //si el id del usuario se ha modificado, se trata de un hacker o un chistoso
            //por ello abortamos la ejecucion
            
            if ($triada != $isTriada) {
                print_r('ErrorAbort');
                exit();
            }
        }

        //marca que indica que queremos ver la informacion de una annotacion es decir
        //queremos ver las resopuestas
        if ($this->request->data['mode'] == "view") {
            $idQuestions = $this->request->data['questions'];
            $idAnnotation = $this->request->data['annotation_id'];
            $AnnotationsQuestion = $this->AnnotationsQuestion->find('all', array(
                'recursive' => -1,
                'order' => 'AnnotationsQuestion.question_id ASC',
                'conditions' => array(
                    "AnnotationsQuestion.annotation_id" => $idAnnotation,
                    "AnnotationsQuestion.question_id" => $idQuestions
                )
            ));
            //si la anotacion no existe devolvemos un error
            $this->AnnotationsQuestion->Annotation->id = $idAnnotation;
            if (!$this->AnnotationsQuestion->Annotation->exists()) {
                print("ErrorGetMarky anotation exist: " . $idAnnotation);
            } else {
                if (!empty($idQuestions[0])) {
                    $AnnotationsQuestion = $this->AnnotationsQuestion->find('all', array(
                        'recursive' => -1,
                        'order' => 'AnnotationsQuestion.question_id ASC',
                        'conditions' => array(
                            "AnnotationsQuestion.annotation_id" => $idAnnotation,
                            "AnnotationsQuestion.question_id" => $idQuestions
                        )
                    ));
                    if (!empty($AnnotationsQuestion))
                        print(json_encode($AnnotationsQuestion));
                    //else
                    //print("ErrorGetMarky AnnotationsQuestion don't exist");
                }
            }
        } else {
            $isEnd = $this->Session->read('isEnd');
            if (!$isEnd) {
                $answers = $this->request->data['answers'];
                $idAnnotation = $this->request->data['annotation_id'];
                $cond = array(
                    'Annotation.id' => $idAnnotation
                );
                $annotation = $this->AnnotationsQuestion->Annotation->find('first', array(
                    'recursive' => -1,
                    'conditions' => $cond
                ));

                $cond = array(
                    'Question.type_id' => $annotation['Annotation']['type_id']
                );
                $question = $this->AnnotationsQuestion->Question->find('count', array(
                    'recursive' => -1,
                    'conditions' => $cond
                ));
                
                //ALERT el array se ha convertido a stdClass  con str_replace
                $answers = str_replace("#M#", $idAnnotation, $answers);
                //substituimos todos los identificadores de Id_annotation del patron introducido
                //al id_annotation real,antes de json_decode
                //print_r($answers);
                $answers = json_decode($answers, true);
                //este metodo a diferencia de addesta hecho asi dado que no se sabe que anotaciones estan creadas
                foreach ($answers as $answer):
                    // $this->AnnotationsQuestion->id = $cond=array('Question.type_id' => $type['Type']['id']);
                    $cond = array(
                        'AnnotationsQuestion.question_id' => $answer['AnnotationsQuestion']['question_id'],
                        'AnnotationsQuestion.annotation_id' => $answer['AnnotationsQuestion']['annotation_id']
                    );
                    $question = $this->AnnotationsQuestion->find('first', array(
                        'recursive' => -1,
                        'conditions' => $cond
                    ));
                    $this->AnnotationsQuestion->id = $question['AnnotationsQuestion']['id'];
                    if (!$this->AnnotationsQuestion->save($answer)) {
                        Print("ErrorSaveMarky");
                    }
                endforeach;

                //}
            }
        }
    }

    /**
     * questionsAnswersView method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    function questionsAnswersView($id = null) {
        $this->AnnotationsQuestion->Annotation->id = $id;
        if ($this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $data = $this->Session->read('data');
            $this->AnnotationsQuestion->Annotation->id = $id;
            if ($this->AnnotationsQuestion->Annotation->exists()) {

                $user_id = $this->Session->read('user_id');
                $group_id = $this->Session->read('group_id');

                $annotation = $this->AnnotationsQuestion->Annotation->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Annotation.id' => $id
                    )
                ));
                if ($group_id != 1 && $annotation['Annotation']['user_id'] != $user_id) {
                    $this->Session->setFlash(__('Do not try to spy on the responses of other scorers. Not a good practice'));
                    $redirect = $this->Session->read('redirect');
                    $this->redirect($redirect);
                }

                $AnnotationsQuestions = $this->AnnotationsQuestion->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'AnnotationsQuestion.annotation_id' => $id
                    )
                ));
                $question_id = array();
                $question_answer = array();
                foreach ($AnnotationsQuestions as $AnnotationsQuestion) {
                    array_push($question_id, $AnnotationsQuestion['AnnotationsQuestion']['question_id']);
                    $question_answer[$AnnotationsQuestion['AnnotationsQuestion']['question_id']] = $AnnotationsQuestion['AnnotationsQuestion']['answer'];
                }
                $questions = $this->AnnotationsQuestion->Question->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Question.id' => $question_id
                    )
                ));
                $this->set('questions', $questions);
                $this->set('question_answer', $question_answer);
                $this->set('annotation', $annotation);
                $this->set('project_id', $annotation);
                $this->set('project_id', $data['Project']['id']);
            } else {
                $this->Session->setFlash(__('This annotation does not exist or it was deleted'));
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            }
        }
    }

    public function ajaxMultiAdd() {
        //no mostrar vista
        $this->autoRender = false;
        $isEnd = $this->Session->read('isEnd');
        $commit = true;
        $annotationIds = array();
        if (!$isEnd) {
            if ($this->request->is('post')) {
                //variable que contiene User.round.Document.user_round_id
                $triada = $this->Session->read('triada');
                $isTriada = $this->request->data['user_id'] . $this->request->data['round_id'] . $this->request->data['document_id'] . $this->request->data['user_round_id'];
                //si el id del usuario se ha modificado, se trata de un hacker o un chistoso
                //por ello abortamos la ejecucion
                if ($triada != $isTriada) {
                    print_r('ErrorAbort');
                    exit();
                }
                $answers = $this->request->data['answers'];
                $newAnnotation = array(
                    'annotated_text' => $this->request->data['text'],
                    'document_id' => $this->request->data['document_id'],
                    'users_round_id' => $this->request->data['user_round_id'],
                    'type_id' => $this->request->data['type_id'],
                    'round_id' => $this->request->data['round_id'],
                    'user_id' => $this->request->data['user_id']
                );
                $actualNumQuestions = $this->AnnotationsQuestion->Question->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "Question.type_id" => $this->request->data['type_id']
                    )
                ));

                if ($this->request->data['numberOfQuestions'] != $actualNumQuestions) {
                    print_r("alert questions");
                    //se produce cuando el numero de preguntas es distinto al numero de respuestas
                    //generalmente cuando se borra o se crea una pregunta mientras estas en la ejecucion de un round
                } else {

                    $db = $this->AnnotationsQuestion->getDataSource();
                    $db->begin();
                    //si se produce un error en alguna parte se producira un reload de la pagina y la anotacion sera borada al no tener
                    //inicio ni final
                    $numberOfAnnotations = $this->request->data['numberOfAnnotations'];
                    for ($index = 0; $index < $numberOfAnnotations; $index++) {
                        $this->AnnotationsQuestion->Annotation->create();
                        $commit = $commit & $this->AnnotationsQuestion->Annotation->save($newAnnotation);
                        array_push($annotationIds, $this->AnnotationsQuestion->Annotation->getLastInsertID());
                    }

                    if ($answers != 'empty') {
                        foreach ($annotationIds as $id) {
                            $answersCopy = str_replace('#M#', $id, $answers);
                            $answersCopy = json_decode($answersCopy, true);
                            //substituimos todos los identificadores de Id_annotation del patron introducido
                            //al id_annotation real,antes de json_decode
                            $commit = $commit & $this->AnnotationsQuestion->saveAll($answersCopy);
                            //throw new Exception;
                            if (!empty($answers) && !$commit) {
                                Print("ErrorSave. Because it has one error in Answer save  annotation number: ");
                                break;
                            }
                        }
                    }

                    if ($commit) {
                        print(json_encode($annotationIds));
                        $db->commit();
                    } else {
                        $db->rollback();
                    }
                    //devolvemos el id de la nueva anotacion
                }
            } else {
                print("Error Post Marky");
            }
        }
    }

}
