<?php

function import_elementor_template() {
    // Define templates and their details
    $templates = [
        [
            'title' => 'Header Template',
            'file' => 'elementor-8527-2024-04-23.json',
            'type' => 'header',
        ],
        [
            'title' => 'hero section',
            'file' => 'elementor-22-2024-04-23.json',
            'type' => 'section',
        ],
        // New entry for the Partner Slider
        [
            'title' => 'partner slider',
            'file' => 'elementor-143-2024-04-23.json',
            'type' => 'slider',  // Specify the type if it's different from other templates
        ],
        // Add the footer template
        [
            'title' => 'footer template',
            'file' => 'elementor-133-2024-04-23.json',
            'type' => 'footer',
        ],
        // Add the Image left text right template
        [
            'title' => 'image left text right template',
            'file' => 'elementor-215-2024-04-23.json',
            'type' => 'section',
        ],
        // Add the text left image right template
        [
            'title' => 'text left text image Template',
            'file' => 'elementor-222-2024-04-23.json',
            'type' => 'section',
        ],
        // Add the text middle template
        [
            'title' => 'text middle template',
            'file' => 'elementor-227-2024-04-23.json',
            'type' => 'section',
        ],
        // Add the faq template
        [
            'title' => 'faq template',
            'file' => 'elementor-233-2024-04-23.json',
            'type' => 'section',
        ]
    ];

    foreach ($templates as $template) {
        // Convert title to slug
        $slug = sanitize_title($template['title']);  
        $exists = get_posts([
            'post_type' => 'elementor_library',
            'meta_key' => '_elementor_template_type',
            'meta_value' => $template['type'],
            'name' => $slug,
            'numberposts' => 1,
            'fields' => 'ids'
        ]);

        if (empty($exists)) {
            $json_file_path = get_stylesheet_directory() . '/assets/json/' . $template['file'];
            $json_content = file_get_contents($json_file_path);
            if (!$json_content) {
                error_log('Failed to read JSON file for ' . $template['title']);
                continue;
            }

            $template_data = json_decode($json_content, true);
            if (!$template_data) {
                error_log('Failed to decode JSON for ' . $template['title']);
                continue;
            }

            $elementor_instance = \Elementor\Plugin::instance();

            $post_data = [
                'post_type'    => 'elementor_library',
                'post_title'   => $template['title'],
                'post_content' => '',
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'post_name'    => $slug
            ];

            $post_id = wp_insert_post($post_data);

            if ($post_id && !empty($template_data['content'])) {
                update_post_meta($post_id, '_elementor_data', wp_slash(json_encode($template_data['content'])));
                update_post_meta($post_id, '_elementor_template_type', $template['type']);
                update_post_meta($post_id, '_elementor_edit_mode', 'builder');
                wp_cache_flush();
                error_log('Template ' . $template['title'] . ' imported successfully!');
            } else {
                error_log('Failed to import template ' . $template['title']);
            }
        } else {
            error_log('Template ' . $template['title'] . ' already exists with ID ' . $exists[0]);
        }
    }
}

add_action('init', 'import_elementor_template');
?>