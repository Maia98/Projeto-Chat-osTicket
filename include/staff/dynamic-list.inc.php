<?php

$info=array();
if ($list) {
    $title = __('Update custom list');
    $action = 'update';
    $submit_text = __('Save Changes');
    $info = $list->getInfo();
    $newcount=3;
} else {
    $title = __('Add New Custom List');
    $action = 'add';
    $submit_text = __('Add List');
    $newcount=4;
}

$info=Format::htmlchars(($errors && $_POST) ? array_merge($info,$_POST) : $info);

$sql =  'SELECT dept.dept_id,dept_name,email.email_id,email.email,email.name as email_name,ispublic,count(staff.staff_id) as users '.
    ',CONCAT_WS(" ",mgr.firstname,mgr.lastname) as manager,mgr.staff_id as manager_id,dept.created,dept.updated  FROM '.DEPT_TABLE.' dept '.
    ' LEFT JOIN '.STAFF_TABLE.' mgr ON dept.manager_id=mgr.staff_id '.
    ' LEFT JOIN '.EMAIL_TABLE.' email ON dept.email_id=email.email_id '.
    ' LEFT JOIN '.STAFF_TABLE.' staff ON dept.dept_id=staff.dept_id';

$sqlSla = 'SELECT * FROM '.SLA_TABLE.' WHERE isactive = 1';

$query         = "$sql GROUP BY dept.dept_id ORDER BY dept.dept_id DESC";
$res           = db_query($query);
$resSla        = db_query($sqlSla);
$departamentos = array();
$slas          = array();
while($row = db_fetch_array($res)){
    array_push($departamentos, $row);
}

while($row = db_fetch_array($resSla)){
    array_push($slas, $row);
}

?>

<?php while ($row = db_fetch_array($res)) { ?>
    <option value="<?php echo $row['dept_id']; ?>"><?php echo $row['dept_name']; ?></option>
<?php } $row = null;?>

