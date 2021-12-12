<?php

namespace Tests\Browser;

use App\Models\Data;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{

    protected $qty = 0;

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $terms = ['Voltuz'];
            $browser->visit('/')
                    ->typeSlowly('.search-field', $terms[array_rand($terms)])
                    ->pause(rand(900, 2000))
                    ->mouseOver('.search-submit')
                    ->click();

            $productNames = [
                'Voltuz vespa',
                'Heart',
                'hourse',
                'honda Grand',
            ];

            $toClick = $productNames[array_rand($productNames)];

            $browser->pause(rand(1000, 7000));

            if( $browser->seeLink($toClick) ) {

                $browser->clickLink($toClick)->pause(rand(1000, 10000));

                $browser->whenAvailable('.wcboost-variation-swatches', function($browser) {

                    $browser->whenAvailable('.wcboost-variation-swatches__wrapper', function($browser) {

                        $sizes      = ['m', 'l', 'xl'];
                        $existedSelector = null;

                        shuffle($sizes);

                        foreach($sizes as $size) {

                            $selector   = 'li.wcboost-variation-swatches__item-' . $size;


                            try {

                                $browser->whenAvailable($selector, function($browser) use($selector, &$existedSelector) {
                                    $browser->pause(rand(200, 1200));

                                    $sizeButton  = $browser->element($selector);

                                    if( ! is_null($sizeButton) ) {
                                        $existedSelector = $selector;
                                    }

                                });

                                break;

                            } catch(\Exception $e) {
                                continue;
                            }


                        }


                        $sizeButton  = $browser->element($existedSelector);

                        if( $sizeButton ) {

                            $sizeButton->click();


                        }


                    });



                });


                $browser->pause(rand(500, 3000));
                $browser->waitUntilEnabled('.single_add_to_cart_button');
                $browser->press('.single_add_to_cart_button')->pause(rand(1000, 5000));
                $this->qty = $this->qty + 1;

                $browser->pause(rand(1000, 10000));

                if( $browser->seeLink('Voltuz superior') ) {
                    $browser->clickLink('Voltuz superior');
                    $browser->pause(rand(2000, 4000));
                }


                $additionalProducts = [
                    'flannel 1',
                    'flannel 2',
                    'flannel 3',
                    'flannel 4',
                    'flannel 5',
                    'Baseball',
                    'vespa',
                    'Allien',
                    'Simple',
                    'Burger',
                    'Rabbit Army',
                    'Heart',
                    'hourse',
                    'honda Grand',
                ];

                $navigations = [
                    '2',
                    '3'
                ];


                $nextSteps = ['ADD_ANOTHER_ITEM', 'MOVE_TO_NEXT_PAGE'];

                $nextStep  = $nextSteps[array_rand($nextSteps)];


                    $nextStep == 'ADD_ANOTHER_ITEM';
                if( $nextStep == 'MOVE_TO_NEXT_PAGE' ) {
                    $page = $navigations[array_rand($navigations)];

                    if( $browser->seeLink($page) ) {
                        $browser->clickLink($page);
                        $browser->pause(rand(400, 1000));
                    }

                    $nextStep == 'ADD_ANOTHER_ITEM';
                }

                $selectedItem = null;

                if( $nextStep == 'ADD_ANOTHER_ITEM' ) {

                    $proceed      = false;

                    while( ! $proceed ) {
                        $item = $additionalProducts[array_rand($additionalProducts)];

                        if( ! $browser->seeLink($item) ) continue;

                        $proceed = true;
                        $selectedItem = $item;
                    }
                }

                $browser->pause(rand(1000, 5000));

                if( $browser->seeLink($selectedItem) ) {
                    $browser->pause(rand(100, 500));
                    $browser->clickLink($selectedItem);



                    try {

                        $browser->whenAvailable('div.wcboost-variation-swatches', function($browser) {

                            \Log::info('swatches 1 exists');

                            $browser->whenAvailable('ul.wcboost-variation-swatches__wrapper', function($browser) {

                                \Log::info('swatches wrtapper exists');
                                $sizes      = ['l', 'xl'];

                                shuffle($sizes);

                                $randomSize = $sizes[array_rand($sizes)];


                                $url = $browser->driver->getCurrentURL() . '?attribute_pa_size=' . $randomSize;
                                \Log::info('visiting: ' . $url);

                                $browser->visit($url);

                                if( $browser->driver->getCurrentURL() == 'https://jakcloth.co.id' ) {
                                    $browser->back();
                                    $browser->visit($url);
                                }


                            });



                        });

                    } catch(\Exception $e) {
                        if( $browser->driver->getCurrentURL() == 'https://jakcloth.co.id' ) {
                            $browser->back();
                        }
                    }


                    if( $browser->driver->getCurrentURL() == 'https://jakcloth.co.id' ) {
                        $browser->back();
                    }

                    $sizes = ['l', 'xl'];
                    $randomSize = $sizes[array_rand($sizes)];


                    $fallbacks = [
                        'https://jakcloth.co.id/product/flannel-1/?attribute_pa_size=' . $randomSize,
                        'https://jakcloth.co.id/product/flannel-2/?attribute_pa_size=' . $randomSize,
                        'https://jakcloth.co.id/product/flannel-3/?attribute_pa_size=' . $randomSize,
                        'https://jakcloth.co.id/product/flannel-4/?attribute_pa_size=' . $randomSize,
                        'https://jakcloth.co.id/product/flannel-5/?attribute_pa_size=' . $randomSize,
                    ];

                    try {

                        $browser->pause(rand(500, 3000));
                        $browser->press('.single_add_to_cart_button')->pause(rand(1000, 5000));
                        $this->qty = $this->qty + 1;

                        $browser->pause(rand(1000, 10000));
                    } catch(\Exception $e) {
                        $url = $fallbacks[array_rand($fallbacks)];
                        $browser->visit($url);
                        $browser->press('.single_add_to_cart_button')->pause(rand(1000, 5000));
                        $this->qty = $this->qty + 1;
                    }


                    if( $this->qty >= 2 ) {
                        $browser->click('.cart-contents');
                    }



                }


            }


            $browser->waitFor('#coupon_code');
            $browser->typeSlowly('coupon_code', 'voltuzdiskon100rb');
            $browser->mouseover('[name="apply_coupon"]');
            $browser->press('[name="apply_coupon"]');


            $browser->pause(rand(10000, 20000));


            $browser->press('.checkout-button');

            $browser->pause(rand(300, 2000));

            //////////

            $data = Data::where('currently_active', true)->first();

            $browser->typeSlowly('billing_first_name', $data->first_name);
            $browser->typeSlowly('billing_last_name', $data->last_name);
            $browser->typeSlowly('billing_address_1', $data->address . ', ' . $data->state . ', ' . $data->city . ', ' . $data->district);
            $browser->typeSlowly('billing_phone', $data->phone_number);
            $browser->typeSlowly('billing_email', $data->email);

            $data->update(['used' => true]);


            dd('done');
        });


    }
}
