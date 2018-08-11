@extends('layouts.app')

{{-- vju za prikaz svih brendova, poziva ga allbrands() metod FrontControllera --}}

@section('content')

<div class="container">
  <h2 class="text-center">All Brands</h2>
  <div class="col-md-12">
    <div class="row allbrandsrow">
  	  @foreach($brands as $key => $brand)  
        <div class="col-md-4 col-xs-4">
          <a href="{{ url('/brandmodels/'.$brand->id) }}">
          	<img class="img-responsive img-thumbnail" src="{{ asset('img/brands/'.$brand->logo) }}">
            <h4>{{ $brand->name }}</h4>
            <p>{{ $brand->brojmodela }} models</p>
          </a>       
        </div>
  	    @php
          if((($key + 1) % 3 == 0) && ($key + 1) < $brandstotal){
      	    echo '</div><div class="row allbrandsrow">';
          }elseif(($key + 1) % 3 == 0){
      	    echo '</div>';
          }
        @endphp
  	  @endforeach
  	</div>
  </div>	
</div>


@endsection






