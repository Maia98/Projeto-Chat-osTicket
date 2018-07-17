<?php
if(($tabs=$nav->getTabs()) && is_array($tabs)){

    foreach($tabs as $name =>$tab) {

        if($subnav=$nav->getSubMenu($name))
        {
            echo sprintf('<li class="dropdown %s">', $tab['active'] ? 'active':'inactive');
            echo sprintf('<a class="dropdown-toggle %s " data-toggle="dropdown" href="#">%s', $tab['active'] ? 'active':'inactive', $tab['desc']); 
                        echo '<span class="caret"></span></a>';
                        echo "<ul class='dropdown-menu'>\n";

                        foreach($subnav as $k => $item) {

                            if (!($id=$item['id']))
                            {
                                $id="nav$k";
                            }

                            echo sprintf('<li><a class="%s" href="%s" title="%s" id="%s" target="%s">%s</a></li>', "", $item['href'], $item['title'], $id, isset($item['target']) ? $item['target'] : "" ,$item['desc']);
                        }

                        echo "\n</ul>\n";

        }else{
            echo sprintf('<li class="%s %s"><a href="%s">%s</a>', $tab['active'] ? 'active':'inactive', @$tab['class'] ?: '', $tab['href'],$tab['desc']); 
        }
        echo "\n</li>\n";
    }
} ?>
