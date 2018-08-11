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
    //vadimo 5 brandova sa najvise dodatih modela za dropdown u navigaciji
    $popularbrands = Brand::whereNotNull('brojmodela')->orderBy('brojmodela', 'DESC')->skip(0)->take(5)->get();
    // ako jeste saljemo ga na vju dodajbrand.blade.php
    return view('admin.dodajbrand')->withBrends($brends)->withPopularbrands($popularbrands);
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
    // $modelimg = $request->file('modelimg');//uzimamo sliku koju je user uploadovao
    // $img = str_slug($request->get('modelname')).'.jpg';//pravimo ime slike
    // $modelimg->move(public_path().'/img/modelibrenda/' . $request->get('brandid'), $img);
    // $phonemodel->img = $img;//podesavamo kolonu logo 'brands' tabele

    $modelimg = $request->file('modelimg');//uzimamo sliku koju je user uploadovao
    $modelName = str_slug($request->get('modelname')).'.jpg';//pravimo ime slike
    $image_resize = Image::make($modelimg->getRealPath());//koristeci Intervention\Image libratry resize-ujemo sliku
    $image_resize->resize(139, 184);
    $image_resize->save(public_path('img/modelibrenda/' . $request->get('brandid') . '/' . $modelName));//cuvamo sliku
    $phonemodel->img = $modelName;//podesavamo kolonu logo 'brands' tabele   
    $saved = $phonemodel->save();//cuvamo red u bazi
    //pozivamo pmData() da upise red u tabelu 'phone_modeldatas'
    $pmData = $this->insertPmd($phonemodel->id, $phonemodel->link, $phonemodel->smart);
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
    return response()->json(['saved' => 1, 'brandid' => $phonemodel->brand_id, 'brandname' => $brandname, 'modelname' => $phonemodel->name, 'img' => $phonemodel->img, 'pmData' => $pmData]);
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
    //pozivamo pmData() da upise red u tabelu 'phone_modeldatas'
    $pmData = $this->insertPmd($model->id, $model->link, $model->smart);
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

//ovaj metod poziva metod insertPmd() kad upisuje red u tabelu 'phone_model_datas', da napravi string koji ce biti upisan
private function maketext($htmlstart, $br1, $br2, $htmlend, $content){
  $pozicija1 = strpos($content, $htmlstart);
  if($pozicija1){
    $text = substr($content, $pozicija1 + $br1, $br2);
    $pozicija2 = strpos($text, $htmlend);
    $text = substr($text, 0, $pozicija2);
    $text = str_replace('-', '', $text);
    $text = str_replace('<br>', ', ', $text);
    $text = str_replace('<br />', ', ', $text);
    $text = str_replace('<sup>', '', $text);
    $text = str_replace('</sup>', '', $text);
    if($text == ''){
      $text = null;
    }
  }else{
    $text = null;
    $pozicija2 = 'N/A';
  }
  
  // return array($text, $pozicija2);
  return $text;
}

