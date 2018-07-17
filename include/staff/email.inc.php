<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info = $qs = array();
if($email && $_REQUEST['a']!='add'){
    $title=__('Update Email');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$email->getInfo();
    $info['id']=$email->getId();
    if($info['mail_delete'])
        $info['postfetch']='delete';
    elseif($info['mail_archivefolder'])
        $info['postfetch']='archive';
    else
        $info['postfetch']=''; //nothing.
    if($info['userpass'])
        $passwdtxt=__('To change password enter new password above.');

    $qs += array('id' => $email->getId());
}else {
    $title=__('Add New Email');
    $action='create';
    $submit_text=__('Submit');
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    if (!$info['mail_fetchfreq'])
        $info['mail_fetchfreq'] = 5;
    if (!$info['mail_fetchmax'])
        $info['mail_fetchmax'] = 10;
    if (!isset($info['smtp_auth']))
        $info['smtp_auth'] = 1;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<h2><?php echo __('Email Address');?></h2>
<form action="emails.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong><?php echo __('Email Information and Settings');?></strong></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
                <?php echo __('Email Address');?> <span class="error">*</span>
                <font class="error"><?php echo $errors['email']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" size="35" name="email" value="<?php echo $info['email']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Email Name');?> <span class="error">*</span>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('New Ticket Settings'); ?></strong></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('Department').":";?>&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#new_ticket_department"></i>
                <font class="error"><?php echo $errors['dept_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="dept_id" class="form-control">
                        <option value="0" selected="selected">&mdash; <?php
                        echo __('System Default'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT dept_id, dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                        }
                        ?>
                    </select>
                </div>

        </span>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Priority').":"; ?>
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#new_ticket_priority"></i>
                <font class="error"><?php echo $errors['priority_id']; ?></font>
            </td>
            <td>
		<span>
            <div class="col-md-5 col-xs-12">
                <select name="priority_id" class="form-control">
                    <option value="0" selected="selected">&mdash; <?php
                    echo __('System Default'); ?> &mdash;</option>
                    <?php
                    $sql='SELECT priority_id, priority_desc FROM '.PRIORITY_TABLE.' pri ORDER by priority_urgency DESC';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                    while(list($id,$name)=db_fetch_row($res)){
                        $selected=($info['priority_id'] && $id==$info['priority_id'])?'selected="selected"':'';
                        echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                    }
                    }
                    ?>
                </select>
            </div>
		</span>
		&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Help Topic').":"; ?>
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#new_ticket_help_topic"></i>
                <font class="error"><?php echo $errors['topic_id']; ?></font>

            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="topic_id" class="form-control">
                        <option value="0" selected="selected">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                        <?php
                            $topics = Topic::getHelpTopics();
                            while (list($id,$topic) = each($topics)) { ?>
                                <option value="<?php echo $id; ?>"<?php echo ($info['topic_id']==$id)?'selected':''; ?>><?php echo $topic; ?></option>
                            <?php
                            } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Auto-Response').":"; ?>
                <i class="help-tip icon-question-sign" href="#auto_response"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="checkbox" name="noautoresp" value="1" <?php echo $info['noautoresp']?'checked="checked"':''; ?> >
                    <?php echo __('<strong>Disable</strong> for this Email Address'); ?>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Email Login Information'); ?></strong>
                &nbsp;<i class="help-tip icon-question-sign" href="#login_information"></i></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('Username').":"; ?>&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['userid']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text"  name="userid" value="<?php echo $info['userid']; ?>" autocomplete="off" autocorrect="off">
                </div>
            </td>
        </tr>
        <tr>
            <td>
               <?php echo __('Password').":"; ?>&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['passwd'];?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="password" class="form-control" name="passwd" value="<?php echo $info['passwd']; ?>"
                        autocomplete="off">
                    <br><em><?php echo $passwdtxt; ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Fetching Email via IMAP or POP'); ?></strong>
                &nbsp;<i class="help-tip icon-question-sign" href="#mail_account"></i>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('Status'); ?>
                &nbsp;<font class="error"><?php echo $errors['mail_active'];?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="mail_active"  value="1"   <?php echo $info['mail_active']?'checked="checked"':''; ?> />&nbsp;<?php echo __('Enable'); ?>
                    <input type="radio" name="mail_active"  value="0"   <?php echo !$info['mail_active']?'checked="checked"':''; ?> />&nbsp;<?php echo __('Disable'); ?></label>
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Hostname').":"; ?>&nbsp;
                <i class="help-tip icon-question-sign" href="#host_and_port"></i></td>
                <font class="error"><?php echo $errors['mail_host']; ?></font>
            <td>
                <div class="col-md-5 col-xs-12">
			        <input type="text" name="mail_host" size=35 value="<?php echo $info['mail_host']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Port Number').":"; ?>&nbsp;
                <i class="help-tip icon-question-sign" href="#host_and_port"></i>
                <font class="error"><?php echo $errors['mail_port']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="mail_port" size=6 value="<?php echo $info['mail_port']?$info['mail_port']:''; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Mail Box Protocol').":"; ?>
                <i class="help-tip icon-question-sign" href="#protocol"></i></td>
                <font class="error">&nbsp;<?php echo $errors['mail_protocol']; ?></font>
            <td>
            <div class="col-md-5 col-xs-12">
			<select name="mail_proto" class="form-control">
                <option value=''>&mdash; <?php echo __('Select protocol'); ?> &mdash;</option>
<?php
    foreach (MailFetcher::getSupportedProtos() as $proto=>$desc) { ?>
                <option value="<?php echo $proto; ?>" <?php
                    if ($info['mail_proto'] == $proto) echo 'selected="selected"';
                    ?>><?php echo $desc; ?></option>
<?php } ?>
			</select>
            </div>
            </td>
        </tr>

        <tr><td><?php echo __('Fetch Frequency')." (".__('minutes')."):";?>
                <i class="help-tip icon-question-sign" href="#fetch_frequency"></i>
                <font class="error"><?php echo $errors['mail_fetchfreq'];?></font></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="mail_fetchfreq" size=4 value="<?php echo $info['mail_fetchfreq']?$info['mail_fetchfreq']:''; ?>">
                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Emails Per Fetch').":"; ?>
                <i class="help-tip icon-question-sign" href="#emails_per_fetch"></i>
                &nbsp;<font class="error"><?php echo $errors['mail_fetchmax']; ?></font></td>
            <td>
		<span>
            <div class="col-md-5 col-xs-12">
                <input type="text" name="mail_fetchmax" value="<?php echo
                $info['mail_fetchmax']?$info['mail_fetchmax']:''; ?>">
            </div>
		</span>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo __('Fetched Emails').":";?>&nbsp;
                <i class="help-tip icon-question-sign" href="#fetched_emails"></i>
                <font class="error"><?php echo $errors['mail_folder']; ?></font>
            </td>
             <td>
                <label>
                    <div class="col-md-12 col-xs-12">
                        <input type="radio" class="radio-post-first" name="postfetch" value="archive" <?php echo ($info['postfetch']=='archive')? 'checked="checked"': ''; ?> >
                        <span class="info-post-first"><?php echo __('Move to folder'); ?>:</span>
                    </div>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" name="mail_archivefolder" class="input-post-first" value="<?php echo $info['mail_archivefolder']; ?>"/>
                    </div>
                </label>
                 <label>
                     <div class="col-md-12 col-xs-12">
                         <input type="radio" name="postfetch" value="delete" <?php echo ($info['postfetch']=='delete')? 'checked="checked"': ''; ?> >
                         <?php echo __('Delete emails'); ?>
                     </div>
                 </label>
                 <label>
                     <div class="col-md-12 col-xs-12">
                         <input type="radio" name="postfetch" value="" <?php echo (isset($info['postfetch']) && !$info['postfetch'])? 'checked="checked"': ''; ?> >
                         <?php echo __('Do nothing <em>(not recommended)</em>'); ?></label>
                    </div>
                 </label>
              <br /><font class="error"><?php echo $errors['postfetch']; ?></font>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Sending Email via SMTP'); ?></strong>
                &nbsp;<i class="help-tip icon-question-sign" href="#smtp_settings"></i>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['smtp']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td><?php echo __('Status').":";?>&nbsp;
                <font class="error"><?php echo $errors['smtp_active']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="smtp_active" value="1" <?php echo $info['smtp_active']?'checked':''; ?> />&nbsp;<?php echo __('Enable');?>
                    <input type="radio" name="smtp_active" value="0" <?php echo !$info['smtp_active']?'checked':''; ?> />&nbsp;<?php echo __('Disable');?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Hostname').":"; ?>&nbsp;
                <i class="help-tip icon-question-sign" href="#host_and_port"></i>
                <font class="error"><?php echo $errors['smtp_host']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="smtp_host" size=35 value="<?php echo $info['smtp_host']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Port Number').":"; ?>&nbsp;
                <i class="help-tip icon-question-sign" href="#host_and_port"></i>
                <font class="error"><?php echo $errors['smtp_port']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="smtp_port" size=6 value="<?php echo $info['smtp_port']?$info['smtp_port']:''; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Authentication Required'); ?>
                <font class="error"><?php echo $errors['smtp_auth']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                 <label><input type="radio" name="smtp_auth"  value="1"
                     <?php echo $info['smtp_auth']?'checked':''; ?> /> <?php echo __('Yes'); ?>
                 <input type="radio" name="smtp_auth"  value="0"
                     <?php echo !$info['smtp_auth']?'checked':''; ?> /> <?php echo __('No'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Header Spoofing').":"; ?>
                <i class="help-tip icon-question-sign" href="#header_spoofing"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="checkbox" name="smtp_spoofing" value="1" <?php echo $info['smtp_spoofing'] ?'checked="checked"':''; ?>>
                    <?php echo __('Allow for this Email Address'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes');?></strong>: <?php
                echo __("be liberal, they're internal.");?> &nbsp;<span class="error">&nbsp;<?php echo $errors['notes']; ?></span></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="5" style="width: 60%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="emails.php"'>
</p>
</form>
<style>

    .radio-post-first{
        float: left;
        margin-top: 10px !important;
        margin-right: 5px !important;
    }

    .info-post-first{
        float: left;
        margin-top: 8px;
        margin-right: 5px;
    }

    .input-post-first{
        width: 32% !important;
    }

    table tr td{
        padding: 10px 10px 4px 10px !important;
    }

    td.required{
        width: 21%;
    }

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    input[type=text]{
        width: 100% !important;
    }

    @media screen and (max-width: 450px) {

        .radio-post-first{
            float: none;
            margin-top: 0 !important;
            margin-right: 0 !important;
        }

        .info-post-first{
            float: none;
            margin-top: 0;
            margin-right: 0;
        }

        .input-post-first{
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
            width: 100%!important;
            float: left;
            margin-right: 10px;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 98% !important;
            margin-top: 10px;
        }

        input[name=passwd]{
            margin-top: -10px !important;
        }

        input[name=userid]{
            margin-top: -10px !important;
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
