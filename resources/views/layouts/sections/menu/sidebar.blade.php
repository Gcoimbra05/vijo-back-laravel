<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ url('admin') }}" class="app-brand-link gap-xl-0 gap-2">
            <span class="app-brand-logo demo me-1">
                <img src="{{ asset('assets/images/group.svg') }}" style="height: 48px" />
            </span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-header mt-7">
            <span class="menu-header-text">{{ __('VIJO Site') }}</span>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('dashboard', 'emotion_datasets', 'emotion_profiles', 'emotion_rules', 'outcome_rules', 'personalities', 'angers', 'riskscores', 'jobQueueLVASuccess', 'jobQueueLVAFailed', 'testJobScore', 'day_of_week_messages', 'email_templates', 'text_templates', 'variables', 'contact_us', 'demo_requests', 'homepages', 'faqs', 'audit_logs', 'static_pages', 'banned_ip_addresses', 'helps', 'guided_tours', 'onboarding_contents', 'onboarding_emojis', 'genders', 'ages', 'educations', 'incomes', 'occupations', 'races', 'marital_statuses', 'seo_tags', 'timezones', 'defaultInsightsFilters', 'versions', 'sites', 'information_contents'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-dashboard-line"></i>
                <div>{{ __('VIJO Site') }}</div>
            </a>

            <ul class="menu-sub">
                <!-- Dashboard -->
                <li class="menu-item <?php if ($nav_bar == 'dashboard') { ?>active<?php } ?>">
                    <a href="{{ url('admin/dashboard') }}" class="menu-link">
                        <div>{{ __('Dashboard') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header mt-7">
            <span class="menu-header-text">{{ __('Customers') }}</span>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('businesses', 'users', 'emailtemplate', 'platformtext', 'userlogin', 'userfeedbacks'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-folders-line"></i>
                <div>{{ __('Customers') }}</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'users') { ?>active<?php } ?>">
                    <a href="{{ url('admin/users') }}" class="menu-link">
                        <div>{{ __('Users') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'userlogin') { ?>active<?php } ?>">
                    <a href="{{ url('admin/userlogin') }}" class="menu-link">
                        <div>{{ __('User Login') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'userfeedbacks') { ?>active<?php } ?>">
                    <a href="{{ url('admin/userfeedbacks') }}" class="menu-link">
                        <div>{{ __('User Feedbacks') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'emailtemplate') { ?>active<?php } ?>">
                    <a href="{{ url('admin/emailtemplate') }}" class="menu-link">
                        <div>{{ __('Email Templates') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'platformtext') { ?>active<?php } ?>">
                    <a href="{{ url('admin/platformtext') }}" class="menu-link">
                        <div>{{ __('Platform Text') }}</div>
                    </a>
                </li>

            </ul>
        </li>

        <li class="menu-header mt-7">
            <span class="menu-header-text">{{ __('Catalog') }}</span>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('journal_types', 'journal_categories', 'journal_subcategories', 'journal_tags', 'catalog_metric_question_labels', 'catalogs', 'promotional_catalogs', 'medias', 'catalogPrograms', 'tags', 'Memberships Plans'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-store-2-line"></i>
                <div>{{ __('Catalog') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'journal_types') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_types') }}" class="menu-link">
                        <div>{{ __('Journal Types') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'journal_categories') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_categories') }}" class="menu-link">
                        <div>{{ __('Journal Categories') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'catalogs') { ?>active<?php } ?>">
                    <a href="{{ url('admin/catalogs') }}" class="menu-link">
                        <div>{{ __('Vijo Journals') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'tags') { ?>active<?php } ?>">
                    <a href="{{ url('admin/tags') }}" class="menu-link">
                        <div>{{ __('Tags') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'Memberships Plans') { ?>active<?php } ?>">
                    <a href="{{ url('admin/memberships') }}" class="menu-link">
                        <div>{{ __('Membership Plans') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header mt-7">
            <span class="menu-header-text">{{ __('Jobs') }}</span>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('job', 'job_batches', 'failed_jobs'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-briefcase-2-line"></i>
                <div>{{ __('Jobs') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'job') { ?>active<?php } ?>">
                    <a href="{{ url('admin/job') }}" class="menu-link">
                        <div>{{ __('Jobs') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'job_batches') { ?>active<?php } ?>">
                    <a href="{{ url('admin/jobbatches') }}" class="menu-link">
                        <div>{{ __('Job Batches') }}</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'failed_jobs') { ?>active<?php } ?>">
                    <a href="{{ url('admin/failedjobs') }}" class="menu-link">
                        <div>{{ __('Failed Jobs') }}</div>
                    </a>
                </li>

            </ul>
        </li>
    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ri ri-menu-line icon-base"></i>
        <i class="ri ri-arrow-right-s-line icon-base"></i>
    </a>
</div>
