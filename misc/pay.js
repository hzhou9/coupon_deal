$(function () {

$(document).on( 'click', 'button[name="credit_card"]', function(e){
  e.preventDefault();
  var section = $(this).parents( 'section' );
  var thatform = section.find( '.pay-credt-card-form' );
  var thisform = $(this).parents( '.pay-buttons' );
  thisform.fadeOut( 100, function(){
    thatform.fadeIn( 100 );
  });
});

$(document).on( 'change', '.choose-gateway select[name="gateway"]', function(){
   window.location = $(this).val();
});

});