<?php
/*
Plugin Name: AccessiWeb Widget
Plugin URI: https://www.accessiweb.it/
Description: AccessiWeb Widget: Il web accessibile e inclusivo.
Version: 1.0.0
Author: dunp scpl
Author URI: https://www.dunp.it/
License: GPL2
License URI: https://it.opensuse.org/GNU_General_Public_License
*/

// exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Funzione per aggiungere le opzioni del plugin
function accessiweb_add_admin_menu() {
    $icon_svg = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" height="45" viewBox="0 0 512 512" fill="currentColor"><path d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm161.5-86.1c-12.2-5.2-26.3 .4-31.5 12.6s.4 26.3 12.6 31.5l11.9 5.1c17.3 7.4 35.2 12.9 53.6 16.3v50.1c0 4.3-.7 8.6-2.1 12.6l-28.7 86.1c-4.2 12.6 2.6 26.2 15.2 30.4s26.2-2.6 30.4-15.2l24.4-73.2c1.3-3.8 4.8-6.4 8.8-6.4s7.6 2.6 8.8 6.4l24.4 73.2c4.2 12.6 17.8 19.4 30.4 15.2s19.4-17.8 15.2-30.4l-28.7-86.1c-1.4-4.1-2.1-8.3-2.1-12.6V235.5c18.4-3.5 36.3-8.9 53.6-16.3l11.9-5.1c12.2-5.2 17.8-19.3 12.6-31.5s-19.3-17.8-31.5-12.6L338.7 175c-26.1 11.2-54.2 17-82.7 17s-56.5-5.8-82.7-17l-11.9-5.1zM256 160a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"></path></svg>');

    add_menu_page(
        'AccessiWeb Widget', // Titolo della pagina
        'AccessiWeb', // Nome del menu
        'manage_options', // Capacità necessaria per accedere
        'accessiweb-widget', // Slug della pagina
        'accessiweb_options_page', // Funzione di callback
        $icon_svg, // Icona SVG del menu
        2 // Posizione nel menu
    );
}
add_action('admin_menu', 'accessiweb_add_admin_menu');

// Funzione per visualizzare la pagina delle opzioni
function accessiweb_options_page() {
    // Ottieni l'URL della cartella del plugin
    $plugin_url = plugin_dir_url(__FILE__);
    ?>
    <div class="wrap">
        <header>
            <a href="https://www.accessiweb.it" target="_blank" title="accessiweb.it">
                <img src="<?php echo esc_url($plugin_url . 'images/accessiweb.svg'); ?>" alt="AccessiWeb.it - il web accessibile e inclusivo" width="203" height="68">
            </a>
        </header>
        <hr>
        <h1>Configurazione Widget di accessibilità AccessiWeb</h1>
        <p>Il widget AccessiWeb aiuta a rendere il tuo sito web <b>accessibile</b> e <b>fruibile</b> da qualsiasi utente.</p>
        <p>Aggiunge al tuo sito profili di navigazione, regolazione dei contenuti, impostazioni di accessibilià e regolazioni cromatiche per un'esperienza di navigazione completa e personalizzabile.</p>
        <p>Registrarti gratuitamente e ottieni la tua chiave di licenza su <a href="https://www.accessiweb.it/" target="_blank" title="accessiweb.it">accessiweb.it</a>.</p>
        <hr>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('accessiweb_widget_options');
            do_settings_sections('accessiweb_widget');
            wp_nonce_field('accessiweb_save_settings', 'accessiweb_nonce');
            submit_button();
            ?>
        </form>
        <hr>
        <footer>
            <small>Accessiweb è proprietà di dunp scpl</small>
        </footer>
    </div>
    <?php
}

