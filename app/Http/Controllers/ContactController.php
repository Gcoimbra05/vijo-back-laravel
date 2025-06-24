<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');
        $search = $request->query('search');
        $perPage = $request->query('limit', 15);
        $page = (int) $request->query('page', 1);
        $startAt = $perPage * ($page - 1);

        $userId = Auth::id() ?? $request->user_id ?? null;
        $result = [];

        if ($type === 'contacts' || $type === 'all') {
            $contactsQuery = Contact::where('user_id', $userId)
                ->orderBy('first_name', 'ASC')
                ->orderBy('last_name', 'ASC');

            if ($search) {
                $contactsQuery->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('mobile', 'like', "%$search%");
                });
            }

            $contactsResult = $contactsQuery->skip($startAt)->take($perPage)->get()->toArray();

            foreach ($contactsResult as &$contact) {
                unset($contact['password']);
            }

            $result = array_merge($result, $contactsResult);
        }

        if ($type === 'groups' || $type === 'all') {
            $groupsQuery = ContactGroup::where('user_id', $userId);

            if ($search) {
                $groupsQuery->where('name', 'like', "%$search%");
            }

            $groupsResult = $groupsQuery->orderBy('name', 'ASC')
                ->skip($startAt)->take($perPage)->get(['id', 'name'])->toArray();

            $result = array_merge($result, $groupsResult);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contacts and groups retrieved successfully.',
            'data' => $result,
        ]);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $contact = Contact::find($id);

        if (!$contact || $contact->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $groups = $contact->groups()->get(['id', 'name']);

        $contactData = $contact->toArray();
        $contactData['groups'] = $groups;

        return response()->json([
            'success' => true,
            'message' => 'Contact retrieved successfully.',
            'data' => $contactData,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $groups = !empty($data['groups']) ? $data['groups'] : [];
        unset($data['groups']);

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'mobile'       => 'required|string|max:15',
            'email'        => 'nullable|email|max:255',
        ]);

        $userId = Auth::id();
        $validated['user_id'] = $userId;
        if (empty($validated['country_code'])) {
            $validated['country_code'] = '1';
        }
        $existingContact = Contact::where('country_code', $validated['country_code'])
            ->where('mobile', $validated['mobile'])
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existingContact) {
            return response()->json([
                'success' => false,
                'message' => 'A contact with this phone number already exists.',
                'data' => null,
            ], 400);
        }

        $contact = Contact::create($validated);

        if (!empty($groups)) {
            foreach ($groups as $groupId) {
                $group = ContactGroup::where('id', $groupId)
                    ->where('user_id', $userId)
                    ->first();
                if (!$group) {
                    return response()->json([
                        'success' => false,
                        'message' => "Group ID {$groupId} does not belong to the user.",
                        'data' => null,
                    ], 400);
                }
            }
            $contact->groups()->sync($groups);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully.',
            'data' => $contact,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $contact = Contact::find($id);

        if (!$contact || $contact->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $data = $request->all();
        $groups = $data['groups'] ?? [];
        unset($data['groups']);

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'mobile'       => 'required|string|max:15',
            'email'        => 'nullable|email|max:255',
        ]);

        if (array_key_exists('country_code', $validated) && is_null($validated['country_code'])) {
            unset($validated['country_code']);
        }

        // Validar grupos
        if (!empty($groups)) {
            foreach ($groups as $groupId) {
                $group = ContactGroup::where('id', $groupId)
                    ->where('user_id', $userId)
                    ->first();
                if (!$group) {
                    return response()->json([
                        'success' => false,
                        'message' => "Group ID {$groupId} does not belong to the user.",
                        'data' => null,
                    ], 400);
                }
            }
        }

        $contact->update($validated);

        // Sincronizar grupos
        if (!empty($groups)) {
            $contact->groups()->sync($groups);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully.',
            'data' => $contact,
        ]);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $contact = Contact::find($id);

        if (!$contact || $contact->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found or does not belong to the user.',
                'data' => null,
            ], 404);
        }

        $contact->groups()->detach();

        if ($contact->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully.',
                'data' => ['id' => $id],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete contact.',
            'data' => null,
        ], 500);
    }

    public function createMultiple(Request $request)
    {
        $data = $request->all();

        $requiredFields = ['first_name', 'last_name', 'mobile'];
        $allowedFields = array_merge($requiredFields, ['email']);
        $filteredData = [];
        $hasDuplicates = false;
        $userId = Auth::id();

        foreach ($data as $contact) {
            // Verificar se os campos obrigatórios estão presentes
            foreach ($requiredFields as $field) {
                if (empty($contact[$field])) {
                    return response()->json([
                        'success' => false,
                        'message' => "The {$field} field is required.",
                        'data' => null,
                    ], 400);
                }
            }

            if (empty($contact['country_code'])) {
                $contact['country_code'] = '1';
            }

            $existingContact = Contact::where('country_code', $contact['country_code'])
                ->where('mobile', $contact['mobile'])
                ->where('user_id', $userId)
                ->first();

            if ($existingContact) {
                $hasDuplicates = true;
                continue;
            }

            $filtered = array_intersect_key($contact, array_flip($allowedFields));
            $filtered['country_code'] = $contact['country_code'];
            $filtered['user_id'] = $userId;
            $filteredData[] = $filtered;
        }

        if (!empty($filteredData)) {
            Contact::insert($filteredData);
        }

        $message = 'Contacts created successfully.';
        if ($hasDuplicates) {
            $message .= ' Some contacts already existed and were not added again.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'created' => $filteredData
            ]
        ], 201);
    }
}