@extends('layouts.app')

@section('content')

{{-- profil jednog usera, poziva ga metod profil() UsersControllera, moze ga viseti user tj vlasnik profila i admin --}}
  
  <div class="container">
    <div class="col-md-10">

      <div class="row">
        <div class="col-md-2 col-xs-12 userimg">
          @if($user->logo == 1)
            <img src="{{ asset('img/users/'.$user->verification.'/1.jpg') }}?vreme={{ date('Y-m-d H:i:s') }}" class="img-thumbnail" id="showimages1" style="max-width:100px;max-height:100px;float:left;">
          @else
            <img src="{{ asset('images/usericon.png') }}" class="img-thumbnail" id="showimages1" style="max-width:100px;max-height:100px;float:left;">
          @endif
        </div>
      	<div class="col-md-6 col-xs-12">
          <h3 class="h3profil">{{ $user->name }}</h3>
          <h5 class="h5profil">{{ $user->email }}, {{ $user->grad }}</h5>  
          <h5 class="h5profil">Broj oglasa: <span class="brojoglasaspan">{{ $user->brojoglasa }}</span></h5>  
        </div>   
        <div class="col-md-4 col-xs-12">
          {{-- link ka vjuu za dodavanje novog oglasa, tj ka metodu novioglas() OglasControllera, ako je user admin link ima parametar user id a ako nije admi onda nema parametar vec ce id ulogovanog usera biti koriscen --}}
          @if(Auth::check() && Auth::user()->role == 'admin' && $user->aktivan == 1)
            <a href="/novioglas/{{ $user->id }}">
              <button type="submit" name="sacuvaj" class="btn btn-primary" style="background-color: green;">
                <i class="fa fa-btn fa-user"></i> Novi Oglas
              </button>
            </a>
          @elseif(Auth::user()->id == $user->id && $user->aktivan == 1)
            <a href="/novioglas">
              <button type="submit" name="sacuvaj" class="btn btn-primary" style="background-color: green;">
                <i class="fa fa-btn fa-user"></i> Novi Oglas
              </button>
            </a>
          @endif        
        </div>   
      </div> 

      <div class="row text-center">
        <div id="izmenilicnepodatkebtn" class="col-md-12 btndiv shadow text-center">
          <h4>Izmeni Lične Podatke</h4>
        </div>
      </div> 

      {{-- ako submit forme nije prosao validaciju tj ima errora koje treba prikazati --}}
      @if($errors->has('imekorisnika') || $errors->has('grad') || $errors->has('telefonkorisnika') || $errors->has('adresakorisnika') || $errors->has('telefon2') || $errors->has('telefon3'))
        <div class="divforma">
      @else{{--ako nema errora tj forma je prosla validaciju div je nevidljiv, isto vazi kad se prvi put dodje na vju--}}
        <div class="divforma" hidden="true">
      @endif
        <img src="{{ asset('images/orangeclosebtn2.png') }}" class="pull-right closebtn">
        {{-- forma za editovanje podataka usera --}}
      	<form class="form-horizontal" role="form" method="POST" action="{{ url('/useredit') }}" enctype="multipart/form-data">
        {{-- CSRF token i hidden input sa id-em usera --}}
      	{{ csrf_field() }}
        <input type="hidden" name="userid" value="{{ $user->id }}">
        {{-- input polje za ime usera --}}
      	<div class="form-group{{ $errors->has('imekorisnika') ? ' has-error' : '' }}">
          <label for="imekorisnika" class="lepfont col-md-4 control-label">
            Ime <img src="{{ asset('images/redinfo.png') }}" class="infoikona" id="infoime">
          </label>
          <div class="col-md-6">
            <input id="imekorisnika" type="text" class="form-control" name="imekorisnika" value="{{ old('imekorisnika') ? old('imekorisnika') : $user->name }}">     
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('imekorisnika'))
              <span class="help-block">
                <strong>{{ $errors->first('imekorisnika') }}</strong>
              </span>
            @endif
          </div>
        </div>

        @php
          $gradovi = ["Aleksinac","Aranđelovac","Aleksandrovac","Beograd","Bor","Bačka Palanka",
                      "Bačka Topola","Bogatić","Bujanovac","Bečej","Novi Sad","Niš","Kragujevac"
          ];    
        @endphp
        {{--polje tj select za promenu grada, ja dodo--}}   
        <div class="form-group{{ $errors->has('grad') ? ' has-error' : '' }}">
          <label for="grad" class="lepfont col-md-4 control-label">
            Grad <img src="{{ asset('images/redinfo.png') }}" class="infoikona" id="infograd">
          </label>
          <div class="col-md-6">
            <select name="grad" id="grad" value="{{ old('grad') }}" class="form-control">
              <option></option>
              @foreach ($gradovi as $key => $grad)
                <option value="{{ $grad}}" {{ ($user->grad == $grad ? "selected":"") }}>{{ $grad }}</option>
              @endforeach
            </select>
            @if ($errors->has('grad'))
              <span class="help-block">
                <strong>{{ $errors->first('grad') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{-- polje za unos tj promenu telefona(ovo je telefon unet pri registraciji) korisnika --}}
        <div class="form-group{{ $errors->has('telefonkorisnika') ? ' has-error' : '' }}">
          <label for="telefonkorisnika" class="lepfont col-md-4 control-label">
            Telefon <img src="{{ asset('images/redinfo.png') }}" class="infoikona" id="infotelefon">
          </label>
          <div class="col-md-6">
            <input id="telefonkorisnika" type="text" class="form-control" name="telefonkorisnika" value="{{ old('telefonkorisnika') ? old('telefonkorisnika') : $user->telefon }}">
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('telefonkorisnika'))
              <span class="help-block">
                <strong>{{ $errors->first('telefonkorisnika') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{-- polje za unos tj promenu telefona2 korisnika --}}
        <div class="form-group{{ $errors->has('telefon2') ? ' has-error' : '' }}">
          <label for="telefon2" class="lepfont col-md-4 control-label">
            Telefon 2 <img src="{{ asset('images/info.png') }}" class="infoikona" id="infotelefon2">
          </label>
          <div class="col-md-6">
            <input id="telefon2" type="text" class="form-control" name="telefon2" value="{{ old('telefon2') ? old('telefon2') :  $user->telefon2 }}">
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('telefon2'))
              <span class="help-block">
                <strong>{{ $errors->first('telefon2') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{-- polje za unos tj promenu telefona3 korisnika --}}
        <div class="form-group{{ $errors->has('telefon3') ? ' has-error' : '' }}">
          <label for="telefon3" class="lepfont col-md-4 control-label">
            Telefon 3 <img src="{{ asset('images/info.png') }}" class="infoikona" id="infotelefon3">
          </label>
          <div class="col-md-6">
            <input id="telefon3" type="text" class="form-control" name="telefon3" value="{{ old('telefon3') ? old('telefon3') :  $user->telefon3 }}">
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('telefon3'))
              <span class="help-block">
                <strong>{{ $errors->first('telefon3') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{-- polje za unos tj promenu adrese korisnika --}}
        <div class="form-group{{ $errors->has('adresakorisnika') ? ' has-error' : '' }}">
          <label for="adresakorisnika" class="lepfont col-md-4 control-label">
            Adresa <img src="{{ asset('images/info.png') }}" class="infoikona" id="infoadresa">
          </label>
          <div class="col-md-6">
            <input id="adresakorisnika" type="text" class="form-control" name="adresakorisnika" value="{{ old('adresakorisnika') ? old('adresakorisnika') :  $user->adresa }}">
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('adresakorisnika'))
              <span class="help-block">
                <strong>{{ $errors->first('adresakorisnika') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{--polje tj select za biranje da li userov email vidljiv korisnicima--}}   
        <div class="form-group{{ $errors->has('prikaziemail') ? ' has-error' : '' }}">
          <label for="prikaziemail" class="lepfont col-md-4 control-label">
            Prikazi E-mail <img src="{{ asset('images/info.png') }}" class="infoikona" id="infoemail">
          </label>
          <div class="col-md-6">
            <select name="prikaziemail" id="prikaziemail" value="{{ old('prikaziemail') }}" class="form-control">
              <option value="0" {{ ($user->prikaziemail != 1 ? "selected":"") }}>Ne</option>
              <option value="1" {{ ($user->prikaziemail == 1 ? "selected":"") }}>Da</option>
            </select>
            @if ($errors->has('grad'))
              <span class="help-block">
                <strong>{{ $errors->first('grad') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{--polje za unos slike tj logo-a marke--}}
        <div class="form-group{{ $errors->has('inputimages') ? ' has-error' : '' }}"> 
          <label for="inputimages" class="lepfont col-md-4 control-label">
            Logo 
            <img src="{{ asset('images/info.png') }}" class="infoikona" id="infologo">
          </label>
          <div class="col-md-6">
            <div class="col-md-12">
              <input type="file" id="inputimages" name="inputimages"><br> 
              {{-- ako je user vec uploadovao neki logo prikazi ga --}}
              @if($user->logo == 1)
                <img src="{{ asset('img/users/'.$user->verification.'/1.jpg') }}?vreme={{ date('Y-m-d H:i:s') }}" id="showimages" style="max-width:200px;max-height:200px;float:left;">
                {{--btn kojim user moze obrisati uneti logo ako ne zeli vise da ima logo, hendler je u profil.js koji salje AJAX obrisilogo()
                metodu UsersControllera koji brise userov logo--}}
                &nbsp;
                <button type="button" userid="{{ $user->id }}" class="deletelogo btn btn btn-primary" style="background-color: red;">
                  Obriši Logo
                </button>
              @else{{-- ako nije uploadovao logo stavi sivu sliku 100 x 100 px sa neta --}}
                <img src="http://placehold.it/100x100" id="showimages" style="max-width:200px;max-height:200px;float:left;">
              @endif
            </div>
            {{-- prikazi error ako forma ne prodje validaciju u kontroleru --}}
            @if ($errors->has('inputimages'))
              <br>
              <span class="help-block">
                <strong>{{ $errors->first('inputimages') }}</strong>
              </span>
            @endif
          </div>
        </div>

        {{-- submit btn --}}
        <div class="form-group">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" name="sacuvaj" class="btn btn-primary">
              <i class="fa fa-btn fa-user"></i> Sačuvaj
            </button>
          </div>
        </div>

      </form>
      </div>{{--kraj div-a divforma--}}   
      
      <hr>
      {{-- prikaz korisnikovih oglasa --}}
      <div class="row text-center">
        <div id="oglasikorisnikabtn" class="col-md-12 btndiv shadow text-center">
          <h4>Oglasi Korisnika</h4>
        </div>
      </div> 

      @if(!isset($novioglasid))
        <div class="row oglasikorisnika" hidden="true">
      @else
        <div class="row oglasikorisnika">
      @endif
        <img src="{{ asset('images/orangeclosebtn2.png') }}" class="pull-right closeoglasikorisnikabtn" style="margin-right: 15px;">
        @foreach($oglasi as $oglas)
          <div class="col-md-12 col-xs-12 jedanoglas" id="oglas{{ $oglas->id }}">
            <div class="col-md-2 col-xs-12">
              @if($oglas->images != 0)
                @php
                  $imaslika = 0;
                  for($i = 1; $i <= 4; $i++){
                    if(file_exists("img/oglasi/$oglas->imagesfolder/$oglas->id/$i.jpg")){
                      echo '<img class="profileoglasimg" src="'.url('/').'/img/oglasi/'.$oglas->user->verification.'/'.$oglas->id.'/'.$i.'.jpg?vreme='.date('Y-m-d H:i:s').'">';
                      $imaslika++;
                    }
                    if($imaslika >= 2){
                      echo '<br class="imgbr">';
                      $imaslika = -1;
                    }
                  }
                @endphp
              @else
                @for($i = 1; $i <= 4; $i++)
                  <img class="profileoglasimg" src="{{ asset('images/phone-silhouette.png') }}">
                @endfor
              @endif
            </div>
            <div class="col-md-6 col-xs-12">
              <h3>{{ $oglas->title }}</h3>
              @if($oglas->brand_id != NULL)
                <h4>{{ $oglas->phonebrand->name }}
              @elseif($oglas->brand != NULL)
                <h4>{{ $oglas->brand }}
              @endif
              @if($oglas->phonemodel_id != NULL)
                 | {{ $oglas->phonemodel->name }}</h4>
              @elseif($oglas->model != NULL)
                 | {{ $oglas->model }}</h4>
              @endif
              <h4>Dodat: {{ $oglas->created_at->format('d.m.Y') }}</h4>
            </div>
            <div class="col-md-4 col-xs-12 text-center">
              <button class="izmeni pb col-md-10 col-xs-10 btn btn-primary pull-right" style="background-color: #00adee; margin-top: 5px;">
                  Izmeni Oglas
              </button><br>
              <button oglasid="{{ $oglas->id }}" userid="{{ $oglas->user->id }}" class="obrisi pb col-md-10 col-xs-10 btn btn btn-primary pull-right" style="background-color: red; margin-top: 5px;">
                &nbsp;Obriši Oglas&nbsp;
              </button><br>
              {{-- ako je user admin ima btn-e za odobravanje i zabranu oglasa --}}
              @if(Auth::user()->role == 'admin')
                @if($oglas->approved == 1)
                  <button class="zabrani pb col-md-10 col-xs-10 btn btn btn-primary pull-right" id="{{ $oglas->id }}" style="background-color: #FE980F; margin-top: 5px;">
                    Zabrani Oglas
                  </button>
                @else
                  <button class="odobri pb col-md-10 col-xs-10 btn btn btn-primary pull-right" id="{{ $oglas->id }}" style="background-color: green; margin-top: 5px;">
                    Odobri Oglas
                  </button>
                @endif
              @endif
            </div>
          </div>       
        @endforeach
      </div>

    </div>{{--kraj div-a col-md-10--}} 
  </div>{{--kraj div-a container--}} 
  
  {{--hendleri za ovaj vju su u profile.js iz 'mobilni\public\js\mobilnijq'--}}
  <script type="text/javascript" src="{{ asset('js/mobilnijq/profile.js') }}"></script>
  <script type="text/javascript">
  	var homeurl = '{{ url('/') }}';
    var urldeletelogo = '{{ url('/deletelogo') }}';
    var urlodobrioglas = '{{ url('/odobrioglas') }}';
    var urlzabranioglas = '{{ url('/zabranioglas') }}';
    var urlobrisioglas = '{{ url('/obrisioglas') }}';

    //kad korisnik doda sliku prikazi mu je
    function readURL(input){
      if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
          $('#showimages').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    $("#inputimages").change(function(){
      readURL(this);
    });
  </script>
	  

@endsection