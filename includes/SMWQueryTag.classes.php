<?php

if ( !defined( 'MEDIAWIKI' ) ) {
       echo 'Not a valid entry point';
       exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
       echo 'This extension requires Semantic MediaWiki to be installed.';
       exit( 1 );
}

/**
 * This class handles SMWQueries.
 */
class SMWQueryTag {


	public static function SMWQueryTagRender( $text, array $args, Parser $parser, PPFrame $frame ) {
	
		global $wgOut;
		$wgOut->addModules( 'ext.SMWQueryTag' );
	
		// First output
		$output = $parser->recursiveTagParse( $text, $frame );
	
		$tag = "div";
		$atrprops = "";
		$extraclass = "";
		$extraid = "";
		$separator = "";
		$stop = "";
	
		if ( isset($args['tag']) ) {
			$tag = $args['tag'];	
		}
	
		if ( isset($args['props']) ) {
			
			$atrprops = "data-SMWQueryTag-props='".$args['props']."'";
		 }
	
	
		if ( isset($args['class']) ) {
			
			$extraclass = $args['class'];
		 }
	
		if ( isset($args['id']) ) {
			
			$extraid = $args['id'];
		 }
		
		if ( isset($args['separator']) ) {
			
			$separator = "data-SMWQueryTag-separator='".$args['separator']."'";
		 }
		
		if ( isset($args['template']) ) {
			
			$template = "data-SMWQueryTag-template='".$args['template']."'";
		 }
		
		// Auto
		
		if ( isset($args['stop']) ) {
			
			$stop = "data-SMWQueryTag-stop='".$args['stop']."'";
		 }
		
	
		$final = "<".$tag." ".$atrprops." ".$separator." class='SMWQueryTag ".$extraclass."' id='".$extraid."' ".$template." ".$stop.">".$output."</".$tag.">";
		
		return ( $final );
	
	}
	
	
	public static function SMWQueryListRender( $text, array $args, Parser $parser, PPFrame $frame ) {
	
		global $wgOut;
		$wgOut->addModules( 'ext.SMWQueryTag' );
	
		// First output
		// $output = $parser->recursiveTagParse( $text, $frame );
		$output = $text;
		
		$tag = "div";
		$atrprops = "";
		$extraclass = "";
		$extraid = "";
		$separator = "";
	
		if ( isset($args['tag']) ) {
			$tag = $args['tag'];	
		}
		
		if ( isset($args['props']) ) {
			
			$atrprops = "data-SMWQueryList-props='".$args['props']."'";
		 }
	
		if ( isset($args['class']) ) {
			
			$extraclass = $args['class'];
		 }
	
		if ( isset($args['id']) ) {
			
			$extraid = $args['id'];
		 }
		
		if ( isset($args['separator']) ) {
			
			$separator = "data-SMWQueryList-separator='".$args['separator']."'";
		 }
		
		if ( isset($args['mainlabel']) ) {
			
			$mainlabel = "data-SMWQueryList-mainlabel='true'";
		 }
				
		if ( isset($args['limit']) ) {
			
			$limit = "data-SMWQueryList-limit='".$args['limit']."'";
		 }
				
				
		$final = "<".$tag." ".$atrprops." ".$mainlabel." ".$limit." ".$separator." class='SMWQueryList ".$extraclass."' id='".$extraid."' >".$output."</".$tag.">";
		
		return ( $parser->insertStripItem( $final, $parser->mStripState ) );
	
	}


	public static function returnProperty ( $title_text, $listprops, $separator="-", $template=null ) {

		$props = array();
		
		$props = explode( ",", $listprops );
		
		$values = self::getStatus( $title_text, $props );
	
		if ( count( $values ) > 0 ) {
			
			if (! $template ) {
					
				$sep = "-";
				if ( $separator ) {
				      $sep = $separator;
				} 
			
				return ( implode( $sep, $values ) );
			}
			
			else {
				
				#print_r($values);
				
				$count = preg_match_all ( "/(\#\d+)/", $template, $matches ) ;
				
				if ( $count > 0 ) {

					foreach ( $matches[0] as $match ) {
						
						#echo $match;
									   
						$num = str_replace( "#", "", $match );
											   
						if ( isset( $values[$num] ) ) {
						
							$matchre = "/\#".$num."/";
						
							$template = preg_replace ( $matchre, $values[$num], $template );
						
						}
					}
					
					return $template;
				 
				} else {
				
					return( null );   
				
				}
				
				
			}

		 } else {
		      return( null );
		 }

	}
	
	
	public static function returnList ( $query, $listprops, $separator="-", $mainlabel=true, $limit=100 ) {
		
		$props = array();
		
		if ($listprops != '') {
			
			$props = explode( ",", $listprops );
		}
		
		$sep = "-";
		if ( $separator ) {
			$sep = $separator;
		}
		
		$main = false;
		
		if ( $mainlabel ) {
			$main = true;
		}
		
		$values = self::getList( $query, $props, $main, $limit );

		if ( count( $values ) > 0 ) {
			return ( implode($sep, $values) );

		} else {
			return( null );
		}
		
	}
	
	

