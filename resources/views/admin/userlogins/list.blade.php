@extends('layouts.app')

@section('title', 'User Logins List')

@php
use Carbon\Carbon;
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>

    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>user id</th>
                    <th>Name</th>
                    <th>user agent</th>
                    <th>ip address</th>
                    <th>logged in at</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($userlogins) && $userlogins->count())
                    @foreach($userlogins as $key => $userlogin)
                        <tr>
                            <td>{{ optional($userlogin->user)->id ?? 'N/A' }}</td>
                            <td>{{ optional($userlogin->user)->first_name ?? optional($userlogin->user)->name ?? 'N/A' }}</td>
                            <td style="word-break: break-word; max-width: 19rem; white-space: normal">{{ $userlogin->user_agent }}</td>
                            <td>{{ $userlogin->ip_address }}</td>
                            <td>
                                @php
                                    // user timezone (fallback to app.timezone or UTC)
                                    $userTimezone = optional($userlogin->user)->timezone ?: config('app.timezone', 'UTC');

                                    // logged_in_at is stored in UTC in the DB; convert to user timezone
                                    if ($userlogin->logged_in_at) {
                                        try {
                                            $lastLoginUtc = Carbon::parse($userlogin->logged_in_at, 'UTC');
                                            $lastLogin = $lastLoginUtc->copy()->setTimezone($userTimezone);
                                        } catch (\Exception $e) {
                                            $lastLogin = null;
                                        }
                                    } else {
                                        $lastLogin = null;
                                    }

                                    // now in the user's timezone
                                    $now = Carbon::now($userTimezone);

                                    // consider online if lastLogin is within the last 5 minutes
                                    $online = false;
                                    if ($lastLogin) {
                                        $online = $lastLogin->greaterThanOrEqualTo($now->copy()->subMinutes(5));
                                    }
                                @endphp

                                @if($online)
                                    <span class="text-success">Online</span>
                                @else
                                    <span class="text-danger">Offline</span>
                                    @if($lastLogin)
                                        <br>
                                        <span title="Timezone: {{ $userTimezone }}">({{ $lastLogin->format('Y-m-d H:i:s') }})</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="icon-base ri ri-more-2-line icon-18px"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- directs to users.upgrade of the specific user --}}
                                        <a class="dropdown-item" href="{{ route('admin.users.edit', optional($userlogin->user)->id) }}">
                                            <i class="bx bx-user me-1"></i> View User
                                        </a>
                                    </div>
                                </div>
                            </td>


                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" class="text-center">No users login found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

@endsection
