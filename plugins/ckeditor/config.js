/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:

	// config.language = 'fr';

	// config.uiColor = '#AADC6E';


	config.baseHref=CMSBasePath;

	config.toolbar_Full = [

    { name: 'tools',       items : [ 'Preview','Maximize','Source' ] },

    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },

    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },

    { name: 'objects',     items : [ 'Link','Unlink','Anchor','Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak' ] },

    '/',

    { name: 'styles',      items : [ 'Styles','Format','TextColor','BGColor','Bold','Italic','Underline','Strike','Subscript','Superscript','RemoveFormat' ] },

    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] }

	];

	config.resize_dir = 'vertical';

	config.startupOutlineBlocks = true;

	config.format_tags = 'p;h3;h4;h5;h6;pre;div';

	config.coreStyles_strike = { element : 'del', overrides : 'strike' };

	config.stylesSet = "default:paladin/styles.js";

	config.contentsCss = 'ckeditor/paladin/contents.css';
};
