<?php
/**
 * XSS (Cross-Site Scripting) Protection Helpers
 */

/**
 * Bezpečný výstup textu - prevence XSS
 */
function xss($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Bezpečný výstup do HTML atributu
 */
function xss_attr($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Bezpečný výstup do JavaScript kontextu
 */
function xss_js($value) {
    return json_encode($value);
}

/**
 * Bezpečný výstup URL
 */
function xss_url($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
