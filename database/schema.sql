-- vijo_laravel_db.audits definição

CREATE TABLE `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(1023) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.cache definição

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.cache_locks definição

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.catalog_metric_question_labels definição

CREATE TABLE `catalog_metric_question_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `metricOption1Emoji` varchar(100) DEFAULT NULL,
  `metricOption1Text` varchar(100) DEFAULT NULL,
  `metricOption3Emoji` varchar(100) DEFAULT NULL,
  `metricOption3Text` varchar(100) DEFAULT NULL,
  `metricOption5Emoji` varchar(100) DEFAULT NULL,
  `metricOption5Text` varchar(100) DEFAULT NULL,
  `metricOption7Emoji` varchar(100) DEFAULT NULL,
  `metricOption7Text` varchar(100) DEFAULT NULL,
  `metricOption9Emoji` varchar(100) DEFAULT NULL,
  `metricOption9Text` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.categories definição

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `emoji` varchar(100) DEFAULT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Inactive, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.emlo_response_param_specs definição

CREATE TABLE `emlo_response_param_specs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `param_name` text NOT NULL,
  `description` text NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.emlo_response_paths definição

CREATE TABLE `emlo_response_paths` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path_key` varchar(255) NOT NULL,
  `json_path` text NOT NULL,
  `data_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.emlo_response_segments definição

CREATE TABLE `emlo_response_segments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.failed_jobs definição

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.job_batches definição

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.jobs definição

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.membership_plans definição

CREATE TABLE `membership_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `payment_mode` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: One Time, 2: Recurring',
  `monthly_cost` double NOT NULL DEFAULT 0,
  `annual_cost` double NOT NULL DEFAULT 0,
  `payment_link` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `membership_plans_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.migrations definição

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.password_reset_tokens definição

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.personal_access_tokens definição

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.rules definição

CREATE TABLE `rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` text NOT NULL,
  `param_name` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.sessions definição

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.video_types definição

CREATE TABLE `video_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `kpi_no` int(11) NOT NULL DEFAULT 0,
  `metric_no` int(11) NOT NULL DEFAULT 0,
  `video_no` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.catalogs definição

CREATE TABLE `catalogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_catalog_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `is_promotional` tinyint(1) NOT NULL DEFAULT 0,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `video_type_id` int(10) unsigned NOT NULL DEFAULT 1,
  `is_multipart` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `min_record_time` int(11) NOT NULL DEFAULT 1,
  `max_record_time` int(11) NOT NULL DEFAULT 30,
  `emoji` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Active, 1: Deleted',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactivated, 1: Active, 2: Deleted, 3: Archived',
  `admin_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalogs_category_id_foreign` (`category_id`),
  KEY `catalogs_video_type_id_foreign` (`video_type_id`),
  CONSTRAINT `catalogs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `catalogs_video_type_id_foreign` FOREIGN KEY (`video_type_id`) REFERENCES `video_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.rule_conditions definição

CREATE TABLE `rule_conditions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`condition`)),
  `message` text NOT NULL,
  `order_index` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_conditions_rule_id_foreign` (`rule_id`),
  CONSTRAINT `rule_conditions_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.users definição

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(10) unsigned DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `guided_tours` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: completed',
  `reminders` tinyint(1) NOT NULL DEFAULT 0,
  `notifications` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` varchar(100) DEFAULT NULL,
  `optInNewsUpdates` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_date` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactivated, 1: Active, 2: Deleted, 3: Archived',
  `is_verified` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: not verified, 1: verified',
  `plan_start_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_plan_id_foreign` (`plan_id`),
  CONSTRAINT `users_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.affiliates definição

