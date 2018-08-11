<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Auth;
use Illuminate\Http\RedirectResponse;
use Redirect;
use App\User;
use App\Brand;
use App\Phonemodel;
use App\PhoneModelData;


class FrontController extends Controller
{


//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod ucitava pocetnu stranicu sajta
public function index(){
  //vadimo 5 brandova sa najvise dodatih modela za dropdown u navigaciji	
  // $popularbrands = Brand::whereNotNull('brojmodela')->orderBy('brojmodela', 'DESC')->skip(0)->take(5)->get();
  return view('welcome');	
}	

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod vadi sve brendove kad se u dropdown-u Brands klikne opcija AllBrands i ovde vadimo sve iz 'brands' tabele i saljemo u vju allbrands.
//blade.php na prikazivanje
public function allbrands(Request $request){
  $brands = Brand::orderBy('name', 'asc')->get();//vadimo sve brendove
  $brandstotal = $brands->count();  //ukupno brendova
  return view('allbrands')->withBrands($brands)->withBrandstotal($brandstotal);//pozivamo vju i saljemo mu podatke
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod vadi za sada 12 modela brenda ciji id je stigao i salje ih u vju brandmodels.blade.php na prikazivanje  
public function brandmodels(Request $request, $brendid){
  //vadimo brend ciji je id stigao
  $brand = Brand::where('id', $brendid)->first();
  //vadimo 5 brandova sa najvise dodatih modela za dropdown u navigaciji	
  // $popularbrands = Brand::whereNotNull('brojmodela')->orderBy('brojmodela', 'DESC')->skip(0)->take(5)->get();
  //koliko modela vadimo po stranica
  $brojmodelapostr = 12;
  //vadimo 12 najnovijih modela brenda
  $brandmodels = Phonemodel::where('brand_id', $brendid)->orderBy('created_at', 'DESC')->skip(0)->take($brojmodelapostr)->get();
  //pozivamo vju brandmodels.blade.php da prikaze izvadjene modele 
  return view('brandmodels')->withBrand($brand)
  							->withBrandmodels($brandmodels)
  							->withBrojmodelapostr($brojmodelapostr);	
} 

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//kad se u brandmodels.blade.php klikne btn Jos Modela iz brandmodels.js stize AJAX u kom je id brenda cije modele vadimo i skip tj koliko 
//modela preskacemo i brojmodelapostr tj koliko modela vadimo, metod radi query i vraca JSON da bi brandmodels.js prikazao modele ispod vec
//prikazanih modela nekog brenda 
public function brandmodelsmore(Request $request){
  $brendid = $request->get('brendid'); 	
  $skip = $request->get('skip');
  $brojmodelapostr = $request->get('brojmodelapostr');		
  //vadimo 12 najnovijih modela brenda
  $brandmodels = Phonemodel::where('brand_id', $brendid)->orderBy('created_at', 'DESC')->skip($skip)->take($brojmodelapostr)->get();
  // $brandmodels = Phonemodel::where('brand_id', $brendid)->orderBy('created_at', 'DESC')->skip($skip)->take($brojmodelapostr)
                 // ->with('phone_model_data')->with('brand')->with('oglas')->get();
  return response()->json(['brandmodels' => $brandmodels]);
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod vadi model ciji je id stigao iz tabele 'phonemodels' i salje ga u vju modeldetails.blade.php gde se prikazuje model i njegovi podatci-
//iz tabele 'phone_model_datas'
public function modeldetails(Request $request, $modelid){
  $model = Phonemodel::where('id', $modelid)->first();//vadimo model ciji je id stigao
  return view('modeldetails')->withModel($model);
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------



}