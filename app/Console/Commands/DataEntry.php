<?php

namespace App\Console\Commands;

use App\Models\Data;
use Illuminate\Console\Command;

class DataEntry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'de';

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
        Data::create([
            'first_name' => $this->ask('First Name'),
            'last_name' => $this->ask('Last Name'),
            'email' => $this->ask('Email'),
            'phone_number' => $this->ask('Phone Number'),
            'address' => $this->ask('Address'),
            'province' => $this->ask('Province'),
            'city' => $this->ask('City'),
            'district' => $this->ask('District'),
        ]);

        $this->info("Saved!");
    }
}
