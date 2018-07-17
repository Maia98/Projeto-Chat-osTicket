<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$qs = array();
if($_REQUEST['type']) {
    $qs += array('type' => $_REQUEST['type']);
}
$type=null;
switch(strtolower($_REQUEST['type'])){
    case 'error':
        $title=__('Errors');
        $type=$_REQUEST['type'];
        break;
    case 'warning':
        $title=__('Warnings');
        $type=$_REQUEST['type'];
        break;
    case 'debug':
        $title=__('Debug logs');
        $type=$_REQUEST['type'];
        break;
    default:
        $type=null;
        $title=__('All logs');
}

$qwhere =' WHERE 1';
//Type
if($type)
    $qwhere.=' AND log_type='.db_input($type);

//dates
$startTime  =($_REQUEST['startDate'] && (strlen($_REQUEST['startDate'])>=8))?strtotime($_REQUEST['startDate']):0;
$endTime    =($_REQUEST['endDate'] && (strlen($_REQUEST['endDate'])>=8))?strtotime($_REQUEST['endDate']):0;

if( ($startTime && $startTime>time()) or ($startTime>$endTime && $endTime>0)){
    $errors['err']=__('Entered date span is invalid. Selection ignored.');
    $startTime=$endTime=0;
}else{
    if($startTime){
        $qwhere.=' AND created>=FROM_UNIXTIME('.$startTime.')';
        $qs += array('startDate' => $_REQUEST['startDate']);
    }
    if($endTime){
        $qwhere.=' AND created<=FROM_UNIXTIME('.$endTime.')';
        $qs += array('endDate' => $_REQUEST['endDate']);
    }
}
//$_REQUEST['startDate'] = date('d/m/Y', $startTime);
//$_REQUEST['endDate'] = date('d/m/Y', $endTime);

