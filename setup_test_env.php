// Enable Cashier Mode on Main Branch
$branch = \App\Models\Branch::first();
$branch->has_cashier = true;
$branch->save();

// Create Pharmacist
$pharmacist = \App\Models\User::firstOrCreate(
['email' => 'pharmacist@example.com'],
[
'name' => 'Test Pharmacist',
'password' => bcrypt('password'),
'role' => 'pharmacist',
'branch_id' => $branch->id
]
);

// Create Cashier
$cashier = \App\Models\User::firstOrCreate(
['email' => 'cashier@example.com'],
[
'name' => 'Test Cashier',
'password' => bcrypt('password'),
'role' => 'cashier',
'branch_id' => $branch->id
]
);

echo "Setup Complete: Branch Mode Enabled. Users Ready.\n";