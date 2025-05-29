<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $search = $request->query('search');
        $perPage = $request->query('limit', 15);
        $page = (int) $request->query('page', 1);

        $query = ContactGroup::where('user_id', $userId)->orderBy('name', 'ASC');

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $groups = $query->skip($perPage * ($page - 1))->take($perPage)->get();

        // Adiciona os contatos de cada grupo
        foreach ($groups as $group) {
            $group->contacts = $group->contacts()->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Groups retrieved successfully.',
            'data' => $groups,
        ]);
    }

    public function show($id)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $group = ContactGroup::where('id', $id)->where('user_id', $userId)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $groupData = $group->toArray();
        $groupData['contacts'] = $group->contacts()->get();

        return response()->json([
            'success' => true,
            'message' => 'Group retrieved successfully.',
            'data' => $groupData,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $contacts = $data['contacts'] ?? [];
        unset($data['contacts']);

        $userId = Auth::id();
        $data['user_id'] = $userId;

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $existingGroup = ContactGroup::where('name', $data['name'])
            ->where('user_id', $userId)
            ->first();

        if ($existingGroup) {
            return response()->json([
                'success' => false,
                'message' => 'A group with this name already exists for the user.',
                'data' => null,
            ], 400);
        }

        $group = ContactGroup::create($data);

        if (!empty($contacts)) {
            $group->contacts()->sync($contacts);
        }

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully.',
            'data' => ['id' => $group->id],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $group = ContactGroup::where('id', $id)->where('user_id', $userId)->first();
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $data = $request->all();
        $contacts = $data['contacts'] ?? [];
        unset($data['contacts']);

        if (!empty($data['name'])) {
            $request->validate([
                'name' => 'required|string|max:100',
            ]);
            $group->name = $data['name'];
            $group->save();
        }

        if (!empty($contacts)) {
            $group->contacts()->sync($contacts);
        }

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully.',
            'data' => ['id' => $group->id],
        ]);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $group = ContactGroup::where('id', $id)->where('user_id', $userId)->first();
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $group->contacts()->detach();

        if ($group->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully.',
                'data' => ['id' => $id],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete group.',
            'data' => null,
        ], 400);
    }

    public function removeContact($groupId = null, $contactId = null)
    {
        if (!$groupId || !$contactId) {
            return response()->json([
                'success' => false,
                'message' => 'Group ID and Contact ID are required.',
                'data' => null,
            ], 400);
        }

        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $group = ContactGroup::where('id', $groupId)->where('user_id', $userId)->first();
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $exists = $group->contacts()->where('contacts.id', $contactId)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'The contact is not associated with the specified group.',
                'data' => null,
            ], 404);
        }

        $group->contacts()->detach($contactId);

        return response()->json([
            'success' => true,
            'message' => 'Contact removed from group successfully.',
            'data' => [
                'group_id' => $groupId,
                'contact_id' => $contactId,
            ],
        ]);
    }
}