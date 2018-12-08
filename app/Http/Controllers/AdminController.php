<?php

namespace App\Http\Controllers;

use App\Groupobject;
use App\Gym;
use App\Hotel;
use App\Soloobject;
use App\Reservation;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;


class AdminController extends Controller
{


/////////////// STATYCZNE FUNKCJE

    public static function delete_reservation($user_id,$reservation_id){
        if(DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost')==0){
                    DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->delete();
                    return redirect()->back()->with('information', 'Rezerwacja została usunięta');
        } 
    }

/////////////////



//////////////////////// USER

    


    public function show_users(){

    	$users=User::all();
        $solo=Soloobject::select('object_name')->distinct()->get();
        $group=Groupobject::select('object_name')->distinct()->get();
        $reservations=Reservation::all();
        $hotels=DB::table('reservations')->join('hotels','reservations.reservation_id','=','hotels.reservation_id')->get();
        $total_cost=0;

        foreach($solo as $soloo){
            $result[$soloo->object_name]=DB::table('reservations')->join($soloo->object_name.'s','reservations.reservation_id','=',$soloo->object_name.'s.reservation_id')->get();
        }

        foreach($group as $groupp){
            $result_group[$groupp->object_name]=DB::table('reservations')->join($groupp->object_name.'s','reservations.reservation_id','=',$groupp->object_name.'s.reservation_id')->get();
        }

    
    	return view('administration.show_users',compact('users','solo','group','hotels','result','result_group','reservations'));
    }




    public function delete_user(User $user,$user_id){
    	$delete=$user::find($user_id);
        $delete->delete();
        
        return redirect()->back()->with('information', 'Użytkownik o adresie email '.$delete->email. ' został usunięty');
    }



    public function render_view_edit_user($user_id){

        $user=User::find($user_id);
        return \Response::json(['body' => view('administration.edit_user_modal_ajax',compact('user'))->render()]);
        
    }


    public function edit_user(User $user,$user_id,Request $request){
        $request->validate([
               'email'=>'email|unique:users,email,'.$user_id.',user_id',
                'password'=>'confirmed',        
        ]);
        $user=User::find($user_id);
        $user->first_name=request('first_name');
        $user->last_name=request('last_name');
        $user->email=request('email');
        if ( ! request('password') == '')
            {
                $user->password = bcrypt(request('password'));
            }
        $user->city=request('city');
        $user->phone=request('phone');
        

        $user->save();
        return redirect()->back();

    }



    public function reservation_activate(){
        $reservation_id=request('reservation_id');
        $reservation=Reservation::find($reservation_id);
        $total_cost= Reservation::where('reservation_id',$reservation_id)->value('total_cost');
        $rest_cost= ($total_cost-request('paid'));
        if($rest_cost>=0){
            Reservation::where('reservation_id',$reservation_id)->update(['status'=>1,'comment'=>request('comment'),'total_cost'=>$rest_cost]);
            return redirect()->back()->with('information', 'Rezerwacja aktywowana');
        }else{
            return redirect()->back()->withErrors(['Została wpisana błędna kwota']);
        }
        
    }
////////////////////////// USER

///////////////////////// GROUP

    public function show_active_group_reservations($name,$user_id){
        $object_name=$name.'s';
        $reservation=Reservation::join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                    ->where('reservations.status','=',1)
                    ->where('user_id',$user_id)
                    ->select('reservations.*')
                    ->distinct()
                    ->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->join("$name"."_fields","$name"."_fields.field_number",'=',"$object_name.field_number")
                            ->where('reservations.user_id','=',$user_id)
                            ->where('reservations.status','=',1)
                            ->get();
        $groups=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->where('reservations.status','=',1)->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_active_group_reservations',compact('reservation','active_reservations','user_id','name','groups','role'));
       
    }


    public function show_inactive_group_reservations($name,$user_id){
        $object_name=$name.'s';
        $reservation=Reservation::join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                    ->where('reservations.status','=',0)
                    ->where('user_id',$user_id)
                    ->select('reservations.*')
                    ->distinct()
                    ->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->join("$name"."_fields","$name"."_fields.field_number",'=',"$object_name.field_number")
                            ->where('reservations.user_id','=',$user_id)
                            ->where('reservations.status','=',0)
                            ->get();
        $groups=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->where('reservations.status','=',0)->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_inactive_group_reservations',compact('reservation','active_reservations','user_id','name','groups','role'));
       
    }



