<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
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
defined('_WP_EXEC') or die('Restricted access');

class JchPlatformUtility implements JchInterfaceUtility
{

        /**
         * 
         * @param type $text
         * @return type
         */
        public static function translate($text)
        {
                return __($text, 'jch-optimize');
        }

        /**
         * 
         * @param type $time
         * @param type $timezone
         * @return type
         */
        public static function unixCurrentDate()
        {
                return current_time('timestamp', TRUE);
        }

        /*
         * 
         */

        public static function getEditorName()
        {
                return '';
        }

        /**
         * 
         * @param type $message
         * @param type $category
         */
        public static function log($message, $priority, $filename)
        {
                $file = JchPlatformUtility::getLogsPath() . '/jch-optimize.log';

                error_log($message . "\n", 3, $file);
        }

        /**
         * 
         * @return type
         */
        public static function lnEnd()
        {
                return "\n";
        }

        /**
         * 
         * @return type
         */
        public static function tab()
        {
                return "\t";
        }

        /**
         * 
         * @param type $path
         */
        public static function createFolder($path)
        {
                $wp_filesystem = JchPlatformCache::getWpFileSystem();
                $wp_filesystem->mkdir($path);
        }

        /**
         * 
         * @param type $file
         * @param type $contents
         */
        public static function write($file, $contents)
        {
                $wp_filesystem = JchPlatformCache::getWpFileSystem();
                $wp_filesystem->put_contents($file, $contents);
        }

        /**
         * 
         * @param type $value
         * @return type
         */
        public static function decrypt($value)
        {
                return self::encrypt_decrypt($value, 'decrypt');
        }

        /**
         * 
         * @param type $value
         * @return type
         */
        public static function encrypt($value)
        {
                return self::encrypt_decrypt($value, 'encrypt');
        }

        /**
         * 
         * @param type $value
         * @param type $action
         * @return type
         */
        private static function encrypt_decrypt($value, $action)
        {

                $output = false;

                $encrypt_method = "AES-256-CBC";
                $secret_key     = AUTH_KEY;
                $secret_iv      = AUTH_SALT;

                // hash
                $key = hash('sha256', $secret_key);

                // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                $iv = substr(hash('sha256', $secret_iv), 0, 16);

                if ($action == 'encrypt')
                {
                        if (version_compare(PHP_VERSION, '5.3.3', '<'))
                        {
                                $output = @openssl_encrypt($value, $encrypt_method, $key, 0);
                        }
                        else
                        {
                                $output = openssl_encrypt($value, $encrypt_method, $key, 0, $iv);
                        }
                        $output = base64_encode($output);
                }
                else if ($action == 'decrypt')
                {
                        if (version_compare(PHP_VERSION, '5.3.3', '<'))
                        {
                                $output = @openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0);
                        }
                        else
                        {
                                $output = openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0, $iv);
                        }
                }

                return $output;
        }

        /**
         * 
         * @param type $value
         * @param type $default
         * @param type $filter
         * @param type $method
         */
        public static function get($value, $default = '', $filter = 'cmd', $method = 'request')
        {
                $request = '_' . strtoupper($method);
                
                if (!isset($GLOBALS[$request][$value]))
                {
                        $GLOBALS[$request][$value] = $default;
                }

                switch ($filter)
                {
                        case 'int':
                                $filter = FILTER_SANITIZE_NUMBER_INT;

                                break;

                        case 'array':
                        case 'json':
                                return (array) $GLOBALS[$request][$value];
                        case 'string':
                        case 'cmd':
                        default :
                                $filter = FILTER_SANITIZE_STRING;

                                break;
                }

                switch ($method)
                {
                        case 'get':
                                $type = INPUT_GET;

                                break;

                        case 'post':
                                $type = INPUT_POST;

                                break;

                        default:

                                return filter_var($_REQUEST[$value], $filter);
                }


                $input = filter_input($type, $value, $filter);

                return is_null($input) ? $default : $input;
        }

        /**
         * 
         */
        public static function getLogsPath()
        {
                return JCH_PLUGIN_DIR . 'logs';
        }

        /**
         * 
         * @param type $url
         */
        public static function loadAsync($url)
        {
                
        }

        /**
         * 
         */
        public static function menuId()
        {
                
        }

        /**
         * 
         * @param type $path
         * @param type $filter
         * @param type $recurse
         * @param type $exclude
         * @return array
         */
        public static function lsFiles($path, $filter = '.', $recurse = false, $exclude = array())
        {
                $wp_filesystem = JchPlatformCache::getWpFileSystem();

                $items = $wp_filesystem->dirlist($path, false, $recurse);

                $files = array();

                self::filterItems($path, $filter, $items, $files);

                return $files;
        }

        /**
         * 
         * @param type $path
         * @param type $filter
         * @param type $items
         * @param string $files
         */
        protected static function filterItems($path, $filter, $items, &$files)
        {
                foreach ($items as $item)
                {
                        if ($item['type'] == 'f' && preg_match('#' . $filter . '#', $item['name']))
                        {
                                $files[] = $path . '/' . $item['name'];
                        }

                        if ($item['type'] == 'd' && !empty($item['files']))
                        {
                                self::filterItems($path . '/' . $item['name'], $filter, $item['files'], $files);
                        }
                }
        }

}
