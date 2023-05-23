<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AgentController extends Controller
{
    //
    public function createLead(Request $request) {



        $new_lead = new Lead();

        /* {"name":"Mickey Mouse","email":"mickey@gmail.com","phone":"676676555452","college_degree":"Maestría en sistema penal acusatorio y adversaria"} */
        $new_lead->name = $request->name;
        $new_lead->phone = $request->phone;
        $new_lead->email = $request->email;
        $new_lead->college_degree = $request->college_degree;
        $new_lead->from = "Ingresado Manualmente";


        $user = User::find($request->userid);

        $user->leads()->save($new_lead);

        $new_lead->save();

        return to_route('dashboard');


    }
}
