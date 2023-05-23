<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    /* Un lead pertenece a un usuario */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /* Un lead tiene muchos leads */
    public function notes(){
        return $this->hasMany(Note::class);
    }

    public function cardpayment(){
        return $this->hasOne(CardPayment::class);
    }

    public function enrolled(){
        return $this->hasOne(Enrolled::class);
    }

}
