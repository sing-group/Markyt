<?php

App::uses('AppController', 'Controller');

/**
 * Files Controller
 *
 * @property File $File
 * @property PaginatorComponent $Paginator
 */
class ProjectResourcesController extends AppController {
    /**
     * Redirect method
     * Esta funcion es la encargada de redireccionar las peticiones con _id
     * @param string $id biofomics_id
     * @throws NotFoundException
     * @return void
     */
//    public function redirect($id = null) {
//
//    }

    /**
     * uploadFile method
     * Esta funcion es la encargada de gestionar los ficheros del 
     * usurio.
     * En projectos add gestiona (crear,eliminar)
     * En projectos edit gestiona (crear) dado que los ficheros se eliminan de la BD
     * La direccion de la carpeta del usuario se establece con la variable 
     * Configure::read('uploadFilesPath') y el mail del usuario (e mail es unico)
     * @require  App::uses('Folder', 'Utility');
     * @require App::uses('File', 'Utility');
     * @require UploadHandler.php
     * @throws exception
     * @return void
     */
    public function uploadFile($projectId = null) {
        App::import('Vendor', 'uploader', array('file' => 'jQuery-File-Upload' . DS . 'UploadHandler.php'));

        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        $uploadPath = Configure::read('uploadFilesPath');
        $filesAllowed = Configure::read('filesAllowed');
        $maxUploads = Configure::read('max_upload_files');
        $maxFileSize = $this->filesize2bytes(Configure::read('max_file_size'));

        if ($maxUploads == 0) {
            $maxUploads = 99999;
        }
        $path = $uploadPath;
        //para que no moleste con los permisos
        //ini_set("display_errors", 0);
        //error_reporting(0);
        $this->autoRender = false;
        $email = $this->Auth->user('email');
        //si no esta logueado
        if (!$this->Auth->loggedIn()) {
            print("One joker!!");
            exit();
        } else {
            $folder = new Folder();
            //si se puede crear la carpeta
            if ($folder->create($path)) {
                //chmod($path, 0600);
//                $path = $path . DS . $email;
                $path = $path . DS . tempnam(sys_get_temp_dir(), '');

                if ($folder->create($path)) {
                    //si no existe la carpeta se crea
                    $folder = new Folder($path, true, 0700);
                    //chmod($path, 0600);
                    $absolutePath = $folder->path . DS;
                    $options = array(
                        'script_url' => Router::url(array('controller' => 'ProjectResources',
                            'action' => 'uploadFile')),
                        'upload_dir' => $absolutePath,
                        'upload_url' => $this->webroot . $path . DS,
                        'user_dirs' => false,
                        'mkdir_mode' => 0700,
                        'param_name' => 'files',
                        // Set the following option to 'POST', if your server does not support
                        // DELETE requests. This is a parameter sent to the client:
                        'delete_type' => 'DELETE',
                        'access_control_allow_origin' => '*',
                        'access_control_allow_credentials' => false,
                        'access_control_allow_methods' => array('OPTIONS', 'HEAD',
                            'GET', 'POST', 'PUT', 'PATCH', 'DELETE'),
                        'access_control_allow_headers' =>
                        array('Content-Type', 'Content-Range',
                            'Content-Disposition'),
                        // Enable to provide file downloads via GET requests to the PHP script:
                        'download_via_php' => false,
                        // Defines which files (based on their names) are accepted for upload:
                        'accept_file_types' => '/(\.|\/)' . $filesAllowed . '$/i',
                        // The php.ini settings upload_max_filesize and post_max_size
                        // take precedence over the following max_file_size setting:
                        'max_file_size' => $maxFileSize,
                        'min_file_size' => 1,
                        // The maximum number of files for the upload directory:
                        'max_number_of_files' => $maxUploads,
                        // Image resolution restrictions:
                        'max_width' => null, 'max_height' => null, 'min_width' => 1,
                        'min_height' => 1,
                        // Set the following option to false to enable resumable uploads:
                        'discard_aborted_uploads' => true,
                        // Set to true to rotate images based on EXIF meta data, if available:
                        'orient_image' => false,);
                    $upload_handler = new UploadHandler($options, false);
                    switch ($_SERVER['REQUEST_METHOD']) {
                        case 'HEAD':
                        case 'GET':
                            throw new Exception;
                            $upload_handler->get();
                            break;
                        case 'POST':
                        case 'PUT':
                            $group_id = $this->Session->read('group_id');
                            if ($group_id == 1) {
                                $this->ProjectResource->Project->id = $projectId;
                                if (!$this->ProjectResource->Project->exists()) {
                                    throw new NotFoundException(__('Invalid project '));
                                }
                                $response = $upload_handler->post();

                                $packagedFiles = array();

                                $files = $folder->find('.*.' . $filesAllowed);
                                if (!empty($files)) {
                                    foreach ($files as $file) {
                                        $file = new File($folder->pwd() . DS . $file, 644);
                                        if ($file->readable()) {
//                                        $md5 = $file->md5();
                                            $name = $file->name();
                                            $ext = $file->ext();
                                            $content = $file->read();
                                            $fileSize = $file->size();
                                            $file->close();
                                            $data = array(
                                                'name' => $name,
                                                'file' => $content,
                                                'extension' => $ext,
                                                'project_id' => $projectId,
                                                'size' => $fileSize
                                            );
                                            $this->ProjectResource->create();
                                            if ($this->ProjectResource->save($data)) {
                                                $packagedFiles[$name . "." . $ext] = $this->ProjectResource->id;
                                            }
                                        }
                                    }
                                    if (!empty($packagedFiles)) {
                                        $files = $response['files'];
                                        $size = sizeof($files);
                                        for ($index = 0; $index < $size; $index++) {
                                            $file = $files[$index];

                                            if (isset($packagedFiles[$file->name])) {
                                                $file->url = Router::url(array('controller' => 'ProjectResources',
                                                            'action' => 'downloadFile',
                                                            $packagedFiles[$file->name],
                                                            $projectId));

                                                $file->deleteUrl = Router::url(array('controller' => 'ProjectResources',
                                                            'action' => 'deleteFile',
                                                            $packagedFiles[$file->name],
                                                            $projectId));
                                            } else {
                                                $file->error = "Could not be saved";
                                            }
                                        }
                                        return $this->correctResponseJson($response);

//                                            $this->correctResponseJson(array("error" => "Could not be saved"));
                                    }
                                }
                                if (!$folder->delete()) {
                                    throw new Exception("Error deleting files");
                                }
                                return $this->correctResponseJson($response);
                            }

                            break;
                        case 'DELETE':


                            break;
                        default:
                        // header('HTTP/1.0 405 Method Not Allowed');
                    }


                    exit;
                }
            } else {

                throw new Exception;
            }
        }
    }

