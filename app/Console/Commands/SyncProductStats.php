<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncProductStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-product-stats';
    protected $description = 'Sync all product stock and sold counts';

    public function handle()
    {
        $this->info('Starting synchronization...');
        
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            foreach ($product->colors as $color) {
                // Update color from sizes
                $color->updateStock();
                $color->updateSold();
                $this->line("Synced Color: {$color->id} for Product: {$product->name}");
            }
            // Update product from colors
            $product->updateStock();
            $product->updateSold();
            $this->info("Synced Product: {$product->name}");
        }

        $this->info('Synchronization completed!');
    }
}
