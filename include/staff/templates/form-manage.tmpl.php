<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><i class="icon-paste"></i> <?php echo __('Manage Forms'); ?></i></h3>
    <!--    <h3>--><?php //echo $info['title']; ?><!--</h3>-->
    <!--    <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
</div>


<div class="modal-body">
    <?php echo __(
        'Sort the forms on this ticket by click and dragging on them. Use the box below the forms list to add new forms to the ticket.'
    ); ?>
    <br/>
    <br/>
    <form method="post" action="<?php echo $info['action']; ?>">
    <div id="ticket-entries">
    <?php
    $current_list = array();
    foreach ($forms as $e) { ?>
    <div class="sortable row-item" data-id="<?php echo $e->get('id'); ?>">
        <input type="hidden" name="forms[]" value="<?php echo $e->get('form_id'); ?>" />
        <i class="icon-reorder"></i> <?php echo $e->getForm()->getTitle();
        $current_list[] = $e->get('form_id');
        if ($e->getForm()->get('type') == 'G') { ?>
        <div class="button-group">
        <div class="delete"><a href="#"><i class="icon-trash"></i></a></div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
    </div>
    <hr/>
    <i class="icon-plus"></i>&nbsp;
    <select name="new-form" onchange="javascript:
        var $sel = $(this).find('option:selected');
        $('#ticket-entries').append($('<div></div>').addClass('sortable row-item')
            .text(' '+$sel.text())
            .data('id', $sel.val())
            .prepend($('<i>').addClass('icon-reorder'))
            .append($('<input/>').attr({name:'forms[]', type:'hidden'}).val($sel.val()))
            .append($('<div></div>').addClass('button-group')
              .append($('<div></div>').addClass('delete')
                .append($('<a href=\'#\'>').append($('<i>').addClass('icon-trash')))
            ))
        );
        $sel.prop('disabled',true);">
    <option selected="selected" disabled="disabled"><?php
        echo __('Add a form'); ?></option>
    <?php foreach (DynamicForm::objects()->filter(array(
        'type'=>'G')) as $f
    ) {
        if (in_array($f->get('id'), $current_list))
            continue;
        ?><option value="<?php echo $f->get('id'); ?>"><?php
        echo $f->getTitle(); ?></option><?php
    } ?>
    </select>
    <div id="delete-warning" style="display:none">
    <hr>
        <div id="msg_warning"><?php echo __(
        'Clicking <strong>Save Changes</strong> will permanently delete data associated with the deleted forms'
        ); ?>
        </div>
    </div>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                    <?php echo __('Cancel'); ?>
                </button>
            </span>
            <span class="buttons pull-right">
                <input class="btn btn-success confirm-action" type="submit" value="<?php echo __('Save Changes'); ?>">
            </span>
         </p>
        </br>
    </form>
</div>
<script type="text/javascript">
$(function() {
    $('#ticket-entries').sortable({containment:'parent',tolerance:'pointer'});
    $('#ticket-entries .delete a').live('click', function() {
        var $div = $(this).closest('.sortable.row-item');
        $('select[name=new-form]').find('option[data-id='+$div.data('id')+']')
            .prop('disabled',false);
        $div.remove();
        $('#delete-warning').show();
        return false;
    })

    $('input[type=submit]').click(function(){
        location.reload();
    });

});
</script>

<style>

    .modal-content{
        max-height: 600px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    @media screen and (max-width: 450px) {

        .modal-content{
            height: 390px !important;
        }

    }

</style>
