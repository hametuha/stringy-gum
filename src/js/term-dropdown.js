/**
 * Description
 */

/* StringGum: false */

(function ($) {
  'use strict';

  $(document).ready(function(){
    $('select[data-replacer=select2]').each(function( index, select ){
      var params = {
        allowClear: true,
        maximumSelectionLength: $(select).attr('data-length'),
        ajax: {
          url: StringyGum.endpoint,
          dataType: 'json',
          delay: 300,
          cache: true,
          minimumInputLength: 1,
          data: function(params){
            return {
              action: 'term_dropdown',
              s: params.term,
              t: $(select).attr('data-taxonomy')
            }
          },
          processResults: function(data, params){
            return data;
          },
        }
      };
      $(select).select2(params);
    });
  });

})(jQuery);
