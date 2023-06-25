<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function position()
    {
        // mendefinisikan relasi many to one yaitu banyak employee memiliki satu posisi untuk mengakses data employee dan position yang saling terkait
        return $this->belongsTo(Position::class);
    }

}
