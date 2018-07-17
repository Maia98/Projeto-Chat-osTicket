<?php
if (!isset($info['title']))
    $info['title'] = Format::htmlchars($user->getName());

if ($info['title']) { ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title"><?php echo $info['title']; ?></h3>
<!--    <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
</div>
<?php
} else {
    echo '<div class="clear"></div>';
}
if ($info['error']) {
    echo sprintf('<p id="msg_error">%s</p>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
} ?>
<div class="modal-body">
    <div id="user-profile" style="display:<?php echo $forms ? 'none' : 'block'; ?>;margin:5px;">
        <i class="icon-user icon-4x pull-left icon-border"></i>
        <?php
        if ($ticket) { ?>
        <a class="btn btn-primary btn-sm pull-right change-user" style="overflow:inherit"
            href="#tickets/<?php echo $ticket->getId(); ?>/change-user" ><i class="icon-user"></i>
            <?php echo __('Change User'); ?></a>
        <?php
        } ?>
        <div><b><?php
        echo Format::htmlchars($user->getName()->getOriginal()); ?></b></div>
        <div class="faded">&lt;<?php echo $user->getEmail(); ?>&gt;</div>
        <?php
        if (($org=$user->getOrganization())) { ?>
        <div style="margin-top: 7px;"><?php echo $org->getName(); ?></div>
        <?php
        } ?>

    <div class="clear"></div>
    <ul class="tabs" style="margin-top:5px">
        <li><a href="#info-tab" class="active"
            ><i class="icon-info-sign"></i>&nbsp;<?php echo __('User'); ?></a></li>
    <?php if ($org) { ?>
        <li><a href="#organization-tab"
            ><i class="icon-fixed-width icon-building"></i>&nbsp;<?php echo __('Organization'); ?></a></li>
    <?php }
        $ext_id = "U".$user->getId();
        $notes = QuickNote::forUser($user, $org)->all(); ?>
        <li><a href="#notes-tab"
            ><i class="icon-fixed-width icon-pushpin"></i>&nbsp;<?php echo __('Notes'); ?></a></li>
    </ul>

    <div class="tab_content" id="info-tab">
    <div class="floating-options">
        <a href="<?php echo $info['useredit'] ?: '#'; ?>" id="edituser" class="action" title="<?php echo __('Edit'); ?>"><i class="icon-edit"></i></a>
        <a id="hidden" href="users.php?id=<?php echo $user->getId(); ?>" title="<?php
            echo __('Manage User'); ?>" class="action"><i class="icon-share"></i></a>
    </div>
        <table class="custom-info" width="100%">
    <?php foreach ($user->getDynamicData() as $entry) {
    ?>
        <tr><th colspan="2"><strong><?php
             echo $entry->getForm()->get('title'); ?></strong></td></tr>
    <?php foreach ($entry->getAnswers() as $a) { ?>
        <tr><td style="width:30%;"><strong><?php echo Format::htmlchars($a->getField()->get('label'));
                    ?>:</strong></td>
        <td><?php echo $a->display(); ?></td>
        </tr>
    <?php }
    }
    ?>
        </table>
    </div>

    <?php if ($org) { ?>
    <div class="tab_content" id="organization-tab" style="display:none">
    <div class="floating-options">
        <a href="orgs.php?id=<?php echo $org->getId(); ?>" title="<?php
        echo __('Manage Organization'); ?>" class="action"><i class="icon-share"></i></a>
    </div>
        <table class="custom-info" width="100%">
    <?php foreach ($org->getDynamicData() as $entry) {
    ?>
        <tr><th colspan="2"><strong><?php
             echo $entry->getForm()->get('title'); ?></strong></td></tr>
    <?php foreach ($entry->getAnswers() as $a) { ?>
        <tr><td style="width:30%"><?php echo Format::htmlchars($a->getField()->get('label'));
             ?>:</td>
        <td><?php echo $a->display(); ?></td>
        </tr>
    <?php }
    }
    ?>
        </table>
    </div>
    <?php } # endif ($org) ?>

    <div class="tab_content" id="notes-tab" style="display:none">
    <?php $show_options = true;
    foreach ($notes as $note)
        include STAFFINC_DIR . 'templates/note.tmpl.php';
    ?>
        <div id="new-note-box">
            <div class="quicknote no-options" id="new-note"
                data-url="users/<?php echo $user->getId(); ?>/note">
                <div class="body">
                    <a href="#"><i class="icon-plus icon-large"></i> &nbsp;
                    <?php echo __('Click to create a new note'); ?></a>
                </div>
            </div>
        </div>
    </div>

    </div>
    <div id="user-form" style="display:<?php echo $forms ? 'block' : 'none'; ?>;">
    <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; <?php echo __(
    'Please note that updates will be reflected system-wide.'
    ); ?></p></div>
    <?php
    $action = $info['action'] ? $info['action'] : ('#users/'.$user->getId());
    if ($ticket && $ticket->getOwnerId() == $user->getId())
        $action = '#tickets/'.$ticket->getId().'/user';
    ?>
    <form method="post" class="user" action="<?php echo $action; ?>">
        <input type="hidden" name="uid" value="<?php echo $user->getId(); ?>" />
        <table class="user-update" width="100%">
        <?php
            if (!$forms) $forms = $user->getForms();
            foreach ($forms as $form)
                $form->render();
        ?>
        </table>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                    <?php echo __('Cancel'); ?>
                </button>
            </span>
            <span class="buttons pull-right">
                <input style="margin-top: 0px" class="btn btn-success teste" type="submit" value="<?php echo __('Update User'); ?>">
            </span>
         </p>
    </form>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {

    $('a#edituser').click( function(e) {
        e.preventDefault();
        if ($(this).attr('href').length > 1) {
            var url = 'ajax.php/'+$(this).attr('href').substr(1);
            $.dialog(url, [201, 204], function (xhr) {
                window.location.href = window.location.href;
            }, {
                onshow: function() { $('#user-search').focus(); }
            });
        } else {
            $('div#user-profile').hide();
            $('div#user-form').fadeIn();
        }

        return false;
     });

    $(document).on('click', 'form.user input.cancel', function (e) {
        e.preventDefault();
        $('div#user-form').hide();
        $('div#user-profile').fadeIn();
        return false;
     });
});

    $("form select, form textarea").attr("class", "form-control");

    $('form').on('submit', function () {
        $(".modal-backdrop").removeAttr("class").attr("class", "modal-backdrop fade out").css("display", "none");
    });

    $("table tr").each(function(index, value){

        var tr      = $(value);
        var tdName  = tr.find("td:first");
        var tdInput = tr.find("td:last");
        var error   = tdInput.find("font");
        if(error.length > 0){
            tdName.append(error);
        }
    });

</script>

<style>

    .modal-content{
        max-height: 550px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    select, textarea{
        width: 320px !important;
    }

    @media screen and (max-width: 450px) {

        select, textarea{
            width: 100% !important;
        }

        table.user-update{
            display: table;
            border: 0 !important;
        }

        table.user-update tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.user-update tr td{
            width:100%;
            display: table;
            /*margin-bottom: 10px !important;*/
            border: 0 !important;
            /*padding: 10px !important;*/
        }

        table.user-update tr td i, table.user-update tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.user-update tr td input[type=radio], table.user-update tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.user-update tr td input, table.user-update tr td select{
            margin-top: 10px !important;
        }

        table.user-update tr td input[type=text], table.user-update tr td select{
            margin: 0 auto !important;
        }

        table.user-update tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        input[type=text], select{
            width: 100% !important;
        }

        textarea{
            width: 100% !important;
        }

        #hidden{
            display: none !important;
        }

        /*.modal-body{*/
            /*height: 555px !important;*/
        /*}*/
    }

</style>

<script>

    $("table.user-update tr td").each(function(index, value){
        var tr    = $(value);
        var span  = tr.find("span");
        var input = tr.find("input");
        if(input.length > 0){
            $(span).css("width" ,"100%");
        }
    });

</script>
