## laravel-api-doc-provider
Simple API Doc generator based on Controller Annotations using with Laravel 5.2.*

Install by steps:
#### Update your composer via command:
```bash
composer require restio/laravel-api-doc-provider
```

#### Add the service provider to the `providers` array in `config/app.php`

```php
RestioDocProvider\RestioDocProvider::class,
```

#### Run artisan command:

```bash
php artisan clear-compiled
php artisan optimize
```

#### Publish vendor

```bash
php artisan vendor:publish
```

#### Configure 
`config/restio_doc.php`

#### Start annotate your Controllers (for ex. `app\Http\Controllers\ExampleController.php`)

#### About annotation
```php
@route                example_index
@description          Example description for route
@required_params      [token]
@optional_params      [page]
```

- @route - route name from RouteCollection
- @description - short description about this route (ex. Main page, List of Users etc.)
- @required_params - Required params (this params be marked with red star)
- @optional_params - Optional params
- token, page - params in app/Models/Doc.php

#### Write unitTest with write success response to json objects (for ex. `tests/RestioExampleTest.php`)

#### Run unit tests for generate success responses
```bash
php phpunit
```

#### After testing we must regenerate all API DOC with command
```bash
php artisan generate:docs
```

**Enjoy!**

