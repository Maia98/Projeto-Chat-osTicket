<?php
$info=($_POST && $errors)?Format::input($_POST):@Format::htmlchars($org->getInfo());

if (!$info['title'])
    $info['title'] = Format::htmlchars($org->getName());
?>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.multiselect.min.js?19292ad"></script>
<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/jquery.multiselect.css?19292ad"/>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><?php echo $info['title']; ?></h3>
    <!--<b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
</div>

<?php
if ($info['error']) {
    echo sprintf('<p id="msg_error">%s</p>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
} ?>
<ul class="tabs ul-responsive">
    <li><a href="#tab-profile" class="active"
        ><i class="icon-edit"></i>&nbsp;<?php echo __('Fields'); ?></a></li>
    <li><a href="#contact-settings"
        ><i class="icon-fixed-width icon-cogs faded"></i>&nbsp;<?php
        echo __('Settings'); ?></a></li>
</ul>
<div class="modal-body">
    <form method="post" class="org" action="<?php echo $action; ?>">

    <div class="tab_content" id="tab-profile" style="margin:5px;">
    <?php
    $action = $info['action'] ? $info['action'] : ('#orgs/'.$org->getId());
    if ($ticket && $ticket->getOwnerId() == $user->getId())
        $action = '#tickets/'.$ticket->getId().'/user';
    ?>
        <input type="hidden" name="id" value="<?php echo $org->getId(); ?>" />
        <table class="table-field" width="100%">
        <?php
            if (!$forms) $forms = $org->getForms();
            foreach ($forms as $form)
                $form->render();
        ?>
        </table>
    </div>

    <div class="tab_content" id="contact-settings" style="display:none;margin:5px;">
        <table class="table-config" style="width:100%">
            <tbody>
                <tr>
                    <td width="180">
                        <?php echo __('Account Manager'); ?>:
                        <font class="error"><?php echo $errors['manager']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control" name="manager">
                                <option value="0" selected="selected">&mdash; <?php
                                    echo __('None'); ?> &mdash;</option><?php
                                if ($users=Staff::getAvailableStaffMembers()) { ?>
                                    <optgroup label="<?php
                                        echo sprintf(__('Agents (%d)'), count($users)); ?>">
        <?php                       foreach($users as $id => $name) {
                                        $k = "s$id";
                                        echo sprintf('<option value="%s" %s>%s</option>',
                                            $k,(($info['manager']==$k)?'selected="selected"':''),$name);
                                    }
                                    echo '</optgroup>';
                                }

                                if ($teams=Team::getActiveTeams()) { ?>
                                    <optgroup label="<?php echo sprintf(__('Teams (%d)'), count($teams)); ?>">
        <?php                       foreach($teams as $id => $name) {
                                        $k="t$id";
                                        echo sprintf('<option value="%s" %s>%s</option>',
                                            $k,(($info['manager']==$k)?'selected="selected"':''),$name);
                                    }
                                    echo '</optgroup>';
                                } ?>
                            </select>
                        </div>
                        <div class="division-margin"></div>
                    </td>
                </tr>
                <tr>
                    <td width="180">
                        <?php echo __('Auto-Assignment'); ?>:
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="checkbox" name="assign-am-flag" value="1" <?php echo $info['assign-am-flag']?'checked="checked"':''; ?>>
                            <?php echo __(
                                'Assign tickets from this organization to the <em>Account Manager</em>'); ?>
                        </div>
                        <div class="division-margin"></div>
                </tr>
                <tr>
                    <td width="180">
                        <?php echo __('Primary Contacts'); ?>:
                        <font class="error"><?php echo $errors['contacts']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                        <select name="contacts[]" id="primary_contacts" multiple="multiple">
    <?php               foreach ($org->allMembers() as $u) { ?>
                            <option value="<?php echo $u->id; ?>" <?php
                                if ($u->isPrimaryContact())
                                echo 'selected="selected"'; ?>><?php echo $u->getName(); ?></option>
    <?php               } ?>
                        </select>
                        </div>
                        <div class="division-margin"></div>
                    </td>
                <tr>
                    <th colspan="2">
                        <?php echo __('Automated Collaboration'); ?>:
                    </th>
                </tr>
                <tr>
                    <div class="division-margin"></div>
                    <td width="180">
                        <?php echo __('Primary Contacts'); ?>:
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                        <input type="checkbox" name="collab-pc-flag" value="1" <?php echo $info['collab-pc-flag']?'checked="checked"':''; ?>>
                        <?php echo __('Add to all tickets from this organization'); ?>
                        </div>
                        <div class="division-margin"></div>
                    </td>
                </tr>
                <tr>
                    <td width="180">
                        <?php echo __('Organization Members'); ?>:
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                        <input type="checkbox" name="collab-all-flag" value="1" <?php echo $info['collab-all-flag']?'checked="checked"':''; ?>>
                        <?php echo __('Add to all tickets from this organization'); ?>
                        </div>
                        <div class="division-margin"></div>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <?php echo __('Main Domain'); ?>
                    </th>
                </tr>
                <tr>
                    <td style="width:180px">
                        <?php echo __('Auto Add Members From'); ?>:
                        <font class="error"><?php echo $errors['domain']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control" maxlength="60" name="domain"
                                   value="<?php echo $info['domain']; ?>" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="clear"></div>

    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                <?php echo __('Cancel'); ?>
            </button>
        </span>
        <span class="buttons pull-right">
            <input class="btn btn-success" type="submit" value="<?php echo __('Update Organization'); ?>">
        </span>
    </p>
    <br>
    </form>
</div>
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
    $("#primary_contacts").multiselect({'noneSelectedText':'<?php echo __('Select Contacts'); ?>'});
});
</script>

