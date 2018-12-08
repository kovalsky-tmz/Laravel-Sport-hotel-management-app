<?php

namespace App\Http\Controllers;
use App\Groupobject;
use App\Gym;
use App\Hotel;
use App\Hotelcart;
use App\SoloObject;
use App\Reservation;
use App\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class ReservationController extends Controller
{

    public function reservation_options(){
        $solo=SoloObject::select('object_name')->distinct()->get();
        $group=Groupobject::all();
        $days=['_monday','_tuesday','_wednesday','_thursday','_friday','_sunday','_saturday'];
    	return view('reservation.options',compact('solo','group','days'));
    }
    // public function hotel_reservation(){
    // 	return view('hotel_reservation');
    // }




    /////////////////////////////////   SOLO

    public function reservation($name){
        $queryOrder = "CASE WHEN day = 'monday' THEN 1 ";
        $queryOrder .= "WHEN day = 'tuesday' THEN 2 ";
        $queryOrder .= "WHEN day = 'wednesday' THEN 3 ";
        $queryOrder .= "WHEN day = 'thursday' THEN 4 ";
        $queryOrder .= "WHEN day = 'friday' THEN 5 ";
        $queryOrder .= "WHEN day = 'saturday' THEN 6 ";
        $queryOrder .= "WHEN day = 'sunday' THEN 6 ";
        $queryOrder .= "ELSE 8 END";
        $days=DB::table('Soloobjects')->where('object_name',$name)->select('*')->orderByRaw($queryOrder)->get();
        $days_pl=array();
        $pl=array('poniedziałek'=>'monday','wtorek'=>'tuesday','środa'=>'wednesday','czwartek'=>'thursday','piątek'=>'friday','sobota'=>'saturday','niedziela'=>'sunday','codziennie'=>'everyday');

        return view('reservation.solo_reservation',compact('name','days','day_object','pl'));
    }




    public function ajax_reservation(Request $request,$name, $date){
    	$object_name=$name.'s';
        $max=SoloObject::where('object_name',$name)->value('max_guests');
        $time=SoloObject::where('object_name',$name)->value('sequence_time');
        $break_time=SoloObject::where('object_name',$name)->value('break_time');
        // $date_month=Carbon::parse($date." ".$hour)->month;
        // $date_day=Carbon::parse($date)->day;
        // $date_now_month=Carbon::now()->month;
        // $date_now_day=Carbon::now()->day;
        $i=0;
        $hours=Array();
        $day=Carbon::parse($date)->format('l');
        $day_hours=SoloObject::where('object_name',$name)->where('day',$day)->orWhere('object_name',$name)->where('day','everyday')->get();

        $day_hours_array_min=array();
        $day_hours_array_max=array();
        foreach($day_hours as $day_hour){
            $day_hours_array_min[]=$day_hour->hour_start;
            $day_hours_array_max[]=$day_hour->hour_end;
        }
        $j=sizeof($day_hours_array_min);

        foreach($day_hours as $day_hour){
            $hours[$i]=$day_hour->hour_start;
            $day_hour_end=$day_hour->hour_end;
            while($hours[$i]<$day_hour_end){
                $hours[$i+1]=Carbon::parse($hours[$i]);
                $hours[$i+1]->minute+=($time+$break_time);
                $hours[$i+1]=$hours[$i+1]->toTimeString();
                $i++;
            };
        }


       //  $hours[$i]=Object::where('object_name',$name)->where('day',$day)->orWhere('object_name',$name)->where('day','everyday')->min('hour_start');
        
       //  while($hours[$i]<Object::where('object_name',$name)->where('day',$day)->orWhere('object_name',$name)->where('day','everyday')->max('hour_end')){
       //      $hours[$i+1]=Carbon::parse($hours[$i]);
       //      $hours[$i+1]->minute+=($time+$break_time);
       //      $hours[$i+1]=$hours[$i+1]->toTimeString();
       //      $i++;
       // };


    	
    	foreach($hours as $hour){ // & przed $hour??
            $guests_cart=(DB::table('solocarts')->where('user_id',Auth::id())->where('reservation_start',$date." ".$hour))->sum('guests_amount');
            // $guests_cart po to zeby dodalo tez liczbe rezerwacji z wózka aktualnie zalogowanego uzytkownika, dalej w zapytaniu to dodaje
    		if(((DB::table($object_name)->where('reservation_start',$date." ".$hour)->sum('guests_amount'))+$guests_cart<$max) && (DB::table($object_name)->where([['reservation_start','<=',$date." ".$hour],['reservation_end','>=',$date." ".$hour],['guests_amount',0]])->count()==0)){
                if((Carbon::parse($date." ".$hour)>Carbon::now('Europe/Warsaw')->addMinutes(15)->toDateTimeString()) && (((DB::table('Soloobjects')->where('object_name',$name)->where('day',$day)->count())>0) || ((DB::table('Soloobjects')->where('object_name',$name)->where('day','everyday')->count())>0)) ){
                      ////// W NAZWIE KTORY DZIEN, i tylko wyswietla w ten dzien
                    for($i=0;$i<$j;$i++){
                        if($hour>=$day_hours_array_min[$i] && $hour<=$day_hours_array_max[$i]){
                			$free_slots[]=$hour;
                            $text[]=" - ".($max-(DB::table($object_name)->where('reservation_start',$date." ".$hour)
                                        ->sum('guests_amount'))-$guests_cart)." Wolnych miejsc"; // TEXT do text_array
                        }
                    }
                }else{
                    continue;
                };
    		}else{
                $free_slots[]=$hour;
                $text[]=" - "." Brak Wolnych miejsc - ".DB::table('reservations')->where('reservation_id',(DB::table($object_name)->where([['reservation_start','<=',$date." ".$hour],['reservation_end','>=',$date." ".$hour],['guests_amount',0]])->value('reservation_id')))->value('event');
            };
   		};
   		$free_slots_single_array=implode(",",$free_slots);
        $text_single=implode(',',$text);
    
    	return \Response::json(['free'=>$free_slots_single_array, 'text'=>$text_single]);
	}




    /////////////////////////////////  SOLO




    ///////////////////////////////// Hotel

    public function room_reservation()
    {
        return view('reservation.room_reservation');
    }

    public function room_reservation_search(Request $request){

        $hotel=new Hotel;
        $room=new Room;
        $date_start=request('date_start');
        $date_end=request('date_end');
        $guests_amount=request('slider_guests_amount');
        $cost_night_min=request('amount_min');
        $cost_night_max=request('amount_max');

        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date',
        ]);
        
        $result=Room::whereDoesntHave('hotel',function($query){
            $query->where('reservation_start','<',request('date_start'))
                ->where('reservation_end','>',request('date_end'))
                ->orWhere('reservation_start','<',request('date_end'))
                ->where('reservation_end','>',request('date_start'));
            })->whereDoesntHave('hotelcart',function($query){
            $query->where('user_id',Auth::id())
                ->where('reservation_start','<',request('date_start'))
                ->where('reservation_end','>',request('date_end'))
                ->orWhere('user_id',Auth::id())
                ->where('reservation_start','<',request('date_end'))
                ->where('reservation_end','>',request('date_start'));
            })
            ->where('max_guests','>=',request('slider_guests_amount'))->whereBetween('cost_night',[request('amount_min'),request('amount_max')])->get();



        return view('reservation.room_reservation_result',compact('result','date_start','date_end','guests_amount','cost_night_min','cost_night_max'));
        //dd($result);
    }

    public function room_reservation_reserve(Request $request){
        $reservation=new Reservation;
        $reservation->reservation_type="hotel";
        $reservation->user_id=Auth::id();
        $reservation->save();
        $reservation_id=$reservation->reservation_id;
        Hotel::create([
            'reservation_id' => $reservation_id,
            'room_number' => request('input_room_number'),
            'guests_amount' =>request('guests_amount'),
            'reservation_start'=> request('date_start'),
            'reservation_end'=> request('date_end'),
            'confirmed'=>1,
        ]);
       Session::flash('information', 'Udało Ci się zrobić rezerwację Hotelu na dzień od '.request('date_start').' do '.request('date_end'));
        return redirect()->home();
    }

    ///////////////////////////////// Hotel

    //////////////////////////////// GROUP



    public function ajax_check_days($name,$field_name){
        $object_name=$name.'s';
        $object=Groupobject::where('object_name',$name)->first();
        $object_fields=DB::table($name.'_fields')->where('field_type',$field_name)->get();
        $days=array();
        $result1=array();
        $result2=array();
        $pl=array('poniedziałek'=>'monday','wtorek'=>'tuesday','Środa'=>'wednesday','czwartek'=>'thursday','piątek'=>'friday','sobota'=>'saturday','niedziela'=>'sunday','codziennie'=>'everyday');
        foreach($object_fields as $object_field){
            $field_number=$object_field->field_number;
            $queryOrder = "CASE WHEN day = 'monday' THEN 1 ";
            $queryOrder .= "WHEN day = 'tuesday' THEN 2 ";
            $queryOrder .= "WHEN day = 'wednesday' THEN 3 ";
            $queryOrder .= "WHEN day = 'thursday' THEN 4 ";
            $queryOrder .= "WHEN day = 'friday' THEN 5 ";
            $queryOrder .= "WHEN day = 'saturday' THEN 6 ";
            $queryOrder .= "WHEN day = 'sunday' THEN 6 ";
            $queryOrder .= "ELSE 8 END";
            $days[$field_number]=DB::table($name.'_days')->where('field_number',$field_number)->orderByRaw($queryOrder)->get();
        }
        foreach($days[$field_number] as $day){
            if(array_search($day->day, $pl)==true){
                $day_pl=array_search($day->day, $pl);
            }
            $result1[]=ucfirst($day_pl).':';
            $result2[]=$day->hour_start.' - '.$day->hour_end;
        }
    

        return response()->json([
                "days" => $result1,
                "hours" => $result2,
            ]);
    }




    public function group_reservation($name){
        $date_start=request('date_start');
        $field_name=$name.'_fields';
        $fields=DB::table($field_name)->get();
        $field_type=request('field_type');
        return view('reservation.group_reservation',compact('name','fields','date_start','field_type'));
    }




    public function group_reservation_search($name){
        $object_name=$name.'s';
        $field_type=request('field_type');
        $field_name=$name.'_fields';
        $date_start=request('date_start');
        $fields=DB::table($field_name)->get();
        $field_number=DB::table($field_name)->where('field_type',request('field_type'))->value('field_number');
        $cost=DB::table($field_name)->where('field_number',$field_number)->value('cost_per_entrance');

        $reserved_count=DB::table($object_name)->where('field_number',$field_number)->count();
        $reserveds=DB::table($object_name)->where('field_number',$field_number)->get();

        $reserved_cart_count=DB::table('groupcarts')->where('field_number',$field_number)->where('user_id',Auth::id())->count();
        $reserveds_cart=DB::table('groupcarts')->where('field_number',$field_number)->where('user_id',Auth::id())->get();

        $sequence_time=DB::table('groupobjects')->where('object_name',$name)->value('sequence_time');
        $break_sequence=$sequence_time=(DB::table('groupobjects')->where('object_name',$name)->value('sequence_time'))+(DB::table('groupobjects')->where('object_name',$name)->value('break_time'));;

        $timer=Array();
        $enter=Array();
        $day=Carbon::parse($date_start)->format('l');
        $time=DB::table($name.'_days')->where('field_number',$field_number)->where('day',$day)->orWhere('day','everyday')->where('field_number',$field_number)->value('hour_start');
        $timer[0]=DB::table($name.'_days')->where('field_number',$field_number)->where('day',$day)->orWhere('day','everyday')->where('field_number',$field_number)->value('hour_start');
        $hour_end=Carbon::parse(DB::table($name.'_days')->where('field_number',$field_number)->where('day',$day)->orWhere('day','everyday')->where('field_number',$field_number)->value('hour_end'))->hour;
        for($i=0;Carbon::parse($timer[$i])->hour<$hour_end;$i++){
            $timer[$i+1]=Carbon::parse($time)->addMinutes($break_sequence*$i)->toTimeString();
            
            // if($reserved_count>0 || $reserved_cart_count>0){
                // foreach($reserveds as $reserved){ 
            if($reserved_cart_count>0){    ////// SPRAWDZAM DOSTĘPNOŚĆ
                foreach($reserveds_cart as $reserved){   ////// SPRAWDZAM KOSZYK
                    if(((Carbon::parse($reserved->reservation_start)->toDateTimeString())!=("$date_start ".(Carbon::parse($timer[$i+1])->toTimeString())))){
                    // $enter[]="$date_start ".$timer[$i+1]->toTimeString();
                    $cond_cart=true;
                    }else{
                        $cond_cart=false ;
                        $enter[]=$timer[$i+1].' Godzina zajęta (oczekiwana)';
                         break;
                        };
                }
            }else{
                $cond_cart=true;
                };
            if((Carbon::parse($date_start." ".$timer[$i+1])>Carbon::now('Europe/Warsaw')->addMinutes(15)->toDateTimeString()) && (((DB::table($name.'_days')->where('field_number',$field_number)->where('day',$day)->count())>0) ||  ((DB::table($name.'_days')->where('field_number',$field_number)->where('day','everyday')->count())>0) ) ){         
            /////// DZIEŃ TERAZNIEJSZY - OD KONKRETNEJ GODZINY WYSWIETLA
                if($reserved_count>0){ 
                    foreach($reserveds as $reserved){
                        if(((Carbon::parse($reserved->reservation_start)->toDateTimeString())!=("$date_start ".(Carbon::parse($timer[$i+1])->toTimeString())))  && (DB::table($object_name)->where([['reservation_start','<=',$date_start." ".$timer[$i+1]],['reservation_end','>=',$date_start." ".$timer[$i+1]],['cost',0]])->count()==0)){
                            $cond=true;
                        }else{
                            $cond=false;
                            $enter[]=$timer[$i+1].' Godzina zajęta: '.DB::table('reservations')->where('reservation_id',(DB::table($object_name)->where([['reservation_start','<=',$date_start." ".$timer[$i+1]],['reservation_end','>=',$date_start." ".$timer[$i+1]],['cost',0]])->value('reservation_id')))->value('event');
                            break;
                            };
                    }
                }else{
                    $cond=true;
                    };
        // }
            }else{
                $cond=false;
                };

            if($cond==true && $cond_cart==true){
                $enter[]=$timer[$i+1];
            }  
            // }
          
        };
        
        return view('reservation.group_reservation_result',compact('name','enter','fields','field_type','date_start','cost','field_number'));
    }





   


    ////// TESTY WOZEK
    ////// TESTY WOZEK
    ////// TESTY WOZEK
    ////// TESTY WOZEK

     public function add_to_cart_group(Request $request){
        $name=request('name');
        $object_name=$name.'s';
        $cost=request('cost');
        $sequence_time=DB::table('groupobjects')->where('object_name',$name)->value('sequence_time');
        DB::table('groupcarts')->insert([
            'user_id' => Auth::id(),
            'field_number' => request('field_number'),
            'object_name' => $name,
            'reservation_start'=> request('date_start'),
            'reservation_end'=> Carbon::parse(request('date_start'))->addMinutes($sequence_time),
            'cost'=>$cost,
        ]);
       Session::flash('information', 'Dodano rezerwację do oczekujących na potwierdzenie przez użytkownika');
        return redirect()->back();

    }



    public function add_to_cart_solo (Request $request){
        $name=request('name');
        $time=SoloObject::where('object_name',$name)->value('sequence_time');
        $time_plus_break=(SoloObject::where('object_name',$name)->value('sequence_time'))+(SoloObject::where('object_name',$name)->value('break_time'));

        $object_name=$name.'s';
        $max=SoloObject::where('object_name',$name)->value('max_guests');
        $cost_hour=SoloObject::where('object_name',$name)->value('cost_hour');
        

        $j=sizeof(request('hours'));
        $hours=request('hours');
        $i=0;
        $cond=false;
        $cond_date=false;

        for($i;$i<$j;$i++){
            $guests_cart=(DB::table('solocarts')->where('user_id',Auth::id())->where('reservation_start',request('date_start')." ".Carbon::parse($hours[$i])->toTimeString()
            )->sum('guests_amount')); // sprawdza tez co mam w koszyku i pozniej do $cond to dodaje

            $cond=((DB::table($object_name)->where('reservation_start',request('date_start')." ".Carbon::parse($hours[$i])
                  ->toTimeString())
                    ->sum('guests_amount'))+(request('slider-guests_amount'))+($guests_cart)<=$max); // DODAJE ZAWARTOSC KOSZYKA
           
           
            $cond_date=(Carbon::parse($hours[$i])->toTimeString())<='20:00:00';

            if(($cond==false) || ($cond_date==false)){
                break;
            };
        }
        
        if(($cond==true) && ($cond_date==true)){

            $i=0;
            do{
                $czas=Carbon::parse($hours[$i]);
                DB::table('solocarts')->insert([
                    'user_id' => Auth::id(),
                    'guests_amount' =>request('slider-guests_amount'),
                    'reservation_start' =>request('date_start')." ".$czas->toTimeString(),
                    'reservation_end'=> request('date_start')." ".Carbon::parse($czas)->addMinutes($time)->toTimeString(),
                    'object_type'=>$name,
                    'cost_hour'=>$cost_hour,
                ]);
                $i++;
            }while($i<$j);

                Session::flash('information', 'Udało Ci się zrobić rezerwację Siłowni na dzień '.request('date_start'));
                return redirect()->home();
            
        }else{
            return back()->withErrors([
                'message'=>"Nie udało się wykonać rezerwacji. Sprawdź ilość wolnych miejsc na daną godzinę lub czy zapisujesz się na poprawne godziny"
            ]);
        };

    }




    public function add_to_cart_hotel(Request $request){
        
        $cost_night=request('input_cost_night');
        $day_start=Carbon::parse(request('date_start'))->dayOfYear;
        $day_end=Carbon::parse(request('date_end'))->dayOfYear;
        $total_cost=$cost_night*($day_end-$day_start);
        DB::table('hotelcarts')->insert([
            'user_id' => Auth::id(),
            'room_number' => request('input_room_number'),
            'guests_amount' =>request('guests_amount'),
            'reservation_start'=> request('date_start'),
            'reservation_end'=> request('date_end'),
            'cost'=>$total_cost,
        ]);
       Session::flash('information', 'Udało Ci się zrobić rezerwację Hotelu na dzień od '.request('date_start').' do '.request('date_end'));
        return redirect()->home();

    }




    public function cart_to_reservation(Request $request){
      
        $solo_all=DB::table('solocarts')->where('user_id',Auth::id())->get();
        $hotel_all=DB::table('hotelcarts')->where('user_id',Auth::id())->get();
        $group_all=DB::table('groupcarts')->where('user_id',Auth::id())->get();
        $total_cost=0;
        $cond=false;
    


////// SOLO COND CHECK
        if(($solo_all->count()>0)){
            $cond=true;
            foreach($solo_all as $one){
                if(!Schema::hasTable($one->object_type.'s')){
                    $cond=false;
                    break;
                }
                 $max_solo=DB::table('Soloobjects')->where('object_name',$one->object_type)->value('max_guests');
                 if(((DB::table($one->object_type.'s')->where('reservation_start',$one->reservation_start))
                    ->sum('guests_amount')+($one->guests_amount)>$max_solo)){
                    $cond=false;
                    break;
                 };
            };
        };




////////HOTEL CONDITION CHECK
        if(($hotel_all->count()>0)){
            $cond=true;
            foreach($hotel_all as $one){

                if((DB::table('hotels')->where('room_number',$one->room_number)
                                        ->where('reservation_start','<',$one->reservation_start)
                                        ->where('reservation_end','>',$one->reservation_end)
                                        ->orWhere('room_number',$one->room_number)
                                        ->where('reservation_start','<',$one->reservation_end)
                                        ->where('reservation_end','>',$one->reservation_start))->count()>0)
                {
                    $cond=false;
                    break;
                };

            };
        };

        
////////GROUP CONDITION CHECK


        if(($group_all->count()>0)){
            $cond=true;
            foreach($group_all as $one){
                if(!Schema::hasTable($one->object_name.'s')){
                    $cond=false;
                    break;
                }
                if(DB::table($one->object_name.'s')->where('field_number',$one->field_number)->where('reservation_start',$one->reservation_start)->count()>0)
                {
                    $cond=false;
                    break;
                };

            };
        };



//////////////// TWORZENIE NOWEJ REZERWACJI ID
        if($cond==true){
            $reservation=new Reservation;

            if(Auth::user()->role=='admin'){
                $reservation->user_id=request('who');
                $reservation->event=request('event');
            }else{
                $reservation->user_id=Auth::id();
            }
            $reservation->save();
            $reservation_id=$reservation->reservation_id;
        }

///////////////////// END CONDITION

//////// SOLO ADD
        if(($solo_all->count()>0) && ($cond==true)){
            foreach($solo_all as $one){
                $cost_hour=DB::table('Soloobjects')->where('object_name',$one->object_type)->value('cost_hour');
                $sequence_time=DB::table('Soloobjects')->where('object_name',$one->object_type)->value('sequence_time');
                DB::table($one->object_type.'s')->insert([
                    'reservation_id' => $reservation_id,
                    'guests_amount' => $one->guests_amount,
                    'reservation_start' =>$one->reservation_start,
                    'reservation_end'=> $one->reservation_end,
                    'cost_hour'=>$one->cost_hour,
                ]);
                $total_cost+=($one->guests_amount)*($one->cost_hour*(((Carbon::parse($one->reservation_end))
                    ->diffInMinutes(Carbon::parse($one->reservation_start)))/$sequence_time));
            };

            $reservation->total_cost=$total_cost;
            $reservation->save();
            DB::table('solocarts')->where('user_id',Auth::id())->delete();
        };



////// HOTEL ADD

        if(($hotel_all->count()>0) && ($cond==true)){
            foreach($hotel_all as $one){
                       
                DB::table('hotels')->insert([
                    'reservation_id' => $reservation_id,
                    'room_number'=>$one->room_number,
                    'guests_amount' => $one->guests_amount,
                    'cost'=>$one->cost,
                    'reservation_start' =>$one->reservation_start,
                    'reservation_end'=> $one->reservation_end,
                ]);
                $total_cost+=$one->cost;
            };

            $reservation->total_cost=$total_cost;
            $reservation->save();
            DB::table('hotelcarts')->where('user_id',Auth::id())->delete();
        }


////// GROUP ADD

        if(($group_all->count()>0) && ($cond==true)){
            foreach($group_all as $one){     
                DB::table($one->object_name.'s')->insert([
                    'reservation_id' => $reservation_id,
                    'field_number'=>$one->field_number,
                    'cost'=>$one->cost,
                    'reservation_start' =>$one->reservation_start,
                    'reservation_end' =>$one->reservation_end,
                ]);
                $total_cost+=$one->cost;
            };

            $reservation->total_cost=$total_cost;
            $reservation->save();
            DB::table('groupcarts')->where('user_id',Auth::id())->delete();
        }
        if($cond==false){
          return redirect()->home()->withErrors(['Nie udało się dokonać rezerwacji, sprawdź dostępność miejsc lub czy obiekt jest otwarty.'])  ;
        };
        $request->session()->flash('information', 'Gratulacje, Twoje oczekujące rezerwacje zostały dodane');
        return redirect()->home();  

    }



};
