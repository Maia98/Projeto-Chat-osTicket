<?php
header("Content-Type: text/html; charset=UTF-8");
if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html <?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo 'dir="rtl" class="rtl"';
?>>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-1.8.3.min.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.10.3.custom.min.js?19292ad"></script>
    <script type="text/javascript" src="./js/scp.js?19292ad"></script>
    
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets/default/css/bootstrap.min.css?19292ad"/>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/bootstrap.min.js?19292ad"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.pjax.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery.multiselect.min.js?19292ad"></script>
    <script type="text/javascript" src="./js/tips.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?19292ad"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/await.js?19292ad"></script>

    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-fonts.js?19292ad"></script>
    <script type="text/javascript" src="./js/bootstrap-typeahead.js?19292ad"></script>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css?19292ad" media="all"/>
    <link rel="stylesheet" href="./css/scp.css?19292ad" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?19292ad" media="screen"/>
    <link rel="stylesheet" href="./css/typeahead.css?19292ad" media="screen"/>
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?19292ad"
         rel="stylesheet" media="screen" />
     <link rel="stylesheet" type="text/css" href="css/bootstrap.css?19292ad"/>
     <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="./css/dropdown.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css?19292ad"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?19292ad"/>
    <script type="text/javascript" src="./js/jquery.dropdown.js?19292ad"></script>

    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    ?>
</head>
<body>

<div class="container" style="margin-top: 7em;">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
              <div class="navbar-header">
                <button id="navbar-header" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php echo $ost->getConfig()->getTitle(); ?>" style="width: 200px" />
              </div>
              
              <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right" style="margin: 17px 0px 0px 0px;">
                        <li>
                            <a href="admin.php"><?php echo __('Admin Panel'); ?></a>
                        </li>
                        <li>
                            <a href="index.php"><?php echo __('Agent Panel'); ?></a>
                        </li>
                        <li>
                            <a href="profile.php"><?php echo __('My Preferences'); ?></a>
                        </li>
                        <li>
                            <a href="logout.php?auth=<?php echo $ost->getLinkToken(); ?>" ><?php echo __('Log Out'); ?></a>
                        </li>
                    </ul>
              </div><!--/.nav-collapse -->
                <?php echo sprintf(__('Welcome, %s.'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?>
            </div>
          </nav>

    <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
    <?php } else {
    header('X-PJAX-Version: ' . GIT_VERSION);
    if ($pjax = $ost->getExtraPjax()) { ?>
    <script type="text/javascript">
    <?php foreach (array_filter($pjax) as $s) echo $s.";"; ?>
    </script>
    <?php }
    foreach ($ost->getExtraHeaders() as $h) {
        if (strpos($h, '<script ') !== false)
            echo $h;
    } ?>
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title><?php
} # endif X_PJAX ?>

    <!--<ul id="nav" class="nav nav-tabs">-->

    <!--<ul id="sub_nav" class="nav nav-tabs"> -->

    <div id="content" class="container">
        <?php
        if($ost->getError())
            echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
        elseif($ost->getWarning())
            echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
        elseif($ost->getNotice())
            echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
        ?>
        <ul class="nav nav-tabs">
            <?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
        </ul>
        <ol class="breadcrumb" style="padding: 10px 12px; list-style: none; background-color: #f5f5f5; border-radius: 4px; margin: -19px 0 15px 0;">
            <?php  include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>
        </ol>    
        
        
        <br/>
        
        <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
        <?php } ?>

        <script>
            $("ul.dropdown-menu a").click(function () {
                var href      = $(this).attr("href");
                var relatorio = href.substr(0,href.indexOf("?"));
                if(relatorio == "relatorio.php"){
                    var url = window.location.protocol+"//"+window.location.host+"<?php echo ROOT_PATH; ?>"+"scp/"+href;
                    window.open(url, '_blank');
                    return false;
                }
            });

            $("#navbar").removeAttr("class").attr("class", "navbar-collapse collapse");

        </script>

        <script>

            if($(window).width() <= 450){
                $(".nav-tabs li.dropdown").each(function (index, value) {
                    var li = $(this);
                    var a  = li.find("a.dropdown-toggle");
                    if(a.length > 0){
                        var text = $(a).text();
                        if(text != "Tickets"){
                            $(li).css("display", "none");
                            if(text == "Painel de Controle"){
                                $(li).css("display", "block");
                                $(li).find(".dropdown-menu li").each(function (index, value) {
                                    if($(value).text() != "Meu Perfil"){
                                        $(value).css("display", "none");
                                    }
                                });
                            }
                        }
                    }
                });

                $(".navbar-nav li").each(function (index, value) {
                    var li = $(this);
                    var a  = li.find("a");
                     if(a.length > 0){
                        var text = $(a).text();
                        if(text != "Sair"){
                           $(li).css("display", "none");
                            if(text == "Minhas Preferências"){
                                $(li).css("display", "block");
                            }
                        }
                    }
                });

            }


            $(window).resize(function () {
                if($(this).width() <= 450){
                    $(".nav-tabs li.dropdown").each(function (index, value) {
                        var li = $(this);
                        var a  = li.find("a.dropdown-toggle");
                        if(a.length > 0){
                            var text = $(a).text();
                            if(text != "Tickets"){
                                $(li).css("display", "none");
                                if(text == "Painel de Controle"){
                                    $(li).css("display", "block");
                                    $(li).find(".dropdown-menu li").each(function (index, value) {
                                        if($(value).text() != "Meu Perfil"){
                                            $(value).css("display", "none");
                                        }
                                    });
                                }
                            }
                        }
                    });

                    $(".navbar-nav li").each(function (index, value) {
                        var li = $(this);
                        var a  = li.find("a");
                        if(a.length > 0){
                            var text = $(a).text();
                            if(text != "Sair"){
                                $(li).css("display", "none");
                                if(text == "Minhas Preferências"){
                                    $(li).css("display", "block");
                                }
                            }
                        }
                    });

                }else{
                    $(".nav-tabs li.dropdown").each(function (index, value) {
                        var li = $(this);
                        $(li).css("display", "block");
                    });

                    $(".nav-tabs li.dropdown .dropdown-menu li").each(function (index, value) {
                        $(value).css("display", "block");
                    });

                    $(".navbar-nav li").each(function (index, value) {
                        var li = $(this);
                        $(li).css("display", "block");
                    });
                }
            });

            $("#navbar-header").on('click', function () {
                var classe = $(this).attr('class');
                var classes = classe.split(" ");
                if(classes.length > 1){
                    $(this).removeAttr("style").css({"background-color" : "#ddd !important" });
                }else{
                    $(this).removeAttr("style").css({"background-color" : "rgba(0,0,0,0)" });
                }

            });

            window.onscroll = function (e) {
                if($("#navbar").hasClass('in')){
                    $("#navbar").removeClass('in').addClass('collapse');
                    $("#navbar-header").addClass('collapsed').removeAttr("style").css({"background-color" : "rgba(0,0,0,0)" });
                }
                if($(".dropdown").hasClass('open')){
                    $(".dropdown").removeClass('open');
                }
            }

            setTimeout(function () {
                $("html, body").scrollTop(0);
            }, 1);

        </script>
