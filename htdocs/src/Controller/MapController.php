<?php

namespace App\Controller;


use App\Service\Mapbox;
use GuzzleHttp\Exception\GuzzleException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    const CACHE_MAX_AGE = 60 * 60 * 24 * 365;

    /**
     * @Route("/map/tile/{z}/{x}/{y}",
     *     name="mapTile",
     *     requirements={"z"="\d+", "x"="\d+", "y"="\d+"}
     *     )
     * @Cache(smaxage="3600", public=true)
     *
     * @param $z
     * @param $x
     * @param $y
     * @param Mapbox $mabox
     * @return Response
     * @throws GuzzleException
     */
    public function getTile($z, $x, $y, Mapbox $mabox): Response
    {
        /** @var string $map */
        $map = $mabox->getMap($z, $x, $y);

        /** @var Response $response */
        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
//        $response->setCache([
//            'etag' => 'abcd',
//            'last_modified' => new \DateTime(),
//            'public' => true,
//            'private' => false,
//            'max_age' => 31536000
//        ]);
        $response->setPublic();
        $response->setMaxAge(self::CACHE_MAX_AGE);
        $response->setContent($map);

        return $response;
    }
}