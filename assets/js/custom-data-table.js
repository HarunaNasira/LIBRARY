(function($) {
  'use strict';
  $(function() {
    $('#book-listing').DataTable({
      "aLengthMenu": [
        [5, 10, 15, -1],
        [5, 10, 15, "All"]
      ],
      "iDisplayLength": 5,
      "language": {
        search: "Search",
        lengthMenu: "Show _MENU_ per page",
        info: "Showing _START_ to _END_ of _TOTAL_ total entries",
        infoEmpty: "No enties found",
        infoFiltered: "(filtered from _MAX_ total entries)"
      },
      "order": [[0, 'asc']], // Sort by Book ID by default
      "columnDefs": [
        {
          "targets": 1, // Cover image column
          "orderable": false
        },
        {
          "targets": 7, // Actions column
          "orderable": false
        }
      ],
      "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
      "responsive": true,
      "bLengthChange": true,
      "bInfo": true,
      "bFilter": true,
      "bSort": true
    });
    
    $('#book-listing').each(function() {
      var datatable = $(this);
      
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      search_input.attr('placeholder', 'Start typing ...');
      search_input.removeClass('form-control-sm');
      
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
    });
  });
})(jQuery);