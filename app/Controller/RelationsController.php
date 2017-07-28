<?php

App::uses('AppController', 'Controller');

/**
 * Relations Controller
 *
 * @property Relation $Relation
 * @property PaginatorComponent $Paginator
 */
class RelationsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    /**
     * index method
     *
     * @return void
     */

    /**
     * add method
     *
     * @return void
     */
    public function add($projectId = null) {
        $this->Project = $this->Relation->Project;
        if ($this->request->is('post')) {
            $this->Relation->create();
            $db = $this->Relation->getDataSource();
            $db->begin();
            $commit = $this->Relation->save($this->request->data);
            $cont = $this->Relation->find('count', array(
                  'recursive' => -1, //int
                  'conditions' => array('project_id' => $projectId), //array of conditions
            ));
            if ($commit && $cont == 1) {
                $this->Project->id = $projectId;
                if ($this->Project->field("relation_level") == 0) {
                    $commit &= $this->Project->saveField("relation_level", 1);
                }
            }
            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__('The relation has been saved.'), 'success');
                return $this->redirect(array('controller' => 'projects', 'action' => 'view',
                          $projectId));
            } else {
                $db->rollback();
                $this->Session->setFlash(__('The relation could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('projectId'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Relation->exists($id)) {
            throw new NotFoundException(__('Invalid relation'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Relation->save($this->request->data)) {
                $this->Session->setFlash(__('The relation has been saved.'), 'success');
                return $this->redirect(array('controller' => 'projects', 'action' => 'view',
                          $this->request->data['Relation']['project_id']));
            } else {
                $this->Session->setFlash(__('The relation could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('id' => $id),
                  'recursive' => -1
            );
            $this->request->data = $this->Relation->find('first', $options);
        }
        $this->set(compact('projects', 'annotations'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->delete($id, 'name');
    }

    public function deleteSelected() {
        $this->CommonFunctions = $this->Components->load('CommonFunctions');
        $this->CommonFunctions->deleteSelected('name');
    }

    public function copy($project_id = null) {
        if ($this->request->is(array('post', 'put'))) {
            $sourceProject = $this->request->data['Relation']['source_project'];
            $detinationProject = $this->request->data['Relation']['detination_project'];
            if (!$this->Relation->Project->exists($this->request->data['Relation']['source_project'])) {
                throw new NotFoundException(__('Invalid Project'));
            }
            if (!$this->Relation->Project->exists($this->request->data['Relation']['detination_project'])) {
                throw new NotFoundException(__('Invalid Project'));
            }
            $db = $this->Relation->getDataSource();
            $options = array(
                  'table' => $db->fullTableName($this->Relation),
                  'alias' => 'Relation',
                  'recursive' => -1,
                  'fields' => array(
                        'name',
                        'colour',
                        "$detinationProject"
                  ),
                  'conditions' => array(
                        'project_id' => $sourceProject,
                  ),
            );
            //$this->ConsensusAnnotation->Annotation->find('all', $options);
            $commit = true;
            $db->begin();
            $query = $db->buildStatement($options, $this->Relation);
            $db->query('INSERT INTO ' . $db->fullTableName($this->Relation) . "(name,colour,project_id) " . $query);
            if ($commit) {
                $db->commit();
                $this->Session->setFlash(__('Relations has been saved.'), 'success');
            } else {
                $this->Session->setFlash(__('Relation could not be saved. Please, try again.'));
            }
            return $this->redirect(array('controller' => 'projects', 'action' => 'view',
                      $detinationProject));
        } else {
            throw new NotFoundException(__('Invalid Project'));
        }
    }

    public function export($round_id = null, $user_id = null) {
        $this->Project = $this->Relation->Project;
        $this->Round = $this->Project->Round;
        $this->User = $this->Project->User;
        $this->Type = $this->Project->Type;
        $scriptTimeLimit = Configure::read('scriptTimeLimit');
        set_time_limit($scriptTimeLimit);
        $scriptMemoryLimit = Configure::read('scriptMemoryLimit');
        ini_set('memory_limit', $scriptMemoryLimit);
        session_write_close();
        if (!$this->Round->exists($round_id)) {
            throw new NotFoundException(__('Invalid Round'));
        }
        if (!$this->User->exists($user_id)) {
            throw new NotFoundException(__('Invalid User'));
        }
        $round = $this->Round->find('first', array(
              'recursive' => -1,
              'conditions' => array(
                    'id' => $round_id
              )
        ));
        $project_id = $round['Round']['project_id'];
        $this->Project->id = $project_id;
        if (!$this->Project->exists()) {
            throw new NotFoundException(__('Invalid proyect'));
        } //!$this->Project->exists()
        $relationLevel = $this->Project->field('relation_level');
        $fields = array(
              'Relation.name',
              'Annotation_A.annotated_text',
              'Annotation_A.init',
              'Annotation_A.end',
              'Annotation_A.section',
              'Annotation_B.annotated_text',
              'Annotation_B.init',
              'Annotation_B.end',
              'Annotation_B.section',
              'Annotation_A.type_id',
              'Annotation_B.type_id',
              'Document.external_id',
              'Document.title'
        );
        $annotationsRelations = $this->Relation->find('all', array(
              'recursive' => -1,
              'fields' => $fields,
              'conditions' => array('Relation.project_id' => $project_id),
              'joins' => array(
                    array(
                          'table' => 'annotations_inter_relations',
                          'alias' => 'AnnotationsInterRelations',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Relation.id = AnnotationsInterRelations.relation_id'
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation_A',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Annotation_A.id = AnnotationsInterRelations.annotation_a_id',
                                'Annotation_A.round_id' => $round_id,
                                'Annotation_A.user_id' => $user_id,
                          )
                    ),
                    array(
                          'table' => 'documents',
                          'alias' => 'Document',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Document.id = Annotation_A.document_id'
                          )
                    ),
                    array(
                          'table' => 'annotations',
                          'alias' => 'Annotation_B',
                          'type' => 'INNER',
                          'conditions' => array(
                                'Annotation_B.id = AnnotationsInterRelations.annotation_b_id',
                                'Annotation_B.round_id' => $round_id,
                                'Annotation_B.user_id' => $user_id,
                          )
                    ),
              ),
        ));
        $types = $this->Type->find("list", array(
              "recursive" => -1,
              "fieds" => array("id", "name"),
              "conditions" => array("project_id" => $project_id),
        ));
        $header = array('document_id',
              'Annotation_A_text',
              'Annotation_A_section',
              'Annotation_A_init',
              'Annotation_A_end',
              'Annotation_A_type',
              'Relation',
              'Annotation_B_text',
              'Annotation_B_section',
              'Annotation_B_init',
              'Annotation_B_end',
              'Annotation_B_type');
        if ($relationLevel == 2) {
            $header = array('document_id',
                  'Annotation_A_text',
                  'Annotation_A_type',
                  'Relation',
                  'Annotation_B_text',
                  'Annotation_B_type');
        }
        $textToExport = array(implode("\t", $header));
        foreach ($annotationsRelations as $annotationsRelation) {
            if (isset($annotationsRelation["Document"]["external_id"])) {
                $document = $annotationsRelation["Document"]["external_id"];
            } else {
                $document = $annotationsRelation["Document"]["title"];
            }
            $typeA = $types[$annotationsRelation["Annotation_A"]["type_id"]];
            $typeB = $types[$annotationsRelation["Annotation_B"]["type_id"]];
            $document .= "\t" .
                $annotationsRelation["Annotation_A"]["annotated_text"] . "\t";
            if ($relationLevel != 2) {
                $document .= $annotationsRelation["Annotation_A"]["section"] . "\t" .
                    $annotationsRelation["Annotation_A"]["init"] . "\t" .
                    $annotationsRelation["Annotation_A"]["end"] . "\t";
            }
            $document .= $typeA . "\t" .
                $annotationsRelation["Relation"]["name"] . "\t" .
                $annotationsRelation["Annotation_B"]["annotated_text"] . "\t";
            if ($relationLevel != 2) {
                $document .= $annotationsRelation["Annotation_B"]["section"] . "\t" .
                    $annotationsRelation["Annotation_B"]["init"] . "\t" .
                    $annotationsRelation["Annotation_B"]["end"] . "\t";
            }
            $document .= $typeB;
            array_push($textToExport, $document);
        }
        $project = $this->Relation->Project->find('first', array(
              'fields' => array('title'),
              'recursive' => -1,
              'conditions' => array('id' => $project_id)
            )
        );
        $title = $project['Project']['title'];
        $title = $project['Project']['title'];
        $title = ltrim(substr($title, 0, 20) . '_relations');
        return $this->exportTsvDocument($textToExport, $title . ".tsv");
    }

}
