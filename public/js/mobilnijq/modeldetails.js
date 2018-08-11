$(window).load(function(){

var dpi_x = document.getElementById('dpi').offsetWidth;
var dpi_y = document.getElementById('dpi').offsetHeight;
//  MERI CEO EKRAN
//  MERI BROWSER
var widthekrana = $(window).width() / dpi_x;
var heightekrana = $(window).height() / dpi_y;
//
if(widthekrana < 10.5 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
  // alert('mali ekran');	
  var cont = '<br>';	
  // $(cont).prependTo('.drugidivdata');
  $('.slikamodel').removeClass('pull-left');
  $(cont).appendTo('.dimensions');
}


});