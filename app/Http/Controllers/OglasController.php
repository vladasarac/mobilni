<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Auth;
use Illuminate\Http\RedirectResponse;
use Redirect;
use DB;
use App\User;
use App\Brand;
use App\Phonemodel;
use App\PhoneModelData;
use App\Oglas;

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;

class OglasController extends Controller
{

public function __construct(){
  $this->middleware('auth');
}  

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se u profile.blade.php klikne link tj btn Novi Oglas i metod vadi usera koji ce da upise oglas ili usera ciji je id -
//stigao i svebrendove(da bi se napravila dropdown za biranje brenda)i salje to u vju novioglas.blade.phpiz'mobilni\resources\views\users'
public function novioglas(Request $request, $id = null){
  $brands = Brand::all()->sortBy("name");//vaddimo sve brendove zbog drop-downa za biranje brenda u formi za novi oglas 
  //ako je user admin nalazimo userra koji ce dodati oglas po pristiglom id-u	
  if($request->user()->is_admin() && $id != null){ 
    $user = User::where('id', $id)->first();
  }elseif($request->user()->aktivan()){//ako nije admin user ce biti trenutno ulogovanni user
  	$user = $request->user();
  }else{
  	$error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
  	return redirect('/');
  }	
  //ako je slucajno temp folder usera tj 'mobilni\public\img\oglasi\$user->verification\temp' ostao popunjen(recimo ako pri prethodnom pokusaju-
  //-dodavanja oglas prvo nije prosla validacija a dodate su slike pa je user odustao od dodavanja oglasa) onda praznimo temp folder da bi u for-
  //-mi placeholderi za file inpute bile siluete telefona a ne vec dodate slike
  $folderslike = User::where('id', $user->id)->value('verification');
  $temppath = public_path().'/img/oglasi/' . $folderslike . '/temp';
  File::deleteDirectory($temppath, true);//prazni se temp folder
  return view('users.novioglas')->withUser($user)->withBrands($brands);//pozivamo vju novioglas.blade.php iz'mobilni\resources\views\users'
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se u novioglas.blade.php u formi za dodavanje novog oglasa odaqbere brand pa onda ide AJAX iz novioglas.js koji preko
//ovog metoda vadi sve modele tog brenda iz 'phonemodels' tabele i popunjava select za model u formi
public function izvadimodele(Request $request){
  // $idbranda = $request['idbranda'];
  $idbranda = $request->get('idbranda');
  // return 'brendid: '.$idbranda;
  $modelibrenda = Phonemodel::where('brand_id', $idbranda)->orderBy('name', 'ASC')->get();
  return response()->json(['modeli' => $modelibrenda]);	
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod upisuje novi oglas u 'oglas' tabelu kad se sabmituje forma u novioglas.blade.php, takodje upisuje slike u folder koji se zove kao id-
//novog oglasa a koji je u folderu 'mobilni\public\img\oglasi\$user->verification'
public function upisioglas(Request $request){
  $oglasid = null;
  if($request->user()->can_post() && ($request->user()->is_admin() || $request->user()->id == $request->get('user_id'))){
    //vadimo verifikacioni kod usera iz 'users' tabele posto se po njemu zove folder u 'mobilni\public\img\oglasi' u koji ovaj user koji 
    //postavlja oglas uploaduje slike
    $folderslike = User::where('id', $request->get('user_id'))->value('verification');
    //pravimo privremeni folder za slike u /public/img/oglasi/$user->verification/temp, ovo nam treba da ako ne prodje validacija useru 
    //prikazemo do sada dodate slike, ako validacija prodje onda se sadrzaj temp foldera prebaci u folder koji je napravljen za oglas i isprazni
    // se temp folder
    $temppath = public_path().'/img/oglasi/' . $folderslike . '/tempimg';
    File::makeDirectory($temppath, $mode = 0777, true, true);
    $tempslike = 1; // brojac za slike
    $tempbrojslika = 0;
    //proveravamo koju je od slika user popunio i ako je popunjena dizemo brojac $slike za 1 i to ce biti ime slike koju upisujemo u folder koji
    //pravimo u folderu usera koji je u public/img/oglasi/verification usera a sam folder se zove temp
    for($tempslike; $tempslike <= 4; $tempslike++){
      if($request->file('slika'.$tempslike)){
        $tempslikaname = $tempslike.'.jpg';
        // $request->file('slika'.$tempslike)->move(public_path().'/img/oglasi/'.$folderslike.'/tempimg/', $tempslikaname);
        $request->file('slika'.$tempslike)->move($temppath, $tempslikaname);
        //pravimo thumbnail dimenzija 180 x 120 px za svaku sliku koji se zove thumb(ime slike broj od 1-12).jpg
        // $img = Image::make('img/oglasi/'.$folderslike.'/temp/'.$tempslike.'.jpg');
        //$img->resize(180, 120);
        // $img->resize(240, 360);
        // $img->save('img/oglasi/'.$folderslike.'/temp/thumb'.$tempslike.'.jpg');
        $tempbrojslika++;      
      }
    }
    //validacija polja u formi
    $messages = [
      'required' => 'Polje je obavezno',
      'max' => 'U polje možete uneti najviše :max karaktera',
      'digits_between' => 'U polje možete uneti izmedju :min i :max cifara',
    ];
    $this->validate($request, array(
        'naslovoglasa' => 'required|max:255',
        'brand' => 'required|max:255',
        'modelbrenda' => 'required|max:255', 
        'cena' => 'required',
        'year' => 'required',
        'damaged' => 'required',
        'new' => 'required'
      ), $messages);
    $no = new Oglas();
    $no->user_id = $request->get('user_id');
    //ako je user nasao i brend i model medju ponudjenima onda je mod 0(popunjavaju se kolone brand_id i phonemodel_id)
    $mod = $request->get('modovi');
    if($mod == '0'){
      $no->brand_id = $request->get('brand');
      $no->phonemodel_id = $request->get('modelbrenda');
    }elseif($mod == '1'){//ako user nije nasao ni brend ni model medju ponudjenima mod je 1(ne popunjavaju se kolone brand_id i phonemodel_id)
      $no->brand = $request->get('brand');//popunjavaju se kolone brand i model
      $no->model = $request->get('modelbrenda');
    }elseif($mod == '2'){//ako user nije nasao samo model medju ponudjenima mod je 2(ne popunjva se phonemodel_id)
      $no->brand_id = $request->get('brand');
      $no->model = $request->get('modelbrenda');//popunjava se kolona model
    }
    $no->title = $request->get('naslovoglasa');
    $no->price = $request->get('cena');
    $no->year = $request->get('year');
    $no->description = $request->get('tekstoglasa');
    $no->new = $request->get('new');
    $no->damaged = $request->get('damaged');
    $no->images = 0;
    $no->imagesfolder = $folderslike;
    $no->save();
    $oglasid = $no->id;
    //pravimo folder za slike u /public/img/oglasi/$user->verification/zatim folder kom je ime id novog oglasa
    $path = public_path().'/img/oglasi/' . $folderslike . '/' . $oglasid;
    File::makeDirectory($path, $mode = 0777, true, true);
    //ako je user dodao slike uz oglas tj temp folder u userovom folderu za slike nije prazan
    $tempimgcount = count(File::allFiles($temppath));
    if($tempimgcount != 0 && $oglasid != null){
      File::copyDirectory($temppath, $path);//kopiramo temp folder u folder oglasa
      //upisujemo u kolonu images 'oglas' tabele koliko slika ima oglas
      $dodajslikeoglas = Oglas::find($oglasid);//nalazimo upravo dodati oglas preko id-a
      $dodajslikeoglas->images = $tempimgcount;//menjamo kolonu images na $slike (tj broj slika koji je user uploadovao)
      $dodajslikeoglas->save(); // save-ujemo
      File::deleteDirectory($temppath, true);//prazni se temp folder
    }
    User::find($request->get('user_id'))->increment('brojoglasa');//povecavamo userovu kolonu brojoglasa
    $success = "Uspesno ste dodali novi oglas.";
    Session::flash('success', $success);
    return redirect()->route('profil', ['id' => $request->get('user_id'), 'novioglasid' => $oglasid]);
  }else{
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad admin u profile.blade.php klikne btn OdobriOglas nekog neodobrenog oglasa i onda ide AJAX iz profile.js sa id-em oglasa
public function odobrioglas(Request $request){
  if($request->user()->is_admin()){
    $id = $request->get('id');
    $oglas = Oglas::where('id', $id)->first();
    $oglas->approved = 1;
    $oglas->save();
    return response()->json(['oglas' => $oglas]);
  }else{
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad admin u profile.blade.php klikne btn ZabraniOglas nekog odobrenog oglasa i onda ide AJAX iz profile.js sa id-em oglasa
public function zabranioglas(Request $request){
  if($request->user()->is_admin()){
    $id = $request->get('id');
    $oglas = Oglas::where('id', $id)->first();
    $oglas->approved = 0;
    $oglas->save();
    return response()->json(['oglas' => $oglas]);
  }else{
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//kad se klikne btrn ObrisiOglas u profile.blade.php pored nekog oglasa
public function obrisioglas(Request $request){
  //ako je user admin ili vlasnik oglasa moze ga obrisati
  $oglasid = $request['oglasid'];
  $userid = $request['iduser'];
  $oglas = Oglas::where('id', $oglasid)->first();
  if($request->user()->is_admin() || $oglas->user_id == $request->user()->id){
    // $path = '/img/oglasi/' . $oglas->imagesfolder . '/' . $oglas->id;
    $delete = $oglas->delete();    
    // File::deleteDirectory($path);//brise se folder sa slikama
    // return 'userid: '.$userid.', oglasid: '.$oglasid.', path: '.$path;
    File::deleteDirectory(public_path('img/oglasi/' . $oglas->imagesfolder . '/' . $oglas->id));
    // $oglas->delete();
    // // if($delete){
    User::find($userid)->decrement('brojoglasa');
    $broglasa = DB::table('users')->where('id', $userid)->value('brojoglasa');
    // // }
    return response()->json(['idoglasa' => $oglasid, 'delete' => $delete, 'broglasa' => $broglasa]); 
    // // return $delete; 
  }else{
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  } 
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------
// public function obrisioglas(Request $request){
//   $oglasid = $request['oglasid'];
//   $oglas = Oglas::where('id', $oglasid)->first();//nalazimo oglas po id koji je stigao
//   $userid = $oglas->user_id;//uzimamo id kreatora oglasa(da bi ga nasli u 'users' tabeli i smanjili mu kolonu brojoglasa za 1)
//   // provera da li je requester admin ili kreator oglasa, ako jeste brisemo oglas i njegove slike
//   if($request->user()->is_admin() || $oglas->user_id == $request->user()->id){
//     // File::deleteDirectory(public_path('img/oglasi/' . $oglas->imagesfolder . '/' . $oglas->id));
//     $delete = $oglas->delete();
//     if($delete){//ako uspe brisanje oglasa smanjujemo kolonu brojoglasa 'users' tabele za 1 useru ciji je oglas obrisan
//       User::find($userid)->decrement('brojoglasa');
//       $broglasa = DB::table('users')->where('id', $userid)->value('brojoglasa');
//     }
//     return response()->json(['delete' => $delete, 'idoglasa' => $oglasid, 'brojoglasa' => $broglasa]);  
//   }else{//ako requester nije admin ili autor oglasa vracamo ga na '/'
//     return redirect('/');
//   }
// }



}