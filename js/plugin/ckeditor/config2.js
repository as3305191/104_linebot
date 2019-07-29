/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	
	// %REMOVE_START%
	// The configuration options below are needed when running CKEditor from source files.
	config.plugins = 'basicstyles,button,colorbutton,colordialog,contextmenu,toolbar,font,format,iframe,wysiwygarea,image,justify,menubutton,link,list,sourcearea';
	config.skin = 'bootstrapck';
	config.format_tags = 'p;h1;h2;h3;pre';
	// %REMOVE_END%
	config.height = '100px';

	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