// Funzione per inizializzare le impostazioni del plugin
function accessiweb_settings_init() {
    register_setting('accessiweb_widget_options', 'accessiweb_widget_settings', 'accessiweb_sanitize_options');

    add_settings_section(
        'accessiweb_widget_section',
        'Impostazioni Widget',
        'accessiweb_settings_section_callback',
        'accessiweb_widget'
    );

    add_settings_field(
        'accessiweb_license_key',
        'License Key',
        'accessiweb_license_key_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_primary_color',
        'Primary Color',
        'accessiweb_primary_color_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_position_x',
        'Position X',
        'accessiweb_position_x_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_position_y',
        'Position Y',
        'accessiweb_position_y_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_offset_x',
        'Offset X',
        'accessiweb_offset_x_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_offset_y',
        'Offset Y',
        'accessiweb_offset_y_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_unit_x',
        'Unit X',
        'accessiweb_unit_x_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_unit_y',
        'Unit Y',
        'accessiweb_unit_y_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );

    add_settings_field(
        'accessiweb_icon_size',
        'Icon Size',
        'accessiweb_icon_size_render',
        'accessiweb_widget',
        'accessiweb_widget_section'
    );
}
add_action('admin_init', 'accessiweb_settings_init');

function accessiweb_sanitize_options($input) {
    // Verifica del nonce
    if (!isset($_POST['accessiweb_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['accessiweb_nonce'])), 'accessiweb_save_settings')) {
        return $input;
    }
    
    $sanitized_input = array();

    if (isset($input['accessiweb_license_key'])) {
        $sanitized_input['accessiweb_license_key'] = sanitize_text_field($input['accessiweb_license_key']);
    }

    if (isset($input['accessiweb_primary_color'])) {
        $sanitized_input['accessiweb_primary_color'] = sanitize_hex_color($input['accessiweb_primary_color']);
    }

    if (isset($input['accessiweb_position_x']) && in_array($input['accessiweb_position_x'], array('left', 'right'), true)) {
        $sanitized_input['accessiweb_position_x'] = sanitize_text_field($input['accessiweb_position_x']);
    }
    
    if (isset($input['accessiweb_position_y']) && in_array($input['accessiweb_position_y'], array('top', 'bottom'), true)) {
        $sanitized_input['accessiweb_position_y'] = sanitize_text_field($input['accessiweb_position_y']);
    }

    if (isset($input['accessiweb_offset_x'])) {
        $sanitized_input['accessiweb_offset_x'] = absint($input['accessiweb_offset_x']);
        if ($sanitized_input['accessiweb_offset_x'] < 0 || $sanitized_input['accessiweb_offset_x'] > 9999) {
            $sanitized_input['accessiweb_offset_x'] = 10; // Default value
        }
    }

    if (isset($input['accessiweb_offset_y'])) {
        $sanitized_input['accessiweb_offset_y'] = absint($input['accessiweb_offset_y']);
        if ($sanitized_input['accessiweb_offset_y'] < 0 || $sanitized_input['accessiweb_offset_y'] > 9999) {
            $sanitized_input['accessiweb_offset_y'] = 10; // Default value
        }
    }

    if (isset($input['accessiweb_unit_x']) && in_array($input['accessiweb_unit_x'], array('px', '%'), true)) {
        $sanitized_input['accessiweb_unit_x'] = sanitize_text_field($input['accessiweb_unit_x']);
    }

    if (isset($input['accessiweb_unit_y']) && in_array($input['accessiweb_unit_y'], array('px', '%'), true)) {
        $sanitized_input['accessiweb_unit_y'] = sanitize_text_field($input['accessiweb_unit_y']);
    }

    if (isset($input['accessiweb_icon_size']) && in_array($input['accessiweb_icon_size'], array('small', 'medium', 'large'), true)) {
        $sanitized_input['accessiweb_icon_size'] = sanitize_text_field($input['accessiweb_icon_size']);
    }
    
    add_settings_error(
        'accessiweb_messages',
        'accessiweb_message',
        'Modifiche salvate.',
        'updated'
    );

    return $sanitized_input;
}

// Callback della sezione
function accessiweb_settings_section_callback() {
    echo 'Configura le impostazioni del widget AccessiWeb qui sotto:';
}

// Funzioni per visualizzare i campi del form
function accessiweb_license_key_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <input type="text" name="accessiweb_widget_settings[accessiweb_license_key]" value="<?php echo esc_attr($options['accessiweb_license_key']); ?>" placeholder="License Key" style="width: 100%;">
    <p class="description">Inserisci la tua License Key AccessiWeb, disponibile all'interno della tua area riservata.</p>
    <?php
}

function accessiweb_primary_color_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <input type="text" name="accessiweb_widget_settings[accessiweb_primary_color]" value="<?php echo esc_attr($options['accessiweb_primary_color']); ?>" placeholder="#0d6efd" style="width: 200px;">
    <p class="description">Inserisci il colore primario del Widget in formato esadecimale (default: #0d6efd).</p>
    <?php
}

function accessiweb_position_x_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <select name="accessiweb_widget_settings[accessiweb_position_x]" style="width:200px">
        <option value="left" <?php selected($options['accessiweb_position_x'], 'left'); ?>>Left</option>
        <option value="right" <?php selected($options['accessiweb_position_x'], 'right'); ?>>Right</option>
    </select>
    <p class="description">Seleziona l'allineamento orizzontale del Widget (default: Left).</p>
    <?php
}

function accessiweb_position_y_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <select name="accessiweb_widget_settings[accessiweb_position_y]" style="width:200px">
        <option value="bottom" <?php selected($options['accessiweb_position_y'], 'bottom'); ?>>Bottom</option>
        <option value="top" <?php selected($options['accessiweb_position_y'], 'top'); ?>>Top</option>
    </select>
    <p class="description">Seleziona l'allineamento verticale del Widget (default: Bottom).</p>
    <?php
}

