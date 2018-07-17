<?php
global $cfg;

if (!$info['title'])
    $info['title'] = 'Change Tickets Status';

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><?php echo $info['title']; ?></h3>
<!--    <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
</div>
<div class="clear"></div>
<?php
if ($info['error']) {
    echo sprintf('<p id="msg_error">%s</p>', $info['error']);
} elseif ($info['warn']) {
    echo sprintf('<p id="msg_warning">%s</p>', $info['warn']);
} elseif ($info['msg']) {
    echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
} elseif ($info['notice']) {
   echo sprintf('<p id="msg_info"><i class="icon-info-sign"></i> %s</p>',
           $info['notice']);
}


$action = $info['action'] ?: ('#tickets/status/'. $state);
?>
<div class="modal-body">
    <div id="ticket-status" style="display:block; margin:5px;">
        <form method="post" name="status" id="status"
            action="<?php echo $action; ?>">
            <table width="100%">
                <?php
                if ($info['extra']) {
                    ?>
                <tbody>
                    <tr><td colspan="2"><strong><?php echo $info['extra'];
                    ?></strong></td> </tr>
                </tbody>
                <?php
                }

                $verb = '';
                if ($state) {
                    $statuses = TicketStatusList::getStatuses(array('states'=>array($state)))->all();
                    $verb = TicketStateField::getVerb($state);
                }

                if ($statuses) {
                ?>
                <tbody>
                    <tr>
                        <td colspan=2>
                            <span>
                            <?php
                            if (count($statuses) > 1) { ?>
                                <strong><?php echo __('Status') ?>:&nbsp;</strong><font class="error">*&nbsp;<?php echo $errors['status_id']; ?></font>
                                <select class="form-control" name="status_id">
                                <?php
                                foreach ($statuses as $s) {
                                    echo sprintf('<option value="%d" %s>%s</option>',
                                            $s->getId(),
                                            ($info['status_id'] == $s->getId())
                                             ? 'selected="selected"' : '',
                                            $s->getName()
                                            );
                                }
                                ?>
                                </select>
                            <?php
                            } elseif ($statuses[0]) {
                                echo  "<input type='hidden' name='status_id' value={$statuses[0]->getId()} />";
                            } ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
                <?php
                } ?>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <?php
                            $placeholder = $info['placeholder'] ?: __('Optional reason for status change (internal note)');
                            ?>
                            <textarea name="comments" id="comments"
                                cols="50" rows="3" style="white-space: normal; width:100%"
                                class="richtext ifhtml no-bar"
                                placeholder="<?php echo $placeholder; ?>"><?php
                                echo $info['comments']; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <p class="full-width">
                <span class="buttons pull-left">
                    <button data-dismiss="modal"  id="close-modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                        <?php echo __('Cancel'); ?>
                    </button>
                </span>
                <span class="buttons pull-right">
                    <input type="submit" class="btn btn-success" value="<?php
                    echo Finalizar ?: __('Submit'); ?>">
                </span>
             </p>
            <br>
        </form>
    </div>
</div>
<div class="clear"></div>

<script type="text/javascript">
$(function() {
    // Copy checked tickets to status form.
    $('form#tickets input[name="tids[]"]:checkbox:checked')
    .each(function() {
        $('<input>')
        .prop('type', 'hidden')
        .attr('name', 'tids[]')
        .val($(this).val())
        .appendTo('form#status');
    });
});
</script>

<style>

    .modal-content{
        max-height: 400px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    @media screen and (max-width: 450px) {

        select{
            width: 100% !important;
        }

        textarea {
            word-wrap: break-word !important;
            width: 95%;
        }

    }

</style>
