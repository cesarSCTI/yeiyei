/*
  app.core.js
  Helpers globales del sistema.
*/

(function () {
  'use strict';

  // Utilidades DOM
  window.qs = window.qs || function(sel, root){ return (root||document).querySelector(sel); };
  window.qsa = window.qsa || function(sel, root){ return Array.from((root||document).querySelectorAll(sel)); };

  // Fix global: backdrops/scroll al cerrar modales
  document.addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  });

  // ===========================================================
  // YamsTabla: tablas con paginación, scroll interno y
  // encabezado fijo (DataTables). Úsalo en lugar de reemplazar
  // el innerHTML del <tbody>, que rompería la paginación.
  // ===========================================================
  const IDIOMA_ES = {
    decimal: '.', thousands: ',',
    emptyTable: 'No hay registros',
    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
    infoEmpty: 'Mostrando 0 a 0 de 0 registros',
    infoFiltered: '(filtrado de _MAX_ registros)',
    lengthMenu: 'Mostrar _MENU_ por página',
    loadingRecords: 'Cargando...',
    processing: 'Procesando...',
    search: 'Buscar:',
    zeroRecords: 'No se encontraron resultados',
    paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
    aria: { sortAscending: ': orden ascendente', sortDescending: ': orden descendente' }
  };

  function disponible() {
    return !!(window.jQuery && jQuery.fn && jQuery.fn.DataTable);
  }

  function aTabla(tabla) {
    return typeof tabla === 'string' ? document.querySelector(tabla) : tabla;
  }

  function numColumnas(t) {
    const fila = t.tHead && t.tHead.rows[0];
    return fila ? fila.cells.length : 0;
  }

  // Quita filas de aviso ("No hay registros", colspan) que DataTables no admite
  function soloFilasDeDatos(t, filas) {
    const n = numColumnas(t);
    return filas.filter(tr => tr.cells.length === n);
  }

  function opcionesDe(t, extra) {
    const conBuscador = !(extra && extra.buscador === false);
    const opts = Object.assign({}, extra);
    delete opts.buscador;
    return Object.assign({
      language: IDIOMA_ES,
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [],            // respeta el orden que entrega el servidor
      autoWidth: false,
      dom:
        "<'row align-items-center g-2 mb-2'<'col-sm-6'l><'col-sm-6'" + (conBuscador ? 'f' : '') + ">>" +
        "<'yams-tabla-scroll't>" +
        "<'row align-items-center g-2 mt-2'<'col-md-5'i><'col-md-7'p>>"
    }, opts);
  }

  window.YamsTabla = {
    // Inicializa (una sola vez) y devuelve la API de DataTables
    init(tabla, extra) {
      const t = aTabla(tabla);
      if (!t || !disponible()) return null;
      if (jQuery.fn.DataTable.isDataTable(t)) return jQuery(t).DataTable();

      const tbody = t.tBodies[0];
      if (tbody) {
        const datos = soloFilasDeDatos(t, Array.from(tbody.rows));
        tbody.replaceChildren(...datos);
      }
      t._yamsOpts = extra || {};
      return jQuery(t).DataTable(opcionesDe(t, extra));
    },

    // Sustituye todas las filas (HTML de <tr>...) conservando la página actual
    reemplazarFilas(tabla, htmlFilas, extra) {
      const t = aTabla(tabla);
      if (!t) return;
      const tmp = document.createElement('tbody');
      tmp.innerHTML = htmlFilas || '';
      const filas = soloFilasDeDatos(t, Array.from(tmp.rows));

      if (!disponible()) { t.tBodies[0].innerHTML = htmlFilas; return; }
      if (!jQuery.fn.DataTable.isDataTable(t)) {
        t.tBodies[0].replaceChildren(...filas);
        this.init(t, extra || t._yamsOpts);
        return;
      }
      const dt = jQuery(t).DataTable();
      dt.clear();
      dt.rows.add(filas);
      dt.draw(false);
    },

    // Todas las filas (no solo las de la página visible)
    filas(tabla) {
      const t = aTabla(tabla);
      if (!t) return [];
      if (disponible() && jQuery.fn.DataTable.isDataTable(t)) {
        return jQuery(t).DataTable().rows().nodes().toArray();
      }
      return t.tBodies[0] ? Array.from(t.tBodies[0].rows) : [];
    },

    // Filtro por rango de fechas (texto AAAA-MM-DD) en la columna `col`.
    // Lee los valores de los inputs `desdeSel` y `hastaSel` en cada dibujado.
    rangoFechas(tabla, col, desdeSel, hastaSel) {
      const t = aTabla(tabla);
      if (!t || !disponible() || t._yamsRango) return;
      t._yamsRango = true;
      jQuery.fn.dataTable.ext.search.push(function (settings, data) {
        if (settings.nTable !== t) return true;
        const desde = document.querySelector(desdeSel)?.value || '';
        const hasta = document.querySelector(hastaSel)?.value || '';
        if (!desde && !hasta) return true;
        const fecha = String(data[col] || '').trim().slice(0, 10);
        if (!fecha) return false;
        if (desde && fecha < desde) return false;
        if (hasta && fecha > hasta) return false;
        return true;
      });
    },

    // Filas que pasan los filtros actuales (todas las páginas)
    filasFiltradas(tabla) {
      const t = aTabla(tabla);
      if (!t || !disponible() || !jQuery.fn.DataTable.isDataTable(t)) return [];
      return jQuery(t).DataTable().rows({ search: 'applied' }).nodes().toArray();
    },

    // Búsqueda exacta (ignora espacios alrededor) para columnas de catálogo
    buscarExacto(dt, col, valor) {
      const re = valor ? '^\\s*' + jQuery.fn.dataTable.util.escapeRegex(valor) + '\\s*$' : '';
      return dt.column(col).search(re, true, false);
    },

    // Avisa a DataTables que se editó el contenido de filas en el DOM
    refrescar(tabla, fila) {
      const t = aTabla(tabla);
      if (!t || !disponible() || !jQuery.fn.DataTable.isDataTable(t)) return;
      const dt = jQuery(t).DataTable();
      (fila ? dt.row(fila) : dt.rows()).invalidate('dom');
      dt.draw(false);
    }
  };
})();
