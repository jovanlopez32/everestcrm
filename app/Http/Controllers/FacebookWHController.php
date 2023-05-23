<?php

namespace App\Http\Controllers;

use App\Events\GetData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use FacebookAds\Object\Ad;
use FacebookAds\Object\Lead;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

use App\Models\Lead as AppLead;
use Carbon\Carbon;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;


use Facebook\Facebook;
use Illuminate\Support\Facades\DB;

class FacebookWHController extends Controller
{
    //

    public function connectWebHook(Request $request){

        /* Its the subscribe method */
        $mode = $request->input('hub_mode');
        /* Facebook App Key */
        $token = $request->input('hub_verify_token');
        /*  */
        $challenge = $request->input('hub_challenge');

        $all_data = "mode: $mode\nchallege: $challenge\ntoken: $token";

        if($mode == 'subscribe' && $token == env('FACEBOOK_VERIFY_TOKEN')){
            Storage::disk('local')->put('facebook_data_request.txt', $all_data);
            Storage::disk('local')->put('facebook_request', $request);
            return response($challenge, 200);
        }else{
            return response('Not Access', 403);
        }
    }

    public function handleWebHook(Request $request){


        /* Delete response data */
        $requestData = file_get_contents('php://input');

        /* Convert the data to json */
        $data = json_decode($requestData);

        /* Acces to facebook form */
        $form_id = $data->entry[0]->changes[0]->value->form_id;

        /* Get information of facebook form trough curl */
        $urlsec = 'https://graph.facebook.com/v16.0/'. $form_id .'?access_token=EAADFZCnUigVsBAGXsKV3AetmupDy60GFl4sngKfbGO2ECVAF3kpv3dJqLRNu741uBVXXlAkqtFBJxVzVZAOLZCxW4OnaNlKKY3w3SRTQ1N9Pc5o6LfkDCfod5d89KHVorikux5E4nRp9tDso7zSRNaeY6jC7hU0fJbjXpOUk8WWh3tUqZCcdvLWXJZBm0gAmWysJ1kgbrogZDZD';

        $chsec = curl_init();
        curl_setopt($chsec, CURLOPT_URL, $urlsec);
        curl_setopt($chsec, CURLOPT_RETURNTRANSFER, true);
        $campaing = curl_exec($chsec);
        curl_close($chsec);

        //para acceder a un campo especifico
        $camp = json_decode($campaing);

        /* Lead CURL */
        $leadgenId = $data->entry[0]->changes[0]->value->leadgen_id;

        $url = 'https://graph.facebook.com/v16.0/'. $leadgenId .'?access_token=EAADFZCnUigVsBAGXsKV3AetmupDy60GFl4sngKfbGO2ECVAF3kpv3dJqLRNu741uBVXXlAkqtFBJxVzVZAOLZCxW4OnaNlKKY3w3SRTQ1N9Pc5o6LfkDCfod5d89KHVorikux5E4nRp9tDso7zSRNaeY6jC7hU0fJbjXpOUk8WWh3tUqZCcdvLWXJZBm0gAmWysJ1kgbrogZDZD';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $lead_curl = curl_exec($ch);
        curl_close($ch);

        /* Acces to created_time field */
        $new_data = json_decode($lead_curl);

        $count = count($new_data->field_data);

        /* Init empty array */
        $other_data = array();

        /* Bucle for get the value of array if is equal to question then assign to the variable */
        for($i = 0; $i < $count; $i++)
        {
            $pregunta = $new_data->field_data[$i]->name;

            switch($pregunta)
            {
                case '¿cuál_es_la_licenciatura_de_interés?_':
                    $licenciatura_de_interes = $new_data->field_data[$i]->values[0];
                    break;
                case 'full_name':
                    $full_name = $new_data->field_data[$i]->values[0];
                    break;
                case 'phone_number':
                    $phone_number = $new_data->field_data[$i]->values[0];
                    break;
                case 'email':
                    $email = $new_data->field_data[$i]->values[0];
                    break;
                case 'número_de_whatsapp':
                    $whatsapp = $new_data->field_data[$i]->values[0];
                    break;
                default:
                    //No hay coincidencia
                    $other_data[$pregunta] = $new_data->field_data[$i]->values[0];
            }
        }


        /* Replace the string for the real college_degree */
        $parts = explode('_', $licenciatura_de_interes);

        foreach ($parts as $part)
        {
            switch ($part) {
                case 'negocios':
                    $licenciatura_de_interes = "Licenciatura en Administración de Negocios";
                    break;
                case 'derecho':
                    $licenciatura_de_interes = "Licenciatura en Derecho";
                    break;
                case 'industrial':
                    $licenciatura_de_interes = "Ingeniería Industrial";
                    break;
                case 'mercadotecnia':
                    $licenciatura_de_interes = "Licenciatura en Mercadotecnia y Diseño Digital";
                    break;
                case 'empresarial':
                    $licenciatura_de_interes = "Maestría en administración y dirección empresarial";
                    break;
                case 'industriales':
                    $licenciatura_de_interes = "Maestría en dirección de empresas industriales";
                    break;
                case 'docencia':
                    $licenciatura_de_interes = "Maestría en docencia y dirección de instituciones educativas";
                    break;
                case 'penal':
                    $licenciatura_de_interes = "Maestría en sistema penal acusatorio y adversarial";
                    break;
                case 'educativa':
                    $licenciatura_de_interes = "Doctorado en pedagogía e investigación educativa";
                    break;
            }
        }

        $checklead = AppLead::where('phone', $whatsapp)->exists();

        if($checklead == true){
            $lead = AppLead::where('phone', $whatsapp)->first();

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

                $new_leads = AppLead::where('status', 'nuevo')->get();
                $new_again = AppLead::where('newagain', 1)->get();

                $leads = $new_leads->merge($new_again);
                event(new GetData($leads));

                return response('Pasaron los 30 dias y se asigno a un nuevo asesor.');
                /* return 'si pasaron 30 dias y aqui va el codigo para asignar un nuevo asesor'; */
            }
        }

        if($checklead == true) {

            $lead = AppLead::where('phone', $whatsapp)->first();
            $user = $lead->user;

            $lead->count++;
            $lead->newagain = true;
            $lead->save();

            $new_leads = AppLead::where('status', 'nuevo')->get();
            $new_again = AppLead::where('newagain', 1)->get();

            $leads = $new_leads->merge($new_again);
            event(new GetData($leads));

            return response('Pidio informes de nuevo, se le notifica al agente.');
            /* return 'lead send again to the agent'; */
        }

        $user =  $this->assignLead();

        $lead = new AppLead();

        $lead->name = $full_name;
        $lead->phone = $whatsapp;
        $lead->email = $email;
        $lead->campaing = $camp->name;
        $lead->college_degree = $licenciatura_de_interes;
        $lead->from = "Facebook Form";
        $lead->other_fields = json_encode($other_data);

        $user->leads()->save($lead);

        $lead->save();

        $new_leads = AppLead::where('status', 'nuevo')->get();
        $new_again = AppLead::where('newagain', 1)->get();

        $leads = $new_leads->merge($new_again);
        event(new GetData($leads));

        return response('Nuevo lead guardado, correctamente.');


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
