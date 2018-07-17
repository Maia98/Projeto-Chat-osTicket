<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$info=$qs= array();
if($rule && $_REQUEST['a']!='add'){
    $title=__('Update Ban Rule');
    $action='update';
    $submit_text=__('Update');
    $info=$rule->getInfo();
    $info['id']=$rule->getId();
    $qs += array('id' => $rule->getId());
}else {
    $title=__('Add New Email Address to Ban List');
    $action='add';
    $submit_text=__('Add');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $qs += array('a' => $_REQUEST['a']);
}

$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="banlist.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Manage Email Ban Rule');?>
    <i class="help-tip icon-question-sign" href="#ban_list"></i>
    </h2>
 <table class="form_table" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Valid email address required');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
                <?php echo __('Ban Status'); ?>:<span class="error">*</span>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong></label>

                    <label><input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><?php echo __('Disabled');?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Email Address');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['val']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input name="val" type="text" value="<?php echo $info['val']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal notes');?></strong>: <?php echo __('Admin notes');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                    <textarea class="richtext no-bar" name="notes" cols="21"
                        rows="8"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="banlist.php"'>
</p>
</form>
<style>

    table tr td{
        padding:10px !important;
    }

    td.required{
        width: 16%;
    }

    input[type=text]{
        width: 100%;
        margin-bottom: 0px;
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
            margin-top: 10px;
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