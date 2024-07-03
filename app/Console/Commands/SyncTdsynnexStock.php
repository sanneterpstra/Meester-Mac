<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use Lunar\FieldTypes\Text;
use Lunar\Models\ProductVariant;
use ZipArchive;

class SyncTdsynnexStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-tdsynnex-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('syncstock')->info('TDSynnex: Retrieving current stock');

        $stockZip = $this->getStockZip();

        // Get correct CSV file from zipfile
        $z = new ZipArchive();
        $filename = 'tdsynnex_stocklist.csv';
        $remoteFilename = '754653_StockFile_'.Carbon::today()->format('Ymd').'.csv';
        if ($z->open($stockZip) === true) {
            if ($csvFile = $z->getFromName($remoteFilename)) {
                file_put_contents(storage_path('app/pricelists/'.$filename), $csvFile);
            }
        }
        unlink($stockZip);

        $reader = Reader::createFromPath(storage_path('app/pricelists/'.$filename));
        $reader->setDelimiter(';');
        $records = $reader->getRecords();
        $number_of_records = count($reader);
        $i = 1;
        $chunks = 5000;
        $data = [];
        $timestamp = Carbon::now();

        foreach ($records as $record) {
            if (in_array($record[0], $data)) {
                continue;
            }
            $product_data['product_id'] = $record[0];
            $product_data['stock'] = $record[1] ?: 0;
            $product_data['expected_delivery'] = $record[2] ? Carbon::createFromDate($record[2]) : null;
            $product_data['created_at'] = $timestamp;
            $product_data['updated_at'] = $timestamp;

            $data[] = $product_data;

            // this is to performing 'chunking' so that an eloquent model isn't saved for each record
            // this *vastly* speeds up performance
            if ($i % $chunks == 0 || $i == $number_of_records) {
                DB::connection('sqlite')->table('tdsynnex_stock')->upsert(
                    $data,
                    uniqueBy: [
                        'product_id',
                    ],
                    update: [
                        'stock',
                        'expected_delivery',
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
                if ($variant->translateAttribute('techdata_product_id')) {

                    $result = DB::connection('sqlite')
                        ->table('tdsynnex_stock')
                        ->where('product_id', $variant->translateAttribute('techdata_product_id'))
                        ->first();
                    if ($result) {
                        $variant->attribute_data['techdata_stock'] = new Text($result->stock);

                        $variants_to_update[] = [
                            'id' => $variant->id,
                            'attribute_data' => $this->prepareAttributeData($variant->attribute_data),
                        ];

                        print_r('Updated TD Synnex stock: '.config('app.url').'/'.config('lunar-hub.system.path').'/products/'.$variant->product->id.'/variants/'.$variant->id."\n");
                    }
                }
            }
        });

        \App\Models\ProductVariant::massUpdate(
            values: $variants_to_update,
            uniqueBy: ['id']
        );

        Log::channel('syncstock')->info('TDSynnex: Stock retrieved and updated');
    }

    protected function getStockZip(): string
    {
        $host = config('services.tdsynnex.host');
        $port = config('services.tdsynnex.port');
        $username = config('services.tdsynnex.username');
        $password = config('services.tdsynnex.password');
        $remote_file_path = '/754653_C_'.Carbon::today()->format('Ymd').'.zip';
        $file_name = 'tdsynnex_stocklist.zip';

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

            file_put_contents($file_name, $contents);
            @fclose($stream);

            return $file_name;
        } catch (Exception $e) {
            return 'Error due to :'.$e->getMessage();
        }
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
