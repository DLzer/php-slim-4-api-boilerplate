# PHP Slim 4 Skeleton

[![CI](https://github.com/DLzer/slim4skeleton/actions/workflows/ci.yml/badge.svg)](https://github.com/DLzer/slim4skeleton/actions/workflows/ci.yml)[![CI-coverage](https://github.com/DLzer/slim4skeleton/actions/workflows/ci-coverage.yml/badge.svg?branch=master)](https://github.com/DLzer/slim4skeleton/actions/workflows/ci-coverage.yml)

Using the [Slim 4](https://www.slimframework.com/docs/v4/) framework

## Requirements
* PHP 7.4+ or 8.0+

## Table of contents
* [Dependencies](#dependencies)
* [Installation](#installation)
    * [Installing with Docker](#setup-with-docker)
    * [Insatlling without Docker](#setup-without-docker)
* [App Environment](#app-environment)
* [External Documentation](#external-documentation)
* [Database Migrations](#database-migrations)
* [App](#app-entry-point)
    * [App Instance](#app-instance)
    * [Container](#container-dependencies-and-services)
    * [App Lifecycle](#request-response-cycle)
    * [Routes](#routes)
    * [Actions](#actions)
    * [Domains](#domains)
* [Security](#cors)
    * [CORS](#cors)
    * [JWT](#jwt)
* [Testing](#testing)

## Dependencies 

**List of Dependencies**
- [Slim 4](https://github.com/slimphp/Slim) as the core framework 
- [nyholm/psr7](https://github.com/Nyholm/psr7) for the PSR-7 implementation 
- [php-di/php-di](http://php-di.org/) to manage dependency injection    
- [tuupola/slim-jwt-auth](https://appelsiini.net/projects/slim-jwt-auth/) To manage the api authentication using JWT.
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) To load environment variables from `.env` file.
- [selective](https://github.com/selective-php) We use multiple packages from selective for testing, validating, and hydrating.
- [phpunit/phpunit](https://phpunit.de/) for testing.
- [Monolog](https://github.com/Seldaek/monolog) for logging   
- [Phinx](https://phinx.org/) for database migrations  
- [Selective/Config](https://github.com/selective-php/config) to manage config settings  
- [Slim/Twig-View](https://github.com/slimphp/Twig-View) for templates  

## Installation

Start by cloning the repository

```bash
git clone https://github.com/DLzer/php-slim-4-api-boilerplate.git
```

Create a `.env` file and set your environment variables

```bash
cp .env-example .env
```

## Setup with Docker

```bash
$ docker-compose build
$ docker-compose-up -d # -d to run in daemon mode
$ composer migrate # Run the database migrations
$ composer seed # Insert seed data into the database
```

## Setup without Docker

> To install for production run composer install with the --no-dev option
```bash
$ composer install           # install all dependenciess
$ composer migrate           # import database
$ composer seed              # insert seed data
$ composer test              # test the build
```
 
## App Environment

All environment variables are stored in the `.env` file. When the application is bootstrapped the settings and container will load the variables into their respective locations. The global environment for the app is under the `App` namespace.

## External Documentation

The app follows the structure of [Slim skeleton application](https://github.com/slimphp/Slim-Skeleton) with minor changes.
The skeleton is a good starting point when developing with Slim framework.
A detailed overview of the directory structure can be found at [this page](docs/directory.md).  

The api is designed according to the [RealWorld](https://github.com/gothinkster/realworld-example-apps) specifications. 
Make sure to familiarized yourself with all endpoints specified in the [Full API Spec](https://github.com/gothinkster/realworld/tree/master/api)

## Database Migrations

***Schema***

The schema is generated using the tool [Phinx Migrations Generator](https://github.com/odan/phinx-migrations-generator). This utility will generate a new schema if one does not exist. Otherwise it will run a comparison against the existing schema and modify with any changes.

To run the utility:
```bash
$ composer generate-migration
```

***Migrations***

Migrations are a way of describing the table structure for the application, and also provide an easy interface for creating tables, tracking changes, and rolling back changes.

The app database migrations can be found in the [migration directory](database/migrations).
Migrations are performed using [Phinx](https://phinx.org/).

To run the migrations:
```bash
$ composer migrate
```

***Seeds***

Seeds are mock data that we can use to inject into the database tables. The seed files can be found in the [seeds directory](database/seeds)

To run the seeds:
```bash
$ composer seed
```

## App Entry Point:
The server will direct all requests to [index.php](public/index.php). 
There, we boot the app by creating an instance of Slim\App and with the container established.

## The App Instance
The instance of Slim\App (`$app`) holds the app settings, routes, and dependencies.

We register routes and methods by calling methods on the `$app` instance. 

More importantly, the `$app` instance has the `Container` which registers the app dependencies to be passed later to the controllers.

## Container Dependencies and Services
In different parts of the application we need to use other classes and services. These classes and services also depends on other classes.
Managing these dependencies becomes easier when we have a container to hold them. Basically, we configure these classes and store them in the container.
Later, when we need a service or a class we ask the container, and it will instantiate the class based on our configuration and return it.

The container is configured in the `container.php`.
We start be retrieving the container from the `$app` instance and configure the required services: 

```php
    Configuration::class => function() {
        return new Configuration(require __DIR__ . '/settings.php');
    },
    DatabaseFactory::class => function (ContainerInterface $container) {
        return new DatabaseFactory($container->get(Configuration::class)->getArray('db'));
    },
```

The above code registers a configured instance of the `DatabaseFactory` in the container. Later we can ask for the `DatabaseFactory` using autowiring and dependency injection.

```php
    final class SomeService
    {
        private DatabaseFactory $db;

        public function __construct(DatabaseFactory $connection)
        {
            $this->db = $connection;
        }

        public function get(int $ID): object
        {
            $this->db->query("...");
            return $this->db->single(true);
        }
    }
```

## Request-Response Cycle
All requests go through the same cycle:  
`routing > middleware > conroller > response`

## Routes:
> Check the list of endpoints defined by the [RealWorld API Spec](https://github.com/gothinkster/realworld/tree/master/api#endpoints)

All the app routes are defined in the [routes.php](src/routes.php) file.

The Slim `$app` variable is responsible for registering the routes. 
You will notice that all routes are enclosed in the `group` method which gives the prefix api to all routes: `http::/localhost/api`. Any groups need to utilize the `RouterCollectorProxy` so no routes are overwritten.

Every route is defined by a method corresponds to the HTTP verb. For example, a post request to register a user is defined by:
```php
    $app->post('/member', \App\Controllers\Member\MemberController:get')->setName('member.get');
``` 
The method, `post()`, defines `/api/member` endpoint and direct the request to method `register` on `MemberController` class.

## Actions
After passing through all assigned middleware, the request will be processed by a action.

The action's job is to validate the request data, check for authorization, process the request by calling a domain service or do other jobs, 
and eventually return a response in the form of JSON response. 

## Domains

Domains store the separated namespaces for the business logic section of the application. Domains will contain Data Types, Services, Repositories, and any helpers relative to the specific domain.

For example, we'll take the *User* Domain locatied [here](src/Domain/User). The User domain contains:
- `UserData` Which is the described model type for an individual user.
- `UserRepository` Which is the storage/retrieval layer for sending and requesting `UserData`.
- `User{Action}Service` The services are the primary business logic section for manipulating data.
- `UserRoleType` Which is a set of constants describing the available roles a user can have.

We separate the services into specific tasks for better control, and less overflow.

## CORS
[CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) is used when the request is coming from a different host.
By default, web browsers will prevent such requests.
The browser will start by sending an `OPTION` request to the server to get the approval and then send the actual request.

Therefor, we handle cross-origin HTTP requests by making two changes to our app:
- Allow `OPTIONS` requests.  
- Return the approval in the response. 

This is done in the by adding two middleware in the [middleware.php](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/b852c69e40271054b5fa9ccbf36667807b71f286/src/middleware.php) file
The first middleware will add the required headers for CORS approval.
And the second, deals with issue of redirect when the route ends with a slash.

For more information check Slim documentations:
- [Setting up CORS](https://www.slimframework.com/docs/cookbook/enable-cors.html)
- [Trailing / in route patterns](https://www.slimframework.com/docs/cookbook/route-patterns.html)

## JWT

JWT's are generated using the [Firebase JWT](https://github.com/firebase/php-jwt) package. When a user is authenticated a new JWT is generated with only relative data to retreiving the user information. We don't pack to much into the JWT in order to follow the general rule of *Only pack what you're willing to lose*. Meaning if we were to send the entire user profile we would be storing personally-identifiable information on the client, which is a huge no-no.

## Testing

Tests are found in the `tests` directory and follow the same namespace convention as the application. We use the `AppTestTrait` to extend the test suite with our own mock routing, database setup, and HTTP Requests.

Tests are run using *PHP-Unit 9.5*

Running the tests:

```bash
$ composer test
```

Running the tests with the coverage module:

```bash
$ composer test:coverage
```