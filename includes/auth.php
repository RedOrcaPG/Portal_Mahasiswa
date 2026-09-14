<?php
/**
 * Helper autentikasi & proteksi halaman berbasis session
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

/** Panggil di paling atas halaman yang wajib login */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/** Panggil di paling atas halaman admin */
function require_admin(): void
{
    require_login();
    if (current_role() !== 'admin') {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/** Panggil di paling atas halaman mahasiswa */
function require_mahasiswa(): void
{
    require_login();
    if (current_role() !== 'mahasiswa') {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/**
 * Menghasilkan path relatif ke root project, supaya link tetap benar
 * baik diakses dari root, /admin/, maupun /student/
 */
function base_url(string $path = ''): string
{
    // Deteksi apakah script berjalan di dalam folder admin/ atau student/
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $depth  = (strpos($script, '/admin/') !== false || strpos($script, '/student/') !== false) ? '../' : '';
    return $depth . $path;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
