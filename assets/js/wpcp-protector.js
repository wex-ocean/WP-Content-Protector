// console.log( 'WP Content Protector loaded' );
( function () {
    if ( typeof wpcpSettings === 'undefined' ) return;
    var p = wpcpSettings.protections;

    if ( p.disable_right_click ) {
        document.addEventListener( 'contextmenu', function ( e ) {
            e.preventDefault();
        } );
    }

    if ( p.disable_copy ) {
        document.addEventListener( 'copy', function ( e ) {
            e.preventDefault();
        } );
    }
} )();