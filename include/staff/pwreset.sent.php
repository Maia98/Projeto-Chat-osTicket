<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>

<div id="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div id="loginBox" class="well">
                <a style="text-align: center" href="index.php">
                    <span class="valign-helper"></span>
                    <img src="logo.php?login" alt="osTicket :: <?php echo __('Agent Password Reset');?>"/>
                </a>
                <h3 class="info" style="color:black;"><?php echo __('A confirmation email has been sent'); ?></h3>
                <h3 class="info"><?php echo __(
                'A password reset email was sent to the email on file for your account.  Follow the link in the email to reset your password.'
                ); ?>
                </h3>

                <form action="index.php" method="get">
                    <input class="submit btn btn-primary" type="submit" name="submit" value="Login"/>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<style>

    .info{
        font-size: 18px;
    }

    #loginBox{
        margin-top: 60px;
        border-radius: 10px;
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
