<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath The path to the CSV file
     * @return void
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */

    public function handle()
    {
        DB::statement("
            COPY products(name, description, product_type_id, created_at, updated_at, \"validFrom\", \"validTo\", status)
            FROM '{$this->filePath}'
            DELIMITER ','
            CSV HEADER;
        ");
    }


    public function failed(Throwable $exception)
    {
        throw new \Exception('Job failed: ' . $exception->getMessage());
    }
}
