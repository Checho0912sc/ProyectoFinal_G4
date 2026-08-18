<?php

declare(strict_types=1);

final class GrupoController extends Controller
{
    public function index(): void
    {
        Auth::exigirLogin();

        $usuarioActual = Auth::usuario();
        $idComunidad   = (int) $usuarioActual['id_comunidad'];

        $repositorio = new GrupoRepository(
            Database::getConnection()
        );

        $servicio = new GrupoService($repositorio);

        $modulo = $servicio->obtenerModulo($idComunidad);

        $this->render('grupos/index', [
            'titulo'        => 'Grupos de trabajo',
            'usuarioActual' => $usuarioActual,
            'resumen'       => $modulo['resumen'],
            'grupos'        => $modulo['grupos'],
            'coordinadores' => $modulo['coordinadores'],
            'tareas'        => $modulo['tareas'],
            'miembros'      => $modulo['miembros'],
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

        $repositorio = new GrupoRepository(
            Database::getConnection()
        );

        $servicio = new GrupoService($repositorio);

        try {
            $servicio->registrarGrupo($idComunidad, $_POST);

            Auth::flash('exito', 'Comité creado correctamente.');
        } catch (InvalidArgumentException $error) {
            Auth::flash('error', $error->getMessage());
        }

        $this->redirect(
            'index.php?controller=grupos&action=index'
        );
    }

    public function asociar(): void
    {
        Auth::exigirLogin();
        $this->exigirMetodo('POST');

        if (!Auth::validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Token de seguridad inválido.');
        }

        $repositorio = new GrupoRepository(
            Database::getConnection()
        );

        $servicio = new GrupoService($repositorio);

        try {
            $servicio->asociarMiembro($_POST);

            Auth::flash('exito', 'Miembro asociado al comité.');
        } catch (InvalidArgumentException $error) {
            Auth::flash('error', $error->getMessage());
        }

        $this->redirect(
            'index.php?controller=grupos&action=index'
        );
    }
}
