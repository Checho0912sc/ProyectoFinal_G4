<?php

declare(strict_types=1);

final class Usuario
{
    public function __construct(
        private readonly int $idUsuario,
        private readonly string $nombre,
        private readonly string $correo,
        private readonly string $contrasenaHash,
        private readonly string $estado
    ) {
    }

    public function id(): int
    {
        return $this->idUsuario;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function correo(): string
    {
        return $this->correo;
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'Activo';
    }

    public function verificarContrasena(
        string $contrasena
    ): bool {
        return password_verify(
            $contrasena,
            $this->contrasenaHash
        );
    }

    public function paraSesion(): array
    {
        return [
            'id_usuario' => $this->idUsuario,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
        ];
    }
}