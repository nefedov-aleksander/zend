<?php


namespace Bpm\Core\Controller\Parameter;

use Attribute;
use Bpm\Common\Str;
use Bpm\Core\Controller\Parameter\Exception\InvalidContentException;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Stdlib\Parameters;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromBody extends AbstractJsonContentParameter implements ParameterInterface
{
    const CONTENT_TYPE_JSON = 'json';

    protected $contentTypes = [
        self::CONTENT_TYPE_JSON => [
            'application/hal+json',
            'application/json'
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

        $contentType = $this->getContentType($request, new ContentType('text/plain'));

        if(in_array($contentType, $this->contentTypes[self::CONTENT_TYPE_JSON]))
        {
            return $this->mapFromJson($mapper, $request);
        }

        return $this->mapFromString($mapper, $request);
    }

    private function mapFromString(\ReflectionMethod $mapper, Request $request)
    {
        parse_str($request->getContent(), $parsedParams);

        if (!is_array($parsedParams) ||
            empty($parsedParams) ||
            (count($parsedParams) == 1 && Str::isEmptyOrWhiteSpace(reset($parsedParams)))
        )
        {
            throw new InvalidContentException("Content cannot be parsed. Content string: {$request->getContent()}");
        }

        return $mapper->invoke(null, new Parameters($parsedParams));
    }
}