
<div class="col-lg-12">Based on annotations already made in this round</div>
<div class="col-lg-12"> 
    <div class="input">

        <h3><input type="checkbox" name="check All" value="All" id="checkAll"/> <span class="label label-default"> Check All</span></h3>
    </div>
</div>
<div  class="selectedTypes">
    <div class="col-md-6">
        <?php
        for ($index = 0; $index < count($types) / 2; $index++) {
            $type = $types[$index];
            ?>
            <div class="input">
                <input type="checkbox" name="data[Type][<?php echo $type['Type']['id'] ?>]" value="<?php echo $type['Type']['id'] ?>" />
                <span class="label label-default" style="background-color: rgba(<?php echo $type['Type']['colour'] ?> )"><?php echo $type['Type']['name'] ?></span>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="col-md-6">
        <?php
        for ($index = count($types) / 2; $index < count($types); $index++) {
            $type = $types[$index];
            ?>
            <div class="input">
                <input type="checkbox" name="data[Type][<?php echo $type['Type']['id'] ?>]" value="<?php echo $type['Type']['id'] ?>" />
                <span class="label label-default" style="background-color: rgba(<?php echo $type['Type']['colour'] ?> )"><?php echo $type['Type']['name'] ?></span>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="clear"></div>
</div>



