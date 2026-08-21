<?php

declare(strict_types=1);

final class PerfilController extends Controller
{
    private ?PerfilService $perfilService = null;


    // ------------------ MOSTRAR MI PERFIL ------------------

    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioSesion = Auth::usuario();

        $idUsuario = (int) (
            $usuarioSesion['id_usuario'] ?? 0
        );

        $perfil = $this
            ->obtenerPerfilService()
            ->obtener($idUsuario);

        $this->render(
            'perfil/index',
            [
                'titulo' => 'Mi perfil',
                'perfil' => $perfil,
                'mensaje' =>
                    Auth::flash('exito'),
                'error' =>
                    Auth::flash('error'),
            ]
        );
    }


    // ------------------ GUARDAR MI PERFIL (Primero valida la solicitud y luego actualiza) ------------------

    public function guardar(): void
    {
        Auth::exigirLogin();

        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            Auth::flash(
                'error',
                'La sesión del formulario venció. Inténtalo nuevamente.'
            );

            $this->redirect(
                'index.php?controller=perfil'
                . '&action=index'
            );
        }

        $usuarioSesion = Auth::usuario();

        $idUsuario = (int) (
            $usuarioSesion['id_usuario'] ?? 0
        );

        try {
            $perfilActualizado = $this
                ->obtenerPerfilService()
                ->actualizar(
                    $idUsuario,
                    $_POST
                );

            /*
              También actualizamos nombre y correo
              en la sesión para que el navbar cambie
              sin tener que cerrar sesión.
             */
            $_SESSION['usuario']['nombre'] =
                $perfilActualizado['nombre'];

            $_SESSION['usuario']['correo'] =
                $perfilActualizado['correo'];

            Auth::flash(
                'exito',
                'El perfil se actualizó correctamente.'
            );
        } catch (InvalidArgumentException $error) {
            Auth::flash(
                'error',
                $error->getMessage()
            );
        }

        $this->redirect(
            'index.php?controller=perfil'
            . '&action=index'
        );
    }


    private function obtenerPerfilService(): PerfilService
    {
        if ($this->perfilService === null) {
            $repositorio =
                new UsuarioRepository(
                    Database::getConnection()
                );

            $this->perfilService =
                new PerfilService(
                    $repositorio
                );
        }

        return $this->perfilService;
    }
}