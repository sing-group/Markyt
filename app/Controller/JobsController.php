<?php

App::uses('AppController', 'Controller');

/**
 * Jobs Controller
 *
 * @property Job $Job
 * @property PaginatorComponent $Paginator
 */
class JobsController extends AppController {

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
    public function index() {
        $this->Job->recursive = 0;

        $this->paginate = array(
            'fields' => array(
                'Job.id',
                'Job.status',
                'Job.percentage',
                'Job.status',
                'Job.program',
                'Job.exception',
//                'Job.status',
                'Round.title',
                'Round.id',
                'User.username',
                'User.surname',
                'User.id',
                'User.image',
                'User.image_type',
//                'Round.ends_in_date',
//                'Round.project_id',
//                'Project.title',
//                'UsersRound.id',
//                'UsersRound.state',
            ),
            'order' => array('created' => 'DESC'),
        );
        $this->initialiceDatabaseUsage();
        $file = file('/proc/cpuinfo');
        $proc = split(":", $file[4]);
        $proc_details = $proc[1];
        $proc = split(":", $file[7]);
        $hz = $proc[1];

        $gHz = round($hz / 1000, 1);

        $proc_details = str_replace("Processor", "", $proc_details);
        $proc_details.=" @ " . $gHz . " GHz";
        $this->set('proc_details', $proc_details);
        $this->set('isServerStatsEnable', Configure::read('enableServerStats', false));
        $this->set('jobs', $this->Paginator->paginate());
    }

    private function getServerMemoryUsage() {
        $free = shell_exec('free');
        $free = (string) trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        return $mem;
    }

    private function getServerCpuUsage() {
        $load = sys_getloadavg();
        $cores = shell_exec('nproc');
        $use = shell_exec('ps -A -o pcpu | tail -n+2 | paste -sd+ | bc');
        return ($use / $cores);
    }

    private function getDatabaseUsage() {
//        $this->Job->query('SELECT * FROM table');
        $lastStatus = $this->Session->read("lastDatabaseUsage");
        $newStatus = $this->getDtabaseStatus();
//        $first  = new DateTime();
//sleep(1);
//$second = new DateTime();
        $diff = $newStatus['UPTIME']->diff($lastStatus['UPTIME']);
//echo $diff->format( '%s' );

        $totalTime = $diff->format('%s');
//        $totalTime = $newStatus['UPTIME'] - $lastStatus['UPTIME'];
        $queries = $newStatus['QUERIES'] - $lastStatus['QUERIES'];
        $reads = $newStatus['INNODB_DATA_READS'] - $lastStatus['INNODB_DATA_READS'];
        $writes = $newStatus['INNODB_DATA_WRITES'] - $lastStatus['INNODB_DATA_WRITES'];
        $annotations = $newStatus['ANNOTATIONS'] - $lastStatus['ANNOTATIONS'];


        if ($totalTime == 0) {
            $usage = array("queries" => 0, "reads" => 0,
                "writes" => 0);
        } else {
            //variables per second
            $usage = array("queries" => round($queries / $totalTime), "reads" => round($reads / $totalTime),
                "writes" => round($writes / $totalTime), "annotations" => round($annotations / $totalTime));
        }

        return $usage;
    }

    public function test() {
        $this->Annotation = $this->Job->Round->Annotation;
        while (true) {
            for ($index = 0; $index < 100; $index++) {

                $annotations = $this->Annotation->find('count', array('recursive' => -1));
            }
            sleep(1);
        }
    }

    private function getDtabaseStatus() {

        $this->Annotation = $this->Job->Round->Annotation;
//        $annotations = $this->Annotation->find('count');
        $db = $this->Job->getDataSource();
        $variables = array("queries", "uptime", "Innodb_data_reads", "Innodb_data_writes");
//                $annotations=$this->Annotation->find('count');
//        $subQuery = $db->buildStatement(array(
//            'fields' => array(
//                'COUNT(*)'
//            ),
//            'table' => 'annotations',
//            'alias' => 'Annotation',
//            
//                ), $this->Annotation);
        $annotations = $this->Job->query(" SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED; ");


        $annotations = $this->Annotation->find('count', array('recursive' => -1));


        $result = $db->fetchAll("SELECT variable_value,variable_name "
                . "FROM information_schema.global_status "
                . "WHERE variable_name IN ('" . implode("','", $variables) . "')", array()
        );
        $result = Set::combine($result, '{n}.global_status.variable_name', '{n}.global_status.variable_value');
        $result["ANNOTATIONS"] = $annotations;
        $result['UPTIME'] = new DateTime();

        $this->Session->write("lastDatabaseUsage", $result);
        return $result;
    }

