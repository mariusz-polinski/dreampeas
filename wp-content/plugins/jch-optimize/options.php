<?php

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
add_action('admin_menu', 'add_jch_optimize_menu');

function add_jch_optimize_menu()
{
        $hook_suffix = add_options_page(__('JCH Optimize Settings', 'jch-optimize'), 'JCH Optimize', 'manage_options', 'jchoptimize-settings',
                                           'jch_options_page');

        add_action('admin_enqueue_scripts' , 'jch_load_resource_files');
        add_action('admin_head-' . $hook_suffix, 'jch_load_scripts');
        add_action('load-' . $hook_suffix, 'jch_initialize_settings');
}

function jch_options_page()
{
        if (version_compare(PHP_VERSION, '5.3.0', '<'))
        {

                ?>

                <div class="notice notice-error">
                        <p> <?php _e('This plugin requires PHP 5.3.0 or greater to run. Your installed version is: ' . PHP_VERSION, 'jch-optimize') ?></p>
                </div>
                <?php

        }

        ?>
        <div>
                <h2>JCH Optimize Settings</h2>
                <form action="options.php" method="post" class="jch-settings-form">
                        <div style="width: 90%;">
                                <input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'jch-optimize'); ?>" />
                                <?php
                                $subscribe_url = 'https://www.jch-optimize.net/subscribe/levels.html/#wordpress';
                                
                                  ?>
                                  <a class="right button button-secondary" href="<?php echo $subscribe_url; ?>" target="_blank"><?php _e('Upgrade to Pro', 'jch-optimize'); ?></a>

                                  <?php
                                  

                                ?>

                        </div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                                <li class="active"><a href="#description" data-toggle="tab"><?php _e('Description', 'jch-optimize') ?></a></li>

                                <?php

                                if (version_compare(PHP_VERSION, '5.3.0', '>='))
                                {

                                        ?>

                                        <li><a href="#basic" data-toggle="tab"><?php _e('Basic Options', 'jch-optimize') ?></a></li>
                                        <li><a href="#exclude" data-toggle="tab"><?php _e('Exclude Options', 'jch-optimize') ?></a></li>
                                        <li><a href="#sprite" data-toggle="tab"><?php _e('Free Features', 'jch-optimize') ?></a></li>
                                        <li><a href="#pro" data-toggle="tab"><?php _e('Pro Features', 'jch-optimize') ?></a></li>
                                        <li><a href="#images" data-toggle="tab"><?php _e('Optimize Images', 'jch-optimize') ?></a></li>
                                        <?php

                                }

                                ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                                <div class="tab-pane active" id="description">
                                        <div id="extension-container" style="text-align:left;">
                                                <h1>JCH Optimize Plugin</h1>
                                                <h3>(Version 2.0.5)</h3>
                                                <?php
                                                $subscribe_message = sprintf( wp_kses( __( 'This is the free version of JCH Optimize for WordPress. For access to advanced features, please <a href="%s" target="_blank">purchase the Pro Version!</a>', 'jch-optimize' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( $subscribe_url ) );
                                                
                                                
                                                  echo '<p class="notice notice-info" style="margin: 1em 0; padding: 10px 12px">' . $subscribe_message . '</p>';

                                                  

                                                ?>
                                                <p><?php _e('This plugin speeds up your website by performing a number of front end optimizations to your website automatically. These optimizations reduce both your webpage size and the number of http requests required to download your webpages and results in reduced server load, lower bandwidth requirements, and faster page loading times.', 'jch-optimize') ?></p>
                                                <p><img src="<?php echo JCH_PLUGIN_URL ?>/logo.png" style="float: none" /></p>
                                                <h2><?php _e('Major Features', 'jch-optimize') ?></h2>
                                                <ul>
                                                        <li><?php _e('Combine and gzip CSS and javascript files', 'jch-optimize') ?></li>
                                                        <li><?php _e('Minify combined files and HTML', 'jch-optimize') ?></li>
                                                        <li><?php _e('Combine select background images into a sprite', 'jch-optimize') ?></li>
                                                        <li><?php _e('Generate sprite to combine background images.', 'jch-optimize') ?></li>
                                                        <li><?php echo wp_kses(__('CDN support <small style="color:red"><em>Pro Version Only</em></small>', 'jch-optimize'), array('small' => array('style' => array()), 'em' => array())) ?></li>
                                                        <li><?php echo wp_kses(__('Lazy-load images <small style="color:red"><em>Pro Version Only</em></small>', 'jch-optimize'), array('small' => array('style' => array()), 'em' => array())) ?></li>
                                                        <li><?php echo wp_kses(__('Optimize CSS Delivery <small style="color:red"><em>Pro Version Only</em></small>', 'jch-optimize'), array('small' => array('style' => array()), 'em' => array())) ?></li>
                                                        <li><?php echo wp_kses(__('Optimize Images <small style="color:red"><em>Pro Version Only</em></small>', 'jch-optimize'), array('small' => array('style' => array()), 'em' => array())) ?></li>
                                                </ul>
                                                <h2><?php _e('Instructions', 'jch-optimize') ?></h2>
                                                <p><?php _e('First deactivate all page caching features and plugins, then use the \'Automatic Settings\' <span class="notranslate">(Minimum - Optimum)</span> to configure the plugin. The \'Automatic Settings\' are concerned with the combining of the CSS and javascript files, and the management of the combined files, and automatically sets the options in the \'Automatic Settings Groups\'. Use the Exclude options to exclude files or plugins that don\'t work so well with JCH Optimize. You can then try the other optimization features in turn to further configure the plugin and optimize your site. Flush all your cache before re-enabling caching plugins.', 'jch-optimize') ?></p>
                                                <h2><?php _e('Support', 'jch-optimize') ?></h2>
                                                <p><?php printf(wp_kses(__('First check out the <a href="%1$s" target="_blank">documentation</a> and especially the <a href="%2$s" target="_blank">tutorials</a> on the plugin\'s website to learn how to use and configure the plugin.', 'jch-optimize'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://www.jch-optimize.net/documentation.html'), esc_url('https://www.jch-optimize.net/documentation/tutorials.html')); ?></p>
                                                <p><?php printf(wp_kses(__('Read <a href="%s" target="_blank">Here</a> for some troubleshooting guides to resolve some common issues users generally encounter with using the plugin.', 'jch-optimize'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://www.jch-optimize.net/documentation/troubleshooting-guide.html')); ?></p>
                                                <p><?php printf(wp_kses(__('You\'ll need a subscription to submit tickets to get premium support in configuring the plugin to resolve conflicts so <a href="%1$s" target="_blank">subscribe</a> to <em>JCH OptimizePro for WordPress</em> and access your account to submit a ticket. Otherwise you can use the <a href="%2$s" target="_blank" >Forums</a> on the plugin\'s website or the <a href="%3$s" target="_blank" >WordPress support system</a> to submit support requests.', 'jch-optimize'), array('a' => array('href' => array(), 'target' => array()), 'em' => array())), esc_url($subscribe_url), esc_url('https://www.jch-optimize.net/forum.html'), esc_url('https://wordpress.org/support/plugin/jch-optimize')); ?></p> 
                                                <p class="notice notice-info" style="margin: 1em 0; padding: 10px 12px"><?php printf(wp_kses(__('If you use this plugin please consider posting a review on the plugin\'s <a href="%s" target="_blank" >WordPress page</a>. If you\'re having problems, please submit for support and give us a chance to resolve your issues before reviewing. Thanks.', 'jch-optimize'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://wordpress.org/support/view/plugin-reviews/jch-optimize')); ?></p>

                                        </div>
                                </div>
                                <?php do_settings_sections('jch-sections'); ?>
                        </div>

                        <?php settings_fields('jch_options'); ?>
                        <?php

                        $options = get_option('jch_options');

                        ?>
                        <input type="hidden" id="jch_options_hidden_containsgf" name="jch_options[hidden_containsgf]" value="<?php echo !empty($options['hidden_containsgf']) ? $options['hidden_containsgf'] : '' ; ?>" >
                        <input type="hidden" id="jch_options_hidden_api_secret" name="jch_options[hidden_api_secret]" value="11e603aa" >
                        <input name="Submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'jch-optimize'); ?>" />
                </form>
        </div>
        <?php

}

add_action('admin_init', 'jch_register_options');

function jch_register_options()
{
        register_setting('jch_options', 'jch_options', 'jch_options_validate');
}

function jch_initialize_settings()
{
        wp_register_style('jch-bootstrap-css', JCH_PLUGIN_URL . 'media/css/bootstrap.css', array(), JCH_VERSION);
        wp_register_style('jch-admin-css', JCH_PLUGIN_URL . 'media/css/css-lib/admin.css', array(), JCH_VERSION);
        wp_register_style('jch-fonts-css', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css');
        wp_register_style('jch-chosen-css', JCH_PLUGIN_URL . 'media/css/jquery.chosen.min.css', array(), JCH_VERSION);
        wp_register_style('jch-wordpress-css', JCH_PLUGIN_URL . 'media/css/wordpress.css', array(), JCH_VERSION);
        
        wp_register_script('jch-bootstrap-js', JCH_PLUGIN_URL . 'media/js/bootstrap.min.js', array('jquery'), JCH_VERSION, TRUE);
        wp_register_script('jch-tabsstate-js', JCH_PLUGIN_URL . 'media/js/tabs-state.js', array('jquery'), JCH_VERSION, TRUE);
        wp_register_script('jch-adminutility-js', JCH_PLUGIN_URL . 'media/js/js-lib/admin-utility.js', array('jquery'), JCH_VERSION, TRUE);
        wp_register_script('jch-chosen-js', JCH_PLUGIN_URL . 'media/js/jquery.chosen.min.js', array('jquery'), JCH_VERSION, TRUE);
        wp_register_script('jch-collapsible-js', JCH_PLUGIN_URL . 'media/js/jquery.collapsible.js', array('jquery'), JCH_VERSION, TRUE);

        

        if (version_compare(PHP_VERSION, '5.3.0', '<'))
        {
                return;
        }

        global $jch_redirect;
        $jch_redirect = FALSE;

        check_jch_tasks();
        jch_get_cache_info();
        jch_redirect();
        jch_get_admin_object();

        if (get_transient('jch_notices'))
        {
                add_action('admin_notices', 'jch_send_notices');
        }

        //Basic Settings
        add_settings_section('jch_basic_pre', '', 'jch_basic_pre_section_text', 'jch-sections');
        add_settings_field('jch_options_combine_files_enable', __('Enable', 'jch-optimize'), 'jch_options_combine_files_enable_string', 'jch-sections',
                                                        'jch_basic_pre');
        add_settings_field('jch_options_auto_settings', __('Automatic Settings', 'jch-optimize'), 'jch_options_auto_settings_string', 'jch-sections',
                                                           'jch_basic_pre');
        add_settings_field('jch_options_html_minify_level', __('HTML Minification Level', 'jch-optimize'), 'jch_options_html_minify_level_string',
                                                               'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_htaccess', __('Enable url re-writing', 'jch-optimize'), 'jch_options_htaccess_string', 'jch-sections',
                                                      'jch_basic_pre');
        add_settings_field('jch_options_try_catch', __('Use <span class="notranslate">try-catch</span>', 'jch-optimize'), 'jch_options_try_catch_string', 'jch-sections', 'jch_basic_pre');
        add_settings_field('jch_options_lifetime', __('Lifetime (days)', 'jch-optimize'), 'jch_options_lifetime_string', 'jch-sections',
                                                      'jch_basic_pre');
        add_settings_section('jch_basic_misc', '', 'jch_basic_misc_section_text', 'jch-sections');
        add_settings_field('jch_options_utility_settings', __('Utility Settings', 'jch-optimize'), 'jch_options_utility_settings_string',
                                                              'jch-sections', 'jch_basic_misc');
        add_settings_field('jch_options_debug', __('Debug plugin', 'jch-optimize'), 'jch_options_debug_string', 'jch-sections', 'jch_basic_misc');
        add_settings_field('jch_options_log', __('Log Exceptions', 'jch-optimize'), 'jch_options_log_string', 'jch-sections', 'jch_basic_misc');

        add_settings_section('jch_basic_auto', '', 'jch_basic_auto_section_text', 'jch-sections');
        //Automatic basic settings
        add_settings_field('jch_options_auto_basic', '<strong><i>' . __('Automatic Basic Settings', 'jch-optimize') . '</i></strong><hr>',
                                                                        'jch_options_auto_basic_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_css', __('Combine CSS Files', 'jch-optimize'), 'jch_options_css_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_javascript', __('Combine Javascript Files', 'jch-optimize'), 'jch_options_javascript_string', 'jch-sections',
                                                        'jch_basic_auto');
        add_settings_field('jch_options_gzip', __('Gzip Combined Files', 'jch-optimize'), 'jch_options_gzip_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_css_minify', __('Minify Combined CSS File', 'jch-optimize'), 'jch_options_css_minify_string', 'jch-sections',
                                                        'jch_basic_auto');
        add_settings_field('jch_options_js_minify', __('Minify Combined Javascript File', 'jch-optimize'), 'jch_options_js_minify_string',
                                                       'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_html_minify', __('Minify HTML', 'jch-optimize'), 'jch_options_html_minify_string', 'jch-sections',
                                                         'jch_basic_auto');
        add_settings_field('jch_options_defer_js', __('Defer combined javascript', 'jch-optimize'), 'jch_options_defer_js_string', 'jch-sections',
                                                      'jch_basic_auto');
        add_settings_field('jch_options_bottom_js', __('Manage Combined Files', 'jch-optimize'), 'jch_options_bottom_js_string', 'jch-sections',
                                                       'jch_basic_auto');
        //Automatic exclude settings
        add_settings_field('jch_options_auto_exclude', '<strong><i>' . __('Automatic Exclude Settings', 'jch-optimize') . '</i></strong><hr>',
                                                                          'jch_options_auto_exclude_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_includeAllExtensions', __('Include files from all plugins', 'jch-optimize'),
                                                                  'jch_options_includeAllExtensions_string', 'jch-sections', 'jch_basic_auto');
        //Automatic pro settings
        add_settings_field('jch_options_auto_pro', '<strong><i>' . __('Automatic Pro Settings', 'jch-optimize') . '</i></strong><hr>',
                                                                      'jch_options_auto_pro_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_replaceImports', __('Replace <span class="notranslate">@imports</span> in CSS', 'jch-optimize'), 'jch_options_pro_replaceImports_string',
                                                                'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_phpAndExternal', __('Include PHP files and files from external domains', 'jch-optimize'),
                                                                'jch_options_pro_phpAndExternal_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_inlineStyle', __('Include inline CSS styles', 'jch-optimize'), 'jch_options_pro_inlineStyle_string',
                                                             'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_inlineScripts', __('Include inline scripts', 'jch-optimize'), 'jch_options_pro_inlineScripts_string',
                                                               'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_searchBody', __('Parse <span class="notranslate">&lt;body&gt;</span> in page for CSS/Js files', 'jch-optimize'),
                                                            'jch_options_pro_searchBody_string', 'jch-sections', 'jch_basic_auto');
        add_settings_field('jch_options_pro_loadAsynchronous', __('Load combined javascript asynchronously', 'jch-optimize'),
                                                                  'jch_options_pro_loadAsynchronous_string', 'jch-sections', 'jch_basic_auto');

        //Exclude Options
        add_settings_section('jch_advanced_auto', '', 'jch_advanced_auto_section_text', 'jch-sections');


        add_settings_section('jch_advanced_exclude', '', 'jch_advanced_exclude_section_text', 'jch-sections');
        add_settings_field('jch_options_excludeCss', __('Exclude these CSS files', 'jch-optimize'), 'jch_options_excludeCss_string', 'jch-sections',
                                                        'jch_advanced_exclude');
        add_settings_field('jch_options_excludeJs', __('Exclude these javascript files', 'jch-optimize'), 'jch_options_excludeJs_string',
                                                       'jch-sections', 'jch_advanced_exclude');
        add_settings_field('jch_options_excludeCssComponents', __('Exclude CSS files from these plugins', 'jch-optimize'),
                                                                  'jch_options_excludeCssComponents_string', 'jch-sections', 'jch_advanced_exclude');
        add_settings_field('jch_options_excludeJsComponents', __('Exclude javascript files from these plugins', 'jch-optimize'),
                                                                 'jch_options_excludeJsComponents_string', 'jch-sections', 'jch_advanced_exclude');
        add_settings_field('jch_options_loadfilesasync_spacer',
                           '<strong><i>' . __('Exclude javascript dynamically', 'jch-optimize') . '</i></strong><hr>',
                                              'jch_options_loadfilesasync_spacer_string', 'jch-sections', 'jch_advanced_exclude');
        add_settings_field('jch_options_pro_loadFilesAsync', __('Load these individual javascript files dynamically', 'jch-optimize'),
                                                                'jch_options_pro_loadFilesAsync_string', 'jch-sections', 'jch_advanced_exclude');

        add_settings_section('jch_inline_exclude', '', 'jch_inline_exclude_section_text', 'jch-sections');
        add_settings_field('jch_options_pro_excludeStyles', __('Exclude inline styles', 'jch-optimize'), 'jch_options_pro_excludeStyles_string',
                                                               'jch-sections', 'jch_inline_exclude');
        add_settings_field('jch_options_pro_excludeScripts', __('Exclude inline scripts', 'jch-optimize'), 'jch_options_pro_excludeScripts_string',
                                                                'jch-sections', 'jch_inline_exclude');

        add_settings_section('jch_advanced_remove', '', 'jch_advanced_remove_section_text', 'jch-sections');
        add_settings_field('jch_options_removeCSS', __('Remove CSS files', 'jch-optimize'), 'jch_options_removeCss_string', 'jch-sections',
                                                       'jch_advanced_remove');
        add_settings_field('jch_options_removeJs', __('Remove javascript files', 'jch-optimize'), 'jch_options_removeJs_string', 'jch-sections',
                                                      'jch_advanced_remove');

        //Free Features
        add_settings_section('jch_sprite_manual', '', 'jch_sprite_manual_section_text', 'jch-sections');
        add_settings_field('jch_options_csg_enable', __('Enable', 'jch-optimize'), 'jch_options_csg_enable_string', 'jch-sections',
                                                        'jch_sprite_manual');
        add_settings_field('jch_options_csg_direction', __('Sprite Build Direction', 'jch-optimize'), 'jch_options_csg_direction_string',
                                                           'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_wrap_images', __('Wrap Images', 'jch-optimize'), 'jch_options_csg_wrap_images_string', 'jch-sections',
                                                             'jch_sprite_manual');
        add_settings_field('jch_options_csg_exclude_images', __('Exclude these images from the sprite', 'jch-optimize'),
                                                                'jch_options_csg_exclude_images_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_field('jch_options_csg_include_images', __('Include these images in the sprite', 'jch-optimize'),
                                                                'jch_options_csg_include_images_string', 'jch-sections', 'jch_sprite_manual');
        add_settings_section('jch_img_attributes', '', 'jch_img_attributes_section_text', 'jch-sections');
        add_settings_field('jch_img_attributes_enable', __('Enable', 'jch-optimize'), 'jch_options_img_attributes_enable_string', 'jch-sections',
                                                        'jch_img_attributes');
        
        //Pro Options
        add_settings_section('jch_pro_group', '', 'jch_pro_group_section_text', 'jch-sections');
        add_settings_field('jch_options_pro_downloadid', __('Download ID', 'jch-optimize'), 'jch_options_pro_downloadid_string', 'jch-sections',
                                                            'jch_pro_group');

        add_settings_section('jch_pro_cookielessdomain', '', 'jch_pro_cookielessdomain_section_text', 'jch-sections');
        add_settings_field('jch_options_pro_cookielessdomain_enable', __('Enable', 'jch-optimize'), 'jch_options_pro_cookielessdomain_enable_string',
                                                                         'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_cookielessdomain', __('Domain 1', 'jch-optimize'), 'jch_options_pro_cookielessdomain_string',
                                                                  'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_cookielessdomain_2', __('Domain 2', 'jch-optimize'), 'jch_options_pro_cookielessdomain_2_string',
                                                                    'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_cookielessdomain_3', __('Domain 3', 'jch-optimize'), 'jch_options_pro_cookielessdomain_3_string',
                                                                    'jch-sections', 'jch_pro_cookielessdomain');
        add_settings_field('jch_options_pro_staticfiles', __('Static Files', 'jch-optimize'), 'jch_options_pro_staticfiles_string', 'jch-sections',
                                                             'jch_pro_cookielessdomain');

        add_settings_section('jch_pro_lazyload', '', 'jch_pro_lazyload_section_text', 'jch-sections');
        add_settings_field('jch_options_pro_lazyload', __('Enable', 'jch-optimize'), 'jch_options_pro_lazyload_string', 'jch-sections',
                                                          'jch_pro_lazyload');
        add_settings_field('jch_options_pro_excludeLazyLoad', __('Exclude these images', 'jch-optimize'), 'jch_options_pro_excludeLazyLoad_string',
                                                                 'jch-sections', 'jch_pro_lazyload');
        add_settings_field('jch_options_pro_excludeLazyLoadFolder', __('Exclude these folders', 'jch-optimize'),
                                                                       'jch_options_pro_excludeLazyLoadFolder_string', 'jch-sections',
                                                                       'jch_pro_lazyload');
        add_settings_field('jch_options_pro_excludeLazyLoadClass', __('Exclude these classes', 'jch-optimize'),
                                                                      'jch_options_pro_excludeLazyLoadClass_string', 'jch-sections',
                                                                      'jch_pro_lazyload');
        add_settings_field('jch_options_pro_lazyload_forceload', __('Force load images', 'jch-optimize'),
                                                                      'jch_options_pro_lazyload_forceload_string', 'jch-sections',
                                                                      'jch_pro_lazyload');

        add_settings_section('jch_pro_ocd', '', 'jch_pro_ocd_section_text', 'jch-sections');

        add_settings_field('jch_options_pro_optimizeCssDelivery_enable', __('Enable', 'jch-optimize'),
                                                                            'jch_options_pro_optimizeCssDelivery_enable_string', 'jch-sections',
                                                                            'jch_pro_ocd');
        add_settings_field('jch_options_pro_optimizeCssDelivery', __('Number of Elements', 'jch-optimize'),
                                                                     'jch_options_pro_optimizeCssDelivery_string', 'jch-sections', 'jch_pro_ocd');
        add_settings_field('jch_options_pro_optimizeCssDelivery_loadfile', __('Load combined CSS file', 'jch-optimize'),
                                                                     'jch_options_pro_optimizeCssDelivery_loadfile_string', 'jch-sections', 'jch_pro_ocd');


        add_settings_section('jch_images', '', 'jch_images_section_text', 'jch-sections');
//        add_settings_field('jch_options_kraken_optimization_level', __('Lossy Optimization', 'jch-optimize'),
//                                                                       'jch_options_kraken_optimization_level_string', 'jch-sections', 'jch_images');
        add_settings_field('jch_options_kraken_backup', __('Backup Images', 'jch-optimize'), 'jch_options_kraken_backup_string', 'jch-sections',
                                                            'jch_images');
        add_settings_section('jch_images_foldertree', '', 'jch_images_foldertree_section_text', 'jch-sections');
        add_settings_field('jch_options_optimizeimages', __('Optimize Images', 'jch-optimize'), 'jch_options_optimize_images_string', 'jch-sections',
                                                            'jch_images_foldertree');

        add_settings_section('jch_section_end', '', 'jch_section_end_text', 'jch-sections');
}

function check_jch_tasks()
{
        if (isset($_GET['jch-task']))
        {
                switch ($_GET['jch-task'])
                {
                        case 'cleancache':
                                delete_jch_cache();
                                break;

                        case 'browsercaching':
                                jch_leverage_browser_cache();
                                break;

                        case 'filepermissions':
                                jch_fix_file_permissions();
                                break;

                        case 'postresults':
                                jch_process_optimize_images_results();
                                break;

                        default:
                                break;
                }
        }
}

function jch_load_resource_files($hook)
{
        if ('settings_page_jchoptimize-settings' != $hook){
                return;
        }
        
        wp_enqueue_style('jch-bootstrap-css');
        wp_enqueue_style('jch-admin-css');
        wp_enqueue_style('jch-fonts-css');
        wp_enqueue_style('jch-chosen-css');
        wp_enqueue_style('jch-wordpress-css');
        
        wp_enqueue_script('jch-bootstrap-js');
        wp_enqueue_script('jch-tabsstate-js');
        wp_enqueue_script('jch-adminutility-js');
        wp_enqueue_script('jch-chosen-js');
        wp_enqueue_script('jch-collapsible-js');

        
}

function jch_load_scripts()
{

        ?> 
        <style type="text/css">
                .chosen-container-multi .chosen-choices li.search-field input[type=text]{ height: 25px; }    
                .chosen-container{margin-right: 4px;}

        </style>  
        <script type="text/javascript">
                function submitJchSettings()
                {
                        jQuery("form.jch-settings-form").submit();
                }

                jQuery(document).ready(function () {
                        jQuery(".chzn-custom-value").chosen({width: "240px"});

                        jQuery('.collapsible').collapsible();
                });

                var jch_form_id = 'jch_options';
        <?php                                  ?>

        </script>
        <?php

}

function delete_jch_cache()
{
        global $jch_redirect;

        JchOptimizeHelper::clearHiddenValues(JchPlatformPlugin::getPluginParams());

        $result = JchPlatformCache::deleteCache();

        if ($result !== FALSE)
        {
                jch_add_notices('success', __('The plugin\'s cache files were deleted successfully!', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('error', __('An error occurred while trying to delete the plugin\'s cache files!', 'jch-optimize'));
        }

        $jch_redirect = TRUE;
}

function jch_leverage_browser_cache()
{
        global $jch_redirect;

        $expires = JchOptimizeAdmin::leverageBrowserCaching();

        if ($expires === FALSE)
        {
                jch_add_notices('error', __('The plugin failed to add the \'leverage browser cache\' codes to the .htaccess file.', 'jch-optimize'));
        }
        elseif ($expires == 'FILEDOESNTEXIST')
        {
                jch_add_notices('warning', __('An .htaccess file could not be found in the root folder of the site.', 'jch-optimize'));
        }
        elseif ($expires == 'CODEALREADYINFILE')
        {
                jch_add_notices('notice', __('Codes for \'leverage browser caching\' already exists in the .htaccess file.', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('success', __('Codes for \'leverage browser caching\' were added to the .htaccess file successfully.', 'jch-optimize'));
        }

        $jch_redirect = TRUE;
}

function jch_fix_file_permissions()
{
        global $jch_redirect;

        $wp_filesystem = JchPlatformCache::getWpFileSystem();

        if ($wp_filesystem === false)
        {
                $result = false;
        }
        else
        {
                $result = true;

                try
                {
                        jch_chmod(JCH_PLUGIN_DIR, $wp_filesystem);
                }
                catch (Exception $ex)
                {
                        $result = false;
                }
        }

        if ($result)
        {
                jch_add_notices('success', __('The permissions of all the files and folders in the plugin were successfully updated.', 'jch-optimize'));
        }
        else
        {
                jch_add_notices('error', __('The plugin failed to update the permissions of the files and folders in the plugin.', 'jch-optimize'));
        }

        $jch_redirect = TRUE;
}

function jch_chmod($file, $wp_fs)
{

        if ($wp_fs->is_file($file))
        {
                $mode = FS_CHMOD_FILE;
        }
        elseif ($wp_fs->is_dir($file))
        {
                $mode = FS_CHMOD_DIR;
        }
        else
        {
                throw new Exception;
        }

        if (!(@chmod($file, $mode)))
        {
                throw new Exception;
        }

        if ($wp_fs->is_dir($file))
        {
                $file     = trailingslashit($file);
                $filelist = $wp_fs->dirlist($file);

                foreach ((array) $filelist as $filename => $filemeta)
                {
                        jch_chmod($file . $filename, $wp_fs);
                }
        }
}

function jch_redirect()
{
        global $jch_redirect;

        if ($jch_redirect)
        {
                $url = admin_url('options-general.php?page=jchoptimize-settings');

                wp_redirect($url);
                exit;
        }
}

function jch_process_optimize_images_results()
{
        global $jch_redirect;

        if (file_exists(JCH_PLUGIN_DIR . 'status.json'))
        {
                unlink(JCH_PLUGIN_DIR . 'status.json');
        }

        $cnt    = filter_input(INPUT_GET, 'cnt', FILTER_SANITIZE_NUMBER_INT);
        $dir    = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
        $msg    = filter_input(INPUT_GET, 'msg', FILTER_DEFAULT);

        $dir = JchPlatformUtility::decrypt($dir);

        if ($cnt !== FALSE && !is_null($cnt))
        {
                jch_add_notices('success', sprintf(__('<span class="notranslate">%1$d</span> images were optimized in <span class="notranslate">%2$s</span>', 'jch-optimize'), $cnt, $dir));
        }
        elseif ($status !== FALSE && !is_null($status))
        {
                jch_add_notices('error', sprintf(__('Failed to optimize image: <span class="notranslate">%1$s</span>', 'jch-optimize'), $msg));
        }

        $jch_redirect = TRUE;
}

function jch_add_notices($type, $text)
{
        $jch_notices = array();

        if ($notices = get_transient('jch_notices'))
        {
                $jch_notices = $notices;
        }

        $jch_notices[$type][] = $text;

        set_transient('jch_notices', $jch_notices, 60 * 5);
}

function jch_send_notices()
{
        $jch_notices = get_transient('jch_notices');

        foreach ($jch_notices as $type => $notices)
        {
                $notices = array_unique($notices);
                ?>
                <div class="notice notice-<?php echo $type ?>">
                        <?php

                        foreach ($notices as $notice)
                        {

                                ?>
                                <p> <?php echo $notice ?></p>
                                <?php

                        }

                        ?>
                </div>
                <?php

        }

        delete_transient('jch_notices');
}

function jch_options_validate($input)
{
        return $input;
}

function jch_group_start($header = '', $description = '', $class = '')
{
        echo '<div class="jch-group">'
        . '<div ' . $class . '>'
        . ($header != '' ? '             <h3>' . $header . '<span class="fa"></span></h3>' : '')
        . '             <p><em>' . $description . '</em></p>'
        . '</div><div>';
}

function jch_group_end()
{
        echo '</div></div>';
}

function jch_basic_pre_section_text()
{
        echo '<div class="tab-pane" id="basic">';
        
        $header      = __('Combine CSS and javascript files', 'jch-optimize');
        $description = __('These settings are concerned with combining CSS and javascript files into one respectively, and the minification of the combined files and the HTML, and also determines where in the HTML the combined files are placed. Refer to the documentation for more information..',
                          'jch-optimize');
        
        jch_group_start($header, $description);
}

function jch_options_combine_files_enable_string()
{
        $description = '';

        echo jch_gen_radio_field('combine_files_enable', '1', $description);
}

function jch_options_auto_settings_string()
{
        $description = __('The six icons that are above represent six preconfigured settings of the options in the \'Automatic Settings Group\'. The level of optimization increases as you go to the right but the risks of conflicts will also increase, so try each in turn and use the highest setting that work for your site. The first, which is the safest, is the default and should work on most websites. These settings do not affect the files/extensions/images etc. that you have excluded.',
                          'jch-optimize');

        $aButton = jch_get_auto_settings_buttons();

        echo '<div style="display: inline-block;">';
        echo jch_gen_button_icons($aButton, $description, '</div>');
}

function jch_options_html_minify_level_string()
{
        $description = __('If \'Minify HTML\' is enabled, this will determine the level of minification. The incremental changes per level are as follows: Basic - Adjoining whitespaces outside of elements are reduced to one whitespace; Advanced - Remove HTML comments, whitespace around block elements and undisplayed elements, Remove unnecessary whitespaces inside of elements and around their attributes; Ultra - Remove redundant attributes, for example, <span class="notranslate">\'text/javascript\'</span>, and remove quotes from around selected attributes (HTML5)',
                          'jch-optimize');

        $values = array(
                '0' => __('Basic', 'jch-optimize'),
                '1' => __('Advanced', 'jch-optimize'),
                '2' => __('Ultra', 'jch-optimize')
        );

        echo jch_gen_select_field('html_minify_level', '0', $values, $description);
}


function jch_options_htaccess_string()
{
        $description = __('By default auto is selected and the plugin will detect if mod_rewrite is enabled and will use \'url rewriting\' to remove the query from the link to the combined file to promote proxy caching. The plugin will then automatically select \'Yes\' or \'No\' based on whether support for url rewriting is detected. You can manually select the one you want if the plugin got it wrong.',
                          'jch-optimize');

        $values = array(
                '0' => __('No', 'jch-optimize'),
                '1' => __('Yes', 'jch-optimize'),
                '3' => __('Yes (Without Options +FollowSymLinks)', 'jch-optimize'),
                '2' => __('Auto', 'jch-optimize')
        );

        echo jch_gen_select_field('htaccess', '2', $values, $description, '');
}

function jch_options_try_catch_string()
{
        $description = __('If you\'re seeing javascript errors in the console, you can try enabling this option to wrap each javascript file in a <span class="notranslate">\'try-catch\'</span> block to prevent the errors from one file affecting the combined file.',
                          'jch-optimize');

        echo jch_gen_radio_field('try_catch', '1', $description);
}

function jch_options_lifetime_string()
{
        $description = __('Lifetime of aggregated cached file, measured in days.', 'jch-optimize');

        echo jch_gen_text_field('cache_lifetime', '1', $description);
}

function jch_basic_misc_section_text()
{
        jch_group_end();

        $header      = __('Miscellaneous Settings', 'jch-optimize');

        jch_group_start($header);
}

function jch_options_utility_settings_string()
{
        $attribute = jch_get_cache_info();

        $description = '';

        $aButtons = JchOptimizeAdmin::getUtilityIcons();

        echo '<div style="display: -webkit-flex; display: -ms-flex; display: -moz-flex; display: flex;">';
        echo jch_gen_button_icons($aButtons, $description, $attribute);
}

function jch_options_debug_string()
{
        $description = __('This option will add the \'commented out\' url of the individual files inside the combined file above the contents that came from that file. This is useful when configuring the plugin and trying to resolve conflicts. This will also add a <span class="notranslate">Profiler</span> menu to the <span class="notranslate">AdminBar</span> so you can review the times that the plugin methods take to run.',
                          'jch-optimize');
        echo jch_gen_radio_field('debug', '0', $description);
}

function jch_options_log_string()
{
        $description = __('Error messages will be written in a file called <span class="notranslate">\'jch-optimize.log\'</span> file in the plugin\'s logs folder.',
                          'jch-optimize');
        echo jch_gen_radio_field('log', '0', $description);
}


function jch_group_start_auto()
{
        jch_group_end();
        
        $header      = __('Automatic Settings Group', 'jch-optimize');
        $description = __('The fields in this group are automatically configured with the Automatic Settings - <span class="notranslate">(Minimum - Optimum)</span>. This is highly recommended to avoid conflicts. It is usually not necessary to set these fields manually unless you are troubleshooting a problem, so do not change these settings yourself unless you know what you are doing .',
                          'jch-optimize');
        $class       = 'class="collapsible" ';

        jch_group_start($header, $description, $class);
}

function jch_basic_auto_section_text()
{
        jch_group_start_auto();
}

function jch_options_auto_basic_string()
{
        echo '&nbsp;';
}

function jch_options_css_string()
{
        $description = __('This will combine all CSS files into one file and remove all the links to the individual files from the page, replacing it with a link generated by the plugin to the combined file.',
                          'jch-optimize');

        echo jch_gen_radio_field('css', '1', $description, 's1-on s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_javascript_string()
{
        $description = __('This will combine all javascript files into one file and remove all the links to the individual files from the page, replacing it with a link generated by the plugin to the combined file.',
                          'jch-optimize');

        echo jch_gen_radio_field('javascript', '1', $description, 's1-on s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_gzip_string()
{
        $description = __('This setting compresses the generated javascript and CSS combined files with gzip, decreasing file size dramatically. This can decrease file size dramatically.',
                          'jch-optimize');

        echo jch_gen_radio_field('gzip', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_css_minify_string()
{
        $description = __('If yes, the plugin will remove all unnecessary whitespaces and comments from the combined CSS file to reduce the total file size.',
                          'jch-optimize');

        echo jch_gen_radio_field('css_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_js_minify_string()
{
        $description = __('If yes, the plugin will remove all unnecessary whitespaces and comments from the combined javascript file to reduce the total file size.',
                          'jch-optimize');

        echo jch_gen_radio_field('js_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_html_minify_string()
{
        $description = __('If yes, the plugin will remove all unneccessary whitespaces and comments from HTML to reduce the total size of the web page.',
                          'jch-optimize');

        echo jch_gen_radio_field('html_minify', '0', $description, 's1-off s2-on s3-on s4-on s5-on s6-on');
}

function jch_options_defer_js_string()
{
        $description = __('This option will add a <span class="notranslate">\'defer\'</span> attribute to the link of the combined javascript file. This will defer the loading of the javascript until after the page is loaded to reduce \'render-blocking\'.  Do not configure this setting manually to avoid breaking your page.',
                          'jch-optimize');

        echo jch_gen_radio_field('defer_js', '0', $description, 's1-off s2-off s3-off s4-off s5-off s6-on');
}

function jch_options_bottom_js_string()
{
        $description = __('Select where and how to place the links for the combined css and javascript files. Options are, \'Preserve Execution Order\', that will split the combined file and place them around and between excluded files accordingly, \'Combine all files to one\', that will combine all included files into one and place them just below the <span class="notranslate">&lt;title&gt;</span> tag, and \'Place JS at bottom\', that will place the combined CSS file below the <span class="notranslate">&lt;title&gt;</span> tag but the combined javascript file will be just before the ending <span class="notranslate">&lt;/body&gt;</span> tag.',
                          'jch-optimize');

        $values = array(
                '0' => __('Preserve execution order', 'jch-optimize'),
                '2' => __('Combine files into one', 'jch-optimize'),
                '1' => __('Place javascript at bottom', 'jch-optimize')
        );

        echo jch_gen_select_field('bottom_js', '0', $values, $description, 'position-javascript');
}

function jch_options_auto_exclude_string()
{
        echo '&nbsp;';
}

function jch_options_includeAllExtensions_string()
{
        $description = __('By default, all files from third party plugins and external domains are excluded. If this setting is enabled, they will be included.', 'jch-optimize');
        echo jch_gen_radio_field('includeAllExtensions', '0', $description, 's1-off s2-off s3-on s4-on s5-on s6-on');
}

function jch_options_auto_pro_string()
{
        echo '&nbsp;';
}

function jch_options_pro_replaceImports_string()
{
        $description = __('The plugin will replace <span class="notranslate">@import</span> at-rules with the contents of the files they are importing. This will be done recursively.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_phpAndExternal_string()
{
        $description = __('Javascript and css files with <span class="notranslate">\'.php\'</span> file extensions, and files from external domains will be included in the combined file. This option requires that either <span class="notranslate">cURL</span> is installed on your server or your php option <span class="notranslate">\'allow_url_fopen\'</span> is enabled.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_inlineStyle_string()
{
        $description = __('In-page CSS inside <span class="notranslate">&lt;style&gt;</span> tags will be included in the aggregated file in the order they appear on the page.', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_inlineScripts_string()
{
        $description = __('In-page javascript inside <span class="notranslate">&lt;script&gt;</span> tags will be included in the combined file in the order they appear on the page.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_searchBody_string()
{
        $description = __('By default, the plugin only combines files found in the <span class="notranslate">&lt;head&gt;</span> section of the HTML. This option extends the search to the <span class="notranslate">&lt;body&gt;</span> section.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_loadAsynchronous_string()
{
        $description = __('The combined javascript file will be loaded asynchronously to avoid \'render-blocking\' and speed up download of the web page.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_get_cache_info()
{
        static $attribute = FALSE;

        if ($attribute === FALSE)
        {
                $wp_filesystem = JchPlatformCache::getWpFileSystem();

                if ($wp_filesystem !== false && $wp_filesystem->exists(JCH_CACHE_DIR))
                {
                        JchPlatformCache::initializecache();
                        
                        $dirlist = $wp_filesystem->dirlist(JCH_CACHE_DIR, false, false);

                        $size = 0;

                        foreach ($dirlist as $file)
                        {
                                if ($file['name'] == 'index.html')
                                {
                                        continue;
                                }

                                $size += $file['size'];
                        }

                        $decimals = 2;
                        $sz       = 'BKMGTP';
                        $factor   = (int) floor((strlen($size) - 1) / 3);
                        $size     = sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . $sz[$factor];

                        $no_files = number_format(count($dirlist) - 1);
                }
                else
                {
                        $size     = '0';
                        $no_files = '0';
                }

                $attribute = '<div><br><div><em>' . sprintf(__('Number of files: <span class="notranslate">%d</span>'), $no_files) . '</em></div>'
                        . '<div><em>' . sprintf(__('Size: <span class="notranslate">%s</span>'), $size) . '</em></div></div>'
                        . '</div>';
        }

        return $attribute;
}

function jch_advanced_auto_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="exclude">';
}

function jch_advanced_exclude_section_text()
{
        $header      = __('Exclude files and plugins', 'jch-optimize');
        $description = __('These are the settings used to exclude individual files or files from select plugins if the files do not work well when they are combined by the plugin. If you\'re not seeing the files or extensions you want to exclude in the drop-down list, you can  manually add the files or plugins to the list. To add a file to the list manually, type the url in the textbox and click the \'Add item\' button.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_excludeCss_string()
{
        $description = __('Select the CSS files you want to exclude.', 'jch-optimize');
        $option      = 'excludeCss';

        $values = jch_get_field_value('css', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeJs_string()
{
        $description = __('Select the javascript files you want to exclude.', 'jch-optimize');
        $option      = 'excludeJs';

        $values = jch_get_field_value('js', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeCssComponents_string()
{
        $description = __('Select the plugins that you want to exclude CSS files from.', 'jch-optimize');
        $option      = 'excludeCssComponents';

        $values = jch_get_field_value('css', $option, 'extension');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_excludeJsComponents_string()
{
        $description = __('Select the plugins that you want to exclude javascript files from.',
                          'jch-optimize');
        $option      = 'excludeJsComponents';

        $values = jch_get_field_value('js', $option, 'extension');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_loadfilesasync_spacer_string()
{
        echo '&nbsp;';
}

function jch_options_pro_loadFilesAsync_string()
{
        $description = __('The links to these files will be removed from the HTML but their contents will not be placed in the combined file. Instead, they will be loaded dynamically from the combined file. You can try using this option if you want to exclude a javascript file while using a setting higher than <span class="notranslate">\'Average\'</span> and still preserve the original execution order of the files on the page.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_inline_exclude_section_text()
{
        jch_group_end();

        $header      = __('Exclude inline styles and inline scripts from aggregation');
        $description = __('These settings are used to exclude \'in-page\' CSS and javascript inside <span class="notranslate">&lt;style&gt;</span> and <span class="notranslate">&lt;script&gt;</span> tags respectively. The first few characters that appear in these tags will show in the drop-down list. To add a <span class="notranslate">&lt;style&gt;</span> or <span class="notranslate">&lt;script&gt;</span> manually, type a word or phrase that is unique to the <span class="notranslate">&lt;style&gt;</span> or <span class="notranslate">&lt;script&gt;</span> in the textbox and click the \'Add item\' button',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_pro_excludeStyles_string()
{
        $description = __('Select the \'in-page\' <span class="notranslate">&lt;style&gt;</span> you want to exclude.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_excludeScripts_string()
{
        $description = __('Select the \'in-page\' <span class="notranslate">&lt;script&gt;</span> you want to exclude.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_advanced_remove_section_text()
{
        jch_group_end();

        $header      = __('Remove CSS and javascript files from the page');
        $description = __('These settings will remove the css and javascript files you have selected from the page and prevent them from downloading. This is useful for removing unused and unwanted files to help prevent conflicts and further optimize the page.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_removeCss_string()
{
        $description = __('Remove the CSS files that you have selected from the page.',
                          'jch-optimize');

        $option = 'pro_removeCss';

        $values = jch_get_field_value('css', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_removeJs_string()
{
        $description = __('Remove the javascript files that you have selected from the page.',
                          'jch-optimize');

        $option = 'pro_removeJs';

        $values = jch_get_field_value('js', $option, 'file');

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_gen_button_icons(array $aButton, $description = '', $attribute = '')
{
        $sField = JchOptimizeAdmin::generateIcons($aButton);
        $sField .= $attribute;
        if ($description != '')
        {
                $sField .= '<div class="description" style="margin-top:-40px"><div>' . $description . '</div></div>';
        }

        return $sField;
}

function jch_sprite_manual_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="sprite">';

        $header      = __('Sprite Generator', 'jch-optimize');
        $description = __('If yes will combine selected background images in one image called a sprite to reduce http requests.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_csg_enable_string()
{
        $description = '';

        echo jch_gen_radio_field('csg_enable', '0', $description);
}

function jch_options_csg_direction_string()
{
        $description = __('Determine in which direction the images must be placed in the sprite.', 'jch-optimize');

        $values = array('vertical' => __('vertical', 'jch-optimize'), 'horizontal' => __('horizontal', 'jch-optimize'));

        echo jch_gen_select_field('csg_direction', 'vertical', $values, $description);
}

function jch_options_csg_wrap_images_string()
{
        $description = __('This setting will wrap images in sprite into another row or column if the length of the sprite becomes longer than 2000px.',
                          'jch-optimize');

        echo jch_gen_radio_field('csg_wrap_images', '0', $description);
}

function jch_options_csg_exclude_images_string()
{
        $description = __('You can exclude one or more of the images if they are displayed incorrectly.',
                          'jch-optimize');

        $option = 'csg_exclude_images';

        $values = jch_get_field_value('images', $option);

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_options_csg_include_images_string()
{
        $description = __('You can include additional images in the sprite to the ones that were selected by default. Exercise care with this option as these files are likely to not display correctly.',
                          'jch-optimize');

        $option = 'csg_include_images';

        $values = jch_get_field_value('images', $option);

        echo jch_gen_multiselect_field($option, $values, $description);
}

function jch_img_attributes_section_text()
{
        jch_group_end();

        $header      = __('Add Image Attributes', 'jch-optimize');
        $description = __('When enabled, the plugin will add missing width and height attributes to <span class="notranslate">&lt;img/&gt;</span>  elements',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_img_attributes_enable_string()
{
        echo jch_gen_radio_field('img_attributes_enable', '0', '');
}

function jch_pro_group_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="pro">';

        jch_group_start();
}

function jch_options_pro_downloadid_string()
{
        $description = __('Enter your download ID to enable automatic updates of the pro version. Log into your account on the jch-optimize.net website and access the download id from the \'My Account -> My Download ID\' menu item',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_pro_cookielessdomain_section_text()
{
        jch_group_end();

        $header      = __('CDN/Cookieless Domain', 'jch-optimize');
        $description = __('Enter your CDN or cookieless domain here. The plugin will load all static files including background images, combined javascript and css files, and generated sprite from this domain. This requires that this domain is already set up and points to your site root. You can also use multiple domains and the plugin will alternate the domains among the static files. You can also select the file types that you want to be loaded over these domains.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_pro_cookielessdomain_enable_string()
{
        
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_cookielessdomain_string()
{
        
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_cookielessdomain_2_string()
{
        
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_cookielessdomain_3_string()
{
        
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_staticfiles_string()
{
        $description = '';

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_pro_lazyload_section_text()
{
        jch_group_end();

        $header      = __('Lazy Load Images');
        $description = __('Enable to delay the loading of images until they are scrolled into view. This further speeds up the loading of the page and reduces http requests.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_pro_lazyload_string()
{
        $description = __('Enable to delay the loading of images until after the page loads and they are scrolled into view. This further reduces http requests and speeds up the loading of the page.',
                          'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_excludeLazyLoad_string()
{
        $description = __('Select or manually add the urls of the images you want to exclude from lazy load.', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_excludeLazyLoadFolder_string()
{
        $description = __('Exclude all the images in the selected folders.', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_excludeLazyLoadClass_string()
{
        $description = __('Exclude all images that have these classes declared on the <span class="notranslate">&lt;img&gt;</span> element', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_options_pro_lazyload_forceload_string()
{
        $description = __('If enabled, will load all images after the page is loaded. Useful if you have images that are not showing when Lazy-load is enabled. Otherwise, images will be loaded when they are scrolled into view.', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}

function jch_pro_ocd_section_text()
{
        jch_group_end();

        $header      = __('Optimize CSS Delivery');
        $description = __('The plugin will attempt to extract the critical CSS that is required to format the page above the fold and put this in a <span class="notranslate">&lt;style&gt;</span> element inside the <span class="notranslate">&lt;head&gt;</span> section of the HTML to prevent \'render-blocking\'. The combined CSS will then be loaded asynchronously via javascript. Select the number of HTML elements from the top of the page that you want the plugin to find the critical CSS for. The smaller the number, the faster your site but you might see some jumping of the page if the number is too small.',
                          'jch-optimize');

        jch_group_start($header, $description);
}

function jch_options_pro_optimizeCssDelivery_enable_string()
{
        
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_optimizeCssDelivery_string()
{
         
          echo jch_gen_proonly_field();
          

        
}

function jch_options_pro_optimizeCssDelivery_loadFile_string()
{
        $description = __('Select when the combined CSS file should be loaded', 'jch-optimize');

        
          echo jch_gen_proonly_field($description);
          

        
}


function jch_images_section_text()
{
        jch_group_end();

        echo '</div>
  <div class="tab-pane" id="images">';

        $header      = __('Optimize Images', 'jch-optimize');
        $description = __('Use our API to optimize the images on your server. Be sure to save your \'Download ID\' in the plugin before trying to optimize images as that will authenticate you to access the API. Use the file tree to select the subfolders and files you want to optimize. Files will be optimized in subfolders recursively. If you want to rescale your images while optimizing, enter the new width and height',
                          'jch-optimize');

        jch_group_start($header, $description);
}

//function jch_options_kraken_optimization_level_string()
//{
//        $description = __('You can sacrifice a small amount of image quality for up to 90% of the original file weight by choosing lossy optimization versus Non-lossy. (Recommended!)',
//                          'jch-optimize');
//
//        $values = array('0' => __('Non-Lossy', 'jch-optimize'), '1' => __('Lossy', 'jch-optimize'));
//
//        echo jch_gen_select_field('kraken_optimization_level', '0', $values, $description);
//        ;
//}

function jch_options_kraken_backup_string()
{
        $description = __('The plugin will copy the original images in the <span class="notranslate">jch_optimize_image_backup</span> directory prior to optimization',
                          'jch-optimize');
        $values = array('1' => __('All optimized images', 'jch-optimize'), '0' => __('Only resized images', 'jch-optimize'));
        
        echo jch_gen_select_field('kraken_backup', '1', $values, $description, $class = '');
}

function jch_images_foldertree_section_text()
{
        jch_group_end();
}

function jch_options_optimize_images_string()
{
        

        
          echo jch_gen_proonly_field();
          
}

function jch_section_end_text()
{
        echo '</div>';
}

function jch_gen_radio_field($option, $default, $description, $class = '', $auto_option = FALSE)
{
        $options = get_option('jch_options');

        $checked = 'checked="checked"';
        $no      = '';
        $yes     = '';
        $auto    = '';
        $symlink = '';

        if (!isset($options[$option]))
        {
                $options[$option] = $default;
        }

        if ($options[$option] == '1')
        {
                $yes = $checked;
        }
        elseif ($options[$option] == '2')
        {
                $auto = $checked;
        }
        elseif ($options[$option] == '3')
        {
                $symlink = $checked;
        }
        else
        {
                $no = $checked;
        }

        $radio = '<fieldset id="jch_options_' . $option . '" class="radio btn-group ' . $class . '">' .
                '        <input type="radio" id="jch_options_' . $option . '0" name="jch_options[' . $option . ']" value="0" ' . $no . ' >' .
                '        <label for="jch_options_' . $option . '0" class="btn">No</label>' .
                '        <input type="radio" id="jch_options_' . $option . '1" name="jch_options[' . $option . ']" value="1" ' . $yes . ' >' .
                '        <label for="jch_options_' . $option . '1" class="btn">Yes</label>';

        if ($auto_option)
        {
                $radio .= '        <input type="radio" id="jch_options_' . $option . '3" name="jch_options[' . $option . ']" value="3" ' . $symlink . ' >' .
                        '        <label for="jch_options_' . $option . '3" class="btn">Yes (Without Options +FollowSymLinks)</label>';

                $radio .= '        <input type="radio" id="jch_options_' . $option . '2" name="jch_options[' . $option . ']" value="2" ' . $auto . ' >' .
                        '        <label for="jch_options_' . $option . '2" class="btn">Auto</label>';
        }

        $radio .= '</fieldset>';

        if ($description)
        {
                $radio .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $radio;
}

function jch_gen_checkboxes_field($option, $values, $description, $class)
{
        $options = get_option('jch_options');

        if (!empty($options[$option]))
        {
                $checked_static_files = $options[$option];
        }
        else
        {
                $checked_static_files = array_keys($values);
        }

        $input = '<fieldset id="jch_options_' . $option . '" class="' . $class . '">' .
                '<ul>';

        $i = 0;
        foreach ($values as $key => $value)
        {
                $checked = '';

                if (in_array($key, $checked_static_files))
                {
                        $checked = 'checked';
                }

                $input .= '<li>'
                        . '<input type="checkbox" id="jch_options_' . $option . $i++ . '" name="jch_options[' . $option . '][]" value="' . $key . '" ' . $checked . '>'
                        . '<label for="jform_params_pro_staticfiles0">' . $value . '</label>'
                        . '</li>';
        }

        $input .= '</li>'
                . '</ul>'
                . '</fieldset>';

        return $input;
}

function jch_gen_text_field($option, $default, $description, $class = '', $size = '6')
{
        $options = get_option('jch_options');

        if (!isset($options[$option]))
        {
                $value = $default;
        }
        else
        {
                $value = $options[$option];
        }

        $input = '<input type="text" name="jch_options[' . $option . ']" id="jch_options_' . $option . '" value="' . $value . '" size="' . $size . '" class="' . $class . '">';

        if ($description)
        {
                $input .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $input;
}

function jch_gen_select_field($option, $default, $values, $description, $class = '')
{
        $options = get_option('jch_options');

        if (!isset($options[$option]))
        {
                $selected_value = $default;
        }
        else
        {
                $selected_value = $options[$option];
        }

        $select = '<select id="jch_options_' . $option . '" name="jch_options[' . $option . ']" class="' . $class . '" >';

        foreach ($values as $key => $value)
        {
                $selected = $selected_value == $key ? 'selected="selected"' : '';
                $select .= '          <option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $select .= '</select>';

        if ($description)
        {
                $select .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $select;
}

function jch_gen_textarea_field($option, $value, $description, $class = '')
{
        $options = get_option('jch_options');

        if (!isset($options[$option]))
        {
                $value = $default;
        }
        else
        {
                $value = $options[$option];
        }

        $textarea = '<textarea name="jch_options[' . $option . ']" id="jch_options_' . $option . '" cols="35" rows="3" class="' . $class . '">'
                . $value . '</textarea>';

        if ($description)
        {
                $textarea .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $textarea;
}

function jch_gen_multiselect_field($option, $values, $description, $class = '')
{
        $options = get_option('jch_options');

        if (isset($options[$option]))
        {
                $selected_values = JchOptimizeHelper::getArray($options[$option]);
        }
        else
        {
                $selected_values = array();
        }

        $select = '<select id="jch_options_' . $option . '" name="jch_options[' . $option . '][]" class="inputbox chzn-custom-value input-xlarge ' . $class . '" multiple="multiple" size="5" data-custom_group_text="Custom Position" data-no_results_text="Add custom item">';

        foreach ($values as $key => $value)
        {
                $selected = in_array($key, $selected_values) ? 'selected="selected"' : '';
                $select .= '          <option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $select .= '</select>';
        $select .= '<button type="button" onclick="addJchOption(\'jch_options_' . $option . '\')">Add item</button>';

        if ($description)
        {
                $select .= '<div class="description"><div>' . $description . '</div></div>';
        }

        return $select;
}

function jch_get_auto_settings_buttons()
{
        return JchOptimizeAdmin::getSettingsIcons();
}

function jch_get_admin_object()
{
        static $oJchAdmin = NULL;

        if (is_null($oJchAdmin))
        {
                global $jch_redirect;

                $params    = JchPlatformSettings::getInstance(get_option('jch_options'));
                $oJchAdmin = new JchOptimizeAdmin($params, TRUE);

                if (get_transient('jch_optimize_ao_exception'))
                {
                        delete_transient('jch_optimize_ao_exception');
                }
                else
                {
                        try
                        {
                                $oJchAdmin->getAdminLinks(new JchPlatformHtml($params), '');
                        }
                        catch (RunTimeException $ex)
                        {
                                jch_add_notices('info', $ex->getMessage());
                                set_transient('jch_optimize_ao_exception', 1, 1);

                                $jch_redirect = TRUE;
                        }
                        catch (Exception $ex)
                        {
                                JchOptimizeLogger::log($ex->getMessage(), $params);

                                jch_add_notices('error', $ex->getMessage());
                                set_transient('jch_optimize_ao_exception', 1, 1);

                                $jch_redirect = TRUE;
                        }
                }
        }

        return $oJchAdmin;
}

function jch_get_field_value($sType, $sExcludeParams, $sGroup = '')
{
        $oJchAdmin = jch_get_admin_object();

        return $oJchAdmin->prepareFieldOptions($sType, $sExcludeParams, $sGroup);
}

__('Only available in Pro Version!', 'jch-optimize');


  function jch_gen_proonly_field($description = '')
  {
  $field = '<div><em style="padding: 5px; background-color: white; border: 1px #ccc;">' . __('Only available in Pro Version!', 'jch-optimize') . '</em></div>';

  if ($description != '')
  {
  $field .= '<div class="description"><div>' . $description . '</div></div>';
  }

  return $field;
  }

  




