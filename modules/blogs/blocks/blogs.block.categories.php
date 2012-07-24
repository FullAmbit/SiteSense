<?php
function categories_content($data,$attributes) {

	$rssLink = isset($data->output['blogInfo']['rssOverride']{1}) ? $data->output['blogInfo']['rssOverride'] : $data->localRoot.'/rss';
	echo '
        <div class="buttonWrapper">
        <a href="',$rssLink,'" class="greenButton">
            <span>
                <b></b>Subscribe
                <i><!-- hover state precache --></i>
            </span>
        </a></div><h2>Blog Categories</h2>';
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