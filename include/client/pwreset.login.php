<div class="container">

    <?php
    if(!defined('OSTCLIENTINC')) die('Access Denied');

    $userid=Format::input($_POST['userid']);
    ?>
    <h1><?php echo __('Forgot My Password'); ?></h1>
    <p><?php echo __(
    'Enter your username or email address again in the form below and press the <strong>Login</strong> to access your account and reset your password.');
    ?>
    <form action="pwreset.php" method="post" >
        <div>
            <?php csrf_token(); ?>
            <input type="hidden" name="do" value="reset"/>
            <input type="hidden" name="token" value="<?php echo Format::htmlchars($_REQUEST['token']); ?>"/>
            <strong><?php echo Format::htmlchars($banner); ?></strong>
            <br />
            <br />
            <label for="username"><?php echo __('Username'); ?>:</label>
            <br />

            <div class="col-md-6">
                <div class="col-md-8">
                    <input id="username"  class="form-control" type="text" name="userid" value="<?php echo $userid; ?>">
                </div>
                <div class="col-md-2">
                    <input class="btn btn-primary" type="submit" value="Login">
                </div>
                
            </div>
            
        </div>
    </form>
</div>