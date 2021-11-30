<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\DeleteUserRequest;
use App\Http\Requests\Api\User\IndexUserRequest;
use App\Http\Requests\Api\User\ShowUserRequest;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexUserRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexUserRequest $request)
    {
        $users = Cache::remember(json_encode($request->safe()->all()), 120, function() use($request){
            $users = User::search($request->search ?? null);

            if ($request->has('order_type') and $request->has('order_by')) {
                $users = $users->sortBy($request->order_by, SORT_REGULAR, $request->order_type === 'desc');
            }
            return $users;
        });

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            User::create($request->validated());
        } catch (Throwable $exception) {
            Log::info('User creation failed. ' . $exception->getMessage());
            abort(404, 'User creation failed. ' . $exception->getMessage());
            throw $exception;
        }

        return response()->noContent(201);

    }

    /**
     * Display the specified resource.
     *
     * @param ShowUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function show(ShowUserRequest $request, User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user->update($request->safe()->all());
        } catch (Throwable $exception) {
            Log::info('User update failed. ' . $exception->getMessage());
            abort(404, 'User update failed. ' . $exception->getMessage());
            throw $exception;
        }

        return UserResource::make($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::info('User deletion failed. ' . $exception->getMessage());
            abort(404, 'User deletion failed. ' . $exception->getMessage());
            throw $exception;
        }

        DB::commit();

        return response()->noContent();

    }
}
