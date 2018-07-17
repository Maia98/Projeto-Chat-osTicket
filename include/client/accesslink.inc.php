<?php

if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);

if ($cfg->isClientEmailVerificationRequired())
    $button = __("Email Access Link");
else
    $button = __("View Ticket");
?>
<h1 style="margin-top: 40px;"><?php echo __('Check Ticket Status'); ?></h1>
<p>
    <?php
        echo __('Please provide your email address and a ticket number.');
        if ($cfg->isClientEmailVerificationRequired())
            echo ' '.__('An access link will be emailed to you.');
        else
            echo ' '.__('This will sign you in to view your ticket.');
    ?>
</p>

<div class="clear" style="padding-bottom:10px;"></div>
<?php if($errors['err']) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $errors['err']; ?></div>
<?php }elseif($msg) { ?>
    <div class="alert alert-success" role="alert"><?php echo $msg; ?></div>
<?php }elseif($warn) { ?>
    <div class="alert alert-warning" role="alert"><?php echo $warn; ?></div>
<?php } ?>

<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
    <div class="col-md-5
">
    <div class="well">
        <div class="row">
            <div><strong><?php echo Format::htmlchars($errors['login']); ?></strong></div>
            <div class="col-md-12 hidden-border" style="border-right: rgba(171, 171, 171, 0.63); border-style: solid; border-bottom: none; border-left: none; border-top: none; ">
                <div class="row">
                    <div class="col-md-12">
                        <label for="email">
                            <?php echo __('E-Mail Address'); ?>:
                        </label>
                        <input class="form-control" id="email" placeholder="<?php echo __('e.g. john.doe@osticket.com'); ?>" type="text" name="lemail" value="<?php echo $email; ?>">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div  class="col-md-12">
                        <label for="ticketno">
                            <?php echo __('Ticket Number'); ?>:
                        </label>
                        <input class="form-control" id="ticketno" type="text" name="lticket" placeholder="<?php echo __('e.g. 051243'); ?>" value="<?php echo $ticketid; ?>">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div  class="col-md-12">
                        <p style="text-align: right">
                            <input class="btn btn-success" type="submit" value="<?php echo $button; ?>">
                        </p>
                    </div>
                </div>

                <div>
                <?php if ($cfg && $cfg->getClientRegistrationMode() !== 'disabled') { ?>
                        <?php echo __('Have an account with us?'); ?>
                        <a href="scp/login.php"><?php echo __('Sign In'); ?></a> <?php
                    if ($cfg->isClientRegistrationEnabled()) { ?>
                <?php echo sprintf(__('or %s register for an account %s to access all your tickets.'),
                    '<a href="account.php?do=create">','</a>');
                    }
                }?>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>

<style>

    .hidden-border{
        border-style: none !important;
    }

</style>
