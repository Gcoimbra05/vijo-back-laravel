@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome {{ config('app.name') }} admin! 🎉</h5>
                        <p class="mb-4">&nbsp;</p>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/chart-success.png') }}" alt="Members" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Members</span>
                        <h3 class="card-title mb-2">{{ $totalMembers }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/wallet-info.png') }}" alt="References" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">References</span>
                        <h3 class="card-title text-nowrap mb-1">{{ $totalReferences }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
            <div class="row row-bordered g-0">
                <div class="col-md-12">
                    <h5 class="card-header m-0 me-2 pb-0">Total API Calls</h5>
                    <div class="card-body pb-0">
                        <div class="text-right">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="growthReportId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $currentYear }}</button>
                                <div class="dropdown-menu dropdown-menu-end growthReportId-div" aria-labelledby="growthReportId">
                                    @for ($year = $currentYear; $year > date('Y', strtotime('-5 year', $current_timestamp->timestamp)) && $year >= $websiteDevelopedYear; $year--)
                                        <a class="dropdown-item" href="javascript:void(0);" data-year="{{ $year }}" onclick="loadTotalApiCallsByMonth('{{ url('admin/totalApiCallsByMonth') }}', '{{ $year }}');">{{ $year }}</a>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="totalRevenueChart" class="px-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!--/ Total Revenue -->
    <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
        <div class="row">
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/paypal.png') }}" alt="Monthly API Calls" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Monthly API Calls</span>
                        <h3 class="card-title mb-2">{{ $totalApiThisMonth }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/cc-primary.png') }}" alt="Total API Calls" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Total API Calls</span>
                        <h3 class="card-title mb-2">{{ $totalApis }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/chart.png') }}" alt="Monthly Avg. Response Time" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Monthly Avg. Response Time</span>
                        <h3 class="card-title mb-2">{{ $monthlyAvgResponseTime }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/wallet.png') }}" alt="Total Avg. Response Time" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Total Avg. Response Time</span>
                        <h3 class="card-title mb-2">{{ $allTimeAvgResponseTime }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/chart.png') }}" alt="Daily Avg. Response Time" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Daily Avg. Response Time</span>
                        <h3 class="card-title mb-2">{{ $dailyAvgResponseTime }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('admin/assets/img/icons/unicons/wallet.png') }}" alt="Weekly Avg. Response Time" class="rounded" />
                            </div>
                        </div>
                        <span class="fw-medium d-block mb-1">Weekly Avg. Response Time</span>
                        <h3 class="card-title mb-2">{{ $weeklyAvgResponseTime }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        loadTotalApiCallsByMonth();
    });
</script>
@endpush
