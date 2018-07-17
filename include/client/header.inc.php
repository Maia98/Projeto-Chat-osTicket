<?php
$title=($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
$signin_url = ROOT_PATH . "scp"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html <?php
if (($lang = Internationalization::getCurrentLanguage())
    && ($info = Internationalization::getLanguageInfo($lang))
    && (@$info['direction'] == 'rtl'))
    echo 'dir="rtl" class="rtl"';
?> style="height: 100%;">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css?19292ad" />
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/bootstrap.min.css?19292ad"/>
    <link href="<?php echo ASSETS_PATH; ?>css/justified-nav.css?19292ad" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/style.css?19292ad"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?19292ad" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>scp/css/typeahead.css?19292ad" />
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?19292ad" rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?19292ad"/>

    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.min.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.10.3.custom.min.js?19292ad"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.multiselect.min.js?19292ad"></script>
    <script src="<?php echo ROOT_PATH; ?>scp/js/bootstrap-typeahead.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-fonts.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/bootstrap.min.js?19292ad"></script>
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    ?>
</head>
<body>
<div class="container" style="margin-top: 50px; min-height: 435px">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php echo $ost->getConfig()->getTitle(); ?>" style="width: 200px" />
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <?php
                if($nav){
                    echo '<ul class="nav navbar-nav navbar-right" style="margin: 17px 0px 0px 0px;">';

                    if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)){
                        foreach($navs as $name =>$nav) {
                            if($name != "status"){
                                echo sprintf('<li class="%s"><a class="%s" href="%s">%s</a></li>%s', $nav['active']?'active':'', $name,(ROOT_PATH.$nav['href']), $nav['desc'],"\n");
                            }
                        }
                    }
                    echo '</ul>';
                }
                ?>
            </div><!--/.nav-collapse -->
    </nav>
          

                
