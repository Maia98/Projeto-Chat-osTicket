</div>
</div>
<style>

    #redactor_toolbar_0{
        z-index: 1 !important;
    }

</style>
<?php if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
    <div id="footer">
        Copyright &copy; 2006-<?php echo date('Y'); ?>&nbsp;<?php echo (string)$ost->company ?: 'osTicket.com'; ?>&nbsp;All
        Rights Reserved.
    </div>
    <?php
    if (is_object($thisstaff) && $thisstaff->isStaff()) { ?>
        <div>
            <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
            <img src="autocron.php" alt="" width="1" height="1" border="0"/>
            <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        </div>
        <?php
    } ?>
    </div>
    <div id="overlay"></div>
    <div id="loading">
        <i class="icon-spinner icon-spin icon-3x pull-left icon-light"></i>
        <h1><?php echo __('Loading ...'); ?></h1>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 20000 !important;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div id="popup-loading">
                    <h1 style="margin-bottom: 20px;"><i class="icon-spinner icon-spin icon-large"></i>
                        <?php echo __('Loading ...'); ?></h1>
                </div>
                <div id="popup-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <h1>Erro...</h1>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dialog draggable" style="display:none;width:650px;" id="popup">
        <div id="popup-loading">
            <h1 style="margin-bottom: 20px;"><i class="icon-spinner icon-spin icon-large"></i>
                <?php echo __('Loading ...'); ?></h1>
        </div>
        <div class="body"></div>
    </div>

    <div style="display:none;" class="dialog" id="alert">
        <h3><i class="icon-warning-sign"></i> <span id="title"></span></h3>
        <a class="close" href=""><i class="icon-remove-circle"></i></a>
        <hr/>
        <div id="body" style="min-height: 20px;"></div>
        <hr style="margin-top:3em"/>
        <p class="full-width">
        <span class="buttons pull-right">
            <input type="button" id="close-modal" value="<?php echo __('OK'); ?>" class="btn btn-primary">
        </span>
        </p>
        <div class="clear"></div>
    </div>

    <script type="text/javascript">
        if ($.support.pjax) {
            $(document).on('click', 'a', function (event) {
                if (!$(this).hasClass('no-pjax')
                    && !$(this).closest('.no-pjax').length
                    && $(this).attr('href')[0] != '#')
                    $.pjax.click(event, {container: $('#pjax-container'), timeout: 2000});
            })
        }

    </script>
    <?php
    if ($thisstaff && $thisstaff->getLanguage() != 'en_US') { ?>
        <script type="text/javascript" src="ajax.php/i18n/<?php
        echo $thisstaff->getLanguage(); ?>/js"></script>
    <?php } ?>
    </body>
    </html>
<?php } # endif X_PJAX ?>

<?php
    global $thisstaff;
    if(!$thisstaff->isAdmin()){
    require_once (STAFFINC_DIR.'chatbox-in.php');
    }
?>

