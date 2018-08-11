@extends('layouts.app')

{{-- vju za prikaz jednog modela nekog brenda, poziva ga modeldetails() metod FrontControllera --}}

@section('content')

<div class="container">

  <h2 class="text-center"><span class="text-info">{{ $model->phonebrand->name }}</span> {{ $model->name }}</h2>
  <div class="col-md-6">
    <div class="col-md-4 modelimg">
      <img class="img-responsive pull-left slikamodel" src="{{ asset('img/modelibrenda/'.$model->phonebrand->id.'/'.$model->img.'') }}"> 	
    </div>
	<div class="col-md-8 modeldata1">
	  <b class="text-info">Year:</b> {{ $model->year }}<br> <b class="text-info">Weight:</b> {{ $model->phone_model_data->weight }} <br>
	  <b class="text-info">SIM:</b> {{ $model->phone_model_data->sim }} <br>
	  <b class="text-info">Network:</b> {{ $model->phone_model_data->network }}	<br>
	  @if($model->phone_model_data->internalmemory != null)
	    <b class="text-info">Memory:</b> {{ $model->phone_model_data->internalmemory }} <br>
	  @else
	    <b class="text-info">Memory:</b> N/A <br>
	  @endif
	  @if($model->phone_model_data->cardslot != null)
	    <b class="text-info">Card Slot:</b> {{ $model->phone_model_data->cardslot }} <br>
	  @else
	    <b class="text-info">Card Slot:</b> N/A <br>
      @endif
	  @if($model->phone_model_data->phonebook != null)
	    <b class="text-info">Phonebook:</b> {{ $model->phone_model_data->phonebook }} <br>
	  @endif
      @if($model->phone_model_data->os != null)
        <b class="text-info">OS:</b> {{ $model->phone_model_data->os }} <br>  
      @else
        <b class="text-info">OS:</b> N/A <br>
      @endif  
      @if($model->phone_model_data->chipset != null)
        <b class="text-info">Chipset:</b> {{ $model->phone_model_data->chipset }} <br>  
      @else
        <b class="text-info">Chipset:</b> N/A <br>
      @endif 
      @if($model->phone_model_data->cpu != null)
        <b class="text-info">Cpu:</b> {{ $model->phone_model_data->cpu }} <br>  
      @else
        <b class="text-info">Cpu:</b> N/A <br>
      @endif     
      @if($model->phone_model_data->gpu != null)
        <b class="text-info">Gpu:</b> {{ $model->phone_model_data->gpu }} <br>  
      @else
        <b class="text-info">Gpu:</b> N/A <br>
      @endif
      @if($model->phone_model_data->browser != null)
        <b class="text-info">Browser:</b> {{ $model->phone_model_data->browser }} <br>  
      @else
        <b class="text-info">Browser:</b> N/A <br>
      @endif
      @if($model->phone_model_data->tripetmmjack != null)
  	    <b class="text-info">3.5mm jack:</b> {{ $model->phone_model_data->tripetmmjack }} <br>
  	  @endif
  	  @if($model->phone_model_data->bluetooth != null)
  	    <b class="text-info">Bluetooth:</b> {{ $model->phone_model_data->bluetooth }} <br>
  	  @endif
  	  @if($model->phone_model_data->usb != null)
        <b class="text-info">USB:</b> {{ $model->phone_model_data->usb }} <br>  
      @else
        <b class="text-info">USB:</b> N/A <br>
      @endif  
      @if($model->phone_model_data->battery != null)
  	    <b class="text-info">Battery:</b> {{ $model->phone_model_data->battery }} <br>
  	  @endif
  	</div>
  	  	
  </div>
  <div class="col-md-6 drugidivdata">	
  	<b class="text-info dimensions">Dimensions:</b> {{ $model->phone_model_data->dimensions }} <br>
  	<b class="text-info">Display:</b> {{ $model->phone_model_data->displaytype }} <br>
  	@if($model->phone_model_data->displaysize != null)
  	  <b class="text-info">Display size:</b> {{ $model->phone_model_data->displaysize }} <br>
  	@else
  	  <b class="text-info">Display size:</b> N/A <br>
  	@endif
  	@if($model->phone_model_data->displayres != null)
  	  <b class="text-info">Resolution:</b> {{ $model->phone_model_data->displayres }} <br>
  	@else
  	  <b class="text-info">Resolution:</b> N/A <br>
  	@endif
  	@if($model->phone_model_data->cameraprimary != null)
  	  <b class="text-info">Camera:</b> {{ $model->phone_model_data->cameraprimary }} <br>
  	@else
  	  <b class="text-info">Camera:</b> N/A <br>
  	@endif
  	@if($model->phone_model_data->cameravideo != null)
  	  <b class="text-info">Camera Video:</b> {{ $model->phone_model_data->cameravideo }} <br>
  	@endif
  	@if($model->phone_model_data->camerasecond != null)
  	  <b class="text-info">Camera Second:</b> {{ $model->phone_model_data->camerasecond }} <br>
  	@endif
  	@if($model->phone_model_data->camerafeatures != null)
  	  <b class="text-info">Camera Features:</b> {{ $model->phone_model_data->camerafeatures }} <br>
  	@endif
  	@if($model->phone_model_data->loudspeaker != null)
  	  <b class="text-info">Loudspeaker:</b> {{ $model->phone_model_data->loudspeaker }} <br>
  	@endif
  	@if($model->phone_model_data->alerttypes != null)
  	  <b class="text-info">Alert Types:</b> {{ $model->phone_model_data->alerttypes }} <br>
  	@endif
  	@if($model->phone_model_data->wlan != null)
  	  <b class="text-info">WLAN:</b> {{ $model->phone_model_data->wlan }} <br>
  	@endif
  	@if($model->phone_model_data->gps != null)
  	  <b class="text-info">GPS:</b> {{ $model->phone_model_data->gps }} <br>
  	@endif
  	@if($model->phone_model_data->radio != null)
      <b class="text-info">Radio:</b> {{ $model->phone_model_data->radio }} <br>  
    @else
      <b class="text-info">Radio:</b> N/A <br>
    @endif 
    @if($model->phone_model_data->sensors != null)
      <b class="text-info">Sensors:</b> {{ $model->phone_model_data->sensors }} <br>  
    @else
      <b class="text-info">Sensors:</b> N/A <br>
    @endif 
    @if($model->phone_model_data->messaging != null)
      <b class="text-info">Messaging:</b> {{ $model->phone_model_data->messaging }} <br>  
    @else
      <b class="text-info">Messaging:</b> N/A <br>
    @endif 
    @if($model->phone_model_data->featuresother != null)
      <b class="text-info">Features:</b> {{ $model->phone_model_data->featuresother }} <br>  
    @else
      <b class="text-info">Features:</b> N/A <br>
    @endif
  </div>
  
</div>

{{--hendleri za ovaj vju su u modeldetails.js iz 'mobilni\public\js\modeldetails'--}}
<script type="text/javascript" src="{{ asset('js/mobilnijq/modeldetails.js') }}"></script>

@endsection