<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/
function admin_blogs_addQueries() {
	return array(
        'getAllBlogs' => '
			SELECT * FROM !prefix!!lang!blogs
		',
        'countBlogs' => '
			SELECT count(id) AS count
			FROM !prefix!!lang!blogs
		',
        'countBlogPosts' => '
			SELECT count(id) FROM !prefix!!lang!blog_posts
			WHERE blogId = :blogId
		',
        'getBlogById' => '
			SELECT * FROM !prefix!!lang!blogs
			WHERE id = :id
		',
        'getBlogByIdAndOwner' => '
			SELECT * FROM !prefix!!lang!blogs
			WHERE id = :id AND owner = :owner
		',
        'getBlogByPost' => '
			SELECT * FROM !prefix!!lang!blogs WHERE id IN (SELECT blogId FROM !prefix!!lang!blog_posts WHERE id = :postId)
		',
        'deleteBlogById' => '
			DELETE FROM !prefix!!lang!blogs
			WHERE id = :id
		',
        'deleteBlogPostByBlogId' => '
			DELETE FROM !prefix!!lang!blog_posts
			WHERE blogId = :id
		',
        'getBlogPostsById' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!!lang!blog_posts
			WHERE id = :id
		',
        'deleteBlogPostById' => '
			DELETE FROM !prefix!!lang!blog_posts
			WHERE id = :id
		',
        'getBlogIdByName' => '
			SELECT id FROM !prefix!!lang!blogs
			WHERE shortName = :shortName
		',
        'getBlogPostIdByName' => '
			SELECT id FROM !prefix!!lang!blog_posts
			WHERE shortName = :shortName
		',
        'getUsersWithBlogAccess' => '
		',
        'updateBlogById' => '
			UPDATE !prefix!!lang!blogs
			SET
				shortName            =   :shortName,
				name                 =   :name,
				title				 =	 :title,
				owner                =   :owner,
				allowComments		 =	 :allowComments,
				numberPerPage        =   :numberPerPage,
				description          =   :description,
				commentsRequireLogin =   :commentsRequireLogin,
				topLevel             =   :topLevel,
				managingEditor = :managingEditor,
				webMaster = :webMaster,
				rssOverride = :rssOverride
			WHERE id = :id
		',
        'insertBlog' => '
			INSERT INTO !prefix!!lang!blogs
			(name,title,managingEditor,shortName,owner,allowComments,numberPerPage,description,commentsRequireLogin, topLevel, webMaster,rssOverride) VALUES (:name, :title, :managingEditor, :shortName, :owner, :allowComments, :numberPerPage, :description, :commentsRequireLogin, :topLevel, :webMaster, :rssOverride)
		',
        'updateShortNameById' => '
			UPDATE !prefix!!lang!blogs
			SET shortName = :shortName
			WHERE id = :id
		',
        'updateBlogPostsById' => '
			UPDATE !prefix!!lang!blog_posts
			SET
				categoryId = :categoryId,
				title        = :title,
				name		 = :name,
				shortName    = :shortName,
				rawSummary      = :rawSummary,
				parsedSummary	= :parsedSummary,
				rawContent      = :rawContent,
				parsedContent	= :parsedContent,
				live         = :live,
				tags         = :tags,
				allowComments = :allowComments
			WHERE id = :id
		',
        'insertBlogPost' => '
			INSERT INTO !prefix!!lang!blog_posts(
				blogId,
				categoryId,
				title,
				name,
				shortName,
				user,
				postTime,
				rawSummary,
				parsedSummary,
				rawContent,
				parsedContent,
				live,
				tags,
				allowComments
			) VALUES (
				:blogId,
				:categoryId,
				:title,
				:name,
				:shortName,
				:user,
				CURRENT_TIMESTAMP,
				:rawSummary,
				:parsedSummary,
				:rawContent,
				:parsedContent,
				:live,
				:tags,
				:allowComments
			)',
        'updatePostShortNameById' => '
			UPDATE !prefix!!lang!blog_posts
			SET shortName = :shortName
			WHERE id = :id
		',
        'getBlogsByOwner' => '
			SELECT * FROM !prefix!!lang!blogs
			ORDER BY owner
			LIMIT :blogStart, :blogLimit
		',
        'getBlogsByUser' => '
			SELECT * FROM !prefix!!lang!blogs
			WHERE owner = :owner
			ORDER BY owner
			LIMIT :blogStart, :blogLimit
		',
        'countBlogPostsByBlogId' => '
			SELECT COUNT(id) AS count
			FROM !prefix!!lang!blog_posts
			WHERE blogID = :id
		',
        'getBlogPostsByBlogIdLimited' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!!lang!blog_posts
			WHERE blogId = :blogId
			ORDER BY postTime DESC
			LIMIT :blogStart, :blogLimit
		',
        'getAllCategories' => '
			SELECT *
				FROM !prefix!!lang!blog_categories
				ORDER BY name ASC
		',
        'getAllCategoriesByBlog' => '
			SELECT *
				FROM !prefix!!lang!blog_categories
				WHERE blogId = :blogId
				ORDER BY name ASC
		',
        'getCategoryById' => '
			SELECT * FROM !prefix!!lang!blog_categories WHERE id = :id
		',
        'editCategory' => '
			UPDATE !prefix!!lang!blog_categories SET name = :name, shortName = :shortName WHERE id = :id LIMIT 1
		',
        'deleteCategory' => '
			DELETE FROM !prefix!!lang!blog_categories WHERE id = :id
		',
        'updatePostsWithinCategory' => '
			UPDATE !prefix!!lang!blog_posts SET categoryId = 0 WHERE categoryId = :categoryId
		',
        'addCategory' => '
			INSERT INTO !prefix!!lang!blog_categories (blogId,name,shortName) VALUES (:blogId,:name,:shortName)
		',
        'getExistingShortNames' => '
			SELECT shortName FROM !prefix!!lang!blog_posts
		',
        'getExistingBlogShortNames' => '
			SELECT shortName FROM !prefix!!lang!blogs
		',
        'getCommentById' => '
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time FROM !prefix!blog_comments
			WHERE id = :id
		',
        'getApprovedCommentsByPost' => '
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time FROM !prefix!blog_comments
			WHERE post = :post AND approved = 1
			ORDER BY `time` ASC
		',
        'getDisapprovedCommentsByPost' => '
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time FROM !prefix!blog_comments
			WHERE post = :post AND approved = -1
			ORDER BY `time` ASC
		',
        'editCommentById' => '
			UPDATE !prefix!blog_comments SET authorFirstName = :authorFirstName, authorLastName = :authorLastName, rawContent = :rawContent, parsedContent = :parsedContent, email = :email WHERE id = :id
		',
        'deleteCommentById' => '
			DELETE FROM !prefix!blog_comments WHERE id = :id
		',
        'countCommentsByPost' => '
			SELECT count(id) AS count
			FROM !prefix!blog_comments
			WHERE post = :post
		',
        'makeComment' => '
			INSERT INTO !prefix!blog_comments
			(post, authorFirstName, authorLastName, content,email,loggedIP)
			VALUES
			(:post, :authorFirstName, :authorLastName, :content,:email,:loggedIP)
		',
        'getCommentsAwaitingApproval' =>'
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time FROM !prefix!blog_comments
			WHERE post = :post AND approved = 0
			ORDER BY `time` ASC
		',
        'approveComment' => '
			UPDATE !prefix!blog_comments SET approved = 1 WHERE id = :id LIMIT 1
		',
        'disapproveComment' => '
			UPDATE !prefix!blog_comments SET approved = -1 WHERE id = :id LIMIT 1
		'
    );
}
?>