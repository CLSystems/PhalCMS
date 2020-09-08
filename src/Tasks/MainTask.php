<?php

namespace CLSystems\PhalCMS\Tasks;

use Exception;
use Phalcon\Cli\Task;

class MainTask extends Task
{
    /**
     * Main action for this task
     */
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    /**
     * @param $output
     * @param Exception $e
     * @param Exception|null $previous
     */
    protected function renderError($output, Exception $e, Exception $previous = null)
    {
        echo $output . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;
        echo PHP_EOL;
        echo $e->getTraceAsString();
        if (null !== $previous) {
            echo '====== previous ======' . PHP_EOL;
            echo $previous->getMessage() . PHP_EOL;
            echo PHP_EOL;
            echo $previous->getTraceAsString();
        }
    }
}
