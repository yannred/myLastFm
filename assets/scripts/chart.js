/**
 * Load all the charts in the page
 */
function loadCharts() {
  console.log('Loading charts ...');

  const charts = $('.widget-canvas');
  console.log('charts', charts);

  for (let chart of charts) {
    loadChart(chart);
  }
}


/**
 * Load a chart from the server (API call) and create it in the page
 * @param chart
 */
function loadChart(chart) {

  const chartId = chart.id.split('-')[1];
  console.log('Loading chart id : ' + chartId);

  let url = '/myPage/chart/' + chartId;
  // url = url + '?XDEBUG_SESSION_START=1';
  console.log('url : ' + url);

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "GET",
    headers: myHeaders,
    redirect: "follow"
  };

  fetch(url, requestOptions)
    .then(response => response.json())
    .then((chartParametersFromApi) => {

      console.log('chartParametersFromApi', chartParametersFromApi);
      console.log('datasets test', chartParametersFromApi.dataAttribute.datasets);

      //dataSets contains the labels of the chart
      const dataLabel = chartParametersFromApi.data.map(row => row.label)

      //dataFormated contains the data of the chart
      const dataFormated = chartParametersFromApi.data.map(row => row.data)

      //Json for generate the chart
      const temporaryChartDefinition = {
        type: chartParametersFromApi.type,
        data: chartParametersFromApi.dataAttribute,
        options : chartParametersFromApi.optionsAttribute,
      }

      // Set the callback functions for the chart options
      const chartDefinition = getJsonChartDefinition(temporaryChartDefinition, chartParametersFromApi, dataLabel, dataFormated);
      console.log('final chart definition', chartDefinition);

      // remove spinner
      const chartNode = document.getElementById('canvas-' + chartParametersFromApi.id)
      removeSpinner(chartNode)


      // Create the chart
      new Chart(chartNode, chartDefinition);

    })
    .catch((error) => {
      console.error('error loading widget chart, detail : ', error);
    })
}


/**
 * Remove the spinner from the chart
 * @param chartNode
 */
function removeSpinner(chartNode) {
  const parentNewChart = chartNode.parentElement
  parentNewChart.style.height = "95%"
  const spinner = parentNewChart.previousElementSibling
  spinner.classList.add('widget-spinner-hide')
}


/**
 * Return the finlay JSON for generate the chart
 * Main purpose is to set the callback functions for the chart options
 * @param chart Temporary chart definition
 * @param chartParametersFromApi datas
 * @param dataLabel labels
 * @param dataFormated
 */
function getJsonChartDefinition(chart, chartParametersFromApi, dataLabel, dataFormated){

  for (let option of chartParametersFromApi.callbackOptions) {
    switch (option) {

      //Truncate text for the x axis
      case 'truncateTickX' :
        chart.options.scales.x.ticks.callback = (value, index, ticks) => {
          return truncateText(dataLabel[index], 20);
        }
        break;

    }
  }

  return chart
}



/**
 * Truncate text to a given length
 * @param text
 * @param length
 * @returns {string|*}
 */
function truncateText(text, length) {
  return text.length > length ? text.substring(0, length) + '...' : text;
}