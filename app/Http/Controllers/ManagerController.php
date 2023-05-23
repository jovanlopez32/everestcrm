<?php

namespace App\Http\Controllers;

use App\Models\User;
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

}
