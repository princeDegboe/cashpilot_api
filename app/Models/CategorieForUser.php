<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieForUser extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_categorie',
        'id_user',
        'solde',
        'type_id'
    ];
}
