<?php
class PluginMessage {
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
}

new PluginMessage();

?>