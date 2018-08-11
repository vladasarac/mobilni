//hendleri za vju dodajbrand.blade.php iz 'mobilni\resources\views\admin'

//funkcije za ubacivanje uploadovane slike u img #showimages  
function readURL(input){
  if(input.files && input.files[0]){
    var reader = new FileReader();
    reader.onload = function(e){
      $('#showimages').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#logoimage").change(function(){
  readURL(this);
});

function readURL1(input){
  if(input.files && input.files[0]){
    var reader = new FileReader();
    reader.onload = function(e){
      $('#showimages1').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#phoneimg").change(function(){
  readURL1(this);
});

//--------------------------------------------------------------------------------------------------------------------------------------

//funkcija pravi error msg ako uploadovan fajl za brend telefona ili model brenda nema jpg, jpeg ili png extenziju
function imgextensionerror(){
  var cont1 = '<div class="errormsg row text-center alert alert-danger" role="alert">';
  cont1 += '<span class="help-block text-center"><strong class="text-danger">';
  cont1 += 'Slika mora imati png, jpeg ili jpg ekstenziju.'
  cont1 += '</strong></span></div>';
  return cont1;
}

//--------------------------------------------------------------------------------------------------------------------------------------

//funkcija radi scroll do vrha odredjenog diva obicno kad se prikazuju errori pri validaciji unosa u neku formu ali i u drugim prilikama
function scroll(divname){
  $('html, body').animate({
    scrollTop: ($(divname).first().offset().top)
  },150);
}

//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad treba isprazniti formu za unosmodela-editmodela i pretvoriti je opet iz forme za edit u formu za unos novog modela
function emptyphonemodelform(){
  //praznimo formu za edit modela tj za unos novog modela
  $('#modelname').val('');
  $('#year').val('');
  $('#link').val('');
  $('#smart').val('1');
  $('#ts').val('1');
  $('#showimages1').attr('src', homeurl+'/images/phonesilhouette.png');
  $('#dodatnibtni').html('');//ukklanjamo dodatne btn-e tj btn za Cancel i Obrisi za brisanje modela
  $('#phoneimg').val(null);//ako je uploadovana slika brisemo je
  //formu iz forme za update modela ponovo pretvaramo u formu za dodavanje novog modela
  $('.formazaupdatemodela').addClass('formazanovimodel').removeClass('formazaupdatemodela');
  $('.formamodelpanelheading').html('Dodaj Modele Telefona');
}

//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se prazni forma za edit brenda i pretvara se opet u formu za dodavanje brenda
function emptybrandeditform(){
  //formu opet vracamo da bude za dodavanje brenda tj vracamo joj klasu .brendforma a uklanjamo klasu .editbrendforma   
  $('.editbrendforma').addClass('brendforma').removeClass('editbrendforma');
  $('.errormsg').remove();//praznimo eventualne error msg-e i formu
  $('#logoimage').val(null);
  $('#showimages').attr('src', homeurl+'/images/brendovi.png');
  $('#name').val(''); 
  $('.formabrandpanelheading').html('Dodaj Brendove Telefona');//vracamo naslov forme za dodavanje brenda
  $('#dodatnibtnibrend').html('');//uklanjamo btn za Cancel edita brenda i za brisanje brenda
}

//--------------------------------------------------------------------------------------------------------------------------------------

//metod pozivaju hendleri za sabmit forme za dodavanje novog brenda i za sabmit forme za dodavanje novog modela, i svi ostali hendleri koji -
//-salju AJAX, metod salje AJAX na zadatu rutu i prima odgovor, ako su errori tj nije prosla validacija u kontroleru prikazuje ih a ako je -
//-uspesno upisano cisti formu i sprema je za novi unos i na odgovarajucem mestu prikazuje novi brend i njegov logo ili novi model i njegovu 
//sliku  
function sendajax(formdata, urladdress, bilim){
  $.ajaxSetup({//Laravelov _token
    headers: {
      'X-CSRF-Token': $('meta[name=_token]').attr('content')
    }
  });
   //saljemo AJAX odgovarajucem metodu BrandsControllera preko rute koja je data kao argument pri pozivu metoda sa podatcima koji su takodje
   //stigli kao argument, i stigao je na kraju bilim ako je b onda se upisuje brend a ako je m onda se upisuje model
  $.ajax({
    method: 'POST',
    url: urladdress,
    data: formdata,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })
  .done(function(o){
    //alert(bilim);
    console.log(o);
    if(o.errors){//ako nije prosla validacija u kontroleru(npr ako unosimo brend cije je ime vec uneto i slicno ...)
      $('.errormsg').remove();//cistimo prikaz prethodnih errora ako ih je bilo i prikazujemo pristigle u <span> .help-block
      var cont = '<br><div class="errormsg row text-center alert alert-danger" role="alert">';
      //iteriramo kroz errore koje je vratio kontroler i prikazujemo ih
      for(var key in o.errors){
        for(var key1 in o.errors[key]){
          cont += ' ' + o.errors[key][key1] + '<br>';
          //cont += '[' + key + ']' + '[' + key1 + ']' + o.errors[key][key1] + '<br>';
        }
      }  
      cont = '<span class="help-block text-center"><strong class="text-danger">' + cont + '</strong></span></div>'; 
      var errordiv = '';  //variabla u koju cemo ubaciti string tj ime diva na koji se radi skrolovanje ekrana ako ima errora da bi se videli
      if(bilim == 'b'){//ako je unet brend errore kacimo na <span> #naslofforme    
        $(cont).insertAfter('#naslofforme');  
        errordiv = '.rowformabrend'
      }else{//ako je unet novi model errore kacimo na <span> #naslofformemodel   
        $(cont).insertAfter('#naslofformemodel');
        errordiv = '.rowformamodel'
      } 
      //scrollujemo do vrha diva u kom je forma za novi brend da bi admin video errore
      scroll(errordiv);
    }else{//ako nije bilo gresaka pri validaciji
      if(bilim == 'b'){//ako je unet brend tj bilim == 'b'
        if(o.saved == true){//ako je upisan red u tabelu cistimo formu da bude spremna za sledeci unos
          $('.errormsg').remove();
          $('#logoimage').val(null);
          $('#showimages').attr('src', homeurl+'/images/brendovi.png');
          $('#name').val(''); 
          //u div #unetibrendovi ubacujemo div koji prikazuje ime i logo upravo unetog brenda
          var cont3 = '<div class="col-md-2 text-center">';
          cont3 += '<h5>' + o.name + '</h5>';
          cont3 += '<img class="img-thumbnail" style="max-width:100px;max-height:100px;" src="' + homeurl + '/img/brands/' + o.logoName + '">';
          cont3 += '</div>';  
          $('#unetibrendovi').removeAttr('hidden');
          $(cont3).appendTo('#unetibrendovi');
        }else{//ako je prosla validacija u kontroleru ali iz nekog razloga nije upisan red u bazu
          var cont3 = '<h3 class="text-center">Upis nije uspeo, pokušajte ponovo.</div>';
        }
      }else if(bilim == 'm'){//ako je unet novi model tj bilim == 'm'
        //console.log(o);
        if(o.saved == true){//ako je upisan red u tabelu cistimo formu da bude spremna za sledeci unos
          $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
          $('.errormsg').remove();
          $('#modelname').val('');
          $('#link').val('');
          $("#smart option[value='1']").prop('selected', true);
          $('#phoneimg').val(null);
          $('#year').val('');
          $('#showimages1').attr('src', homeurl+'/images/phonesilhouette.png');
          //u div #unetimodeli ubacujemo div koji prikazuje ime i logo upravo unetog modela
          var cont4 = '<div class="col-md-2 text-center">';
          cont4 += '<h5>' + o.brandname + ' ' + o.modelname + '</h5>';
          cont4 += '<img class="img-thumbnail" style="max-width:100px;max-height:100px;" src="' + homeurl + '/img/modelibrenda/' + o.brandid + '/' + o.img + '">';
          cont4 += '</div>';  
          $('#unetimodeli').removeAttr('hidden');
          $(cont4).appendTo('#unetimodeli');
        }else if(o.saved == false){//ako u bazi vec postoji model istog imena i brand_id-a, tj ako je model vec upisan 
          $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
          $('.errormsg').remove();
          //pravimo error msg da je istoimeni model vec unet i prikazujemo je ispod span-a'#naslofformemodel' i radimo scroll na vrh forme
          var cont5 = '<div class="errormsg row text-center alert alert-danger" role="alert">';
          cont5 += '<span class="help-block text-center"><strong class="text-danger">'+o.upisan+'</strong></span></div>'; 
          $(cont5).insertAfter('#naslofformemodel');
          scroll('.rowformamodel');
        }else{//ako je prosla validacija u kontroleru ali iz nekog razloga nije upisan red u bazu
          var cont4 = '<h3 class="text-center">Upis nije uspeo, pokušajte ponovo.</div>';         
        }
      }else if(bilim == 'mb' || bilim == 'jmb'){//ako je kliknut div #svimodelibrenda za prikaz svih modela nekog brenda ili div #josmodelabrenda
        $('#svimodelibrenda').remove();  
        var cont6 = '';
        if(o['modelibrenda'].length <= 0){
          cont6 += '<h2 class="text-center text-danger">Brend nema dodatih modela.</h2>';
        }else{       
          //povecavamo skip za onoliko koliko smo izvukli u ovoj turi tj za onoliko koliki je take
          skip = skip + take;
          if(bilim == 'mb'){
            cont6 += '<h3 class="text-center" style="color: #FE980F;">Do sada uneti modeli brenda ' + o.brand.name + '</h3><hr>';
            cont6 += '<img class="pull-right orangeclosebtn" idbrenda="'+o.brand.id+'" src="'+homeurl+'/images/orangeclosebtn2.png"><br>';
          }     
          for(var i = 0; i < o['modelibrenda'].length; i++){
            cont6 += '<div id="jedanmodel" brendid="'+o.brand.id+'" modelid="'+o['modelibrenda'][i]['id']+'" imgname="'+o['modelibrenda'][i]['img']+'" ime="'+o['modelibrenda'][i]['name']+'" link="'+o['modelibrenda'][i]['link']+'" year="'+o['modelibrenda'][i]['year']+'" smart="'+o['modelibrenda'][i]['smart']+'" ts="'+o['modelibrenda'][i]['ts']+'" img="'+o['modelibrenda'][i]['img']+'" class="model'+o['modelibrenda'][i]['id']+' col-md-3 text-center">';
            cont6 += '<h5>' + o.brand.name + ' ' + o['modelibrenda'][i]['name'] + '</h5>';
            cont6 += '<img class="img-thumbnail" style="max-width:100px;max-height:100px;" src="'+homeurl+'/img/modelibrenda/'+o.brand.id+'/'+o['modelibrenda'][i]['img']+'">';
            cont6 += '</div>'; 
          }
          if(bilim == 'mb' && skip < o.brand.brojmodela){
            var cont7 = '<div id="josmodelabrenda" class="col-md-12 btndiv text-center" brendid="'+o.brand.id+'">';
            cont7 += '<h4>Učitaj Još Modela</h4>';
            cont7 += '</div>';
          }else if(skip >= o.brand.brojmodela){
            $('#josmodelabrenda').remove();
          }         
        }
        $(cont6).appendTo('#modelibrenda');
        $(cont7).insertAfter('#modelibrenda');
      }else if(bilim == 'em'){//ako je radjen edit modela tj bilim == 'em'
        if(o.saved == true){//ako je uspesno uradjen update modela
          //praznimo formu za edit modela tj za unos novog modela
          emptyphonemodelform();
          //scroll-ujemo na div koji prikazuje editovani model i u tom div-u menjamo naslov i img i malo se zezamo sa bojama i pozdinom
          scroll('.model'+o.modelid);
          $('.model'+o.modelid).children('img').attr('src', homeurl+'/img/modelibrenda/'+o['brand']['id']+'/'+o.img);
          $('.model'+o.modelid).css({"background-color": "#FE980F"});
          $('.model'+o.modelid).children('h5').html(o['brand']['name']+' '+o.modelname).css({"color": "red"});
          setTimeout(function(){
            $('.model'+o.modelid).children('h5').addClass('menjajboju');
            $('.model'+o.modelid).addClass('menjajbekgraundboju');
          }, 500);
        }else if(o.saved == false){//ako u bazi vec postoji model istog imena i brand_id-a, tj ako je model vec upisan 
          $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
          $('.errormsg').remove();
          //pravimo error msg da je istoimeni model vec unet i prikazujemo je ispod span-a'#naslofformemodel' i radimo scroll na vrh forme
          var cont5 = '<div class="errormsg row text-center alert alert-danger" role="alert">';
          cont5 += '<span class="help-block text-center"><strong class="text-danger">'+o.upisan+'</strong></span></div>'; 
          $(cont5).insertAfter('#naslofformemodel');
          scroll('.rowformamodel');
        }else{//ako je prosla validacija u kontroleru ali iz nekog razloga nije upisan red u bazu
          var cont4 = '<h3 class="text-center">Upis nije uspeo, pokušajte ponovo.</div>';         
        }
      }else if(bilim == 'dm'){//ako je obrisan model tj kliknut btn Obrisi u formi za edit modela
        emptyphonemodelform();
        $('.model'+o.modelid).remove();
        scroll('.orangeclosebtn');
      }else if(bilim == 'eb'){//ako je sabmitovana forma .editbrendforma za editovanje Branda
        if(o.saved == true){//ako je uspesno uradjen update brenda
          //pozivamo funkciju da formu opet vrati da bude za dodavanje brenda tj vracamo joj klasu .brendforma a uklanjamo klasu .editbrendforma   
          emptybrandeditform();
          var cont8 = '<div class="row izmenjenbrend text-center">';
          cont8 += '<img class="pull-right closebtnbrendedit" idbrenda="'+o.brand.id+'" src="'+homeurl+'/images/orangeclosebtn2.png"><br>';
          cont8 += '<h3>Uspešno ste izmenili brand: '+o.brand.name+'</h3>';
          cont8 += '<img style="max-width:100px;max-height:100px;"  src="'+homeurl+'/img/brands/'+o.img+'?vreme='+new Date()+'">';
          cont8 += '</div>';
          $(cont8).appendTo('.panelbodybrendforma');
        }else if(o.saved == false){//ako u bazi vec postoji model istog imena i brand_id-a, tj ako je model vec upisan
          $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
          $('.errormsg').remove();
          //pravimo error msg da je istoimeni model vec unet i prikazujemo je ispod span-a'#naslofformemodel' i radimo scroll na vrh forme
          var cont9 = '<div class="errormsg row text-center alert alert-danger" role="alert">';
          cont9 += '<span class="help-block text-center"><strong class="text-danger">'+o.upisan+'</strong></span></div>'; 
          $(cont9).insertAfter('#naslofforme');
          scroll('.rowformabrend');
        }else{
          var errorcont = '<h3 class="text-center">Upis nije uspeo, pokušajte ponovo.</div>';  
        } 
      }else if(bilim == 'db'){//ako je kliknut btn #deletebrend Obrisi za brisanje brenda
        $('#brend'+o.brendid).remove();//uklanjamo <option> iz selecta za brend koji smo obrisali 
        $("#brandname option[value=0]").attr('selected', 'selected');//selectujemo nultu opciju u <select> u za izbor brenda
        $('.btndiv').remove();//uklanjamo btn div prikazi modele brenda
        $('.logobrenda').attr('hidden', 'true');   //dodaj opet atribut hidden za img .logobrenda
        $('#divmodeldata').attr('hidden', 'true'); //sakrivamo div za dodavanje novog modela
        $('#izmenibrend').remove();//uklanjamo btn za edit brenda ako je prethodno selektovan neki brend
        $('#izmenibrendbr').remove();
        $('#modelibrenda').html('');//ako je admin gledao modele obrisanog brenda i taj div praznimo
        emptyphonemodelform();//praznimo forme za edit brenda i edit modela tj dodavanje brenda i dodavanje modela
        emptybrandeditform()
      }
    } 
  });
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//koristimo varijable kad se vade svi modeli brenda da bi vadio po 8 modela u jednom requestu
var skip = 0;
var take = 8;

$(document).ready(function(){

  //submit forme za dodavanje novog brenda u dodajbrend.blade.php iz 'mobilni\resources\views\admin'
  $("body").on("submit", ".brendforma", function(e) {
    e.preventDefault();
    $('.help-block').remove();
    var extension = $('#logoimage').val().split('.').pop().toLowerCase();//uzimamo extenziju unete slike u formu za unos novog brenda
    if ($.inArray(extension, ['jpg', 'jpeg', 'png']) == -1) {//proveravamo da li je jpg, jpeg ili png posto su samo te dozvoljene
      $('.errormsg').remove();
      var c = imgextensionerror();//pravimo error message koji ce biti prikazan useru
      $(c).insertAfter('#naslofforme');
      //scrollujemo do vrha diva u kom je forma za novi brend da bi admin video errore
      scroll('.rowformabrend');
    }else{//ako je slika OK pripremamo AJAX koji ce biti poslat u metod dodajbrend() BrandsControllera preko rute '/dodajbrendforma'
      var logoimage = $('#logoimage').prop('files')[0];//uzimamo sliku
      var name = $('#name').val();//uzimamo ime novog brenda
      var form_data = new FormData();//pravimo objekat u kom ce biti podatci
      form_data.append('logoimage', logoimage);
      form_data.append('name', name);
      sendajax(form_data, urldodajbrendforma, 'b');	
    }   
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //promena u select - u za biranje brenda u formi za dodavanje modela
  $('#brandname').on('change', function(){
    $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
    $('.errormsg').remove();
    $('#unetimodeli').html('');
    $('#izmenibrend').remove();//uklanjamo btn za edit brenda ako je prethodno selektovan neki brend
    $('#izmenibrendbr').remove();
    $('#modelibrenda').html('');//praznimo div koji postoji ako je admin gledao modele nekog brenda pre nego je izabrao novi brend
    //ako je admin radio edit nekog modela tj forma za unos modela je bila menjana u formu za edit modela praznimo polja koja su u tom slu
    //caju bila popunjena vrednostima i vracamo sliku na pocetno stanje tj prikaz siluete telefona i vracamo formi klasu za unos novog modela
    emptyphonemodelform();
    var logo = $('option:selected', this).attr('logo');//uzmamo naziv slike(logo kolona brands tabele) koji je u atributu logo
    var brend = $('option:selected', this).text();//uzimamo text optiona tj ime brenda
    var idbrenda = $(this).val();//uzimamo id brenda
    if(logo == undefined){//ako je logo undefined tj birali smo neki brend pa smo opet izabrali prazno polje u selectu
      $('.logobrenda').attr('hidden', 'true');   //dodaj opet atribut hidden za img .logobrenda
      $('#divmodeldata').attr('hidden', 'true'); //sakrivamo div za dodavanje novog modela
      //ako je user uploadovao img za model vracamo da slika bude silueta mobilnog
      $('#showimages1').attr('src', homeurl+'/images/phonesilhouette.png' + '?vreme='+new Date()+'');
      $('#modelname').val('');//vracamo inpute u formi da budu prazni
      $('#year').val('');
      $('#link').val('');
      $('.btndiv').remove();
    }else{
      $('.btndiv').remove();
      $('.logobrenda').attr("src", "img/brands/"+logo+"?vreme="+new Date()+"");
      $('.logobrenda').removeAttr('hidden');//takodje uklanjamo atribut hidden(koji img .logobrenda ima po difoltu)
      $('#divmodeldata').removeAttr('hidden');
      var cont = '';
      cont += '<div id="svimodelibrenda" class="col-md-12 btndiv text-center" brendid="'+idbrenda+'">';
      cont += '<h4>Prikazi Sve Modele</h4>';
      cont += '</div>';
      $(cont).insertAfter('.formamodel');
      var cont2 = '<br id="izmenibrendbr"><button id="izmenibrend" class="btn btn-primary" logobrenda="'+logo+'" brendname="'+brend+'" brendid="'+idbrenda+'">Izmeni Brend</button>';
      $(cont2).insertAfter('.logobrenda');
    }
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //klik na btn #izmenibrend koji je vidljiv ispod logo-a brenda u formi za dodavanje novog modela
  $('body').on('click', '#izmenibrend', function(e){
    e.preventDefault();
    //alert('izmena brenda');
    var brendid = $(this).attr('brendid');
    var brendname = $(this).attr('brendname');
    brendname = $.trim(brendname);
    var logobrenda = $(this).attr('logobrenda');
    $('.formabrandpanelheading').html('Izmeni Brand: <b style="color: #FE980F;">'+brendname+'</b>');
    $('.brendforma').addClass('editbrendforma').removeClass('brendforma');
    $('#unetibrendovi').attr('hidden', true);
    $('#name').val(brendname);
    $('#logoimage').val(null);
    $('#showimages').attr('src', homeurl+'/img/brands/'+logobrenda + '?vreme='+new Date()+'');
    //dodajem i btn-e za brisanje modela i za Cancel editovanja i hidden inpute (id brenda i logo brenda koji ce trebati metodu kontroelru)
    var cont = ' &nbsp;<button class="btn btn-primary" id="canceleb" style="background-color: green;">&nbsp;Cancel&nbsp;</button>';
    cont += ' &nbsp;<button class="btn btn-primary" id="deletebrend" brendid="'+brendid+'" style="background-color: red;">&nbsp;&nbsp;Obriši&nbsp;&nbsp;</button>';
    cont += '<input type="hidden" id="idbrenda" name="idbrenda" value="'+brendid+'">';
    cont += '<input type="hidden" id="logobrenda" name="logobrenda" value="'+logobrenda+'">';
    $('#dodatnibtnibrend').html(cont);//dodadtne btn-e i hiden inpute ubacujem u span #dodatnibtni koji je pored btn-a za sabmit forme 
    //scrollujemo do vrha diva u kom je forma za brend
    scroll('.formabrandpanelheading');
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

  //klik na button Cancel ispod forme za edit brenda
  $('body').on('click', '#canceleb', function(e){
    e.preventDefault();
    //pozivamo funkciju da formu opet vrati da bude za dodavanje brenda tj vracamo joj klasu .brendforma a uklanjamo klasu .editbrendforma   
    emptybrandeditform();
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //submit forme za edit brenda (ista forma kao i za dodavanje brenda samo joj je promenjena klasa)
  $("body").on("submit", ".editbrendforma", function(e){
    e.preventDefault();
    $('.help-block').remove();
    alert('forma za edit brenda submit!!!');
    var logoimage = $('#logoimage').prop('files')[0];//uzimamo sliku
    var name = $('#name').val();//uzimamo ime novog brenda
    var idbrenda = $('#idbrenda').val();//uzimamo id brenda
    var logobrenda = $('#logobrenda').val();//uzimamo stari(a ako nije dodat novi i sadasnji) logo brenda
    var form_data = new FormData();//pravimo objekat u kom ce biti podatci iz forme za edit brenda
    form_data.append('logoimage', logoimage);
    form_data.append('name', name);
    form_data.append('idbrenda', idbrenda);
    form_data.append('logobrenda', logobrenda);
    sendajax(form_data, urleditbrenda, 'eb');//pozivamo sendajax() i kao treci arg dajemo 'eb' (EditBrenda)   
  });

//--------------------------------------------------------------------------------------------------------------------------------------
 
  //klik na x ikonu u divu koji prikazuje izmanjen brend ispod forme za unos tj edit brenda
  $("body").on("click", ".closebtnbrendedit", function(e){
    //alert('kliknut x za zatvaranje editbrenda');
    $('.izmenjenbrend').remove();
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

  //kad se klikne btn Obrisi ispod forme za edit brenda, uzima se id brendai salje se AJAX preko rute'/deletebrend' u metod deletebrend() Bran-
  //-dsControllera koji brise brend ciji id stigne njegove modele i logo i foldeer sa slikama modela brenda
  $('body').on('click', '#deletebrend', function(e){
    e.preventDefault();
    var brendid = $(this).attr('brendid');//uzimamo id brenda
    //ako admin potvrdi brisanje
    if(confirm("Da li ste sigurni da želite da obrišete ovaj brend?")){      
      var form_data = new FormData();
      form_data.append('brendid', brendid);
      //saljemo AJAX preko funkcije sendajax preko rute /deletebrend u deletebrend() BrandsControllera i kao treci argument dajemo db(DeleteBrend)
      sendajax(form_data, urldeletebrend, 'db');
    }
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
 
  //submit forme za dodavanje novog modela nekog brenda u dodajbrend.blade.php iz 'mobilni\resources\views\admin'
  $("body").on("submit", ".formazanovimodel", function(e) {
    e.preventDefault();
    $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
    $('.errormsg').remove();
    var extension = $('#phoneimg').val().split('.').pop().toLowerCase();//uzimamo extenziju unete slike u formu za unos novog brenda
    if($.inArray(extension, ['jpg', 'jpeg', 'png']) == -1) {//proveravamo da li je jpg, jpeg ili png posto su samo te dozvoljene     
      $('.errormsg').remove();
      var c = imgextensionerror();//pravimo error message koji ce biti prikazan useru
      $(c).insertAfter('#naslofformemodel');
      //scrollujemo do vrha diva u kom je forma za model da bi admin video errore
      scroll('.rowformamodel');
    }else{
      var brandid = $('#brandname').val();//uzimamo id brena
      var modelimg = $('#phoneimg').prop('files')[0];//uzimamo sliku
      var modelname = $('#modelname').val();//uzimamo ime novog modela
      var year = $('#year').val();//uzimamo godister novog modela
      var link = $('#link').val();//uzimamo link ka GSM Arena za ovaj model
      var smart = $('#smart').val();//da li telefon ima smart tehnologiju
      var ts = $('#ts').val();//da li tel. ima touchscreen
      var form_data = new FormData();//pravimo objekat u kom ce biti podatci
      form_data.append('brandid', brandid);
      form_data.append('modelimg', modelimg);
      form_data.append('modelname', modelname);
      form_data.append('year', year);
      form_data.append('link', link);
      form_data.append('smart', smart);
      form_data.append('ts', ts);
      sendajax(form_data, urldodajmodelforma, 'm');
    }
  }); 

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //kad kliknemo na <span> .help-block koji prikazuje errore na vrhu forme za novi model ili novi brend
  $('body').on("click", '.help-block', function(e){
    $(this).remove();//uklanjamo ga
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //klik na div #svimodelibrenda, uzima id brenda koji je trenutno odabran u formi za unos modela i poziva sendajax() da izvlaci modele tog -
  //brenda i prikazuje ih ispod forme za unos novog modela brenda
  $('body').on("click", '#svimodelibrenda', function(e){
    skip = 0;
    $('#modelibrenda').html('');
    var brendid = $(this).attr('brendid');
    var form_data = new FormData();//pravimo objekat u kom ce biti podatci
    form_data.append('brendid', brendid);//saljemo id brenda cije modele vadimo
    form_data.append('skip', skip);//skip i take
    form_data.append('take', take);
    sendajax(form_data, urlmodelibrenda, 'mb');//kao treci argument saljemo'mb'da sendajax() zna sta da radi kad stigne odgovor od kontrolera
  }); 
//--------------------------------------------------------------------------------------------------------------------------------------  
  //klik na div #josmodelabrenda koji se vidi kad se prikazuju modeli nekog brenda, da nastavi da ucitava modele brenda
  $('body').on("click", '#josmodelabrenda', function(e){
    var brendid = $(this).attr('brendid');
    var form_data = new FormData();//pravimo objekat u kom ce biti podatci
    form_data.append('brendid', brendid);//saljemo id brenda cije modele vadimo
    form_data.append('skip', skip);//skip i take(skip je sada povecan)
    form_data.append('take', take);
    sendajax(form_data, urlmodelibrenda, 'jmb');//kao treci argument saljemo'jmb'da sendajax()zna sta da radi kad stigne odgovor od kontrolera
  }); 

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

  //klik na ikonu za zatvaranje diva koji prikazuje sve modele nekog brenda
  $('body').on("click", '.orangeclosebtn', function(e){
    skip = 0;//variablu skip vracamo na nulu da bi vadio od pocetka modele prilikom novog vadjenja modela
    $('#modelibrenda').html('');//praznimo div u kom su prikazani modeli brenda
    $('#josmodelabrenda').remove();//uklanjamo div btn za ucitavanje modela
    var idbrenda = $(this).attr('idbrenda');//uzimamo id brenda koji je i dalje izabran u formi za dodavanje modela
    var cont = '';//ponovo crtamo div koji kad se klikne prikazuje modele brenda
    cont += '<div id="svimodelibrenda" class="col-md-12 btndiv text-center" brendid="'+idbrenda+'">';
    cont += '<h4>Prikazi Sve Modele</h4>';
    cont += '</div>';
    $(cont).insertAfter('.formamodel');//ubacujemo div btn ispod forme za novi model
  }); 

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

  //klik na neki od prikazanih modela nekog brenda cime forma za dodavanje novog modela postaje forma za edittovanje modela koji smo kliknuli
  $('body').on("click", '#jedanmodel', function(e){
    //iz atributa kliknutog diva #jedanmodel uzimam podatke kojima cu napuniti polja u formi za edit modela
    var brendid = $(this).attr('brendid');
    var modelid = $(this).attr('modelid');
    var imemodela = $(this).attr('ime');
    var imgname = $(this).attr('imgname');
    var link = $(this).attr('link');
    var year = $(this).attr('year');
    var smart = $(this).attr('smart');
    var ts = $(this).attr('ts');
    var img = $(this).attr('img');
    //menjam klasu forme za novi model iz .formazanovimodel u .formazaupdatemodela
    $('.formazanovimodel').addClass('formazaupdatemodela').removeClass('formazanovimodel');
    //popunjavam polja forme podatcima koje sam uzeo iz atributa kliknutog div-a
    $('#modelname').val(imemodela);
    $('#year').val(year);
    $('#link').val(link);
    $('#smart').val(smart);
    $('#ts').val(ts);
    $('#showimages1').attr('src',  homeurl+'/img/modelibrenda/'+brendid+'/'+img);
    //menjam naslov panela
    $('.formamodelpanelheading').html('Izmeni Model: <b style="color: #FE980F;">' + imemodela + '</b>');
    //dodajem i btn-e za brisanje modela i za Cancel editovanja
    var cont = ' &nbsp;<button class="btn btn-primary" id="cancelem" style="background-color: green;">&nbsp;Cancel&nbsp;</button>';
    cont += ' &nbsp;<button class="btn btn-primary" id="deletemodel" modelid="'+modelid+'" brendid="'+brendid+'" style="background-color: red;">&nbsp;&nbsp;Obriši&nbsp;&nbsp;</button>';
    //dodajem i hidden inpute za idmodela i naziv stare slike, ako dodamo novu sliku staru brisemo pa nam treba njeno ime
    cont += '<input type="hidden" id="idmodela" name="idmodela" value="'+modelid+'">';
    cont += '<input type="hidden" id="imgname" name="imgname" value="'+imgname+'">';
    $('#dodatnibtni').html(cont);//dodadtne btn-e i hiden inpute ubacujem u span #dodatnibtni koji je pored btn-a za sabmit forme 
    scroll('.rowformamodel');//scrolujemo na vrh forme
  });
 
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //klik na Cancel btn u formi za Edit modela
  $('body').on('click', '#cancelem', function(e){
    e.preventDefault();
    emptyphonemodelform();
    //alert('cancel edit modela');
  });
 
//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
  //klik na btn Obrisi ispod forme za edit modela koji salje AJAX preko rute '/deletemodel' u deletemodel() metod BrandsControllera
  $('body').on('click', '#deletemodel', function(e){
    e.preventDefault();
    //uzima se idmodela i idbrenda
    var brendid = $(this).attr('brendid');
    var modelid = $(this).attr('modelid');
    var form_data = new FormData();
    form_data.append('brendid', brendid);
    form_data.append('modelid', modelid);
    //saljemo AJAX preko funkcije sendajax preko rute /deletemodel u deletemodel() BrandsControllera i kao treci argument dajemo dm(DeleteModela)
    sendajax(form_data, urldeletemodel, 'dm');
  });

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

  //sabmit forme za edit modela (to je ista forma kao i za dodavanje novog modela samo sa promenjenom klasom)
  $("body").on("submit", ".formazaupdatemodela", function(e){
    e.preventDefault();
    //alert('forma za novi model');
    $('.help-block').remove();//uklanjamo prikaz prethodnih error msg-a ako ih je bilo
    $('.errormsg').remove();
    var brandid = $('#brandname').val();//uzimamo id brena
    var idmodela = $('#idmodela').val();//uzimamo id modela
    var imgname = $('#imgname').val();//uzimamo putanju do stare slike ako bude trebalo da je obrisemo ako je dodata nova slika
    var modelimg = $('#phoneimg').prop('files')[0];//uzimamo sliku
    var modelname = $('#modelname').val();//uzimamo ime novog modela
    var year = $('#year').val();//uzimamo godister novog modela
    var link = $('#link').val();//uzimamo link ka GSM Arena za ovaj model
    var smart = $('#smart').val();//da li telefon ima smart tehnologiju
    var ts = $('#ts').val();//da li tel. ima touchscreen
    //alert(imgname);
    var form_data = new FormData();//pravimo objekat u kom ce biti podatci
    form_data.append('brandid', brandid);//dodajemo podatke iz forme
    form_data.append('idmodela', idmodela);
    form_data.append('imgname', imgname);
    form_data.append('modelimg', modelimg);
    form_data.append('modelname', modelname);
    form_data.append('year', year);
    form_data.append('link', link);
    form_data.append('smart', smart);
    form_data.append('ts', ts);
    //saljemo AJAX preko funkcije sendajax preko rute /editmodela u editmodela() BrandsControllera i kao treci argument dajemo em(EditModela)
    sendajax(form_data, urleditmodela, 'em');
  });


});