<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    //
       protected $fillable = [
        'username',
        'phone',
     
    ];


      public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

}
