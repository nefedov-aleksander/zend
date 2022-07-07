<?php


namespace Bpm\Core\Route\Part;


interface RouteConstraintInterface extends RoutePartInterface
{
    public function getConstraint();
}