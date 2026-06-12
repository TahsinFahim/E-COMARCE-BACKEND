<?php

namespace Modules\Store\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'countries';

    protected $fillable = [
        'iso2',
        'name',
    ];

    public $timestamps = false;

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}