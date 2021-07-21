# Laravel Popular (Laravel Popularity)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jordanmiguel/laravel-popular.svg?style=flat-square)](https://packagist.org/packages/jordanmiguel/laravel-popular)
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](license.md)

With Laravel Popular Package you can Track your most popular Eloquent Models based on unique hits in a time range and then sort by popularity in a time frame.

## Usage

Use the visitable trait on the model you intend to track
``` php
use \JordanMiguel\LaravelPopular\Traits\Visitable;

class Post extends Model
{
    use Visitable;

    ...
}
```

Here are some code examples:

``` php
// Adding a visit to the post. Recommended on the show() method of your controller.
$post->visit();

// Retrieving the count of visitors in a timeframe
$post->visitsDay();
$post->visitsWeek();
$post->visitsMonth();
$post->visitsBetween($from, $to);
$post->visitsForever();

// Ordering the posts by the most visited
Posts::popularLast(3)->get(); // Get popular posts on the last 3 days

Posts::popularDay()->get(); // Get posts ordered by the most visited on the last 24h
Posts::popularWeek()->get();
Posts::popularMonth()->get();
Posts::popularYear()->get();
Posts::popularBetween($from, $to)->get(); // Get posts ordered by the most visited in a given interval date
Posts::popularAllTime()->get();
```

## Install

Via Composer

``` bash
$ composer require jordanmiguel/laravel-popular
```

If you're on Laravel <= 5.4 add `'JordanMiguel\LaravelPopular\LaravelPopularServiceProvider::class',` in your `config/app.php` to the end of the `$providers` array

``` php
'providers' => array(

    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'JordanMiguel\LaravelPopular\LaravelPopularServiceProvider::class',

),
```

Now, let's create our table on the database:

``` bash
$ php artisan migrate
```

We're ready!

## Testing

There is no test setup yet, please pull request if you do it =)

## Contributing

Feel free to Pull Request anytime!

## Author
- [Jordan Miguel](https://www.linkedin.com/in/joordanmiguel/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
