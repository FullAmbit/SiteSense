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
			SELECT * FROM !prefix!blogs
		',
		'countBlogs' => '
			SELECT count(id) AS count
			FROM !prefix!blogs
		',
		'countBlogPosts' => '
			SELECT count(id) FROM !prefix!blog_posts
			WHERE blogId = :blogId
		',
		'getBlogById' => '
			SELECT * FROM !prefix!blogs
			WHERE id = :id
		',
		'getBlogByIdAndOwner' => '
			SELECT * FROM !prefix!blogs
			WHERE id = :id AND owner = :owner
		',
		'getBlogByPost' => '
			SELECT * FROM !prefix!blogs WHERE id IN (SELECT blogId FROM !prefix!blog_posts WHERE id = :postId)
		',
		'deleteBlogById' => '
			DELETE FROM !prefix!blogs
			WHERE id = :id
		',
		'deleteBlogPostByBlogId' => '
			DELETE FROM !prefix!blog_posts
			WHERE blogId = :id
		',
		'getBlogPostsById' => '
			SELECT * FROM !prefix!blog_posts
			WHERE id = :id
		',
		'deleteBlogPostById' => '
			DELETE FROM !prefix!blog_posts
			WHERE id = :id
		',
		'getBlogIdByName' => '
			SELECT id FROM !prefix!blogs
			WHERE shortName = :shortName
		',
		'getBlogPostIdByName' => '
			SELECT id FROM !prefix!blog_posts
			WHERE shortName = :shortName
		',
		'getBloggersByUserLevel' => '
			SELECT id,name,userLevel FROM !prefix!users
			WHERE userLevel >= '.USERLEVEL_BLOGGER.'
			ORDER BY userLevel DESC,id ASC
		',
        'getUsersWithBlogAccess' => '
			SELECT id,name FROM !prefix!users
			WHERE userLevel >= '.USERLEVEL_BLOGGER.'
			ORDER BY userLevel DESC,id ASC
		',
		'updateBlogById' => '
			UPDATE !prefix!blogs
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
			INSERT INTO !prefix!blogs
			(name,title,managingEditor,shortName,owner,allowComments,numberPerPage,description,commentsRequireLogin, topLevel, webMaster,rssOverride) VALUES (:name, :title, :managingEditor, :shortName, :owner, :allowComments, :numberPerPage, :description, :commentsRequireLogin, :topLevel, :webMaster, :rssOverride)
		',
		'updateShortNameById' => '
			UPDATE !prefix!blogs
			SET shortName = :shortName
			WHERE id = :id
		',
		'updateBlogPostsById' => '
			UPDATE !prefix!blog_posts
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
			INSERT INTO !prefix!blog_posts(
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
			UPDATE !prefix!blog_posts
			SET shortName = :shortName
			WHERE id = :id
		',
		'getBlogsByOwner' => '
			SELECT * FROM !prefix!blogs
			ORDER BY owner
			LIMIT :blogStart, :blogLimit
		',
		'getBlogsByUser' => '
			SELECT * FROM !prefix!blogs
			WHERE owner = :owner
			ORDER BY owner
			LIMIT :blogStart, :blogLimit
		',
		'countBlogPostsByBlogId' => '
			SELECT COUNT(id) AS count
			FROM !prefix!blog_posts
			WHERE blogID = :id
		',
		'getBlogPostsByBlogIdLimited' => '
			SELECT *
				FROM !prefix!blog_posts
				WHERE blogId = :blogId
				ORDER BY postTime DESC
				LIMIT :blogStart, :blogLimit
		',
		'getAllCategories' => '
			SELECT * 
				FROM !prefix!blog_categories
				ORDER BY name ASC
		',
		'getAllCategoriesByBlog' => '
			SELECT *
				FROM !prefix!blog_categories
				WHERE blogId = :blogId
				ORDER BY name ASC
		',
		'getCategoryById' => '
			SELECT * FROM !prefix!blog_categories WHERE id = :id
		',
		'editCategory' => '
			UPDATE !prefix!blog_categories SET name = :name, shortName = :shortName WHERE id = :id LIMIT 1
		',
		'deleteCategory' => '
			DELETE FROM !prefix!blog_categories WHERE id = :id
		',
		'updatePostsWithinCategory' => '
			UPDATE !prefix!blog_posts SET categoryId = 0 WHERE categoryId = :categoryId
		',
		'addCategory' => '
			INSERT INTO !prefix!blog_categories (blogId,name,shortName) VALUES (:blogId,:name,:shortName)
		',
		'getExistingShortNames' => '
			SELECT shortName FROM !prefix!blog_posts
		',
		'getExistingBlogShortNames' => '
			SELECT shortName FROM !prefix!blogs
		'
	);
}
?>