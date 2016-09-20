<?php
class CorecostingHooks {

	// Hook our callback function into the parser
	public static function onParserFirstCallInit( $parser ) {
	
		$parser->setHook( 'SMWQueryTag', 'SMWQueryTag::SMWQueryTagRender' );
		$parser->setHook( 'SMWQueryList', 'SMWQueryTag::SMWQueryListRender' );

		return true;
	}

}