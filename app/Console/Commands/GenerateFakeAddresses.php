<?php

namespace App\Console\Commands;

use App\Models\Data;
use App\Models\IgnoredResult;
use Illuminate\Console\Command;
use Faker\Factory;
use Faker\Provider\id_ID\Person;
use Faker\Provider\id_ID\PhoneNumber;
use Faker\Provider\id_ID\Address;
use Faker\Provider\Internet;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

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


    protected $resultToIgnore = [];

    protected $totalGeneratedAddress = 0;

    protected $translations  = [
        'west' => 'barat',
        'east' => 'timur',
        'south' => 'selatan',
        'north' => 'utara',
        'java' => 'Jawa'
    ];

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
        $faker->addProvider(new Address($faker));
        $faker->addProvider(new Internet($faker));

        $client = new Client([
            'base_uri' => 'https://api.foursquare.com/v3/',
            'headers' => [
                'Authorization' => 'fsq3ZZuJx9XvLMC1PwTj8JWQXvjh34FNunoFiY1nxQ04aYM=',
                'Accept' => 'application/json',
                'Accept-Language' => 'id'
            ]
        ]);

        $near = [
            'semarang',
            'solo',
            'temanggung',
            'purwokerto',
            'wonosobo',
            'purwakarta'
        ];

        $categoryId = 12094;

        $selectedArea = $near[array_rand($near)];

        $res = $client->get('places/search?categories=' . $categoryId . '&locale=id&limit=40&ll=-6.1721539,106.7995288');

        $results = json_decode($res->getBody()->getContents(), true)['results'];

        $heads = [
            '0812',
            '0813',
            '0856',
            '0857',
            '0838',
            '0878',
            '0877',
            '0895',
            '0898',
            '0899',
            '0881',
            '0886',
            '0832'
        ];

        shuffle($results);


        foreach($results as $result) {

            $fsqId = Arr::get($result, 'fsq_id');

            if( IgnoredResult::where('sq_id', $fsqId)->first() ) {
                $this->comment("SkippinG: " . $fsqId);
                continue;
            }

            $randomHead = $heads[array_rand($heads)];

            $phoneNumber = sprintf("%s%s", $randomHead, substr(str_shuffle("0123456789"), 0, rand(7, 8)));

            $location = Arr::get($result, 'location');

            $name = sprintf('%s %s',
                (rand(1, 2) == 1) ? strtolower($faker->firstName) : $faker->firstName,
                (rand(1, 2) == 1) ? strtolower($faker->lastName) : $faker->lastName,
            );

            [$f, $l] = explode(" ", $name);

            $emailAddress = sprintf("%s@%s",
                substr($f, 1, strlen($f)) . substr($l, 1, strlen($f)) . ((rand(1, 2) == 1) ? str_pad(rand(01, 31), 2, 0, STR_PAD_LEFT) : ''),
                $faker->freeEmailDomain
            );

            $location = Arr::get($result, 'location');
            $address  = Arr::get($location, 'address');
            $province  = Arr::get($location, 'region');
            $city  = Arr::get($location, 'locality');

            $cross = Arr::get($location, 'cross_street');

            if( ! empty($cross) ) {
                $address = sprintf("%s, %s", $address, $cross);
            }

            if( is_null($address) ) continue;
            $this->info("Adding: " . $f . ' ' . $l . ' ' . $phoneNumber . ' ' . $emailAddress . ' ' . $address) ;

            Data::create([
                'first_name' => $f,
                'last_name' => $l,
                'phone_number' => $phoneNumber,
                'email' => $emailAddress,
                'address' => $address,
                'province' => $this->translate($province),
                'city' => $this->translate($city),
                'district' => '.'
            ]);

            $this->totalGeneratedAddress++;


            IgnoredResult::create(['sq_id' => $fsqId]);



        }

        if( $this->totalGeneratedAddress < 300 ) {
            sleep(3);
            return $this->handle();
        }

    }

    protected function translate($string) {
        $lowered = strtolower($string);

        $parts = explode(" ", $lowered);

        $final = '';
        $toShift = '';
        $undoShift = false;

        if( count($parts) > 1 ) {
            foreach($parts as $i => $part) {

                if( isset($this->translations[$part]) && $i == 0 ) {
                    $toShift = ucwords($this->translations[$part]);
                    unset($parts[$i]);
                }
                else
                {
                    if( isset($this->translations[$part]) ) {
                        $parts[$i] = ucwords($this->translations[$part]);
                        $undoShift = true;
                    }
                }
            }
        }

        if( ! $undoShift ) {
            $final = sprintf('%s %s', $toShift, collect($parts)->implode(' '));
        }
        else
        {
            $final = sprintf('%s %s', collect($parts)->implode(' '), $toShift,);
        }


        return ucwords( $final);
    }
}
