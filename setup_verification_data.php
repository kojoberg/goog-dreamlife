$user = \App\Models\User::where('email', 'pharmacist2@example.com')->first();
if ($user) {
$user->role = 'admin';
$user->save();
echo "User promoted to Admin.\n";
} else {
echo "User not found.\n";
}

$branch = \App\Models\Branch::first();
if (!$branch) {
$branch = \App\Models\Branch::create(['name' => 'Main Branch', 'is_main' => true]);
}
if ($user) {
$user->branch_id = $branch->id;
$user->save();
}

$product = \App\Models\Product::first();
if (!$product) {
$cat = \App\Models\Category::firstOrCreate(['name' => 'General']);
$product = \App\Models\Product::create([
'name' => 'Paracetamol',
'category_id' => $cat->id,
'unit_price' => 10.00,
'stock' => 100, // Accessor might override this if relying on batches, but let's try.
'product_type' => 'goods'
]);
// Create batch just in case
\App\Models\InventoryBatch::create([
'product_id' => $product->id,
'batch_number' => 'BATCH-TEST',
'quantity' => 100,
'expiry_date' => now()->addYear(),
'cost_price' => 5.00
]);
echo "Product Created.\n";
} else {
echo "Product Exists.\n";
}