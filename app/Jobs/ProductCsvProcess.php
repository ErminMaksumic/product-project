<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProductCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $header;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $header)
    {
        $this->data   = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->data as $product) {

            $productData = array_combine($this->header, $product);

            $productId = $productData['id'];

            unset($productData['id']);

            Product::updateOrCreate(['id' => $productId], $productData);
        }
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
