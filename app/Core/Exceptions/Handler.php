<?php

namespace App\Core\Exceptions;

/**
 * Exception handler class
 */
class Handler
{
    /**
     * Handle the exception
     *
     * @param \Exception $e
     * @return void
     */
    public function handle(\Exception $e)
    {
        echo $e->getMessage();
    }

    /**
     * Handle the exception
     *
     * @param \Exception $e
     * @return void
     */
    public function report(\Exception $e)
    {
        echo $e->getMessage();
    }

    /**
     * Handle the exception
     *
     * @param \Exception $e
     * @return void
     */
    public function render(\Exception $e)
    {
        echo $e->getMessage();
    }

}