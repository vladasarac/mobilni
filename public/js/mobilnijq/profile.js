$(window).load(function(){
//hendlerti za vju profile.blade.php
// alert('radi');
//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
  //ako je mali ekran dodajemo malo margine profilepic slici
  var dpi_x = document.getElementById('dpi').offsetWidth;
  var dpi_y = document.getElementById('dpi').offsetHeight;
  //  MERI CEO EKRAN
  //  MERI BROWSER
  var widthekrana = $(window).width() / dpi_x;
  var heightekrana = $(window).height() / dpi_y;
  //
  if(widthekrana < 10.5 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
    // alert('mali ekran'); 
    $('.profileoglasimg').css('width', '10%');
    $('.imgbr').remove();
    $('.pb').removeClass('pull-right');
  }

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
//klik na div #izmenilicnepodatkebtn koji skida formi za dodavanje podataka korisnika attribut hidden 
$('body').on('click', '#izmenilicnepodatkebtn', function(){
  $('.divforma').removeAttr('hidden');
  $(this).removeClass('shadow');
});

//zatvaranje forme za dodavanje podataka korisnika
$('body').on('click', '.closebtn', function(){
  $('.divforma').attr('hidden', 'true');
  $('#izmenilicnepodatkebtn').addClass('shadow');
  $('.errormsg').remove();//uklanjamo sve error msg-ove u formi
  $('#infoime').unbind('click', false);
  $('#infograd').unbind('click', false);
  $('#infotelefon').unbind('click', false);
  $('#infotelefon2').unbind('click', false);
  $('#infotelefon3').unbind('click', false);
  $('#infoadresa').unbind('click', false);
  $('#infoemail').unbind('click', false);
});

//klik na info ikonu kod polja za ime u formi za dodavanje podataka usera
$('body').on('click', '#infoime', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-danger infoblock infoimeblock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoriimekorisnikainfo">';
  cont += '<small class="text-danger">';
  cont += 'Ime je Obavezno polje bla bla bla truc truc trucObavezno polje bla bla bla truc truc trucObavezno polje bla bla bla truc truc truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#imekorisnika');
});
//klik na  div koji prikazuje info msg u infobloku za polje ime u formi za dodavanje podataka usera
$('body').on('click', '.infoimeblock', function(){
  $('#infoime').unbind('click', false);
});

//klik na info ikonu kod polja za grad u formi za dodavanje podataka usera
$('body').on('click', '#infograd', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-danger infoblock infogradblock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvorigradinfo">';
  cont += '<small class="text-danger">';
  cont += 'Grad je Obavezno polje bla bla bla truc truc trucObavezno polje bla bla bla truc truc trucObavezno polje bla bla bla truc truc truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#grad');
});
//klik na  div koji prikazuje info msg u infobloku za polje grad u formi za dodavanje podataka usera
$('body').on('click', '.infogradblock', function(){
  $('#infograd').unbind('click', false);
});

//klik na info ikonu kod polja za telefon korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infotelefon', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-danger infoblock infotelefonblock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoritelefoninfo">';
  cont += '<small class="text-danger">';
  cont += 'Telefon je Obavezno polje bla bla bla truc truc Obavezno polje bla bla bla truc truc trucObavezno polje bla bla bla truc truc truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#telefonkorisnika');
});
//klik na  div koji prikazuje info msg u infobloku za polje telefon korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infotelefonblock', function(){
  $('#infotelefon').unbind('click', false);
});

//klik na info ikonu kod polja za telefon2 korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infotelefon2', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-warning infoblock infotelefon2block" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoritelefon2info">';
  cont += '<small class="text-warning">';
  cont += 'Telefon 2 nije Obavezno polje bla bla bla  truc nije Obavezno polje bla bla bla  truc truc nije Obavezno polje bla bla bla truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#telefon2');
});
//klik na div koji prikazuje info msg u infobloku za polje telefon2 korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infotelefon2block', function(){
  $(this).remove();	
  $('#infotelefon2').unbind('click', false);
});

