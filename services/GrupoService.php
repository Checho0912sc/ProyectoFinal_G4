<?php

declare(strict_types=1);

final class GrupoService
{
    public function __construct(
        private readonly GrupoRepository $grupoRepository
    ) {
    }

    public function obtenerModulo(int $idComunidad): array
    {
        return [
            'resumen' => [
                'comites' => $this->grupoRepository->contarActivosPorComunidad($idComunidad),
                'miembros' => $this->grupoRepository->contarMiembrosActivos($idComunidad),
                'tareas' => $this->grupoRepository->contarTareasEnCurso($idComunidad),
                'proyectos' => $this->grupoRepository->contarProyectosApoyados($idComunidad),
            ],

            'grupos' => $this->grupoRepository->listarPorComunidad($idComunidad),

            'coordinadores' => $this->grupoRepository->listarCoordinadores($idComunidad),

            'tareas' => $this->grupoRepository->listarTareasRecientes($idComunidad),

            'miembros' => $this->grupoRepository->listarMiembrosDeComunidad($idComunidad),
        ];
    }

    public function registrarGrupo(
        int $idComunidad,
        array $post
    ): void {
        $nombre = trim((string) ($post['nombre'] ?? ''));
        $area = trim((string) ($post['area'] ?? ''));
        $descripcion = trim((string) ($post['descripcion'] ?? ''));
        $idResponsable = (int) ($post['id_responsable'] ?? 0);

        if (strlen($nombre) < 3) {
            throw new InvalidArgumentException(
                'El nombre del comité debe tener al menos 3 caracteres'
            );
        }

        if ($area === '') {
            throw new InvalidArgumentException(
                'Debe seleccionar el área del comité'
            );
        }

        if ($idResponsable <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un responsable'
            );
        }

        $this->grupoRepository->insertar([
            'id_comunidad' => $idComunidad,
            'id_responsable' => $idResponsable,
            'nombre' => $nombre,
            'area' => $area,
            'descripcion' => $descripcion,
        ]);
    }

    public function asociarMiembro(array $post): void
    {
        $idGrupo = (int) ($post['id_grupo'] ?? 0);
        $idUsuario = (int) ($post['id_usuario'] ?? 0);
        $rolGrupo = trim((string) ($post['rol_grupo'] ?? 'Colaborador'));

        if ($idGrupo <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un comité'
            );
        }

        if ($idUsuario <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un miembro'
            );
        }

        $this->grupoRepository->insertarMiembro(
            $idGrupo,
            $idUsuario,
            $rolGrupo
        );
    }
}
