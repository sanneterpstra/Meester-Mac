<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\ProductVariant;
use ZipArchive;

class SyncAlsoPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-also-prices';

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

        $host = env('ALSO_FTP_HOST');
        $port = env('ALSO_FTP_PORT');
        $username = env('ALSO_FTP_USERNAME');
        $password = env('ALSO_FTP_PASSWORD');
        $connection = null;
        $remote_file_path = '/pricelist-1.csv.zip';

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

            file_put_contents('pricelist.csv.zip', $contents);
            @fclose($stream);
            $connection = null;
        } catch (Exception $e) {
            echo 'Error due to :'.$e->getMessage();
        }

        $z = new ZipArchive;
        $z->open('pricelist.csv.zip');
        $numFiles = $z->numFiles;
        $index = $numFiles - 1;
        while ($index >= 0) {
            $n = $z->getNameIndex($index);
            $fname = 'also_pricelist.csv';
            $z->extractTo('.', $n);
            rename($n, $fname);
            $index--;
        }
        unlink('pricelist.csv.zip');

        $csv = new \ParseCsv\Csv();
        $csv->auto('also_pricelist.csv');
        $items = $csv->data;

        ProductVariant::chunk(200, function ($productvariants) use ($items) {
            foreach ($productvariants as $variant) {
                if ($variant->translateAttribute('also_product_id')) {
                    $key = array_search($variant->translateAttribute('also_product_id'), array_column($items, 'ProductID'));
                    if ($key) {
                        $item = $items[$key];
                        // Sync price
                        $pricing['EUR'] = [
                            'id' => $variant->getPrices()->first()->id,
                            'price' => (0.05 * round(($item['NetRetailPrice'] * 1.21) * 20)),
                            'currency_id' => 1,
                            'tier' => 1,
                            'compare_price' => 0,
                        ];
                        app(UpdatePrices::class)->execute($variant, collect($pricing));

                        print_r('Updated: '.$variant->sku." \n");
                    }
                }
            }
        });

        $this->call('lunar:search:index');
    }
}
