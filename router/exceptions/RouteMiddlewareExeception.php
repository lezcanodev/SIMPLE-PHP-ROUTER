<?php


class RouteMiddlewareExeception extends Exception{
    function __construct($msg = null) {
        parent::__construct($msg ?? 'Requested Forbidden', 403);
    }
}