<?php

declare(strict_types=1);

final class MovimientoFinancieroRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    /**
     * Obtiene todos los movimientos de la comunidad.
     */
    public function obtenerPorComunidad(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    mf.id_movimiento,
    mf.id_comunidad,
    mf.id_proyecto,
    mf.id_usuario,
    mf.tipo,
    mf.descripcion,
    mf.monto,
    mf.fecha,
    mf.estado,
    u.nombre AS usuario,
    p.nombre AS proyecto
FROM movimientos_financieros AS mf
INNER JOIN usuarios AS u
    ON u.id_usuario = mf.id_usuario
LEFT JOIN proyectos AS p
    ON p.id_proyecto = mf.id_proyecto
WHERE mf.id_comunidad = :id_comunidad
ORDER BY mf.fecha DESC, mf.id_movimiento DESC
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    /**
     * Obtiene el resumen financiero.
     */
    public function obtenerResumen(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    COALESCE(
        SUM(
            CASE
                WHEN tipo = 'Ingreso'
                AND estado = 'Registrado'
                THEN monto
                ELSE 0
            END
        ),
        0
    ) AS ingresos,

    COALESCE(
        SUM(
            CASE
                WHEN tipo = 'Egreso'
                AND estado = 'Registrado'
                THEN monto
                ELSE 0
            END
        ),
        0
    ) AS egresos

FROM movimientos_financieros
WHERE id_comunidad = :id_comunidad
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        $resultado = $consulta->fetch();

        $ingresos = (float) ($resultado['ingresos'] ?? 0);
        $egresos = (float) ($resultado['egresos'] ?? 0);

        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo' => $ingresos - $egresos,
        ];
    }

    /**
     * Obtiene los proyectos de la comunidad activa.
     *
     * Los proyectos no tienen id_comunidad.
     * La relación es:
     * proyectos -> grupos -> comunidades.
     */
    public function obtenerProyectos(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
SELECT
    p.id_proyecto,
    p.nombre,
    p.estado
FROM proyectos AS p
INNER JOIN grupos AS g
    ON g.id_grupo = p.id_grupo
WHERE g.id_comunidad = :id_comunidad
ORDER BY p.nombre ASC
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    /**
     * Comprueba que un proyecto pertenezca a la comunidad.
     */
    public function proyectoPerteneceAComunidad(
        int $idProyecto,
        int $idComunidad
    ): bool {
        $sql = <<<'SQL'
SELECT COUNT(*)
FROM proyectos AS p
INNER JOIN grupos AS g
    ON g.id_grupo = p.id_grupo
WHERE p.id_proyecto = :id_proyecto
AND g.id_comunidad = :id_comunidad
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_proyecto' => $idProyecto,
            'id_comunidad' => $idComunidad,
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Registra un movimiento.
     */
    public function crear(
        MovimientoFinanciero $movimiento
    ): bool {
        $sql = <<<'SQL'
INSERT INTO movimientos_financieros
(
    id_comunidad,
    id_proyecto,
    id_usuario,
    tipo,
    descripcion,
    monto,
    fecha,
    estado
)
VALUES
(
    :id_comunidad,
    :id_proyecto,
    :id_usuario,
    :tipo,
    :descripcion,
    :monto,
    :fecha,
    'Registrado'
)
SQL;

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_comunidad' => $movimiento->getIdComunidad(),
            'id_proyecto' => $movimiento->getIdProyecto(),
            'id_usuario' => $movimiento->getIdUsuario(),
            'tipo' => $movimiento->getTipo(),
            'descripcion' => $movimiento->getDescripcion(),
            'monto' => $movimiento->getMonto(),
            'fecha' => $movimiento->getFecha(),
        ]);
    }

    /**
     * Busca un movimiento verificando la comunidad.
     */
    public function obtenerPorId(
        int $idMovimiento,
        int $idComunidad
    ): ?array {
        $sql = <<<'SQL'
SELECT
    id_movimiento,
    id_comunidad,
    id_proyecto,
    id_usuario,
    tipo,
    descripcion,
    monto,
    fecha,
    estado
FROM movimientos_financieros
WHERE id_movimiento = :id_movimiento
AND id_comunidad = :id_comunidad
LIMIT 1
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_movimiento' => $idMovimiento,
            'id_comunidad' => $idComunidad,
        ]);

        $resultado = $consulta->fetch();

        return $resultado !== false
            ? $resultado
            : null;
    }

    /**
     * Anula un movimiento sin eliminarlo.
     */
    public function anular(
        int $idMovimiento,
        int $idComunidad
    ): bool {
        $sql = <<<'SQL'
UPDATE movimientos_financieros
SET estado = 'Anulado'
WHERE id_movimiento = :id_movimiento
AND id_comunidad = :id_comunidad
AND estado = 'Registrado'
SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_movimiento' => $idMovimiento,
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->rowCount() > 0;
    }
}