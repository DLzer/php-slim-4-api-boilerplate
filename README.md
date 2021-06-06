Slim v4 Skeleton API
==========

Configuration
-------------

The directory for all configuration files is: `config/`
In this file are the core files for running the application.
- `settings.php`: Manages all core settings and environment variables for the application
- `bootstrap.php`: Loads the Container, Routes, Middleware and Settings.
- `middleware.php`: Contains middleware for the application.
- `routes.php`: Contains defined routes for the application.
- `container.php`: Houses the ContainerInterface utilizing PHP-DI for injection into the application.

FrontController
---------------

The front controller is just the `index.php` file and entry point to the application. This handles all requests through the application by channeling requests through a single handler object.

Container
---------

Traditionally the style of fetching dependencies was to inject the whole container into your class which is considered an **anti-pattern**. We switch up the method in this application by using modern tools like [PHP-DI](http://php-di.org/).

The DI used in this application is housed in a **Depedency Injection Container** ( DIC ). The method we use in this application is [composition over inheritance](https://en.wikipedia.org/wiki/Composition_over_inheritance) and (constructor) DI.

Domain
------

The domain in this application houses the complex **business logic**.
Instead of putting together business logic into massive fat "Models", they are separated into specialized *Services* aka an **Application Service**

Each service can have multiple clients, e.g Action (request), CLI (console), Data (logic), Unit Testing (phpunit). This way each service manages only one responsibility and not more by separating data from behavior.

Eloquent
--------

In this application example i'm using the [Eloquent](https://laravel.com/docs/5.0/eloquent) ORM, which is a minor package from Laravel. Eloquent simply allows us to create queries in a simplified manner. As well as determining user models that match table schema's. This is just a personal preference caused by a hatred of writing long SQL queries.

Deployment
----------

Deployment is best served through a **build pipeline** however if manual deployment is necessary it's as simple as running:
````
composer install --no-dev --optimize-autoloader
````
This will remove dev-dependencies as well as optimize the composer autoloader for better performance.

For security reasons it's also best practice to turn of output of error details:
````
$settings['error_handler_middleware'] = [
    'display_error_details' => false,
];
````

Phinx Migration
---------------

Phinx is a dependency used for creating database migrations and seeds.
To create a migration run the composer script
````bash
$ composer create-migration {Migration Name}
````

This will create a new migration file under `database/migrations`. In the newly created file
you can define new tables or existing table modifications. After creating your migration and uploading
the migration file to your server run this command to populate migrations
````bash
$ composer migrate
````

This will push any changes you wrote to your specified database, all the while keeping a log of changes
under the self created `phinxlog` table.

Please Note: Windows users cannot use the composer scripts due to the *nix directory separator. But instead
will have to manually use the commands `vendor\bin\phinx create migration {Migration}`.

This package can do a lot more, you can read about migrations and seeding here: [Phinx](https://book.cakephp.org/phinx/0/en/intro.html)
