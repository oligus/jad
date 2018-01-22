<?php

namespace Jad\Support\Lumen;

use Jad\Jad;
use Jad\Configure;
use Jad\Map\AnnotationsMapper;
use \Illuminate\Http\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

class JadMiddleWare
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * JadMiddleware constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $pathPrefix = config()['jad']['pathPrefix'];
        $pathMatch = (bool) strpos($pathPrefix, $request->path());

        if(!$pathMatch) {
            return $next($request);
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->registry->getManager();

        $jad = new Jad(new AnnotationsMapper($em));
        $jad->setPathPrefix(config()['jad']['pathPrefix']);

        if($jad->jsonApiResult()) {
            return $next($request);
        }

        $response = $next($request);

        return $response;
    }
}
