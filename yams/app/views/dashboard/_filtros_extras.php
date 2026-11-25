<?php
/**
 * Búsqueda y filtros de ingresos extras (ajustes 07 y 08).
 * Se incluye en la sección Finanzas (Administrador) y en Ingresos (Operador);
 * solo una de las dos existe en la página, así que los id no se repiten.
 */
?>
<div class="yams-filtros">
    <div class="yams-filtro yams-filtro-texto">
        <label for="buscarExtra">Buscar</label>
        <input type="text" id="buscarExtra" class="form-control" placeholder="Evento o concepto...">
    </div>
    <div class="yams-filtro">
        <label for="filtroFormaExtra">Forma de pago</label>
        <select id="filtroFormaExtra" class="form-select">
            <option value="">Todas</option>
            <option value="Efectivo">Efectivo</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Transferencia">Transferencia</option>
        </select>
    </div>
    <div class="yams-filtro">
        <label for="extraDesde">Desde</label>
        <input type="date" id="extraDesde" class="form-control">
    </div>
    <div class="yams-filtro">
        <label for="extraHasta">Hasta</label>
        <input type="date" id="extraHasta" class="form-control">
    </div>
    <div class="yams-filtro">
        <label for="extraPeriodo">Periodo</label>
        <select id="extraPeriodo" class="form-select">
            <option value="">Personalizado</option>
            <option value="hoy">Hoy</option>
            <option value="semana">Esta semana</option>
            <option value="mes">Este mes</option>
            <option value="mesAnterior">Mes anterior</option>
            <option value="anio">Este año</option>
        </select>
    </div>
    <button type="button" class="btn btn-outline-secondary yams-limpiar" id="limpiarFiltrosExtras">
        <i class="fas fa-eraser me-1"></i>Limpiar
    </button>
</div>

<!-- Totales según los filtros aplicados -->
<div class="yams-totales" id="totalesExtras" aria-live="polite"></div>