    /**
     * getFiles method
     * Esta funcion es la encargada de obtener los datos del fichero
     * de la base de datos y envialos a traves de ajax 
     * @param int $projectId id del projectoo
     * @throws NotFoundException
     * @return json_array datos del fichero (ver appcontroller correctResponseJson())
     */
    public function getFiles($projectId = null) {

        $this->ProjectResource->Project->id = $projectId;
        if (!$this->ProjectResource->Project->exists()) {
            throw new NotFoundException(__('Invalid project '));
        }
        $this->ProjectResource->virtualFields['size'] = 'LENGTH(file)';
        if ($this->request->is("ajax")) {
            $files = $this->ProjectResource->find('all', array('conditions' => array(
                    'project_id' => $projectId), 'recursive' => -1));
            $requestFiles = array();
            foreach ($files as $file) {
                $name = $file['ProjectResource']['name'] . '.' . $file['ProjectResource']['extension'];
                $urlDelete = Router::url(array('controller' => 'ProjectResources',
                            'action' => 'deleteFile', $file['ProjectResource']['id'],
                            $file['ProjectResource']['project_id']));
                $urldownload = Router::url(array('controller' => 'ProjectResources',
                            'action' => 'downloadFile', $file['ProjectResource']['id'],
                            $file['ProjectResource']['project_id']));
                //size=intval($file['ProjectResource']['size']);
                array_push($requestFiles, array('name' => $name, 'size' => intval($file['ProjectResource']['size']),
                    "url" => $urldownload, 'deleteUrl' => $urlDelete, "deleteType" => "DELETE"));
            }

            return $this->correctResponseJson(json_encode(array('files' => $requestFiles)));
        }
    }