<form action="" method="post" id="save">
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="<?php echo $action; ?>">
    <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
    <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
    <h2><?php echo __('Custom List'); ?>
        <?php echo $list ? $list->getName() : __('Add new list'); ?></h2>

    <ul class="tabs">
        <li>
            <a href="#definition" class="active">
                <i class="icon-plus"></i> <?php echo __('Definition'); ?>
                <div style="clear: both;"></div>
            </a>

        </li>
        <li>
            <a href="#items">
                <i class="icon-list"></i> <?php echo __('Items'); ?>
                <div style="clear: both;"></div>
            </a>
        </li>
        <li>
            <a href="#properties">
                <i class="icon-asterisk"></i> <?php echo __('Properties'); ?>
                <div style="clear: both;"></div>
            </a>
        </li>
    </ul>
    <div id="definition" class="tab_content">
        <table class="form_table table-pattern" border="0" cellspacing="0" cellpadding="2">
            <thead>
            <tr>
                <th colspan="2">
                    <h4><?php echo $title; ?></h4>
                    <em><?php echo __(
                            'Custom lists are used to provide drop-down lists for custom forms.'
                        ); ?>&nbsp;<i class="help-tip icon-question-sign" href="#custom_lists"></i></em>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="required"><?php echo __('Name'); ?>:<span class="error">*</span>
                    <font class="error"><?php if($errors['name']) echo __($errors['name']); ?></font>
                </td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <?php
                        if ($list && !$list->isEditable())
                            echo $list->getName();
                        else {
                            echo sprintf('<input type="text" name="name"
                            value="%s"/>',
                                $info['name']);
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Plural Name'); ?>:</td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <?php
                        if ($list && !$list->isEditable())
                            echo $list->getPluralName();
                        else
                            echo sprintf('<input type="text"
                                name="name_plural" value="%s"/>',
                                $info['name_plural']);
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Sort Order'); ?>:</td>
                <td>
                    <div class="col-md-5 col-xs-12">
                        <select name="sort_mode" class="form-control">
                            <?php
                            $sortModes = $list ? $list->getSortModes() : DynamicList::getSortModes();
                            foreach ($sortModes as $key=>$desc) { ?>
                                <option value="<?php echo $key; ?>" <?php
                                if ($key == $info['sort_mode']) echo 'selected="selected"';
                                ?>><?php echo $desc; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <th colspan="7">
                    <em><strong><?php echo __('Internal Notes'); ?>:</strong>
                        <?php echo __("be liberal, they're internal"); ?></em>
                </th>
            </tr>
            <tr>
                <td colspan="7"><textarea name="notes" class="richtext no-bar"
                                          rows="6" cols="80"><?php
                        echo $info['notes']; ?></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="properties" class="tab_content" style="display:none">
        <div class="table-responsive">
            <table class="form_table table-properties" border="0" cellspacing="0" cellpadding="2">
                <thead>
                <tr>
                    <th colspan="7">
                        <em><strong><?php echo __('Item Properties'); ?></strong>
                            <?php echo __('properties definable for each item'); ?></em>
                    </th>
                </tr>
                <tr>
                    <th nowrap></th>
                    <th nowrap><?php echo __('Label'); ?></th>
                    <th nowrap><?php echo __('Type'); ?></th>
                    <th nowrap><?php echo __('Variable'); ?></th>
                    <th nowrap><?php echo __('Delete'); ?></th>
                </tr>
                </thead>
                <tbody class="sortable-rows" data-sort="prop-sort-">
                <?php if ($list && $form=$list->getForm()) foreach ($form->getDynamicFields() as $f) {
                    $id = $f->get('id');
                    $deletable = !$f->isDeletable() ? 'disabled="disabled"' : '';
                    $force_name = $f->isNameForced() ? 'disabled="disabled"' : '';
                    $fi = $f->getImpl();
                    $ferrors = $f->errors(); ?>
                    <tr>
                        <td><i class="icon-sort"></i></td>
                        <td><input class="size-input" type="text" size="32" name="prop-label-<?php echo $id; ?>"
                                   value="<?php echo Format::htmlchars($f->get('label')); ?>"/>
                            <font class="error"><?php
                                if ($ferrors['label']) echo '<br/>'; echo $ferrors['label']; ?>
                        </td>
                        <td nowrap><select style="max-width:150px; float: left;" name="type-<?php echo $id; ?>" <?php
                            if (!$fi->isChangeable() || !$f->isChangeable()) echo 'disabled="disabled"'; ?>>
                                <?php foreach (FormField::allTypes() as $group=>$types) {
                                    ?><optgroup label="<?php echo Format::htmlchars(__($group)); ?>"><?php
                                    foreach ($types as $type=>$nfo) {
                                        if ($f->get('type') != $type
                                            && isset($nfo[2]) && !$nfo[2]) continue; ?>
                                        <option value="<?php echo $type; ?>" <?php
                                        if ($f->get('type') == $type) echo 'selected="selected"'; ?>>
                                            <?php echo __($nfo[0]); ?></option>
                                    <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                            <?php if ($f->isConfigurable()) { ?>
                                <a class="action-button field-config"
                                   style="overflow:inherit"
                                   href="#form/field-config/<?php
                                   echo $f->get('id'); ?>"><i
                                            class="icon-cog"></i> <?php echo __('Config'); ?></a> <?php } ?></td>
                        <td>
                            <input type="text" size="20" name="name-<?php echo $id; ?>"
                                   value="<?php echo Format::htmlchars($f->get('name'));
                                   ?>" <?php echo $force_name ?>/>
                            <font class="error"><?php
                                if ($ferrors['name']) echo '<br/>'; echo $ferrors['name'];
                                ?></font>
                        </td>
                        <td align="center">
                            <?php
                            if (!$f->isDeletable())
                                echo '<i class="icon-ban-circle"></i>';
                            else
                                echo sprintf('<input type="checkbox" name="delete-prop-%s">', $id);
                            ?>
                            <input type="hidden" name="prop-sort-<?php echo $id; ?>"
                                   value="<?php echo $f->get('sort'); ?>"/>
                        </td>
                    </tr>
                    <?php
                }
                for ($i=0; $i<$newcount; $i++) { ?>
                    <td><em>+</em>
                        <input type="hidden" name="prop-sort-new-<?php echo $i; ?>"
                               value="<?php echo $info["prop-sort-new-$i"]; ?>"/></td>
                    <td><input class="size-input" type="text" size="32" name="prop-label-new-<?php echo $i; ?>"
                               value="<?php echo $info["prop-label-new-$i"]; ?>"/></td>
                    <td><select class="size-input" style="max-width:150px" name="type-new-<?php echo $i; ?>">
                            <?php foreach (FormField::allTypes() as $group=>$types) {
                                ?><optgroup label="<?php echo Format::htmlchars(__($group)); ?>"><?php
                                foreach ($types as $type=>$nfo) {
                                    if (isset($nfo[2]) && !$nfo[2]) continue; ?>
                                    <option value="<?php echo $type; ?>"
                                        <?php if ($info["type-new-$i"] == $type) echo 'selected="selected"'; ?>>
                                        <?php echo __($nfo[0]); ?>
                                    </option>
                                <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select></td>
                    <td><input class="size-input" type="text" size="20" name="name-new-<?php echo $i; ?>"
                               value="<?php echo $info["name-new-$i"]; ?>"/>
                        <font class="error"><?php
                            if ($errors["new-$i"]['name']) echo '<br/>'; echo $errors["new-$i"]['name'];
                            ?></font>
                    <td></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="items" class="tab_content" style="display:none">
        <div class="table-responsive">
            <table class="form_table table-items" border="0" cellspacing="0" cellpadding="2">
                <thead>
                <?php if ($list) {
                    $page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
                    $count = $list->getNumItems();
                    $pageNav = new Pagenate($count, $page, PAGE_LIMIT);
                    $pageNav->setURL('list.php', array('id' => $list->getId()));
                    $showing=$pageNav->showing().' '.__('list items');
                    ?>
                <?php }
                else $showing = __('Add a few initial items to the list');
                ?>
                <tr>
                    <th colspan="5">
                        <em><?php echo $showing; ?></em>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <th><?php echo __('Value'); ?></th>
                    <?php
                    if (!$list || $list->hasAbbrev()) { ?>
                        <th><?php echo __(/* Short for 'abbreviation' */ 'Abbrev'); ?> <em style="display:inline">&mdash;
                                <?php echo __('abbreviations and such'); ?></em></th>
                        <?php
                    } ?>
                    <?php if($info['id'] == 4){ ?>
                        <th>Departamento</th>
                        <th>SLA</th>
                    <?php } ?>
                    <th><?php echo __('Disabled'); ?></th>
                    <th><?php echo __('Delete'); ?></th>
                </tr>
                </thead>

                <tbody <?php if ($info['sort_mode'] == 'SortCol') { ?>
                    class="sortable-rows" data-sort="sort-"<?php } ?>>
                <?php
                if ($list) {
                    $icon = ($info['sort_mode'] == 'SortCol')
                        ? '<i class="icon-sort"></i>&nbsp;' : '';
                    foreach ($list->getAllItems() as $i) {
                        $id = $i->getId(); ?>
                        <tr class="<?php if (!$i->isEnabled()) echo 'disabled'; ?>">
                            <td><?php echo $icon; ?>
                                <input type="hidden" name="sort-<?php echo $id; ?>"
                                       value="<?php echo $i->getSortOrder(); ?>"/></td>
                            <td nowrap>
                                <input type="text" style="max-width:150px; float: left"" name="value-<?php echo $id; ?>"
                                value="<?php echo $i->getValue(); ?>"/>
                                <?php if ($list->hasProperties()) { ?>
                                    <a class="action-button field-config prop"
                                       style="overflow:inherit"
                                       href="#list/<?php
                                       echo $list->getId(); ?>/item/<?php
                                       echo $id ?>/properties"
                                       id="item-<?php echo $id; ?>"
                                    ><?php
                                        echo sprintf('<i class="icon-edit" %s></i> ',
                                            $i->getConfiguration()
                                                ? '': 'font-weight:bold;"');
                                        echo __('Properties');
                                        ?></a>
                                    <?php
                                }

                                if ($errors["value-$id"])
                                    echo sprintf('<br><span class="error">%s</span>',
                                        $errors["value-$id"]);
                                ?>
                            </td>
                            <?php
                            if ($list->hasAbbrev()) { ?>
                                <td>
                                    <input class="size-input" type="text" size="30" name="abbrev-<?php echo $id; ?>" value="<?php echo $i->getAbbrev(); ?>"/>
                                </td>
                                <?php if($info['id'] == 4){ ?>
                                    <td>
                                        <select name="dept-<?php echo $id; ?>" class="form-data">
                                            <?php foreach ($departamentos as $dept) { ?>
                                                <?php
                                                if($i->getDept() == $dept['dept_id']){?>
                                                    <option value="<?php echo $dept['dept_id']; ?>" selected><?php echo $dept['dept_name']; ?></option>
                                                <?php }else{ ?>
                                                    <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                                                <?php } ?>
                                                ?>
                                            <?php }?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="sla-<?php echo $id; ?>" class="form-data">
                                            <?php foreach ($slas as $sla) { ?>
                                                <?php
                                                if($i->getSla() == $sla['id']){?>
                                                    <option value="<?php echo $sla['id']; ?>" selected><?php echo $sla['name']; ?></option>
                                                <?php }else{ ?>
                                                    <option value="<?php echo $sla['id']; ?>"><?php echo $sla['name']; ?></option>
                                                <?php } ?>
                                                ?>
                                            <?php }?>
                                        </select>
                                    </td>
                                <?php } ?>
                                <?php
                            } ?>
                            <td align="center">
                                <?php
                                if (!$i->isDisableable())
                                    echo '<i class="icon-ban-circle"></i>';
                                else
                                    echo sprintf('<input type="checkbox" name="disable-%s"
                                %s %s />',
                                        $id,
                                        !$i->isEnabled() ? ' checked="checked" ' : '',
                                        (!$i->isEnabled() && !$i->isEnableable()) ? ' disabled="disabled" ' : ''
                                    );
                                ?>
                            </td>
                            <td align="center">
                                <?php
                                if (!$i->isDeletable())
                                    echo '<i class="icon-ban-circle"></i>';
                                else
                                    echo sprintf('<input type="checkbox" name="delete-item-%s">', $id);

                                ?>
                            </td>
                        </tr>
                    <?php }
                }

                if (!$list || $list->allowAdd()) {
                    for ($f=0; $f<$newcount; $f++) { ?>
                        <tr>
                            <td><?php echo $icon; ?> <em>+</em>
                                <input type="hidden" name="sort-new-<?php echo $f; ?>"
                                       value="<?php echo $info["sort-new-$f"]; ?>"/></td>
                            <td><input class="size-input" type="text" size="40" name="value-new-<?php echo $f; ?>"/></td>
                            <?php
                            if (!$list || $list->hasAbbrev()) { ?>
                                <td><input class="size-input" type="text" size="30" name="abbrev-new-<?php echo $f; ?>"/></td>
                                <?php
                            } ?>
                            <td>
                                <select name="dept-new-<?php echo $f; ?>" class="form-data">
                                    <?php foreach ($departamentos as $dept) { ?>
                                        <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                                    <?php }?>

                                </select>
                            </td>
                            <td>
                                <select name="sla-new-<?php echo $f; ?>" class="form-data">
                                    <?php foreach ($slas as $sla) { ?>
                                        <option value="<?php echo $sla['id']; ?>"><?php echo $sla['name']; ?></option>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    }
                }?>
                </tbody>
            </table>
        </div>
    </div>
    <p class="alinhamentoCenter">
        <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $submit_text; ?>" >
        <input type="button" class="btn btn-primary" name="cancel" value="<?php echo __('Cancel'); ?>"
               onclick='window.location.href="?"'>
    </p>
</form>

<script type="text/javascript">
    $(function() {
        $('a.field-config').click( function(e) {
            e.preventDefault();
            var $id = $(this).attr('id');
            var url = 'ajax.php/'+$(this).attr('href').substr(1);
            $.dialog(url, [201], function (xhr) {
                $('a#'+$id+' i').removeAttr('style');
            });
            return false;
        });
    });
</script>
<style>

    input[type=submit], input[type=reset], input[type=button], button.button{
        color: #fff !important;
    }

    .table-pattern tr td{
        padding:10px !important;
    }

    td.required{
        width: 10%;
    }

    input[type=text], select{
        margin-bottom: 0px;
    }

    .action-button{
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
        color: #333;
        background-color: #fff;
        border-color: #ccc;
        margin-top: 5px !important;
        margin-left: 10px !important;
    }

    .action-button i{
        color: #333 !important;
    }

    input, select{
        width: 100%;
    }

    table.table-items tr td input[type=text]{
        margin-top: 10px;
        margin-bottom: 10px;
    }

    table.table-properties tr td input[type=text]{
        margin-top: 10px;
        margin-bottom: 10px;
        width: 98%;
    }

    a.prop{
        margin-top: 15px !important;
    }

    @media screen and (max-width: 450px) {

        .tabs{
            height: auto !important;
            padding: 0 !important;
        }

        .tabs li{
            display: block !important;
            height: 30px;
            background-color: rgb(251,251,251);
        }

        .tabs li a{
            display: block !important;
        }

        table.table-pattern{
            display: table;
            border: 0 !important;
        }

        table.table-pattern tr{
            width: 100% !important;
            display: table-row;
            border: 0 !important;
        }

        table.table-pattern tr td{
            width:100%;
            display: table;
            margin-bottom: 10px !important;
            border: 0 !important;
            padding: 10px !important;
        }

        table.table-pattern tr td i, table.table-pattern tr th i{
            margin-top: 5px !important;
            float: right;
        }

        .col-xs-12{
            padding: 0 !important;
        }

        table.table-pattern tr td input[type=radio], table.table-pattern tr td input[type=checkbox]{
            margin-top: -10px !important;
            margin-right: 5px !important;
        }

        table.table-pattern tr td input, table.table-pattern tr td select{
            margin-top: 10px !important;
        }

        table.table-pattern tr td input[type=text], table.table-pattern tr td select{
            margin: 0 auto !important;
        }

        table.table-pattern tr td label{
            width: auto !important;
            float: left;
            margin-right: 10px;
        }


        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
            color: #fff !important;
        }

        input[type=text], input[type=checkbox], select{
            margin-top: 10px;
            width: 100%;
        }

        .size-input{
            width: 97% !important;
        }

        .field-config{
            margin-left: 6px !important;
            width: 37% !important;
            margin-top: 15px !important;
        }

    }
</style>

<script>

    $("table tr td").each(function (index, value) {
        var td     = $(this);
        var button = $(this).find("a.action-button");
        if(button.length > 0){
//            console.log("dfdf");
            td.css("width", "260px");
        }

    });

    $("select").addClass("form-control");

    $("table tr td").each(function (index, value) {
        var input = $(value).find("font.error");
        if(input.length > 0){
            input.css("display", "block");
        }
    });

</script>