    public function delete_all_group_inactive_reservations(Reservation $reservation,$name, $user_id,$reservation_id){
        $object_name=$name.'s';
        $delete=DB::table($object_name)->join('reservations','reservations.reservation_id','=', $object_name.'.reservation_id')
                            ->where($object_name.'.reservation_id','=',$reservation_id);
        $cost=0;
        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        // $cost=(DB::table($object_name)->where('reservation_id','=',$reservation_id)->sum('cost_hour'))*(DB::table($object_name)->where('reservation_id','=',$reservation_id)->sum('guests_amount'));
        $result_costs=DB::table($object_name)->where('reservation_id','=',$reservation_id)->get();
        foreach($result_costs as $result_cost){
            $cost+=($result_cost->cost);
        };
        $cost_new=($total_cost)-($cost);

        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);  

        self::delete_reservation($user_id,$reservation_id);                   
                              
        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }




    public function delete_group_inactive_reservations(Reservation $reservation,$name,$user_id,$reservation_id,$reservation_start){
        $object_name=$name.'s';
        $delete=DB::table($object_name)
                            ->join('reservations','reservations.reservation_id','=',"$object_name.reservation_id")
                            ->where("$object_name.reservation_start",'=',$reservation_start)->where("$object_name.reservation_id",'=',$reservation_id);

        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        $cost=DB::table($object_name)->where('reservation_start','=',$reservation_start)->where('reservation_id','=',$reservation_id)->first();
        $cost_new=($total_cost)-($cost->cost);

        // UPDATE NOWEGO KOSZTU $cost_new
        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);

        self::delete_reservation($user_id,$reservation_id);     

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }


////////////////////////// GROUP


////////////////////////// SOLO

    public function show_active_solo_reservations($name,$user_id){
        $object_name=$name.'s';
        $reservation=Reservation::join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')->where('reservations.status','=',1)->where('reservations.user_id',$user_id)->select('reservations.*')->distinct()->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->where('reservations.status','=',1)
                            ->where('reservations.user_id','=',$user_id)
                            ->get();
        $solos=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->where('reservations.status','=',1)->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_active_solo_reservations',compact('reservation','active_reservations','user_id','name','solos','role'));
       
    }


     public function delete_all_solo_active_reservations(Reservation $reservation,$name, $user_id,$reservation_id){
        $object_name=$name.'s';
        $delete=DB::table('reservations')->where('reservation_id',$reservation_id);
            
        // self::delete_reservation($user_id,$reservation_id);      

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }



    public function delete_solo_active_reservations(Reservation $reservation,$name,$user_id,$reservation_id,$reservation_start){
        $object_name=$name.'s';
        $delete=DB::table($object_name)
                            ->join('reservations','reservations.reservation_id','=',"$object_name.reservation_id")
                            ->where("$object_name.reservation_start",'=',$reservation_start)->where("$object_name.reservation_id",'=',$reservation_id);

        // self::delete_reservation($user_id,$reservation_id);     

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }






    public function show_inactive_solo_reservations($name,$user_id){
        $object_name=$name.'s';
        $reservation=Reservation::join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')->where('reservations.status','=',0)->where('reservations.user_id',$user_id)->select('reservations.*')->distinct()->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->where('reservations.status','=',0)
                            ->where('reservations.user_id','=',$user_id)
                            ->get();
        $solos=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->where('reservations.status','=',0)->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_inactive_solo_reservations',compact('reservation','active_reservations','user_id','name','solos','role'));
       
    }



    public function delete_all_solo_inactive_reservations(Reservation $reservation,$name, $user_id,$reservation_id){
        $object_name=$name.'s';
        $delete=DB::table($object_name)->join('reservations','reservations.reservation_id','=', $object_name.'.reservation_id')
                            ->where($object_name.'.reservation_id','=',$reservation_id);
        $cost=0;
        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        // $cost=(DB::table($object_name)->where('reservation_id','=',$reservation_id)->sum('cost_hour'))*(DB::table($object_name)->where('reservation_id','=',$reservation_id)->sum('guests_amount'));
        $result_costs=DB::table($object_name)->where('reservation_id','=',$reservation_id)->get();
        foreach($result_costs as $result_cost){
            $cost+=($result_cost->guests_amount)*($result_cost->cost_hour);
        };
        $cost_new=($total_cost)-($cost);

        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);                      
                           
        self::delete_reservation($user_id,$reservation_id);      

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }




    public function delete_solo_inactive_reservations(Reservation $reservation,$name,$user_id,$reservation_id,$reservation_start){
        $object_name=$name.'s';
        $delete=DB::table($object_name)
                            ->join('reservations','reservations.reservation_id','=',"$object_name.reservation_id")
                            ->where("$object_name.reservation_start",'=',$reservation_start)->where("$object_name.reservation_id",'=',$reservation_id);

        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        $cost=DB::table($object_name)->where('reservation_start','=',$reservation_start)->where('reservation_id','=',$reservation_id)->first();
        $cost_new=($total_cost)-(($cost->guests_amount)*($cost->cost_hour));

        // UPDATE NOWEGO KOSZTU $cost_new
        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);

        self::delete_reservation($user_id,$reservation_id);     

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }







