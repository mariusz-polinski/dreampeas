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

class JchPlatformSettings implements JchInterfaceSettings
{
        private $params;
        
        /**
         * 
         * @param type $params
         * @return \JchOptimizeSettings
         */
        public static function getInstance($params)
        {
                return new JchPlatformSettings($params);
        }

        /**
         * 
         * @param type $param
         * @param type $default
         * @return type
         */
        public function get($param, $default = NULL)
        {
                if(!isset($this->params[$param]))
                {
                        return $default;
                }
                
                return $this->params[$param];
        }

        /**
         * 
         * @param type $params
         */
        private function __construct($params)
        {
                $this->params = $params;
                
                if (!defined('JCH_DEBUG'))
                {
                        define('JCH_DEBUG', ($this->get('debug', 0)));
                }
        }
        
        /**
         * 
         * @param type $param
         * @param type $value
         */
        public function set($param, $value)
        {
                $this->params[$param] = $value;
        }
        
        /**
         * 
         * @return type
         */
        public function getOptions()
        {
                return $this->params;
        }
}