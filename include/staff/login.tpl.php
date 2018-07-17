<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>
<div id="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
          <section class="login-form">
            <form method="post" action="login.php" role="login">
              <span class="valign-helper"></span>
              <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php echo $ost->getConfig()->getTitle(); ?>"/>
              <h3 class="info"><?php echo Format::htmlchars($msg); ?></h3>
                <?php csrf_token(); ?>
                <input type="hidden" name="do" value="scplogin">
                <fieldset>
                    <input type="text" name="userid" class="form-control input-lg" id="name" value="<?php echo $info['userid']; ?>" placeholder="<?php echo __('Email or Username'); ?>" autocorrect="off" autocapitalize="off">
                    <input type="password" name="passwd" class="form-control input-lg" id="pass" placeholder="<?php echo __('Password'); ?>" autocorrect="off" autocapitalize="off">
                    <?php if ($show_reset && $cfg->allowPasswordReset()) { ?>
                    <h3 class="info" style="display:inline;"><a href="pwreset.php"><?php echo __('Forgot my password'); ?></a></h3>
                    <?php } ?>
                    <input class="submit btn btn-lg btn-primary btn-block" type="submit" name="submit" value="<?php echo __('Log In'); ?>">
                </fieldset>
            </form>
                <?php
                $ext_bks = array();
                foreach (StaffAuthenticationBackend::allRegistered() as $bk)
                    if ($bk instanceof ExternalAuthentication)
                        $ext_bks[] = $bk;

                if (count($ext_bks)) { ?>
                <div class="or">
                    <hr/>
                </div><?php
                    foreach ($ext_bks as $bk) { ?>
                <div class="external-auth"><?php $bk->renderExternalLink(); ?></div><?php
                    }
                } ?>
            <div class="form-links">
              <a href="<?php echo  ROOT_PATH."/"; ?>">Voltar</a>
            </div>
          </section>  
        </div>
    </div>
</div>

<style>

    body{
        overflow: hidden;
    }

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
