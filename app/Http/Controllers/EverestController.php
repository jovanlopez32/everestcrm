<?php

namespace App\Http\Controllers;

use App\Models\CardPayment;
use App\Models\Enrolled;
use App\Models\Lead;
use App\Models\Note;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EverestController extends Controller
{
    //

    public function leadsHome(){
        $new_leads = Auth::user()->leads()->where('status', 'nuevo')->where('user_id', Auth::user()->id)->get();
        $new_again = Auth::user()->leads()->where('newagain', 1)->where('user_id', Auth::user()->id)->get();

        $my_new_leads = $new_leads->merge($new_again);

        return Inertia::render('Everest/Leads/LeadsHome', ['my_new_leads' => $my_new_leads]);
    }

    public function getNotes(Request $request){

        $lead = Lead::find($request->idlead);
        $notesLead = $lead->notes;
        return $notesLead;
    }

    public function leadsPanel(Request $filter){

        $all_leads = Auth::user()->leads()->where('status', '!=', 'nuevo')->latest()->with('notes')->paginate(30);

        if($filter->session()->get('leads_filter') != null){
            $all_leads = $filter->session()->get('leads_filter');
            $filter->session()->put('leads_filter', null);
        }else{
            $all_leads = Auth::user()->leads()->where('status', '!=', 'nuevo')->latest()->with('notes')->paginate(60);
        }

        return Inertia::render('Everest/Leads/LeadsPanel', ['all_leads' => $all_leads]);
    }

    public function searchLead(Request $filter){

        $nm_filter = $filter->name == null ? '%' : '%' . $filter->name . '%';
        $pn_filter = $filter->phone == null ? '%' : '%' . $filter->phone . '%';
        $cd_filter = $filter->college_degree == 'Todas las carreras' ? '%' : $filter->college_degree;
        $st_filter = $filter->status == 'Todos los estados' ? '%' : $filter->status;
        $fr_filter = $filter->from == 'Todos los canales' ? '%' : $filter->from;

        //add
        $df_filter = $filter->date_from == null ? null : $filter->date_from; 
        //$dt_filter = $filter->date_to == null ? null : $filter->date_to; 
        $dt_filter = $filter->date_to == null ? null : date('Y-m-d', strtotime('+1 day', strtotime($filter->date_to))); 
        //end add

        if($df_filter != null && $dt_filter != null)
        {
            $all_leads = Auth::user()->leads()->whereBetween('created_at', [$df_filter, $dt_filter])->where('college_degree', 'like', $cd_filter)->where('status', 'like', $st_filter)->where('from', 'like', $fr_filter)->where('name', 'like', $nm_filter)->where('phone', 'like', $pn_filter)->latest()->with('notes')->paginate(10);
        }
        else
            if($df_filter != null && $dt_filter == null)
            {
                $all_leads = Auth::user()->leads()->where('created_at', 'LIKE', '%'.$df_filter.'%')->where('college_degree', 'like', $cd_filter)->where('status', 'like', $st_filter)->where('from', 'like', $fr_filter)->where('name', 'like', $nm_filter)->where('phone', 'like', $pn_filter)->latest()->with('notes')->paginate(10);
                
            }else
                if($df_filter == null && $dt_filter == null)
            {
                $all_leads = Auth::user()->leads()->where('college_degree', 'like', $cd_filter)->where('status', 'like', $st_filter)->where('from', 'like', $fr_filter)->where('name', 'like', $nm_filter)->where('phone', 'like', $pn_filter)->latest()->with('notes')->paginate(10);
            }

        $filter->session()->put('leads_filter', $all_leads);

        return to_route('leads.panel');
    }


    public function storeLead(Request $request) {

        $checklead = Lead::where('phone', $request->phone)->exists();

        if($checklead == true) {

            $its_me = Lead::where('user_id', $request->userid)->exists();

            if($its_me == true){
                $lead = Lead::where('phone', $request->phone)->first();
                $lead->count++;
                $lead->newagain = true;
                $lead->save();
                return to_route('leads.home');
            } else {
                return 'Este lead ya existe';
            }
        }

        $new_lead = new Lead();

        $new_lead->name = $request->name;
        $new_lead->phone = $request->phone;
        $new_lead->email = $request->email;
        $new_lead->college_degree = $request->college_degree;
        $new_lead->from = "Ingresado Manualmente";

        $user = User::find($request->userid);
        $user->leads()->save($new_lead);

        $new_lead->save();

        return to_route('leads.home');
    }

    public function storeNote(Request $request){

        $new_note = new Note();

        $new_note->text = $request->notetext;

        $new_note->save();

        $lead = Lead::find($request->idlead);

        $lead->status = $request->status;

        $lead->newagain = false;

        $diff = $lead->created_at->diff(Carbon::now());

        $lead->accepted_time = strval($diff->format('%d, %i, %s'));

        $lead->save();

        $lead->notes()->save($new_note);

        return to_route('leads.home');
    }


    public function storeFollowUpLead(Request $request){

        $lead = Lead::find($request->id_lead_follow);

        $flag = false;
        if($lead->name != $request->name_follow){
            $lead->name = $request->name_follow;
            $flag = true;
        }
        if($lead->college_degree != $request->college_degree_follow){
            $lead->college_degree = $request->college_degree_follow;
            $flag = true;
        }

        if($lead->phone != $request->phone_follow){
            $lead->phone = $request->phone_follow;
            $flag = true;
        }

        if($lead->email != $request->email_follow){
            $lead->email  = $request->email_follow;
            $flag = true;
        }

        if($lead->status != $request->status_follow){
            $lead->status  = $request->status_follow;
            $flag = true;
        }

        if($request->new_note != null){
            $new_note = new Note();
            $new_note->text = $request->new_note;
            $new_note->save();

            $lead->notes()->save($new_note);
        }

        if($flag == true){
            $lead->save();
        }

        return to_route('leads.panel');
    }

    public function createCardPayment(Request $request){

        $lead = Lead::find($request->id_lead);

        return Inertia::render('Everest/CardPayment/CardPayment', ['lead' => $lead]);
    }

    public function deleteNote(Request $request){
        $note = Note::find($request->idnote);
        $lead = $note->lead;

        $note->delete();

        $notesLead = $lead->notes;
        return $notesLead;
    }

    public function storeCardPayment(Request $request){

        $lead = Lead::find($request->id_lead);

        if($lead->has_card == false){

            $lead->has_card = true;
            $lead->save();

            $card_payment = new CardPayment();

            $card_payment->fullname = $request->fullname;
            $card_payment->start_month = $request->month;
            $card_payment->boot_cycle = $request->boot_cycle;
            $card_payment->promotion = $request->promotion;
            $card_payment->limit_date = $request->date;
            $card_payment->note_one = $request->note_one;
            $card_payment->note_two = $request->note_two;
            $card_payment->total = $request->total;

            $card_payment->lead()->associate($lead);

            $card_payment->save();

        }else{
            $card_payment = $lead->cardpayment;

            $card_payment->fullname = $request->fullname;
            $card_payment->start_month = $request->month;
            $card_payment->boot_cycle = $request->boot_cycle;
            $card_payment->promotion = $request->promotion;
            $card_payment->limit_date = $request->date;
            $card_payment->note_one = $request->note_one;
            $card_payment->note_two = $request->note_two;
            $card_payment->total = $request->total;

            $card_payment->save();
        }

        return to_route('leads.panel');
    }

    public function enrolledLead(Request $request) {

        $lead = Lead::find($request->idlead);
        $lead->enrolled = true;
        $lead->status = 'Inscrito';
        $lead->save();

        $card_payment = $lead->cardpayment;

        $enrolled = new Enrolled();

        $enrolled->name = $card_payment->fullname;
        $enrolled->boot_cycle = $card_payment->boot_cycle;
        $enrolled->promotion =  $card_payment->promotion;
        $enrolled->start_month = $card_payment->start_month;
        $enrolled->total = $card_payment->total;

        $enrolled->lead()->associate($lead);

        $enrolled->save();


        return $lead;
    }

    public function deleteLead(Request $request)
    {
        //$lead = Lead::find($request->id);

        //
        
        $id = $request->input('id');
        $phone = $request->input('phone');

        $lead = Lead::where('id', $id)->where('phone', $phone)->first();
        
        /* 
        if($lead)
        {
            
        }
        */

        //return $lead;
       
        $lead->delete();

        return to_route('manager.settings');
    }

}
