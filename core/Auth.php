<?php

declare(strict_types=1);

final class Auth // se va a encargar de toda la autenticacion
{
    private const DURACION_LOGIN_PENDIENTE = 300;

    private function __construct()
    {
    }

    public static function iniciarSesion(): void //Se inicializa la sesion
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_name('comunigest_session');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => self::usaHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function login( //Se validan los datos y se hace el login
        array $usuario,
        array $membresia
    ): void {
        session_regenerate_id(true);

        $_SESSION['usuario'] = [ //Datos capturados para el login
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre' => (string) $usuario['nombre'],
            'correo' => (string) $usuario['correo'],

            'id_comunidad' => (int) $membresia['id_comunidad'],
            'comunidad' => (string) $membresia['comunidad'],

            'id_rol' => (int) $membresia['id_rol'],
            'rol' => (string) $membresia['rol'],
        ];

        unset(
            $_SESSION['login_pendiente'],
            $_SESSION['csrf_token']
        );
    }

    public static function guardarLoginPendiente(
        array $usuario,
        array $membresias
    ): void {
        session_regenerate_id(true);

        $_SESSION['login_pendiente'] = [
            'usuario' => $usuario,
            'membresias' => $membresias,
            'creado_en' => time(),
        ];
    }

    public static function loginPendiente(): ?array
    {
        $pendiente = $_SESSION['login_pendiente'] ?? null;

        if (!is_array($pendiente)) {
            return null;
        }

        $creadoEn = (int) ($pendiente['creado_en'] ?? 0);

        if (
            time() - $creadoEn
            > self::DURACION_LOGIN_PENDIENTE
        ) {
            unset($_SESSION['login_pendiente']);
            return null;
        }

        return $pendiente;
    }

    public static function cancelarLoginPendiente(): void
    {
        unset($_SESSION['login_pendiente']);
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $parametros['path'],
                'domain' => $parametros['domain'],
                'secure' => $parametros['secure'],
                'httponly' => $parametros['httponly'],
                'samesite' => 'Lax',
            ]);
        }

        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['usuario']['id_usuario']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function tieneRol(string ...$roles): bool
    {
        $usuario = self::usuario();

        return $usuario !== null
            && in_array($usuario['rol'], $roles, true);
    }

    public static function exigirLogin(): void
    {
        if (self::check()) {
            return;
        }

        self::flash(
            'error',
            'Debes iniciar sesión para acceder a esa sección.'
        );

        header(
            'Location: '
            . url('index.php?controller=auth&action=login')
        );

        exit;
    }

    public static function exigirRol(string ...$roles): void
    {
        self::exigirLogin();

        if (self::tieneRol(...$roles)) {
            return;
        }

        http_response_code(403);
        exit('No tienes permisos para realizar esta acción.');
    }

    public static function csrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return $_SESSION['csrf_token'];
    }

    public static function validarCsrf(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function flash(
        string $clave,
        ?string $mensaje = null
    ): ?string {
        if ($mensaje !== null) {
            $_SESSION['flash'][$clave] = $mensaje;
            return null;
        }

        $valor = $_SESSION['flash'][$clave] ?? null;

        unset($_SESSION['flash'][$clave]);

        return $valor;
    }

    private static function usaHttps(): bool
    {
        return !empty($_SERVER['HTTPS'])
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    }
}