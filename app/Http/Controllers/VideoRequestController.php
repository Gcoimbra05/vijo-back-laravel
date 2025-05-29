<?php

namespace App\Http\Controllers;

use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Jobs\ProcessVideoRequest;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessVideoUpload;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;
use App\Mail\VideoRequestShared;
use App\Models\Catalog;
use App\Models\CatalogQuestion;
use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VideoRequestController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $userId = Auth::id();

        $toRequests = VideoRequest::with('latestVideo')
            ->where('user_id', $userId)
            ->get();

        $toRequestsData = [];
        foreach ($toRequests as $request) {
            // Contatos (contact_id não nulo, group_id nulo)
            $contacts = Contact::select('contacts.id as contact_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email', 'contacts.mobile')
                ->join('video_requests as vr', function($join) use ($request, $userId) {
                    $join->on('vr.contact_id', '=', 'contacts.id')
                         ->whereNull('vr.group_id')
                         ->where('vr.catalog_id', $request->catalog_id)
                         ->where('vr.user_id', $userId);
                })
                ->distinct()
                ->get();

            // Grupos (group_id não nulo)
            $groups = ContactGroup::select('contact_groups.id as group_id', 'contact_groups.name as group_name')
                ->join('video_requests as vr', function($join) use ($request, $userId) {
                    $join->on('vr.group_id', '=', 'contact_groups.id')
                         ->where('vr.catalog_id', $request->catalog_id)
                         ->where('vr.user_id', $userId);
                })
                ->distinct()
                ->get();

            $isGrouped = ($contacts->count() + $groups->count()) > 1;

            $toRequestsData[] = [
                'request'    => $request,
                'contacts'   => $contacts,
                'groups'     => $groups,
                'is_grouped' => $isGrouped,
            ];
        }

        $fromRequests = VideoRequest::with('latestVideo')
            ->where('ref_user_id', $userId)
            ->get();

        $fromRequestsData = [];
        foreach ($fromRequests as $request) {
            // Contatos (contact_id não nulo, group_id nulo)
            $contacts = Contact::select('contacts.id as contact_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email', 'contacts.mobile')
                ->join('video_requests as vr', function($join) use ($request, $userId) {
                    $join->on('vr.contact_id', '=', 'contacts.id')
                         ->whereNull('vr.group_id')
                         ->where('vr.catalog_id', $request->catalog_id)
                         ->where('vr.ref_user_id', $userId);
                })
                ->distinct()
                ->get();

            // Grupos (group_id não nulo)
            $groups = ContactGroup::select('contact_groups.id as group_id', 'contact_groups.name as group_name')
                ->join('video_requests as vr', function($join) use ($request, $userId) {
                    $join->on('vr.group_id', '=', 'contact_groups.id')
                         ->where('vr.catalog_id', $request->catalog_id)
                         ->where('vr.ref_user_id', $userId);
                })
                ->distinct()
                ->get();

            $isGrouped = ($contacts->count() + $groups->count()) > 1;

            $fromRequestsData[] = [
                'request'    => $request,
                'contacts'   => $contacts,
                'groups'     => $groups,
                'is_grouped' => $isGrouped,
            ];
        }

        $responseData = [
            'status'  => true,
            'message' => "",
            'results' => [
                'toRequests'   => $toRequestsData,
                'fromRequests' => $fromRequestsData
            ]
        ];

        return response()->json($responseData);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $videoRequest = VideoRequest::with(['latestVideo', 'catalog'])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('ref_user_id', $userId);
            })
            ->find($id);

        if (!$videoRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Video request not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video request retrieved successfully.',
            'data' => $videoRequest,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|integer',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $videoRequest = VideoRequest::create($data);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $tmpPath = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName(), 'local');
            $fullTmpPath = storage_path('app/' . $tmpPath);

            ProcessVideoUpload::dispatch(
                $videoRequest->id,
                $fullTmpPath,
                $request->input('video_duration'),
                $file->getClientOriginalName()
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Video request created successfully.',
            'data' => $videoRequest,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $videoRequest = VideoRequest::find($id);

        if (!$videoRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Video request not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'catalog_id' => 'required|integer',
            'type' => 'required|string|in:daily,request',
        ]);

        $data = $request->only(['catalog_id', 'type']);
        $data['user_id'] = Auth::id();
        $videoRequest->update($data);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $tmpPath = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName(), 'local');
            $fullTmpPath = storage_path('app/' . $tmpPath);

            ProcessVideoUpload::dispatch(
                $videoRequest->id,
                $fullTmpPath,
                $request->input('video_duration'),
                $file->getClientOriginalName()
            );
        }

        $catalog = Catalog::find($videoRequest->catalog_id);
        $videoRequest->journal_title = $catalog ? $catalog->title : null;
        $videoRequest->recommendation_id = "";

        return response()->json([
            'success' => true,
            'message' => 'Video request updated successfully.',
            'data' => $videoRequest,
        ]);
    }

    public function shareVideoRequests(Request $request)
    {
        $request->validate([
            'catalog_id'  => 'required|integer|min:1',
            'contact_ids' => 'nullable|array',
            'group_ids'   => 'nullable|array',
            'note'        => 'required|string',
            'recordUrl'   => 'required|string',
        ]);
        if (empty($request->input('contact_ids')) && empty($request->input('group_ids'))) {
            return response()->json([
                'success' => false,
                'message' => 'At least one of the fields contact_ids or group_ids must be provided.',
                'results' => null
            ], 422);
        }
        $user = Auth::user();
        $catalogId = $request->input('catalog_id');
        $note = $request->input('note');
        $recordUrl = $request->input('recordUrl');
        $contactIds = $request->input('contact_ids', []);
        $groupIds = $request->input('group_ids', []);

        $createdRequests = [];
        $skippedContacts = [];

        if (!empty($contactIds)) {
            foreach ($contactIds as $contactId) {
                $contact = Contact::find($contactId);
                if (!$contact) {
                    $skippedContacts[] = $contactId;
                    continue;
                }

                $result = $this->createVideoRequestToContact($contact, $catalogId, $note, $user);
                if ($result['created']) {
                    $createdRequests[] = $result['created'];
                }
                if ($result['skipped']) {
                    $skippedContacts[] = $result['skipped'];
                }
            }
        }

        if (!empty($groupIds)) {
            foreach ($groupIds as $groupId) {
                $group = ContactGroup::with('contacts')->find($groupId);
                if (!$group) {
                    continue;
                }
                foreach ($group->contacts as $contact) {
                    $result = $this->createVideoRequestToContact($contact, $catalogId, $note, $user);
                    if ($result['created']) {
                        $createdRequests[] = $result['created'];
                    }
                    if ($result['skipped']) {
                        $skippedContacts[] = $result['skipped'];
                    }
                }
            }
        }

        $this->sendRequestNotification($createdRequests, $recordUrl);

        $requestIds = collect($createdRequests)->pluck('id')->toArray();

        $responseData = [
            'success' => true,
            'message' => !empty($skippedContacts)
                ? "Request(s) created/updated successfully. Some contacts were skipped as they already received this request or are the logged user."
                : "Request(s) created/updated successfully.",
            'results' => [
                'catalog_id'       => $catalogId,
                'request_ids'      => $requestIds,
                'skipped_contacts' => $skippedContacts
            ]
        ];

        return response()->json($responseData, 201);
    }

    public function createVideoRequestToContact($contact, $catalogId, $note, $user)
    {
        if ($user->mobile == $contact->mobile) {
            return ['created' => null, 'skipped' => $contact->id];
        }

        $alreadyExists = VideoRequest::where('catalog_id', $catalogId)
            ->where('contact_id', $contact->id)
            ->exists();

        if ($alreadyExists) {
            return ['created' => null, 'skipped' => $contact->id];
        }

        $videoRequest = VideoRequest::create([
            'user_id'     => $user->id,
            'catalog_id'  => $catalogId,
            'contact_id'  => $contact->id,
            'ref_first_name' => $contact->first_name,
            'ref_last_name' => $contact->last_name,
            'ref_country_code' => $contact->country_code,
            'ref_mobile' => $contact->mobile,
            'ref_email' => $contact->email,
            'ref_note'    => $note,
            'status'      => 'Pending',
        ]);

        return ['created' => $videoRequest, 'skipped' => null];
    }

    public function sendRequestNotification($createdRequests, $recordUrl)
    {
        if (!empty($createdRequests)) {
            foreach ($createdRequests as $videoRequest) {
                if (!empty($videoRequest->ref_email)) {
                    Mail::to($videoRequest->ref_email)->send(new VideoRequestShared($videoRequest));
                }

                if (!empty($videoRequest->ref_country_code) && !empty($videoRequest->ref_mobile)) {
                    $fullPhoneNumber = $videoRequest->ref_country_code . $videoRequest->ref_mobile;
                    $twilio = new TwilioService();
                    $twilio->sendSms($fullPhoneNumber, "You have received a new video request. Check it out: {$recordUrl}");
                }
            }
        }
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $videoRequest = VideoRequest::with('videos')->find($id);

        if (!$videoRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Video request not found.',
                'data' => null,
            ], 404);
        }

        if ($videoRequest->user_id !== $userId && $videoRequest->ref_user_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 403);
        }

        foreach ($videoRequest->videos as $video) {
            if ($video->user_id === $userId) {
                $video->delete();
            }
        }

        $videoRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video request and related videos deleted successfully.',
            'data' => null,
        ]);
    }

    public function initProcess(Request $request, $id)
    {
        $userId = Auth::id();
        $videoRequest = VideoRequest::with(['latestVideo', 'catalog'])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('ref_user_id', $userId);
            })
            ->find($id);

        if (!$videoRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Video request not found.',
                'data' => null,
            ], 404);
        }

        ProcessVideoRequest::dispatch($videoRequest);

        return response()->json($videoRequest, 201);
    }

    public function cancelDeclineRecordRequest(Request $request)
    {
        $userId = Auth::id();

        $allowedStatuses = [
            'decline' => 'Reject',
            'cancel'  => 'Not Right Now'
        ];

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'read_status' => 'required|in:' . implode(',', array_keys($allowedStatuses)),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $requestId = $request->input('id');
        $videoRequest = VideoRequest::find($requestId);
        if ($videoRequest && ($videoRequest->user_id !== $userId && $videoRequest->ref_user_id !== $userId)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access to this request.'
            ], 403);
        }

        $readStatus = $request->input('read_status');
        $dbStatus = $allowedStatuses[$readStatus] ?? null;

        if (!$dbStatus) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid read status value.'
            ], 400);
        }

        if (!$videoRequest) {
            return response()->json([
                'status' => false,
                'message' => 'No request found for the given ID.'
            ], 404);
        }

        $catalogId = $videoRequest->catalog_id;
        $groupId = $videoRequest->group_id;

        if (!empty($groupId) && $readStatus === 'cancel' && $videoRequest->user_id === $userId) {
            VideoRequest::where('catalog_id', $catalogId)
                ->where('group_id', $groupId)
                ->where('status', 'Pending')
                ->update(['status' => $dbStatus]);

            return response()->json([
                'status' => true,
                'message' => 'Pending requests for the group have been successfully canceled.'
            ]);
        } else {
            $videoRequest->status = $dbStatus;
            $videoRequest->save();

            $msg_status = ($readStatus === 'decline') ? 'declined' : 'cancelled';

            return response()->json([
                'status'  => true,
                'message' => "Request {$msg_status} successfully"
            ]);
        }
    }

    public function getRelatedRequests(Request $request, $requestId = 0)
    {
        $userId = Auth::id();

        if ($requestId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Request ID is required.'
            ], 400);
        }

        // Fetch the main request
        $mainRequest = VideoRequest::where('id', $requestId)
            ->where('status', 1)
            ->where('read_status', '<>', 'Not Right Now')
            ->first();

        if (!$mainRequest) {
            return response()->json([
                'status' => false,
                'message' => 'No request found for the given ID.'
            ], 404);
        }

        $catalog_id = $mainRequest->catalog_id;

        // Fetch catalog details
        $catalogDetails = Catalog::select('id as catalog_id', 'emoji', 'description', 'title')
            ->where('id', $catalog_id)
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->first();

        // Fetch all related requests
        $relatedRequests = VideoRequest::where('catalog_id', $catalog_id)
            ->where('status', 1)
            ->where('user_id', $userId)
            ->whereNotNull('ref_mobile')
            ->where(function($q) {
                $q->whereNotNull('contact_id')
                  ->orWhereNotNull('group_id');
            })
            ->where('read_status', '<>', 'Not Right Now')
            ->orderBy('ref_first_name')
            ->orderBy('ref_last_name')
            ->get();

        if ($relatedRequests->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No related requests found.'
            ]);
        }

        $results = [];
        foreach ($relatedRequests as $req) {
            // Contacts
            $contacts = [];
            if ($req->contact_id) {
                $contact = \App\Models\Contact::select('id as contact_id', 'first_name', 'last_name', 'email', 'mobile')
                    ->where('id', $req->contact_id)
                    ->first();
                if ($contact) {
                    $contacts[] = $contact;
                }
            }

            // Groups
            $groups = [];
            if ($req->group_id) {
                $group = \App\Models\ContactGroup::select('id as group_id', 'name as group_name')
                    ->where('id', $req->group_id)
                    ->first();
                if ($group) {
                    $groups[] = $group;
                }
            }

            $results[] = [
                'request_id'      => $req->id,
                'ref_first_name'  => $req->ref_first_name,
                'ref_last_name'   => $req->ref_last_name,
                'ref_country_code'=> $req->ref_country_code,
                'ref_mobile'      => $req->ref_mobile,
                'ref_email'       => $req->ref_email,
                'ref_note'        => $req->ref_note,
                'read_status'     => $req->read_status,
                'created_at'      => $req->created_at,
                'contacts'        => $contacts,
                'groups'          => $groups,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Related requests retrieved successfully.',
            'catalog_details' => $catalogDetails,
            'results' => $results
        ]);
    }

    public function deleteVideoRequests(Request $request, $requestId = 0)
    {
        $userId = Auth::id();

        if ($requestId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Request ID is required.'
            ], 400);
        }

        // Busca o request principal
        $mainRequest = VideoRequest::where('id', $requestId)
            ->where('status', 1)
            ->where('read_status', '<>', 'Not Right Now')
            ->first();

        if (!$mainRequest) {
            return response()->json([
                'status' => false,
                'message' => 'No request found for the given ID.'
            ], 404);
        }

        $catalog_id = $mainRequest->catalog_id;

        VideoRequest::where('catalog_id', $catalog_id)
            ->where('user_id', $userId)
            ->whereNotNull('ref_mobile')
            ->where(function($q) {
                $q->whereNotNull('contact_id')
                  ->orWhereNotNull('group_id');
            })
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Requests deleted successfully.'
        ]);
    }

    public function startVideoRequest(Request $request)
    {
        $userId = Auth::id();
        $catalogId = $request->input('catalog_id');

        if (!$catalogId) {
            return response()->json([
                'status' => false,
                'message' => 'Catalog ID is required.'
            ], 400);
        }

        $catalog = Catalog::with(['parentCatalog', 'category'])->find($catalogId);
        if (!$catalog) {
            return response()->json([
                'status' => false,
                'message' => 'Catalog not found.'
            ], 404);
        }

        // Create the VideoRequest
        $videoRequest = VideoRequest::create([
            'user_id' => $userId,
            'catalog_id' => $catalogId,
            'status' => 'Pending',
        ]);

        $record_time = $catalog->max_record_time ?? '60';
        $min_record_time = $catalog->min_record_time ?? '15';
        $parent_catalog_id = $catalog->parent_catalog_id ?? '0';

        $questions = CatalogQuestionController::getQuestionsByCatalogId($catalogId);

        $userTags = [
            [
                'id' => '200',
                'name' => 'Mindset'
            ]
        ];

        $journal_types = [
            'metrics' => '0',
            'kpis' => '1',
            'kpi_metrics' => 0
        ];

        return response()->json([
            'status' => true,
            'message' => '',
            'results' => [
                'parent_catalog_id' => (string)$parent_catalog_id,
                'catalog_id' => (string)$catalogId,
                'request_id' => $videoRequest->id,
                'record_date' => now()->toDateString(),
                'journal_types' => $journal_types,
                'min_record_time' => (string)$min_record_time,
                'record_time' => (string)$record_time,
                'questions' => $questions,
                'userTags' => $userTags
            ]
        ]);
    }

    public static function getMyVideoRequests(Request $request)
    {
        $userId = Auth::id();

        // Fetch all distinct catalogs from the user's requests (as user_id or ref_user_id)
        $catalogs = Catalog::whereIn('id', function($query) use ($userId) {
                $query->select('catalog_id')
                    ->from('video_requests')
                    ->where(function($q) use ($userId) {
                        $q->where('user_id', $userId)
                          ->orWhere('ref_user_id', $userId);
                    });
            })
            ->where('parent_catalog_id', 0)
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->get();

        $results = $catalogs->map(function($catalog) {
            return [
                'catalog_id'      => (string)$catalog->id,
                'title'           => $catalog->title,
                'description'     => $catalog->description,
                'mediaId'         => '0',
                'isPremium'       => (string)($catalog->is_premium ?? 0),
                'isMultipart'     => '0',
                'catalogPrograms' => '0',
                'catalogEmoji'    => $catalog->emoji ?? '',
                'journal_type_id' => (string)($catalog->journal_type_id ?? ''),
                'metric_no'       => '0',
                'kpi_no'          => '1',
                'dashboard_id'    => '',
                'kpi_metrics'     => 0,
            ];
        })->toArray();

        return response()->json($results);
    }

    public function saveVideoRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer|min:1',
        ]);

        $requestId = $request->input('request_id');
        $journalName = $request->input('journal_name', '');
        $tags = $request->input('journal_tags', []);

        $categoryId = null;
        $videoRequest = VideoRequest::find($requestId);
        if ($videoRequest && $videoRequest->catalog_id) {
            $catalog = Catalog::find($videoRequest->catalog_id);
            if ($catalog && $catalog->category_id) {
                $categoryId = $catalog->category_id;
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Video request not found.'
            ], 404);
        }

        $tags = TagController::handleProvidedTags($tags, $categoryId);

        $videoRequest->title = $journalName;
        $videoRequest->tags = implode(',', $tags);
        $videoRequest->save();

        return response()->json([
            'status' => true,
            'message' => '',
            'results' => [
                'request_id' => (string)$videoRequest->id
            ]
        ]);
    }
}