////////////////////////// HOTEL

    public function show_active_hotel_reservations($user_id){
        
        $reservation=Reservation::join('hotels',"hotels.reservation_id",'=','reservations.reservation_id')->where('reservations.status','=',1)->where('reservations.user_id',$user_id)->select('reservations.*')->distinct()->get();  
        $active_reservations=Reservation::with('user')
                            ->join('hotels','hotels.reservation_id','=','reservations.reservation_id')
                            ->join('rooms','rooms.room_number','=','hotels.room_number')
                            ->where('reservations.status','=',1)
                            ->where('reservations.user_id','=',$user_id)
                            ->get();
        $hotels=DB::table('reservations')->join('hotels','hotels.reservation_id','=','reservations.reservation_id')->where('reservations.status','=',1)->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_active_hotel_reservations',compact('reservation','active_reservations','user_id','hotels','role'));
       
    }



    public function show_inactive_hotel_reservations($user_id){
        
        $reservation=Reservation::join('hotels',"hotels.reservation_id",'=','reservations.reservation_id')->where('reservations.status','=',0)->where('reservations.user_id',$user_id)->select('reservations.*')->distinct()->get();     
        $active_reservations=Reservation::with('user')
                            ->join('hotels','hotels.reservation_id','=','reservations.reservation_id')
                            ->join('rooms','rooms.room_number','=','hotels.room_number')
                            ->where('reservations.status','=',0)
                            ->where('reservations.user_id','=',$user_id)
                            ->get();
        $hotels=DB::table('reservations')->join('hotels','hotels.reservation_id','=','reservations.reservation_id')->get();
        $role=DB::table('users')->where('user_id',$user_id)->value('role');
        return view('administration.show_inactive_hotel_reservations',compact('reservation','active_reservations','user_id','hotels','role'));
       
    }




    public function delete_all_hotel_inactive_reservations(Hotel $hotel,$user_id,$reservation_id){
        
        $delete=Hotel::with('reservation')
                            ->join('reservations','reservations.reservation_id','=','hotels.reservation_id')
                            ->where('hotels.reservation_id','=',$reservation_id);
        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        $cost=DB::table('hotels')->where('reservation_id','=',$reservation_id)->sum('cost');
        $cost_new=($total_cost)-($cost);

        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);       

        self::delete_reservation($user_id,$reservation_id);      

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }




    public function delete_hotel_inactive_reservations(Hotel $hotel,$user_id,$reservation_id,$reservation_start){
        $delete=Hotel::with('reservation')
                            ->join('reservations','reservations.reservation_id','=','hotels.reservation_id')
                            ->where('hotels.reservation_start','=',$reservation_start)->where('hotels.reservation_id','=',$reservation_id);
                            ;

        $total_cost=DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->value('total_cost');
        $cost=DB::table('hotels')->where('reservation_start','=',$reservation_start)->where('reservation_id','=',$reservation_id)->first();
        $cost_new=($total_cost)-($cost->cost);

        DB::table('reservations')->where('user_id',$user_id)->where('reservation_id',$reservation_id)->update(['total_cost'=>$cost_new]);

        self::delete_reservation($user_id,$reservation_id);     

        $delete->delete();
        
        return redirect()->back()->with('information', 'Rezerwacja została usunięta');
    }




////////////////////////// HOTEL


