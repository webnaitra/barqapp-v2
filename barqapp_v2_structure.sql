-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 19, 2026 at 06:40 AM
-- Server version: 10.9.3-MariaDB
-- PHP Version: 8.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barqapp_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_ads`
--

DROP TABLE IF EXISTS `admin_ads`;
CREATE TABLE IF NOT EXISTS `admin_ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `source_icon` int(11) DEFAULT NULL,
  `fav_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `share_count` int(11) NOT NULL DEFAULT 0,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help_text` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_ad_category`
--

DROP TABLE IF EXISTS `admin_ad_category`;
CREATE TABLE IF NOT EXISTS `admin_ad_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_ad_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_ad_location`
--

DROP TABLE IF EXISTS `admin_ad_location`;
CREATE TABLE IF NOT EXISTS `admin_ad_location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_ad_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `adsenses`
--

DROP TABLE IF EXISTS `adsenses`;
CREATE TABLE IF NOT EXISTS `adsenses` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adsense_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adsense_code` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adsense_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ads_areas`
--

DROP TABLE IF EXISTS `ads_areas`;
CREATE TABLE IF NOT EXISTS `ads_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `advertisers`
--

DROP TABLE IF EXISTS `advertisers`;
CREATE TABLE IF NOT EXISTS `advertisers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adv_first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adv_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_age` int(11) DEFAULT NULL,
  `adv_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `adv_lang` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ar',
  `adv_login_type` enum('default','social') COLLATE utf8mb4_unicode_ci NOT NULL,
  `adv_social` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_forgot_password_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_notifications_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `push_notifications_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `last_email_digest_sent` datetime DEFAULT NULL,
  `adv_verify_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adv_reset_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adv_email` (`adv_email`),
  KEY `image` (`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

