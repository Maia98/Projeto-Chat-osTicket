<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

?>
<h2><?php echo __('Access Control Settings'); ?></h2>
<form action="settings.php?t=access" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="access" >
<table class="form_table settings_table table-pattern" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Configure Access to this Help Desk'); ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('Agent Authentication Settings'); ?></b></em>
            </th>
        </tr>
        <tr>
            <td class="td-label"><?php echo __('Password Expiration Policy'); ?>:
                <i class="help-tip icon-question-sign" href="#password_expiration_policy"></i>
                <font class="error"><?php echo $errors['passwd_reset_period']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="passwd_reset_period" class="form-control">
                   <option value="0"> &mdash; <?php echo __('No expiration'); ?> &mdash;</option>
                  <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $i,(($config['passwd_reset_period']==$i)?'selected="selected"':''),
                                sprintf(_N('Monthly', 'Every %d months', $i), $i));
                    }
                    ?>
                </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Allow Password Resets'); ?>:
                <i class="help-tip icon-question-sign" href="#allow_password_resets"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="allow_pw_reset" <?php echo $config['allow_pw_reset']?'checked="checked"':''; ?>>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Reset Token Expiration'); ?> (<?php echo __('minutes'); ?>):
                <i class="help-tip icon-question-sign" href="#reset_token_expiration"></i>
                &nbsp;<font class="error"><?php echo $errors['pw_reset_window']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                  <input type="text" class="form-control" name="pw_reset_window" size="6" value="<?php
                        echo $config['pw_reset_window']; ?>">

                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Agent Excessive Logins'); ?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="staff_max_logins" class="form-control">
                      <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_max_logins']==$i)?'selected="selected"':''), $i);
                        }
                        ?>
                    </select>
                </div>
                <div style="clear: both"></div>
                <div class="col-md-5 col-xs-12">
                    <?php echo __('failed login attempt(s) allowed before a lock-out is enforced'); ?>
                    <select name="staff_login_timeout" class="form-control">
                      <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_login_timeout']==$i)?'selected="selected"':''), $i);
                        }
                        ?>
                    </select>
                    <?php echo __('minutes locked out'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Agent Session Timeout'); ?> (<?php echo __('minutes'); ?>): <em><?php echo __('(0 to disable)'); ?></em>
                <i class="help-tip icon-question-sign" href="#staff_session_timeout"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="staff_session_timeout" size=6 value="<?php echo $config['staff_session_timeout']; ?>">


                </div>
            </td>
        </tr>
        <tr><td><?php echo __('Bind Agent Session to IP'); ?>:
                <i class="help-tip icon-question-sign" href="#bind_staff_session_to_ip"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="staff_ip_binding" <?php echo $config['staff_ip_binding']?'checked="checked"':''; ?>>
                </div>       
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('End User Authentication Settings'); ?></b></em>
            </th>
        </tr>
        <tr>
            <td><?php echo __('Registration Required'); ?>:
                <i class="help-tip icon-question-sign" href="#registration_method"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="clients_only" <?php
                    if ($config['clients_only'])
                        echo 'checked="checked"'; ?>/> <?php echo __(
                        'Require registration and login to create tickets'); ?>
                </div>
            </td>
        <tr>
            <td><?php echo __('Registration Method'); ?>:
                <i class="help-tip icon-question-sign" href="#registration_method"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="client_registration" class="form-control">
                    <?php foreach (array(
                        'disabled' => __('Disabled — All users are guests'),
                        'public' => __('Public — Anyone can register'),
                        'closed' => __('Private — Only agents can register users'),)
                        as $key=>$val) { ?>
                            <option value="<?php echo $key; ?>" <?php
                            if ($config['client_registration'] == $key)
                                echo 'selected="selected"'; ?>><?php echo $val;
                            ?></option><?php
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
        <td><?php echo __('User Excessive Logins'); ?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="client_max_logins" class="form-control">
                      <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_max_logins']==$i)?'selected="selected"':''), $i);
                        }

                        ?>
                    </select> 
                </div>
                <div style="clear: both"></div>
                <div class="col-md-5 col-xs-12">
                    <?php echo __('failed login attempt(s) allowed before a lock-out is enforced'); ?>
                    <select name="client_login_timeout" class="form-control">
                      <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_login_timeout']==$i)?'selected="selected"':''), $i);
                        }
                        ?>
                    </select>
                    <?php echo __('minutes locked out'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('User Session Timeout'); ?>:<i class="help-tip icon-question-sign" href="#client_session_timeout"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="client_session_timeout" size=6 value="<?php echo $config['client_session_timeout']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Client Quick Access'); ?>:<i class="help-tip icon-question-sign" href="#client_verify_email"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                <input type="checkbox" name="client_verify_email" <?php
                if ($config['client_verify_email'])
                    echo 'checked="checked"'; ?>/> <?php echo __(
                'Require email verification on "Check Ticket Status" page'); ?>
                </div>
            </td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Authentication and Registration Templates'); ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = db_query('select distinct(`type`), content_id, notes, name, updated from '
            .PAGE_TABLE
            .' where isactive=1 group by `type`');
        $contents = array();
        while (list($type, $id, $notes, $name, $u) = db_fetch_row($res))
            $contents[$type] = array($id, $name, $notes, $u);

        $manage_content = function($title, $content) use ($contents) {
            list($id, $name, $notes, $upd) = $contents[$content];
            $notes = explode('. ', $notes);
            $notes = $notes[0];
            ?><tr><td colspan="2">
            <a href="#ajax.php/content/<?php echo $id; ?>/manage"
            onclick="javascript:
                $.dialog($(this).attr('href').substr(1), 201);
            return false;" class="pull-left"><i class="icon-file-text icon-2x"
                style="color:#bbb;"></i> </a>
            <span style="display:inline-block;width:90%;padding-left:10px;line-height:1.2em">
            <a href="#ajax.php/content/<?php echo $id; ?>/manage"
            onclick="javascript:
                $.dialog($(this).attr('href').substr(1), 201);
            return false;"><?php
            echo Format::htmlchars($title); ?></a><br/>
            <span class="faded"><?php
                echo Format::display($notes); ?>
            <em>(<?php echo sprintf(__('Last Updated %s'), Format::db_datetime($upd));
                ?>)</em></span></span></td></tr><?php
        }; ?>
        <tr>
            <th colspan="2">
                <em><b><?php echo __(
                'Authentication and Registration Templates'); ?></b></em>
            </th>
        </tr>
        <?php $manage_content(__('Agents'), 'pwreset-staff'); ?>
        <?php $manage_content(__('Clients'), 'pwreset-client'); ?>
        <?php $manage_content(__('Guest Ticket Access'), 'access-link'); ?>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('Sign In Pages'); ?></b></em>
            </th>
        </tr>
        <?php $manage_content(__('Agent Login Banner'), 'banner-staff'); ?>
        <?php $manage_content(__('Client Sign-In Page'), 'banner-client'); ?>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('User Account Registration'); ?></b></em>
            </th>
        </tr>
        <?php $manage_content(__('Please Confirm Email Address Page'), 'registration-confirm'); ?>
        <?php $manage_content(__('Confirmation Email'), 'registration-client'); ?>
        <?php $manage_content(__('Account Confirmed Page'), 'registration-thanks'); ?>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('Agent Account Registration'); ?></b></em>
            </th>
        </tr>
        <?php $manage_content(__('Agent Welcome Email'), 'registration-staff'); ?>
