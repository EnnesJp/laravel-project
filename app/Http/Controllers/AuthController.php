<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\User\DTOs\LoginDTO;
use App\Domains\User\Resources\LoginResource;
use App\Domains\User\Services\UserService;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\JsonResponse;
use Illuminate\Http\JsonResponse as BaseJsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function login(LoginRequest $request): BaseJsonResponse
    {
        try {
            $dto = LoginDTO::fromRequest($request);

            $result = $this->userService->login($dto);

            return JsonResponse::success(
                new LoginResource($result),
                'Login successful'
            );

        } catch (ValidationException $e) {
            return JsonResponse::validationError(
                $e->errors(),
                'Invalid credentials'
            );
        } catch (\Exception $e) {
            return JsonResponse::error(
                'Login failed',
                $e->getMessage(),
                500
            );
        }
    }
}
