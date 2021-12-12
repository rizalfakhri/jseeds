<?php

namespace App\Console\Commands;

use App\Models\Data;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PerformFakeOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        Data::select()->update(['currently_active' => false]);
        $data = Data::where('used', false)->inRandomOrder()->first()->update(['currently_active' => true]);

        Artisan::call('dusk');
    }
}
