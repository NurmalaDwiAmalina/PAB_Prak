<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TourClient extends Model
{
    protected $fillable = [
        'tour_id',
        'client_name',
        'is_booked',
        'is_attended',
        'email',
        'channel',
    ];
    protected static function booted()
    {
        static::creating(function ($tour_client) {
            if (empty($tour_client->unique_id)) {
                $tour_client->unique_id = (string) Str::uuid();
            }
        });
    }
    public function getOrderIdAttribute()
    {
        $order_date = new Carbon($this->created_at);
        return $order_date->year
            . str_pad($order_date->month, 2, "0", STR_PAD_LEFT)
            . str_pad($order_date->day, 2, "0", STR_PAD_LEFT)
            . str_pad($this->id, 5, "0", STR_PAD_LEFT);
    }
}