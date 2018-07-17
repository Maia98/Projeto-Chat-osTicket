<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($topic && $_REQUEST['a']!='add') {
    $title=__('Update Help Topic');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$topic->getInfo();
    $info['id']=$topic->getId();
    $info['pid']=$topic->getPid();
    $qs += array('id' => $topic->getId());
} else {
    $title=__('Add New Help Topic');
    $action='create';
    $submit_text=__('Add Topic');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['form_id'] = Topic::FORM_USE_PARENT;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="helptopics.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Help Topic');?></h2>
 <table class="form_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Help Topic Information');?>
                &nbsp;<i class="help-tip icon-question-sign" href="#help_topic_information"></i></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
               <?php echo __('Topic');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#topic"></i>
                <font class="error"><?php echo $errors['topic']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="30" name="topic" value="<?php echo $info['topic']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Status');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#status"></i>
            </td>
            <td>
                <div class="col-md-12 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><?php echo __('Active'); ?>&nbsp;&nbsp;
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><?php echo __('Disabled'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Type');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#type"></i>
            </td>
            <td>
                <div class="col-md-12 col-xs-12">
                    <input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><?php echo __('Public'); ?>&nbsp;&nbsp;
                    <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><?php echo __('Private/Internal'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Parent Topic');?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#parent_topic"></i>
                <font class="error"><?php echo $errors['pid']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="topic_pid" class="form-control">
                    <option value="">&mdash; <?php echo __('Top-Level Topic'); ?> &mdash;</option><?php
                    $topics = Topic::getAllHelpTopics();
                    while (list($id,$topic) = each($topics)) {
                        if ($id == $info['topic_id'])
                            continue; ?>
                        <option value="<?php echo $id; ?>"<?php echo ($info['topic_pid']==$id)?'selected':''; ?>><?php echo $topic; ?></option>
                    <?php
                    } ?>
                </select> 
                </div>
            </td>
        </tr>

        <tr>
            <th colspan="2"><em><?php echo __('New ticket options');?></em></th></tr>
        <tr>
            <td><?php echo __('Custom Form'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#custom_form"></i></td>
                <font class="error"><?php echo $errors['form_id']; ?></font>
           <td>
               <div class="col-md-5 col-xs-12">
                  <select name="form_id" class="form-control">
                <option value="0" <?php
                    if ($info['form_id'] == '0') echo 'selected="selected"';
                    ?>>&mdash; <?php echo __('None'); ?> &mdash;</option>
                <option value="<?php echo Topic::FORM_USE_PARENT; ?>"  <?php
                    if ($info['form_id'] == Topic::FORM_USE_PARENT) echo 'selected="selected"';
                    ?>><?php echo __('Use Parent Form'); ?></option>
               <?php foreach (DynamicForm::objects()->filter(array('type'=>'G')) as $group) { ?>
                <option value="<?php echo $group->get('id'); ?>"
                       <?php if ($group->get('id') == $info['form_id'])
                            echo 'selected="selected"'; ?>>
                       <?php echo $group->get('title'); ?>
                   </option>
               <?php } ?>
               </select>  
            </div>
           </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Department'); ?>: &nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#department"></i>
                <font class="error"><?php echo $errors['dept_id']; ?></font>
            </td>
           <td>
               <div class="col-md-5 col-xs-12">
                <select name="dept_id" class="form-control">
                    <option value="0">
                    &mdash; <?php echo __('System Default'); ?> &mdash;</option>
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
                        <option value="">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                        foreach (TicketStatusList::getStatuses(array('states'=>array('open'))) as $status) {
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
                <?php echo __('Priority'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#priority"></i>
                <font class="error"><?php echo $errors['priority_id']; ?></font>
            </td>
           <td>
               <div class="col-md-5 col-xs-12">
                    <select name="priority_id" class="form-control">
                        <option value="">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
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
                <?php echo __('SLA Plan');?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#sla_plan"></i>
                <font class="error"><?php echo $errors['sla_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sla_id" class="form-control">
                        <option value="0">&mdash; <?php echo __("Department's Default");?> &mdash;</option>
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
            <td><?php echo __('Thank-you Page'); ?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#thank_you_page"></i></td>
                <font class="error"><?php echo $errors['page_id']; ?></font>
            <td>
               <div class="col-md-5 col-xs-12">
                    <select name="page_id" class="form-control">
                        <option value="">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                        if(($pages = Page::getActiveThankYouPages())) {
                            foreach($pages as $page) {
                                if(strcasecmp($page->getType(), 'thank-you')) continue;
                                echo sprintf('<option value="%d" %s>%s</option>',
                                        $page->getId(),
                                        ($info['page_id']==$page->getId())?'selected="selected"':'',
                                        $page->getName());
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Auto-assign To');?>: &nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#auto_assign_to"></i>
                <font class="error"><?php echo $errors['assign']; ?></font>
            </td>
           <td>
               <div class="col-md-5 col-xs-12">
                    <select name="assign" class="form-control">
                        <option value="0">&mdash; <?php echo __('Unassigned'); ?> &mdash;</option>
                        <?php
                        if (($users=Staff::getStaffMembers())) {
                            echo sprintf('<OPTGROUP label="%s">', sprintf(__('Agents (%d)'), count($users)));
                            foreach ($users as $id => $name) {
                                $name = new PersonsName($name);
                                $k="s$id";
                                $selected = ($info['assign']==$k || $info['staff_id']==$id)?'selected="selected"':'';
                                ?>
                                <option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>

                            <?php
                            }
                            echo '</OPTGROUP>';
                        }
                        $sql='SELECT team_id, name, isenabled FROM '.TEAM_TABLE.' ORDER BY name';
                        if(($res=db_query($sql)) && ($cteams = db_num_rows($res))) {
                            echo sprintf('<OPTGROUP label="%s">', sprintf(__('Teams (%d)'), $cteams));
                            while (list($id, $name, $isenabled) = db_fetch_row($res)){
                                $k="t$id";
                                $selected = ($info['assign']==$k || $info['team_id']==$id)?'selected="selected"':'';

                                if (!$isenabled)
                                    $name .= ' '.__('(disabled)');
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
                <?php echo __('Auto-response'); ?>:
                <i class="help-tip icon-question-sign" href="#ticket_auto_response"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="noautoresp" value="1" <?php echo $info['noautoresp']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Disable</strong> new ticket auto-response'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Ticket Number Format'); ?>:
            </td>
            <td>
                <div class="col-md-12 col-xs-12">
                    <input type="radio" name="custom-numbers" value="0" <?php echo !$info['custom-numbers']?'checked="checked"':''; ?>
                    onchange="javascript:$('#custom-numbers').hide();"> <?php echo __('System Default'); ?>
                    &nbsp;
                    <input type="radio" name="custom-numbers" value="1" <?php echo $info['custom-numbers']?'checked="checked"':''; ?>
                        onchange="javascript:$('#custom-numbers').show(200);"> <?php echo __('Custom'); ?>
                        &nbsp; <i class="help-tip icon-question-sign" href="#custom_numbers"></i>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="custom-numbers" style="<?php if (!$info['custom-numbers']) echo 'display:none'; ?>">
        <tr>
            <td>
                <?php echo __('Format'); ?>:
                <font class="error"><?php echo $errors['number_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="number_format" value="<?php echo $info['number_format']; ?>"/>    
                </div>
                <span class="faded"><?php echo __('e.g.'); ?> <span id="format-example"><?php
                    if ($info['custom-numbers']) {
                        if ($info['sequence_id'])
                            $seq = Sequence::lookup($info['sequence_id']);
                        if (!isset($seq))
                            $seq = new RandomSequence();
                        echo $seq->current($info['number_format']);
                    } ?></span></span>
            </td>
        </tr>
        <tr>
<?php $selected = 'selected="selected"'; ?>
            <td>
                <?php echo __('Sequence'); ?>:
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sequence_id" class="form-control">
                    <option value="0" <?php if ($info['sequence_id'] == 0) echo $selected;
                    ?>>&mdash; <?php echo __('Random'); ?> &mdash;</option>
                    <?php foreach (Sequence::objects() as $s) { ?>
                    <option value="<?php echo $s->id; ?>" <?php
                    if ($info['sequence_id'] == $s->id) echo $selected;
                    ?>><?php echo $s->name; ?></option>
                    <?php } ?>
                    </select>
                    <button class="action-button" onclick="javascript:
                        $.dialog('ajax.php/sequence/manage', 205);
                        return false;
                    "><i class="icon-gear"></i> <?php echo __('Manage'); ?></button>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes');?></strong>: <?php echo __("be liberal, they're internal.");?></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <div class="col-md-12 col-xs-12">
                    <textarea class="richtext no-bar" name="notes" cols="21" rows="8"><?php echo $info['notes']; ?></textarea>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center; padding-top: 20px;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="helptopics.php"'>
</p>
</form>
<script type="text/javascript">
$(function() {
    var request = null,
      update_example = function() {
      request && request.abort();
      request = $.get('ajax.php/sequence/'
        + $('[name=sequence_id] :selected').val(),
        {'format': $('[name=number_format]').val()},
        function(data) { $('#format-example').text(data); }
      );
    };
    $('[name=sequence_id]').on('change', update_example);
    $('[name=number_format]').on('keyup', update_example);
});
</script>
<style>

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    .dialog{
        margin-top: 20px !important;
    }

    tr td{
        padding: 5px;
    }

    td.required{
        width: 20%;
    }

    select, input[type=text]{
        margin-top: 10px;
    }

    input[type=radio]{
        margin: 0px !important;
    }

    .action-button{
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
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
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
        color: #333;
        background-color: #fff;
        border-color: #ccc;
        margin-top: 10px !important;
        margin-left: 0 !important;
    }

    @media screen and (max-width: 450px) {

        td.required{
            width: 100%;
        }

        table{
            display: table;
            border: 0 !important;
        }

        table.form_table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.form_table tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.form_table tr td label i, table.form_table tr th label i{
            margin-top: 3px !important;
            float: right;
        }

        table.form_table tr td i, table.form_table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        table.form_table .col-xs-12{
            padding: 0 !important;
        }

        table.form_table tr td input[type=radio], table.form_table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.form_table tr td input, table.form_table tr td select{
            margin-top: 10px !important;
        }

        table.form_table tr td input[type=text], table.form_table tr td select{
            margin: 0 auto !important;
        }

        table.form_table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        table.form_table input[type=submit], table.form_table input[type=reset], table.form_table input[type=button], table.form_table button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        table.form_table input[type=text], table.form_table select{
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