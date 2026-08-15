<?php

declare(strict_types=1);

final class GrupoRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    public function contarActivosPorComunidad(int $idComunidad): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM grupos
            WHERE id_comunidad = :id_comunidad
              AND estado       = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return (int) $consulta->fetchColumn();
    }

    public function contarMiembrosActivos(int $idComunidad): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(DISTINCT ug.id_usuario)
            FROM usuario_grupo AS ug
            INNER JOIN grupos AS g
                ON g.id_grupo = ug.id_grupo
            WHERE g.id_comunidad = :id_comunidad
              AND ug.estado      = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return (int) $consulta->fetchColumn();
    }

    public function contarTareasEnCurso(int $idComunidad): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM tareas AS t
            INNER JOIN proyectos AS p
                ON p.id_proyecto = t.id_proyecto
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            WHERE g.id_comunidad = :id_comunidad
              AND t.estado IN ('Pendiente', 'En proceso')
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return (int) $consulta->fetchColumn();
    }

    public function contarProyectosApoyados(int $idComunidad): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(DISTINCT p.id_proyecto)
            FROM proyectos AS p
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            WHERE g.id_comunidad = :id_comunidad
              AND p.estado IN ('Planificado', 'En proceso')
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return (int) $consulta->fetchColumn();
    }

    public function listarPorComunidad(int $idComunidad): array
    {
        $sql = <<<'SQL'
            SELECT
                g.id_grupo,
                g.nombre,
                g.area,
                g.estado,
                u.nombre AS responsable,
                (
                    SELECT COUNT(*)
                    FROM usuario_grupo AS ug
                    WHERE ug.id_grupo = g.id_grupo
                      AND ug.estado   = 'Activo'
                ) AS total_miembros,
                (
                    SELECT p.nombre
                    FROM proyectos AS p
                    WHERE p.id_grupo = g.id_grupo
                      AND p.estado   IN ('Planificado', 'En proceso')
                    ORDER BY p.fecha_creacion DESC
                    LIMIT 1
                ) AS proyecto_activo
            FROM grupos AS g
            INNER JOIN usuarios AS u
                ON u.id_usuario = g.id_responsable
            WHERE g.id_comunidad = :id_comunidad
            ORDER BY g.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return $consulta->fetchAll();
    }

    public function listarCoordinadores(int $idComunidad): array
    {
        $sql = <<<'SQL'
            SELECT
                u.nombre,
                g.nombre AS grupo_nombre,
                g.area
            FROM grupos AS g
            INNER JOIN usuarios AS u
                ON u.id_usuario = g.id_responsable
            WHERE g.id_comunidad = :id_comunidad
              AND g.estado        = 'Activo'
            ORDER BY u.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return $consulta->fetchAll();
    }

    public function listarTareasRecientes(
        int $idComunidad,
        int $limite = 5
    ): array {
        $sql = <<<'SQL'
            SELECT
                t.titulo,
                t.estado,
                g.nombre AS grupo_nombre
            FROM tareas AS t
            INNER JOIN proyectos AS p
                ON p.id_proyecto = t.id_proyecto
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            WHERE g.id_comunidad = :id_comunidad
            ORDER BY t.fecha_creacion DESC
            LIMIT :limite
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id_comunidad', $idComunidad, PDO::PARAM_INT);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function listarMiembrosDeComunidad(int $idComunidad): array
    {
        $sql = <<<'SQL'
            SELECT
                u.id_usuario,
                u.nombre
            FROM usuario_comunidad AS uc
            INNER JOIN usuarios AS u
                ON u.id_usuario = uc.id_usuario
            WHERE uc.id_comunidad = :id_comunidad
              AND uc.estado        = 'Activo'
              AND u.estado         = 'Activo'
            ORDER BY u.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return $consulta->fetchAll();
    }

    public function insertar(array $datos): void
    {
        $sql = <<<'SQL'
            INSERT INTO grupos (
                id_comunidad,
                id_responsable,
                nombre,
                area,
                descripcion,
                estado
            ) VALUES (
                :id_comunidad,
                :id_responsable,
                :nombre,
                :area,
                :descripcion,
                'Activo'
            )
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad'   => $datos['id_comunidad'],
            'id_responsable' => $datos['id_responsable'],
            'nombre'         => $datos['nombre'],
            'area'           => $datos['area'],
            'descripcion'    => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
        ]);
    }

    public function insertarMiembro(
        int    $idGrupo,
        int    $idUsuario,
        string $rolGrupo
    ): void {
        $sql = <<<'SQL'
            INSERT INTO usuario_grupo (
                id_grupo,
                id_usuario,
                rol_grupo,
                fecha_asignacion,
                estado
            ) VALUES (
                :id_grupo,
                :id_usuario,
                :rol_grupo,
                CURRENT_DATE,
                'Activo'
            )
            ON DUPLICATE KEY UPDATE
                rol_grupo = VALUES(rol_grupo),
                estado    = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_grupo'  => $idGrupo,
            'id_usuario' => $idUsuario,
            'rol_grupo' => $rolGrupo,
        ]);
    }
}
