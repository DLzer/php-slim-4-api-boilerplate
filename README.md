# PHP Slim 4 Skeleton

[![CI](https://github.com/DLzer/slim4skeleton/actions/workflows/ci.yml/badge.svg)](https://github.com/DLzer/slim4skeleton/actions/workflows/ci.yml)[![CI-coverage](https://github.com/DLzer/slim4skeleton/actions/workflows/ci-coverage.yml/badge.svg?branch=master)](https://github.com/DLzer/slim4skeleton/actions/workflows/ci-coverage.yml)

Using the [Slim 4](https://www.slimframework.com/docs/v4/) framework

### Pre-requisites
- PHP 7.4 or above is required
- Composer is required
- Docker is optional

### Core Dependencies 

**List of Dependencies**
- [Slim 4](https://github.com/slimphp/Slim) as the core framework 
- [nyholm/psr7](https://github.com/Nyholm/psr7) for the PSR-7 implementation 
- [php-di/php-di](http://php-di.org/) to manage dependency injection    
- [tuupola/slim-jwt-auth](https://appelsiini.net/projects/slim-jwt-auth/) To manage the api authentication using JWT.
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) To load environment variables from `.env` file.
- [phpunit/phpunit](https://phpunit.de/) for testing.
- [Monolog](https://github.com/Seldaek/monolog) for logging   
- [Phinx](https://phinx.org/) for database migrations  
- [Selective/Config](https://github.com/selective-php/config) to manage config settings  
- [Slim/Twig-View](https://github.com/slimphp/Twig-View) for templates  

### Installation
> Start by cloning the repository into your local machine.

! Set the MySQL environment variables by creating a `.env` file based on the `.env.example` file !

> To install for local development using docker
```bash
$ docker-compose build
$ docker-compose-up -d # -d is to run the daemon in detached ( background )
```

> To install for development
```bash
$ composer install           # install all dependenciess
$ composer migrate           # import database
$ composer test              # test the build
```

> To update for production
```bash
$ composer install --no-dev  # install non-dev dependencies
$ composer migrate           # import database
$ composer test              # test the build
```
 
### Environments Variables
All necessary app environment variables are stored in the `.env` file. This project will come standard
with an `.env-example` file. To run correctly you'll need to copy this to a `.env` file and place it inside
the `/src/config` directory.


# Code Overview
## Directory Structure
> Open the project directory using your favorite editor.

The app follows the structure of [Slim skeleton application](https://github.com/slimphp/Slim-Skeleton) with minor changes.
The skeleton is a good starting point when developing with Slim framework.
A detailed overview of the directory structure can be found at [this page](docs/directory.md).  

## Design Architecture

### Api Design
The api is designed according to the [RealWorld](https://github.com/gothinkster/realworld-example-apps) specifications. 
Make sure to familiarized yourself with all endpoints specified in the [Full API Spec](https://github.com/gothinkster/realworld/tree/master/api)

### Code Design
The code utilizes the MVC pattern where requests are redirected to a controller to process the request and returned a JSON response.
Persistence of data is managed by the models which provide the source of truth and the database status. 
### Data Structure
***Database Schema***

The app is built using a relational database (e.g. MySQL). 
MySQL Schemas ( if available ) live in `/database/schemas`

***Database Migration:***

Database migrations or [Schema migration](https://en.wikipedia.org/wiki/Schema_migration) 
is where the app defines the database structure and blueprints. 
It also holds the history of any changes made to the database schema and provides easy way to rollback changes to older version.

The app database migrations can be found at [the migration directory](database/migrations).
Migrations are performed using [Phinx](https://phinx.org/).

> Migrate the Database
```bash
$ composer migrate
```

***Data Models***

The data is managed by models which represent the business entities of the app.
They can be found at [Models Directory](src/Conduit/Models). Each model has corresponding table in the database. 
These models extends `Illuminate\Database\Eloquent\Model` which provides the ORM implementations.

Relationships with other models are defined by each model using Eloquent.
For example, `User-Comment` is a one-to-many relationship 
which is defined [by the User model](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/51ef4cba018673ba63ec2f8cb210effff26aaec5/src/Conduit/Models/User.php#L66-L69)
and [by the Comment model](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/51ef4cba018673ba63ec2f8cb210effff26aaec5/src/Conduit/Models/Comment.php#L41-L43).
This relationship is stored in the database by having a foreign key `user_id` in the comments table.

Beside The four tables in the database representing each model, the database has three other tables to store many-to-many relationships (`article_tag`, `user_favorite`, `users_following`).
For example, An article can have many tags, and a tag can be assigned to many articles. This relationship is defined by the 
[Article model](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/51ef4cba018673ba63ec2f8cb210effff26aaec5/src/Conduit/Models/Article.php#L69-L72) 
and the [Tag model](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/51ef4cba018673ba63ec2f8cb210effff26aaec5/src/Conduit/Models/Tag.php#L31-L34),
and is stored in the table `article_tag`.

### Entry Point:
The server will direct all requests to [index.php](public/index.php). 
There, we boot the app by creating an instance of Slim\App and with the container established.

### The App Instance
The instance of Slim\App (`$app`) holds the app settings, routes, and dependencies.

We register routes and methods by calling methods on the `$app` instance. 

More importantly, the `$app` instance has the `Container` which register the app dependencies to be passed later to the controllers.

### Container Dependencies and Services
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
        /** @var DatabaseFactory */
        private $db;

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
All requests go through the same cycle:  `routing > middleware > conroller > response`

### Routes:
> Check the list of endpoints defined by the [RealWorld API Spec](https://github.com/gothinkster/realworld/tree/master/api#endpoints)

All the app routes are defined in the [routes.php](src/routes.php) file.

The Slim `$app` variable is responsible for registering the routes. 
You will notice that all routes are enclosed in the `group` method which gives the prefix api to all routes: `http::/localhost/api`. Any groups need to utilize the `RouterCollectorProxy` so no routes are overwritten.

Every route is defined by a method corresponds to the HTTP verb. For example, a post request to register a user is defined by:
```php
    $app->post('/member', \App\Controllers\Member\MemberController:get')->setName('member.get');
``` 
The method, `post()`, defines `/api/member` endpoint and direct the request to method `register` on `MemberController` class.

> see [CORS](#cors) for details

### Controllers
After passing through all assigned middleware, the request will be processed by a controller.
> Note: You could process the request inside a closure passed as the second argument to the method defining the route.
> For example, [the last route](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/51ef4cba018673ba63ec2f8cb210effff26aaec5/src/routes.php#L88-L95),
which is left as an example from the skeleton project, handles the request in a closure
> [Check the documentations](https://www.slimframework.com/docs/objects/router.html#route-callbacks).

The controller's job is to validate the request data, check for authorization, process the request by calling a model or do other jobs, 
and eventually return a response in the form of JSON response. 
> // TODO : Explain how dependencies are injected to the controller.


### Basic Idea
Unlike traditional web application, when designing a RESTful Api, when don't have a session to authenticate.
On popular way to authenticate api requests is by using [JWT](https://jwt.io/).

The basic workflow of *JWT* is that our application will generate a token and send it with the response when the user sign up
or login. The user will keep this token and send it back with any subsequent requests to authenticate his request. 
The generated token will have header, payload, and a signature.
It also should have an expiration time and other data to identify the subject/user of the token. 
For more details, the [JWT Introduction](https://jwt.io/introduction/) is a good resource.

> Dealing with *JWT* is twofold: 
> - Generate a *JWT* and send to the user when he sign up or login using his email/password.  
> - Verify the validity of *JWT* submitted with any subsequent requests.

### Generating The Token
We generate the Token when the user sign up or login using his email/password.
This is done in the [RegisterController](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/b852c69e40271054b5fa9ccbf36667807b71f286/src/Conduit/Controllers/Auth/RegisterController.php#L55)
and [LoginController](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/b852c69e40271054b5fa9ccbf36667807b71f286/src/Conduit/Controllers/Auth/RegisterController.php#L55)
by the [Auth service class](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/b852c69e40271054b5fa9ccbf36667807b71f286/src/Conduit/Services/Auth/Auth.php#L47-L64).
> Review [Container Dependencies](#container-dependencies-and-services) about the auth service.

Finally, we send the token with the response back to the user/client.

### JWT Verification
To verify the *JWT* Token we are using [tuupola/slim-jwt-auth](https://appelsiini.net/projects/slim-jwt-auth/) library.
The library provides a middleware to add to the protected routes. The documentations suggest adding the middleware to app globally
and define the protected routes. However, in this app, we are taking slightly different approach.

We add a configured instance of the middleware to the Container, and then add the middleware to every protected route individually.
> Review [Container Dependencies](#container-dependencies-and-services) about registering the middleware.

In the [routes.php](https://github.com/alhoqbani/slim-php-realworld-example-app/blob/b852c69e40271054b5fa9ccbf36667807b71f286/src/routes.php#L19) file,
we resolve the middleware out of the container and assign to the variable `$jwtMiddleware`

Then, when defining the protected route, we add the middleware using the `add` method:
```php
        $jwtMiddleware = $this->getContainer()->get('jwt');
        $this->post('/articles', ArticleController::class . ':store')->add($jwtMiddleware)
```
The rest is on the `tuupola/slim-jwt-auth` to verify the token.
If the token is invalid or not provided, a 401 response will be returned.
Otherwise, the request will be passed to the controller for processing.

### Optional Routes
For the optional authentication, we create a custom middleware [OptionalAuth](src/Conduit/Middleware/OptionalAuth.php).
The middleware will check if there a token present in the request header, it will invoke the jwt middleware to verify the token. 

Again, we use the OptionalAuth middleware by store it in Container and retrieve it to add to the optional routes.
```php
        $optionalAuth = $this->getContainer()->get('optionalAuth');
        $this->get('/articles/feed', ArticleController::class . ':index')->add($optionalAuth);
```

## Authorization
Some routes required authorization to verify that user is authorized to submit the request.
For example, when a user wants to edit an article, we need to verify that he is the owner of the article.

The authorization is handled by the controller. Simply, the controller will compare the article's user_id with request's user id.
If not authorized, the controller will return a 403 response.
```php
        if ($requestUser->id != $article->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }
```
However, in a bigger application you might want to implement more robust authorization system.

## Security
### CORS
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

# Test

Tests are run using PHP-Unit-9
> To run the tests
```bash
$ composer test
```