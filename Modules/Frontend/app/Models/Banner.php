<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'banners';

    protected $fillable = [
        'banner_image',
        'title',
        'subtitle',
        'smtag',
        'primary_btn',
        'primary_btn_url',
        'secondary_btn',
        'secondary_btn_url',
        'sort_order',
        'status',
    ];
}