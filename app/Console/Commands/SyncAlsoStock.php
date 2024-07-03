<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use Lunar\FieldTypes\Text;
use Lunar\Models\ProductVariant;
use ZipArchive;

class SyncAlsoStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-also-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::channel('syncstock')->info('Also: Retrieving current stock');
        $host = config('services.also.host');
        $port = config('services.also.port');
        $username = config('services.also.username');
        $password = config('services.also.password');
        $remote_file_path = '/pricelist-1.csv.zip';

        try {
            $connection = ssh2_connect($host, $port);
            if (! $connection) {
                throw new Exception("Could not connect to $host on port $port");
            }
            $auth = ssh2_auth_password($connection, $username, $password);
            if (! $auth) {
                throw new Exception("Could not authenticate with username $username and password ");
            }
            $sftp = ssh2_sftp($connection);
            if (! $sftp) {
                throw new Exception('Could not initialize SFTP subsystem.');
            }

            $stream = fopen('ssh2.sftp://'.$sftp.$remote_file_path, 'r');
            if (! $stream) {
                throw new Exception('Could not open file: ');
            }

            $contents = stream_get_contents($stream);

            file_put_contents('pricelist.csv.zip', $contents);
            @fclose($stream);
            $connection = null;
        } catch (Exception $e) {
            echo 'Error due to :'.$e->getMessage();
        }

        // Get correct CSV file from zipfile
        $filename = 'also_pricelist.csv';
        $z = new ZipArchive();
        $z->open('pricelist.csv.zip');
        $numFiles = $z->numFiles;
        $index = $numFiles - 1;
        while ($index >= 0) {
            $n = $z->getNameIndex($index);
            $fname = $filename;
            $z->extractTo(storage_path('app/pricelists/'), $n);
            rename(storage_path('app/pricelists/'.$n), storage_path('app/pricelists/'.$fname));
            $index--;
        }
        unlink('pricelist.csv.zip');

        $reader = Reader::createFromPath(storage_path('app/pricelists/'.$filename));
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(';');
        $records = $reader->getRecords();
        $number_of_records = count($reader);
        $i = 1;
        $chunks = 5000;
        $data = [];
        $timestamp = Carbon::now();

        foreach ($records as $record) {
            $product_data['product_id'] = $record['ProductID'];
            $product_data['stock'] = $record['AvailableQuantity'];
            $product_data['price'] = intval((floatval($record['NetPrice']) + floatval($record['VatAmount'])) * 100);
            $product_data['available_next_date'] = $record['AvailableNextDate'] ? Carbon::createFromFormat('Ymd', $record['AvailableNextDate']) : null;
            $product_data['available_next_quantity'] = $record['AvailableNextQuantity'] ? intval($record['AvailableNextQuantity']) : null;
            $product_data['created_at'] = $timestamp;
            $product_data['updated_at'] = $timestamp;

            $data[] = $product_data;

            if ($i % $chunks == 0 || $i == $number_of_records) {
                DB::connection('sqlite')->table('also_stock')->upsert($data,
                    [
                        'product_id',
                    ], [
                        'stock',
                        'price',
                        'available_next_date',
                        'available_next_quantity',
                        'updated_at',
                    ]
                );
                $data = [];
            }
            $i++;
        }

        $variants_to_update = [];

        ProductVariant::chunk(200, function ($product_variants) use (&$variants_to_update) {
            foreach ($product_variants as $variant) {
                if ($variant->translateAttribute('also_product_id')) {

                    $result = DB::connection('sqlite')
                        ->table('also_stock')
                        ->where('product_id', $variant->translateAttribute('also_product_id'))
                        ->first();

                    if ($result) {
                        // Update ALSO attribute data
                        $variant->attribute_data['also_stock'] = new Text($result->stock);
                        $variant->attribute_data['also_price'] = new Text((floatval($result->price) / 100));
                        // Update total stock value
                        $variant->stock = intval(($variant->attribute_data->has('techdata_stock') ? $variant->attribute_data['techdata_stock']->getValue() : '0')) + intval($result->stock);

                        $variants_to_update[] = [
                            'id' => $variant->id,
                            'attribute_data' => $this->prepareAttributeData($variant->attribute_data),
                            'stock' => $variant->stock,
                        ];
                        // Notify updated status
                        print_r('Updated ALSO stock: '.config('app.url').'/'.config('lunar-hub.system.path').'/products/'.$variant->product->id.'/variants/'.$variant->id."\n");
                    }
                }
            }
        });

        \App\Models\ProductVariant::massUpdate(
            values: $variants_to_update,
            uniqueBy: ['id']
        );

        Log::channel('syncstock')->info('Also: Stock retrieved and updated + total stock updated');

    }

    protected function prepareAttributeData($attributeData): string
    {
        $data = [];

        foreach ($attributeData ?? [] as $handle => $item) {
            $data[$handle] = [
                'field_type' => get_class($item),
                'value' => $item->getValue(),
            ];
        }

        return json_encode($data);
    }
}
