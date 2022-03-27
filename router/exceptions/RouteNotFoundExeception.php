<?php

class RouteNotFoundExeception extends Exception{
    function __construct($msg = null) {
        parent::__construct($msg ?? 'Route Not found', 404);
    }
}