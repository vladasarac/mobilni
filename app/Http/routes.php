<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//
Route::get('/', 'FrontController@index');

Route::auth();

Route::get('/home', 'HomeController@index');

//gadja metod profil UsersControllera() koji vraca vju profil i salje mu podatke usera
// Route::get('/profil/{id?}/{slike?}/{novioglasid?}', 'UsersController@profil')->name('profil');
Route::get('/profil/{id?}/{novioglasid?}', 'UsersController@profil')->name('profil');

//Route::get('/profil', 'UsersController@profil')->name('profil');

//ruta ide na metod users() UsersControllera a poziva se kad u app.blade.php admin klikne na link korisnici, metod vraca za sada po 5 -
//- korisnika
Route::get('/users/{sort?}', 'UsersController@users')->name('users');

//
Route::post('/searchusers', 'UsersController@searchusers');

//ruta za verifikaciju tj aktivaciju naloga, koristi je registracija.blade.php to je vju koji se salje kao mail novo registrovanom useru i kad
//klikne link koji gadja ovu rutu salje ga na aktivacija() metod UsersControllera koji menja klonu aktivan 'users' tabele iz NULL u 1
Route::get('aktivacija/{verication}', 'UsersController@aktivacija');

//manaktdeakt ruta gadja metod manaktdeakt() UsersControllera koji usera aktivira ili deaktivira
Route::post('manaktdeakt', 'UsersController@manaktdeakt'); 

//ruta zove metod index() BrandsControlelra koji vraca vju dodajbrand.blade.php
Route::get('/dodajbrend', 'BrandsController@index');

//ruta poziva metod dodajbrend() BrandsControllera kad se sabmituje forma za dodavanje novog brenda u dodajbrand.blade.php(ide AJAX iz dodajbrand.js)
Route::post('/dodajbrendforma', 'BrandsController@dodajbrend');

//ruta poziva metod dodajmodel()BrandsControllera kad se sabmituje forma za dodavanje novog modela u dodajbrand.blade.php(ide AJAX iz dodajbrand.js) 
Route::post('/dodajmodelforma', 'BrandsController@dodajmodel');

//ruta poziva metod modelibrenda() BrandsControllera kad se u dodajbrand.blade.php klikne div btn ##svimodelibrenda ili ##josmodelabrenda kad ad
//min hoce ispod forme za unos novog modela da vidi do sad unete modele telefona 
Route::post('/modelibrenda', 'BrandsController@modelibrenda');

//kad se sabmittuje forma za edit modela u dodajbrand.blade.php(ista forma kao i za unos novog modela) salje se AJAX preko ove rute u metod -
//-editmodela() BrandsControllera koji radi update odgovarajuceg reda u 'phonemodels' tabeli
Route::post('/editmodela', 'BrandsController@editmodela');

//kad se klikne btn Obrisi u dodajbrand.blade.php ispod forme za edit modela salje se preko ove rutu AJAX iz dodajbrand.js u deletemodel() metod
//BrandsController koji brise po pristiglom id-u red u 'phonemodels' tabeli i smanjuje za 1 vrednost kolone brojmodela 'brands' tabele
Route::post('/deletemodel', 'BrandsController@deletemodel');

//ruta za edit brenda kad se sabmituje forma za edit brenda u dodajbrand.blade.php ide na metod editbrenda() BrandsControllera
Route::post('/editbrenda', 'BrandsController@editbrenda');

//kad se klikn btn Obrisi tj #deletebrend u dodajbrand.blade.php ispod forme za edit brenda, preko ove rute ide AJAX u deleterend() metod BrandsCo-
//ntrollera koji dobija id brenda koji brise i zatim brise njegove modele, logo i oflder sa slikama modela
Route::post('/deletebrend', 'BrandsController@deletebrend');

//kad se klikne link AllBrands u navigaciji preko ove rute se poziva metod allbrands FrontControllera koji vadi sve brendove i prikazuje ih -
//-u vjuu allbrands.blade.php
Route::get('/allbrands', 'FrontController@allbrands');

//kad se klikne neki brend u dropdownu 'Brands' u app.blade.php preko ove rute se poziva metod brandmodels() FrontControllera
Route::get('/brandmodels/{id}', 'FrontController@brandmodels');

//kad se klikne div btn 'Ucitaj Jos Model'('#josmodelabrenda') u brandmodels.blade.php preko ove rute ide AJAX u metod brandmodelsmore() Front-
//Controllera
Route::post('/brandmodelsmore', 'FrontController@brandmodelsmore');

//kad se klikne neki model u brandmodels.blade.php
Route::get('/modeldetails/{id}', 'FrontController@modeldetails');

//ruta se koristi kad se sabmituje forma za edit podataka usera u profile.blade.php
Route::post('/useredit', 'UsersController@useredit');

//kad se u formi za dodavanje ili izmenu podataka korisnika u profil.blade.php (ako user ima dodat logo) klikne btn Obrisi Logo, hendler u 
//-profil.js salje ajax deletelogo() metodu UsersControllera koristeci ovu rutu
Route::post('/deletelogo', 'UsersController@deletelogo');

//ruta gadja metod novioglas() OglasControllera koji vraca vju novioglas.blade.php u kom je forma za oglas, ako je dat argument id to znaci da
//admin poziva metod tj da ce admin za usera upisati oglas a ako nema parametra znaci da user za sebe upisuje oglas
Route::get('/novioglas/{id?}', 'OglasController@novioglas');

//ruta se koristi kad u novioglas.blade.php u selectu za odabir brenda telefona odaberemo brend, onda preko ove rute ide iz novioglas.js
//AJAX ka metodu izvadimodele() OglasControllera koji vadi sve modele brenda koji smo odabrali
Route::post('/izvadimodele', 'OglasController@izvadimodele');

//kad se sabmituje forma za novioglas u novioglas.blade.php iz 'mobilni\resources\views\users'
Route::post('/upisinovioglas', 'OglasController@upisioglas');

//Kad admin u profile.blade.php klikne btn OdobriOglas iz profile.js ide AJAX u kom je id oglasa koji se odobraba u metod odobrioglas()
Route::post('/odobrioglas', 'OglasController@odobrioglas');

//Kad admin u profile.blade.php klikne btn ZabraniOglas iz profile.js ide AJAX u kom je id oglasa koji se odobraba u metod zabranioglas()
Route::post('/zabranioglas', 'OglasController@zabranioglas');

//kad se klikne btn ObrisiOglas u profile.blade.php pored nekog oglasa, ide AJAX na metod obrisioglas OglasControllera
Route::post('/obrisioglas', 'OglasController@obrisioglas');















