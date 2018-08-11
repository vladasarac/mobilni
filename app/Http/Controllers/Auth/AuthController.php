<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Support\Facades\Session;
use Mail;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'telefon' => 'required|digits_between:9,20', // ja dodo posto se pri registraciji upisuje i telefon
            'grad' => 'required'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //poruka koja ce se prikazati novo registrovanom useru 
        $success = "Registrovali ste se uspešno. Obavezno je aktivirati nalog. Na vašu email adresu poslat je aktivacioni email.";
        Session::flash('success', $success);
        //uzimamo ime user (ovo nam treba za aktivacioni mail koji mu saljemo)
        $name = $data['name'];
        //uzimamo email usera (ovo nam treba za aktivacioni mail koji mu saljemo)
        $useremail = $data['email'];
        $date1 = date("Y-m-d H:i:s"); 
        //pravimo code koji cemo upisati u kolonu verification 'users' tabele a takodje mu saljemo taj kod na email
        //$verification_code = str_random(30);
        $verification_code = md5($useremail . $date1);
        // array koji cemo poslati u vju 'registracija.blade.php' iz 'auto\resources\views\email' koji ce zapravo biti email
        $data1 = array(
          'subject' => "Potvrda Registracije mobilni.com",
          // VAZNO ne sme se dati naziv kljucu 'message' zato sto je to laravelova zasticena varijabla tako da cemo mi message zvati bodyMessage
          'bodyMessage' => "Uspešna registracija",
          'user' => $name,//saljemu u vju koji je mail i ime i emial usera da bi ih ubacili u sadrzaj maila
          'useremail' => $useremail,
          'verification' => $verification_code//verifikacioni kod koji ce biti dodat linku koji user treba da klikne
        ); 
        //saljemo mail useru koristeci vju 'registracija.blade.php' iz 'auto\resources\views\email'
        Mail::send('email.registracija', $data1, function($message) use ($data1){
          $message->from('kantarion35@gmail.com');
          $message->to($data1['useremail']);
          $message->subject($data1['subject']);
        });
        //proba kreiranja foldera za slike koje ce ovaj user dodavati kad postavlja oglase
        $path = public_path().'/img/oglasi/' . $verification_code;
        File::makeDirectory($path, $mode = 0777, true, true);      
        //pravi se i folder u koji user moze da doda svoj logo
        $path = public_path().'/img/users/' . $verification_code;
        File::makeDirectory($path, $mode = 0777, true, true);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'telefon' => $data['telefon'], //ja dodo posto se pri registraciji upisuje i telefon
            'grad' => $data['grad'], //ja dodo posto se pri registraciji upisuje i grad
            'verification' => $verification_code //ja dodo posto se pri registraciji upisuje i verifikacioni kod
        ]);
    }
}
