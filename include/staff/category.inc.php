<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canManageFAQ()) die('Access Denied');
$info=array();
$qs = array();
if($category && $_REQUEST['a']!='add'){
    $title=__('Update Category').': '.$category->getName();
    $action='update';
    $submit_text=__('Save Changes');
    $info=$category->getHashtable();
    $info['id']=$category->getId();
    $info['notes'] = Format::viewableImages($category->getNotes());
    $qs += array('id' => $category->getId());
}else {
    $title=__('Add New Category');
    $action='create';
    $submit_text=__('Add');
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<div class="col-md-12">
    <div class="row">
        <form action="categories.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
         <?php csrf_token(); ?>
         <input type="hidden" name="do" value="<?php echo $action; ?>">
         <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
         <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
         <h2><?php echo __('FAQ Category');?></h2>
         <table class="form_table category_form" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="2">
                        <h4><?php echo $title; ?></h4>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="2">
                        <em><?php echo __('Category information'); ?>
                        <i class="help-tip icon-question-sign" href="#category_information"></i></em>
                    </th>
                </tr>
                <tr>
                    <td width="180" class="required"><?php echo __('Category Type');?>:&nbsp;
                        <span class="error">*</span>
                        <font class="error"><?php echo $errors['ispublic']; ?></span></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><b><?php echo __('Public');?></b> <?php echo __('(publish)');?>
                            &nbsp;
                            <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><?php echo __('Private');?> <?php echo __('(internal)');?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                            <b><?php echo __('Category Name');?></b>:&nbsp;
                            <span class="faded"><?php echo __('Short descriptive name.');?>
                            </span>&nbsp;<span class="error">*</span>
                            <font class="error"><?php echo $errors['name']; ?></font>
                    </td>
                    <td>
                        <div class="col-md-5 col-xs-12">
                            <input type="text" name="name" value="<?php echo $info['name']; ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><?php echo __('Category Description');?></b>:&nbsp;
                        <span class="faded"><?php echo __('Summary of the category.');?></span>&nbsp;
                        <span class="error">*</span>
                        <font class="error"><?php echo $errors['description']; ?></font>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea class="richtext" name="description" cols="21" rows="12" style="width:98%;"><?php echo $info['description']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <em><?php echo __('Internal Notes');?>&nbsp;</em>
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea class="richtext" name="notes" cols="21" rows="8" style="width: 80%;">
                            <?php echo $info['notes']; ?>
                        </textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="responsive-buttons" style="text-align: center; padding-top: 20px;">
            <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
            <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="categories.php"'>
        </p>
        </form>
    </div>
</div>

<style>

    input[type=text]{
        width: 100%;
    }

    table.category_form tr td{
        padding: 10px !important;
    }

    @media screen and (max-width: 450px) {

        table.category_form{
            display: table;
            border: 0 !important;
        }

        table.category_form tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.category_form tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.category_form tr td i, table tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.category_form tr td input[type=radio], table tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.category_form tr td input[type=text], table tr td select{
            width: 100%;
            margin-top: 10px !important;
        }

        table.category_form tr td input[type=text], table tr td select{
            margin: 0 auto !important;
        }

        table.category_form tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }

        .responsive-buttons input{
            width: 100%;
            margin-top: 10px;
        }

        .navbar {
            z-index: 2 !important;
        }

        .redactor_box{
            height: 150px !important;
            overflow: hidden;
            clear: both;
            margin-bottom: 20px;
            z-index: 1 !important;
        }

    }

</style>

<script>
    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            var text = input.text().replace(/\s/g, '');
            if(text.length != 0){
                input.css("display", "block");
            }
        }
    });
</script>
