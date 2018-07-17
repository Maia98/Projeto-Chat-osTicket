<?php
    if(!defined('OSTCLIENTINC') || !$faq  || !$faq->isPublished()) die('Access Denied');

    $category=$faq->getCategory();
?>
<br />
<h1><?php echo __('Frequently Asked Questions');?></h1>
<div id="breadcrumbs">
    <a href="index.php"><?php echo __('All Categories');?></a>
    &raquo; <a href="faq.php?cid=<?php echo $category->getId(); ?>"><?php echo $category->getName(); ?></a>
</div>
<div class="col-md-12">
    <strong style="font-size:16px;"><?php echo $faq->getQuestion() ?></strong>
</div>
<br />
<div class="clear"></div>
<br />
<div class="thumbnail thread-body">
    <?php echo Format::safe_html($faq->getAnswerWithImages()); ?>
</div>
<p>
<?php
if($faq->getNumAttachments()) { ?>
 <div><span class="faded"><b><?php echo __('Attachments');?>:</b></span>  <?php echo $faq->getAttachmentsLinks(); ?></div>
<?php
} ?>

<div class="article-meta"><span class="faded"><b><?php echo __('Help Topics');?>:</b></span>
    <?php echo ($topics=$faq->getHelpTopics())?implode(', ',$topics):' '; ?>
</div>
</p>
<hr>
<div class="faded">&nbsp;<?php echo __('Last updated').' '.Format::db_daydatetime($category->getUpdateDate()); ?></div>

<style>

    .col-md-12{
        padding-left: 0px !important;
        margin-top: 5px !important;
    }

    .thumbnail{
        width: 45% !important;
    }

    @media screen and (max-width: 450px) {

        .col-md-12{
            margin-bottom: -25px !important;
            margin-top: 15px !important;
        }

        .thumbnail{
            width: 100% !important;
        }

    }

</style>