<?php

final class Database
{
    private static ?PDO $conexion = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$conexion === null) {
            $host = 'db';
            $puerto = '3306';
            $baseDatos = 'comunigest_db';
            $usuario = 'appuser';
            $contrasena = 'apppass';

            $dsn = "mysql:host=$host;port=$puerto;dbname=$baseDatos;charset=utf8mb4";

            try {
                self::$conexion = new PDO(
                    $dsn,
                    $usuario,
                    $contrasena,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Resultados asociativos
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $error) {
                throw new RuntimeException(
                    'No se pudo establecer la conexión con la base de datos: '
                    . $error->getMessage()
                );
            }
        }

        return self::$conexion;
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('No se puede deserializar la conexión.');
    }
}