<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
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

class JchPlatformPlugin implements JchInterfacePlugin
{

        protected static $plugin   = null;

        /**
         * 
         * @return type
         */
        public static function getPluginId()
        {
                return ;
        }

        /**
         * 
         * @return type
         */
        public static function getPlugin()
        {
                return ;
        }

       
        /**
         * 
         * @param type $params
         */
        public static function saveSettings($params)
        {
                $options = $params->getOptions();
                
                update_option('jch_options', $options);                
        }

        /**
         * 
         * @return type
         */
        public static function getPluginParams()
        {
                $options = get_option('jch_options');
                
                return JchPlatformSettings::getInstance($options);
        }

}