function accessiweb_offset_x_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <input type="number" name="accessiweb_widget_settings[accessiweb_offset_x]" value="<?php echo esc_attr($options['accessiweb_offset_x']); ?>" min="0" max="9999" style="width:200px" placeholder="10">
    <p class="description">Inserisci la distanza orizzontale del Widget (offsetX) in pixel o percentuale (default: 10).</p>
    <?php
}

function accessiweb_offset_y_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <input type="number" name="accessiweb_widget_settings[accessiweb_offset_y]" value="<?php echo esc_attr($options['accessiweb_offset_y']); ?>" min="0" max="9999" style="width:200px" placeholder="10">
    <p class="description">Inserisci la distanza verticale del Widget (offsetY) in pixel o percentuale (default: 10).</p>
    <?php
}

function accessiweb_unit_x_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <select name="accessiweb_widget_settings[accessiweb_unit_x]" style="width:200px">
        <option value="px" <?php selected($options['accessiweb_unit_x'], 'px'); ?>>px</option>
        <option value="%" <?php selected($options['accessiweb_unit_x'], '%'); ?>>%</option>
    </select>
    <p class="description">Scegli l'unità di misura per la distanza orizzontale (default: px).</p>
    <?php
}

function accessiweb_unit_y_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <select name="accessiweb_widget_settings[accessiweb_unit_y]" style="width:200px">
        <option value="px" <?php selected($options['accessiweb_unit_y'], 'px'); ?>>px</option>
        <option value="%" <?php selected($options['accessiweb_unit_y'], '%'); ?>>%</option>
    </select>
    <p class="description">Scegli l'unità di misura per la distanza verticale (default: px).</p>
    <?php
}

function accessiweb_icon_size_render() {
    $options = get_option('accessiweb_widget_settings');
    ?>
    <select name="accessiweb_widget_settings[accessiweb_icon_size]" style="width:200px">
        <option value="small" <?php selected($options['accessiweb_icon_size'], 'small'); ?>>Small</option>
        <option value="medium" <?php selected($options['accessiweb_icon_size'], 'medium'); ?>>Medium</option>
        <option value="large" <?php selected($options['accessiweb_icon_size'], 'large'); ?>>Large</option>
    </select>
    <p class="description">Seleziona la dimensione dell'icona del Widget (default: Medium).</p>
    <?php
}

// Funzione per aggiungere il JavaScript del widget al footer
function accessiweb_add_widget_script() {
    if (!is_admin()) {
        $options = get_option('accessiweb_widget_settings');
        
        wp_register_script(
            'accessiweb_widget_script',
            'https://www.accessiweb.it/widget/acsw.js',
            array(),
            '1.0.0',
            true
        );
        
        wp_enqueue_script('accessiweb_widget_script');
        
        $inline_script = '
            document.addEventListener("DOMContentLoaded", function(){
                acsw.init({
                    LicenseKey: "' . esc_js($options['accessiweb_license_key']) . '",
                    PrimaryColor:"' . esc_js($options['accessiweb_primary_color']) . '",
                    PositionX:"' . esc_js($options['accessiweb_position_x']) . '",
                    PositionY:"' . esc_js($options['accessiweb_position_y']) . '",
                    OffsetX:' . esc_js($options['accessiweb_offset_x']) . ',
                    OffsetY:' . esc_js($options['accessiweb_offset_y']) . ',
                    UnitX:"' . esc_js($options['accessiweb_unit_x']) . '",
                    UnitY:"' . esc_js($options['accessiweb_unit_y']) . '",
                    IconSize:"' . esc_js($options['accessiweb_icon_size']) . '"
                });
            });
        ';
        
        wp_add_inline_script('accessiweb_widget_script', $inline_script);
    }
}
add_action('wp_footer', 'accessiweb_add_widget_script');