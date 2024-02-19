window.grid = null;

function loadGrid () {

  let url = '/myPage/grid';
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
      console.error('error loading widget grid, detail : ', error);
    })

}



function setGridstackEvents () {

  grid.on('change', function(event, gridStackItems) {
    gridStackItems.forEach(function(gridStackItem) {
      updateWidget(gridStackItem);
    });
  });

}

function addWidget () {

  let url = '/myPage/widget/new';
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
      grid.addWidget('<div class="grid-stack-item"><div class="grid-stack-item-content"></div></div>', widgetResponse);
    })
    .catch((error) => {
      console.error('error creating new widget, detail : ', error);
    })

}

function updateWidget (gridStackItem) {

  let url = '/myPage/widget/' + gridStackItem.id;
  // url = url + '?XDEBUG_SESSION_START=1';

  let widgetData = JSON.stringify({
    "id": gridStackItem.id + "",
    "w": gridStackItem.w + "",
    "h": gridStackItem.h + "",
    "x": gridStackItem.x + "",
    "y": gridStackItem.y + ""
  })

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "UPDATE",
    headers: myHeaders,
    redirect: "follow",
    body: widgetData
  };

  fetch(url, requestOptions)
    .then(response => response.json())
    .then((widgetResponse) => {
      // console.log('widget saved, detail : ', widgetResponse);
    })
    .catch((error) => {
      console.error('error saving widget, detail : ', error);
    })


}


function deleteWidget (gridstackItemId) {

  let gridstackItem = null;

  window.grid.getGridItems().forEach(function(item) {
    if (item.gridstackNode.id == gridstackItemId) {
      gridstackItem = item;
    }
  });

  if (gridstackItem === null) {
    console.error('Can\'t delete widget, gridstackItem not found (id : ' + gridstackItemId + ')');
    return;
  }

  let url = '/myPage/widget/' + gridstackItemId;
  // url = url + '?XDEBUG_SESSION_START=1';

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "DELETE",
    headers: myHeaders,
    redirect: "follow"
  };

  fetch(url, requestOptions)
    .then((widgetResponse) => {
      grid.removeWidget(gridstackItem);
    })
    .catch((error) => {
      console.error('error deleting widget, detail : ', error);
    })

}