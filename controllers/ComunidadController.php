<?php

declare(strict_types=1);

final class ComunidadController extends Controller
{
    private ?ComunidadService $comunidadService = null;

    public function salir(): void
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
                'La sesión del formulario venció.'
            );

            $this->redirect(
                'index.php?controller=dashboard'
                . '&action=index'
            );
        }

        $usuarioActual = Auth::usuario();

        try {
            $this
                ->obtenerComunidadService()
                ->abandonar(
                    (int) (
                        $usuarioActual[
                            'id_usuario'
                        ] ?? 0
                    ),
                    (int) (
                        $usuarioActual[
                            'id_comunidad'
                        ] ?? 0
                    )
                );
        } catch (
            InvalidArgumentException $error
        ) {
            Auth::flash(
                'error',
                $error->getMessage()
            );

            $this->redirect(
                'index.php?controller=dashboard'
                . '&action=index'
            );
        } catch (Throwable $error) {
            error_log($error->__toString());

            Auth::flash(
                'error',
                'No fue posible salir de la comunidad.'
            );

            $this->redirect(
                'index.php?controller=dashboard'
                . '&action=index'
            );
        }

        Auth::logout();

        $this->redirect(
            'index.php?controller=auth'
            . '&action=login&salida=1'
        );
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