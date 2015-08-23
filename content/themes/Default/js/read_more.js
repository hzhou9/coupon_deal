(function ( $ ) {

$.fn.readmore = function( o ) {

  var s = $.extend({
  height: "25"
  }, o );

  this.each( function() {

  var t = $(this);

  if( t.height() > s.height ) {
    t.css( {'height': s.height + 'px', 'overflow': 'hidden'} );
    t.attr('data-read-more', '').css( {'cursor': 'zoom-in'} );
  }

  });

  $('[data-read-more]').click(function(e) {

    e.preventDefault();

    var d = $(this);

    if( d.height() > s.height ) {
      d.css( {'cursor': 'zoom-in'} ).animate( {'height': s.height + 'px'}, 100 );
    } else {
      d.css( {'height': 'auto', 'cursor': 'zoom-out'} );
    }

  });

};

}( jQuery ));