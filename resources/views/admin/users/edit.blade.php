@extends('layouts.app')
@section('title', 'User Data')

<style>
.nav-pills .nav-link.active{
    background-color: #1e0e84ff !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    box-shadow: none !important;
}

.nav-link{
    transition: transform 0.2s linear !important;
}

.nav-link:hover{
    transform: scale(1.06);
}

.coluns{
        display: grid;
        grid-auto-flow: column;
}

.section-title{
    margin-top:20px;
    margin-bottom:0px;
}

.line{
    margin-top: 0px
}

.cbx{
    border: 1px solid #afb0b1ff !important;
}


</style>


@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2"> User Data </h5>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <span>Error saving Edit</span>
        </div>
    @endif

    <div class="card-body">
        <!-- Nav Tabs -->
        <ul class="nav nav-pills" id="editTabs" role="tablist" style="border-bottom: 1px solid #d3d8dcff;">
            <!-- Personal Data -->
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">Personal Data</button>
            </li>
            <!-- Contacts -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">Contacts</button>
            </li>
            <!-- Groups -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button" role="tab">Groups</button>
            </li>
            <!-- Affiliates -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="affiliates-tab" data-bs-toggle="tab" data-bs-target="#affiliates" type="button" role="tab">Affiliates</button>
            </li>
            <!-- Coupons -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="coupons-tab" data-bs-toggle="tab" data-bs-target="#coupons" type="button" role="tab">Coupons</button>
            </li>
            <!-- Sent Emails -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sent_email-tab" data-bs-toggle="tab" data-bs-target="#sent_email" type="button" role="tab">Sent Emails</button>
            </li>
            <!-- Log -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="log-tab" data-bs-toggle="tab" data-bs-target="#log" type="button" role="tab">Activity Log</button>
            </li>
             <!-- Insights filters -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="insights_filter-tab" data-bs-toggle="tab" data-bs-target="#insights_filter" type="button" role="tab">Insights Filters</button>
            </li>
            <!-- My Plans -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vijo_plans-tab" data-bs-toggle="tab" data-bs-target="#vijo_plans" type="button" role="tab">My Plans</button>
            </li>
        </ul>
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Conteúdo das Abas -->
                <div class="tab-content mt-3" id="editTabsContent">

                    <!-- Conteúdo Personal Data -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <h5 class="section-title">Personal Data</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                            <input class="form-control wide-input @error('first_name') is-invalid @enderror" type="text" id="first_name" name="first_name" placeholder="Ex: Career" autocomplete="off" value="{{ old('first_name', $user->first_name ?? '') }}" autofocus required />
                                            @error('first_name')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                            <input class="form-control wide-input @error('last_name') is-invalid @enderror" type="text" id="last_name" name="last_name" placeholder="Ex: Career" autocomplete="off" value="{{ old('last_name', $user->last_name ?? '') }}" autofocus required />
                                            @error('last_name')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                            <input class="form-control wide-input @error('email') is-invalid @enderror" type="text" id="email" name="email" placeholder="Ex: ex@gmail.com" autocomplete="off" value="{{ old('email', $user->email ?? '') }}" autofocus required />
                                            @error('email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="mobile" class="form-label">Mobile</label>
                                            <input class="form-control wide-input @error('mobile') is-invalid @enderror" type="phone" id="mobile" name="mobile" placeholder="Ex: (123) 456-7890" maxlength="14" autocomplete="off" value="{{ old('mobile',  $user->mobile ?? '') }}" autofocus required />
                                            @error('mobile')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                </div>

                        <h5 class="section-title">Datatime and Country</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="coluns">

                                    <div class="mb-3 col-md-10">
                                        <label for="country_code" class="form-label">Country</label>
                                        <select class="form-select" id="country_code" name="country_code" style="cursor: pointer;" required>
                                            <option value="">Select</option>
                                            @foreach($countries as $code => $name)
                                                <option value="{{ $code }}" {{ old('country_code', $user->country_code) === $code ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-8">
                                        <label for="timezone">Timezone</label>
                                        <select class="form-select" id="timezone" name="timezone" style="cursor: pointer;" required>
                                            <option value="">Select</option>
                                        </select>
                                        @error('timezone')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>


                                    <div class="mb-3 col-md-10">
                                        <label for="email_verified_at" class="form-label">Email Verified At</label>
                                            <input class="form-control wide-input @error('email_verified_at') is-invalid @enderror" type="datetime-local" id="email_verified_at" name="email_verified_at" placeholder="Ex: ex@gmail.com" autocomplete="off" value="{{ old('email_verified_at',  $user->email_verified_at ?? '') }}" style="cursor: text;" autofocus  />
                                            @error('email_verified_at')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                    <div class="mb-3 col-md-10">
                                        <label for="last_login_date" class="form-label">Last Login Date</label>
                                            <input class="form-control wide-input @error('last_login_date') is-invalid @enderror" type="datetime-local" id="last_login_date" name="last_login_date" placeholder="Ex: ex@gmail.com" autocomplete="off" value="{{ old('last_login_date',  $user->last_login_date ?? '') }}" style="cursor: text;" autofocus  />
                                            @error('last_login_date')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>
                            </div>


                        <h5 class="section-title">Status</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="coluns">
                                    <div class="mb-3 col-md-10">
                                        <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control wide-input @error('status') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                                <option value="" disabled selected>Select</option>
                                                <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Activate</option>
                                                <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Deactivate</option>
                                            </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-10">
                                        <label for="is_verified">Verified</label>
                                            <select name="is_verified" id="is_verified" class="form-control wide-input @error('is_verified') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                                <option value="" disabled selected>Select</option>
                                                <option value="0" {{ old('is_verified', $user->is_verified) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('is_verified', $user->is_verified) == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        @error('is_verified')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-10">
                                        <label for="is_admin">Admin</label>
                                            <select name="is_admin" id="is_admin" class="form-control wide-input @error('is_admin') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                                <option value="" disabled selected>Select</option>
                                                <option value="0" {{ old('is_admin', $user->is_admin) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('is_admin', $user->is_admin) == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        @error('is_admin')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-10">
                                        <label for="guided_tours">Guided Tours</label>
                                            <select name="guided_tours" id="guided_tours" class="form-control wide-input @error('guided_tours') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                                <option value="" disabled selected>Select</option>
                                                <option value="0" {{ old('guided_tours', $user->guided_tours) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('guided_tours', $user->guided_tours) == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        @error('guided_tours')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                            </div>


                        <h5 class="section-title">Plan</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="plan_id">Plan</label>
                                            <select name="plan_id" id="plan_id" class="form-control wide-input @error('plan_id') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                                <option value="" disabled selected>Select</option>
                                                @foreach($membershipPlans as $mp)
                                                    <option value="{{ $mp->id }}"
                                                        {{ old('plan_id', $user->plan_id) == $mp->id ? 'selected' : '' }}>
                                                        {{ $mp->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @error('plan_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="plan_start_date" class="form-label">Plan Start Date</label>
                                            <input class="form-control wide-input @error('plan_start_date') is-invalid @enderror" type="datetime-local" id="plan_start_date" name="plan_start_date" placeholder="Ex: ex@gmail.com" autocomplete="off" value="{{ old('plan_start_date',  $user->plan_start_date ?? '') }}" style="cursor: text;" autofocus required />
                                            @error('plan_start_date')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>
                            </div>


                        <h5 class="section-title">Password</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="coluns">
                                <div class="mb-3 col-md-4">
                                    <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input class="form-control wide-input @error('password') is-invalid @enderror" type="password"  name="password" placeholder="Ex: password" autocomplete="off"  value="{{ old('password',  $user->password ?? '') }}" autofocus required />
                                            @error('password')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror

                                            <span class="input-group-text bg-transparent border-0 p-0.3rem">
                                                <i class="bx bx-edit-alt" style="font-size: 1.35rem; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#myModal" title="Click to change password" ></i>
                                            </span>
                                        </div>

                                            <!-- Modal -->
                                        <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm"> <!-- tamanho e centralização -->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Password</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Back"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form onsubmit="validateFormNewpassword(event)">

                                                            <div class="mb-3">
                                                                <label for="newpassoword" class="form-label">New Password</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control wide-input password-field @error('newpassoword') is-invalid @enderror" type="password" id="newpassoword" name="newpassoword" placeholder="Ex: New Password" autocomplete="off"  />

                                                                        <span class="input-group-text bg-transparent border-0 p-0.3rem toggle-password">
                                                                            <i class="bx bx-hide"  style="font-size: 1.35rem; cursor: pointer;"></i>
                                                                        </span>
                                                                    </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="repeatpassword" class="form-label">Repeat the new password</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control wide-input password-field @error('repeatpassword') is-invalid @enderror" type="password" id="repeatpassword" name="repeatpassword" placeholder="Ex: Repeat the new password" autocomplete="off" />

                                                                        <span class="input-group-text bg-transparent border-0 p-0.3rem toggle-password">
                                                                            <i class="bx bx-hide"  style="font-size: 1.35rem; cursor: pointer;"></i>
                                                                        </span>
                                                                        <div class="invalid-feedback">
                                                                            Passwords do not match.
                                                                        </div>
                                                                        <div class="valid-feedback">
                                                                            Passwords match!
                                                                        </div>
                                                                    </div>

                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                        <button type="button" class="btn btn-primary" id="savePasswordBtn" formnovalidate>Save</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>


                        <h5 class="section-title">Notifications</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="coluns">
                                    <div class="mb-3 col-md-5">
                                        <label for="notifications" class="form-label">Notifications</label>
                                            <input type="hidden" name="notifications" value="0">
                                            <input class="form-check-input cbx @error('notifications') is-invalid @enderror" type="checkbox" id="notifications" name="notifications"   autocomplete="off" value="1"{{ old('notifications', $user->notifications) ? 'checked' : '' }} style="cursor: pointer; margin-left: 1%;" autofocus />
                                            @error('notifications')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                    <div class="mb-3 col-md-5">
                                        <label for="reminders" class="form-label">Reminders</label>
                                            <input type="hidden" name="reminders" value="0">
                                            <input class="form-check-input cbx @error('reminders') is-invalid @enderror" type="checkbox" id="reminders" name="reminders"   autocomplete="off" value="1"{{ old('reminders',  $user->reminders ?? '') }} style="cursor: pointer; margin-left: 1%;" autofocus />
                                            @error('reminders')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>

                                    <div class="mb-3 col-md-5">
                                        <label for="optInNewsUpdates" class="form-label">Opt In News Updates</label>
                                            <input type="hidden" name="reminders" value="0">
                                            <input class="form-check-input cbx @error('optInNewsUpdates') is-invalid @enderror" type="checkbox" id="optInNewsUpdates" name="optInNewsUpdates"   autocomplete="off" value="1"{{ old('optInNewsUpdates',  $user->optInNewsUpdates ?? '') }} style="cursor: pointer; margin-left: 1%;" autofocus />
                                            @error('optInNewsUpdates')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                    </div>
                            </div>
                    </div>


                    <!-- Conteúdo Contacts -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <h5>Contact list</h5>
                            <hr class="line" style="border:1px solid #d3d8dc;">
                            <div class="table-responsive text-nowrap p-0 pt-0">
                                <table class="table table-striped table-hover dataTableList">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Country Code</th>
                                            <th>Advisor</th>
                                            <th>ADM</th>
                                            <th>Status</th>
                                            <th>Created at</th>
                                            <th>Updated at</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @if(isset($contacts) && count($contacts) > 0)
                                            @foreach($contacts as $contact)
                                                <tr>
                                                    <td>{{ $contact->id }}</td>
                                                    <td>{{ $contact->first_name }} {{ $contact->last_name }}</td>
                                                    <td>{{ $contact->mobile }}</td>
                                                    <td>{{ $contact->email }}</td>
                                                    <td>{{ $contact->country_code }}</td>
                                                    <td>{{ $contact->is_advisor ? 'Yes' : 'No' }}</td>
                                                    <td>{{ $contact->is_administrator ? 'Yes' : 'No' }}</td>
                                                    <td>{{ $contact->status ? 'Active' : 'Inactive' }}</td>
                                                    <td>{{ $contact->created_at }}</td>
                                                    <td>{{ $contact->uptaded_at }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="12" class="text-center">No records found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    <!-- Conteúdo Groups -->
                    <div class="tab-pane fade" id="groups" role="tabpanel">
                        <h5>Contact Group</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @if(isset($contactgroups) && count($contactgroups) > 0)
                                        @foreach($contactgroups as $contactgroup)
                                            <tr>
                                                <td>{{ $contactgroup->user_id }}</td>
                                                <td>{{ $contactgroup->name }}</td>
                                                <td>{{ $contactgroup->status ? 'Active' : 'Inactive' }}</td>
                                                <td>{{ $contactgroup->created_at }}</td>
                                                <td>{{ $contactgroup->updated_at }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="12" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Conteúdo Affiliates -->
                    <div class="tab-pane fade" id="affiliates" role="tabpanel">
                        <h5>Affiliate list</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Created ID</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>

                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php if (isset($affiliates) && count($affiliates) > 0): ?>
                                        <?php foreach ($affiliates as $key => $affiliate): ?>
                                            <tr>
                                                <td>{{ $affiliate->id }}</td>
                                                <td>{{ $affiliate->creator_id }}</td>
                                                <td>{{ $affiliate->type }}</td>
                                                <td>{{ $affiliate->status ? 'Active' : 'Inactive' }}</td>
                                                <td>{{ $affiliate->created_at }}</td>
                                                <td>{{ $affiliate->updated_at }}</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center">No records found.</td>
                                        </tr>
                                        <div>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Conteúdo Coupons -->
                    <div class="tab-pane fade" id="coupons" role="tabpanel">
                        <!-- <p>Aqui vai o conteúdo da aba <strong>Coupons</strong></p> -->
                    </div>

                    <!-- Conteúdo Sent Emails -->
                    <div class="tab-pane fade" id="sent_email" role="tabpanel">
                        <h5>Emails </h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Email Body</th>
                                        <th>Sent in</th>
                                        <th>Open in</th>

                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @if(isset($sentEmails) && count($sentEmails) > 0)
                                        @foreach($sentEmails as $sentEmail)
                                            <tr>

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="12" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Conteúdo log -->
                    <div class="tab-pane fade" id="log" role="tabpanel">
                        <h5>Activity Log</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th>Datatime</th>

                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @if(isset($logs) && count($logs) > 0)
                                        @foreach($logs as $log)
                                            <tr>

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="12" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Conteúdo Insights filter -->
                    <div class="tab-pane fade" id="insights_filter" role="tabpanel">
                        <h5>Insights Filter</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>title</th>
                                        <th>start date</th>
                                        <th>end date</th>
                                        <th>default</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @if(isset($insights_filter) && count($insights_filter) > 0)
                                        @foreach($insights_filter as $insight_filter)
                                            <tr>
                                                <td>{{ $insight_filter->id }}</td>
                                                <td>{{ $insight_filter->title }}</td>
                                                <td>{{ $insight_filter->start_date }}</td>
                                                <td>{{ $insight_filter->end_date }}</td>
                                                <td>{{ $insight_filter->default }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="icon-base ri ri-more-2-line icon-18px"></i>
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="<?php echo route('insightsfilter.update', $insight_filter->id); ?>"><i class="bx bx-edit me-1"></i> Edit</a>
                                                            <form action="{{ route('insightsfilters.destroy', $insight_filter->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item"><i class="icon-base ri ri-delete-bin-line me-1"></i> Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                     <!-- Conteúdo My Plans -->
                    <div class="tab-pane fade" id="vijo_plans" role="tabpanel">
                        <h5>My Plans</h5>
                        <hr class="line" style="border:1px solid #d3d8dc;">
                        <div class="table-responsive text-nowrap p-0 pt-0">
                            <table class="table table-striped table-hover dataTableList">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Length (weeks)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vijoPlansTbody" class="table-border-bottom-0">
                                    @if(isset($vijoplans) && count($vijoplans) > 0)
                                        @foreach($vijoplans as $vijoplan)
                                            <tr>
                                                <td>{{ $vijoplan->name }}</td>
                                                <td>{{ $vijoplan->description }}</td>
                                                <td>{{ $vijoplan->length_in_weeks }}</td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="icon-base ri ri-more-2-line icon-18px"></i>
                                                        </button>

                                                        <div class="dropdown-menu">

                                                            <!-- EDIT -->
                                                            <a class="dropdown-item" href="{{ route('admin.vijoplan.index') }}?edit={{ $vijoplan->id }}">
                                                                <i class="icon-base ri ri-edit-line me-1"></i> Edit
                                                            </a>

                                                            <!-- DELETE -->
                                                            <a class="dropdown-item"
                                                            href="{{ route('vijoplan.delete', $vijoplan->id) }}"
                                                            onclick="return confirm('Are you sure you want to delete this record?');">
                                                                <i class="icon-base ri ri-delete-bin-line me-1"></i> Delete
                                                            </a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No records found.</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>

                <div class="mb-3 col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control wide-input @error('description') is-invalid @enderror" id="description" name="description" placeholder="Ex: Description" autocomplete="off">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                    <a href="{{ url('admin/users') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </form>


    </div>
</div>
@endsection

@section('scripts')
<script>

///Funções do Personal Data

document.getElementById('mobile').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); // remove tudo que não for número
    if (value.length > 10) value = value.slice(0, 10); // limita a 10 dígitos

    if (value.length > 6) {
        value = `(${value.slice(0,3)}) ${value.slice(3,6)}-${value.slice(6,10)}`;
    } else if (value.length > 3) {
        value = `(${value.slice(0,3)}) ${value.slice(3,6)}`;
    } else if (value.length > 0) {
        value = `(${value}`;
    }

    e.target.value = value;
});

document.querySelectorAll('.toggle-password').forEach(span => {
    span.addEventListener('click', () => {
        // pega o input correspondente ao ícone clicado
        const input = span.closest('.input-group').querySelector('.password-field');
        const icon = span.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    });
});


const newPassword = document.getElementById('newpassoword');
const repeatPassword = document.getElementById('repeatpassword');

const timezones = @json($timezonesByCountry);
const countrySelect = document.getElementById('country_code');
const timezoneSelect = document.getElementById('timezone');

// Monitora quando o usuário começa a digitar no repeat
repeatPassword.addEventListener('input', function () {
    if (repeatPassword.value.length > 0) {
        if (newPassword.value !== repeatPassword.value) {
            repeatPassword.classList.remove('is-valid');
            repeatPassword.classList.add('is-invalid');
        } else {
            repeatPassword.classList.remove('is-invalid');
            repeatPassword.classList.add('is-valid');
        }
    } else {
        // Se apagou tudo, remove o erro
        repeatPassword.classList.remove('is-invalid');
    }
});

document.getElementById('savePasswordBtn').addEventListener('click', function () {
    // Validação final
    if (newPassword.value !== repeatPassword.value) {
        alert("Enter the same password in both fields!");
        return;
    }
    // Fecha o modal se estiver correto
    const modalElement = document.getElementById('myModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    repeatPassword.classList.remove('is-valid');
    repeatPassword.classList.remove('is-invalid');
    modal.hide();
});

  // Timezones por país vindos do backend
function populateTimezones(countryCode) {
    timezoneSelect.innerHTML = '<option value="">Select</option>';
    if (!timezones[countryCode]) return;

    timezones[countryCode].forEach(tz => {
        const option = document.createElement('option');
        option.value = tz;
        option.textContent = tz;

        // Mantém a seleção anterior
        if (tz === "{{ old('timezone', $user->timezone) }}") {
            option.selected = true;
        }

        timezoneSelect.appendChild(option);
    });

    }

    // Se já tiver um país selecionado no carregamento, popula
    if (countrySelect.value) {
        populateTimezones(countrySelect.value);
    }

    // Atualiza ao mudar o país
    countrySelect.addEventListener('change', function () {
        populateTimezones(this.value);
    });

    // Fetch and render Vijo plans for the user being viewed
    document.addEventListener('DOMContentLoaded', function () {
        const userId = {{ (int) $user->id }};
        const tbody = document.getElementById('vijoPlansTbody');
        if (!tbody) return;

        async function loadVijoPlans() {
            try {
                const resp = await fetch(`{{ url('admin/vijoplans/user') }}/${userId}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!resp.ok) throw new Error('Network error fetching plans');
                const json = await resp.json();
                tbody.innerHTML = '';
                const plans = Array.isArray(json.data) ? json.data : [];
                if (plans.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No records found.</td></tr>';
                    return;
                }
                plans.forEach(plan => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${escapeHtml(plan.name || '')}</td>
                        <td>${escapeHtml(plan.description || '')}</td>
                        <td>${escapeHtml(plan.length_in_weeks ?? '')}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ri ri-more-2-line icon-18px"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.vijoplan.index') }}?edit=${plan.id}">
                                        <i class="icon-base ri ri-edit-line me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item" href="{{ url('admin/vijoplans/delete') }}/${plan.id}" onclick="return confirm('Are you sure you want to delete this record?');">
                                        <i class="icon-base ri ri-delete-bin-line me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error('Error loading Vijo plans:', err);
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Failed to load plans.</td></tr>';
            }
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>"'`=\/]/g, function (s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '`': '&#96;',
                    '=': '&#61;',
                    '/': '&#47;'
                })[s];
            });
        }

        // Load when the tab is shown (so it's fresh), also load immediately if tab already visible
        const tabEl = document.querySelector('#vijo_plans');
        if (tabEl) {
            tabEl.addEventListener('shown.bs.tab', loadVijoPlans);
            // If tab is active on load, fetch immediately
            if (tabEl.classList.contains('show') || tabEl.classList.contains('active')) {
                loadVijoPlans();
            }
        } else {
            loadVijoPlans();
        }
    });
</script>
@endsection
