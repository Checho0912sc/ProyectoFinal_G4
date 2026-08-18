<?php

declare(strict_types=1);

final class UsuarioController extends Controller
{
    private ?UsuarioService $usuarioService = null;

    public function index(): void
    {
        Auth::exigirRol('Administrador');

        $this->render(
            'usuarios/index',
            [
                'titulo' => 'Usuarios',
                'scripts' => [
                    'assets/js/usuarios.js',
                ],
            ]
        );
    }

    public function listar(
        int $idComunidad
    ): array {
        return $this
            ->obtenerUsuarioService()
            ->listar($idComunidad);
    }

    public function obtener(
        int $idUsuario,
        int $idComunidad
    ): array {
        return $this
            ->obtenerUsuarioService()
            ->obtener(
                $idUsuario,
                $idComunidad
            );
    }

    public function listarRoles(): array
    {
        return $this
            ->obtenerUsuarioService()
            ->listarRoles();
    }

    public function crear(
        array $datos,
        int $idComunidad
    ): int {
        return $this
            ->obtenerUsuarioService()
            ->crear(
                $datos,
                $idComunidad
            );
    }

    public function actualizar(
        int $idUsuario,
        array $datos,
        int $idComunidad
    ): void {
        $this
            ->obtenerUsuarioService()
            ->actualizar(
                $idUsuario,
                $datos,
                $idComunidad
            );
    }

    public function eliminar(
        int $idUsuario,
        int $idComunidad,
        int $idUsuarioActual
    ): void {
        $this
            ->obtenerUsuarioService()
            ->eliminar(
                $idUsuario,
                $idComunidad,
                $idUsuarioActual
            );
    }

    private function obtenerUsuarioService(): UsuarioService
    {
        if ($this->usuarioService === null) {
            $repositorio =
                new UsuarioRepository(
                    Database::getConnection()
                );

            $this->usuarioService =
                new UsuarioService(
                    $repositorio
                );
        }

        return $this->usuarioService;
    }
}