<?php
function blogs_content($data,$attributes) {
    $rssLink = isset($data->output['blogInfo']['rssOverride']{1}) ? $data->output['blogInfo']['rssOverride'] : $data->localRoot.'/rss';
    echo '
	<h2>Blog Categories</h2>
        <div class="buttonWrapper">
        <a href="',$rssLink,'" class="greenButton">
            <span>
                <b></b>Subscribe
                <i><!-- hover state precache --></i>
            </span>
        </a>';
    if(!empty($data->output['blogCategoryList'])){
        echo '<ul>';
        foreach($data->output['blogCategoryList'] as $categoryItem) {
            echo '<li>
		            <a href="',$data->localRoot,'/categories/',$categoryItem['shortName'],'">',$categoryItem['name'],'</a>
			     </li>';
        }
            echo '</ul>';
    } else {
        echo 'No Blog Categories Found';
    }
}
?>