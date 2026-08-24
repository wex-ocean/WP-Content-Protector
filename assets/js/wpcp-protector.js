// console.log( 'WP Content Protector loaded' );
// ( function () {
//     if ( typeof wpcpSettings === 'undefined' ) return;
//     var p = wpcpSettings.protections;

//     if ( p.disable_right_click ) {
//         document.addEventListener( 'contextmenu', function ( e ) {
//             e.preventDefault();
//         } );
//     }

//     if ( p.disable_copy ) {
//         document.addEventListener( 'copy', function ( e ) {
//             e.preventDefault();
//         } );
//     }
// } )();


( function () {
    'use strict';
    if ( typeof wpcpSettings === 'undefined' ) return;
    var p = wpcpSettings.protections || {};
    var msgTimeout = null;

    function showMessage() {
        if ( ! wpcpSettings.showMessage || ! wpcpSettings.customMessage ) return;
        var el = document.querySelector( '.wpcp-message' );
        if ( ! el ) {
            el = document.createElement( 'div' );
            el.className = 'wpcp-message';
            document.body.appendChild( el );
        }
        el.textContent = wpcpSettings.customMessage;
        void el.offsetWidth;
        el.classList.add( 'wpcp-visible' );
        clearTimeout( msgTimeout );
        msgTimeout = setTimeout( function () { el.classList.remove( 'wpcp-visible' ); }, 1800 );
    }

    if ( p.disable_right_click ) {
        document.addEventListener( 'contextmenu', function ( e ) { e.preventDefault(); showMessage(); } );
    }
    if ( p.disable_copy ) {
        document.addEventListener( 'copy', function ( e ) { e.preventDefault(); showMessage(); } );
    }
    if ( p.disable_cut ) {
        document.addEventListener( 'cut', function ( e ) { e.preventDefault(); showMessage(); } );
    }
    if ( p.disable_drag_images ) {
        document.addEventListener( 'dragstart', function ( e ) {
            if ( e.target && e.target.tagName === 'IMG' ) { e.preventDefault(); showMessage(); }
        } );
    }

    document.addEventListener( 'keydown', function ( e ) {
        var key = e.key ? e.key.toUpperCase() : '';
        var blocked = false;
        if ( p.disable_devtools_f12 && key === 'F12' ) blocked = true;
        if ( ( e.ctrlKey || e.metaKey ) && ! e.shiftKey && p.disable_view_source && key === 'U' ) blocked = true;
        if ( ( e.ctrlKey || e.metaKey ) && e.shiftKey && p.disable_devtools_ctrl_shift_i && key === 'I' ) blocked = true;
        if ( ( e.ctrlKey || e.metaKey ) && e.shiftKey && p.disable_console_ctrl_shift_j && key === 'J' ) blocked = true;
        if ( blocked ) { e.preventDefault(); showMessage(); }
    } );
} )();