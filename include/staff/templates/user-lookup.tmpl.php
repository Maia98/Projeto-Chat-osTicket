    <div id="the-lookup-form">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <!--<h4 class="modal-title" id="myModalLabel"><?php echo $info['title']; ?></h4>-->
            <h3 class="modal-title"><?php echo $info['title']; ?></h3>
            <!--<b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
        </div>

        <div class="modal-body">
            <?php
            if (!isset($info['lookup']) || $info['lookup'] !== false) { ?>
                <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; <?php echo __(
                            'Search existing users or add a new user.'
                        ); ?></p></div>
                <div style="margin-bottom:10px;">
                    <input type="text" class="search-input" style="width:100%;"
                           placeholder="<?php echo __('Search by email, phone or name'); ?>" id="user-search"
                           autocorrect="off" autocomplete="off"/>
                </div>

                <?php
            }

            if ($info['error']) {
                echo sprintf('<p id="msg_error">%s</p>', $info['error']);
            } elseif ($info['warn']) {
                echo sprintf('<p id="msg_warning">%s</p>', $info['warn']);
            } elseif ($info['msg']) {
                echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
            }
            ?>
            <div id="selected-user-info" style="display:<?php echo $user ? 'block' : 'none'; ?>;margin:5px;">
                <form method="post" class="user" action="<?php echo $info['action'] ? $info['action'] : '#users/lookup'; ?>">
                    <input type="hidden" id="user-id" name="id" value="<?php echo $user ? $user->getId() : 0; ?>"/>
                    <i class="icon-user icon-4x pull-left icon-border"></i>
                    <?php
                        if ($user) { ?>
                            <div>
                                <a class="btn btn-primary btn-sm pull-right button-size" style="overflow:inherit" id="unselect-user" href="#"><i class="icon-remove"></i> <?php echo __('Add New User'); ?></a>
                                <strong id="user-name"><?php echo Format::htmlchars($user->getName()->getOriginal()); ?></strong>
                            </div>
                            <div>&lt;<span id="user-email"><?php echo $user->getEmail(); ?></span>&gt;</div>
                            <?php
                                if ($org = $user->getOrganization()) {
                            ?>
                                    <div><span id="user-org"><?php echo $org->getName(); ?></span></div>
                            <?php
                                }
                            ?>
                            <table style="margin-top: 1em;" class="table-size">
                                <?php foreach ($user->getDynamicData() as $entry) { ?>
                                    <tr>
                                        <td colspan="2" style="border-bottom: 1px dotted black"><strong><?php echo $entry->getForm()->get('title'); ?></strong></td>
                                    </tr>
                                    <?php foreach ($entry->getAnswers() as $a) { ?>
                                        <tr style="vertical-align:top">
                                            <td class="td-size" style="width:30%;border-bottom: 1px dotted #ccc"><strong><?php echo Format::htmlchars($a->getField()->get('label'));
                                                ?>:</strong>
                                            </td>
                                            <td style="border-bottom: 1px dotted #ccc"><?php echo $a->display(); ?></td>
                                        </tr>
                                    <?php }
                                }
                                ?>
                            </table>
                    <?php } ?>
                    <div class="clear"></div>
                    <hr>
                    <p class="full-width">
                        <span class="buttons pull-left">
                            <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                                <?php echo __('Cancel'); ?>
                            </button>
                        </span>
                        <span class="buttons pull-right">
                            <button type="submit" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                                <?php echo __('Continue'); ?>
                            </button>
                        </span>
                    </p>
                    <br>
                </form>
            </div>
            <div id="new-user-form" style="display:<?php echo $user ? 'none' : 'block'; ?>;">
                <form method="post" class="user" action="<?php echo $info['action'] ?: '#users/lookup/form'; ?>">
                    <table width="100%" class="">
                        <?php
                            if (!$form) $form = UserForm::getInstance();
                                $form->render(true, __('Create New User'));
                            ?>
                    </table>
                    <hr>
                    <p class="full-width">
                        <span class="buttons pull-left">
                            <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                                <?php echo __('Cancel'); ?>
                            </button>
                        </span>
                        <span class="buttons pull-right">
                            <input class="btn btn-success" type="submit" value="<?php echo __('Add User'); ?>" style="margin-top: 0 !important;">
                        </span>
                        <br />
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="clear"></div>

