window.grid = null;

function loadGrid () {

  //TODO : test jQuery UI Layout Plugin (https://www.formget.com/jquery-layout-plugins/ from https://gridstackjs.com/#getStarted)

  console.log('loadGrid');

  let url = '/myPage/widget/load/grid';
  // url = url + '?XDEBUG_SESSION_START=1';

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
    .then((widgetResponse) => {
      console.log('JsonWidgetResponse')
      console.log(widgetResponse)
      // TODO : control grid options
      const gridOption = {
        "minRow": 2,
        "cellHeight": "7rem",
        //"cellHeight" : 'initial', // start square but will set to % of window width later
        "animate": false, // show immediate (animate : true is nice for user dragging though)
        "disableOneColumnMode": true, // will manually do 1 column
        "float": true
      };
      window.grid = GridStack.init(gridOption);
      setGridstackEvents(grid);
      grid.load(widgetResponse);
      $('#button-add-widget').css("display", "inline");
    })
    .catch((error) => {
      console.log('error loading widget grid, detail bellow');
      console.log(error);
    })

}



function setGridstackEvents () {

  grid.on('change', function(event, gridStackItems) {
    gridStackItems.forEach(function(gridStackItem) {
      saveWidget(gridStackItem);
    });
  });

}

function addWidget () {
  console.log('adding widget');

  let url = '/myPage/widget/new';
  url = url + '?XDEBUG_SESSION_START=1';

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
    .then((widgetResponse) => {
      console.log('NEW widget')
      console.log(widgetResponse)

      const gridStackItem = grid.addWidget('<div class="grid-stack-item"><div class="grid-stack-item-content"></div></div>', widgetResponse);
      console.log('gridStackItem bellow');
      console.log(gridStackItem);
    })
    .catch((error) => {
      console.log('error creating new widget, detail bellow');
      console.log(error);
    })

}

function saveWidget (gridStackItem) {

  console.log('gridStackItem in saveWidget bellow');
  console.log(gridStackItem);

  let widgetData = JSON.stringify({
    "id": gridStackItem.id + "",
    "w": gridStackItem.w + "",
    "h": gridStackItem.h + "",
    "x": gridStackItem.x + "",
    "y": gridStackItem.y + ""
  })

  console.log('widgetData for saving request bellow');
  console.log(widgetData);

  let url = '/myPage/widget/save';
  // url = url + '?XDEBUG_SESSION_START=1';

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    redirect: "follow",
    body: widgetData
  };

  fetch(url, requestOptions)
    .then(response => response.json())
    .then((widgetResponse) => {
      console.log('widget saved')
      console.log(widgetResponse)
      gridStackItem.id = widgetResponse.id;
    })
    .catch((error) => {
      console.log('error saving widget, detail bellow');
      console.log(error);
    })


}