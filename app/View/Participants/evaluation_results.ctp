<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));

echo $this->Html->script('Bootstrap/markyShortTableResults', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
?>
<h1>CEMP evaluation results</h1>
<div class="col-md-12">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1" class="tab" data-toggle="tab"><i class="fa fa-tachometer"></i><?php echo __('CEMP'); ?></a></li>
        <li><a href="#tab-2" class="tab" data-toggle="tab"><i class="fa fa-tachometer"></i><?php echo __('GPRO'); ?></a></li>
        <li><a href="#tab-3" class="tab" data-toggle="tab" ><i class="fa fa-tachometer"></i><?php echo __('CPD'); ?></a></li>
    </ul>
    <div class="tab-content form-tabs">
        <div class="related tab-pane fade in active data-table" id="tab-1">
            <h2>Chemical entity mention in patents (CEMP) result overview</h2>
            <div clas="bold">
                Refer to: Krallinger et al. Overview of the CHEMDNER patents task. Proceedings of the Fifth BioCreative Challenge Evaluation Workshop (2015).
            </div>
            <div class="col-md-8">
                <table class="table table-hover table-responsive viewTable" id="CEMP">
                    <thead>
                        <tr>
                            <th>#Team-Id</th>
                            <th class="datatable-nofilter">Run</th>
                            <th class="datatable-nofilter">Precision</th>
                            <th class="datatable-nofilter">Recall</th>
                            <th class="datatable-nofilter">F-score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>3</td><td>0.85007</td><td>0.83590</td><td>0.84293</td></tr>
                        <tr class="pointer" data-team-id="350"><td>350</td><td>1</td><td>0.87031</td><td>0.83811</td><td>0.85391</td></tr>
                        <tr class="pointer" data-team-id="278"><td>278</td><td>3</td><td>0.81988</td><td>0.79711</td><td>0.80833</td></tr>
                        <tr class="pointer" data-team-id="308"><td>308</td><td>1</td><td>0.77251</td><td>0.75728</td><td>0.76482</td></tr>
                        <tr class="pointer" data-team-id="293"><td>293</td><td>3</td><td>0.87821</td><td>0.86197</td><td>0.87002</td></tr>
                        <tr class="pointer" data-team-id="350"><td>350</td><td>3</td><td>0.87002</td><td>0.83617</td><td>0.85276</td></tr>
                        <tr class="pointer" data-team-id="350"><td>350</td><td>2</td><td>0.86437</td><td>0.84250</td><td>0.85329</td></tr>
                        <tr class="pointer" data-team-id="277"><td>277</td><td>3</td><td>0.87915</td><td>0.83269</td><td>0.85529</td></tr>
                        <tr class="pointer" data-team-id="276"><td>276</td><td>5</td><td>0.86825</td><td>0.86810</td><td>0.86817</td></tr>
                        <tr class="pointer" data-team-id="348"><td>348</td><td>2</td><td>0.81986</td><td>0.69495</td><td>0.75226</td></tr>
                        <tr class="pointer" data-team-id="277"><td>277</td><td>5</td><td>0.87327</td><td>0.83298</td><td>0.85265</td></tr>
                        <tr class="pointer" data-team-id="281"><td>281</td><td>4</td><td>0.78343</td><td>0.60874</td><td>0.68512</td></tr>
                        <tr class="pointer" data-team-id="296"><td>296</td><td>4</td><td>0.86298</td><td>0.81852</td><td>0.84016</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>4</td><td>0.84418</td><td>0.85873</td><td>0.85139</td></tr>
                        <tr class="pointer" data-team-id="277"><td>277</td><td>2</td><td>0.88020</td><td>0.83060</td><td>0.85468</td></tr>
                        <tr class="pointer" data-team-id="313"><td>313</td><td>1</td><td>0.79399</td><td>0.71852</td><td>0.75437</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>3</td><td>0.89078</td><td>0.89175</td><td>0.89126</td></tr>
                        <tr class="pointer" data-team-id="359"><td>359</td><td>4</td><td>0.78230</td><td>0.87007</td><td>0.82385</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>4</td><td>0.85226</td><td>0.83178</td><td>0.84189</td></tr>
                        <tr class="pointer" data-team-id="292"><td>292</td><td>5</td><td>0.00439</td><td>0.00021</td><td>0.00039</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>1</td><td>0.87519</td><td>0.91293</td><td>0.89366</td></tr>
                        <tr class="pointer" data-team-id="337"><td>337</td><td>4</td><td>0.00000</td><td>0.00000</td><td>0.00000</td></tr>
                        <tr class="pointer" data-team-id="277"><td>277</td><td>4</td><td>0.88073</td><td>0.83195</td><td>0.85565</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>2</td><td>0.82899</td><td>0.87676</td><td>0.85221</td></tr>
                        <tr class="pointer" data-team-id="315"><td>315</td><td>2</td><td>0.84308</td><td>0.81295</td><td>0.82774</td></tr>
                        <tr class="pointer" data-team-id="356"><td>356</td><td>1</td><td>0.85534</td><td>0.88860</td><td>0.87165</td></tr>
                        <tr class="pointer" data-team-id="308"><td>308</td><td>2</td><td>0.80944</td><td>0.75761</td><td>0.78267</td></tr>
                        <tr class="pointer" data-team-id="359"><td>359</td><td>3</td><td>0.83470</td><td>0.87428</td><td>0.85403</td></tr>
                        <tr class="pointer" data-team-id="362"><td>362</td><td>1</td><td>0.86885</td><td>0.88689</td><td>0.87778</td></tr>
                        <tr class="pointer" data-team-id="296"><td>296</td><td>3</td><td>0.86210</td><td>0.81985</td><td>0.84044</td></tr>
                        <tr class="pointer" data-team-id="293"><td>293</td><td>5</td><td>0.86145</td><td>0.84777</td><td>0.85456</td></tr>
                        <tr class="pointer" data-team-id="356"><td>356</td><td>5</td><td>0.86065</td><td>0.87852</td><td>0.86950</td></tr>
                        <tr class="pointer" data-team-id="356"><td>356</td><td>4</td><td>0.85385</td><td>0.87181</td><td>0.86274</td></tr>
                        <tr class="pointer" data-team-id="337"><td>337</td><td>2</td><td>0.00000</td><td>0.00000</td><td>0.00000</td></tr>
                        <tr class="pointer" data-team-id="288"><td>288</td><td>5</td><td>0.87556</td><td>0.89637</td><td>0.88585</td></tr>
                        <tr class="pointer" data-team-id="348"><td>348</td><td>3</td><td>0.81051</td><td>0.69083</td><td>0.74590</td></tr>
                        <tr class="pointer" data-team-id="337"><td>337</td><td>5</td><td>0.00000</td><td>0.00000</td><td>0.00000</td></tr>
                        <tr class="pointer" data-team-id="288"><td>288</td><td>4</td><td>0.86265</td><td>0.89375</td><td>0.87793</td></tr>
                        <tr class="pointer" data-team-id="288"><td>288</td><td>1</td><td>0.86953</td><td>0.90498</td><td>0.88690</td></tr>
                        <tr class="pointer" data-team-id="278"><td>278</td><td>2</td><td>0.82877</td><td>0.79696</td><td>0.81255</td></tr>
                        <tr class="pointer" data-team-id="292"><td>292</td><td>4</td><td>0.00257</td><td>0.00012</td><td>0.00023</td></tr>
                        <tr class="pointer" data-team-id="296"><td>296</td><td>5</td><td>0.86272</td><td>0.82020</td><td>0.84092</td></tr>
                        <tr class="pointer" data-team-id="284"><td>284</td><td>5</td><td>0.86632</td><td>0.78895</td><td>0.82583</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>2</td><td>0.80521</td><td>0.77831</td><td>0.79153</td></tr>
                        <tr class="pointer" data-team-id="281"><td>281</td><td>3</td><td>0.77158</td><td>0.60623</td><td>0.67898</td></tr>
                        <tr class="pointer" data-team-id="284"><td>284</td><td>4</td><td>0.86596</td><td>0.78862</td><td>0.82549</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>1</td><td>0.87506</td><td>0.81923</td><td>0.84622</td></tr>
                        <tr class="pointer" data-team-id="348"><td>348</td><td>4</td><td>0.81819</td><td>0.69301</td><td>0.75041</td></tr>
                        <tr class="pointer" data-team-id="281"><td>281</td><td>5</td><td>0.80643</td><td>0.61433</td><td>0.69740</td></tr>
                        <tr class="pointer" data-team-id="315"><td>315</td><td>1</td><td>0.85063</td><td>0.79393</td><td>0.82130</td></tr>
                        <tr class="pointer" data-team-id="293"><td>293</td><td>1</td><td>0.87778</td><td>0.86126</td><td>0.86944</td></tr>
                        <tr class="pointer" data-team-id="337"><td>337</td><td>3</td><td>0.00000</td><td>0.00000</td><td>0.00000</td></tr>
                        <tr class="pointer" data-team-id="288"><td>288</td><td>3</td><td>0.87444</td><td>0.90465</td><td>0.88929</td></tr>
                        <tr class="pointer" data-team-id="276"><td>276</td><td>1</td><td>0.77762</td><td>0.90836</td><td>0.83792</td></tr>
                        <tr class="pointer" data-team-id="348"><td>348</td><td>5</td><td>0.83068</td><td>0.67572</td><td>0.74523</td></tr>
                        <tr class="pointer" data-team-id="313"><td>313</td><td>5</td><td>0.85803</td><td>0.83422</td><td>0.84596</td></tr>
                        <tr class="pointer" data-team-id="313"><td>313</td><td>3</td><td>0.48416</td><td>0.92327</td><td>0.63521</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>1</td><td>0.83146</td><td>0.85387</td><td>0.84252</td></tr>
                        <tr class="pointer" data-team-id="281"><td>281</td><td>2</td><td>0.83116</td><td>0.64514</td><td>0.72643</td></tr>
                        <tr class="pointer" data-team-id="315"><td>315</td><td>3</td><td>0.84787</td><td>0.80642</td><td>0.82663</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>4</td><td>0.79668</td><td>0.93143</td><td>0.85880</td></tr>
                        <tr class="pointer" data-team-id="296"><td>296</td><td>1</td><td>0.86303</td><td>0.82147</td><td>0.84174</td></tr>
                        <tr class="pointer" data-team-id="292"><td>292</td><td>1</td><td>0.00254</td><td>0.00012</td><td>0.00023</td></tr>
                        <tr class="pointer" data-team-id="359"><td>359</td><td>2</td><td>0.85474</td><td>0.82315</td><td>0.83865</td></tr>
                        <tr class="pointer" data-team-id="292"><td>292</td><td>2</td><td>0.00251</td><td>0.00012</td><td>0.00023</td></tr>
                        <tr class="pointer" data-team-id="284"><td>284</td><td>1</td><td>0.86565</td><td>0.78821</td><td>0.82512</td></tr>
                        <tr class="pointer" data-team-id="292"><td>292</td><td>3</td><td>0.00371</td><td>0.00018</td><td>0.00034</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>3</td><td>0.82966</td><td>0.82783</td><td>0.82875</td></tr>
                        <tr class="pointer" data-team-id="359"><td>359</td><td>1</td><td>0.87669</td><td>0.84144</td><td>0.85870</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>2</td><td>0.89711</td><td>0.88224</td><td>0.88961</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>5</td><td>0.82932</td><td>0.87664</td><td>0.85232</td></tr>
                        <tr class="pointer" data-team-id="277"><td>277</td><td>1</td><td>0.87878</td><td>0.84282</td><td>0.86042</td></tr>
                        <tr class="pointer" data-team-id="278"><td>278</td><td>4</td><td>0.81995</td><td>0.79723</td><td>0.80843</td></tr>
                        <tr class="pointer" data-team-id="278"><td>278</td><td>1</td><td>0.82030</td><td>0.78285</td><td>0.80114</td></tr>
                        <tr class="pointer" data-team-id="281"><td>281</td><td>1</td><td>0.69394</td><td>0.47386</td><td>0.56316</td></tr>
                        <tr class="pointer" data-team-id="293"><td>293</td><td>4</td><td>0.87852</td><td>0.86226</td><td>0.87031</td></tr>
                        <tr class="pointer" data-team-id="356"><td>356</td><td>2</td><td>0.86065</td><td>0.87852</td><td>0.86950</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>5</td><td>0.52022</td><td>0.97617</td><td>0.67873</td></tr>
                        <tr class="pointer" data-team-id="313"><td>313</td><td>2</td><td>0.85607</td><td>0.83726</td><td>0.84656</td></tr>
                        <tr class="pointer" data-team-id="288"><td>288</td><td>2</td><td>0.87177</td><td>0.90777</td><td>0.88941</td></tr>
                        <tr class="pointer" data-team-id="293"><td>293</td><td>2</td><td>0.87793</td><td>0.86159</td><td>0.86968</td></tr>
                        <tr class="pointer" data-team-id="278"><td>278</td><td>5</td><td>0.82884</td><td>0.79708</td><td>0.81265</td></tr>
                        <tr class="pointer" data-team-id="337"><td>337</td><td>1</td><td>0.00000</td><td>0.00000</td><td>0.00000</td></tr>
                        <tr class="pointer" data-team-id="356"><td>356</td><td>3</td><td>0.85135</td><td>0.87019</td><td>0.86067</td></tr>
                        <tr class="pointer" data-team-id="276"><td>276</td><td>2</td><td>0.77538</td><td>0.90925</td><td>0.83700</td></tr>
                        <tr class="pointer" data-team-id="276"><td>276</td><td>3</td><td>0.85875</td><td>0.86801</td><td>0.86335</td></tr>
                        <tr class="pointer" data-team-id="296"><td>296</td><td>2</td><td>0.86323</td><td>0.81949</td><td>0.84079</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>5</td><td>0.81749</td><td>0.82344</td><td>0.82046</td></tr>
                        <tr class="pointer" data-team-id="348"><td>348</td><td>1</td><td>0.81912</td><td>0.69710</td><td>0.75320</td></tr>
                        <tr class="pointer" data-team-id="276"><td>276</td><td>4</td><td>0.84918</td><td>0.88250</td><td>0.86552</td></tr>
                        <tr class="pointer" data-team-id="284"><td>284</td><td>3</td><td>0.86545</td><td>0.78721</td><td>0.82448</td></tr>
                        <tr class="pointer" data-team-id="313"><td>313</td><td>4</td><td>0.86981</td><td>0.42941</td><td>0.57497</td></tr>
                        <tr class="pointer" data-team-id="284"><td>284</td><td>2</td><td>0.88592</td><td>0.80497</td><td>0.84351</td></tr>

                    </tbody>
                </table>
            </div>
            <!--            <div class="col-md-4 resultsChart">
                            <div class="">
                                <div id="CEMP-chart" class="text-center color-grey chart">
                                    <i class="fa fa-bar-chart fa-5 " style="margin-top: 90px;"></i>
                                    <h2>
                                        Click in one team to see
                                    </h2>
                                </div>
            
                            </div>
                        </div>-->
            <div class="col-md-10 resultsChart view ">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-info"></i>Results forr team XXX</h4>
                    </div>
                    <div class="panel-body">
                        <div id="CEMP-chart" class="text-center color-grey" style="height: 500px;">
                            <i class="fa fa-bar-chart fa-5 " style="margin-top: 90px;"></i>
                            <h2>
                                Click in one team to see
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="clear"></div>
            <?php
            echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', DS . 'files' . DS . 'Results' . DS . 'cemp_test_all_results.eval', array(
                'download' => "cemp_test_all_results.eval",
                'class' => 'btn btn-blue ladda-button noHottie',
                'escape' => false, "data-style" => "slide-down",
                "data-spinner-size" => "20",
                "data-spinner-color" => "#fff",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                'id' => false,
                "data-original-title" => 'Download all teams results'));
            ?>
        </div>
        <div class="related tab-pane fade in data-table" id="tab-2">
            <h2>Gene and protein related object (GPRO) result overview</h2>
            <div clas="bold">
                Refer to: Krallinger et al. Overview of the CHEMDNER patents task. Proceedings of the Fifth BioCreative Challenge Evaluation Workshop (2015).
            </div>
            <div class="col-md-8">
                <table class="table table-hover table-responsive viewTable" id="GPRO" >
                    <thead>
                        <tr>
                            <th>#Team-Id</th>
                            <th class="datatable-nofilter">Run</th>
                            <th class="datatable-nofilter">Precision</th>
                            <th class="datatable-nofilter">Recall</th>
                            <th class="datatable-nofilter">F-score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>3</td><td>0.78801</td><td>0.63572</td><td>0.70372</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>4</td><td>0.78676</td><td>0.72025</td><td>0.75204</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>3</td><td>0.80587</td><td>0.79819</td><td>0.80201</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>4</td><td>0.07573</td><td>0.01197</td><td>0.02068</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>1</td><td>0.78349</td><td>0.83020</td><td>0.80617</td></tr>
                        <tr class="pointer" data-team-id="368"><td>368</td><td>1</td><td>0.65258</td><td>0.61862</td><td>0.63514</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>2</td><td>0.72914</td><td>0.76423</td><td>0.74627</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>2</td><td>0.40671</td><td>0.38822</td><td>0.39725</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>1</td><td>0.41133</td><td>0.40972</td><td>0.41053</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>1</td><td>0.71989</td><td>0.68190</td><td>0.70038</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>4</td><td>0.76770</td><td>0.85023</td><td>0.80686</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>3</td><td>0.08502</td><td>0.01539</td><td>0.02607</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>2</td><td>0.82242</td><td>0.78524</td><td>0.80340</td></tr>
                        <tr class="pointer" data-team-id="304"><td>304</td><td>5</td><td>0.78528</td><td>0.72196</td><td>0.75229</td></tr>
                        <tr class="pointer" data-team-id="274"><td>274</td><td>5</td><td>0.81429</td><td>0.81310</td><td>0.81369</td></tr>
                        <tr class="pointer" data-team-id="286"><td>286</td><td>5</td><td>0.09478</td><td>0.01906</td><td>0.03173</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 resultsChart">
                <div class="">
                    <div id="GPRO-chart" class="text-center color-grey chart">
                        <i class="fa fa-bar-chart fa-5 " style="margin-top: 90px;"></i>
                        <h2>
                            Click in one team to see
                        </h2>
                    </div>

                </div>
                <div class="clear"></div>
            </div>


            <div class="clear"></div>
            <?php
            echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', DS . 'files' . DS . 'Results' . DS . 'gpro_test_all_results.eval', array(
                'class' => 'btn btn-blue ladda-button noHottie',
                'download' => "gpro_test_all_results.eval",
                'escape' => false, "data-style" => "slide-down",
                "data-spinner-size" => "20",
                "data-spinner-color" => "#fff",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                'id' => false,
                "data-original-title" => 'Download all teams results'));
            ?>
        </div>
        <div class="related tab-pane fade data-table" id="tab-3">
            <h2>Chemical passage detection (CPD) result overview</h2>
            <div clas="bold">
                Refer to: Krallinger et al. Overview of the CHEMDNER patents task. Proceedings of the Fifth BioCreative Challenge Evaluation Workshop (2015).
            </div>
            <div class="col-md-8">
                <table class="table table-hover table-responsive viewTable" id="CPD">
                    <thead>
                        <tr>
                            <th>#Team-Id</th>
                            <th class="datatable-nofilter">Run</th>
                            <th class="datatable-nofilter">TP</th>
                            <th class="datatable-nofilter">FP</th>
                            <th class="datatable-nofilter">FN</th>
                            <th class="datatable-nofilter">TN</th>
                            <th class="datatable-nofilter">Sens</th>
                            <th class="datatable-nofilter">Spec.</th>
                            <th class="datatable-nofilter">Accur.</th>
                            <th class="datatable-nofilter">MCC</th>
                            <th class="datatable-nofilter">P_full_R</th>
                            <th class="datatable-nofilter">AUC_PR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-team-id="304"><td>304</td><td>3</td><td>8796</td><td>450</td><td>474</td><td>4280</td><td>0.94887</td><td>0.90486</td><td>0.93400</td><td>0.85268</td><td>0.66224</td><td>0.95522</td>
                        <tr data-team-id="278"><td>278</td><td>3</td><td>8572</td><td>668</td><td>698</td><td>4062</td><td>0.92470</td><td>0.85877</td><td>0.90243</td><td>0.78227</td><td>0.66214</td><td>0.93936</td>
                        <tr data-team-id="308"><td>308</td><td>1</td><td>8691</td><td>774</td><td>579</td><td>3956</td><td>0.93754</td><td>0.83636</td><td>0.90336</td><td>0.78218</td><td>0.66247</td><td>0.94276</td>
                        <tr data-team-id="276"><td>276</td><td>5</td><td>8911</td><td>536</td><td>359</td><td>4194</td><td>0.96127</td><td>0.88668</td><td>0.93607</td><td>0.85614</td><td>0.66214</td><td>0.93626</td>
                        <tr data-team-id="304"><td>304</td><td>4</td><td>8923</td><td>529</td><td>347</td><td>4201</td><td>0.96257</td><td>0.88816</td><td>0.93743</td><td>0.85919</td><td>0.66224</td><td>0.95271</td>
                        <tr data-team-id="313"><td>313</td><td>1</td><td>7629</td><td>564</td><td>1641</td><td>4166</td><td>0.82298</td><td>0.88076</td><td>0.84250</td><td>0.67559</td><td>0.66233</td><td>0.93292</td>
                        <tr data-team-id="286"><td>286</td><td>4</td><td>8804</td><td>672</td><td>466</td><td>4058</td><td>0.94973</td><td>0.85793</td><td>0.91871</td><td>0.81682</td><td>0.66214</td><td>0.95492</td>
                        <tr data-team-id="292"><td>292</td><td>5</td><td>7925</td><td>939</td><td>1345</td><td>3791</td><td>0.85491</td><td>0.80148</td><td>0.83686</td><td>0.64417</td><td>0.66243</td><td>0.94888</td>
                        <tr data-team-id="304"><td>304</td><td>2</td><td>9066</td><td>713</td><td>204</td><td>4017</td><td>0.97799</td><td>0.84926</td><td>0.93450</td><td>0.85262</td><td>0.66399</td><td>0.94636</td>
                        <tr data-team-id="356"><td>356</td><td>1</td><td>9138</td><td>784</td><td>132</td><td>3946</td><td>0.98576</td><td>0.83425</td><td>0.93457</td><td>0.85362</td><td>0.66214</td><td>0.92119</td>
                        <tr data-team-id="308"><td>308</td><td>2</td><td>8582</td><td>549</td><td>688</td><td>4181</td><td>0.92578</td><td>0.88393</td><td>0.91164</td><td>0.80412</td><td>0.66252</td><td>0.95271</td>
                        <tr data-team-id="356"><td>356</td><td>5</td><td>9117</td><td>751</td><td>153</td><td>3979</td><td>0.98350</td><td>0.84123</td><td>0.93543</td><td>0.85523</td><td>0.66214</td><td>0.92330</td>
                        <tr data-team-id="356"><td>356</td><td>4</td><td>9117</td><td>751</td><td>153</td><td>3979</td><td>0.98350</td><td>0.84123</td><td>0.93543</td><td>0.85523</td><td>0.66214</td><td>0.92330</td>
                        <tr data-team-id="288"><td>288</td><td>1</td><td>9140</td><td>605</td><td>130</td><td>4125</td><td>0.98598</td><td>0.87209</td><td>0.94750</td><td>0.88237</td><td>0.66571</td><td>0.93468</td>
                        <tr data-team-id="278"><td>278</td><td>2</td><td>8552</td><td>589</td><td>718</td><td>4141</td><td>0.92255</td><td>0.87548</td><td>0.90664</td><td>0.79289</td><td>0.66214</td><td>0.94338</td>
                        <tr data-team-id="292"><td>292</td><td>4</td><td>7768</td><td>874</td><td>1502</td><td>3856</td><td>0.83797</td><td>0.81522</td><td>0.83029</td><td>0.63563</td><td>0.66224</td><td>0.93580</td>
                        <tr data-team-id="286"><td>286</td><td>2</td><td>8794</td><td>832</td><td>476</td><td>3898</td><td>0.94865</td><td>0.82410</td><td>0.90657</td><td>0.78859</td><td>0.66214</td><td>0.94409</td>
                        <tr data-team-id="286"><td>286</td><td>1</td><td>8642</td><td>500</td><td>628</td><td>4230</td><td>0.93225</td><td>0.89429</td><td>0.91943</td><td>0.82127</td><td>0.66214</td><td>0.96432</td>
                        <tr data-team-id="288"><td>288</td><td>3</td><td>9114</td><td>602</td><td>156</td><td>4128</td><td>0.98317</td><td>0.87273</td><td>0.94586</td><td>0.87846</td><td>0.66271</td><td>0.93513</td>
                        <tr data-team-id="276"><td>276</td><td>1</td><td>9087</td><td>625</td><td>183</td><td>4105</td><td>0.98026</td><td>0.86786</td><td>0.94229</td><td>0.87026</td><td>0.66219</td><td>0.93379</td>
                        <tr data-team-id="313"><td>313</td><td>5</td><td>8694</td><td>681</td><td>576</td><td>4049</td><td>0.93786</td><td>0.85603</td><td>0.91021</td><td>0.79834</td><td>0.66285</td><td>0.94968</td>
                        <tr data-team-id="313"><td>313</td><td>3</td><td>9073</td><td>1919</td><td>197</td><td>2811</td><td>0.97875</td><td>0.59429</td><td>0.84886</td><td>0.65990</td><td>0.66314</td><td>0.89211</td>
                        <tr data-team-id="304"><td>304</td><td>1</td><td>8967</td><td>636</td><td>303</td><td>4094</td><td>0.96731</td><td>0.86554</td><td>0.93293</td><td>0.84871</td><td>0.66409</td><td>0.94905</td>
                        <tr data-team-id="292"><td>292</td><td>1</td><td>7805</td><td>894</td><td>1465</td><td>3836</td><td>0.84196</td><td>0.81099</td><td>0.83150</td><td>0.63671</td><td>0.66224</td><td>0.93713</td>
                        <tr data-team-id="292"><td>292</td><td>2</td><td>7777</td><td>1433</td><td>1493</td><td>3297</td><td>0.83894</td><td>0.69704</td><td>0.79100</td><td>0.53435</td><td>0.66224</td><td>0.86239</td>
                        <tr data-team-id="292"><td>292</td><td>3</td><td>7421</td><td>1180</td><td>1849</td><td>3550</td><td>0.80054</td><td>0.75053</td><td>0.78364</td><td>0.53548</td><td>0.66233</td><td>0.91345</td>
                        <tr data-team-id="286"><td>286</td><td>3</td><td>8908</td><td>790</td><td>362</td><td>3940</td><td>0.96095</td><td>0.83298</td><td>0.91771</td><td>0.81391</td><td>0.66219</td><td>0.94789</td>
                        <tr data-team-id="304"><td>304</td><td>5</td><td>9066</td><td>711</td><td>204</td><td>4019</td><td>0.97799</td><td>0.84968</td><td>0.93464</td><td>0.85294</td><td>0.66399</td><td>0.94662</td>
                        <tr data-team-id="278"><td>278</td><td>4</td><td>8572</td><td>668</td><td>698</td><td>4062</td><td>0.92470</td><td>0.85877</td><td>0.90243</td><td>0.78227</td><td>0.66214</td><td>0.93937</td>
                        <tr data-team-id="278"><td>278</td><td>1</td><td>8482</td><td>649</td><td>788</td><td>4081</td><td>0.91499</td><td>0.86279</td><td>0.89736</td><td>0.77242</td><td>0.66214</td><td>0.93766</td>
                        <tr data-team-id="356"><td>356</td><td>2</td><td>9117</td><td>751</td><td>153</td><td>3979</td><td>0.98350</td><td>0.84123</td><td>0.93543</td><td>0.85523</td><td>0.66214</td><td>0.92330</td>
                        <tr data-team-id="313"><td>313</td><td>2</td><td>8727</td><td>686</td><td>543</td><td>4044</td><td>0.94142</td><td>0.85497</td><td>0.91221</td><td>0.80254</td><td>0.66285</td><td>0.94998</td>
                        <tr data-team-id="288"><td>288</td><td>2</td><td>9101</td><td>689</td><td>169</td><td>4041</td><td>0.98177</td><td>0.85433</td><td>0.93871</td><td>0.86238</td><td>0.66271</td><td>0.92745</td>
                        <tr data-team-id="278"><td>278</td><td>5</td><td>8552</td><td>589</td><td>718</td><td>4141</td><td>0.92255</td><td>0.87548</td><td>0.90664</td><td>0.79289</td><td>0.66214</td><td>0.94338</td>
                        <tr data-team-id="356"><td>356</td><td>3</td><td>9136</td><td>774</td><td>134</td><td>3956</td><td>0.98554</td><td>0.83636</td><td>0.93514</td><td>0.85486</td><td>0.66214</td><td>0.92196</td>
                        <tr data-team-id="276"><td>276</td><td>2</td><td>9100</td><td>668</td><td>170</td><td>4062</td><td>0.98166</td><td>0.85877</td><td>0.94014</td><td>0.86556</td><td>0.66219</td><td>0.93042</td>
                        <tr data-team-id="276"><td>276</td><td>3</td><td>8952</td><td>512</td><td>318</td><td>4218</td><td>0.96570</td><td>0.89175</td><td>0.94071</td><td>0.86657</td><td>0.66219</td><td>0.94124</td>
                        <tr data-team-id="286"><td>286</td><td>5</td><td>8935</td><td>894</td><td>335</td><td>3836</td><td>0.96386</td><td>0.81099</td><td>0.91221</td><td>0.80134</td><td>0.66219</td><td>0.94476</td>
                        <tr data-team-id="276"><td>276</td><td>4</td><td>8992</td><td>572</td><td>278</td><td>4158</td><td>0.97001</td><td>0.87907</td><td>0.93929</td><td>0.86318</td><td>0.66214</td><td>0.93517</td>
                        <tr data-team-id="313"><td>313</td><td>4</td><td>3963</td><td>271</td><td>5307</td><td>4459</td><td>0.42751</td><td>0.94271</td><td>0.60157</td><td>0.38123</td><td>0.66214</td><td>0.84503</td>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 resultsChart">
                <div class="">
                    <div id="CPD-chart" class="text-center color-grey chart">
                        <i class="fa fa-bar-chart fa-5 " style="margin-top: 90px;"></i>
                        <h2>
                            Click in one team to see
                        </h2>
                    </div>

                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <?php
            echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', DS . 'files' . DS . 'Results' . DS . 'cpd_test_all_results.eval', array(
                'class' => 'btn btn-blue ladda-button noHottie',
                'download' => "cpd_test_all_results.eval",
                'escape' => false, "data-style" => "slide-down",
                "data-spinner-size" => "20",
                "data-spinner-color" => "#fff",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                'id' => false,
                "data-original-title" => 'Download all teams results'));
            ?>
        </div>
        <div class="clear"></div>

    </div>
</div>
<div class="clear"></div>
