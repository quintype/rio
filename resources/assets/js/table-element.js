var Papa = require('./vendor/papaparse');
var _ = require("lodash");

function toObject(value) {
  return {title: value}
}

function handleFileSelect(tableData) {
  // var hasHeader = $('#data-table').attr('header');
  var hasHeader = true
  Papa.parse(tableData, {
  header: false, //always be false because it will remove first row from data if it is true
  dynamicTyping: true,
  complete: function(results) {
    var columns;
    if (hasHeader) {
      columns = _.map(results.data[0], toObject)
    } else {
      columns = _.map(results.data[0], toObject)
      $('#data-table').addClass('hide-table-header-row')
    }
    console.log(results,"results hhh");
    $('#data-table').DataTable({
      data: results.data,
      columns: columns
    });
    }
  });

}

module.exports = handleFileSelect;

