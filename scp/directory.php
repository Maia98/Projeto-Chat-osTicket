<?php
/*********************************************************************
    directory.php

    Staff directory

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
$page='directory.inc.php';
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.staff_directory" />',
    "$('#content').data('tipNamespace', 'dashboard.staff_directory');");
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>

<style>

    @media screen and (max-width: 450px) {

        button[type=submit]{
            margin-top: 10px;
        }

        button[type=button]{
            margin-top: 10px;
        }

        button[type=button]:focus i{
            color: #fff !important;
        }

    }

</style>

<script>

    if($(window).width() <= 450){
        var button_help_w    = $("button[type=button]").width() + 12;
        var group_buttons    = $(".input-group-btn").width();
        var new_width_button = group_buttons - button_help_w;
        $("button[type=submit]").width(0);
        $("button[type=submit]").width(new_width_button);
    }

</script>
