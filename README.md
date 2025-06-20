<h1 align="center">
Simple MVC for PHP
</h1>

<div align="center">

![](https://github.com/RobertWesner/simple-mvc-php/actions/workflows/tests.yml/badge.svg)
![](https://raw.githubusercontent.com/RobertWesner/simple-mvc-php/image-data/coverage.svg)
![](https://img.shields.io/github/v/release/RobertWesner/simple-mvc-php)
[![License: MIT](https://img.shields.io/github/license/RobertWesner/simple-mvc-php)](../../raw/main/LICENSE.txt)

</div>

A small library for creating PHP web servers.

Initially created for private use in place of Node-JS when creating very simple websites.
Now able to run more complex applications.
Feel free to use if it fits your needs.

Websites using this:
- https://scripts.yt
- https://robert.wesner.io
- https://ytplaylist.robert.wesner.io

## Features

- Request handling (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`)
  - Query parameters
  - JSON parameters
  - URI parameters
- Intuitive Syntax
- Simple to use composer template
- Integrated Twig templating engine
- [Optional] Autowiring of controller dependencies
- [Optional] Ability to load external bundles

## Installation

### New Docker project

This creates a Docker PHP-FPM + Nginx Project and is the preferred way of use.

```bash
composer create-project robertwesner/simple-mvc-php-docker-template
```

### New project

This creates a new project with the required folder structure.

```bash
composer create-project robertwesner/simple-mvc-php-template
```

### Existing project

If you already have a project, require the package and migrate your files manually.

```bash
composer require robertwesner/simple-mvc-php
```

## Usage

### Project structure

```
PROJECT_ROOT
|-- public
|   '-- any publicly accessible data like JS, CSS, images, ...
|-- routes
|   '-- PHP routing scripts
|-- views
|   '-- twig views
|-- src
|-- vendor
'-- route.php
```

### Routing scripts

You can create any amount of routing scripts.
They define a mapping between a URL and a controller function or method.

Example:

```
PROJECT_ROOT
'-- routes
|   |-- api.php
|   '-- view.php
'-- views
    '-- main.twig
```

api.php

```php
Route::post('/api/login', function (Request $request) {
    // Reads either Query or JSON-Body Parameter
    $password = $request->getRequestParameter('password');
    if ($password === null) {
        return Route::response('Bad Request', 400);
    }

    // ...
    
    return Route::json([
        'success' => $success,
    ]);
});

Route::post('/api/logout', function () {
    // ...
});

// Also able to read URI parameters
Route::get('/api/users/(?<userId>\d+)', function (Request $request) {
    $userId = $request->getUriParameter('userId'); // Returns numeric userId from capture group

    // ...
});

// 404 page, FALLBACK will be called when no other route matches
Route::get(Route::FALLBACK, function (Request $request) {
    return Route::render('404.twig');
});
```

view.php
```php
Route::get('/', function () {
    // ...

    return Route::render('main.twig', [
        'loggedIn' => $loggedIn,
    ]);
});
```

### Using Controller Classes

More complex Logic can be handled with class controllers.

Resolving the controller requires [robertwesner/dependency-injection](https://github.com/RobertWesner/dependency-injection).

See: [demo class](./tests/Route/Class/Controller/UserController.php) and [demo routing](./tests/Route/Class/routes/user.php)

```php
final class UserService
{
    // ...
}

readonly class UserController
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function all(): ResponseInterface
    {
        // ...
    }

    public function get(Request $request): ResponseInterface
    {
        // ...
    }

    public function create(Request $request): ResponseInterface
    {
        // ...
    }

    public function delete(Request $request): ResponseInterface
    {
        // ...
    }
}
```

```php
// Note: this requires robertwesner/dependency-injection
Route::get('/api/users', [UserController::class, 'all']);
Route::get('/api/users/(?<userId>\d+)', [UserController::class, 'get']);
Route::post('/api/users', [UserController::class, 'create']);
Route::delete('/api/users/(?<userId>\d+)', [UserController::class, 'delete']);
```

### Autowiring

Installing [robertwesner/dependency-injection](https://github.com/RobertWesner/dependency-injection) allows for automatic resolution of Route dependencies:

```php
// Autowired service class (AuthenticationService) inside Route
// Note: this requires robertwesner/dependency-injection
Route::post('/api/admin/some-endpoint', function (Request $request, AuthenticationService $authenticationService) {
    // ...
});
```

### Configuration

Configurations are optional and stored in `$PROJECT_ROOT$/configuration`, written in PHP.

You can run this server with zero configuration if you do not need the following features.

#### Container 

File: `container.php`

Configures additional autowiring steps if you intend to use `robertwesner/dependency-injection` in complex use cases.
You can manually define container instances with this configuration.

```php
Configuration::CONTAINER
    // Either let the container do all the heavy lifting via class names,
    // MySQLEntityManager would be automatically instantiated by the container.
    // This is necessary for usage of interfaces, rather than implementations.
    ::instantiate(EntityManagerInterface::class, MySQLEntityManager::class)
    // Or pass your own instance when necessary, since Bar is not to be autowired.
    ::register(FooInterface::class, new Bar('SOME VALUE'));
```

#### Bundles

File `bundles.php`

Loads external bundles (implementing [BundleInterface](./src/Bundles/BundleInterface.php)) which may configure their own Container values.

> Feel free to store configurations for your bundles in a **subfolder** inside `$PROJECT_ROOT$/configuration`.
> 
> Example: `$PROJECT_ROOT$/configurations/database/database.yml`

```php
Configuration::BUNDLES
    ::load(FooBundle::class)
    // Optionally with additional configuration of any type, depending on the bundle.
    ::load(BarBundle::class, ['faz' => 'baz']);
```

### Error handling

Requires the use of configuration files, refer to the previous section for mor information.

Use your preferred ThrowableHandler by instantiating it as `ThrowableHandlerInterface`.
By default, without registering a handler, no exception information will be stored or printed.

Example: PrintThrowableHandler outputs directly to the browser

```php
Configuration::CONTAINER
    ::instantiate(ThrowableHandlerInterface::class, PrintThrowableHandler::class);
```

Example: StdoutThrowableHandler outputs into the server stderr and doesn't leak to the browser 

```php
Configuration::CONTAINER
    ::instantiate(ThrowableHandlerInterface::class, StderrThrowableHandler::class);
```

You can implement your own handler quite easily for additional tasks like sending automated mails. 
