<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once './../vendor/autoload.php';
include_once './../Applicative/API.php';

/**
 * Created by PhpStorm.
 * User: clovis
 * Date: 22/01/17
 * Time: 16:15
 */
class Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response|static
     */
    public static function routeHandler(Request $request, Response $response, $next)
    {
        $response = $response->withHeader("Content-Type", "application/json");
        $route = $request->getAttribute("route");

        try {
            $response = $next($request, $response, $route->getArguments());
            if ($response->getBody()->getContents() === "") {
                $response = $response->withStatus(405);
            }
        }
        catch(Exception $e)
        {
            $error = [];
            $error["error"] = [];
            $error["error"]["code"] = $e->getCode();
            $error["error"]["message"] = $e->getMessage();
            $error["error"]["details"] = [];
            $error["error"]["details"]["trace"] = [$e->getTraceAsString()];
            $response = $response->withBody(new \Slim\Http\Body(fopen('php://temp', 'w+')));
            $response->getBody()->write(json_encode($error));
        }

        return $response;
    }
}