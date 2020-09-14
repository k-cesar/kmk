<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Deposit\DepositImage;

$factory->define(DepositImage::class, function (Faker $faker) {

    return [
        'base64_image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
        'deposit_id'   => factory(Deposit::class),
    ];
});
