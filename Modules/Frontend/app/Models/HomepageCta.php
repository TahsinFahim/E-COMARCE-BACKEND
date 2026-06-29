<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageCta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'homepage_ctas';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_link',
        'background_color',
        'text_color',
        'button_color',
        'button_text_color',
        'sort_order',
        'status',
    ];
}