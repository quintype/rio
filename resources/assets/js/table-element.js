var Papa = require('./vendor/papaparse');
var _ = require("lodash");

function toObject(value) {
  return {title: value}
}

function getTableElement($element) {
  var tableElement = $element.find('#data-table');
  var tableData = tableElement.attr('content');
  var hasHeader = tableElement.attr('header');
  Papa.parse(tableData, {
  header: false, //always be false because it will remove first row from data if it is true
  dynamicTyping: true,
  complete: function(results) {
    var columns, data;
    if (hasHeader == 1) {
      columns = _.map(results.data[0], toObject);
      data = _.drop(results.data);
    } else {
      columns = _.map(results.data[0], toObject)
      tableElement.addClass('hide-table-header-row');
      data = results.data;
    }
    tableElement.DataTable({
      data: data,
      columns: columns,
      scrollX: true,
      responsive: true,
    });
  }
  });

}

module.exports = getTableElement;

