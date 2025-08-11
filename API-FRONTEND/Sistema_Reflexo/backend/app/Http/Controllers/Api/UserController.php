<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SearchUsersRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\Profile\UploadImageRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\User\UserService;
use App\Http\Controllers\Api\ImageController;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private ImageController $imageController
    ) {
        $this->userService = $userService;
        $this->imageController = $imageController;

        $this->middleware('can:users.index')->only('index');
        $this->middleware('can:users.store')->only('store');
        $this->middleware('can:users.show')->only('show');
        $this->middleware('can:users.update')->only('update');
        $this->middleware('can:users.destroy')->only('destroy');
        $this->middleware('can:users.search')->only('searchUsers');
        
        // Middlewares para funciones de imagen
        $this->middleware('can:users.photo.upload')->only(['uploadPhoto']);
        $this->middleware('can:users.photo.show')->only(['showPhoto']);
        $this->middleware('can:users.photo.delete')->only(['deletePhoto']);
    }

    public function index(Request $request): UserCollection
    {
        $users = $this->userService->getPaginated($request);
        return new UserCollection($users);
    }

    public function searchUsers(SearchUsersRequest $request): UserCollection
    {
        $users = $this->userService->searchUsers($request->validated());
        return new UserCollection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->userService->storeOrRestore($request->validated(), $request->file('photo'));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return $this->userService->update($user, $request->validated(), $request->file('photo'));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->destroy($user);
        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }

    /**
     * Subir foto de perfil del usuario autenticado
     */
    public function uploadPhoto(UploadImageRequest $request): JsonResponse
    {
        $user = auth()->user(); // Obtener el usuario autenticado
        return $this->imageController->uploadUserPhoto($request, $user);
    }

    /**
     * Mostrar la foto del usuario autenticado
     */
    public function showPhoto()
    {
        $user = auth()->user();
        return $this->imageController->showUserPhoto($user);
    }

    /**
     * Eliminar la foto del usuario autenticado
     */
    public function deletePhoto(): JsonResponse
    {
        $user = auth()->user();
        return $this->imageController->deleteUserPhoto($user);
    }
}