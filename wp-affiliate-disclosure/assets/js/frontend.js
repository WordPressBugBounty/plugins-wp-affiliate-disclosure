(function () {
	'use strict';

	function repositionAfterParagraph( el ) {
		var index    = parseInt( el.getAttribute( 'data-paragraph-index' ), 10 );
		var selector = el.getAttribute( 'data-parent-selector' ) || '.entry-content';

		if ( ! index || index < 1 ) {
			return;
		}

		var container = document.querySelector( selector );
		if ( ! container ) {
			return;
		}

		// Collect only direct <p> children of the container.
		var children   = container.children;
		var paragraphs = [];
		for ( var i = 0; i < children.length; i++ ) {
			if ( children[ i ].tagName.toLowerCase() === 'p' ) {
				paragraphs.push( children[ i ] );
			}
		}

		if ( ! paragraphs.length ) {
			return;
		}

		var targetIndex = Math.min( index, paragraphs.length ) - 1;
		var afterNode   = paragraphs[ targetIndex ];

		// Move the disclosure immediately after the target paragraph.
		afterNode.parentNode.insertBefore( el, afterNode.nextSibling );
	}

	function init() {
		var disclosures = document.querySelectorAll( '.wpadc-disclosure[data-paragraph-index]' );
		for ( var i = 0; i < disclosures.length; i++ ) {
			repositionAfterParagraph( disclosures[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