//klik na info ikonu kod polja za telefon3 korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infotelefon3', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-warning infoblock infotelefon3block" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoritelefon3info">';
  cont += '<small class="text-warning">';
  cont += 'Telefon 3 nije Obavezno polje bla bla bla  truc nije Obavezno polje bla bla bla  truc truc nije Obavezno polje bla bla bla truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#telefon3');
});
//klik na div koji prikazuje info msg u infobloku za polje telefon3 korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infotelefon3block', function(){
  $(this).remove();	
  $('#infotelefon3').unbind('click', false);
});

//klik na info ikonu kod polja za Adresa korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infoadresa', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-warning infoblock infoadresablock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoriadresainfo">';
  cont += '<small class="text-warning">';
  cont += 'Adresa nije Obavezno polje bla bla bla  truc nije Obavezno polje bla bla bla  truc truc nije Obavezno polje bla bla bla truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#adresakorisnika');
});
//klik na div koji prikazuje info msg u infobloku za polje Adresa korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infoadresablock', function(){
  $(this).remove();	
  $('#infoadresa').unbind('click', false);
});

//klik na info ikonu kod polja za Email(prikazi ili ne) korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infoemail', function(){
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-warning infoblock infemailblock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvoriemailinfo">';
  cont += '<small class="text-warning">';
  cont += 'Email nije Obavezno polje bla bla bla truc nije Obavezno polje bla bla bla truc truc nije Obavezno polje bla bla bla truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#prikaziemail');
});
//klik na div koji prikazuje info msg u infobloku za polje Email(prikazi ili ne) korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infemailblock', function(){
  $(this).remove();	
  $('#infoemail').unbind('click', false);
});

//klik na info ikonu kod polja za unos Logo-a korisnika u formi za dodavanje podataka usera
$('body').on('click', '#infologo', function(e){
  e.preventDefault();
  $(this).bind('click', false);
  var cont = '<div class="errormsg alert alert-warning infoblock infologoblock" role="alert"><span class="help-block">';
  cont += '<img src="'+homeurl+'/images/redclose.png" class="pull-right zatvoriinfo" id="zatvorilogoinfo">';
  cont += '<small class="text-warning">';
  cont += 'Logo nije Obavezno polje bla bla bla truc nije Obavezno polje bla bla bla truc truc nije Obavezno polje bla bla bla truc';
  cont += '</small></span></div>';
  $(cont).insertAfter('#inputimages');
});
//klik na div koji prikazuje info msg u infobloku za polje Logo-a korisnika u formi za dodavanje podataka usera
$('body').on('click', '.infologoblock', function(){
  $(this).remove(); 
  $('#infologo').unbind('click', false);
});
  
//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

//klik na btn za brisanje logo - a   
$('body').on('click', '.deletelogo', function(e){
  var userid = $(this).attr('userid');//iz atributa userid btn-a koji smo kliknuli uzimamo id usera
  var form_data = new FormData();//pravimo objekat u kom ce biti podatci iz forme za edit brenda
  form_data.append('userid', userid);
  //saljemo AJAX obrisilogo() metodu UsersControllera preko rute obrisilogo(url i _token su definisani na dnu vjua profil.blade.php) i saljemo
  //id usera ciji logo se brise posto se folder u kom je logo zove po id-u usera
  $.ajaxSetup({//Laravelov _token
    headers: {
      'X-CSRF-Token': $('meta[name=_token]').attr('content')
    }
  });
  $.ajax({
    method: 'POST',
    url: urldeletelogo,
    data:  form_data,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })
  .done(function(o){
    console.log(o);
    if(o == 1){
      //ovo se ubacuje na vrh profil.blade.php u tabelu koja prikazuje osnovne podatke usera, tj menjamo sliku na difoltnu posto nema vise logo
      var output1 = '<img src="'+homeurl+'/images/usericon.png" class="img-thumbnail" id="showimages1" style="max-width:100px;max-height:100px;float:left;">';
      $('.userimg').html(output1);
      //pravimo html koji ce se ubaciti u formu pored inputa za upload fajla, ovo je siva slika sa neta koju po difoltu prikazuejmo dok user
      //nema logo. pa posto ga je sada obrisao vracamo tu sliku
      var output2 = '<br><img src="http://placehold.it/100x100" id="showimages" style="max-width:200px;max-height:200px;float:left;">';
      //uklanjamo logo koji je do sada bio prikazan useru(tj njegov bivsi logo) u formi pored inputa za upload fajla
      $('#showimages').remove();
      //uklanjamao btn za brisanje logo-a posto user nema vise logo
      $('.deletelogo').remove();
      //ubacujemo html iza inputa za upload fajla u formi u profil.blade.php
      $(output2).insertAfter($('#inputimages'));
    }
  });
});
  
