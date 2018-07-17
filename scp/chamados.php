<?php require('staff.inc.php');

$nav->setTabActive('chamados');

if ($thisstaff->isAdmin()) {


require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.'chamados.inc.php');
require_once(STAFFINC_DIR.'footer.inc.php');

}
else
    header('Location: index.php');


?>