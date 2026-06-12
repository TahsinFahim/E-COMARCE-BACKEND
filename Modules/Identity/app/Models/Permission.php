<?php

namespace Modules\Identity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Identity\Models\Role;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps()
            ->withPivot('deleted_at');
    }
}