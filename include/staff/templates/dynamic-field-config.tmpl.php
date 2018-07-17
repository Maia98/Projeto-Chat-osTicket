    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <!--<h4 class="modal-title" id="myModalLabel"><?php echo $info['title']; ?></h4>-->
        <h3><?php echo __('Field Configuration'); ?> &mdash; <?php echo $field->get('label') ?></h3>
        <!--<b> <a class="close" href=""><i class="icon-remove-circle"></i></a></b>-->
    </div>
    <div class="modal-body">
        <form method="post" action="#form/field-config/<?php
                echo $field->get('id'); ?>">
            <div>
                <?php
                echo csrf_token();
                $form = $field->getConfigurationForm();
                echo $form->getMedia();
                foreach ($form->getFields() as $name=>$f) { ?>
                    <div class="flush-left custom-field" id="field<?php echo $f->getWidget()->id;
                        ?>"<?php if (!$f->isVisible()) echo 'style="display:none;"'; ?>>
                    <div class="field-label <?php if ($f->get('required')) echo 'required'; ?>">
                    <label for="<?php echo $f->getWidget()->name; ?>">
                        <?php echo Format::htmlchars($f->get('label')); ?>:
              <?php if ($f->get('required')) { ?>
                        <span class="error">*</span>
              <?php } ?>
                    </label>
                    </div><div>
                    <?php
                    $f->render();
                    ?>
                    <?php
                    if ($f->get('hint')) { ?>
                        <em style="color:gray;display:inline-block"><?php
                            echo Format::htmlchars($f->get('hint')); ?></em>
                        <?php
                    } ?>
                    </div>
                    <?php
                    foreach ($f->errors() as $e) { ?>
                        <div class="error"><?php echo $e; ?></div>
                    <?php } ?>
                    </div>
                <?php }
                ?>
                <div class="flush-left custom-field">
                <div class="field-label">
                <label for="hint"
                    style="vertical-align:top;"><?php echo __('Help Text') ?>:</label>
                </div>
                <div>
                <textarea style="width:100%; resize: vertical" name="hint" rows="2" cols="40"><?php
                    echo Format::htmlchars($field->get('hint')); ?></textarea>
                    <em style="color:gray;display:inline-block">
                        <?php echo __('Help text shown with the field'); ?></em>
                </div>
                </div>
                <hr>
                <p class="full-width">
                <span class="buttons pull-left">
                    <input class="btn btn-danger" type="button" value="<?php echo __('Cancel'); ?>" class="close"  onclick="$('#popup').modal('toggle');">
                </span>
                    <span class="buttons pull-right">
                    <input class="btn btn-primary" type="submit" value="<?php echo __('Save'); ?>"  onclick="javascript:   $('#sequences .save a').each(function() { $(this).trigger('click'); }); $('#popup').modal('toggle');location.reload(); ">
                </span>
                </p>
            </div>
        </form>
    </div>    
    <div class="clear"></div>
    <br />

    <script>
        $("form input[type=text], form select, form .ui-multiselect, textarea").addClass("form-control").css("width", "100%");
        $("form div span").each(function(index, value){
            var span  = $(this);
            span.css("display", "block");
            var input = span.find("input#_801036f05530c5fc");
            if(input.length > 0){
                span.css("width", "100%");
            }
        });


    </script>

    <style>

        .modal-content{
            max-width: 550px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .modal-content::-webkit-scrollbar{
            width: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb{
            width: 3px;
            background-color: #a9a9a9;
        }

        .flush-left{
            margin-bottom: 10px !important;
        }

        .flush-left input[type=checkbox]{
            margin-top: 3px !important;
        }

        .flush-left select, .flush-left textarea, .flush-left input{
            margin-bottom: 0px !important;
            margin-top: -5px !important;
        }

        .ui-multiselect{
            margin-top: -5px !important;
        }

    </style>

    <script>

        var over = 0;

        $(".modal-content").scroll(function () {
            if($(this).scrollTop() != over){
                $(".ui-state-active").click();
            }
        });

        $("button span").click(function () {
            over = $(".modal-content").scrollTop();
        });

        $(".modal-body .flush-left").each(function (index, value) {

            var div = $(value).find("div:last-child");
            $(div).css({
                "width" : "100%",
            });

            var type = $(div).find("input[type=checkbox]");

            if(type.length > 0){
                var text = $(div).find("em").text();
                $(div).find("em").remove();
                $(div).find("input").css({
                    "width" : "auto",
                    "float" : "left",
                    "margin-right" : "5px"
                });
                $(div).append(text);
            }

        });

        if($(window).width() > 450){
            $(".modal-content").css("max-height", "400px");
        }else{
            $(".modal-content").css("max-height", "550px");
        }

    </script>
