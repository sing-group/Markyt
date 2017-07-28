<div id="documentBody">
    <input type="hidden" id="interRelationsMap"  name="interRelationsMap" value='<?php echo json_encode($interRelationsMap) ?>'>

    <?php
    if (!$isMultiDocument) {
        echo $this->Session->flash();
        if (strpos($text, 'class="onlyAbstract"') === false) {
            ?>
            <div>
                <div>
                    <h2 id="title">
                        <?php
                        echo $title;
                        $class = "";
                        if (isset($documentAssessments[$document_id])) {
                            $class = "used";
                        }
                        ?>
                        <span class="rate-document <?php echo $class ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>" data-document-id="<?php echo $document_id ?>" ><i class="fa fa-star"></i></span>
                    </h2>
                </div>

            </div>
            <?php
        }
        ?>
        <div id="textContent" data-document-id="<?php echo $document_id ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>"   class="minRequired <?php if ($isEnd) echo 'roundIsEnd'; ?>" >
            <?php
            echo $text;
            ?>
        </div>
        <?php
    }
    else {
        foreach ($annotatedDocuments as $annotatedDocument) {
            $document_id = $annotatedDocument['document_id'];
            $document_annotated_id = $annotatedDocument['id'];
            $text = $annotatedDocument['text_marked'];
            ?>
            <a name="<?php echo $document_id ?>" class="anchor" id="anchor-<?php echo $document_annotated_id ?>"  data-referer="<?php echo $document_annotated_id ?>">anchor-<?php echo $document_annotated_id ?></a>

            <div class="documentToAnnotate">
                <div>
                    <h2 id="title">
                        <?php
                        echo $annotatedDocument['title'];
                        $class = "";
                        if (isset($documentAssessments[$document_id])) {
                            $class = "used";
                        }
                        ?>
                        <span class="rate-document <?php echo $class ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>" data-document-id="<?php echo $document_id ?>"><i class="fa fa-star"></i></span>
                    </h2>
                </div>
                <div id="<?php echo $document_annotated_id ?>" data-document-id="<?php echo $document_id ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>" class="textContent minRequired <?php if ($isEnd) echo 'roundIsEnd'; ?>" >
                    <?php
                    echo $text;
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>