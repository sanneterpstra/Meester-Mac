<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-new';

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
        $this->syncStock();
        $this->call('lunar:search:index');
        Log::channel('syncstock')->info('Search reindexed');
    }

    protected function syncStock()
    {
        $this->call('app:sync-tdsynnex-stock');
        $this->call('app:sync-also-stock');
    }
}
