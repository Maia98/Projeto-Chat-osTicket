<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>

<div id="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
          <section class="login-form">
            <form action="pwreset.php" method="post" role="login">
                <span class="valign-helper"></span>
                <img src="logo.php?login" alt="osTicket :: <?php echo __('Agent Password Reset');?>"/>
                <h3 class="info"><?php echo Format::htmlchars($msg); ?></h3>
                <?php csrf_token(); ?>
                <input type="hidden" name="do" value="sendmail" class="form-control input-lg">
                <fieldset>
                    <input type="text" name="userid" class="form-control input-lg" id="name" value="<?php echo $info['userid']; ?>" placeholder="<?php echo __('Email or Username'); ?>" autocorrect="off" autocapitalize="off">
                </fieldset>
                <input class="btn btn-primary form-control input-lg submit" type="submit" name="submit" value="<?php echo __('Send Email'); ?>"/>
            </form>
        </div>
    </div>
</div>

<style>

    .info{
        font-size: 18px;
    }

    img{
        width: 230px;
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 6px;
    }

    @media screen and (max-width: 450px) {

        img {
            width: 100% !important;
        }
        .info{
            font-size: 15px;
        }
    }

</style>
</html>
