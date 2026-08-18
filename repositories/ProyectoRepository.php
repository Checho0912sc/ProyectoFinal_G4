<?php

declare(strict_types=1);

final class ProyectoRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    public function listarPorComunidad(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                p.id_proyecto,
                p.id_grupo,
                p.id_responsable,
                p.nombre,
                p.descripcion,
                p.fecha_inicio,
                p.fecha_fin,
                p.estado,
                p.presupuesto,
                g.nombre AS grupo,
                u.nombre AS responsable
            FROM proyectos AS p
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_responsable
            WHERE g.id_comunidad = :id_comunidad
            ORDER BY p.fecha_inicio DESC, p.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function buscarPorIdYComunidad(
        int $idProyecto,
        int $idComunidad
    ): ?array {
        $sql = <<<'SQL'
            SELECT
                p.id_proyecto,
                p.id_grupo,
                p.id_responsable,
                p.nombre,
                p.descripcion,
                p.fecha_inicio,
                p.fecha_fin,
                p.estado,
                p.presupuesto,
                g.nombre AS grupo,
                u.nombre AS responsable
            FROM proyectos AS p
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            INNER JOIN usuarios AS u
                ON u.id_usuario = p.id_responsable
            WHERE p.id_proyecto = :id_proyecto
              AND g.id_comunidad = :id_comunidad
            LIMIT 1
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_proyecto' => $idProyecto,
            'id_comunidad' => $idComunidad,
        ]);

        $fila = $consulta->fetch();

        return $fila === false ? null : $fila;
    }

    public function listarGrupos(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                id_grupo,
                nombre
            FROM grupos
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Activo'
            ORDER BY nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function listarResponsables(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT DISTINCT
                u.id_usuario,
                u.nombre
            FROM usuarios AS u
            INNER JOIN usuario_comunidad AS uc
                ON uc.id_usuario = u.id_usuario
            WHERE uc.id_comunidad = :id_comunidad
              AND uc.estado = 'Activo'
              AND u.estado = 'Activo'
            ORDER BY u.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function existeGrupoEnComunidad(
        int $idGrupo,
        int $idComunidad
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM grupos
            WHERE id_grupo = :id_grupo
              AND id_comunidad = :id_comunidad
              AND estado = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_grupo' => $idGrupo,
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function existeResponsableEnComunidad(
        int $idUsuario,
        int $idComunidad
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM usuarios AS u
            INNER JOIN usuario_comunidad AS uc
                ON uc.id_usuario = u.id_usuario
            WHERE u.id_usuario = :id_usuario
              AND uc.id_comunidad = :id_comunidad
              AND u.estado = 'Activo'
              AND uc.estado = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function existeNombreEnGrupo(
        string $nombre,
        int $idGrupo,
        ?int $idProyectoExcluir = null
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM proyectos
            WHERE nombre = :nombre
              AND id_grupo = :id_grupo
        SQL;

        $parametros = [
            'nombre' => $nombre,
            'id_grupo' => $idGrupo,
        ];

        if ($idProyectoExcluir !== null) {
            $sql .= <<<'SQL'

                AND id_proyecto <> :id_proyecto
            SQL;

            $parametros['id_proyecto'] =
                $idProyectoExcluir;
        }

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function crear(
        Proyecto $proyecto
    ): int {
        $sql = <<<'SQL'
            INSERT INTO proyectos (
                id_grupo,
                id_responsable,
                nombre,
                descripcion,
                fecha_inicio,
                fecha_fin,
                estado,
                presupuesto
            )
            VALUES (
                :id_grupo,
                :id_responsable,
                :nombre,
                :descripcion,
                :fecha_inicio,
                :fecha_fin,
                :estado,
                :presupuesto
            )
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_grupo' =>
                $proyecto->getIdGrupo(),

            'id_responsable' =>
                $proyecto->getIdResponsable(),

            'nombre' =>
                $proyecto->getNombre(),

            'descripcion' =>
                $proyecto->getDescripcion(),

            'fecha_inicio' =>
                $proyecto->getFechaInicio(),

            'fecha_fin' =>
                $proyecto->getFechaFin(),

            'estado' =>
                $proyecto->getEstado(),

            'presupuesto' =>
                $proyecto->getPresupuesto(),
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function actualizar(
        Proyecto $proyecto
    ): void {
        $sql = <<<'SQL'
            UPDATE proyectos
            SET
                id_grupo = :id_grupo,
                id_responsable = :id_responsable,
                nombre = :nombre,
                descripcion = :descripcion,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                estado = :estado,
                presupuesto = :presupuesto
            WHERE id_proyecto = :id_proyecto
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_grupo' =>
                $proyecto->getIdGrupo(),

            'id_responsable' =>
                $proyecto->getIdResponsable(),

            'nombre' =>
                $proyecto->getNombre(),

            'descripcion' =>
                $proyecto->getDescripcion(),

            'fecha_inicio' =>
                $proyecto->getFechaInicio(),

            'fecha_fin' =>
                $proyecto->getFechaFin(),

            'estado' =>
                $proyecto->getEstado(),

            'presupuesto' =>
                $proyecto->getPresupuesto(),

            'id_proyecto' =>
                $proyecto->getIdProyecto(),
        ]);
    }

    public function cancelar(
        int $idProyecto
    ): void {
        $sql = <<<'SQL'
            UPDATE proyectos
            SET estado = 'Cancelado'
            WHERE id_proyecto = :id_proyecto
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_proyecto' => $idProyecto,
        ]);
    }
}