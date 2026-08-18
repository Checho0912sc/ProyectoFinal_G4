<?php

declare(strict_types=1);

final class Proyecto
{
    public function __construct(
        private ?int $idProyecto,
        private int $idGrupo,
        private int $idResponsable,
        private string $nombre,
        private ?string $descripcion,
        private string $fechaInicio,
        private ?string $fechaFin,
        private string $estado,
        private float $presupuesto
    ) {
    }

    public function getIdProyecto(): ?int
    {
        return $this->idProyecto;
    }

    public function getIdGrupo(): int
    {
        return $this->idGrupo;
    }

    public function getIdResponsable(): int
    {
        return $this->idResponsable;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getFechaInicio(): string
    {
        return $this->fechaInicio;
    }

    public function getFechaFin(): ?string
    {
        return $this->fechaFin;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getPresupuesto(): float
    {
        return $this->presupuesto;
    }
}