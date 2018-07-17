<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2><?php echo __('Knowledge Base Settings and Options');?></h2>
<form action="settings.php?t=kb" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="kb" >
<table class="form_table settings_table" style="width: 100%;">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo __('Knowledge Base Settings');?></h4>
                <em><?php echo __("Disabling knowledge base disables clients' interface.");?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="top"><?php echo __('Knowledge Base Status'); ?>:
                <font class="error"><?php echo $errors['enable_kb']; ?></font>
                <font class="error"><?php echo $errors['restrict_kb']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="enable_kb" value="1" <?php echo $config['enable_kb']?'checked="checked"':''; ?>>
                    <?php echo __('Enable Knowledge Base'); ?>
                    <i class="help-tip icon-question-sign" href="#knowledge_base_status"></i>
                    <div style="clear: both"></div>
                    <input type="checkbox" name="restrict_kb" value="1" <?php echo $config['restrict_kb']?'checked="checked"':''; ?> >
                    <?php echo __('Require Client Login'); ?>
                    <i class="help-tip icon-question-sign" href="#restrict_kb"></i>
                </div>
            </td>
        </tr>
        <tr>
            <td class="td-label"><?php echo __('Canned Responses');?>:
                <i class="help-tip icon-question-sign" href="#canned_responses"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="checkbox" name="enable_premade" value="1" <?php echo $config['enable_premade']?'checked="checked"':''; ?>>
                    <?php echo __('Enable Canned Responses'); ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-top:20px; text-align: center;">
    <input class="btn btn-primary" type="submit" name="submit" value="<?php echo __('Save Changes'); ?>">
</p>
</form>
<style>

    table tr td{
        padding: 5px !important;
    }

    td.td-label{
        width: 20%;
    }

    @media screen and (max-width: 450px) {

        td.td-label{
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
            border-right: solid 1px #eaeaea !important;
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


        input[type=submit], input[type=reset], input[type=button] {
            width: 100% !important;
            margin-bottom: 10px;
            margin-top: 10px;
        }

    }
</style>

<script>

    $("form input[type=checkbox]").click(function () {
        $("input[type=submit]").css("color", "#fff");
    });

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });
</script>