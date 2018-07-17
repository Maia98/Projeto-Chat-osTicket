<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$qs = array();
$select='SELECT staff.*, grp.group_name, dept.dept_name as dept,count(m.team_id) as teams ';
$from='FROM '.STAFF_TABLE.' staff '.
      'LEFT JOIN '.GROUP_TABLE.' grp ON(staff.group_id=grp.group_id) '.
      'LEFT JOIN '.DEPT_TABLE.' dept ON(staff.dept_id=dept.dept_id) '.
      'LEFT JOIN '.TEAM_MEMBER_TABLE.' m ON(m.staff_id=staff.staff_id) ';
$where='WHERE 1 ';

if($_REQUEST['did'] && is_numeric($_REQUEST['did'])) {
    $where.=' AND staff.dept_id='.db_input($_REQUEST['did']);
    $qs += array('did' => $_REQUEST['did']);
}

if($_REQUEST['gid'] && is_numeric($_REQUEST['gid'])) {
    $where.=' AND staff.group_id='.db_input($_REQUEST['gid']);
    $qs += array('gid' => $_REQUEST['gid']);
}

if($_REQUEST['tid'] && is_numeric($_REQUEST['tid'])) {
    $where.=' AND m.team_id='.db_input($_REQUEST['tid']);
    $qs += array('tid' => $_REQUEST['tid']);
}

$sortOptions=array('name'=>'staff.firstname,staff.lastname','username'=>'staff.username','status'=>'isactive',
                   'group'=>'grp.group_name','dept'=>'dept.dept_name','created'=>'staff.created','login'=>'staff.lastlogin');

switch ($cfg->getDefaultNameFormat()) {
case 'last':
case 'lastfirst':
case 'legal':
    $sortOptions['name'] = 'staff.lastname, staff.firstname';
    break;
// Otherwise leave unchanged
}

$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'staff.firstname,staff.lastname';

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

$total=db_count('SELECT count(DISTINCT staff.staff_id) '.$from.' '.$where);
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);

$pageNav->setURL('staff.php', $qs);
$qstr .= '&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');
$query="$select $from $where GROUP BY staff.staff_id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;

?>

<div class="pull-left">
    <h2><?php echo __('Agents');?></h2>
</div>
<div class="pull-right flush-right"><b><a href="staff.php?a=add" class="Icon input-button input-button-primary"><?php echo __('Add New Agent');?></a></b></div>
<div class="clear"></div>

<div class="pull-right flush-right" style="width: 100%; margin-top: 10px;">
    <form action="staff.php" method="GET" name="filter">
     <input type="hidden" name="a" value="filter" >
        <select name="did" id="did" class="form-control">
             <option value="0">&mdash; <?php echo __('All Department');?> &mdash;</option>
             <?php
             $sql='SELECT dept.dept_id, dept.dept_name,count(staff.staff_id) as users  '.
                  'FROM '.DEPT_TABLE.' dept '.
                  'INNER JOIN '.STAFF_TABLE.' staff ON(staff.dept_id=dept.dept_id) '.
                  'GROUP By dept.dept_id HAVING users>0 ORDER BY dept_name';
             if(($res=db_query($sql)) && db_num_rows($res)){
                 while(list($id,$name, $users)=db_fetch_row($res)){
                     $sel=($_REQUEST['did'] && $_REQUEST['did']==$id)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>%s (%s)</option>',$id,$sel,$name,$users);
                 }
             }
             ?>
        </select>
        <select name="gid" id="gid" class="form-control">
            <option value="0">&mdash; <?php echo __('All Groups');?> &mdash;</option>
             <?php
             $sql='SELECT grp.group_id, group_name,count(staff.staff_id) as users '.
                  'FROM '.GROUP_TABLE.' grp '.
                  'INNER JOIN '.STAFF_TABLE.' staff ON(staff.group_id=grp.group_id) '.
                  'GROUP BY grp.group_id ORDER BY group_name';
             if(($res=db_query($sql)) && db_num_rows($res)){
                 while(list($id,$name,$users)=db_fetch_row($res)){
                     $sel=($_REQUEST['gid'] && $_REQUEST['gid']==$id)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>%s (%s)</option>',$id,$sel,$name,$users);
                 }
             }
             ?>
        </select>
        <select name="tid" id="tid" class="form-control">
            <option value="0">&mdash; <?php echo __('All Teams');?> &mdash;</option>
             <?php
             $sql='SELECT team.team_id, team.name, count(member.staff_id) as users FROM '.TEAM_TABLE.' team '.
                  'INNER JOIN '.TEAM_MEMBER_TABLE.' member ON(member.team_id=team.team_id) '.
                  'GROUP BY team.team_id ORDER BY team.name';
             if(($res=db_query($sql)) && db_num_rows($res)){
                 while(list($id,$name,$users)=db_fetch_row($res)){
                     $sel=($_REQUEST['tid'] && $_REQUEST['tid']==$id)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>%s (%s)</option>',$id,$sel,$name,$users);
                 }
             }
             ?>
        </select>
        &nbsp;&nbsp;
        <input type="submit" class="btn btn-primary margin-top" name="submit" value="<?php echo __('Apply');?>"/>
    </form>
 </div>
