<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function blogs_addQueries() {
	return array(
		'getBlogById' => '
			SELECT * FROM !prefix!blogs
			WHERE id = :blogId
		',
		'getBlogByName' => '
			SELECT * FROM !prefix!blogs
			WHERE shortName = :shortName
		',
		'getBlogPostsByIDandName' => '
			SELECT * FROM !prefix!blogPosts
			WHERE blogId = :blogId
			AND shortName = :shortName
		',
		'countBlogPosts' => '
			SELECT count(id) AS count
			FROM !prefix!blogPosts
			WHERE blogId = :blogId
			AND live = TRUE
		',
		'getBlogPostsDelimited' => '
			SELECT * FROM !prefix!blogPosts
			WHERE blogId = :blogId
			AND live = TRUE
			ORDER BY postTime DESC
			LIMIT :start, :count
		'
	);
}

?>