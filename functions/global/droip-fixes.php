<?php

add_action('wp_head', 'droip_fix_variables', 999);
function droip_fix_variables() {
  if (is_admin()) return;

  global $wpdb;

  $raw_meta_value = $wpdb->get_var(
    "SELECT meta_value
     FROM {$wpdb->postmeta}
     WHERE meta_key = 'droip_user_saved_data'
     ORDER BY meta_id DESC
     LIMIT 1"
  );

  if (!$raw_meta_value) return;

  $saved = maybe_unserialize($raw_meta_value);
  if (!is_array($saved)) return;

  $groups = $saved['variableData']['data'] ?? null;
  if (!is_array($groups)) return;

  $decl = '';

  foreach ($groups as $g) {
    $vars = $g['variables'] ?? null;
    if (!is_array($vars)) continue;

    foreach ($vars as $v) {
      $id    = $v['id']    ?? null;
      $type  = $v['type']  ?? null;
      $value = $v['value'] ?? null;

      if (!is_string($id) || $id === '') continue;
      if (!is_array($value)) continue;

      $raw = $value['default'] ?? null;
      if ($raw === null) continue;

      $raw = is_string($raw) ? trim($raw) : (string)$raw;
      if ($raw === '') continue;

      if ($type === 'font-family') {
        $raw = trim($raw, "\"'");
        $raw = "'" . $raw . "'";
      }

      $raw = preg_replace('/\s+/', ' ', str_replace(["\n", "\r", "\t"], ' ', $raw));

      $decl .= '--' . $id . ':' . $raw . ';';
    }
  }

  if ($decl === '') return;

  echo '<style id="droip-variables-global-2">:root{' . $decl . '}</style>' . "\n";
}