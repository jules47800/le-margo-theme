<?php
/**
 * Gestion des méta-données SEO et Open Graph
 *
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajoute une meta box pour les paramètres SEO
 */
function le_margo_add_seo_meta_box() {
    $post_types = array('page', 'post', 'daily_menu', 'testimonial'); // Ajoutez ici vos custom post types
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'le_margo_seo_meta_box',
            __('Paramètres SEO & Partage Social', 'le-margo'),
            'le_margo_render_seo_meta_box',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'le_margo_add_seo_meta_box');

/**
 * Affiche le contenu de la meta box
 */
function le_margo_render_seo_meta_box($post) {
    // Récupération des valeurs existantes
    $meta_title = get_post_meta($post->ID, '_le_margo_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_le_margo_meta_description', true);
    $og_image = get_post_meta($post->ID, '_le_margo_og_image', true);
    
    // Nonce pour la sécurité
    wp_nonce_field('le_margo_seo_meta_box', 'le_margo_seo_meta_box_nonce');
    ?>
    <div class="le-margo-seo-meta-box">
        <style>
            .le-margo-seo-meta-box .form-field { margin: 1em 0; }
            .le-margo-seo-meta-box .form-field label { display: block; margin-bottom: 5px; font-weight: 600; }
            .le-margo-seo-meta-box .form-field input[type="text"],
            .le-margo-seo-meta-box .form-field textarea { width: 100%; }
            .le-margo-seo-meta-box .form-field textarea { height: 80px; }
            .le-margo-seo-meta-box .description { color: #666; font-style: italic; margin-top: 5px; }
            .le-margo-seo-meta-box .og-image-preview { max-width: 300px; margin-top: 10px; }
            .le-margo-seo-meta-box .og-image-preview img { max-width: 100%; height: auto; }
        </style>

        <div class="form-field">
            <label for="le_margo_meta_title"><?php _e('Meta Title', 'le-margo'); ?></label>
            <input type="text" id="le_margo_meta_title" name="le_margo_meta_title" 
                   value="<?php echo esc_attr($meta_title); ?>" />
            <p class="description">
                <?php _e('Le titre qui apparaîtra dans les résultats de recherche. Idéalement entre 50-60 caractères.', 'le-margo'); ?>
            </p>
        </div>

        <div class="form-field">
            <label for="le_margo_meta_description"><?php _e('Meta Description', 'le-margo'); ?></label>
            <textarea id="le_margo_meta_description" name="le_margo_meta_description"><?php echo esc_textarea($meta_description); ?></textarea>
            <p class="description">
                <?php _e('La description qui apparaîtra dans les résultats de recherche. Idéalement entre 150-160 caractères.', 'le-margo'); ?>
            </p>
        </div>

        <div class="form-field">
            <label for="le_margo_og_image"><?php _e('Image de Partage Social', 'le-margo'); ?></label>
            <input type="hidden" id="le_margo_og_image" name="le_margo_og_image" 
                   value="<?php echo esc_attr($og_image); ?>" />
            
            <button type="button" class="button" id="le_margo_og_image_button">
                <?php _e('Choisir une image', 'le-margo'); ?>
            </button>

            <div class="og-image-preview">
                <?php if ($og_image): ?>
                    <img src="<?php echo esc_url(wp_get_attachment_image_url($og_image, 'medium')); ?>" />
                <?php endif; ?>
            </div>
            
            <p class="description">
                <?php _e('Cette image sera utilisée lors du partage sur les réseaux sociaux. Taille recommandée : 1200x630 pixels.', 'le-margo'); ?>
            </p>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#le_margo_og_image_button').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: '<?php _e('Choisir une image de partage', 'le-margo'); ?>',
                button: {
                    text: '<?php _e('Utiliser cette image', 'le-margo'); ?>'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#le_margo_og_image').val(attachment.id);
                $('.og-image-preview').html('<img src="' + attachment.url + '" />');
            });
            
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

/**
 * Sauvegarde les méta-données
 */
function le_margo_save_seo_meta_box($post_id) {
    // Vérifications de sécurité
    if (!isset($_POST['le_margo_seo_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['le_margo_seo_meta_box_nonce'], 'le_margo_seo_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sauvegarde des méta-données
    if (isset($_POST['le_margo_meta_title'])) {
        update_post_meta($post_id, '_le_margo_meta_title', sanitize_text_field($_POST['le_margo_meta_title']));
    }
    
    if (isset($_POST['le_margo_meta_description'])) {
        update_post_meta($post_id, '_le_margo_meta_description', sanitize_textarea_field($_POST['le_margo_meta_description']));
    }
    
    if (isset($_POST['le_margo_og_image'])) {
        update_post_meta($post_id, '_le_margo_og_image', absint($_POST['le_margo_og_image']));
    }
}
add_action('save_post', 'le_margo_save_seo_meta_box');

/**
 * Ajoute les méta-données dans le head
 */
function le_margo_output_seo_meta() {
    global $post;
    
    $meta_title = '';
    $meta_description = '';
    $og_image_id = '';
    
    // Gestion des pages singulières
    if (is_singular() && isset($post->ID)) {
        $meta_title = get_post_meta($post->ID, '_le_margo_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_le_margo_meta_description', true);
        $og_image_id = get_post_meta($post->ID, '_le_margo_og_image', true);
    }
    // Gestion des archives avec configuration automatique
    elseif (is_archive()) {
        $config = le_margo_get_seo_config();
        
        if (is_post_type_archive('daily_menu') && isset($config['archive-daily_menu'])) {
            $meta_title = $config['archive-daily_menu']['title'];
            $meta_description = $config['archive-daily_menu']['description'];
        }
        elseif (is_post_type_archive('testimonial') && isset($config['archive-testimonial'])) {
            $meta_title = $config['archive-testimonial']['title'];
            $meta_description = $config['archive-testimonial']['description'];
        }
    }
    // Gestion de la 404
    elseif (is_404()) {
        $config = le_margo_get_seo_config();
        if (isset($config['404'])) {
            $meta_title = $config['404']['title'];
            $meta_description = $config['404']['description'];
        }
    }
    
    // Titre personnalisé
    if (!empty($meta_title)) {
        add_filter('document_title_parts', function($title) use ($meta_title) {
            return array('title' => $meta_title);
        }, 99);
        
        echo '<meta property="og:title" content="' . esc_attr($meta_title) . '" />' . "\n";
    }
    
    // Description personnalisée
    if (!empty($meta_description)) {
        echo '<meta name="description" content="' . esc_attr($meta_description) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($meta_description) . '" />' . "\n";
    }
    
    // Image Open Graph personnalisée
    if (!empty($og_image_id)) {
        $og_image_url = wp_get_attachment_image_url($og_image_id, 'full');
        if ($og_image_url) {
            echo '<meta property="og:image" content="' . esc_url($og_image_url) . '" />' . "\n";
            
            $og_image_meta = wp_get_attachment_metadata($og_image_id);
            if (!empty($og_image_meta['width'])) {
                echo '<meta property="og:image:width" content="' . esc_attr($og_image_meta['width']) . '" />' . "\n";
            }
            if (!empty($og_image_meta['height'])) {
                echo '<meta property="og:image:height" content="' . esc_attr($og_image_meta['height']) . '" />' . "\n";
            }
        }
    }
    
    // Méta-données générales
    echo '<meta property="og:type" content="' . (is_front_page() ? 'website' : 'article') . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
    echo '<meta property="og:site_name" content="Le Margo" />' . "\n";
}
add_action('wp_head', 'le_margo_output_seo_meta', 0); // Priorité 0 pour s'exécuter avant les autres actions 