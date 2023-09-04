<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBracelets extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_bracelets';

    protected $fillable = ['*'];
}
