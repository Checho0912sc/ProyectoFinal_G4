<?php

declare(strict_types=1);

final class ProyectoController extends Controller
{
    private ?ProyectoService $proyectoService = null;

    public function index(): void
    {
        Auth::exigirLogin();

        $this->render(
            'proyectos/index',
            [
                'titulo' => 'Proyectos',
                'scripts' => [
                    'assets/js/proyectos.js',
                ],
            ]
        );
    }

    public function listar(
        int $idComunidad
    ): array {
        return $this
            ->obtenerProyectoService()
            ->listar($idComunidad);
    }

    public function obtener(
        int $idProyecto,
        int $idComunidad
    ): array {
        return $this
            ->obtenerProyectoService()
            ->obtener(
                $idProyecto,
                $idComunidad
            );
    }

    public function listarGrupos(
        int $idComunidad
    ): array {
        return $this
            ->obtenerProyectoService()
            ->listarGrupos($idComunidad);
    }

    public function listarResponsables(
        int $idComunidad
    ): array {
        return $this
            ->obtenerProyectoService()
            ->listarResponsables(
                $idComunidad
            );
    }

    public function crear(
        array $datos,
        int $idComunidad
    ): int {
        return $this
            ->obtenerProyectoService()
            ->crear(
                $datos,
                $idComunidad
            );
    }

    public function actualizar(
        int $idProyecto,
        array $datos,
        int $idComunidad
    ): void {
        $this
            ->obtenerProyectoService()
            ->actualizar(
                $idProyecto,
                $datos,
                $idComunidad
            );
    }

    public function eliminar(
        int $idProyecto,
        int $idComunidad
    ): void {
        $this
            ->obtenerProyectoService()
            ->eliminar(
                $idProyecto,
                $idComunidad
            );
    }

    private function obtenerProyectoService(): ProyectoService
    {
        if ($this->proyectoService === null) {
            $repositorio =
                new ProyectoRepository(
                    Database::getConnection()
                );

            $this->proyectoService =
                new ProyectoService(
                    $repositorio
                );
        }

        return $this->proyectoService;
    }
}