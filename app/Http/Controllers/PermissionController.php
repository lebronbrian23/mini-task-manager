<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PermissionController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('permissions/ListPermissions');
    }

    /** Fetch a list of permissions */
    public function getPermissions()
    {
        $this->authorize('view-any', Permission::class);;
        $permissions = Permission::all();

        return response()->json(['permissions' => $permissions], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('permissions/AddPermissionForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {

        $this->authorize('create', Permission::class);

        $request->validated();

        Permission::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Permission Added'], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Inertia::render('permissions/ShowPermission', ['permission_id' => $id]);
    }

    /**
     * Fetch a single permission
     */
    public function getPermission($id)
    {

        $permission = Permission::findorFail($id);

        $this->authorize('view-any', $permission);

        return response()->json(['permission' => $permission], 200);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return Inertia::render('permissions/EditPermissionForm', ['permission_id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        $request->validated();

        $permission = Permission::where('id' , $id)->first();

        $this->authorize('update', $permission);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Permission updated.', 'permission' => $permission],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::where('id' , $id)->first();

        $this->authorize('delete', $permission);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found.'], 400);
        }
        $permission->delete();

        return response()->json(['message' => 'Permission deleted.'], 200);
    }

    /**
     * Restore soft deleted permissions
     */
    public function restore($id)
    {
        $permission = Permission::withTrashed()->findOrFail($id);

        $this->authorize('restore', $permission);

        $permission->restore();

        return response()->json(['message' => 'Permission restored'], 200);
    }

    /**
     * Delete permanently soft deleted permissions
     */
    public function deletePermanently($id)
    {
        $permission = Permission::findOrFail($id);

        $this->authorize('force-delete', $permission);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 400);
        }
        $permission->forceDelete();

        return response()->json(['message' => 'Permission deleted permanently'], 200);
    }


}
