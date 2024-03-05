/**
 * Add or remove a track from the user's favorite tracks
 * @param node
 */
function loveTrack(node) {
  const idTrack = node.id.split('-')[3];

  if (node.classList.contains('loved-track')) {
    console.log('loved-track');
    node.classList.add('not-loved-track');
    node.classList.remove('loved-track');
    // node.firstElementChild.src = "../img/not-loved.svg";
  } else {
    console.log('not-loved-track');
    node.classList.add('loved-track');
    node.classList.remove('not-loved-track');
    // node.firstElementChild.src = "../img/loved.svg";
  }

  let url = '/myPage/myScrobbles/love/' + idTrack;
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
    .catch((error) => {
      console.error('error loving track, detail : ', error);
    })

}



function resetSearchBar(){
  $('#search_bar_artist').val('');
  $('#search_bar_album').val('');
  $('#search_bar_track').val('');
  $('#search_bar_from').val('');
  $('#search_bar_to').val('');

  $('#search-bar-submit').click();
}