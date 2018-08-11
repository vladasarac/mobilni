//hendlerti za vju novioglas.blade.php 

//funkcija vadi sve modele odredjenog brenda i ubacuje ih u select za biranje modela u formi u novioglas.blade.php
function izvadimodele(formdata, urladdress, bilim){
  //saljemo metodu id marke , izvadimodelurl je definisan na dnu vjua to je ruta '/izvadimodele'
  $.ajax({ 
    method: 'POST',
    url: urladdress,
    data: formdata,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false
  })//kad kontroler vrati odgovor pravimo optione za select modela i lepimo ih na select #modelbrenda tako da se moze birati model
  .done(function(o){ 
    console.log(o);
    var out = '';
    out += '<select name="modelbrenda" id="modelbrenda" class="form-control obaveznopolje">';
    out += '<option></option>';
    for(var i = 0; i < o['modeli'].length; i++){ 
      //ako postoji oldmodel variabla tj ako je jednaka trenutnom modelu u iteraciji to znaci da nije prosla validacija a da je user vec odabrao
      //neki model pa mu ga selectujemo opet da ne bi morao opet da bira model
      if(oldmodel == o['modeli'][i]['id']){
        out += '<option value="'+o['modeli'][i]['id']+'" selected>'+o['modeli'][i]['name']+'</option>';
      }else{
        out += '<option value="'+o['modeli'][i]['id']+'">'+o['modeli'][i]['name']+'</option>'; 
      }     
    }
    //ako user medju ponudjenim modelima ne nadje sta mu treba moze izabrati ovu opciju i hendler za change selecta #modelmarke ako 
    //user izabere ovu opciju polje iz selecta pretvara u text input u koji user moze ukucati model
    out += '<option value="ostalo">Ostalo...</option>';
    out += '</lect>';
    //alert(out);
    $('#modelbrenda').remove();
    $('#modelbrendaselect').html(out);
  });
}

