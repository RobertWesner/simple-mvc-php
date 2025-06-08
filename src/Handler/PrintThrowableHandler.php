<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Handler;

use Throwable;

/**
 * Outputs the Throwable to the browser content.
 */
class PrintThrowableHandler implements ThrowableHandlerInterface
{
    public function handle(Throwable $throwable): void
    {
        $title = $throwable->getMessage();
        $file = $throwable->getFile();
        $line = $throwable->getLine();
        $trace = $throwable->getTraceAsString();

        echo <<<HTML
            <div style="
                padding: 1rem;
                border-radius: 1rem;
                color: #570808;
                background-color:
                #c77878;
                margin-bottom: 1rem
            ">
                <div style="font-size: 1.4rem"><b>$title</b></div>
                <div>In file $file:$line</div>
                <code><pre>$trace</pre></code>
            </div>
            HTML;
        while (($throwable = $throwable->getPrevious()) !== null) {
            $this->handle($throwable);
        }
    }
}
