<?php

App::uses('AppController', 'Controller');

/**
 * UsersRounds Controller
 *
 * @property UsersRound $UsersRound
 */
class UsersRoundsController extends AppController {

    var $helpers = array('Time');

    /**
     * index method
     * @param boolean $id
     * @return void
     */
    public function index() {
        //borarmos esta variable de session para que se actualicen los satos del round
        $this->Session->delete('data');
        $this->Session->read('data');
        $this->UsersRound->recursive = -1;
        $this->UsersRound->contain = array(
            'Project'
        );
        $this->paginate = array(
            'fields' => array(
                'UsersRound.id',
                'UsersRound.user_id',
                'UsersRound.round_id',
                'UsersRound.document_id',
                'UsersRound.text_marked',
                'UsersRound.created',
                'UsersRound.modified',
                'Round.title',
                'Round.id',
                'Round.ends_in_date',
                'Project.title',
                'Project.id'
            ),
            'joins' => array(
                array(
                    'type' => 'INNER',
                    'table' => 'rounds',
                    'alias' => 'Round',
                    'conditions' => 'Round.title!="Removing..."  AND Round.ends_in_date IS NOT NULL AND `UsersRound`.`round_id` = `Round`.`id`'
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'projects',
                    'alias' => 'Project',
                    'foreignKey' => 'id',
                    'conditions' => 'Project.id = Round.project_id'
                )
            ),
            'group' => array(
                'UsersRound.round_id'
            )
        );
        $user_id = $this->Session->read('user_id');
        $cond = array(
            'UsersRound.user_id' => $user_id
        );

        $this->set('usersRounds', $this->paginate($cond));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->UsersRound->create();
            if ($this->UsersRound->save($this->request->data)) {
                $this->Session->setFlash(__('The users round has been saved'));
                $this->redirect(array(
                    'action' => 'index'
                ));
            } //$this->UsersRound->save($this->request->data)
            else {
                $this->Session->setFlash(__('The users round could not be saved. Please, try again.'));
            }
        } //$this->request->is('post')
        $users = $this->UsersRound->User->find('list');
        $rounds = $this->UsersRound->Round->find('list');
        $documents = $this->UsersRound->Document->find('list');
        $this->set(compact('users', 'rounds', 'documents'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid users round'));
        } //!$this->UsersRound->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->UsersRound->save($this->request->data)) {
                $this->Session->setFlash(__('The users round has been saved'));
                $this->redirect(array(
                    'action' => 'index'
                ));
            } //$this->UsersRound->save($this->request->data)
            else {
                $this->Session->setFlash(__('The users round could not be saved. Please, try again.'));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->request->data = $this->UsersRound->read(null, $id);
        }
        $users = $this->UsersRound->User->find('list');
        $rounds = $this->UsersRound->Round->find('list');
        $documents = $this->UsersRound->Document->find('list');
        $this->set(compact('users', 'rounds', 'documents'));
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
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid users round'));
        } //!$this->UsersRound->exists()
        if ($this->UsersRound->delete()) {
            $this->Session->setFlash(__('Users round deleted'));
            $this->redirect(array(
                'action' => 'index'
            ));
        } //$this->UsersRound->delete()
        $this->Session->setFlash(__('Users round was not deleted'));
        $this->redirect(array(
            'action' => 'index'
        ));
    }

    /**
     * start method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function start($id = null) {
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->request->is('post') || !$this->request->is('put')) {
            $cond = array(
                'UsersRound.id' => $this->UsersRound->id
            );
            $this->request->data = $this->UsersRound->find('first', array(
                'recursive' => -1,
                'conditions' => $cond
            ));
            //eliminamos todas las anotaciones que no hayan sido guardadas por si se hiciera un reload sin save
            $UsersRound = $this->request->data;
            $data = $this->Session->read('data');
            if (!empty($data) && $data['roundId'] != $UsersRound['UsersRound']['round_id']) {
                $this->Session->delete('data');
                $data = null;
            }
            if (empty($data)) {
                $data = array();
                $cond = array(
                    'TypesRound.Round_id' => $UsersRound['UsersRound']['round_id']
                );
                $typesId = $this->UsersRound->Round->TypesRound->find('all', array(
                    'fields' => 'TypesRound.type_id',
                    'conditions' => $cond,
                    'recursive' => -1
                ));
                $typesId = $this->flatten($typesId);
                $types = $this->UsersRound->Round->Project->Type->find('all', array(
                    'contain' => array('Question'),
                    'recursive' => -1,
                    'conditions' => array(
                        'Type.id' => $typesId
                    )
                ));
                //$typesId = $this->UsersRound->Round->Project->Type->find('list', array('fields'=>'Type.id','recursive' => -1, 'conditions' => array('Type.id' => $typesId)));
                //cogemos el id de un tipo para saber cual es el proyecto al que pertenece y ahorrarnos una llamada a la BD
                if (!empty($types) > 0) {
                    $projectId = $types[0]['Type']['project_id'];
                    //buscamos el primer documento del proyecto
                    $document = $this->UsersRound->Round->Project->DocumentsProject->find('first', array(
                        'fields' => 'DocumentsProject.document_id',
                        'recursive' => -1,
                        'conditions' => array(
                            'DocumentsProject.project_id' => $projectId
                        ),
                        'order' => 'DocumentsProject.document_id ASC'
                    ));
                } //!empty($types) > 0
            } //empty($data)
            else {
                $data = $this->Session->read('data');
                $document = $data['document'];
            }
            //si no existe ningun documento no seguimos y ahorramos busquedas
            if (!empty($document)) {
                if (empty($data)) {
                    //guardamos el documento y el id del proyecto dado que nos haran falta luego para gurdarlos en session
                    $data['document'] = $document;
                    $data['projectId'] = $projectId;
                    //cogemos el nombre para poder borrar los tipos que ya no necesitamos o ya no estan en el round
                    $nonTypes = $this->UsersRound->Round->Type->find('all', array(
                        'fields' => 'Type.name',
                        'recursive' => -1,
                        'conditions' => array(
                            "NOT" => array(
                                'Type.id' => $typesId
                            ),
                            'Type.project_id' => $projectId
                        )
                    ));
                    $nonTypes = $this->flatten($nonTypes);
                    //a partir de este momento, en el array, project_id pasara a tener la lista de questions de cada type
                    //dado que este atributo ya no es necesari
                    foreach ($types as &$type):
                        //se modifocan las comillas simples para que no haya errores
                        $type['Type']['description'] = str_replace("'", '"', $type['Type']['description']);
                    endforeach;



                    //buscamos todos los documentos del proyecto para el selector
                    $projectDocuments = $this->UsersRound->Round->Project->DocumentsProject->find('all', array(
                        'fields' => 'DocumentsProject.document_id',
                        'recursive' => -1,
                        'conditions' => array(
                            'DocumentsProject.project_id' => $projectId
                        ),
                        'order' => 'DocumentsProject.document_id ASC'
                    ));
                    $projectDocuments = $this->flatten($projectDocuments);
                    $projectDocuments = $this->UsersRound->Round->Project->Document->find('all', array(
                        'fields' => 'Document.title',
                        'recursive' => -1,
                        'conditions' => array(
                            'Document.id' => $projectDocuments
                        ),
                        'order' => 'Document.id ASC'
                    ));
                    //buscamos el round para saber la fecha de finalizacion
                    $round = $this->UsersRound->Round->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Round.id' => $UsersRound['UsersRound']['round_id']
                        )
                    ));
                    $isEnd = (time() > strtotime($round['Round']['ends_in_date']));
                    $data['types'] = $types;
                    $data['nonTypes'] = $nonTypes;
                    $data['projectDocuments'] = $projectDocuments;
                    $data['roundId'] = $round['Round']['id'];
                    //guardamos todos los datos importantes en sesion para no tener que hacer re-busquedas ineficientes
                    $this->Session->write('data', $data);
                } //empty($data)
                else {
                    $types = $data['types'];
                    $nonTypes = $data['nonTypes'];
                    $projectDocuments = $data['projectDocuments'];
                    $projectId = $data['projectId'];
                    $isEnd = $this->Session->read('isEnd');
                }
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                    'Annotation.users_round_id' => $this->UsersRound->id,
                    'Annotation.init IS NULL',
                    'Annotation.end IS NULL'
                        ), $deleteCascade);
                //$project= $this->UsersRound->Project->findById($round['Project']['id']);
                $page = 1;
                if (trim($UsersRound['UsersRound']['text_marked']) != '') {
                    //esto lo podemos hacer dado que creamos los UsersRound por id de documento ascendente
                    //asi podemos saber en que documento estamos
                    $page = $this->UsersRound->find('count', array(
                        'fields' => 'id',
                        'recursive' => -1,
                        'conditions' => array(
                            'UsersRound.user_id' => $UsersRound['UsersRound']['user_id'],
                            'UsersRound.round_id' => $UsersRound['UsersRound']['round_id'],
                            'UsersRound.document_id <=' => $UsersRound['UsersRound']['document_id']
                        )
                    ));

                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Document.id' => $UsersRound['UsersRound']['document_id']
                        )
                    ));
                    $this->set('text', $UsersRound['UsersRound']['text_marked']);
                    // throw new Exception;
                } //trim($UsersRound['UsersRound']['text_marked']) != ''
                else {
                    //aqui se suele crear el primer documento, el resto se suele crear en en el save
                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Document.id' => $document['DocumentsProject']['document_id']
                        )
                    ));
                    if (trim($document['Document']['html']) == '') {
                        $this->Session->setFlash(__('This round contains empty documents'));
                        $this->redirect(array(
                            'action' => 'index'
                        ));
                    }

                    $this->set('text', $document['Document']['html']);
                }
                $this->set('projectDocuments', $projectDocuments);
                $this->set('title', $document['Document']['title']);
                $this->set('document_id', $document['Document']['id']);
                $this->set('project_id', $projectId);
                $this->set('types', $types);
                //lo utilizaremos para eliminar las anotaciones de un tipo eliminado
                $this->set('nonTypes', $nonTypes);
                $this->set('round_id', $UsersRound['UsersRound']['round_id']);
                $this->set('user_id', $UsersRound['UsersRound']['user_id']);
                $this->set('user_round_id', $this->UsersRound->id);
                $this->set('isEnd', $isEnd);
                //escribimos la variable en una variable de session puesto que nos sera util a la hora de verificar la fecha cuando se intente crear anotaciones o editarlas
                $this->Session->write('isEnd', $isEnd);
                //variable que contiene User.round.Document.user_round_id
                $triada = $UsersRound['UsersRound']['user_id'] . $UsersRound['UsersRound']['round_id'] . $document['Document']['id'] . $this->UsersRound->id;
                //esta variable sera usada para constatar que no se intentan modificar dichas variables
                $this->Session->write('triada', $triada);
                $this->paginate = array(
                    'recursive' => -1,
                    'order' => array(
                        'DocumentsProject.document_id' => 'asc'
                    ),
                    'limit' => 1,
                    'page' => $page
                );
                $this->paginate($this->UsersRound->Round->Project->DocumentsProject, array(
                    'DocumentsProject.project_id' => $projectId
                ));
            } //!empty($document)
            else {
                $this->Session->setFlash(__('There are no documents associated with this project or this round does not have any type associated'));
                $this->redirect(array(
                    'action' => 'index'
                ));
            }
        } //!$this->request->is('post') || !$this->request->is('put')
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function save($id = null) {
        $this->autoRender = false;
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if ($this->request->is('post') || $this->request->is('put')) {
            $pos = $this->request->data['UsersRound']['page'];
            if ($this->request->data['UsersRound']['deleteSessionData'])
                $this->Session->delete('data');
            //borarmos esta variable de session para que se actualicen los satos del round
            $isEnd = $this->Session->read('isEnd');
            //usado para paginacion
            if ($this->request->data['UsersRound']['text_marked'] != 'empty' && !$isEnd) { // nos ahorramos transacciones si se le da a borra sin modificar nada
                $textoMarky = trim($this->request->data['UsersRound']['text_marked']);
                $textoMarky = preg_replace('/\s+/', ' ', $textoMarky);
                //las siguientes lineas son necesarias dado que cada navegador hace lo  que le da la gana con el DOM con respecto a la gramatica,
                //no hay un estandar asi por ejemplo en crhome existe Style:valor y en Explorer Style :valor,etc
                $textoForMatches = str_replace(array(
                    "\n",
                    "\t",
                    "\r"
                        ), '', $textoMarky);
                //$textoForMatches = str_replace('> <', '><', $textoForMatches);

                $textoForMatches = strip_tags($textoForMatches, '<mark>');
                $textoForMatches = utf8_decode(html_entity_decode($textoForMatches));
                //buscamnos el comienzo por ello devemos volver a machear todos los span
                preg_match_all("/(<mark[^>]*Marky[^>]*>)(.*?)<\/mark>/", $textoForMatches, $matches, PREG_OFFSET_CAPTURE);
                //array donde guardaremos las nuevas anotaciones
                $allAnnotations = array();
                if (sizeof($matches[1]) != 0) {
                    $this->recursive = -1;
                    //ahora comenzaremos a guardarlas en la BD
                    $accum = 0;
                    //guarda el acumulado de los spans creados
                    preg_match('/[^>]*value=.?(\w*).?[^>]/', $matches[1][0][0], $value);
                    //se corresponde con el ultimo id inserado /[^>]*value=.?(\w*).?[^>]/
                    $insertID = $value[1];
                    $lastID = -1;
                    //se corresponde al identificador del ultimo id del final del array, sirve para saber si la ultima anotacion se ha insertado
                    $original_start = $matches[1][0][1];
                    //variable que guarda el tamanho else texto acumulado empieza en -1 debido a que match empieza en la posicion 0 y length empieza uno
                    //si esto no fuera asi tendriamos una anotacion que termina en 59 y otra que empieza en 59
                    $textAcummulate = -1;

                    for ($i = 0; $i < sizeof($matches[1]); $i++) {
                        preg_match('/[^>]*value=.?(\w*).?[^>]/', $matches[1][$i][0], $value);
                        //es necesario hacer esto debido a que pude darse el caso de que tengamos <mark> entre tags html anidadas con el mismo id
                        if ($insertID != $value[1]) {
                            $lastID = $insertID;
                            if ($this->UsersRound->Annotation->updateAll(array(
                                        'Annotation.init' => $original_start,
                                        'Annotation.end' => $original_start + strlen($texto)
                                            ), array(
                                        'Annotation.id' => $insertID
                                            ), -1)) {
                                array_push($allAnnotations, $insertID);
                            }
                            //actualizamos las variables para la proxima anotacion
                            $textAcummulate = -1;
                            $texto = "";
                            $insertID = $value[1];
                            $original_start = $matches[1][$i][1] - $accum;
                        } //$insertID != $value[1]
                        $texto = strip_tags($matches[0][$i][0]);
                        $accum = $accum + strlen($matches[1][$i][0]) + strlen("</mark>");
                    } //$i = 0; $i < sizeof($matches[1]); $i++
                    //introducimos la ultima anotacion dado que ha quedado sin introducir
                    //LENGTH (Annotation.annotated_text)-1 tamanho del texto original -1 dado que preg match empieza en 0
                    if ($lastID != $insertID) {
                        if ($this->UsersRound->Annotation->updateAll(array(
                                    'Annotation.init' => $original_start,
                                    'Annotation.end' => $original_start + strlen($matches[2][$i - 1][0])
                                        ), array(
                                    'Annotation.id' => $insertID
                                        ), -1)) {

                            array_push($allAnnotations, $insertID);
                        }
                    } //$lastID != $insertID
                } //sizeof($matches[1]) != 0
                //$textoMarky=str_ireplace("id=\"Marky","onmousedown='unHiglight(event);' id=\"Marky",$textoMarky); //for much browsers
                $this->request->data['UsersRound']['text_marked'] = $textoMarky;
                unset($this->request->data['UsersRound']['page']);
                //borramos todas las anotaciones que no tengan inicio ni final
                $deleteCascade = Configure::read('deleteCascade');
                $this->UsersRound->Annotation->deleteAll(array(
                    'not' => array(
                        'Annotation.id' => $allAnnotations
                    ),
                    'Annotation.users_round_id' => $this->UsersRound->id
                        ), $deleteCascade);


                if ($this->UsersRound->save($this->request->data)) {
                    $cond = array(
                        'round_id' => $this->request->data['UsersRound']['round_id'],
                        'user_id' => $this->request->data['UsersRound']['user_id']
                    );
                    $this->UsersRound->UpdateAll(array(
                        'modified' => 'NOW()'
                            ), $cond);

                    //modificamos todos los rounds dado que todos ellos forman un nico round
                    if ($pos != '#')
                        $this->Session->setFlash(__('The previous document has been saved'), 'success');
                    else
                        $this->Session->setFlash(__('Document has been saved', 'success'));
                } //$this->UsersRound->save($this->request->data)
                else {
                    $this->Session->setFlash(__('The round could not be saved.Info: save Annoted text. Please, try again.'));
                    //$this->redirect(array('action' => 'start', $this->UsersRound->id));
                }
            } //$this->request->data['UsersRound']['text_marked'] != 'empty' && !$isEnd
            //$this->Session->setFlash(__('the request text is empty. A herror has occurred or the text was already empty. Therefore, the round will not be saved.'));
            if ($pos != '#') {
                $pos = $pos - 1;
                //buscamos el round para obtener el proyecto_id
                $round = $this->UsersRound->Round->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'Round.id' => $this->request->data['UsersRound']['round_id']
                    )
                ));
                //buscamos el documento que queremos, atentos al offset y limit
                $documents = $this->UsersRound->Round->Project->DocumentsProject->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'DocumentsProject.project_id' => $round['Round']['project_id']
                    ),
                    'order' => 'DocumentsProject.Document_id ASC',
                    'offset' => $pos,
                    'limit' => $pos
                ));
                //buscamos si existe algun UsersRound con dicho documento para este round y este usuario
                $UsersRound = $this->UsersRound->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'UsersRound.user_id' => $this->request->data['UsersRound']['user_id'],
                        'UsersRound.round_id' => $round['Round']['id'],
                        'UsersRound.document_id' => $documents[0]['DocumentsProject']['document_id']
                    )
                ));
                //aqui se crean los documentos a demanda
                if (empty($UsersRound)) {
                    //se busca el documento en cuestion
                    $document = $this->UsersRound->Round->Project->Document->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Document.id' => $documents[0]['DocumentsProject']['document_id']
                        )
                    ));
                    //se crean aqui para no tener rounds_sin que el usuario haya llegado a esa pagina,optimizamos espacio
                    $data = array(
                        'user_id' => $this->request->data['UsersRound']['user_id'],
                        'round_id' => $round['Round']['id'],
                        'document_id' => $documents[0]['DocumentsProject']['document_id'],
                        'text_marked' => $document['Document']['html']
                    );
                    $this->UsersRound->id = null;
                    $this->UsersRound->create();
                    if ($this->UsersRound->save($data)) {
                        $sigId = $this->UsersRound->id;
                    } //$this->UsersRound->save($data)
                    else {
                        $this->Session->setFlash(__('The users round could not be saved. Uncknow server error. Please, try again.'));
                        $sigId = $id;
                    }
                } //empty($UsersRound)
                else {
                    $sigId = $UsersRound['UsersRound']['id'];
                }
                $this->redirect(array(
                    'controller' => 'usersRounds',
                    'action' => 'start',
                    $sigId,
                    "page" => $pos + 1
                ));
            } //$pos != '#'
            else {
                $this->Session->setFlash(__('Document has been saved'), 'success');
                $this->redirect(array(
                    'controller' => 'usersRounds',
                    'action' => 'start',
                    $this->UsersRound->id
                ));
            }
        } //$this->request->is('post') || $this->request->is('put')
        else {
            $this->request->data = $this->UsersRound->read(null, $id);
        }
    }

    /**
     * start method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($round_id = null, $user_id = null) {
        $this->UsersRound->Round->id = $round_id;
        $this->UsersRound->User->id = $user_id;


        if (!$this->UsersRound->Round->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        if (!$this->UsersRound->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        } //!$this->UsersRound->exists()

        $count = $this->UsersRound->find('count', array(
            'recursive' => -1,
            'conditions' => array('user_id' => $user_id, 'round_id' => $round_id, array('NOT' => array('text_marked' => NULL))
        )));
        if ($count == 0) {
            $this->Session->setFlash(__('This user has not annoted any document'));
            $this->redirect(array(
                'controller' => 'rounds',
                'action' => 'view',
                $round_id
            ));
        } //!
        $data = $this->Session->read('data');
        if (!empty($data) && !isset($data['nonTypes'])) {
            $this->Session->delete('data');
            $data = null;
        }
        if (empty($data)) {
            $data = array();
            $cond = array(
                'TypesRound.Round_id' => $round_id
            );
            $typesId = $this->UsersRound->Round->TypesRound->find('all', array(
                'fields' => 'TypesRound.type_id',
                'conditions' => $cond,
                'recursive' => -1
            ));
            $typesId = $this->flatten($typesId);
            $types = $this->UsersRound->Round->Project->Type->find('all', array(
                'contain' => array('Question'),
                'recursive' => -1,
                'conditions' => array(
                    'Type.id' => $typesId
                )
            ));


            $projectId = $types[0]['Type']['project_id'];
            $data['projectId'] = $projectId;
            //cogemos el nombre para poder borrar los tipos que ya no necesitamos o ya no estan en el round
            $nonTypes = $this->UsersRound->Round->Type->find('all', array(
                'fields' => 'Type.name',
                'recursive' => -1,
                'conditions' => array(
                    "NOT" => array(
                        'Type.id' => $typesId
                    ),
                    'Type.project_id' => $projectId
                )
            ));
            $nonTypes = $this->flatten($nonTypes);



            //a partir de este momento, en el array, project_id pasara a tener la lista de questions de cada type
            //dado que este atributo ya no es necesari
            foreach ($types as &$type):
                //se modifocan las comillas simples para que no haya errores
                $type['Type']['description'] = str_replace("'", '"', $type['Type']['description']);
            endforeach;
            $documents = $this->UsersRound->Round->Project->Document->find('list', array(
                'joins' => array(
                    array(
                        'table' => 'documents_projects',
                        'alias' => 'DocumentsProject',
                        'type' => 'Inner',
                        'conditions' => array(
                            'DocumentsProject.document_id=Document.id'
                        )
                    ),
                    array(
                        'table' => 'users_rounds',
                        'alias' => 'UsersRound',
                        'type' => 'Inner',
                        'conditions' => array(
                            'UsersRound.document_id=Document.id'
                        )
                    )),
                'conditions' => array('project_id' => $projectId, array('NOT' => array('UsersRound.text_marked' => NULL))),
                'order' => array(
                    'id' => 'asc'
                )
            ));


            $data['types'] = $types;
            $data['nonTypes'] = $nonTypes;
            $data['projectId'] = $projectId;
            $data['documents'] = $documents;

            //guardamos todos los datos importantes en sesion para no tener que hacer re-busquedas ineficientes
            $this->Session->write('data', $data);
        } //empty($data)
        else {

            $types = $data['types'];
            $nonTypes = $data['nonTypes'];
            $projectId = $data['projectId'];
            $documents = $data['documents'];
        }
        $user = $this->UsersRound->User->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $user_id)
        ));

        //$this->set('text', ));
        $this->set('types', $types);
        //lo utilizaremos para eliminar las anotaciones de un tipo eliminado
        $this->set('nonTypes', $nonTypes);
        $this->set('round_id', $round_id);
        $this->set('user_id', $user_id);
        $this->set('project_id', $projectId);
        $this->set('isEnd', true);
        $this->set('documents', $documents);
        $this->set('fullName', $user['User']['full_name']);

        $this->paginate = array(
            'recursive' => -1,
            'order' => array(
                'UsersRound.document_id' => 'asc'
            ),
            'limit' => 1,
            'conditions' => array('user_id' => $user_id, 'round_id' => $round_id, array('NOT' => array('text_marked' => NULL)))
        );
        $this->set('UsersRound', $this->paginate());
        //!empty($document)
    }

    public function rate($id = null) {
        //$this->autoRender = false;
        $this->UsersRound->id = $id;
        if (!$this->UsersRound->exists()) {
            throw new NotFoundException(__('Invalid round'));
        } //!$this->UsersRound->exists()
        else {
            $userRound = $this->UsersRound->find('first', array('fields'=>array('user_id','document_id'),'conditions' => array('id' => $id), 'recursive' => -1));
            $user_id = $this->Session->read('user_id');
            if ($userRound['UsersRound']['user_id'] == $user_id) {
                if ($this->UsersRound->updateAll(array('rate'=>$this->request->data['rate']),array('user_id'=>$user_id,'document_id'=>$userRound['UsersRound']['document_id']))) {
                    return $this->correctResponseJson(json_encode(array('success' => true)));
                } 
            }
            return $this->correctResponseJson(json_encode(array('success' => false)));
        }
    }

}
