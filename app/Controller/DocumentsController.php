<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'uploader', array('file' => 'php-upload' . DS . 'UploadHandler.php'));

/**
 * Documents Controller
 *
 * @property Document $Document
 */
class DocumentsController extends AppController {

    /**
     * index method
     * @param boolean $post
     * @return void
     */
    public function index($post = null) {
        $this->Document->recursive = -1;
        $this->paginate = array('fields' => array('`Document`.`id`, `Document`.`title`, `Document`.`created`'));
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } else {
            $conditions = array('conditions' => array('OR' => $data));
            $this->paginate = $conditions;
            $this->set('search', $busqueda);
        }
        $name = strtolower($this->name);
        $this->set($name, $this->paginate());
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
            $cond['Document.title  LIKE'] = '%' . addslashes($search) . '%';
            $cond['Document.created  LIKE'] = '%' . addslashes($search) . '%';
            //$cond['Document.html  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array('action' => 'index', 1));
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
        $this->Document->id = $id;
        if (!$this->Document->exists()) {
            throw new NotFoundException(__('Invalid document'));
        }
        $contain = array('Project' => array('id', 'title', 'created', 'modified'));
        $document = $this->Document->find('first', array('contain' => $contain, 'conditions' => array('Document.id' => $id)));
        $this->set('document', $document);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Document->create();
            if ($this->Document->save($this->request->data)) {
                $this->Session->setFlash(__('Document has been saved'), 'success');
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            } else {
                $this->Session->setFlash(__('Document could not be saved. Please, try again.'));
            }
        }

        $deleteCascade = Configure::read('deleteCascade');
        $conditions = array();
        if ($deleteCascade)
            $conditions = array('title !=' => 'Removing...');
        $projects = $this->Document->Project->find('list', array('conditions' => $conditions));
        $this->set(compact('projects'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->Document->id = $id;
        if (!$this->Document->exists()) {
            throw new NotFoundException(__('Invalid document'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {

            if ($this->Document->save($this->request->data)) {
                $this->Session->setFlash(__('Document has been saved'), 'success');
                $redirect = $this->Session->read('redirect');
                $this->redirect($redirect);
            } else {
                $this->Session->setFlash(__('Document could not be saved. Please, try again.'));
            }
        } else {
            //$this->request->data = $this->Document->read(null, $id);
            $contain = array('Project' => array('id', 'title', 'created', 'modified'));
            $this->request->data = $this->Document->find('first', array('contain' => $contain, 'conditions' => array('Document.id' => $id)));
            $haveAnnotations = $this->Document->UsersRound->Annotation->find('first', array('recursive' => -1, 'conditions' => array('document_id' => $id)));
        }
        $this->set('html', $this->request->data['Document']['html']);
        $this->set('haveAnnotations', !empty($haveAnnotations));
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
        $this->Document->id = $id;
        if (!$this->Document->exists()) {
            throw new NotFoundException(__('Invalid document'));
        }
        $deleteCascade = Configure::read('deleteCascade');
        $redirect = $this->Session->read('redirect');
        if ($deleteCascade) {
            if ($this->Document->save(array('title' => 'Removing...'), false)) {
                $this->Session->setFlash(__('Selected document is being deleted. Please be patient'), 'information');
                if ($redirect['controller'] == 'documents') {
                    $redirect = array('controller' => 'documents', 'action' => 'index');
                }
                $this->backGround($redirect);
                $this->Document->delete($id, $deleteCascade);
            }
        } else {
            if ($this->Document->delete($id, $deleteCascade)) {
                //$this -> Document -> UsersRound -> deleteAll(array('UsersRound.document_id' => $id), $deleteCascade);
                $this->Session->setFlash(__('Document selected has been deleted'), 'success');
                if ($redirect['controller'] == 'documents') {
                    $redirect = array('controller' => 'documents', 'action' => 'index');
                }
                $this->redirect($redirect);
            }
        }
        $this->Session->setFlash(__("Document hasn't been deleted"));
        $this->redirect($redirect);
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
            $idDocuments = json_decode($this->request->data['allDocuments']);
            $redirect = $this->Session->read('redirect');

            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $conditions = array('Document.id' => $idDocuments);
                if ($this->Document->UpdateAll(array('title' => '\'Removing...\''), $conditions, -1)) {
                    $this->Session->setFlash(__('Selected documents are being deleted. Please be patient'), 'information');
                    $this->backGround($redirect);
                    $this->Document->deleteAll($conditions, $deleteCascade);
                }
            } else {
                if ($this->Document->deleteAll(array('Document.id' => $idDocuments), $deleteCascade)) {
                    $this->Session->setFlash(__('Documents selected have been deleted'), 'success');
                    $this->redirect($redirect);
                }

                $this->Session->setFlash(__("Documents selected haven't been deleted"));
                $this->redirect($redirect);
            }
        }
    }

    /**
     * multiUploadDocument method
     *
     * @return void
     */
    public function multiUploadDocument() {
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        $user = $this->Session->read('email');
        $path = Configure::read('uploadFolder');
        $path=$path .DS. $user . DS;
        $dir=new Folder($path, true, 0700);
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $fileName = '';
            $files = $dir->find('.*');
            $webDir = $this->request->data['Document']['Url'];
            //aunque htmLawed remplaza las url de las imagenes no lo hace con los links por ello lo hara marky
            $webDir = str_replace('http://', '', $webDir);
            $webDir = 'http://' . $webDir . '/';
            $config = array('deny_attribute' => 'id,on*', 'cdata' => 1, 'elements' => '* -script -object -meta -link -head -select -input -button', 'comment' => 1, 'safe' => 1, 'keep_bad' => 6, 'no_deprecated_attr' => 2, 'valid_xhtml' => 1, 'abs_url' => 1, 'base_url' => $webDir);
            foreach ($files as $file) {
                //sleep(5);
                $fileName = substr($file, 0, strrpos($file, '.'));
                $file = new File($dir->pwd() . DS . $file);
                if ($file->readable()) {
                    $content = $file->read();
                    $file->close();
                    $content = $this->cleanHtml($content, $config, $webDir);
                    $data = array('clean'=>true,'title' => $fileName, 'html' => $content, 'Project' => $this->request->data['Project']);
                    $this->Document->create();
                    if (!$this->Document->save($data)) {
                        $this->Session->setFlash(__('The document ' . $fileName . 'could not be saved. Please, try again.'));
                    }
                }
                if (!$dir->delete()) {
                    print_r("warning error file delete");
                }
            }
            $this->redirect(array('controller' => 'documents', 'action' => 'view', $this->Document->id));
        } else {//estas lineas son por si se interrumpe o pasa algo en la subida de archivos que no haya elementos repetidos
            $dir->delete();
        }
        $projects = $this->Document->Project->find('list');
        $this->set(compact('projects'));
    }

    /**
     * multiUploadDocument method
     * @param string $content
     * @param Array $config
     * @param string $webDir
     * @return void
     */
    public function cleanHtml($content = null, $config = null, $webDir = null) {
        App::import('Vendor', 'htmLawed', array('file' => 'htmLawed' . DS . 'htmLawed.php'));
        $content = str_replace('href="/', 'TARGET="_blank" href="/', $content);
        $webDirTarget = 'href="' . $webDir;
        $content = str_replace('href="/', $webDirTarget, $content);
        //pequenha limpieza
        $content = preg_replace('#<(script|meta|link|select|input|button)(.*?)>(.*?)<\/(.*?)>#is', '', $content);
        //$content = preg_replace('/body>/', 'div>', $content);
        $content = preg_replace('/<(\?|!)(.*?)(\?|!?)>/', '', $content);
        //$content = preg_replace('/ on([^<]*?)=(\"|\')[^>]*?(\"|\')/', '', $content);
        //$content = preg_replace('/<span *?/', '<div', $content);
        //$content = str_replace('span>', 'div>', $content);
        $content = htmLawed($content, $config);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(array("\n", "\t", "\r"), '', $content);
        return $content;
    }

    /**
     * multiUploadDocument method
     * @throws Exception
     * @return void
     */
    public function pubmedImport() {
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $notResponse = array();
            $config = array('deny_attribute' => ' on*', 'cdata' => 1, 'elements' => '* -script -object -meta -link -head', 'comment' => 1, 'safe' => 1, 'keep_bad' => 6, 'no_deprecated_attr' => 2, 'valid_xhtml' => 1, 'abs_url' => 1, 'base_url' => 'http://www.ncbi.nlm.nih.gov/pmc/articles/',);
            $code = $this->request->data['code'];
            $html = @file_get_contents('http://www.ncbi.nlm.nih.gov/pmc/articles/pmid/' . trim($code));
            if ($html != '' && strpos($html, 'Page not available') == false) {
                preg_match('/(<div[^>]*id=.maincontent[^>]*>.*)<div[^>]*id=.rightcolumn./smi', $html, $html);
                if (empty($html))
                    throw new Exception("Error Processing Request", 1);
                $webDir = 'http://www.ncbi.nlm.nih.gov/';
                $html = $this->cleanHtml($html[1], $config, $webDir);
                preg_match('/<[^>]*content-title[^>]*>([^<]*)<[^>]*>/', $html, $title);
                $title = $title[1];
                $data = array('clean'=>true,'title' => $title . '[PMID:' . $code . ']', 'html' => '<div id="pubmedImport">' . $html . '</div>', 'Project' => $this->request->data['Project']);
                $this->Document->create();
                if (!$this->Document->save($data)) {
                    $this->Session->setFlash(__('An error occurred with the document ' . $title . 'could not be saved. Please, try again.'));
                }
                echo $code . ' is: successful';
            } else {
                echo $code . ' is: failed';
            }
        } else {
            $projects = $this->Document->Project->find('list');
            $this->set('projects', $projects);
            $notResponse = $this->Session->write('notResponse');
            $this->set('notResponse', $notResponse);
        }
    }

    /**
     * UploadDocument method
     * @return void
     */
    public function UploadDocument() {
        
        $path = Configure::read('uploadFolder');
        $user = $this->Session->read('email');
        $path=$path.DS.$user.DS;
        //para que no moleste con los permisos
        ini_set("display_errors", 0);
        error_reporting(0);

        $this->autoRender = false;
       
        $options = array(
            //'script_url' => $this->get_full_url().'/',
            'upload_dir' => $path,
            //'upload_url' => $this->get_full_url().'/files/',
            'user_dirs' => false, 'mkdir_mode' => 0777, 'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE', 'access_control_allow_origin' => '*', 'access_control_allow_credentials' => false, 'access_control_allow_methods' => array('OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE'), 'access_control_allow_headers' => array('Content-Type', 'Content-Range', 'Content-Disposition'),
            // Enable to provide file downloads via GET requests to the PHP script:
            'download_via_php' => false,
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/(\.|\/)(txt|htm|html)$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null, 'min_file_size' => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => 20,
            // Image resolution restrictions:
            'max_width' => null, 'max_height' => null, 'min_width' => 1, 'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,);
        $upload_handler = new UploadHandler($options);
    }

    function exportDocuments($projectId = null, $roundId = null) {
        $this->autoRender = false;
        $this->Document->Project->id = $projectId;
        if (!$this->Document->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $this->Document->Project->Round->id = $roundId;
        if (!$this->Document->Project->Round->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        else {
            $downloadPath = Configure::read('downloadFolder');
            $documentsBuffer = Configure::read('documentsBuffer');
            
            $user = $this->Session->read('email');
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
            $this->RequestHandler = $this->Components->load('RequestHandler');
            if (!isset($user)) {
                print("A joker!!");
                exit();
            } else {
                $this->Document->Project->recursive = -1;
                $projectTitle = $this->Document->Project->read('title');
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
                        $this->Document->UsersRound->virtualFields['title'] = 'Document.title';
                        $documentsSize = $this->Document->UsersRound->find('count', array(
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
                            'conditions' => array('UsersRound.document_id = DocumentsProject.document_id', 'UsersRound.round_id'=>$roundId,'NOT' => array('text_marked' => 'NULL')),
                            //'fields' => array('UsersRound.text_marked', 'UsersRound.title')
                        ));

                        $this->Document->Project->User->virtualFields = array(
                            'full_name' => "CONCAT(username,'_',surname)"
                        );
                        $users = $this->Document->Project->User->find('list', array(
                            'recursive' => -1,
                            'fields' => array('User.id', 'User.full_name'),
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

                        foreach ($users as $key => $userName) {
                            $folderName = ltrim($userName, '/');
                            $folderName = str_replace('\s', '_', $userName);
                            $users[$key] = $folderName;
                            //creamos una carpeta para cada usuario
                            new Folder($tempPath . DS . $folderName, true, 0700);
                        }


                        $types = $this->Document->Project->Type->find('all', array(
                            'recursive' => -1,
                            'conditions' => array('project_id' => $projectId),
                            'fields' => array('name', 'colour'),
                        ));

                        if ($documentsSize == 0) {
                            $this->Session->setFlash(__('This round has not annotated documents'));
                            $this->redirect(array('controller' => 'projects', 'action' => 'view', $projectId));
                        }
                        // Initialize archive object
                        $zip = new ZipArchive;
                        $packetName = $projectTitle . ".zip";

                        if (!$zip->open($tempFolderAbsolutePath . $packetName, ZipArchive::CREATE)) {
                            die("Failed to create archive\n");
                        }

                        $dinamicCSS = '';
                        foreach ($types as $type) {

                            $dinamicCSS .= ".myMark" . $type['Type']['name'] . "{ "
                                    . "background-color: rgba(" . $type['Type']['colour'] . ") !important; 
                                     -webkit-print-color-adjust:exact !important; 
                            }
                            @media print {
                            .myMark" . $type['Type']['name'] . "{ background-color: rgba(" . $type['Type']['colour'] . ") !important;} \n } \n";
                        }



                        $index = 0;
                        while ($index < $documentsSize) {
                            $documents = $this->Document->UsersRound->find('all', array(
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
                                'conditions' => array('UsersRound.document_id = DocumentsProject.document_id','UsersRound.round_id'=>$roundId, 'NOT' => array('text_marked' => 'NULL')),
                                'fields' => array('UsersRound.text_marked', 'UsersRound.title', 'UsersRound.user_id'),
                                'limit' => $documentsBuffer, //int
                                'offset' => $index, //int
                            ));

                            foreach ($documents as $document) {
                                $fileName = $document['UsersRound']['title'] . ".html";
                                $userFolder = $users[$document['UsersRound']['user_id']];
                                $file = new File($tempFolder->pwd() . DS . $userFolder . DS . $fileName, 600);
                                if ($file->exists()) {
                                    $content = "<!DOCTYPE HTML>"
                                            . "<html>"
                                            . "<head>"
                                            . "<link rel='stylesheet' type='text/css' href='../markyAnnotation.css' />"
                                            . "<title>Marky - " . $document['UsersRound']['title'] . "</title>"
                                            . "<style  type='text/css' >"
                                            . $dinamicCSS
                                            . "</style>"
                                            . "</head>"
                                            . "<body>"
                                            . $document['UsersRound']['text_marked']
                                            . "</body>"
                                            . "</html>";
                                    $file->write($content);
                                    $file->close();
                                    $zip->addFile($file->path, $userFolder . DS . ltrim($fileName, '/'));
                                } else {
                                    throw new Exception("Error creating files ");
                                }
                            }
                            $index+=$documentsBuffer;
                        }

                        $cssFile = new File(".." . DS . "webroot" . DS . "css" . DS . "markyAnnotation.css");
                        $zip->addFile($cssFile->path, "markyAnnotation.css");

                        $zip->close();
                        if (!$zip->status == ZIPARCHIVE::ER_OK) {
                            throw new Exception("Error creating zip ");
                        }
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
