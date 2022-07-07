<?php

namespace Bpm\Core\Route;

enum HttpRouteType: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
}