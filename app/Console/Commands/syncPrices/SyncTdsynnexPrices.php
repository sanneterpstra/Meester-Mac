<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use League\Csv\Reader;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\ProductVariant;
use ZipArchive;

class SyncTdsynnexPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-tdsynnex-prices';

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
        $host = env('TDSYNNEX_FTP_HOST');
        $port = env('TDSYNNEX_FTP_PORT');
        $username = env('TDSYNNEX_FTP_USERNAME');
        $password = env('TDSYNNEX_FTP_PASSWORD');
        $connection = null;
        $remote_file_path = '/754653_A_'.Carbon::today()->format('Ymd').'.zip';

        try {
            $connection = ssh2_connect($host, $port);
            if (! $connection) {
                throw new \Exception("Could not connect to $host on port $port");
            }
            $auth = ssh2_auth_password($connection, $username, $password);
            if (! $auth) {
                throw new \Exception("Could not authenticate with username $username and password ");
            }
            $sftp = ssh2_sftp($connection);
            if (! $sftp) {
                throw new \Exception('Could not initialize SFTP subsystem.');
            }

            $stream = fopen('ssh2.sftp://'.$sftp.$remote_file_path, 'r');
            if (! $stream) {
                throw new \Exception('Could not open file: ');
            }

            $contents = stream_get_contents($stream);

            file_put_contents('tdsynnex_pricelist.zip', $contents);
            @fclose($stream);
            $connection = null;
        } catch (Exception $e) {
            echo 'Error due to :'.$e->getMessage();
        }

        // Get correct CSV file from zipfile
        $z = new ZipArchive;
        $filename = '754653_MaterialFile_'.Carbon::today()->format('Ymd').'.csv';
        if ($z->open('tdsynnex_pricelist.zip') === true) {
            if ($csvFile = $z->getFromName($filename)) {
                file_put_contents('./tdsynnex_pricelist.csv', $csvFile);
            }
        }
        unlink('tdsynnex_pricelist.zip');

        $reader = Reader::createFromPath('tdsynnex_pricelist.csv', 'r');
        $reader->setDelimiter(';');
        $records = $reader->getRecords();

        ProductVariant::where('product_id', '=', 89)->chunk(200, function ($productvariants) use ($records) {
            foreach ($productvariants as $variant) {
                if ($variant->translateAttribute('techdata_product_id')) {

                    foreach ($records as $index => $row) {
                        if ($row[0] == $variant->translateAttribute('techdata_product_id')) {
                            $pricing['EUR'] = [
                                'id' => $variant->getPrices()->first()->id,
                                'price' => (0.05 * round((str_replace(',', '.', $row[20]) * 1.21) * 20)),
                                'currency_id' => 1,
                                'tier' => 1,
                                'compare_price' => 0,
                            ];
                            app(UpdatePrices::class)->execute($variant, collect($pricing));
                            print_r('Updated: '.$variant->sku."\n");

                            continue;
                        }
                    }
                }
            }
        });

        $this->call('lunar:search:index');
    }
}
