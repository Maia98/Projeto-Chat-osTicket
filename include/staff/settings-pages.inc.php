<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
$pages = Page::getPages();
?>
<h2><?php echo __('Company Profile'); ?></h2>
<form action="settings.php?t=pages" method="post" id="save"
    enctype="multipart/form-data">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="pages" >
<table class="form_table table-pattern settings_table table-script" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Basic Information'); ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php
        $form = $ost->company->getForm();
        $form->addMissingFields();
        $form->render();
    ?>
    </tbody>
</table>
<table class="form_table table-pattern settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Site Pages'); ?></h4>
                <em><?php echo sprintf(__(
                'To edit or add new pages go to %s Manage &gt; Site Pages %s'),
                '<a href="pages.php">','</a>'); ?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required" width="270"><?php echo __('Landing Page'); ?>:<font class="error">*&nbsp;<?php echo $errors['landing_page_id']; ?></font><i class="help-tip icon-question-sign" href="#landing_page"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="landing_page_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Select Landing Page'); ?> &mdash;</option>
                        <?php
                        foreach($pages as $page) {
                            if(strcasecmp($page->getType(), 'landing')) continue;
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $page->getId(),
                                    ($config['landing_page_id']==$page->getId())?'selected="selected"':'',
                                    $page->getName());
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required" width="270"><?php echo __('Offline Page'); ?>:<font class="error">*&nbsp;<?php echo $errors['offline_page_id']; ?></font><i class="help-tip icon-question-sign" href="#offline_page"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="offline_page_id" class="form-control">
                    <option value="">&mdash; <?php echo __('Select Offline Page');
                        ?> &mdash;</option>
                    <?php
                    foreach($pages as $page) {
                        if(strcasecmp($page->getType(), 'offline')) continue;
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $page->getId(),
                                ($config['offline_page_id']==$page->getId())?'selected="selected"':'',
                                $page->getName());
                    } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required" width="270"><?php
                echo __('Default Thank-You Page'); ?>:<font class="error">*&nbsp;<?php echo $errors['thank-you_page_id']; ?></font><i class="help-tip icon-question-sign" href="#default_thank_you_page"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="thank-you_page_id" class="form-control">
                        <option value="">&mdash; <?php
                            echo __('Select Thank-You Page'); ?> &mdash;</option>
                        <?php
                        foreach($pages as $page) {
                            if(strcasecmp($page->getType(), 'thank-you')) continue;
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $page->getId(),
                                    ($config['thank-you_page_id']==$page->getId())?'selected="selected"':'',
                                    $page->getName());
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<table class="form_table table-logo settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Logos'); ?>
                    <i class="help-tip icon-question-sign allign-tip" href="#logos"></i>
                    </h4>
                <em><?php echo __('System Default Logo'); ?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
        <td colspan="2">
<table class="table-logo" style="width:100%">
    <thead><tr>
        <th>Client</th>
        <th>Staff</th>
        <th>Logo</th>
    </tr></thead>
    <tbody>
        <tr>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="selected-logo" value="0"
                        <?php if (!$ost->getConfig()->getClientLogoId())
                            echo 'checked="checked"'; ?>/>
                </div>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="selected-logo-scp" value="0"
                        <?php if (!$ost->getConfig()->getStaffLogoId())
                            echo 'checked="checked"'; ?>/>
                </div>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <img src="<?php echo ROOT_PATH; ?>assets/default/images/logo.png"
                        alt="Default Logo" valign="middle"
                        style="box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
                            margin: 0.5em; height: 5em;
                            vertical-align: middle"/>

                    <img src="<?php echo ROOT_PATH; ?>scp/images/ost-logo.png"
                        alt="Default Logo" valign="middle"
                        style="box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
                            margin: 0.5em; height: 5em;
                            vertical-align: middle"/>
                </div>
            </td>
        </tr>
        <tr><th colspan="3">
            <em><?php echo __('Use a custom logo'); ?>&nbsp;<i class="help-tip icon-question-sign allign-tip" href="#upload_a_new_logo"></i></em>
        </th></tr>
    <?php
    $current = $ost->getConfig()->getClientLogoId();
    $currentScp = $ost->getConfig()->getStaffLogoId();
    foreach (AttachmentFile::allLogos() as $logo) { ?>
        <tr>
            <td>
                <input class="checkbox-allign" type="radio" name="selected-logo"
                    style="margin-left: 1em" value="<?php
                    echo $logo->getId(); ?>" <?php
                    if ($logo->getId() == $current)
                        echo 'checked="checked"'; ?>/>
            </td><td>
                <input class="checkbox-allign" type="radio" name="selected-logo-scp"
                    style="margin-left: 1em" value="<?php
                    echo $logo->getId(); ?>" <?php
                    if ($logo->getId() == $currentScp)
                        echo 'checked="checked"'; ?>/>
            </td><td>
                <img src="<?php echo $logo->getDownloadUrl(); ?>"
                    alt="Custom Logo" valign="middle"
                    class="person-img" style="box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
                        margin: 0.5em; height: 5em;
                        vertical-align: middle;"/>
                <?php if ($logo->getId() != $current && $logo->getId() != $currentScp) { ?>
                <label>
                <input type="checkbox" class="checkbox-allign" name="delete-logo[]" value="<?php
                    echo $logo->getId(); ?>"/> <?php echo __('Delete'); ?>
                </label>
                <?php } ?>
            </td>
        </tr>
<?php } ?>
    </tbody>
