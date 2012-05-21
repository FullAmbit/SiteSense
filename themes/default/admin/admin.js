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
/* used by all our admin forms to explain fields */
var secondSideBar=document.getElementById('secondSideBar');
var showDescription_saved=secondSideBar.innerHTML;
function attachDescription(tagName) {
	targetList=document.getElementsByTagName(tagName);
	for (t=0; t<targetList.length; t++) {
		if (targetList[t].className.indexOf('nsDesc')!=-1) {
			targetList[t].onfocus=function() {
				tInner=document.getElementById('ns_'+this.id).innerHTML;
			  moveTop=this.offsetTop+this.parentNode.offsetTop;
			  secondSideBar.innerHTML='<div class="description" style="top:'+moveTop+'px;">'+tInner+'</div>';
			}
			targetList[t].onblur=function() {
				secondSideBar.innerHTML=showDescription_saved;
			}
		}
	}
}
attachDescription('input');
attachDescription('select');
attachDescription('textarea');
var formList=document.getElementsByTagName('form');
for (var t=0; t<formList.length; t++) {
	formList[t].className+=' script';
}