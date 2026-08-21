<?php

declare(strict_types=1);

final class ComunidadRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    // ------------------ BUSCA LAS COMUNIDADES ACTIVAS (Para luego listarlas) ----------------------
    public function listarActivas(): array
    {
        $sql = <<<'SQL'
            SELECT
                c.id_comunidad,
                c.nombre,
                c.descripcion,
                COUNT(uc.id_usuario) AS cantidad_miembros
            FROM comunidades AS c
            LEFT JOIN usuario_comunidad AS uc
                ON uc.id_comunidad = c.id_comunidad
                AND uc.estado = 'Activo'
            WHERE c.estado = 'Activa'
            GROUP BY
                c.id_comunidad,
                c.nombre,
                c.descripcion
            ORDER BY c.nombre
        SQL;

        $consulta =
            $this->conexion->query($sql);

        return $consulta->fetchAll();
    }

    // ------------------ BUSCA UNA COMUNIDAD ACTIVA POR EL ID ----------------------
    public function buscarActivaPorId(
        int $idComunidad
    ): ?array {
        $sql = <<<'SQL'
            SELECT
                id_comunidad,
                nombre,
                descripcion
            FROM comunidades
            WHERE id_comunidad = :id_comunidad
              AND estado = 'Activa'
            LIMIT 1
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        $comunidad = $consulta->fetch();

        return $comunidad === false
            ? null
            : $comunidad;
    }

    // ------------------ VALIDAR SI EL NOMBRE DE LA COMUNIDAD YA EXISTE ----------------------
    public function existeNombre(
        string $nombre
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM comunidades
            WHERE nombre = :nombre
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'nombre' => $nombre,
        ]);

        return (int)
            $consulta->fetchColumn() > 0;
    }

    // ------------------ CREAR UNA COMUNIDAD Y SER EL ADMIN ----------------------
    public function crearConAdministrador(
        int $idUsuario,
        string $nombre,
        ?string $descripcion
    ): array {
        try {
            $this->conexion
                ->beginTransaction();

            $sqlComunidad = <<<'SQL'
                INSERT INTO comunidades (
                    nombre,
                    descripcion,
                    estado
                )
                VALUES (
                    :nombre,
                    :descripcion,
                    'Activa'
                )
            SQL;

            $consultaComunidad =
                $this->conexion->prepare(
                    $sqlComunidad
                );

            $consultaComunidad->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
            ]);

            $idComunidad = (int)
                $this->conexion
                    ->lastInsertId();

            $sqlRol = <<<'SQL'
                SELECT id_rol
                FROM roles
                WHERE nombre = 'Administrador'
                AND estado = 'Activo'
                LIMIT 1
            SQL;

            $consultaRol =
                $this->conexion->query($sqlRol);

            $idRolAdministrador =
                $consultaRol->fetchColumn();

            if ($idRolAdministrador === false) {
                throw new RuntimeException(
                    'No se encontró el rol de administrador.'
                );
            }

            $sqlMembresia = <<<'SQL'
                INSERT INTO usuario_comunidad (
                    id_comunidad,
                    id_usuario,
                    id_rol,
                    fecha_ingreso,
                    estado
                )
                VALUES (
                    :id_comunidad,
                    :id_usuario,
                    :id_rol,
                    CURRENT_DATE,
                    'Activo'
                )
            SQL;

            $consultaMembresia =
                $this->conexion->prepare(
                    $sqlMembresia
                );

            $consultaMembresia->execute([
                'id_comunidad' =>
                    $idComunidad,
                'id_usuario' =>
                    $idUsuario,
                'id_rol' =>
                    (int) $idRolAdministrador,
            ]);

            $this->conexion->commit();

            return [
                'id_comunidad' =>
                    $idComunidad,
                'comunidad' =>
                    $nombre,
                'id_rol' =>
                    (int) $idRolAdministrador,
                'rol' =>
                    'Administrador',
            ];
        } catch (Throwable $error) {
            if (
                $this->conexion
                    ->inTransaction()
            ) {
                $this->conexion
                    ->rollBack();
            }

            throw $error;
        }
    }

    // ------------------ VALIDAR SI EL USUARIO YA SE ENCUENTRA UNIDO A UNA COMUNIDAD----------------------
    public function tieneComunidadActiva(
        int $idUsuario
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM usuario_comunidad AS uc
            INNER JOIN comunidades AS c
                ON c.id_comunidad = uc.id_comunidad
            WHERE uc.id_usuario = :id_usuario
            AND uc.estado = 'Activo'
            AND c.estado = 'Activa'
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
        ]);

        return (int)
            $consulta->fetchColumn() > 0;
    }

    // ------------------ COBTENER EL ROL DE UN USUARIO EN UNA COMUNIDAD ----------------------
    public function obtenerMembresiaActiva(
        int $idUsuario,
        int $idComunidad
    ): ?array {
        $sql = <<<'SQL'
            SELECT
                uc.id_comunidad,
                c.nombre AS comunidad,
                uc.id_rol,
                r.nombre AS rol
            FROM usuario_comunidad AS uc
            INNER JOIN comunidades AS c
                ON c.id_comunidad = uc.id_comunidad
            INNER JOIN roles AS r
                ON r.id_rol = uc.id_rol
            WHERE uc.id_usuario = :id_usuario
              AND uc.id_comunidad = :id_comunidad
              AND uc.estado = 'Activo'
              AND c.estado = 'Activa'
              AND r.estado = 'Activo'
            LIMIT 1
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_comunidad' => $idComunidad,
        ]);

        $membresia = $consulta->fetch();

        return $membresia === false
            ? null
            : $membresia;
    }

    // ------------------ UNIRSE A UNA COMUNIDAD ----------------------
    public function unirUsuario(
        int $idUsuario,
        int $idComunidad
    ): void {
        $sql = <<<'SQL'
            INSERT INTO usuario_comunidad (
                id_comunidad,
                id_usuario,
                id_rol,
                fecha_ingreso,
                estado
            )
            SELECT
                :id_comunidad,
                :id_usuario,
                id_rol,
                CURRENT_DATE,
                'Activo'
            FROM roles
            WHERE nombre = 'Miembro'
              AND estado = 'Activo'
            LIMIT 1
            ON DUPLICATE KEY UPDATE
                id_rol = VALUES(id_rol),
                fecha_ingreso = CURRENT_DATE,
                estado = 'Activo'
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_comunidad' => $idComunidad,
        ]);
    }

    // ------------------ CONTAR MIEMBROS ACTIVOS (Para determinar si no es el unico miembro de la comunidad) ----------------------
    public function contarMiembrosActivos(
        int $idComunidad
    ): int {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM usuario_comunidad
            WHERE id_comunidad = :id_comunidad
            AND estado = 'Activo'
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int)
            $consulta->fetchColumn();
    }

    // ------------------ CONTAR ADMINISTRADORES ACTIVOS (Para ver si no es el unico admin a la hora de salirse)----------------------
    public function contarAdministradoresActivos(
    int $idComunidad
    ): int {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM usuario_comunidad AS uc
            INNER JOIN roles AS r
                ON r.id_rol = uc.id_rol
            WHERE uc.id_comunidad = :id_comunidad
            AND uc.estado = 'Activo'
            AND r.nombre = 'Administrador'
            AND r.estado = 'Activo'
        SQL;

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return (int)
            $consulta->fetchColumn();
    }


    // ------------------ABANDONAR COMUNIDAD ----------------------
    public function abandonar(
        int $idUsuario,
        int $idComunidad,
        bool $desactivarComunidad
    ): void {
        try {
            $this->conexion
                ->beginTransaction();

            $sqlMembresia = <<<'SQL'
                UPDATE usuario_comunidad
                SET estado = 'Inactivo'
                WHERE id_usuario = :id_usuario
                AND id_comunidad = :id_comunidad
            SQL;

            $consultaMembresia =
                $this->conexion->prepare(
                    $sqlMembresia
                );

            $consultaMembresia->execute([
                'id_usuario' => $idUsuario,
                'id_comunidad' => $idComunidad,
            ]);

            if ($desactivarComunidad) {
                $sqlComunidad = <<<'SQL'
                    UPDATE comunidades
                    SET estado = 'Inactiva'
                    WHERE id_comunidad = :id_comunidad
                SQL;

                $consultaComunidad =
                    $this->conexion->prepare(
                        $sqlComunidad
                    );

                $consultaComunidad->execute([
                    'id_comunidad' =>
                        $idComunidad,
                ]);
            }

            $this->conexion->commit();
        } catch (Throwable $error) {
            if (
                $this->conexion
                    ->inTransaction()
            ) {
                $this->conexion
                    ->rollBack();
            }

            throw $error;
        }
    }
}