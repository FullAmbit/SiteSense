<?php
function blogs_content($data,$attributes) {

    echo '
					<h2>Blog Categories</h2>';
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