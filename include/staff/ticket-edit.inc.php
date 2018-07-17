<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canEditTickets() || !$ticket) die('Access Denied');

$info=Format::htmlchars(($errors && $_POST)?$_POST:$ticket->getUpdateInfo());
if ($_POST)
    $info['duedate'] = Format::date($cfg->getDateFormat(),
       strtotime($info['duedate']));
?>
<form action="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="a" value="edit">
 <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
 <h2><?php echo sprintf(__('Update Ticket #%s'),$ticket->getNumber());?></h2>

 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('User Information'); ?></strong>: <?php echo __('Currently selected user'); ?></em>
            </th>
        </tr>
    <?php
    if(!$info['user_id'] || !($user = User::lookup($info['user_id'])))
        $user = $ticket->getUser();
    ?>
    <tr><td><?php echo __('User'); ?>:</td><td>
        <div id="client-info">
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $ticket->getOwnerId(); ?>/edit',
                        function (user) {
                            $('#client-name').text(user.name);
                            $('#client-email').text(user.email);
                        });
                return false;
                "><i class="icon-user"></i>
            <span id="client-name"><?php echo Format::htmlchars($user->getName()); ?></span>
            &lt;<span id="client-email"><?php echo $user->getEmail(); ?></span>&gt;
            </a>
            <a class="btn btn-primary btn-sm open-modal" style="overflow:inherit;" href="#"
                onclick="javascript:
                    $.userLookup('ajax.php/tickets/<?php echo $ticket->getId(); ?>/change-user',
                            function(user) {
                                $('input#user_id').val(user.id);
                                $('#client-name').text(user.name);
                                $('#client-email').text('<'+user.email+'>');
                    });
                    return false;
                "><i class="icon-edit"></i> <?php echo __('Change'); ?></a>
            <input type="hidden" name="user_id" id="user_id"
                value="<?php echo $info['user_id']; ?>" />
        </div>
        </td></tr>
    <tbody>
        <tr>
            <th colspan="2">
            <em><strong><?php echo __('Ticket Information'); ?></strong>: <?php echo __("Due date overrides SLA's grace period."); ?></em>
            </th>
        </tr>
        <tr>
            <td>
                <?php echo __('SLA Plan');?>:
                <font class="error"><?php echo $errors['slaId']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="slaId" class="form-control">
                        <option value="0" selected="selected" >&mdash; <?php echo __('None');?> &mdash;</option>
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
            </td>
        </tr>
        <tr>
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
    </tbody>
</table>

<table class="form_table dynamic-forms form_table_one" width="940" border="0" cellspacing="0" cellpadding="2">
    <?php if ($forms)
        foreach ($forms as $form) {
            $form->render(true, false, array('mode'=>'edit','width'=>160,'entry'=>$form));
        } ?>
</table>
    <br/>
<table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Note');?></strong>: <?php echo __('Reason for editing the ticket (required)');?> <font class="error">&nbsp;<?php echo $errors['note'];?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <div class="col-md-5 col-xs-12">
                    <textarea class="form-control" class="richtext no-bar" name="note" cols="21"
                              rows="6" ><?php echo $info['note'];
                        ?></textarea>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<br>
    <div style="clear: both"></div>
<p style="text-align: center;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo __('Save');?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="tickets.php?id=<?php echo $ticket->getId(); ?>"'>
</p>
</form>
<div style="display:none;" class="dialog draggable" id="user-lookup">
    <div class="body"></div>
</div>
<script type="text/javascript">

    $('html, body').animate({
        scrollTop: $('body').offset().top
    }, 1);

    $('table.dynamic-forms').sortable({
      items: 'tbody',
      handle: 'th',
      helper: function(e, ui) {
        ui.children().each(function() {
          $(this).children().each(function() {
            $(this).width($(this).width());
          });
        });
        ui=ui.clone().css({'background-color':'white', 'opacity':0.8});
        return ui;
      }
    });

    $('.open-modal').click(function () {
        $('.modal').css('display', 'block');
    })

</script>

