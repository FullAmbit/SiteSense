<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function blogcomments_addQueries() {
	return array(
		'getCommentById' => '
			SELECT * FROM !prefix!blogcomments
			WHERE id = :blogId
		',
		'getCommentsByPost' => '
			SELECT * FROM !prefix!blogcomments
			WHERE post = :post
			ORDER BY `time` DESC
		',
		'countCommentsByPost' => '
			SELECT count(id) AS count
			FROM !prefix!blogcomments
			WHERE post = :post
		',
		'makeComment' => '
			INSERT INTO !prefix!blogcomments
			(post, commenter, content)
			VALUES
			(:post, :commenter, :content)
		'
	);
}

?>