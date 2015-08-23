$(function () {


openwindow = function( URL, width, height, scrollbar ) {
  window.open( URL, '_blank', 'width=' + width + ', height=' + height + ', scrollbars=' + scrollbar );
}


$('.description').readmore();

$(document).on( 'click', '.user-menu a:first', function(e){
  e.preventDefault();
  var details = $(this).parent().find('ul');
  if( details.is(':visible') ) {
    details.fadeOut(150);
  } else {
    details.fadeIn(150);
  }
});


$(document).on( 'click', 'li.swcat', function(e){
  var cont = $('header:first');
  cont.find('li').hide();
  cont.find('.close').show();
  $('body').append('<div class="mask" />');
  $('.top-categories').slideDown(100);
});


$(document).on( 'click', 'li.close, .mask', function(e){
  var cont = $('header:first');
  cont.find('li').show();
  cont.find('.close').hide();
  $('.top-categories').slideUp(100);
  $('.mask').remove();
});


$(document).on( 'click', 'article .more_details', function(e){
  e.preventDefault();
  var details = $(this).parents('article').find('.details');
  if( details.is(':visible') ) {
    details.slideUp(50);
    $(this).text('More Details');
  } else {
    details.slideDown(50);
    $(this).text('Less Details');
  }
});


$(document).on( 'click', 'article .share', function(e){
  e.preventDefault();
  var details = $(this).parents('article').find('.share-coupon');
  if( details.is(':visible') ) {
    details.slideUp(250);
  } else {
    details.slideDown(250);
  }
});


$(document).on( 'click', '.code .infos a', function(e){
  e.preventDefault();
  var details = $(this).next('ul');
  if( details.is(':visible') ) {
    details.fadeOut(150);
  } else {
    details.fadeIn(150);
  }
});


$(document).on( 'click', 'a.write_review', function(e){
  e.preventDefault();
  var t = $(this);
  var details = t.next('div');
  if( details.not(':visible') ) {
    details.slideDown(250, function(){
      t.hide();
    });
  }
});


$(document).on( 'click', '.twopl .redeem-btn', function(e){
  e.preventDefault();
  var details = $(this).prev('form');
  if( details.is(':visible') ) {
    details.slideUp(250);
  } else {
    details.slideDown(250);
    $(this).hide();
  }
});


$(document).on( 'click', '.twopl .redeem-form .cancel', function(e){
  e.preventDefault();
  var details = $(this).parent('form');
  details.fadeOut(250, function(){
    details.next().fadeIn(500);
  });
});


$(document).on( 'click', '.pointsinfo .faq > a', function(e){
  e.preventDefault();
  var details = $(this).next();
  if( details.is(':visible') ) {
    details.slideUp(250);
  } else {
    details.slideDown(250);
  }
});


$('[data-ttip]').mouseenter(function() {
  var t = $(this);
  var top = t.width();
  t.append( '<div class="tooltip" style="left: '+ (t.width() + 10) +'px;">' + t.attr('data-ttip') + '</div>' );
}).mouseleave(function(){
  $(this).find('.tooltip').remove();
});


$(document).on( 'scroll', function(){
  var a = $(this).scrollTop();
  if( a > 160 ) {
    $('body > header:first').css('position', 'fixed');
  } else {
    $('body > header:first').css({ 'position': 'absolute', 'top': 0 });
  }
});


$(document).on( 'click', '[data-vw-goto]', function(){
  var t = $(this);
  t.text();
  t.removeClass('codeviewanim');
  t.animate({ width: "toggle" }, 1000, function(){
    window.open( t.attr('data-vw-goto'), '_blank' );
  });
});


$(document).on( 'submit', '[data-submit="footer-ajax"]', function(e){
  e.preventDefault();
  var t = $(this), section = $(this).parent(), button = $(this).find('button');
  $.post( t.attr('data-ajax'), $(this).find('input'), function(){
   // process submit
   section.find('.success,.error').remove();
  }, "json").done(function( state ){
    if( state.state == 'success' ) {
      section.prepend('<div class="success">' + state.message + '</div>');
      t.find( 'input[type="email"]' ).prop( 'disabled', true );
      button.prop( 'disabled', true ).addClass( 'active' );
    } else {
      section.prepend('<div class="error">' + state.message + '</div>');
      button.prop( 'disabled', false ).removeClass( 'active' );
    }
  });
});


var client = new ZeroClipboard( document.getElementById("copy-button") );
client.on('aftercopy', function(event) {
  // nothing :(
});


});