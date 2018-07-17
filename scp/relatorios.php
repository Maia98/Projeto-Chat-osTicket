<?php
/*********************************************************************
    kb.php

    Knowlegebase

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');

$nav->setTabActive('relatorios');

if ($thisstaff->isAdmin()) {


require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.'filtro-relatorios.inc.php');
require_once(STAFFINC_DIR.'footer.inc.php');

}
else
    header('Location: index.php');


?>