//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

//klik na div #oglasikorisnikabtn koji skida attribut hidden divu u kom su oglasi korisnika
$('body').on('click', '#oglasikorisnikabtn', function(){
  $('.oglasikorisnika').removeAttr('hidden');
  $(this).removeClass('shadow');
});  
//skrivanje diva u kom su oglasi korisnika
$('body').on('click', '.closeoglasikorisnikabtn', function(){
  $('.oglasikorisnika').attr('hidden', 'true');
  $('#oglasikorisnikabtn').addClass('shadow');
});

//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

//klik na btn za odobravanje oglasa u profile.blade.php, salje se AJAX sa id-em oglas u metod odobrioglas() OglasControllera
$('body').on('click', '.odobri', function(e){
  e.preventDefault();
  var id = $(this).attr('id');
  var form_data1 = new FormData();//pravimo objekat u kom ce biti podatci iz forme za edit brenda
  form_data1.append('id', id);
  $.ajaxSetup({//Laravelov _token
    headers: {
      'X-CSRF-Token': $('meta[name=_token]').attr('content')
    }
  });
  $.ajax({
    method: 'POST',
    url: urlodobrioglas,
    data:  form_data1,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })
  .done(function(o){
    if(o.oglas.approved == 1){ //menjamo kliknuti btn i od njega opet pravimo btn za zabranu oglasa 
      $('#'+o.oglas.id).css('background-color', '#FE980F').removeClass('odobri').addClass('zabrani').text('Zabrani Oglas');
    }else{ //ako je doslo do neke greske pa kolona odobren oglasa koji smo pokusali da odobrimo nije 1
      alert('Došlo je do greške, trenutno nije moguće odobriti oglas, pokušajte kasnije.');
    }
  });
});

//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

//klik na btn za zabranu oglasa u profile.blade.php, salje se AJAX sa id-em oglas u metod zabranioglas() OglasControllera
$('body').on('click', '.zabrani', function(e){
  e.preventDefault();
  var id = $(this).attr('id');
  var form_data2 = new FormData();//pravimo objekat u kom ce biti podatci iz forme za edit brenda
  form_data2.append('id', id);
  $.ajaxSetup({//Laravelov _token
    headers: {
      'X-CSRF-Token': $('meta[name=_token]').attr('content')
    }
  });
  $.ajax({
    method: 'POST',
    url: urlzabranioglas,
    data:  form_data2,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })
  .done(function(o){//menjamo kliknuti btn i od njega opet pravimo btn za odobravanje oglasa 
    if(o.oglas.approved == 0){ 
      $('#'+o.oglas.id).css('background-color', 'green').removeClass('zabrani').addClass('odobri').text('Odobri Oglas');
    }else{ //ako je doslo do neke greske pa kolona odobren oglasa koji smo pokusali da odobrimo nije 1
      alert('Došlo je do greške, trenutno nije moguće zabraniti oglas, pokušajte kasnije.');
    }
  });
});

//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

//
$('body').on('click', '.obrisi', function(e){
  e.preventDefault();
  var oglasid = $(this).attr('oglasid');
  var iduser = $(this).attr('userid');
  var form_data = new FormData();//pravimo objekat u kom ce biti podatci iz forme za edit brenda
  form_data.append('iduser', iduser);
  form_data.append('oglasid', oglasid);
  // alert('oglasid: '+oglasid+'userid: '+userid+'token:'+token);
  if(confirm("Da li ste sigurni da želite da obrišete ovaj oglas?")){
    $.ajaxSetup({//Laravelov _token
      headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
      }
    });
    $.ajax({
      method: 'POST',
      url: urlobrisioglas,
      data:  form_data,
      contentType: false, // The content type used when sending data to the server.
      cache: false, // To unable request pages to be cached
      processData: false
    })
    .done(function(o){
      console.log(o);
      if(o.delete == true){
        $('.brojoglasaspan').html(o.broglasa);
        $('#oglas'+o.idoglasa).remove();
      }
    });
  }
});

//-----------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------

});






















