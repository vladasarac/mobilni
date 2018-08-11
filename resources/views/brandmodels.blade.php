@extends('layouts.app')

{{-- vju za prikaz modela nekog brenda, poziva ga brandmodels() metod FrontControllera --}}

@section('content')

<div class="container">
  
    {{-- @if($brand->brojmodela != null) --}}
    @if(count($brandmodels) > 0)
      <p class="text-center"><img class="text-center" src="{{ asset('img/brands/'.$brand->logo) }}"></p>
      <h2 class="text-center">{{ $brand->name }} </h2>
      <h4 class="text-center">Modela - {{ $brand->brojmodela }}</h4><hr>
      <div class="row"> 
      @foreach($brandmodels as $key => $phonemodel)
        <a href="{{ url('/modeldetails/'.$phonemodel->id) }}" target="_blank">
          <div class="col-md-2 col-xs-6" style="padding-bottom: 25px;">
            <img class="img-responsive" src="{{ asset('img/modelibrenda/'.$brand->id.'/'.$phonemodel->img.'') }}"><br>	
            <p class="text-center"><small class="imemodelasmall">{{ $phonemodel->name }}</small></p>	
          </div>
        </a>  
        @php
          if((($key + 1) % 6 == 0) && ($key + 1) < $brojmodelapostr){
          	echo '</div><div class="row">';
          }elseif(($key + 1) % 6 == 0){
          	echo '</div>';
          }
        @endphp      
      @endforeach
      {{-- div u koji ce biti ubaceni ostal modeli brenda(ako ih ima), to radi brandmodels.js --}}
      <div class="divjosmodelabrenda"></div>
      {{-- ako ima vise od pocetnih 12 ucitanih modela dodaemo div btn za ucitavanje jos modela --}}
      @if($brand->brojmodela > 12)
        <div class="row text-center">
          <div id="josmodelabrenda" class="col-md-12 btndiv shadow text-center" brendid="{{ $brand->id }}">
            <h4>Učitaj Još Modela</h4>
          </div>
        </div>
      @endif

    @else
      <h2 class="text-danger text-center">Brend Nema Dodatih Modela</h2>
    @endif

</div>{{-- kraj div-a .container --}}

  {{--hendleri za ovaj vju su u brandmodels.js iz 'mobilni\public\js\brandmodels'--}}
  <script type="text/javascript" src="{{ asset('js/mobilnijq/brandmodels.js') }}"></script>
  <script type="text/javascript">
    var token = '{{ Session::token() }}';
    var brojmodela = {{ $brand->brojmodela }};
    var brojmodelapostr = {{ $brojmodelapostr }};
    var skip = {{ $brojmodelapostr }};
    var urlbrandmodelsmore = '{{ url('/brandmodelsmore') }}';
    var urlmodeldetails = '{{ url('/modeldetails/') }}';
    var homeurl = '{{ url('/') }}';
  </script>



@endsection



