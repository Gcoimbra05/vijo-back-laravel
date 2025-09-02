-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: 1925rds
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `affiliates`
--

DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `affiliates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliates_user_id_foreign` (`user_id`),
  KEY `affiliates_creator_id_foreign` (`creator_id`),
  CONSTRAINT `affiliates_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `affiliates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliates`
--

LOCK TABLES `affiliates` WRITE;
/*!40000 ALTER TABLE `affiliates` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_pdfs`
--

DROP TABLE IF EXISTS `ai_pdfs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_pdfs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int unsigned NOT NULL,
  `s3_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_pdfs_response_id_foreign` (`response_id`),
  CONSTRAINT `ai_pdfs_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_pdfs`
--

LOCK TABLES `ai_pdfs` WRITE;
/*!40000 ALTER TABLE `ai_pdfs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_pdfs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audits`
--

DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint unsigned NOT NULL,
  `old_values` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `new_values` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2094 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits`
--

LOCK TABLES `audits` WRITE;
/*!40000 ALTER TABLE `audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog_answers`
--

DROP TABLE IF EXISTS `catalog_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalog_answers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `catalog_id` int unsigned NOT NULL,
  `request_id` int unsigned NOT NULL,
  `cred_score` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metric1_answer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `metric1Range` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metric1Significance` tinyint NOT NULL DEFAULT '0',
  `metric2_answer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `metric2Range` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metric2Significance` tinyint NOT NULL DEFAULT '0',
  `metric3_answer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `metric3Range` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metric3Significance` tinyint NOT NULL DEFAULT '0',
  `n8n_executionId` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalog_answers_user_id_foreign` (`user_id`),
  KEY `catalog_answers_catalog_id_foreign` (`catalog_id`),
  KEY `catalog_answers_request_id_foreign` (`request_id`),
  CONSTRAINT `catalog_answers_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `catalog_answers_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `catalog_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_answers`
--

LOCK TABLES `catalog_answers` WRITE;
/*!40000 ALTER TABLE `catalog_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalog_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog_metric_question_labels`
--

DROP TABLE IF EXISTS `catalog_metric_question_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalog_metric_question_labels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption1Emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption1Text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption3Emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption3Text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption5Emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption5Text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption7Emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption7Text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption9Emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metricOption9Text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_metric_question_labels`
--

LOCK TABLES `catalog_metric_question_labels` WRITE;
/*!40000 ALTER TABLE `catalog_metric_question_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalog_metric_question_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalog_questions`
--

DROP TABLE IF EXISTS `catalog_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalog_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `catalog_id` int unsigned NOT NULL DEFAULT '0',
  `reference_type` tinyint NOT NULL DEFAULT '0',
  `metric1_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric1_question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric1_question_option1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric1_question_option2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric1_question_option1val` int NOT NULL DEFAULT '0',
  `metric1_question_option2val` int NOT NULL DEFAULT '0',
  `metric1_question_label` int NOT NULL DEFAULT '0',
  `metric1_significance` tinyint NOT NULL DEFAULT '0',
  `metric2_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric2_question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric2_question_option1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric2_question_option2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric2_question_option1val` int NOT NULL DEFAULT '0',
  `metric2_question_option2val` int NOT NULL DEFAULT '0',
  `metric2_question_label` int NOT NULL DEFAULT '0',
  `metric2_significance` tinyint NOT NULL DEFAULT '0',
  `metric3_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric3_question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric3_question_option1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric3_question_option2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric3_question_option1val` int NOT NULL DEFAULT '0',
  `metric3_question_option2val` int NOT NULL DEFAULT '0',
  `metric3_question_label` int NOT NULL DEFAULT '0',
  `metric3_significance` tinyint NOT NULL DEFAULT '0',
  `video_question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metric4_significance` tinyint NOT NULL DEFAULT '0',
  `metric5_significance` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalog_questions_catalog_id_foreign` (`catalog_id`),
  CONSTRAINT `catalog_questions_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalog_questions`
--

LOCK TABLES `catalog_questions` WRITE;
/*!40000 ALTER TABLE `catalog_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `catalog_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalogs`
--

DROP TABLE IF EXISTS `catalogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalogs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_catalog_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `is_promotional` tinyint(1) NOT NULL DEFAULT '0',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `video_type_id` int unsigned NOT NULL DEFAULT '1',
  `is_multipart` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `message_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_record_time` int NOT NULL DEFAULT '1',
  `max_record_time` int NOT NULL DEFAULT '30',
  `emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint NOT NULL DEFAULT '0' COMMENT '0: Active, 1: Deleted',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactivated, 1: Active, 2: Deleted, 3: Archived',
  `admin_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cred_score_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `catalogs_category_id_foreign` (`category_id`),
  KEY `catalogs_video_type_id_foreign` (`video_type_id`),
  KEY `catalogs_cred_score_id_foreign` (`cred_score_id`),
  CONSTRAINT `catalogs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `catalogs_cred_score_id_foreign` FOREIGN KEY (`cred_score_id`) REFERENCES `cred_scores` (`id`),
  CONSTRAINT `catalogs_video_type_id_foreign` FOREIGN KEY (`video_type_id`) REFERENCES `video_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogs`
--

LOCK TABLES `catalogs` WRITE;
/*!40000 ALTER TABLE `catalogs` DISABLE KEYS */;
INSERT INTO `catalogs` VALUES (1,NULL,1,0,0,1,0,'Check List','Keep Going — Progress adds up one task at a time.','Share what you got done today and what’s still ahead',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(2,NULL,1,0,0,1,0,'Today\'s Headline','Every day tells a story — today’s moments shape tomorrow.','Tell the story of the biggest thing that happened today',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(3,NULL,1,0,0,1,0,'Thought Bank','Ideas grow stronger when you take time to reflect on them.','Record ideas or reflections you’d like to remember',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(4,NULL,1,0,0,1,0,'Dream Log','Dreams are clues — pay attention, they may guide your path.','Remember a dream from last night and what it meant to you',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(5,NULL,1,0,0,1,0,'Favorite Things','Joy multiplies when you notice the little things that matter.','Talk about something you enjoy that makes you smile',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(6,NULL,1,0,0,1,0,'Photo Story','Pictures fade, but the stories you tell will last forever.','Tell the story and feelings behind a favorite picture',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(7,NULL,1,0,0,1,0,'Big Moment','Celebrate progress — milestones are proof you’re moving forward.','Celebrate a milestone or major event  worth keeping forever',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(8,NULL,1,0,0,1,0,'Health Record ','Your health is your wealth — small steps make a big difference.','Note important health details or how you are feeling',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(9,NULL,1,0,0,1,0,'Family Traditions','Sharing keeps traditions alive for generations to come.','Record customs, recipes, or celebrations to remember',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(10,NULL,1,0,0,1,0,'Spiritual Voice','Strength comes from within — your values light the way.','Reflect on your faith, beliefs, or personal journey',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(11,NULL,2,0,0,3,0,'Stress Snapshot','Stay steady — every breath brings you closer to calm.','Check in on how stressed or calm you feel today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(12,NULL,2,0,0,3,0,'Mood Swing','Awareness is power — noticing shifts helps you stay balanced.','Notice how your emotions changed and what influenced them.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(13,NULL,2,0,0,3,0,'Confidence Boost ','Believe in yourself — every win builds your strength.','Reflect on what made you feel strong or capable today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(14,NULL,2,0,0,3,0,'Anger Reset','Calm follows clarity — you’re learning to reset faster.','Explore anger and how you bring yourself back to calm.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(15,NULL,2,0,0,3,0,'Gratitude Glow ','Gratitude grows joy — you’ve added light to your day.','Recognize what you’re thankful for to lift your outlook.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(16,NULL,2,0,0,3,0,'Cognitive Balance ','Balance brings clarity — you’re tuning mind and heart together.','See how well your thoughts and emotions align today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(17,NULL,2,0,0,3,0,'Emotion Detective','Spotting your triggers is the first step toward control.','Describe a strong reaction and what triggered it.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(18,NULL,2,0,0,3,0,'Empathy Practice','Empathy builds bridges — you’re seeing with fresh eyes.','Step into someone else’s shoes and see their view.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(19,NULL,2,0,0,3,0,'Feeling Words','Expanding your vocabulary expands your self-awareness.','Use 5 precise emotion words to describe your day.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(20,NULL,2,0,0,3,0,'Social Mirror','Your presence shapes others — awareness makes it stronger.','Reflect on how others experienced your energy today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(21,NULL,3,0,0,3,0,'Habit Capture','Each recording reinforces your habit and self-awareness.','Strengthen your habit of daily video journaling.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(22,NULL,3,0,0,3,0,'Goal Getter','Every step counts—progress compounds over time.','Share progress toward a personal goal you care about.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(23,NULL,3,0,0,3,0,'Growth Mindset','Lessons turn setbacks into fuel for growth.','See challenges as chances to learn and improve.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(24,NULL,3,0,0,3,0,'Comfort Zone','Courage grows each time you stretch a little.','Do one thing that felt uncomfortable but important.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(25,NULL,3,0,0,3,0,'Bounce Back','Resilience strengthens every time you reset.','Reflect on a setback and how you recovered.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(26,NULL,3,0,0,3,0,'Energy Audit','Protect your energy—invest it where it matters.','Notice what boosted or drained your energy today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(27,NULL,3,0,0,3,0,'Learning Moment','Insights stick when you take time to name them.','Capture one insight about yourself or the world.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(28,NULL,3,0,0,3,0,'Fear Face','Naming fear shrinks it—small steps change everything.','Name a fear and take one small step toward it.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(29,NULL,3,0,0,3,0,'Value Compass','Living your values builds real confidence.','Show how you lived one core value today.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL),(30,NULL,3,0,0,3,0,'Pattern Spotter','Awareness is step one—now choose the next move.','Spot a recurring pattern you’re ready to change.',NULL,NULL,1,30,NULL,0,1,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `catalogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `emoji` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Inactive, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Time Capsule','Capture meaningful memories and life events',NULL,0,1,NULL,NULL),(2,'Emotional Wellbeing','Measure and understand emotional state for awareness',NULL,0,1,NULL,NULL),(3,'Personal Growth','Track progress on goals, habits, and aspirations',NULL,0,1,NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_groups`
--

DROP TABLE IF EXISTS `contact_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL COMMENT 'ID of the user who owns the group',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the contact group',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_groups_user_id_foreign` (`user_id`),
  CONSTRAINT `contact_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_groups`
--

LOCK TABLES `contact_groups` WRITE;
/*!40000 ALTER TABLE `contact_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_n2n_group`
--

DROP TABLE IF EXISTS `contact_n2n_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_n2n_group` (
  `contact_id` int unsigned NOT NULL,
  `group_id` int unsigned NOT NULL,
  PRIMARY KEY (`contact_id`,`group_id`),
  KEY `contact_n2n_group_group_id_foreign` (`group_id`),
  CONSTRAINT `contact_n2n_group_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contact_n2n_group_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `contact_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_n2n_group`
--

LOCK TABLES `contact_n2n_group` WRITE;
/*!40000 ALTER TABLE `contact_n2n_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_n2n_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `group_id` int unsigned DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` int DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cred_score_insights_aggregates`
--

DROP TABLE IF EXISTS `cred_score_insights_aggregates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cred_score_insights_aggregates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `catalog_id` int unsigned NOT NULL,
  `request_id` int unsigned NOT NULL,
  `last_7_days` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_30_days` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `since_start` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `morning` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `afternoon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `evening` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_7_days_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_30_days_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `since_start_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_average` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cred_score_insights_aggregates_catalog_id_foreign` (`catalog_id`),
  KEY `cred_score_insights_aggregates_request_id_foreign` (`request_id`),
  CONSTRAINT `cred_score_insights_aggregates_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`),
  CONSTRAINT `cred_score_insights_aggregates_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cred_score_insights_aggregates`
--

LOCK TABLES `cred_score_insights_aggregates` WRITE;
/*!40000 ALTER TABLE `cred_score_insights_aggregates` DISABLE KEYS */;
/*!40000 ALTER TABLE `cred_score_insights_aggregates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cred_score_kpis`
--

DROP TABLE IF EXISTS `cred_score_kpis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cred_score_kpis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cred_score_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cred_score_kpi_cred_score_id_foreign` (`cred_score_id`),
  CONSTRAINT `cred_score_kpi_cred_score_id_foreign` FOREIGN KEY (`cred_score_id`) REFERENCES `cred_scores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cred_score_kpis`
--

LOCK TABLES `cred_score_kpis` WRITE;
/*!40000 ALTER TABLE `cred_score_kpis` DISABLE KEYS */;
INSERT INTO `cred_score_kpis` VALUES (1,NULL,NULL,1),(2,NULL,NULL,2),(3,NULL,NULL,3),(4,NULL,NULL,4),(5,NULL,NULL,5),(6,NULL,NULL,6),(7,NULL,NULL,7),(8,NULL,NULL,8),(9,NULL,NULL,9),(10,NULL,NULL,10),(11,NULL,NULL,11),(12,NULL,NULL,12),(13,NULL,NULL,13),(14,NULL,NULL,14),(15,NULL,NULL,15),(16,NULL,NULL,16),(17,NULL,NULL,17),(18,NULL,NULL,18),(19,NULL,NULL,19),(20,NULL,NULL,20),(21,NULL,NULL,21),(22,NULL,NULL,22),(23,NULL,NULL,23),(24,NULL,NULL,24),(25,NULL,NULL,25),(26,NULL,NULL,26),(27,NULL,NULL,27),(28,NULL,NULL,28),(29,NULL,NULL,29),(30,NULL,NULL,30);
/*!40000 ALTER TABLE `cred_score_kpis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cred_score_values`
--

DROP TABLE IF EXISTS `cred_score_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cred_score_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cred_score` int NOT NULL,
  `measured_score` int NOT NULL,
  `percieved_score` int NOT NULL,
  `request_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cred_score_values_request_id_foreign` (`request_id`),
  CONSTRAINT `cred_score_values_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cred_score_values`
--

LOCK TABLES `cred_score_values` WRITE;
/*!40000 ALTER TABLE `cred_score_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `cred_score_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cred_scores`
--

DROP TABLE IF EXISTS `cred_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cred_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `catalog_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cred_score_catalog_id_foreign` (`catalog_id`),
  CONSTRAINT `cred_score_catalog_id_foreign` FOREIGN KEY (`catalog_id`) REFERENCES `catalogs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cred_scores`
--

LOCK TABLES `cred_scores` WRITE;
/*!40000 ALTER TABLE `cred_scores` DISABLE KEYS */;
INSERT INTO `cred_scores` VALUES (1,1,NULL,NULL,NULL),(2,2,NULL,NULL,NULL),(3,3,NULL,NULL,NULL),(4,4,NULL,NULL,NULL),(5,5,NULL,NULL,NULL),(6,6,NULL,NULL,NULL),(7,7,NULL,NULL,NULL),(8,8,NULL,NULL,NULL),(9,9,NULL,NULL,NULL),(10,10,NULL,NULL,NULL),(11,11,NULL,NULL,NULL),(12,12,NULL,NULL,NULL),(13,13,NULL,NULL,NULL),(14,14,NULL,NULL,NULL),(15,15,NULL,NULL,NULL),(16,16,NULL,NULL,NULL),(17,17,NULL,NULL,NULL),(18,18,NULL,NULL,NULL),(19,19,NULL,NULL,NULL),(20,20,NULL,NULL,NULL),(21,21,NULL,NULL,NULL),(22,22,NULL,NULL,NULL),(23,23,NULL,NULL,NULL),(24,24,NULL,NULL,NULL),(25,25,NULL,NULL,NULL),(26,26,NULL,NULL,NULL),(27,27,NULL,NULL,NULL),(28,28,NULL,NULL,NULL),(29,29,NULL,NULL,NULL),(30,30,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cred_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csvs`
--

DROP TABLE IF EXISTS `csvs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `csvs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int unsigned NOT NULL,
  `s3_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `csvs_response_id_foreign` (`response_id`),
  CONSTRAINT `csvs_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csvs`
--

LOCK TABLES `csvs` WRITE;
/*!40000 ALTER TABLE `csvs` DISABLE KEYS */;
/*!40000 ALTER TABLE `csvs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_insights_param_aggregates`
--

DROP TABLE IF EXISTS `emlo_insights_param_aggregates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_insights_param_aggregates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `emlo_param_spec_id` bigint unsigned NOT NULL,
  `request_id` int unsigned NOT NULL,
  `last_7_days` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_30_days` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `since_start` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `morning` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `afternoon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `evening` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_7_days_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_30_days_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `since_start_progress_over_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_average` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_insights_param_aggregates_emlo_param_spec_id_foreign` (`emlo_param_spec_id`),
  KEY `emlo_insights_param_aggregates_request_id_foreign` (`request_id`),
  CONSTRAINT `emlo_insights_param_aggregates_emlo_param_spec_id_foreign` FOREIGN KEY (`emlo_param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emlo_insights_param_aggregates_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=826 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_insights_param_aggregates`
--

LOCK TABLES `emlo_insights_param_aggregates` WRITE;
/*!40000 ALTER TABLE `emlo_insights_param_aggregates` DISABLE KEYS */;
/*!40000 ALTER TABLE `emlo_insights_param_aggregates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_insights_secondary_metrics`
--

DROP TABLE IF EXISTS `emlo_insights_secondary_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_insights_secondary_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `emlo_param_spec_id` bigint unsigned NOT NULL,
  `request_id` int unsigned NOT NULL,
  `info_array` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_insights_secondary_metrics_emlo_param_spec_id_foreign` (`emlo_param_spec_id`),
  KEY `emlo_insights_secondary_metrics_request_id_foreign` (`request_id`),
  CONSTRAINT `emlo_insights_secondary_metrics_emlo_param_spec_id_foreign` FOREIGN KEY (`emlo_param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emlo_insights_secondary_metrics_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_insights_secondary_metrics`
--

LOCK TABLES `emlo_insights_secondary_metrics` WRITE;
/*!40000 ALTER TABLE `emlo_insights_secondary_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `emlo_insights_secondary_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_response_param_specs`
--

DROP TABLE IF EXISTS `emlo_response_param_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_response_param_specs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `param_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emoji` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min` int NOT NULL,
  `max` int NOT NULL,
  `simplified_param_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `needs_normalization` tinyint(1) NOT NULL DEFAULT '0',
  `path_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `distribution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_response_param_specs`
--

LOCK TABLES `emlo_response_param_specs` WRITE;
/*!40000 ALTER TABLE `emlo_response_param_specs` DISABLE KEYS */;
INSERT INTO `emlo_response_param_specs` VALUES (1,NULL,NULL,'EDP-Anticipation','Anticipation is the energy in your voice that shows excitement, curiosity, or nervousness about what’s ahead.','U+1F440',1,100,'Anticipation','regular',0,NULL,'gaussian'),(2,NULL,NULL,'EDP-Concentrated','Concentration is the focus in your voice that shows how deeply engaged or absorbed you are.','U+1F9D0',1,100,'Concentration','regular',0,NULL,'gaussian'),(3,NULL,NULL,'EDP-Confident','Confidence is the steadiness in your voice that reflects how sure you feel and believe in your words.','U+1F451',1,100,'Confidence','regular',0,NULL,'gaussian'),(4,NULL,NULL,'EDP-Emotional','Emotional shows how much feeling you express and how engaged you are in what you share.','U+1F496',1,100,'Emotional','regular',0,NULL,'gaussian'),(5,NULL,NULL,'EDP-Energetic','Energy is the drive in your voice that reveals how alert, lively, or drained you feel.','U+26A1',1,100,'Energy','regular',0,NULL,'gaussian'),(6,NULL,NULL,'EDP-Hesitation','Hesitation is the pause in your voice that signals uncertainty, caution, or holding back.','U+1F937',1,100,'Hesitation','regular',0,NULL,'gaussian'),(7,NULL,NULL,'EDP-Passionate','Passion is the fire in your voice that shows strong emotion, deep interest, and personal connection.','U+1F4A5',1,100,'Passion','regular',0,NULL,'gaussian'),(8,NULL,NULL,'EDP-Stressful','Stress is the tension in your voice that reveals pressure, overwhelm, or feeling stretched thin.','U+1F48E',1,100,'Stress','regular',0,NULL,'gaussian'),(9,NULL,NULL,'EDP-Thoughtful','Thoughtfulness is the calm focus in your voice when you reflect and choose your words with care.','U+1F4AD',1,100,'Thoughtfulness','regular',0,NULL,'gaussian'),(10,NULL,NULL,'EDP-Uneasy','Uneasiness is the tension in your voice that signals discomfort, embarrassment, or feeling not quite at ease.','U+1F300',1,100,'Uneasiness','regular',0,NULL,'gaussian'),(11,NULL,NULL,'finalRiskLevel','Risk is the signal in your voice that suggests hesitation, uncertainty, or holding back from full self-honesty.','U+1F3AD',1,100,'Risk','segment',0,NULL,'definitive_state'),(12,NULL,NULL,'overallCognitiveActivity','Cognitive Balance shows how well your thoughts and emotions are aligned, too low may signal withdrawal or too high may reflect stress.','U+1F9D8',1,2000,'Cognitive Balance','regular',1,'overallCognitiveActivity.averageLevel','definitive_state'),(13,NULL,NULL,'Aggression','Aggression shows how strongly anger comes through in your voice, from calm confidence to intense tension that may signal stress or frustration.','U+1F624',0,100,'Aggression','regular',0,'aggression.averageLevel','definitive_state'),(14,NULL,NULL,'clStress','Stress Recovery is the ability to return to a calm, balanced state after experiencing stress.','U+2696',0,6,'Stress Recovery','regular',0,'clStress.clStress','definitive_state'),(15,NULL,NULL,'self_honesty','Shows how honest you are when you are speaking.',NULL,1,100,'Honesty','segment',0,NULL,'definitive_state');
/*!40000 ALTER TABLE `emlo_response_param_specs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_response_paths`
--

DROP TABLE IF EXISTS `emlo_response_paths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_response_paths` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `path_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `json_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `emlo_param_spec_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_response_paths_emlo_param_spec_id_foreign` (`emlo_param_spec_id`),
  CONSTRAINT `emlo_response_paths_emlo_param_spec_id_foreign` FOREIGN KEY (`emlo_param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_response_paths`
--

LOCK TABLES `emlo_response_paths` WRITE;
/*!40000 ALTER TABLE `emlo_response_paths` DISABLE KEYS */;
INSERT INTO `emlo_response_paths` VALUES (1,'reports','0.data.reports','arr',NULL,NULL,NULL),(2,'channel-0','0.data.reports.channel-0','arr',NULL,NULL,NULL),(3,'callPriority','0.data.reports.channel-0.callPriority','arr',NULL,NULL,NULL),(4,'distressPriority','0.data.reports.channel-0.callPriority.distressPriority','int',NULL,NULL,NULL),(5,'finalCallPriority','0.data.reports.channel-0.callPriority.finalCallPriority','int',NULL,NULL,NULL),(6,'maxCallPriority','0.data.reports.channel-0.callPriority.maxCallPriority','int',NULL,NULL,NULL),(7,'tonePriority','0.data.reports.channel-0.callPriority.tonePriority','int',NULL,NULL,NULL),(8,'edp','0.data.reports.channel-0.edp','arr',NULL,NULL,NULL),(9,'EDP-Anticipation','0.data.reports.channel-0.edp.EDP-Anticipation','int',NULL,'2025-08-29 08:27:06',1),(10,'EDP-Concentrated','0.data.reports.channel-0.edp.EDP-Concentrated','int',NULL,'2025-08-29 08:27:06',2),(11,'EDP-Confident','0.data.reports.channel-0.edp.EDP-Confident','int',NULL,'2025-08-29 08:27:07',3),(12,'EDP-Emotional','0.data.reports.channel-0.edp.EDP-Emotional','int',NULL,'2025-08-29 08:27:07',4),(13,'EDP-Energetic','0.data.reports.channel-0.edp.EDP-Energetic','int',NULL,'2025-08-29 08:27:08',5),(14,'EDP-Hesitation','0.data.reports.channel-0.edp.EDP-Hesitation','int',NULL,'2025-08-29 08:27:09',6),(15,'EDP-Passionate','0.data.reports.channel-0.edp.EDP-Passionate','int',NULL,'2025-08-29 08:27:09',7),(16,'EDP-Stressful','0.data.reports.channel-0.edp.EDP-Stressful','int',NULL,'2025-08-29 08:27:10',8),(17,'EDP-Thoughtful','0.data.reports.channel-0.edp.EDP-Thoughtful','int',NULL,'2025-08-29 08:27:11',9),(18,'EDP-Uneasy','0.data.reports.channel-0.edp.EDP-Uneasy','int',NULL,'2025-08-29 08:27:11',10),(19,'profile','0.data.reports.channel-0.profile','arr',NULL,NULL,NULL),(20,'aggression','0.data.reports.channel-0.profile.aggression','arr',NULL,NULL,NULL),(21,'aggression.averageLevel','0.data.reports.channel-0.profile.aggression.averageLevel','float',NULL,'2025-08-29 08:27:13',13),(22,'aggression.highPercentage','0.data.reports.channel-0.profile.aggression.highPercentage','float',NULL,NULL,NULL),(23,'aggression.lowPercentage','0.data.reports.channel-0.profile.aggression.lowPercentage','float',NULL,NULL,NULL),(24,'aggression.midPercentage','0.data.reports.channel-0.profile.aggression.midPercentage','float',NULL,NULL,NULL),(25,'aggression.noReactionPercentage','0.data.reports.channel-0.profile.aggression.noReactionPercentage','float',NULL,NULL,NULL),(26,'anticipation','0.data.reports.channel-0.profile.anticipation','arr',NULL,NULL,NULL),(27,'anticipation.averageLevel','0.data.reports.channel-0.profile.anticipation.averageLevel','float',NULL,NULL,NULL),(28,'anticipation.highPercentage','0.data.reports.channel-0.profile.anticipation.highPercentage','float',NULL,NULL,NULL),(29,'anticipation.lowPercentage','0.data.reports.channel-0.profile.anticipation.lowPercentage','float',NULL,NULL,NULL),(30,'anticipation.midPercentage','0.data.reports.channel-0.profile.anticipation.midPercentage','float',NULL,NULL,NULL),(31,'anticipation.noReactionPercentage','0.data.reports.channel-0.profile.anticipation.noReactionPercentage','float',NULL,NULL,NULL),(32,'arousal','0.data.reports.channel-0.profile.arousal','arr',NULL,NULL,NULL),(33,'arousal.averageLevel','0.data.reports.channel-0.profile.arousal.averageLevel','float',NULL,NULL,NULL),(34,'arousal.highPercentage','0.data.reports.channel-0.profile.arousal.highPercentage','float',NULL,NULL,NULL),(35,'arousal.lowPercentage','0.data.reports.channel-0.profile.arousal.lowPercentage','float',NULL,NULL,NULL),(36,'arousal.midPercentage','0.data.reports.channel-0.profile.arousal.midPercentage','float',NULL,NULL,NULL),(37,'arousal.noReactionPercentage','0.data.reports.channel-0.profile.arousal.noReactionPercentage','float',NULL,NULL,NULL),(38,'atmosphere','0.data.reports.channel-0.profile.atmosphere','arr',NULL,NULL,NULL),(39,'atmosphere._comments','0.data.reports.channel-0.profile.atmosphere._comments','string',NULL,NULL,NULL),(40,'atmosphere.averageLevel','0.data.reports.channel-0.profile.atmosphere.averageLevel','float',NULL,NULL,NULL),(41,'atmosphere.highPercentage','0.data.reports.channel-0.profile.atmosphere.highPercentage','float',NULL,NULL,NULL),(42,'atmosphere.lowPercentage','0.data.reports.channel-0.profile.atmosphere.lowPercentage','float',NULL,NULL,NULL),(43,'atmosphere.midPercentage','0.data.reports.channel-0.profile.atmosphere.midPercentage','float',NULL,NULL,NULL),(44,'atmosphere.noReactionPercentage','0.data.reports.channel-0.profile.atmosphere.noReactionPercentage','float',NULL,NULL,NULL),(45,'clStress','0.data.reports.channel-0.profile.clStress','arr',NULL,NULL,NULL),(46,'clStress.clStress','0.data.reports.channel-0.profile.clStress.clStress','int',NULL,'2025-08-29 08:27:13',14),(47,'clStress.high','0.data.reports.channel-0.profile.clStress.high','int',NULL,NULL,NULL),(48,'clStress.low','0.data.reports.channel-0.profile.clStress.low','int',NULL,NULL,NULL),(49,'concentration','0.data.reports.channel-0.profile.concentration','arr',NULL,NULL,NULL),(50,'concentration.averageLevel','0.data.reports.channel-0.profile.concentration.averageLevel','float',NULL,NULL,NULL),(51,'concentration.highPercentage','0.data.reports.channel-0.profile.concentration.highPercentage','float',NULL,NULL,NULL),(52,'concentration.lowPercentage','0.data.reports.channel-0.profile.concentration.lowPercentage','float',NULL,NULL,NULL),(53,'concentration.midPercentage','0.data.reports.channel-0.profile.concentration.midPercentage','float',NULL,NULL,NULL),(54,'concentration.noReactionPercentage','0.data.reports.channel-0.profile.concentration.noReactionPercentage','float',NULL,NULL,NULL),(55,'discomfort','0.data.reports.channel-0.profile.discomfort','arr',NULL,NULL,NULL),(56,'discomfort.uneasyEnd','0.data.reports.channel-0.profile.discomfort.uneasyEnd','int',NULL,NULL,NULL),(57,'discomfort.uneasyStart','0.data.reports.channel-0.profile.discomfort.uneasyStart','int',NULL,NULL,NULL),(58,'energy','0.data.reports.channel-0.profile.energy','arr',NULL,NULL,NULL),(59,'energy.averageLevel','0.data.reports.channel-0.profile.energy.averageLevel','float',NULL,NULL,NULL),(60,'energy.highPercentage','0.data.reports.channel-0.profile.energy.highPercentage','float',NULL,NULL,NULL),(61,'energy.lowPercentage','0.data.reports.channel-0.profile.energy.lowPercentage','float',NULL,NULL,NULL),(62,'energy.midPercentage','0.data.reports.channel-0.profile.energy.midPercentage','float',NULL,NULL,NULL),(63,'energy.noReactionPercentage','0.data.reports.channel-0.profile.energy.noReactionPercentage','float',NULL,NULL,NULL),(64,'excitement','0.data.reports.channel-0.profile.excitement','arr',NULL,NULL,NULL),(65,'excitement._comments','0.data.reports.channel-0.profile.excitement._comments','string',NULL,NULL,NULL),(66,'excitement.averageLevel','0.data.reports.channel-0.profile.excitement.averageLevel','float',NULL,NULL,NULL),(67,'excitement.highPercentage','0.data.reports.channel-0.profile.excitement.highPercentage','float',NULL,NULL,NULL),(68,'excitement.lowPercentage','0.data.reports.channel-0.profile.excitement.lowPercentage','float',NULL,NULL,NULL),(69,'excitement.midPercentage','0.data.reports.channel-0.profile.excitement.midPercentage','float',NULL,NULL,NULL),(70,'excitement.normalReactionPercentage','0.data.reports.channel-0.profile.excitement.normalReactionPercentage','float',NULL,NULL,NULL),(71,'hesitation','0.data.reports.channel-0.profile.hesitation','arr',NULL,NULL,NULL),(72,'hesitation._comments','0.data.reports.channel-0.profile.hesitation._comments','string',NULL,NULL,NULL),(73,'hesitation.averageLevel','0.data.reports.channel-0.profile.hesitation.averageLevel','float',NULL,NULL,NULL),(74,'hesitation.highPercentage','0.data.reports.channel-0.profile.hesitation.highPercentage','float',NULL,NULL,NULL),(75,'hesitation.lowPercentage','0.data.reports.channel-0.profile.hesitation.lowPercentage','float',NULL,NULL,NULL),(76,'hesitation.midPercentage','0.data.reports.channel-0.profile.hesitation.midPercentage','float',NULL,NULL,NULL),(77,'hesitation.normalReactionPercentage','0.data.reports.channel-0.profile.hesitation.normalReactionPercentage','float',NULL,NULL,NULL),(78,'imagination','0.data.reports.channel-0.profile.imagination','arr',NULL,NULL,NULL),(79,'imagination.averageLevel','0.data.reports.channel-0.profile.imagination.averageLevel','float',NULL,NULL,NULL),(80,'imagination.highPercentage','0.data.reports.channel-0.profile.imagination.highPercentage','float',NULL,NULL,NULL),(81,'imagination.lowPercentage','0.data.reports.channel-0.profile.imagination.lowPercentage','float',NULL,NULL,NULL),(82,'imagination.midPercentage','0.data.reports.channel-0.profile.imagination.midPercentage','float',NULL,NULL,NULL),(83,'imagination.noReactionPercentage','0.data.reports.channel-0.profile.imagination.noReactionPercentage','float',NULL,NULL,NULL),(84,'joy','0.data.reports.channel-0.profile.joy','arr',NULL,NULL,NULL),(85,'joy.averageLevel','0.data.reports.channel-0.profile.joy.averageLevel','float',NULL,NULL,NULL),(86,'joy.highPercentage','0.data.reports.channel-0.profile.joy.highPercentage','float',NULL,NULL,NULL),(87,'joy.lowPercentage','0.data.reports.channel-0.profile.joy.lowPercentage','float',NULL,NULL,NULL),(88,'joy.midPercentage','0.data.reports.channel-0.profile.joy.midPercentage','float',NULL,NULL,NULL),(89,'joy.noReactionPercentage','0.data.reports.channel-0.profile.joy.noReactionPercentage','float',NULL,NULL,NULL),(90,'mentalEfficiency','0.data.reports.channel-0.profile.mentalEfficiency','arr',NULL,NULL,NULL),(91,'mentalEfficiency.bioAverage','0.data.reports.channel-0.profile.mentalEfficiency.bioAverage','int',NULL,NULL,NULL),(92,'mentalEfficiency.bioHigh','0.data.reports.channel-0.profile.mentalEfficiency.bioHigh','int',NULL,NULL,NULL),(93,'mentalEfficiency.bioLow','0.data.reports.channel-0.profile.mentalEfficiency.bioLow','int',NULL,NULL,NULL),(94,'mentalEfficiency.mentalEffortEfficiency','0.data.reports.channel-0.profile.mentalEfficiency.mentalEffortEfficiency','int',NULL,NULL,NULL),(95,'mentalEffort','0.data.reports.channel-0.profile.mentalEffort','arr',NULL,NULL,NULL),(96,'mentalEffort.averageLevel','0.data.reports.channel-0.profile.mentalEffort.averageLevel','float',NULL,NULL,NULL),(97,'mentalEffort.highPercentage','0.data.reports.channel-0.profile.mentalEffort.highPercentage','float',NULL,NULL,NULL),(98,'mentalEffort.lowPercentage','0.data.reports.channel-0.profile.mentalEffort.lowPercentage','float',NULL,NULL,NULL),(99,'mentalEffort.midPercentage','0.data.reports.channel-0.profile.mentalEffort.midPercentage','float',NULL,NULL,NULL),(100,'mentalEffort.noReactionPercentage','0.data.reports.channel-0.profile.mentalEffort.noReactionPercentage','float',NULL,NULL,NULL),(101,'overallCognitiveActivity','0.data.reports.channel-0.profile.overallCognitiveActivity','arr',NULL,NULL,NULL),(102,'overallCognitiveActivity.averageLevel','0.data.reports.channel-0.profile.overallCognitiveActivity.averageLevel','float',NULL,'2025-08-29 08:27:12',12),(103,'overallCognitiveActivity.highPercentage','0.data.reports.channel-0.profile.overallCognitiveActivity.highPercentage','float',NULL,NULL,NULL),(104,'overallCognitiveActivity.lowPercentage','0.data.reports.channel-0.profile.overallCognitiveActivity.lowPercentage','float',NULL,NULL,NULL),(105,'overallCognitiveActivity.midPercentage','0.data.reports.channel-0.profile.overallCognitiveActivity.midPercentage','float',NULL,NULL,NULL),(106,'overallCognitiveActivity.noReactionPercentage','0.data.reports.channel-0.profile.overallCognitiveActivity.noReactionPercentage','float',NULL,NULL,NULL),(107,'sad','0.data.reports.channel-0.profile.sad','arr',NULL,NULL,NULL),(108,'sad.averageLevel','0.data.reports.channel-0.profile.sad.averageLevel','float',NULL,NULL,NULL),(109,'sad.highPercentage','0.data.reports.channel-0.profile.sad.highPercentage','float',NULL,NULL,NULL),(110,'sad.lowPercentage','0.data.reports.channel-0.profile.sad.lowPercentage','float',NULL,NULL,NULL),(111,'sad.midPercentage','0.data.reports.channel-0.profile.sad.midPercentage','float',NULL,NULL,NULL),(112,'sad.noReactionPercentage','0.data.reports.channel-0.profile.sad.noReactionPercentage','float',NULL,NULL,NULL),(113,'stress','0.data.reports.channel-0.profile.stress','arr',NULL,NULL,NULL),(114,'stress.averageLevel','0.data.reports.channel-0.profile.stress.averageLevel','float',NULL,NULL,NULL),(115,'stress.highPercentage','0.data.reports.channel-0.profile.stress.highPercentage','float',NULL,NULL,NULL),(116,'stress.lowPercentage','0.data.reports.channel-0.profile.stress.lowPercentage','float',NULL,NULL,NULL),(117,'stress.midPercentage','0.data.reports.channel-0.profile.stress.midPercentage','float',NULL,NULL,NULL),(118,'stress.noReactionPercentage','0.data.reports.channel-0.profile.stress.noReactionPercentage','float',NULL,NULL,NULL),(119,'uncertainty','0.data.reports.channel-0.profile.uncertainty','arr',NULL,NULL,NULL),(120,'uncertainty._comments','0.data.reports.channel-0.profile.uncertainty._comments','string',NULL,NULL,NULL),(121,'uncertainty.averageLevel','0.data.reports.channel-0.profile.uncertainty.averageLevel','float',NULL,NULL,NULL),(122,'uncertainty.highPercentage','0.data.reports.channel-0.profile.uncertainty.highPercentage','float',NULL,NULL,NULL),(123,'uncertainty.lowPercentage','0.data.reports.channel-0.profile.uncertainty.lowPercentage','float',NULL,NULL,NULL),(124,'uncertainty.midPercentage','0.data.reports.channel-0.profile.uncertainty.midPercentage','float',NULL,NULL,NULL),(125,'uncertainty.normalReactionPercentage','0.data.reports.channel-0.profile.uncertainty.normalReactionPercentage','float',NULL,NULL,NULL),(126,'uneasy','0.data.reports.channel-0.profile.uneasy','arr',NULL,NULL,NULL),(127,'uneasy.averageLevel','0.data.reports.channel-0.profile.uneasy.averageLevel','float',NULL,NULL,NULL),(128,'uneasy.highPercentage','0.data.reports.channel-0.profile.uneasy.highPercentage','float',NULL,NULL,NULL),(129,'uneasy.lowPercentage','0.data.reports.channel-0.profile.uneasy.lowPercentage','float',NULL,NULL,NULL),(130,'uneasy.midPercentage','0.data.reports.channel-0.profile.uneasy.midPercentage','float',NULL,NULL,NULL),(131,'uneasy.noReactionPercentage','0.data.reports.channel-0.profile.uneasy.noReactionPercentage','float',NULL,NULL,NULL),(132,'riskSummary','0.data.reports.channel-0.riskSummary','arr',NULL,NULL,NULL),(133,'averageRiskOZ3','0.data.reports.channel-0.riskSummary.averageRiskOZ3','int',NULL,NULL,NULL),(134,'riskCounter1','0.data.reports.channel-0.riskSummary.riskCounter1','int',NULL,NULL,NULL),(135,'riskCounter2','0.data.reports.channel-0.riskSummary.riskCounter2','int',NULL,NULL,NULL),(136,'riskCounter3','0.data.reports.channel-0.riskSummary.riskCounter3','int',NULL,NULL,NULL),(137,'riskOZCounter','0.data.reports.channel-0.riskSummary.riskOZCounter','int',NULL,NULL,NULL),(138,'tags','0.data.reports.channel-0.tags','list',NULL,NULL,NULL),(139,'tags[0]','0.data.reports.channel-0.tags[0]','string',NULL,NULL,NULL),(140,'tags[1]','0.data.reports.channel-0.tags[1]','string',NULL,NULL,NULL),(141,'tags[2]','0.data.reports.channel-0.tags[2]','string',NULL,NULL,NULL),(142,'tags[3]','0.data.reports.channel-0.tags[3]','string',NULL,NULL,NULL),(143,'tags[4]','0.data.reports.channel-0.tags[4]','string',NULL,NULL,NULL),(144,'testReport','0.data.reports.channel-0.testReport','arr',NULL,NULL,NULL),(145,'biomarkers','0.data.reports.channel-0.testReport.biomarkers','arr',NULL,NULL,NULL),(146,'biomarkers.averageCognitionLevel','0.data.reports.channel-0.testReport.biomarkers.averageCognitionLevel','int',NULL,NULL,NULL),(147,'biomarkers.averageEmotionLevel','0.data.reports.channel-0.testReport.biomarkers.averageEmotionLevel','int',NULL,NULL,NULL),(148,'biomarkers.averageStressLevel','0.data.reports.channel-0.testReport.biomarkers.averageStressLevel','int',NULL,NULL,NULL),(149,'biomarkers.cognitiveChangeLevel','0.data.reports.channel-0.testReport.biomarkers.cognitiveChangeLevel','int',NULL,NULL,NULL),(150,'biomarkers.concetrationChangeLevel','0.data.reports.channel-0.testReport.biomarkers.concetrationChangeLevel','int',NULL,NULL,NULL),(151,'biomarkers.emotionChangeLevel','0.data.reports.channel-0.testReport.biomarkers.emotionChangeLevel','int',NULL,NULL,NULL),(152,'biomarkers.energyChangeLevel','0.data.reports.channel-0.testReport.biomarkers.energyChangeLevel','int',NULL,NULL,NULL),(153,'biomarkers.engagedChangeLevel','0.data.reports.channel-0.testReport.biomarkers.engagedChangeLevel','int',NULL,NULL,NULL),(154,'biomarkers.stressChangeLevel','0.data.reports.channel-0.testReport.biomarkers.stressChangeLevel','int',NULL,NULL,NULL),(155,'testReport.extremeEmotionSegments','0.data.reports.channel-0.testReport.extremeEmotionSegments','int',NULL,NULL,NULL),(156,'testReport.extremeStressConversationPortions','0.data.reports.channel-0.testReport.extremeStressConversationPortions','int',NULL,NULL,NULL),(157,'testReport.extremeStressSegments','0.data.reports.channel-0.testReport.extremeStressSegments','int',NULL,NULL,NULL),(158,'summary','0.data.reports.summary','arr',NULL,NULL,NULL),(159,'summary.channel-0','0.data.reports.summary.channel-0','arr',NULL,NULL,NULL),(160,'summary.channel-0.CSCscore','0.data.reports.summary.channel-0.CSCscore','int',NULL,NULL,NULL),(161,'summary.channel-0.angerPercentage','0.data.reports.summary.channel-0.angerPercentage','int',NULL,NULL,NULL),(162,'summary.channel-0.callLength','0.data.reports.summary.channel-0.callLength','arr',NULL,NULL,NULL),(163,'summary.channel-0.callLength.minutes','0.data.reports.summary.channel-0.callLength.minutes','int',NULL,NULL,NULL),(164,'summary.channel-0.callLength.seconds','0.data.reports.summary.channel-0.callLength.seconds','int',NULL,NULL,NULL),(165,'summary.channel-0.channelAgentPriority','0.data.reports.summary.channel-0.channelAgentPriority','int',NULL,NULL,NULL),(166,'summary.channel-0.channelDistressPriority','0.data.reports.summary.channel-0.channelDistressPriority','int',NULL,NULL,NULL),(167,'summary.channel-0.channelFinalPriority','0.data.reports.summary.channel-0.channelFinalPriority','int',NULL,NULL,NULL),(168,'summary.channel-0.channelMaxPriority','0.data.reports.summary.channel-0.channelMaxPriority','int',NULL,NULL,NULL),(169,'summary.channel-0.code','0.data.reports.summary.channel-0.code','int',NULL,NULL,NULL),(170,'summary.channel-0.corroboratedAngerPercentage','0.data.reports.summary.channel-0.corroboratedAngerPercentage','int',NULL,NULL,NULL),(171,'summary.channel-0.corroboratedStressPercentage','0.data.reports.summary.channel-0.corroboratedStressPercentage','int',NULL,NULL,NULL),(172,'summary.channel-0.dissatisfaction','0.data.reports.summary.channel-0.dissatisfaction','int',NULL,NULL,NULL),(173,'summary.channel-0.energyAverage','0.data.reports.summary.channel-0.energyAverage','int',NULL,NULL,NULL),(174,'summary.channel-0.final10Score','0.data.reports.summary.channel-0.final10Score','int',NULL,NULL,NULL),(175,'summary.channel-0.highEnergyPercentage','0.data.reports.summary.channel-0.highEnergyPercentage','int',NULL,NULL,NULL),(176,'summary.channel-0.joyPercentage','0.data.reports.summary.channel-0.joyPercentage','int',NULL,NULL,NULL),(177,'summary.channel-0.lowAggressionPercentage','0.data.reports.summary.channel-0.lowAggressionPercentage','int',NULL,NULL,NULL),(178,'summary.channel-0.lowEnergyPercentage','0.data.reports.summary.channel-0.lowEnergyPercentage','int',NULL,NULL,NULL),(179,'summary.channel-0.mediumEnergyPercentage','0.data.reports.summary.channel-0.mediumEnergyPercentage','int',NULL,NULL,NULL),(180,'summary.channel-0.sadPercentage','0.data.reports.summary.channel-0.sadPercentage','int',NULL,NULL,NULL),(181,'summary.channel-0.segmentsCount','0.data.reports.summary.channel-0.segmentsCount','int',NULL,NULL,NULL),(182,'summary.channel-0.stressPercentage','0.data.reports.summary.channel-0.stressPercentage','int',NULL,NULL,NULL),(183,'summary.channel-0.volumeAverage','0.data.reports.summary.channel-0.volumeAverage','int',NULL,NULL,NULL),(184,'summary.general','0.data.reports.summary.general','arr',NULL,NULL,NULL),(185,'summary.general.code','0.data.reports.summary.general.code','string',NULL,NULL,NULL),(186,'summary.general.priority','0.data.reports.summary.general.priority','int',NULL,NULL,NULL);
/*!40000 ALTER TABLE `emlo_response_paths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_response_segments`
--

DROP TABLE IF EXISTS `emlo_response_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_response_segments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `number` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_response_segments`
--

LOCK TABLES `emlo_response_segments` WRITE;
/*!40000 ALTER TABLE `emlo_response_segments` DISABLE KEYS */;
INSERT INTO `emlo_response_segments` VALUES (1,0,'segment_no',NULL,NULL),(2,1,'channel',NULL,NULL),(3,2,'start_pos_sec',NULL,NULL),(4,3,'end_pos_sec',NULL,NULL),(5,4,'validSegment',NULL,NULL),(6,5,'topic',NULL,NULL),(7,6,'question',NULL,NULL),(8,7,'online_lva',NULL,NULL),(9,8,'risk1',NULL,NULL),(10,9,'risk2',NULL,NULL),(11,10,'riskOZ',NULL,NULL),(12,14,'energy',NULL,NULL),(13,15,'content',NULL,NULL),(14,16,'upset',NULL,NULL),(15,17,'angry',NULL,NULL),(16,18,'stress',NULL,NULL),(17,19,'uncertainty',NULL,NULL),(18,20,'uneasy',NULL,NULL),(19,21,'emotional',NULL,NULL),(20,22,'concentration',NULL,NULL),(21,23,'anticipation',NULL,NULL),(22,24,'hesitation',NULL,NULL),(23,25,'emoBalance',NULL,NULL),(24,26,'emoEnergyBalance',NULL,NULL),(25,27,'mentalEffort',NULL,NULL),(26,28,'imagin',NULL,NULL),(27,29,'sAF',NULL,NULL),(28,30,'oCA',NULL,NULL),(29,31,'emoCogRatio',NULL,NULL),(30,32,'extremeEmotion',NULL,NULL),(31,33,'atmosphere',NULL,NULL),(32,34,'cogHighLowBalance',NULL,NULL),(33,35,'voice_energy',NULL,NULL),(34,36,'dissat',NULL,NULL),(35,37,'lVAGLBStress',NULL,NULL),(36,38,'lVAEmoStress',NULL,NULL),(37,39,'lVACOGStress',NULL,NULL),(38,40,'lVAENRStress',NULL,NULL),(39,41,'lVAMentalEffort',NULL,NULL),(40,42,'lVASOSSTRESS',NULL,NULL),(41,43,'emoPlayerEnergy',NULL,NULL),(42,44,'emoPlayerJoy',NULL,NULL),(43,45,'emoPlayerSad',NULL,NULL),(44,46,'emoPlayerAggression',NULL,NULL),(45,47,'emoPlayerStress',NULL,NULL),(46,48,'emoPlayerRisk',NULL,NULL),(47,49,'finalRiskLevel',NULL,NULL),(48,50,'EDPEnergetic',NULL,NULL),(49,51,'EDPPassionate',NULL,NULL),(50,52,'EDPEmotional',NULL,NULL),(51,53,'EDPUneasy',NULL,NULL),(52,54,'EDPStressful',NULL,NULL),(53,55,'EDPThoughtful',NULL,NULL),(54,56,'EDPConfident',NULL,NULL),(55,57,'EDPConcentrated',NULL,NULL),(56,58,'EDPAnticipation',NULL,NULL),(57,59,'EDPHesitation',NULL,NULL),(58,60,'callPriority',NULL,NULL),(59,61,'callPriorityAgent',NULL,NULL),(60,62,'callDistressPriority',NULL,NULL),(61,63,'vOL1',NULL,NULL),(62,64,'vOL2',NULL,NULL),(63,65,'jQcl',NULL,NULL),(64,66,'sOS',NULL,NULL),(65,67,'aVJ',NULL,NULL),(66,68,'cHL',NULL,NULL),(67,69,'fant',NULL,NULL),(68,70,'fcen',NULL,NULL),(69,71,'fflic',NULL,NULL),(70,72,'fmain',NULL,NULL),(71,73,'fmainPos',NULL,NULL),(72,74,'fq',NULL,NULL),(73,75,'fsubCog',NULL,NULL),(74,76,'fsubEmo',NULL,NULL),(75,77,'fx',NULL,NULL),(76,78,'jQ',NULL,NULL),(77,79,'lJ',NULL,NULL),(78,80,'maxVolAmp',NULL,NULL),(79,81,'sampleSize',NULL,NULL),(80,82,'p1',NULL,NULL),(81,83,'p2',NULL,NULL),(82,84,'p3',NULL,NULL),(83,85,'sPJ',NULL,NULL),(84,86,'sPJhl',NULL,NULL),(85,87,'sPJll',NULL,NULL),(86,88,'sPJsh',NULL,NULL),(87,89,'sPJsl',NULL,NULL),(88,90,'sPT',NULL,NULL),(89,91,'sPST',NULL,NULL),(90,92,'sPBT',NULL,NULL),(91,93,'sPBth',NULL,NULL),(92,94,'sPBtl',NULL,NULL),(93,95,'sPSth',NULL,NULL),(94,96,'sPStl',NULL,NULL),(95,97,'sPBtl_DIF',NULL,NULL),(96,98,'sPBth_DIF',NULL,NULL),(97,99,'lJQ',NULL,NULL),(98,100,'mJQ',NULL,NULL),(99,101,'hJQ',NULL,NULL),(100,102,'sPJsav',NULL,NULL),(101,103,'sPJlav',NULL,NULL),(102,104,'intCHL',NULL,NULL),(103,105,'sPTJtot',NULL,NULL),(104,106,'sPJdist',NULL,NULL),(105,107,'sPJcomp',NULL,NULL),(106,108,'jHLratio',NULL,NULL),(107,109,'nCHL',NULL,NULL),(108,110,'cHLdif',NULL,NULL),(109,111,'cCCHL',NULL,NULL),(110,112,'sptBdiff',NULL,NULL),(111,113,'hASv',NULL,NULL),(112,114,'aVJcl',NULL,NULL),(113,115,'cPor',NULL,NULL),(114,116,'feelGPT',NULL,NULL),(115,117,'GPTCommand',NULL,NULL),(116,118,'offlineLVAValue',NULL,NULL),(117,119,'offlineLVARiskStress',NULL,NULL),(118,120,'offlineLVARiskProbability',NULL,NULL),(119,121,'offlineLVARiskEmotionStress',NULL,NULL),(120,122,'offlineLVARiskCognitiveStress',NULL,NULL),(121,123,'offlineLVARiskGlobalStress',NULL,NULL),(122,124,'sPBofflineLVARiskFrgStressT',NULL,NULL),(123,125,'offlineLVARiskSubjectiveEffortLevel',NULL,NULL),(124,126,'offlineLVARiskDeceptionPatterns',NULL,NULL),(125,127,'lVARiskStress',NULL,NULL),(126,128,'offline_lva',NULL,NULL),(127,129,'iThink',NULL,NULL),(128,130,'aF1',NULL,NULL),(129,131,'aF2',NULL,NULL),(130,132,'aF3',NULL,NULL),(131,133,'aF4',NULL,NULL),(132,134,'aF5',NULL,NULL),(133,135,'aF6',NULL,NULL),(134,136,'aF7',NULL,NULL),(135,137,'aF8',NULL,NULL),(136,138,'aF9',NULL,NULL),(137,139,'aF10',NULL,NULL),(138,0,'index',NULL,NULL),(139,1,'channel',NULL,NULL),(140,2,'startPosSec',NULL,NULL),(141,3,'endPosSec',NULL,NULL),(142,4,'validSegment',NULL,NULL),(143,5,'topics',NULL,NULL),(144,6,'question',NULL,NULL),(145,7,'onlineLVA',NULL,NULL),(146,8,'risk1',NULL,NULL),(147,9,'risk2',NULL,NULL),(148,10,'riskOZ',NULL,NULL),(149,11,'oz1',NULL,NULL),(150,12,'oz2',NULL,NULL),(151,13,'oz3',NULL,NULL),(152,14,'energy',NULL,NULL),(153,15,'joy',NULL,NULL),(154,16,'sad',NULL,NULL),(155,17,'aggression',NULL,NULL),(156,18,'stress',NULL,NULL),(157,19,'uncertainty',NULL,NULL),(158,20,'excitement',NULL,NULL),(159,21,'uneasy',NULL,NULL),(160,22,'concentration',NULL,NULL),(161,23,'anticipation',NULL,NULL),(162,24,'hesitation',NULL,NULL),(163,25,'emotionBalance',NULL,NULL),(164,26,'emotionEnergyBalance',NULL,NULL),(165,27,'mentalEffort',NULL,NULL),(166,28,'imagination',NULL,NULL),(167,29,'arousal',NULL,NULL),(168,30,'overallCognitiveActivity',NULL,NULL),(169,31,'emotionCognitiveRatio',NULL,NULL),(170,32,'extremeEmotion',NULL,NULL),(171,33,'atmosphere',NULL,NULL),(172,34,'cognitiveHighLowBalance',NULL,NULL),(173,35,'voiceEnergy',NULL,NULL),(174,36,'dissatisfied',NULL,NULL),(175,37,'LVA-GlobalStress',NULL,NULL),(176,38,'LVA-EmotionStress',NULL,NULL),(177,39,'LVA-CognitiveStress',NULL,NULL),(178,40,'LVA-EnergyStress ',NULL,NULL),(179,41,'LVA-MentalEffort',NULL,NULL),(180,42,'LVA-SOSStress',NULL,NULL),(181,43,'EmotionPlayer-Energy',NULL,NULL),(182,44,'EmotionPlayer-Joy',NULL,NULL),(183,45,'EmotionPlayer-Sad',NULL,NULL),(184,46,'EmotionPlayer-Aggression',NULL,NULL),(185,47,'EmotionPlayer-Stress',NULL,NULL),(186,48,'EmotionPlayer-Risk',NULL,NULL),(187,49,'finalRiskLevel',NULL,NULL),(188,50,'EDP-Energetic',NULL,NULL),(189,51,'EDP-Passionate',NULL,NULL),(190,52,'EDP-Emotional',NULL,NULL),(191,53,'EDP-Uneasy',NULL,NULL),(192,54,'EDP-Stressful',NULL,NULL),(193,55,'EDP-Thoughtful',NULL,NULL),(194,56,'EDP-Confident',NULL,NULL),(195,57,'EDP-Concentrated',NULL,NULL),(196,58,'EDP-Anticipation',NULL,NULL),(197,59,'EDP-Hesitation',NULL,NULL),(198,60,'callPriority',NULL,NULL),(199,61,'callPriorityAgent',NULL,NULL),(200,62,'callDistressPriority',NULL,NULL),(201,63,'VOL1',NULL,NULL),(202,64,'VOL2',NULL,NULL),(203,65,'JQcl',NULL,NULL),(204,66,'SOS',NULL,NULL),(205,67,'AVJ',NULL,NULL),(206,68,'CHL',NULL,NULL),(207,69,'Fant',NULL,NULL),(208,70,'Fcen',NULL,NULL),(209,71,'Fflic',NULL,NULL),(210,72,'Fmain',NULL,NULL),(211,73,'FmainPos',NULL,NULL),(212,74,'Fq',NULL,NULL),(213,75,'FsubCog',NULL,NULL),(214,76,'FsubEmo',NULL,NULL),(215,77,'Fx',NULL,NULL),(216,78,'JQ',NULL,NULL),(217,79,'LJ',NULL,NULL),(218,80,'MaxVolAmp',NULL,NULL),(219,81,'sampleSize',NULL,NULL),(220,82,'P1',NULL,NULL),(221,83,'P2',NULL,NULL),(222,84,'P3',NULL,NULL),(223,85,'SPJ',NULL,NULL),(224,86,'SPJhl',NULL,NULL),(225,87,'SPJll',NULL,NULL),(226,88,'SPJsh',NULL,NULL),(227,89,'SPJsl',NULL,NULL),(228,90,'SPT',NULL,NULL),(229,91,'SPST',NULL,NULL),(230,92,'SPBT',NULL,NULL),(231,93,'SPBth',NULL,NULL),(232,94,'SPBtl',NULL,NULL),(233,95,'SPSth',NULL,NULL),(234,96,'SPStl',NULL,NULL),(235,97,'SPBtl_DIF',NULL,NULL),(236,98,'SPBth_DIF',NULL,NULL),(237,99,'lJQ',NULL,NULL),(238,100,'mJQ',NULL,NULL),(239,101,'hJQ',NULL,NULL),(240,102,'SPJsav',NULL,NULL),(241,103,'SPJlav',NULL,NULL),(242,104,'intCHL',NULL,NULL),(243,105,'SPTJtot',NULL,NULL),(244,106,'SPJdist',NULL,NULL),(245,107,'SPJcomp',NULL,NULL),(246,108,'JHLratio',NULL,NULL),(247,109,'nCHL',NULL,NULL),(248,110,'CHLdif',NULL,NULL),(249,111,'CCCHL',NULL,NULL),(250,112,'sptBdiff',NULL,NULL),(251,113,'HASv',NULL,NULL),(252,114,'AVJcl',NULL,NULL),(253,115,'cPor',NULL,NULL),(254,116,'feelGPT',NULL,NULL),(255,117,'GPTCommand',NULL,NULL),(256,118,'offlineLVA-value',NULL,NULL),(257,119,'offlineLVA-riskStress',NULL,NULL),(258,120,'offlineLVA-riskProbability',NULL,NULL),(259,121,'offlineLVA-emotionStress',NULL,NULL),(260,122,'offlineLVA-cognitiveStress',NULL,NULL),(261,123,'offlineLVA-globalStress',NULL,NULL),(262,124,'offlineLVA-frgStress',NULL,NULL),(263,125,'offlineLVA-subjectiveEffortLevel',NULL,NULL),(264,126,'offlineLVA-deceptionPatterns',NULL,NULL),(265,127,'lVARiskStress',NULL,NULL),(266,128,'offline_lva',NULL,NULL),(267,129,'iThink',NULL,NULL),(268,130,'aF1',NULL,NULL),(269,131,'aF2',NULL,NULL),(270,132,'aF3',NULL,NULL),(271,133,'aF4',NULL,NULL),(272,134,'aF5',NULL,NULL),(273,135,'aF6',NULL,NULL),(274,136,'aF7',NULL,NULL),(275,137,'aF8',NULL,NULL),(276,138,'aF9',NULL,NULL),(277,139,'aF10',NULL,NULL);
/*!40000 ALTER TABLE `emlo_response_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_response_values`
--

DROP TABLE IF EXISTS `emlo_response_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_response_values` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `response_id` int unsigned NOT NULL,
  `path_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `string_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `numeric_value` int DEFAULT NULL,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `emlo_param_spec_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_response_values_response_id_foreign` (`response_id`),
  KEY `emlo_response_values_path_id_foreign` (`path_id`),
  KEY `emlo_response_values_emlo_param_spec_id_foreign` (`emlo_param_spec_id`),
  CONSTRAINT `emlo_response_values_emlo_param_spec_id_foreign` FOREIGN KEY (`emlo_param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`),
  CONSTRAINT `emlo_response_values_path_id_foreign` FOREIGN KEY (`path_id`) REFERENCES `emlo_response_paths` (`id`),
  CONSTRAINT `emlo_response_values_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `emlo_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_response_values`
--

LOCK TABLES `emlo_response_values` WRITE;
/*!40000 ALTER TABLE `emlo_response_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `emlo_response_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emlo_responses`
--

DROP TABLE IF EXISTS `emlo_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emlo_responses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int unsigned NOT NULL,
  `raw_response` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emlo_responses_request_id_foreign` (`request_id`),
  CONSTRAINT `emlo_responses_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emlo_responses`
--

LOCK TABLES `emlo_responses` WRITE;
/*!40000 ALTER TABLE `emlo_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `emlo_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=668 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kpi_metric_specifications`
--

DROP TABLE IF EXISTS `kpi_metric_specifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_metric_specifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kpi_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `video_question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `range` double DEFAULT NULL,
  `significance` double DEFAULT NULL,
  `emlo_param_spec_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kpi_metric_specifications_kpi_id_foreign` (`kpi_id`),
  KEY `kpi_metric_specifications_emlo_response_param_specs_FK` (`emlo_param_spec_id`),
  KEY `kpi_metric_specifications_significance_IDX` (`significance`,`emlo_param_spec_id`) USING BTREE,
  CONSTRAINT `kpi_metric_specifications_emlo_response_param_specs_FK` FOREIGN KEY (`emlo_param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`),
  CONSTRAINT `kpi_metric_specifications_kpi_id_foreign` FOREIGN KEY (`kpi_id`) REFERENCES `cred_score_kpis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kpi_metric_specifications`
--

LOCK TABLES `kpi_metric_specifications` WRITE;
/*!40000 ALTER TABLE `kpi_metric_specifications` DISABLE KEYS */;
INSERT INTO `kpi_metric_specifications` VALUES (1,NULL,NULL,1,NULL,NULL,'What did you accomplish today, and what’s still on your list?',NULL,NULL,NULL),(2,NULL,NULL,2,NULL,NULL,'What’s the biggest highlight or headline from your day?',NULL,NULL,NULL),(3,NULL,NULL,3,NULL,NULL,'What thoughts or ideas are on your mind today?',NULL,NULL,NULL),(4,NULL,NULL,4,NULL,NULL,'What dream do you remember from last night? How did it feel?',NULL,NULL,NULL),(5,NULL,NULL,5,NULL,NULL,'What’s one of your favorite things you enjoy or makes you happy?',NULL,NULL,NULL),(6,NULL,NULL,6,NULL,NULL,'What is the your story or feelings behind the photo?',NULL,NULL,NULL),(7,NULL,NULL,7,NULL,NULL,'What milestone or big event do you want to capture today?',NULL,NULL,NULL),(8,NULL,NULL,8,NULL,NULL,'What’s an important health detail or progress you want to note today?',NULL,NULL,NULL),(9,NULL,NULL,9,NULL,NULL,'What family custom, recipe, or celebration is meaningful to you?',NULL,NULL,NULL),(10,NULL,NULL,10,NULL,NULL,'What belief, value, or spiritual thought guides you today?',NULL,NULL,NULL),(11,NULL,NULL,11,NULL,'I feel calm and able to handle today’s challenges.','What’s been your biggest source of stress today?',10,1,NULL),(12,NULL,NULL,11,NULL,NULL,NULL,100,1,15),(13,NULL,NULL,11,NULL,NULL,NULL,100,1,8),(14,NULL,NULL,11,NULL,NULL,NULL,100,1,3),(15,NULL,NULL,11,NULL,NULL,NULL,100,1,10),(16,NULL,NULL,12,NULL,'My emotions feel steady and balanced today.','What triggered your emotions to change today?',10,1,NULL),(17,NULL,NULL,12,NULL,NULL,NULL,100,1,15),(18,NULL,NULL,12,NULL,NULL,NULL,100,1,4),(19,NULL,NULL,12,NULL,NULL,NULL,100,1,5),(20,NULL,NULL,12,NULL,NULL,NULL,100,1,8),(21,NULL,NULL,13,NULL,'I feel confident and sure of myself right now.','What gave you confidence or pride today?',10,1,NULL),(22,NULL,NULL,13,NULL,NULL,NULL,100,1,15),(23,NULL,NULL,13,NULL,NULL,NULL,100,1,3),(24,NULL,NULL,13,NULL,NULL,NULL,100,1,5),(25,NULL,NULL,13,NULL,NULL,NULL,100,1,7),(26,NULL,NULL,14,NULL,'I can manage my anger without losing control.','What made you angry, and how did you calm down?',10,1,NULL),(27,NULL,NULL,14,NULL,NULL,NULL,100,1,15),(28,NULL,NULL,14,NULL,NULL,NULL,100,1,13),(29,NULL,NULL,14,NULL,NULL,NULL,100,1,8),(30,NULL,NULL,14,NULL,NULL,NULL,100,1,3),(31,NULL,NULL,15,NULL,'I feel grateful for something in my life today.','What are you most thankful for right now?',10,1,NULL),(32,NULL,NULL,15,NULL,NULL,NULL,100,1,15),(33,NULL,NULL,15,NULL,NULL,NULL,100,1,9),(34,NULL,NULL,15,NULL,NULL,NULL,100,1,4),(35,NULL,NULL,15,NULL,NULL,NULL,100,1,5),(36,NULL,NULL,16,NULL,'My thoughts and emotions feel in sync today.','Why do you feel mentally and emotionally balanced today?',10,1,NULL),(37,NULL,NULL,16,NULL,NULL,NULL,100,1,15),(38,NULL,NULL,16,NULL,NULL,NULL,100,1,8),(39,NULL,NULL,16,NULL,NULL,NULL,100,1,3),(40,NULL,NULL,16,NULL,NULL,NULL,100,1,10),(41,NULL,NULL,17,NULL,'I can clearly notice what triggers my emotions.','What reaction stood out today, and what caused it?',10,1,NULL),(42,NULL,NULL,17,NULL,NULL,NULL,100,1,15),(43,NULL,NULL,17,NULL,NULL,NULL,100,1,4),(44,NULL,NULL,17,NULL,NULL,NULL,100,1,3),(45,NULL,NULL,17,NULL,NULL,NULL,100,1,8),(46,NULL,NULL,18,NULL,'I understand others’ feelings even when I disagree','Think of someone you disagreed with — what were they feeling?',10,1,NULL),(47,NULL,NULL,18,NULL,NULL,NULL,100,1,15),(48,NULL,NULL,18,NULL,NULL,NULL,100,1,4),(49,NULL,NULL,18,NULL,NULL,NULL,100,1,10),(50,NULL,NULL,18,NULL,NULL,NULL,100,1,7),(51,NULL,NULL,19,NULL,'I can describe my feelings with clear and specific words.','Which 5 words capture your emotions today?',10,1,NULL),(52,NULL,NULL,19,NULL,NULL,NULL,100,1,15),(53,NULL,NULL,19,NULL,NULL,NULL,100,1,4),(54,NULL,NULL,19,NULL,NULL,NULL,100,1,8),(55,NULL,NULL,19,NULL,NULL,NULL,100,1,3),(56,NULL,NULL,20,NULL,'I’m aware of the emotional impact I have on others.','How do you think others felt being around you today?',10,1,NULL),(57,NULL,NULL,20,NULL,NULL,NULL,100,1,15),(58,NULL,NULL,20,NULL,NULL,NULL,100,1,9),(59,NULL,NULL,20,NULL,NULL,NULL,100,1,4),(60,NULL,NULL,20,NULL,NULL,NULL,100,1,5),(61,NULL,NULL,21,NULL,'I recorded my Vijo today as part of my habit.','How does recording today build your journaling habit?',10,1,NULL),(62,NULL,NULL,21,NULL,NULL,NULL,100,1,15),(63,NULL,NULL,21,NULL,NULL,NULL,100,1,2),(64,NULL,NULL,21,NULL,NULL,NULL,100,1,1),(65,NULL,NULL,21,NULL,NULL,NULL,100,1,8),(66,NULL,NULL,22,NULL,'I made meaningful progress toward my goal today.','What did you do today that moved your goal forward?',10,1,NULL),(67,NULL,NULL,22,NULL,NULL,NULL,100,1,15),(68,NULL,NULL,22,NULL,NULL,NULL,100,1,3),(69,NULL,NULL,22,NULL,NULL,NULL,100,1,7),(70,NULL,NULL,22,NULL,NULL,NULL,100,1,9),(71,NULL,NULL,23,NULL,'I viewed challenges as opportunities to learn.','What challenge taught you something today?',10,1,NULL),(72,NULL,NULL,23,NULL,NULL,NULL,100,1,15),(73,NULL,NULL,23,NULL,NULL,NULL,100,1,1),(74,NULL,NULL,23,NULL,NULL,NULL,100,1,3),(75,NULL,NULL,23,NULL,NULL,NULL,100,1,8),(76,NULL,NULL,24,NULL,'I stepped outside my comfort zone today.','What felt uncomfortable, and why was it worth doing?',10,1,NULL),(77,NULL,NULL,24,NULL,NULL,NULL,100,1,15),(78,NULL,NULL,24,NULL,NULL,NULL,100,1,10),(79,NULL,NULL,24,NULL,NULL,NULL,100,1,3),(80,NULL,NULL,24,NULL,NULL,NULL,100,1,8),(81,NULL,NULL,25,NULL,'I recovered well after a setback.','Describe a setback—how did you respond and reset?',10,1,NULL),(82,NULL,NULL,25,NULL,NULL,NULL,100,1,15),(83,NULL,NULL,25,NULL,NULL,NULL,100,1,8),(84,NULL,NULL,25,NULL,NULL,NULL,100,1,3),(85,NULL,NULL,25,NULL,NULL,NULL,100,1,4),(86,NULL,NULL,26,NULL,'I managed my energy wisely today.','What boosted or drained your energy today and why?',10,1,NULL),(87,NULL,NULL,26,NULL,NULL,NULL,100,1,15),(88,NULL,NULL,26,NULL,NULL,NULL,100,1,5),(89,NULL,NULL,26,NULL,NULL,NULL,100,1,9),(90,NULL,NULL,26,NULL,NULL,NULL,100,1,4),(91,NULL,NULL,27,NULL,'I learned something useful today','What insight did you gain today, and from what?',10,1,NULL),(92,NULL,NULL,27,NULL,NULL,NULL,100,1,15),(93,NULL,NULL,27,NULL,NULL,NULL,100,1,9),(94,NULL,NULL,27,NULL,NULL,NULL,100,1,1),(95,NULL,NULL,27,NULL,NULL,NULL,100,1,4),(96,NULL,NULL,28,NULL,' I took a step toward a fear today.','What fear did you face? What tiny step did you take?',10,1,NULL),(97,NULL,NULL,28,NULL,NULL,NULL,100,1,15),(98,NULL,NULL,28,NULL,NULL,NULL,100,1,10),(99,NULL,NULL,28,NULL,NULL,NULL,100,1,3),(100,NULL,NULL,28,NULL,NULL,NULL,100,1,8),(101,NULL,NULL,29,NULL,'My actions aligned with my core values today.','Which value guided you today? How did you live it?',10,1,NULL),(102,NULL,NULL,29,NULL,NULL,NULL,100,1,15),(103,NULL,NULL,29,NULL,NULL,NULL,100,1,3),(104,NULL,NULL,29,NULL,NULL,NULL,100,1,1),(105,NULL,NULL,29,NULL,NULL,NULL,100,1,7),(106,NULL,NULL,30,NULL,'I noticed a pattern I want to change.','What recurring pattern do you see? What’s your plan?',10,1,NULL),(107,NULL,NULL,30,NULL,NULL,NULL,100,1,15),(108,NULL,NULL,30,NULL,NULL,NULL,100,1,8),(109,NULL,NULL,30,NULL,NULL,NULL,100,1,3),(110,NULL,NULL,30,NULL,NULL,NULL,100,1,1);
/*!40000 ALTER TABLE `kpi_metric_specifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kpi_metric_values`
--

DROP TABLE IF EXISTS `kpi_metric_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_metric_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kpi_metric_spec_id` bigint unsigned NOT NULL,
  `request_id` int unsigned NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kpi_metric_values_kpi_metric_spec_id_foreign` (`kpi_metric_spec_id`),
  KEY `kpi_metric_values_request_id_foreign` (`request_id`),
  CONSTRAINT `kpi_metric_values_kpi_metric_spec_id_foreign` FOREIGN KEY (`kpi_metric_spec_id`) REFERENCES `kpi_metric_specifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kpi_metric_values_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kpi_metric_values`
--

LOCK TABLES `kpi_metric_values` WRITE;
/*!40000 ALTER TABLE `kpi_metric_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `kpi_metric_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llm_responses`
--

DROP TABLE IF EXISTS `llm_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `llm_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `llm_responses_request_id_foreign` (`request_id`),
  CONSTRAINT `llm_responses_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llm_responses`
--

LOCK TABLES `llm_responses` WRITE;
/*!40000 ALTER TABLE `llm_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `llm_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llm_templates`
--

DROP TABLE IF EXISTS `llm_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `llm_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `system_prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `llm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `examples` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `llm_temperature` decimal(8,2) NOT NULL,
  `llm_response_max_length` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `llm_templates_user_id_foreign` (`user_id`),
  CONSTRAINT `llm_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llm_templates`
--

LOCK TABLES `llm_templates` WRITE;
/*!40000 ALTER TABLE `llm_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `llm_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membership_plans`
--

DROP TABLE IF EXISTS `membership_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membership_plans` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_mode` tinyint NOT NULL DEFAULT '1' COMMENT '1: One Time, 2: Recurring',
  `monthly_cost` double NOT NULL DEFAULT '0',
  `annual_cost` double NOT NULL DEFAULT '0',
  `payment_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactivated, 1: Active, 2: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `membership_plans_slug_unique` (`slug`),
  UNIQUE KEY `membership_plans_price_id_unique` (`price_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membership_plans`
--

LOCK TABLES `membership_plans` WRITE;
/*!40000 ALTER TABLE `membership_plans` DISABLE KEYS */;
INSERT INTO `membership_plans` VALUES (1,'Basic','basic','Register for freemium access to guided journals, 7-day history, and weekly emotional insights',1,0,0,'https://buy.stripe.com/test_28o5kB7UieR98BqfZ0',NULL,1,NULL,NULL),(2,'Vijo+','vijoplus','Access complete journal history, emotional insights, and premium content – and emotional progress reports',1,0,0,'https://buy.stripe.com/test_28o5kB7UieR98BqfZ0','price_1QaVAa2MTNMYHGSeHOAhmA4m',1,NULL,NULL);
/*!40000 ALTER TABLE `membership_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int unsigned DEFAULT NULL,
  `customerID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: paid, 2: failed, 3: refunded',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=702 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_codes`
--

DROP TABLE IF EXISTS `referral_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referral_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` int unsigned NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `number_uses` int NOT NULL DEFAULT '0',
  `max_number_uses` int DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referral_codes_code_unique` (`code`),
  KEY `referral_codes_affiliate_id_foreign` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral_codes`
--

LOCK TABLES `referral_codes` WRITE;
/*!40000 ALTER TABLE `referral_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rule_conditions`
--

DROP TABLE IF EXISTS `rule_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rule_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition` json NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_index` int NOT NULL,
  `active` tinyint(1) NOT NULL,
  `emotion_performance` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `rule_conditions_rule_id_foreign` (`rule_id`),
  CONSTRAINT `rule_conditions_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rule_conditions`
--

LOCK TABLES `rule_conditions` WRITE;
/*!40000 ALTER TABLE `rule_conditions` DISABLE KEYS */;
INSERT INTO `rule_conditions` VALUES (1,1,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Anticipation\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Anticipation\", \"value\": -0.68, \"operator\": \"<\"}]}','Low Anticipation: You\'re showing minimal forward-thinking or expectation, possibly feeling disconnected from upcoming events.',1,1,'Below Normal'),(2,1,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Anticipation\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Anticipation\", \"value\": 0.67, \"operator\": \"<\"}]}','Balanced Anticipation: Your anticipation is healthy and well-regulated, keeping you engaged without feeling overwhelmed.',2,1,'Normal'),(3,1,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Anticipation\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Anticipation\", \"value\": 3, \"operator\": \"<\"}]}','High Anticipation: You\'re highly focused on what\'s coming next with excitement or anxiety that may need monitoring.',3,1,'Above Normal'),(4,2,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Concentrated\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Concentrated\", \"value\": -0.68, \"operator\": \"<\"}]}','Reduced Focus: Your concentration appears scattered, suggesting a need for breaks or fewer distractions.',1,1,'Below Normal'),(5,2,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Concentrated\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Concentrated\", \"value\": 0.67, \"operator\": \"<\"}]}','Steady Focus: You\'re maintaining good concentration that supports clear thinking without mental strain.',2,1,'Normal'),(6,2,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Concentrated\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Concentrated\", \"value\": 3, \"operator\": \"<\"}]}','Intense Focus: Your concentration is very high, showing deep engagement that may require periodic breaks.',3,1,'Above Normal'),(7,7,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Passionate\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Passionate\", \"value\": -0.68, \"operator\": \"<\"}]}','Low Enthusiasm: Your emotional investment appears minimal, possibly indicating fatigue or disconnection.',1,1,'Below Normal'),(8,7,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Passionate\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Passionate\", \"value\": 0.67, \"operator\": \"<\"}]}','Balanced Passion: You\'re showing healthy enthusiasm that helps you connect authentically with others.',2,1,'Normal'),(9,7,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Passionate\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Passionate\", \"value\": 3, \"operator\": \"<\"}]}','High Passion: Your voice conveys strong emotional investment that should be channeled constructively.',3,1,'Above Normal'),(10,10,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Uneasy\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Uneasy\", \"value\": -0.68, \"operator\": \"<\"}]}','Calm and Comfortable: You\'re showing minimal discomfort, supporting clear thinking and positive interactions.',1,1,'Below Normal'),(11,10,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Uneasy\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Uneasy\", \"value\": 0.67, \"operator\": \"<\"}]}','Mild Uneasiness: You\'re experiencing typical situational discomfort that doesn\'t indicate significant distress.',2,1,'Normal'),(12,10,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Uneasy\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Uneasy\", \"value\": 3, \"operator\": \"<\"}]}','Elevated Anxiety: Your voice reveals notable worry that may benefit from stress-relief techniques.',3,1,'Above Normal'),(13,3,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Confident\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Confident\", \"value\": -0.68, \"operator\": \"<\"}]}','Reduced Confidence: Your self-assurance is lower than usual but can be rebuilt with practice.',1,1,'Below Normal'),(14,3,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Confident\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Confident\", \"value\": 0.67, \"operator\": \"<\"}]}','Healthy Confidence: You\'re expressing appropriate self-assurance that supports effective communication.',2,1,'Normal'),(15,3,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Confident\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Confident\", \"value\": 3, \"operator\": \"<\"}]}','Strong Confidence: Your voice projects high self-assurance that should remain balanced with openness.',3,1,'Above Normal'),(16,4,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Emotional\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Emotional\", \"value\": -0.68, \"operator\": \"<\"}]}','Emotionally Reserved: You\'re showing limited emotional expression, which may be situational or indicate distance.',1,1,'Below Normal'),(17,4,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Emotional\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Emotional\", \"value\": 0.67, \"operator\": \"<\"}]}','Balanced Expression: Your emotional expression is natural and appropriate for the situation.',2,1,'Normal'),(18,4,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Emotional\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Emotional\", \"value\": 3, \"operator\": \"<\"}]}','Heightened Emotion: You\'re expressing strong emotions that may benefit from supportive outlets.',3,1,'Above Normal'),(19,5,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Energetic\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Energetic\", \"value\": -0.68, \"operator\": \"<\"}]}','Low Energy: Your voice suggests fatigue that may require rest or energizing activities.',1,1,'Below Normal'),(20,5,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Energetic\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Energetic\", \"value\": 0.67, \"operator\": \"<\"}]}','Balanced Energy: You\'re showing healthy, sustainable energy levels for daily activities.',2,1,'Normal'),(21,5,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Energetic\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Energetic\", \"value\": 3, \"operator\": \"<\"}]}','High Energy: Your voice conveys strong vitality that should be harnessed productively.',3,1,'Above Normal'),(22,6,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Hesitation\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Hesitation\", \"value\": -0.68, \"operator\": \"<\"}]}','Direct Speech: You\'re speaking decisively with minimal pauses, showing clarity and confidence.',1,1,'Below Normal'),(23,6,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Hesitation\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Hesitation\", \"value\": 0.67, \"operator\": \"<\"}]}','Natural Pausing: Your speech includes normal pauses that support thoughtful communication.',2,1,'Normal'),(24,6,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Hesitation\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Hesitation\", \"value\": 3, \"operator\": \"<\"}]}','Frequent Hesitation: You\'re pausing often, indicating careful consideration or reduced confidence.',3,1,'Above Normal'),(25,8,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Stressful\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Stressful\", \"value\": -0.68, \"operator\": \"<\"}]}','Low Stress: Your voice shows minimal tension, promoting clear thinking and well-being.',1,1,'Below Normal'),(26,8,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Stressful\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Stressful\", \"value\": 0.67, \"operator\": \"<\"}]}','Normal Stress: You\'re experiencing typical daily stress that can enhance performance when balanced.',2,1,'Normal'),(27,8,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Stressful\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Stressful\", \"value\": 3, \"operator\": \"<\"}]}','High Stress: Your voice reveals elevated stress that requires active management techniques.',3,1,'Above Normal'),(28,9,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Thoughtful\", \"value\": -3, \"operator\": \">\"}, {\"param\": \"EDP-Thoughtful\", \"value\": -0.68, \"operator\": \"<\"}]}','Quick Responses: You\'re speaking spontaneously, which is efficient but may skip important considerations.',1,1,'Below Normal'),(29,9,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Thoughtful\", \"value\": -0.67, \"operator\": \">\"}, {\"param\": \"EDP-Thoughtful\", \"value\": 0.67, \"operator\": \"<\"}]}','Balanced Thinking: You\'re taking appropriate time to consider your words thoughtfully.',2,1,'Normal'),(30,9,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"EDP-Thoughtful\", \"value\": 0.67, \"operator\": \">\"}, {\"param\": \"EDP-Thoughtful\", \"value\": 3, \"operator\": \"<\"}]}','Deep Contemplation: You\'re carefully considering responses, balancing thoroughness with decision speed.',3,1,'Above Normal'),(31,11,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"finalRiskLevel\", \"value\": 1.0, \"operator\": \">\"}, {\"param\": \"finalRiskLevel\", \"value\": 40.0, \"operator\": \"<\"}]}','Balanced Caution: You\'re showing appropriate awareness of challenges for prudent decision-making.',1,1,'Normal '),(32,11,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"finalRiskLevel\", \"value\": 41.0, \"operator\": \">\"}, {\"param\": \"finalRiskLevel\", \"value\": 60.0, \"operator\": \"<\"}]}','Heightened Vigilance: Your increased risk awareness is protective but shouldn\'t paralyze necessary action.',2,1,'Above Normal'),(33,11,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"finalRiskLevel\", \"value\": 61.0, \"operator\": \">\"}, {\"param\": \"finalRiskLevel\", \"value\": 100, \"operator\": \"<\"}]}','High Alert: You\'re perceiving significant threats that may be protective but could increase stress.',3,1,'High'),(34,14,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"Aggression\", \"value\": -1, \"operator\": \">\"}, {\"param\": \"Aggression\", \"value\": 1, \"operator\": \"<\"}]}','No Aggression Detected: Your voice sounds calm and composed with no signs of anger or frustration',1,1,'Normal '),(35,14,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"Aggression\", \"value\": 1, \"operator\": \">\"}, {\"param\": \"Aggression\", \"value\": 2, \"operator\": \"<\"}]}','Milld Aggression Detected: Your voice shows some tension and mild frustration coming through in your tone',2,1,'Above Normal'),(36,14,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"Aggression\", \"value\": 3, \"operator\": \">\"}, {\"param\": \"Aggression\", \"value\": 100, \"operator\": \"<\"}]}','High Aggression Detected: Your voice indicates significant anger and tension that\'s strongly affecting your communication.',3,1,'High'),(37,13,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"overallCognitiveActivity\", \"value\": 1, \"operator\": \">\"}, {\"param\": \"overallCognitiveActivity\", \"value\": 350, \"operator\": \"<\"}]}','Mental Disconnect: Your thoughts and emotions are out of sync, possibly from distraction or overwhelm.',1,1,'Disconnected'),(38,13,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"overallCognitiveActivity\", \"value\": 351, \"operator\": \">\"}, {\"param\": \"overallCognitiveActivity\", \"value\": 1099, \"operator\": \"<\"}]}','Mental Harmony: Your thinking and feeling are well-integrated for clear decisions.',2,1,'Steady'),(39,13,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"overallCognitiveActivity\", \"value\": 1100, \"operator\": \">\"}, {\"param\": \"overallCognitiveActivity\", \"value\": 1200, \"operator\": \"<\"}]}','Mental Conflict: You\'re experiencing tension between logic and emotion causing stress.',3,1,'Tense'),(40,13,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"overallCognitiveActivity\", \"value\": 1201, \"operator\": \">\"}, {\"param\": \"overallCognitiveActivity\", \"value\": 2000, \"operator\": \"<\"}]}','Cognitive Overload: Your mental resources are overwhelmed and need a reset break.',4,1,'Overloaded '),(41,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 0, \"operator\": \"=\"}','Emotionally Detached: You\'re showing minimal stress but also low engagement with life\'s challenges.',1,1,'No stress - emotionally disengaged'),(42,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 1, \"operator\": \"=\"}','Excellent Resilience: You\'re managing stress wonderfully with effective coping strategies.',2,1,'Low stress with good recovery'),(43,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 2, \"operator\": \"=\"}','Good Resilience: You\'re handling moderate stress well with healthy recovery patterns.',3,1,'Medium stress with good recovery'),(44,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 3, \"operator\": \"=\"}','Resilient Under Pressure: Despite high stress, you\'re recovering well but should monitor for fatigue.',4,1,'High stress with good recovery'),(45,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 4, \"operator\": \"=\"}','Struggling to Recover: You\'re under high stress and need additional support or stress reduction.',5,1,'High stress with difficult recovery'),(46,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 5, \"operator\": \"=\"}','Overwhelmed: Your stress remains high without relief, requiring immediate intervention.',6,1,'High stress with no recovery'),(47,15,NULL,NULL,'{\"type\": \"simple\", \"param\": \"clStress\", \"value\": 6, \"operator\": \"=\"}','Critical Stress Level: You\'re experiencing extreme stress that needs immediate professional support.',7,1,'Extreme stress requiring attention'),(48,16,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"self_honesty\", \"value\": 1, \"operator\": \">\"}, {\"param\": \"self_honesty\", \"value\": 40, \"operator\": \"<\"}]}','Guarded Expression: You\'re holding back significant parts of your truth, filtering your words carefully which may be creating distance from authentic communication.',1,1,'Normal '),(49,16,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"self_honesty\", \"value\": 41, \"operator\": \">\"}, {\"param\": \"self_honesty\", \"value\": 60, \"operator\": \"<\"}]}','Emerging Openness: You\'re beginning to express more genuine thoughts and feelings, though some hesitation suggests you\'re still protecting certain vulnerabilities.',2,1,'Above Normal'),(50,16,NULL,NULL,'{\"type\": \"compound\", \"operator\": \"AND\", \"conditions\": [{\"param\": \"self_honesty\", \"value\": 61, \"operator\": \">\"}, {\"param\": \"self_honesty\", \"value\": 100, \"operator\": \"<\"}]}','Authentic Transparency: You\'re speaking with full self-honesty, expressing your genuine thoughts and feelings openly without filters or hesitation—this authentic communication builds trust and connection.',3,1,'High');
/*!40000 ALTER TABLE `rule_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `param_spec_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rules_param_spec_id_foreign` (`param_spec_id`),
  CONSTRAINT `rules_param_spec_id_foreign` FOREIGN KEY (`param_spec_id`) REFERENCES `emlo_response_param_specs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rules`
--

LOCK TABLES `rules` WRITE;
/*!40000 ALTER TABLE `rules` DISABLE KEYS */;
INSERT INTO `rules` VALUES (1,NULL,NULL,'EDP-Anticipation',1,1),(2,NULL,NULL,'EDP-Concentrated',1,2),(3,NULL,NULL,'EDP-Confident',1,3),(4,NULL,NULL,'EDP-Emotional',1,4),(5,NULL,NULL,'EDP-Energetic',1,5),(6,NULL,NULL,'EDP-Hesitation',1,6),(7,NULL,NULL,'EDP-Passionate',1,7),(8,NULL,NULL,'EDP-Stressful',1,8),(9,NULL,NULL,'EDP-Thoughtful',1,9),(10,NULL,NULL,'EDP-Uneasy',1,10),(11,NULL,NULL,'finalRiskLevel',1,11),(13,NULL,NULL,'overallCognitiveActivity',1,12),(14,NULL,NULL,'Aggresion',1,13),(15,NULL,NULL,'clStress',1,14),(16,NULL,NULL,'self_honesty',1,15);
/*!40000 ALTER TABLE `rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `plan_id` int unsigned DEFAULT NULL,
  `stripe_customer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_subscription_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: active, 2: inactive, 3: canceled, 4: past_due, 5: unpaid',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `cancel_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancel_at_period_end` tinyint(1) NOT NULL DEFAULT '0',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('catalog','journalTag','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'catalog',
  `created_by_user` int unsigned DEFAULT NULL COMMENT 'User who created the tag',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_category_id_foreign` (`category_id`),
  KEY `tags_created_by_user_foreign` (`created_by_user`),
  CONSTRAINT `tags_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tags_created_by_user_foreign` FOREIGN KEY (`created_by_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transcripts`
--

DROP TABLE IF EXISTS `transcripts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transcripts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `text_w_segment_emotions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `request_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transcripts_request_id_foreign` (`request_id`),
  CONSTRAINT `transcripts_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transcripts`
--

LOCK TABLES `transcripts` WRITE;
/*!40000 ALTER TABLE `transcripts` DISABLE KEYS */;
/*!40000 ALTER TABLE `transcripts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `logged_in_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_logins_user_id_foreign` (`user_id`),
  CONSTRAINT `user_logins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_verifications`
--

DROP TABLE IF EXISTS `user_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_verifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_verifications_user_id_foreign` (`user_id`),
  CONSTRAINT `user_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_verifications`
--

LOCK TABLES `user_verifications` WRITE;
/*!40000 ALTER TABLE `user_verifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int unsigned DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guided_tours` tinyint NOT NULL DEFAULT '0' COMMENT '0: pending, 1: completed',
  `reminders` tinyint(1) NOT NULL DEFAULT '0',
  `notifications` tinyint(1) NOT NULL DEFAULT '0',
  `timezone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `optInNewsUpdates` tinyint(1) NOT NULL DEFAULT '0',
  `last_login_date` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactivated, 1: Active, 2: Deleted, 3: Archived',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint NOT NULL DEFAULT '0' COMMENT '0: not verified, 1: verified',
  `plan_start_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_plan_id_foreign` (`plan_id`),
  CONSTRAINT `users_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_requests`
--

DROP TABLE IF EXISTS `video_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `catalog_id` int unsigned NOT NULL,
  `contact_id` int unsigned DEFAULT NULL,
  `group_id` int unsigned DEFAULT NULL,
  `ref_user_id` int unsigned DEFAULT NULL,
  `ref_first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_country_code` int DEFAULT NULL,
  `ref_mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('daily','request','share') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'request',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('Pending','Accept','Approved','Reject','Not Right Now','Delete') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `llm_template_id` int unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=778 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_requests`
--

LOCK TABLES `video_requests` WRITE;
/*!40000 ALTER TABLE `video_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `video_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_types`
--

DROP TABLE IF EXISTS `video_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kpi_no` int NOT NULL DEFAULT '0',
  `metric_no` int NOT NULL DEFAULT '0',
  `video_no` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0: Deactived, 1: Active, 2: Deleted, 3: Archieved',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_types`
--

LOCK TABLES `video_types` WRITE;
/*!40000 ALTER TABLE `video_types` DISABLE KEYS */;
INSERT INTO `video_types` VALUES (1,'0M 1KPI 1V',1,0,1,1,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(2,'0M 3KPI 3V',3,0,3,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(3,'1M 1KPI 1V',1,1,1,1,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(4,'3M 3KPI 3V',3,3,3,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(5,'9M 3KPI 3V',3,9,3,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(6,'Record Yourself',1,0,1,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(7,'Record Your Screen',1,0,1,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(8,'Upload a Picture and Record',1,0,1,1,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(9,'Upload a Video and Record',1,0,1,0,'2024-05-23 13:34:23','2024-05-23 13:34:23'),(10,'3M 1KPI 1V',1,3,1,0,'2024-09-11 09:59:00','2024-09-11 09:59:00');
/*!40000 ALTER TABLE `video_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int unsigned NOT NULL,
  `video_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `video_duration` int DEFAULT NULL,
  `thumbnail_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `videos_request_id_foreign` (`request_id`),
  CONSTRAINT `videos_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `video_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database '1925rds'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-01 12:57:01
