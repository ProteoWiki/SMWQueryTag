<?php

#Check to see if we're being called as an extension or directly
if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

if ( version_compare( $GLOBALS['wgVersion'], '1.27c', '>' ) ) {
	if ( function_exists( 'wfLoadExtension' ) ) {
		wfLoadExtension( 'SMWQueryTag' );
		// Keep i18n globals so mergeMessageFileList.php doesn't break
		$GLOBALS['wgMessagesDirs']['SMWQueryTag'] = __DIR__ . '/i18n';
		$GLOBALS['wgExtensionMessagesFiles']['SMWQueryTagMagic'] = __DIR__ . '/SMWQueryTag.i18n.magic.php';

		/* wfWarn(
			'Deprecated PHP entry point used for Corecosting extension. ' .
			'Please use wfLoadExtension instead, ' .
			'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
		); */
		return true;
	}
}




