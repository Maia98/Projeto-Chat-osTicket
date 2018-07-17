<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><?php echo __('Item Properties'); ?> &mdash; <?php echo $item->getValue() ?></h3>
<!--    <a class="close" href=""><i class="icon-remove-circle"></i></a>-->
</div>
<div class="modal-body">
    <form method="post" action="#list/<?php
            echo $list->getId(); ?>/item/<?php
            echo $item->getId(); ?>/properties">
        <?php
        echo csrf_token();
        $config = $item->getConfiguration();
        $internal = $item->isInternal();
        $form = $item->getConfigurationForm();
        echo $form->getMedia();
        foreach ($form->getFields() as $f) {
            ?>
            <div class="custom-field" id="field<?php
                echo $f->getWidget()->id; ?>"
                <?php
                if (!$f->isVisible()) echo 'style="display:none;"'; ?>>
            <div class="field-label">
            <label for="<?php echo $f->getWidget()->name; ?>"
                style="vertical-align:top;padding-top:0.2em">
                <?php echo Format::htmlchars($f->get('label')); ?>:</label>
                <?php
                if (!$internal && $f->isEditable() && $f->get('hint')) { ?>
                    <br /><em style="color:gray;display:inline-block"><?php
                        echo Format::htmlchars($f->get('hint')); ?></em>
                <?php
                } ?>
            </div><div>
            <?php
            if ($internal && !$f->isEditable())
                $f->render('view');
            else {
                $f->render();
                if ($f->get('required')) { ?>
                    <font class="error">*</font>
                <?php
                }
            }
            ?>
            </div>
            <?php
            foreach ($f->errors() as $e) { ?>
                <div class="error"><?php echo $e; ?></div>
            <?php } ?>
            </div>
            <?php
        }
        ?>
        </table>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <input class="btn btn-danger" data-dismiss="modal" type="button" value="<?php echo __('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input class="btn btn-primary" type="submit" value="<?php echo __('Save'); ?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>

<style>

    textarea{
        width: 100%;
    }

    @media screen and (max-width: 450px) {

        input[type=reset], input[type=button], input[type=submit]{
            margin-top: 10px !important;
            width: 100% !important;
        }

    }

</style>

<script>

    $("select").addClass("form-control");

</script>
