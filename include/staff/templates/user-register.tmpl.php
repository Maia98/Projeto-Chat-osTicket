<?php
global $cfg;

if (!$info['title'])
    $info['title'] = sprintf(__('Register: %s'), Format::htmlchars($user->getName()));

if (!$_POST) {

    $info['sendemail'] = true; // send email confirmation.

    if (!isset($info['timezone_id']))
        $info['timezone_id'] = $cfg->getDefaultTimezoneId();

    if (!isset($info['dst']))
        $info['dst'] = $cfg->observeDaylightSaving();
}

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title"><?php echo $info['title']; ?></h3>
</div>

<div class="modal-body">
    <?php
    if ($info['error']) {
        echo sprintf('<p id="msg_error">%s</p>', $info['error']);
    } elseif ($info['msg']) {
        echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
    } ?>
    <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp;<?php
    echo sprintf(__(
    'Complete the form below to create a user account for <b>%s</b>.'
    ), Format::htmlchars($user->getName()->getOriginal())
    ); ?>
    </p></div>
    <div id="user-registration" style="display:block; margin:5px;">
        <form method="post" class="user"
            action="#users/<?php echo $user->getId(); ?>/register">
            <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
            <table class="table-user-register" width="100%">
            <tbody>
                <tr>
                    <th colspan="2">
                        <em><strong><?php echo __('User Account Login'); ?></strong></em>
                    </th>
                </tr>
                <tr>
                    <td><?php echo __('Authentication Sources'); ?>:</td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control" name="backend" id="backend-selection" onchange="javascript:
                                if (this.value != '' && this.value != 'client') {
                                    $('#activation').hide();
                                    $('#password').hide();
                                }
                                else {
                                    $('#activation').show();
                                    if ($('#sendemail').is(':checked'))
                                        $('#password').hide();
                                    else
                                        $('#password').show();
                                }
                                ">
                                <option value="">&mdash; <?php echo __('Use any available backend'); ?> &mdash;</option>
                            <?php foreach (UserAuthenticationBackend::allRegistered() as $ab) {
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
                <tr>
                    <td width="180">
                        <?php echo __('Username'); ?>:
                        <font class="error">&nbsp;<?php echo $errors['username']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="text" name="username" value="<?php echo $info['username'] ?: $user->getEmail(); ?>">
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody id="activation">
                <tr>
                    <td width="180">
                        <?php echo __('Status'); ?>:
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                          <input type="checkbox" id="sendemail" name="sendemail" value="1"
                            <?php echo $info['sendemail'] ? 'checked="checked"' :
                            ''; ?> ><?php echo sprintf(__(
                            'Send account activation email to %s.'), $user->getEmail()); ?>
                        </div>
                        <div class="division"></div>
                    </td>
                </tr>
            </tbody>
            <tbody id="password"
                style="<?php echo $info['sendemail'] ? 'display:none;' : ''; ?>"
                >
                <tr>
                    <td>
                        <?php echo __('Temporary Password'); ?>:
                        <font class="error"><?php echo $errors['passwd1']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="password" class="form-control" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                        </div>

                    </td>
                </tr>
                <tr>
                    <td>
                       <?php echo __('Confirm Password'); ?>:
                        <font class="error"><?php echo $errors['passwd2']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="password" class="form-control" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo __('Password Change'); ?>:
                    </td>
                    <td colspan=2>
                        <div class="col-md-12 col-xs-12">
                        <input type="checkbox" name="pwreset-flag" value="1" <?php
                            echo $info['pwreset-flag'] ?  'checked="checked"' : ''; ?>>
                            <?php echo __('Require password change on login'); ?>
                        </div>
                        <div class="col-md-12 col-xs-12">
                        <input type="checkbox" name="forbid-pwreset-flag" value="1" <?php
                            echo $info['forbid-pwreset-flag'] ?  'checked="checked"' : ''; ?>>
                            <?php echo __('User cannot change password'); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <th colspan="2"><em><strong><?php echo
                        __('User Preferences'); ?></strong></em></th>
                </tr>
                    <td>
                        <?php echo __('Time Zone'); ?>:
                        <font class="error"><?php echo $errors['timezone_id']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control" name="timezone_id" id="timezone_id">
                                <?php
                                $sql='SELECT id, offset, timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                                if(($res=db_query($sql)) && db_num_rows($res)){
                                    while(list($id, $offset, $tz) = db_fetch_row($res)) {
                                        $sel=($info['timezone_id']==$id) ? 'selected="selected"' : '';
                                        echo sprintf('<option value="%d" %s>GMT %s - %s</option>',
                                                $id, $sel, $offset, $tz);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="180">
                       <?php echo __('Daylight Saving'); ?>:
                    </td>
                    <td>
                        <div class="col-md-12 col-xs-12">
                            <input type="checkbox" name="dst" value="1" <?php echo $info['dst'] ? 'checked="checked"' : ''; ?>>
                            <?php echo __('Observe daylight saving'); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            </table>
            <hr>
            <p class="full-width">
                <span class="buttons pull-left">
                    <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                        <?php echo __('Cancel'); ?>
                    </button>
                </span>
                <span class="buttons pull-right">
                    <input class="btn btn-success" type="submit" value="<?php echo __('Create Account'); ?>">
                </span>
             </p>
        </form>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    $(document).on('click', 'input#sendemail', function(e) {
        if ($(this).prop('checked'))
            $('tbody#password').hide();
        else
            $('tbody#password').show();
    });

    $("table.table-user-register tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

});
</script>

<style>

    table.table-user-register input[type=text], table.table-user-register select{
        width: 100%;
    }

    .division{
        clear: both;
        margin-bottom: 10px;
    }

    .modal-content{
        max-height: 600px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    @media screen and (max-width: 450px) {

        table.table-user-register {
            display: table;
            border: 0 !important;
        }

        table.table-user-register tr {
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.table-user-register tr td {
            width: 100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.table-user-register tr td i, table.table-user-register tr th i {
            margin-top: 5px !important;
            float: right;
        }

        table.table-user-register .col-xs-12 {
            padding: 0 !important;
        }

        table.table-user-register tr td input[type=radio], table.table-user-register tr td input[type=checkbox] {
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.table-user-register tr td input, table.table-user-register tr td select {
            margin-top: 10px !important;
        }

        table.table-user-register tr td input[type=text], table.table-user-register tr td select {
            width: 100% !important;
            margin: 0 auto !important;
        }

        table.table-user-register tr td label {
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        table.table-user-register tr td input[name=passwd1], table.table-user-register tr td input[name=passwd2]{
            margin-top: -10px !important;
        }
    }

</style>