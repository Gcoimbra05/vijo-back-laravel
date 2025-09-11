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
        <li class="menu-item <?php if (in_array($nav_bar, array('businesses', 'users'))) { ?>open<?php } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($nav_bar == 'users') { ?>active<?php } ?>">
                    <a href="{{ url('admin/users') }}" class="menu-link">
                        <div data-i18n="Members">Members</div>
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
                <!-- Journal Types -->
                <li class="menu-item <?php if ($nav_bar == 'journal_types') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_types') }}" class="menu-link">
                        <div data-i18n="Journal Types">Journal Types</div>
                    </a>
                </li>

                <!-- Journal Categories -->
                <li class="menu-item <?php if ($nav_bar == 'journal_categories') { ?>active<?php } ?>">
                    <a href="{{ url('admin/journal_categories') }}" class="menu-link">
                        <div data-i18n="Journal Categories">Journal Categories</div>
                    </a>
                </li>

                <!-- Vijo Journals -->
                <li class="menu-item <?php if ($nav_bar == 'catalogs') { ?>active<?php } ?>">
                    <a href="{{ url('admin/catalogs') }}" class="menu-link">
                        <div data-i18n="Catalogs">Vijo Journals</div>
                    </a>
                </li>

                <!-- Tags -->
                <li class="menu-item <?php if ($nav_bar == 'tags') { ?>active<?php } ?>">
                    <a href="{{ url('admin/tags') }}" class="menu-link">
                        <div data-i18n="Tags">Tags</div>
                    </a>
                </li>

                <!-- Membership Plan -->
                <li class="menu-item <?php if ($nav_bar == 'Memberships Plans') { ?>active<?php } ?>">
                    <a href="{{ url('admin/memberships') }}" class="menu-link">
                        <div data-i18n="memberships">Membership Plans</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>
