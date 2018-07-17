<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($dept && $_REQUEST['a']!='add') {
    //Editing Department.
    $title=__('Update Department');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$dept->getInfo();
    $info['id']=$dept->getId();
    $info['groups'] = $dept->getAllowedGroups();
    $qs += array('id' => $dept->getId());
} else {
    $title=__('Add New Department');
    $action='create';
    $submit_text=__('Create Dept');
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    if (!isset($info['group_membership']))
        $info['group_membership'] = 1;

    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="departments.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Department');?></h2>
 <table class="table form_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Department Information');?></em>
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
                    <input type="text" name="name" value="<?php echo $info['name']; ?>" style="width: 100%">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Type');?>:&nbsp;
                <i class="help-tip icon-question-sign" href="#type"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><strong><?php echo __('Public');?></strong>
                    <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><strong><?php echo __('Private');?></strong> <?php echo mb_convert_case(__('(internal)'), MB_CASE_TITLE);?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('SLA'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#sla"></i>
                <font class="error"><?php echo $errors['sla_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sla_id">
                        <option value="0">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                        if($slas=SLA::getSLAs()) {
                            foreach($slas as $id =>$name) {
                                echo sprintf('<option value="%d" %s>%s</option>',
                                        $id, ($info['sla_id']==$id)?'selected="selected"':'',$name);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Manager'); ?>: &nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#manager"></i>
                <font class="error"><?php echo $errors['manager_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="manager_id">
                        <option value="0">&mdash; <?php echo __('None'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT staff_id,CONCAT_WS(", ",lastname, firstname) as name '
                            .' FROM '.STAFF_TABLE.' staff '
                            .' ORDER by name';
                        if(($res=db_query($sql)) && db_num_rows($res)) {
                            while(list($id,$name)=db_fetch_row($res)){
                                $selected=($info['manager_id'] && $id==$info['manager_id'])?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                Sub-Gerente: &nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#manager"></i>
                <font class="error"><?php echo $errors['sub_manager_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sub_manager_id">
                        <option value="0">&mdash; <?php echo __('None'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT staff_id,CONCAT_WS(", ",lastname, firstname) as name '
                            .' FROM '.STAFF_TABLE.' staff '
                            .' ORDER by name';
                        if(($res=db_query($sql)) && db_num_rows($res)) {
                            while(list($id,$name)=db_fetch_row($res)){
                                $selected=($info['sub_manager_id'] && $id==$info['sub_manager_id'])?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Ticket Assignment'); ?>:
                <i class="help-tip icon-question-sign" href="#sandboxing"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="assign_members_only" <?php echo
                    $info['assign_members_only']?'checked="checked"':''; ?>>
                    <?php echo __('Restrict ticket assignment to department members'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Outgoing Email Settings'); ?></strong>:</em>
            </th>
        </tr>
        <tr>
            <td width="180">
                <?php echo __('Outgoing Email'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#email"></i>
                <font class="error"><?php echo $errors['email_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="email_id">
                        <option value="0">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$email,$name)=db_fetch_row($res)){
                                $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                                if($name)
                                    $email=Format::htmlchars("$name <$email>");
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Template Set'); ?>:
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#template"></i>
                <font class="error"><?php echo $errors['tpl_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="tpl_id">
                        <option value="0">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_GRP_TABLE.' tpl WHERE isactive=1 ORDER by name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$name)=db_fetch_row($res)){
                                $selected=($info['tpl_id'] && $id==$info['tpl_id'])?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Autoresponder Settings'); ?></strong>:
                <i class="help-tip icon-question-sign" href="#auto_response_settings"></i></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('New Ticket');?>:
                <i class="help-tip icon-question-sign" href="#new_ticket"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                <input type="checkbox" name="ticket_auto_response" value="0" <?php echo !$info['ticket_auto_response']?'checked="checked"':''; ?> >
                <?php echo __('<strong>Disable</strong> for this Department'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('New Message');?>:
                <i class="help-tip icon-question-sign" href="#new_message"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                <input type="checkbox" name="message_auto_response" value="0" <?php echo !$info['message_auto_response']?'checked="checked"':''; ?> >
                <?php echo __('<strong>Disable</strong> for this Department'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Auto-Response Email'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#auto_response_email"></i>
                <font class="error"><?php echo $errors['autoresp_email_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                <select name="autoresp_email_id">
                    <option value="0" selected="selected">&mdash; <?php echo __('Department Email'); ?> &mdash;</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$email,$name)=db_fetch_row($res)){
                            $selected = (isset($info['autoresp_email_id'])
                                    && $id == $info['autoresp_email_id'])
                                ? 'selected="selected"' : '';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                    }
                    ?>
                </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Alerts and Notices'); ?>:</strong>
                <i class="help-tip icon-question-sign" href="#group_membership"></i></em>
            </th>
        </tr>
        <tr>
            <td width="180">
                <?php echo __('Recipients'); ?>:
                <i class="help-tip icon-question-sign" href="#group_membership"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                <select name="group_membership">
                <?php foreach (array(
                    Dept::ALERTS_DISABLED =>        __("No one (disable Alerts and Notices)"),
                    Dept::ALERTS_DEPT_ONLY =>       __("Department members only"),
                    Dept::ALERTS_DEPT_AND_GROUPS => __("Department and Group members"),
                ) as $mode=>$desc) { ?>
                    <option value="<?php echo $mode; ?>" <?php
                        if ($info['group_membership'] == $mode) echo 'selected="selected"';
                    ?>><?php echo $desc; ?></option><?php
                } ?>
                </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Group Access'); ?></strong>:
                <?php echo __('Check all groups allowed to access this department.'); ?>
                <i class="help-tip icon-question-sign" href="#department_access"></i></em>
            </th>
        </tr>
        <?php
         $sql='SELECT group_id, group_name, count(staff.staff_id) as members '
             .' FROM '.GROUP_TABLE.' grp '
             .' LEFT JOIN '.STAFF_TABLE. ' staff USING(group_id) '
             .' GROUP by grp.group_id '
             .' ORDER BY group_name';
         if(($res=db_query($sql)) && db_num_rows($res)){
            while(list($id, $name, $members) = db_fetch_row($res)) {
                if($members>0)
                    $members=sprintf('<a href="staff.php?a=filter&gid=%d">%d</a>', $id, $members);

                $ck=($info['groups'] && in_array($id,$info['groups']))?'checked="checked"':'';
                echo sprintf('<tr><td colspan=2><div class="col-md-5 col-xs-12"><input type="checkbox" name="groups[]" value="%d" %s>&nbsp;%s</label> (%s)</div></td></tr>',
                        $id, $ck, $name, $members);
            }
         }
        ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Department Signature'); ?></strong>:
                <span class="error">&nbsp;<?php echo $errors['signature']; ?></span>
                <i class="help-tip icon-question-sign" href="#department_signature"></i></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?php echo $info['signature']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="departments.php"'>
</p>
</form>
<style>
    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }


    table tr td{
        padding: 10px 10px 4px 10px !important;
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

    $("form select").attr("class", "form-control");
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

