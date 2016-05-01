<?php

/**
 * Plugin Name: JCH Optimize
 * Plugin URI: http://www.jch-optimize.net/
 * Description: This plugin aggregates and minifies CSS and Javascript files for optimized page download
 * Version: 2.0.5
 * Author: Samuel Marshall
 * License: GNU/GPLv3
 * Text Domain: jch-optimize
 * Domain Path: /languages
 * 
 */
/**
 * JCH Optimize - Plugin to aggregate and minify external resources for
 * optmized downloads
 *
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2014 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
$backend = filter_input(INPUT_GET, 'jchbackend', FILTER_SANITIZE_STRING);

define('_WP_EXEC', '1');

define('JCH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JCH_PLUGIN_DIR', plugin_dir_path(__FILE__));


if (!defined('JCH_VERSION'))
{
        define('JCH_VERSION', '2.0.5');
}

require_once(JCH_PLUGIN_DIR . 'jchoptimize/loader.php');

if (!file_exists(dirname(__FILE__) . '/dir.php'))
{
        jch_optimize_activate();
}

if (is_admin())
{
        add_action('wp_ajax_garbagecron', 'jch_ajax_garbage_cron');
        add_action('wp_ajax_nopriv_garbagecron', 'jch_ajax_garbage_cron');

        function jch_ajax_garbage_cron()
        {
                $params = JchPlatformSettings::getInstance(get_option('jch_options'));
                JchOptimizeAjax::garbageCron($params);

                die();
        }

        require_once(JCH_PLUGIN_DIR . 'options.php');
}
else
{
        if (defined('WP_USE_THEMES')
                && WP_USE_THEMES
                && $backend != 1
                && version_compare(PHP_VERSION, '5.3.0', '>=')
                && !defined('DOING_AJAX')
                && !defined('DOING_CRON')
                && !defined('APP_REQUEST')
                && !defined('XMLRPC_REQUEST')
                && (!defined('SHORTINIT') || (defined('SHORTINIT') && !SHORTINIT)))
        {
                add_action('init', 'jch_buffer_start', 0);
                add_action('template_redirect', 'jch_buffer_start', 0);
                add_action('shutdown', 'jch_buffer_end', -1);
        }
}

function jch_load_plugin_textdomain()
{
        load_plugin_textdomain('jch-optimize', FALSE, basename(dirname(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'jch_load_plugin_textdomain');

function jchoptimize($sHtml)
{
        $options = get_option('jch_options');

        try
        {
                $sOptimizedHtml = JchOptimize::optimize($options, $sHtml);
        }
        catch (Exception $e)
        {
                JchOptimizeLogger::log($e->getMessage(), JchPlatformSettings::getInstance($options));

                $sOptimizedHtml = $sHtml;
        }

        return $sOptimizedHtml;
}

function jch_buffer_start()
{
        ob_start();
}

function jch_buffer_end()
{
        while ($level = ob_get_level())
        {
                if (JchOptimizeHelper::validateHtml($sHtml = ob_get_contents()))
                {
                        $sOptimizedHtml = jchoptimize($sHtml);

                        ob_clean();

                        echo $sOptimizedHtml;

                        break;
                }

                ob_end_flush();

                //buffer not flushed for some reason.
                if ($level == ob_get_level())
                {
                        break;
                }
        }
}

add_filter('plugin_action_links', 'jch_plugin_action_links', 10, 2);

function jch_plugin_action_links($links, $file)
{
        static $this_plugin;

        if (!$this_plugin)
        {
                $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin)
        {
                $settings_link = '<a href="' . admin_url('options-general.php?page=jchoptimize-settings') . '">' . __('Settings') . '</a>';
                array_unshift($links, $settings_link);
        }

        return $links;
}

function jch_optimize_activate()
{
        try
        {
                $wp_filesystem = JchPlatformCache::getWpFileSystem();
        }
        catch(Exception $e)
        {
                return false;
        }

        if ($wp_filesystem === false)
        {
                return false;
        }

        $file    = $wp_filesystem->wp_plugins_dir() . '/jch-optimize/dir.php';
        $abspath = ABSPATH;
        $code    = <<<PHPCODE
<?php
           
\$DIR = '$abspath';
           
PHPCODE;

        $wp_filesystem->put_contents($file, $code, FS_CHMOD_FILE);
}

register_activation_hook(__FILE__, 'jch_optimize_activate');

function jch_optimize_uninstall()
{
        delete_option('jch_options');

        JchPlatformCache::deleteCache();
}

register_uninstall_hook(__FILE__, 'jch_optimize_uninstall');

