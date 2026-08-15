<?php

declare(strict_types=1);

final class Grupo
{
    public function __construct(
        private readonly ?int    $idGrupo,
        private readonly int     $idComunidad,
        private readonly int     $idResponsable,
        private readonly string  $nombre,
        private readonly string  $area,
        private readonly ?string $descripcion,
        private readonly string  $estado
    ) {
    }

    public function getIdGrupo(): ?int
    {
        return $this->idGrupo;
    }

    public function getIdComunidad(): int
    {
        return $this->idComunidad;
    }

    public function getIdResponsable(): int
    {
        return $this->idResponsable;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getArea(): string
    {
        return $this->area;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}
