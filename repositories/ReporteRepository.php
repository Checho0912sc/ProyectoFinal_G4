<?php

declare(strict_types=1);

final class ReporteRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    /**
     * INDICADORES
     */

    public function contarProyectos(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
SELECT COUNT(*)
FROM proyectos AS p
INNER JOIN grupos AS g
    ON g.id_grupo = p.id_grupo
WHERE g.id_comunidad = :id_comunidad
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn();
    }

    public function contarActividades(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
SELECT COUNT(*)
FROM actividades
WHERE id_comunidad = :id_comunidad
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn();
    }

    public function obtenerTotalIngresos(
        int $idComunidad
    ): float {
        $sql = <<<'SQL'
SELECT COALESCE(
    SUM(monto),
    0
)
FROM movimientos_financieros
WHERE id_comunidad = :id_comunidad
AND tipo = 'Ingreso'
AND estado = 'Registrado'
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (float) $consulta->fetchColumn();
    }

    public function contarUsuariosActivos(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
SELECT COUNT(*)
FROM usuario_comunidad AS uc
INNER JOIN usuarios AS u
    ON u.id_usuario = uc.id_usuario
WHERE uc.id_comunidad = :id_comunidad
AND uc.estado = 'Activo'
AND u.estado = 'Activo'
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * REPORTE DE PROYECTOS
     */
    public function obtenerReporteProyectos(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    p.id_proyecto,
    p.nombre,
    u.nombre AS responsable,
    p.estado,
    p.presupuesto,

    COALESCE(
        ROUND(
            100 *
            SUM(
                CASE
                    WHEN t.estado = 'Finalizada'
                    THEN 1
                    ELSE 0
                END
            )
            /
            NULLIF(
                SUM(
                    CASE
                        WHEN t.estado <> 'Cancelada'
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            )
        ),
        0
    ) AS avance

FROM proyectos AS p

INNER JOIN grupos AS g
    ON g.id_grupo = p.id_grupo

INNER JOIN usuarios AS u
    ON u.id_usuario = p.id_responsable

LEFT JOIN tareas AS t
    ON t.id_proyecto = p.id_proyecto

WHERE g.id_comunidad = :id_comunidad

GROUP BY
    p.id_proyecto,
    p.nombre,
    u.nombre,
    p.estado,
    p.presupuesto,
    p.fecha_creacion

ORDER BY p.fecha_creacion DESC
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    /**
     * REPORTE FINANCIERO
     */
    public function obtenerReporteFinanciero(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    mf.id_movimiento,
    mf.fecha,
    mf.descripcion,
    mf.tipo,
    mf.monto,
    mf.estado,
    u.nombre AS usuario,
    p.nombre AS proyecto

FROM movimientos_financieros AS mf

INNER JOIN usuarios AS u
    ON u.id_usuario = mf.id_usuario

LEFT JOIN proyectos AS p
    ON p.id_proyecto = mf.id_proyecto

WHERE mf.id_comunidad = :id_comunidad

ORDER BY
    mf.fecha DESC,
    mf.id_movimiento DESC
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    /**
     * REPORTE DE ACTIVIDADES
     */
    public function obtenerReporteActividades(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    a.id_actividad,
    a.titulo,
    a.tipo,
    a.fecha,
    a.hora,
    a.lugar,
    a.estado,
    p.nombre AS proyecto,
    u.nombre AS responsable

FROM actividades AS a

INNER JOIN usuarios AS u
    ON u.id_usuario = a.id_responsable

LEFT JOIN proyectos AS p
    ON p.id_proyecto = a.id_proyecto

WHERE a.id_comunidad = :id_comunidad

ORDER BY
    a.fecha DESC,
    a.hora DESC
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }
}