( function () {
	'use strict';

	var timer = document.getElementById( 'hello-haha-timer' );

	if ( ! timer ) {
		return;
	}

	var seconds = 2;

	window.setInterval( function () {
		timer.textContent = seconds + 's';
		seconds += 1;
	}, 500 );
}() );
