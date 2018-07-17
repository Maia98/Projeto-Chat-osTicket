<?php
/*********************************************************************
    emailtest.php

    Email Diagnostic

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.email.php');
include_once(INCLUDE_DIR.'class.csrf.php');
$info=array();
$info['subj']='osTicket test email';

if($_POST){
    $errors=array();
    $email=null;
    if(!$_POST['email_id'] || !($email=Email::lookup($_POST['email_id'])))
        $errors['email_id']=__('Select from email address');

    if(!$_POST['email'] || !Validator::is_valid_email($_POST['email']))
        $errors['email']=__('Valid recipient email address required');

    if(!$_POST['subj'])
        $errors['subj']=__('Subject required');

    if(!$_POST['message'])
        $errors['message']=__('Message required');

    if(!$errors && $email){
        if($email->send($_POST['email'],$_POST['subj'],
                Format::sanitize($_POST['message']),
                null, array('reply-tag'=>false))) {
            $msg=Format::htmlchars(sprintf(__('Test email sent successfully to <%s>'),
                $_POST['email']));
            Draft::deleteForNamespace('email.diag');
        }
        else
            $errors['err']=__('Error sending email - try again.');
    }elseif($errors['err']){
        $errors['err']=__('Error sending email - try again.');
    }
}
$nav->setTabActive('emails');
$ost->addExtraHeader('<meta name="tip-namespace" content="emails.diagnostic" />',
    "$('#content').data('tipNamespace', '".$tip_namespace."');");
require(STAFFINC_DIR.'header.inc.php');

$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="emailtest.php" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <h2><?php echo __('Test Outgoing Email');?></h2>

    <table class="form_table">
    <thead>
        <tr>
            <th colspan="2">
                <em><?php echo __('Use the following form to test whether your <strong>Outgoing Email</strong> settings are properly established.');
                    ?>&nbsp;<i class="help-tip icon-question-sign" href="#test_outgoing_email"></i></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="120" class="required">
                <?php echo __('From');?>:
                <span class="error">*</span>
                <font class="error"><?php echo $errors['email_id']; ?></font>
            </td>
            <td>
                <select name="email_id" class="form-control">
                    <option value="0">&mdash; <?php echo __('Select FROM Email');?> &mdash;</option>
                    <?php
                    $sql='SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$email,$name,$smtp)=db_fetch_row($res)){
                            $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            if($smtp)
                                $email.=' ('.__('SMTP').')';

                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="120" class="required">
                <?php echo __('To');?>:
                <span class="error">*</span>
                <font class="error"><?php echo $errors['email']; ?></font>
            </td>
            <td>
                <input type="text" name="email" value="<?php echo $info['email']; ?>">
            </td>
        </tr>
        <tr>
            <td width="120" class="required">
                <?php echo __('Subject');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['subj']; ?></font>
            </td>
            <td>
                <input type="text" name="subj" value="<?php echo $info['subj']; ?>">
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div style="padding-top:0.5em;padding-bottom:0.5em">
                <strong><?php echo __('Message');?></strong>: <?php echo __('email message to send.');?>
                    <span class="error">*</div>
                <font class="error"><?php echo $errors['message']; ?></span></font>
                <textarea class="richtext draft draft-delete" name="message" cols="21"
                    data-draft-namespace="email.diag"
                    rows="10" style="width: 90%;"><?php echo $info['message']; ?></textarea>
            </td>
        </tr>
    </tbody>
    </table>

<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo __('Send Message');?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="emails.php"'>
</p>
</form>
<?php
include(STAFFINC_DIR.'footer.inc.php');
?>

<style>

    table tr td{
        padding-left:10px !important;
    }

    .form-control{
        width: 30% !important;
    }

    input[type=text], select{
        width: 30%;
        margin-top: 10px;
        margin-left: 10px;
    }

    @media screen and (max-width: 450px) {

        .form-control{
            width: 100% !important;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 100%;
            margin-top: 0px !important;
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
            margin-bottom: 0px !important;
            border: 0 !important;
            margin-top: 10px !important;
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
            margin-top: 10px !important;
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
