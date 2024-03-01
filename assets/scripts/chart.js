function loadCharts() {
  console.log('Loading charts ...');

  const charts = $('.widget-canva');
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

      new Chart(
        document.getElementById('canvas-' + chartData.id),
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

