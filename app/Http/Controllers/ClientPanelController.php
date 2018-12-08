<?php
////////////// TUTAJ REZERWACJA Z KOSZYKA
////////////// TUTAJ REZERWACJA Z KOSZYKA
////////////// TUTAJ REZERWACJA Z KOSZYKA
////////////// TUTAJ REZERWACJA Z KOSZYKA
namespace App\Http\Controllers;

use App\Groupobject;
use App\SoloObject;
use App\Reservation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientPanelController extends Controller
{



    public function show_edit_profile(Request $request){
    	$user=Auth::User();
        return view('client_panel.edit_profile',compact('user'));
    }



    public function edit_profile(Request $request){

        $request->validate([
           	'email'=>'email|unique:users,email,'.Auth::User()->user_id.',user_id',
            'password'=>'confirmed',
            'last_name'=>'required',         
        ]);
        $user_id=Auth::User()->user_id;
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




    public function reservations_in_cart(Request $request){

        $my_id=Auth::id();
     
        $solo_carts=Reservation::carts('solocarts');
        //DB::table('solocarts')->where('user_id',Auth::id())->get();
        $group_carts=Reservation::carts('groupcarts');
        //DB::table('groupcarts')->where('user_id',Auth::id())->get();
        $hotel_carts=Reservation::carts('hotelcarts');
        //DB::table('hotelcarts')->where('user_id',Auth::id())->get();

        $solo_count=Reservation::carts_count('solocarts');
        //DB::table('solocarts')->where('user_id',Auth::id())->count();
        $group_count=Reservation::carts_count('groupcarts');
        $hotel_count=Reservation::carts_count('hotelcarts');

        $all_users=User::all_users();

        return view('client_panel.reservation_cart', compact('all_users','solo_carts','group_carts','hotel_carts','solo_count','group_count','hotel_count','my_id'));
    }




    public function remove_reservations_in_cart(Request $request,$user_id,$id,$object_type){
        DB::table($object_type)->where('user_id',Auth::id())->where('id',$id)->delete();

        $request->session()->flash('information', 'Rezerwacja usunięta');
        return redirect('reservations_in_cart');
    }




    public function remove_cart(Request $request,$user_id){

        $solo_to_remove=DB::table('solocarts')->where('user_id',$user_id);
        $group_to_remove=DB::table('groupcarts')->where('user_id',$user_id);
        $hotel_to_remove=DB::table('hotelcarts')->where('user_id',$user_id);

        $solo_to_remove->delete();
        $group_to_remove->delete();
        $hotel_to_remove->delete();
        
        $request->session()->flash('information', 'Rezerwacja usunięta');
        return redirect('reservations_in_cart');
    }




    public function show_my_reservations(Request $request){
        $reservations=DB::table('reservations')->where('user_id',Auth::id())->get();
        $solo=SoloObject::all();
        $group=Groupobject::all();
        $hotels=DB::table('reservations')->join('hotels','reservations.reservation_id','=','hotels.reservation_id')->get();
        $total_cost=0;

        foreach($solo as $soloo){
            $result[$soloo->object_name]=DB::table('reservations')->join($soloo->object_name.'s','reservations.reservation_id','=',$soloo->object_name.'s.reservation_id')->get();
        }

        foreach($group as $groupp){
            $result_group[$groupp->object_name]=DB::table('reservations')->join($groupp->object_name.'s','reservations.reservation_id','=',$groupp->object_name.'s.reservation_id')->get();
        }
        return view('client_panel.my_reservations',compact('solo','group','hotels','result','result_group','reservations'));
    }

    

    public function remove_my_reservation(){
        $delete=DB::table('reservations')->where('user_id',Auth::id())->where('reservation_id',request('reservation_id'));
        $delete->delete();
        return back()->with('information','Rezerwacja usunięta');
    }



    public function show_my_reservations_solo($name,$reservation_id){
        $object_name=$name.'s';
        $reservation=Reservation::where('user_id',Auth::id())->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->where('reservations.user_id','=',Auth::id())
                            ->where('reservations.reservation_id','=',$reservation_id)
                            ->get();
        $solos=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->get();

        return view('client_panel.my_reservations_solo',compact('reservation','active_reservations','user_id','name','solos'));
       
    }

    // public function remove_my_reservation_solo(){
    //     $object_name=request('name').'s';
    //     $delete=DB::table($object_name)->where('user_id',Auth::id())->where('reservation_id',request('reservation_id'));
    //     $delete->delete();
    // }



    public function show_my_reservations_hotel($reservation_id){
        
        $reservation=Reservation::where('user_id',Auth::id())->get();    
        $active_reservations=Reservation::with('user')
                            ->join('hotels','hotels.reservation_id','=','reservations.reservation_id')
                            ->join('rooms','rooms.room_number','=','hotels.room_number')
                            ->where('reservations.user_id','=',Auth::id())
                            ->where('reservations.reservation_id','=',$reservation_id)
                            ->get();
        $hotels=DB::table('reservations')->join('hotels','hotels.reservation_id','=','reservations.reservation_id')->get();
        return view('client_panel.my_reservations_hotel',compact('reservation','active_reservations','user_id','hotels'));
       
    }

    public function show_my_reservations_group($name,$reservation_id){
        $object_name=$name.'s';
        $reservation=Reservation::where('user_id',Auth::id())->get();
        $active_reservations=Reservation::with('user')
                            ->join("$object_name","$object_name.reservation_id",'=','reservations.reservation_id')
                            ->join("$name"."_fields","$name"."_fields.field_number",'=',"$object_name.field_number")
                            ->where('reservations.user_id','=',Auth::id())
                            ->where('reservations.reservation_id','=',$reservation_id)
                            ->get();
        $groups=DB::table('reservations')->join($name.'s',$name.'s.reservation_id','=','reservations.reservation_id')->get();

        return view('client_panel.my_reservations_group',compact('reservation','active_reservations','user_id','name','groups'));
       
    }


    public function welcome(){
        $events=DB::table('reservations')->whereNotNull('event')->get();
        $event_count=DB::table('reservations')->whereNotNull('event')->count();
        $users_count=DB::table('users')->count();
        $active_count=DB::table('reservations')->where('status',1)->count();
        $inactive_count=DB::table('reservations')->where('status',0)->count();
        $solos=array();
        $groups=array();
        $solo_count=0;
        $group_count=0;
        $i=0;
        $j=0;
        foreach($events as $event){
            // if in soloobjects, else///
            if(DB::table('Soloobjects')->where('object_name', $event->reservation_type)->count()>0){
                $solo_count=DB::table('Soloobjects')->where('object_name', $event->reservation_type)->count();
                $object_name=$event->reservation_type.'s';
                $append_solo=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','=','reservations.reservation_id')->where('reservations.reservation_id',$event->reservation_id)->get();
                $solos[$i]=$append_solo;
                $i++;
            }elseif(DB::table('groupobjects')->where('object_name', $event->reservation_type)->count()>0){
                $group_count=DB::table('groupobjects')->where('object_name', $event->reservation_type)->count();
                $object_name=$event->reservation_type.'s';
                $append_group=DB::table($object_name)->join('reservations',$object_name.'.reservation_id','=','reservations.reservation_id')->join($event->reservation_type."_fields",$object_name.'.field_number','=',$event->reservation_type."_fields.field_number")->where('reservations.reservation_id',$event->reservation_id)->get();
                $groups[$j]=$append_group;
                $j++;
                };
        }
        
        return view('layout.welcome',compact('events','solos','groups','event_count','solo_count','group_count','users_count','active_count','inactive_count'));
    }

}
