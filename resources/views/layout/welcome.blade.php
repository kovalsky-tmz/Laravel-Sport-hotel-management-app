@extends('layout.master')

<body style="margin-top: 0rem">
<div class="jumbotron welcome" style='background-color: #008080;color:#e6e6e6;padding-bottom: 2rem;padding-top: 6rem'>
      @if (session()->has('information'))
        <div class="col-md-10 alert alert-success"><span class="fa fa-check" aria-hidden="true"></span>{{ session('information') }}</div>
      @endif
      @if (session()->has('account_activated'))
        <div class="col-md-10 alert alert-success"><span class="fa fa-check" aria-hidden="true"></span>{{ session('account_activated') }}</div>
      @endif
      @include('layout.errors')
    <div class="starter-template">
        @if(!Auth::check())
            <h1>Szkoleniowy ośrodek sportowy</h1><br><h2>Aplikacja Internetowa</h2>
        @else
            <h1>Witaj {{Auth::user()->first_name}}!</h1><br>
        @endif
        <p class="lead">Aplikacja stworzona w celu zarządzania szkoleniowym ośrodkiem sportowym, organizacją treningów, zawodów, obozów grupowych, noclegów w hotelu itp.,<br> oraz do zarządzania hotelem sportowym.<p>
        @if(!Auth::check())
            <button class='btn-primary btn-lg' style='margin-top: 3rem'>Zaloguj się!</button>
        @endif
        @if(Auth::check() && Auth::user()->role=='admin')
            <button class='btn-primary btn-lg' style='margin-top: 3rem' onClick="location.href='/objects_list'">Zarządzaj obiektami!</button>
        @endif
        @if(Auth::check() && Auth::user()->role=='organizator')
            <button class='btn-primary btn-lg' style='margin-top: 3rem' onClick="location.href='/users_list'">Zarządzaj użytkownikami!</button>
        @endif
        @if(Auth::check() && Auth::user()->role=='klient')
            <button class='btn-primary btn-lg' style='margin-top: 3rem' onClick="location.href='/reservation_options'">Zrób rezerwację!</button>
        @endif
    </div>
</div>
<div style="padding-bottom:1rem; border-bottom: 2px solid #e6e6e6;"> </div>
@if(Auth::check() && (Auth::user()->role=='organizator' || Auth::user()->role=='admin'))
<div class="row" style='text-align: center;margin-top:3rem'>

      <div class="col-lg-4">
        <h1><span class="badge badge-dark">{{$active_count}}</span><h1>
        <h2>Liczba aktywnych rezerwacji</h2>
        <p>Aktualnie w bazie szkoleniowego ośrodka sportowego znajduje się <span class='special_font'>{{$active_count}}</span> aktywnych rezerwacji</p>
        <p><a class="btn btn-dark" href="#" role="button">Sprawdź szczegóły &raquo;</a></p>
      </div><!-- /.col-lg-4 -->

      <div class="col-lg-4">
        <h1><span class="badge badge-dark">{{$inactive_count}}</span><h1>
        <h2>Liczba nieaktywnych rezerwacji</h2>
        <p>Aktualnie w bazie szkoleniowego ośrodka sportowego znajduje się <span class='special_font'>{{$inactive_count}}</span> nieaktywnych rezerwacji</p>
        <p><a class="btn btn-dark" href="#" role="button">Sprawdź szczegóły &raquo;</a></p>
      </div><!-- /.col-lg-4 -->

      <div class="col-lg-4">
        <h1><span class="badge badge-dark">{{$users_count}}</span><h1>
        <h2>Ilość użytkowników</h2>
         <p>Aktualnie w bazie szkoleniowego ośrodka sportowego znajduje się <span class='special_font'>{{$users_count}}</span> użytkowników</p>
        <p><a class="btn btn-dark" href="/users_list" role="button">Sprawdź szczegóły &raquo;</a></p>
      </div><!-- /.col-lg-4 -->
</div><!-- /.row -->
@endif
@if(Auth::check() && (Auth::user()->role=='organizator' || Auth::user()->role=='admin' || Auth::user()->role=='klient'))
<div class="row" style='text-align: center;margin-top:4rem'>
    <div class="col-* center">
        <h1><span class="badge badge-dark">{{$event_count}}</span><h1>
        <h2>Nadchodzące Wydarzenia</h2>
         <p>Aktualnie w bazie szkoleniowego ośrodka sportowego znajduje się <span class='special_font'>{{$event_count}}</span> użytkowników</p>
        <p><a class="btn btn-dark" href="#" data-toggle="modal" data-target="#eventModal" role="button">Sprawdź szczegóły &raquo;</a></p>
    </div>
@endif
</div>



<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nadchodzące wydarzenia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                     @if($event_count>0 && ($solo_count>0 || $group_count>0))

                         @if($solo_count>0)
                             <div class="row"  style='margin-top: 2rem'>
                                 <div class="col-md-12" >  
                                  
                                     <table class="table" >
                                         <thead >
                                             <tr>
                                               <th scope="col">Obiekt</th>
                                               <th scope="col">Od</th>
                                               <th scope="col">Do</th>
                                               <th scope="col">Wydarzenie</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach($solos as $solo)
                                               <?php $i=0; ?>
                                                 <tr>
                                                     <th scope="row">{{str_replace('_', ' ', $solo[$i]->reservation_type)}}</th>
                                                     <td>{{$solo[$i]->reservation_start}}</td>
                                                     <td>{{$solo[$i]->reservation_end}}</td>
                                                     <td>{{$solo[$i]->event}}</td>
                                                 </tr>  
                                               <?php $i++; ?>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             @endif
                             </div>

                             @if($group_count>0)
                             <div class='row' style='margin-top: 2rem'>
                                 <div class="col-md-12">  
                                     <table class="table">
                                         <thead>
                                             <tr>
                                               <th scope="col">Obiekt</th>
                                               <th scope="col">Boisko</th>
                                               <th scope="col">Od</th>
                                               <th scope="col">Do</th>
                                               <th scope="col">Wydarzenie</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach($groups as $group)
                                               <?php $i=0; ?>
                                                 <tr>
                                                     <th scope="row">{{str_replace('_', ' ', $group[$i]->reservation_type)}}</th>
                                                     <td>{{$group[$i]->field_type}}</td>
                                                     <td>{{$group[$i]->reservation_start}}</td>
                                                     <td>{{$group[$i]->reservation_end}}</td>
                                                     <td>{{$group[$i]->event}}</td>
                                                 </tr>  
                                               <?php $i++; ?>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             @endif
                         </div>

                     @endif

                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
          </div>
    </div>
  </div>
</div>



@section('content')

@endsection