<style>

    .modal-body input[type=text], select, textarea{
        width: 100%;
    }

    .modal-body table.table-config .ui-multiselect{
        width: 100% !important;
    }

    @media screen and (max-width: 450px) {

        .modal-body table.table-field, .modal-body table.table-config {
            display: table;
            border: 0 !important;
        }

        .modal-body table.table-field tr, .modal-body table.table-config tr {
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        .modal-body table.table-field tr td, .modal-body table.table-config tr td {
            width: 100%;
            display: table;
            margin-bottom: 0px !important;
            /*border-bottom: 1px dotted #ccc !important;*/
            /*padding: 10px !important;*/
        }

        .modal-body table.table-field tr td i, .modal-body table.table-field tr th i, .modal-body table.table-config tr td i, .modal-body table.table-config tr th i {
            margin-top: 5px !important;
            float: right;
        }

        .modal-body .col-xs-12 {
            padding: 0 !important;
        }

        .modal-body table.table-field tr td input[type=radio], .modal-body table.table-field tr td input[type=checkbox], .modal-body table.table-config tr td input[type=radio], .modal-body table.table-config tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        .modal-body table.table-field tr td input, .modal-body table.table-field tr td select, .modal-body table.table-config tr td input, .modal-body table.table-config tr td select {
            margin-top: 10px !important;
        }

        .modal-body table.table-field tr td input[type=text], .modal-body table.table-field tr td select, .modal-body table.table-config tr td input[type=text], .modal-body table.table-config tr td select {
            margin: 0 auto !important;
        }

        .modal-body table.table-field tr td label, .modal-body table.table-config tr td label {
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .modal-body select, .modal-body textarea, .modal-body input[type=text]{
            width: 100% !important;
            margin-left: 0px;
        }
        
        .modal-body table.table-config .division-margin{
            clear: both;
            margin-bottom: 10px;
        }

        .modal-body table.table-config .ui-multiselect{
            width: 100% !important;
        }

        .modal-body .ul-responsive{
            width: 100%;
            height: auto !important;
            padding-left: 0 !important;
        }

        .modal-body .ul-responsive li a {
            width:100% !important;
        }

        .org-tabs .division-org{
            clear: both !important;
            margin-bottom: 10px !important;
        }

    }

</style>

<script>

    $(".modal-body table.table-field tr td").each(function (index, value) {
        var td     = $(this);
        var tdPrev = $(this).prev();
        var font = $(value).find("font.error");

        if(font.length > 0){
            var text = font.text().replace(/\s/g, '');
            if(text == "*"){
                font.remove();
                $(tdPrev).append("<span class='error'>*</span>");
            }else{
                var textTwo = font.text().substring(font.text().indexOf("*") + 1);
                $(tdPrev).append("<span class='error'>*</span>");
                $(tdPrev).append("<font class='error' style='display: block'>"+textTwo+"</font>");
                font.remove();
            }
        }
    });

    $(".modal-body table.table-field tr td").each(function(index, value){
        var tr    = $(value);
        var span  = tr.find("span");
        var input = tr.find("input");
        var select = tr.find("select");
        if(input.length > 0){
            $(span).css("width" ,"100%");
        }

        if(select.length > 0){
            $(select).css("width", "100%");
        }
    });

</script>