</tbody>
</table>
<p style="text-align:center; padding-top: 20px;">
    <input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes'); ?>">
</p>
</form>
<style>

    table tr td{
        padding: 5px !important;
    }

    select, input{
        margin-top: 10px;
    }

    td.td-label{
        width: 25%;
    }

    @media screen and (max-width: 450px) {

        td.td-label{
            width: 100% !important;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.table-pattern{
            display: table;
            border: 0 !important;
        }

        table.table-pattern tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        .table-pattern tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        .table-pattern tr td div span{
            width: 100% !important;
        }

        .table-pattern tr td i, .table-pattern tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .table-pattern tr td input[type=radio], .table-pattern tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        .table-pattern tr td input, .table-pattern tr td select{
            margin-top: 10px !important;
        }

        .table-pattern tr td input[type=text], .table-pattern tr td select{
            margin: 0 auto !important;
        }

        .table-pattern tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }


        input[type=submit], input[type=reset], input[type=button] {
            width: 100% !important;
            margin-bottom: 10px;
        }

        .redactor-modal {
            display: inline-block;
            text-align: left;
            vertical-align: middle;
        }

        .modal-content{
            overflow: scroll !important;
            max-height: 550px !important;
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

    $("body").on("change", "select", function () {
        $("input[type=submit]").css("color", "#fff");
    });

</script>