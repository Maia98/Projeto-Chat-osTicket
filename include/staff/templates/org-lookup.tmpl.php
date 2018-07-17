<?php

if (!$info['title'])
    $info['title'] = __('Organization Lookup');

$msg_info = __('Search existing organizations or add a new one.');
if ($info['search'] === false)
    $msg_info = __('Complete the form below to add a new organization.');

?>
<div id="the-lookup-form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <!--<h4 class="modal-title" id="myModalLabel"><?php echo $info['title']; ?></h4>-->
        <h3 class="modal-title"><?php echo $info['title']; ?></h3>
        <!--<b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
    </div>

    <div class="modal-body">
        <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; <?php echo $msg_info; ?></p></div>
        <?php
        if ($info['search'] !== false) { ?>
        <div style="margin-bottom:10px;">
            <input type="text" class="search-input" style="width:100%;"
            placeholder="Search by name" id="org-search" autocorrect="off" autocomplete="off"/>
        </div>
        <?php
        }

        if ($info['error']) {
            echo sprintf('<p id="msg_error">%s</p>', $info['error']);
        } elseif ($info['warning']) {
            echo sprintf('<p id="msg_warning">%s</p>', $info['warning']);
        } elseif ($info['msg']) {
            echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
        } ?>
        <div id="selected-org-info" style="display:<?php echo $org ? 'block' :'none'; ?>;margin:5px;">
        <form method="post" class="org" action="<?php echo $info['action'] ?: '#orgs/lookup'; ?>">
            <input type="hidden" id="org-id" name="orgid" value="<?php echo $org ? $org->getId() : 0; ?>"/>
            <i class="icon-group icon-4x pull-left icon-border"></i>
            <a class="btn-xs btn-primary pull-right" style="overflow:inherit"
                id="unselect-org"  href="#"><i class="icon-remove"></i>
                <?php echo __('Add New Organization'); ?></a>
            <div><strong id="org-name"><?php echo $org ?  Format::htmlchars($org->getName()) : ''; ?></strong></div>
        <?php if ($org) { ?>
            <table class="table-org" style="margin-top: 1em;">
        <?php foreach ($org->getDynamicData() as $entry) { ?>
            <tr><td colspan="2" style="border-bottom: 1px dotted black"><strong><?php
                 echo $entry->getForm()->get('title'); ?></strong></td></tr>
        <?php foreach ($entry->getAnswers() as $a) { ?>
            <tr style="vertical-align:top"><td style="width:30%;border-bottom: 1px dotted #ccc"><?php echo Format::htmlchars($a->getField()->get('label'));
                 ?>:</td>
            <td style="border-bottom: 1px dotted #ccc"><?php echo $a->display(); ?></td>
            </tr>
        <?php }
            } ?>
           </table>
         <?php
          } ?>
        <div class="clear"></div>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" name="cancel" class="btn btn-danger" data-dismiss="modal"  value="<?php echo __('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input type="submit" class="btn btn-primary" value="<?php echo __('Continue'); ?>">
            </span>
         </p>
        </form>
        </div>
        <div id="new-org-form" style="display:<?php echo $org ? 'none' :'block'; ?>;">
        <form method="post" class="org" action="<?php echo $info['action'] ?: '#orgs/add'; ?>">
            <table width="100%" class="table-org">
            <?php
                if (!$form) $form = OrganizationForm::getInstance();
                $form->render(true, __('Create New Organization')); ?>
            </table>
            <hr>
            <p class="full-width">
                <span class="buttons pull-left">
                    <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                        <?php echo __('Cancel'); ?>
                    </button>
                </span>
                <span class="buttons pull-right">
                    <input class="btn btn-success" type="submit" value="<?php echo __('Add Organization'); ?>" style="margin-top: 0">
                </span>
             </p>
        </form>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    var last_req;
    $('#org-search').typeahead({
        source: function (typeahead, query) {
            if (last_req) last_req.abort();
            last_req = $.ajax({
                url: "ajax.php/orgs/search?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#the-lookup-form').load(
                '<?php echo $info['onselect'] ?: 'ajax.php/orgs/select'; ?>/'+encodeURIComponent(obj.id)
            );
        },
        property: "/bin/true"
    });

    $('a#unselect-org').click( function(e) {
        e.preventDefault();
        $('div#selected-org-info').hide();
        $('div#new-org-form').fadeIn({start: function(){ $('#org-search').focus(); }});
        return false;
     });

    $(document).on('click', 'form.org input.cancel', function (e) {
        e.preventDefault();
        $('div#new-org-form').hide();
        $('div#selected-org-info').fadeIn({start: function(){ $('#org-search').focus(); }});
        return false;
     });

    $("table.table-org tr").each(function(index, value){

        var tr      = $(value);
        var tdName  = tr.find("td:first");
        var tdInput = tr.find("td:last");
        var error   = tdInput.find("font");
        if(error.length > 0){
            tdName.append(error);
        }
    });

    $("input[type=submit]").click(function () {
        $(".modal-backdrop").removeAttr("class").attr("class", "modal-backdrop fade out").css("display", "none");
    });

});
</script>

<style>

    table.table-org span, table.table-org textarea{
        width: 100%;
    }

    .modal-content{
        max-height: 550px !important;
        overflow: scroll !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    @media screen and (max-width: 450px) {

        table.table-org{
            display: table;
            border: 0 !important;
        }

        table.table-org tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.table-org tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.table-org tr td i, table.table-org tr th i{
            margin-top: 5px !important;
            float: right;
        }

        table.table-org .col-xs-12{
            padding: 0 !important;
        }

        table.table-org tr td input[type=radio], table.table-org tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.table-org tr td input, table.table-org tr td select{
            margin-top: 10px !important;
        }

        table.table-org tr td input[type=text], table.table-org tr td select{
            margin: 0 auto !important;
        }

        table.table-org tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        table.table-org tr td div span{
            width: 100% !important;
        }

        table.table-org input, table.table-org textarea{
            width: 100%;
        }

    }

</style>

<script>

    $("table.table-org tr td").each(function (index, value) {
        var td     = $(this);
        var error  = $(td).find("font.error:first");
        if(error.length > 0){
            if($(error).text() == "*"){
                $(td).append("<span class='error'>*</span>");
                error.remove();
                var errorTwo  = $(td).find("font.error");
                var textTow = $(errorTwo).text();
                if(textTow != null || textTow != ""){
                    $(td).append("<font class='error' style='display: block'>" + textTow + "</font>");
                }
                errorTwo.remove();
            }
        }
    });

</script>
