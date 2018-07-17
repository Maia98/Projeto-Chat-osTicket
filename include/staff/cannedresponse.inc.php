<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
$info=$qs = array();
if($canned && $_REQUEST['a']!='add'){
    $title=__('Update Canned Response');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$canned->getInfo();
    $info['id']=$canned->getId();
    $qs += array('id' => $canned->getId());
    // Replace cid: scheme with downloadable URL for inline images
    $info['response'] = $canned->getResponseWithImages();
    $info['notes'] = Format::viewableImages($info['notes']);
}else {
    $title=__('Add New Canned Response');
    $action='create';
    $submit_text=__('Add Response');
    $info['isenabled']=isset($info['isenabled'])?$info['isenabled']:1;
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<form action="canned.php?<?php echo Http::build_query($qs); ?>" method="post" id="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?php echo __('Canned Response')?>
 &nbsp;<i class="help-tip icon-question-sign" href="#canned_response"></i></h2>

<div class="col-md-12">
 <div class="row">
     <table class="form_table" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="2">
                    <h4><?php echo $title; ?></h4>
                    <em><?php echo __('Canned response settings');?></em>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="180" class="required" style="padding-left: 5px;">
                <br>
                <?php echo __('Status');?>:&nbsp;<span class="error">*&nbsp;<?php echo $errors['isenabled']; ?></span></td>
                <td>
                    <div class="col-md-4">
                    &nbsp;
                        <label><input type="radio" name="isenabled" value="1" <?php
                            echo $info['isenabled']?'checked="checked"':''; ?>>&nbsp;<?php echo __('Active'); ?>&nbsp;</label>
                        <label><input type="radio" name="isenabled" value="0" <?php
                                echo !$info['isenabled']?'checked="checked"':''; ?>>&nbsp;<?php echo __('Disabled'); ?>&nbsp;</label>
                        &nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td width="180" class="required" style="padding-left: 5px;"><?php echo __('Department');?>:&nbsp;<span class="error">*&nbsp;<?php echo $errors['dept_id']; ?></span></td>
                <td>
                <br>
                    <div class="col-md-5">
                        <select name="dept_id" class="form-control">
                        <option value="0">&mdash; <?php echo __('All Departments');?> &mdash;</option>
                        <?php
                        $sql='SELECT dept_id, dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                        if(($res=db_query($sql)) && db_num_rows($res)) {
                            while(list($id,$name)=db_fetch_row($res)) {
                                $selected=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';
                                echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                            }
                        }
                        ?>
                    </select>
                    </div>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('Canned Response');?></strong>: <?php echo __('Make the title short and clear.');?>&nbsp;</em>
                </th>
            </tr>
            <tr>
                <td style="padding-left: 5px;">
                    <br>
                    <b><?php echo __('Title');?>:</b>
                    <span class="error">*&nbsp;<?php echo $errors['title']; ?></span>
                    
                </td>
                <td>
                    <br>
                    <div class="col-md-4">
                        <input type="text" size="70" name="title" value="<?php echo $info['title']; ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 5px;">                    
                    
                    <br><br>
                    <div style="margin-bottom:0.5em"><b><?php echo __('Canned Response'); ?></b>
                        <font class="error">*&nbsp;<?php echo $errors['response']; ?></font>
                        &nbsp;&nbsp;&nbsp;(<a class="tip" href="#ticket_variables"><?php echo __('Supported Variables'); ?></a>)
                        </div>
                    <textarea name="response" class="richtext draft draft-delete" cols="21" rows="12"
                        data-draft-namespace="canned"
                        data-draft-object-id="<?php if (isset($canned)) echo $canned->getId(); ?>"
                        style="width:98%;" class="richtext draft"><?php
                            echo $info['response']; ?></textarea>
                    <div><h3><?php echo __('Canned Attachments'); ?> <?php echo __('(optional)'); ?>
                    &nbsp;<i class="help-tip icon-question-sign" href="#canned_attachments"></i></h3>
                    <div class="error"><?php echo $errors['files']; ?></div>
                    </div>
                    <?php
                    $attachments = $canned_form->getField('attachments');
                    if ($canned && ($files=$canned->attachments->getSeparates())) {
                        $ids = array();
                        foreach ($files as $f)
                            $ids[] = $f['id'];
                        $attachments->value = $ids;
                    }
                    print $attachments->render(); ?>
                    <br/>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('Internal Notes');?></strong>: <?php echo __('Notes about the canned response.');?>&nbsp;</em>
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
    <p style="text-align:center; margin-top: 30px;">
    <input type="submit" name="submit" class="btn btn-primary" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  class="btn btn-primary" value="<?php echo __('Reset'); ?>" onclick="javascript:
        $(this.form).find('textarea.richtext')
            .redactor('deleteDraft');
        location.reload();" />
    <input type="button" name="cancel" class="btn btn-primary" value="<?php echo __('Cancel'); ?>" onclick='window.location.href="canned.php"'>
    </p>
</div>
 <?php if ($canned && $canned->getFilters()) { ?>
    <br/>
    <div id="msg_warning"><?php echo __('Canned response is in use by email filter(s)');?>: <?php
    echo implode(', ', $canned->getFilters()); ?></div>
 <?php } ?>

</form>
