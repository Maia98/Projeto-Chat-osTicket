<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$info = $qs = array();
if($template && $_REQUEST['a']!='add'){
    $title=__('Update Template');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$template->getInfo();
    $info['tpl_id']=$template->getId();
    $qs += array('tpl_id' => $template->getId());
}else {
    $title=__('Add New Template');
    $action='add';
    $submit_text=__('Add Template');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:0;
    $info['lang_id'] = $cfg->getSystemLanguage();
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="templates.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="tpl_id" value="<?php echo $info['tpl_id']; ?>">
 <h2><?php echo __('Email Template');?></h2>
 <div class="table-responsive">
 <table class="form_table">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Template information');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
              <?php echo __('Name');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['name']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="text" name="name" value="<?php echo $info['name']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td class="required">
                <?php echo __('Status');?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#status"></i>
                <font class="error"><?php echo $errors['isactive']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <label><input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong>&nbsp;<?php echo __('Enabled'); ?></strong>
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>>&nbsp;<?php echo __('Disabled'); ?></label>
                </div>
            </td>
        </tr>
        <?php
        if($template){ ?>
        <tr>
            <td class="required">
                <?php echo __('Language');?>:
                <i class="help-tip icon-question-sign" href="#template_to_clone"></i>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <?php
                    echo Internationalization::getLanguageDescription($info['lang']);
                    ?>
                </div>
            </td>
        </tr>
        <?php
            $current_group = false;
            $impl = $template->getTemplates();
            $_tpls = $template::$all_names;
            $_groups = $template::$all_groups;
            uasort($_tpls, function($a,$b) {
                return strcmp($a['group'].$a['name'], $b['group'].$b['name']);
            });
         foreach($_tpls as $cn=>$info){
             if (!$info['name'])
                 continue;
             if (!$current_group || $current_group != $info['group']) {
                $current_group = $info['group']; ?>
        <tr>
            <th colspan="2">
            <em><strong><?php echo isset($_groups[$current_group])
            ? $_groups[$current_group] : $current_group; ?></strong>
            :: <?php echo __('Click on the title to edit.'); ?></em>
            </th>
        </tr>
<?php } # end if ($current_group)
            if (isset($impl[$cn])) {
                echo sprintf('<tr><td colspan="2">&nbsp;<strong><a href="templates.php?id=%d&a=manage">%s</a></strong>, <span class="faded">%s</span><br/>&nbsp;%s</td></tr>',
                $impl[$cn]->getId(), Format::htmlchars(__($info['name'])),
                sprintf(__('Updated %s'), Format::db_datetime($impl[$cn]->getLastUpdated())),
                Format::htmlchars(__($info['desc'])));
            } else {
                echo sprintf('<tr><td colspan=2>&nbsp;<strong><a
                    href="templates.php?tpl_id=%d&a=implement&code_name=%s"
                    >%s</a></strong><br/>&nbsp%s</td></tr>',
                    $template->getid(),$cn,format::htmlchars(__($info['name'])),
                    format::htmlchars(__($info['desc'])));
            }
         } # endfor
        } else { ?>
        <tr>
            <td class="required">
                <?php echo __('Template Set To Clone');?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['tpl_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                <select name="tpl_id" class="form-control" onchange="javascript:
                    if ($(this).val() == 0)
                        $('#language').show();
                    else
                        $('#language').hide();
">
                    <option value="0">&mdash; <?php echo __('Stock Templates'); ?> &mdash;</option>
                    <?php
                    $sql='SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_GRP_TABLE.' ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['tpl_id'] && $id==$info['tpl_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                </div>
            </td>
        </tr>
</tbody>
<tbody id="language">
        <tr>
            <td class="required">
                <?php echo __('Language'); ?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#language"></i>
                <font class="error"><?php echo $errors['lang_id']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <?php
                    $langs = Internationalization::availableLanguages(); ?>
                    <select name="lang_id" class="form-control">
                        <?php foreach($langs as $l) {
                            $selected = ($info['lang_id'] == $l['code']) ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                            ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
</tbody>
<tbody>
        <?php } ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes');?></strong>: <?php echo __(
                "be liberal, they're internal");?></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
</div>
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="templates.php"'>
</p>
</form>
<style>

    table tr td{
        padding:10px !important;
    }

    td.required{
        width: 21%;
    }

    input[type=text], select{
        width: 100%;
        margin-bottom: 0px;
    }

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    @media screen and (max-width: 450px) {

        table{
            display: table;
            border: 0 !important;
        }

        .table-responsive{
            border: none !important;
        }

        table tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table tr td{
            width:100% !important;
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
            margin-left: 5px !important;
        }

        table tr td input, table tr td select{
            margin-top: 10px !important;
        }

        table tr td input[type=text], table tr td select{
            margin: 0 auto !important;
            margin-left: 5px !important;
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
            width: 97% !important;
            margin-top: 10px;
        }

    }
</style>
<script>

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });

</script>