////////////////////// NEW FIELD AND ROOM

    public function new_field_form(Request $request, $name){
        $object_name=$name.'s';
        if(Schema::hasTable($object_name)){
            return view('administration.new_field',compact('name'));
        }else{
            return redirect('/')->withErrors(['Taki obiekt nie istnieje']);
        };

    }



    public function new_field(Request $request){
            $this->validate(request(),[
                'field_type'=>'required',
                'cost'=>'required|integer',
                'hour_start'=>'required',
                'hour_end'=>'required',
            ]);
            $name=request('name');
            $days=request('day');

            $field=DB::table($name.'_fields')->insert([
                'field_type' => request('field_type'),
                'cost_per_entrance' =>request('cost'),
                'description' =>request('description'),
            ]);
            $field_number=DB::getPdo()->lastInsertId();
            if($days!='null'){
                foreach($days as $day){
                    DB::table($name.'_days')->insert([
                        'field_number'=>$field_number,
                        'hour_start' => request('hour_start').':00',
                        'hour_end' =>request('hour_end').':00',
                        'day' =>$day,
                    ]);
                }
            }
        return redirect('/object/group/'.$name)->with('information','Utworzono nowy obiekt');
    }



    public function new_room_form(Request $request){
        
        return view('administration.new_room');

    }



    public function new_room(Request $request){

        $this->validate(request(),[
                'room_number'=>'required|integer',
                'max_guests'=>'required|integer',
                'cost'=>'required|integer',
        ]);
        DB::table('rooms')->insert([
            'room_number' => request('room_number'),
            'max_guests' =>request('max_guests'),
            'cost_night' =>request('cost'),
            'description'=>request('description'),
            // 'description' =>request('description'),
        ]);

         return redirect('/object/hotel/')->with('information','Utworzono nowy pokój');
    }


////////////////////// NEW FIELD AND ROOM


