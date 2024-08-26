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

## Installation

### New project

This creates a new project with the required folder structure and is the preferred way to use it.

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
    $password = $request->get('password');
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

TODO: `[$controller, 'getWhatever']` as callable
