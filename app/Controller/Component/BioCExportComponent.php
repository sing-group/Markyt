<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BioCExport
 *
 * @author Sing-pc
 */


App::uses('Component', 'Controller');

class BioCExportComponent extends Component {

    public $components = array('Session');
    private $Model;
    private $Controller;
    private $request;
    private $modelAlias;

    public function __construct(ComponentCollection $collection, $settings = array()) {
        $settings = array_merge($this->settings, (array) $settings);
        $this->Controller = $collection->getController();
        $this->Model = $this->Controller->{$this->Controller->modelClass};
        $this->modelAlias = $this->Model->alias;
        parent::__construct($collection, $settings);
    }

    public function export(&$annotations = array(), &$documents = array(), &$types = array(), $source = "PUBMED") {
        ini_set("memory_limit", -1);
        set_time_limit(20);

        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><!DOCTYPE collection SYSTEM \"BioC.dtd\"><collection></collection>");
        $xml->addChild('source', 'Markyt');
        $xml->addChild('date', date("Y-m-d H:i:s"));


        $documentPassages = array();


        foreach ($documents as $document) {
            $documentXML = $xml->addChild('document');
            $documentXML->addChild('id', $document["external_id"]);

            $document["abstract"] = $document["html"];

            $titlePassage = $documentXML->addChild('passage');
            $titlePassage->addChild('infon', 'title')->addAttribute("key", "type");
            $titlePassage->addChild('offset', 0);
            $titlePassage->addChild('text', htmlspecialchars($document["title"]));

            $abstractPassage = $documentXML->addChild('passage');
            $abstractPassage->addChild('infon', 'abstract')->addAttribute("key", "type");
            $abstractPassage->addChild('offset', strlen($document["title"]) + 1);
            $abstractPassage->addChild('text', htmlspecialchars($document["abstract"]));

            $documentPassages[$document["id"]]["T"] = $titlePassage;
            $documentPassages[$document["id"]]["A"] = $abstractPassage;
        }

        foreach ($annotations as $annotation) {
            $documentId = $annotation["document_id"];
            $section = ($annotation["section"] != '' ? strtoupper($annotation["section"]) : null);


            if (isset($section) && isset($documentPassages[$documentId][$section])) {
                $passage = $documentPassages[$documentId][$section];
                $annotationXML = $passage->addChild("annotation");
                $annotationXML->addAttribute("key", $annotation["id"]);
                $annotationXML->addChild('infon', $types[$annotation["type_id"]])->addAttribute("key", 'type');

                $location = $annotationXML->addChild('location');
                $location->addAttribute("offset", $annotation["offset"]);
                $location->addAttribute("length", strlen($annotation["text"]));
                $annotationXML->addChild('text', htmlspecialchars($annotation["text"]));
            }
        }

        return $xml->asXML();
    }

}
