<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\EverestController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/* Everest Controller */
Route::middleware('auth')->group(function (){
    Route::get('/nuevosleads', [EverestController::class, 'leadsHome'])->name('leads.home');
    Route::get('/panelleads', [EverestController::class, 'leadsPanel'])->name('leads.panel');
    Route::get('/crearFicha', [EverestController::class, 'createCardPayment'])->name('leads.card_payment');




    /* Axios */
    Route::post('/getnotes', [EverestController::class, 'getNotes'])->name('leads.get.notes');
    Route::post('/deletenote', [EverestController::class, 'deleteNote'])->name('lead.delete.note');
    Route::post('enrolledlead', [EverestController::class, 'enrolledLead'])->name('lead.enrolled');

    /*  */
    Route::post('/searchlead', [EverestController::class, 'searchLead'])->name('leads.search');
    Route::post('/createlead', [EverestController::class, 'storeLead'])->name('leads.create');
    Route::post('/createnote', [EverestController::class, 'storeNote'])->name('notes.create');
    Route::post('/addfollowup', [EverestController::class, 'storeFollowUpLead'])->name('leads.follow_up');
    Route::post('/storecardpayment', [EverestController::class, 'storeCardPayment'])->name('card.store');

    Route::post('/deletelead', [EverestController::class, 'deleteLead'])->name('lead.destroy');
});


/* Manager Controller */
Route::middleware('auth', 'admin')->group(function () {
    Route::get('/usuarios', [ManagerController::class, 'viewUsers'])->name('users.view');
    Route::post('/storeuser', [ManagerController::class, 'storeUser'])->name('users.create');
    Route::put('/edituser/{id}', [ManagerController::class, 'editUser'])->name('users.edit');


    Route::get('/configuracion', [ManagerController::class, 'settingsData'])->name('manager.settings');
    Route::post('/storestatus', [ManagerController::class, 'storeStatus'])->name('manager.store.status');


    Route::get('/listadodeleads', [ManagerController::class, 'listLeads'])->name('manager.listleads');
    Route::post('/searchlistleads', [ManagerController::class, 'searchlistLeads'])->name('manager.search.listleads');


    Route::get('/agregarleads', [ManagerController::class, 'addLeads'])->name('manager.add.leads');
    Route::post('/guardarleads', [ManagerController::class, 'storeLeadsM'])->name('manager.store.leads');
   /*  Route::get('todoslosleads', [ManagerController::class, 'allLeads'])->name('manager.allleads');
    Route::get('configuracion', [ManagerController::class], 'settingsData')->name('manager.settings'); */
});



Route::middleware('web')->group(function (){
    Route::post('senddata', [GuestController::class, 'sendData'])->name('send.data');
    Route::get('neuuniuniversidad', [GuestController::class, 'neuuniUniverisdad'])->name('guest.neuuniuniverisdad');


    Route::get('testjson', [GuestController::class, 'testJSON'])->name('guest.json');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});







require __DIR__.'/auth.php';
