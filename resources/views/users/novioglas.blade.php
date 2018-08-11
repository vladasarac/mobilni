@extends('layouts.app')

{{-- vju sa formom za dodavanje novog oglasa, poziva ga metod novioglas() OglasControllera --}}

@section('content')

<div class="container">

  {{-- ako je $user kog posalje metod novioglas() OglasControllera neaktivan nije moguce da on ili admin postave za njega oglas --}}
  @if($user->aktivan != 1)
    <div class="alert alert-danger text-center" role="alert">
      Korisnikov profil nije aktiviran. Nije moguće postaviti oglas.
    </div>
  @else {{--ako je $user kog vrati metod novioglas() OglasControllera aktivan--}}
    <h3 class="text-center">Popunite formu i postavite oglas za vaš telefon</h3><br>
    <div class="errorsuccess alert alert-danger text-center" role="alert">
      <b>Polja kojima je naslov crvene boje su OBAVEZNA.</b>
      <div class="errori"></div>
    </div>
    {{-- forma za dodavanje novog oglasa --}}
    <form role="form" method="POST" action="{{ url('/upisinovioglas') }}" enctype="multipart/form-data">

      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
      {{-- ako user odabere opciju Ostalo... u selectu za brend ovaj input dobija vrednost 1 a ako odabere opciju Ostalo... u selctu za model ovaj input dobija vrednost 2, ovo je potrebno da bi novioglas.js znao kako da popuni polja za brend i model ako ne prodje validacija --}}
      <input type="hidden" name="modovi" id="modovi" value="0">

      <div class="row">
        {{-- polje za naslov oglasa, obavezno --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('naslovoglasa') ? ' has-error' : '' }}">
            <label for="naslovoglasa" id="naslovoglasalabel" class="control-label text-danger">Naslov Oglasa</label>
            <input type="text" class="form-control obaveznopolje" name="naslovoglasa" id="naslovoglasa" value="{{ old('naslovoglasa') }}">
            @if ($errors->has('naslovoglasa'))
              <span class="help-block">
                <strong>{{ $errors->first('naslovoglasa') }}</strong>
              </span>
            @endif
          </div>
        </div>
        {{-- polje za brend, obavezno --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('brand') ? ' has-error' : '' }}">
            <label for="brand" id="brandlabel" class="control-label text-danger">Brend</label>
            <select name="brand" id="brand" class="form-control obaveznopolje">
              <option></option>
              @foreach ($brands as $key => $brand)
                <option brandid="{{ $brand->id }}" value="{{ $brand->id }}" {{ old('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
              @endforeach
              <option>Ostalo...</option>
            </select>
            @if ($errors->has('brand'))
              <span class="help-block">
                <strong>{{ $errors->first('brand') }}</strong>
              </span>
            @endif
          </div>
        </div>
        {{-- polje za model, obavezno --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('modelbrenda') ? ' has-error' : '' }}">
            <label for="modelbrenda" id="modelbrendalabel" class="control-label text-danger" id="labelzamodel">Model</label>
            <div id="modelbrendaselect">
              <select name="modelbrenda" id="modelbrenda" class="form-control obaveznopolje">
                <option id="prazanoptionzamodele"></option>
              </select>
            </div>
            @if ($errors->has('modelbrenda'))
              <span class="help-block">
                <strong>{{ $errors->first('modelbrenda') }}</strong>
              </span>
            @endif
          </div>
        </div>
        {{-- polje za cenu, obavezno --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('cena') ? ' has-error' : '' }}">
            <label for="cena" id="cenalabel" class="control-label text-danger">Cena (Eur)</label>
            <input type="number" min="1" max="1000000" class="form-control obaveznopolje" name="cena" id="cena" value="{{ old('cena') }}">
            @if ($errors->has('cena'))
              <span class="help-block">
                <strong>{{ $errors->first('cena') }}</strong>
              </span>
            @endif
          </div>
        </div>
      </div>{{--kraj div-a .row--}}

      <div class="row">
        @php
          $g = 2018;
        @endphp
        {{-- polje za godiste --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('year') ? ' has-error' : '' }}">
            <label for="year" id="yearlabel" class="control-label text-danger">Godište</label>
            <select name="year" id="year" class="form-control">
              <option></option>
              @for($g; $g >= 1973; $g--)
                <option value="{{ $g }}" {{ old('year') == $g ? 'selected' : '' }}>{{ $g }}</option>
              @endfor
            </select>
            @if ($errors->has('year'))
              <span class="help-block">
                <strong>{{ $errors->first('year') }}</strong>
              </span>
            @endif
          </div>
        </div>
        {{-- ostecen da ili ne --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('damaged') ? ' has-error' : '' }}">
            <label for="damaged" id="damagedlabel" class="control-label text-danger">Oštećen</label>
            <select name="damaged" id="damaged" class="form-control">
              <option value="0" {{ old('damaged') == 0 ? 'selected' : '' }}>Ne</option>
              <option value="1" {{ old('damaged') == 1 ? 'selected' : '' }}>Da</option>
            </select>
            @if ($errors->has('damaged'))
              <span class="help-block">
                <strong>{{ $errors->first('damaged') }}</strong>
              </span>
            @endif
          </div>
        </div>
        {{-- ostecen da ili ne --}}
        <div class="col-md-3">
          <div class="form-group form-group-sm {{ $errors->has('new') ? ' has-error' : '' }}">
            <label for="new" id="newlabel" class="control-label text-danger">Nov</label>
            <select name="new" id="new" class="form-control">
              <option value="0" {{ old('new') == 0 ? 'selected' : '' }}>Ne</option>
              <option value="1" {{ old('new') == 1 ? 'selected' : '' }}>Da</option>
            </select>
            @if ($errors->has('new'))
              <span class="help-block">
                <strong>{{ $errors->first('new') }}</strong>
              </span>
            @endif
          </div>
        </div>
      </div>{{--kraj div-a .row--}}

      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-sm">
            <textarea name="tekstoglasa" id="tekstoglasa" rows="5" class="form-control" placeholder="Ovde možete rečima opisati vaš telefon i navesti dodatne informacije koje želite...">@if(old('tekstoglasa')){{ old('tekstoglasa') }}@endif</textarea>
          </div>
        </div>
      </div>{{--kraj div-a .row--}}

      {{-- Slike --}}
      <div class="row">
        <div class="col-md-3">
          <div class="image-upload">
            <label for="slika1" class="slikaupload">
              @if(file_exists(public_path().'/img/oglasi/'.$user->verification.'/tempimg/1.jpg')) 
                <img style="width: 150px; height: 180px;" id="slika1holder" src="{{ asset('img/oglasi/'.$user->verification.'/tempimg/1.jpg?vreme='.date('Y-m-d H:i:s')) }}"/>
              @else
                <img style="width: 150px; height: 180px;" id="slika1holder" src="{{ asset('images/phone-silhouette.png') }}"/>
              @endif           
            </label>
            <input id="slika1" name="slika1" type="file"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="image-upload">
            <label for="slika2" class="slikaupload">
              @if(file_exists(public_path().'/img/oglasi/'.$user->verification.'/tempimg/2.jpg')) 
                <img style="width: 150px; height: 180px;" id="slika2holder" src="{{ asset('img/oglasi/'.$user->verification.'/tempimg/2.jpg?vreme='.date('Y-m-d H:i:s')) }}"/>
              @else
                <img style="width: 150px; height: 180px;" id="slika2holder" src="{{ asset('images/phone-silhouette.png') }}"/>
              @endif   
            </label>
            <input id="slika2" name="slika2" type="file"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="image-upload">
            <label for="slika3" class="slikaupload">
              @if(file_exists(public_path().'/img/oglasi/'.$user->verification.'/tempimg/3.jpg')) 
                <img style="width: 150px; height: 180px;" id="slika3holder" src="{{ asset('img/oglasi/'.$user->verification.'/tempimg/3.jpg?vreme='.date('Y-m-d H:i:s')) }}"/>
              @else
                <img style="width: 150px; height: 180px;" id="slika3holder" src="{{ asset('images/phone-silhouette.png') }}"/>
              @endif   
            </label>
            <input id="slika3" name="slika3" type="file"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="image-upload">
            <label for="slika4" class="slikaupload">
              @if(file_exists(public_path().'/img/oglasi/'.$user->verification.'/tempimg/4.jpg')) 
                <img style="width: 150px; height: 180px;" id="slika4holder" src="{{ asset('img/oglasi/'.$user->verification.'/tempimg/4.jpg?vreme='.date('Y-m-d H:i:s')) }}"/>
              @else
                <img style="width: 150px; height: 180px;" id="slika4holder" src="{{ asset('images/phone-silhouette.png') }}"/>
              @endif   
            </label>
            <input id="slika4" name="slika4" type="file"/>
          </div>
        </div>
      </div>{{--kraj div-a .row--}}

      <div class="row">
        <div class="col-md-2">
          <div class="form-group form-group-sm">
            <button type="submit" name="sacuvaj" class="btn btn-primary" style="background-color: green;">
             Objavi Oglas
            </button>
          </div>
        </div>      
      </div>{{--kraj div-a .row--}}

    </form>
  @endif

</div>{{--kraj div-a .container--}}

<script type="text/javascript" src="{{ asset('js/mobilnijq/novioglas.js') }}"></script>
<script type="text/javascript">
  var token = '{{ Session::token() }}';
  //ruta ka metodu izvadimodele OglasControllera koji koristi hendler za promenu u brenda telefona selectu da izvuce imena modela nekog brenda
  var izvadimodeleurl = '{{ url('/izvadimodele') }}';
  //ako nije prosla validacija ova variabla ce imati vrednost
  var oldmodel = '{{ old('modelbrenda') }}';
  //ako nije prosla validacija ova variabla ce imati vrednost
  var oldbrand = '{{ old('brand') }}';
  //ako nije prosla validacija ova variabla ce imati vrednost
  var oldmodovi = '{{ old('modovi') }}';
</script>

@endsection