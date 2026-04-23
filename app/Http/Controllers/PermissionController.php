<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController
{
    /**
     * Get all permissions
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        return response()->json($permissions);
    }

    /**
     * Store new permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'description' => 'required|string',
            'group' => 'required|string',
        ]);

        $permission = Permission::create($request->all());

        return response()->json($permission, 201);
    }

    /**
     * Get specific permission
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    /**
     * Update permission
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'required|string',
            'group' => 'required|string',
        ]);

        $permission->update($request->all());

        return response()->json($permission);
    }

    /**
     * Delete permission
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
