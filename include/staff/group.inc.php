<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($group && $_REQUEST['a']!='add'){
    $title=__('Update Group');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$group->getInfo();
    $info['id']=$group->getId();
    $info['depts']=$group->getDepartments();
    $qs += array('id' => $group->getId());
}else {
    $title=__('Add New Group');
    $action='create';
    $submit_text=__('Create Group');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['can_create_tickets']=isset($info['can_create_tickets'])?$info['can_create_tickets']:1;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="groups.php?<?php echo Http::build_query($qs); ?>" method="post" id="save" name="group">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Group Access and Permissions');?></h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong><?php echo __('Group Information');?></strong>: <?php echo __("Disabled group will limit agents' access. Admins are exempted.");?></em>
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
                    <input type="text" name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Status');?>:&nbsp;
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#status"></i>
                <font class="error"><?php echo $errors['status']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong>
                    &nbsp;
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Disabled');?></strong>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Group Permissions');?></strong>: <?php echo __('Applies to all group members');?>&nbsp;</em>
            </th>
        </tr>
        <tr><td><?php echo __('Can <b>Create</b> Tickets');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_create_tickets"  value="1"   <?php echo $info['can_create_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_create_tickets"  value="0"   <?php echo !$info['can_create_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>

                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to open tickets on behalf of users.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Edit</b> Tickets</td>');?>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_edit_tickets"  value="1"   <?php echo $info['can_edit_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                &nbsp;&nbsp;
                    <input type="radio" name="can_edit_tickets"  value="0"   <?php echo !$info['can_edit_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to edit tickets.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Post Reply</b>');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_post_ticket_reply"  value="1"   <?php echo $info['can_post_ticket_reply']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_post_ticket_reply"  value="0"   <?php echo !$info['can_post_ticket_reply']?'checked="checked"':''; ?> /><?php echo __('No');?>

                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to post a ticket reply.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Close</b> Tickets');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_close_tickets"  value="1" <?php echo $info['can_close_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_close_tickets"  value="0" <?php echo !$info['can_close_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to close tickets.  Agents can still post a response.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Assign</b> Tickets');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_assign_tickets"  value="1" <?php echo $info['can_assign_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_assign_tickets"  value="0" <?php echo !$info['can_assign_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to assign tickets to agents.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Transfer</b> Tickets');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_transfer_tickets"  value="1" <?php echo $info['can_transfer_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_transfer_tickets"  value="0" <?php echo !$info['can_transfer_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to transfer tickets between departments.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can <b>Delete</b> Tickets');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_delete_tickets"  value="1"   <?php echo $info['can_delete_tickets']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_delete_tickets"  value="0"   <?php echo !$info['can_delete_tickets']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __("Ability to delete tickets (Deleted tickets can't be recovered!)");?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can Ban Emails');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_ban_emails"  value="1" <?php echo $info['can_ban_emails']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_ban_emails"  value="0" <?php echo !$info['can_ban_emails']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to add/remove emails from banlist via ticket interface.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can Manage Premade');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_manage_premade"  value="1" <?php echo $info['can_manage_premade']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_manage_premade"  value="0" <?php echo !$info['can_manage_premade']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to add/update/disable/delete canned responses and attachments.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can Manage FAQ');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_manage_faq"  value="1" <?php echo $info['can_manage_faq']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_manage_faq"  value="0" <?php echo !$info['can_manage_faq']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to add/update/disable/delete knowledgebase categories and FAQs.');?></i>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Can View Agent Stats');?></td>
            <td>
                <div class="col-md-2 col-xs-12">
                    <input type="radio" name="can_view_staff_stats"  value="1" <?php echo $info['can_view_staff_stats']?'checked="checked"':''; ?> /><?php echo __('Yes');?>
                    &nbsp;&nbsp;
                    <input type="radio" name="can_view_staff_stats"  value="0" <?php echo !$info['can_view_staff_stats']?'checked="checked"':''; ?> /><?php echo __('No');?>
                </div>
                <div class="col-md-10 col-xs-12">
                    <i><?php echo __('Ability to view stats of other agents in allowed departments.');?></i>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Department Access');?></strong>:
                <i class="help-tip icon-question-sign" href="#department_access"></i>
                &nbsp;<a id="selectAll" href="#deptckb"><?php echo __('Select All');?></a>
                &nbsp;&nbsp;
                <a id="selectNone" href="#deptckb"><?php echo __('Select None');?></a></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php
                $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name';
                if(($res=db_query($sql)) && db_num_rows($res)){
                    while(list($id,$name) = db_fetch_row($res)){
                        $ck=($info['depts'] && in_array($id,$info['depts']))?'checked="checked"':'';
                        echo sprintf('<div class="col-md-12 col-xs-12" ><input type="checkbox" class="deptckb" name="depts[]" value="%d" %s>%s</div>',$id,$ck,$name);
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Admin Notes');?></strong>: <?php echo __('Internal notes viewable by all admins.');?>&nbsp;</em>
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
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="groups.php"'>
</p>
</form>
<style>

    table tr td{
        padding:10px !important;
    }

    input[type=text]{
        width: 100% !important;
        margin-bottom: 0px;
    }

    td.required{
        width: 20%;
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

        table tr td div.col-xs-12 i{
            float: left !important;
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
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

</script>