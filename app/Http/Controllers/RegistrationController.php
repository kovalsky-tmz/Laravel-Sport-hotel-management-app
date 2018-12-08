<?php
/////////////////// NA KONCU TWORZENIE OBIEKTU PRZEZ ADMINA
/////////////////// NA KONCU TWORZENIE OBIEKTU PRZEZ ADMINA
/////////////////// NA KONCU TWORZENIE OBIEKTU PRZEZ ADMINA
/////////////////// NA KONCU TWORZENIE OBIEKTU PRZEZ ADMINA
namespace App\Http\Controllers;

use App\Groupobject;
use App\Soloobject;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;



class RegistrationController extends Controller
{


    public function form(){
    	return view('registration.client_registration_form');
    }
	



    public function register(Request $request){
       	$this->validate(request(),[
    			
    			'email'=>'required|email|unique:users',
    			'password'=>'required|confirmed',
    			'first_name'=>'required',
    			'last_name'=>'required',
    			'phone'=>'required',
    			'city'=>'required',
    		]);

       	$confirmation_code = str_random(30);

    	$user = User::create([
                'email' => request('email'),
                'password' => Hash::make(request('password')),// ALBO bcrypt
                'first_name' => request('first_name'),
                'last_name' => request('last_name'),
                'phone' => request('phone'),
                'city' => request('city'),
                'confirmation_code' => $confirmation_code
                ]);
    	 Mail::send('mail.account-confirmation',['confirmation_code' => $confirmation_code], function($message) {
             $message->to(Input::get('email'))->subject('Verify your email address');
         });

    	$request->session()->flash('information', 'Gratulacje, Twoje konto zostało utworzone. Aktywuj je klikając w link, wysłany na podany adres email: '. Input::get('email'));
		return redirect()->home();  
    }




