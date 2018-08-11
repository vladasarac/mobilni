<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneModelData extends Model
{
  
  //
  protected $fillable = ['phonemodel_id', 'network', 'yearreleased', 'dimensions', 'weight', 'sim', 'displaytype', 'displaysize', 'displayres', 'os', 'chipset', 'cpu', 'gpu', 'cardslot', 'internalmemory', 'phonebook', 'cameraprimary', 'camerafeatures', 'cameravideo', 'camerasecond', 'alerttypes', 'loudspeaker', 'tripetmmjack', 'wlan', 'bluetooth', 'gps', 'radio', 'usb', 'sensors', 'messaging', 'browser', 'featuresother', 'battery'];

  
  //one-to-one relacija sa 'phonemodels' tabelom
  public function phonemodel(){
  	return $this->belongsTo('App\Phonemodel', 'phonemodel_id');
  }



}