    /**
     * downloadFile method
     * Esta funcion devuelve un unico fichero  asociado al $projectId
     * en caso de no ser un curador o admin o en el caso de no pertenecerle
     * se envia una excepcion.
     * @param int $id ProjectResource id
     * @param int $projectId id del projectoo
     * @throws NotFoundException
     * @return file
     */
    public function downloadFile($id = null, $projectId = null) {

        $this->ProjectResource->Project->id = $projectId;
        $this->ProjectResource->id = $id;

        if (!$this->ProjectResource->Project->exists()) {
            throw new NotFoundException(__('Invalid project '));
        } else {
            if (!$this->ProjectResource->exists()) {
                throw new NotFoundException(__('Invalid file '));
            } else {

                $options = array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $projectId
                    )
                );
                $project = $this->ProjectResource->Project->find('first', $options);
                $user_id = $this->Session->read('user_id');
                $group_id = $this->Session->read('group_id');

                if ((!isset($user_id)) || ($group_id > 1)) {
                    throw new NotFoundException();
                } else {
                    $options = array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $id,
                            'project_id' => $projectId,
                        )
                    );

                    $file = $this->ProjectResource->find('first', $options);
                    $fileBody = $file['ProjectResource']['file'];
                    //obtenemos el mime del fichero  atraves de su extension
                    $mimeExtension = $this->mimeToExtension($file['ProjectResource']['extension']);
                    $fileName = $file['ProjectResource']['name'] . "." . $file['ProjectResource']['extension'];
                    $this->response->type($mimeExtension);
                    $this->response->body($fileBody);
                    $this->response->download($fileName);
                    $this->autoRender = false;
                    return $this->response;
                }
            }
        }
    }

    /**
     * downloadProject method
     * Esta funcion llama a la funcion que esta en appController
     * @param int $id ProjectResource id
     * @param int $projectId id del projectoo
     * @throws NotFoundException
     * @return file
     */
