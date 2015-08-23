$(document).ready(function() {


$('.sinspan').show_in_next();


$(document).on( 'click', '.main-nav > ul.nav li a', function( e ){

  if( $(this).next( '.subnav' ).length > 0 ) {
  e.preventDefault();
  $(this).show_next( { element: '.subnav' } );
  }

});


$(document).on( 'click', '.title .options > a', function( e ){

  e.preventDefault();

  var menu = $(this).parent().children( 'ul' );

  if( menu.is(':visible') ) {
    $(this).removeClass('btn-active');
    menu.slideUp( 80 );
  } else {
  $(this).addClass('btn-active');
    menu.slideDown( 80 );
  }

});


$(document).on( 'click', '.title .options a.more_fields', function( e ){

  e.preventDefault();
  var t = $(this), ul = $(this).parents('ul'), options = $(this).parents('.options');
  options.find('a:first').removeClass('btn-active');

  options.parents( '.title' ).nextAll( '.form-table' ).find('.row:hidden').slideDown(80);

  ul.slideUp( 80, function(){
    if( ul.find( 'li' ).length === 1 ) {
      options.remove();
    } else {
      t.parent().remove();
    }
  });

});


$(document).on( 'click', '.top-nav > ul.left-top li a:first', function( e ){

  e.preventDefault();

  $('body').show_next( { type: '', element: '.main-nav' } );

});


$(document).on( 'click', '.elements-list input[checkall]', function(){

  if( $(this).is( ':checked' ) ) {
  $(this).parents( '.elements-list' ).find( 'li > input[type="checkbox"]:enabled' ).prop( 'checked', true );
  } else {
  $(this).parents( '.elements-list' ).find( 'li > input[type="checkbox"]' ).prop( 'checked', false );
  }

});


$(document).on( 'change', '.elements-list input[checkall], .elements-list > li input[type="checkbox"]', function(){

  var count = $(this).parents('.elements-list').find('li > input[type="checkbox"]:checked').length;
  if( count > 0 ) {
  $(this).parents( '.elements-list' ).find( '.bulk_options' ).slideDown( 'fast' );
  } else {
  $(this).parents( '.elements-list' ).find( '.bulk_options' ).slideUp( 'fast' );
  }

});


$(document).on( 'click', '.info-bar > a.show_theme_desc', function( e ){

e.preventDefault();

var themedesc = $(this).parents( '.info-bar' ).find( '.theme-desc' );

  if( themedesc.is( ':visible' ) ) {
  $(this).children( 'span' ).text( '↙' );
  themedesc.slideUp( 'fast' );
  } else {
  $(this).children( 'span' ).text( '↗' );
  themedesc.slideDown( 'fast' );
  }

});


$( document ).on( 'submit', '#upload-theme-form form', function(){

  var form = $(this).parents( '#upload-theme-form' );

  form.hide();
  form.next( '#process-theme' ).show();

});


$( document ).on( 'submit', '#upload-plugin-form form', function(){

  var form = $(this).parents( '#upload-plugin-form' );

  form.hide();
  form.next( '#process-plugin' ).show();

});

$( document ).on( 'change', 'input[name="shn-site"], input[name="shn-expiration"]', function(){

  $(this).parents( '.row' ).show_next( { after_action: '',  type: 'rightnext', element: '.row' } );

});


$( document ).on( 'click', '#modify_mt_but', function(e){

  e.preventDefault();
  $('body').show_next( { after_action: '',  type: '', element: '#modify_mt' } );

});


$( document ).on( 'change', 'input[name="coupon_ownlink"], input[name="product_ownlink"]', function(){

  $(this).show_next( { after_action: '', element: 'input' } );

});


$( document ).on( 'click', '#ban_fast_choice > a', function(e){

  e.preventDefault();

  var json = jQuery.parseJSON( $(this).attr('data') );

  var date = new Date();

  date.setMonth( date.getMonth()+1 );

  switch( json.interval ) {
    case 'day':
    date.setHours( date.getHours() + 24*json.nr );
    break;
    case 'week':
    date.setHours( date.getHours() + 24*7*json.nr );
    break;
    case 'month':
    date.setMonth( date.getMonth() + json.nr );
    break;
    case 'year':
    date.setMonth( date.getMonth() + 12*json.nr );
    break;
  }

  if( date.getMonth() == 0 ) {
    date.setMonth(1);
  }

  var nd = date.getFullYear() + '-' + ( '0' + ( date.getMonth() ) ).slice(-2) + '-' + ( '0' + date.getDate() ).slice(-2);
  $(this).parents( '.row' ).prev('.row').find( 'input[type="date"]' ).val( nd );

});


$('[data-search="store"]').each(function(){

  $(this).data_search();

});


$('[data-search="user"]').each(function(){

  $(this).data_search({search: '../?ajax=search_user'});

});


$(document).on( 'click', '[data-set-catID]', function() {

  $('select').val($(this).attr('data-set-catID'));

});


$(document).on( 'click', '#fileds_table ~ a:first', function( e ){

e.preventDefault();

  var ul = $(this).prev( 'ul' );
  var head = ul.children( '.head' );
  var row = ul.children( '#fileds_table_new' ).html();
  var lis = ul.find( '.added_field' ).length;

  ul.append( '<li class="added_field">' + row + '</li>' );

  if( !head.is(':visible') ) {
    head.show();
  }

});


$(document).on( 'click', '#fileds_table li.added_field a:last-child', function( e ){

  e.preventDefault();
  var ul = $(this).parents( 'ul' );
  var head = ul.children( '.head' );
  var lis = ul.find( '.added_field' ).length;

  $(this).parents('li').remove();

  if( lis <= 1 ) {
    head.hide();
  }

});


$(document).on( 'click', '[data-delete-msg]', function(){

  if( !confirm( $(this).attr( 'data-delete-msg' ) ) ) {
    return false;
  }

});


$(document).on( 'click', 'section.el-row h2 > a', function( e ) {

  e.preventDefault();
  var t = $(this);
  var body = $(this).parents( 'section' ).find( '.el-row-body' );
  var type = 0;

  if( body.is(':visible') ) {
    t.text('S');
    body.slideUp( 50 );
    type = 1;
  } else {
    t.text('R');
    body.slideDown( 50 );
    type = 0;
  }

  $.post( 'ajax/set_sessions.php', {ses: t.attr('data-set'), type: type} );

});


$(document).on( 'submit', '#post-chat form', function( e ) {

  e.preventDefault();
  var it = $(this).find( '[name="text"]' );
  var csrf = $(this).find( '[name="chat_csrf"]' ).val();
  var text = it.val();
  var ul = $( '#chat-msgs-list' );
  var ul_val = ul.html();

  if( text == '' ) {
    return false;
  }

  ul.html( '<li style="line-height: 60px; text-align: center;"><img src="theme/ajax.gif" alt="" /></li>' );
  it.val( '' );

  $.post( '?ajax=post-chat-msg.php', {msg: text, csrf: csrf }, function( a ){

  if( a.answer ) {

  $.get( '?ajax=chat-msgs.php', function( msg ){

  var newul = '';

  $.each( msg, function( k, v ) {
    newul += '<li> <div style="display: table;"> <img src="' + v.avatar + '" alt="" /> <div class="info-div"><h2>' + v.name + ' <span class="fright date">' + v.gfdate + '</span></h2> <div class="info-bar">' + v.text + '</div> </div></div> </li>';
  });

  ul.html( newul );

  }, "json" );

  } else {

    ul.html( ul_val );

  }

  }, "json" );

});


$(document).on( 'click', '#post-chat form a', function( e ) {

  e.preventDefault();
  var ul = $( '#chat-msgs-list' );

  ul.html( '<li style="line-height: 60px; text-align: center;"><img src="theme/ajax.gif" alt="" /></li>' );

  $.get( '?ajax=chat-msgs.php', function( msg ){

  var newul = '';

  $.each( msg, function( k, v ) {
    newul += '<li> <div style="display: table;"> <img src="' + v.avatar + '" alt="" /> <div class="info-div"><h2>' + v.name + ' <span class="fright date">' + v.gfdate + '</span></h2> <div class="info-bar">' + v.text + '</div> </div></div> </li>';
  });

  ul.html( newul );

  }, "json" );

});


$('select[name="privileges"]').on('change', function( e ){

 if( $(this).val() == 1 ) {
    $('#privileges_scope').slideDown( 80 );
  } else {
    $('#privileges_scope').slideUp( 80 );
  }

});


$(document).on( 'change', 'select[name="mail_meth"]', function(){

var parent = $(this).parents( 'div' );

  if( $(this).val() == 'SMTP' ) {
  parent.next().slideDown( 'fast' );
  } else {
  parent.next().slideUp( 'fast' );
  }

  if( $(this).val() == 'sendmail' ) {
  parent.next().next().slideDown( 'fast' );
  } else {
  parent.next().next().slideUp( 'fast' );
  }

});


$(document).on( 'change', 'select[name="admin_theme"]', function(){

  $('link:first').attr( 'href', $(this).val() );

});


});