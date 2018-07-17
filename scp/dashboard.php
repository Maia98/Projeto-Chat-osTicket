<?php
/*********************************************************************
    dashboard.php

    Staff's Dashboard - basic stats...etc.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.dashboard" />',
    "$('#content').data('tipNamespace', 'dashboard.dashboard');");
require(STAFFINC_DIR.'header.inc.php');
?>

<script type="text/javascript" src="js/raphael-min.js?19292ad"></script>
<script type="text/javascript" src="js/g.raphael.js?19292ad"></script>
<script type="text/javascript" src="js/g.line-min.js?19292ad"></script>
<script type="text/javascript" src="js/g.dot-min.js?19292ad"></script>

<script type="text/javascript" src="js/dashboard.inc.js?19292ad"></script>

<link rel="stylesheet" type="text/css" href="css/dashboard.css?19292ad"/>

<h2><?php echo __('Ticket Activity');
?>&nbsp;<i class="help-tip icon-question-sign" href="#ticket_activity"></i></h2>
<p><?php echo __('Select the starting time and period for the system activity graph');?></p>
<form class="well form-inline" id="timeframe-form">
    <label>
        <?php
            echo __('Report timeframe'); ?>:
        <i class="help-tip icon-question-sign" href="#report_timeframe"></i>&nbsp;&nbsp;
        <input type="text" class="form-control dp input-medium search-query"
            name="start" placeholder="<?php echo __('Last month');?>" style="height: auto;"/>
    </label>
    <label>
        <?php echo __('period');?>:
        <select name="period" class="form-control">
            <option value="now" selected="selected"><?php echo __('Up to today');?></option>
            <option value="+7 days"><?php echo __('One Week');?></option>
            <option value="+14 days"><?php echo __('Two Weeks');?></option>
            <option value="+1 month"><?php echo __('One Month');?></option>
            <option value="+3 months"><?php echo __('One Quarter');?></option>
        </select>
    </label>
    <button class="btn btn-primary" type="submit"><?php echo __('Refresh');?></button>
</form>

<!-- Create a graph and fetch some data to create pretty dashboard -->
<div class="graphic-responsive">
    <div class="grafic-responsive-child" style="position:relative">
        <div id="line-chart-here" style="height:300px"></div>
        <div style="position:absolute;right:0;top:0" id="line-chart-legend"></div>
    </div>
</div>

<hr/>
<h2><?php echo __('Statistics'); ?>&nbsp;<i class="help-tip icon-question-sign" href="#statistics"></i></h2>
<p><?php echo __('Statistics of tickets organized by department, help topic, and agent.');?></p>
<ul class="nav nav-tabs" id="tabular-navigation"></ul>
<div class="table-responsive">
    <div id="table-here"></div>
</div>
<?php
include(STAFFINC_DIR.'footer.inc.php');
?>

<style>

    @media screen and (max-width: 450px) {

        button[type=submit]{
            width: 100%;
            margin-top: 10px;
        }

        .ui-datepicker-trigger{
            display: none;
        }

        label, input, select{
            width: 100% !important;
        }

        #line-chart-here{
            width: 1000px !important;
        }

        .grafic-responsive-child{
            width: 1000px !important;
        }

        .graphic-responsive{
            position: relative;
            width: 100%;
            overflow: scroll;
            overflow-x: auto;
            overflow-y: hidden;
        }

    }

</style>
