<?php

declare(strict_types=1);

final class AuthController extends Controller
{
    private ?AuthService $authService = null;

    private ?ComunidadService $comunidadService = null;

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect(
                'index.php?controller=dashboard&action=index'
            );
        }

        $mensaje = Auth::flash('exito');

        if (isset($_GET['logout'])) {
            $mensaje =
                'La sesión se cerró correctamente.';
        }

        if (isset($_GET['salida'])) {
            $mensaje =
                'Saliste de la comunidad correctamente.';
        }

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

    public function registro(): void
    {
        if (Auth::check()) {
            $this->redirect(
                'index.php?controller=dashboard&action=index'
            );
        }

        Auth::cancelarLoginPendiente();

        $this->render(
            'auth/registro',
            [
                'titulo' => 'Crear cuenta',
                'datos' => [
                    'nombre' => '',
                    'correo' => '',
                    'telefono' => '',
                ],
                'errores' => [],
                'mostrarNavegacion' => false,
            ]
        );
    }

    public function registrar(): void
    {
        $this->exigirMetodo('POST');

        $datosFormulario = [
            'nombre' => trim(
                (string) ($_POST['nombre'] ?? '')
            ),
            'correo' => trim(
                (string) ($_POST['correo'] ?? '')
            ),
            'telefono' => trim(
                (string) ($_POST['telefono'] ?? '')
            ),
        ];

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            $this->render(
                'auth/registro',
                [
                    'titulo' => 'Crear cuenta',
                    'datos' => $datosFormulario,
                    'errores' => [
                        'general' =>
                            'La sesión del formulario venció. Inténtalo nuevamente.',
                    ],
                    'mostrarNavegacion' => false,
                ],
                419
            );

            return;
        }

        try {
            $resultado = $this
                ->obtenerAuthService()
                ->registrarCuenta($_POST);
        } catch (Throwable $error) {
            error_log($error->__toString());

            $this->render(
                'auth/registro',
                [
                    'titulo' => 'Crear cuenta',
                    'datos' => $datosFormulario,
                    'errores' => [
                        'general' =>
                            'No fue posible crear la cuenta en este momento.',
                    ],
                    'mostrarNavegacion' => false,
                ],
                500
            );

            return;
        }

        if (!$resultado['exito']) {
            $this->render(
                'auth/registro',
                [
                    'titulo' => 'Crear cuenta',
                    'datos' => $datosFormulario,
                    'errores' =>
                        $resultado['errores'],
                    'mostrarNavegacion' => false,
                ],
                422
            );

            return;
        }

        Auth::flash(
            'exito',
            'La cuenta fue creada correctamente. Ahora puedes iniciar sesión.'
        );

        $this->redirect(
            'index.php?controller=auth&action=login'
        );
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

        $comunidades = $this
            ->obtenerComunidadService()
            ->listarActivas();

        $this->render(
            'auth/seleccionar-comunidad',
            [
                'titulo' => 'Comunidades',
                'usuarioPendiente' =>
                    $pendiente['usuario'],
                'membresias' =>
                    $pendiente['membresias'],
                'comunidades' =>
                    $comunidades,
                'error' => Auth::flash('error'),
                'mostrarNavegacion' => false,
                'scripts' => [
                    'assets/js/comunidades.js',
                ],
            ]
        );
    }

    public function crearComunidad(): void
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
                'El proceso de inicio de sesión venció.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=login'
            );
        }

        $this->render(
            'auth/crear-comunidad',
            [
                'titulo' => 'Crear comunidad',
                'usuarioPendiente' =>
                    $pendiente['usuario'],
                'datos' => [
                    'nombre' => '',
                    'descripcion' => '',
                ],
                'errores' => [],
                'mostrarNavegacion' => false,
            ]
        );
    }

    public function guardarComunidad(): void
    {
        $this->exigirMetodo('POST');

        $pendiente = Auth::loginPendiente();

        if ($pendiente === null) {
            Auth::flash(
                'error',
                'El proceso de inicio de sesión venció.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=login'
            );
        }

        $datosFormulario = [
            'nombre' => trim(
                (string) (
                    $_POST['nombre'] ?? ''
                )
            ),
            'descripcion' => trim(
                (string) (
                    $_POST['descripcion'] ?? ''
                )
            ),
        ];

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            $this->render(
                'auth/crear-comunidad',
                [
                    'titulo' => 'Crear comunidad',
                    'usuarioPendiente' =>
                        $pendiente['usuario'],
                    'datos' =>
                        $datosFormulario,
                    'errores' => [
                        'general' =>
                            'La sesión del formulario venció.',
                    ],
                    'mostrarNavegacion' => false,
                ],
                419
            );

            return;
        }

        try {
            $membresia = $this
                ->obtenerComunidadService()
                ->crear(
                    (int) $pendiente[
                        'usuario'
                    ]['id_usuario'],
                    $datosFormulario
                );
        } catch (
            InvalidArgumentException $error
        ) {
            $this->render(
                'auth/crear-comunidad',
                [
                    'titulo' => 'Crear comunidad',
                    'usuarioPendiente' =>
                        $pendiente['usuario'],
                    'datos' =>
                        $datosFormulario,
                    'errores' => [
                        'general' =>
                            $error->getMessage(),
                    ],
                    'mostrarNavegacion' => false,
                ],
                422
            );

            return;
        } catch (Throwable $error) {
            error_log($error->__toString());

            $this->render(
                'auth/crear-comunidad',
                [
                    'titulo' => 'Crear comunidad',
                    'usuarioPendiente' =>
                        $pendiente['usuario'],
                    'datos' =>
                        $datosFormulario,
                    'errores' => [
                        'general' =>
                            'No fue posible crear la comunidad.',
                    ],
                    'mostrarNavegacion' => false,
                ],
                500
            );

            return;
        }

        $this->completarLogin(
            $pendiente['usuario'],
            $membresia
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

    public function unirseComunidad(): void
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
                'El proceso de inicio de sesión venció.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=login'
            );
        }

        $idComunidad = filter_input(
            INPUT_POST,
            'id_comunidad',
            FILTER_VALIDATE_INT
        );

        if (
            $idComunidad === false
            || $idComunidad === null
        ) {
            Auth::flash(
                'error',
                'Selecciona una comunidad válida.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=seleccionarComunidad'
            );
        }

        try {
            $membresia = $this
                ->obtenerComunidadService()
                ->unirse(
                    (int) $pendiente[
                        'usuario'
                    ]['id_usuario'],
                    (int) $idComunidad
                );
        } catch (
            InvalidArgumentException
            | RuntimeException $error
        ) {
            Auth::flash(
                'error',
                $error->getMessage()
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=seleccionarComunidad'
            );
        } catch (Throwable $error) {
            error_log($error->__toString());

            Auth::flash(
                'error',
                'No fue posible unirse a la comunidad.'
            );

            $this->redirect(
                'index.php?controller=auth'
                . '&action=seleccionarComunidad'
            );
        }

        $this->completarLogin(
            $pendiente['usuario'],
            $membresia
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

    private function obtenerComunidadService(): ComunidadService
    {
        if ($this->comunidadService === null) {
            $repositorio =
                new ComunidadRepository(
                    Database::getConnection()
                );

            $this->comunidadService =
                new ComunidadService(
                    $repositorio
                );
        }

        return $this->comunidadService;
    }
}