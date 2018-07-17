<?php
if (!$info['title'])
    $info['title'] = Format::htmlchars($org->getName());
?>
<div class="modal-header">
    <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
    <h3><?php echo $info['title']; ?></h3>
</div>
<?php
if ($info['error']) {
    echo sprintf('<p id="msg_error">%s</p>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
} ?>
<div id="org-profile" style="display:<?php echo $forms ? 'none' : 'block'; ?>;margin:5px;">
    <i class="icon-group icon-4x pull-left icon-border"></i>
    <?php
    if ($user) { ?>
    <a class="btn-xs action-button pull-right user-action" style="overflow:inherit"
        href="#users/<?php echo $user->getId(); ?>/org/<?php echo $org->getId(); ?>" ><i class="icon-user"></i>
        <?php echo __('Change'); ?></a>
    <a class="btn-xs action-button pull-right" href="orgs.php?id=<?php echo $org->getId(); ?>"><i class="icon-share"></i>
        <?php echo __('Manage'); ?></a>
    <?php
    } ?>
    <div><b><a href="#" id="editorg"><i class="icon-edit"></i>&nbsp;<?php
    echo Format::htmlchars($org->getName()); ?></a></b></div>
    <table style="margin-top: 1em;">
<?php foreach ($org->getDynamicData() as $entry) {
?>
    <tr><td colspan="2" style="border-bottom: 1px dotted black"><strong><?php
         echo $entry->getForm()->get('title'); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr style="vertical-align:top"><td style="width:30%;border-bottom: 1px dotted #ccc"><?php echo Format::htmlchars($a->getField()->get('label'));
         ?>:</td>
    <td style="border-bottom: 1px dotted #ccc"><?php echo $a->display(); ?></td>
    </tr>
<?php }
}
?>
    </table>
    <div class="clear"></div>
    <hr>
    <div class="faded">Last updated <b><?php echo Format::db_datetime($org->getUpdateDate()); ?> </b></div>
</div>
<div class="modal-body">
<div id="org-form" style="display:<?php echo $forms ? 'block' : 'none'; ?>;">
<div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; <?php echo __(
'Please note that updates will be reflected system-wide.'); ?></p></div>
<?php
$action = $info['action'] ? $info['action'] : ('#orgs/'.$org->getId());
if ($ticket && $ticket->getOwnerId() == $user->getId())
    $action = '#tickets/'.$ticket->getId().'/user';
?>
<form method="post" class="org" action="<?php echo $action; ?>">
    <input type="hidden" name="id" value="<?php echo $org->getId(); ?>" />
    <table class="table-support-org" width="100%">
    <?php
        if (!$forms) $forms = $org->getForms();
        foreach ($forms as $form)
            $form->render();
    ?>
    </table>
    <hr>

    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" class="btn btn-danger" id="close-modal" value="<?php echo __('Cancel');?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" class="btn btn-success" value="<?php echo __('Update Organization'); ?>">
        </span>
    </p>
</form>
</div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<script type="text/javascript">
$(function() {
    $('a#editorg').click( function(e) {
        e.preventDefault();
        $('div#org-profile').hide();
        $('div#org-form').fadeIn();
        return false;
     });

    $(document).on('click', 'form.org input.cancel', function (e) {
        e.preventDefault();
        $('div#org-form').hide();
        $('div#org-profile').fadeIn();
        return false;
     });

    $("body").on("click", ".modal-body #close-modal", function (e) {
        e.preventDefault();
        $(this).parents('div.dialog').hide();
        $.toggleOverlay(false);
        return false;
    });
});
</script>

<style>

    .modal-content{
        max-height: 550px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    .action-button{
        display: inline-block;
        padding: 3px 6px;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border-radius: 3px;
        color: #fff !important;
        background-color: #337ab7;
        border-color: #2e6da4;
    }

    table.table-support-org span{
        width: 100%;
    }

    table.table-support-org textarea, table.table-support-org input[type=text], table.table-support-org select{
        width: 100% !important;
    }

    @media screen and (max-width: 450px) {

        table.table-support-org{
            display: table;
            border: 0 !important;
        }

        table.table-support-org tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.table-support-org tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.table-support-org tr td span{
            width: 100%;
        }

        table.table-support-org tr td i, table.table-support-org tr th i{
            margin-top: 5px !important;
            float: right;
        }

        table.table-support-org .col-xs-12{
            padding: 0 !important;
        }

        table.table-support-org tr td input[type=radio], table.table-support-org tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.table-support-org tr td input, table.table-support-org tr td select{
            margin-top: 10px !important;
        }

        table.table-support-org tr td input[type=text], table.table-support-org tr td select{
            margin: 0 auto !important;
        }

        table.table-support-org tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }
    }

</style>

<script>
    $("table.table-support-org tr td").each(function (index, value) {
        var td     = $(value);
        var tdPrev = td.prev();
        var error  = td.find("font.error");
        if(error.length > 0){
            if(tdPrev.hasClass("required")){
                td.find("font.error").remove();
                tdPrev.append("<span class='error'>*</span>");
                tdPrev.append(error);
                tdPrev.find("font.error").each(function (index, value) {
                    if($(value).text() == "*"){
                        $(value).remove();
                    }else{
                        $(value).css("display", "block");
                    }
                });
            }
        }
    });
</script>
