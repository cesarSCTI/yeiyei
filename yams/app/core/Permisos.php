<?php
/**
 * Permisos por rol (ajustes 14 y 15 de la cotización).
 *
 * Los empleados (rol Operador) consultan y registran información, pero no
 * modifican ni eliminan registros existentes: eso queda para el Administrador.
 * Las vistas usan yams_puede_modificar() para ocultar los botones y los
 * controladores llaman a Controller::soloAdministrador() para impedirlo en el
 * servidor aunque alguien llame la ruta directamente.
 */

function yams_rol(): string
{
    return $_SESSION['user']['role'] ?? '';
}

function yams_puede_modificar(): bool
{
    return yams_rol() === 'Administrador';
}
