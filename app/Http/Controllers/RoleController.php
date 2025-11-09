<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class RoleController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     *
     */
    public function getRoles()
    {
        $this->authorize('view-any', Role::class);
        return Role::all();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia::render('');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {

        $this->authorize('create', Role::class);

        $request->validated();

        Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Role Added'], 200);

    }

    /**
     * @ AI
     * @param $id
     * @return mixed
     */
    public function getRole($id)
    {
        $role = Role::findorfail($id);
        $this->authorize('view-any', $role);
        return $role;
    }
    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