<style>

    .typeahead{
        z-index: 99999 !important;
    }

    input[type=text]{
        width: 100% !important;
        float: left;
    }

    .dynamic-forms select{
        width: 35%;
    }

    #duedate{
        width: 90% !important;
        margin-right: 5px;
        float: left !important;
        margin-bottom: 10px;
        margin-top: 0px !important;
    }

    #time{
        display: block !important;
        clear: both;
        margin-top: 10px !important;
    }

    textarea{
        width: 100% !important;
    }

    input[type=submit], input[type=button], input[type=reset]{
        width: auto !important;
        float: none !important;
        color: #fff !important;
    }

    #save select, #duedate{
        display: block;
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

    select{
        width: 35%;
    }

    table.form_table tr td {
        padding: 10px;
    }

    table.form_table tr td input[type=text], table.form_table tr td select, table.form_table tr td textarea{
        margin-bottom: 0px !important;
    }

    @media screen and (max-width: 450px) {

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

        table.form_table{
            display: table;
            border: 0 !important;
        }

        table.form_table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.form_table tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.form_table tr td i, table.form_table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.form_table tr td input[type=radio], table.form_table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.form_table tr td input, table.form_table tr td select{
            margin-top: 10px !important;
        }

        table.form_table tr td input[type=text], table.form_table tr td select{
            margin: 0 auto !important;
        }

        table.form_table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        input[type=submit], input[type=button], input[type=reset]{
            width: 90% !important;
            margin-bottom: 10px !important;
            margin-right: 10px !important;
        }

        .ui-datepicker-trigger{
            display: none;
        }

        input, select, textarea {
            width: 100% !important;
            margin-top: 10px;
            margin-left: 10px;
            display: block;
            clear: both;
        }

        textarea{
            float: left;
            width: 94% !important;
        }

        .dialog{
            width: 90% !important;
            margin-top: 20px;
        }

        #duedate{
            width: 100% !important;
            margin-left: 0 !important;
        }

        .modal-content{
            height: 620px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        /*.form_table_one{*/
            /*margin-top: 50px !important;*/
        /*}*/

        /*table tr td{*/
            /*width: 100% !important;*/
            /*display: block;*/
        /*}*/

        /*table tr td div{*/
            /*padding: 0 !important;*/
        /*}*/

        /*table tr td span{*/
            /*margin-bottom: 10px !important;*/
        /*}*/

        /*table tr td em{*/
            /*margin-top: 30px !important;*/
        /*}*/

        /*table tr td input[type=radio]{*/
            /*width: 10% !important;*/
            /*margin-left: -6px !important;*/
            /*float: left;*/
        /*}*/

        /*table tr td em{*/
            /*text-align: justify;*/
            /*margin-bottom: 10px !important;*/
        /*}*/

        /*table th em{*/
            /*margin-top: 10px !important;*/
        /*}*/

        /*.flush-right span{*/
            /*display: block !important;*/
            /*width: 100% !important;*/
            /*height: 30px !important;*/
        /*}*/

        /*.flush-right a{*/
            /*width: 100% !important;*/
            /*margin-bottom: 10px !important;*/
        /*}*/

    }

</style>

<script>

    $("table.dynamic-forms tbody tr td").each(function (index, value) {
        var td     = $(this);
        var select = $(td).find("select");
        if(select.length > 0){
            $(td).prepend("<div class='col-md-5 col-xs-12'>");
            $(td).append("</div>");
        }
    });

    $("table.dynamic-forms tbody tr td").each(function (index, value) {
        var td     = $(this);
        var tdPrev = $(td).prev();
        var div    = $(td).find("div");
        var error  = $(td).find("font.error:first");
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
    });

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

    $("table tr td").each(function(index, td){
        if($(td).css("min-width") == "120px"){
            $(td).removeAttr("style").removeAttr("width").attr("width", "300");
        }
        $(td).find("select").css("width", "330px");
        var content = $(td).find(".col-md-5").text();
        console.log(content);
        if(content == ""){
            $(td).find(".col-md-5").remove();
        }
    });

</script>
