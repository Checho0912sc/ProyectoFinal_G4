<?php

require_once __DIR__ . '/config/database.php';

try {
    $conexion = Database::getConnection();

    $consulta = $conexion->query("
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
        ORDER BY table_name
    ");

    $tablas = $consulta->fetchAll(PDO::FETCH_COLUMN);

    echo "<h2>Conexión realizada correctamente</h2>";
    echo "<p>Base conectada: " . $conexion->query("SELECT DATABASE()")->fetchColumn() . "</p>";
    echo "<p>Total de tablas: " . count($tablas) . "</p>";

    echo "<ul>";

    foreach ($tablas as $tabla) {
        echo "<li>" . htmlspecialchars($tabla) . "</li>";
    }

    echo "</ul>";
} catch (Throwable $error) {
    echo "<h2>Error de conexión</h2>";
    echo "<p>" . htmlspecialchars($error->getMessage()) . "</p>";
}