$sortOptions=array('id'=>'log.log_id', 'title'=>'log.title','type'=>'log_type','ip'=>'log.ip_address'
                    ,'date'=>'log.created','created'=>'log.created','updated'=>'log.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'id';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'log.created';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order?$order:'DESC';

if($order_column && strpos($order_column,',')){
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$qselect = 'SELECT log.* ';
$qfrom=' FROM '.SYSLOG_TABLE.' log ';
$total=db_count("SELECT count(*) $qfrom $qwhere");
$page = ($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
//pagenate
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->setURL('logs.php',$qs);
$qs += array('order' => ($order=='DESC' ? 'ASC' : 'DESC'));
$qstr = '&amp;'. Http::build_query($qs);
$query="$qselect $qfrom $qwhere ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' '.$title;
else
    $showing=__('No logs found!');
?>

<h2><?php echo __('System Logs');?>
    &nbsp;<i class="help-tip icon-question-sign allign" href="#system_logs"></i>
</h2>
<div id='filter'>
 <form action="logs.php" method="get">
    <div style="width: 100%">
        <div class="col-md-4 col-xs-12">
            <b><?php echo __('Date Span'); ?></b>
            <?php echo __('Between'); ?>:&nbsp;<i class="help-tip icon-question-sign" href="#date_span"></i>
            <input class="dp form-control" id="sd" size=15 name="startDate" value="<?php echo Format::htmlchars($_REQUEST['startDate']); ?>" autocomplete=OFF style="display: inline-block;">
        </div>
        <div class="division"></div>
        <div class="col-md-2 col-xs-12">
            <input class="dp form-control" id="ed" size=15 name="endDate" value="<?php echo Format::htmlchars($_REQUEST['endDate']); ?>" autocomplete=OFF style="display: inline-block;">
        </div>
        <div class="col-md-4 col-xs-12">
            <?php echo __('Log Level'); ?>:&nbsp;<i class="help-tip icon-question-sign" href="#type"></i>
            <select name='type' class="form-control log-nivel" style="display: inline-block; width: 70%">
                <option value="" selected><?php echo __('All');?></option>
                <option value="Error" <?php echo ($type=='Error')?'selected="selected"':''; ?>><?php echo __('Errors');?></option>
                <option value="Warning" <?php echo ($type=='Warning')?'selected="selected"':''; ?>><?php echo __('Warnings');?></option>
                <option value="Debug" <?php echo ($type=='Debug')?'selected="selected"':''; ?>><?php echo __('Debug');?></option>
            </select>
        </div>
        <div class="division"></div>
        <div class="col-md-1 col-xs-12">
            &nbsp;&nbsp;<button type="submit" class="btn btn-primary"><?php echo __('Go!');?></button>
        </div>
        <div style="clear: both;"></div>
    </div>
 </form>
</div>
<form action="logs.php" method="POST" name="logs">
<?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
    <div class="table-responsive" style="overflow: hidden">

 <table class="list" border="0" cellspacing="1" cellpadding="0" width="100%">
    <caption><?php echo $showing; ?></caption>
    <thead>
        <tr>
            <th width="7">&nbsp;</th>
            <th width="320"><a <?php echo $title_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=title"><?php echo __('Log Title');?></a></th>
            <th width="100"><a  <?php echo $type_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=type"><?php echo __('Log Type');?></a></th>
            <th width="200" nowrap><a  <?php echo $date_sort; ?>href="logs.php?<?php echo $qstr; ?>&sort=date"><?php echo __('Log Date');?></a></th>
            <th width="120"><a  <?php echo $ip_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=ip"><?php echo __('IP Address');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['log_id'],$ids))
                    $sel=true;
                ?>
            <tr id="<?php echo $row['log_id']; ?>">
                <td width=7px>
                  <input type="checkbox" class="ckb" name="ids[]" value="<?php echo $row['log_id']; ?>"
                            <?php echo $sel?'checked="checked"':''; ?>> </td>
                <td><a class="tip" href="#log/<?php echo $row['log_id']; ?>"><?php echo Format::htmlchars($row['title']); ?></a></td>
                <td><?php echo $row['log_type']; ?></td>
                <td>&nbsp;<?php echo Format::db_daydatetime($row['created']); ?></td>
                <td><?php echo $row['ip_address']; ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="6">
            <?php if($res && $num){ ?>
            <?php echo __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No logs found');
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
    <input class="button input-button input-button-danger" type="submit" name="delete" value="<?php echo __('Delete Selected Entries');?>">
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
            _N('selected log entry', 'selected log entries', 2));?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered.');?>
    </p>
    <div><?php echo __('Please confirm to continue.');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" id="close-modal" value="<?php echo __('No, Cancel'); echo __('!')?>" class="input-button input-button-primary">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!');?>" class="confirm input-button input-button-danger">
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

    .input-button-danger{
        color: #fff;
        background-color: #d9534f;
        border:solid 1px #d43f3a;
    }

    .input-button-warnig{
        color: #fff;
        background-color: #f0ad4e;
        border:solid 1px #e38d13;
    }

    @media screen and (max-width: 450px){

        .help-tip{
            float: right;
        }

        .allign{
            margin-top: 10px !important;
        }

        input[type=checkbox]{
            width: 24px !important;
        }

        .dialog{
            width: 90% !important;
        }

        input, select{
            width: 100% !important;
            margin-bottom: 10px;
        }

        button[type=submit]{
            width: 100% !important;
        }

        .ui-datepicker-trigger{
            display: none;
        }

        ui-datepicker{
            width: 20% !important;
        }

        .division{
            clear: both !important;
        }

        .navigation{
            text-align: center !important;
        }
        
        .col-xs-12{
            padding: 0 !important;
        }

        .list{
            width: 940px;
        }

        .table-responsive{
            overflow: scroll !important;
        }

        .log-nivel{
            margin-bottom: -20px !important;
        }

        .dialog{
            margin-top: 20px !important;
        }

    }

</style>

<script>

    $("input.dp").each(function () {

        if($(this).val() != ""){
            date = $(this).val();
            splitDate = date.split('-');
            correctDate = splitDate[2]+'/'+splitDate[1]+'/'+splitDate[0];

            $(this).val(correctDate);
        }

    });

</script>