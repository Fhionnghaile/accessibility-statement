=== Accessibility Statement ===
Contributors: fhionnghaile
Tags: accessibility, a11y, wcag, statement, compliance
Requires at least: 6.2
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An honest, WCAG 2.2 AA accessibility statement via a shortcode, with a configurable organisation name, contact e-mail and conformance level.

== Description ==

Publishes a clear accessibility statement at `/accessibility` (auto-created) using the
`[accessibility_statement]` shortcode. The organisation name, contact e-mail and WCAG 2.2
conformance level are set under **Settings → Accessibility Statement** (Settings API,
sanitised and capability-checked). The review date updates automatically.

Pair it with an accessibility toolbar (such as the companion "Accessible Nav" plugin) for
a complete, honest accessibility story.

== Installation ==

1. Upload the `accessibility-statement` folder to `/wp-content/plugins/` and activate.
2. Set your details under **Settings → Accessibility Statement**.
3. Visit `/accessibility` (created automatically) or drop `[accessibility_statement]` on any page.

== Changelog ==

= 1.0.0 =
* Initial public release. Distilled and generalised from a production module.
