<?php

namespace App\Http\Controllers;

use App\Events\GetData;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ManagerController extends Controller
{
    //
    public function viewUsers(){
        $users = User::all();
        return Inertia::render('Manager/Users', ['users' => $users]);
    }

    public function storeUser(Request $r){
        $key = 0;
        $flag_key = false;

        do {
            $key = rand(100000, 999999);
            $flag_key = User::where('key', $key)->exists();
        } while ($flag_key == true);

        $new_user = new User();
        $new_user->key = $key;
        $new_user->name = $r->name;
        $new_user->email = $r->email;
        $new_user->password = Hash::make('Neuuni123');
        $new_user->privileges = strtoupper($r->privileges);
        $new_user->enable_get_lead = $r->enable_get_leads;
        $new_user->save();

        /* return Redirect::route('users.view'); */
        return to_route('users.view');
    }

    public function editUser(Request $request, $id)
    {
        $user = User::find($id);

        if($request->filled('password'))
        {
            $this->validate($request, [
                'name' => 'required', 'string', 'max:255',
                'email' => 'required', 'string', 'email',
                'privileges' => 'required', 'string',
                'password' =>  'required', 'string', 'min:6',
                'enable_get_lead' => 'required', 'integer',
            ]);

            $user->password = Hash::make($request->input('password'));
        }
        else
        {
            $this->validate($request, [
                'name' => 'required', 'string', 'max:255',
                'email' => 'required', 'string', 'email',
                'privileges' => 'required', 'integer',
                'enable_get_lead' => 'required', 'integer',
            ]);
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->privileges = $request->input('privileges');
        $user->enable_get_lead = $request->input('enable_get_lead');

        $user->save();


        return to_route('users.view');
    }

    public function settingsData(){

        $status  = DB::table('status')->get();

        return Inertia::render('Manager/Settings', ['status' => $status]);
    }

    public function storeStatus(Request $request){
        DB::table('status')->insert([
            'text' => $request->text,
        ]);
        return to_route('manager.settings');
    }

    public function FunctionName(Request $request)
    {
        return $request;
    }


    public function listLeads(Request $filter){

        $all_leads = Lead::latest()->with('notes')->with('user')->paginate(100);

        $users = User::all();

        if($filter->session()->get('leads_filter') != null) {
            $all_leads = $filter->session()->get('leads_filter');
            $filter->session()->put('leads_filter', null);
        }else{
            $all_leads = Lead::latest()->with('notes')->with('user')->paginate(100);
        }
        return Inertia::render('Manager/ListLeads', ['all_leads' => $all_leads, 'users' => $users]);
    }


    public function searchlistLeads(Request $filter){

        $nm_filter = $filter->name == null ? '%' : '%' . $filter->name . '%';
        $pn_filter = $filter->phone == null ? '%' : '%' . $filter->phone . '%';
        $cd_filter = $filter->college_degree == 'Todas las carreras' ? '%' : $filter->college_degree;
        $st_filter = $filter->status == 'Todos los estados' ? '%' : $filter->status;
        $fr_filter = $filter->from == 'Todos los canales' ? '%' : $filter->from;
        $fr_user = $filter->user == 'Todos los usuarios' ? '%' : $filter->user;


        $all_leads = Lead::where('college_degree', 'like', $cd_filter)->where('status', 'like', $st_filter)->where('user_id', 'like', $fr_user)->where('from', 'like', $fr_filter)->where('name', 'like', $nm_filter)->where('phone', 'like', $pn_filter)->latest()->with('notes')->with('user')->paginate(30);


        $filter->session()->put('leads_filter', $all_leads);

        return to_route('manager.listleads');
    }

    public function addLeads(){

        $users = User::all();

        return Inertia::render('Manager/AddLead', ['users' => $users]);;
    }

    public function storeLeadsM(Request $request){

        $checklead = Lead::where('phone', $request->phone)->exists();

        if($checklead == true){

            $lead = Lead::where('phone', $request->phone)->first();

            $date_now = Carbon::now(); //Get the actually day.
            $created_at = Carbon::parse($lead->created_at);
            $expires_at = $created_at->copy()->addDays(30);

            if($date_now->gte($expires_at)){

                /* Quitamos al usuario asignado */
                $user = $lead->user;
                $lead->user()->dissociate();

                /* Asiganamos al nuevo usuario */
                $new_user = $this->assignLead();
                $lead->count++;
                $lead->newagain = true;

                DB::table('leads')->where('id', $lead->id)->update(['created_at' => now()]);

                $lead->save();
                $new_user->leads()->save($lead);

                $new_leads = Lead::where('status', 'nuevo')->get();
                $new_again = Lead::where('newagain', 1)->get();

                $leads = $new_leads->merge($new_again);
                event(new GetData($leads));

                return 'si pasaron 30 dias y aqui va el codigo para asignar un nuevo asesor';
            }
        }

        if($checklead == true) {

            $lead = Lead::where('phone', $request->phone)->first();
            $user = $lead->user;

            $lead->count++;
            $lead->newagain = true;
            $lead->save();

            $new_leads = Lead::where('status', 'nuevo')->get();
            $new_again = Lead::where('newagain', 1)->get();

            $leads = $new_leads->merge($new_again);
            event(new GetData($leads));

            return 'lead-enviado';
        }

        $new_lead = new Lead();

        $new_lead->name = $request->name;
        $new_lead->college_degree = $request->college_degree;
        $new_lead->phone = $request->phone;
        $new_lead->email = $request->email;
        $new_lead->from = $request->from;

        $user = null;

        if($request->user == 'Seguir Algoritmo'){
            $user = $this->assignLead();
        }else{
            $user = User::find($request->user);
        }

        $user->leads()->save($new_lead);

        $new_lead->save();

        $new_leads = Lead::where('status', 'nuevo')->get();
        $new_again = Lead::where('newagain', 1)->get();

        $leads = $new_leads->merge($new_again);
        /* event(new GetData($leads)); */

        return 'Lead asignado con exito';

    }

    public function assignLead(){

        $active = User::where('active', true)->where('enable_get_lead', true)->exists();

        if($active){

            $allusers = User::where('enable_get_lead', true)->get();
            $active_user = User::where('active', true)->where('enable_get_lead', true)->first();

            $count = 0;
            /* Find the user that has the flag */
            foreach ($allusers as $key => $user) {

                if($user->id == $active_user->id){
                    $count++;
                    if($count >= sizeof($allusers)){
                        $count = 0; /* When get the latest user, we back to the start */
                    }
                    break;
                }else{
                    $count++;
                }
            }

            $active_user->active = false;
            $active_user->save();

            $new_active_user = User::where('id', $allusers[$count]->id)->first();

            $new_active_user->active = true;
            $new_active_user->save();

            return $new_active_user;    /* Return the next user */


        }else{
            $user = User::where('enable_get_lead', true)->first();
            $user->active = true;
            $user->save();
            return $user;
        }

    }

}
