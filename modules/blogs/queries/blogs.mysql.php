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
function blogs_addQueries() {
	return array(
		'getBlogById' => '
			SELECT * FROM !prefix!blogs!lang!
			WHERE id = :blogId
		',
		'getBlogByName' => '
			SELECT * FROM !prefix!blogs!lang!
			WHERE shortName = :shortName
		',
		'getTopLevelBlogByName' => '
			SELECT * FROM !prefix!blogs!lang!
			WHERE shortName = :shortName
			AND topLevel = 1
		',
		'getTopLevelBlogs' => '
			SELECT * FROM !prefix!blogs!lang!
			WHERE topLevel = 1
		',
		'getBlogPostsByIDandName' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!blog_posts!lang!
			WHERE blogId = :blogId
			AND shortName = :shortName
		',
		'countBlogPosts' => '
			SELECT count(id) AS count
			FROM !prefix!blog_posts!lang!
			WHERE blogId = :blogId
			AND live = TRUE
		',
		'getBlogPostsDelimited' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!blog_posts!lang!
			WHERE blogId = :blogId
			AND live = TRUE
			ORDER BY postTime DESC
			LIMIT :start, :count
		',
		'getAllUsers' => '
			SELECT * FROM !prefix!users ORDER BY id ASC
		',
		'getBlogPostsByTag' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!blog_posts!lang! WHERE blogId = :blogId AND tags LIKE :tags ORDER BY id DESC
		',
		'getCategoryIdByShortName' => '
			SELECT * FROM !prefix!blog_categories WHERE shortName = :shortName LIMIT 1
		',
		'getBlogPostsByCategory' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!blog_posts!lang! WHERE categoryId = :categoryId AND blogId = :blogId ORDER BY id DESC
		',
		'getBlogPostsByParentBlog' => '
			SELECT *,
			UNIX_TIMESTAMP(CONCAT(modifiedTime,"+00:00")) AS modifiedTime,
			UNIX_TIMESTAMP(CONCAT(postTime,"+00:00")) AS postTime
			FROM !prefix!blog_posts!lang! WHERE blogId = :blogId AND live = 1 ORDER BY id DESC
		',
		'getUniqueAuthorCountByBlog' => '
			SELECT COUNT(DISTINCT user) FROM !prefix!blog_posts!lang! WHERE blogId = :blogId
		',
		'getAllCategoriesByBlogId' => '
			SELECT * FROM !prefix!blog_categories!lang! WHERE blogId = :blogId ORDER BY name ASC
		',
        'getCommentById' => '
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time
			FROM !prefix!blog_comments
			WHERE id = :blogId
		',
        'getApprovedCommentsByPost' => '
			SELECT *,UNIX_TIMESTAMP(CONCAT(time,"+00:00")) AS time FROM !prefix!blog_comments
			WHERE post = :post AND approved = 1
			ORDER BY `time` ASC
		',
        'editCommentById' => '
			UPDATE !prefix!blog_comments SET author = :author, rawContent = :rawContent, parsedContent = :parsedContent, email = :email WHERE id = :id
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
			(post, authorFirstName, authorLastName, rawContent, parsedContent, email,loggedIP)
			VALUES
			(:post, :authorFirstName, :authorLastName, :rawContent, :parsedContent, :email,:loggedIP)
		',
        'getCommentsAwaitingApproval' =>'
			SELECT COUNT(*) AS commentsWaiting FROM !prefix!blog_comments WHERE post = :post AND APPROVED=0
		'
    );
}
?>