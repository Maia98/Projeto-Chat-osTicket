<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST)?Format::htmlchars($_POST):array();
?>

<div class="container">
    <div class="col-md-6 col-md-offset-3" style="margin-top: 10%;">
        <div id="loginBox" class="well">
            <h1 id="logo" style="text-align: center;">
                <a href="index.php" >
                    <span class="valign-helper"></span>
                    <img src="logo.php?login" alt="osTicket :: <?php echo __('Agent Password Reset');?>" />
                </a>
            </h1>
            <h3 class="info" "><?php echo Format::htmlchars($msg); ?></h3>

            <form action="pwreset.php" method="post">
                <?php csrf_token(); ?>
                <input type="hidden" name="do" value="newpasswd"/>
                <input type="hidden" name="token" value="<?php echo Format::htmlchars($_REQUEST['token']); ?>"/>
                <fieldset>
                    <input class="form-control" type="text" name="userid" id="name" value="<?php echo $info['userid']; ?>" placeholder="<?php echo __('Email or Username'); ?>"autocorrect="off" autocapitalize="off"/>
                </fieldset>
                <br />
                <input type="submit"  class="pull-right" />
                <br />
                <br />
            </form>
        </div>
    </div>

</div>

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

</body>
</html>
