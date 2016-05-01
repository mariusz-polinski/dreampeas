<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_WP_EXEC') or die('Restricted access');

class JchPlatformPaths implements JchInterfacePaths
{

        /**
         * 
         * @param type $url
         * @return type
         */
        public static function absolutePath($url)
        {
                return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, ABSPATH) . 
                        ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $url), '\\/');
        }

        /**
         * 
         * @return type
         */
        public static function assetPath($pathonly=FALSE)
        {
                if($pathonly)
                {
                        return self::rewriteBase() . 'jch-optimize/assets';
                }
                
                return plugins_url() . '/jch-optimize/assets';
        }

        /**
         * 
         * @return type
         */
        public static function rewriteBase()
        {
                static $rewrite_base;

                if (!isset($rewrite_base))
                {
                        $uri = JchPlatformUri::getInstance(plugins_url());
                        $rewrite_base = trailingslashit($uri->toString(array('path')));
                }

                return $rewrite_base;
        }

        /**
         * 
         * @return type
         */
        public static function spriteDir($url = FALSE)
        {
                if ($url)
                {
                        return self::rewriteBase() . 'jch-optimize/media/sprites/';
                }

                return JCH_PLUGIN_DIR . 'media/sprites';
        }

        /**
         * 
         * @param type $sPath
         */
        public static function path2Url($sPath)
        {
                $oUri        = clone JchPlatformUri::getInstance();
                $sBaseFolder = JchOptimizeHelper::getBaseFolder();

                $abspath = str_replace(DIRECTORY_SEPARATOR, '/', ABSPATH);
                $sPath   = str_replace(DIRECTORY_SEPARATOR, '/', $sPath);

                $sUriPath = $oUri->toString(array('scheme', 'user', 'pass', 'host', 'port')) . $sBaseFolder .
                        (str_replace($abspath, '', $sPath));

                return $sUriPath;
        }

        /**
         * 
         * @param type $function
         * @return type
         */
        public static function ajaxUrl($function)
        {
                return add_query_arg(array('action' => $function), admin_url('admin-ajax.php'));
        }
        
        /**
         * 
         * @return type
         */
        public static function rootPath()
        {
                return ABSPATH;
        }

        /**
         * 
         * @param type $name
         */
        public static function adminController($name)
        {
                return add_query_arg(array('jch-task' => $name), admin_url('options-general.php?page=jchoptimize-settings'));
        }

        /**
         * 
         */
        public static function backupImagesParentFolder()
        {
                return '';
        }

}
