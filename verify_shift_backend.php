<?php

use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;

$user = User::first(); // Admin
echo "Using User: {$user->name} (ID {$user->id})\n";

// 1. Check for existing open shift and close it to start fresh
$existingShift = Shift::where('user_id', $user->id)->whereNull('end_time')->first();
if ($existingShift) {
    echo "   Found existing open shift (ID {$existingShift->id}). Closing it.\n";
    $existingShift->update(['end_time' => Carbon::now()]);
}

// 2. Start Shift
echo "1. Starting Shift...\n";
$shift = Shift::create([
    'user_id' => $user->id,
    'branch_id' => $user->branch_id ?? 1,
    'start_time' => Carbon::now(),
    'starting_cash' => 100.00
]);
echo "   Shift Started: ID {$shift->id} at {$shift->start_time}\n";

// 3. End Shift
echo "2. Ending Shift...\n";
sleep(1); // Ensure time difference
$shift->update([
    'end_time' => Carbon::now(),
    'actual_cash' => 150.00 // Simulate 50 sales
]);
echo "   Shift Ended: ID {$shift->id} at {$shift->end_time}\n";

// 4. Verify
$verifyShift = Shift::find($shift->id);
if ($verifyShift && $verifyShift->end_time) {
    echo "SUCCESS: Shift Lifecycle Verified.\n";
} else {
    echo "FAILURE: Shift did not close.\n";
}
