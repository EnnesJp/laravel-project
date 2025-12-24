<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\User\DTOs\CreateUserDTO;
use App\Domains\User\Resources\UserResource;
use App\Domains\User\Services\UserService;
use App\Http\Requests\CreateUserRequest;
use App\Http\Responses\JsonResponse;
use Illuminate\Http\JsonResponse as BaseJsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function store(CreateUserRequest $request): BaseJsonResponse
    {
        try {
            $dto = CreateUserDTO::fromRequest($request);

            $user = $this->userService->createUser($dto);

            return JsonResponse::created(
                new UserResource($user),
                'User created successfully'
            );
        } catch (\Exception $e) {
            return JsonResponse::error(
                'Failed to create user',
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}
