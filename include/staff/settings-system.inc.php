<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

$gmtime = Misc::gmtime();
?>
<h2><?php echo __('System Settings and Preferences');?> - <span class="ltr">osTicket (<?php echo $cfg->getVersion(); ?>)</span></h2>
<form action="settings.php?t=system" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="system" >
<table class="form_table settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2" style="width: 100%;">
                <h4><?php echo __('System Settings and Preferences'); ?></h4>
                <em><b><?php echo __('General Settings'); ?></b></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required"><?php echo __('Helpdesk Status');?>:&nbsp;
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#helpdesk_status"></i>
                <font class="error"><?php echo $config['isoffline']?'osTicket '.__('Offline'):''; ?></font>
            </td>
            <td>
                <div class="col-md-12 col-xs-12">
                    <input type="radio" name="isonline" value="1" <?php echo $config['isonline']?'checked="checked"':''; ?> />&nbsp;<b><?php echo __('Online'); ?></b>
                </div>
                <div class="col-md-12 col-xs-12">
                    <input type="radio" name="isonline" value="0" <?php echo !$config['isonline']?'checked="checked"':''; ?> />&nbsp;<b><?php echo __('Offline'); ?></b>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Helpdesk URL');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#helpdesk_url"></i>
                <font class="error"><?php echo $errors['helpdesk_url']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="40" name="helpdesk_url" value="<?php echo $config['helpdesk_url']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Helpdesk Name/Title');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#helpdesk_name_title"></i>
                <font class="error"><?php echo $errors['helpdesk_title']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" size="40" name="helpdesk_title" value="<?php echo $config['helpdesk_title']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Default Department');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_department"></i>
                <font class="error"><?php echo $errors['default_dept_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_dept_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Select Default Department');?> &mdash;</option>
                        <?php
                        $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' WHERE ispublic=1';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while (list($id, $name) = db_fetch_row($res)){
                                $selected = ($config['default_dept_id']==$id)?'selected="selected"':''; ?>
                                <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?> <?php echo __('Dept');?></option>
                            <?php
                            }
                        } ?>
                    </select>
                </div>
            </td>
        </tr>

        <tr>
            <td><?php echo __('Default Page Size');?>:<i class="help-tip icon-question-sign" href="#default_page_size"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="max_page_size" class="form-control">
                        <?php
                         $pagelimit=$config['max_page_size'];
                        for ($i = 5; $i <= 50; $i += 5) {
                            ?>
                            <option <?php echo $config['max_page_size']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Default Log Level');?>:
                    <i class="help-tip icon-question-sign" href="#default_log_level"></i>
                    <font class="error"><?php echo $errors['log_level']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="log_level" class="form-control">
                        <option value=0 <?php echo $config['log_level'] == 0 ? 'selected="selected"':''; ?>> <?php echo __('None (Disable Logger)');?></option>
                        <option value=3 <?php echo $config['log_level'] == 3 ? 'selected="selected"':''; ?>> <?php echo __('Debug');?></option>
                        <option value=2 <?php echo $config['log_level'] == 2 ? 'selected="selected"':''; ?>> <?php echo __('Aviso');?></option>
                        <option value=1 <?php echo $config['log_level'] == 1 ? 'selected="selected"':''; ?>> <?php echo __('Error');?></option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Purge Logs');?>:<i class="help-tip icon-question-sign" href="#purge_logs"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="log_graceperiod" class="form-control">
                        <option value=0 selected><?php echo __('Never Purge Logs');?></option>
                        <?php
                        for ($i = 1; $i <=12; $i++) {
                            ?>
                            <option <?php echo $config['log_graceperiod']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                                <?php echo sprintf(_N('After %d month', 'After %d months', $i), $i);?>
                            </option>
                            <?php
                        } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Default Name Formatting'); ?>:<i class="help-tip icon-question-sign" href="#default_name_formatting"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="name_format" class="form-control">
                        <?php foreach (PersonsName::allFormats() as $n=>$f) {
                            list($desc, $func) = $f;
                            $selected = ($config['name_format'] == $n) ? 'selected="selected"' : ''; ?>
                                            <option value="<?php echo $n; ?>" <?php echo $selected;
                                                ?>><?php echo __($desc); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('Date and Time Options'); ?></b>&nbsp;
                <i class="help-tip icon-question-sign" href="#date_time_options"></i>
                </em>
            </th>
        </tr>
        <tr>
            <td class="required"><?php echo __('Time Format');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['time_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control margin-bottom-0" name="time_format" value="<?php echo $config['time_format']; ?>">
                    <em><?php echo Format::date($config['time_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Date Format');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['date_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control margin-bottom-0" name="date_format" value="<?php echo $config['date_format']; ?>">
                    <em><?php echo Format::date($config['date_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Date and Time Format');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['datetime_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control margin-bottom-0" name="datetime_format" value="<?php echo $config['datetime_format']; ?>">
                    <em><?php echo Format::date($config['datetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Day, Date and Time Format');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['daydatetime_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control margin-bottom-0" name="daydatetime_format" value="<?php echo $config['daydatetime_format']; ?>">
                    <em><?php echo Format::date($config['daydatetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Default Time Zone');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['default_timezone_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_timezone_id" class="form-control">
                        <option value="">&mdash; <?php echo __('Select Default Time Zone');?> &mdash;</option>
                        <?php
                        $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                        if(($res=db_query($sql)) && db_num_rows($res)){
                            while(list($id, $offset, $tz)=db_fetch_row($res)){
                                $sel=($config['default_timezone_id']==$id)?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>GMT %s - %s</option>', $id, $sel, $offset, $tz);
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Daylight Saving');?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="enable_daylight_saving" <?php echo $config['enable_daylight_saving'] ? 'checked="checked"': ''; ?>><?php echo __('Observe daylight savings');?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align: center; padding-top: 20px;">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo __('Save Changes');?>">

</p>
</form>

<style>

    table tr td{
        padding: 5px !important;
    }

    select, textarea, input[type=text]{
        margin-top: 10px !important;
    }

    input.margin-bottom-0{
        margin-bottom: 0px;
    }

    td.required{
        width: 22% !important;
    }

    @media screen and (max-width: 450px) {

        td.required{
            width: 100% !important;
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
            margin-bottom: 10px !important;
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

        table tr td input, table tr td select{
            margin-top: 10px !important;
        }

        table tr td input[type=text], table tr td select{
            margin: 0 auto !important;
        }

        table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        input[type=submit], input[type=reset], input[type=button] {
            width: 100% !important;
            margin-bottom: 10px;
            color: #fff !important;
        }

    }

</style>

<script>

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