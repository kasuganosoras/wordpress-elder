( function () {
	'use strict';

	if ( typeof helloHaha === 'undefined' || ! helloHaha.quote ) {
		return;
	}

	var elements = document.getElementsByClassName( 'haha' );

	for ( var i = 0; i < elements.length; i++ ) {
		elements[ i ].textContent = helloHaha.quote;
	}
}() );