<?php
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing() . ' ' . _N('agent', 'agents', $num);
else
    $showing=__('No agents found!');
?>

<form action="staff.php" method="POST" name="staff" >
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
    <caption><?php echo $showing; ?></caption>
 <div class="table-responsive">
 <table class="table table-striped table-bordered" style="width: 100%;">
    <thead>
        <tr>
            <th width="7px">&nbsp;</th>
            <th width="200"><a <?php echo $name_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=name"><?php echo __('Name');?></a></th>
            <th width="100"><a <?php echo $username_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=username"><?php echo __('Username');?></a></th>
            <th width="100"><a  <?php echo $status_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=status"><?php echo __('Status');?></a></th>
            <th width="120"><a  <?php echo $group_sort; ?>href="staff.php?<?php echo $qstr; ?>&sort=group"><?php echo __('Group');?></a></th>
            <th width="150"><a  <?php echo $dept_sort; ?>href="staff.php?<?php echo $qstr; ?>&sort=dept"><?php echo __('Department');?></a></th>
            <th width="100"><a <?php echo $created_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=created"><?php echo __('Created');?></a></th>
            <th width="145"><a <?php echo $login_sort; ?> href="staff.php?<?php echo $qstr; ?>&sort=login"><?php echo __('Last Login');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        if($res && db_num_rows($res)):
            $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['staff_id'],$ids))
                    $sel=true;
                $name = new PersonsName(array('first' => $row['firstname'], 'last' => $row['lastname']));
                ?>
               <tr id="<?php echo $row['staff_id']; ?>">
                <td width=7px>
                  <input type="checkbox" class="ckb" name="ids[]" value="<?php echo $row['staff_id']; ?>" <?php echo $sel?'checked="checked"':''; ?> >
                <td><a href="staff.php?id=<?php echo $row['staff_id']; ?>"><?php echo Format::htmlchars($name); ?></a>&nbsp;</td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['isactive']?__('Active'):'<b>'.__('Locked').'</b>'; ?>&nbsp;<?php echo $row['onvacation']?'<small>(<i>'.__('vacation').'</i>)</small>':''; ?></td>
                <td><a href="groups.php?id=<?php echo $row['group_id']; ?>"><?php echo Format::htmlchars($row['group_name']); ?></a></td>
                <td><a href="departments.php?id=<?php echo $row['dept_id']; ?>"><?php echo Format::htmlchars($row['dept']); ?></a></td>
                <td><?php echo Format::db_date($row['created']); ?></td>
                <td><?php echo Format::db_datetime($row['lastlogin']); ?>&nbsp;</td>
               </tr>
            <?php
            } //end of while.
        endif; ?>
    <tfoot>
     <tr>
        <td colspan="8">
            <?php if($res && $num){ ?>
            <?php echo __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No agents found!');
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

<p class="centered" id="actions">
    <input class="button btn btn-primary" style="color: #ffffff;" type="submit" name="enable" value="<?php echo __('Enable');?>" >
    <input class="button btn btn-warning" style="color: #ffffff;" type="submit" name="disable" value="<?php echo __('Lock');?>" >
    <input class="button btn btn-danger" style="color: #ffffff;" type="submit" name="delete" value="<?php echo __('Delete');?>">
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
        <?php echo sprintf(__('Are you sure you want to <b>enable</b> (unlock) %s?'),
            _N('selected agent', 'selected agents', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="disable-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>disable</b> (lock) %s?'),
            _N('selected agent', 'selected agents', 2));?>
        <br><br><?php echo __("Locked staff won't be able to login to Staff Control Panel.");?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected agent', 'selected agents', 2));?></strong></font>
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

    .margin-top{
        margin-top: -3px;
    }

    form{
        margin: 0px;
    }

    .dialog{
        margin-top: 20px !important;
    }

    .form-control{
        width: auto !important;
        display: inline-block !important;
        margin-right: 10px;
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

        .form-control{
            width: 100% !important;
            display: block !important;
            margin-right: 0 !important;
        }

        a.input-button{
            margin-bottom: 20px !important;
        }

        .margin-top{
            margin-top: -15px;
        }

        input[type=submit], input[type=reset], input[type=button], button.button{
            width: 100% !important;
            margin-bottom: 10px !important;
        }

        select{
            display: block !important;
            width: 100% !important;
        }

        .dialog{
            width: 95% !important;
            margin-top: 20px !important;
        }

        .dialog input{
            width: auto !important;
        }

        .dialog .pull-left{
            width: 49% !important;
            float: left;
            text-align: left;
        }

        .dialog .pull-right{
            width: 49% !important;
            float: right;
            text-align: right;
        }

        .navigation{
            text-align: center !important;
        }

        .pull-left a{
            display: block;
            text-align: center;
        }

    }

</style>

