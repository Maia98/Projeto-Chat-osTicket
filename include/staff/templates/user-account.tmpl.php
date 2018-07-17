<?php
$account = $user->getAccount();
$access = (isset($info['_target']) && $info['_target'] == 'access');

if (!$info['title'])
    $info['title'] = Format::htmlchars($user->getName());
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3><?php echo $info['title']; ?></h3>
<!--    <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
</div>

<div class="modal-body">
    <div class="clear"></div>
    <?php
    if ($info['error']) {
        echo sprintf('<p id="msg_error">%s</p>', $info['error']);
    } elseif ($info['msg']) {
        echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
    } ?>

    <ul class="tabs user-tabs">
        <li><a href="#user-account" <?php echo !$access? 'class="active"' : ''; ?>
            ><i class="icon-user"></i>&nbsp;<?php echo __('User Information'); ?></a></li>
        <div class="division"></div>
        <li><a href="#user-access" <?php echo $access? 'class="active"' : ''; ?>
            ><i class="icon-fixed-width icon-lock faded"></i>&nbsp;<?php echo __('Manage Access'); ?></a></li>
        <div style="clear: both; margin-bottom: 10px;"></div>
    </ul>
    
    <div style="clear: both; margin-bottom: 10px;"></div>

    <form method="post" class="user" action="#users/<?php echo $user->getId(); ?>/manage" style="margin-top: 30px;">
     <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
     <div class="tab_content"  id="user-account" style="display:<?php echo $access? 'none' : 'block'; ?>; margin:5px;">
        <form method="post" class="user" action="#users/<?php echo $user->getId(); ?>/manage" >
            <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
            <table width="100%" class="user-account">
            <tbody>
                <tr>
                    <th colspan="2">
                        <em><strong><?php echo __('User Information'); ?></strong></em>
                    </th>
                </tr>
                <tr>
                    <td>
                        <?php echo __('Name'); ?>:
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <?php echo Format::htmlchars($user->getName()); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo __('Email'); ?>:
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <?php echo $user->getEmail(); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo __('Organization'); ?>:
                        &nbsp;<font class="error">&nbsp;<?php echo $errors['org']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <input type="text" name="org" class="form-control" value="<?php echo $info['org']; ?>">
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <th colspan="2"><em><strong><?php echo __('User Preferences'); ?></strong></em></th>
                </tr>
                    <td>
                        <?php echo __('Time Zone'); ?>:
                        <font class="error"><?php echo $errors['timezone_id']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <select name="timezone_id" id="timezone_id" class="form-control">
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
                    <td width="180">
                       <?php echo __('Daylight Saving'); ?>:
                    </td>
                    <td>
                        <input type="checkbox" name="dst" value="1" <?php echo $info['dst']?'checked="checked"':''; ?>>
                        <?php echo __('Observe daylight saving'); ?>
                    </td>
                </tr>
            </tbody>
            </table>
     </div>
     <div class="tab_content"  id="user-access" style="display:<?php echo $access? 'block' : 'none'; ?>; margin:5px;">
            <table width="100%" class="user-access">
            <tbody>
                <tr>
                    <th colspan="2"><em><strong><?php echo __('Account Access'); ?></strong></em></th>
                </tr>
                <tr>
                    <td><?php echo __('Status'); ?>:</td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <?php echo $user->getAccountStatus(); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo __('Username'); ?>:
                        <i class="help-tip icon-question-sign" data-title="<?php
                        echo __("Login via email"); ?>"
                           data-content="<?php echo sprintf('%s: %s',
                               __('Users can always sign in with their email address'),
                               $user->getEmail()); ?>"></i>
                        <font class="error">&nbsp;<?php echo $errors['username']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <input type="text" class="form-control" name="username" value="<?php echo $info['username']; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo __('New Password'); ?>:
                        <font class="error"><?php echo $errors['passwd1']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
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
                        <div class="col-md-5 col-xs-12">
                            <input type="password" class="form-control" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <th colspan="2"><em><strong><?php echo __('Account Flags'); ?></strong></em></th>
                </tr>
                <tr>
                    <td colspan="2">
                    <?php
                      echo sprintf('<div><input type="checkbox" name="locked-flag" %s
                           value="1"> %s</div>',
                           $account->isLocked() ?  'checked="checked"' : '',
                           __('Administratively Locked')
                           );
                      ?>
                       <div><input type="checkbox" name="pwreset-flag" value="1" <?php
                        echo $account->isPasswdResetForced() ?
                        'checked="checked"' : ''; ?>> <?php echo __('Password Reset Required'); ?></div>
                       <div><input type="checkbox" name="forbid-pwchange-flag" value="1" <?php
                        echo !$account->isPasswdResetEnabled() ?
                        'checked="checked"' : ''; ?>> <?php echo __('User Cannot Change Password'); ?></div>
                    </td>
                </tr>
            </tbody>
            </table>
       </div>
       <hr>
       <p class="full-width">
            <span class="buttons pull-left">
                <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                    <?php echo __('Cancel'); ?>
                </button>
            </span>
            <span class="buttons pull-right">
                <input class="btn btn-success" type="submit"
                    value="<?php echo __('Save Changes'); ?>">
            </span>
        </p>
    </form>
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
});
</script>
<style>

    .modal-content{
        max-height: 550px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    @media screen and (max-width: 450px) {

        table.user-account, table.user-access{
            display: table;
            border: 0 !important;
        }

        table.user-account tr, table.user-access tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.user-account tr td, table.user-access tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.user-account tr td i, table.user-account tr th i, table.user-access tr td i, table.user-access tr th i{
            margin-top: 5px !important;
            float: right;
        }

        table.user-account .col-xs-12, table.user-access .col-xs-12{
            padding: 0 !important;
        }

        table.user-account tr td input[type=radio], table.user-account tr td input[type=checkbox], table.user-access tr td input[type=radio], table.user-access tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.user-account tr td input, table.user-account tr td select, table.user-access tr td input, table.user-access tr td select{
            margin-top: 10px !important;
        }

        table.user-account tr td input[type=text], table.user-account tr td select, table.user-access tr td input[type=text], table.user-access tr td select{
            margin: 0 auto !important;
        }

        table.user-account tr td label, table.user-access tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .user-tabs li a{
            display: block !important;
            width: 100% !important;
        }

        .user-tabs .division{
            clear: both !important;
            margin-bottom: 10px !important;
        }

    }

</style>