DROP TABLE IF EXISTS `affiliates`;
CREATE TABLE IF NOT EXISTS `affiliates` (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `selling_price` varchar(191) DEFAULT NULL,
  `country_id` bigint(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `country_affiliates_1` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_categories`
--

DROP TABLE IF EXISTS `affiliate_categories`;
CREATE TABLE IF NOT EXISTS `affiliate_categories` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_product_category`
--

DROP TABLE IF EXISTS `affiliate_product_category`;
CREATE TABLE IF NOT EXISTS `affiliate_product_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aiwords`
--

DROP TABLE IF EXISTS `aiwords`;
CREATE TABLE IF NOT EXISTS `aiwords` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `word_words` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `word_cat_id` int(11) DEFAULT NULL,
  `word_sub_cat_id` int(11) DEFAULT NULL,
  `tags_id` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `word_number` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `word_word` (`word_words`(768)),
  KEY `word_word_2` (`word_words`(768)),
  KEY `word_word_3` (`word_words`(768)),
  KEY `word_cat_id` (`word_cat_id`),
  KEY `word_sub_cat_id` (`word_sub_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `icon_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` tinyint(4) DEFAULT NULL,
  `freeze` tinyint(1) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image` (`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_source`
--

DROP TABLE IF EXISTS `category_source`;
CREATE TABLE IF NOT EXISTS `category_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

DROP TABLE IF EXISTS `contactus`;
CREATE TABLE IF NOT EXISTS `contactus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_body` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `arabic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `currency_code` varchar(191) DEFAULT NULL,
  `arabic_currency_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `country_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `country_keyword`
--

DROP TABLE IF EXISTS `country_keyword`;
CREATE TABLE IF NOT EXISTS `country_keyword` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_keyword_1` (`country_id`),
  KEY `country_keyword_ab_1` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_live_stream`
--

DROP TABLE IF EXISTS `country_live_stream`;
CREATE TABLE IF NOT EXISTS `country_live_stream` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `live_stream_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `country_source`
--

DROP TABLE IF EXISTS `country_source`;
CREATE TABLE IF NOT EXISTS `country_source` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_source_1` (`country_id`),
  KEY `country_source_ab_1` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbarchieves`
--

DROP TABLE IF EXISTS `dbarchieves`;
CREATE TABLE IF NOT EXISTS `dbarchieves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fav_user_id` int(11) DEFAULT NULL,
  `fav_news_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fav_user_id` (`fav_user_id`),
  KEY `fav_news_id` (`fav_news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `short_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_streams`
--

DROP TABLE IF EXISTS `live_streams`;
CREATE TABLE IF NOT EXISTS `live_streams` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_parent_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_ads`
--

DROP TABLE IF EXISTS `mobile_ads`;
CREATE TABLE IF NOT EXISTS `mobile_ads` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `sourcefeed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `shares` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `urgent` int(11) NOT NULL DEFAULT 0,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` tinyint(1) DEFAULT 1,
  `excerpt` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `run_cron` tinyint(1) DEFAULT 0,
  `is_updated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `news_category_ssg_unq_1` (`category_id`),
  KEY `news_source_jkl_unq_1` (`source_id`),
  KEY `news_sourcefeed_hjk_unq_1` (`sourcefeed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletters`
--

DROP TABLE IF EXISTS `newsletters`;
CREATE TABLE IF NOT EXISTS `newsletters` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `newsletter_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `newsletter_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_email` (`newsletter_email`),
  KEY `newsletter_user_id` (`newsletter_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_archieves`
--

DROP TABLE IF EXISTS `news_archieves`;
CREATE TABLE IF NOT EXISTS `news_archieves` (
  `id` int(11) NOT NULL,
  `news_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_keywords`
--

DROP TABLE IF EXISTS `news_keywords`;
CREATE TABLE IF NOT EXISTS `news_keywords` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `word_word` (`news_id`),
  KEY `word_word_2` (`news_id`),
  KEY `word_word_3` (`news_id`),
  KEY `word_cat_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_tags`
--

DROP TABLE IF EXISTS `news_tags`;
CREATE TABLE IF NOT EXISTS `news_tags` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `word_word` (`news_id`),
  KEY `word_word_2` (`news_id`),
  KEY `word_word_3` (`news_id`),
  KEY `word_cat_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

DROP TABLE IF EXISTS `notification_settings`;
CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_user_id` int(11) DEFAULT NULL,
  `setting_cats` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_sub_cats` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_urgent` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `setting_user_id` (`setting_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `old_news`
--

DROP TABLE IF EXISTS `old_news`;
CREATE TABLE IF NOT EXISTS `old_news` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `news_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `news_database` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'self',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `word_word` (`news_id`),
  KEY `word_word_2` (`news_id`),
  KEY `word_word_3` (`news_id`),
  KEY `word_cat_id` (`news_title`),
  KEY `word_sub_cat_id` (`news_database`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE IF NOT EXISTS `options` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `page_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `selling_price` varchar(191) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `image` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product_product_category`
--

DROP TABLE IF EXISTS `product_product_category`;
CREATE TABLE IF NOT EXISTS `product_product_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_product_categorgy_unq_1` (`product_category_id`),
  KEY `product_product_categorgy_unq_2` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscribable_id` bigint(20) UNSIGNED NOT NULL,
  `subscribable_type` varchar(255) NOT NULL,
  `endpoint` varchar(500) NOT NULL,
  `public_key` varchar(255) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `content_encoding` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `endpoint` (`endpoint`),
  KEY `subscribable_index` (`subscribable_id`,`subscribable_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

DROP TABLE IF EXISTS `social_media`;
CREATE TABLE IF NOT EXISTS `social_media` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `social_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

DROP TABLE IF EXISTS `sources`;
CREATE TABLE IF NOT EXISTS `sources` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arabic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `freeze` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placeholder_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `filter_classes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_classes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_classes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `source_type_id` (`source_type_id`),
  KEY `sources_countries_1` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `source_feeds`
--

DROP TABLE IF EXISTS `source_feeds`;
CREATE TABLE IF NOT EXISTS `source_feeds` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `category_id` bigint(20) DEFAULT NULL,
  `status_id` tinyint(1) NOT NULL DEFAULT 0,
  `freeze` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`),
  KEY `source_feeds_category_1` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `source_types`
--

DROP TABLE IF EXISTS `source_types`;
CREATE TABLE IF NOT EXISTS `source_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `user_group_id` (`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_categories`
--

DROP TABLE IF EXISTS `user_categories`;
CREATE TABLE IF NOT EXISTS `user_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_category_ssg_unq_1` (`category_id`),
  KEY `users_category_advertisors_ssg_unq_1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_privilages` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_keywords`
--

DROP TABLE IF EXISTS `user_keywords`;
CREATE TABLE IF NOT EXISTS `user_keywords` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_user_keyword_ssg_unq_1` (`user_id`),
  KEY `users_keyword_keywords_ssg_unq_1` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
CREATE TABLE IF NOT EXISTS `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('email','push') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sent','failed','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_notifications_user_type_sentat_index` (`user_id`,`type`,`sent_at`),
  KEY `user_notifications_createdat_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sources`
--

DROP TABLE IF EXISTS `user_sources`;
CREATE TABLE IF NOT EXISTS `user_sources` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_user_ssg_unq_1` (`source_id`),
  KEY `users_source_ssg_unq_1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint(20) NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `source_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapps`
--

DROP TABLE IF EXISTS `whatsapps`;
CREATE TABLE IF NOT EXISTS `whatsapps` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_email` (`whatsapp_number`),
  KEY `whatsapp_user_id` (`whatsapp_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD CONSTRAINT `country_affiliates_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `country_keyword`
--
ALTER TABLE `country_keyword`
  ADD CONSTRAINT `country_keyword_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `country_keyword_ab_1` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `country_source`
--
ALTER TABLE `country_source`
  ADD CONSTRAINT `country_source_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `country_source_ab_1` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_category_ssg_unq_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_source_jkl_unq_1` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_sourcefeed_hjk_unq_1` FOREIGN KEY (`sourcefeed_id`) REFERENCES `source_feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news_keywords`
--
ALTER TABLE `news_keywords`
  ADD CONSTRAINT `news_keyword_keywords_unq_1` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_keyword_news_unq_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
