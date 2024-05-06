<?php

namespace App\Core;

class Controller
{

    protected string $controller;
    protected string $namespace;

    public function __construct()
    {
        // we will use this to define some common methods that we will use in our Controllers
    }

    public function extractNamespace($controller)
    {
        // first remove the class name by taking the last part of the string away backwards to the first backslash
        $namespace = substr($controller, 0, strrpos($controller, '\\'));

        // if the namespace is now just \Controllers\ we will remove the Controllers part
        $namespace = str_replace('Controllers\\', '', $namespace);

        // add the first slash
        $namespace .= '/';

        // add the controller name
        $this->controller = $controller;

        // add the namespace to the controller
        $this->namespace = $namespace;
    }

}