$(window).load(function(){

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
//kad se promni option u selectu za brend telefona
$('#brand').on('change', function(){
  var brand = $('option:selected', this).text();//uzimamo text optiona tj ime brenda
  // alert(brand);
  var idbranda = $('option:selected', this).attr('brandid');//uzimamo id branda
  // alert(idbranda);
  //ako je user izabrao opet prazan option za marku
  if(brand == ''){
    //generisemo opet prazan select za biranje modela kao na pocetku(jer je mozda user izabrao u polju za model ostalo... pa je onda
    //select za izbor modela pretvoren u text input pa sada vracamo u prvobitno stanje) 
    var out1 = '<select name="modelbrenda" id="modelbrenda" class="form-control obaveznopolje">'; 
    out1 += '<option id="prazanoptionzamodele"></option></select>';
    $('#modelbrenda').remove();//brisemo input za upis modela(bez obzira da li je select ili text input)
    $(out1).insertAfter('#modelbrendalabel');//ubacujemo novi select za izbor modela ispod labela za naslov polja za model
    $('#modelbrendalabel').removeClass('text-success');//labelu za ovo polje uklanjamo text-success klasu
    $('#modelbrendalabel').addClass('text-danger');//i dajemo text-danger klasu
    // proveramodela = 0;//takodje opet varijable za proveru polja za model vracamo na 0 i za errormodela vracamo kako je bilo 
    // errormodela = '<br><b>"Model"</b> je obavezno polje!';
  }else if(brand == 'Ostalo...'){//ako je user izabrao opciju 'Ostalo...' tj medu ponudjenim brendovima nema brenda koji mu treba
    var out2 = '<input type="text" class="form-control obaveznopolje" name="brand" id="brand">';//pravimo textr input za brend
    $('#brand').remove();//uklanjamo select za biranje brenda
    $(out2).insertAfter('#brandlabel');//ubacujemo izgenerisani text input umesto selecta
    var out3 = '<input type="text" class="form-control obaveznopolje" name="modelbrenda" id="modelbrenda">';//pravimo textr input za model
    $('#modelbrenda').remove();//uklanjamo select za biranje modela
    $(out3).insertAfter('#modelbrendalabel');//ubacujemo izgenerisani text input umesto selecta
    //menjamo vrednost hidden inputa modovi u 1, vazno radi repopulacije forme ako ne prodje validacija
    $('#modovi').val(1);
  }else{//ako je user selectovao neki brend saljemo AJAX u metod izvadimodele() OglasControllera koji vadi sve modele tog brenda
    var form_data = new FormData();//pravimo objekat u kom ce biti podatci
    form_data.append('idbranda', idbranda);
    form_data.append('_token', token);
    //pozivamo funkciju izvadimodele koja vadi modele i ubacuje ih u select u formi u novioglas.blade.php
    izvadimodele(form_data, izvadimodeleurl, 1);
  } 
});

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------

//kad se promeni option u selectu za brend telefona
$('body').on('change', '#modelbrenda', function(){
  var model = $('option:selected', this).text();//uzimamo text optiona tj ime modela
  if(model == 'Ostalo...'){
    var out3 = '<input type="text" class="form-control obaveznopolje" name="modelbrenda" id="modelbrenda">';
    $('#modelbrenda').remove();//uklanjamo select za biranje modela
    $(out3).insertAfter('#modelbrendalabel');//ubacujemo izgenerisani text input umesto selecta
    $('#modelbrendalabel').removeClass('text-success');//labelu za ovo polje uklanjamo text-success klasu
    $('#modelbrendalabel').addClass('text-danger');//i dajemo text-danger klasu
    //menjamo vrednost hidden inputa modovi u 2, vazno radi repopulacije forme ako ne prodje validacija
    $('#modovi').val(2);
  }
});

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------

//ako nije prosla validacija postoji variabla oldbrand i opet moramo popuniti polja Brend i Model u formi u novioglas.blade.php
if(oldbrand != ''){
  // alert('postoji oldBrand');
  $('#modovi').val(oldmodovi);
  var form_data = new FormData();//pravimo objekat u kom ce biti podatci
  form_data.append('idbranda', oldbrand);
  form_data.append('_token', token);
  //akoje user odabrao opciju Ostalo... u selectu za model(time je select pretvoren u text input i opet ga pravimo i popunjavamo vrednost)
  if(oldmodovi == '2'){
    var out4 = '<input type="text" class="form-control obaveznopolje" value="'+oldmodel+'" name="modelbrenda" id="modelbrenda">';
    $('#modelbrenda').remove();//uklanjamo select za biranje modela
    $(out4).insertAfter('#modelbrendalabel');//ubacujemo izgenerisani text input umesto selecta
  //akoje user odabrao opciju Ostalo... u selectu za brend(time su selecti za brend i model pretvoreni u text inpute i opet ih pravimo i 
  //popunjavamo im prethodno unete vrednosti)
  }else if(oldmodovi == '1'){//pravimo text input za brend
    var out2 = '<input type="text" class="form-control obaveznopolje" value="'+oldbrand+'"name="brand" id="brand">';
    $('#brand').remove();//uklanjamo select za biranje brenda
    $(out2).insertAfter('#brandlabel');//ubacujemo izgenerisani text input umesto selecta
    var out4 = '<input type="text" class="form-control obaveznopolje" value="'+oldmodel+'" name="modelbrenda" id="modelbrenda">';
    $('#modelbrenda').remove();//uklanjamo select za biranje modela
    $(out4).insertAfter('#modelbrendalabel');//ubacujemo izgenerisani text input umesto selecta
  }else{
    //ako je mod 0 onda znaci da su i za model i za brend i dalje select i pozivamo izvadimodele() da napravi select za modele
    //(vju novioglas.blade.php pravi select za brendove)
    izvadimodele(form_data, izvadimodeleurl, 1);
  }  
}

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
  
  //inputi za upload slike prikazuju uploadovanu sliku
  var holder = '';
  //kad korisnik doda sliku prikazi mu je
  function readURL(input){
    if(input.files && input.files[0]){
      var reader = new FileReader();
      reader.onload = function(e){
        $('#'+holder).attr('src', e.target.result);
        //alert(holder);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  //hendler za change za svaku sliku koju user uploaduje(ima ih 12), poziva funkciju readURL da prikaze sliku useru umesto one difoltne
  // koju prikazuje u <labe> elementu
  $("#slika1").change(function(){
    holder = 'slika1holder';
    readURL(this);
  });
  $("#slika2").change(function(){
    holder = 'slika2holder';
    readURL(this);
  });
  $("#slika3").change(function(){
    holder = 'slika3holder';
    readURL(this);
  });
  $("#slika4").change(function(){
    holder = 'slika4holder';
    readURL(this);
  });

//---------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------



});