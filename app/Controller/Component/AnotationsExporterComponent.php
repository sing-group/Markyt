<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * CakePHP Component
 * @author Sing-pc
 */
class AnotationsExporterComponent extends Component {
    //    public $components = array();
    public $components = array('Session');
    private $Model;
    private $Controller;
    private $request;
    private $modelAlias;
    private $format;
    public $appendDocumentsText = true;
    public function __construct(ComponentCollection $collection, $settings = array()) {
        $settings = array_merge($this->settings, (array) $settings);
        $this->Controller = $collection->getController();
        $this->Model = $this->Controller->{$this->Controller->modelClass};
        $this->modelAlias = $this->Model->alias;
        parent::__construct($collection, $settings);
    }
    public function setModel($modelString) {
        $this->Model = $modelString;
    }
    public function format($format) {
        $this->format = strtoupper($format);
    }
    public function export(&$annotations, &$documentsList, &$typeList, &$relations = array()) {
        ini_set('memory_limit', Configure::read('scriptMemoryLimit'));
        set_time_limit(Configure::read('scriptTimeLimit'));
        session_write_close();
        switch ($this->format) {
            case "TSV":
                return $this->annotationsToTSV($annotations, $documentsList, $typeList);
                break;
            case "BIOCTSV":
                return $this->annotationsToBIOCTSV($annotations, $documentsList, $typeList);
                break;
            case "BIOC":
                return $this->annotationsToBioC($annotations, $documentsList, $typeList, $relations);
                break;
            case "JSON":
                return $this->annotationsToJson($annotations, $documentsList, $typeList, $relations);
                break;
            case "BIOCJSON":
                return $this->annotationsToBioCJson($annotations, $documentsList, $typeList);
                break;
            case "PUB":
                return $this->annotationsToPubannotation($annotations, $documentsList, $typeList);
                break;
            default :
                throw new Exception("Incorrect parser format: " . $this->format);
                break;
        }
    }
    private function annotationsToTSV(&$annotations, &$documentsList, &$typeList) {
        $content = "DOCUMENT_ID\tSECTION\tINIT\tEND\tSCORE\tANNOTATED_TEXT\tTYPE\tDATABASE_ID\n";
        $size = count($annotations);
        for ($i = 0; $i < $size; $i++) {
            $annotation = $annotations[$i];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $document = $documentsList[$annotation['document_id']];
            if (is_array($document)) {
                $document = $document["external_id"];
            }
            $content .= $document . "\t";
            $type = $typeList[$annotation['type_id']];
            if (isset($annotation['section'])) {
                $content .= $annotation['section'] . "\t";
            }
            if (isset($annotation['init'])) {
                $content .= $annotation['init'] . "\t";
            }
            if (isset($annotation['end'])) {
                $content .= $annotation['end'] . "\t";
            }
            if (isset($annotation['score'])) {
                $content .= $annotation['score'] . "\t";
            }
            $content .= $annotation['annotated_text'] . "\t";
            $content .= $type . "\t";
            if (isset($annotation['database_id'])) {
                $content .= $annotation['database_id'] . "\t";
            }
            $content .= "\n";
        }
        return $content;
    }
    private function annotationsToBIOCTSV(&$annotations, &$documentsList, &$typeList) {
        $content = "";
        $size = count($annotations);
        $cont = 1;
        for ($i = 0; $i < $size; $i++) {
            $annotation = $annotations[$i];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $document = $documentsList[$annotation['document_id']];
            $type = $typeList[$annotation['type_id']];
            $content .= $document . "\t";
            if (isset($annotation['section'])) {
                $content .= $annotation['section'] . ":";
            }
            if (isset($annotation['init'])) {
                $content .= $annotation['init'] . ":";
            }
            if (isset($annotation['end'])) {
                $content .= $annotation['end'] . "\t";
            }
            $content .= $i + 1 . "\t";
            if (isset($annotation['score'])) {
                $content .= $annotation['score'] . "\t";
            }
            $content .= $annotation['annotated_text'] . "\t\n";
        }
        return $content;
    }
    private function annotationsToJson(&$annotations, &$documentsList, &$typeList, &$relations = array()) {
        $content = "";
        $size = count($annotations);
        for ($i = 0; $i < $size; $i++) {
            $annotation = $annotations[$i];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $annotation['document_id'] = $documentsList[$annotation['document_id']];
            $annotation['type'] = $typeList[$annotation['type_id']];
            unset($annotation['id']);
            unset($annotation['user_id']);
            unset($annotation['prediction_upload_id']);
            unset($annotation['prediction_request_id']);
            unset($annotation['server_id']);
            unset($annotation['server_version_id']);
            unset($annotation['length']);
            unset($annotation['type_id']);
            $annotation["init"] = (int) $annotation["init"];
            $annotation["end"] = (int) $annotation["end"];
            if (!isset($annotation["score"])) {
                $annotation["score"] = null;
            }
            $annotation["score"] = (int) $annotation["score"];
            $annotations[$i] = $annotation;
        }
        $size = count($relations);
        for ($i = 0; $i < $size; $i++) {
            $relations[$i]["Annotation_A"]['document_id'] = $documentsList[$relations[$i]["Annotation_A"]['document_id']];
            $relations[$i]["Annotation_B"]['document_id'] = $documentsList[$relations[$i]["Annotation_B"]['document_id']];
        }
        return json_encode(array("entities" => $annotations, "relations" => $relations), JSON_PRETTY_PRINT);
    }
    private function annotationsToBioC(&$annotations = array(), &$documents = array(), &$types = array(), $relations = array(), $source = "PUBMED") {
        ini_set('memory_limit', Configure::read('scriptMemoryLimit'));
        set_time_limit(Configure::read('scriptTimeLimit'));
        $dateFormat = Configure::read('dateFormat');
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><!DOCTYPE collection SYSTEM \"BioC.dtd\"><collection></collection>");
        $xml->addChild('source', $source);
        $xml->addChild('date', date($dateFormat));
        $xml->addChild('key', "BioC_" . uniqid());
        $documentPassages = array();
        foreach ($documents as $document) {
            $documentXML = $xml->addChild('document');
            $documentXML->addChild('id', $document["external_id"]);
            $title = $document["title"];
            $abstract = $document["abstract"];
            if (!$this->appendDocumentsText) {
                $abstract = "DISABLED";
                $title = "DISABLED";
            }
            $titlePassage = $documentXML->addChild('passage');
            $titlePassage->addChild('infon', 'title')->addAttribute("key", "type");
            $titlePassage->addChild('offset', 0);
            $titlePassage->addChild('text', htmlspecialchars($title));
            $abstractPassage = $documentXML->addChild('passage');
            $abstractPassage->addChild('infon', 'abstract')->addAttribute("key", "type");
            $abstractPassage->addChild('offset', strlen($document["title"]) + 1);
            $abstractPassage->addChild('text', htmlspecialchars($abstract));
            $documentId = $document["id"];
            $documentPassages[$documentId]["T"] = array("passageObject" => $titlePassage);
            $documentPassages[$documentId]["A"] = array("passageObject" => $abstractPassage);
            if (isset($relations[$documentId])) {
                $relationsInDocument = $relations[$documentId];
                foreach ($relationsInDocument as $relation) {
                    if ($relation["Annotation_A"]["section"] == 'T') {
                        $passage = $titlePassage;
                    } else {
                        $passage = $abstractPassage;
                    }
                    $relationXML = $passage->addChild("relation");
                    $relationXML->addAttribute("id", $relation["Relation"]["Rid"]);
                    $relationXML->addChild('infon', $relation["Relation"]["name"])->addAttribute("key", "relation");
                    $annotA = $relationXML->addChild('node', $relation["Annotation_A"]["annotated_text"]);
                    $annotA->addAttribute("role", $types[$relation["Annotation_A"]["type_id"]]);
                    $annotA->addAttribute("refid", $relation["Annotation_A"]["id"]);
                    $annotB = $relationXML->addChild('node', $relation["Annotation_B"]["annotated_text"]);
                    $annotB->addAttribute("role", $types[$relation["Annotation_B"]["type_id"]]);
                    $annotB->addAttribute("refid", $relation["Annotation_B"]["id"]);
                }
            }
        }
        $documents = null;
        $size = count($annotations);
        for ($index = 0; $index < $size; $index++) {
            $annotation = $annotations[$index];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $documentId = $annotation["document_id"];
            $section = ($annotation["section"] != '' ? strtoupper($annotation["section"]) : null);
            $mapAnnotationTextToObject = array();
            if (isset($section) && isset($documentPassages[$documentId][$section])) {
                $text = $annotation["text"];
                $typeId = $annotation["type_id"];
                $passage = $documentPassages[$documentId][$section]["passageObject"];
                if (!isset($documentPassages[$documentId][$section]["annotations"][$typeId . $text])) {
                    $annotationXML = $passage->addChild("annotation");
                    $annotationXML->addAttribute("id", $annotation["id"]);
                    $annotationXML->addChild('infon', $types[$annotation["type_id"]])->addAttribute("key", 'type');
                    $annotationXML->addChild('text', htmlspecialchars($annotation["text"]));
                    $location = $annotationXML->addChild('location');
                    $location->addAttribute("offset", $annotation["offset"]);
                    $location->addAttribute("length", $annotation["length"]);
                    $documentPassages[$documentId][$section]["annotations"] = array(
                          $typeId . $text => $annotationXML);
                } else {
                    $annotationXML = $documentPassages[$documentId][$section]["annotations"][$typeId . $text];
                    $location = $annotationXML->addChild('location');
                    $location->addAttribute("offset", $annotation["offset"]);
                    $location->addAttribute("length", $annotation["length"]);
                }
            }
            $annotations[$index] = null;
        }
        //cear space
        $annotations = null;
        $documentPassages = null;
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $content = $dom->saveXML();
        return $content;
    }
    private function annotationsToBioCJson(&$annotations = array(), &$documents = array(), &$types = array(), $source = "PUBMED") {
        ini_set('memory_limit', Configure::read('scriptMemoryLimit'));
        set_time_limit(Configure::read('scriptTimeLimit'));
        $dateFormat = Configure::read('dateFormat');
        $content = array();
        $content['source'] = $source;
        $content['date'] = date($dateFormat);
        $content['key'] = "BioC_" . uniqid();
        $content['documents'] = [];
        $documentsJson = [];
        foreach ($documents as $document) {
            $documentId = $document["id"];
            $abstract = htmlspecialchars($document["abstract"]);
            $title = htmlspecialchars($document["title"]);
            $abstractSize = strlen($abstract);
            if (!$this->appendDocumentsText) {
                $abstract = "DISABLED";
                $title = "DISABLED";
            }
            $documentsJson[$documentId] = array(
                  "id" => $document["external_id"],
                  "passages" => array(
                        array(
                              "offset" => 0,
                              "text" => $title,
                              "infons" => array(
                                    "type" => "title"
                              ),
                              "annotations" => []
                        ),
                        array(
                              "offset" => $abstractSize + 1,
                              "text" => $abstract,
                              "infons" => array(
                                    "type" => "abstract"
                              ),
                              "annotations" => []
                        ),
                  )
            );
            ;
        }
        $documents = null;
        $cont = 0;
        $size = count($annotations);
        $multiSpanAnnotation = array();
        for ($index = 0; $index < $size; $index++) {
            $annotation = $annotations[$index];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $documentId = $annotation["document_id"];
            $section = ($annotation["section"] != '' ? strtoupper($annotation["section"]) : null);
            $section = ($section == 'T' ? 0 : 1);
            if (isset($section) && isset($documentsJson[$documentId]) && isset($documentsJson[$documentId]["passages"][$section])) {
                $cont++;
                $type = $annotation["type_id"];
                $text = $annotation["text"];
                if (!isset($multiSpanAnnotation[$documentId . $section . $type . $text])) {
                    $multiSpanAnnotation[$documentId . $section . $type . $text] = array(
                          "id" => $annotation["id"],
                          "infons" => array(
                                "type" => $types[$type]
                          ),
                          "text" => htmlspecialchars($annotation["text"]),
                          "locations" => array(array(
                                      "offset" => intval($annotation["offset"]),
                                      "length" => intval($annotation["length"]),
                                )),
                    );
                    $documentsJson[$documentId]["passages"][$section]["annotations"][] = &$multiSpanAnnotation[$documentId . $section . $type . $text];
                } else {
                    array_push($multiSpanAnnotation[$documentId . $section . $type . $text]["locations"], array(
                          "offset" => intval($annotation["offset"]),
                          "length" => intval($annotation["length"]),
                    ));
                }
            }
            $annotations[$index] = null;
        }
        $multiSpanAnnotation = null;
        foreach ($documentsJson as $id => $document) {
            if (!empty($document["passages"][0]["annotations"]) && !empty($document["passages"][1]["annotations"])) {
                $content["documents"][] = $document;
            }
        }
        $documentsJson = null;
        return json_encode($content, JSON_PRETTY_PRINT);
    }
    private function annotationsToPubannotation(&$annotations, &$documentsList, &$typeList, $source = "PUBMED") {
        $content = "";
        $size = count($annotations);
        $pubannotations = array();
        $titleCount = 0;
        $abstractCount = 0;
        for ($i = 0; $i < $size; $i++) {
            $annotation = $annotations[$i];
            if (is_string($this->Model)) {
                $annotation = $annotation[$this->Model];
            } else {
                $annotation = $annotation[$this->Model->name];
            }
            $documentId = $annotation['document_id'];
            $document = $documentsList[$documentId];
            $type = $typeList[$annotation['type_id']];
            $title = $document["title"];
            $abstract = $document["abstract"];
            if (!$this->appendDocumentsText) {
                $abstract = "";
                $title = "";
            }
            if (!isset($pubannotation[$documentId])) {
                $pubannotations[$documentId] = array("sourcedb" => $source, "sourceId" => $document["external_id"],
                      "text" => $title . " " . $abstract, "denotations" => array());
            }
            $num = 0;
            if ($annotation['section'] == 'T') {
                $num = $titleCount;
                $titleCount++;
            } else {
                $num = $abstractCount;
                $abstractCount++;
            }
            $obj = $type;
            if (isset($annotation['database_id']) && $annotation['database_id'] != "") {
                $obj .= ":" . $annotation['database_id'];
            }
            $pubAnnotation = array("id" => $annotation['section'] . $num, "span" => array(
                        "begin" => $annotation['init'], "end" => $annotation['end']),
                  "obj" => $obj);
            array_push($pubannotations[$documentId]["denotations"], $pubAnnotation);
        }
        return json_encode(array_values($pubannotations), JSON_PRETTY_PRINT);
    }
}
