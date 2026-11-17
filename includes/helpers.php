<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Validate/clean an email, falling back to a default. Pure (unit-tested). */
function accessibility_statement_clean_email( $value, $fallback = '' ) {
	$value = is_string( $value ) ? trim( $value ) : '';
	if ( function_exists( 'is_email' ) ) {
		return is_email( $value ) ? $value : $fallback;
	}
	return filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $value : $fallback;
}

/** Clamp the conformance value to an allowed token. Pure (unit-tested). */
function accessibility_statement_clean_conformance( $value ) {
	$allowed = array( 'fully', 'partially', 'not' );
	return in_array( $value, $allowed, true ) ? $value : 'partially';
}

/** Build the leading conformance sentence. Pure (unit-tested). */
function accessibility_statement_conformance_sentence( $conformance, $org ) {
	$org = $org !== '' ? $org : 'This site';
	switch ( $conformance ) {
		case 'fully':
			return sprintf( '%s is fully conformant with WCAG 2.2 level AA.', $org );
		case 'not':
			return sprintf( '%s is not yet conformant with WCAG 2.2 level AA; work is in progress.', $org );
		case 'partially':
		default:
			return sprintf( '%s is partially conformant with WCAG 2.2 level AA — most of the site meets the standard, with known exceptions listed below.', $org );
	}
}