<script type="text/javascript">
    $(function () {
        var last_req;
        $('#user-search').typeahead({
            source: function (typeahead, query) {
                if (last_req) last_req.abort();
                last_req = $.ajax({
                    url: "ajax.php/users<?php
                        echo $info['lookup'] ? "/{$info['lookup']}" : '' ?>?q=" + query,
                    dataType: 'json',
                    success: function (data) {
                        typeahead.process(data);
                    }
                });
            },
            onselect: function (obj) {
                $('#the-lookup-form').load(
                    '<?php echo $info['onselect'] ? $info['onselect'] : "ajax.php/users/select/"; ?>' + encodeURIComponent(obj.id)
                );
            },
            property: "/bin/true"
        });

        $('a#unselect-user').click(function (e) {
            e.preventDefault();
            $("#msg_error, #msg_notice, #msg_warning").fadeOut();
            $('div#selected-user-info').hide();
            $('div#new-user-form').fadeIn({
                start: function () {
                    $('#user-search').focus();
                }
            });
            return false;
        });

        $(document).on('click', 'form.user input.cancel', function (e) {
            e.preventDefault();
            $('div#new-user-form').hide();
            $('div#selected-user-info').fadeIn({
                start: function () {
                    $('#user-search').focus();
                }
            });
            return false;
        });
    });

    $("form select, form textarea").attr("class", "form-control");

    $('form').on('submit', function () {
        $(".modal-backdrop").removeAttr("class").attr("class", "modal-backdrop fade out").css("display", "none");
    });

    $("table tr").each(function(index, value){

        var tr      = $(value);
        var tdName  = tr.find("td:first");
        var tdInput = tr.find("td:last");
        var error   = tdInput.find("font");
        if(error.length > 0){
            tdName.append(error);
        }
    });


</script>

<style>

    .modal-content{
        max-height: 550px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }


    @media screen and (max-width: 450px) {

        .modal-body table {
            display: table;
            border: 0 !important;
        }

        .modal-body table tr {
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        .modal-body table tr td {
            width: 100%;
            display: table;
            margin-bottom: 0px !important;
            /*border-bottom: 1px dotted #ccc !important;*/
            /*padding: 10px !important;*/
        }

        .modal-body table tr td i, .modal-body table tr th i {
            margin-top: 5px !important;
            float: right;
        }

        .modal-body .col-xs-12 {
            padding: 0 !important;
        }

        .modal-body table tr td input[type=radio], .modal-body table tr td input[type=checkbox] {
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        .modal-body table tr td input, .modal-body table tr td select {
            margin-top: 10px !important;
        }

        .modal-body table tr td label {
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .modal-body select, .modal-body textarea, .modal-body input[type=text] {
            width: 100% !important;
            margin-left: 0px;
        }

        .modal-body .table-size {
            width: 100% !important;
        }

        .td-size {
            width: 100% !important;
        }

        .button-size {
            width: 100%;
            margin-bottom: 20px;
            margin-top: 20px;
        }

    }

</style>

    <script>

        $(".modal-body table tr td").each(function (index, value) {
            var td   = $(this);
            var font = $(value).find("font.error:first");

            if(font.length > 0){
                var text = font.text().replace(/\s/g, '');
                if(text == "*"){
                    font.remove();
                    $(td).append("<span class='error'>*</span>");
                }

                var fontTwo = $(value).find("font.error");
                var textTwo = fontTwo.text();
                if(textTwo != null || textTwo != ""){
                    fontTwo.remove();
                    $(td).append("<font class='error' style='display: block'>"+textTwo+"</font>");
                }
            }
        });

        $(".modal-body table tr td").each(function(index, value){
            var tr    = $(value);
            var span  = tr.find("span");
            var input = tr.find("input");
            var select = tr.find("select");
            if(input.length > 0){
                $(span).css("width" ,"100%");
            }

            if(select.length > 0){
                $(select).css("width", "100%");
            }
        });

    </script>
