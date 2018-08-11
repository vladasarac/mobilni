<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $fillable = ['name', 'logo', 'brojmodela'];
    
    //
    public function phonemodels(){
      return $this->hasMany('App\Phonemodel', 'brand_id');	
    }

    //one to many relacija sa Oglas.php modelom tj tabelom 'oglas'
    public function oglas(){
      return $this->hasMany('App\Oglas', 'brand_id');
    }
}
