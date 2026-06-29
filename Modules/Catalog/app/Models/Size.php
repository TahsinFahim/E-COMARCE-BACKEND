<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sizes';

    protected $fillable = [
        'group_name',
        'sizes',
        'status',
    ];
}