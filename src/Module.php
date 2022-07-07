<?php

namespace Bpm\Crm;

use Bpm\Common\Str;
use Bpm\Core\Route\ApiVersion;
use Bpm\Core\Route\Builder\RouteSchemeBuilder;
use Bpm\Core\Route\Route;
use Bpm\Core\Route\RouteInterface;
use Ds\Vector;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Stdlib\ArrayUtils;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface,
    ControllerProviderInterface
{
    private $controllerConfig;
    private $routingConfig;

    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $config      = $application->getConfig();

//        echo '<pre>';
//        var_dump($e->getName());
//        die();

        $config = $e->getTarget()->getConfig();

        $this->writeCache(
            __DIR__ . '/../data/cache/module-controllers-cache.php',
            $this->controllerConfig);
        $this->writeCache(
            __DIR__ . '/../data/cache/module-routing-cache.php',
            $this->routingConfig);

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 0);
    }

    public function onDispatchError(EventInterface $e)
    {
        echo '<pre>onDispatchError';
        var_dump($e->getError());
        die();
    }

    public function onRenderError(EventInterface $e)
    {
        echo '<pre>onDispatchError';
        var_dump($e->getError());
        die();
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';

        return ArrayUtils::merge($config, $this->getRoutes());
    }

    public function getControllerConfig()
    {
        $factories = $this->getControllers()
            ->map(fn($x) => [$x->getName() => InvokableFactory::class])
            ->toArray();

        return $this->controllerConfig =
            [
                'factories' => array_merge(...$factories)

        ];

    }

    private function getRoutes()
    {
        $routes = [];

        foreach ($this->getControllers() as $controller)
        {
            $version = $controller->getAttributes(ApiVersion::class);
            if(count($version) == 0)
            {
                throw new \RuntimeException("Attribute ApiVersion is required for {$class}");
            }

            $route = $controller->getAttributes(Route::class);
            if(count($route) == 0)
            {
                throw new \RuntimeException("Attribute Route is required for {$class}");
            }

            $actions = new Vector();
            $methods = $controller->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method)
            {
                $attr = $method->getAttributes(RouteInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

                if(count($attr) == 0)
                {
                    continue;
                }


                $actions->push($attr[0]->newInstance()->setActionName($method->getName()));
            }

            $routeSchemeBuilder = new RouteSchemeBuilder($actions);

            $routes[Str::replace($controller->getName(), '\\', '_')] = [
                'type'    => \Zend\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/' . $route[0]->newInstance()->create([
                        '<version:apiVersion>' => $version[0]->newInstance()->version
                    ]),
                    'defaults' => [
                        'controller' => $controller->getName()
                    ],
                ],
                'child_routes' => $routeSchemeBuilder->build()
            ];
        }
        return $this->routingConfig = [
            'router' => [
                'routes' => $routes
            ]
        ];
    }

    private function getControllers(): Vector
    {
        $controllers = new Vector();

        $dir = new \DirectoryIterator(__DIR__ . '/Controller');
        foreach ($dir as $file)
        {
            if($file->isDot())
            {
                continue;
            }

            $class = substr($file->getFilename(), 0, strpos($file->getFilename(), '.'));

            $controllers->push(new \ReflectionClass("Bpm\\Crm\\Controller\\" . $class));
        }

        return $controllers;
    }

    private function writeCache($filePath, $config)
    {
        $tmp = tempnam(sys_get_temp_dir(), md5($filePath));

        $content = "<?php\nreturn " . var_export($config, true) . ';';
        file_put_contents($tmp, $content);
        chmod($tmp, 0666 & ~umask());

        rename($tmp, $filePath);
    }
}
