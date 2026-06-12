<?php

namespace Modules\Reviews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'webhooks';

    protected $fillable = [
        'name', 'url', 'secret', 'events', 'status', 'retry_count', 'timeout_seconds', 'description',
    ];

    protected $casts = [
        'events' => 'json',
    ];

    public function deliveries() { return $this->hasMany(WebhookDelivery::class, 'webhook_id'); }
}