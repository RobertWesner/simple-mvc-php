<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route\Class\Controller;

use Psr\Http\Message\ResponseInterface;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhp\Tests\Route\Class\Service\UserService;

/**
 * Small example for complex controllers.
 */
readonly class UserController
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function all(): ResponseInterface
    {
        return Route::json($this->userService->findAll());
    }

    public function get(Request $request): ResponseInterface
    {
        $userId = $request->getUriParameter('userId');
        if (!is_numeric($userId)) {
            return Route::response('Bad Request.', 400);
        }

        return Route::json($this->userService->findOneBy($userId));
    }

    public function create(Request $request): ResponseInterface
    {
        $username = $request->getRequestParameter('name');

        // Validate username...

        return Route::json(
            // create() throws on failure
            $this->userService->create($username),
            201,
        );
    }

    public function delete(Request $request): ResponseInterface
    {
        $userId = $request->getUriParameter('userId');
        if (!is_numeric($userId)) {
            return Route::response('Bad Request.', 400);
        }

        $this->userService->delete($userId);

        return Route::response('', 204);
    }
}
