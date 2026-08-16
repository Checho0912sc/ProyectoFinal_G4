<?php

declare(strict_types=1);

final class UsuarioRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

    /* =========================================================
       MÉTODOS UTILIZADOS POR AUTENTICACIÓN
       ========================================================= */

    public function buscarPorCorreo(
        string $correo
    ): ?Usuario {
        $sql = <<<'SQL'
            SELECT
                id_usuario,
                nombre,
                correo,
                contrasena_hash,
                estado
            FROM usuarios
            WHERE correo = :correo
            LIMIT 1
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'correo' => $correo,
        ]);

        $fila = $consulta->fetch();

        if ($fila === false) {
            return null;
        }

        return new Usuario(
            (int) $fila['id_usuario'],
            (string) $fila['nombre'],
            (string) $fila['correo'],
            (string) $fila['contrasena_hash'],
            (string) $fila['estado']
        );
    }

    public function obtenerMembresiasActivas(
        int $idUsuario
    ): array {
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
              AND uc.estado = 'Activo'
              AND c.estado = 'Activa'
              AND r.estado = 'Activo'
            ORDER BY c.nombre
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
        ]);

        return $consulta->fetchAll();
    }

    public function actualizarUltimoAcceso(
        int $idUsuario
    ): void {
        $sql = <<<'SQL'
            UPDATE usuarios
            SET ultimo_acceso = CURRENT_TIMESTAMP
            WHERE id_usuario = :id_usuario
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
        ]);
    }

    /* =========================================================
       CRUD DE USUARIOS
       ========================================================= */

    public function listarPorComunidad(
        int $idComunidad
    ): array {
        $sql = <<<'SQL'
            SELECT
                u.id_usuario,
                u.nombre,
                u.correo,
                u.telefono,
                u.estado AS estado_usuario,
                uc.id_rol,
                r.nombre AS rol,
                uc.estado
            FROM usuarios AS u
            INNER JOIN usuario_comunidad AS uc
                ON uc.id_usuario = u.id_usuario
            INNER JOIN roles AS r
                ON r.id_rol = uc.id_rol
            WHERE uc.id_comunidad = :id_comunidad
            ORDER BY u.nombre ASC
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_comunidad' => $idComunidad,
        ]);

        return $consulta->fetchAll();
    }

    public function buscarPorIdYComunidad(
        int $idUsuario,
        int $idComunidad
    ): ?array {
        $sql = <<<'SQL'
            SELECT
                u.id_usuario,
                u.nombre,
                u.correo,
                u.telefono,
                u.estado AS estado_usuario,
                uc.id_rol,
                r.nombre AS rol,
                uc.estado
            FROM usuarios AS u
            INNER JOIN usuario_comunidad AS uc
                ON uc.id_usuario = u.id_usuario
            INNER JOIN roles AS r
                ON r.id_rol = uc.id_rol
            WHERE u.id_usuario = :id_usuario
              AND uc.id_comunidad = :id_comunidad
            LIMIT 1
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_comunidad' => $idComunidad,
        ]);

        $fila = $consulta->fetch();

        return $fila === false
            ? null
            : $fila;
    }

    public function listarRolesActivos(): array
    {
        $sql = <<<'SQL'
            SELECT
                id_rol,
                nombre
            FROM roles
            WHERE estado = 'Activo'
            ORDER BY nombre ASC
        SQL;

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }

    public function existeRolActivo(
        int $idRol
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM roles
            WHERE id_rol = :id_rol
              AND estado = 'Activo'
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_rol' => $idRol,
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function existeCorreo(
        string $correo,
        ?int $idUsuarioExcluir = null
    ): bool {
        $sql = <<<'SQL'
            SELECT COUNT(*)
            FROM usuarios
            WHERE correo = :correo
        SQL;

        $parametros = [
            'correo' => $correo,
        ];

        if ($idUsuarioExcluir !== null) {
            $sql .= <<<'SQL'

                AND id_usuario <> :id_usuario
            SQL;

            $parametros['id_usuario'] =
                $idUsuarioExcluir;
        }

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function crear(
        array $datos,
        int $idComunidad
    ): int {
        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = <<<'SQL'
                INSERT INTO usuarios (
                    nombre,
                    correo,
                    contrasena_hash,
                    telefono,
                    estado
                )
                VALUES (
                    :nombre,
                    :correo,
                    :contrasena_hash,
                    :telefono,
                    'Activo'
                )
            SQL;

            $consultaUsuario =
                $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute([
                'nombre' => $datos['nombre'],
                'correo' => $datos['correo'],
                'contrasena_hash' =>
                    $datos['contrasena_hash'],
                'telefono' => $datos['telefono'],
            ]);

            $idUsuario = (int)
                $this->conexion->lastInsertId();

            $sqlComunidad = <<<'SQL'
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
                    :estado
                )
            SQL;

            $consultaComunidad =
                $this->conexion->prepare($sqlComunidad);

            $consultaComunidad->execute([
                'id_comunidad' => $idComunidad,
                'id_usuario' => $idUsuario,
                'id_rol' => $datos['id_rol'],
                'estado' => $datos['estado'],
            ]);

            $this->conexion->commit();

            return $idUsuario;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function actualizar(
        int $idUsuario,
        int $idComunidad,
        array $datos
    ): void {
        try {
            $this->conexion->beginTransaction();

            if (
                isset($datos['contrasena_hash'])
                && $datos['contrasena_hash'] !== null
            ) {
                $sqlUsuario = <<<'SQL'
                    UPDATE usuarios
                    SET
                        nombre = :nombre,
                        correo = :correo,
                        telefono = :telefono,
                        contrasena_hash = :contrasena_hash
                    WHERE id_usuario = :id_usuario
                SQL;

                $parametrosUsuario = [
                    'nombre' => $datos['nombre'],
                    'correo' => $datos['correo'],
                    'telefono' => $datos['telefono'],
                    'contrasena_hash' =>
                        $datos['contrasena_hash'],
                    'id_usuario' => $idUsuario,
                ];
            } else {
                $sqlUsuario = <<<'SQL'
                    UPDATE usuarios
                    SET
                        nombre = :nombre,
                        correo = :correo,
                        telefono = :telefono
                    WHERE id_usuario = :id_usuario
                SQL;

                $parametrosUsuario = [
                    'nombre' => $datos['nombre'],
                    'correo' => $datos['correo'],
                    'telefono' => $datos['telefono'],
                    'id_usuario' => $idUsuario,
                ];
            }

            $consultaUsuario =
                $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute(
                $parametrosUsuario
            );

            $sqlComunidad = <<<'SQL'
                UPDATE usuario_comunidad
                SET
                    id_rol = :id_rol,
                    estado = :estado
                WHERE id_usuario = :id_usuario
                  AND id_comunidad = :id_comunidad
            SQL;

            $consultaComunidad =
                $this->conexion->prepare($sqlComunidad);

            $consultaComunidad->execute([
                'id_rol' => $datos['id_rol'],
                'estado' => $datos['estado'],
                'id_usuario' => $idUsuario,
                'id_comunidad' => $idComunidad,
            ]);

            $this->conexion->commit();
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function desactivarEnComunidad(
        int $idUsuario,
        int $idComunidad
    ): void {
        $sql = <<<'SQL'
            UPDATE usuario_comunidad
            SET estado = 'Inactivo'
            WHERE id_usuario = :id_usuario
              AND id_comunidad = :id_comunidad
        SQL;

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_comunidad' => $idComunidad,
        ]);
    }
}