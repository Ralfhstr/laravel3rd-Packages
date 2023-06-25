<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    public function employees()
    {
        // mendefinisikan relasi one to many yaitu satu posisi dimiliki banyak employee untuk mengakses data position dan employee yang saling terkait
        return $this->hasMany(Employee::class);
    }


}
