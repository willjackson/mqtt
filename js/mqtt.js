(function ($, Drupal, drupalSettings) {
  /**
   * @namespace
   */
  Drupal.behaviors.mymoduleAccessData = {
    attach: function (context) {
      var data = drupalSettings.csvData;
      var subName = drupalSettings.subName;


      window.onload = function() {
        var dataPoints = [];

        function getDataPointsFromCSV(csv) {
          var dataPoints = csvLines = points = [];
          csvLines = csv.split(/[\r?\n|\r|\n]+/);
          for (var i = 0; i < csvLines.length; i++) {
            if (csvLines[i].length > 0) {
              points = csvLines[i].split(",");
              var date = new Date(0);

              date.setUTCSeconds(points[0]);

              dataPoints.push({
                x: date,
                y: parseFloat(points[1])
              });
            }
          }
          return dataPoints;
        }

        $.get(data, function(data) {
          var chart = new CanvasJS.Chart("chartContainer", {
            title: {
              text: subName,
            },
            data: [{
              type: "line",
              xValueType: "dateTime",
              dataPoints: getDataPointsFromCSV(data)
            }]
          });

          chart.render();

        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
