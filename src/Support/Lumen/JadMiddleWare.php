<?php declare(strict_types=1);

namespace Jad\Support\Lumen;

use Jad\Jad;
use Jad\Map\AnnotationsMapper;
use \Illuminate\Http\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class JadMiddleWare
 * @package Jad\Support\Lumen
 */
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
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function handle(Request $request, \Closure $next)
    {
        $pathPrefix = config()['jad']['pathPrefix'];

        $pathMatch = (bool)preg_match('!' . ltrim($pathPrefix, '/') . '!', $request->path(), $matches);

        if (!$pathMatch) {
            return $next($request);
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->registry->getManager();

        $jad = new Jad(new AnnotationsMapper($em));
        $jad->setPathPrefix(config()['jad']['pathPrefix']);

        if ($jad->jsonApiResult()) {
            return $next($request);
        }

        $response = $next($request);

        return $response;
    }
}
