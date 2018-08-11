//hendleri za vju users.blade.php iz 'mobilni\resources\views\admin'

$(window).load(function(){

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
    $('.profilepic').css('margin-top', '25px');
    $('.singleuser').css('border-bottom', '1px solid black');
  }

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
  //klik na btn .ailid koji sluzi za aktivaciju ili deaktivaciju korisnika
  $('body').on('click', '.ailid', function(){
  	var userid = $(this).attr('userid');
  	var ailid = $(this).attr('ailid');
  	//alert(userid+" "+ailid);	
  	$.ajax({ 
      method: 'POST',
      url: urlmanaktdeakt,
      data: { userid: userid, ailid: ailid, _token: token }
    })
  	.done(function(o){
      if(o.user.aktivan == 1){
        //alert('aktivan');
        $('#user'+o.user.id).attr('ailid', 'd');
        $('#user'+o.user.id).css('background-color', 'red');
        $('#user'+o.user.id).text('Deaktiviraj');
      }else if(o.user.aktivan == 0){
        $('#user'+o.user.id).attr('ailid', 'a');
        $('#user'+o.user.id).css('background-color', 'green');
        $('#user'+o.user.id).html('&nbsp;&nbsp;Aktiviraj&nbsp;&nbsp;');
      }   
  	  console.log(o);
    });
  });

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
  //metod pozivaju hendleri za keyup u #searchinput (koji je u app.blade.php) i ovde ga koristimo za search usera i hendler za klik na btn
  //#moreres tj ucitavanje jos usera ako ih ima vise od 4, metod crta div-ove koji prikazuju usera i njegove osnovne podatke
  function showusers(o){
    var out = '';
    for(var i = 0; i < o['users'].length; i++){
      var id = o['users'][i]['id'];
      if(widthekrana < 10.5 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        out += '<div class="col-md-6 col-xs-12 singleuser"><div class="col-md-4 col-xs-4">';
      }else{
        out += '<div class="col-md-6 col-xs-12 singleuser"><div class="col-md-4 col-xs-4">';
      }        
      if(o['users'][i]['logo'] == 1){
        out += '<img src="'+homeurl+'/img/users/'+o['users'][i]['verification']+'/1.jpg" class="profilepic" style="float:left;">';         
      }else{
        out += '<img src="'+homeurl+'/images/usericon.png" class="profilepic" style="float:left;">';   
      }
      out += '</div>';//kraj diva .col-md-4 col-xs-4
      out += '<div class="col-md-8 col-xs-8">';
      out += '<h3 class="usertext">'+o['users'][i]['name']+'</h3>';
      out += '<p class="usertext">'+o['users'][i]['email']+' <br>'+o['users'][i]['grad']+'</p>';
      if(o['users'][i]['aktivan'] == 0){
        out += '<button type="button" id="user'+o['users'][i]['id']+'" class="userbtn ailid btn btn-default get" ailid="a" userid="'+o['users'][i]['id']+'" style="background-color: green;">';
        out += '<small>&nbsp;&nbsp;Aktiviraj&nbsp;&nbsp;</small>';
        out += '</button>';
      }else{
        out += '<button type="button" id="user'+o['users'][i]['id']+'" class="userbtn ailid btn btn-default get" ailid="d" userid="'+o['users'][i]['id']+'" style="background-color: red;">';
        out += '<small>Deaktiviraj</small>';
        out += '</button>';
      }
      out += "<button type='button' class='userbtn btn btn-default get' style='margin-left: 3px;'>";
      out += '<a href="'+homeurl+'/profil/'+o['users'][i]['id']+'" target="_blank" style="text-decoration: none; color: white;">';
      out += '<small>&nbsp;&nbsp;&nbsp;Profil&nbsp;&nbsp;&nbsp;</small>';
      out += '</a>'; 
      out += '</button>';
      out += '</div>'//kraj diva .col-md-8 col-xs-8
      out += '</div>';//kraj diva .col-md-6 col-xs-12 singleuser
    }
    return out;
  }

  //varijabla u koju ubacujemo userov unos u input #searchuser u users.blade.php (input je zapravo u app.blade.php layoutu)
  var searchinput = '';
  //ovo je za paginaciju koju radi metod searchusers() UsersControllera
  var limit = 4;
  //offset se povecava za 3 u hendleru za klik na <h4> #moreres koji je vidljiv ako ima jos rezultata
  var offset = 0;
  //varijablu koristi hendler za klik na <h4> #moreres da zna koliko je redova vraceno do sada iz kontrolera
  var loaded = limit; 

  //hendler za unos karaktera u text input #searchinput koji je u app.blade.php(linija 214) i koji ovde koristim za pretragu usera
  $('#searchinput').on('keyup', function(e){
    searchinput = $(this).val();//uzimamo trenutni unos u input polje 
    offset = 0;
    loaded = limit;
    //saljemo AJAX u searchusers() metod UsersControllera preko rute /searchusers,varijable url i _token su definisane na dnu vjua 
    //allusers.blade.php,pored njih saljemo searchinput i limit(3) i offset(0)
    $.ajax({ 
      method: 'POST',
      url: urlsearchusers, 
      data: { search: searchinput, limit: limit, offset: offset, _token: token }
    })
    .done(function(o1){
      console.log(o1);
      $('.allusers').html('');
      var out1 = '<div class="row reslist">';
      out1 += '<div class="row text-center"><h3 style="color: #FE980F;">pronadjeno '+o1.count+' korisnika</h3></div>';
      if(o1['users'].length == 0){ // ako ne nadje nista u bazi pod unetim pojmom ispisujemo da nije nista nasao 
        out1 += '<h3 class="text-danger text-center">No Results, Try Again...</h3>'; 
      }else{//ako nadje neke usere prikazujemo ih
        out1 += showusers(o1, out1);//pozivamo funkciju showusers() koja je napisan iznad da iscrta pronadjene usere
      }
      out1 += '</div>';//kraj diva .row
      if(o1['count'] > limit){ 
        // ako ima vise od 4 rezultata dodajemo <h4> #moreres koji kad se klikne poziva novi hendler koji salje AJAX na istu rutu samo sada sa
        //novim offsetom(ovo koristimo umesto klasicne paginacije, hendler za klik na ovaj <h4> je ispod ovog hendlera)
        out1 += '<div class="row text-center"><div id="moreres" class="col-xs-12 btndiv shadow text-center">';
        out1 += '<h4>Još Korisnika</h4>';
        out1 += '</div></div>';
      }
      $('.allusers').html(out1);
      if(widthekrana < 10.5 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        // alert('mali ekran'); 
        $('.profilepic').css('margin-top', '25px');
        $('.singleuser').css('border-bottom', '1px solid black');
      }
    });
  });

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
  //hendler za klik na <h4> #moreres(njega je izgenerisao prethodni hendler ako je nadjeno vise od 4 usera tako da je ovo kao paginacija)
  $('body').on('click', '#moreres', function(e){ 
    offset = offset + limit;//povecavamo offset za 4
    $('#moreres').remove();//uklanjamo <h4> #moreres posto cemo na dnu napraviti novi ako treba tj ako ima jos rezultata koji nisu prikazani
    //saljemo AJAX u searchusers() metod AuthorsControllera preko rute /searchusers sve je isto samo je offset povecan za 3
    $.ajax({ 
      method: 'POST',
      url: urlsearchusers,
      data: { search: searchinput, limit: limit, offset: offset, _token: token }
    })
    //kad kontroller vrati nesto(vraca usere koje nadje(saada ih offsetuje) i ukupan broj(count) bez limita i offseta)
    .done(function(o2){
      console.log(o2);
      //ovde vodimo evidenciju koliko je usera do sada prikazano(na vrhu je ovoj varijabli data vrednost 4 tj jeednaka je linitu i sada je opet
      //povecavamo za toliko)ona nam treba da bi dole znali da li smo prikazali usera koliko ih ima u count i ako jesmo onda vise ne prikazujemo
      // <h4> #moreres
      loaded = loaded + limit;
      //u ovu varijablu cemo generisati HTML sa podatcima koje vraca kontroler i to cemo append-ovati na div .reslist koji smo izgenerisali u 
      //prethodnom hendleru u koji su ubaceni i prethodni rezultati
      var out2 = '';
      out2 += showusers(o2);//pozivamo funkciju showusers() koja je napisan iznad da iscrta pronadjene usere
      //ako je count veci od broja do sada prikazanih usera(loaded varijabla) opet izbacujemo <h4> #moreres
      if(o2['count'] > loaded){
        out2 += '<div class="row text-center"><div id="moreres" class="col-xs-12 btndiv shadow text-center">';
        out2 += '<h4>Jos Korisnika</h4>';
        out2 += '</div></div>';
      }
      //izgenerisani HTML appendujemo na dno div-a #reslist koji smo izgenerisali u prethodnom hendleru
      $(out2).appendTo('.reslist');
      if(widthekrana < 10.5 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        // alert('mali ekran'); 
        $('.profilepic').css('margin-top', '25px');
        $('.singleuser').css('border-bottom', '1px solid black');
      }
    });
  });

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
});	