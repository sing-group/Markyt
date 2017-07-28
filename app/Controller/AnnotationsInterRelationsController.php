<?php
App::uses('AppController', 'Controller');
/**
 * AnnotationsInterRelations Controller
 *
 * @property AnnotationsInterRelation $AnnotationsInterRelation
 * @property PaginatorComponent $Paginator
 */
class AnnotationsInterRelationsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public function add() {
        if ($this->request->is('post')) {
            $this->Annotation = $this->AnnotationsInterRelation->Annotation;
            $this->Annotation->recursive = -1;
            $userId = $this->Session->read("user_id");
            $roundId = $this->Session->read("round_id");
            if (isset($this->request->data['id']) && $this->request->data['id'] == -1) {
                unset($this->request->data['id']);
            }
            if (isset($this->request->data['target']) && $this->request->data['target'] == $this->request->data['annotation_a_id']) {
                $this->request->data['annotation_a_id'] = $this->request->data['annotation_b_id'];
                $this->request->data['annotation_b_id'] = $this->request->data['target'];
            }
            $data = array("user_id" => $userId, "round_id" => $roundId, "id" => $this->request->data['annotation_a_id']);
            if (!$this->Annotation->hasAny($data)) {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "Ops! This annotations [A] is not in the database")));
            }
            $data = array("user_id" => $userId, "round_id" => $roundId, "id" => $this->request->data['annotation_b_id']);
            if (!$this->Annotation->hasAny($data)) {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "Ops! This annotations [B] is not in the database")));
            }
            $this->AnnotationsInterRelation->Relation->id = $this->request->data['relation_id'];
            if (!$this->AnnotationsInterRelation->Relation->exists()) {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "Ops! This Relation is not in the database")));
            }
            if ($this->AnnotationsInterRelation->save($this->request->data)) {
                $this->Annotation->id = $this->request->data['annotation_a_id'];
                $annotation = $this->Annotation->read();
                return $this->correctResponseJson(json_encode(array('success' => true,
                          'id' => $this->AnnotationsInterRelation->id)));
            } else {
                return $this->correctResponseJson(json_encode(array(
                          'success' => false,
                          'message' => "Ops! This relation could not be saved")));
            }
        }
        return $this->correctResponseJson(json_encode(array(
                  'success' => false,
                  'message' => "Ops! This relation could not be saved")));
    }
    public function saveComment() {
        $ids = $this->request->data['selected-items'];
        $comment = $this->request->data['comment'];
        $this->AnnotationsInterRelation->recursive = -1;
        App::uses('Sanitize', 'Utility');
        $comment = Sanitize::escape($comment);
        if ($this->AnnotationsInterRelation->updateAll(array("comment" => "'$comment'"), array(
                  "id" => $ids))) {
            return $this->correctResponseJson(json_encode(array('success' => true)));
        } else {
            return $this->correctResponseJson(json_encode(array(
                      'success' => false,
                      'message' => "Ops! The relation comment could not be saved")));
        }
    }
    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('name');
    }
}
