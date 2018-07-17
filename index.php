<?php
/*********************************************************************
index.php

Helpdesk landing page. Please customize it to fit your needs.

Peter Rotich <peter@osticket.com>
Copyright (c)  2006-2013 osTicket
http://www.osticket.com

Released under the GNU General Public License WITHOUT ANY WARRANTY.
See LICENSE.TXT for details.

vim: expandtab sw=4 ts=4 sts=4:
 **********************************************************************/

require('client.inc.php');
$section = 'home';
require(CLIENTINC_DIR.'header.inc.php');
?>

<?php
if($cfg && ($page = $cfg->getLandingPage()))
{
    echo '<div class="jumbotron">';
    echo $page->getBodyWithImages();
    echo '</div>';
}else{ ?>
    <div class="jumbotron">
        <h1><?php echo  '<h1>'. __('Welcome to the Support Center') . '</h1>'; ?> </h1>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-5 thumbnail" style="text-align:center; padding: 20px;">
        <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="font-size:55px"></span>
        <h3><?php echo __('Check Ticket Status');?></h3>
        <div><?php echo __('We provide archives and history of all your current and past support requests complete with responses.');?></div>

        <br />
        <a class="btn btn-primary btn-lg" href="tickets.php" class="blue button"><?php echo __('Check Ticket Status');?></a>

    </div>
    <div class="col-md-5 col-md-offset-2 thumbnail" style="text-align:center; padding: 20px;">
        <span class="glyphicon glyphicon-question-sign" aria-hidden="true" style="font-size:55px"></span>
        <h3><?php echo __('Frequently Asked Questions (FAQs)');?></h3>
        <div>Para qual quer problema temos nossas perguntas frequentes para lhe auxiliar em qual quer dúvida.</div>
        <br />
        <?php
        if($cfg && $cfg->isKnowledgebaseEnabled()){
            ?>
            <a class="btn btn-success btn-lg" href="kb/index.php"><?php echo __('Frequently Asked Questions (FAQs)');?></a>
            <?php
        } ?>
    </div>
</div>

   
    
<script>

    if($(window).width() > 450){
        $(".jumbotron").css({
            "padding" : "10px 0 0 0"
        });
    }

</script>


   


<?php require(CLIENTINC_DIR.'footer.inc.php'); ?>
<link rel="stylesheet" type="text/css" href="/osTicketPrener/css/btnstyle.css">
