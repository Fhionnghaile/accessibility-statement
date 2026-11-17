<?php
define( 'ABSPATH', __DIR__ );
require __DIR__ . '/../includes/helpers.php';
$fail = 0; $n = 0;
function check( $name, $cond ) { global $fail, $n; $n++; echo ( $cond ? "  ok  " : "  FAIL" ) . " $name\n"; if ( ! $cond ) $GLOBALS['fail']++; }

check( 'valid email kept', accessibility_statement_clean_email( 'a@b.com' ) === 'a@b.com' );
check( 'invalid email -> fallback', accessibility_statement_clean_email( 'nope', 'x@y.z' ) === 'x@y.z' );
check( 'conformance valid kept', accessibility_statement_clean_conformance( 'fully' ) === 'fully' );
check( 'conformance invalid -> partially', accessibility_statement_clean_conformance( 'bogus' ) === 'partially' );
check( 'sentence fully', strpos( accessibility_statement_conformance_sentence( 'fully', 'Acme' ), 'fully conformant' ) !== false );
check( 'sentence partially default', strpos( accessibility_statement_conformance_sentence( 'x', 'Acme' ), 'partially conformant' ) !== false );
check( 'sentence uses org name', strpos( accessibility_statement_conformance_sentence( 'fully', 'Acme' ), 'Acme' ) === 0 );
check( 'sentence empty org -> This site', strpos( accessibility_statement_conformance_sentence( 'fully', '' ), 'This site' ) === 0 );

echo "\n$n checks, $fail failed\n";
exit( $fail ? 1 : 0 );
