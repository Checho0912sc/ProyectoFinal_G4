<?php

declare(strict_types=1);

final class DashboardRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    public function contarProyectosActivos(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM proyectos AS p
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            WHERE g.id_comunidad = :id_comunidad
              AND p.estado IN (
                  'Planificado',
                  'En proceso',
                  'Pausado'
              )
        SQL;

        return $this->contar($sql, $idComunidad);
    }

    public function contarActividadesProximas(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM actividades
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Programada'
              AND fecha >= CURRENT_DATE
        SQL;

        return $this->contar($sql, $idComunidad);
    }

    public function contarMiembrosActivos(
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

        return $this->contar($sql, $idComunidad);
    }

    public function obtenerSaldo(
        int $idComunidad
    ): float {
        $sql = <<<'SQL'
            SELECT COALESCE(
                SUM(
                    CASE
                        WHEN tipo = 'Ingreso'
                            THEN monto
                        ELSE -monto
                    END
                ),
                0
            ) AS saldo
            FROM movimientos_financieros
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Registrado'
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (float) $consulta->fetchColumn();
    }

    public function obtenerProyectosRecientes(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                p.id_proyecto,
                p.nombre,
                u.nombre AS responsable,
                p.estado,

                COALESCE(
                    ROUND(
                        100
                        * SUM(
                            CASE
                                WHEN t.estado = 'Finalizada'
                                    THEN 1
                                ELSE 0
                            END
                        )
                        / NULLIF(
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
                p.fecha_creacion

            ORDER BY p.fecha_creacion DESC

            LIMIT 5
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function obtenerActividadesProximas(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                titulo,
                fecha,
                hora,
                lugar,
                tipo
            FROM actividades
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Programada'
              AND fecha >= CURRENT_DATE
            ORDER BY fecha, hora
            LIMIT 5
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function obtenerMovimientosRecientes(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                descripcion,
                tipo,
                monto,
                fecha
            FROM movimientos_financieros
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Registrado'
            ORDER BY fecha DESC, id_movimiento DESC
            LIMIT 5
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    private function contar(
        string $sql,
        int $idComunidad
    ): int {
        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn();
    }
}