    private function initialiceDatabaseUsage() {
        $this->Session->write("lastDatabaseUsage", $this->getDtabaseStatus());
    }

    public function getServerStat() {
        $isEnablesServerStats = Configure::read('enableServerStats');

        $jobs = array();
        if ($this->request->is('post')) {
            if (isset($this->request->data['jobs'])) {
                $ids = $this->request->data['jobs'];
                if (!empty($ids)) {
                    $jobs = $this->Job->find('all', array(
                        'recursive' => -1, //int
                        'fields' => array('Job.id', 'Job.percentage', 'Job.status',
                            'Job.exception'),
                        'conditions' => array('id' => $ids), //array of conditions
                    ));
                    for ($i = 0; $i < count($jobs); $i++) {
                        if (isset($jobs[$i]["Job"]["exception"])) {
                            $jobs[$i]["Job"]["exception"] = substr($jobs[$i]["Job"]["exception"], 0, 100) . "...";
                        }
                    }

                    $jobs = Set::combine($jobs, '{n}.Job.id', '{n}.Job');
                }
            }
        }

        if ($isEnablesServerStats) {
            $mem = $this->getServerMemoryUsage();
            $memory_usage = round($mem[2] / $mem[1] * 100, 1);
            $total = $this->bytesToHuman($mem[1], "GB");
            $used = $this->bytesToHuman($mem[2], "GB");
            $free = $this->bytesToHuman($mem[1] - $mem[2], "GB");
            $database = $this->getDatabaseUsage();
//            debug($this->getDtabaseStatus());
//            debug($this->getDatabaseUsage());
            $this->correctResponseJson(array(
                "isServerStatsEnabled" => true,
                "memory" => $total,
                "memory_used" => $used,
                "memory_free" => $free,
                "memory_percentage" => $memory_usage,
                "cpu" => $this->getServerCpuUsage(),
                "jobsUpdate" => $jobs,
                "database" => $database,
                    )
            );
        } else {
            $this->correctResponseJson(array("isServerStatsEnabled" => false, "jobsUpdate" => $jobs,));
        }
    }

    public function export($id = null) {

        $this->Round = $this->Job->Round;
        $group_id = $this->Session->read('group_id');
        if ($group_id != 1) {
            throw new NotFoundException(__('Invalid group'));
        }

        $job = $this->Job->recursive=-1;
        $job = $this->Round->recursive=-1;

        $this->Job->id = $id;
        if (!$this->Job->exists()) {
            throw new NotFoundException(__('Invalid job'));
        }


        $job = $this->Job->read();
        $job = $job["Job"];
        $round_name = null;
        if (isset($job['round_id'])) {
            $this->Round->id = $job['round_id'];
            $round_name = $this->Round->read('title');
            $round_name = $round_name["Round"]['title'];
        }


        $lines = array();
        array_push($lines, "Status\tProgram\tCreated\tModified\tException\tComments");
        $line = "";
        if (isset($round_name))
            $line.="\n" . $round_name;
        $line.="\n" . $job['status'];
        $line.="\n" . $job['program'];
        $line.="\n" . $job['program'];
        $line.="\n" . $job['created'];
        $line.="\n" . $job['modified'];
        $line.="\n" . $job['exception'];

        array_push($lines, $line);

        $json = json_decode($job['comments'], true);
        $annotatedDocuments = $json["documentsWithAnnotations"];
        if (json_last_error() == JSON_ERROR_NONE) {
            $line = "========================================\n";
            $line.="========================================\n";
            $line.="========================================\n";
            array_push($lines, $line);

            if (!empty($annotatedDocuments)) {
                foreach ($annotatedDocuments as $document) {
                    foreach ($document as $id => $words) {
                        $line = $id;
                        foreach ($words as $word => $ocurrences) {
                            $line = "$id,$word,$ocurrences";
                            array_push($lines, $line);
                        }
                    }
                }
            }
        } else {
            $line.="\t" . $job['comments'];
        }


        return $this->exportTsvDocument($lines, "Job_" . $job['id'] . ".tsv");
    }

    public function kill($id) {
        $this->killJob($id);
    }

}
