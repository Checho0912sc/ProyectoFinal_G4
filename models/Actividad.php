<?php

declare(strict_types=1);

final class Actividad
{
    public function __construct(
        private readonly ?int    $idActividad,
        private readonly int     $idComunidad,
        private readonly ?int    $idProyecto,
        private readonly int     $idResponsable,
        private readonly string  $titulo,
        private readonly string  $tipo,
        private readonly ?string $descripcion,
        private readonly string  $fecha,
        private readonly string  $hora,
        private readonly string  $lugar,
        private readonly string  $estado
    ) {
    }

    public function getIdActividad(): ?int
    {
        return $this->idActividad;
    }

    public function getIdComunidad(): int
    {
        return $this->idComunidad;
    }

    public function getIdProyecto(): ?int
    {
        return $this->idProyecto;
    }

    public function getIdResponsable(): int
    {
        return $this->idResponsable;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getHora(): string
    {
        return $this->hora;
    }

    public function getLugar(): string
    {
        return $this->lugar;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}
