<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'uploader', array(
    'file' => 'php-upload' . DS . 'UploadHandler.php'));

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
        $this->paginate = array(
            'fields' => array(
                '`Document`.`id`, `Document`.`title`, `Document`.`created`',
                '`Document`.`external_id`'));
        $data = $this->Session->read('data');
        $busqueda = $this->Session->read('search');
        if ($post == null) {
            $this->Session->delete('data');
            $this->Session->delete('search');
            $this->set('search', '');
        } else if (!empty($data)) {
            $conditions = array(
                'conditions' => array(
                    'OR' => $data));
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
            $cond['Document.external_id  LIKE'] = '%' . addslashes($search) . '%';
            //$cond['Document.html  LIKE'] = '%' . addslashes($search) . '%';
            $this->Session->write('data', $cond);
            $this->Session->write('search', $search);
            $this->redirect(array(
                'action' => 'index',
                1));
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
        $contain = array(
            'Project' => array(
                'id',
                'title',
                'created',
                'modified'));
        $document = $this->Document->find('first', array(
            'contain' => $contain,
            'conditions' => array(
                'Document.id' => $id)));
        $this->set('document', $document);
        $this->set('document_id', $id);
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
            $conditions = array(
                'title !=' => 'Removing...');
        $projects = $this->Document->Project->find('list', array(
            'conditions' => $conditions));
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
            $contain = array(
                'Project' => array(
                    'id',
                    'title',
                    'created',
                    'modified'));
            $this->request->data = $this->Document->find('first', array(
                'contain' => $contain,
                'conditions' => array(
                    'Document.id' => $id)));
//            $haveAnnotations = $this->Document->Annotation->find('first', array(
//                'recursive' => -1,
//                'conditions' => array(
//                    'document_id' => $id)));
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

        $redirect = array(
            'controller' => 'documents',
            'action' => 'index');
        $this->Session->write('redirect', $redirect);

        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id, 'title');

