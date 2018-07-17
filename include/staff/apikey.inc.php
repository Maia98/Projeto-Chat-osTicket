<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$info=$qs = array();
if($api && $_REQUEST['a']!='add'){
    $title=__('Update API Key');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$api->getHashtable();
    $qs += array('id' => $api->getId());
}else {
    $title=__('Add New API Key');
    $action='add';
    $submit_text=__('Add Key');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="apikeys.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('API Key');?>
    <i class="help-tip icon-question-sign" href="#api_key"></i>
    </h2>
 <table class="form_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('API Key is auto-generated. Delete and re-add to change the key.');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
                <?php echo __('Status');?>:<span class="error">*&nbsp;</span>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong>&nbsp;&nbsp;
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><?php echo __('Disabled');?>    
                </div>
            </td>
        </tr>
        <?php if($api){ ?>
        <tr>
            <td>
                <?php echo __('IP Address');?>:
                <i class="help-tip icon-question-sign" href="#ip_addr"></i>
            </td>
            <td>
                <span>
                    <?php echo $api->getIPAddr(); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('API Key');?>:
            </td>
            <td>
                <?php echo $api->getKey(); ?> &nbsp;
            </td>
        </tr>
        <?php }else{ ?>
        <tr>
            <td class="required">
               <?php echo __('IP Address');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#ip_addr"></i>
                <font class="error"><?php echo $errors['ipaddr']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="30" name="ipaddr" value="<?php echo $info['ipaddr']; ?>">
                </div>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Services');?>:</strong> <?php echo __('Check applicable API services enabled for the key.');?></em>
            </th>
        </tr>
        <tr>
            <td colspan=2 style="margin-top: 10px;">
                <label>
                    <input type="checkbox" name="can_create_tickets" value="1" <?php echo $info['can_create_tickets']?'checked="checked"':''; ?> >
                    <?php echo __('Can Create Tickets <em>(XML/JSON/EMAIL)</em>');?>
                </label>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <label>
                    <input type="checkbox" name="can_exec_cron" value="1" <?php echo $info['can_exec_cron']?'checked="checked"':''; ?> >
                    <?php echo __('Can Execute Cron');?>
                </label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Admin Notes');?></strong>: <?php echo __('Internal notes.');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align: center; padding-top: 20px;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="apikeys.php"'>
</p>
</form>
<style>

    table tr td{
        padding:10px !important;
    }

    input[type=text]{
        margin-bottom: 0px !important;
    }

    @media screen and (max-width: 450px) {

        table{
            display: table;
            border: 0 !important;
        }

        table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table tr td i, table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table tr td input[type=radio], table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table tr td input, table tr td select{
            margin-top: 10px !important;
        }

        table tr td input[type=text], table tr td select{
            margin: 0 auto !important;
        }

        table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .navbar {
            z-index: 2 !important;
        }

        .redactor_box {
            z-index: 1 !important;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 100%;
        }


    }
</style>

<script>

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });

</script>