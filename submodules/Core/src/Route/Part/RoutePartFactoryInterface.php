<?php


namespace Bpm\Core\Route\Part;


interface RoutePartFactoryInterface
{
    public function create(string $part): RoutePartInterface;
}