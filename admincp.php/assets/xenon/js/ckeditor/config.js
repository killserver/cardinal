/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
// разрешить теги <style>
CKEDITOR.config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
// разрешить теги <script>
CKEDITOR.config.protectedSource.push(/<(script)[^>]*>.*<\/script>/ig);
// разрешить php-код
CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
// разрешить любой код: <!--dev-->код писать вот тут<!--/dev-->
CKEDITOR.config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);