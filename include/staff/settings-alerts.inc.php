<h2><?php echo __('Alerts and Notices'); ?>
<i class="help-tip icon-question-sign" href="#page_title"></i></h2>
<form action="settings.php?t=alerts" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="alerts" >
<table class="form_table settings_table" style="width: 100%;">
	<thead>
		<tr>
			<th>
				<h4><?php echo __('Alerts and Notices sent to agents on ticket "events"'); ?></h4>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>
				<em><b><?php echo __('New Ticket Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#ticket_alert"></i>
				</em>
			</th>
		</tr>
		<tr>
			<td>
				<em><b><?php echo __('Status'); ?>:</b></em> &nbsp;
				<input type="radio" name="ticket_alert_active"  value="1"
				<?php echo $config['ticket_alert_active']?'checked':''; ?>
				/> <?php echo __('Enable'); ?>
				<input type="radio" name="ticket_alert_active"  value="0"   <?php echo !$config['ticket_alert_active']?'checked':''; ?> />
				 <?php echo __('Disable'); ?>
                &nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['ticket_alert_active']; ?></font></em>
                <div style="clear: both"></div>
                <input type="checkbox" name="ticket_alert_admin" <?php echo $config['ticket_alert_admin']?'checked':''; ?>>
                <?php echo __('Admin Email'); ?> <em>(<?php echo $cfg->getAdminEmail(); ?>)</em>
                <div style="clear: both"></div>
                <input type="checkbox" name="ticket_alert_dept_manager" <?php echo $config['ticket_alert_dept_manager']?'checked':''; ?>>
                <?php echo __('Department Manager'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="ticket_alert_dept_members" <?php echo $config['ticket_alert_dept_members']?'checked':''; ?>>
                <?php echo __('Department Members'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="ticket_alert_acct_manager" <?php echo $config['ticket_alert_acct_manager']?'checked':''; ?>>
                <?php echo __('Organization Account Manager'); ?>
			 </td>
		</tr>
		<tr>
			<th>
				<em><b><?php echo __('New Message Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#message_alert"></i>
				</em>
			</th>
		</tr>
        <tr>
			<td>
				<em><b><?php echo __('Status'); ?>:</b></em> &nbsp;
			  		<input type="radio" name="message_alert_active"  value="1"
				  <?php echo $config['message_alert_active']?'checked':''; ?>
				  /> <?php echo __('Enable'); ?>
				  &nbsp;&nbsp;
				  <input type="radio" name="message_alert_active"  value="0"   <?php echo !$config['message_alert_active']?'checked':''; ?> />
				  <?php echo __('Disable'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="message_alert_laststaff" <?php echo $config['message_alert_laststaff']?'checked':''; ?>>
                <?php echo __('Last Respondent'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="message_alert_laststaff" <?php echo $config['message_alert_laststaff']?'checked':''; ?>>
                <?php echo __('Last Respondent'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="message_alert_assigned" <?php
                echo $config['message_alert_assigned']?'checked':''; ?>>
                <?php echo __('Assigned Agent / Team'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="message_alert_dept_manager" <?php
                echo $config['message_alert_dept_manager']?'checked':''; ?>>
                <?php echo __('Department Manager'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="message_alert_acct_manager" <?php echo $config['message_alert_acct_manager']?'checked':''; ?>>
                <?php echo __('Organization Account Manager'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<em><b><?php echo __('New Internal Activity Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#internal_note_alert"></i>
				</em>
			</th>
		</tr>
		<tr>
			<td>
				<em><b><?php echo __('Status'); ?>:</b></em> &nbsp;
			  <input type="radio" name="note_alert_active"  value="1"   <?php echo $config['note_alert_active']?'checked':''; ?> />
				<?php echo __('Enable'); ?>
			  &nbsp;&nbsp;
			  <input type="radio" name="note_alert_active"  value="0"   <?php echo !$config['note_alert_active']?'checked':''; ?> />
				<?php echo __('Disable'); ?>
			  &nbsp;&nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['note_alert_active']; ?></font>
                <div style="clear: both"></div>
                <input type="checkbox" name="note_alert_laststaff" <?php echo
                $config['note_alert_laststaff']?'checked':''; ?>> <?php echo __('Last Respondent'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="note_alert_assigned" <?php echo $config['note_alert_assigned']?'checked':''; ?>>
                <?php echo __('Assigned Agent / Team'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="note_alert_dept_manager" <?php echo $config['note_alert_dept_manager']?'checked':''; ?>>
                <?php echo __('Department Manager'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<em><b><?php echo __('Ticket Assignment Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#assignment_alert"></i>
				</em></th></tr>
		<tr>
			<td>
				<em><b><?php echo __('Status'); ?>: </b></em> &nbsp;
			  	<input name="assigned_alert_active" value="1" type="radio"
				<?php echo $config['assigned_alert_active']?'checked="checked"':''; ?>> <?php echo __('Enable'); ?>
			  	&nbsp;&nbsp;
			  	<input name="assigned_alert_active" value="0" type="radio"
				<?php echo !$config['assigned_alert_active']?'checked="checked"':''; ?>> <?php echo __('Disable'); ?>
			   	&nbsp;&nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['assigned_alert_active']; ?></font>
                <div style="clear: both"></div>
                <input type="checkbox" name="assigned_alert_staff" <?php echo
                $config['assigned_alert_staff']?'checked':''; ?>> <?php echo __('Assigned Agent / Team'); ?>
                <div style="clear: both"></div>
                <input type="checkbox"name="assigned_alert_team_lead" <?php
                echo $config['assigned_alert_team_lead']?'checked':''; ?>> <?php echo __('Team Lead'); ?>
                <div style="clear: both"></div>
                <input type="checkbox"name="assigned_alert_team_members" <?php echo $config['assigned_alert_team_members']?'checked':''; ?>>
                <?php echo __('Team Members'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<em><b><?php echo __('Ticket Transfer Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#transfer_alert"></i>
				</em></th></tr>
		<tr>
			<td>
				<em><b><?php echo __('Status'); ?>:</b></em> &nbsp;
					<input type="radio" name="transfer_alert_active"  value="1"   <?php echo $config['transfer_alert_active']?'checked':''; ?> />
					<?php echo __('Enable'); ?>
					<input type="radio" name="transfer_alert_active"  value="0"   <?php echo !$config['transfer_alert_active']?'checked':''; ?> />
					<?php echo __('Disable'); ?>
			  		&nbsp;&nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['alert_alert_active']; ?></font>
                <div style="clear: both"></div>
                <input type="checkbox" name="transfer_alert_assigned" <?php echo $config['transfer_alert_assigned']?'checked':''; ?>>
                <?php echo __('Assigned Agent / Team'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="transfer_alert_dept_manager" <?php echo $config['transfer_alert_dept_manager']?'checked':''; ?>>
                <?php echo __('Department Manager'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="transfer_alert_dept_members" <?php echo $config['transfer_alert_dept_members']?'checked':''; ?>>
                <?php echo __('Department Members'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<em><b><?php echo __('Overdue Ticket Alert'); ?></b>:
					<i class="help-tip icon-question-sign" href="#overdue_alert"></i>
				</em></th></tr>
		<tr>
			<td>
			<em><b><?php echo __('Status'); ?>:</b></em> &nbsp;
			  <input type="radio" name="overdue_alert_active"  value="1"
				<?php echo $config['overdue_alert_active']?'checked':''; ?> /> <?php echo __('Enable'); ?>
			  <input type="radio" name="overdue_alert_active"  value="0"
				<?php echo !$config['overdue_alert_active']?'checked':''; ?> /> <?php echo __('Disable'); ?>
			  &nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['overdue_alert_active']; ?></font>
                <div style="clear: both"></div>
                <input type="checkbox" name="overdue_alert_assigned" <?php
                echo $config['overdue_alert_assigned']?'checked':''; ?>> <?php echo __('Assigned Agent / Team'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="overdue_alert_dept_manager" <?php
                echo $config['overdue_alert_dept_manager']?'checked':''; ?>> <?php echo __('Department Manager'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="overdue_alert_dept_members" <?php
                echo $config['overdue_alert_dept_members']?'checked':''; ?>> <?php echo __('Department Members'); ?>
			</td>
		</tr>
		<tr><th>
			<em><b><?php echo __('System Alerts'); ?></b>: <i class="help-tip icon-question-sign" href="#system_alerts"></i></em></th></tr>
		<tr>
			<td>
			  <input type="checkbox" class="margin-top-10" name="send_sys_errors" checked="checked" disabled="disabled">
				<?php echo __('System Errors'); ?>
			  <em><?php echo __('(enabled by default)'); ?></em>
                <div style="clear: both"></div>
                <input type="checkbox" name="send_sql_errors" <?php echo $config['send_sql_errors']?'checked':''; ?>>
                <?php echo __('SQL errors'); ?>
                <div style="clear: both"></div>
                <input type="checkbox" name="send_login_errors" <?php echo $config['send_login_errors']?'checked':''; ?>>
                <?php echo __('Excessive failed login attempts'); ?>
			</td>
		</tr>
	</tbody>
</table>
<p style="text-align:center; padding-top: 20px;">
	<input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes'); ?>" style="color: #fff;">
</p>
</form>

<style>

    table tr td{
        padding: 5px !important;
    }

    @media screen and (max-width: 450px) {

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

        h2 i{
            margin-top: 15px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table tr td input[type=checkbox]{
            margin-top: -10px;
            margin-right: 5px !important;
        }

        table tr td input[type=radio]{
            margin-top: 5px !important;
            margin-right: 5px !important;
            margin-bottom: 5px !important;
        }

        .margin-top-10{
            margin-top: 5px !important;
        }

        input[type=submit], input[type=reset], input[type=button] {
            width: 100% !important;
            margin-bottom: 10px;
            margin-top: 10px;
            color: #fff !important;
        }

    }
</style>

<script>

    $("body").on("click", "input[type=checkbox]", function () {
        $("input[type=submit]").css("color", "#fff");
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


</script>