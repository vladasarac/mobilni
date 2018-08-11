<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'aktivan', 'grad', 'telefon', 'brojoglasa', 'verification', 'prikaziemail', 'pravnolice', 'adresa',
        'telefon2', 'telefon3', 'logo', 'lat', 'lng', 'zoom'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // metod koji proverava da li je useru dozvoljeno da kreira nove oglase tj da li je u role koloni 'users' tabele upisano da je author ili admin
    public function can_post(){
      $role = $this->role; // uzmi sta pise u role koloni 'users' tabele  
      if($role == 'author' || $role == 'admin'){ // ako je user autor ili admin vrati true
        return true;
      }   
      // ako je subscriber vrati false
      return false;
    }

    // metod koji proverava da li je user admin tj da li u role koloni 'users' tabele pise 'admin'
    public function is_admin(){
      $role = $this->role; // uzmi sta pise u role koloni 'users' tabele  
      if($role == 'admin'){ // ako je admin vrati true
        return true;
      }    
      // ako nije admin vrati false
      return false;
    }

    //metod prverava da li je user aktivirao nalog tj da li je kolona aktivan 'users' tabele 1
    public function aktivan(){
      $aktivan = $this->aktivan;
      if($aktivan == 1){
        return true;
      }
      return false;
    }
    //one to many relacija sa Oglas.php modelom tj tabelom 'oglas'
    public function oglas(){
      return $this->hasMany('App\Oglas', 'user_id');
    }
}
