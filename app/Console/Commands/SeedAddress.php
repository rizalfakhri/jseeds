<?php

namespace App\Console\Commands;

use App\Models\Data;
use Illuminate\Console\Command;

class SeedAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed-address';

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
        Data::select()->update(['used' => 3]);

        $addresses = collect(json_decode(file_get_contents(base_path('addresses.json')), true));

        $addresses->map(function($address) {
            unset(
                $address['id'],
                $address['created_at'],
                $address['updated_at'],
            );

            return $address;
        })
        ->each(function($address) {

            Data::create($address);

        });

        $this->info($addresses->count() . ' data seeded.');
    }
}
