<?php

declare(strict_types=1);

final class ActividadRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    public function contarPorComunidad(int $idComunidad): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM actividades
            WHERE id_comunidad = :id_comunidad
        SQL;

        return $this->contarFila($sql, $idComunidad);
    }

    public function contarPorTipo(
        int    $idComunidad,
        string $tipo
    ): int {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM actividades
            WHERE id_comunidad = :id_comunidad
              AND tipo = :tipo
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
            'tipo'         => $tipo,
        ]);

        return (int) $consulta->fetchColumn();
    }

    public function listarPorComunidad(
        int     $idComunidad,
        ?string $tipo = null
    ): array {
        $sql = <<<'SQL'
            SELECT
                a.id_actividad,
                a.titulo,
                a.tipo,
                a.descripcion,
                a.fecha,
                a.hora,
                a.lugar,
                a.estado,
                u.nombre AS responsable,
                p.nombre AS proyecto
            FROM actividades AS a
            INNER JOIN usuarios AS u
                ON u.id_usuario = a.id_responsable
            LEFT JOIN proyectos AS p
                ON p.id_proyecto = a.id_proyecto
            WHERE a.id_comunidad = :id_comunidad
        SQL;

        $parametros = ['id_comunidad' => $idComunidad];

        if ($tipo !== null && $tipo !== '') {
            $sql        .= ' AND a.tipo = :tipo';
            $parametros['tipo'] = $tipo;
        }

        $sql .= ' ORDER BY a.fecha DESC, a.hora ASC';

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    public function listarProximas(
        int $idComunidad,
        int $limite = 5
    ): array {
        $sql = <<<'SQL'
            SELECT
                a.titulo,
                a.fecha,
                a.hora,
                a.lugar
            FROM actividades AS a
            WHERE a.id_comunidad = :id_comunidad
              AND a.estado       = 'Programada'
              AND a.fecha        >= CURRENT_DATE
            ORDER BY a.fecha ASC, a.hora ASC
            LIMIT :limite
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindValue(':id_comunidad', $idComunidad, PDO::PARAM_INT);
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function listarProyectosDeComunidad(int $idComunidad): array
    {
        $sql = <<<'SQL'
            SELECT
                p.id_proyecto,
                p.nombre
            FROM proyectos AS p
            INNER JOIN grupos AS g
                ON g.id_grupo = p.id_grupo
            WHERE g.id_comunidad = :id_comunidad
              AND p.estado NOT IN ('Finalizado', 'Cancelado')
            ORDER BY p.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return $consulta->fetchAll();
    }

    public function insertar(array $datos): void
    {
        $sql = <<<'SQL'
            INSERT INTO actividades (
                id_comunidad,
                id_proyecto,
                id_responsable,
                titulo,
                tipo,
                descripcion,
                fecha,
                hora,
                lugar,
                estado
            ) VALUES (
                :id_comunidad,
                :id_proyecto,
                :id_responsable,
                :titulo,
                :tipo,
                :descripcion,
                :fecha,
                :hora,
                :lugar,
                'Programada'
            )
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad'   => $datos['id_comunidad'],
            'id_proyecto'    => $datos['id_proyecto'] !== '' ? (int) $datos['id_proyecto'] : null,
            'id_responsable' => $datos['id_responsable'],
            'titulo'         => $datos['titulo'],
            'tipo'           => $datos['tipo'],
            'descripcion'    => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
            'fecha'          => $datos['fecha'],
            'hora'           => $datos['hora'],
            'lugar'          => $datos['lugar'],
        ]);
    }

    private function contarFila(string $sql, int $idComunidad): int
    {
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_comunidad' => $idComunidad]);

        return (int) $consulta->fetchColumn();
    }
}
