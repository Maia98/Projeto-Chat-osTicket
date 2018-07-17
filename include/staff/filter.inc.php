<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$matches=Filter::getSupportedMatches();
$match_types=Filter::getSupportedMatchTypes();

$info = $qs = array();
if($filter && $_REQUEST['a']!='add'){
    $title=__('Update Filter');
    $action='update';
    $submit_text=__('Save Changes');
    $info=array_merge($filter->getInfo(),$filter->getFlatRules());
    $info['id']=$filter->getId();
    $qs += array('id' => $filter->getId());
}else {
    $title=__('Add New Filter');
    $action='add';
    $submit_text=__('Add Filter');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:0;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="filters.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Ticket Filter');?></h2>
 <table class="form_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Filters are executed based on execution order. Filter can target specific ticket source.');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
              <?php echo __('Filter Name');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text"class="form-control" name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
              <?php echo __('Execution Order');?>: <em>(1...99)</em><span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#execution_order"></i>
                <font class="error"><?php echo $errors['execorder']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="6" name="execorder" value="<?php echo $info['execorder']; ?>">
                </div>
                <div class="col-md-5 col-xs-12">
                    <input style="margin-top: 10px !important;" type="checkbox" name="stop_onmatch" value="1" <?php echo $info['stop_onmatch']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Stop</strong> processing further on match!');?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Filter Status');?>:<span class="error">*</span>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo
                    $info['isactive']?'checked="checked"':''; ?>> <?php echo __('Active'); ?>&nbsp;
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>
                    > <?php echo __('Disabled'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Target Channel');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#target_channel"></i>
                <font class="error"><?php echo $errors['target']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="target" class="form-control">
                   <option value="">&mdash; <?php echo __('Select a Channel');?> &mdash;</option>
                   <?php
                   foreach(Filter::getTargets() as $k => $v) {
                       echo sprintf('<option value="%s" %s>%s</option>',
                               $k, (($k==$info['target'])?'selected="selected"':''), $v);
                    }
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        echo sprintf('<OPTGROUP label="%s">', __('System Emails'));
                        while(list($id,$email,$name)=db_fetch_row($res)) {
                            $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Filter Rules');?></strong>: <?php
                echo __('Rules are applied based on the criteria.');?><span class="error">*</span> </em>
                <font class="error"><?php echo $errors['rules']; ?></font>
            </th>
        </tr>
        <tr>
            <td colspan=2>
               <p>
                   <strong><?php echo __('Rules Matching Criteria');?>:&nbsp;</strong>
                   <i class="help-tip icon-question-sign" href="#rules_matching_criteria"></i>
               </p>
                <div class="division"></div>
                <input type="radio" name="match_all_rules" value="1" <?php echo $info['match_all_rules']?'checked="checked"':''; ?>><?php echo __('Match All');?>
                &nbsp;&nbsp;&nbsp;<div class="division"></div>
                <input type="radio" name="match_all_rules" value="0" <?php echo !$info['match_all_rules']?'checked="checked"':''; ?>><?php echo __('Match Any');?> (<?php echo __('case-insensitive comparison');?>)<span class="error">*&nbsp;</span>

            </td>
        </tr>
        <?php
        $n=($filter?$filter->getNumRules():0)+2; //2 extra rules of unlimited.
        for($i=1; $i<=$n; $i++){ ?>
        <tr id="r<?php echo $i; ?>">
            <td colspan="2">
                <div class="col-md-5 col-xs-12">
                    <select class="form-control margin-top-10"  name="rule_w<?php echo $i; ?>">
                        <option value="">&mdash; <?php echo __('Select One');?> &mdash;</option>
                        <?php
                        foreach ($matches as $group=>$ms) { ?>
                            <optgroup label="<?php echo __($group); ?>"><?php
                            foreach ($ms as $k=>$v) {
                                $sel=($info["rule_w$i"]==$k)?'selected="selected"':'';
                                echo sprintf('<option value="%s" %s>%s</option>',
                                    $k,$sel,__($v));
                            } ?>
                        </optgroup>
                        <?php } ?>
                    </select>
                    <div class="division"></div>
                    <select class="form-control margin-top-10" name="rule_h<?php echo $i; ?>">
                        <option value="0">&mdash; <?php echo __('Select One');?> &mdash;</option>
                        <?php
                        foreach($match_types as $k=>$v){
                            $sel=($info["rule_h$i"]==$k)?'selected="selected"':'';
                            echo sprintf('<option value="%s" %s>%s</option>',
                                $k,$sel,$v);
                        }
                        ?>
                    </select>
                    <div class="division"></div>
                    <input class="ltr form-control margin-top-10" type="text" name="rule_v<?php echo $i; ?>" value="<?php echo $info["rule_v$i"]; ?>">
                    &nbsp;<font class="error">&nbsp;<?php echo $errors["rule_$i"]; ?></font>
                    <div class="pull-right"><a href="#" class="clearrule btn btn-danger" style="text-transform: capitalize; margin-top: 20px !important;"><?php echo __('clear');?></a></div>

                </div>
                <div class="margin"></div>
            </td>
        </tr>
        <?php
            if($i>=25) //Hardcoded limit of 25 rules...also see class.filter.php
               break;
        } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Filter Actions');?></strong>: <?php
                echo __('Can be overwridden by other filters depending on processing order.');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('Reject Ticket');?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#reject_ticket"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="reject_ticket" value="1" <?php echo $info['reject_ticket']?'checked="checked"':''; ?> >
                    <span class="error"><?php echo __('Reject Ticket');?></span>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Reply-To Email');?>:&nbsp;
                <i class="help-tip icon-question-sign" href="#reply_to_email"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="use_replyto_email" value="1" <?php echo $info['use_replyto_email']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Use</strong> Reply-To Email');?> <em>(<?php echo __('if available');?>)</em>
                    </em>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Ticket auto-response');?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#ticket_auto_response"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="disable_autoresponder" value="1" <?php echo $info['disable_autoresponder']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Disable</strong> auto-response.');?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Canned Response');?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#canned_response"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="canned_response_id" class="form-control">
                    <option value="">&mdash; <?php echo __('None');?> &mdash;</option>
                    <?php
                    $sql='SELECT canned_id, title, isenabled FROM '.CANNED_TABLE .' ORDER by title';
                    if ($res=db_query($sql)) {
                        while (list($id, $title, $isenabled)=db_fetch_row($res)) {
                            $selected=($info['canned_response_id'] &&
                                    $id==$info['canned_response_id'])
                                ? 'selected="selected"' : '';

                            if (!$isenabled)
                                $title .= ' ' . __('(disabled)');

                            echo sprintf('<option value="%d" %s>%s</option>',
                                $id, $selected, $title);
                        }
                    }
                    ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Department');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#department"></i>
                <font class="error"><?php echo $errors['dept_id']; ?></font>
            </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="dept_id" class="form-control">
                            <option value="">&mdash; <?php echo __('Default');?> &mdash;</option>
                            <?php
                            $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                            if(($res=db_query($sql)) && db_num_rows($res)){
                                while(list($id,$name)=db_fetch_row($res)){
                                    $selected=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';
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
                <?php echo __('Status'); ?>:
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#status"></i>
                <font class="error"><?php echo $errors['status_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="status_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Default'); ?> &mdash;</option>
                        <?php
                        foreach (TicketStatusList::getStatuses() as $status) {
                            $name = $status->getName();
                            if (!($isenabled = $status->isEnabled()))
                                $name.=' '.__('(disabled)');

                            echo sprintf('<option value="%d" %s %s>%s</option>',
                                    $status->getId(),
                                    ($info['status_id'] == $status->getId())
                                     ? 'selected="selected"' : '',
                                     $isenabled ? '' : 'disabled="disabled"',
                                     $name
                                    );
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Priority');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#priority"></i>
                <font class="error"><?php echo $errors['priority_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="priority_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Default');?> &mdash;</option>
                        <?php
                        $sql='SELECT priority_id,priority_desc FROM '.PRIORITY_TABLE.' pri ORDER by priority_urgency DESC';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$name)=db_fetch_row($res)){
                                $selected=($info['priority_id'] && $id==$info['priority_id'])?'selected="selected"':'';
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
                <?php echo __('SLA Plan');?>:
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#sla_plan"></i>
                <font class="error"><?php echo $errors['sla_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sla_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('System Default');?> &mdash;</option>
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
                <?php echo __('Auto-assign To');?>:
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#auto_assign"></i>
                <font class="error"><?php echo $errors['assign']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="assign" class="form-control">
                        <option value="0">&mdash; <?php echo __('Unassigned');?> &mdash;</option>
                        <?php
                        if (($users=Staff::getStaffMembers())) {
                            echo '<OPTGROUP label="'.__('Agents').'">';
                            foreach($users as $id => $name) {
                                $name = new PersonsName($name);
                                $k="s$id";
                                $selected = ($info['assign']==$k || $info['staff_id']==$id)?'selected="selected"':'';
                                ?>
                                <option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                            <?php
                            }
                            echo '</OPTGROUP>';
                        }
                        $sql='SELECT team_id, isenabled, name FROM '.TEAM_TABLE .' ORDER BY name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            echo '<OPTGROUP label="'.__('Teams').'">';
                            while (list($id, $isenabled, $name) = db_fetch_row($res)){
                                $k="t$id";
                                $selected = ($info['assign']==$k || $info['team_id']==$id)?'selected="selected"':'';
                                if (!$isenabled)
                                    $name .= ' (disabled)';
                                ?>
                                <option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                            <?php
                            }
                            echo '</OPTGROUP>';
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Help Topic').":";  ?>
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#help_topic"></i>
                <font class="error"><?php echo $errors['topic_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="topic_id" class="form-control">
                        <option value="0" selected="selected">&mdash; <?php
                            echo __('Unchanged'); ?> &mdash;</option>
                        <?php
                        foreach (Topic::getHelpTopics(false, Topic::DISPLAY_DISABLED) as $id=>$name) {
                            $selected=($info['topic_id'] && $id==$info['topic_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes');?></strong>: <?php
                    echo __("be liberal, they're internal");?></em>
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
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="filters.php"'>
</p>
</form>
<style>

    input[type=submit], input[type=reset], input[type=button]{
        color: #fff !important;
    }

    table tr td{
        padding:10px !important;
    }

    td.required{
        width: 21%;
    }

    .pull-right{
        margin-top: -50px !important;
        padding: 0 !important;
    }

    .btn-danger{
        margin-top: 10px !important;
        margin-right: 0 !important;
    }

    input[type=text], select{
        margin-bottom: 0px !important;
    }

    .margin-top-10{
        margin-top: 10px !important;
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
            width: 100% !important;
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

        .pull-right{
            margin-top: -40px !important;
            padding: 0 !important;
        }

        .btn-danger{
            margin-top: 10px !important;
            margin-right: 0 !important;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 100% !important;
            max-width: 100% !important;
        }

        .division{
            clear: both;
            margin-bottom: 10px;
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