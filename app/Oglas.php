<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oglas extends Model
{

  protected $fillable = ['user_id', 'brand_id', 'phonemodel_id', 'brand', 'model', 'title', 'price', 'year', 'description', 'damaged', 'new', 'images', 'imagesfolder', 'timesviewed', 'approved'];

  //one to many relacija sa User.php modelom tj tabelom 'users'
  public function user(){
    return $this->belongsTo('App\User', 'user_id');	
  }
  //one to many relacija sa Brand.php modelom tj tabelom 'brands'
  public function phonebrand(){
    return $this->belongsTo('App\Brand', 'brand_id');	
  }
  //one to many relacija sa Phonemodel.php modelom tj tabelom 'phonemodels'
  public function phonemodel(){
    return $this->belongsTo('App\Phonemodel', 'phonemodel_id');	
  }
}
