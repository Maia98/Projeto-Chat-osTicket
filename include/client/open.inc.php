<?php

    if(!defined('OSTCLIENTINC')) die('Access Denied!');
    $info=array();
    if($thisclient && $thisclient->isValid()) {
        $info=array('name'=>$thisclient->getName(),
                    'email'=>$thisclient->getEmail(),
                    'phone'=>$thisclient->getPhoneNumber());
    }

    $info=($_POST && $errors)?Format::htmlchars($_POST):$info;

    $form = null;
    if (!$info['topicId'])
        $info['topicId'] = $cfg->getDefaultTopicId();

    if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
        $form = $topic->getForm();
        if ($_POST && $form) {
            $form = $form->instanciate();
            $form->isValidForClient();
        }
    }

    if (!$info['deptId'])
        $info['deptId'] = 0;

?>
<h1 style="text-align:center;"><?php echo __('Open a New Ticket');?></h1>
<p><?php echo __('Please fill in the form below to open a new ticket.');?></p>
<form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
    <?php csrf_token(); ?>
    <input type="hidden" name="a" value="open">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label><?php echo __('Department'); ?>:</label>
                        <font class="error">* &nbsp;<?php echo $errors['deptId']; ?></font>
                        <select name="deptId" class="form-control">
                            <option value="" selected >&mdash; <?php echo __('Select Department'); ?> &mdash;</option>
                            <?php
                            if($depts=Dept::getDepartments(["publiconly" => "1"])) {
                                foreach($depts as $id =>$name) {
                                    echo sprintf('<option value="%d" %s>%s</option>',
                                        $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label><?php echo __('SLA Plan'); ?>:</label>
                        <font class="error">* &nbsp;<?php echo $errors['slaId']; ?></font>
                        <select name="slaId" class="form-control" readonly="readonly">
                            <option value="0" selected >&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                            <?php
                            if($slas=SLA::getSLAs()) {
                                foreach($slas as $id =>$name) {
                                    echo sprintf('<option value="%d" %s>%s</option>',
                                        $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-5">
                    <?php
                        if (!$thisclient) {
                            $uform = UserForm::getUserForm()->getForm($_POST);

                            if ($_POST)
                              $uform->isValid();

                            $uform->render(false);
                        }
                        else { ?>
                            <label><?php echo __('Client'); ?>: </label>
                            <span><?php echo $thisclient->getName(); ?></span>
                            <br />
                            <label><?php echo __('Email'); ?>: </label>
                            <span><?php echo $thisclient->getEmail(); ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                  <?php if ($form) {
                      include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
                  } ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-10 deatils-ticket">
                    <?php
                        $tform = TicketForm::getInstance();
                        if ($_POST) {
                            $tform->isValidForClient();
                        }
                        $tform->render(false);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-10">
                    <?php
                        if($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
                            if($_POST && $errors && !$errors['captcha'])
                                $errors['captcha']=__('Please re-enter the text again');
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 required">
                    <?php echo __('CAPTCHA Text');?>:
                </div>
                <div class="col-md-9">
                    <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
                    &nbsp;&nbsp;
                    <input id="captcha" type="text" name="captcha" size="6" autocomplete="off">
                    <em><?php echo __('Enter the text shown on the image.');?></em>
                    <font class="error">*&nbsp;<?php echo $errors['captcha']; ?></font>
                </div>
            </div>
        </div>
      </div>

    <br />
  <p style="text-align:center;">
        <input class="btn btn-success" type="submit" value="<?php echo __('Create Ticket');?>">
        <input class="btn btn-danger" type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick="javascript:
            $('.richtext').each(function() {
                var redactor = $(this).data('redactor');
                if (redactor && redactor.opts.draftDelete)
                    redactor.deleteDraft();
            });
            window.location.href='tickets.php';">
  </p>
</form>

<script type="text/javascript">

    $(document).ready(function () {

        $(".deatils-ticket div:eq(1) select").addClass("select-subjects");

        $("select[name=deptId]").change(function (event) {
            getSubjects($(this).val());
        });

        if ($("select[name=deptId]").val() != 0) {
            getSubjects($("select[name=deptId]").val());
        }

        $(".select-subjects").change(function (event) {
            getSla($(this).val());
        });

        if($(".select-subjects").val() != 0){
            getSla($(".select-subjects").val());
        }

    });

    function getSubjects(id) {
        $.ajax({
            type: "POST",
            data: "id="+id,
            url: "ajax.php/form/subjects",
            dataType: 'json',
            success: function(data) {
                if(data.success === true){
                    var subjects = data.subjects;
                    $(".select-subjects option").remove();
                    $(".select-subjects").append('<option value="0" name="0"> — Selecionar — </option>');
                    subjects.forEach(function (index, value) {
                        $(".select-subjects").append("<option value='"+index.id+"'>"+ index.value +"</option>");
                    });
                }else{

                }
            }
        });
    }

    function getSla(id) {
        $.ajax({
            type: "POST",
            data: "id="+id,
            url: "ajax.php/form/sla",
            dataType: 'json',
            success: function(data) {
                if(data.success === true){
                    var sla = data.sla;
                    console.log(sla);
                    $("select[name=slaId] option").remove();
                    sla.forEach(function (index, value) {
                        $("select[name=slaId]").append("<option value='"+index.id+"'>"+index.name+" ("+index.period+" Horas)</option>");
                    });
                }else{

                }
            }
        });
    }

</script>