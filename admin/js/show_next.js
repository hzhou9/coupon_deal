(function ( $ ) {

    $.fn.show_next = function( o ) {

    var s = $.extend({
    element: '',
    type: 'next',
    after_action: 'hide_all',
    visible_than_close: 'yes'
    }, o );

    if( s.after_action == 'hide_all' ) {
    $('body').find( s.element ).slideUp( 'fast' );
    }

    var target;

    if( s.type == 'next' ) {
    target = this.nextAll( s.element );
    } else if( s.type == 'rightnext' ) {
    target = this.next( s.element );
    } else {
    target = this.find( s.element );
    }

    if( s.visible_than_close == 'yes' && target.is(':visible') ) {
      target.slideUp( 'fast' );
    } else {
      target.slideDown( 'fast' );
    }

    };

}( jQuery ));