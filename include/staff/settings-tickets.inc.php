<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
if(!($maxfileuploads=ini_get('max_file_uploads')))
    $maxfileuploads=DEFAULT_MAX_FILE_UPLOADS;
?>
<h2><?php echo __('Ticket Settings and Options');?></h2>
<form action="settings.php?t=tickets" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="tickets" >
<table class="form_table settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Global Ticket Settings');?></h4>
                <em><?php echo __('System-wide default ticket settings and options.'); ?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="td-label">
                <b><?php echo __('Default Ticket Number Format'); ?>:</b><span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#number_format"></i>
                <font class="error"><?php echo $errors['number_format']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="number_format" value="<?php echo $config['number_format']; ?>"/>
                </div>
            </td>
        </tr>
        <tr><td class="td-label"><?php echo __('Default Ticket Number Sequence'); ?>:<i class="help-tip icon-question-sign" href="#sequence_id"></i></td>
<?php $selected = 'selected="selected"'; ?>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="sequence_id" class="form-control">
                    <option value="0" <?php if ($config['sequence_id'] == 0) echo $selected;
                    ?>>&mdash; <?php echo __('Random'); ?> &mdash;</option>
                    <?php foreach (Sequence::objects() as $s) { ?>
                                    <option value="<?php echo $s->id; ?>" <?php
                                        if ($config['sequence_id'] == $s->id) echo $selected;
                                        ?>><?php echo $s->name; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-md-5 col-xs-12">
                    <button style="margin-left: -15px; margin-top: 15px" class=" btn btn-default btn-xs " onclick="javascript:
                    $.dialog('ajax.php/sequence/manage', 205);
                    return false;"><i class="icon-gear"></i> <?php echo __('Manage'); ?></button>
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Default Status'); ?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_ticket_status"></i>
                <font class="error"><?php echo $errors['default_ticket_status_id'];?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_ticket_status_id" class="form-control">
                <?php
                $criteria = array('states' => array('open'));
                foreach (TicketStatusList::getStatuses($criteria) as $status) {
                    $name = $status->getName();
                    if (!($isenabled = $status->isEnabled()))
                        $name.=' '.__('(disabled)');

                    echo sprintf('<option value="%d" %s %s>%s</option>',
                            $status->getId(),
                            ($config['default_ticket_status_id'] ==
                             $status->getId() && $isenabled)
                             ? 'selected="selected"' : '',
                             $isenabled ? '' : 'disabled="disabled"',
                             $name
                            );
                }
                ?>
                </select>    
                </div>
            </td>
        </tr>
        <tr>
            <td class="required"><?php echo __('Default Priority');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_priority"></i>
                <font class="error"><?php echo $errors['default_priority_id'];?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_priority_id" class="form-control">
                        <?php
                        $priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
                        while (list($id,$tag) = db_fetch_row($priorities)){ ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_priority_id']==$id)?'selected':''; ?>><?php echo $tag; ?></option>
                        <?php
                        } ?>
                    </select>    
                </div>
             </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Default SLA');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#default_sla"></i>
                <font class="error"><?php echo $errors['default_sla_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_sla_id" class="form-control">
                    <option value="0">&mdash; <?php echo __('None');?> &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id => $name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id,
                                    ($config['default_sla_id'] && $id==$config['default_sla_id'])?'selected="selected"':'',
                                    $name);
                        }
                    }
                    ?>
                </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Default Help Topic'); ?>:
                <font class="error"><?php echo $errors['default_help_topic']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_help_topic" class="form-control">
                        <option value="0">&mdash; <?php echo __('None'); ?> &mdash;</option><?php
                        $topics = Topic::getHelpTopics(false, Topic::DISPLAY_DISABLED);
                        while (list($id,$topic) = each($topics)) { ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_help_topic']==$id)?'selected':''; ?>><?php echo $topic; ?></option>
                        <?php
                        } ?>
                    </select>    
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Máximo de Tickets Abertos');?> <?php echo __('per end user'); ?>:
                <i class="help-tip icon-question-sign" href="#maximum_open_tickets"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="max_open_tickets" size=4 value="<?php echo $config['max_open_tickets']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Agent Collision Avoidance Duration'); ?> (<?php echo __('minutes'); ?>):
                <span class="error"></span>
                <i class="help-tip icon-question-sign" href="#agent_collision_avoidance"></i>
                <font class="error"><?php echo $errors['autolock_minutes']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" class="form-control" name="autolock_minutes" size=4 value="<?php echo $config['autolock_minutes']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Human Verification');?>:<i class="help-tip icon-question-sign" href="#human_verification"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="enable_captcha" <?php echo $config['enable_captcha']?'checked="checked"':''; ?>>
                    <?php echo __('Enable CAPTCHA on new web tickets.');?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Claim on Response'); ?>:<i class="help-tip icon-question-sign" href="#claim_tickets"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="auto_claim_tickets" <?php echo $config['auto_claim_tickets']?'checked="checked"':''; ?>>
                    <?php echo __('Enable'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Assigned Tickets');?>:<i class="help-tip icon-question-sign" href="#assigned_tickets"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="show_assigned_tickets" <?php
                    echo !$config['show_assigned_tickets']?'checked="checked"':''; ?>>
                    <?php echo __('Exclude assigned tickets from open queue.'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Answered Tickets');?>:<i class="help-tip icon-question-sign" href="#answered_tickets"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="show_answered_tickets" <?php
                    echo !$config['show_answered_tickets']?'checked="checked"':''; ?>>
                    <?php echo __('Exclude answered tickets from open queue.'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Agent Identity Masking'); ?>:<i class="help-tip icon-question-sign" href="#staff_identity_masking"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="hide_staff_name" <?php echo $config['hide_staff_name']?'checked="checked"':''; ?>>
                    <?php echo __("Hide agent's name on responses."); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Enable HTML Ticket Thread'); ?>:<i class="help-tip icon-question-sign" href="#enable_html_ticket_thread"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="enable_html_thread" <?php
                    echo $config['enable_html_thread']?'checked="checked"':''; ?>>
                    <?php echo __('Enable rich text in ticket thread and autoresponse emails.'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Allow Client Updates'); ?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="allow_client_updates" <?php
                    echo $config['allow_client_updates']?'checked="checked"':''; ?>>
                    <?php echo __('Allow clients to update ticket details via the web portal'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b><?php echo __('Attachments');?></b>:  <?php echo __('Size and maximum uploads setting mainly apply to web tickets.');?></em>
            </th>
        </tr>
        <tr>
            <td><?php echo __('EndUser Attachment Settings');?>:<i class="help-tip icon-question-sign" href="#ticket_attachment_settings"></i></td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <?php
                    $tform = TicketForm::objects()->one()->getForm();
                    $f = $tform->getField('message');
                ?>
                <a class="btn btn-default btn-xs" style="overflow:inherit; margin-top: 10px; margin-bottom: 10px;"
                    href="#ajax.php/form/field-config/<?php
                        echo $f->get('id'); ?>"
                    onclick="javascript:
                        $.dialog($(this).attr('href').substr(1), [201]);
                        return false;">
                        <i class="icon-edit"></i> <?php echo __('Config'); ?>
                </a>
                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo __(
                // Maximum size for agent-uploaded files (via SCP)
                'Agent Maximum File Size');?>:
                <i class="help-tip icon-question-sign" href="#max_file_size"></i>
                <font class="error"><?php echo $errors['max_file_size']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="max_file_size" class="form-control">
                        <option value="262144">&mdash; <?php echo __('Small'); ?> &mdash;</option>
                        <?php $next = 512 << 10;
                        $max = strtoupper(ini_get('upload_max_filesize'));
                        $limit = (int) $max;
                        if (!$limit) $limit = 2 << 20; # 2M default value
                        elseif (strpos($max, 'K')) $limit <<= 10;
                        elseif (strpos($max, 'M')) $limit <<= 20;
                        elseif (strpos($max, 'G')) $limit <<= 30;
                        while ($next <= $limit) {
                            // Select the closest, larger value (in case the
                            // current value is between two)
                            $diff = $next - $config['max_file_size'];
                            $selected = ($diff >= 0 && $diff < $next / 2)
                                ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $next; ?>" <?php echo $selected;
                                 ?>><?php echo Format::file_size($next);
                                 ?></option><?php
                            $next *= 2;
                        }
                        // Add extra option if top-limit in php.ini doesn't fall
                        // at a power of two
                        if ($next < $limit * 2) {
                            $selected = ($limit == $config['max_file_size'])
                                ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $limit; ?>" <?php echo $selected;
                                 ?>><?php echo Format::file_size($limit);
                                 ?></option><?php
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <?php if (($bks = FileStorageBackend::allRegistered())
                && count($bks) > 1) { ?>
        <tr>
            <td><?php echo __('Store Attachments'); ?>:</td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="default_storage_bk"><?php
                        foreach ($bks as $char=>$class) {
                            $selected = $config['default_storage_bk'] == $char
                                ? 'selected="selected"' : '';
                            ?><option <?php echo $selected; ?> value="<?php echo $char; ?>"
                            ><?php echo $class::$desc; ?></option><?php
                        } ?>
                </div>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<p style="text-align: center; padding-top: 20px;">
    <input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes');?>" >
</p>
</form>
<script type="text/javascript">
$(function() {
    var request = null,
      update_example = function() {
      request && request.abort();
      request = $.get('ajax.php/sequence/'
        + $('[name=sequence_id] :selected').val(),
        {'format': $('[name=number_format]').val()},
        function(data) { $('#format-example').text(data); }
      );
    };
    $('[name=sequence_id]').on('change', update_example);
    $('[name=number_format]').on('keyup', update_example);
});
</script>
<style>

    table tr td{
        padding: 5px !important;
    }

    .ui-multiselect-menu.ui-widget.ui-widget-content.ui-corner-all{
        z-index: 999999;
    }

    select, textarea, input{
        margin-top: 10px !important;
    }

    td.required{
        width: 32% !important;
    }

    @media screen and (max-width: 450px) {

        td.required{
            width: 100% !important;
        }

        a{
            margin-top: 0px !important;
            margin-bottom: 0px !important;
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
            border-right: solid 1px #eaeaea !important;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table tr td button{
            margin-top: 10px;
            margin-left: 0px !important;
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
        }

    }

</style>

<script>

    $("form select").change(function () {
        $("form input[type=submit]").css("color", "#fff");
    });


    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });


</script>