//metod upisuje red u tabelu 'phone_model_datas' prilikom upisa ili edita modela, tj ovaj metod pozivaju metodi dodajmodel() i editmodela(),
//metod ide na link koji je dat na gsm areni i iz tabele na stranici vadi podatke i upisuje ih u odgovarajuce kolone tabele'phone_model_datas'
//poziva i metod maketext() koji sluzi da parsira string koji mu se da tj sadrzaj gsm arena stranice za odgovarajuci model i vraca string koji
//ce biti upisan u tabelu 'phone_model_datas'
private function insertPmd($modelid, $url, $smart){
  //ako je unet los link tj link ne pokazuje na GSM arenu
  if(!$content = @file_get_contents('https://www.gsmarena.com/' . $url . '.php')){ 
    $pmd = new PhoneModelData();
    $pmd->phonemodel_id = $modelid;
    $pmd->save();
    return false;
  }else{ 
    //ako vec postoji red u 'phone_model_datas' tabeli sa tim phonemodel_id-em tj ako je npr dat pogresan link prilikom upisa modela
    $pmd = PhoneModelData::where('phonemodel_id', $modelid)->first();
    if(!$pmd){//ako n postoji taj red znaci da je upis modela a ne update i pravimo novi red za 'phone_model_datas' tabelu
      $pmd = new PhoneModelData();
      $pmd->phonemodel_id = $modelid;
    }
    $pozicija1 = strpos($content, '<div id="specs-list">');
    $pozicija2 = strpos($content, '<p class="note">');
    $content = substr($content, $pozicija1, ($pozicija2 - $pozicija1));
    //pozivam za svaku kolonu 'phone_model_datas' tabele metod maketext() koji ce napraviti string koji cemo upisati u tabelu
    //Network
    $network = $this->maketext('nettech">', 9, 70, '</a></td>', $content);
    $pmd->network = $network; 
    //Year
    $year = $this->maketext('year">', 6, 52, '</td>', $content);
    $pmd->yearreleased = $year; 
    //Dimensions
    $dimensions = $this->maketext('dimensions">', 12, 100, '</td>', $content);
    $pmd->dimensions = $dimensions;
    //Weight
    $weight = $this->maketext('weight">', 8, 100, '</td>', $content);
    $pmd->weight = $weight;
    //Sim
    $sim = $this->maketext('spec="sim">', 11, 255, '</td>', $content);
    $pmd->sim = $sim;
    //Displaytype
    $displaytype = $this->maketext('displaytype">', 13, 100, '</td>', $content);
    $pmd->displaytype = $displaytype;
    //DisplaySize
    $displaysize = $this->maketext('displaysize">', 13, 100, '</td>', $content);
    if($displaysize == ''){
      $displaysize = null;
    }  
    $pmd->displaysize = $displaysize;
    //ako mobilni nije smart tj nema Operativni Sistem necemo skidati ove kolone tabele sa GSM-arene
    if($smart != 0){
      //OS
      $os = $this->maketext('spec="os">', 10, 100, '</td>', $content);
      $pmd->os = $os;
      //ChipSet
      $chipset = $this->maketext('spec="chipset">', 15, 255, '</td>', $content); 
      $pmd->chipset = $chipset;
      //CPU
      $cpu = $this->maketext('spec="cpu">', 11, 255, '</td>', $content); 
      $pmd->cpu = $cpu;
      //GPU
      $gpu = $this->maketext('spec="gpu">', 11, 255, '</td>', $content);
      $pmd->gpu = $gpu;  
    }
    //DisplayResolution
    $displayres = $this->maketext('displayresolution">', 19, 100, '</td>', $content);
    $pmd->displayres = $displayres;
    //CardSlot
    $cardslot = $this->maketext('memoryslot">', 12, 100, '</td>', $content);
    $pmd->cardslot = $cardslot;
    //InternalMemory
    $internalmemory = $this->maketext('internalmemory">', 16, 100, '</td>', $content);
    $pmd->internalmemory = $internalmemory;
    //PhoneBook
    $phonebook = $this->maketext('Phonebook</a></td>', 36, 100, '</td>', $content);
    $pmd->phonebook = $phonebook;
    //CameraPrimary, za kolone u vezi kamere ne pozivamo maketext() metod posto se malo razlikuju na GSM arena stranici od drugih stvari
    $pozicijaCameraPrimary = strpos($content, 'cameraprimary">');
    $cameraprimary = substr($content, $pozicijaCameraPrimary + 15, 255);
    $pozicijaCameraPrimary2 = strpos($cameraprimary, '<a href="piccmp');//ako ima dodat link
    if(!$pozicijaCameraPrimary2){//ako nema dodat link onda se sece na td
      $pozicijaCameraPrimary2 = strpos($cameraprimary, '</td>');
    }
    $cameraprimary = substr($cameraprimary, 0, $pozicijaCameraPrimary2);
    $pmd->cameraprimary = $cameraprimary;
    $camerafeatures = null;
    $cameravideo = null;
    $camerasecond = null;
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
      if($camerasecond == ''){
        $camerasecond = null;
        // $camerasecond[1] = 'N/A';
      }  
    }
    $pmd->camerafeatures = $camerafeatures;
    $pmd->cameravideo = $cameravideo;
    $pmd->camerasecond = $camerasecond;
    //AlertTypes
    $alerttypes = $this->maketext('Alert types</a></td>', 38, 255, '</td>', $content);
    $pmd->alerttypes = $alerttypes;
    //Loudspeaker
    $loudspeaker = $this->maketext('Loudspeaker</a> </td>', 39, 255, '</td>', $content);
    $pmd->loudspeaker = $loudspeaker;
    //3.5mmJack
    $tripetmmjack = $this->maketext('3.5mm jack</a> </td>', 38, 255, '</td>', $content);
    $pmd->tripetmmjack = $tripetmmjack;
    //Wlan
    $wlan = $this->maketext('spec="wlan">', 12, 255, '</td>', $content);
    $pmd->wlan = $wlan;
    //Bluetooth
    $bluetooth = $this->maketext('spec="bluetooth">', 17, 255, '</td>', $content);
    $pmd->bluetooth = $bluetooth;
    //GPS
    $gps = $this->maketext('spec="gps">', 11, 255, '</td>', $content);
    $pmd->gps = $gps;
    //Radio
    $radio = $this->maketext('spec="radio">', 13, 255, '</td>', $content);
    $pmd->radio = $radio;
    //USB
    $usb = $this->maketext('spec="usb">', 11, 255, '</td>', $content); 
    if($usb == ''){
      $usb = null;
      //$usb[1] = 'N/A';
    }
    $pmd->usb = $usb;
    //Sensors
    $sensors = $this->maketext('spec="sensors">', 15, 255, '</td>', $content); 
    $pmd->sensors = $sensors;
    //Messaging
    $messaging = $this->maketext('Messaging</a></td>', 36, 255, '</td>', $content);
    $pmd->messaging = $messaging;  
    //Browser
    $browser = $this->maketext('Browser</a></td>', 34, 255, '</td>', $content);  
    if($browser == ''){
      $browser = null;
      // $browser[1] = 'N/A';
    }
    $pmd->browser = $browser;
    //FeaturesOther
    $featuresother = $this->maketext('featuresother">', 15, 555, '</td>', $content);
    $pmd->featuresother = $featuresother;  
    //Battery
    $battery = $this->maketext('batdescription1">', 17, 170, '</td>', $content);
    $pmd->battery = $battery;  
    $saved = $pmd->save();//cuvamo unetou 'phone_model_datas' tabelu u bazi
    return $saved; 
  
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
  
public function modeldata(Request $request, $modelid){

} 


}