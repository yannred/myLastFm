function initGridStack () {

  let widgetItems = [
    {content: 'my first widget'}, // will default to location (0,0) and 1x1
  ];

  // TODO : control grid options
  const gridOption = {
    "minRow": 2,
    "cellHeight": "7rem",
    //"cellHeight" : 'initial', // start square but will set to % of window width later
    "animate": false, // show immediate (animate : true is nice for user dragging though)
    "disableOneColumnMode": true, // will manually do 1 column
    "float": true
  };
  let grid = GridStack.init(gridOption);
  grid.load(widgetItems);

  //Add saving events
  let gridStackItems = document.getElementsByClassName('grid-stack-item')
  for (let i = 0; i < gridStackItems.length; i++) {
    gridStackItems[i].onmouseleave = function () {
      addWidgetSavingEvent(gridStackItems[i]);
    }
  }

  return grid;
}

function addWidgetSavingEvent (widget) {
  widget.onmouseleave = function () {
    saveWidget(widget);
  }
}

function addWidget (grid) {
  console.log('add widget');
  let widget = grid.addWidget('<div class="grid-stack-item" id="0"><div class="grid-stack-item-content"></div></div>', {w: 3});
  saveWidget(widget);
  addWidgetSavingEvent(widget);
}

function saveWidget (widget) {

  console.log('saving widget');
  console.log('id: ' + widget.id);
  console.log('width: ' + widget.getAttribute('gs-w'));
  console.log('height: ' + widget.getAttribute('gs-h'));
  console.log('x: ' + widget.getAttribute('gs-x'));
  console.log('y: ' + widget.getAttribute('gs-y'));

  let widgetData = JSON.stringify({
    "id": widget.id + "",
    "w": widget.getAttribute('gs-w'),
    "h": widget.getAttribute('gs-h'),
    "x": widget.getAttribute('gs-x'),
    "y": widget.getAttribute('gs-y'),
  })

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
      widget.id = widgetResponse.id;
    })
    .catch((error) => {
      console.log('error saving widget, detail bellow');
      console.log(error);
    })


}