</table>
            <b><?php echo __('Upload a new logo'); ?>:<font class="error"><br/><?php echo $errors['logo']; ?></font></b>
            <input type="file" name="logo[]" size="30" value="" />
        </td>
        </tr>
    </tbody>
</table>
<p style="text-align: center; padding-top:20px;">
    <input class="btn btn-primary" type="submit" name="submit-button" value="<?php echo __('Save Changes'); ?>">
</p>
</form>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm'); ?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(
        __('Are you sure you want to DELETE %s?'),
        _N('selected logo', 'selected logos', 2)); ?></strong></font>
        <br/><br/><?php echo __('Deleted data CANNOT be recovered.'); ?>
    </p>
    <div><?php echo __('Please confirm to continue.'); ?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?php echo __('No, Cancel'); ?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!'); ?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>

<script type="text/javascript">
$(function() {
    $('#save input:submit.button').bind('click', function(e) {
        var formObj = $('#save');
        if ($('input:checkbox:checked', formObj).length) {
            e.preventDefault();
            $('.dialog#confirm-action').undelegate('.confirm');
            $('.dialog#confirm-action').delegate('input.confirm', 'click', function(e) {
                e.preventDefault();
                $('.dialog#confirm-action').hide();
                $('#overlay').hide();
                formObj.submit();
                return false;
            });
            $('#overlay').show();
            $('.dialog#confirm-action .confirm-action').hide();
            $('.dialog#confirm-action p#delete-confirm')
            .show()
            .parent('div').show().trigger('click');
            return false;
        }
        else return true;
    });
});
</script>

<style>

    .table-pattern tr td{
        padding-left: 10px;
    }

    .allign-tip{
        float: right;
        margin-top: 4px !important;
    }

    .table-script tr td span{
        width: 40% !important;
    }

    input[type=submit]{
        color: #fff !important;
    }

    input, select, textarea {
        margin-top: 10px !important;
    }

    .person-img{
        margin-left: 21px !important;
    }

    @media screen and (max-width: 450px) {

        .person-img{
            margin-left: 7px !important;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.table-pattern{
            display: table;
            border: 0 !important;
        }

        table.table-pattern tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        .table-pattern tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        .table-pattern tr td div span{
            width: 100% !important;
        }

        .table-pattern tr td i, .table-pattern tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .table-pattern tr td input[type=radio], .table-pattern tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        .table-pattern tr td input, .table-pattern tr td select{
            margin-top: 10px !important;
        }

        .table-pattern tr td input[type=text], .table-pattern tr td select, .table-pattern tr td textarea{
            margin: 0 auto !important;
            /*margin-bottom: 10px !important;*/
            width: 100% !important;
        }

        .table-pattern tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        input, select, textarea {
            width: 98% !important;
            margin-top: 10px !important;
        }

        input[type=file]{
            width: 80% !important;
        }

        input[type=submit], input[type=reset], input[type=button] {
            margin-bottom: 10px;
        }

        .navbar {
            z-index: 2 !important;
        }

        .redactor_box {
            z-index: 1 !important;
        }

        .modal-content {
            height: 620px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        table.table-logo tr img{
            width: 95% !important;
        }

        .dialog{
            width: 90% !important;
        }

        .dialog input.close, .dialog input.confirm{
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
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
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .dialog input.close{
            color: #fff;
            background-color: #d9534f;
            border-color: #d43f3a;
            opacity: 1;
        }

        .dialog input.confirm{
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
        }

        table.table-logo tr td label input{
            width: auto !important;
            float: left !important;
            margin-right: 10px !important;
        }

        .checkbox-allign{
            margin-left: 0em !important;
            margin-top: 2px !important;
        }


    }

</style>


<script>

    $(".table-script tr td textarea").addClass("form-control");

    $("table.table-script tr td").each(function (index, td){
        var input = ( $(td).find("input").length > 0 ? 1 : ($(td).find("select").length > 0 ? 1 : ($(td).find("textarea").length > 0 ? 1 : ($(td).find("img").length > 0 ? 1 : 0))));
        if((index != 0 && index != 1)){
            if(input == 0){
                $(td).attr("style", null);
                $(td).attr("width", "280");
                if($(window).width() > 450){
                    $(td).css("padding", "15px 0 0 10px");
                }
            }
        }
    });

    $(".table-script tr td").each(function (index, value) {
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

    if($(window).width() <= 450){
        $(".table-script tr")[1].remove();
    }


</script>
