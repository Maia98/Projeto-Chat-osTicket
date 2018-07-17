<?php

//TODO: Aqui Onde está para adicionar o campo de agentes.
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canCreateTickets()) die('Access Denied');
$info=array();
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

$useremail = $thisstaff->ht['email'];
$username  = $thisstaff->ht['firstname']." ".$thisstaff->ht['lastname'];

if (!$info['topicId'])
    $info['topicId'] = $cfg->getDefaultTopicId();

$form = null;
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    $form = $topic->getForm();
    if ($_POST && $form) {
        $form = $form->instanciate();
        $form->isValid();
    }
}

if ($_POST)
    $info['duedate'] = Format::date($cfg->getDateFormat(),
       strtotime($info['duedate']));
?>
<form action="tickets.php?a=open" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="create">
 <input type="hidden" name="a" value="open">
 <h2><?php echo __('Open a New Ticket');?></h2>
 <table class="form_table table-ticket" style="width: 100%;">
    <thead>
    <!-- This looks empty - but beware, with fixed table layout, the user
         agent will usually only consult the cells in the first row to
         construct the column widths of the entire toable. Therefore, the
         first row needs to have two cells -->
        <tr><td></td><td></td></tr>
        <tr>
            <th colspan="2">
                <h4><?php echo __('New Ticket');?></h4>
            </th>
        </tr>
    </thead>
    <tbody class="user-info">
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('User Information'); ?></strong>: </em>
            </th>
        </tr>
        <?php
        if ($user) { ?>
        <tr><td><?php echo __('User'); ?>:</td><td>
            <div id="user-info">
                <input type="hidden" name="uid" id="uid" value="<?php echo $user->getId(); ?>" />
            <a href="#" class="open-modal" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $user->getId(); ?>/edit',
                        function (user) {
                            $('#user-name').text(user.name);
                            $('#user-email').text(user.email);
                        });
                return false;
                "><i class="icon-user"></i>
                <span id="user-name"><?php echo Format::htmlchars($user->getName()); ?></span>
                &lt;<span id="user-email"><?php echo $user->getEmail(); ?></span>&gt;
                </a>
                <a class="action-button open-modal" style="overflow:inherit" href="#"
                    onclick="javascript:
                        $.userLookup('ajax.php/users/select/'+$('input#uid').val(),
                            function(user) {
                                $('input#uid').val(user.id);
                                $('#user-name').text(user.name);
                                $('#user-email').text('<'+user.email+'>');
                        });
                        return false;
                    "><i class="icon-edit"></i> <?php echo __('Change'); ?></a>
            </div>
        </td></tr>
        <?php
        } else { //Fallback: Just ask for email and name
            ?>
        <tr>
            <td class="required"><?php echo __('Email Address'); ?>:
                <span class="error">*</span>
                <font class="error"><?php echo $errors['email']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
<!--                        <input type="text" class="form-control" name="email" id="user-email" autocomplete="off" autocorrect="off" value="--><?php //echo $info['email']; ?><!--" />-->
                    <input type="text" class="form-control" name="email" id="user-email" autocomplete="off" autocorrect="off" value="<?php echo $useremail; ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Full Name'); ?>:
                <span class="error">*</span>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
<!--                    <input type="text" name="name" class="form-control" id="user-name" value="--><?php //echo $info['name']; ?><!--" />-->
                    <input type="text" name="name" class="form-control" id="user-name" value="<?php echo $username; ?>" />
                    <input type="hidden" name="slaId" class="form-control" />
                </div>
            </td>
        </tr>
        <?php
        } ?>

        <?php
        if($cfg->notifyONNewStaffTicket()) {  ?>
        <tr>
            <td><?php echo __('Ticket Notice'); ?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="alertuser" <?php echo (!$errors || $info['alertuser'])? 'checked="checked"': ''; ?>><?php
                    echo __('Send alert to user.'); ?>
                </div>
            </td>
        </tr>
        <?php
        } ?>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Ticket Information and Options');?></strong>:</em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('Department'); ?>:
                &nbsp;<font class="error"><?php echo $errors['deptId']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="deptId" class="form-control">
                    <option value="" selected >&mdash; <?php echo __('Select Department'); ?>&mdash;</option>
                    <?php
                    if($depts=Dept::getDepartments()) {
                        foreach($depts as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                    </select>    
                </div>
            </td>
        </tr>

         <tr>
            <td>
                <?php echo __('SLA Plan');?>:
                <font class="error"><?php echo $errors['sla_duedate']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sla_duedate" class="form-control" disabled>
                    <option value="0" selected="selected" >&mdash; <?php echo __('System Default');?> &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['sla_duedate']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                </div>
            </td>
         </tr>

         <tr style="display: none">
            <td>
                <?php echo __('Due Date');?>:
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input class="dp" class="form-control" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" autocomplete=OFF>
                    <?php
                    $min=$hr=null;
                    if($info['time'])
                        list($hr, $min)=explode(':', $info['time']);

                        echo Misc::timeDropdown($hr, $min, 'time');
                    ?>

                    <em><?php echo __('Time is based on your time zone');?> (GMT <?php echo $thisstaff->getTZoffset(); ?>)</em>
                </div>
            </td>
        </tr>

        <?php
        if($thisstaff->canAssignTickets()) { ?>
        <tr>
            <td>
                <?php echo __('Assign To');?>:
                <font class='error'><?php echo $errors['assignId']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                 <select class="form-control" id="assignId" name="assignId">
                    <option value="0" selected="selected">&mdash; <?php echo __('Select an Agent OR a Team');?> &mdash;</option>
                    <?php
                    if(($users=Staff::getAvailableStaffMembers())) {
                        echo '<OPTGROUP label="'.sprintf(__('Agents (%d)'), count($users)).'">';
                        foreach($users as $id => $name) {
                            $k="s$id";
                            echo sprintf('<option value="%s" %s style="font-size: 12px;">%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }

                    if(($teams=Team::getActiveTeams())) {
                        echo '<OPTGROUP label="'.sprintf(__('Teams (%d)'), count($teams)).'">';
                        foreach($teams as $id => $name) {
                            $k="t$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>
                </div>
            </td>
        </tr>
        <?php } ?>

        </tbody>
        <tbody id="dynamic-form">
        <div class="col-md-5 col-xs-12">
        <?php
            if ($form) {
                print $form->getForm()->getMedia();
                include(STAFFINC_DIR .  'templates/dynamic-form.tmpl.php');
            }
        ?>
        </div>
        </tbody>
        <tbody class="deatils-ticket"> <?php
        $tform = TicketForm::getInstance();
        if ($_POST && !$tform->errors())
            $tform->isValidForStaff();
        $tform->render(true);
        ?>
        </tbody>
        <tbody>
        <?php
        //is the user allowed to post replies??
        if($thisstaff->canPostReply()) { ?>
<!--            COMENTADO ABAIXO Resposta Pronta->
<!--        <tr>-->
<!--            <th colspan="2">-->
<!--                <em><strong>--><?php //echo __('Response');?><!--</strong>: --><?php //echo __('Optional response to the above issue.');?><!--</em>-->
<!--            </th>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td>-->
<!--                --><?php //echo __('Canned Response');?><!--:&nbsp;-->
<!--            </td>-->
<!--            <td style="padding-top: 20px;">-->
<!--                     --><?php
//                    if(($cannedResponses=Canned::getCannedResponses())) {
//                    ?>
<!--                        <div class="col-md-5 col-xs-12">-->
<!--                            <select id="cannedResp" class="form-control" name="cannedResp">-->
<!--                                <option value="0" selected="selected">&mdash; --><?php //echo __('Select a canned response');?><!-- &mdash;</option>-->
<!--                                --><?php
//                                foreach($cannedResponses as $id =>$title) {
//                                    echo sprintf('<option value="%d">%s</option>',$id,$title);
//                                }
//                                ?>
<!--                            </select>-->
<!--                        </div>-->
<!--                        <div class="col-md-5 col-xs-12" style="margin-top: 10px;">-->
<!--                            <label><input type='checkbox' value='1' name="append" id="append" checked="checked">--><?php //echo __('Append');?><!--</label>-->
<!--                        </div>-->
<!--                        --><?php
//                    }
//                    ?><!--   -->
<!--            </td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td colspan="2">-->
<!--            --><?php
//                $signature = '';
//                if ($thisstaff->getDefaultSignatureType() == 'mine')
//                    $signature = $thisstaff->getSignature(); ?>
<!--                <textarea class="richtext ifhtml draft draft-delete"-->
<!--                    data-draft-namespace="ticket.staff.response"-->
<!--                    data-signature="--><?php
//                        echo Format::htmlchars(Format::viewableImages($signature)); ?><!--"-->
<!--                    data-signature-field="signature" data-dept-field="deptId"-->
<!--                    placeholder="--><?php //echo __('Initial response for the ticket'); ?><!--"-->
<!--                    name="response" id="response" cols="21" rows="8"-->
<!--                    style="width:80%;">--><?php //echo $info['response']; ?><!--</textarea>-->
<!--                    <div class="attachments">-->
<!--            --><?php
//            print $response_form->getField('attachments')->render();
//            ?>
<!--                    </div>-->
<!--            </td>-->
<!--        </tr>-->
        <tr>            
            <td ><?php echo __('Ticket Status');?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="statusId" class="form-control">
                    <?php
                    $statusId = $info['statusId'] ?: $cfg->getDefaultTicketStatusId();
                    $states = array('open');
                    if ($thisstaff->canCloseTickets())
                        $states = array_merge($states, array('closed'));
                    foreach (TicketStatusList::getStatuses(
                                array('states' => $states)) as $s) {
                        if (!$s->isEnabled()) continue;
                        $selected = ($statusId == $s->getId());
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $s->getId(),
                                $selected
                                 ? 'selected="selected"' : '',
                                __($s->getName()));
                    }
                    ?>
                    </select>
                </div>
            </td>
        </tr>
         <tr>
            <td><?php echo __('Signature');?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <?php
                        $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                        <input type="radio" name="signature" value="none" checked="checked"> <?php echo __('None');?>&nbsp;&nbsp;
                    <?php
                    if($thisstaff->getSignature()) { ?>
                        <input type="radio" name="signature" value="mine"
                        <?php echo ($info['signature']=='mine')?'checked="checked"':''; ?>> <?php echo __('My signature');?>
                    <?php
                    } ?>
                    <input type="radio" name="signature" value="dept"
                    <?php echo ($info['signature']=='dept')?'checked="checked"':''; ?>> <?php echo sprintf(__('Department Signature (%s)'), __('if set')); ?>
                </div>
            </td>
         </tr>
        <?php
        } //end canPostReply
        ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Note');?></strong>
                <font class="error">&nbsp;<?php echo $errors['note']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext ifhtml draft draft-delete"
                    placeholder="<?php echo __('Optional internal note (recommended on assignment)'); ?>"
                    data-draft-namespace="ticket.staff.note" name="note"
                    cols="21" rows="6" style="width:80%;"
                    ><?php echo $info['note']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center; padding-top: 20px;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo _P('action-button', 'Open');?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick="javascript:
        $('.richtext').each(function() {
            var redactor = $(this).data('redactor');
            if (redactor && redactor.opts.draftDelete)
                redactor.deleteDraft();
        });
        window.location.href='tickets.php';
    ">
</p>
</form>
<script type="text/javascript">

    $(document).ready(function () {

        $(".deatils-ticket tr:eq(2) td:eq(1) select").addClass("select-subjects");

        $("select[name=deptId]").change(function (event) {
            $("select[name=sla_duedate] option").remove();
            $("select[name=sla_duedate]").append("<option value='0'> — Padrão do Sistema — </option>");
            getSubjects($(this).val());
        });

        if($("select[name=deptId]").val() != 0){
            getSubjects($("select[name=deptId]").val());
        }

        $(".select-subjects").change(function (event) {
            getSla($(this).val());
        });

        if($(".select-subjects").val() != 0){
            getSla($(".select-subjects").val());
        }

    });

$(function() {
    $('input#user-email').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#uid').val(obj.id);
            $('#user-name').val(obj.name);
            $('#user-email').val(obj.email);
        },
        property: "/bin/true"
    });

   <?php
    // Popup user lookup on the initial page load (not post) if we don't have a
    // user selected
    if (!$_POST && !$user) {?>
//    setTimeout(function() {
//      $.userLookup('ajax.php/users/lookup/form', function (user) {
//        window.location.href = window.location.href+'&uid='+user.id;
//      });
//    }, 100);
    <?php
    } ?>
});

$('.open-modal').click(function () {
    $('.modal').css('display', 'block');
});


function getSubjects(id) {
    $.ajax({
        type: "POST",
        data: "id="+id,
        url: "ajax.php/tickets/subjects",
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
        url: "ajax.php/tickets/sla",
        dataType: 'json',
        success: function(data) {
            if(data.success === true){
                var sla = data.sla;
                console.log(sla);
                $("select[name=sla_duedate] option").remove();
                sla.forEach(function (index, value) {
                    $("select[name=sla_duedate]").append("<option value='"+index.id+"'>"+index.name+" ("+index.period+" Horas)</option>");
                    $('input[name=slaId]').val(index.id);
                });
            }else{

            }
        }
    });
}

</script>
<style>

    .user-info{
        display: none;
    }

    .typeahead{
        z-index: 99999 !important;
    }

    input[type=submit], input[type=button]{
        color: #fff !important;
    }

    td.required{
        width: 14%;
    }

    .action-button{
        display: inline-block;
        padding: 3px 6px;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border-radius: 3px;
        color: #fff !important;
        background-color: #337ab7;
        border-color: #2e6da4;
    }

    table.table-ticket tr td{
        padding: 10px;
    }

    table.table-ticket tr td input[type=text], table.table-ticket tr td select{
        margin-bottom: 0px;
    }

    .navbar{
        z-index: 2 !important;
    }

    .redactor_box{
        z-index: 1 !important;
    }

    #duedate{
        width: 90%;
        height: 30px;
        margin-right: 5px;
        float: left !important;
        margin-bottom: 10px;
        margin-top: 0px !important;
        border:solid 1px #ccc;
        border-radius: 3px;
    }

    @media screen and (max-width: 450px){

        .typeahead{
            width: 86% !important;
            z-index: 99999 !important;
            padding: 0 !important;
        }

        .typeahead li{
            width: 100% !important;
            padding: 0 !important;
        }

        .typeahead li a{
            overflow: hidden;
            text-overflow: ellipsis !important;
        }

        table.table-ticket{
            display: table;
            border: 0 !important;
        }

        table.table-ticket tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.table-ticket tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.table-ticket tr td i, table.table-ticket tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.table-ticket tr td input[type=radio], table.table-ticket tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.table-ticket tr td input, table.table-ticket tr td select{
            margin-top: 10px !important;
        }

        table.table-ticket tr td input[type=text], table.table-ticket tr td select{
            margin: 0 auto !important;
        }

        table.table-ticket tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        input[type=text], select{
            width: 100% !important;
            margin-top: 10px;
        }

        input[type=submit], input[type=reset], input[type=button]{
            width: 100%;
            margin-bottom: 10px;
            color: #fff !important;
        }

        .modal-content{
            height: 620px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        #duedate{
            width: 100%;
            margin-right: 5px;
            float: left !important;
            margin-bottom: 10px;
            margin-top: 0px !important;
        }

        #save select, #duedate{
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }

        .ui-datepicker-trigger{
            display: none;
        }

    }

</style>

<script>

    $("select").each(function(index, select){
        if(!$(select).hasClass("form-control")){
            $(select).addClass("form-control").css("width", "400px");
        }
    });

    $("table.table-ticket tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

    $("table.table-ticket tbody.deatils-ticket tr td").each(function (index, value) {
        var td     = $(this);
        var tdPrev = $(td).prev();
        var div    = $(td).find("div");
        var error  = $(td).find("font.error:first");
        if(div.length == 4){
            var divPrimary = div[0];
            if(error.length > 0){
                var text = $(error).text().replace(/\s/g, '');
                if(text == "*") {
                    $(divPrimary).append("<span class='error'>" + text + "</span>");
                }
                error.remove();
                var errorTwo  = $(td).find("font.error");
                var textTow = $(errorTwo).text();

                if(textTow != null || textTow != ""){
                    $(divPrimary).append("<font class='error' style='display: block'>" + textTow + "</font>");
                }
                errorTwo.remove();
            }
        }else{
            if(error.length > 0){
                var text = $(error).text().replace(/\s/g, '');
                if(text == "*") {
                    $(tdPrev).append("<span class='error'>" + text + "</span>");
                }
                error.remove();
                var errorTwo  = $(td).find("font.error");
                var textTow = $(errorTwo).text();

                if(textTow != null || textTow != ""){
                    $(tdPrev).append("<font class='error' style='display: block'>" + textTow + "</font>");
                }
                errorTwo.remove();
            }
        }
    });

</script>