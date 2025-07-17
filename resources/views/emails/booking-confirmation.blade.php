<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    
    <p>Dear {{ $booking->guest->name }},</p>
    
    <p>Thanks for booking your stay from {{ $booking->check_in_date->format('M d, Y') }} to {{ $booking->check_out_date->format('M d, Y') }}!</p>
    
    <h2>Booking Details:</h2>
    <ul>
        <li><strong>Property:</strong> {{ $booking->property->name }}</li>
        <li><strong>Address:</strong> {{ $booking->property->address }}</li>
        <li><strong>Check-in:</strong> {{ $booking->check_in_date->format('M d, Y') }}</li>
        <li><strong>Check-out:</strong> {{ $booking->check_out_date->format('M d, Y') }}</li>
        <li><strong>Total Price:</strong> ${{ number_format($booking->total_price, 2) }}</li>
    </ul>
    
    @if($booking->extras->count() > 0)
        <h3>Extras:</h3>
        <ul>
            @foreach($booking->extras as $extra)
                <li>{{ $extra->name }} ({{ $extra->pivot->quantity }}x) - ${{ number_format($extra->pivot->price_at_booking * $extra->pivot->quantity, 2) }}</li>
            @endforeach
        </ul>
    @endif
    
    <p>We look forward to hosting you!</p>
</body>
</html>