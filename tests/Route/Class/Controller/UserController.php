<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route\Class\Controller;

use Psr\Http\Message\ResponseInterface;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;

/**
 * Small example for complex controllers.
 */
class UserController
{
    private mixed $userService;

    public function __construct()
    {
        // Mock of a potential service managing users
        $this->userService = new class {
            public function findAll(): array
            {
                return [];
            }

            public function findOneBy(int $id): array
            {
                return ['name' => 'foo'];
            }

            public function create(string $username): array
            {
                return ['name' => $username];
            }

            public function delete(int $id): void {}
        };
    }

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
        $username = $request->getParameter('name');

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
