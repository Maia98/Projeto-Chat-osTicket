</div>
</div>
<footer class="footer" style="background-color: #F8F8F8; overflow: hidden;">
    <div class="row">
        <div class="col-md-12" style="text-align: center; padding:0 !important; margin:0 !important;">
            <p>Copyright &copy; <?php echo date('Y'); ?> <?php echo (string) $ost->company ?: 'osTicket.com'; ?> - Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script>

    if($(window).width() > 450){
        var wHeight = $(window).height();
        var cHeight = $(".container").height() + $(".footer").height() + 90;
        if(cHeight < wHeight){
            $(".footer").css({
                "width"    : "100%",
                "position" : "absolute",
                "bottom"   : "0",
                "left"     : "0"
            });
        }
    }

</script>

<div id="overlay"></div>
<div id="loading">
    <h4><?php echo __('Please Wait!');?></h4>
    <p><?php echo __('Please wait... it will take a second!');?></p>
</div>
<?php
//if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
<!--    <script type="text/javascript" src="ajax.php/i18n/--><?php //echo $lang; ?><!--/js"></script>-->
<?php //} ?>
</body>
</html>
