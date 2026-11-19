<?php
/**
 * Plugin Name:       Accessibility Statement
 * Plugin URI:        https://github.com/fhionnghaile/accessibility-statement
 * Description:       Adds an honest, WCAG 2.2 AA accessibility statement via the [accessibility_statement] shortcode and auto-creates an /accessibility page. Organisation name, contact e-mail and review date are configurable.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Pádraig Fhionnghaile
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       accessibility-statement
 *
 * Distilled and generalised from a production module for public release.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/includes/helpers.php';

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'accessibility-statement', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

/* ---- settings (Settings API: nonce + capability handled by core) ---- */
const ACCESSIBILITY_STATEMENT_OPTION = 'accessibility_statement_options';

function accessibility_statement_defaults() {
	return array(
		'org_name'      => get_bloginfo( 'name' ),
		'contact_email' => get_option( 'admin_email' ),
		'conformance'   => 'partially', // 'fully' | 'partially' | 'not'
	);
}

function accessibility_statement_get( $key ) {
	$opts = wp_parse_args( get_option( ACCESSIBILITY_STATEMENT_OPTION, array() ), accessibility_statement_defaults() );
	return $opts[ $key ] ?? '';
}

add_action( 'admin_init', function () {
	register_setting( 'accessibility_statement', ACCESSIBILITY_STATEMENT_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'accessibility_statement_sanitize',
		'default'           => accessibility_statement_defaults(),
	) );
	add_settings_section( 'as_main', '', '__return_false', 'accessibility-statement' );
	add_settings_field( 'org_name', __( 'Organisation name', 'accessibility-statement' ),
		'accessibility_statement_field_text', 'accessibility-statement', 'as_main', array( 'key' => 'org_name' ) );
	add_settings_field( 'contact_email', __( 'Contact e-mail', 'accessibility-statement' ),
		'accessibility_statement_field_text', 'accessibility-statement', 'as_main', array( 'key' => 'contact_email' ) );
	add_settings_field( 'conformance', __( 'WCAG 2.2 AA conformance', 'accessibility-statement' ),
		'accessibility_statement_field_conformance', 'accessibility-statement', 'as_main' );
} );

add_action( 'admin_menu', function () {
	add_options_page(
		__( 'Accessibility Statement', 'accessibility-statement' ),
		__( 'Accessibility Statement', 'accessibility-statement' ),
		'manage_options', 'accessibility-statement', 'accessibility_statement_render_settings'
	);
} );

function accessibility_statement_sanitize( $input ) {
	$d = accessibility_statement_defaults();
	return array(
		'org_name'      => isset( $input['org_name'] ) ? sanitize_text_field( $input['org_name'] ) : $d['org_name'],
		'contact_email' => accessibility_statement_clean_email( $input['contact_email'] ?? '', $d['contact_email'] ),
		'conformance'   => accessibility_statement_clean_conformance( $input['conformance'] ?? '' ),
	);
}

function accessibility_statement_field_text( $args ) {
	$key = $args['key'];
	printf( '<input type="text" name="%s[%s]" value="%s" class="regular-text">',
		esc_attr( ACCESSIBILITY_STATEMENT_OPTION ), esc_attr( $key ),
		esc_attr( accessibility_statement_get( $key ) ) );
}

function accessibility_statement_field_conformance() {
	$cur = accessibility_statement_get( 'conformance' );
	$opts = array(
		'fully'     => __( 'Fully conformant', 'accessibility-statement' ),
		'partially' => __( 'Partially conformant', 'accessibility-statement' ),
		'not'       => __( 'Not conformant', 'accessibility-statement' ),
	);
	echo '<select name="' . esc_attr( ACCESSIBILITY_STATEMENT_OPTION ) . '[conformance]">';
	foreach ( $opts as $v => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $v ), selected( $cur, $v, false ), esc_html( $label ) );
	}
	echo '</select>';
}

function accessibility_statement_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	echo '<div class="wrap"><h1>' . esc_html__( 'Accessibility Statement', 'accessibility-statement' ) . '</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'accessibility_statement' );
	do_settings_sections( 'accessibility-statement' );
	submit_button();
	echo '</form></div>';
}

/* ---- auto-create the /accessibility page (idempotent) ---- */
add_action( 'admin_init', function () {
	if ( get_page_by_path( 'accessibility' ) ) { return; }
	wp_insert_post( array(
		'post_title'   => __( 'Accessibility Statement', 'accessibility-statement' ),
		'post_name'    => 'accessibility',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => '[accessibility_statement]',
	) );
} );

/* ---- the shortcode ---- */
add_shortcode( 'accessibility_statement', function () {
	$org   = accessibility_statement_get( 'org_name' );
	$email = accessibility_statement_get( 'contact_email' );
	$conf  = accessibility_statement_get( 'conformance' );
	$date  = function_exists( 'date_i18n' ) ? date_i18n( 'F Y' ) : date( 'F Y' );

	$conf_sentence = accessibility_statement_conformance_sentence( $conf, $org );

	ob_start();
	?>
	<section class="accessibility-statement" aria-label="<?php esc_attr_e( 'Accessibility statement', 'accessibility-statement' ); ?>">
		<h1><?php esc_html_e( 'Accessibility statement', 'accessibility-statement' ); ?></h1>
		<p><?php echo esc_html( $conf_sentence ); ?></p>

		<h2><?php esc_html_e( 'What we do', 'accessibility-statement' ); ?></h2>
		<ul>
			<li><?php esc_html_e( 'Semantic landmarks, skip links, and visible keyboard focus.', 'accessibility-statement' ); ?></li>
			<li><?php esc_html_e( 'Text-size, contrast, reduced-motion and readable-font controls.', 'accessibility-statement' ); ?></li>
			<li><?php esc_html_e( 'Respect for the browser’s prefers-reduced-motion and colour-scheme settings.', 'accessibility-statement' ); ?></li>
			<li><?php esc_html_e( 'Keyboard-operable navigation, including a command palette.', 'accessibility-statement' ); ?></li>
		</ul>

		<h2><?php esc_html_e( 'Known limitations', 'accessibility-statement' ); ?></h2>
		<p><?php esc_html_e( 'Some third-party embeds may not fully meet WCAG 2.2 AA. We are working to reduce these.', 'accessibility-statement' ); ?></p>

		<h2><?php esc_html_e( 'Contact us', 'accessibility-statement' ); ?></h2>
		<p>
			<?php esc_html_e( 'If you encounter a barrier, please tell us:', 'accessibility-statement' ); ?>
			<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>.
		</p>
		<p><em><?php printf( esc_html__( 'This statement was last reviewed in %s.', 'accessibility-statement' ), esc_html( $date ) ); ?></em></p>
	</section>
	<?php
	return ob_get_clean();
} );
