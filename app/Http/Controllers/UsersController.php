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
use App\Oglas;

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManagerStatic as Image;

class UsersController extends Controller
{



public function __construct(){
  $this->middleware('auth');
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod poziva ruta 'aktivacija/{verication}' kada user koji se registrovao primi aktivacioni email link u njemu ide na ovaj metod koji kolonu
//aktivan 'users' tabele menja u 1
public function aktivacija(Request $request, $verification_code){
  //nalazimo usera po koloni 'verification' a to je stiglo kroz link kao argument	
  $user = User::where('verification', $verification_code)->first();
  $user->aktivan = 1;// menjamo kolonu aktivan u 1
  $user->save(); // cuvamo promenu
  $successactivation = 'Uspesno ste aktivirali vas nalog.';//pravimo succes message
  Session::flash('successactivation', $successactivation);
  //return redirect()->route('profil')->withUser($user);//saljemo usera na vju profile.blade.php iz 'auto\resources\views\users'
  return view('/users/profile')->withUser($user);//saljemo usera na vju profile.blade.php iz 'auto\resources\views\users'
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//stize AJAX iz users.js preko rute manaktdeakt i u njemu id korisnika kog aktiviramo ili deaktiviramo i stize ailid variabla u kojoj pise a(onda
//aktiviramo usera tj user je trenutno neaktivan) ili d(onda deaktiviramo usera tj user je trenutno aktivan)
public function manaktdeakt(Request $request){
  if($request->user()->is_admin()){//koristeci is_admin() metod User.php modela proveravamo da li je user koji poziva metod admin
    $userid = $request['userid'];
    $ailid = $request['ailid'];
    $user = User::where('id', $userid)->first();
    if($ailid == 'a'){
      $user->aktivan = 1;
    }else{
      $user->aktivan = 0;
    }
    $user->save();
    return response()->json(['user' => $user]);
  }else{//ako nije admin saljemo ga na '/'
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

public function profil(Request $request, $id = null, $novioglasid = null){
  //ako postoji $id tj nije null i ako je user admin
  if($request->user()->is_admin()){
    if($request->user()->is_admin() && $id != null){//proveravamo da li je requester admin
      $userId = $id;//vadimo usera iz 'users' tabele (po id koloni, uzimamo id ulogovanog usera)
      $user = User::where('id', $userId)->first();
      //posto profil.blade.php prikazuje i do sada unete oglase korisnika ovde ih vadimo iz 'oglas' tabele(ovo je moglo i u vjuu da se uradi
      // koristeci $user->oglasis ali je orderovao od najstarijih ka novijim a ja sam hteo obrnuto)
      $oglasi = Oglas::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
      //idemo na vju profile.blade.php i saljemo usera, sve njegove oglase i ako je metod zvan iz upisinoviogla() OglasControllera i $slike i
      //$noviooglas ako nije to u pitanju ove dve varijable ce biti null
      // return view('/users/profile')->withUser($user)->withOglasi($oglasi)->withSlike($slike)->withNovioglas($novioglas);
      return view('/users/profile')->withUser($user)->withOglasi($oglasi)->withNovioglasid($novioglasid);
    }else{
      return redirect('/');//ako nije admin salji ga na '/'
    }
  }else{
    if($request->user()->aktivan()){//user mora biti aktivan tj ako metod aktivan() User.php modela vrati true
      $userId = Auth::id();//vadimo usera iz 'users' tabele (po id koloni, uzimamo id ulogovanog usera)
      $user = User::where('id', $userId)->first();
      //posto profil.blade.php prikazuje i do sada unete oglase korisnika ovde ih vadimo iz 'oglas' tabele(ovo je moglo i u vjuu da se uradi
      // koristeci $user->oglas ali je orderovao od najstarijih ka novijim a ja sam hteo obrnuto)
      $oglasi = Oglas::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
      //idemo na vju profile.blade.php i saljemo usera, sve njegove oglase i ako je metod zvan iz upisinoviogla() OglasControllera i $slike i
      //$noviooglas ako nije to u pitanju ove dve varijable ce biti null
      //return view('/users/profile')->withUser($user)->withOglasi($oglasi)->withSlike($slike)->withNovioglas($novioglas);
      return view('/users/profile')->withUser($user)->withOglasi($oglasi)->withNovioglasid($novioglasid);
    }else{ // ako nije aktivan redirect na '/'
      return redirect('/');
    }
  }
  
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod za sada vraca po 5 korisnika u users.blade.php iz 'auto\resources\views\admin', taj vju je vidljiv samo adminu
public function users(Request $request, $sort = null){
  if($request->user()->is_admin()){ 
    //po difoltu su varijable za sortiranje podesene da sortira po imenu uzlazno ako admin u users.blade.php ne odluci drugacije
    $sort = 'name';
    $ascdesc = 'ASC';
    //ako nije radjena pretraga vadimo sve usere kojima je rola author
    $users = User::where('role', 'author')->orderBy($sort, $ascdesc)->paginate(4);
    //vadimo ukupan broj usera bez paginacije
    $userstotal = User::where('role', 'author')->orderBy($sort, $ascdesc)->count();
    //u users.blade.php vracamo osim $users i varijable za sortiranje da bi paginacija mogla da se pravilno napravi i $userstotal
    return view('admin.users')->withUsers($users)
                              ->withSort($sort)
                              ->withAscdesc($ascdesc)
                              ->withUserstotal($userstotal);
  }else{
  	$error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/');
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se sabmituje forma za editovanje podataka usera u profile.blade.php i onda se u 'users' tabeli menjaju postojeci podatci
//koji su upisani preilikom registracije ili se dodaju novi, takodje metod moze i da uploaduje userov logo ako ga je ovaj dodao
public function useredit(Request $request){
  $user = User::find($request['userid']); //nalazimo usera u 'users' tabeli po id-u koji je hidden polje u formi za edit usera
  if($user && (($request->user()->aktivan() && Auth::id() == $request['userid']) || $request->user()->is_admin())){
    //prvo validacija polja u formi
    $messages = [
      'required' => 'Polje je obavezno',
      'max' => 'U polje možete uneti najviše :max karaktera',
      'digits_between' => 'U polje možete uneti izmedju :min i :max cifara',
    ];
    $this->validate($request, array(
        'imekorisnika' => 'required|max:255',
        'grad' => 'required',
        'telefonkorisnika' => 'required|digits_between:9,20', //
        'adresakorisnika' => 'max:255',
        'telefon2' => 'digits_between:9,20',
        'telefon3' => 'digits_between:9,20'
      ), $messages);
    if($request->file('inputimages')){  //ako je user uploadovao sliku tj logo
      //ako je uploadovana slika brisemo sliku koja je mozda bila u userovom folderu koji se zove mobilni\public\img\users\+verification kod
      $path = public_path('/img/users/').$user->verification;
      File::cleanDirectory($path);
      //uzimamo sliku koja je uploadovana
      $image = $request->file('inputimages');
      $fileName = '1.jpg'; //zvace se 1.jpg(ali posto je ime foldera jedinstveno bice opusteno...)
      $image_resize = Image::make($image->getRealPath());//koristeci Intervention\Image libratry resize-ujemo sliku
      $image_resize->resize(100, 100);
      //cuvamo sliku tj logo u folderu 'auto\public\img\user\.user->verification'
      $image_resize->save(public_path('img/users/'.$user->verification.'/'.$fileName));
      $user->logo = 1;//u kolonu logo 'users' tabele upisujemo 1(po difoltu je 0) sto znaci da user ima logo 
    }
    //podesavamo ime korisnika tj name kolonu 'users' tabele unosom u polje imekorisnika
    $user->name = $request['imekorisnika'];
    //podesavamo grad korisnika tj grad kolonu 'users' tabele unosom u polje grad
    $user->grad = $request['grad'];
    //podesavamo telefon korisnika tj telefon kolonu 'users' tabele unosom u polje telefonkorisnika
    $user->telefon = $request['telefonkorisnika'];
    //ako je user uneo broj u polje telefon2 tu vrednost upisujemo u telefon2 kolonu 'users' tabele
    if($request['telefon2'] != ''){
      $user->telefon2 = $request['telefon2'];
    }
    //ako je user uneo broj u polje telefon3 tu vrednost upisujemo u telefon3 kolonu 'users' tabele
    if($request['telefon3'] != ''){
      $user->telefon3 = $request['telefon3'];
    }
    //ako je user uneo nesto u polje adresakorisnika tu vrednost upisujemo u adresa kolonu 'users' tabele
    if($request['adresakorisnika'] != ''){
      $user->adresa = $request['adresakorisnika'];
    }
    //ako je user rekao 'da' u select-u za prikaziemail tj stiglo je 1 iz forme, koloni prikazi email dajemo vrednost 1
    if($request['prikaziemail'] != 0){
      $user->prikaziemail = 1;
    }else{//ako je rekao ne onda joj dajemo verednost 0
      $user->prikaziemail = 0;
    }
    //cuvamo promene
    $user->save();
    //podesavamo success poruku
    if($request->user()->is_admin()){
      $success = 'Uspešno ste dopunili podatke profila korisnika '.$request['imekorisnika'].'.';
    }else{
      $success = 'Uspešno ste dopunili podatke vašeg profila.';
    }  
    Session::flash('success', $success);
    return redirect()->back();//redirectujemo nazad na profil.blade.php
  }else{//ako nije admin ili ako user pokusava da edituje tudji profil 
    $error = "Nemate pravo da pristupite traženoj stranici.";
    Session::flash('error', $error);
    return redirect('/'); 
  }
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se koristi ako user ima dodat logo ali zeli da ga obrise, ako ima logo u formi u profile.blade.php pored slike loga ima btn ObrisiLogo
//(.obrisilogo) koji kad se klikne poziva hendler iz profile.js koji salje AJAX u ovaj metod i u njemu id usera kom treba obrisati logo 
public function deletelogo(Request $request){
  //nalazimo usera u 'users' tabeli po id-u koji je stigao AJAX-om
  $user = User::find($request['userid']);
  $user->logo = 0;//menjamo logo kolonu 'users' tabele u 0
  $user->save();//cuvamo promenu u bazi
  //putanja ka folderu u 'auto\public\img\users' u kom je bio logo usera koji brise logo
  $path = public_path('/img/users/').$user->verification;
  File::cleanDirectory($path);//brisemo sliku iz foldera
  return response()->json(1);//vracamo hendleru u profil.js 1
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

//metod se poziva kad se radi pretraga u users.blade.php(searchinput je zapravo u layputu app.blade.php),hendler na keydown kad se unosi nesto 
//je u users.js i on salje AJAX ovde posle svakog unetog karaktera a ovaj metod pretrazuje name kolonu'users'tabele i vraca nadjene usere 
public function searchusers(Request $request){
  $search = $request['search']; // pojam koji je user uneo u input #searchuser u allusers.blade.php
  $limit = $request['limit'];//limit(za sada je 3, definisan je u searchusers.js)
  $offset = $request['offset'];//offset je promenljiv
  //vadimo po 4 usera iz 'users' tabele, pretrazujemo name kolonu po unetom tekstu u input #searchuser u allusers.blade.php
  $users = User::where('name', 'like', '%'.$search.'%')
               ->where('role', 'author')
               ->orderBy('created_at', 'desc')
               ->take($limit)->skip($offset)->get();
  //ukupan broj rezultata bez limita i offseta da bi mogli da pravimo paginaciju u searchusers.js
  $userscount = User::where('name', 'like', '%'.$search.'%')->where('role', 'author')->orderBy('created_at', 'desc')->count();
  return response()->json(['users' => $users, 'count' => $userscount]);//vracamo nadjeno u searchusers.js na dalju obradu...
}

//--------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------

}