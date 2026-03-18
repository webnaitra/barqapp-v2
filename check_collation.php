<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function checkCollation($tableName) {
    try {
        $columns = Illuminate\Support\Facades\DB::select("SHOW FULL COLUMNS FROM $tableName");
        echo "Table: $tableName\n";
        foreach ($columns as $column) {
            echo "  Column: {$column->Field}, Collation: " . ($column->Collation ?? 'N/A') . "\n";
        }
    } catch (\Exception $e) {
        echo "Error checking $tableName: " . $e->getMessage() . "\n";
    }
}

checkCollation('affiliates');
checkCollation('countries');
checkCollation('categories');
checkCollation('affiliate_categories');
checkCollation('product_categories');
checkCollation('affiliate_product_category');
