<?php

App::uses('AppController', 'Controller');

/**
 * ConsensusAnnotations Controller
 *
 * @property ConsensusAnnotation $ConsensusAnnotation
 * @property PaginatorComponent $Paginator
 */
class ConsensusAnnotationsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array(
        'Paginator');

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $this->ConsensusAnnotation->create();
            $id = $this->request->data['consensusAnnotation']['id'];
            $round_id = $this->request->data['consensusAnnotation']['round_id'];
            $project_id = $this->request->data['consensusAnnotation']['project_id'];
            $data = $this->ConsensusAnnotation->Annotation->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id)));
            $newConsensus = array(
                'round_id' => $round_id,
                'project_id' => $project_id,
                'document_id' => $data['Annotation']['document_id'],
                'type_id' => $data['Annotation']['type_id'],
                'annotation' => $data['Annotation']['annotated_text'],
                'init' => $data['Annotation']['init'],
                'end' => $data['Annotation']['end'],
                'section' => $data['Annotation']['section'],
            );
            if ($this->ConsensusAnnotation->save($newConsensus)) {
                return $this->correctResponseJson(json_encode(array(
                            'success' => true)));
            } else {
                $this->Session->setFlash(__('The condition could not be saved. Please, try again.'));
                return $this->correctResponseJson(json_encode(array(
                            'success' => false)));
            }
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->ConsensusAnnotation->id = $id;
        if (!$this->ConsensusAnnotation->exists()) {
            throw new NotFoundException(__('Invalid condition'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->ConsensusAnnotation->delete()) {
            return $this->correctResponseJson(json_encode(array(
                        'success' => true)));
        } else {
            $this->Session->setFlash(__('The condition could not be saved. Please, try again.'));
            return $this->correctResponseJson(json_encode(array(
                        'success' => false)));
        }
        return $this->redirect(array(
                    'action' => 'index'));
    }

    public function automatic() {

        if ($this->request->is(array(
                    'post',
                    'put'))) {
            $projectId = $this->request->data['consensusAnnotation']['project_id'];
            $roundId = $this->request->data['consensusAnnotation']['round_id'];
            $percent = $this->request->data['consensusAnnotation']['percent'];
            $this->ConsensusAnnotation->Project->id = $projectId;
            $this->ConsensusAnnotation->Project->Round->id = $roundId;
            if (!$this->ConsensusAnnotation->Project->exists()) {
                throw new NotFoundException(__('Invalid proyect'));
            } //!$this->Project->exists()
            if (!$this->ConsensusAnnotation->Project->Round->exists()) {
                throw new NotFoundException(__('Invalid Round'));
            } //!$this->Project->exists()


            $count = $this->ConsensusAnnotation->Project->ProjectsUser->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectsUser.project_id' => $projectId)
            ));

            if ($percent == '') {
                $percent = 100;
            } else {
                $percent = intval($percent);
                if ($percent > 100 || $percent < 0) {
                    $this->ConsensusAnnotation->deleteAll(array(
                        'round_id' => $roundId));
                    $this->Session->setFlash(__('Deleted consensus has had success'), 'success');
                    return $this->redirect(array(
                                'controller' => 'annotations',
                                'action' => 'generateConsensus',
                                $projectId,
                                $roundId));
                }
            }

            $percent = round($percent * ($count / 100), PHP_ROUND_HALF_DOWN);


            $this->ConsensusAnnotation->Annotation->virtualFields = array(
                'project_id' => $projectId
            );
            $db = $this->ConsensusAnnotation->Annotation->getDataSource();
            $options = array(
                'table' => $db->fullTableName($this->ConsensusAnnotation->Annotation),
                'alias' => 'Annotation',
                'recursive' => -1,
                'fields' => array(
                    $projectId,
                    'Annotation.round_id',
                    'Annotation.document_id',
                    'Annotation.type_id',
                    'Annotation.init',
                    'Annotation.end',
                    'Annotation.annotated_text',
                    'Annotation.section',
                ),
                'group' => array(
                    'init',
                    'end HAVING count(DISTINCT Annotation.user_id) >= ' . $percent),
                'conditions' => array(
                    'round_id' => $roundId,
                    'Annotation.init IS NOT NULL',
                    'Annotation.end IS NOT NULL'
                ),
            );



            //$this->ConsensusAnnotation->Annotation->find('all', $options);
            $commit = true;
            $db->begin();
            $commit = $commit & $this->ConsensusAnnotation->deleteAll(array(
                        'project_id' => $projectId));
            $query = $db->buildStatement($options, $this->ConsensusAnnotation->Annotation);
            $db->query('INSERT INTO ' . $db->fullTableName($this->ConsensusAnnotation) . ' (project_id,round_id,document_id,type_id,init,end,annotation,section) ' . $query);
            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__('Automatic consensus has had success'), 'success');
                $this->redirect(array(
                    'controller' => 'annotations',
                    'action' => 'generateConsensus',
                    $projectId,
                    $roundId));
            } else {
                $db->rollback();
                $this->Session->setFlash(__('Automatic consensus has not had success'));
                $this->redirect(array(
                    'controller' => 'annotations',
                    'action' => 'generateConsensus',
                    $projectId,
                    $roundId));
            }
        } else {
            throw new NotFoundException(__('Invalid proyect'));
        }
    }

    function download($projectId = null, $roundId = null, $diferenceSection = false) {



        $this->Project = $this->ConsensusAnnotation->Project;
        $this->Round = $this->Project->Round;
        $this->AnnotatedDocument = $this->Round->UsersRound->AnnotatedDocument;
        $this->Document = $this->Project->Document;



        //$this->autoRender = false;
        $this->Project->id = $projectId;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $this->Round->id = $roundId;
        if (!$this->Round->exists()) {
            throw new NotFoundException(__('Invalid Round'));
        } //!$this->Project->exists()
        else {
            $downloadPath = Configure::read('downloadFolder');
            $documentsBuffer = Configure::read('documentsBuffer');
            $annotationsBuffer = Configure::read('annotationsBuffer');

            $user = $this->Session->read('email');
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
            $this->RequestHandler = $this->Components->load('RequestHandler');
            if (!isset($user)) {
                print("A joker!!");
                exit();
            } else {
                $this->Project->recursive = -1;
                $projectTitle = $this->Project->read('title');
                $projectTitle = ltrim($projectTitle['Project']['title'], '/');
                $projectTitle = str_replace(' ', '_', $projectTitle);
                $projectTitle = "Marky_#" . substr($projectTitle, 0, 20);

                $downloadDir = new Folder($downloadPath, true, 0700);
                if ($downloadDir->create('')) {
                    //si se puede crear la carpeta
                    //creamos una carpeta temporal
                    $tempPath = $downloadDir->pwd() . DS . uniqid();

                    $tempFolder = new Folder($tempPath, true, 0700);
                    if ($tempFolder->create('')) {
                        //se le añaden permisos

                        $tempFolderAbsolutePath = $tempFolder->path . DS;
                        $this->AnnotatedDocument->virtualFields['count'] = 'COUNT(DISTINCT (AnnotatedDocument.document_id))';
                        //no tiene por que corresponderse el numero de documentos con los documentos
                        //realmente anotados
                        $annotatedDocumentsSize = $this->AnnotatedDocument->find('count', array(
                            'recursive' => -1,
                            'joins' => array(
                                array(
                                    'type' => 'inner',
                                    'table' => 'documents_projects',
                                    'alias' => 'DocumentsProject',
                                    'conditions' => array(
                                        'DocumentsProject.project_id' => $projectId,
                                    )
                                ),
                                array(
                                    'type' => 'inner',
                                    'table' => 'documents',
                                    'alias' => 'Document',
                                    'conditions' => array(
                                        'Document.id = DocumentsProject.document_id',
                                    )
                                ),
                            ),
                            'conditions' => array(
                                'AnnotatedDocument.document_id = DocumentsProject.document_id',
                                'AnnotatedDocument.round_id' => $roundId,
                                'NOT' => array(
                                    'text_marked' => 'NULL')),
                            'fields' => array(
                                'count'),
                        ));
//                        $documentsSize = $documentsSize[0]['UsersRound']['count'];

                        if ($annotatedDocumentsSize == 0) {
                            $this->Session->setFlash(__('This round has not annotated documents'));
                            $this->redirect(array(
                                'controller' => 'projects',
                                'action' => 'view',
                                $projectId));
                        }
                        // Initialize archive object
                        $zip = new ZipArchive;
                        $packetName = $projectTitle . ".zip";

                        if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                            die("Failed to create archive\n");
                        }

                        $index = 0;
                        while ($index < $annotatedDocumentsSize) {
                            $annotatedDocuments = $this->AnnotatedDocument->find('all', array(
                                'recursive' => -1,
                                'joins' => array(
                                    array(
                                        'type' => 'inner',
                                        'table' => 'documents_projects',
                                        'alias' => 'DocumentsProject',
                                        'conditions' => array(
                                            'DocumentsProject.project_id' => $projectId,
                                        )
                                    ),
                                    array(
                                        'type' => 'inner',
                                        'table' => 'documents',
                                        'alias' => 'Document',
                                        'conditions' => array(
                                            'Document.id = DocumentsProject.document_id',
                                        )
                                    ),
                                ),
                                'conditions' => array(
                                    'AnnotatedDocument.document_id = DocumentsProject.document_id',
                                    'AnnotatedDocument.round_id' => $roundId,
                                    'NOT' => array(
                                        'text_marked' => 'NULL')),
                                'fields' => array(
                                    'AnnotatedDocument.text_marked',
                                    'Document.external_id',
                                    'Document.title',
                                    'AnnotatedDocument.user_id',
                                    'Document.id'),
                                'group' => array(
                                    'AnnotatedDocument.user_id',
                                    'AnnotatedDocument.round_id',
                                    'AnnotatedDocument.document_id'),
                                'limit' => $documentsBuffer, //int
                                'offset' => $index, //int
                            ));

                            foreach ($annotatedDocuments as $document) {
                                $id = $document['Document']['external_id'];
                                $fileName = $id . ".txt";
                                $file = new File($tempFolder->pwd() . DS . $fileName, 600);

                                if ($file->exists()) {
                                    $content = $document['AnnotatedDocument']['text_marked'];
                                    $content = preg_replace('/\s+/', ' ', $content);
                                    //las siguientes lineas son necesarias dado que cada navegador hace lo  que le da la gana con el DOM con respecto a la gramatica,
                                    //no hay un estandar asi por ejemplo en crhome existe Style:valor y en Explorer Style :valor,etc
                                    $content = str_replace(array(
                                        "\n",
                                        "\t",
                                        "\r"), '', $content);
                                    $content = str_replace('> <', '><', $content);
                                    $content = strip_tags($content);
                                    $content = html_entity_decode($content, ENT_QUOTES, "UTF-8");
                                    //echo $content;
                                    //throw new Exception;
                                    $file->write($content);
                                    $file->close();
                                    $zip->addFile($file->path, ltrim($fileName, '/'));
                                } else {
                                    throw new Exception("Error creating files ");
                                }
                            }
                            $index+=$documentsBuffer;
                        }


                        $documentsList = $this->Document->find('list', array(
                            'recursive' => -1,
                            'joins' => array(
                                array(
                                    'type' => 'inner',
                                    'table' => 'documents_projects',
                                    'alias' => 'DocumentsProject',
                                    'conditions' => array(
                                        'DocumentsProject.project_id' => $projectId,
                                        'DocumentsProject.document_id = Document.id',
                                    )
                                ),
                            ),
                        ));


                        $annotationsSize = $this->ConsensusAnnotation->find('count', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'ConsensusAnnotation.round_id' => $roundId,
                            ),
                        ));
                        $index = 0;
                        $file = new File($tempFolder->pwd() . DS . "annotations.tsv", 600);


                        $content = "Document\tSection\tStarting_offset\tEnding_offset\tAnnotation_text\tType\n";

                        while ($index < $annotationsSize) {
                            $annotations = $this->ConsensusAnnotation->find('all', array(
                                //'recursive' => -1,
                                'conditions' => array(
                                    'ConsensusAnnotation.round_id' => $roundId,
                                ),
                                'fields' => array(
                                    'Document.external_id',
                                    'Document.title',
                                    'Type.name',
                                    'init',
                                    'end',
                                    'annotation',
                                    'section',
                                    'document_id'),
                                'limit' => $annotationsBuffer, //int
                                'offset' => $index, //int
                            ));


                            foreach ($annotations as $annotation) {

                                if (isset($annotation['Document']['external_id'])) {
                                    $content .= $annotation['Document']['external_id'] . "\t";
                                } else {
                                    $content .= $annotation['Document']['title'] . "\t";
                                }
                                if ($annotation['ConsensusAnnotation']['section'] == 'A') {
                                    $titleSize = strlen($documentsList[$annotation['ConsensusAnnotation']['document_id']]);
                                    $annotation['ConsensusAnnotation']['init'] -=$titleSize;
                                    $annotation['ConsensusAnnotation']['end']-=$titleSize;
                                }
                                if (isset($annotation['ConsensusAnnotation']['section'])) {
                                    $content.=$annotation['ConsensusAnnotation']['section'] . "\t";
                                } else {
                                    $content.="NULL\t";
                                }


                                $content.=
                                        $annotation['ConsensusAnnotation']['init'] . "\t" .
                                        $annotation['ConsensusAnnotation']['end'] . "\t" .
                                        $annotation['ConsensusAnnotation']['annotation'] . "\t" .
                                        $annotation['Type']['name'] . "\n";
                            }

                            //throw new Exception;

                            if ($file->exists()) {
                                $file->append($content);
                            } else {
                                throw new Exception("Error creating files ");
                            }
                            $content = '';
                            $index+=$annotationsBuffer;
                        }
                        $file->close();
                        $zip->addFile($file->path, ltrim("annotations.tsv", '/'));


                        if (!$zip->status == ZIPARCHIVE::ER_OK) {
                            throw new Exception("Error creating zip ");
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
                }
            }
        }
    }

}
