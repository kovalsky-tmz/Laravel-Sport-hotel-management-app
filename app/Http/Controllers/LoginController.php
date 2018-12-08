<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
class LoginController extends Controller
{
    public function form(){
    	return view ('login.login_form');
    }
    public function login(){
    	$credentials = [
            'email' => request('email'),
            'password' => request('password'),
            'confirmed' => 1
        ];
        if(User::where([
                    ['email',request('email')],
                    ['confirmed','===',0]
                    ])->first())        // koniec zapytania
        {   // poczatek if            
            return back()->withErrors([
                'message'=>"Konto nie zostało aktywowane. Aktywuj konto klikając w przycisk 'weryfikuj' w wysłanej przez nas wiadomości email."
            ]);
        }; //koniec if
        
    	if(Auth::attempt($credentials,true)){
    		return redirect()->home();
    	}else{
    	return back()->withErrors([
    			'message'=>"Błąd logowania, spróbuj ponownie."
    		]);
    	};
    }

    public function destroy(){
      	auth()->logout();
      	return redirect()->home();
    		
    }





}
