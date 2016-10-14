/* jshint strict:true, browser:true */
/** Functions **/
( function( $, mw ) {

	jQuery( function( $ ) {
	
		$('.SMWQueryTag').each(function() {
			
			if ($(this).attr('data-SMWQueryTag-stop') !== undefined) {
				// attribute exists
			} else {
				processSMWQueryTag(this);
			}
			
		});
	
		$('.SMWQueryList').each(function() {
			processSMWQueryList(this);
		});
	});


	$( document ).on( '.SMWQueryTag', 'DOMNodeInserted', function(event) {
		
		if ($(this).attr('data-SMWQueryTag-stop') !== undefined) {
			// attribute exists
		} else {
			processSMWQueryTag(this);
		}
		
	});
	
	$( document ).on( '.SMWQueryLive', 'DOMNodeInserted', function(event) {
		processSMWQueryList(this);
	});


	function processSMWQueryTag ( query ) {
	
		var page = $(query).text();
		var props = $(query).attr("data-SMWQueryTag-props");
	
		// Substitute spaces for dash
		props = props.replace(" ", "_");
	
		var sep = $(query).attr("data-SMWQueryTag-separator");
		var template = $(query).attr("data-SMWQueryTag-template");
		
		if (page && props) {
	
			if (!sep) { sep = "-"; }
	
			if (! $(query).hasClass('SMWQueryTag_changed') ) {
	
				$.get( mw.util.wikiScript(), {
					format: 'json',
					action: 'ajax',
					rs: 'SMWQueryTag::returnProperty',
					rsargs: [page, props, sep, template] // becomes &rsargs[]=arg1&rsargs[]=arg2...
				}, function(data) {
	
					$(query).addClass("SMWQueryTag_changed");
					$(query).empty();
					$(query).append(data);
	
				});
			}
	
		}
	
	}
	
	function processSMWQueryList ( query ) {
	
		var querystr = $(query).text();
		
		var props = "";
		var mainlabel = "";
		var limit = 100;
		
		if ($(query).attr("data-SMWQueryList-props")) {
			props = $(query).attr("data-SMWQueryList-props");
		}
		
		if ($(query).attr("data-SMWQueryList-mainlabel")) {
			mainlabel = $(query).attr("data-SMWQueryList-mainlabel");
		}
	
		if ($(query).attr("data-SMWQueryList-limit")) {
			limit = $(query).attr("data-SMWQueryList-limit");
		}
				
		// Substitute spaces for dash
		props = props.replace(" ", "_");
	
		var sep = $(query).attr("data-SMWQueryList-separator");
		
		
		if (querystr) {
	
			if (!sep) { sep = "-"; }
	
			if (! $(query).hasClass('SMWQueryList_changed') ) {
	
				$.get( mw.util.wikiScript(), {
					format: 'json',
					action: 'ajax',
					rs: 'SMWQueryTag::returnList',
					rsargs: [querystr, props, sep, mainlabel, limit] // becomes &rsargs[]=arg1&rsargs[]=arg2...
				}, function(data) {
	
					$(query).addClass("SMWQueryList_changed");
					$(query).empty();
					$(query).append(data);
	
				});
			}
	
		}
	
	} 

})( jQuery, mediaWiki );