<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory;
use Faker\Provider\id_ID\Person;
use Faker\Provider\id_ID\PhoneNumber;

class GenerateFakeAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:fake-addresses';

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
        $faker = new \Faker\Generator;;
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new PhoneNumber($faker));

        for($i = 0; $i <= 1000; $i++) {

            $this->info($i+1 . '. ' . $faker->firstName( ) . ' ' . $faker->lastName() . ' - ' . $faker->phoneNumber());
        }


    }
}
