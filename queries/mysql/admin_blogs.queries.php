<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function admin_blogs_addQueries() {
	return array(
		'getAllBlogs' => '
			SELECT * FROM !prefix!blogs
		',
		'countBlogs' => '
			SELECT count(id) AS count
			FROM !prefix!blogs
		',
		'countBlogPosts' => '
			SELECT count(id) FROM !prefix!blogPosts
			WHERE blogId = :blogId
		',
		'getBlogById' => '
			SELECT * FROM !prefix!blogs
			WHERE id = :id
		',
		'deleteBlogById' => '
			DELETE FROM !prefix!blogs
			WHERE id = :id
		',
		'deleteBlogPostByBlogId' => '
			DELETE FROM !prefix!blogPosts
			WHERE blogId = :id
		',
		'getBlogPostsById' => '
			SELECT * FROM !prefix!blogPosts
			WHERE id = :id
		',
		'deleteBlogPostById' => '
			DELETE FROM !prefix!blogPosts
			WHERE id = :id
		',
		'getBlogIdByName' => '
			SELECT id FROM !prefix!blogs
			WHERE shortName = :shortName
		',
		'getBlogPostIdByName' => '
			SELECT id FROM !prefix!blogPosts
			WHERE shortName = :shortName
		',
		'getBloggersByUserLevel' => '
			SELECT id,name,userLevel FROM !prefix!users
			WHERE userLevel >= '.USERLEVEL_BLOGGER.'
			ORDER BY userLevel DESC,id ASC
		',
		'updateBlogById' => '
			UPDATE !prefix!blogs
			SET
				shortName            =   :shortName,
				name                 =   :name,
				owner                =   :owner,
				minPermission        =   :minPermission,
				numberPerPage        =   :numberPerPage,
				description          =   :description,
				commentsRequireLogin =   :commentsRequireLogin
			WHERE id = :id
		',
		'insertBlog' => '
			INSERT INTO !prefix!blogs
			(name,shortName,owner,minPermission,numberPerPage,description,commentsRequireLogin) VALUES (:name, :shortName, :owner, :minPermission, :numberPerPage, :description, :commentsRequireLogin)
		',
		'updateShortNameById' => '
			UPDATE !prefix!blogs
			SET shortName = :shortName
			WHERE id = :id
		',
		'updateBlogPostsById' => '
			UPDATE !prefix!blogPosts
			SET
				title        = :title,
				shortName    = :shortName,
				modifiedTime = :modifiedTime,
				summary      = :summary,
				content      = :content,
				live         = :live
			WHERE id = :id
		',
		'insertBlogPost' => '
			INSERT INTO !prefix!blogPosts
			(blogId,title,shortName,user,postTime,modifiedTime,summary,content,live) VALUES (:blogId, :title, :shortName, :user, :postTime, :modifiedTime, :summary, :content, :live)
		',
		'updatePostShortNameById' => '
			UPDATE !prefix!blogPosts
			SET shortName = :shortName
			WHERE id = :id
		',
		'getBlogsByOwner' => '
			SELECT * FROM !prefix!blogs
			ORDER BY owner
			LIMIT :blogStart, :blogLimit
		',
		'countBlogPostsByBlogId' => '
			SELECT COUNT(id) AS count
			FROM !prefix!blogPosts
			WHERE blogID = :id
		',
		'getBlogPostsByBlogIdLimited' => '
			SELECT * FROM !prefix!blogPosts
			WHERE blogId = :blogId
			ORDER BY postTime DESC
			LIMIT :blogStart, :blogLimit
		'

	);
}

?>