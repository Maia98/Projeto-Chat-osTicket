<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$filter) die('Access Denied');

$qs = array();
$select='SELECT rule.* ';
$from='FROM '.FILTER_RULE_TABLE.' rule ';
$where='WHERE rule.filter_id='.db_input($filter->getId());
$search=false;
if($_REQUEST['q'] && strlen($_REQUEST['q'])>3) {
    $search=true;
    if(strpos($_REQUEST['q'],'@') && Validator::is_email($_REQUEST['q']))
        $where.=' AND rule.val='.db_input($_REQUEST['q']);
    else
        $where.=' AND rule.val LIKE "%'.db_input($_REQUEST['q'],false).'%"';

}elseif($_REQUEST['q']) {
    $errors['q']=__('Term too short!');
}

$sortOptions=array('email'=>'rule.val','status'=>'isactive','created'=>'rule.created','created'=>'rule.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'email';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'rule.val';

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

$total=db_count('SELECT count(DISTINCT rule.id) '.$from.' '.$where);
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('banlist.php', $qs);
$qstr.='&amp;order='.($order=='DESC'?'ASC':'DESC');
$query="$select $from $where ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
?>

<div class="pull-left">
    <h2><?php echo __('Banned Email Addresses');?>
        <i class="help-tip icon-question-sign" href="#ban_list"></i>
    </h2>
</div>
<div class="pull-right flush-right" style="padding-right:5px;"><b><a href="banlist.php?a=add" class="Icon input-button input-button-primary"><?php echo __('Ban New Email');?></a></b></div>
<div class="clear"></div>
<div class="row ende-margin">
    <form action="banlist.php" method="GET" name="filter">
     <input type="hidden" name="a" value="filter" >
     <div class="col-md-12 col-xs-12">
         <?php echo __('Query');?>:
         <input name="q" type="text" value="<?php echo Format::htmlchars($_REQUEST['q']); ?>">
         <input type="submit" class="btn btn-primary" name="submit" value="<?php echo __('Search');?>"/>
     </div>
    </form>
 </div>
<?php
if(($res=db_query($query)) && ($num=db_num_rows($res)))
    $showing=$pageNav->showing();
else
    $showing=__('No banned emails matching the query found!');

if($search)
    $showing=__('Search Results').': '.$showing;

?>
<form action="banlist.php" method="POST" name="banlist">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
<input type="hidden" id="action" name="a" value="" >
    <caption><?php echo $showing; ?></caption>
  <div class="table-responsive">
     <table class="table table-striped table-bordered table-pattern">
        <thead>
            <tr>
                <th width="7px">&nbsp;</th>
                <th width="350"><a <?php echo $email_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=email"><?php echo __('Email Address');?></a></th>
                <th width="200"><a  <?php echo $status_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=status"><?php echo __('Ban Status');?></a></th>
                <th width="120"><a <?php echo $created_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=created"><?php echo __('Date Added');?></a></th>
                <th width="120"><a <?php echo $updated_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=updated"><?php echo __('Last Updated');?></a></th>
            </tr>
        </thead>
        <tbody>
        <?php
            if($res && db_num_rows($res)):
                $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
                while ($row = db_fetch_array($res)) {
                    $sel=false;
                    if($ids && in_array($row['id'],$ids))
                        $sel=true;
                    ?>
                   <tr id="<?php echo $row['id']; ?>">
                    <td width=7px>
                      <input type="checkbox" class="ckb" name="ids[]" value="<?php echo $row['id']; ?>" <?php echo $sel?'checked="checked"':''; ?>>
                    </td>
                    <td>&nbsp;<a href="banlist.php?id=<?php echo $row['id']; ?>"><?php echo Format::htmlchars($row['val']); ?></a></td>
                    <td>&nbsp;&nbsp;<?php echo $row['isactive']?__('Active'):'<b>'.__('Disabled').'</b>'; ?></td>
                    <td><?php echo Format::db_date($row['created']); ?></td>
                    <td><?php echo Format::db_datetime($row['updated']); ?>&nbsp;</td>
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
                    echo __('No banned emails found!');
                } ?>
            </td>
         </tr>
        </tfoot>
    </table>
</div>
<?php
if($res && $num): //Show options..
    echo '<div class="navigation">';
        echo '<ul class="pagination">';
            echo $pageNav->getPageLinks();
        echo '</ul>';
    echo '</div>';
?>
<p class="alinhamentoCenter" id="actions">
    <input class="button btn btn-primary" type="submit" name="enable" value="<?php echo __('Enable');?>" >
    <input class="button btn btn-warning" type="submit" name="disable" value="<?php echo __('Disable');?>" >
    <input class="button btn btn-danger" type="submit" name="delete" value="<?php echo __('Delete');?>">
</p>
<?php
endif;
?>
</form>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="enable-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>enable</b> %s?'),
            _N('selected ban rule', 'selected ban rules', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="disable-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>disable</b> %s?'),
            _N('selected ban rule', 'selected ban rules', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected ban rule', 'selected ban rules', 2));?></strong></font>
    </p>
    <div><?php echo __('Please confirm to continue.');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" id="close-modal" value="<?php echo __('No, Cancel');?>" class="btn btn-danger">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!');?>" class="confirm btn btn-primary">
        </span>
     </p>
    <div class="clear"></div>
</div>
<style>

    .dialog{
        margin-top: 20px !important;
    }

    .ende-margin{
        margin-top: 10px;
        margin-bottom: -10px;
        text-align: right;
        padding-right: 5px !important;
    }

    input[name=q]{
        margin-right: 20px;
        margin-top: 1px !important;
    }

    input[type=submit]{
        margin-top: -1px !important;
    }

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

    @media screen and (max-width: 450px) {

        .ende-margin{
            margin-top: 0;
            margin-bottom: 0;
            text-align: left;
        }

        input[name=q]{
            width: 100%;
        }

        .flush-right{
            width: 100%;
        }

        .flush-right a{
            text-align: center;
            width: 100%;
        }

        a.input-button{
            margin-bottom: 20px !important;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
        }

        .dialog{
            width: 95% !important;
            margin-top: 20px !important;
        }

        .division{
            clear: both !important;
        }

        .navigation{
            text-align: center !important;
        }

    }

</style>

