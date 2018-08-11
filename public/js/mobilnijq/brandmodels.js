$(window).load(function(){

  // alert(urlmodeldetails);

// ovde su hendleri za vju brandmodels.blade.php koji prikazuje modele nekog brenda u front endu kad se klikne neki brend u dropdown-u u navi-
//-gaciji na vrhu stranice

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod salje AJAX gde mu se kaze i kad dobije odgovor radi sta treba
function sendajax(formdata, urladdress, bilim){
  $.ajaxSetup({//Laravelov _token
    headers: {
      'X-CSRF-Token': $('meta[name=_token]').attr('content')
    }
  });
  $.ajax({
    method: 'POST',
    url: urladdress,
    data: formdata,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })
  .done(function(o){
  	console.log(o);
    if(bilim == 'jmb'){ // ako je kliknut btn Ucitaj Jos Modela ('#josmodelabrenda') iz brandmodels.blade.php
      skip = skip + brojmodelapostr;
      var cont = '<div class="row"> ';
      for(var i = 0; i < o['brandmodels'].length; i++){
        cont += '<a href="'+urlmodeldetails+'/'+o['brandmodels'][i]['id']+'" target="_blank">';
        cont += '<div class="col-md-2 col-xs-6" style="padding-bottom: 25px;">';
        cont += '<img class="img-responsive" src="'+homeurl+'/img/modelibrenda/'+o['brandmodels'][i]['brand_id']+'/'+o['brandmodels'][i]['img']+'"><br>';  
        cont += '<p class="text-center"><small class="imemodelasmall">'+o['brandmodels'][i]['name']+'</small></p>'; 
        cont += '</div>';
        cont += '</a>'; 
        if(((i + 1) % 6 == 0) && (i + 1) < brojmodelapostr){
          cont += '</div><div class="row">';
        }else if((i + 1) % 6 == 0){
          cont += '</div>';
        }
      }
      $(cont).appendTo('.divjosmodelabrenda');//dodajemo izgenerisanni HTML u div .josmodelabrenda u brandmodels.blade.php
      $('#josmodelabrenda').addClass('shadow');
      if(skip >= brojmodela){
        $('#josmodelabrenda').remove(); 
      }
    }	
  });
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//klik na div btn Ucitaj Jos Modela ispod prikazanih modela nekog brenda u brandmodels.blade.php, hendler uzima id brenda ciji modeli se -
//-prikazuju i skip(koliko je do sad prikazano modela) i brojmodelapostr(tj koliko da se jos izvadi iz baze) i poziva metod sendajax() koji-
//-ce poslati AJAX u metod brandmodelsmore() FrontControllera i kad dobije odgovor prikazati modele izvadjene iz baze
$('body').on('click', '#josmodelabrenda', function(e){
  $(this).removeClass('shadow');
  var brendid = $(this).attr('brendid');
  var form_data = new FormData();//pravimo objekat u kom ce biti podatci
  form_data.append('brendid', brendid);//saljemo id brenda cije modele vadimo
  form_data.append('skip', skip);//skip i take(skip je sada povecan)
  form_data.append('brojmodelapostr', brojmodelapostr);
  sendajax(form_data, urlbrandmodelsmore, 'jmb');
});

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

});