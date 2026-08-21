<?php

declare(strict_types=1);

final class MovimientoFinanciero
{
    private ?int $idMovimiento;
    private int $idComunidad;
    private ?int $idProyecto;
    private int $idUsuario;
    private string $tipo;
    private string $descripcion;
    private float $monto;
    private string $fecha;
    private string $estado;

    public function __construct(
        ?int $idMovimiento,
        int $idComunidad,
        ?int $idProyecto,
        int $idUsuario,
        string $tipo,
        string $descripcion,
        float $monto,
        string $fecha,
        string $estado = 'Registrado'
    ) {
        $this->idMovimiento = $idMovimiento;
        $this->idComunidad = $idComunidad;
        $this->idProyecto = $idProyecto;
        $this->idUsuario = $idUsuario;
        $this->tipo = $tipo;
        $this->descripcion = $descripcion;
        $this->monto = $monto;
        $this->fecha = $fecha;
        $this->estado = $estado;
    }

    public function getIdMovimiento(): ?int
    {
        return $this->idMovimiento;
    }

    public function getIdComunidad(): int
    {
        return $this->idComunidad;
    }

    public function getIdProyecto(): ?int
    {
        return $this->idProyecto;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getMonto(): float
    {
        return $this->monto;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}