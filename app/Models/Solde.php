<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solde extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_users',
        'id_devis',
        'solde'
    ];
}
