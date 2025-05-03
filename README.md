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

Use case: Serving semi-static content; not intended for large scale sites with complex logic.

Initially created for private use in place of Node-JS when creating very simple websites.
Feel free to use if it fits your needs.

Websites using this:
- https://scripts.yt
- https://robert.wesner.io

## Features

- Request handling (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`)
  - Query parameters
  - JSON parameters
  - URI parameters
- Intuitive Syntax
- Simple to use composer template
- Integrated Twig templating engine

## Installation

### New project

This creates a new project with the required folder structure and is the preferred way of use.

```bash
composer create-project robertwesner/simple-mvc-php-template
```

### Existing project

If you already have a project, require the package and migrate your files manually.

```bash
composer require robertwesner/simple-mvc-php "*"
```

## Configuration

### nginx

All traffic except for "/public" should be redirected to "/route.php".

Below is a Nginx sample configuration running under Docker.

```nginx
server {
    index index.php index.html;
    server_name ...;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /var/www/html;

    proxy_intercept_errors on;

    location / {
        try_files /public$uri /public /route.php?$query_string;
    }

    location ~ /route\.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
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
<?php

use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;

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
```

view.php
```php
<?php

use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;

Route::get('/', function () {
    // ...

    return Route::render('main.twig', [
        'loggedIn' => $loggedIn,
    ]);
});
```

### Using Controller Classes

More complex Logic can be handled with class controllers.

> Note: This is not recommended. Complex applications should use _more sophisticated_ frameworks.

See: [demo class](./tests/Route/Class/Controller/UserController.php) and [demo routing](./tests/Route/Class/routes/user.php)

```php
<?php

use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Tests\Route\Class\Controller\UserController;

$controller = new UserController();
Route::get('/api/users', $controller->all(...));
Route::get('/api/users/(?<userId>\d+)', $controller->get(...));
Route::post('/api/users', $controller->create(...));
Route::delete('/api/users/(?<userId>\d+)', $controller->delete(...));
```
