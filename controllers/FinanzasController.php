<?php

declare(strict_types=1);

final class FinanzasController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioActual = Auth::usuario();

        $idComunidad =
            (int) $usuarioActual['id_comunidad'];

        $repository = new MovimientoFinancieroRepository(
            Database::getConnection()
        );

        $service = new FinanzasService(
            $repository
        );

        $datos = $service->obtenerDatos(
            $idComunidad
        );

        $this->render(
            'finanzas/index',
            [
                'titulo' => 'Finanzas',
                'usuarioActual' => $usuarioActual,
                'resumen' => $datos['resumen'],
                'movimientos' => $datos['movimientos'],
                'proyectos' => $datos['proyectos'],
                'mensajeExito' => Auth::flash('success'),
                'mensajeError' => Auth::flash('error'),
            ]
        );
    }

    public function guardar(): void
    {
        Auth::exigirRol(
            'Administrador',
            'Coordinador'
        );

        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            http_response_code(403);
            exit('Token CSRF inválido.');
        }

        $usuarioActual = Auth::usuario();

        $idComunidad =
            (int) $usuarioActual['id_comunidad'];

        $idUsuario =
            (int) $usuarioActual['id_usuario'];

        $idProyecto = null;

        if (
            isset($_POST['id_proyecto'])
            && $_POST['id_proyecto'] !== ''
        ) {
            $idProyecto =
                (int) $_POST['id_proyecto'];
        }

        $tipo =
            (string) ($_POST['tipo'] ?? '');

        $descripcion =
            (string) ($_POST['descripcion'] ?? '');

        $montoTexto =
            str_replace(
                ',',
                '.',
                (string) ($_POST['monto'] ?? '')
            );

        $monto =
            is_numeric($montoTexto)
                ? (float) $montoTexto
                : 0;

        $fecha =
            (string) ($_POST['fecha'] ?? '');

        $repository =
            new MovimientoFinancieroRepository(
                Database::getConnection()
            );

        $service =
            new FinanzasService($repository);

        try {

            $service->registrar(
                $idComunidad,
                $idUsuario,
                $idProyecto,
                $tipo,
                $descripcion,
                $monto,
                $fecha
            );

            Auth::flash(
                'success',
                'Movimiento registrado correctamente.'
            );

        } catch (Throwable $error) {

            Auth::flash(
                'error',
                $error->getMessage()
            );
        }

        $this->redirect(
            'index.php?controller=finanzas&action=index'
        );
    }

    public function anular(): void
    {
        Auth::exigirRol(
            'Administrador',
            'Coordinador'
        );

        $this->exigirMetodo('POST');

        if (
            !Auth::validarCsrf(
                $_POST['csrf_token'] ?? null
            )
        ) {
            http_response_code(403);
            exit('Token CSRF inválido.');
        }

        $usuarioActual = Auth::usuario();

        $idComunidad =
            (int) $usuarioActual['id_comunidad'];

        $idMovimiento =
            (int) ($_POST['id_movimiento'] ?? 0);

        $repository =
            new MovimientoFinancieroRepository(
                Database::getConnection()
            );

        $service =
            new FinanzasService($repository);

        try {

            $service->anular(
                $idMovimiento,
                $idComunidad
            );

            Auth::flash(
                'success',
                'Movimiento anulado correctamente.'
            );

        } catch (Throwable $error) {

            Auth::flash(
                'error',
                $error->getMessage()
            );
        }

        $this->redirect(
            'index.php?controller=finanzas&action=index'
        );
    }
}