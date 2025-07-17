<?php
// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
        'total_price',
        'status'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function extras()
    {
        return $this->belongsToMany(Extra::class, 'booking_extras')
            ->withPivot('quantity', 'price_at_booking')
            ->withTimestamps();
    }

    public function calculateTotalPrice()
    {
        $nights = Carbon::parse($this->check_in_date)->diffInDays($this->check_out_date);
        $basePrice = $this->property->price_per_night * $nights;
        
        $extrasPrice = $this->extras->sum(function ($extra) {
            return $extra->pivot->price_at_booking * $extra->pivot->quantity;
        });

        return $basePrice + $extrasPrice;
    }

    public function getNightsAttribute()
    {
        return Carbon::parse($this->check_in_date)->diffInDays($this->check_out_date);
    }
}