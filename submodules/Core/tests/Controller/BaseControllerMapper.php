<?php


namespace Bpm\Test\Core\Controller;


use Zend\Stdlib\Parameters;

class BaseControllerMapper
{
    public static function mapFromQuery(Parameters $parameters): MockFromQueryRequest
    {
        $query = new MockFromQueryRequest();
        $query->name = $parameters->get('name');
        return $query;
    }

    public static function mapFromPost(Parameters $parameters): MockFromPostRequest
    {
        $post = new MockFromPostRequest();
        $post->data = $parameters->get('data');
        return $post;
    }

    public static function mapFromBody(Parameters $parameters): MockFromBodyRequest
    {
        $post = new MockFromBodyRequest();
        $post->body = $parameters->get('bodydata');
        return $post;
    }
}