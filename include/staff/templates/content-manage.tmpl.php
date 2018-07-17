<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><?php echo __('Manage Content'); ?> &mdash; <?php echo Format::htmlchars($content->getName()); ?></h3>
</div>
<div class="modal-body">
    <?php if ($errors['err']) { ?>
        <div class="error-banner">
            <?php echo $errors['err']; ?>
        </div>
    <?php } ?>
    <form method="post" id="form" action="#content/<?php echo $info['id']; ?>">
        <div class="error"><?php echo $errors['name']; ?></div>
        <input type="text" style="width: 100%; font-size: 14pt" name="name" value="<?php
        echo Format::htmlchars($info['name']); ?>" />
        <div>
            <div class="error"><?php echo $errors['body']; ?></div>
            <div id="toolbar"></div>
            <textarea class="richtext draft" name="body"  wrap="soft" data-toolbar-external="#toolbar"
                  data-draft-namespace="tpl.<?php echo Format::htmlchars($selected); ?>"
                  data-draft-object-id="<?php echo $tpl_id; ?>"><?php echo Format::viewableImages($info['body']); ?>
            </textarea>
        </div>
        <div id="msg_info" style="margin-top:7px"><?php echo $content->getNotes(); ?></div>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" data-dismiss="modal" class="btn btn-primary" value="<?php echo _('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input type="submit" id="salvar" class="btn btn-success" value="<?php echo _('Save Changes'); ?>">
            </span>
        </p>
    </form>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    
    $('#form').submit(function(){
        $.ajax({
          url: $('#form').attr('action'),
          type: 'POST',
          data : $('#form').serialize(),
          success: function(e){
            if(e != ''){
                $('#popup').modal('toggle');
            }
          }
        });
    });

});

</script>

<style>

    .full-width {
        margin-top: 10px !important;
    }

    #redactor_modal{
        margin: -250px !important;
    }

    .redactor_box.no-pjax{
        max-height: 450px;
    }

    .redactor_richtext.redactor_editor{
        max-height: 250px !important;
        overflow-y: scroll !important;
    }

    @media screen and (max-width: 450px) {

        .modal-dialog{
            overflow: scroll !important;
            height: 570px !important;
        }

        #redactor_modal{
            height: 420px !important;
            width: 100% !important;
            margin: 0px !important;
        }

        .redactor_modal_btn{
            width: 33.3% !important;
        }

    }

</style>