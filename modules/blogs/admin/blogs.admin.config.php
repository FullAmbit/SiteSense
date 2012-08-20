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

function blogs_admin_config($data,$db) {
	$data->permissions['blogs']=array(
        'access'            => $data->phrases['core']['permission_blogs_access'],
        'accessOthers'      => $data->phrases['core']['permission_blogs_accessOthers'],

        'blogAdd'           => $data->phrases['core']['permission_blogs_blogAdd'],
        'blogEdit'          => $data->phrases['core']['permission_blogs_blogEdit'],
        'blogDelete'        => $data->phrases['core']['permission_blogs_blogDelete'],
        'blogList'          => $data->phrases['core']['permission_blogs_blogList'],

        'categoryAdd'       => $data->phrases['core']['permission_blogs_categoryAdd'],
        'categoryEdit'      => $data->phrases['core']['permission_blogs_categoryEdit'],
        'categoryDelete'    => $data->phrases['core']['permission_blogs_categoryDelete'],
        'categoryView'      => $data->phrases['core']['permission_blogs_categoryView'],

        'commentAdd'        => $data->phrases['core']['permission_blogs_commentAdd'],
        'commentEdit'       => $data->phrases['core']['permission_blogs_commentEdit'],
        'commentDelete'     => $data->phrases['core']['permission_blogs_commentDelete'],
        'commentApprove'    => $data->phrases['core']['permission_blogs_commentApprove'],
        'commentDisapprove' => $data->phrases['core']['permission_blogs_commentDisapprove'],
        'commentList'       => $data->phrases['core']['permission_blogs_commentList'],

        'postAdd'           => $data->phrases['core']['permission_blogs_postAdd'],
        'postEdit'          => $data->phrases['core']['permission_blogs_postEdit'],
        'postDelete'        => $data->phrases['core']['permission_blogs_postDelete'],
        'postList'          => $data->phrases['core']['permission_blogs_postList']
    );
    
	if(checkPermission('access','blogs',$data)) {
		$data->admin['menu'][]=array(
			'category'  => $data->phrases['core']['siteManagement'],
			'command'   => 'blogs/list',
			'name'      => $data->phrases['core']['blogs'],
			'sortOrder' => 4
		);
	}
}
?>