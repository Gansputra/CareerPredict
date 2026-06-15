<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Total Jobs: " . App\Models\JobListing::count() . "\n";
echo "Total Categories: " . App\Models\JobCategory::count() . "\n";
foreach (App\Models\JobCategory::withCount('jobs')->get() as $c) {
    echo "- {$c->name} (ID: {$c->id}): {$c->jobs_count} jobs\n";
}
