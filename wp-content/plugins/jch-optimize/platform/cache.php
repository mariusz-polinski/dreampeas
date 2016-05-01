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

class JchPlatformCache implements JchInterfaceCache
{

        protected static $wp_filesystem;

        /**
         * 
         * @param type $id
         * @param type $lifetime
         * @return type
         */
        public static function getCache($id, $lifetime)
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }
                
                $filename = self::_getFileName($id);

                $file = JCH_CACHE_DIR . $filename;

                if (!$wp_filesystem->exists($file))
                {
                        return FALSE;
                }

                return self::_getCacheFile($file, $wp_filesystem);
        }

        /**
         * 
         * @param type $id
         * @param type $lifetime
         * @param type $function
         * @param type $args
         * @return type
         */
        public static function getCallbackCache($id, $lifetime, $function, $args)
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }

                $filename = self::_getFileName($id);

                $file = JCH_CACHE_DIR . $filename;

                if (!file_exists($file) || filemtime($file) > (time() + $lifetime))
                {
                        $contents = call_user_func_array($function, $args);

                        $filecontents = base64_encode(serialize($contents));

                        self::initializeCache();

                        if ($wp_filesystem->put_contents($file, $filecontents, FS_CHMOD_FILE))
                        {
                                return $contents;
                        }
                        else
                        {
                                throw new Exception(__('Error writing files to cache'));
                        }
                }

                return self::_getCacheFile($file, $wp_filesystem);
        }

        /**
         * 
         * @param type $type
         * @return type
         */
        public static function deleteCache()
        {
                $wp_filesystem = self::getWpFileSystem();

                if ($wp_filesystem === false || !$wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        return FALSE;
                }

                if ($wp_filesystem->rmdir(JCH_CACHE_DIR, TRUE))
                {
                        return TRUE;
                }
                else
                {
                        return FALSE;
                }
        }

        /**
         * 
         * @param type $file
         * @return type
         */
        private static function _getCacheFile($file, $wp_filesystem)
        {
                $content = $wp_filesystem->get_contents($file);

                return unserialize(base64_decode($content));
        }

        /**
         * 
         */
        public static function initializeCache()
        {
                $wp_filesystem = self::getWpFileSystem();

                if ($wp_filesystem !== false && !$wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        if (!$wp_filesystem->exists($wp_filesystem->wp_content_dir() . 'cache'))
                        {
                                $wp_filesystem->mkdir($wp_filesystem->wp_content_dir() . 'cache', FS_CHMOD_DIR);
                        }

                        $wp_filesystem->mkdir(JCH_CACHE_DIR, FS_CHMOD_DIR);

                        $index = JCH_CACHE_DIR . 'index.html';
                        $wp_filesystem->put_contents($index, '<html><body></body></html>', FS_CHMOD_FILE);
                }
        }

        /**
         * 
         * @global type $wp_filesystem
         * @return type
         */
        public static function getWpFileSystem()
        {
                if (!isset(self::$wp_filesystem))
                {
                        global $wp_filesystem;
                        
                        $wp_filesystem_cache = $wp_filesystem;

                        if (!class_exists('WP_Filesystem_Base'))
                        {
                                include_once ABSPATH . 'wp-admin/includes/file.php';
                        }

                        add_filter('request_filesystem_credentials', array('JchPlatformCache', 'requestFilesystemCredentials'), 10, 7);
                        
                        if (false === ($creds = request_filesystem_credentials(admin_url('options-general.php?page=jchoptimize-settings'), '', false,
                                                                                         WP_CONTENT_DIR, null, true)))
                        {
                                $message = 'The plugin needs to access the filesystem via FTP. Please define your FTP credentials in your wp_config.php file with FTP_HOST, FTP_USER, FTP_PASS, etc.';

                                self::$wp_filesystem = false;
                                
                                if (function_exists('jch_add_notices'))
                                {
                                        jch_add_notices('error', __($message, 'jch-optimize'));
                                        
                                        return;
                                }
                                else
                                {
                                        throw new Exception(__($message, 'jch-optimize'));
                                }
                        }

                        if (false === WP_Filesystem($creds, WP_CONTENT_DIR, true))
                        {
                                $message = 'Could not connect to the filesystem. Please check your FTP credentials in wp_config.php file';

                                if (function_exists('jch_add_notices'))
                                {
                                        jch_add_notices('error', __($message, 'jch-optimize'));
                                        
                                        return self::$wp_filesystem = false;
                                }
                                else
                                {
                                        throw new Exception(__($message));
                                }
                        }
                        
                        self::$wp_filesystem = $wp_filesystem;
                       
                        if(!defined('JCH_CACHE_DIR'))
                        {
                                define('JCH_CACHE_DIR', $wp_filesystem->wp_content_dir() . 'cache/jch-optimize/');
                        }
                        
                        $wp_filesystem = $wp_filesystem_cache;
                }
                
                return self::$wp_filesystem;
        }

        /**
         * 
         */
        public static function requestFilesystemCredentials($value, $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership)
        {
                $method = get_filesystem_method(array(), $context, $allow_relaxed_file_ownership);

                if ($method == 'direct')
                {
                        return true;
                }

                $credentials = get_option('ftp_credentials', array('hostname' => '', 'username' => ''));

                $credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : $credentials['hostname'];
                $credentials['username'] = defined('FTP_USER') ? FTP_USER : $credentials['username'];
                $credentials['password'] = defined('FTP_PASS') ? FTP_PASS : '';

                if ($method == 'ssh2')
                {
                        // Check to see if we are setting the public/private keys for ssh
                        $credentials['public_key']  = defined('FTP_PUBKEY') ? FTP_PUBKEY : '';
                        $credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : '';
                }

                if (in_array('', $credentials))
                {
                        return false;
                }

                return $credentials;
        }

        /**
         * 
         * @param type $id
         * @return type
         */
        private static function _getFileName($id)
        {
                return md5(NONCE_SALT . $id);
        }

        /**
         * 
         * @param type $lifetime
         */
        public static function gc($lifetime)
        {
                $wp_filesystem = self::getWpFileSystem();
                
                if($wp_filesystem === false)
                {
                        return false;
                }

                $result = true;

                $files = JchPlatformUtility::lsFiles(rtrim(JCH_CACHE_DIR, '/\\'), '.', TRUE);
                $now   = time();

                foreach ($files as $file)
                {
                        $time = $wp_filesystem->mtime($file);

                        if (($time + $lifetime) < $now || empty($time))
                        {
                                $result |= $wp_filesystem->delete($file);
                        }
                }

                return $result;
        }

}
