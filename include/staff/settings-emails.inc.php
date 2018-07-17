<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2><?php echo __('Email Settings and Options');?></h2>
<form action="settings.php?t=emails" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="emails" >
<table class="form_table settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Email Settings');?></h4>
                <em><?php echo __('Note that some of the global settings can be overwridden at department/email level.');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required"><?php echo __('Default Template Set'); ?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_email_templates"></i>
                <font class="error"><?php echo $errors['default_template_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_template_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Select Default Email Template Set'); ?> &mdash;</option>
                        <?php
                        $sql='SELECT tpl_id, name FROM '.EMAIL_TEMPLATE_GRP_TABLE
                            .' WHERE isactive =1 ORDER BY name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while (list($id, $name) = db_fetch_row($res)){
                                $selected = ($config['default_template_id']==$id)?'selected="selected"':''; ?>
                                <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                            <?php
                            }
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Default System Email');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_system_email"></i>
                <font class="error"><?php echo $errors['default_email_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_email_id" class="form-control">
                        <option value=0 disabled><?php echo __('Select One');?></option>
                        <?php
                        $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE;
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while (list($id,$email,$name) = db_fetch_row($res)){
                                $email=$name?"$name &lt;$email&gt;":$email;
                                ?>
                                <option value="<?php echo $id; ?>"<?php echo ($config['default_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                            <?php
                            }
                        } ?>
                     </select>
                 </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Default Alert Email');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_alert_email"></i>
                <font class="error"><?php echo $errors['alert_email_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="alert_email_id" class="form-control">
                        <option value="0" selected="selected"><?php echo __('Use Default System Email (above)');?></option>
                        <?php
                        $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' WHERE email_id != '.db_input($config['default_email_id']);
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while (list($id,$email,$name) = db_fetch_row($res)){
                                $email=$name?"$name &lt;$email&gt;":$email;
                                ?>
                                <option value="<?php echo $id; ?>"<?php echo ($config['alert_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                            <?php
                            }
                        } ?>
                     </select>
                 </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __("Admin's Email Address");?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#admins_email_address"></i>
                <font class="error"><?php echo $errors['admin_email']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" size=40 class="form-control" name="admin_email" value="<?php echo $config['admin_email']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Verificar endereços de e-mail");?>:
                <i class="help-tip icon-question-sign" href="#verify_email_addrs"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="verify_email_addrs" <?php
                    if ($config['verify_email_addrs']) echo 'checked="checked"'; ?>>
                    <?php echo __('Verificar o domínio do endereço de e-mail'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan=2><em><strong><?php echo __('Incoming Emails'); ?>:</strong>&nbsp;</em></th>
        <tr>
            <td><?php echo __('Email Fetching'); ?>:</td>
            <td>
                <div class="col-md-12 col-xs-12">
                    <input type="checkbox" name="enable_mail_polling" value=1 <?php echo $config['enable_mail_polling']? 'checked="checked"': ''; ?>>
                    <?php echo __('Enable'); ?>
                    <i class="help-tip icon-question-sign" href="#email_fetching"></i>
                </div>
                <div class="col-md-12 col-xs-12">
                    <input type="checkbox" name="enable_auto_cron" <?php echo $config['enable_auto_cron']?'checked="checked"':''; ?>>
                    <?php echo __('Fetch on auto-cron'); ?>&nbsp;
                    <i class="help-tip icon-question-sign" href="#enable_autocron_fetch"></i>
                </div>
                
            </td>
        </tr>
        <tr>
            <td><?php echo __('Strip Quoted Reply');?>:
                <i class="help-tip icon-question-sign" href="#strip_quoted_reply"></i>
                <font class="error"><?php echo $errors['strip_quoted_reply']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="strip_quoted_reply" <?php echo $config['strip_quoted_reply'] ? 'checked="checked"':''; ?>>
                    <?php echo __('Enable'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Reply Separator Tag');?>:
                <i class="help-tip icon-question-sign" href="#reply_separator_tag"></i>
                <font class="error"><?php echo $errors['reply_separator']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="reply_separator" value="<?php echo $config['reply_separator']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Emailed Tickets Priority'); ?>:
                <i class="help-tip icon-question-sign" href="#emailed_tickets_priority"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="use_email_priority" value="1" <?php echo $config['use_email_priority'] ?'checked="checked"':''; ?>>
                    &nbsp;<?php echo __('Enable'); ?>&nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Accept All Emails'); ?>:
                <i class="help-tip icon-question-sign" href="#accept_all_emails"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="accept_unregistered_email" <?php
                    echo $config['accept_unregistered_email'] ? 'checked="checked"' : ''; ?>/>
                    <?php echo __('Accept email from unknown Users'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Accept Email Collaborators'); ?>:
                <i class="help-tip icon-question-sign" href="#accept_email_collaborators"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="add_email_collabs" <?php
                    echo $config['add_email_collabs'] ? 'checked="checked"' : ''; ?>/>
                    <?php echo __('Automatically add collaborators from email fields'); ?>&nbsp;
                </div>
        </tr>
        <tr><th colspan=2><em><strong><?php echo __('Outgoing Emails');?></strong>: <?php echo __('Default email only applies to outgoing emails without SMTP setting.');?></em></th></tr>
        <tr>
            <td><?php echo __('Default MTA'); ?>:
                <i class="help-tip icon-question-sign" href="#default_mta"></i>
                <font class="error"><?php echo $errors['default_smtp_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_smtp_id" class="form-control">
                    <option value=0 selected="selected"><?php echo __('None: Use PHP mail function');?></option>
                    <?php
                    $sql=' SELECT email_id, email, name, smtp_host '
                        .' FROM '.EMAIL_TABLE.' WHERE smtp_active = 1';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while (list($id, $email, $name, $host) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_smtp_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                </div>
           </td>
       </tr>
        <tr>
            <td><?php echo __('Attachments');?>:<i class="help-tip icon-question-sign" href="#ticket_response_files"></i> </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="email_attachments" <?php echo $config['email_attachments']?'checked="checked"':''; ?>>
                    <?php echo __('Email attachments to the user'); ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align: center; padding-top: 20px;">
    <input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes');?>">
</p>
</form>
<style>

    table tr td{
        padding: 5px !important;
    }

    select, input{
        margin-top: 10px;
    }

    td.required{
        width: 25%;
    }

    @media screen and (max-width: 450px) {

        td.required{
            width: 100%;
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

        input[type=submit], input[type=reset], input[type=button] {
            width: 100% !important;
            margin-bottom: 10px;
        }

    }
</style>

<script>

    $("form select").change(function () {
        $("form input[type=submit]").css("color", "#fff");
    });


    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        var help  = $(value).find("i.help-tip");
        if(input.length > 0){
            input.css("display", "block");
        }

        if(help.length > 1){
            help.css("float", "none");
        }
    });

</script>