<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Models\User;
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
        return inertia::render('roles/ListRoles');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
        return inertia::render('roles/AddRoleForm');
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
     * Retrieve the specified role by its ID.
     * @param int $id
     * @return \App\Models\Role
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
    public function show($id)
    {
        return inertia::render('roles/ShowRole', ['role_id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Authorize against the specific role instance
        $this->authorize('update', $role);
        return inertia::render('roles/EditRoleForm');
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {

        $update_role = Role::where('id', $role->id)->first();

        $this->authorize('update', $update_role);

        $request->validated();

        $update_role->update([
           'name' => $request->name,
           'description' => $request->description
        ]);

        return response()->json(['message' => 'Role updated.', 'role' => $update_role ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( $id)
    {
        $role = Role::findorfail($id);

        $this->authorize('delete', $role);

        if ( $role ) {
            $role->delete();

            return response()->json(['message' => 'Role deleted'], 200);
        } else {
            return response()->json(['message' => 'Role not found.'], 200);
        }
    }

    /**
     *  restore a soft deleted role
     */
    public function restore($id)
    {
        $restore_role = Role::withTrashed()->findOrFail($id);

        $this->authorize('restore', $restore_role);

        $restore_role->restore();

        return response()->json(['message' => 'Role restored'], 200);
    }

    /**
     *  Delete role permanently
     */
    public function delete_permanently($id)
    {
        $role = Role::findorFail($id);

        $this->authorize('force-delete', $role);

        if ($role) {
            $role->delete();

            $role->forceDelete();

            return response()->json(['message' => 'Role deleted permanently.'], 200);
        } else  {
            return response()->json(['message' => 'Role not found.' ], 200);
        }
    }

}
