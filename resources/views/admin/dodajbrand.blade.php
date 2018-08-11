@extends('layouts.app')

@section('content')
  
  <div class="container">

    {{-- <div class="col-md-10"> --}}
    
    <div class="row rowformabrend">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-heading formabrandpanelheading">Dodaj Brendove Telefona</div>
          <div class="panel-body panelbodybrendforma">
            <span id="naslofforme" class="text-center"></span>
            {{-- forma za dodavanje novog brenda telefona, kad se sabmituje ide AJAX iz dodajbrand.js --}}
            <form id="brendforma" class="form-horizontal brendforma" role="form" method="POST" enctype="multipart/form-data">
        	    {{ csrf_field() }}
              {{-- input za ime novog brenda --}}
        	    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        	      <label for="name" class="col-md-4 control-label">Ime</label>
        	      <div class="col-md-6">
        	        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
        	 		    @if ($errors->has('name'))
        	          <span class="help-block">
        	            <strong>{{ $errors->first('name') }}</strong>
        	          </span>
        	        @endif
        	      </div>
        	    </div>
              {{-- input za upload slike --}}
        	    <div class="form-group"> 
                <label for="logoimage" class="lepfont col-md-4 control-label">Logo</label>
                <div class="col-md-6">
          	      <div class="col-md-12">
          	  	    <input type="file" id="logoimage" name="logoimage"><br>	
          	  	    <img src="{{ asset('images/brendovi.png') }}" class="img-thumbnail" id="showimages" style="max-width:100px;max-height:100px;float:left;">
          	      </div>
          	    </div>
          	  </div>
              {{-- submit btn --}}
              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <button type="submit" class="btn btn-primary">
                    Sačuvaj
                  </button>
                  <span id="dodatnibtnibrend"></span>
                </div>
              </div>
        	  </form>
          </div>  {{-- kraj div-a panel-body --}}
        </div>  {{-- kraj div-a panel panel-default --}}
      </div>  {{-- kraj div-a panel col-md-10 col-md-offset-1 --}}
    </div> {{-- kraj div-a .row --}}
    
    <div class="row col-md-12"><hr>

    </div>
      
    <div class="col-md-10 col-md-offset-1" id="unetibrendovi" hidden="true">
      @for($i = 0; $i < 6; $i++)
      <div class="col-md-2 text-center">
        <h5>BenQ Siemens</h5>
        <img class="img-thumbnail" style="max-width:100px;max-height:100px;" src="{{ asset('img/brands/benq-siemens.png') }}">
      </div>
      @endfor
    </div>{{-- kraj div-a .col-md-12 #unetibrendovi, dodajbrand.js ovde prikazuje upravo unete brendove telefona --}}

    @php
      if(!isset($brendime)){
        $brendime = null;
      }
    @endphp

    <div class="row rowformamodel">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-heading formamodelpanelheading">Dodaj Modele Telefona</div>
          <div class="panel-body">
            <span id="naslofformemodel" class="text-center"></span>
            {{--forma za dodavanje modela, preko rute '/storemodel' ide na storemodel() metod MarkasControllra--}}
            <form class="form-horizontal formazanovimodel formamodel" role="form" method="POST" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('brandname') ? ' has-error' : '' }}">
                <label for="brandname" class="lepfont col-md-4 control-label">Odaberi Brend</label>
                <div class="col-md-6">
                  {{--select ce poslati id marke i ta vrednost ce se upisati u marka_id kolonu 'modelis' tabele--}}
                  <select name="brandname" id="brandname" class="form-control">
                      <option value="0"></option>
                    @foreach($brends as $brend)
                      <option logo="{{ $brend->logo }}" id="brend{{ $brend->id }}" value="{{ $brend->id }}" {{ $brend->name == $brendime ? 'selected' : '' }}>
                        {{ $brend->name }}
                      </option>
                    @endforeach
                  </select>
                  <p class="text-center">
                    <img hidden="true" class="logobrenda" src="{{ asset('images/brendovi.png') }}?vreme={{ date('Y-m-d H:i:s') }}" style="max-width:100px;max-height:100px; margin-top: 10px;">
                  </p>          
                </div>
              </div>
              {{-- skriveni div sa poljima koja su vidljiva tek kad se odabere brend kom se dodaje model --}}
              <div hidden="true" id="divmodeldata">
                {{-- input za ime novog modela --}}
                <div class="form-group {{ $errors->has('modelname') ? ' has-error' : '' }}">
                  <label for="modelname" class="col-md-4 control-label">Ime Modela</label>
                  <div class="col-md-6">
                    <input id="modelname" type="text" class="form-control" name="modelname" value="{{ old('modelname') }}">
                    @if ($errors->has('modelname'))
                      <span class="help-block">
                        <strong>{{ $errors->first('modelname') }}</strong>
                      </span>
                    @endif
                  </div>          
                </div> 
                {{-- input za godiste novog modela --}}
                <div class="form-group {{ $errors->has('year') ? ' has-error' : '' }}">
                  <label for="year" class="col-md-4 control-label">Godište</label>
                  <div class="col-md-6">
                    <input id="year" type="text" class="form-control" name="year" value="{{ old('year') }}">
                    @if ($errors->has('year'))
                      <span class="help-block">
                        <strong>{{ $errors->first('year') }}</strong>
                      </span>
                    @endif
                  </div>          
                </div>
                {{-- input za link ga GSM Arena strani novog modela --}}
                <div class="form-group {{ $errors->has('link') ? ' has-error' : '' }}">
                  <label for="link" class="col-md-4 control-label">Link</label>
                  <div class="col-md-6">
                    <input id="link" type="text" class="form-control" name="link" value="{{ old('link') }}">
                    @if ($errors->has('link'))
                      <span class="help-block">
                        <strong>{{ $errors->first('link') }}</strong>
                      </span>
                    @endif
                  </div>          
                </div> 
                {{-- select da li je telefon smart ili ne --}}
                <div class="form-group {{ $errors->has('smart') ? ' has-error' : '' }}">
                  <label for="smart" class="col-md-4 control-label">Smart</label>
                  <div class="col-md-2">
                    <select name="smart" id="smart" class="form-control">
                      <option value="1">1</option>
                      <option value="0">0</option>
                    </select>
                  </div>          
                </div> 
                {{-- select da li je telefon ima touchscreen ili ne --}}
                <div class="form-group {{ $errors->has('ts') ? ' has-error' : '' }}">
                  <label for="ts" class="col-md-4 control-label">TS</label>
                  <div class="col-md-2">
                    <select name="ts" id="ts" class="form-control">
                      <option value="1">1</option>
                      <option value="0">0</option>
                    </select>
                  </div>          
                </div> 
                {{-- input za upload slike --}}
                <div class="form-group"> 
                  <label for="phoneimg" class="lepfont col-md-4 control-label">Slika</label>
                  <div class="col-md-6">
                    <div class="col-md-12">
                      <input type="file" id="phoneimg" name="phoneimg"><br> 
                      <img src="{{ asset('images/phonesilhouette.png') }}" class="img-thumbnail" id="showimages1" style="max-width:100px;max-height:100px;float:left;">
                    </div>
                  </div>
                </div> 
                {{-- submit btn --}}
                <div class="form-group">
                  <div class="col-md-6 col-md-offset-4">
                    <button id="submitformamodel" type="submit" class="btn btn-primary">
                      Sačuvaj
                    </button>
                    <span id="dodatnibtni"></span>
                  </div>
                </div>
              </div> {{-- kraj hidden div-a #divmodeldata --}}          
            </form>
            <div id="modelibrenda">
            </div>{{--div u kom se prikazuju do sada uneti modeli nekog brenda --}}
          </div> {{-- kraj div-a panel-body --}}
        </div> {{-- kraj div-a panel panel-default --}}
      </div> {{-- kraj div-a panel col-md-10 col-md-offset-1 --}}
    </div>  {{-- kraj div-a .row --}}   

    <div class="row col-md-12"><hr>

    </div>
      
    <div class="col-md-10 col-md-offset-1" id="unetimodeli" hidden="true">
      @for($i = 0; $i < 6; $i++)
      <div class="col-md-2 text-center">
        <h5>Acer DX900</h5>
        <img class="img-thumbnail" style="max-width:100px;max-height:100px;" src="{{ asset('img/modelibrenda/4/dx900.jpg') }}">
      </div>
      @endfor
    </div>{{-- kraj div-a .col-md-12 #unetimodeli, dodajbrand.js ovde prikazuje upravo unete brendove telefona --}}

    {{-- <div class="row col-md-12 ispodunetimodeli"><hr>

    </div> --}}

  </div> {{-- kraj div-a .container --}}

  {{--hendleri za ovaj vju su u dodajbrand.js iz 'mobilni\public\js\mobilnijq'--}}
  <script type="text/javascript" src="{{ asset('js/mobilnijq/dodajbrand.js') }}"></script>
  <script type="text/javascript">
    var token = '{{ Session::token() }}';
    var urldodajbrendforma = '{{ url('/dodajbrendforma') }}';//ruta za sabmit forme za novi brend
    var urldodajmodelforma = '{{ url('/dodajmodelforma') }}';//ruta za sabmit forme za novi model
    var urlmodelibrenda = '{{ url('/modelibrenda') }}';//kad se klikne div #svimodelibrenda za prikaz svih modela nekog brenda
    var urleditmodela = '{{ url('/editmodela') }}';//ruta za sabmit forme za edit modela(ista forma kao i za novi model samo nova klasa dodata)
    var urldeletemodel = '{{ url('/deletemodel') }}';//kad se klikne btn Obrisi u formi za edit modela ide AJAX preko rute '/deletemodel'
    var urleditbrenda = '{{ url('/editbrenda') }}';//ruta za edit brenda tj za submit forme za edit brenda
    var urldeletebrend = '{{ url('/deletebrend') }}';//ruta za delete brenda tj klik na btn Obrisi ispod forme za edit brenda
    var homeurl = '{{ url('/') }}';
  </script>

@endsection