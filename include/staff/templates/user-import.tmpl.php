<div id="the-lookup-form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3><?php echo $info['title']; ?></h3>
<!--        <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>-->
    </div>
<?php
if ($info['error']) {
    echo sprintf('<p id="msg_error">%s</p>', $info['error']);
} elseif ($info['warn']) {
    echo sprintf('<p id="msg_warning">%s</p>', $info['warn']);
} elseif ($info['msg']) {
    echo sprintf('<p id="msg_notice">%s</p>', $info['msg']);
} ?>
<ul class="tabs">
    <li><a href="#copy-paste" class="active"
        ><i class="icon-edit"></i>&nbsp;<?php echo __('Copy Paste'); ?></a></li>
    <li><a href="#upload"
        ><i class="icon-fixed-width icon-cloud-upload"></i>&nbsp;<?php echo __('Upload'); ?></a></li>
</ul>
    <div class="modal-body">
        <form action="<?php echo $info['action']; ?>" method="post" enctype="multipart/form-data"
            onsubmit="javascript:
            if ($(this).find('[name=import]').val()) {
                $(this).attr('action', '<?php echo $info['upload_url']; ?>');
                $(document).unbind('submit.dialog');
            }">
        <?php echo csrf_token();
        if ($org_id) { ?>
            <input type="hidden" name="id" value="<?php echo $org_id; ?>"/>
        <?php } ?>

        <div class="tab_content" id="copy-paste" style="margin:5px;">
        <h2 style="margin-bottom:10px"><?php echo __('Name and Email'); ?></h2>
        <p><?php echo __(
        'Enter one name and email address per line.'); ?><br/><em><?php echo __(
        'To import more other fields, use the Upload tab.'); ?></em>
        </p>
        <textarea name="pasted" style="display:block;width:100%;height:8em" placeholder="<?php echo __('e.g. John Doe, john.doe@osticket.com'); ?>"><?php echo $info['pasted']; ?></textarea>
        </div>

        <div class="tab_content" id="upload" style="display:none;margin:5px;">
        <h2 style="margin-bottom:10px"><?php echo __('Import a CSV File'); ?></h2>
        <p>
        <em><?php echo sprintf(__(
        'Use the columns shown in the table below. To add more fields, visit the Admin Panel -&gt; Manage -&gt; Forms -&gt; %s page to edit the available fields.  Only fields with `variable` defined can be imported.'),
            UserForm::getUserForm()->get('title')
        ); ?>
        </p>
            <div class="table-responsive">
        <table class="list"><tr
        <?php
            $fields = array();
            $data = array(
                array('name' => __('John Doe'), 'email' => __('john.doe@osticket.com'))
            );
            foreach (UserForm::getUserForm()->getFields() as $f)
                if ($f->get('name'))
                    $fields[] = $f->get('name');
            foreach ($fields as $f) { ?>
                    <th><?php echo mb_convert_case($f, MB_CASE_TITLE); ?></th>
        <?php } ?>
        </tr>
        <?php
            foreach ($data as $d) {
                foreach ($fields as $f) {
                    ?><td><?php
                    if (isset($d[$f])) echo $d[$f];
                    ?></td><?php
                }
            } ?>
        </tr></table>
            </div>
        <br/>
        <input type="file" name="import"/>
        </div>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <button data-dismiss="modal" class="btn btn-primary <?php // echo $user ? 'cancel' : 'close' ?>">
                    <?php echo __('Cancel'); ?>
                </button>
            </span>
            <span class="buttons pull-right">
                <input class="btn btn-success" type="submit" value="<?php echo __('Import Users'); ?>" style="margin-top: 0;">
            </span>
            <br>
         </p>
    </div>
    </form>
</div>