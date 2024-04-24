<?php
class AdminMessages {
    private $required_plugins;

    public function __construct() {
        $this->required_plugins = [
            [
                'path' => 'advanced-custom-fields/acf.php', // Path for ACF.
                'name' => 'Advanced Custom Fields',
                'url'  => 'https://wordpress.org/plugins/advanced-custom-fields/'
            ],
            [
                'path' => 'sitepress-multilingual-cms/sitepress.php', // Path for WPML.
                'name' => 'WPML Multilingual CMS',
                'url'  => 'https://wpml.org/'
            ],
            [
                'path' => 'wp-rocket/wp-rocket.php', // Path for WP Rocket.
                'name' => 'WP Rocket',
                'url'  => 'https://wp-rocket.me/'
            ],
            [
                'path' => 'wordfence/wordfence.php', // Path for Wordfence Security.
                'name' => 'Wordfence Security',
                'url'  => 'https://wordpress.org/plugins/wordfence/'
            ],
            [
                'path' => 'custom-post-type-ui/custom-post-type-ui.php', // Path for CPT UI.
                'name' => 'Custom Post Type UI',
                'url'  => 'https://wordpress.org/plugins/custom-post-type-ui/'
            ],
            [
                'path' => 'elementor/elementor.php', // Path for Elementor.
                'name' => 'Elementor Plugin',
                'url'  => 'https://wordpress.org/plugins/elementor/'
            ]
        ];

        add_action('admin_notices', array($this, 'showAdminMessages'));
        add_action('admin_post_install_all_plugins', array($this, 'installAllPlugins'));
    }

    public function showAdminMessages() {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $inactive_plugins = [];

        foreach ($this->required_plugins as $plugin) {
            if (!is_plugin_active($plugin['path'])) {
                $inactive_plugins[] = $plugin['name'];
            }
        }

        if (!empty($inactive_plugins)) {
            echo '<div id="message" class="error"><p><strong>This theme requires the following plugins:</strong></p>';
            foreach ($inactive_plugins as $plugin_name) {
                echo '<p>' . $plugin_name . '</p>';
            }
            echo '<p><button onclick="document.location.href=\''. admin_url('admin-post.php?action=install_all_plugins') .'\'">Install & Ativate</button></p></div>';
        }
    }

    public function installAllPlugins() {
        if (!current_user_can('install_plugins')) {
            wp_die('You do not have sufficient permissions to install plugins on this site.');
        }

        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
        include_once(ABSPATH . 'wp-admin/includes/file.php');

        $plugin_upgrader = new Plugin_Upgrader();
        $plugin_upgrader->init(); // Initialize the upgrader.

        foreach ($this->required_plugins as $plugin) {
            $plugin_path = $plugin['path'];
            if (!is_plugin_active($plugin_path)) {
                // Check if the plugin is already installed
                $installed_plugins = get_plugins();
                if (!array_key_exists($plugin_path, $installed_plugins)) {
                    // The plugin is not installed; attempt installation.
                    $api = plugins_api('plugin_information', ['slug' => dirname($plugin_path)]);
                    if (is_wp_error($api)) {
                        wp_die($api);
                    } else {
                        // Download and install the plugin
                        $result = $plugin_upgrader->install($api->download_link);
                        if (is_wp_error($result)) {
                            wp_die($result);
                        }
                    }
                }
                // Check again if the plugin is installed (including newly installed)
                if (array_key_exists($plugin_path, get_plugins())) {
                    // Activate the plugin if it's installed but not active
                    activate_plugin($plugin_path);
                }
            }
        }

        wp_redirect(admin_url());
        exit;
    }
}

new AdminMessages();

?>