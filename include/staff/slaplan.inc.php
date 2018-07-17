<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($sla && $_REQUEST['a']!='add'){
    $title=__('Update SLA Plan' /* SLA is abbreviation for Service Level Agreement */);
    $action='update';
    $submit_text=__('Save Changes');
    $info=$sla->getInfo();
    $info['id']=$sla->getId();
    $qs += array('id' => $sla->getId());
}else {
    $title=__('Add New SLA Plan' /* SLA is abbreviation for Service Level Agreement */);
    $action='add';
    $submit_text=__('Add Plan');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['enable_priority_escalation']=isset($info['enable_priority_escalation'])?$info['enable_priority_escalation']:1;
    $info['disable_overdue_alerts']=isset($info['disable_overdue_alerts'])?$info['disable_overdue_alerts']:0;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="slas.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Service Level Agreement');?></h2>
 <table class="form_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Tickets are marked overdue on grace period violation.');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
              <?php echo __('Name');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#name"></i>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control"  name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
              <?php echo __('Grace Period');?>: <em>(<?php echo __('in hours');?>)</em><span class="error">*</span>
                    <i class="help-tip icon-question-sign" href="#grace_period"></i>
                    <font class="error"><?php echo $errors['grace_period']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="10" name="grace_period" value="<?php echo $info['grace_period']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Status');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['isactive']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong>
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><?php echo __('Disabled');?>
                </div>                
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Transient'); ?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#transient"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="transient" value="1" <?php echo $info['transient']?'checked="checked"':''; ?> >
                    <?php echo __('SLA can be overridden on ticket transfer or help topic change'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Ticket Overdue Alerts');?>:
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="disable_overdue_alerts" value="1" <?php echo $info['disable_overdue_alerts']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Disable</strong> overdue alerts notices.'); ?>
                    <em><?php echo __('(Override global setting)'); ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Admin Notes');?></strong>: <?php echo __('Internal notes.');?>
                &nbsp;&nbsp;<i class="help-tip icon-question-sign" href="#admin_notes"></i></em>
                </em>
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
<p style="text-align:center; padding-top: 20px;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="slas.php"'>
</p>
</form>
<style>

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    table tr td{
        padding:10px !important;
    }

    td.required{
        width: 22%;
    }

    input[type=text], select{
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
            width:100% !important;
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


        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 100% !important;
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