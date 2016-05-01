<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class JchPlatformHttp implements JchInterfaceHttp
{

        protected static $instance;

        /**
         * 
         * @staticvar null $available
         * @return boolean
         */
        public function available()
        {
                static $available = NULL;

                if (is_null($available))
                {
                        global $wp_version;

                        $args = array(
                                'method'              => 'GET',
                                'timeout'             => apply_filters('http_request_timeout', 10),
                                'redirection'         => apply_filters('http_request_redirection_count', 5),
                                'httpversion'         => apply_filters('http_request_version', '1.0'),
                                'user-agent'          => apply_filters('http_headers_useragent',
                                                                       'WordPress/' . $wp_version . '; ' . get_bloginfo('url')),
                                'reject_unsafe_urls'  => apply_filters('http_request_reject_unsafe_urls', false),
                                'blocking'            => true,
                                'headers'             => array(),
                                'cookies'             => array(),
                                'body'                => null,
                                'compress'            => false,
                                'decompress'          => true,
                                'sslverify'           => true,
                                'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
                                'stream'              => false,
                                'filename'            => null,
                                'limit_response_size' => null,
                        );

                        $request_order = apply_filters('http_api_transports', array('curl', 'streams'), $args, NULL);

                        // Loop over each transport on each HTTP request looking for one which will serve this request's needs.
                        foreach ($request_order as $transport)
                        {
                                $class = 'WP_HTTP_' . ucfirst($transport);

                                // Check to see if this transport is a possibility, calls the transport statically.
                                if (!call_user_func(array($class, 'test'), $args, NULL))
                                {
                                        continue;
                                }

                                $available = TRUE;

                                return $available;
                        }

                        $available = FALSE;
                }

                return $available;
        }

        /**
         * 
         * @param type $sPath
         * @param type $aPost
         * @return type
         */
        public function request($sPath, $aPost = null, $aHeaders = null, $sUserAgent='')
        {
                $args = array('timeout' => 10);

                if (isset($aHeaders))
                {
                        $args['headers'] = $aHeaders;
                }
                
                if(!empty($sUserAgent))
                {
                        $args['user-agent'] = $sUserAgent;
                }

                if (isset($aPost))
                {
                        $args['body'] = $aPost;

                        $response = wp_remote_post($sPath, $args);
                }
                else
                {
                        $response = wp_remote_get($sPath, $args);
                }

                $return = array(
                        'body' => wp_remote_retrieve_body($response),
                        'code' => (int) wp_remote_retrieve_response_code($response)
                );

                return $return;
        }

        /**
         * 
         * @return type
         */
        public static function getHttpAdapter()
        {
                if (!self::$instance)
                {
                        self::$instance = new JchPlatformHttp();
                }

                return self::$instance;
        }

}
