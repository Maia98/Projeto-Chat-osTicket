<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qs = array();
$sql='SELECT cat.category_id, cat.name, cat.ispublic, cat.updated, count(faq.faq_id) as faqs '.
     ' FROM '.FAQ_CATEGORY_TABLE.' cat '.
     ' LEFT JOIN '.FAQ_TABLE.' faq ON (faq.category_id=cat.category_id) ';
$sql.=' WHERE 1';
$sortOptions=array('name'=>'cat.name','type'=>'cat.ispublic','faqs'=>'faqs','updated'=>'cat.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'cat.name';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order?$order:'ASC';

if($order_column && strpos($order_column,',')){
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=db_count('SELECT count(*) FROM '.FAQ_CATEGORY_TABLE.' cat ');
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('categories.php', $qs);
$qstr = '&amp;order='.($order=='DESC'?'ASC':'DESC');
$query="$sql GROUP BY cat.category_id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' '.__('categories');
else
    $showing=__('No FAQ categories found!');

?>
<div class="pull-left" style="">
 <h2><?php echo __('FAQ Categories');?></h2>
 </div>
<div class="pull-right flush-right">
    <b><a href="categories.php?a=add" class="Icon input-button input-button-primary"><?php echo __('Add New Category');?></a></b></div>
<div class="clear"></div>
<form action="categories.php" method="POST" name="cat">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
    <caption><?php echo $showing; ?></caption>
 <div class="table-responsive">
<div>
 <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th width="7">&nbsp;</th>
            <th width="500"><a <?php echo $name_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=name"><?php echo __('Name');?></a></th>
            <th width="150"><a  <?php echo $type_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=type"><?php echo __('Type');?></a></th>
            <th width="80"><a  <?php echo $faqs_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=faqs"><?php echo __('FAQs');?></a></th>
            <th width="150" nowrap><a  <?php echo $updated_sort; ?>href="categories.php?<?php echo $qstr; ?>&sort=updated"><?php echo __('Last Updated');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['category_id'],$ids))
                    $sel=true;

                $faqs=0;
                if($row['faqs'])
                    $faqs=sprintf('<a href="faq.php?cid=%d">%d</a>',$row['category_id'],$row['faqs']);
                ?>
            <tr id="<?php echo $row['category_id']; ?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?php echo $row['category_id']; ?>" class="ckb"
                            <?php echo $sel?'checked="checked"':''; ?>>
                </td>
                <td><a href="categories.php?id=<?php echo $row['category_id']; ?>"><?php echo Format::truncate($row['name'],200); ?></a>&nbsp;</td>
                <td><?php echo $row['ispublic']?'<b>'.__('Public').'</b>':__('Internal'); ?></td>
                <td style="text-align:right;padding-right:25px;"><?php echo $faqs; ?></td>
                <td>&nbsp;<?php echo Format::db_datetime($row['updated']); ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    <tfoot>
     <tr>
        <td colspan="5">
            <?php if($res && $num){ ?>
            <?php echo __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No FAQ categories found.');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
</div>
 </div>
    <?php
    echo '<div class="navigation">';
        echo '<ul class="pagination">';
            echo $pageNav->getPageLinks();
        echo '</ul>';
    echo '</div>';
    if($res && $num): //Show options..
    endif;
    ?>
    <div class="cold-md-4 col-md-offset-4">
    <p id="actions">
        <input class="button form-control btn-primary" type="submit" name="make_public" value="<?php echo __('Make Public');?>" style="width: 110px; float: left; color: #ffffff; margin: 0px 0px 5px 5px;">
        <input class="button form-control btn-primary" type="submit" name="make_private" value="<?php echo __('Make Internal');?>" style="width: 110px; float: left; margin: 0px 0px 5px 5px; color: #ffffff;">
        <input class="button form-control btn-primary" type="submit" name="delete" value="<?php echo __('Delete');?>" style="width: 110px; float: left; color: #ffffff; margin: 0px 0px 5px 5px;">
    </p>
    </div>

<input type="hidden" id="action" name="a" value="" >

</form>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="make_public-confirm">
        <?php echo sprintf(__('Are you sure you want to make %s <b>public</b>?'),
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="make_private-confirm">
        <?php echo sprintf(__('Are you sure you want to make %s <b>private</b> (internal)?'),
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected category', 'selected categories', 2));?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered, including any associated FAQs.'); ?>
    </p>
    <div><?php echo __('Please confirm to continue.');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?php echo __('No, Cancel');?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!');?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>

<style>

    .input-button{
        padding: 6px 12px !important;
        border-radius: 6px;
        font-size: 14px;
        opacity: 1;
    }

    a.input-button:hover{
        text-decoration: none;
        color:#fff;
    }

    .input-button-primary {
        color: #fff;
        background-color: #337ab7;
        border:solid 1px #2e6da4;
    }

    .input-button-default {
        color: #333;
        background-color: #fff;
        border:solid 1px #ccc;
    }

    @media screen and (max-width: 450px){

        h2{
            margin-bottom: 10px !important;
        }

        .flush-right{
            width: 100%;
        }

        .button{
            margin: 0px !important;
        }

        .flush-right a{
            text-align: center;
            width: 100%;
        }

        input[type=submit], input[type=reset], input[type=button]{
            margin-top: 10px !important;
        }

        table th td{
            white-space: nowrap;
        }

        .navbar{
            z-index: 2 !important;
        }

        .redactor_box{
            z-index: 1 !important;
        }

        .modal-content{
            height: 620px !important;
            overflow: scroll !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        a.input-button{
            margin-bottom: 20px;
        }

        #alert{
            width: 95%;
        }
        input[type=submit]{
            width: 100% !important;
        }

        .dialog{
            width: 90% !important;
        }

        .dialog input[type=button]{
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
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
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .dialog input.close{
            color: #fff;
            background-color: #d9534f;
            border-color: #d43f3a;
            opacity: 1;
        }

        .dialog input.confirm{
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
        }

        .navigation{
            text-align: center !important;
        }

    }

</style>