	/**
	 * Returns an array of properties based on $query_word
	 * @param $query_word String: the property that designate the users to notify.
	 */
	static function getStatus( $title_text, $properties_to_display ) {

		// Array of values to return
		$values_gr = array();
		$values_arr = array();

		// get the result of the query "[[$title]][[$query_word::+]]"
		$results = self::getQueryResults( "[[$title_text]]", $properties_to_display, false );

		// In theory, there is only one row
		while ( $row = $results->getNext() ) {
			for ($i=1; $i < count($row); $i++) {
				array_push($values_gr, $row[$i]);
			}
		}
		
		// If not any row, do nothing
		if ( count( $values_gr ) > 0 ) {

			foreach ($values_gr as $values_elem) {
		
				while ( $value_wiki = $values_elem->getNextObject() ) {

					$value = $value_wiki->getWikiValue();

					array_push( $values_arr, $value );
				}
			}
		}


		return $values_arr;
	}

      	/**
	 * Returns an array of properties based on $query
	 * @param $query String: Query to be done.
	 * @param $properties_to_display array(String): array of property names to display
	 */
	static function getList( $query, $properties_to_display, $main, $limit ) {

		// Array of values to return
		$values_gr = array();
		$values_arr = array();

		// get the result of the query "[[$title]][[$query_word::+]]"
		$results = self::getQueryResults( "$query", $properties_to_display, $main, $limit );

		// In theory, there is only one row
		while ( $row = $results->getNext() ) {
			for ($i=1; $i < count($row); $i++) {
				array_push($values_gr, $row[$i]);
			}
		}
		
		// If not any row, do nothing
		if ( count( $values_gr ) > 0 ) {

			foreach ($values_gr as $values_elem) {
		
				while ( $value_wiki = $values_elem->getNextObject() ) {

					$value = $value_wiki->getWikiValue();

					array_push( $values_arr, $value );
				}
			}
		}


		return $values_arr;
	} 

	/**
	 * This function returns to results of a certain query
	 * Thank you Yaron Koren for advices concerning this code
	 * @param $query_string String : the query
	 * @param $properties_to_display array(String): array of property names to display
	 * @param $display_title Boolean : add the page title in the result
	 * @return TODO
	 */
	static function getQueryResults( $query_string, $properties_to_display, $display_title, $limit=100 ) {
		// We use the Semantic MediaWiki Processor
		// $smwgIP is defined by Semantic MediaWiki, and we don't allow
		// this file to be sourced unless Semantic MediaWiki is included.
		global $smwgIP;
		include_once( $smwgIP . "/includes/SMW_QueryProcessor.php" );

		$params = array();
		$params['limit'] = $limit;
		
		$inline = true;
		$printlabel = "";
		$printouts = array();
				
		// add the page name to the printouts
		if ( $display_title ) {
		     
		     $to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_THIS, $printlabel );
		     array_push( $printouts, $to_push );

		}

		// Push the properties to display in the printout array.
		foreach ( $properties_to_display as $property ) {

			if ( class_exists( 'SMWPropertyValue' ) ) { // SMW 1.4
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, SMWPropertyValue::makeProperty( $property ) );
			} else {
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, Title::newFromText( $property, SMW_NS_PROPERTY ) );
			}
			array_push( $printouts, $to_push );
		}
		


		if ( version_compare( SMW_VERSION, '1.6.1', '>' ) ) {
			SMWQueryProcessor::addThisPrintout( $printouts, $params );
			$params = SMWQueryProcessor::getProcessedParams( $params, $printouts );
			$format = null;
		}
		else {
			$format = 'auto';
		}
		
		$query = SMWQueryProcessor::createQuery( $query_string, $params, $inline, $format, $printouts );
		$results = smwfGetStore()->getQueryResult( $query );

		
		return $results;
	}


}
