<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Route;

Route::get('/foo', function () {
    return Route::response('bar');
});
