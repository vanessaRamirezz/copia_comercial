// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    // Configuración para inicializar DataTable en español
    "language": {
      "url": baseURL +"public/js/es-ES.json"
    },
    // Limpiar filtros y entradas de búsqueda al cargar la página
    "initComplete": function() {
      $('#dataTable_filter input').val(''); // Limpiar el campo de búsqueda global
      $('.dataTables_filter input').val(''); // Limpiar el campo de búsqueda en cada columna si aplica
    }
  });
});
