<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TourClient;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $fillable = [
        'tour_date',
        'booked',
        'attended',
    ];
    public function clients(): HasMany
    {
        return $this->hasMany(TourClient::class);
    }
}
