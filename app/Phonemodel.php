<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phonemodel extends Model
{

  protected $fillable = ['brand_id', 'name', 'year', 'img', 'link', 'smart', 'ts'];

  //one-to-many relacija sa 'brands' tabelom (jedan brand ima vise modela)
  public function phonebrand(){
    return $this->belongsTo('App\Brand', 'brand_id');	
  }

  //one-to-one relacija sa tabelom 'phone_model-datas'
  public function phone_model_data(){
  	return $this->hasOne('App\PhoneModelData', 'phonemodel_id');
  }

  //one to many relacija sa Oglas.php modelom tj tabelom 'oglas'
  public function oglas(){
    return $this->hasMany('App\Oglas', 'phonemodel_id');
  }

}
