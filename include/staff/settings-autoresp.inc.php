<h2><?php echo __('Autoresponder Settings'); ?></h2>

<form action="settings.php?t=autoresp" method="post" id="save">

<?php csrf_token(); ?>

<input type="hidden" name="t" value="autoresp" >

<table class="form_table settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Autoresponder Setting'); ?></h4>
                <em><?php echo __('Global setting - can be disabled at department or email level.'); ?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="td-label"><?php echo __('New Ticket'); ?>:
                <i class="help-tip icon-question-sign" href="#new_ticket"></i>
            </td>
            <td>
                <input type="checkbox" name="ticket_autoresponder" <?php
                    echo $config['ticket_autoresponder'] ? 'checked="checked"' : ''; ?>/>
                <?php echo __('Ticket Owner'); ?>&nbsp;
            </td>
        </tr>
        <tr>
            <td><?php echo __('New Ticket by Agent'); ?>:<i class="help-tip icon-question-sign" href="#new_ticket_by_staff"></i></td>
            <td>
                <input type="checkbox" name="ticket_notice_active" <?php
                echo $config['ticket_notice_active'] ? 'checked="checked"' : ''; ?>/>
                <?php echo __('Ticket Owner'); ?>&nbsp;
            </td>
        </tr>
        <tr>
            <td width="160" rowspan="2"><?php echo __('New Message'); ?>:</td>
            <td style="width:250px;">
                <input type="checkbox" name="message_autoresponder" <?php
                echo $config['message_autoresponder'] ? 'checked="checked"' : ''; ?>/>
                <?php echo __('Submitter: Send receipt confirmation'); ?>&nbsp;
                <i class="help-tip icon-question-sign icon-allign" href="#new_message_for_submitter"></i>
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="message_autoresponder_collabs" <?php
                echo $config['message_autoresponder_collabs'] ? 'checked="checked"' : ''; ?>/>
                <?php echo __('Participants: Send new activity notice'); ?>
                <i class="help-tip icon-question-sign" href="#new_message_for_participants"></i>
                </div>
            </td>
        </tr>
        <tr>
            <td width="160"><?php echo __('Overlimit Notice'); ?>:<i class="help-tip icon-question-sign" href="#overlimit_notice"></i></td>
            <td>
                <input type="checkbox" name="overlimit_notice_active" <?php
                echo $config['overlimit_notice_active'] ? 'checked="checked"' : ''; ?>/>
                <?php echo __('Ticket Submitter'); ?>&nbsp;
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-top:20px; text-align: center;">
    <input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes'); ?>">
</p>
</form>

<style>

    table tr td{
        padding: 5px !important;
    }

    td.td-label{
        width: 20%;
    }

    @media screen and (max-width: 450px) {

        td.td-label{
            width: 100%;
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

        .icon-allign{
            margin-right: -57px;
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
            margin-top: 10px;
        }

    }
</style>

<script>

    $("form input[type=checkbox]").click(function () {
        $("input[type=submit]").css("color", "#fff");
    });

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });
</script>