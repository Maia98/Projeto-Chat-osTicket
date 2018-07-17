<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($team && $_REQUEST['a']!='add'){
    //Editing Team
    $title=__('Update Team');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$team->getInfo();
    $info['id']=$team->getId();
    $qs += array('id' => $team->getId());
}else {
    $title=__('Add New Team');
    $action='create';
    $submit_text=__('Create Team');
    $info['isenabled']=1;
    $info['noalerts']=0;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="teams.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Team');?>&nbsp;<i class="help-tip icon-question-sign" href="#teams"></i></h2>

 <table class="form_table">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong><?php echo __('Team Information'); ?></strong>:</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
                <?php echo __('Name');?>:&nbsp;
                <span class="error">*</span>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td  class="required">
                <?php echo __('Status');?>:&nbsp;
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#status"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="isenabled" value="1" <?php echo $info['isenabled']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong></label>
                    <label><input type="radio" name="isenabled" value="0" <?php echo !$info['isenabled']?'checked="checked"':''; ?>><?php echo __('Disabled');?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Team Lead');?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#lead"></i>
                <font class="error"><?php echo $errors['lead_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="lead_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('None');?> &mdash;</option>
                        <?php
                        if($team && ($members=$team->getMembers())){
                            foreach($members as $k=>$staff){
                                $selected=($info['lead_id'] && $staff->getId()==$info['lead_id'])?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$staff->getId(),$selected,$staff->getName());
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Assignment Alert');?>:
                <i class="help-tip icon-question-sign" href="#assignment_alert"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="noalerts" value="1" <?php echo $info['noalerts']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Desabilitar</strong> para esta equipe'); ?>
                </div>
            </td>
        </tr>
        <?php
        if($team && ($members=$team->getMembers())){ ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Team Members'); ?></strong>:
                <i class="help-tip icon-question-sign" href="#members"></i>
</em>
            </th>
        </tr>
        <?php
            foreach($members as $k=>$staff){
                echo sprintf('<tr><td colspan=2 class="teste"><div class="col-xs-9 padding"><span style="width:300px;padding-left:5px; display:block;" class="pull-left team-members">
                            <b><a href="staff.php?id=%d">%s</a></span></b></div>
                            <div class="col-xs-3 padding"><input type="checkbox" name="remove[]" value="%d"><i>'.__('Remove').'</i></div></td></tr>',
                          $staff->getId(),$staff->getName(),$staff->getId());
            }
        } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Admin Notes');?></strong>: <?php echo __('Internal notes viewable by all admins.');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar form-control" name="notes" cols="21"
                    rows="8" style="width: 100%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="teams.php"'>
</p>
</form>
<style>

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    table tr td{
        padding:10px !important;
    }

    input[type=text], select{
        margin-bottom: 0px;
    }

    td.required{
        width: 14%;
    }

    @media screen and (max-width: 450px) {

        .padding{
            padding: 0px !important;
        }

        .teste{
            margin-top: 5px !important;
        }

        .col-xs-3 input{
            margin-top: 10px !important;
        }

        .team-members{
            padding-left: 0px !important;
            width: 100% !important;
        }

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

        table tr td i.help-tip, table tr th i.help-tip{
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
            width: 98% ;
            margin-top: 10px;
        }
    }
</style>

<script>

    $("input[type=radio]").focus(function () {
        setTimeout(function () {
            $("input[type=submit]").css("color", "#fff");
        }, 100);
    });

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

</script>