//    public function downloadProject($projectId = null) {
//        return $this->downloadProjectParent($projectId);
//    }

    /**
     * deleteFile method
     * Esta funcion es la encargada de eliminar los ficheros en la BD
     * @param int $id ProjectResource id
     * @param int $projectId id del projectoo
     * @return json_array estado de la eliminacion (ver appcontroller correctResponseJson())
     */
    public function deleteFile($id = null, $project_id = null) {
        if (!$this->ProjectResource->Project->exists($project_id)) {
            return $this->correctResponseJson(json_encode(array('success' => false)));
        }
        if ($this->request->is("delete")) {
            $user_id = $this->Session->read('user_id');
            $group_id = $this->Session->read('group_id');

            if ($group_id == 1) {

                $this->ProjectResource->id = $id;
                $this->request->onlyAllow('post', 'delete');
                if ($this->ProjectResource->delete()) {
                    return $this->correctResponseJson(json_encode(array('success' => true)));
                } else {
                    return $this->correctResponseJson(json_encode(array('success' => false)));
                }
            }
        }
    }

    public function downloadAll($id) {

        if (!$this->ProjectResource->Project->exists($id)) {
            throw new NotFoundException(__('Invalid project'));
        }
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');


        $group_id = $this->Session->read('group_id');

        $this->RequestHandler = $this->Components->load('RequestHandler');
        $options = array(
            'conditions' => array('project_id' => $id),
            'recursive' => -1,
        );

        $resources = $this->ProjectResource->find('all', $options);

        $downloadPath = Configure::read('downloadFilesPath') . DS . uniqid();
        $packet = new Folder($downloadPath, true, 0700);
        if ($packet->create('')) {
            //si se puede crear la carpeta
            //creamos una carpeta temporal
            $zip = new ZipArchive;
            $packetName = 'Guidelines&resources.zip';
            if (!$zip->open($packet->pwd() . DS . $packetName, ZipArchive::CREATE)) {
                throw new Exception("Failed to create archive\n");
            }
            if (!empty($resources)) {
                foreach ($resources as $resource) {
                    $fileName = $resource['ProjectResource']['name'] . "." . $resource['ProjectResource']['extension'];
                    $file = new File($packet->pwd() . DS . $fileName, 600);
                    if ($file->exists()) {
                        $file->write($resource['ProjectResource']['file']);
                        $file->close();
                        //debug($file->path);
                        $zip->addFile($file->path, ltrim($fileName, '/'));
                    } else {
                        throw new Exception("Error creating files ");
                    }
                }
            }
            else
            {
                $zip->addFromString('empty.txt', 'No files');
            }
            $zip->close();
            if (!$zip->status == ZIPARCHIVE::ER_OK) {
                throw new Exception("Error creating zip ");
            }
            $zipFolder = new File($packet->pwd() . DS . $packetName);
            $packetToDownload = $zipFolder->read();
            $zipFolder->close();
            if (!$packet->delete()) {
                throw new Exception("Error delete zip ");
            }
            $mimeExtension = 'application/zip';
            $this->autoRender = false;
            $this->response->type($mimeExtension);
            $this->response->body($packetToDownload);
            $this->response->download($packetName);
            return $this->response;
        } else {
            throw new Exception("Error creating folsder ");
        }
    }

    /**
     * mimeToExtension method
     * Esta funcion devuelve la extension para un valor mime dado
     * @param string $string valor mime
     * @return string extension
     */
    public function mimeToExtension($string) {
        $map = Array('application/andrew-inset' => 'ez',
            'application/atom+xml' => 'atom',
            'application/java-archive' => 'jar',
            'application/mac-binhex40' => 'hqx',
            'application/mac-compactpro' => 'cpt',
            'application/mathml+xml' => 'mathml',
            'application/msword' => 'doc',
            'application/octet-stream' => 'rar',
            'application/oda' => 'oda',
            'application/ogg' => 'ogg',
            'application/pdf' => 'pdf',
            'application/postscript' => 'ps',
            'application/rdf+xml' => 'rdf',
            'application/rss+xml' => 'rss',
            'application/smil' => 'smil',
            'application/srgs' => 'gram',
            'application/srgs+xml' => 'grxml',
            'application/vnd.google-earth.kml+xml' => 'kml',
            'application/vnd.google-earth.kmz' => 'kmz',
            'application/vnd.mif' => 'mif',
            'application/vnd.mozilla.xul+xml' => 'xul',
            'application/vnd.ms-excel' => 'xlt',
            'application/vnd.ms-excel.addin.macroEnabled.12' => 'xlam',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'xlsb',
            'application/vnd.ms-excel.sheet.macroEnabled.12' => 'xlsm',
            'application/vnd.ms-excel.template.macroEnabled.12' => 'xltm',
            'application/vnd.ms-word.document.macroEnabled.12' => 'docm',
            'application/vnd.ms-word.template.macroEnabled.12' => 'dotm',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12' => 'ppam',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => 'pptm',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => 'ppsm',
            'application/vnd.ms-powerpoint.template.macroEnabled.12' => 'potm',
            'application/vnd.ms-powerpoint' => 'pps',
            'application/vnd.oasis.opendocument.chart' => 'odc',
            'application/vnd.oasis.opendocument.database' => 'odb',
            'application/vnd.oasis.opendocument.formula' => 'odf',
            'application/vnd.oasis.opendocument.graphics' => 'odg',
            'application/vnd.oasis.opendocument.graphics-template' => 'otg',
            'application/vnd.oasis.opendocument.image' => 'odi',
            'application/vnd.oasis.opendocument.presentation' => 'odp',
            'application/vnd.oasis.opendocument.presentation-template' => 'otp',
            'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
            'application/vnd.oasis.opendocument.spreadsheet-template' => 'ots',
            'application/vnd.oasis.opendocument.text' => 'odt',
            'application/vnd.oasis.opendocument.text-master' => 'odm',
            'application/vnd.oasis.opendocument.text-template' => 'ott',
            'application/vnd.oasis.opendocument.text-web' => 'oth',
            'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx',
            'application/vnd.visio' => 'vsd',
            'application/vnd.wap.wbxml' => 'wbxml',
            'application/vnd.wap.wmlc' => 'wmlc',
            'application/vnd.wap.wmlscriptc' => 'wmlsc',
            'application/voicexml+xml' => 'vxml',
            'application/x-bcpio' => 'bcpio',
            'application/x-cdlink' => 'vcd',
            'application/x-chess-pgn' => 'pgn',
            'application/x-cpio' => 'cpio',
            'application/x-csh' => 'csh',
            'application/x-director' => 'dxr',
            'application/x-dvi' => 'dvi',
            'application/x-futuresplash' => 'spl',
            'application/x-gtar' => 'gtar',
            'application/x-hdf' => 'hdf',
            'application/x-javascript' => 'js',
            'application/x-koan' => 'skm',
            'application/x-latex' => 'latex',
            'application/x-netcdf' => 'cdf',
            'application/x-sh' => 'sh',
            'application/x-shar' => 'shar',
            'application/x-shockwave-flash' => 'swf',
            'application/x-stuffit' => 'sit',
            'application/x-sv4cpio' => 'sv4cpio',
            'application/x-sv4crc' => 'sv4crc',
            'application/x-tar' => 'tar',
            'application/x-tcl' => 'tcl',
            'application/x-tex' => 'tex',
            'application/x-texinfo' => 'texi',
            'application/x-troff' => 'roff',
            'application/x-troff-man' => 'man',
            'application/x-troff-me' => 'me',
            'application/x-troff-ms' => 'ms',
            'application/x-ustar' => 'ustar',
            'application/x-wais-source' => 'src',
            'application/xhtml+xml' => 'xht',
            'application/xslt+xml' => 'xslt',
            'application/xml' => 'xsl',
            'application/xml-dtd' => 'dtd',
            'application/zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'audio/basic' => 'snd',
            'audio/midi' => 'kar',
            'audio/mpeg' => 'mp3',
            'audio/x-aiff' => 'aifc',
            'audio/x-mpegurl' => 'm3u',
            'audio/x-ms-wma' => 'wma',
            'audio/x-ms-wax' => 'wax',
            'audio/x-pn-realaudio' => 'ra',
            'application/vnd.rn-realmedia' => 'rm',
            'audio/x-wav' => 'wav',
            'chemical/x-pdb' => 'pdb',
            'chemical/x-xyz' => 'xyz',
            'image/bmp' => 'bmp',
            'image/cgm' => 'cgm',
            'image/gif' => 'gif',
            'image/ief' => 'ief',
            'image/jpeg' => 'jpe',
            'image/png' => 'png',
            'image/svg+xml' => 'svg',
            'image/tiff' => 'tif',
            'image/vnd.djvu' => 'djv',
            'image/vnd.wap.wbmp' => 'wbmp',
            'image/x-cmu-raster' => 'ras',
            'image/x-icon' => 'ico',
            'image/x-portable-anymap' => 'pnm',
            'image/x-portable-bitmap' => 'pbm',
            'image/x-portable-graymap' => 'pgm',
            'image/x-portable-pixmap' => 'ppm',
            'image/x-rgb' => 'rgb',
            'image/x-xbitmap' => 'xbm',
            'image/x-photoshop' => 'psd',
            'image/x-xpixmap' => 'xpm',
            'image/x-xwindowdump' => 'xwd',
            'message/rfc822' => 'eml',
            'model/iges' => 'iges',
            'model/mesh' => 'silo',
            'model/vrml' => 'vrml',
            'text/calendar' => 'ifb',
            'text/css' => 'css',
            'text/csv' => 'csv',
            'text/html' => 'htm',
            'text/plain' => 'txt',
            'text/richtext' => 'rtx',
            'text/rtf' => 'rtf',
            'text/sgml' => 'sgm',
            'text/xml' => 'xml',
            'text/tab-separated-values' => 'tsv',
            'text/vnd.wap.wml' => 'wml',
            'text/vnd.wap.wmlscript' => 'wmls',
            'text/x-setext' => 'etx',
            'video/mpeg' => 'mpe',
            'video/quicktime' => 'mov',
            'video/vnd.mpegurl' => 'm4u',
            'video/x-flv' => 'flv',
            'video/x-ms-asf' => 'asx',
            'video/x-ms-wmv' => 'wmv',
            'video/x-ms-wm' => 'wm',
            'video/x-ms-wmx' => 'wmx',
            'video/x-msvideo' => 'avi',
            'video/ogg' => 'ogv',
            'video/x-sgi-movie' => 'movie',
            'x-conference/x-cooltalk' => 'ice');

        if (isset($map[$string]))
            $return = $map[$string];
        else
            $return = null;

        return $return;
    }

    

}
