function loadCharts() {
  console.log('Loading charts ...');

  const charts = $('.widget-canvas');
  console.log('charts', charts);

  for (let chart of charts) {
    loadChart(chart);
  }
}


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
    .then((chartData) => {

      console.log('chartData', chartData);
      const dataSets = chartData.data.map(row => row.label)
      console.log('dataSets', dataSets);
      const dataFormated = chartData.data.map(row => row.data)
      console.log('dataFormated', dataFormated);

      const newChart = document.getElementById('canvas-' + chartData.id)
      const parentNewChart = newChart.parentElement
      parentNewChart.style.height = "95%"
      const spinner = parentNewChart.previousElementSibling
      console.log('spinner', spinner);
      spinner.classList.add('widget-spinner-hide')

      new Chart(
        newChart,
        {
          type: chartData.type,
          data: {
            labels: dataSets,
            datasets: [
              {
                label: chartData.label,
                data: dataFormated
              }
            ]
          },
          options: {
            aspectRatio: 1,
            scales: {
              x: {
                max: 50
              },
              y: {
                max: 50
              }
            }
          }
        }
      );

    })
    .catch((error) => {
      console.error('error loading widget chart, detail : ', error);
    })
}

