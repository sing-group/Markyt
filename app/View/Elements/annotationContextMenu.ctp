<ul id="context-menu" class="dropdown-menu" role="menu" class="hidden" aria-labelledby="dLabel">
    
    <li><a><h5 class="page-header bold" style="margin: 0">Entity</h5></a></li>

    <li class="action"><a tabindex="1"><i class="fa fa-eye"></i>View</a></li>
    <li class="action"><a tabindex="2"><i class="fa fa-pencil-square-o"></i>Edit</a></li>
    <li class="action notEnd java-action" data-toggle="tooltip" data-placement="left" title="Annotate all terms like this with same type"><a tabindex="3"><i class="fa fa-history"></i>Annotate this term (All documents) </a></li>

    <li class="dropdown-submenu notEnd">
        <a tabindex="40" ><i class="fa fa-exchange yellow"></i>Change <label class="label label-primary">type</label></a>
        <ul class="dropdown-menu">
            <?php
            foreach ($types as $type) {
                ?>
                <li class="action"><a class="type" tabindex="4" data-type-id="<?php echo $type['Type']['id'] ?>" data-colour="<?php echo $type['Type']['colour'] ?>">
                        <div class="box-colour"  style="background-color: rgba(<?php echo $type['Type']['colour'] ?>);"></div>Change to: <?php echo $type['Type']['name'] ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>


    </li>
    <li class="dropdown-submenu notEnd java-action">
        <a tabindex="50"><i class="fa fa-exchange yellow"></i>Change <label class="label label-primary">type</label> (All documents)</a>
        <ul class="dropdown-menu">
            <?php
            foreach ($types as $type) {
                ?>
                <li class="action"><a class="type" tabindex="5" data-type-id="<?php echo $type['Type']['id'] ?>" data-colour="<?php echo $type['Type']['colour'] ?>">
                        <div class="box-colour"  style="background-color: rgba(<?php echo $type['Type']['colour'] ?>);"></div>Change to: <?php echo $type['Type']['name'] ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>


    </li>
    <li class="action notEnd"><a tabindex="6"><i class="fa fa-trash red"></i>Delete</a></li>
    <li class="action notEnd" data-toggle="tooltip" data-placement="left" title="Delete all terms with this type"><a tabindex="7"><i class="fa fa-trash red"></i>Delete all (with this <label class="label label-primary">type</label>)</a></li>
    <li class="action notEnd" data-toggle="tooltip" data-placement="left" title="Delete same terms with this type in all documents"><a tabindex="8"><i class="fa fa-trash red"></i>Delete (All documents)</a></li>
    <li class="action notEnd" data-toggle="tooltip" data-placement="left" title="Delete all parent annotations"><a tabindex="deleteParents"><i class="fa fa-trash red"></i>Delete outer</a></li>



    <?php
    if (!empty($relations)) {
        ?>
        <li class="divider"></li>
        <li><a><h5 class="page-header bold" style="margin: 0">Relation</h5></a></li>
        <li class="dropdown-submenu notEnd">
            <a tabindex="110"><i class="fa fa-share-alt"></i>Create</a>
            <ul class="dropdown-menu">
                <?php
                foreach ($relations as $relation) {
                    ?>
                    <li class="action"><a class="relation" tabindex="11" data-relation-id="<?php echo $relation['Relation']['id'] ?>" data-colour="<?php echo $relation['Relation']['colour'] ?>" data-marker="<?php echo $relation['Relation']['marker'] ?>" data-directed="<?php echo $relation['Relation']['is_directed'] ?>">
                            <div class="box-colour"  style="background-color: <?php echo $relation['Relation']['colour'] ?>">
                            </div>
                            <?php echo $relation['Relation']['name'] ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </li>
        <li class="action hasRelationOption"><a tabindex="12"><i class="fa fa-eye"></i>View</a></li>
        <li class="action notEnd hasRelationOption"><a tabindex="13"><i class="fa fa-trash red"></i>Delete all</a></li>
        <li class="action">
            <a tabindex="14"><i class="fa fa-eraser"></i>Clear (only <i class="fa fa-desktop"></i>)</a>
        </li>
        <?php
    }
    ?>


    <li class="divider"></li>
    <li><a><h5 class="page-header bold" style="margin: 0">Search</h5></a></li>
    <li class="action"><a tabindex="9"><i class="fa fa-google"></i>Google</a></li>
    <li class="dropdown-submenu">
        <a tabindex="100"><i class="fa fa-database"></i>Bio-Databases</a>
        <ul class="dropdown-menu">
            <li class="action"><a tabindex="10" data-database-id="0"><i class="fa fa-database"></i>All</a></li>
            <li class="action"><a tabindex="10" data-database-id="1">DrugBank</a></li>
            <li class="action"><a tabindex="10" data-database-id="2">PubChem</a></li>
            <li class="action"><a tabindex="10" data-database-id="3">EBI</a></li>
            <li class="action"><a tabindex="10" data-database-id="4">Uniprot</a></li>
            
            <li class="action"><a tabindex="10" data-database-id="6">PubMed</a></li>         
        </ul>
    </li>


</ul>
