@extends('layouts.app')

{{-- vju za rad sa userima, samo admin moze u njega, poziva ga metod users() UsersControllera --}}

@section('content')
  <div class="container allusers">
    <div class="row">
      @foreach($users as $user)
        <div class="col-md-6 col-xs-12 singleuser">
          <div class="col-md-4 col-xs-4">
            @if($user->logo == 1)
              <img src="{{ asset('img/users/'.$user->verification.'/1.jpg') }}?vreme={{ date('Y-m-d H:i:s') }}" class="profilepic" style="float:left;">
            @else
              <img src="{{ asset('images/usericon.png') }}" class="profilepic" style="float:left;">
            @endif
          </div>
          <div class="col-md-8 col-xs-8">
            <h3 class="usertext">{{ $user->name }}</h3>
            <p class="usertext">{{ $user->email }}<br>{{ $user->grad }}</p>
            @if($user->aktivan == 0)
              <button type="button" id="user{{ $user->id }}" class="userbtn ailid btn btn-default get" ailid="a" userid="{{ $user->id }}" style="background-color: green;">
                <small>&nbsp;&nbsp;Aktiviraj&nbsp;&nbsp;</small>
              </button>
            @else
              <button type="button" id="user{{ $user->id }}" class="userbtn ailid btn btn-default get" ailid="d" userid="{{ $user->id }}" style="background-color: red;">
                <small>Deaktiviraj</small>
              </button> 
            @endif  
            <button type="button" class="userbtn btn btn-default get" onclick="window.location.href='/profil/{{ $user->id }}'">
               <small>&nbsp;&nbsp;&nbsp;Profil&nbsp;&nbsp;&nbsp;</small> 
            </button>
          </div>         
        </div>    
      @endforeach	
    </div>   
    <br>
    <ul class="pager">
  	  {!! $users->appends(['sort' => $sort, 'ascdesc' => $ascdesc])->links() !!} 
    </ul>

  </div>

  {{--hendleri za ovaj vju su u users.js iz 'mobilni\public\js\mobilnijq'--}}
  <script type="text/javascript" src="{{ asset('js/mobilnijq/users.js') }}"></script>
  <script type="text/javascript">
    var token = '{{ Session::token() }}';
    var homeurl = '{{ url('/') }}';
    var urlmanaktdeakt = '{{ url('/manaktdeakt') }}';
    var urlsearchusers = '{{ url('/searchusers') }}';
  </script>

@endsection