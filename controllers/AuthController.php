<?php

declare(strict_types=1);

final class AuthController extends Controller
{
    private ?AuthService $authService = null;

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect(
                'index.php?controller=dashboard&action=index'
            );
        }

        $mensaje = isset($_GET['logout'])
            ? 'La sesión se cerró correctamente.'
            : Auth::flash('exito');

        $error = Auth::flash('error');

        Auth::cancelarLoginPendiente();

        $this->render('auth/login', [
            'titulo' => 'Iniciar sesión',
            'correo' => '',
            'errores' => $error === null
                ? []
                : ['general' => $error],
            'mensaje' => $mensaje,
            'mostrarNavegacion' => false,
        ]);
    }

    public function autenticar(): void
    {
        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar sesión',
                'correo' => trim(
                    (string) ($_POST['correo'] ?? '')
                ),
                'errores' => [
                    'general' =>
                        'La sesión del formulario venció. Inténtalo nuevamente.',
                ],
                'mensaje' => null,
                'mostrarNavegacion' => false,
            ], 419);

            return;
        }

        $correo = trim(
            (string) ($_POST['correo'] ?? '')
        );

        $contrasena = (string) (
            $_POST['contrasena'] ?? ''
        );

        try {
            $resultado = $this
                ->obtenerAuthService()
                ->autenticar($correo, $contrasena);
        } catch (Throwable $error) {
            error_log($error->__toString());

            $this->render('auth/login', [
                'titulo' => 'Iniciar sesión',
                'correo' => $correo,
                'errores' => [
                    'general' =>
                        'No fue posible validar la cuenta en este momento.',
                ],
                'mensaje' => null,
                'mostrarNavegacion' => false,
            ], 500);

            return;
        }

        if (!$resultado['exito']) {
            $this->render('auth/login', [
                'titulo' => 'Iniciar sesión',
                'correo' => $correo,
                'errores' => $resultado['errores'],
                'mensaje' => null,
                'mostrarNavegacion' => false,
            ], 422);

            return;
        }

        /** @var Usuario $usuario */
        $usuario = $resultado['usuario'];
        $membresias = $resultado['membresias'];

        if (count($membresias) === 1) {
            $this->completarLogin(
                $usuario->paraSesion(),
                $membresias[0]
            );
        }

        Auth::guardarLoginPendiente(
            $usuario->paraSesion(),
            $membresias
        );

        $this->redirect(
            'index.php?controller=auth'
            . '&action=seleccionarComunidad'
        );
    }

    public function seleccionarComunidad(): void
    {
        if (Auth::check()) {
            $this->redirect(
                'index.php?controller=dashboard&action=index'
            );
        }

        $pendiente = Auth::loginPendiente();

        if ($pendiente === null) {
            Auth::flash(
                'error',
                'El proceso de inicio de sesión venció. Ingresa nuevamente.'
            );

            $this->redirect(
                'index.php?controller=auth&action=login'
            );
        }

        $this->render(
            'auth/seleccionar-comunidad',
            [
                'titulo' => 'Seleccionar comunidad',
                'usuarioPendiente' =>
                    $pendiente['usuario'],
                'membresias' =>
                    $pendiente['membresias'],
                'error' => Auth::flash('error'),
                'mostrarNavegacion' => false,
            ]
        );
    }

    public function confirmarComunidad(): void
    {
        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            Auth::flash(
                'error',
                'La sesión del formulario venció.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=seleccionarComunidad'
            );
        }

        $pendiente = Auth::loginPendiente();

        if ($pendiente === null) {
            Auth::flash(
                'error',
                'El proceso de inicio de sesión venció. Ingresa nuevamente.'
            );

            $this->redirect(
                'index.php?controller=auth&action=login'
            );
        }

        $idComunidad = filter_input(
            INPUT_POST,
            'id_comunidad',
            FILTER_VALIDATE_INT
        );

        $membresiaElegida = null;

        foreach (
            $pendiente['membresias'] as $membresia
        ) {
            if (
                (int) $membresia['id_comunidad']
                === $idComunidad
            ) {
                $membresiaElegida = $membresia;
                break;
            }
        }

        if ($membresiaElegida === null) {
            Auth::flash(
                'error',
                'Selecciona una comunidad válida.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=seleccionarComunidad'
            );
        }

        $this->completarLogin(
            $pendiente['usuario'],
            $membresiaElegida
        );
    }

    public function logout(): void
    {
        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            http_response_code(419);

            exit(
                'La solicitud para cerrar sesión no es válida.'
            );
        }

        Auth::logout();

        $this->redirect(
            'index.php?controller=auth'
            . '&action=login&logout=1'
        );
    }

    private function completarLogin(
        array $usuario,
        array $membresia
    ): never {
        try {
            $this->obtenerAuthService()
                ->registrarUltimoAcceso(
                    (int) $usuario['id_usuario']
                );
        } catch (Throwable $error) {
            error_log($error->__toString());
        }

        Auth::login($usuario, $membresia);

        $this->redirect(
            'index.php?controller=dashboard&action=index'
        );
    }

    private function obtenerAuthService(): AuthService
    {
        if ($this->authService === null) {
            $repositorio = new UsuarioRepository(
                Database::getConnection()
            );

            $this->authService = new AuthService(
                $repositorio
            );
        }

        return $this->authService;
    }
}