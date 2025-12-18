<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\User\CreateUserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\JsonResponse;
use App\Services\UserService;
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
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
