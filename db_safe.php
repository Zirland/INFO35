<?php
/**
 * Bezpečné databázové operace s prepared statements
 */

/**
 * Bezpečný SELECT s prepared statement
 */
function dbSelect($link, $query, $types, $params) {
    $stmt = $link->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    return $stmt->get_result();
}

/**
 * Bezpečný INSERT/UPDATE/DELETE s prepared statement
 */
function dbExecute($link, $query, $types, $params) {
    $stmt = $link->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $link->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    return $stmt->affected_rows;
}

/**
 * Bezpečné escapování stringu (fallback, pokud prepared statement není možný)
 */
function dbEscape($link, $string) {
    return $link->real_escape_string($string);
}
?>
