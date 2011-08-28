
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