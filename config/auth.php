<?php
// ============================================================
// RBAC Auth Middleware
// Usage: require_auth(); or require_permission('members','can_edit');
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function require_auth(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
    // Enforce session timeout (2 hours)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
        session_destroy();
        header('Location: /admin/login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function get_permissions(int $roleId): array {
    $db = getDB();
    $stmt = $db->prepare('SELECT section_key, can_view, can_edit, can_delete FROM role_permissions WHERE role_id = ?');
    $stmt->execute([$roleId]);
    $perms = [];
    foreach ($stmt->fetchAll() as $row) {
        $perms[$row['section_key']] = [
            'view'   => (bool)$row['can_view'],
            'edit'   => (bool)$row['can_edit'],
            'delete' => (bool)$row['can_delete'],
        ];
    }
    return $perms;
}

function can(string $section, string $action = 'view'): bool {
    if (empty($_SESSION['permissions'])) return false;
    return !empty($_SESSION['permissions'][$section][$action]);
}

function require_permission(string $section, string $action = 'view'): void {
    require_auth();
    if (!can($section, $action)) {
        http_response_code(403);
        include __DIR__ . '/../admin/403.php';
        exit;
    }
}

function is_super_admin(): bool {
    return ($_SESSION['role_key'] ?? '') === 'super_admin';
}

function admin_display_name(): string {
    return htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
}

function admin_role_label(): string {
    return htmlspecialchars($_SESSION['role_display'] ?? '');
}
