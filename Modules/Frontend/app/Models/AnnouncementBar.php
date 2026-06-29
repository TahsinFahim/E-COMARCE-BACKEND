<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementBar extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'announcement_bars';

    protected $fillable = [
        'left_text',
        'center_text',
        'right_text',
        'background_color',
        'text_color',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}