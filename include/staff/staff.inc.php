<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$info = $qs = array();
if($staff && $_REQUEST['a']!='add'){
    //Editing Department.
    $title=__('Update Agent');
    $action='update';
    $submit_text=__('Save Changes');
    $passwd_text=__('To reset the password enter a new one below');
    $info=$staff->getInfo();
    $info['id']=$staff->getId();
    $info['teams'] = $staff->getTeams();
    $info['signature'] = Format::viewableImages($info['signature']);
    $qs += array('id' => $staff->getId());
}else {
    $title=__('Add New Agent');
    $action='create';
    $submit_text=__('Add Agent');
    $passwd_text=__('Temporary password required only for "Local" authentication');
    //Some defaults for new staff.
    $info['change_passwd']=1;
    $info['welcome_email']=1;
    $info['isactive']=1;
    $info['isvisible']=1;
    $info['isadmin']=0;
    $info['timezone_id'] = $cfg->getDefaultTimezoneId();
    $info['daylight_saving'] = $cfg->observeDaylightSaving();
    $qs += array('a' => 'add');
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="staff.php?<?php echo Http::build_query($qs); ?>" method="post" id="save" autocomplete="off">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Agent Account');?></h2>
 <table class="form_table">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong><?php echo __('User Information');?></strong></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
                <?php echo __('Username');?>:&nbsp;
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#username"></i>
                <font class="error"><?php echo $errors['username']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="staff-username typeahead" name="username" value="<?php echo $info['username']; ?>">
                </div>
            </td>
        </tr>

        <tr>
            <td class="required">
                <?php echo __('First Name');?>:&nbsp;
                <span class="error">*</span>&nbsp;
                <font class="error"><?php echo $errors['firstname']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="firstname" class="auto first" value="<?php echo $info['firstname']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Last Name');?>:&nbsp;
                <span class="error">*</span>&nbsp;
                <font class="error"><?php echo $errors['lastname']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="lastname" class="auto last" value="<?php echo $info['lastname']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Email Address');?>:
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#email_address"></i>
                <font class="error"><?php echo $errors['email']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="email" class="auto email" value="<?php echo $info['email']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Phone Number');?>:&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['phone']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="phone" class="auto phone" value="<?php echo $info['phone']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Ext');?>:&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['phone_ext']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="phone_ext" class="auto" value="<?php echo $info['phone_ext']; ?>">
                </div>
            </td>
        </tr>
<?php if (!$staff) { ?>
        <tr>
            <td>
                <?php echo __('Welcome Email'); ?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#welcome_email"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="welcome_email" id="welcome-email" <?php
                    if ($info['welcome_email']) echo 'checked="checked"';
                    ?> onchange="javascript:
                    var sbk = $('#backend-selection');
                    if ($(this).is(':checked'))
                        $('#password-fields').hide();
                    else if (sbk.val() == '' || sbk.val() == 'local')
                        $('#password-fields').show();
                    " />
                    <?php echo __('Send sign in information'); ?>
                </div>
            </td>
        </tr>
<?php } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Authentication'); ?></strong>: <?php echo $passwd_text; ?> &nbsp;
                    <span class="error">&nbsp;<?php echo $errors['temppasswd']; ?></span>&nbsp;
                    <i class="help-tip icon-question-sign" href="#account_password"></i></em>
            </th>
        </tr>
        <tr>
            <td><?php echo __('Authentication Backend'); ?></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="backend" id="backend-selection" class="form-control" onchange="javascript:
                if (this.value != '' && this.value != 'local')
                    $('#password-fields').hide();
                else if (!$('#welcome-email').is(':checked'))
                    $('#password-fields').show();
                ">
                        <option value="">&mdash; <?php echo __('Use any available backend'); ?> &mdash;</option>
                        <?php foreach (StaffAuthenticationBackend::allRegistered() as $ab) {
                            if (!$ab->supportsInteractiveAuthentication()) continue; ?>
                            <option value="<?php echo $ab::$id; ?>" <?php
                            if ($info['backend'] == $ab::$id)
                                echo 'selected="selected"'; ?>><?php
                                echo $ab->getName(); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="password-fields" style="<?php
        if ($info['welcome_email'] || ($info['backend'] && $info['backend'] != 'local'))
            echo 'display:none;'; ?>">
        <tr>
            <td>
                <?php echo __('Password');?>:&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['passwd1']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="password" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Confirm Password');?>: &nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['passwd2']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="password" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo __('Forced Password Change');?>: &nbsp;
                <i class="help-tip icon-question-sign" href="#forced_password_change"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="change_passwd" value="0" <?php echo $info['change_passwd']?'checked="checked"':''; ?>>
                    <?php echo __('<strong>Force</strong> password change on next login.');?>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __("Agent's Signature");?></strong>:&nbsp;<span class="error">&nbsp;<?php echo $errors['signature']; ?></span>&nbsp;<i class="help-tip icon-question-sign" href="#agents_signature"></i></em>
                <?php echo __('Optional signature used on outgoing emails.');?>
                </em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext" class="form-control" name="signature" cols="21"
                          rows="5" style="width: 60%;"><?php echo $info['signature']; ?></textarea>
                    <br><em><?php echo __('Signature is made available as a choice, on ticket reply.');?></em>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Account Status & Settings');?></strong>: <?php echo __('Department and group assigned control access permissions.');?></em>
            </th>
        </tr>
        <tr>
            <td width="180" class="required">
                <?php echo __('Account Type');?>:&nbsp;
                <span class="error"></span>
                <font class="error"><?php echo $errors['isadmin']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="isadmin" value="1" <?php echo $info['isadmin']?'checked="checked"':''; ?>><font color="red"><strong><?php echo __('Admin');?></strong></font>
                    </label>

                    <label><input type="radio" name="isadmin" value="0" <?php echo !$info['isadmin']?'checked="checked"':''; ?>><strong><?php echo __('Agent');?></strong></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Account Status');?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#account_status"></i>
                <font class="error"><?php echo $errors['isactive']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active');?></strong></label>
                    <label><input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Locked');?></strong></label>
                </div>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                <?php echo __('Assigned Group');?>:&nbsp;
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#assigned_group"></i>
                <font class="error"><?php echo $errors['group_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="group_id" id="group_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('Select Group');?> &mdash;</option>
                        <?php
                        $sql='SELECT group_id, group_name, group_enabled as isactive FROM '.GROUP_TABLE.' ORDER BY group_name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$name,$isactive)=db_fetch_row($res)){
                                $sel=($info['group_id']==$id)?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s %s</option>',$id,$sel,$name,($isactive?'':__('(disabled)')));
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Primary Department');?>:&nbsp;
                <span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#primary_department"></i>
                <font class="error"><?php echo $errors['dept_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="dept_id" id="dept_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('Select Department');?> &mdash;</option>
                        <?php
                        $sql='SELECT dept_id, dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$name)=db_fetch_row($res)){
                                $sel=($info['dept_id']==$id)?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$sel,$name);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __("Agent's Time Zone");?>:
                <span class="error">*</span>
                <font class="error"><?php echo $errors['timezone_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="timezone_id" id="timezone_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('Select Time Zone');?> &mdash;</option>
                        <?php
                        $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id,$offset, $tz)=db_fetch_row($res)){
                                $sel=($info['timezone_id']==$id)?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>GMT %s - %s</option>',$id,$sel,$offset,$tz);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td>
               <?php echo __('Daylight Saving');?>:&nbsp;
                <i class="help-tip icon-question-sign" href="#daylight_saving"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="daylight_saving" value="1" <?php echo $info['daylight_saving']?'checked="checked"':''; ?>>
                    <?php echo __('Observe daylight saving');?>
                    <em>(<?php echo __('Current Time');?>: <strong><?php
                            echo Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$info['tz_offset'],$info['daylight_saving']);
                            ?></strong>)
                    </em>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Limited Access');?>:&nbsp;
                <i class="help-tip icon-question-sign" href="#limited_access"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="assigned_only" value="1" <?php echo $info['assigned_only']?'checked="checked"':''; ?>><?php echo __('Limit ticket access to ONLY assigned tickets.');?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Directory Listing');?>:
                <i class="help-tip icon-question-sign" href="#directory_listing"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="isvisible" value="1" <?php echo $info['isvisible']?'checked="checked"':''; ?>>&nbsp;<?php
                    echo __('Make visible in the Agent Directory'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('Vacation Mode');?>:&nbsp;<i class="help-tip icon-question-sign" href="#vacation_mode"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="onvacation" value="1" <?php echo $info['onvacation']?'checked="checked"':''; ?>>
                    <?php echo __('Change Status to Vacation Mode'); ?>
                </div>

            </td>
        </tr>
        <?php
         //List team assignments.
         $sql='SELECT team.team_id, team.name, isenabled FROM '.TEAM_TABLE.' team  ORDER BY team.name';
         if(($res=db_query($sql)) && db_num_rows($res)){ ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Assigned Teams');?></strong>: <?php echo __("Agent will have access to tickets assigned to a team they belong to regardless of the ticket's department.");?> </em>
            </th>
        </tr>
        <?php
         while(list($id,$name,$isactive)=db_fetch_row($res)){
             $checked=($info['teams'] && in_array($id,$info['teams']))?'checked="checked"':'';
             echo sprintf('<tr><td colspan=2><div class="col-md-5 col-xs-12 check"><input type="checkbox" name="teams[]" value="%d" %s> %s %s</td></tr>',
                     $id,$checked,$name,($isactive?'':__('(disabled)')));
         }
        } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes'); ?></strong></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="28"
                    rows="7" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-top:20px; text-align: center;">
    <input type="submit" name="submit" class="btn btn-primary" value="<?php echo $submit_text; ?>">
    <input type="button" name="cancel" class="btn btn-primary" value="<?php echo __('Cancel');?>" onclick='window.location.href="staff.php"'>
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
        width: 19%;
    }

    input[type=text], input[type=password], select{
        width: 100%;
        margin-bottom: 0px;
    }

    select{
        width: 100%;
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

        input[name=passwd1], input[name=passwd2]{
            width: 100% !important;
            margin-top: -10px !important;
        }

        input[type=text], select{
            width: 98% !important;
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