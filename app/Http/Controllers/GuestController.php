<?php

namespace App\Http\Controllers;

use App\Events\GetData;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GuestController extends Controller
{
    //
    public function sendData(Request $data){

        /* Limpiamos el numero celular */
        $phone_clean = preg_replace("/[^0-9]/", "", $data->phone);
        $phone = substr($phone_clean, -10);
        $checklead = Lead::where('phone', $phone)->exists();

        if($checklead == true){

            $lead = Lead::where('phone', $phone)->first();

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

            $lead = Lead::where('phone', $phone)->first();
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

        $user =  $this->assignLead();

        $new_lead = new Lead();

        $new_lead->name = $data->name;
        $new_lead->phone = $phone;
        $new_lead->email = $data->email;
        $new_lead->campaing = null;
        $new_lead->college_degree = $data->college_degree;
        $new_lead->other_fields = null;
        $new_lead->from = 'Pagina Web';

        $user->leads()->save($new_lead);

        $new_lead->save();

        $new_leads = Lead::where('status', 'nuevo')->get();
        $new_again = Lead::where('newagain', 1)->get();

        $leads = $new_leads->merge($new_again);
        event(new GetData($leads));

        return 'lead-guardado';
    }

    public function neuuniUniverisdad(){
        return Inertia::render('Guest/NeuuniUniversidad');
    }


    public function testJSON(){
        $user = User::find(1);

        $nombres = ["José Lopez", "Pedro Macias", "Raúl Hernandez", "María Juana Lopez Marquez", "Luisa Hegeminia Lopez Kool", "Adriana ", "Angela", "Allan Federico Lopez Montez", "Jovan Lopez", "Dante Larrauri Laso", "Axel Alejandro Hernandez Hernandez", "Federico Ortiz Lopez", "Jocelyn Campos Huerta"];
        $name = $nombres[ mt_rand(0, count($nombres) -1) ];

        $llegada = ["Formulario Facebook", "Pagina Web", "Ingresado Manualmente"];
        $from = $llegada[ mt_rand(0, count($llegada) -1) ];

        $carrera = ["Licenciatura en Administración de Negocios", "Licenciatura en Derecho", "Ingeniería Industrial", "Licenciatura en Mercadotecnia y Diseño Digital", "Maestría en administración y dirección empresarial", "Maestría en dirección de empresas industriales", "Maestría en docencia y dirección de instituciones educativas", "Maestría en sistema penal acusatorio y adversarial", "Doctorado en pedagogía e investigación educativa", "Ingenieria en Sistemas Inteligentes"];
        $college_degree = $carrera[ mt_rand(0, count($carrera) -1) ];


        $telefono = ["4445473439", "4445484960", "5558695433", "8983223231", "4445085314", "4423424212", "9928388273"];
        $phone = $telefono[ mt_rand(0, count($telefono) -1) ];


        $checklead = Lead::where('phone', $phone)->exists();

        if($checklead == true) {

            $lead = Lead::where('phone', $phone)->first();

            $lead->count++;
            $lead->newagain = true;
            $lead->save();

            $leads = $user->leads()->where('status', 'nuevo')->orWhere('newagain', true)->get();
            event(new GetData($leads));

            return $lead;
        }


        $new_lead = new Lead();

        $new_lead->name = $name;
        $new_lead->phone = $phone;
        $new_lead->email = strtolower(str_replace(' ', '', $name)) . '@gmail.com';
        $new_lead->campaing = 'DOC27JUL23';
        $new_lead->college_degree = $college_degree;
        $new_lead->other_fields = null;
        $new_lead->from = $from;

        $user->leads()->save($new_lead);

        $new_lead->save();

        $leads = $user->leads()->where('status', 'nuevo')->orWhere('newagain', true)->get();

        event(new GetData($leads));
        /* return $new_lead->user; //Devolvemos el usuario con el que  esta asignado el lead*/

        return $new_lead; //Devolvemos el lead creado.
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
