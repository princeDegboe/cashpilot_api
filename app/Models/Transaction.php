<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'description',
        'view',
        'amount',
        'transactionDate',
        'type_id',
        'categorie_id',
        'user_id'
    ];
}