CREATE TABLE `affiliates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliates_user_id_foreign` (`user_id`),
  CONSTRAINT `affiliates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.catalog_questions definição

CREATE TABLE `catalog_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_id` int(10) unsigned NOT NULL DEFAULT 0,
  `reference_type` tinyint(4) NOT NULL DEFAULT 0,
  `metric1_title` varchar(255) DEFAULT NULL,
  `metric1_question` varchar(255) DEFAULT NULL,
  `metric1_question_option1` varchar(50) DEFAULT NULL,
  `metric1_question_option2` varchar(50) DEFAULT NULL,
  `metric1_question_option1val` int(11) NOT NULL DEFAULT 0,
  `metric1_question_option2val` int(11) NOT NULL DEFAULT 0,
  `metric1_question_label` int(11) NOT NULL DEFAULT 0,
  `metric1_significance` tinyint(4) NOT NULL DEFAULT 0,
  `metric2_title` varchar(255) DEFAULT NULL,
  `metric2_question` varchar(255) DEFAULT NULL,
  `metric2_question_option1` varchar(50) DEFAULT NULL,
  `metric2_question_option2` varchar(50) DEFAULT NULL,
  `metric2_question_option1val` int(11) NOT NULL DEFAULT 0,
  `metric2_question_option2val` int(11) NOT NULL DEFAULT 0,
  `metric2_question_label` int(11) NOT NULL DEFAULT 0,
  `metric2_significance` tinyint(4) NOT NULL DEFAULT 0,
  `metric3_title` varchar(255) DEFAULT NULL,
  `metric3_question` varchar(255) DEFAULT NULL,
  `metric3_question_option1` varchar(50) DEFAULT NULL,
  `metric3_question_option2` varchar(50) DEFAULT NULL,
  `metric3_question_option1val` int(11) NOT NULL DEFAULT 0,
  `metric3_question_option2val` int(11) NOT NULL DEFAULT 0,
  `metric3_question_label` int(11) NOT NULL DEFAULT 0,
  `metric3_significance` tinyint(4) NOT NULL DEFAULT 0,
  `video_question` varchar(255) DEFAULT NULL,
  `metric4_significance` tinyint(4) NOT NULL DEFAULT 0,
  `metric5_significance` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalog_questions_catalog_id_foreign` (`catalog_id`),
  CONSTRAINT `catalog_questions_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.contact_groups definição

CREATE TABLE `contact_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'ID of the user who owns the group',
  `name` varchar(255) NOT NULL COMMENT 'Name of the contact group',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_groups_user_id_foreign` (`user_id`),
  CONSTRAINT `contact_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.contacts definição

CREATE TABLE `contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `country_code` int(11) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.llm_templates definição

CREATE TABLE `llm_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `system_prompt` text NOT NULL,
  `llm` text NOT NULL,
  `name` text NOT NULL,
  `examples` text NOT NULL,
  `llm_temperature` decimal(8,2) NOT NULL,
  `llm_response_max_length` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `llm_templates_user_id_foreign` (`user_id`),
  CONSTRAINT `llm_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.referral_codes definição

CREATE TABLE `referral_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(10) unsigned NOT NULL,
  `code` varchar(100) NOT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `number_uses` int(11) NOT NULL DEFAULT 0,
  `max_number_uses` int(11) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referral_codes_code_unique` (`code`),
  KEY `referral_codes_affiliate_id_foreign` (`affiliate_id`),
  CONSTRAINT `referral_codes_affiliate_id_foreign` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.subscriptions definição

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `plan_id` int(10) unsigned DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: active, 2: inactive, 3: canceled, 4: past_due, 5: unpaid',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `cancel_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.tags definição

CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('catalog','journalTag','custom') NOT NULL DEFAULT 'catalog',
  `created_by_user` int(10) unsigned DEFAULT NULL COMMENT 'User who created the tag',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_category_id_foreign` (`category_id`),
  KEY `tags_created_by_user_foreign` (`created_by_user`),
  CONSTRAINT `tags_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tags_created_by_user_foreign` FOREIGN KEY (`created_by_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.user_verifications definição

