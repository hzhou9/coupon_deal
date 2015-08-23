(function ( $ ) {

// Search data

$.fn.data_search = function( o ) {

  var s = $.extend({
  search: "../?ajax=search_store"
  }, o );

var $this = this;
var $input = $this.find('input');
var $a = $this.find('a');

function search() {

  $this.find('.search_box').html('<div style="min-height: 40px; margin-top: 17px; text-align: center;"><img src="theme/ajax.gif" alt="Wait ..." /></div>');

  $.post( s.search, {search: $this.find('input').val()}, function( data ) {

  var text = '<ul>';

  $.each(data, function(id, ajax) {
    text += '<li data-id="'+ id +'"' + ( ajax["catID"] !== undefined ? ' data-set-catID="' + ajax.catID + '"' : '' ) + '>' + ajax.name + '</li>';
  });

  text += '</ul>';

  $this.find('.search_box').html(text);

  $this.find('li').click(function() {

    $input.val($(this).attr('data-id'));
    $a.text( 'R' );
    $this.find('.search_box').remove();

  });

  }, "json");

}

$a.click(function() {

if( $this.find('.search_box').length > 0 ) {

  $a.text( 'S' );
  $this.find('.search_box').remove();

} else {

  $a.text( 'R' );
  $this.append('<div class="search_box"></div>');
  search();

}

});

$input.keyup(function() {

if( $(this).val() == '' ) {
  $a.text( 'S' );
  $this.find('.search_box').remove();
    return false;
}

if( $this.find('.search_box').length > 0 ) {

  search();

} else {

  if( $.isNumeric( $(this).val() ) ) {
    return false;
  }

    $a.text( 'R' );

  $this.append('<div class="search_box"></div>');
  search();

}

});

};

}( jQuery ));