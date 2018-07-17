<?php
if(!defined('OSTADMININC') || !$thisstaff->isAdmin()) die('Access Denied');

$qs = array();
$sql='SELECT email.*,dept.dept_name as department,priority_desc as priority '.
     ' FROM '.EMAIL_TABLE.' email '.
     ' LEFT JOIN '.DEPT_TABLE.' dept ON (dept.dept_id=email.dept_id) '.
     ' LEFT JOIN '.TICKET_PRIORITY_TABLE.' pri ON (pri.priority_id=email.priority_id) ';
$sql.=' WHERE 1';
$sortOptions=array('email'=>'email.email','dept'=>'department','priority'=>'priority','created'=>'email.created','updated'=>'email.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'email';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'email.email';

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

$total=db_count('SELECT count(*) FROM '.EMAIL_TABLE.' email ');
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('emails.php', $qs);
$qstr = '&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');
$query="$sql GROUP BY email.email_id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' '.__('emails');
else
    $showing=__('No emails found!');

$def_dept_id = $cfg->getDefaultDeptId();
$def_dept_name = $cfg->getDefaultDept()->getName();
$def_priority = $cfg->getDefaultPriority()->getDesc();

?>
<div class="pull-left" style="">
 <h2><?php echo __('Email Addresses');?></h2>
 </div>
<div class="pull-right flush-right" style="padding-top:5px;padding-right:5px;">
    <b><a href="emails.php?a=add" class="Icon input-button input-button-primary"><?php echo __('Add New Email');?></a></b></div>
<div class="clear"></div>
<form action="emails.php" method="POST" name="emails">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
    <caption><?php echo $showing; ?></caption>
 <div class="table-responsive">
     <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th width="7">&nbsp;</th>
                <th width="400"><a <?php echo $email_sort; ?> href="emails.php?<?php echo $qstr; ?>&sort=email"><?php echo __('Email');?></a></th>
                <th width="120"><a  <?php echo $priority_sort; ?> href="emails.php?<?php echo $qstr; ?>&sort=priority"><?php echo __('Priority');?></a></th>
                <th width="250"><a  <?php echo $dept_sort; ?> href="emails.php?<?php echo $qstr; ?>&sort=dept"><?php echo __('Department');?></a></th>
                <th width="110" nowrap><a  <?php echo $created_sort; ?>href="emails.php?<?php echo $qstr; ?>&sort=created"><?php echo __('Created');?></a></th>
                <th width="150" nowrap><a  <?php echo $updated_sort; ?>href="emails.php?<?php echo $qstr; ?>&sort=updated"><?php echo __('Last Updated');?></a></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $total=0;
            $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
            if($res && db_num_rows($res)):
                $defaultId=$cfg->getDefaultEmailId();
                while ($row = db_fetch_array($res)) {
                    $sel=false;
                    if($ids && in_array($row['email_id'],$ids))
                        $sel=true;
                    $default=($row['email_id']==$defaultId);
                    $email=$row['email'];
                    if($row['name'])
                        $email=$row['name'].' <'.$row['email'].'>';
                    ?>
                <tr id="<?php echo $row['email_id']; ?>">
                    <td width=7px>
                      <input type="checkbox" class="ckb" name="ids[]" value="<?php echo $row['email_id']; ?>"
                                <?php echo $sel?'checked="checked"':''; ?>  <?php echo $default?'disabled="disabled"':''; ?>>
                    </td>
                    <td><span class="ltr"><a href="emails.php?id=<?php echo $row['email_id']; ?>"><?php echo Format::htmlchars($email); ?></a></span></td>
                    <td><?php echo $row['priority'] ?: $def_priority; ?></td>
                    <td><a href="departments.php?id=<?php $row['dept_id'] ?: $def_dept_id; ?>"><?php
                        echo $row['department'] ?: $def_dept_name; ?></a></td>
                    <td>&nbsp;<?php echo Format::db_date($row['created']); ?></td>
                    <td>&nbsp;<?php echo Format::db_datetime($row['updated']); ?></td>
                </tr>
                <?php
                } //end of while.
            endif; ?>
        <tfoot>
         <tr>
            <td colspan="6">
                <?php if($res && $num){ ?>
                <?php echo __('Select');?>:&nbsp;
                <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
                <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
                <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
                <?php }else{
                    echo __('No help emails found');
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
    <input class="button btn btn-danger" type="submit" name="delete" value="<?php echo __('Delete Email(s)');?>" >
</p>
<?php
endif;
?>
</form>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected email', 'selected emails', 2)) ;?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered.');?>
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

        .navigation{
            text-align: center !important;
        }

    }

</style>
