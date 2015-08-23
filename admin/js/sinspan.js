(function ( $ ) {

// Set In Next Span

$.fn.show_in_next = function( o ) {

  var s = $.extend({
  elem: "span"
  }, o );

  this.keyup( function( k ) {

  var t = $(this);

  t.next( s.elem ).text( t.val() );

  });

};

}( jQuery ));