<?php

declare(strict_types=1);

final class ActividadController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioActual = Auth::usuario();
        $idComunidad   = (int) $usuarioActual['id_comunidad'];

        $filtroTipo = isset($_GET['tipo'])
            ? (string) $_GET['tipo']
            : null;

        $repositorio = new ActividadRepository(
            Database::getConnection()
        );

        $servicio = new ActividadService($repositorio);

        $modulo = $servicio->obtenerModulo($idComunidad, $filtroTipo);

        $this->render('actividades/index', [
            'titulo'        => 'Actividades',
            'usuarioActual' => $usuarioActual,
            'resumen'       => $modulo['resumen'],
            'listado'       => $modulo['listado'],
            'proximas'      => $modulo['proximas'],
            'proyectos'     => $modulo['proyectos'],
            'filtroTipo'    => $filtroTipo,
        ]);
    }

    public function guardar(): void
    {
        Auth::exigirLogin();
        $this->exigirMetodo('POST');

        if (!Auth::validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Token de seguridad inválido.');
        }

        $usuarioActual = Auth::usuario();
        $idComunidad   = (int) $usuarioActual['id_comunidad'];
        $idResponsable = (int) $usuarioActual['id_usuario'];

        $repositorio = new ActividadRepository(
            Database::getConnection()
        );

        $servicio = new ActividadService($repositorio);

        try {
            $servicio->registrar($idComunidad, $idResponsable, $_POST);

            Auth::flash('exito', 'Actividad registrada correctamente.');
        } catch (InvalidArgumentException $error) {
            Auth::flash('error', $error->getMessage());
        }

        $this->redirect(
            'index.php?controller=actividades&action=index'
        );
    }
}
