<?php

namespace Bpm\Core\Controller\Parameter;

use Attribute;
use Bpm\Core\Controller\Parameter\Exception\InvalidContentTypeException;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Stdlib\Parameters;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromPost extends AbstractJsonContentParameter implements ParameterInterface
{
    const HEADER_CONTENT_TYPE = 'content-type';

    const CONTENT_TYPE_JSON = 'json';
    const CONTENT_TYPE_FORM = 'form';

    protected $contentTypes = [
        self::CONTENT_TYPE_JSON => [
            'application/hal+json',
            'application/json'
        ],
        self::CONTENT_TYPE_FORM => [
            'multipart/form-data'
        ]
    ];

    private string $mapper;

    public function __construct(string $mapper)
    {
        $this->mapper = $mapper;
    }

    public function map(Request $request, string $exceptedClass)
    {
        $mapper = $this->getMapper(new \ReflectionClass($this->mapper), $exceptedClass);

        $contentType = $this->getContentType($request, new ContentType('multipart/form-data'));

        if(in_array($contentType, $this->contentTypes[self::CONTENT_TYPE_FORM]))
        {
            return $this->mapFromPost($mapper, $request);
        }

        if(in_array($contentType, $this->contentTypes[self::CONTENT_TYPE_JSON]))
        {
            return $this->mapFromJson($mapper, $request);
        }

        throw new InvalidContentTypeException("Content type {$contentType} not declared.");
    }

    private function mapFromPost(\ReflectionMethod $mapper, Request $request)
    {
        return $mapper->invoke(null, $request->getPost());
    }
}