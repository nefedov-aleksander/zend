<?php


namespace Bpm\Core\Route;


interface RouteInterface
{
    public function compile(): string;

    public function hasConstraints(): bool;

    public function getConstraints(): array;

    public function getRouteTypeName(): string;

    public function setActionName(string $actionName): RouteInterface;

    public function getActionName(): string;
}