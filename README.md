# JSONApi strategy

The [league/route](https://github.com/thephpleague/route) routing package which allows custom strategies. This package 
 provides a custom strategy for compatibility with the [JSONAPI](http://jsonapi.org/) standard.

## Installation

This package can be used with league/route version 3 or higher. In previous versions the custom strategies aren't enabled.

You can install the package via composer:

`composer require inthere/jsonapistrategy`

## Usage

Set the strategy for the route collection:

```php
use InThere\Route\JsonApi\JsonApiStrategy;

$route = new League\Route\RouteCollection();
 
$route->setStrategy(new JsonApiStrategy());
```

Or use the strategy for individual routes:

```php
$route = new League\Route\RouteCollection;
 
$route->get('/foo', Foo\FooController::bar::class)
    ->setStrategy(new JsonApiStrategy());
```

## Tests

`$ vendor/bin/phpunit`

## Contributors

Contributions are welcome. We accept contributions via pull requests on 
Github.

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more 
information.

## About InThere

InThere - "The training Through Gaming Company" - speeds up training your team 
and change processes by providing a micro-training concept based on serious games.  
