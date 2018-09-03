<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $helpers = array(
          'Session');
    public $components = array(
          'Session',
          'Auth' => array(
                'loginRedirect' => array(
                      'controller' => 'posts',
                      'action' => 'publicIndex'),
                //'loginAction' => array('controller' => 'posts', 'action' => 'publicIndex'),
                'authError' => 'You are not authorized to access this location'));

    public function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    /**
     * randomAlphaNum method
     * @param int $length
     * @return String
     */
    function randomAlphaNum($length = null) {
        if ($length == null)
            $length = rand(8, 15);
        $a_z = "!$&/()=?:;-*+";
        $int = rand(0, 12);
        $unique_key = substr(md5(rand(0, 1000000)), 0, $length) . $a_z[$int] . substr(md5(rand(0, 1000000)), 0, rand(0, 3));
        return $unique_key;
    }

    /**
     * backGround method
     * @param Array $location
     * @return void
     */
    public function backGround($location = null) {
        $redirect = $this->Session->read('redirect');
        $scriptTimeLimit = Configure::read('scriptTimeLimit');
        set_time_limit($scriptTimeLimit);
        if (!$this->request->is('ajax')) {
            if (isset($redirect) && is_array($redirect)) {
                if ($redirect['action'] == 'view' && $redirect['controller'] != 'projects') {
                    $redirect['action'] = 'index';
                    unset($redirect[0]);
                }
                header("Location: " . Router::url(($redirect)), true);
            } else if (isset($location)) {
                header("Location: " . $location);
            }
        }
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        header("Content-Length: 0");
        ob_end_flush();
        flush();
        session_write_close();
    }

    public function correctResponseJson($response) {
        $this->response->header(array(
              "Pragma" => "no-cache",
        ));
        $this->response->expires(0);
        $this->response->disableCache();
        $this->autoRender = false;
        if (isset($response)) {
            if (is_array($response)) {
                $response = json_encode($response);
            }
            $this->response->body($response);
        } else {
            $this->response->body('');
        }
        $this->response->type('json');
        return $this->response;
    }

    public function exportTsvDocument($lines = array(), $name = "export.tsv") {
        $response = "";
        foreach ($lines as $line) {
            $response .= $line . "\n";
        }
        $mimeExtension = 'text/tab-separated-values';
        $this->response->type($mimeExtension);
        $this->response->body($response);
        $this->response->download($name);
        $this->autoRender = false;
        return $this->response;
    }

    public function sendMailWithAttachment($template = null, $to_email = null, $subject = null, $contents = array(), $attachments = array()) {
        App::uses('CakeEmail', 'Network/Email');
        $emailProfile = Configure::read('emailProfile');
        $from_email = 'markyt.noreplay@gmail.com';
        $email = new CakeEmail($emailProfile);
        $result = $email
            ->to($to_email)
            ->template($template)
            ->emailFormat('html')
            ->from($from_email)
            ->subject($subject)
            ->attachments($attachments)
            ->viewVars($contents);
        if ($email->send()) {
            return true;
        }
        return false;
    }

    public function cleanLessCharacter(&$content) {
        $end = false;
        $regex = "/<(\d|[\-=_!\"#%&'*{},.:;?\(\)\[\]@\\$\^*+<>~`^_`{\|}~\]])/miu";
        $content = preg_replace($regex, "≺$1", trim($content));
        $regex = "/<([^>]*(<|$))/miu";
        preg_match($regex, $content, $matches);
        $end = empty($matches);
        while (!$end) {
            preg_match($regex, $content, $matches);
            $end = empty($matches);
            $content = preg_replace($regex, "≺$1", trim($content));
        }
        return $content;
    }

    public function parseHtmlToGetAnnotations($content = null) {
        $content = $this->cleanLessCharacter($content);
        $content = strip_tags($content, '<mark>');
        $codification = mb_detect_encoding($content, mb_detect_order());
        if ($codification != "UTF-8")
            $content = iconv($codification, "UTF-8//TRANSLIT", $content);
        return $content;
    }

    function getAnnotations($documentContent = null, $document_id = null, $countTitleSize = true) {
        $allAnnotations = array();
        $parseKey = Configure::read('parseKey');
        $classKey = Configure::read('classKey');
        $parseIdAttr = Configure::read('parseIdAttr');
        if (isset($documentContent) && $documentContent != '') { // nos ahorramos tran
            $documentContent = preg_replace("/(?i)(<mark[^>]*>)(\s)(<\/mark>)/", "$1&nbsp;$3", $documentContent);
            $totalAnnotations = substr_count($documentContent, "<mark");
            $headerEndPosition = strpos($documentContent, "</h3>") + 4;
            $titleSize = 0;
            if ($countTitleSize)
                $titleSize = mb_strlen(trim($this->entityStripTags(substr($documentContent, 0, $headerEndPosition))));
            $documentContent = $this->cleanLessCharacter($documentContent);
            $dom = new DOMDocument();
            $markRepresentation = new DOMDocument('5.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = true;
            $dom->loadHTML(
                '<!DOCTYPE html>'
                . '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head><body>'
                . $documentContent
                . '</body></html>'
            );
            libxml_clear_errors();
            $documentContent = $this->parseHtmlToGetAnnotations($documentContent);
            $marks = $dom->getElementsByTagName('mark');
            $matches = array();
            foreach ($marks as $mark) {
                $newdoc = new DOMDocument('5.0', 'UTF-8');
                $text = $mark->nodeValue;
                $cloned = $mark->cloneNode(TRUE);
                $newdoc->appendChild($newdoc->importNode($cloned, TRUE));
                $html = $newdoc->saveHTML();
                array_push($matches, array(
                      "tag" => trim(substr($html, 0, strpos($html, ">") + 1)),
                      "text" => $text, "id" => $mark->getAttribute($parseIdAttr),
                      "type" => $mark->getAttribute("data-type-id")
                ));
            }
            $size = sizeof($matches);
            if ($size != 0) {
                for ($i = 0; $i < $size; $i++) {
                    $mark = $matches[$i];
                    $texto = $mark['text'];
                    $isAutomatic = strpos($mark['tag'], "automatic");
                    $mode = 0;
                    if ($isAutomatic) {
                        $mode = 2;
                    }
                    $insertID = $mark['id'];
                    if (!isset($allAnnotations[$insertID])) {
                        $markPosition = strpos($documentContent, $mark['tag']);
                        if ($markPosition === false) {
                            debug($mark['tag']);
                            debug($documentContent);
                            debug(strpos($documentContent, $mark['tag']));
                            debug(substr($documentContent, 0, strpos($documentContent, $mark['tag'])));
                            throw new Exception("Mark not found: " . $mark['tag']);
                        }
                        $substring = substr($documentContent, 0, $markPosition);
                        $substring = html_entity_decode(strip_tags($substring), ENT_COMPAT | ENT_HTML5, 'UTF-8');
                        $original_start = mb_strlen($substring);
                        if ($mark['type'] != '') {
                            $type = $mark['type'];
                        } else {
                            preg_match('/[^>]*class=.?(\w*).?[^>]/', $mark['tag'], $type);
                            $type = str_replace($classKey, '', $type[1]);
                        }
                        $section = 'A';
                        if ($original_start < $titleSize) {
                            $section = 'T';
                        } else {
                            $original_start = $original_start - $titleSize - 1;
                        }
                        $allAnnotations[$insertID] = array(
                              'init' => $original_start,
                              'annotated_text' => $texto,
                              'type_id' => $type,
                              'document_id' => $document_id,
                              'id' => $insertID,
                              'section' => $section
                        );
                    } else {
                        $allAnnotations[$insertID]['annotated_text'] .= $texto;
                        $allAnnotations[$insertID]['nested'] = true;
                    }
                }
                foreach ($allAnnotations as $key => $value) {
                    $initialSpaces = mb_strlen($value['annotated_text']) - mb_strlen(ltrim($value['annotated_text']));
                    if ($initialSpaces != 0) {
                        $allAnnotations[$key]['init'] += $initialSpaces;
                    }
                    $allAnnotations[$key]['end'] = $allAnnotations[$key]['init'] + mb_strlen(trim($value['annotated_text']));
                }
            }
        } //sizeof($matches[1]) != 0
        return $allAnnotations;
    }

    function entityStripTags($content) {
        return html_entity_decode(strip_tags($content), ENT_COMPAT | ENT_HTML5, 'UTF-8');
    }

    public function filesize2bytes($str) {
        $bytes = 0;
        $bytes_array = array(
              'B' => 1,
              'KB' => 1024,
              'MB' => 1024 * 1024,
              'GB' => 1024 * 1024 * 1024,
              'TB' => 1024 * 1024 * 1024 * 1024,
              'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
        );
        $bytes = floatval($str);
        if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[$matches[1]])) {
            $bytes *= $bytes_array[$matches[1]];
        }
        $bytes = intval(round($bytes, 2));
        return $bytes;
    }

    public function bytesToHuman($size, $unit = "") {
        if ((!$unit && $size >= 1 << 30) || $unit == "TB")
            return number_format($size / (1 << 30), 2) . "TB";
        if ((!$unit && $size >= 1 << 20) || $unit == "GB")
            return number_format($size / (1 << 20), 2) . "GB";
        if ((!$unit && $size >= 1 << 10) || $unit == "MB")
            return number_format($size / (1 << 10), 2) . "MB";
        return number_format($size) . " bytes";
    }

    public function correctTsvFormat($file, $columns) {
        $columns--;
        $lines = $this->getNumberOfLines($file->pwd());
        $tabs = $this->getNumberOfTabs($file->pwd());
        debug($lines);
        debug(($lines * $columns));
        debug(($tabs));
        debug(($lines * $columns) == $tabs);
        throw new Exception;
        return ($lines * $columns) == $tabs;
    }

    public function incorrecLineTsvFormat($file) {
        $content = $file->read();
        $file->close();
        $lines = explode("\n", $content);
        $incorrectFormat = empty($lines);
        $count = -1;
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
        return $count += 2;
    }

    private function getNumberOfLines($file) {
        $f = fopen($file, 'rb');
        $lines = 0;
        while (!feof($f)) {
            $line = fread($f, 8192);
            $lines += substr_count($line, "\n");
        }
        if (substr($line, -1) == "\n" && substr_count($line, "\n") > 1) {
            $lines--;
        }
        fclose($f);
        return $lines;
    }

    private function getNumberOfTabs($file) {
        $f = fopen($file, 'rb');
        $lines = 0;
        while (!feof($f)) {
            $lines += substr_count(fread($f, 8192), "\t");
        }
        fclose($f);
        return $lines;
    }

    public function killJob($id) {
        $this->loadModel('Job');
        $this->loadModel('UsersRound');
        $this->Job->id = $id;
        $job = $this->Job->read();
        $PID = $job["Job"]['PID'];
        $userIdJob = $job["Job"]['user_id'];
        $round_id = $job["Job"]['round_id'];
        $success = false;
        $group_id = $this->Session->read('group_id');
        $user_id = $this->Session->read('user_id');
        if (isset($PID) && $PID != '' && file_exists("/proc/$PID")) {
            $success = posix_kill($PID, 9);
            sleep(1);
            $success = !file_exists("/proc/$PID");
        } else {
            $success = true;
        }
        if ($success) {
            $usersRound = $this->UsersRound->find('first', array(
                  'recursive' => -1,
                  'fields' => "id",
                  'conditions' => array("user_id" => $userIdJob, "round_id" => $round_id)
            ));
            if (!empty($usersRound)) {
                $this->UsersRound->id = $usersRound["UsersRound"]["id"];
                $this->UsersRound->saveField('state', 0);
            }
            $this->Job->saveField('percentage', 100);
            $this->Job->saveField('status', "Canceled by user");
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => $success)));
    }

    public function sendJob($id, $programName, $arguments, $returnJson = true, $logName = "/dev/null") {
        $this->loadModel('Job');
        $runJava = Configure::read('runJava');
        $javaJarPath = Configure::read('javaJarPath');
        $javaProgram = "MARKYT_Scripts.jar";
        if ($programName != "importation.jar") {
            $javaProgram = "MARKYT_Scripts.jar";
        } else {
            $javaProgram = $programName;
        }
        $program = $javaJarPath . DS . $javaProgram;
        $javaLog = $javaJarPath . DS . "java.log";
        $date = date('Y-m-d H:i:s');
        exec("echo \"$date:$runJava $program $arguments\" >> $javaLog 2>&1 &");
        $PID = exec("$runJava $program $arguments > $logName 2>&1 & echo $!;");
        $this->Job->id = $id;
        $this->Job->set('PID', $PID);
        $this->Job->save(array('PID' => $PID, 'program' => $programName, "arguments" => $arguments));
        if ($returnJson) {
            return $this->correctResponseJson(json_encode(array(
                      'success' => true,
                      'PID' => $this->Job->id)));
        }
    }

    public function beforeFilter() {
        $theme = Configure::read('Theme');
        $this->theme = $theme;
        /* $this->Auth->allow(array('forward', 'processUrl')); */
        $this->Auth->allow(array(
              'controller' => 'pages',
              'action' => 'display',
              'markyInformation'));
        $this->Auth->allow('postsSearch', 'publicIndex', 'recoverAccount');
        $this->Auth->allow('login', 'register', 'Logout');
        $group = $this->Session->read('group_id');
        $controller = $this->request->params['controller'];
        $action = $this->request->params['action'];
        $controller = strtolower($controller);
        $deniedMessagge = 'You are not authorized to enter this area, your action has been reported';
        $deniedRedirect = array(
              'controller' => 'rounds',
              'action' => 'index');
        if ($group == 99) {
            $this->Session->destroy();
        }
        if (isset($group) && $group != 1) {
            switch ($controller) {
                case 'pages' :
                    break;
                case 'videos' :
                    break;
                case 'annotations' :
                    switch ($action) {
                        case 'redirectToAnnotatedDocument' :
                            break;
                    }
                    break;
                case 'annotationsquestions' :
                    break;
                case 'annotations_questions' :
                    break;
                case 'annotationsinterrelations' :
                    break;
                case 'annotations_inter_relations' :
                    break;
                case 'rounds' :
                    switch ($action) {
                        case 'user_view' :
                        case 'userView' :
                            break;
                        case 'index' :
                            break;
                        case 'search' :
                            break;
                        case 'getTypes' :
                            break;
                        case 'automaticAnnotation' :
                            break;
                        default :
                            $this->Session->setFlash($deniedMessagge);
                            $this->redirect($deniedRedirect);
                            break;
                    }
                    break;
                case 'users' :
                    switch ($action) {
                        case 'view' :
                            break;
                        case 'edit' :
                            break;
                        case 'login' :
                            break;
                        case 'logout' :
                            break;
                        case 'renewSession' :
                            break;
                        default :
                            $this->Session->setFlash($deniedMessagge);
                            $this->redirect($deniedRedirect);
                            break;
                    }
                    break;
                case 'annotateddocuments':
                case 'annotated_documents':
                case 'users_rounds' :
                case 'usersrounds' :
                    break;
                case 'projectresources' :
                    switch ($action) {
                        case 'downloadAll' :
                            break;
                    }
                    break;
                case 'documentsassessments' :
                    switch ($action) {
                        case 'view' :
                            break;
                        case 'save' :
                            break;
                    }
                    break;
                case 'projects' :
                    switch ($action) {
                        case 'index' :
                            break;
                        case 'search' :
                            break;
                        case 'userView' :
                            break;
                        case 'statisticsForUser' :
                            break;
                        default :
                            $this->Session->setFlash($deniedMessagge);
                            $this->redirect($deniedRedirect);
                            break;
                    }
                    break;
                default :
                    $this->Session->setFlash($deniedMessagge);
                    $this->redirect($deniedRedirect);
                    break;
            }
        } elseif (isset($group) && $group == 1) {
            $action = $this->request->params['action'];
            if ($action == 'view' && $controller != "usersrounds") {
                $redirect = array(
                      'controller' => $controller,
                      'action' => $action);
                if (!empty($this->request->params['pass'][0])) {
                    $redirect = array(
                          'controller' => $controller,
                          'action' => $action,
                          $this->request->params['pass'][0]);
                }
                $this->Session->write('redirect', $redirect);
            }
            if ($action == 'index') {
                $redirect = array(
                      'action' => 'index');
                $this->Session->write('redirect', $redirect);
                $this->Session->write('comesFrom', $redirect);
            }
        }
    }

    /* ============================ */
    /*          Relations           */
    /* ============================ */

    /**
     * [getNodeData description]
     * @param [type] $typesShapes [description]
     * @param [type] $annotation [description]
     * @param [type] $shapes [description]
     * @param [type] $nodeCss [description]
     * @param [type] $types [description]
     * @param [type] $nodesMap [description]
     * @param [type] $cont [description]
     * @param [type] $nodes [description]
     */
    public function getNodeData(&$typesShapes, &$annotation, &$shapes, &$nodeCss, &$types, &$nodesMap, &$annotationTypesDistribution, &$nodes) {
        $size = count($nodesMap);
        if (!isset($nodesMap[$annotation["annotated_text"]])) {
            if (!isset($typesShapes[$annotation["type_id"]])) {
                $shape = array_shift($shapes);
                $typesShapes[$annotation["type_id"]] = $shape;
                array_push($shapes, $shape);
            }
            if (!isset($annotationTypesDistribution[$annotation["type_id"]])) {
                $annotationTypesDistribution[$annotation["type_id"]] = 0;
            }
            $annotationTypesDistribution[$annotation["type_id"]] ++;
            $nodeCss["background-color"] = "rgba(" . $types[$annotation["type_id"]]["colour"] . ")";
            $nodeCss["shape"] = $typesShapes[$annotation["type_id"]];
            $nodesMap[$annotation["annotated_text"]] = $annotation["id"];
            $nodes[$annotation["annotated_text"]] = array("data" => array(
                        "id" => $annotation["id"],
                        "userId" => $annotation["user_id"],
                        "roundId" => $annotation["round_id"],
                        'weight' => 1,
                        "name" => $annotation["annotated_text"]),
                  "css" => $nodeCss);
        } else {
            $nodes[$annotation["annotated_text"]]["data"]['weight'] *= 1.05;
            if ($nodes[$annotation["annotated_text"]]["css"]['width'] < 2000) {
                $nodes[$annotation["annotated_text"]]["css"]['width'] *= 1.05;
                $nodes[$annotation["annotated_text"]]["css"]['height'] *= 1.05;
                $nodes[$annotation["annotated_text"]]["css"]['font-size'] += 2;
            }
        }
    }

}
