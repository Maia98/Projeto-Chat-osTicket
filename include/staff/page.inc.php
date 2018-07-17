<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$pageTypes = array(
        'landing' => __('Landing page'),
        'offline' => __('Offline page'),
        'thank-you' => __('Thank you page'),
        'other' => __('Other'),
        );
$info = $qs = array();
if($page && $_REQUEST['a']!='add'){
    $title=__('Update Page');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$page->getHashtable();
    $info['body'] = Format::viewableImages($page->getBody());
    $info['notes'] = Format::viewableImages($info['notes']);
    $slug = Format::slugify($info['name']);
    $qs += array('id' => $page->getId());
}else {
    $title=__('Add New Page');
    $action='add';
    $submit_text=__('Add Page');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:0;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="pages.php?<?php echo Http::build_query($qs); ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Site Pages'); ?>
    <i class="help-tip icon-question-sign" href="#site_pages"></i>
    </h2>
 <table class="form_table" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr><td></td><td></td></tr> <!-- For fixed table layout -->
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><?php echo __('Page information'); ?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="required">
              <?php echo __('Name'); ?>:<span class="error">*</span>
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
                <?php echo __('Type'); ?>:<span class="error">*</span>
                <i class="help-tip icon-question-sign" href="#type"></i>
                <font class="error"><?php echo $errors['type']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <select name="type" class="form-control">
                        <option value="" selected="selected">&mdash; <?php
                        echo __('Select Page Type'); ?> &mdash;</option>
                        <?php
                        foreach($pageTypes as $k => $v)
                            echo sprintf('<option value="%s" %s>%s</option>',
                                    $k, (($info['type']==$k)?'selected="selected"':''), $v);
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <?php if ($info['name'] && $info['type'] == 'other') { ?>
        <tr>
            <td width="180" class="required">
                <?php echo __('Public URL'); ?>:
            </td>
            <td><a href="<?php echo sprintf("%s/pages/%s",
                    $ost->getConfig()->getBaseUrl(), urlencode($slug));
                ?>">pages/<?php echo $slug; ?></a>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td width="180" class="required">
                <?php echo __('Status'); ?>:<span class="error">*</span>
                <font class="error"><?php echo $errors['isactive']; ?></font>
            </td>
            <td>
                <div class="col-md-5 col-xs-12">
                    <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?php echo __('Active'); ?></strong>&nbsp;&nbsp;
                    <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><?php echo __('Disabled'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><?php echo __(
                '<b>Page body</b>: Ticket variables are only supported in thank-you pages.'
                ); ?><span class="error">*</span></em>
                <font class="error"><?php echo $errors['body']; ?></font>
            </th>
        </tr>
         <tr>
            <td colspan=2 style="padding-left:3px;">
                <textarea name="body" cols="21" rows="12" style="width:98%;" class="richtext draft"
                    data-draft-namespace="page" data-draft-object-id="<?php echo $info['id']; ?>"
                    ><?php echo $info['body']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Notes'); ?></strong>:
                <?php echo __("be liberal, they're internal"); ?></em>
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
<p class="alinhamentoCenter">
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>">
    <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel'); ?>" onclick='window.location.href="pages.php"'>
</p>
</form>
<style>

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    table tr td{
        padding:10px !important;
    }

    input{
        width:100%;
    }

    input[type=text], select{
        margin-bottom: 0px !important;
    }

    @media screen and (max-width: 450px) {

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
            width: 100%;
        }

        .division{
            clear: both !important;
        }

        .navbar{
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
            input.css("display", "block");
        }
    });

</script>