CREATE TABLE `user_verifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_verifications_user_id_foreign` (`user_id`),
  CONSTRAINT `user_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.video_requests definição

CREATE TABLE `video_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `catalog_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `ref_user_id` int(10) unsigned DEFAULT NULL,
  `ref_first_name` varchar(100) DEFAULT NULL,
  `ref_last_name` varchar(100) DEFAULT NULL,
  `ref_country_code` int(11) DEFAULT NULL,
  `ref_mobile` varchar(20) DEFAULT NULL,
  `ref_email` varchar(200) DEFAULT NULL,
  `ref_note` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `type` enum('daily','request') NOT NULL DEFAULT 'request',
  `status` enum('Pending','Accept','Approved','Reject','Not Right Now','Delete') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `error` text DEFAULT NULL,
  `llm_template_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_requests_user_id_foreign` (`user_id`),
  KEY `video_requests_catalog_id_foreign` (`catalog_id`),
  KEY `video_requests_ref_user_id_foreign` (`ref_user_id`),
  KEY `video_requests_contact_id_foreign` (`contact_id`),
  KEY `video_requests_group_id_foreign` (`group_id`),
  KEY `video_requests_llm_template_id_foreign` (`llm_template_id`),
  CONSTRAINT `video_requests_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_requests_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `video_requests_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `contact_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `video_requests_llm_template_id_foreign` FOREIGN KEY (`llm_template_id`) REFERENCES `llm_templates` (`id`),
  CONSTRAINT `video_requests_ref_user_id_foreign` FOREIGN KEY (`ref_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.videos definição

CREATE TABLE `videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int(10) unsigned NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `video_url` text NOT NULL,
  `video_duration` int(11) NOT NULL,
  `thumbnail_name` varchar(255) NOT NULL,
  `thumbnail_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `videos_request_id_foreign` (`request_id`),
  CONSTRAINT `videos_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.catalog_answers definição

CREATE TABLE `catalog_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `catalog_id` int(10) unsigned NOT NULL,
  `request_id` int(10) unsigned NOT NULL,
  `cred_score` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metric1_answer` varchar(50) NOT NULL DEFAULT '0',
  `metric1Range` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metric1Significance` tinyint(4) NOT NULL DEFAULT 0,
  `metric2_answer` varchar(50) NOT NULL DEFAULT '0',
  `metric2Range` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metric2Significance` tinyint(4) NOT NULL DEFAULT 0,
  `metric3_answer` varchar(50) NOT NULL DEFAULT '0',
  `metric3Range` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metric3Significance` tinyint(4) NOT NULL DEFAULT 0,
  `n8n_executionId` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalog_answers_user_id_foreign` (`user_id`),
  KEY `catalog_answers_catalog_id_foreign` (`catalog_id`),
  KEY `catalog_answers_request_id_foreign` (`request_id`),
  CONSTRAINT `catalog_answers_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `catalog_answers_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `catalog_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.contact_n2n_group definição

CREATE TABLE `contact_n2n_group` (
  `contact_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_id`,`group_id`),
  KEY `contact_n2n_group_group_id_foreign` (`group_id`),
  CONSTRAINT `contact_n2n_group_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contact_n2n_group_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `contact_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.emlo_responses definição

CREATE TABLE `emlo_responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int(10) unsigned NOT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_responses_request_id_foreign` (`request_id`),
  CONSTRAINT `emlo_responses_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.ai_pdfs definição

CREATE TABLE `ai_pdfs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int(10) unsigned NOT NULL,
  `s3_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_pdfs_response_id_foreign` (`response_id`),
  CONSTRAINT `ai_pdfs_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.csvs definição

CREATE TABLE `csvs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int(10) unsigned NOT NULL,
  `s3_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `csvs_response_id_foreign` (`response_id`),
  CONSTRAINT `csvs_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- vijo_laravel_db.emlo_response_values definição

CREATE TABLE `emlo_response_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int(10) unsigned NOT NULL,
  `path_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `string_value` text DEFAULT NULL,
  `numeric_value` int(11) DEFAULT NULL,
  `boolean_value` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_response_values_response_id_foreign` (`response_id`),
  KEY `emlo_response_values_path_id_foreign` (`path_id`),
  CONSTRAINT `emlo_response_values_path_id_foreign` FOREIGN KEY (`path_id`) REFERENCES `emlo_response_paths` (`id`),
  CONSTRAINT `emlo_response_values_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;