//        throw new Exception;
//        
//        if (!$this->request->is('post')) {
//            throw new MethodNotAllowedException();
//        }
//        $this->Document->id = $id;
//        if (!$this->Document->exists()) {
//            throw new NotFoundException(__('Invalid document'));
//        }
//        $deleteCascade = Configure::read('deleteCascade');
//        $redirect = $this->Session->read('redirect');
//        if ($deleteCascade) {
//            if ($this->Document->save(array('title' => 'Removing...'), false)) {
//                $this->Session->setFlash(__('Selected document is being deleted. Please be patient'), 'information');
//                if ($redirect['controller'] == 'documents') {
//                    $redirect = array('controller' => 'documents', 'action' => 'index');
//                }
//                $this->backGround($redirect);
//                $this->Document->delete($id, $deleteCascade);
//            }
//        } else {
//            if ($this->Document->delete($id, $deleteCascade)) {
//                //$this -> Document -> UsersRound -> deleteAll(array('UsersRound.document_id' => $id), $deleteCascade);
//                if (!$this->request->is('ajax')) {
//
//                    $this->Session->setFlash(__('Document selected has been deleted'), 'success');
//                    if ($redirect['controller'] == 'documents') {
//                        $redirect = array('controller' => 'documents', 'action' => 'index');
//                    }
//                    $this->redirect($redirect);
//                }
//            }
//        }
//        if (!$this->request->is('ajax')) {
//            $this->Session->setFlash(__("Document hasn't been deleted"));
//            $this->redirect($redirect);
//        } else {
//            throw new NotFoundException(__('Invalid question'));
//        }
    }

    /**
     * deleteAll method
     *
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('title');
//        $this->autoRender = false;
//        if (!$this->request->is('post')) {
//            throw new MethodNotAllowedException();
//        } else {
//            $idDocuments = json_decode($this->request->data['selected-items']);
//            $redirect = $this->Session->read('redirect');
//            $deleteCascade = Configure::read('deleteCascade');
//            if ($deleteCascade) {
//                $conditions = array('Document.id' => $idDocuments);
//                if ($this->Document->UpdateAll(array('title' => '\'Removing...\''), $conditions, -1)) {
//                    $this->Session->setFlash(__('Selected documents are being deleted. Please be patient'), 'information');
//                    $this->backGround($redirect);
//                    $this->Document->deleteAll($conditions, $deleteCascade);
//                }
//            } else {
//
//                if ($this->Document->deleteAll(array('Document.id' => $idDocuments), $deleteCascade)) {
//                    if (!$this->request->is('ajax')) {
//                        $this->Session->setFlash(__('Documents selected have been deleted'), 'success');
//                        $this->redirect($redirect);
//                    }  else {
//                        return $this->correctResponseJson(array('success' => true));
//                    }
//                }
//                if (!$this->request->is('ajax')) {
//                    $this->Session->setFlash(__("Documents selected haven't been deleted"));
//                    $this->redirect($redirect);
//                } else {
//                    throw new NotFoundException(__('Invalid question'));
//                }
//            }
//        }
    }

    public function deleteAll() {
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        } else {
            $redirect = $this->Session->read('redirect');

            $deleteCascade = Configure::read('deleteCascade');
            if ($deleteCascade) {
                $conditions = array(
                    '1' => 1);
                if ($this->Document->UpdateAll(array(
                            'title' => '\'Removing...\''), $conditions, -1)) {
                    $this->Session->setFlash(__('All documents are being deleted. Please be patient'), 'information');
                    $this->backGround($redirect);
                    $this->Document->deleteAll($conditions, $deleteCascade);
                    $this->Document->Project->Round->deleteAll($conditions, $deleteCascade);
                }
            } else {
                if ($this->Document->deleteAll(array(
                            '1' => 1), $deleteCascade)) {
                    $this->Session->setFlash(__('All Documents  have been deleted'), 'success');
                    $this->redirect($redirect);
                    $this->Document->Project->Round->deleteAll($conditions, $deleteCascade);
                }

                $this->Session->setFlash(__("Documents haven't been deleted"));
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
        $path = $path . DS . $user . DS;
        $dir = new Folder($path, true, 0700);

        if ($this->request->is('post') || $this->request->is('put')) {
            $fileName = '';
            $files = $dir->find('.*');
            $webDir = $this->request->data['Document']['Url'];
            //aunque htmLawed remplaza las url de las imagenes no lo hace con los links por ello lo hara marky
            $webDir = str_replace('http://', '', $webDir);
            $webDir = 'http://' . $webDir . '/';
            $config = array(
                'deny_attribute' => 'id,on*',
                'cdata' => 1,
                'elements' => '* -script -object -meta -link -head -select -input -button',
                'comment' => 1,
                'safe' => 1,
                'keep_bad' => 6,
                'no_deprecated_attr' => 2,
                'valid_xhtml' => 1,
                'abs_url' => 1,
                'base_url' => $webDir);
            foreach ($files as $file) {
                //sleep(5);
                $fileName = substr($file, 0, strrpos($file, '.'));
                $file = new File($dir->pwd() . DS . $file);
                if ($file->readable()) {
                    $content = $file->read();
                    $file->close();
                    $content = $this->cleanHtml($content, $config, $webDir);


                    if (strpos($fileName, '--') !== false) {
                        $fileName = split("--", $fileName);
                        $name = $fileName[1];
                        $external_id = $fileName[0];
                        $data = array(
                            'clean' => true,
                            'title' => $name,
                            'html' => $content,
                            'Project' => $this->request->data['Project'],
                            'external_id' => $external_id);
                    } else {
                        $data = array(
                            'clean' => true,
                            'title' => $fileName,
                            'html' => $content,
                            'Project' => $this->request->data['Project']);
                    }

                    $this->Document->create();
                    if (!$this->Document->save($data)) {
                        $this->Session->setFlash(__('The document ' . $fileName . 'could not be saved. Please, try again.'));
                    }
                }
                if (!$dir->delete()) {
                    print_r("warning error file delete");
                }
            }
            $this->redirect(array(
                'controller' => 'documents',
                'action' => 'view',
                $this->Document->id));
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
        App::import('Vendor', 'htmLawed', array(
            'file' => 'htmLawed' . DS . 'htmLawed.php'));
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
        $content = str_replace(array(
            "\n",
            "\t",
            "\r"), '', $content);
        return $content;
    }

    /**
     * multiUploadDocument method
     * @throws Exception
     * @return void
     */
    public function pubmedImport($onlyAbstarcts = false) {
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->autoRender = false;
            $notResponse = array();
            $config = array(
                'deny_attribute' => ' on*',
                'cdata' => 1,
                'elements' => '* -script -object -meta -link -head',
                'comment' => 1,
                'safe' => 1,
                'keep_bad' => 6,
                'no_deprecated_attr' => 2,
                'valid_xhtml' => 1,
                'abs_url' => 1,
                'base_url' => 'http://www.ncbi.nlm.nih.gov/pmc/articles/',);
            $code = trim($this->request->data['code']);

            if ($onlyAbstarcts) {
                $html = @file_get_contents('http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=' . $code . '&retmode=text&rettype=abstract');
                if ($html != '' && strpos($html, 'Error occurred') == false) {

                    $sections = preg_split("/\n\n/", $html);

                    if (empty($html) && sizeof($sections) < 5)
                        throw new Exception("Error Processing Request", 1);

                    $abstract = $sections[4];
                    $webDir = "<div><a href='http://www.ncbi.nlm.nih.gov/pubmed/$code' target='_blank'>$code</a></div>";
                    $title = $sections[1];
//                    $html = $this->cleanHtml('<div id="pubmedImport">'.$title.$webDir.$abstract.'</div>', $config, $webDir);
                    $html = "<h1>$title</h1>$webDir$abstract";

                    $data = array(
                        'clean' => true,
                        'external_id' => $code,
                        'title' => $title . '[PMID:' . $code . ']',
                        'html' => '<div class="onlyAbstract" id="pubmedImport">' . $html . '</div>',
                        'Project' => $this->request->data['Project']);
                    $this->Document->create();
                    if (!$this->Document->save($data)) {
                        $this->Session->setFlash(__('An error occurred with the document ' . $title . 'could not be saved. Please, try again.'));
                    }
                    return $this->correctResponseJson(array(
                                'code' => $code,
                                'success' => true));
                } else {
                    return $this->correctResponseJson(array(
                                'code' => $code,
                                'success' => false));
                }
            } else {
                $html = @file_get_contents('http://www.ncbi.nlm.nih.gov/pmc/articles/pmid/' . $code);
                if ($html != '' && strpos($html, 'Page not available') == false) {
                    preg_match('/(<div[^>]*id=.maincontent[^>]*>.*)<div[^>]*id=.rightcolumn./smi', $html, $html);
                    if (empty($html))
                        throw new Exception("Error Processing Request", 1);
                    $webDir = 'http://www.ncbi.nlm.nih.gov/';
                    $html = $this->cleanHtml($html[1], $config, $webDir);
                    preg_match('/<[^>]*content-title[^>]*>([^<]*)<[^>]*>/', $html, $title);
                    $title = $title[1];
                    $data = array(
                        'clean' => true,
                        'external_id' => $code,
                        'title' => $title . '[PMID:' . $code . ']',
                        'html' => '<div id="pubmedImport">' . $html . '</div>',
                        'Project' => $this->request->data['Project']);
                    $this->Document->create();
                    if (!$this->Document->save($data)) {
                        $this->Session->setFlash(__('An error occurred with the document ' . $title . 'could not be saved. Please, try again.'));
                    }
                    return $this->correctResponseJson(array(
                                'code' => $code,
                                'success' => true));
                } else {
                    return $this->correctResponseJson(array(
                                'code' => $code,
                                'success' => false));
                }
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
    public function deleteUploadDocument() {
        $this->autoRender = false;
        if ($this->request->is('DELETE')) {
            $fileName = $this->request->query['file'];
            $path = Configure::read('uploadFolder');
            $user = $this->Session->read('email');
            $path = $path . $user . DS;
            App::uses('Folder', 'Utility');
            App::uses('File', 'Utility');
            $dir = new Folder($path);
            $file = new File($dir->pwd() . DS . $fileName);
            if ($file->exists()) {
                $contents = $file->delete();
            } else {
                throw new NotFoundException();
            }
        } else {
            throw new MethodNotAllowedException();
        }
    }

    public function UploadDocument() {
        $path = Configure::read('uploadFolder');
        $maxUpload = Configure::read('max_number_of_files');
        $user = $this->Session->read('email');
        $path = $path . $user . DS;

        //para que no moleste con los permisos
        //ini_set("display_errors", 0);
        //error_reporting(0);

        $this->autoRender = false;
//str_replace("webroot/", "", $path),
        $options = array(
            'script_url' => Router::url(array(
                'controller' => 'documents',
                'action' => 'deleteUploadDocument')),
            'upload_dir' => $path,
            'upload_url' => str_replace("webroot/", "", $path),
            'user_dirs' => false,
            'mkdir_mode' => 0777,
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => array(
                'OPTIONS',
                'HEAD',
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE'),
            'access_control_allow_headers' => array(
                'Content-Type',
                'Content-Range',
                'Content-Disposition'),
            // Enable to provide file downloads via GET requests to the PHP script:
            'download_via_php' => false,
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/(\.|\/)(txt|htm|htm?l)$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => $maxUpload,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,);
        $upload_handler = new UploadHandler($options);
    }

    function exportDocuments($projectId = null, $roundId = null) {

        $this->Project = $this->Document->Project;
        $this->UsersRound = $this->Document->UsersRound;
        $this->Round = $this->Project->Round;
        $this->AnnotatedDocument = $this->Round->AnnotatedDocument;
        $this->Annotation = $this->Round->Annotation;
        $this->User = $this->Project->User;
        $this->Type = $this->Project->Type;



        $this->autoRender = false;
        $this->Project->id = $projectId;
        if (!$this->Project->exists()) {
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
                        $this->AnnotatedDocument->virtualFields['title'] = 'Document.title';

                        $documentsSize = $this->AnnotatedDocument->find('count', array(
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
                                //'fields' => array('UsersRound.text_marked', 'UsersRound.title')
                        ));

                        $this->User->virtualFields = array(
                            'full_name' => "CONCAT(username,'_',surname)"
                        );
                        $users = $this->User->find('list', array(
                            'recursive' => -1,
                            'fields' => array(
                                'User.id',
                                'User.full_name'),
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


                        $types = $this->Type->find('all', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'project_id' => $projectId),
                            'fields' => array(
                                'name',
                                'colour'),
                        ));

                        if ($documentsSize == 0) {
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

                        $dinamicCSS = '';
                        $typesHtml = "<div class=\"types-annotations\">";
                        foreach ($types as $type) {
                            $name = $type['Type']['name'];
                            $colour = $type['Type']['colour'];
                            $typesHtml .= "<span name=\"$name\" style=\"color:#000000; background-color:rgba($colour)\" class=\"label\" title=\"Type: $name\">$name</span>";
                            $dinamicCSS .= ".myMark" . $type['Type']['name'] . "{ "
                                    . "background-color: rgba(" . $colour . ") !important; 
                                     -webkit-print-color-adjust:exact !important; 
                            }
                            @media print {
                            .myMark" . $type['Type']['name'] . "{ background-color: rgba(" . $type['Type']['colour'] . ") !important;} \n } \n";
                        }
                        $typesHtml .= "</div><div class=\"clear\"></div>";


                        $this->AnnotatedDocument->virtualFields['external_id'] = 'Document.external_id';
                        $this->AnnotatedDocument->virtualFields['id'] = 'Document.id';


                        $index = 0;
                        while ($index < $documentsSize) {
                            $documents = $this->AnnotatedDocument->find('all', array(
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
                                    'AnnotatedDocument.title',
                                    'AnnotatedDocument.external_id',
                                    'AnnotatedDocument.user_id',
                                    'AnnotatedDocument.document_id',),
                                'limit' => $documentsBuffer, //int
                                'offset' => $index, //int
                            ));
                            $cssToExport = "export";
                            foreach ($documents as $document) {
                                $fileName = $document['AnnotatedDocument']['external_id'] . ".html";
                                $title = "<h1>ID: " . $document['AnnotatedDocument']['external_id'] . "</h1>";

                                $userFolder = $users[$document['AnnotatedDocument']['user_id']];
                                $file = new File($tempFolder->pwd() . DS . $userFolder . DS . $fileName, 600);

                                if ($file->exists()) {
                                    $content = "<!DOCTYPE HTML>"
                                            . "<html>"
                                            . "<head>"
                                            . "<meta charset=\"UTF-8\">"
                                            . "<link rel='stylesheet' type='text/css' href='../" . $cssToExport . ".css' />"
                                            . "<title>Marky - " . $document['AnnotatedDocument']['title'] . "</title>"
                                            . "<style  type='text/css' >"
                                            . $dinamicCSS
                                            . "</style>"
                                            . "</head>"
                                            . "<body>"
                                            . $typesHtml
                                            . "<div class=\"corpora\">"
                                            . $title
                                            . $document['AnnotatedDocument']['text_marked']
                                            . "</div>"
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

                        $annotationsBuffer = Configure::read('annotationsBuffer');
                        $documentsList = $this->Document->find('list', array(
                            'fields' => array(
                                'id',
                                'external_id'),
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
                        $documentsTitles = $this->Document->find('list', array(
                            'fields' => array(
                                'id',
                                'title'),
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
                        $typesList = $this->Type->find('list', array(
                            'recursive' => -1,
                            'fields' => array(
                                'id',
                                'name'),
                            'joins' => array(
                                array(
                                    'type' => 'inner',
                                    'table' => 'types_rounds',
                                    'alias' => 'TypesRounds',
                                    'conditions' => array(
                                        'TypesRounds.round_id' => $roundId,
                                        'TypesRounds.type_id = Type.id',
                                    )
                                ),
                            ),
                        ));


                        $this->Annotation->virtualFields['comment'] = 'AnnotationsQuestions.answer';

                        foreach ($users as $id => $userName) {

                            $userFolder = $users[$id];
                            $index = 0;
                            $file = new File($tempFolder->pwd() . DS . $userFolder . DS . "annotations.tsv", 600);
                            $content = "Document\tSection\tStarting_offset\tEnding_offset\tAnnotation_text\tType\tcomments\n";

                            $annotationsSize = $this->Annotation->find('count', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'round_id' => $roundId,
                                    'user_id' => $id,
                                ),
                            ));


                            while ($index < $annotationsSize) {
                                $annotations = $this->Annotation->find('all', array(
                                    'recursive' => -1,
                                    'joins' => array(
                                        array(
                                            'type' => 'LEFT',
                                            'table' => 'annotations_questions',
                                            'alias' => 'AnnotationsQuestions',
                                            'conditions' => array(
                                                'AnnotationsQuestions.annotation_id = Annotation.id',
                                            )
                                        ),
                                    ),
                                    'conditions' => array(
                                        'round_id' => $roundId,
                                        'user_id' => $id,
                                        array(
                                            "not" => array(
                                                "init" => null,
                                                "end" => null))
                                    ),
                                    'fields' => array(
                                        'init',
                                        'end',
                                        'annotated_text',
                                        'section',
                                        'type_id',
                                        'document_id',
                                        'comment'),
                                    'limit' => $annotationsBuffer, //int
                                    'offset' => $index, //int
                                    'order' => 'document_id'
                                ));


                                foreach ($annotations as $annotation) {
                                    if (isset($documentsList[$annotation['Annotation']['document_id']])) {
                                        $content .= $documentsList[$annotation['Annotation']['document_id']] . "\t";
                                    } else {
                                        $content .= $annotation['Annotation']['document_id'] . "\t";
                                    }
                                    if (isset($annotation['Annotation']['section'])) {
                                        if ($annotation['Annotation']['section'] == 'A') {
                                            $content.=$annotation['Annotation']['section'] . "\t";
                                            $title = $documentsTitles[$annotation['Annotation']['document_id']];
                                            $title = utf8_decode($title);
                                            $titleSize = strlen($title);

                                            $annotation['Annotation']['init'] -=$titleSize;
                                            $annotation['Annotation']['end']-=$titleSize;
                                        } else {
                                            $content.=$annotation['Annotation']['section'] . "\t";
                                        }
                                    } else {
                                        $content.="\t";
                                    }
                                    $content.=
                                            $annotation['Annotation']['init'] . "\t" .
                                            $annotation['Annotation']['end'] . "\t" .
                                            $typesList[$annotation['Annotation']['type_id']] . "\t" .
                                            trim($annotation['Annotation']['annotated_text']) . "\t" .
                                            $annotation['Annotation']['comment']
                                            . "\n";
                                }

                                if ($file->exists()) {
                                    $file->append($content);
                                } else {
                                    throw new Exception("Error creating files ");
                                }
                                $content = '';
                                $index+=$annotationsBuffer;
                            }
                            $file->close();
                            $zip->addFile($file->path, $userFolder . DS . ltrim("annotations.tsv", '/'));


                            /* ========================================================= */
                            /* =                       BioC                            = */
                            /* ========================================================= */



                            $conditions = array(
                                'Annotation.round_id' => $roundId,
                                'Annotation.user_id' => $id,
                            );

                            $this->Annotation->virtualFields["offset"] = "Annotation.init";
                            $this->Annotation->virtualFields["text"] = "Annotation.annotated_text";

                            $annotations = $this->Annotation->find('all', array(
                                'recursive' => -1,
                                'conditions' => $conditions,
                                'fields' => array(
                                    'id',
                                    'offset',
                                    'end',
                                    'text',
                                    'document_id',
                                    'type_id',
                                    'section'),
                                'order' => array('section ASC', 'init DESC'),
                            ));
                            $annotations = Set::combine($annotations, '{n}.Annotation.id', '{n}.Annotation');


                            $documents = $this->Document->find('all', array(
                                'recursive' => -1,
                                'fields' => array(
                                    'Document.id',
                                    'Document.external_id',
                                    'Document.html',
                                    'Document.title',
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'documents_projects',
                                        'alias' => 'DocumentProjects',
                                        'type' => 'INNER',
                                        'conditions' => array(
                                            'Document.id = DocumentProjects.document_id',
                                            'DocumentProjects.project_id' => $projectId,
                                        )
                                    ),
                                ),
                            ));

                            $documents = Set::combine($documents, '{n}.Document.id', '{n}.Document');


                            $this->CommonFunctions = $this->Components->load('BioCExport');
                            $bioC = $this->CommonFunctions->export($annotations, $documents, $typesList);


                            $file = new File($tempFolder->pwd() . DS . $userFolder . DS . "annotations.bioc", 600);

                            if ($file->exists()) {
                                $file->append($bioC);
                            } else {
                                throw new Exception("Error creating files ");
                            }
                            $file->close();
                            $zip->addFile($file->path, $userFolder . DS . ltrim("annotations.bioc", '/'));





                            /* ========================================================= */
                        }


                        $cssFile = new File(".." . DS . "webroot" . DS . "css" . DS . $cssToExport . ".css");
                        $zip->addFile($cssFile->path, $cssToExport . ".css");

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

    public function importTsv() {
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        if ($this->request->is('post') || $this->request->is('put')) {
            if (isset($this->request->data['Document']['File']) && $this->request->data['Document']['File']['size'] > 0) {
                $fileName = $this->request->data['Document']['File']['name'];
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $checkextension = array(
                    "tsv",
                    "txt");
                if (in_array($extension, $checkextension)) {
                    //your code goes here.
                    $db = $this->Document->getDataSource();
                    $timeLimit = Configure::read('scriptTimeLimit');
                    set_time_limit($timeLimit);
                    $db->begin();
                    $project_id = $this->request->data['Project']['Project'];

                    $file = new File($this->request->data['Document']['File']['tmp_name']);
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
                                        $external_id = strtoupper($section[0]);
                                        $title = $section[1];
                                        $corpus = $section[2];


                                        $html = "<div class='corpusSections'><h3 class='title'>" . $title . "</h3><div class='abstract'>" . $corpus . "</div></div>";
                                        $data = array(
                                            'title' => $title,
                                            'external_id' => $external_id,
                                            'html' => $html);

                                        if ($project_id != 0) {
                                            $data = array(
                                                'Project' => array(
                                                    'id' => $project_id),
                                                'Document' => $data);
                                        }
                                        $conditions = array(
                                            'external_id' => $external_id
                                        );
                                        if (!$this->Document->hasAny($conditions)) {
                                            $this->Document->create();
                                            if (!$this->Document->save($data)) {
                                                $db->rollback();
                                                $index++;
                                                throw new Exception("Line " . $index . " could not be save");
                                            }
                                        } else {
                                            $this->Document->DocumentsProject->create();
                                            $document = $this->Document->find('first', array(
                                                'recursive' => -1,
                                                'fields' => 'id',
                                                'conditions' => $conditions
                                            ));
                                            $document_id = $document['Document']['id'];
                                            $data = array(
                                                'project_id' => $project_id,
                                                'document_id' => $document_id);
                                            if (!$this->Document->DocumentsProject->save($data)) {
                                                $db->rollback();
                                                $index++;
                                                throw new Exception("Line " . $index . " could not be save");
                                            }
                                        }
                                    } else {
                                        $db->rollback();
                                        $index++;
                                        throw new Exception("Line " . $index . " is incorrect");
                                    }
                                }
                            }

                            $this->Session->setFlash(__('All documents have been saved'), 'success');
                            $db->commit();
                            if ($project_id != 0) {
                                $this->redirect(array(
                                    'controller' => 'projects',
                                    'action' => 'view',
                                    $project_id));
                            } else {
                                $this->redirect(array(
                                    'controller' => 'documents',
                                    'action' => 'index'));
                            }
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The document could not be processed. Incorrect format or file empty (try txt or tsv file).'));
        }
        $projects = $this->Document->Project->find('list');
        $this->set(compact('projects'));
    }

}
