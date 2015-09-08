<?php

/** The name of the database for CouponsCMS coupon site */
define('DB_NAME', 'couponcms30');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset */
define('DB_CHARSET', 'utf8');

/** Tables prefix */
define('DB_TABLE_PREFIX', '');

/** Here is the administration panel, this directory name can be changed at anytime  */
define('ADMINDIR', 'admin');

/** Here are stored include files, this directory name can be changed at anytime */
define('IDIR', 'includes');

/** Here are stored language files, this directory name can be changed at anytime */
define('LDIR', 'languages');

/** Here are stored CouponsCMS plugins, this directory name can be changed at anytime  */
define('PDIR', 'plugins');

/** Here are stored plugins intalled by users, this directory name can be changed at anytime  */
define('UPDIR', 'includes/user_plugins');

/** Here is stored the library, this directory name can be changed at anytime */
define('LBDIR', 'libs');

/** Here are stored themes, 'themes' directory can be changed at anytime */
define('THEMES_LOC', 'content/themes');

/** Here are stored images, this directory can be changed ONLY before uploading data */
define('UPLOAD_IMAGES_LOC', 'content/uploads/images');

/** Here are stored default images, this directory can be changed ONLY before uploading data */
define('DEFAULT_IMAGES_LOC', 'content/uploads/default');

/** Here are stored widgets, this directory name can be changed at anytime */
define('WIGETS_LOCATION', 'content/widgets');

/** Here are stored common parts, this directory name can be changed at anytime */
define('COMMON_LOCATION', 'content/common');

/** Here are stored temporary files, this directory name can be changed at anytime */
define('TEMP_LOCATION', 'content/temp');

/** Here are stored ajax calls, this directory name can be changed at anytime */
define('AJAX_LOCATION', 'content/ajax');

/** Here are stored email templates, this directory name can be changed at anytime */
define('TMAIL_LOCATION', 'content/mail_templates');

/** Here are stored miscellaneous files, this directory name can be changed at anytime */
define('MISCDIR', 'misc');

/** Here are stored cron files, this directory name can be changed at anytime */
define('CRONDIR', 'cron');

/** BAN user after fail attempts */
define('BAN_AFTER_ATTEMPTS', 7);

/** For how long ban an user after too many login fail attempts, default: 2 hours */
define('BAN_AFTER_FAIL', 120); // in minutes!

/** CouponsCMS Site version */
define('VERSION', '3.00');

/** CouponsCMS Site Authorization Key, not required */
define('KEY', '');

/** Default user session, default: 3 hours */
define('DEF_USER_SESSION', 180); // in minutes!

/** Default user sesstion if checked `keep me logged`, default: 3 days */
define('DEF_USER_SESSION_KL', 4320); // in minutes! (4320 = 3 days)

/** How often to check for new news or/and tutorials on couposcms.com, it's important to know if new versions exists to upgrade */
define('CHECK_NEWS_TIME', 1); // in minutes (minimum 1, maximum 1440)

/** Cache preference, default: Apc */
define('PREF_CACHE', 'Apc');

/** For how long to keep saved a variable in cache */
define('DEF_CACHE', 600); // in seconds!

/** Set it true if use htaccess with SEO Links */
define('SEO_LINKS', true);

/** Currency for all payments. Default: USD. example: USD, GBP, EUR, CAD */
define('CURRENCY', 'USD');

/** Prices format. %s will be replaced by the price. */
define('PRICE_FORMAT', '$%s');

/** Money format decimal separator */
define('MONEY_DECIMAL_SEPARATOR', '.');

/** Money format thousand separator.
Note: Do not use the same separator with decimal separator.
Format example: 1,200.45, where , (comma) is the thousand separator, and . (dot) is the decimal separator */

define('MONEY_THOUSAND_SEPARATOR', ',');

/** DO NOT MODIFY THIS !! */

define('DIR', __DIR__);

    function nltobr($str){
        if(strpos($str, "<br>") === false){
            return nl2br($str);
        }else{
            return $str;
        }
    }