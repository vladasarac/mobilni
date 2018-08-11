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

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;

class BrandsController extends Controller
{


public function __construct(){
  $this->middleware('auth');
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod vraca vju dodajbrand.blade.php iz 'mobilni\resources\views\admin' u kom je forma za dodavanje novog brenda
public function index(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){	
    //vadimo sve iz 'brands' tabele(order po name koloni),ovo nam treba da bi mogli da napravimo select u formi za dodavanje modela nekog branda
    $brends = Brand::orderBy("name")->get();
    return view('admin.dodajbrand')->withBrends($brends);// ako jeste saljemo ga na vju dodajbrand.blade.php
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
  	return redirect('/');
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod upisuje red u 'brands' tabelu, kad se sabmituje forma u dodajbrand.blade.php ide AJAX iz dodajbrand.js preko rute '/dodajbrendforma'  -
//-i stize ime brenda i logo brenda
public function dodajbrend(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    //prvo validacija
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:brands|max:255',
      'logoimage' => 'required|image'
    ]);
    //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
    if($validator->fails()) {    
      return response()->json(['errors'=>$validator->errors()]);
    }
    //ako validacija prodje upisujemo red u 'brands' tabelu
    $brand = new Brand();
    $brand->name = $request->get('name');
    $logo = $request->file('logoimage');//uzimamo sliku koju je user uploadovao
    $logoName = str_slug($request->get('name')).'.png';//pravimo ime slike
    $image_resize = Image::make($logo->getRealPath());//koristeci Intervention\Image libratry resize-ujemo sliku
    $image_resize->resize(100, 100);
    $image_resize->save(public_path('img/brands/' .$logoName));//cuvamo sliku tj logo u folderu 'mobilni\public\img\brands'
    $brand->logo = $logoName;//podesavamo kolonu logo 'brands' tabele   
    $saved = $brand->save();//cuvamo u bazi ime i logo
    //pravimo folder za slike modela brenda u folderu mobilni\public\img\modelibrenda\id brenda
    $path = public_path().'/img/modelibrenda/' . $brand->id;
    File::makeDirectory($path, $mode = 0777, true, true);
    //vracamo hendleru u brands.js koji je poslao AJAX poruku da je uspeo upis u tabelu
    return response()->json(['saved' => $saved, 'name' => $brand->name, 'logoName' => $logoName]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod upisuje novi model u 'phonemodels' tabelu, stize AJAX iz dodajbrand.js kad se u dodajbrand.blade.php sabmituje forma za dodavanje novog -
//-modela
public function dodajmodel(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    //prvo validacija
    $validator = Validator::make($request->all(), [
      'brandid' => 'required|integer',
      'modelname' => 'required|max:255',
      'year' => 'required|integer',
      'link' => 'required|string',
      'smart' => 'required|integer|between:0,1',
      'ts' => 'required|integer|between:0,1',
      'modelimg' => 'required|image'
    ]);
    //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
    if($validator->fails()) {    
      return response()->json(['errors'=>$validator->errors()]);
    }
    //proveravamo da li je model vec upisan tj da li u tabeli 'phonemodels' postoji red sa id-em brenda i imenom modela koji su stigli u requestu
    $exists = Phonemodel::where('brand_id', $request->get('brandid'))->where('name', $request->get('modelname'))->first();
    if($exists){ //ako postoji model je vec upisan i vracamo JSON u dodajbrand.js da je model vec upisan
      return response()->json(['saved' => false, 'upisan' => 'Model istog imena i brenda je već upisan u bazu.']);
    }
    //ako validacija prodje i model jos nije upisan u bazu upisujemo red u 'phonemodels' tabelu
    $phonemodel = new Phonemodel();
    $phonemodel->brand_id = $request->get('brandid');
    $brandname = Brand::where('id', $phonemodel->brand_id)->value('name');//vadimo ime brenda kom smo dodali model
    $phonemodel->name = $request->get('modelname');
    $phonemodel->year = $request->get('year');
    $phonemodel->link = $request->get('link');
    $phonemodel->smart = $request->get('smart');
    $phonemodel->ts = $request->get('ts');
    $modelimg = $request->file('modelimg');//uzimamo sliku koju je user uploadovao
    $img = str_slug($request->get('modelname')).'.jpg';//pravimo ime slike
    // $image_resize = Image::make($logo->getRealPath());//koristeci Intervention\Image libratry resize-ujemo sliku
    // $image_resize->resize(100, 100);
    // $image_resize->save(public_path('img/brands/' .$logoName));//cuvamo sliku tj logo u folderu 'mobilni\public\img\brands'
    $modelimg->move(public_path().'/img/modelibrenda/' . $request->get('brandid'), $img);
    $phonemodel->img = $img;//podesavamo kolonu logo 'brands' tabele
    $saved = $phonemodel->save();//cuvamo red u bazi
    //vadimo vrednost kolone brojmodela 'brands' tabele
    $brojmodela = Brand::where('id', $request->get('brandid'))->value('brojmodela');
    //posto je inicijalno kolona brojmodela 'brands' tabele NULL nije moguce increment-ovati NULL za 1 pa ako je rec o upisu prvog modela nekog
    //brenda rucno menjamo kolonu brojmodela u 1 a ako to nije slucaj(vec upisujemo recimo 10 model po redu) radimo samo increment
    if($brojmodela == null){
      Brand::where('id', $request->get('brandid'))->update(['brojmodela' => 1]);
    }else{
      Brand::find($request->get('brandid'))->increment('brojmodela');
    }    
    //vracamo hendleru u brands.js koji je poslao AJAX poruku da je uspeo upis u tabelu
    return response()->json(['saved' => 1, 'brandid' => $phonemodel->brand_id, 'brandname' => $brandname, 'modelname' => $phonemodel->name, 'img' => $phonemodel->img]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod vadi modele iz 'phonemodels' tabele kad se u dodajbrand.blade.php klikne div btn #svimodelibrenda ili #josmodelabrenda kad admin 
//hoce ispod forme za unos novog modela da vidi do sad unete modele telefona, stize AJAX preko rute '/modelibrenda' 
public function modelibrenda(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    $skip = $request->get('skip');//skip i take su definisani u dodajbrand.js
    $take = $request->get('take');
    $brand_id = $request->get('brendid');
    $modelibrenda = Phonemodel::where('brand_id', $brand_id)->orderBy('created_at', 'DESC')->skip($skip)->take($take)->get();
    //vadimo i brend cije modele vadimo
    $brand = Brand::where('id', $brand_id)->first();
    return response()->json(['modelibrenda' => $modelibrenda, 'brand' => $brand]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod prima AJAX iz dodajbrand.js kad se sabmituje forma za edit modela (to je ista forma kao i za dodavanje novog modela samo joj je promenjena klasa) preko rute '/editmodela'
public function editmodela(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    //nalazimo model koji cemo update - ovati u 'phonemodels' tabeli 
    $model = Phonemodel::where('id', $request->get('idmodela'))->first();
    $brand = Brand::where('id', $request->get('brandid'))->first();
    //proveravamo da li je model vec upisan tj da li u tabeli 'phonemodels' postoji red sa id-em brenda i imenom modela koji su stigli u requestu
    //ali da to nije model koji menjamo tj da id kolona nije jednaka id-u modela koji je stigao
    $exists = Phonemodel::where('brand_id', $request->get('brandid'))
                        ->where('name', $request->get('modelname'))
                        ->where('id', '!=', $request->get('idmodela'))
                        ->first();
    if($exists){ //ako postoji model je vec upisan i vracamo JSON u dodajbrand.js da je model vec upisan
      return response()->json(['saved' => false, 'upisan' => 'Model istog imena i brenda je već upisan u bazu.']);
    }
    //ako ima dodata slika 
    if($request->file('modelimg')){
      //validacija sa slikom
      $validator = Validator::make($request->all(), [
        'brandid' => 'required|integer',
        'modelname' => 'required|max:255',
        'year' => 'required|integer',
        'link' => 'required|string',
        'smart' => 'required|integer|between:0,1',
        'ts' => 'required|integer|between:0,1',
        'modelimg' => 'required|image'
      ]);
      //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
      if($validator->fails()) {    
        return response()->json(['errors'=>$validator->errors()]);
      }
      //brisemo staru sliku iz foldera 'mobilni\public\img\modelibrenda\id_brenda' i uploadujemo novu sliku
      $modelimg = $request->file('modelimg');
      unlink(public_path('img/modelibrenda/' . $request->get('brandid') . '/' . $request->get('imgname')));
      $img = str_slug($request->get('modelname')).'.jpg';//pravimo ime slike
      $modelimg->move(public_path().'/img/modelibrenda/' . $request->get('brandid'), $img);
      $model->img = $img;
    }else{//ako nema dodata slika
      $img = $request->get('imgname');//ako nije dodata nova slika vraticemo u JSON-u ime stare slike 
      //validacija bez slike
      $validator = Validator::make($request->all(), [
        'brandid' => 'required|integer',
        'modelname' => 'required|max:255',
        'year' => 'required|integer',
        'link' => 'required|string',
        'smart' => 'required|integer|between:0,1',
        'ts' => 'required|integer|between:0,1',
      ]);
      //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
      if($validator->fails()) {    
        return response()->json(['errors'=>$validator->errors()]);
      }
    }
    $model->name = $request->get('modelname');//upisujemo vrednosti u kolone tabele 'phonemodels'
    $model->year = $request->get('year');
    $model->link = $request->get('link');
    $model->smart = $request->get('smart');
    $model->ts = $request->get('ts');
    $saved = $model->save();//cuvamo uneto
    //vracamo hendleru u brands.js koji je poslao AJAX poruku da je uspeo upis u tabelu
    return response()->json(['saved' => 1, 'brand' => $brand, 'modelid' => $request->get('idmodela'), 'modelname' => $model->name, 'img' => $img]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod brise red iz 'phonemodels' tabele po id koji stigne kad se klikne btn Obrisi u dodajbrand.blade.php. Stize AJAX iz dodajbrands.js preko 
//rute '/deletemodel' u kom su idmodela i idbrenda. takodje metod brise sliku modela i smanjuje za 1 vrednost kolone brojmodela 'brands' tabele
//u redu ciji je idbrenda stigao
public function deletemodel(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    $modelid = $request->get('modelid');
    $model = Phonemodel::where('id', $modelid)->first();//nalazimo model po pristiglom id-u
    //$brand = Brand::where('id', $request->get('brendid'))->first();//nalazimo brend po pristiglom id-u
    //brisemo sliku modela iz 'mobilni\public\img\modelibrenda\$idbrenda'
    unlink(public_path('img/modelibrenda/' . $request->get('brendid') . '/' . $model->img));
    $delete = $model->delete();
    if($delete){//ako uspe brisanje
      Brand::find($request->get('brendid'))->decrement('brojmodela');//smanjujemo vrednost kolone brojmodela 'brands' tabele gde je idbrenda
    }
    return response()->json(['delete' => $delete, 'modelid' => $modelid]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//kad se u dodajbrand.blade.php sabmituje forma za edit brenda(koja je i forma za dodavanje novog brenda samo joj je sad promenjena klasa) preko-
//rute '/editbrenda' stize AJAX iz dodajbrand.js u kom je unos u formu i metod updateuje red u 'brands' tabeli po id-u koji je stigao
public function editbrenda(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    $brand = Brand::where('id', $request->get('idbrenda'))->first();//nalazimo red za update
    //proveravamo da li postoji neki drugi red koji ima istu name kolonu kao ono ime koje je uneto
    $exists = Brand::where('name', $request->get('name'))
                   ->where('id', '!=', $request->get('idbrenda'))
                   ->first();
    if($exists){ //ako postoji brend istog imena a drugog id-a vracamo JSON u dodajbrand.js da je brend vec upisan
      return response()->json(['saved' => false, 'upisan' => 'Brand istog imena je već upisan u bazu.']);
    }
    if($request->file('logoimage')){//ako ima uploadovana slika
      //validacija sa slikom
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
        'logoimage' => 'required|image'
      ]);
      //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
      if($validator->fails()) {    
        return response()->json(['errors'=>$validator->errors()]);
      }
      //brisemo staru sliku iz foldera 'mobilni\public\img\brands' i uploadujemo novu sliku
      $logoimage = $request->file('logoimage');
      unlink(public_path('img/brands/' . $request->get('logobrenda')));
      $img = str_slug($request->get('name')).'.jpg';//pravimo ime slike
      $logoimage->move(public_path().'/img/brands/', $img);
      $brand->logo = $img;
    }else{//ako nema uploadovana slika
      $img = $request->get('logobrenda');//ako nije dodata nova slika vraticemo u JSON-u ime stare slike 
      //validacija bez slike
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255'
      ]);
      //ako ne prodje validacija saljemo hendleru u brands.js error-e u JSON formatu
      if($validator->fails()) {    
        return response()->json(['errors'=>$validator->errors()]);
      }
    }
    //upisujemo novu vrednost kolone name i cuvamo unos
    $brand->name = $request->get('name');
    $saved = $brand->save();
    //vracamo hendleru u brands.js koji je poslao AJAX poruku da je uspeo upis u tabelu
    return response()->json(['saved' => 1, 'brand' => $brand, 'img' => $img]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod dobija AJAX iz dodajbrand.js preko rute kad se klikne btn Obrisi ispod formwe za edit brenda u dodajbrand.blade.php
public function deletebrend(Request $request){
  //koristeci is_admin() metod User.php modela proveravamo da li je user admin
  if($request->user()->is_admin()){
    $brendid = $request->get('brendid');
    $brend = Brand::where('id', $brendid)->first();//nalazimo brend po pristiglom id-u
    //brisemo folder sa slikama modela brenda 'mobilni\public\img\modelibrenda\idbrenda' 
    File::deleteDirectory(public_path('img/modelibrenda/' . $brendid));
    // brisemo logo brenda iz 'mobilni\public\img\brands'
    unlink(public_path('img/brands/' . $brend->logo));
    //brisemo red 'brands' tabele a posto je ondelete->cascade bice obrisani svi redovi 'phonemodels' tabele gde je brend_id isti kao id brenda koji smo obrisali iz 'brands' tabele
    $delete = $brend->delete();
    return response()->json(['delete' => $delete, 'brendid' => $brendid]);
  }else{ //ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

private function maketext($htmlstart, $br1, $br2, $htmlend, $content){
  $pozicija1 = strpos($content, $htmlstart);
  if($pozicija1){
    $text = substr($content, $pozicija1 + $br1, $br2);
    $pozicija2 = strpos($text, $htmlend);
    $text = substr($text, 0, $pozicija2);
  }else{
    $text = 'N/A';
    $pozicija2 = 'N/A';
  }
  return array($text, $pozicija2);
}

//
public function getcontent(Request $request){
  $smart = 1;
  // $content = file_get_contents('https://www.gsmarena.com/xiaomi_redmi_note_4x-8580.php'); 
  // $content = file_get_contents('https://www.gsmarena.com/acer_betouch_e200-2961.php'); 
  // $content = file_get_contents('https://www.gsmarena.com/apple_iphone_7_plus-8065.php');
  $content = file_get_contents('https://www.gsmarena.com/samsung_galaxy_s9-8966.php');
  // $content = file_get_contents('https://www.gsmarena.com/blackberry_bold_9650-3284.php');
  // 

  // $smart = 0;
  // $content = file_get_contents('https://www.gsmarena.com/nokia_3410-315.php');
  // $content = file_get_contents('https://www.gsmarena.com/motorola_c331-363.php');
  // $content = file_get_contents('https://www.gsmarena.com/ericsson_s_868-111.php');
  
  $pozicija1 = strpos($content, '<div id="specs-list">');
  $pozicija2 = strpos($content, '<p class="note">');
  $content = substr($content, $pozicija1, ($pozicija2 - $pozicija1));
  //Network
  // $pozicijaNetwork = strpos($content, 'nettech">');
  // if($pozicijaNetwork){
  //   $network = substr($content, $pozicijaNetwork + 9, 70);
  //   $pozicijaNetwork2 = strpos($network, '</a></td>');
  //   $network = substr($network, 0, $pozicijaNetwork2);
  // }else{
  //   $network = 'N/A';
  //   $pozicijaNetwork2 = 'N/A';
  // }

  $network = $this->maketext('nettech">', 9, 70, '</a></td>', $content);

  //Year
  $pozicijaYear = strpos($content, 'year">');
  if($pozicijaYear){
    $year = substr($content, $pozicijaYear + 6, 52);
    $pozicijaYear2 = strpos($year, '</td>');
    $year = substr($year, 0, $pozicijaYear2);
  }else{
    $year = 'N/A';
    $pozicijaYear2 = 'N/A';
  }
  //Dimensions
  $pozicijaDimensions = strpos($content, 'dimensions">');
  if($pozicijaDimensions){
    $dimensions = substr($content, $pozicijaDimensions + 12, 55);
    $pozicijaDimensions2 = strpos($dimensions, '</td>');
    $dimensions = substr($dimensions, 0, $pozicijaDimensions2);
  }else{
    $dimensions = 'N/A';
    $pozicijaDimensions2 = 'N/A';
  }
  //Weight
  $pozicijaWeight = strpos($content, 'weight">');
  if($pozicijaWeight){
    $weight = substr($content, $pozicijaWeight + 8, 22);
    $pozicijaWeight2 = strpos($weight, '</td>');
    $weight = substr($weight, 0, $pozicijaWeight2);
  }else{
    $weight = 'N/A';
    $pozicijaWeight2 = 'N/A';
  }
  //Sim
  $pozicijaSim = strpos($content, 'spec="sim">');
  if($pozicijaSim){
    $sim = substr($content, $pozicijaSim + 11, 255);
    $pozicijaSim2 = strpos($sim, '</td>');
    $sim = substr($sim, 0, $pozicijaSim2);
  }else{
    $sim = 'N/A';
    $pozicijaSim2 = 'N/A';
  }
  //Displaytype
  $pozicijaDisplaytype = strpos($content, 'displaytype">');
  if($pozicijaDisplaytype){
    $displaytype = substr($content, $pozicijaDisplaytype + 13, 70);
    $pozicijaDisplaytype2 = strpos($displaytype, '</td>');
    $displaytype = substr($displaytype, 0, $pozicijaDisplaytype2);
  }else{
    $displaytype = 'N/A';
    $pozicijaDisplaytype2 = 'N/A';
  }
  //DisplaySize
  $pozicijaDisplaysize = strpos($content, 'displaysize">');
  if($pozicijaDisplaysize){
    $displaysize = substr($content, $pozicijaDisplaysize + 13, 70);
    $pozicijaDisplaysize2 = strpos($displaysize, '</td>');
    $displaysize = substr($displaysize, 0, $pozicijaDisplaysize2); 
  }else{
    $displaysize = 'N/A';
    $pozicijaDisplaysize2 = 'N/A';
  }
  if($displaysize == ''){
    $displaysize = 'N/A';
    $pozicijaDisplaysize2 = 'N/A';
  }  
  if($smart != 0){
    //OS
    $pozicijaOS = strpos($content, 'spec="os">');
    if($pozicijaOS){
      $os = substr($content, $pozicijaOS + 10, 70);
      $pozicijaOS2 = strpos($os, '</td>');
      $os = substr($os, 0, $pozicijaOS2);
    }else{
      $os = 'N/A';
      $pozicijaOS2 = 'N/A';
    }  
    //ChipSet
    $pozicijaChipSet = strpos($content, 'spec="chipset">');
    if($pozicijaChipSet){
      $chipset = substr($content, $pozicijaChipSet + 15, 255);
      $pozicijaChipSet2 = strpos($chipset, '</td>');
      $chipset = substr($chipset, 0, $pozicijaChipSet2);
    }else{
      $chipset = 'N/A';
      $pozicijaChipSet2 = 'N/A';
    }   
    //CPU
    $pozicijaCPU = strpos($content, 'spec="cpu">');
    if($pozicijaCPU){
      $cpu = substr($content, $pozicijaCPU + 11, 255);
      $pozicijaCPU2 = strpos($cpu, '</td>');
      $cpu = substr($cpu, 0, $pozicijaCPU2);
    }else{
      $cpu = 'N/A';
      $pozicijaCPU2 = 'N/A';
    }  
    //GPU
    $pozicijaGPU = strpos($content, 'spec="gpu">');
    if($pozicijaGPU){
      $gpu = substr($content, $pozicijaGPU + 11, 255);
      $pozicijaGPU2 = strpos($gpu, '</td>');
      $gpu = substr($gpu, 0, $pozicijaGPU2);
    }else{
      $gpu = 'N/A';
      $pozicijaGPU2 = 'N/A';
    }   
  }else{
    $os = 'N/A';
    $pozicijaOS2 = 'N/A';
    $chipset = 'N/A';
    $pozicijaChipSet2 = 'N/A';
    $cpu = 'N/A';
    $pozicijaCPU2 = 'N/A';
    $gpu = 'N/A';
    $pozicijaGPU2 = 'N/A';
  }
  //DisplayResolution
  $pozicijaDisplayres = strpos($content, 'displayresolution">');
  if($pozicijaDisplayres){
    $displayres = substr($content, $pozicijaDisplayres + 19, 70);
    $pozicijaDisplayres2 = strpos($displayres, '</td>');
    $displayres = substr($displayres, 0, $pozicijaDisplayres2);
  }else{
    $displayres = 'N/A';
    $pozicijaDisplayres2 = 'N/A';
  }
  //MemorySlot
  $pozicijaMemorySlot = strpos($content, 'memoryslot">');
  if($pozicijaMemorySlot){
    $memoryslot = substr($content, $pozicijaMemorySlot + 12, 70);
    $pozicijaMemorySlot2 = strpos($memoryslot, '</td>');
    $memoryslot = substr($memoryslot, 0, $pozicijaMemorySlot2);
  }else{
    $memoryslot = 'N/A';
    $pozicijaMemorySlot2 = 'N/A'; 
  }
  //InternalMemory
  $pozicijaInternalMemory = strpos($content, 'internalmemory">');
  if($pozicijaInternalMemory){
    $internalmemory = substr($content, $pozicijaInternalMemory + 16, 70);
    $pozicijaInternalMemory2 = strpos($internalmemory, '</td>');
    $internalmemory = substr($internalmemory, 0, $pozicijaInternalMemory2);
  }else{
    $internalmemory = 'N/A';
    $pozicijaInternalMemory2 = 'N/A';
  }
  //PhoneBook
  $pozicijaPhoneBook = strpos($content, 'Phonebook</a></td>');
  if($pozicijaPhoneBook){
    $phonebook = substr($content, $pozicijaPhoneBook + 36, 10);
    $pozicijaPhoneBook2 = strpos($phonebook, '</td>');
    $phonebook = substr($phonebook, 0, $pozicijaPhoneBook2);
  }else{
    $phonebook = 'N/A';
    $pozicijaPhoneBook2 = 'N/A';
  }
  //CameraPrimary
  $pozicijaCameraPrimary = strpos($content, 'cameraprimary">');
  $cameraprimary = substr($content, $pozicijaCameraPrimary + 15, 255);
  $pozicijaCameraPrimary2 = strpos($cameraprimary, '<a href="piccmp');//ako ima dodat link
  if(!$pozicijaCameraPrimary2){//ako nema dodat link onda se sece na td
    $pozicijaCameraPrimary2 = strpos($cameraprimary, '</td>');
  }
  $cameraprimary = substr($cameraprimary, 0, $pozicijaCameraPrimary2);
  $camerafeatures = 'N/A';
  $pozicijaCameraFeatures2 = 'N/A';
  $cameravideo = 'N/A';
  $pozicijaCameraVideo2 = 'N/A';
  $camerasecond = 'N/A';
  $pozicijaCameraSecond2 = 'N/A';
  //ako model ima kameru vadimo i CameraFeatures, CameraVideo i CameraSecond
  if($cameraprimary != 'No'){
    //CameraFeatures
    $pozicijaCameraFeatures = strpos($content, 'camerafeatures">');
    if($pozicijaCameraFeatures){
      $camerafeatures = substr($content, $pozicijaCameraFeatures + 16, 255);
      $pozicijaCameraFeatures2 = strpos($camerafeatures, '</td>');
      $camerafeatures = substr($camerafeatures, 0, $pozicijaCameraFeatures2);
    } 
    //CameraVideo
    $pozicijaCameraVideo = strpos($content, 'cameravideo">');
    if($pozicijaCameraVideo){
      $cameravideo = substr($content, $pozicijaCameraVideo + 13, 255);
      $pozicijaCameraVideo2 = strpos($cameravideo, '<a href="vidcmp');//ako ima dodat link
      if(!$pozicijaCameraVideo2){//ako nema dodat link onda se sece na td
        $pozicijaCameraVideo2 = strpos($cameravideo, '</td>');
      }  
      $cameravideo = substr($cameravideo, 0, $pozicijaCameraVideo2);
    }
    //CameraSecond
    $pozicijaCameraSecond = strpos($content, 'camerasecondary">');
    if($pozicijaCameraSecond){
      $camerasecond = substr($content, $pozicijaCameraSecond + 17, 255);
      $pozicijaCameraSecond2 = strpos($camerasecond, '</td>');
      $camerasecond = substr($camerasecond, 0, $pozicijaCameraSecond2);
      if($camerasecond == ''){
        $camerasecond = 'N/A';
        $pozicijaCameraSecond2 = 'N/A';
      }
    } 
  }
  //AlertTypes
  $pozicijaAlertTypes = strpos($content, 'Alert types</a></td>');
  if($pozicijaAlertTypes){
    $alerttypes = substr($content, $pozicijaAlertTypes + 38, 170);
    $pozicijaAlertTypes2 = strpos($alerttypes, '</td>');
    $alerttypes = substr($alerttypes, 0, $pozicijaAlertTypes2);
  }else{
    $alerttypes = 'N/A';
    $pozicijaAlertTypes2 = 'N/A';
  }
  //Loudspeaker
  $pozicijaLoudspeaker = strpos($content, 'Loudspeaker</a> </td>');
  if($pozicijaLoudspeaker){
    $loudspeaker = substr($content, $pozicijaLoudspeaker + 39, 170);
    $pozicijaLoudspeaker2 = strpos($loudspeaker, '</td>');
    $loudspeaker = substr($loudspeaker, 0, $pozicijaLoudspeaker2);
  }else{
    $loudspeaker = 'N/A';
    $pozicijaLoudspeaker2 = 'N/A';
  }
  //3.5mmJack
  $pozicijatripetmmJack = strpos($content, '3.5mm jack</a> </td>');
  if($pozicijatripetmmJack){
    $tripetmmjack = substr($content, $pozicijatripetmmJack + 38, 170);
    $pozicijatripetmmJack2 = strpos($tripetmmjack, '</td>');
    $tripetmmjack = substr($tripetmmjack, 0, $pozicijatripetmmJack2);
  }else{
    $tripetmmjack = 'N/A';
    $pozicijatripetmmJack2 = 'N/A';
  } 
  //Wlan
  $pozicijaWlan = strpos($content, 'spec="wlan">');
  if($pozicijaWlan){
    $wlan = substr($content, $pozicijaWlan + 12, 170);
    $pozicijaWlan2 = strpos($wlan, '</td>');
    $wlan = substr($wlan, 0, $pozicijaWlan2);
  }else{
    $wlan = 'N/A';
    $pozicijaWlan2 = 'N/A';  
  }
  //Bluetooth
  $pozicijaBluetooth = strpos($content, 'spec="bluetooth">');
  if($pozicijaBluetooth){
    $bluetooth = substr($content, $pozicijaBluetooth + 17, 170);
    $pozicijaBluetooth2 = strpos($bluetooth, '</td>');
    $bluetooth = substr($bluetooth, 0, $pozicijaBluetooth2);
  }else{
    $bluetooth = 'N/A';
    $pozicijaBluetooth2 = 'N/A';
  }
  //GPS
  $pozicijaGPS = strpos($content, 'spec="gps">');
  if($pozicijaGPS){
    $gps = substr($content, $pozicijaGPS + 11, 170);
    $pozicijaGPS2 = strpos($gps, '</td>');
    $gps = substr($gps, 0, $pozicijaGPS2);
  }else{
    $gps = 'N/A';
    $pozicijaGPS2 = 'N/A';
  }
  //Radio
  $pozicijaRadio = strpos($content, 'spec="radio">');
  if($pozicijaRadio){
    $radio = substr($content, $pozicijaRadio + 13, 170);
    $pozicijaRadio2 = strpos($radio, '</td>');
    $radio = substr($radio, 0, $pozicijaRadio2);
  }else{
    $radio = 'N/A';
    $pozicijaRadio2 = 'N/A';
  }
  //USB
  $pozicijaUsb = strpos($content, 'spec="usb">');
  if($pozicijaUsb){
    $usb = substr($content, $pozicijaUsb + 11, 170);
    $pozicijaUsb2 = strpos($usb, '</td>');
    $usb = substr($usb, 0, $pozicijaUsb2);
  }else{
    $usb = 'N/A';
    $pozicijaUsb2 = 'N/A';
  }  
  if($usb == ''){
    $usb = 'N/A';
    $pozicijaUsb2 = 'N/A';
  }
  //Sensors
  $pozicijaSensors = strpos($content, 'spec="sensors">');
  if($pozicijaSensors){
    $sensors = substr($content, $pozicijaSensors + 15, 255);
    $pozicijaSensors2 = strpos($sensors, '</td>');
    $sensors = substr($sensors, 0, $pozicijaSensors2);
  }else{
    $sensors = 'N/A';
    $pozicijaSensors2 = 'N/A';
  }
  //Messaging
  $pozicijaMessaging = strpos($content, 'Messaging</a></td>');
  if($pozicijaMessaging){
    $messaging = substr($content, $pozicijaMessaging + 36, 170);
    $pozicijaMessaging2 = strpos($messaging, '</td>');
    $messaging = substr($messaging, 0, $pozicijaMessaging2);
  }else{
    $messaging = 'N/A';
    $pozicijaMessaging2 = 'N/A';
  } 
  //Browser
  $pozicijaBrowser = strpos($content, 'Browser</a></td>');
  if($pozicijaBrowser){
    $browser = substr($content, $pozicijaBrowser + 34, 170);
    $pozicijaBrowser2 = strpos($browser, '</td>');
    $browser = substr($browser, 0, $pozicijaBrowser2);
  }else{
    $browser = 'N/A';
    $pozicijaBrowser2 = 'N/A';
  }  
  if($browser == ''){
    $browser = 'N/A';
    $pozicijaBrowser2 = 'N/A';
  }
  //FeaturesOther
  $pozicijaFeaturesOther = strpos($content, 'featuresother">');
  if($pozicijaFeaturesOther){
    $featuresother = substr($content, $pozicijaFeaturesOther + 15, 555);
    $pozicijaFeaturesOther2 = strpos($featuresother, '</td>');
    $featuresother = substr($featuresother, 0, $pozicijaFeaturesOther2);
    $featuresother = str_replace('-', '', $featuresother);
    $featuresother = str_replace('<br />', ', ', $featuresother);
  }else{
    $featuresother = 'N/A';
    $pozicijaFeaturesOther2 = 'N/A';
  }
  //Battery
  $pozicijaBattery = strpos($content, 'batdescription1">');
  if($pozicijaBattery){
    $battery = substr($content, $pozicijaBattery + 17, 170);
    $pozicijaBattery2 = strpos($battery, '</td>');
    $battery = substr($battery, 0, $pozicijaBattery2);
  }else{
    $battery = 'N/A';
    $pozicijaBattery2 = 'N/A';
  }
  


  //echo $content;

  echo '<br><hr><p>Network: '.$network[0].' ('.$network[1].')</p><p>Year: '.$year.' ('.$pozicijaYear2.')</p>'.
  '<p>Dimensions: '.$dimensions.' ('.$pozicijaDimensions2.')</p><p>Weight: '.$weight.' ('.$pozicijaWeight2.')</p>'.
  '<p>Sim: '.$sim.' ('.$pozicijaSim2.')</p><p>Displaytpe: '.$displaytype.' ('.$pozicijaDisplaytype2.')</p>'.
  '<p>Displaysize: '.$displaysize.' ('.$pozicijaDisplaysize2.')</p><p>DisplayResolution: '.$displayres.' ('.$pozicijaDisplayres2.')</p>'.
  '<p>OS: '.$os.' ('.$pozicijaOS2.')</p><p>ChipSet: '.$chipset.' ('.$pozicijaChipSet2.')</p><p>CPU: '.$cpu.' ('.$pozicijaCPU2.')</p>'.
  '<p>GPU: '.$gpu.' ('.$pozicijaGPU2.')</p><p>MemorySlot: '.$memoryslot.' ('.$pozicijaMemorySlot2.')</p>'.
  '<p>InternalMemory: '.$internalmemory.' ('.$pozicijaInternalMemory2.')</p><p>PhoneBook: '.$phonebook.' ('.$pozicijaPhoneBook2.')</p>'.
  '<p>CameraPrimary: '.$cameraprimary.' ('.$pozicijaCameraPrimary2.')</p><p>CameraFeatures: '.$camerafeatures.' ('.$pozicijaCameraFeatures2.')</p><p>CameraVideo: '.$cameravideo.' ('.$pozicijaCameraVideo2.')</p><p>CameraSecond: '.$camerasecond.' ('.$pozicijaCameraSecond2.')</p>'.
  '<p>AlertTypes: '.$alerttypes.' ('.$pozicijaAlertTypes2.')</p><p>Loudspeaker: '.$loudspeaker.' ('.$pozicijaLoudspeaker2.')</p>'.
  '<p>3.5mmJack: '.$tripetmmjack.' ('.$pozicijatripetmmJack2.')</p><p>Wlan: '.$wlan.' ('.$pozicijaWlan2.')</p>'.
  '<p>Bluetooth: '.$bluetooth.' ('.$pozicijaBluetooth2.')</p><p>GPS: '.$gps.' ('.$pozicijaGPS2.')</p>'.
  '<p>Radio: '.$radio.' ('.$pozicijaRadio2.')</p><p>USB: '.$usb.' ('.$pozicijaUsb2.')</p><p>Sensors: '.$sensors.' ('.$pozicijaSensors2.')</p>'.
  '<p>Messaging: '.$messaging.' ('.$pozicijaMessaging2.')</p><p>Browser: '.$browser.' ('.$pozicijaBrowser2.')</p>'.
  '<p>FeaturesOther: '.$featuresother.' ('.$pozicijaFeaturesOther2.')</p><p>Battery: '.$battery.' ('.$pozicijaBattery2.')</p>';
}

public function getcontent(Request $request){
  $smart = 1;
  $content = file_get_contents('https://www.gsmarena.com/xiaomi_redmi_note_4x-8580.php'); 
  // $content = file_get_contents('https://www.gsmarena.com/acer_betouch_e200-2961.php'); 
  // $content = file_get_contents('https://www.gsmarena.com/apple_iphone_7_plus-8065.php');
  // $content = file_get_contents('https://www.gsmarena.com/samsung_galaxy_s9-8966.php');
  // $content = file_get_contents('https://www.gsmarena.com/blackberry_bold_9650-3284.php');
  // 
  // $smart = 0;
  // $content = file_get_contents('https://www.gsmarena.com/nokia_3410-315.php');
  // $content = file_get_contents('https://www.gsmarena.com/motorola_c331-363.php');
  // $content = file_get_contents('https://www.gsmarena.com/ericsson_s_868-111.php');
  
  $pozicija1 = strpos($content, '<div id="specs-list">');
  $pozicija2 = strpos($content, '<p class="note">');
  $content = substr($content, $pozicija1, ($pozicija2 - $pozicija1));

  //Network
  $network = $this->maketext('nettech">', 9, 70, '</a></td>', $content);
  //Year
  $year = $this->maketext('year">', 6, 52, '</td>', $content);
  //Dimensions
  $dimensions = $this->maketext('dimensions">', 12, 100, '</td>', $content);
  //Weight
  $weight = $this->maketext('weight">', 8, 100, '</td>', $content);
  //Sim
  $sim = $this->maketext('spec="sim">', 11, 255, '</td>', $content);
  //Displaytype
  $displaytype = $this->maketext('displaytype">', 13, 100, '</td>', $content);
  //DisplaySize
  $displaysize = $this->maketext('displaysize">', 13, 100, '</td>', $content);
  if($displaysize[0] == ''){
    $displaysize[0] = 'N/A';
    $displaysize[1] = 'N/A';
  }  
  //OS
  $os = $this->maketext('spec="os">', 10, 100, '</td>', $content);
  //ChipSet
  $chipset = $this->maketext('spec="chipset">', 15, 255, '</td>', $content); 
  //CPU
  $cpu = $this->maketext('spec="cpu">', 11, 255, '</td>', $content); 
  //GPU
  $gpu = $this->maketext('spec="gpu">', 11, 255, '</td>', $content);   
  //DisplayResolution
  $displayres = $this->maketext('displayresolution">', 19, 100, '</td>', $content);
  //CardSlot
  $cardslot = $this->maketext('memoryslot">', 12, 100, '</td>', $content);
  //InternalMemory
  $internalmemory = $this->maketext('internalmemory">', 16, 100, '</td>', $content);
  //PhoneBook
  $phonebook = $this->maketext('Phonebook</a></td>', 36, 100, '</td>', $content);
  //CameraPrimary
  $pozicijaCameraPrimary = strpos($content, 'cameraprimary">');
  $cameraprimary = substr($content, $pozicijaCameraPrimary + 15, 255);
  $pozicijaCameraPrimary2 = strpos($cameraprimary, '<a href="piccmp');//ako ima dodat link
  if(!$pozicijaCameraPrimary2){//ako nema dodat link onda se sece na td
    $pozicijaCameraPrimary2 = strpos($cameraprimary, '</td>');
  }
  $cameraprimary = substr($cameraprimary, 0, $pozicijaCameraPrimary2);
  $camerafeatures[0] = 'N/A';
  $camerafeatures[1] = 'N/A';
  $cameravideo = 'N/A';
  $pozicijaCameraVideo2 = 'N/A';
  $camerasecond[0] = 'N/A';
  $camerasecond[1] = 'N/A';
  //ako model ima kameru vadimo i CameraFeatures, CameraVideo i CameraSecond
  if($cameraprimary != 'No'){
    //CameraFeatures
    $camerafeatures = $this->maketext('camerafeatures">', 16, 255, '</td>', $content);
    //CameraVideo
    $pozicijaCameraVideo = strpos($content, 'cameravideo">');
    if($pozicijaCameraVideo){
      $cameravideo = substr($content, $pozicijaCameraVideo + 13, 255);
      $pozicijaCameraVideo2 = strpos($cameravideo, '<a href="vidcmp');//ako ima dodat link
      if(!$pozicijaCameraVideo2){//ako nema dodat link onda se sece na td
        $pozicijaCameraVideo2 = strpos($cameravideo, '</td>');
      }  
      $cameravideo = substr($cameravideo, 0, $pozicijaCameraVideo2);
    }
    //CameraSecond
    $camerasecond = $this->maketext('camerasecondary">', 17, 255, '</td>', $content);
    if($camerasecond[0] == ''){
      $camerasecond[0] = 'N/A';
      $camerasecond[1] = 'N/A';
    }  
  }
  //AlertTypes
  $alerttypes = $this->maketext('Alert types</a></td>', 38, 255, '</td>', $content);
  //Loudspeaker
  $loudspeaker = $this->maketext('Loudspeaker</a> </td>', 39, 255, '</td>', $content);
  //3.5mmJack
  $tripetmmjack = $this->maketext('3.5mm jack</a> </td>', 38, 255, '</td>', $content);
  //Wlan
  $wlan = $this->maketext('spec="wlan">', 12, 255, '</td>', $content);
  //Bluetooth
  $bluetooth = $this->maketext('spec="bluetooth">', 17, 255, '</td>', $content);
  //GPS
  $gps = $this->maketext('spec="gps">', 11, 255, '</td>', $content);
  //Radio
  $radio = $this->maketext('spec="radio">', 13, 255, '</td>', $content);
  //USB
  $usb = $this->maketext('spec="usb">', 11, 255, '</td>', $content); 
  if($usb[0] == ''){
    $usb[0] = 'N/A';
    $usb[1] = 'N/A';
  }
  //Sensors
  $sensors = $this->maketext('spec="sensors">', 15, 255, '</td>', $content); 
  //Messaging
  $messaging = $this->maketext('Messaging</a></td>', 36, 255, '</td>', $content);  
  //Browser
  $browser = $this->maketext('Browser</a></td>', 34, 255, '</td>', $content);  
  if($browser[0] == ''){
    $browser[0] = 'N/A';
    $browser[1] = 'N/A';
  }
  //FeaturesOther
  $featuresother = $this->maketext('featuresother">', 15, 555, '</td>', $content);  
  //Battery
  $battery = $this->maketext('batdescription1">', 17, 170, '</td>', $content); 
  
  //echo $content;

  echo '<br><hr><p>Network: '.$network[0].' ('.$network[1].')</p><p>Year: '.$year[0].' ('.$year[1].')</p>'.
  '<p>Dimensions: '.$dimensions[0].' ('.$dimensions[1].')</p><p>Weight: '.$weight[0].' ('.$weight[1].')</p>'.
  '<p>Sim: '.$sim[0].' ('.$sim[1].')</p><p>Displaytpe: '.$displaytype[0].' ('.$displaytype[1].')</p>'.
  '<p>Displaysize: '.$displaysize[0].' ('.$displaysize[1].')</p><p>DisplayResolution: '.$displayres[0].' ('.$displayres[1].')</p>'.
  '<p>OS: '.$os[0].' ('.$os[1].')</p><p>ChipSet: '.$chipset[0].' ('.$chipset[1].')</p><p>CPU: '.$cpu[0].' ('.$cpu[1].')</p>'.
  '<p>GPU: '.$gpu[0].' ('.$gpu[1].')</p><p>CardSlot: '.$cardslot[0].' ('.$cardslot[1].')</p>'.
  '<p>InternalMemory: '.$internalmemory[0].' ('.$internalmemory[1].')</p><p>PhoneBook: '.$phonebook[0].' ('.$phonebook[1].')</p>'.
  '<p>CameraPrimary: '.$cameraprimary.' ('.$pozicijaCameraPrimary2.')</p><p>CameraFeatures: '.$camerafeatures[0].' ('.$camerafeatures[1].')</p>'.
  '<p>CameraVideo: '.$cameravideo.' ('.$pozicijaCameraVideo2.')</p><p>CameraSecond: '.$camerasecond[0].' ('.$camerasecond[1].')</p>'.
  '<p>AlertTypes: '.$alerttypes[0].' ('.$alerttypes[1].')</p><p>Loudspeaker: '.$loudspeaker[0].' ('.$loudspeaker[1].')</p>'.
  '<p>3.5mmJack: '.$tripetmmjack[0].' ('.$tripetmmjack[1].')</p><p>Wlan: '.$wlan[0].' ('.$wlan[1].')</p>'.
  '<p>Bluetooth: '.$bluetooth[0].' ('.$bluetooth[1].')</p><p>GPS: '.$gps[0].' ('.$gps[1].')</p>'.
  '<p>Radio: '.$radio[0].' ('.$radio[1].')</p><p>USB: '.$usb[0].' ('.$usb[1].')</p><p>Sensors: '.$sensors[0].' ('.$sensors[1].')</p>'.
  '<p>Messaging: '.$messaging[0].' ('.$messaging[1].')</p><p>Browser: '.$browser[0].' ('.$browser[1].')</p>'.
  '<p>FeaturesOther: '.$featuresother[0].' ('.$featuresother[1].')</p><p>Battery: '.$battery[0].' ('.$battery[1].')</p>';
}