<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'name',
        'amount',
        'price',
        'description',
        'image'
    ];

    public function scopeNAME($query, $value)
    {
        $query->orWhere('name', 'like', '%' . $value . '%');
    }

    public function scopeCODE($query, $value)
    {
        $query->orWhere('code', 'like', '%' . $value . '%');
    }
}
