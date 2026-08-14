<?php

declare(strict_types=1);

final class UsuarioRepository
{
    public function __construct(
        private readonly PDO $conexion
    ) {
    }

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
}