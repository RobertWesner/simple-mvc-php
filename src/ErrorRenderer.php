<?php

namespace RobertWesner\SimpleMvcPhp;

use Exception;
use Throwable;

final class ErrorRenderer
{
    public static function render(Throwable $throwable): string
    {
        $title = $throwable->getMessage();
        $file = $throwable->getFile();
        $line = $throwable->getLine();
        $trace = $throwable->getTraceAsString();

        $result = <<<HTML
            <div style="padding: 1rem; border-radius: 1rem; color: #570808; background-color: #c77878">
                <div style="font-size: 1.4rem"><b>$title</b></div>
                <div>In file $file:$line</div>
                <code><pre>$trace</pre></code>
            </div>
            HTML;
        if ($throwable->getPrevious() !== null) {
            $result .= self::render($throwable->getPrevious());
        }

        return $result;
    }
}
