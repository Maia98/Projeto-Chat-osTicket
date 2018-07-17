<?php

$info=array();
if($plugin && $_REQUEST['a']!='add') {
    $config = $plugin->getConfig();
    if (!($page = $config->hasCustomConfig())) {
        if ($config)
            $form = $config->getForm();
        if ($form && $_POST)
            $form->isValid();
    }
    $title = __('Update Plugin');
    $action = 'update';
    $submit_text = __('Save Changes');
    $info = $plugin->ht;
}

$info = Format::htmlchars(($errors && $_POST) ? $_POST : $info);
?>

<form action="?<?php echo Http::build_query(array('id' => $_REQUEST['id'])); ?>" method="post" id="save">
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="<?php echo $action; ?>">
    <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
    <h2><?php echo __('Manage Plugin'); ?>
        <br/><small><?php echo $plugin->getName(); ?></small></h2>

    <h3><?php echo __('Configuration'); ?></h3>
<?php
if ($page)
    $config->renderCustomConfig();
elseif ($form) { ?>
    <table class="form_table" border="0" cellspacing="0" cellpadding="2">
    <tbody>
    <div class="col-md-5 col-xs-12">
        <?php $form->render(); ?>
    </div>
    </tbody></table>
<?php
}
else { ?>
    <tr><th><?php echo __('This plugin has no configurable settings'); ?><br>
        <em><?php echo __('Every plugin should be so easy to use.'); ?></em></th></tr>
<?php }
?>
<p class="alinhamentoCenter">
<?php if ($page || $form) { ?>
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
<?php } ?>
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel'); ?>" onclick='window.location.href="?"'>
</p>
</form>

<style>

    table tr td select{
        width: 32% !important;
    }

    table tr td{
        padding:10px !important;
    }

    @media screen and (max-width: 450px) {

        em{
            margin-top: 10px;
        }

        table tr td select{
            width: 100% !important;
        }

        table tr td span{
            width: 100%;
        }

        table{
            display: table;
            border: 0 !important;
        }

        table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table tr td i, table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table tr td input[type=radio], table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table tr td input, table tr td select{
            margin-top: 10px !important;
        }

        table tr td input[type=text], table tr td select{
            margin: 0 auto !important;
        }

        table tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }


        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], select{
            width: 98% !important;
            margin-top: 10px;
        }


    }
</style>

<script>

    $("table tr td").each(function (index, td) {
        $(td).find("br").remove();
        $(td).find("em").css("display", "block");
    });

    $("table tr")[0].remove();

    $("form select").attr("class", "form-control");

</script>