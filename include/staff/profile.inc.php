<?php
if(!defined('OSTSTAFFINC') || !$staff || !$thisstaff) die('Access Denied');

$info=$staff->getInfo();
$info['signature'] = Format::viewableImages($info['signature']);
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
$info['id']=$staff->getId();
?>
<form action="profile.php" method="post" id="save" autocomplete="off">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('My Account Profile');?></h2>
 <div class="col-md-12">
     <div class="row" style="padding: 0">
         <div class="panel panel-default">
          <div class="panel-heading" style="border: 0">
            <h3 class="panel-title"><?php echo __('Account Information');?> (<em><?php echo __('Contact information');?></em>)</h3>
          </div>
          <div class="panel-body" style="padding: 0">
            <table class="profile_table" style="width: 100%;">
        <tbody>
            <tr>
                <td class="required">
                    <?php echo __('Username');?>:&nbsp;<i class="help-tip icon-question-sign" href="#username"></i>
                </td>
                <td>
                    <div class="col-md-12 col-xs-12">
                        <p><?php echo $staff->getUserName(); ?></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="required">
                    <?php echo __('First Name');?>:<span class="error">*</span>
                    <font class="error"><?php echo $errors['firstname']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" name="firstname" class="form-control" value="<?php echo $info['firstname']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="required">
                    <?php echo __('Last Name');?>:<span class="error">*</span>
                    <font class="error"><?php echo $errors['lastname']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" class="form-control" name="lastname" value="<?php echo $info['lastname']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="required">
                    <?php echo __('Email Address');?>:<span class="error">*</span>
                    <font class="error"><?php echo $errors['email']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" class="form-control" name="email" value="<?php echo $info['email']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Phone Number');?>:&nbsp;
                    <span class="error"></span>
                    <font class="error"><?php echo $errors['phone']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" class="form-control" name="phone" value="<?php echo $info['phone']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span>Telefone Externo:</span>
                    <font class="error">&nbsp;<?php echo $errors['phone_ext']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" class="form-control" name="phone_ext" value="<?php echo $info['phone_ext']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Mobile Number');?>:&nbsp;
                    <span class="error"></span>
                    <font class="error"><?php echo $errors['mobile']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="text" class="form-control" name="mobile" value="<?php echo $info['mobile']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('Preferences');?></strong>: <?php echo __('Profile preferences and settings.');?></em>
                </th>
            </tr>
            <tr>
                <td width="180" class="required">
                    <?php echo __('Time Zone');?>:<span class="error">*</span>
                    <font class="error"><?php echo $errors['timezone_id']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="timezone_id" id="timezone_id" class="form-control">
                            <option value="0">&mdash; <?php echo __('Select Time Zone');?> &mdash;</option>
                            <?php
                            $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                            if(($res=db_query($sql)) && db_num_rows($res)){
                                while(list($id,$offset, $tz)=db_fetch_row($res)){
                                    $sel=($info['timezone_id']==$id)?'selected="selected"':'';
                                    echo sprintf('<option value="%d" %s>GMT %s - %s</option>',$id,$sel,$offset,$tz);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Preferred Language'); ?>:
                    <font class="error"><?php echo $errors['lang']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                    <?php
                        $langs = Internationalization::availableLanguages(); ?>
                        <select name="lang" class="form-control">
                            <option value="">&mdash; <?php echo __('Use Browser Preference'); ?> &mdash;</option>
                            <?php foreach($langs as $l) {
                                $selected = ($info['lang'] == $l['code']) ? 'selected="selected"' : ''; ?>
                                 <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                            ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Daylight Saving');?>:</td>
                <td style="padding-bottom: 6px;">
                    <div class="col-md-5 col-xs-12">
                        <input type="checkbox" name="daylight_saving" value="1" <?php echo $info['daylight_saving']?'checked="checked"':''; ?>>
                    <?php echo __('Observe daylight saving');?>
                    <div style="clear: both;"></div>
                    <em><?php echo __('Current Time');?>: <strong><?php echo Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$info['tz_offset'],$info['daylight_saving']); ?></strong></em>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Maximum Page size');?>:</td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="max_page_size" class="form-control">
                            <option value="0">&mdash; <?php echo __('system default');?> &mdash;</option>
                            <?php
                            $pagelimit=$info['max_page_size']?$info['max_page_size']:$cfg->getPageSize();
                            for ($i = 5; $i <= 50; $i += 5) {
                                $sel=($pagelimit==$i)?'selected="selected"':'';
                                 echo sprintf('<option value="%d" %s>'.__('show %s records').'</option>',$i,$sel,$i);
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-5 col-xs-12 info">
                        <em><?php echo __('per page.');?></em>
                    </div>

                </td>
            </tr>
            <tr>
                <td><?php echo __('Auto Refresh Rate');?>:</td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="auto_refresh_rate" class="form-control">
                          <option value="0">&mdash; <?php echo __('disable');?> &mdash;</option>
                          <?php
                          $y=1;
                           for($i=1; $i <=30; $i+=$y) {
                             $sel=($info['auto_refresh_rate']==$i)?'selected="selected"':'';
                             echo sprintf('<option value="%1$d" %2$s>'
                                .sprintf(
                                    _N('Every minute', 'Every %d minutes', $i), $i)
                                 .'</option>',$i,$sel);
                             if($i>9)
                                $y=2;
                           } ?>
                        </select>
                    </div>
                    <div class="col-md-5 col-xs-12 info">
                        <em><?php echo __('(Tickets page refresh rate in minutes.)');?></em>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Default Signature');?>:
                    &nbsp;<span class="error"></span>
                    <font class="error"><?php echo $errors['default_signature_type']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="default_signature_type" class="form-control">
                      <option value="none" selected="selected">&mdash; <?php echo __('None');?> &mdash;</option>
                      <?php
                       $options=array('mine'=>__('My Signature'),'dept'=>sprintf(__('Department Signature (%s)'),
                           __('if set' /* This is used in 'Department Signature (>if set<)' */)));
                      foreach($options as $k=>$v) {
                          echo sprintf('<option value="%s" %s>%s</option>',
                                    $k,($info['default_signature_type']==$k)?'selected="selected"':'',$v);
                      }
                      ?>
                    </select>
                    </div>
                    <div class="col-md-5 col-xs-12 info">
                        <em><?php echo __('(This can be selected when replying to a ticket)');?></em>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Default Paper Size');?>:
                    &nbsp;<span class="error"></span>
                    <font class="error"><?php echo $errors['default_paper_size']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="default_paper_size" class="form-control">
                      <option value="none" selected="selected">&mdash; <?php echo __('None');?> &mdash;</option>
                      <?php

                      foreach(Export::$paper_sizes as $v) {
                          echo sprintf('<option value="%s" %s>%s</option>',
                                    $v,($info['default_paper_size']==$v)?'selected="selected"':'',__($v));
                      }
                      ?>
                    </select>
                    </div>
                    <div class="col-md-5 col-xs-12 info">
                        <em><?php echo __('Paper size used when printing tickets to PDF');?></em>
                    </div>

                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0px;"><?php echo __('Show Assigned Tickets');?>:&nbsp;
                    <i class="help-tip icon-question-sign" href="#show_assigned_tickets"></i>
                    <font class="error"><?php echo $errors['passwd']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="show_assigned_tickets" <?php echo $info['show_assigned_tickets']?'checked="checked"':''; ?>>
                    <?php echo __('Mostrar tickets designados na fila de abertos.');?>
                </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('Password');?></strong>: <?php echo __('To reset your password, provide your current password and a new password below.');?></em>
                </th>
            </tr>
            <?php if (!isset($_SESSION['_staff']['reset-token'])) { ?>
            <tr>
                <td>
                    <?php echo __('Current Password');?>:
                    <font class="error"><?php echo $errors['cpasswd']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="password" class="form-control" name="cpasswd" value="<?php echo $info['cpasswd']; ?>">
                    </div>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td>
                    <?php echo __('New Password');?>:
                    &nbsp;<font class="error"><?php echo $errors['passwd1']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="password" class="form-control" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Confirm New Password');?>:
                    <font class="error"><?php echo $errors['passwd2']; ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <input type="password" class="form-control" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('Signature');?></strong>: <?php echo __('Optional signature used on outgoing emails.');?>
                        <i class="help-tip icon-question-sign" href="#signature"></i>
                        <font class="error"><?php echo $errors['signature']; ?></font>
                    </em>
                </th>
            </tr>
            <tr>
                <td colspan=2>
                    <div class="col-md-5 col-xs-12" style="padding: 0">
                        <textarea class="richtext no-bar" name="signature" cols="21"
                            rows="5" style="width: 100%;"><?php echo $info['signature']; ?></textarea>
                        <em><?php echo __('Signature is made available as a choice, on ticket reply.');?></em>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
          </div>
        </div>
     </div>
 </div>

<p style="text-align:center; margin-top: 30px">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo __('Save Changes');?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel Changes');?>" onclick='window.location.href="index.php"'>
</p>
</form>

<style>

   /*.panel-body{*/
        /*border: solid 1px red !important;*/
    /*}*/

    table tr{
        border-bottom: solid 1px #cccccc;
    }

    table tr:last-child{
        border: 0 !important;
    }

    table tr td{
        padding: 10px !important;
    }

    table tr td .info{
        margin-top: 15px !important;
    }

    input[type=submit], input[type=reset], input[type=button] {
        color: #fff !important;
    }

    p{
        margin-top: 10px;
    }

    table.profile_table tr td input[type=text], table.profile_table tr td select, table.profile_table tr td input[type=password]{
        margin-top: 10px !important;
    }

    @media screen and (max-width: 450px) {

        .panel-default{
            border: 0;
        }

        .division{
            clear: both;
            margin-bottom: 10px !important;
        }

        #pjax-container {
            width: 100% !important;
        }

        .desc-allign{
            margin-top: -5px;
        }

        input[type=text], input[type=password], select {
            width: 100% !important;
            margin-top: 5px;
        }

        input[type=submit], input[type=reset], input[type=button] {
            margin-bottom: 10px;
            width: 100%;
            color: #fff !important;
        }

        .navbar {
            z-index: 2 !important;
        }

        .redactor_box {
            z-index: 1 !important;
        }

        .modal-content {
            height: 620px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        table{
            display: table;
            border: 0 !important;
        }

        table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table tr td{
            width:100%;
            display: table;
            border: 0 !important;
            padding: 10px !important;
        }

        table tr td i, table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table tr td input[type=radio], table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table tr td input[type=text], table tr td select, table tr td input[type=password]{
            margin-top: 10px !important;
        }

        table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .breadcrumb>li+li:before{
            content: none !important;
        }

        /*.division_two{*/
            /*margin-bottom: 50px !important;*/
        /*}*/

        /*.tip_content .links a{*/
            /*display: none;*/
        /*}*/

        /*.margin-left-21 {*/
            /*margin-left: 21px;*/
            /*width: 99%;*/
        /*}*/

    }
</style>

<script>

//    if($(window).width() <= 450){
//        $("table tr td:first .span-user-name").remove();
//        $("table tr:first td:first").append("<span class='span-user-name'>"+$("table .td-user-name").text()+"</span>").css("margin-bottom", "10px");
//        $("table .td-user-name").css("display", "none");
//    }else{
//        $("table tr .span-user-name").remove();
//        $("table .td-user-name").css("display", "block");
//    }
//
//    $(window).resize(function () {
//        if($(window).width() <= 450){
//            $("table tr td:first .span-user-name").remove();
//            $("table tr:first td:first").append("<span class='span-user-name'>"+$("table .td-user-name").text()+"</span>").css("margin-bottom", "10px");
//            $("table .td-user-name").css("display", "none");
//        }else{
//            $("table tr:first .span-user-name").remove();
//            $("table .td-user-name").css("display", "block");
//        }
//    });
//
    if($(window).width() <= 450) {
        $("ol.breadcrumb li").each(function (index, value) {
            var li = $(this);
            var a = li.find("a");
            console.log(a);
            if (a.length > 0) {
                var text = $(a).text();
                if (text != "Meu Perfil") {
                    $(li).css("display", "none");
                }
            }
        });
    }

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });

</script>