    public function verify($confirmation_code, Request $request){
        if(!$confirmation_code)
        {
            // kod nie działa 
            return redirect()->home()->withErrors(['Kod aktywacyjny nieaktywny!']);
            
        }

        $user = User::where('confirmation_code',$confirmation_code)->first();
        
        if ( !$user)
        {

            return redirect()->home()->withErrors(['Kod aktywacyjny nieaktywny!']);
            // nie istnieje user
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

        $request->session()->flash('information', 'Gratulacje, poprawnie aktywowałeś swoje konto. Możesz teraz się zalogować');

        return redirect()->home();
    }





    public function admin_register_form(){

        return view('registration.admin_registration_form');
    }






    public function admin_register(Request $request){
        $user=new User;
        $new_password = str_random(10);
        $confirmation_code = str_random(30);
        $this->validate(request(),[
                
                'email'=>'required|email|unique:users',
                
                'first_name'=>'required',
                'last_name'=>'required',
                'phone'=>'required',
                
                'city'=>'required',
                
            ]);


        $user->email=request('email');
        $user->password= Hash::make($new_password);
        $user->first_name=request('first_name');
        $user->last_name=request('last_name');
        $user->phone=request('phone');
        $user->city=request('city');
        $user->confirmation_code=$confirmation_code;
        $user->role=request('role');
        $user->save();

        Mail::send('mail.admin_registration',['new_password' => $new_password,'confirmation_code'=>$confirmation_code, 'email'=>request('email')], function($message) {
             $message->to(Input::get('email'))->subject('Verify your email address');
         });
        Mail::send('mail.admin_registration',['new_password' => $new_password,'confirmation_code'=>$confirmation_code,'email'=>request('email')], function($message) {
             $message->to(Auth::user()->email)->subject('Verify your email address');
         });

        $request->session()->flash('information', 'Gratulacje, Twoje konto zostało utworzone. Aktywuj je klikając w link, wysłany na podany adres email: '. Input::get('email'));
        return redirect()->home();  
    
    }



//////////////////////////////////////////////////////////////////////




    public function new_object_form(Request $request){
        return view('administration.nowy_obiekt_test');
    }



    public function new_object(Request $request){
        $name=request('object_name');
        // $myfile = fopen("../routes/web.php", "a+") or die("Unable to open file!");
        // $txt = "\nRoute::get('/reservation/{$name}', 'ReservationController@gym_reservation);\n";
        // fwrite($myfile, $txt);
        //SPROBUJE TKA JAK SIE NIE UDA MOJE
        $name=str_replace(' ', '_', $name);
        $system_type=request('system_type');
        
        
        if($system_type=='individual'){
            $days=request('day');
            $this->validate(request(),[
                'max_guests'=>'required|integer',
                'object_name'=>'required',
                'sequence_time'=>'required|integer',
                'cost_hour'=>'required|integer',

            ]);
            if(Schema::hasTable($name.'s')==true){
            	return redirect('new_object')->withErrors(['Ten obiekt już istnieje']);
            }
	        if($days!='null'){
	        	Schema::create($name.'s', function (Blueprint $table) {
	                $table->increments('id');
	                $table->integer('guests_amount');
	                $table->datetime('reservation_start');
	                $table->datetime('reservation_end');
	                $table->integer('cost_hour');
	                $table->integer('reservation_id')->unsigned();
	                $table->timestamps();
	                $table->foreign('reservation_id')->references('reservation_id')->on('reservations')->onDelete('cascade');;
	    		});	
	        	foreach($days as $day){    
	        			
			   	 	$object=Soloobject::create([
			                'object_name'=>$name,
			                'sequence_time'=>request('sequence_time'),
			                'day'=>$day,
			                'break_time'=>request('break_time'),
			                'cost_hour'=>request('cost_hour'),
			                'max_guests'=>request('max_guests'),
			                'system'=>request('system_type'),
			                'hour_start'=>request('hour_start').':00',  
			                'hour_end'=>request('hour_end').':00',                    
			            ]);       
	       	 	}

	    	}else{

	    		Schema::create($name.'s', function (Blueprint $table) {
	                $table->increments('id');
	                $table->integer('guests_amount');
	                $table->datetime('reservation_start');
	                $table->datetime('reservation_end');
	                $table->integer('cost_hour');
	                $table->integer('reservation_id')->unsigned();
	                $table->timestamps();
	                $table->foreign('reservation_id')->references('reservation_id')->on('reservations')->onDelete('cascade');;
	    		});

	    		$object=Soloobject::create([
			                'object_name'=>$name,
			                'sequence_time'=>request('sequence_time'),
			                'break_time'=>request('break_time'),
			                'cost_hour'=>request('cost_hour'),
			                'max_guests'=>request('max_guests'),
			                'system'=>request('system_type'),
			                'hour_start'=>request('hour_start').':00',  
			                'hour_end'=>request('hour_end').':00',                    
			            ]); 
	    	};

        }elseif($system_type=='group'){
            $this->validate(request(),[
                 'object_name'=>'required',
                'sequence_time'=>'required|integer',

            ]);
            Schema::create($name.'_fields', function (Blueprint $table) {
                $table->increments('field_number');
                $table->integer('cost_per_entrance');
                $table->string('field_type');
                $table->string('description')->nullable();
                $table->timestamps();
            });

            Schema::create($name.'_days', function (Blueprint $table) use($name) {
                $table->increments('id');
                $table->integer('field_number')->unsigned();
                $table->time('hour_start');
                $table->time('hour_end');
                $table->string('day');
                $table->timestamps();
                $table->foreign('field_number')->references('field_number')->on($name.'_fields')->onDelete('cascade');;
            });

            Schema::create($name.'s', function (Blueprint $table) use($name) {
                $table->increments('id');
                $table->integer('field_number')->unsigned();
                $table->integer('cost');
                $table->datetime('reservation_start');
                $table->datetime('reservation_end');
                $table->integer('reservation_id')->unsigned();
                $table->timestamps();
                $table->foreign('reservation_id')->references('reservation_id')->on('reservations')->onDelete('cascade');
                $table->foreign('field_number')->references('field_number')->on($name.'_fields')->onDelete('cascade');
            });

            
            $object=Groupobject::create([
                        'object_name'=>$name,
                        'sequence_time'=>request('sequence_time'),
                        'break_time'=>request('break_time'),
                        'system'=>request('system_type'),
                    ]);
        }
        //  Artisan::call('krlove:generate:model',[
        //     'class-name'=>$name
        // ]);
        if($system_type=='group'){
            return redirect('new_field/'.$name)->with('information','Udało Ci się stworzyc nowy obiekt. Zacznij od utworzenia nowego obszaru podlegającego pod stworzony obiekt');
        }else{
            return redirect('/')->with('information','Udało Ci się stworzyc nowy obiekt indywidualny.');
        };
    }


}
