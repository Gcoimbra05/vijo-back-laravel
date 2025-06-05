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
use App\Models\User;
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
            ->where(function($query) {
                $query->whereNotNull('contact_id')
                      ->orWhereNotNull('group_id');
            })
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

            // add catalog details
            $catalog = Catalog::select('emoji', 'title')
                ->where('id', $request->catalog_id)
                ->first();

            $request->emoji = $catalog ? $catalog->emoji : '';
            $request->catalog_title = $catalog ? $catalog->title : '';

            $toRequestsData[] = [
                'request'    => $request,
                'contacts'   => $contacts,
                'groups'     => $groups,
                'is_grouped' => $isGrouped,
            ];
        }

        $fromRequests = VideoRequest::with('latestVideo')
            ->where('ref_user_id', $userId)
            ->where(function($query) {
                $query->whereNotNull('contact_id')
                      ->orWhereNotNull('group_id');
            })
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

            $catalog = Catalog::select('emoji', 'title')
                ->where('id', $request->catalog_id)
                ->first();

            $request->emoji = $catalog ? $catalog->emoji : '';
            $request->catalog_title = $catalog ? $catalog->title : '';

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
            $filename = uniqid() . '.' . $file->guessExtension();
            $tmpPath = $file->storeAs('temp', $filename, 'local');
            $fullTmpPath = storage_path('app/' . $tmpPath);

            ProcessVideoUpload::dispatch(
                $videoRequest->id,
                $fullTmpPath,
                $request->input('video_duration'),
                $filename
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
            $filename = uniqid() . '.' . $file->guessExtension();
            $tmpPath = $file->storeAs('temp', $filename, 'local');
            $fullTmpPath = storage_path('app/' . $tmpPath);

            ProcessVideoUpload::dispatch(
                $videoRequest->id,
                $fullTmpPath,
                $request->input('video_duration'),
                $filename
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
                $contact = Contact::select('id as contact_id', 'first_name', 'last_name', 'email', 'mobile')
                    ->where('id', $req->contact_id)
                    ->first();
                if ($contact) {
                    $contacts[] = $contact;
                }
            }

            // Groups
            $groups = [];
            if ($req->group_id) {
                $group = ContactGroup::select('id as group_id', 'name as group_name')
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

        $catalog = Catalog::with(['parentCatalog', 'category', 'videoType'])->find($catalogId);
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

        $recordTime = $catalog->max_record_time ?? '60';
        $minRecordTime = $catalog->min_record_time ?? '15';
        $parentCatalogId = $catalog->parent_catalog_id ?? '0';

        $vtMetricNo = $vtKpiNo = $vtKpiMetrics = 0;
        if ($catalog->videoType) {
            $vtMetricNo = (int) ($catalog->videoType->metric_no ?? 0);
            $vtKpiNo    = (int) ($catalog->videoType->kpi_no ?? 0);
            $vtKpiMetrics = ($vtKpiNo > 0) ? floor($vtMetricNo / $vtKpiNo) : 0;
        }

        $questions = CatalogQuestionController::getQuestionsByCatalogId($catalog, $vtKpiMetrics, $vtKpiNo);

        $userTags = [
            [
                'id' => '200',
                'name' => 'Mindset'
            ]
        ];

        $videoType = [
            'metrics' => $vtMetricNo,
            'kpis' => $vtKpiNo,
            'kpi_metrics' => $vtKpiMetrics
        ];

        return response()->json([
            'status' => true,
            'message' => '',
            'results' => [
                'parent_catalog_id' => (string)$parentCatalogId,
                'catalog_id' => (string)$catalogId,
                'request_id' => $videoRequest->id,
                'record_date' => now()->toDateString(),
                'video_types' => $videoType,
                'video_type_id' => $catalog->video_type_id,
                'min_record_time' => (string)$minRecordTime,
                'record_time' => (string)$recordTime,
                'questions' => $questions,
                'userTags' => $userTags,
            ]
        ]);
    }

    public static function getMyVideoRequests()
    {
        $userId = Auth::id();

        $catalogs = Catalog::with(['videoType'])
            ->whereIn('id', function($query) use ($userId) {
                $query->select('catalog_id')
                    ->from('video_requests')
                    ->where(function($q) use ($userId) {
                        $q->where('user_id', $userId)
                          ->orWhere('ref_user_id', $userId);
                    });
            })
            ->where(function($query) {
                $query->whereNull('parent_catalog_id')
                      ->orWhere('parent_catalog_id', 0);
            })
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->orderBy('admin_order')
            ->get();

        $results = $catalogs->map(function($catalog) {
            $videoType = $catalog->videoType;
            $metric_no = $videoType ? (int)$videoType->metric_no : 0;
            $kpi_no = $videoType ? (int)$videoType->kpi_no : 1;
            $kpi_metrics = ($kpi_no > 0) ? floor($metric_no / $kpi_no) : 0;

            return [
                'catalog_id'      => (string)$catalog->id,
                'title'           => $catalog->title,
                'description'     => $catalog->description,
                'mediaId'         => (string)($mediaId ?? 0),
                'isPremium'       => (string)($catalog->is_premium ?? 0),
                'isMultipart'     => (string)($catalog->is_multipart ?? 0),
                'catalogPrograms' => [],
                'catalogEmoji'    => $catalog->emoji ?? '',
                'journal_type_id' => (string)($catalog->video_type_id ?? ''),
                'metric_no'       => (string)$metric_no,
                'kpi_no'          => (string)$kpi_no,
                'dashboard_id'    => '',
                'kpi_metrics'     => $kpi_metrics,
            ];
        })->toArray();

        return $results;
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

    public function getVideoGalleries(Request $request)
    {
        $userId = Auth::id();

        $allRequests = VideoRequest::with(['latestVideo', 'catalog.category', 'user'])
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('ref_user_id', $userId);
            })
            ->get();

        $requestData = $allRequests->map(function($req) {
            $video = $req->latestVideo;
            $catalog = $req->catalog;
            $user = $req->user;

            return [
                'id'                  => $req->id,
                'user_id'             => $req->user_id,
                'journal_title'       => $req->title ?? ($catalog->title ?? ''),
                'ref_user_id'         => $req->ref_user_id ?? 0,
                'journal_type'        => $req->type ?? 'daily',
                'recommendation_id'   => $req->recommendation_id ?? '',
                'category_name'       => $catalog && $catalog->category ? $catalog->category->name : '',
                'makeJournalPrivate'  => $req->makeJournalPrivate ?? 0,
                'rrc_video1'          => $video ? $video->video_name : '',
                'rrc_video1_thumb'    => $video ? $video->thumbnail_name : '',
                'video'               => $video ? $video->video_url : '',
                'video_thumb'         => $video ? $video->thumbnail_url : '',
                'recordedBy'          => $req->user_id == $req->ref_user_id ? 'self' : 'other',
                'parent_catalog_id'   => $catalog->parent_catalog_id ?? 0,
                'cp_id'               => $catalog->cp_id ?? 0,
                'created_at'          => $req->created_at ? date('M d, Y', strtotime($req->created_at)) : '',
                'mediaId'             => $video ? $video->id : 0,
                'catalogEmoji'        => $catalog->emoji ?? '',
                'user_name'           => $user ? ($user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) : '',
            ];
        })->toArray();

        return response()->json([
            'status' => true,
            'message' => '',
            'results' => $requestData
        ]);
    }

    public function getVideoDetail(Request $request, $id = 0)
    {
        $userId = Auth::id();

        if ($id <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Id parameter is required',
                'results' => null
            ], 400);
        }

        // Fetch the VideoRequest (journal) with related catalog, category, latest video, and user
        $videoRequest = VideoRequest::with([
            'catalog.category',
            'latestVideo',
            'user'
        ])->where('id', $id)
          ->where(function($q) use ($userId) {
              $q->where('user_id', $userId)
                ->orWhere('ref_user_id', $userId);
          })
          ->first();

        if (!$videoRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Journal not found or access denied.',
                'results' => null
            ], 404);
        }

        // Fetch contacts and groups shared via video_requests
        $contacts = [];
        $groups = [];

        if ($videoRequest) {
            // Contacts: requests with same catalog_id, same user_id, filled contact_id and null group_id
            $contacts = Contact::select('contacts.id as contact_id', 'contacts.first_name', 'contacts.last_name', 'contacts.email', 'contacts.mobile')
                ->join('video_requests as vr', function($join) use ($videoRequest) {
                    $join->on('contacts.id', '=', 'vr.contact_id')
                        ->where('vr.catalog_id', $videoRequest->catalog_id)
                        ->where('vr.user_id', $videoRequest->user_id)
                        ->whereNull('vr.group_id');
                })
                ->distinct()
                ->get();

            // Groups: requests with same catalog_id, same user_id, filled group_id
            $groups = ContactGroup::select('contact_groups.id as group_id', 'contact_groups.name as group_name')
                ->join('video_requests as vr', function($join) use ($videoRequest) {
                    $join->on('contact_groups.id', '=', 'vr.group_id')
                        ->where('vr.catalog_id', $videoRequest->catalog_id)
                        ->where('vr.user_id', $videoRequest->user_id)
                        ->whereNotNull('vr.group_id');
                })
                ->distinct()
                ->get();
        }

        $catalog = $videoRequest->catalog;
        $category = $catalog ? $catalog->category : null;
        $video = $videoRequest->latestVideo;

        $transcriptions = [
            [
                'id' => 1,
                'text' => 'Today was an incredible day at work. I finally solved that technical problem that had been bothering me for weeks.',
                'thumb' => 'https://placehold.co/300x200/0066cc/ffffff?text=Work+Day',
                'emoji' => 'U+1F4AA', // Flexed Biceps
                'emotion_score' => 0.85,
                'answer' => 'Breakthrough at work',
                'emotion' => 'proud'
            ],
            [
                'id' => 2,
                'text' => 'I had a meaningful conversation with an old friend. We reconnected after years and it felt like no time had passed.',
                'thumb' => 'https://placehold.co/300x200/ff9900/ffffff?text=Friendship',
                'emoji' => 'U+1F64F', // Folded Hands
                'emotion_score' => 0.92,
                'answer' => 'Reconnecting',
                'emotion' => 'happy'
            ],
            [
                'id' => 3,
                'text' => 'The presentation didn\'t go as planned. I was nervous and forgot some key points. I need to practice more next time.',
                'thumb' => 'https://placehold.co/300x200/cc3300/ffffff?text=Presentation',
                'emoji' => 'U+1F4C4', // Page Facing Up
                'emotion_score' => 0.31,
                'answer' => 'Presentation struggles',
                'emotion' => 'disappointed'
            ]
        ];

        $journalEmotionalData = [
            'emotional_insights' => [
                [
                    'emotion' => 'confidence',
                    'score' => 0.82,
                    'description' => 'Your confidence appears to be strong when discussing your work achievements and problem-solving abilities. You express satisfaction in overcoming technical challenges.'
                ],
                [
                    'emotion' => 'determination',
                    'score' => 0.76,
                    'description' => 'There\'s a notable sense of determination in your approach to difficult tasks, showing persistence even when facing obstacles.'
                ],
                [
                    'emotion' => 'curiosity',
                    'score' => 0.69,
                    'description' => 'You demonstrate intellectual curiosity, particularly about learning new technologies and exploring different solutions to problems.'
                ],
                [
                    'emotion' => 'anxiety',
                    'score' => 0.34,
                    'description' => 'Some mild anxiety appears when mentioning deadlines and expectations, though it doesn\'t seem to overwhelm your overall positive outlook.'
                ],

                'series' => ['82', '76', '69', '34', '22', '18', '12', '5'],
                'average' => ['65', '58', '52', '42', '38', '25', '22', '10'],
                'labels' => [
                    'Confidence',
                    'Determination',
                    'Curiosity',
                    'Anxiety',
                    'Frustration',
                    'Doubt',
                    'Uncertainty',
                    'Hesitation'
                ],
            ],

            'emotional_outcomes' => [
                [
                    'outcome_type' => 'professional growth',
                    'strength' => 'high',
                    'description' => 'Your emotional state indicates strong potential for continued professional development. The confidence you express in your abilities suggests you\'re likely to take on increasingly challenging projects.'
                ],
                [
                    'outcome_type' => 'work satisfaction',
                    'strength' => 'medium-high',
                    'description' => 'Your emotional response to work accomplishments suggests good job satisfaction, though there may be room to find even greater fulfillment through more diverse projects.'
                ],
                [
                    'outcome_type' => 'stress management',
                    'strength' => 'medium',
                    'description' => 'While you generally handle work pressure well, developing additional stress management techniques might help during particularly demanding periods.'
                ]
            ],

            'final_video_transcript' => "Today was a really productive day at work. I finally managed to solve that bug that's been affecting our main feature for the past week. It turned out to be related to an edge case in data validation that nobody had anticipated. I spent most of the morning digging through the codebase and eventually found where the problem was happening. The fix itself was actually pretty simple once I understood the root cause.\n\nI felt really good about sharing the solution with my team during our afternoon standup. My project manager was particularly impressed with how quickly I was able to isolate the issue. This kind of problem-solving is exactly why I enjoy software development so much - there's always a puzzle to solve, and finding the solution is incredibly satisfying.\n\nI'm looking forward to tackling our next sprint planning tomorrow. I have some ideas about how we can improve our testing process to catch these kinds of issues earlier in the development cycle. Overall, today reminded me why I chose this career path - the challenges are real, but overcoming them is so rewarding.",

            'summaryReport' => [
                'key_points' => [
                    "Successfully debugged a critical issue affecting a main product feature",
                    "Solution involved identifying an unexpected edge case in data validation",
                    "Shared findings with team during afternoon standup meeting",
                    "Received positive feedback from project manager",
                    "Planning to suggest improvements to testing processes"
                ],
                'mood_analysis' => "Primarily positive with high satisfaction from problem-solving success. Confident and optimistic about future work.",
                'time_references' => [
                    'past' => ["debugging experience", "discovering solution"],
                    'present' => ["feeling accomplished", "enjoying development work"],
                    'future' => ["sprint planning", "improving testing processes"]
                ]
            ],

            'gptSummary' => "The journal entry captures a moment of professional triumph as the author successfully resolved a complex technical issue that had been impacting a key feature. There's a clear sense of satisfaction and validation, especially when sharing the solution with colleagues and receiving recognition. The experience seems to have reinforced the author's career choice and passion for problem-solving. Looking ahead, they're motivated to improve team processes and take on new challenges. Overall, this represents a positive peak experience in their professional journey, balancing the difficulties of technical work with the rewards of overcoming obstacles."
        ];

        $data = [
            'id'                => $videoRequest->id,
            'user_id'           => $videoRequest->user_id,
            'journal_title'     => $videoRequest->title ?? ($catalog->title ?? ''),
            'ref_user_id'       => $videoRequest->ref_user_id ?? 0,
            'journal_type'      => $videoRequest->type ?? 'daily',
            'recommendation_id' => $videoRequest->recommendation_id ?? '',
            'category_name'     => $category ? $category->name : '',
            'journal_tags'      => $videoRequest->tags ?? '',
            'makeJournalPrivate'=> $videoRequest->makeJournalPrivate ?? 0,
            'rrc_video1'        => $video ? $video->video_name : '',
            'rrc_video1_thumb'  => $video ? $video->thumbnail_name : '',
            'video'             => $video ? $video->video_url : '',
            'video_thumb'       => $video ? $video->thumbnail_url : '',
            'user_tags'         => '',
            'outcomes'          => '',
            'emotions'          => '',
            'transcription'     => $transcriptions,
            'emotional_insights' => $journalEmotionalData['emotional_insights'],
            'emotional_outcomes' => $journalEmotionalData['emotional_outcomes'],
            'final_video_transcript' => $journalEmotionalData['final_video_transcript'],
            'summaryReport' => $journalEmotionalData['summaryReport'],
            'gptSummary' => $journalEmotionalData['gptSummary'],
            'journal_type_id'   => $catalog->video_type_id ?? '',
            'catalog_id'        => $catalog->id ?? '',
            'catalog_name'      => $catalog->title ?? '',
            'created_at'        => $videoRequest->created_at ? $videoRequest->created_at->format('M d, Y') : '',
            'recordedBy'        => $videoRequest->user_id == $userId ? 'self' : ($videoRequest->ref_user_id == $userId ? 'recordResponse' : 'shared'),
            'contacts'          => $contacts,
            'groups'            => $groups,
        ];

        return response()->json([
            'status' => true,
            'message' => '',
            'results' => [
                'journal_data' => [$data]
            ]
        ]);
    }

    public function makeVideoRequest(Request $request, $id = 0)
    {
        $user = Auth::user();
        $userId = $user->id ?? null;

        $request->validate([
            'catalog_id'  => 'required|integer|min:1|exists:catalogs,id',
            'contact_ids' => 'nullable|array',
            'contact_ids.*' => 'integer|exists:contacts,id',
            'group_ids'   => 'nullable|array',
            'group_ids.*' => 'integer|exists:contact_groups,id',
            'note'        => 'required|string',
            'recordUrl'   => 'required|string',
        ]);

        $catalogId = $request->input('catalog_id');
        $contactIds = $request->input('contact_ids', []);
        $groupIds = $request->input('group_ids', []);
        $note = $request->input('note');
        $recordUrl = $request->input('recordUrl');

        $catalog = Catalog::find($catalogId);
        if (!$catalog) {
            return response()->json([
                'status' => false,
                'message' => 'Catalog not found.',
                'results' => null
            ], 404);
        }

        // Monta lista de contatos a partir dos IDs e dos grupos
        $phoneNumbers = [];

        if (!empty($contactIds)) {
            $contacts = Contact::whereIn('id', $contactIds)->get();
            foreach ($contacts as $contact) {
                $phoneNumbers[] = [
                    'contact_id'   => $contact->id,
                    'country_code' => $contact->country_code,
                    'mobile'       => $contact->mobile,
                    'email'        => $contact->email,
                    'first_name'   => $contact->first_name,
                    'last_name'    => $contact->last_name,
                    'group_id'     => null
                ];
            }
        }

        if (!empty($groupIds)) {
            $groupContacts = Contact::whereHas('groups', function($q) use ($groupIds) {
                $q->whereIn('contact_groups.id', $groupIds);
            })->get();

            foreach ($groupContacts as $contact) {
                foreach ($contact->groups as $group) {
                    if (in_array($group->id, $groupIds)) {
                        $phoneNumbers[] = [
                            'contact_id'   => $contact->id,
                            'country_code' => $contact->country_code,
                            'mobile'       => $contact->mobile,
                            'email'        => $contact->email,
                            'first_name'   => $contact->first_name,
                            'last_name'    => $contact->last_name,
                            'group_id'     => $group->id
                        ];
                    }
                }
            }
        }

        $phoneNumbers = collect($phoneNumbers)->unique(function($item) {
            return $item['contact_id'] . '-' . ($item['group_id'] ?? '0');
        })->values();

        $requestIds = [];
        $skippedContacts = [];

        foreach ($phoneNumbers as $row) {
            // Do not create for the current user
            if ($user->mobile && $row['mobile'] && $user->mobile == $row['mobile']) {
                $skippedContacts[] = $row['contact_id'];
                continue;
            }

            // Check if a similar VideoRequest already exists
            $alreadyExists = VideoRequest::where('catalog_id', $catalogId)
                ->where('contact_id', $row['contact_id'])
                ->where(function($q) use ($row) {
                    if ($row['group_id']) {
                    $q->where('group_id', $row['group_id']);
                    } else {
                    $q->whereNull('group_id');
                    }
            })->exists();

            if ($alreadyExists) {
                $skippedContacts[] = $row['contact_id'];
                continue;
            }

            // Find ref_user_id if a user exists with the same mobile
            $refUserId = null;
            if ($row['mobile']) {
                $refUser = User::where('mobile', $row['mobile'])->first();
                $refUserId = $refUser ? $refUser->id : null;
            }

            $videoRequest = VideoRequest::create([
                'user_id'         => $userId,
                'catalog_id'      => $catalogId,
                'contact_id'      => $row['contact_id'],
                'group_id'        => $row['group_id'],
                'ref_first_name'  => $row['first_name'],
                'ref_last_name'   => $row['last_name'],
                'ref_country_code'=> $row['country_code'],
                'ref_mobile'      => $row['mobile'],
                'ref_email'       => $row['email'],
                'ref_note'        => $note,
                'ref_user_id'     => $refUserId,
                'status'          => 'Pending',
                'type'            => 'request',
            ]);

            $requestIds[] = $videoRequest->id;

            // Notifications
            $full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $url = rtrim($recordUrl, "/") . '/' . base64_encode($videoRequest->id);

            // Send email
            if (!empty($row['email'])) {
                try {
                    Mail::to($row['email'])->send(new VideoRequestShared($videoRequest, $full_name, $url));
                } catch (\Exception $e) {
                    // Log::error('Error sending email: ' . $e->getMessage());
                }
            }

            // Send SMS
            if (!empty($row['country_code']) && !empty($row['mobile'])) {
                try {
                    $twilio = new TwilioService();
                    $twilio->sendSms('+' . $row['country_code'] . $row['mobile'], "Hello {$full_name}, you have received a new video request. Access: {$url}");
                } catch (\Exception $e) {
                    // Log::error('Error sending SMS: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => !empty($skippedContacts)
                ? "Request(s) created/updated successfully. Some contacts were skipped as they already received this request or are the logged user."
                : "Request(s) created/updated successfully.",
            'results' => [
                'catalog_id'       => $catalogId,
                'request_ids'      => $requestIds,
                'skipped_contacts' => $skippedContacts
            ]
        ]);
    }
}
