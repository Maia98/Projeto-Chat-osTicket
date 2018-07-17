<?php

if(!defined('OSTCLIENTINC') || !$thisclient || !$ticket || !$ticket->checkUserAccess($thisclient)) die('Access Denied!');

$info=($_POST && $errors)?Format::htmlchars($_POST):array();

$dept = $ticket->getDept();

if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = __('This ticket is marked as closed and cannot be reopened.');

//Making sure we don't leak out internal dept names
if(!$dept || !$dept->isPublic())
    $dept = $cfg->getDefaultDept();

if ($thisclient && $thisclient->isGuest()
    && $cfg->isClientRegistrationEnabled()) { ?>
    <div id="msg_info">
        <i class="icon-compass icon-2x pull-left"></i>
        <strong><?php echo __('Looking for your other tickets?'); ?></strong></br>
        <a href="<?php echo ROOT_PATH; ?>login.php?e=<?php
            echo urlencode($thisclient->getEmail());
        ?>" style="text-decoration:underline"><?php echo __('Sign In'); ?></a>
        <?php echo sprintf(__('or %s register for an account %s for the best experience on our help desk.'),
            '<a href="account.php?do=create" style="text-decoration:underline">','</a>'); ?>
    </div>
<?php } ?>

    <div class="row">
        <div class="col-md-5">
            <h1>
                <?php echo sprintf(__('Ticket #%s'), $ticket->getNumber()); ?>
                    <a class="link-edit" href="tickets.php?id=<?php echo $ticket->getId(); ?>" title="Reload"><span class="Icon refresh">&nbsp;</span></a><?php if ($cfg->allowClientUpdates() && $thisclient->getId() == $ticket->getUserId()) { ?><a class="action-button pull-right btn btn-primary" href="tickets.php?a=edit&id=<?php echo $ticket->getId(); ?>"><i class="icon-edit"></i> Edit</a>
                <?php } ?>
            </h1>
        </div>
        <div class="col-md-12 well">
            <div class="row">
                <div class="col-md-5">
                    <label><?php echo __('Ticket Status');?>: </label>
                    <span><?php echo $ticket->getStatus(); ?></span>
                </div>
                <div class="col-md-5">
                    <label><?php echo __('Name');?>:</label>
                    <span><?php echo mb_convert_case(Format::htmlchars($ticket->getName()), MB_CASE_TITLE); ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label><?php echo __('Department');?>:</label>
                    <span><?php echo Format::htmlchars($dept instanceof Dept ? $dept->getName() : ''); ?></span>
                </div>
                <div class="col-md-5">
                    <label><?php echo __('Email');?>:</label>
                    <span><?php echo Format::htmlchars($ticket->getEmail()); ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label><?php echo __('Create Date');?>:</label>
                    <span><?php echo Format::db_datetime($ticket->getCreateDate()); ?></span>
                </div>
                <div class="col-md-5">
                    <label><?php echo __('Phone');?>:</label>
                    <span><?php echo $ticket->getPhoneNumber(); ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label><?php echo "Data de vencimento";?>:</label>
                    <span><?php echo Format::db_datetime($ticket->getDueDate()); ?></span>
                </div>
            </div>
            <div class="row">
                <?php
                    foreach (DynamicFormEntry::forTicket($ticket->getId()) as $idx=>$form) {
                        $answers = $form->getAnswers();

                        if ($idx > 0 and $idx % 2 == 0) { ?>
                            <br />
                        <?php } ?>

                        <div class="col-md-6">
                            <div class="infoTable row">
                                <?php foreach ($answers as $answer) {
                                    if (in_array($answer->getField()->get('name'), array('name', 'email', 'subject')))
                                        continue;
                                    elseif ($answer->getField()->get('private'))
                                        continue;
                                    ?>
                                    <div class="col-md-10">
                                        <label><?php echo $answer->getField()->get('label'); ?>:</label>
                                        <span><?php echo $answer->display(); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php } ?>
            </div>
            <hr />
            <div class="row">

                <div class="col-md-6">
                    <h3 class="subject">
                        <?php echo __('Subject'); ?>:
                        <strong>
                            <?php echo Format::htmlchars($ticket->getSubject()); ?>
                        </strong>
                    </h3>
                </div>
                <div id="ticketThread" class="col-md-12">
                    <?php
                        if($ticket->getThreadCount() && ($thread=$ticket->getClientThread())) {
                            $threadType=array('M' => 'message', 'R' => 'response');
                            foreach($thread as $entry) {

                                //Making sure internal notes are not displayed due to backend MISTAKES!
                                if(!$threadType[$entry['thread_type']]) continue;
                                $poster = $entry['poster'];
                                if($entry['thread_type']=='R' && ($cfg->hideStaffName() || !$entry['staff_id']))
                                    $poster = ' ';
                                ?>
                                <div class="thumbnail <?php echo $threadType[$entry['thread_type']]; ?>">
                                    <div>
                                        <label cl><?php echo $poster; ?> - </label>
                                        <span class="label label-default"><?php echo Format::db_datetime($entry['created']); ?></span>
                                        &nbsp;&nbsp;<span class="textra"></span>

                                    </div>

                                    <div>
                                        <?php echo Format::clickableurls($entry['body']->toHtml()); ?>
                                    </div>


                                    <?php
                                        if($entry['attachments']
                                                && ($tentry=$ticket->getThreadEntry($entry['id']))
                                                && ($urls = $tentry->getAttachmentUrls())
                                                && ($links=$tentry->getAttachmentsLinks())) { ?>
                                            <div class="label label-info"><?php echo $links; ?></div>
                                    <?php }
                                        if ($urls) { ?>
                                            <script type="text/javascript">
                                                $(function() { showImagesInline(<?php echo
                                                    JsonDataEncoder::encode($urls); ?>); });
                                            </script>
                                    <?php } ?>
                                </div>
                            <?php
                            }
                        }
                    ?>
                </div>

            </div>
        </div>
    </div>

<div class="clear" style="padding-bottom:10px;"></div>
<?php if($errors['err']) { ?>
    <div class="alert alert-danger" role="alert"><?php echo $errors['err']; ?></div>
<?php }elseif($msg) { ?>
    <div class="alert alert-success" role="alert"><?php echo $msg; ?></div>
<?php }elseif($warn) { ?>
    <div class="alert alert-warning" role="alert"><?php echo $warn; ?></div>
<?php } ?>

<?php

if (!$ticket->isClosed()) { ?>
    <div class="row">
        <div class="col-md-12 thumbnail" style="padding: 0 25px;">
            <form id="reply" action="tickets.php?id=<?php echo $ticket->getId(); ?>#reply" name="reply" method="post" enctype="multipart/form-data">
                <?php csrf_token(); ?>
                <h2><?php echo __('Post a Reply');?></h2>
                <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
                <input type="hidden" name="a" value="reply">
                <div class="col-md-12">
                    <div class="row">
                        <div >
                            <?php
                                if($ticket->isClosed()) {
                                    $msg='<b>'.__('Ticket will be reopened on message post').'</b>';
                                } else {
                                    $msg=__('To best assist you, we request that you be specific and detailed');
                                }
                            ?>
                            <span id="msg"><em><?php echo $msg; ?> </em></span><font class="error">*&nbsp;<?php echo $errors['message']; ?></font>
                            <br/>
                            <textarea name="message" id="message" cols="50" rows="9" wrap="soft"
                                data-draft-namespace="ticket.client"
                                data-draft-object-id="<?php echo $ticket->getId(); ?>"
                                class="richtext ifhtml draft"><?php echo $info['message']; ?></textarea>
                            <?php
                            if ($messageField->isAttachmentsEnabled()) { ?>
                                <link rel="stylesheet" type="text/css" href="/ticket/css/filedrop.css">
                                <?php
                                    print $attachments->render(true);
                                    print $attachments->getForm()->getMedia();
                                ?>

                            <?php
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input class="btn btn-success" type="submit" value="<?php echo __('Post Reply');?>">
                        <input class="btn btn-danger" type="button" value="<?php echo __('Cancel');?>" onClick="javascript:
        window.location.href='tickets.php';">
                    </div>
                </div>
            </form>
            <br />
        </div>
    </div>
<?php
} ?>

<style>



</style>
