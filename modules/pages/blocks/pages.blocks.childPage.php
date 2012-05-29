<?php
function childPage_content($data,$attributes) {
    echo '<ul class="childPages">';
    foreach ($data->output['pageContent']['children'] as $item) {
        echo '<li><a href="',$data->linkRoot.$data->output['pageContent']['shortName'].'/'.$item['shortName'],'">',$item['title'],'</a></li>';
    }
    echo '</ul>';
}
?>