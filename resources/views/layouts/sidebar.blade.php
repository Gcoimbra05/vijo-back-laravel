<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('admin') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/images/group.svg') }}" style="width: 80%;" />
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <li class="menu-item <?php if (in_array($nav_bar, array('dashboard', 'emotion_datasets', 'emotion_profiles', 'emotion_rules', 'outcome_rules', 'personalities', 'angers', 'riskscores', 'jobQueueLVASuccess', 'jobQueueLVAFailed', 'testJobScore', 'day_of_week_messages', 'email_templates', 'text_templates', 'variables', 'contact_us', 'demo_requests', 'homepages', 'faqs', 'audit_logs', 'static_pages', 'banned_ip_addresses', 'helps', 'guided_tours', 'onboarding_contents', 'onboarding_emojis', 'genders', 'ages', 'educations', 'incomes', 'occupations', 'races', 'marital_statuses', 'seo_tags', 'timezones', 'defaultInsightsFilters', 'versions', 'sites', 'information_contents'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-tachometer"></i>
                <div data-i18n="vijo Site">vijo Site</div>
            </a>
            <ul class="menu-sub">
                <!-- Dashboard -->
                <li class="menu-item <?php if ($nav_bar == 'dashboard') { ?>active<?php } ?>">
                    <a href="{{ url('admin/dashboard') }}" class="menu-link">
                        <div data-i18n="Dashboard">Dashboard</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Customers -->
        <li class="menu-item <?php if (in_array($nav_bar, array('businesses', 'users', 'emailtemplate', 'platformtext', 'userlogin', 'userfeedbacks'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'users') { ?>active<?php } ?>">
                    <a href="{{ url('admin/users') }}" class="menu-link">
                        <div data-i18n="users">Users</div>
                    </a>
                </li>
                
                <li class="menu-item <?php if ($nav_bar == 'userlogin') { ?>active<?php } ?>">
                    <a href="{{ url('admin/userlogin') }}" class="menu-link">
                        <div data-i18n="userlogin">User Login</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'userfeedbacks') { ?>active<?php } ?>">
                    <a href="{{ url('admin/userfeedbacks') }}" class="menu-link">
                        <div data-i18n="userfeedbacks">User Feedbacks</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'emailtemplate') { ?>active<?php } ?>">
                    <a href="{{ url('admin/emailtemplate') }}" class="menu-link">
                        <div data-i18n="EmailTemplate">Email Templates</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'platformtext') { ?>active<?php } ?>">
                    <a href="{{ url('admin/platformtext') }}" class="menu-link">
                        <div data-i18n="PlatformText">Platform Text</div>
                    </a>
                </li>

            </ul>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('journal_types', 'journal_categories', 'journal_subcategories', 'journal_tags', 'catalog_metric_question_labels', 'catalogs', 'promotional_catalogs', 'medias', 'catalogPrograms', 'tags', 'Memberships Plans'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div data-i18n="Marketplace">Catalog</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'journal_types') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_types') }}" class="menu-link">
                        <div data-i18n="Journal Types">Journal Types</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'journal_categories') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_categories') }}" class="menu-link">
                        <div data-i18n="Journal Categories">Journal Categories</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'catalogs') { ?>active<?php } ?>">
                    <a href="{{ url('admin/catalogs') }}" class="menu-link">
                        <div data-i18n="Catalogs">Vijo Journals</div>
                    </a>
                </li>

                <!-- <li class="menu-item <?php if ($nav_bar == 'tags') { ?>active<?php } ?>">
                    <a href="{{ url('admin/tags') }}" class="menu-link">
                        <div data-i18n="Tags">Tags</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'Memberships Plans') { ?>active<?php } ?>">
                    <a href="{{ url('admin/memberships') }}" class="menu-link">
                        <div data-i18n="memberships">Membership Plans</div>
                    </a>
                </li> -->
            </ul>
        </li>

        <li class="menu-item <?php if (in_array($nav_bar, array('job', 'job_batches', 'failed_jobs'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-briefcase"></i>
                <div data-i18n="Jobs">Jobs</div>
            </a>
            <ul class="menu-sub">
                <!-- <li class="menu-item <?php if ($nav_bar == 'job') { ?>active<?php } ?>">
                    <a href="{{ url('admin/job') }}" class="menu-link">
                        <div data-i18n="Jobs">Jobs</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'job_batches') { ?>active<?php } ?>">
                    <a href="{{ url('admin/jobbatches') }}" class="menu-link">
                        <div data-i18n="Job_Batches">Job Batches</div>
                    </a>
                </li>

                <li class="menu-item <?php if ($nav_bar == 'failed_jobs') { ?>active<?php } ?>">
                    <a href="{{ url('admin/failedjobs') }}" class="menu-link">
                        <div data-i18n="failed_jobs">Failed Jobs</div>
                    </a>
                </li> -->

            </ul>
        </li>
    </ul>
</aside>
