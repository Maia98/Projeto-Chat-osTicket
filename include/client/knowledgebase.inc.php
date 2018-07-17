<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

?>
<br />
<h1><?php echo __('Frequently Asked Questions');?></h1>
<br />
<form action="index.php" method="get" id="kb-search">
    <input type="hidden" name="a" value="search">
    <div class="row" style="margin-bottom: -15px">

        <div class="col-md-3 col-xs-12">
            <input id="query" type="text" class="form-control" name="q" value="<?php echo Format::htmlchars($_REQUEST['q']); ?>">
        </div>

        <div class="col-md-3 col-xs-12">
            <select class="form-control" name="cid" id="cid">
                <option value="">&mdash; <?php echo __('All Categories');?> &mdash;</option>
                <?php
                $sql='SELECT category_id, name, count(faq.category_id) as faqs '
                    .' FROM '.FAQ_CATEGORY_TABLE.' cat '
                    .' LEFT JOIN '.FAQ_TABLE.' faq USING(category_id) '
                    .' WHERE cat.ispublic=1 AND faq.ispublished=1 '
                    .' GROUP BY cat.category_id '
                    .' HAVING faqs>0 '
                    .' ORDER BY cat.name DESC ';
                if(($res=db_query($sql)) && db_num_rows($res)) {
                    while($row=db_fetch_array($res))
                        echo sprintf('<option value="%d" %s>%s (%d)</option>',
                            $row['category_id'],
                            ($_REQUEST['cid'] && $row['category_id']==$_REQUEST['cid']?'selected="selected"':''),
                            $row['name'],
                            $row['faqs']);
                }
                ?>
            </select>
        </div>

        <div class="col-md-1 col-xs-12" style="margin-right: 20px; ">
            <input class="btn btn-primary" id="searchSubmit" type="submit" value="<?php echo __('Search');?>">
        </div>

        <div class="col-md-3 col-xs-12">
            <select class="form-control" name="topicId" id="topic-id">
                <option value="">&mdash; <?php echo __('All Help Topics');?> &mdash;</option>
                <?php
                $sql='SELECT ht.topic_id, CONCAT_WS(" / ", pht.topic, ht.topic) as helptopic, count(faq.topic_id) as faqs '
                    .' FROM '.TOPIC_TABLE.' ht '
                    .' LEFT JOIN '.TOPIC_TABLE.' pht ON (pht.topic_id=ht.topic_pid) '
                    .' LEFT JOIN '.FAQ_TOPIC_TABLE.' faq ON(faq.topic_id=ht.topic_id) '
                    .' WHERE ht.ispublic=1 '
                    .' GROUP BY ht.topic_id '
                    .' HAVING faqs>0 '
                    .' ORDER BY helptopic ';
                if(($res=db_query($sql)) && db_num_rows($res)) {
                    while($row=db_fetch_array($res))
                        echo sprintf('<option value="%d" %s>%s (%d)</option>',
                            $row['topic_id'],
                            ($_REQUEST['topicId'] && $row['topic_id']==$_REQUEST['topicId']?'selected="selected"':''),
                            $row['helptopic'], $row['faqs']);
                }
                ?>
            </select>
        </div>

    </div>
</form>
<hr>
<div>
    <div class="row">
        <div class="col-md-12">
            <?php
            if($_REQUEST['q'] || $_REQUEST['cid'] || $_REQUEST['topicId']) { //Search.
                $sql='SELECT faq.faq_id, question '
                    .' FROM '.FAQ_TABLE.' faq '
                    .' LEFT JOIN '.FAQ_CATEGORY_TABLE.' cat ON(cat.category_id=faq.category_id) '
                    .' LEFT JOIN '.FAQ_TOPIC_TABLE.' ft ON(ft.faq_id=faq.faq_id) '
                    .' WHERE faq.ispublished=1 AND cat.ispublic=1';

                if($_REQUEST['cid'])
                    $sql.=' AND faq.category_id='.db_input($_REQUEST['cid']);

                if($_REQUEST['topicId'])
                    $sql.=' AND ft.topic_id='.db_input($_REQUEST['topicId']);


                if($_REQUEST['q']) {
                    $sql.=" AND (question LIKE ('%".db_input($_REQUEST['q'],false)."%')
                 OR answer LIKE ('%".db_input($_REQUEST['q'],false)."%')
                 OR keywords LIKE ('%".db_input($_REQUEST['q'],false)."%')
                 OR cat.name LIKE ('%".db_input($_REQUEST['q'],false)."%')
                 OR cat.description LIKE ('%".db_input($_REQUEST['q'],false)."%')
                 )";
                }

                $sql.=' GROUP BY faq.faq_id ORDER BY question';
                echo "<div><strong>".__('Search Results').'</strong></div><div class="clear"></div>';
                if(($res=db_query($sql)) && ($num=db_num_rows($res))) {
                    echo '<div id="faq">'.sprintf(__('%d FAQs matched your search criteria.'),$num).'
                <ol>';
                    while($row=db_fetch_array($res)) {
                        echo sprintf('
                <li><a href="faq.php?id=%d" class="previewfaq">%s</a></li>',
                            $row['faq_id'],$row['question'],$row['ispublished']?__('Published'):__('Internal'));
                    }
                    echo '  </ol>
             </div>';
                } else {
                    echo '<strong class="faded">'.__('The search did not match any FAQs.').'</strong>';
                }
            } else { //Category Listing.
                $sql='SELECT cat.category_id, cat.name, cat.description, cat.ispublic, count(faq.faq_id) as faqs '
                    .' FROM '.FAQ_CATEGORY_TABLE.' cat '
                    .' LEFT JOIN '.FAQ_TABLE.' faq ON(faq.category_id=cat.category_id AND faq.ispublished=1) '
                    .' WHERE cat.ispublic=1 '
                    .' GROUP BY cat.category_id '
                    .' HAVING faqs>0 '
                    .' ORDER BY cat.name';
                if(($res=db_query($sql)) && db_num_rows($res)) {
                    echo '<div>'.__('Click on the category to browse FAQs.').'</div><br />
                
                    <div class="list-group col-md-4">';
                    while($row=db_fetch_array($res)) {

                        echo sprintf('
                                <a href="faq.php?cid=%d" class="list-group-item">
                                    <h4 class="list-group-item-heading">%s (%d)</h4>
                                    <p class="list-group-item-text">%s</p>
                                </a>
                                ',$row['category_id'],
                            Format::htmlchars($row['name']),$row['faqs'],
                            Format::safe_html($row['description']));
                    }
                    echo '     
              </div>';
                } else {
                    echo __('NO FAQs found');
                }
            }
            ?>
        </div>
    </div>
</div>

<style>

    .col-xs-12{
        margin-bottom: 20px;
    }

    @media screen and (max-width: 450px) {

        input[type=submit]{
            width: 100%;
        }

        .list-group{
            padding-right: 0px !important;
        }

    }

</style>
