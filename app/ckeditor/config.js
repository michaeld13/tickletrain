/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.contentsCss = 'CustomFonts/fonts.css';
	config.font_names = 'Calibri font/Calibri-font;' + config.font_names;
	//config.font_names = 'Source Serif Pro Bold/SourceSerifPro-Bold;' + config.font_names;
};