/////////////////// OBJECTS

    public function objects_list(Request $request){
        $solo=Soloobject::select('object_name')->distinct()->get();
        $group=Groupobject::all();
        return view('administration.objects_list',compact('solo','group'));

    }


    public function object_group(Request $request,$name){
        $object_name=$name.'s';
        $object=Groupobject::where('object_name',$name)->first();
        $object_fields=DB::table($name.'_fields')->get();

        $days=array();
        $pl=array('poniedziałek'=>'monday','wtorek'=>'tuesday','środa'=>'wednesday','czwartek'=>'thursday','piątek'=>'friday','sobota'=>'saturday','niedziela'=>'sunday','codziennie'=>'everyday');
        foreach($object_fields as $object_field){
            $field_number=$object_field->field_number;
            $days[$field_number]=DB::table($name.'_days')->where('field_number',$field_number)->get();
        }
      

        $reserv_amount_inactive=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->where('reservations.status',0)->count();
        $users_inactive=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_inactive=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->distinct()->get();

        $reserv_amount_active=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->where('reservations.status',1)->count();
        $users_active=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_active=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();


        $reserv_amount_event=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event','!=',null)->count();
        $users_event=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_event=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();

        return view('administration.object_group',compact('object','name','object_fields','reserv_amount_inactive','users_inactive','user_hours_inactive','reserv_amount_active','users_active','user_hours_active','reserv_amount_event','users_event','user_hours_event','days','pl'));

    }




    public function object_group_remove(){
        $name=request('name');
        $rows=DB::table($name.'s')->get();
        foreach($rows as $row){
            DB::table('reservations')->where('reservation_id',$row->reservation_id)->delete();
        }
        Schema::dropIfExists($name.'s');
        Schema::dropIfExists($name.'_days');
        Schema::dropIfExists($name.'_fields');
        DB::table('groupobjects')->where('object_name',$name)->delete();
        return redirect('/objects_list');
    }





    public function group_add_day(){
        $name=request('name');
        $object_name=$name.'s';
        $days=request('day');
        
        $hour_start=request('day_hour_start');
        $hour_end=request('day_hour_end');
        $field_number=request('choose_field');
        $this->validate(request(),[
                'day'=>'required',
                'day_hour_start'=>'required',
                'day_hour_end'=>'required|greater_than:day_hour_start',
        ]);

        if(DB::table($name.'_days')->whereIn('day',$days)->where(function($query) use($hour_start,$hour_end){
                    $query->where('hour_start','<',$hour_start)
                    ->where('hour_end','>',$hour_end)
                    ->orWhere('hour_start','<',$hour_end)
                    ->where('hour_end','>',$hour_start);
                })->count()>0){
            return redirect('/object/group/'.$name)->withErrors(['Jeden z wybranych dni jest już w bazie.']);
        }
        if(in_array('everyday',$days)){
            DB::table($name.'_days')->where('field_number',$field_number)->delete();
        }
        foreach($days as $day){
            DB::table($name.'_days')->insert([
                'field_number'=>$field_number,
                'hour_start'=>$hour_start.':00',
                'hour_end'=>$hour_end.':00',
                'day'=>$day,

            ]);
        }
        return redirect('/object/group/'.$name);
    }



    public function group_edit_day_time(){
        $day=request('day');
        $name=request('name');
        $field_number=request('field_number');
        $object_name=$name.'s';
        $hour_start=request('day_hour_start');
        $hour_end=request('day_hour_end');
        $choose_day=request('choose_day');
        $id=request('id');
        $variables=DB::table($name.'_days')->where('field_number',$field_number)->first();
        $reservations=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->get();

        foreach($reservations as $reservation){
            if(Carbon::parse($reservation->reservation_start)->format('l')==ucfirst($day) || Carbon::parse($reservation->reservation_end)->format('l')==ucfirst($day)){
                return redirect('/object/group/'.$name)->withErrors(['W ten dzień są aktywne rezerwacje']);
            }
        }

        // if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
        //     return redirect('/object/group/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        // };
        $this->validate(request(),[
                'choose_day'=>'required',
                'day_hour_start'=>'required',
                'day_hour_end'=>'required|greater_than:day_hour_start',

        ]);
        if(DB::table($name.'_days')->where('field_number',$field_number)->where('day',$choose_day)->where(function($query) use($hour_start,$hour_end){
            $query->where('hour_start','<',$hour_start)
            ->where('hour_end','>',$hour_end)
            ->orWhere('hour_start','<',$hour_end)
            ->where('hour_end','>',$hour_start);
        })
        ->where('id','!=',$id)->count()>0){
             return redirect('/object/group/'.$name)->withErrors(['W ten dzień i godzinę obiekt jest juz otwarty']);
        }
        if($choose_day=='everyday'){
            DB::table($name.'_days')->where('field_number',$field_number)->delete();
            DB::table($name.'_days')->insert([
                'field_number'=>$field_number,
                'hour_start'=>$hour_start.':00',
                'hour_end'=>$hour_end.':00',
                'day'=>'everyday',
            ]);
        }
        DB::table($name.'_days')->where('field_number',$field_number)->where('id',$id)->where('day',$day)->distinct()->update(['day'=>$choose_day,'hour_start'=>$hour_start,'hour_end'=>$hour_end]);
        return redirect('/object/group/'.$name);
    }



    public function group_remove_day(){
        $day=request('day');
        $name=request('name');
        $field_number=request('field_number');
        $id=request('id');
        if(DB::table($name.'_days')->where('field_number',$field_number)->count()==1){
            return redirect('/object/group/'.$name)->withErrors(['Nie możesz usunąć ostatniej dostępnej opcji']);
        }
        DB::table($name.'_days')->where('field_number',$field_number)->where('day',$day)->where('id',$id)->delete();
        return redirect('/object/group/'.$name);
    }




    public function group_edit_time(){
        $name=request('name');
        $object_name=$name.'s';
        $sequence_time=request('time');
        if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
            return redirect('/object/group/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        };
        $this->validate(request(),[
                'sequence_time'=>'required|integer',
        ]);
        DB::table('Groupobjects')->where('object_name',$name)->update(['sequence_time'=>$sequence_time]);
        return redirect('/object/group/'.$name);
    }




    public function group_edit_break_time(){
        $name=request('name');
        $object_name=$name.'s';
        if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
            return redirect('/object/group/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        };
        $this->validate(request(),[
                'break_time'=>'nullable|integer',
        ]);
        $break_time=request('break_time');
        DB::table('Groupobjects')->where('object_name',$name)->update(['break_time'=>$break_time]);
        return redirect('/object/group/'.$name);
    }


    public function group_edit_field($name,$field_number){
        $name=request('name');
        $field_number=request('field_number');
        $field_type=request('field_type');
        $cost_per_entrance=request('cost_per_entrance');
        $description=request('description');
        $this->validate(request(),[
                'field_type'=>'required',
                'cost_per_entrance'=>'required|integer',
        ]);
        DB::table($name.'_fields')->where('field_number',$field_number)->update(['field_type'=>$field_type,'cost_per_entrance'=>$cost_per_entrance,'description'=>$description]);
        return redirect('/object/group/'.$name);
    }


    public function group_remove_field(){
        $name=request('name');
        $field_number=request('field_number');
        DB::table($name.'_fields')->where('field_number',$field_number)->delete();
        return redirect('/object/group/'.$name);
    }



    public function object_solo(Request $request,$name){
        $object_name=$name.'s';
        $object=Soloobject::where('object_name',$name)->first();
        $days=DB::table('Soloobjects')->where('object_name',$name)->select('*')->get();
        $days_pl=array();
        $pl=array('poniedziałek'=>'monday','wtorek'=>'tuesday','środa'=>'wednesday','czwartek'=>'thursday','piątek'=>'friday','sobota'=>'saturday','niedziela'=>'sunday','codziennie'=>'everyday');

        // foreach($days as $day){

        //     $day_object[$day->day]=DB::table('Objects')->where('object_name',$name)->where('day',$day->day)->get();
        // }

        $reserv_amount_inactive=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->where('reservations.status',0)->count();
        $users_inactive=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_inactive=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->distinct()->get();

        $reserv_amount_active=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->where('reservations.status',1)->count();
        $users_active=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_active=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();


        $reserv_amount_event=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event','!=',null)->count();
        $users_event=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_event=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();


        return view('administration.object_solo',compact('object','name','reserv_amount_inactive','reserv_amount_active','reserv_amount_event','users_inactive','users_active','users_event','days','day_object','pl','user_hours_inactive','user_hours_active','user_hours_event'));

    }




    public function object_solo_remove(){
        $name=request('name');
        $rows=DB::table($name.'s')->get();
        foreach($rows as $row){
            DB::table('reservations')->where('reservation_id',$row->reservation_id)->delete();
        }
        Schema::dropIfExists($name.'s');
        Schema::dropIfExists($name.'_fields');
        DB::table('Soloobjects')->where('object_name',$name)->delete();
        return redirect('/objects_list');
    }





    public function solo_edit_time(){
        $name=request('name');
        $object_name=$name.'s';
        $sequence_time=request('time');
        if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
            return redirect('/object/solo/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        };
        $this->validate(request(),[
                'sequence_time'=>'required|integer',
        ]);
        DB::table('Soloobjects')->where('object_name',$name)->update(['sequence_time'=>$sequence_time]);
        return redirect('/object/solo/'.$name);
    }


    public function solo_add_day(){
        $name=request('name');
        $object_name=$name.'s';
        $days=request('day');
        
        $hour_start=request('day_hour_start');
        $hour_end=request('day_hour_end');
        $variables=DB::table('Soloobjects')->where('object_name',$name)->first();
        $this->validate(request(),[
                'day'=>'required',
                'day_hour_start'=>'required',
                'day_hour_end'=>'required|greater_than:day_hour_start',
        ]);

        if(DB::table('Soloobjects')->where('object_name',$name)->whereIn('day',$days)->where(function($query) use($hour_start,$hour_end){
                $query->where('hour_start','<',$hour_start)
                ->where('hour_end','>',$hour_end)
                ->orWhere('hour_start','<',$hour_end)
                ->where('hour_end','>',$hour_start);
            })->count()>0){
            return redirect('/object/solo/'.$name)->withErrors(['Jeden z wybranych dni jest już w bazie.']);
        }
        if(in_array('everyday',$days)){
            DB::table('Soloobjects')->where('object_name',$name)->delete();
        }
        foreach($days as $day){
            DB::table('Soloobjects')->insert([
                'object_name'=>$name,
                'system'=>'individual',
                'cost_hour'=>$variables->cost_hour,
                'sequence_time'=>$variables->sequence_time,
                'max_guests'=>$variables->max_guests,
                'break_time'=>$variables->break_time,
                'hour_start'=>$hour_start.':00',
                'hour_end'=>$hour_end.':00',
                'day'=>$day,
            ]);
        }
        return redirect('/object/solo/'.$name);
    }


    public function solo_edit_day_time(){
        $name=request('name');
        $day=request('day');
        $object_name=$name.'s';
        $hour_start=request('day_hour_start');
        $hour_end=request('day_hour_end');
        $choose_day=request('choose_day');
        $id=request('id');
        $variables=DB::table('Soloobjects')->where('object_name',$name)->first();
        
        $reservations=DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->get();
        foreach($reservations as $reservation){
            if(Carbon::parse($reservation->reservation_start)->format('l')==ucfirst($day) || Carbon::parse($reservation->reservation_end)->format('l')==ucfirst($day)){
                return redirect('/object/solo/'.$name)->withErrors(['W ten dzień są aktywne rezerwacje']);
            }
        }
        // if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
        //     return redirect('/object/solo/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        // };
        $this->validate(request(),[
                'choose_day'=>'required',
                'day_hour_start'=>'required',
                'day_hour_end'=>'required|greater_than:day_hour_start',
        ]);

        if(DB::table('Soloobjects')->where('object_name',$name)->where('day',$choose_day)->where(function($query) use($hour_start,$hour_end){
                $query->where('hour_start','<',$hour_start)
                ->where('hour_end','>',$hour_end)
                ->orWhere('hour_start','<',$hour_end)
                ->where('hour_end','>',$hour_start);
            })
            ->where('id','!=',$id)->count()>0){
             return redirect('/object/solo/'.$name)->withErrors(['W ten dzień obiekt jest juz otwarty']);
        }
        
        if($choose_day=='everyday'){
            DB::table('Soloobjects')->where('object_name',$name)->delete();
            DB::table('Soloobjects')->insert([
                'object_name'=>$name,
                'system'=>'individual',
                'cost_hour'=>$variables->cost_hour,
                'sequence_time'=>$variables->sequence_time,
                'max_guests'=>$variables->max_guests,
                'break_time'=>$variables->break_time,
                'hour_start'=>$hour_start.':00',
                'hour_end'=>$hour_end.':00',
                'day'=>'everyday',
            ]);
        }
       
        DB::table('Soloobjects')->where('id',$id)->distinct()->update(['day'=>$choose_day,'hour_start'=>$hour_start,'hour_end'=>$hour_end]);
        return redirect('/object/solo/'.$name);
    }




    public function solo_remove_day(){
        $name=request('name');
        $day=request('day');
        $id=request('id');
        if(DB::table('Soloobjects')->where('object_name',$name)->count()==1){
            return redirect('/object/solo/'.$name)->withErrors(['Nie możesz usunąć ostatniej dostępnej opcji']);
        }
        DB::table('Soloobjects')->where('object_name',$name)->where('day',$day)->where('id',$id)->delete();
        return redirect('/object/solo/'.$name);
    }





    public function solo_edit_break_time(){
        $name=request('name');
        $object_name=$name.'s';
        $break_time=request('break_time');
        if(DB::table($object_name)->join('reservations','reservations.reservation_id',$object_name.'.reservation_id')->where('event',null)->count()>0){
            return redirect('/object/solo/'.$name)->withErrors(['Nie można edytować ponieważ istnieją aktywne rezerwacje']);
        };
        $this->validate(request(),[
                'break_time'=>'integer',
        ]);
        DB::table('Soloobjects')->where('object_name',$name)->update(['break_time'=>$break_time]);
        return redirect('/object/solo/'.$name);
    }


    public function solo_edit_maxguests(){
        $name=request('name');
        $object_name=$name.'s';
        $max_guests=request('maxGuests');
        $this->validate(request(),[
                'maxGuests'=>'required|integer',
        ]);
        DB::table('Soloobjects')->where('object_name',$name)->update(['max_guests'=>$max_guests]);
        return redirect('/object/solo/'.$name);
    }
    
    public function solo_edit_cost(){
        $name=request('name');
        $object_name=$name.'s';
        $cost_hour=request('cost');
        $this->validate(request(),[
                'cost'=>'required|integer',
        ]);
        DB::table('Soloobjects')->where('object_name',$name)->update(['cost_hour'=>$cost_hour]);
        return redirect('/object/solo/'.$name);
    }





    public function object_hotel(Request $request){
       
        $hotel=Hotel::all();
        $object_rooms=DB::table('rooms')->orderBy('room_number')->get();


        $reserv_amount_inactive=DB::table('hotels')->join('reservations','reservations.reservation_id','hotels'.'.reservation_id')->where('event',null)->where('reservations.status',0)->count();
        $users_inactive=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_inactive=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',0)->where('event',null)->distinct()->get();

        $reserv_amount_active=DB::table('hotels')->join('reservations','reservations.reservation_id','hotels'.'.reservation_id')->where('event',null)->where('reservations.status',1)->count();
        $users_active=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_active=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('reservations.status','=',1)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();


        $reserv_amount_event=DB::table('hotels')->join('reservations','reservations.reservation_id','hotels'.'.reservation_id')->where('event','!=',null)->count();
        $users_event=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('users.email','users.user_id')->distinct()->get();
        $user_hours_event=DB::table('hotels')->join('reservations','hotels'.'.reservation_id','reservations.reservation_id')->join('users','users.user_id','reservations.user_id')->where('event','!=',null)->select('reservation_start','reservation_end')->distinct()->orderBy('reservation_start')->get();



      
        return view('administration.object_hotel',compact('hotel','object_rooms','reserv_amount_inactive','users_inactive','user_hours_inactive','reserv_amount_active','users_active','user_hours_active','reserv_amount_event','users_event','user_hours_event'));

    }


    public function hotel_edit_room(){
        $number=request('number');
        $max_guests=request('max_guests');
        $cost=request('cost');
        $description=request('description');
        $this->validate(request(),[
                'cost'=>'required|integer',
                'max_guests'=>'required|integer',
        ]);
        DB::table('rooms')->where('room_number',$number)->update(['cost_night'=>$cost,'max_guests'=>$max_guests,'description'=>$description]);
        return redirect('/object/hotel/');
    }



    public function hotel_remove_room(){
        $number=request('number');
        DB::table('rooms')->where('room_number',$number)->delete();
        return redirect('/object/hotel');
    }



    public function close_solo_object($name){
            $object_name=$name.'s';
            $sequence_time=DB::table('Soloobjects')->where('object_name',$name)->value('sequence_time');
            $close_reason=request('close_reason');
            $date_start=request('date_start');
            $date_end=request('date_end');
            $hour_start=request('hour_start').':00';
            $hour_end=request('hour_end').':00';

            $this->validate(request(),[
                'date_start'=>'required|date_format:"Y-m-d"',
                'date_end'=>'required|date_format:"Y-m-d"',
                'hour_start'=>'required|date_format:"H:i"',
                'hour_end'=>'required|date_format:"H:i"',
                'close_reason'=>'required',
            ]); 
            $reservation=new Reservation;
                $reservation->event=$close_reason;
                $reservation->user_id=Auth::id();
                $reservation->reservation_type=$name;
                $reservation->save();
                $reservation_id=$reservation->reservation_id;
            
                

            DB::table($object_name)->insert([
                'reservation_id'=>$reservation_id,
                'guests_amount'=>0,
                'reservation_start'=>$date_start." ".$hour_start,
                'reservation_end'=>$date_end." ".$hour_end,
                'cost_hour'=>0,
            ]);
       
         }

    public function close_group_object(){
        $name=request('name');
        $field_number=request('field_number');
        $object_name=$name.'s';
        $sequence_time=DB::table('groupobjects')->where('object_name',$name)->value('sequence_time');
        $close_reason=request('close_reason');
        $date_start=request('date_start');
        $date_end=request('date_end');
        $hour_start=request('hour_start').':00';
        $hour_end=request('hour_end').':00';
        $this->validate(request(),[
                'date_start'=>'required|date_format:"Y-m-d"',
                'date_end'=>'required|date_format:"Y-m-d"',
                'hour_start'=>'required|date_format:"H:i"',
                'hour_end'=>'required|date_format:"H:i"',
                'close_reason'=>'required',
        ]); 
        $reservation=new Reservation;
            $reservation->event=$close_reason;
            $reservation->user_id=Auth::id();
            $reservation->reservation_type=$name;
            $reservation->save();
            $reservation_id=$reservation->reservation_id;
        
        DB::table($object_name)->insert([
            'reservation_id'=>$reservation_id,
            'field_number'=>$field_number,
            'reservation_start'=>$date_start." ".$hour_start,
            'reservation_end'=>$date_end." ".$hour_end,
            'cost'=>0,
        ]);
               
    }

    public function close_room(){
        
        $room_number=request('room_number');
        $close_reason=request('close_reason');
        $date_start=request('date_start');
        $date_end=request('date_end');
        $this->validate(request(),[
                'date_start'=>'required|date',
                'date_end'=>'required|date',
                'close_reason'=>'required',
        ]); 

        $reservation=new Reservation;
            $reservation->event=$close_reason;
            $reservation->user_id=Auth::id();
            $reservation->save();
            $reservation_id=$reservation->reservation_id;
        
        DB::table('hotels')->insert([
            'reservation_id'=>$reservation_id,
            'room_number'=>$room_number,
            'guests_amount'=>0,
            'reservation_start'=>$date_start,
            'reservation_end'=>$date_end,
            'cost'=>0,
        ]);
               
    }


/////////////// OBJECTS

}
