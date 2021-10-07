<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

class InvalidateCache implements Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $response->withAddedHeader('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');
        $response->withAddedHeader('Pragma', 'no-cache');
        $response->withAddedHeader('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
