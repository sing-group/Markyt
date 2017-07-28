<style>
    .rowSeparator
    {
        margin-top: 5px;
        margin-bottom: 5px;
        border: 0;
        border-top: 1px solid #ddd;
    }
    table.table>tbody>tr>td,
    table.table>thead>tr>th
    {
        text-align: center;
        vertical-align: middle;
    }
    .carousel {
        max-height: 420px;
        overflow: hidden;

        .item img {
            width: 100%;
            height: auto;
        }
    }
</style>

<div class="row">
    <h1 id="welcome">Markyt demo projects</h1>
    <div class="col-md-6">
        <div>
            <p>
                Demos of various annotation projects are available as part of Markyt documentation in this website. 
                These demos are inspired on public corpora, which supported a given text mining challenge or a research project. 
                The rationale behind the selection of corpora to produce the tool demos is to show the annotation features and analysis capabilities 
                of Markyt at different levels of complexity. Hence, demos encompass documents of varied source (e.g. PubMed abstracts, PubMed full texts, 
                Tweets and clinical notes), different document encodings (e.g. English, Spanish and Chinese), multi-round and multi-user entity 
                annotation (i.e. quality assessments and annotator agreement), and different levels of complexity in entity and event annotation. 
            </p>
            <h3 class="page-header">Credentials</h3>
            <p>
                To sign in the different demo projects, please, use one of the following credentials:
            </p>
            <div class="table-responsive " style="width: 50%; margin: 0 auto; ">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr class="warning">
                            <th>Privileges</th>
                            <th>User</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="info">
                            <td>
                                Annotator
                            </td>
                            <td>
                                demo
                            </td>
                            <td>
                                123456
                            </td>
                        </tr>
                        <tr class="info">
                            <td>
                                Admin
                            </td>
                            <td>
                                admin
                            </td>
                            <td>
                                123456
                            </td>
                        </tr>
                        <tr class="info">
                            <td>
                                Annotator
                            </td>
                            <td>
                                jane
                            </td>
                            <td>
                                123456
                            </td>
                        </tr>
                        <tr class="info">
                            <td>
                                Annotator
                            </td>
                            <td>
                                john
                            </td>
                            <td>
                                123456
                            </td>
                        </tr>

                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div id="barrSportCarousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#barrSportCarousel" data-slide-to="0" class="active" style="background-color: #333333"></li>
                <li data-target="#barrSportCarousel" data-slide-to="1" style="background-color: #333333" class=""></li>
                <li data-target="#barrSportCarousel" data-slide-to="2" style="background-color: #333333" class=""></li>
                <li data-target="#barrSportCarousel" data-slide-to="3" style="background-color: #333333" class=""></li>
                <li data-target="#barrSportCarousel" data-slide-to="4" style="background-color: #333333" class=""></li>
            </ol>
            <div class="carousel-inner">
                <div class="item active carouselImage">
                    <img src="img/demoCarousel1.png" alt="Full-texts document with multiple annotated and overlaped entites" title="Full-texts document with multiple annotated and overlaped entites" style="width: 100%;margin: 0 auto;"> 
                </div>
                <div class="item carouselImage">
                    <img src="img/demoCarousel2.png" alt="Inline-tabular relation perspective" title="Inline-tabular relation perspective" style="width: 100%;margin: 0 auto;"> 
                </div>
                <div class="item carouselImage">
                    <img src="img/demoCarousel3.png" alt="Document with multiple types of relations" title="Document with multiple types of relations" style="width: 100%;margin: 0 auto;">
                </div>
                <div class="item carouselImage">
                    <img src="img/demoCarousel4.png" alt="Drug label document with entities and relations" title="Drug label document with entities and relations" style="width: 100%;margin: 0 auto;">
                </div>
                <div class="item carouselImage">
                    <img src="img/demoCarousel5.png" alt="Tweets with entities" title="Tweets with entities" style="width: 100%;margin: 0 auto;">
                </div>
            </div>
            <a class="left carousel-control" href="#barrSportCarousel" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#barrSportCarousel" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3 class="page-header">About the demonstrations</h3>
        <div class="table-responsive" style="height: 100%">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Format</th>
                        <th>Language</th>
                        <th>Document type</th>
                        <th>#Documents</th>
                        <th>#Types</th>
                        <th>Annotations</th>
                    </tr>
                </thead>
                <tbody>                   
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC4051513/" target="_blank">AB3P</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            1250
                        </td>
                        <td>
                            E: 2
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 2423
                            <hr class="rowSeparator">
                            R: 1200
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="http://www.cs.utexas.edu/~ml/papers/bionlp-aimed-04.pdf" target="_blank">AIMed</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            225
                        </td>
                        <td>
                            E: 1
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 4236
                            <hr class="rowSeparator">
                            R: 1000
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pubmed/27161011" target="_blank">BCV CDR</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            500
                        </td>
                        <td>
                            E: 1
                        </td>
                        <td>
                            E: 5107
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pubmed/19958517/" target="_blank">BioADI</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            1201
                        </td>
                        <td>
                            E: 2
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 3402
                            <hr class="rowSeparator">
                            R: 1698
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.informatik.hu-berlin.de/de/forschung/gebiete/wbi/research/publications/2012/lrec2012_corpus.pdf" target="_blank">CellFinder</a> 
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Full-texts
                        </td>
                        <td>
                            10
                        </td>
                        <td>
                            E: 6
                        </td>
                        <td>
                            E: 5842
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://bmcbioinformatics.biomedcentral.com/articles/10.1186/1471-2105-13-207" target="_blank">Craft 2.0</a>
                        </td>
                        <td>
                            BRAT
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Full-texts
                        </td>
                        <td>
                            67
                        </td>
                        <td>
                            E: 4
                            <hr class="rowSeparator">
                            R: 3
                        </td>
                        <td>
                            E: 81036
                            <hr class="rowSeparator">
                            R: 54077
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="http://labda.inf.uc3m.es/DDIExtraction2011/paper0.pdf" target="_blank">DDI2011</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            435
                        </td>
                        <td>
                            E: 1
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 11260
                            <hr class="rowSeparator">
                            R: 2402
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="http://www.lrec-conf.org/proceedings/lrec2010/pdf/407_Paper.pdf" target="_blank">Genereg</a> 
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            314
                        </td>
                        <td>
                            E: 10
                            <hr class="rowSeparator">
                            R: 3
                        </td>
                        <td>
                            E: 6357
                            <hr class="rowSeparator">
                            R: 1767
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://bmcbioinformatics.biomedcentral.com/articles/10.1186/1471-2105-9-10" target="_blank">GENIA</a>
                        </td>
                        <td>
                            BioNLP
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            800
                        </td>
                        <td>
                            E: 11
                            <hr class="rowSeparator">
                            R: 10
                        </td>
                        <td>
                            E: 16185
                            <hr class="rowSeparator">
                            R: 6454
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://bmcbioinformatics.biomedcentral.com/articles/10.1186/1471-2105-10-349" target="_blank">Grec_ecoli</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            167
                        </td>
                        <td>
                            E: 57
                            <hr class="rowSeparator">
                            R: 13
                        </td>
                        <td>
                            E: 6332
                            <hr class="rowSeparator">
                            R: 4016
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://bmcbioinformatics.biomedcentral.com/articles/10.1186/s12859-016-1249-5" target="_blank">Herb-Chemical</a>
                        </td>
                        <td>
                            BioNLP
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            1.109
                        </td>
                        <td>
                            E: 3
                            <hr class="rowSeparator">
                            R: 2
                        </td>
                        <td>
                            E: 2815
                            <hr class="rowSeparator">
                            R: 1194
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC4986661/" target="_blank">Mantra</a>
                        </td>
                        <td>
                            BRAT
                        </td>
                        <td>
                            Spanish, German, French
                        </td>
                        <td>
                            EMEA documents
                        </td>
                        <td>
                            100
                        </td>
                        <td>
                            E: 11, 10, 10
                        </td>
                        <td>
                            E: 349, 348, 351
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pubmed/11604766/" target="_blank">Medstract</a>
                        </td>
                        <td>
                            BioC
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            198
                        </td>
                        <td>
                            E: 2
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 317
                            <hr class="rowSeparator">
                            R: 158
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://academic.oup.com/bioinformatics/article-lookup/doi/10.1093/bioinformatics/bts407" target="_blank">MLEE</a>
                        </td>
                        <td>
                            BioNLP
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            175
                        </td>
                        <td>
                            E: 44
                            <hr class="rowSeparator">
                            R: 15
                        </td>
                        <td>
                            E: 9249
                            <hr class="rowSeparator">
                            R: 3660
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://academic.oup.com/bioinformatics/article-lookup/doi/10.1093/bioinformatics/btv259" target="_blank">Phylogeography</a>
                        </td>
                        <td>
                            BRAT
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Full-texts
                        </td>
                        <td>
                            28
                        </td>
                        <td>
                            E: 7
                        </td>
                        <td>
                            E: 17486
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://sites.google.com/site/bionlpst/" target="_blank">Protein coreference</a>
                        </td>
                        <td>
                            BioNLP
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            800
                        </td>
                        <td>
                            E: 2
                            <hr class="rowSeparator">
                            R: 1
                        </td>
                        <td>
                            E: 13427
                            <hr class="rowSeparator">
                            R: 2284
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://bionlp.nlm.nih.gov/tac2017adversereactions/" target="_blank">TAC2017</a>
                        </td>
                        <td>
                            XML
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Drug labels
                        </td>
                        <td>
                            101
                        </td>
                        <td>
                            E: 6
                            <hr class="rowSeparator">
                            R: 3
                        </td>
                        <td>
                            E: 14486
                            <hr class="rowSeparator">
                            R: 2611
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://academic.oup.com/jamia/article-lookup/doi/10.1093/jamia/ocv092" target="_blank">TCMRelationExtraction</a>
                        </td>
                        <td>
                            TSV
                        </td>
                        <td>
                            Chinese
                        </td>
                        <td>
                            Abstracts
                        </td>
                        <td>
                            2683
                        </td>
                        <td>
                            E: 4
                        </td>
                        <td>
                            E: 5786
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://www.ncbi.nlm.nih.gov/pubmed/25755127" target="_blank">TweetADR</a>
                        </td>
                        <td>
                            TSV
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Tweets
                        </td>
                        <td>
                            799
                        </td>
                        <td>
                            E: 3
                        </td>
                        <td>
                            E: 554
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="https://publichealth.jmir.org/2017/2/e24/#app1" target="_blank">TwiMed</a>
                        </td>
                        <td>
                            BRAT
                        </td>
                        <td>
                            English
                        </td>
                        <td>
                            Tweets
                        </td>
                        <td>
                            693
                        </td>
                        <td>
                            E: 3
                            <hr class="rowSeparator">
                            R: 3
                        </td>
                        <td>
                            E: 1111
                            <hr class="rowSeparator">
                            R: 439
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
