<?php
const REMEMBER_COOKIE_NAME = 'simpus_remember';
const REMEMBER_DURATION = 2592000;

function remember_cookie_options(int $expires): array
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $projectPath = dirname(dirname($scriptName));
    $path = $projectPath === '/' || $projectPath === '.' ? '/' : rtrim($projectPath, '/') . '/';

    return [
        'expires' => $expires,
        'path' => $path,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function set_remember_cookie(string $value, int $expires): void
{
    if (!setcookie(REMEMBER_COOKIE_NAME, $value, remember_cookie_options($expires))) {
        throw new RuntimeException('Cookie "Ingat Saya" tidak dapat disimpan.');
    }
    $_COOKIE[REMEMBER_COOKIE_NAME] = $value;
}

function clear_remember_cookie(): void
{
    if (!setcookie(REMEMBER_COOKIE_NAME, '', remember_cookie_options(time() - 3600))) {
        throw new RuntimeException('Cookie "Ingat Saya" tidak dapat dihapus.');
    }
    unset($_COOKIE[REMEMBER_COOKIE_NAME]);
}

function remember_token_selector(?string $cookie): ?string
{
    if ($cookie === null || !preg_match('/^([a-f0-9]{32}):([a-f0-9]{64})$/', $cookie, $matches)) {
        return null;
    }

    return $matches[1];
}

function insert_remember_token(PDO $pdo, int $userId): array
{
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));
    $expires = time() + REMEMBER_DURATION;

    $stmt = $pdo->prepare(
        'INSERT INTO remember_tokens (selector, user_id, token_hash, expires_at)
         VALUES (:selector, :user_id, :token_hash, to_timestamp(:expires_at))'
    );
    $stmt->execute([
        'selector' => $selector,
        'user_id' => $userId,
        'token_hash' => hash('sha256', $validator),
        'expires_at' => $expires,
    ]);

    return [
        'cookie' => $selector . ':' . $validator,
        'expires' => $expires,
    ];
}

function create_remember_token(PDO $pdo, int $userId): void
{
    $token = insert_remember_token($pdo, $userId);

    try {
        set_remember_cookie($token['cookie'], $token['expires']);
    } catch (Throwable $e) {
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE selector = :selector');
        $stmt->execute(['selector' => substr($token['cookie'], 0, 32)]);
        throw $e;
    }
}

function revoke_remember_token(): void
{
    $cookie = $_COOKIE[REMEMBER_COOKIE_NAME] ?? null;
    $selector = remember_token_selector($cookie);

    if ($selector !== null) {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $pdo = $GLOBALS['pdo'];
        } else {
            require __DIR__ . '/koneksi.php';
        }

        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE selector = :selector');
        $stmt->execute(['selector' => $selector]);
    }

    clear_remember_cookie();
}

function set_authenticated_user(array $user): void
{
    session_regenerate_id(true);

    $id = (int) $user['id'];
    $name = (string) $user['nama'];
    $role = (string) ($user['role'] ?? 'petugas');

    $_SESSION['user_id'] = $id;
    $_SESSION['nama'] = $name;
    $_SESSION['role'] = $role;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $role;
    $_SESSION['petugas'] = [
        'id' => $id,
        'username' => (string) $user['username'],
        'nama' => $name,
        'role' => $role,
    ];
}

function restore_remembered_login(): void
{
    if (isset($_SESSION['user_id'])) {
        return;
    }

    $cookie = $_COOKIE[REMEMBER_COOKIE_NAME] ?? null;
    if ($cookie !== null && remember_token_selector($cookie) === null) {
        clear_remember_cookie();
        return;
    }

    $selector = remember_token_selector($cookie);
    if ($selector === null) {
        return;
    }

    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
        $pdo = $GLOBALS['pdo'];
    } else {
        require __DIR__ . '/koneksi.php';
    }

    $stmt = $pdo->prepare(
        'SELECT t.user_id, t.token_hash, t.expires_at, u.nama, u.username, u.role
         FROM remember_tokens t
         JOIN users u ON u.id = t.user_id
         WHERE t.selector = :selector AND t.expires_at > NOW()
         LIMIT 1'
    );
    $stmt->execute(['selector' => $selector]);
    $rememberedUser = $stmt->fetch(PDO::FETCH_ASSOC);
    $validator = substr($cookie, 33);

    if (!$rememberedUser) {
        $delete = $pdo->prepare('DELETE FROM remember_tokens WHERE selector = :selector');
        $delete->execute(['selector' => $selector]);
        clear_remember_cookie();
        return;
    }

    if (!hash_equals($rememberedUser['token_hash'], hash('sha256', $validator))) {
        clear_remember_cookie();
        return;
    }

    $pdo->beginTransaction();
    try {
        $newToken = insert_remember_token($pdo, (int) $rememberedUser['user_id']);
        $delete = $pdo->prepare('DELETE FROM remember_tokens WHERE selector = :selector');
        $delete->execute(['selector' => $selector]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    set_authenticated_user([
        'id' => $rememberedUser['user_id'],
        'nama' => $rememberedUser['nama'],
        'username' => $rememberedUser['username'],
        'role' => $rememberedUser['role'],
    ]);
    set_remember_cookie($newToken['cookie'], $newToken['expires']);
}
