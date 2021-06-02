
let resultsLoader = false;

function click5_sitemap_notification(type, msg, timeout = 3500) {
  let curElement = document.getElementById('click5_sitemap_notification');
  if (curElement) {
    curElement.remove();
    setTimeout(() => {
      let notificationElement = document.createElement('div');
      notificationElement.setAttribute('id', 'click5_sitemap_notification');
      notificationElement.className = type;
      notificationElement.innerHTML = '<span>' + msg + '</span>';

      document.querySelector('body').appendChild(notificationElement);
      notificationElement.style.opacity = '1';
      setTimeout(() => {
        notificationElement.opacity = '0';
        setTimeout(() => {
          notificationElement.remove();
        }, 300);
      }, timeout);
    }, 500);
  } else {
    let notificationElement = document.createElement('div');
    notificationElement.setAttribute('id', 'click5_sitemap_notification');
    notificationElement.className = type;
    notificationElement.innerHTML = '<span>' + msg + '</span>';

    document.querySelector('body').appendChild(notificationElement);
    notificationElement.style.opacity = '1';
    setTimeout(() => {
      notificationElement.opacity = '0';
      setTimeout(() => {
        notificationElement.remove();
      }, 300);
    }, timeout);
  }
}


function hasElementsArr(arr) {
  if (!arr) {
    return false;
  }

  if (!arr.length) {
    return false;
  }

  return true;
}


function toggleLoader(loaderElement = 'loader_results' , toggle_type = undefined) {
  if (toggle_type === undefined) {
    resultsLoader = !resultsLoader;
  } else {
    resultsLoader = toggle_type;
  }
  document.getElementById(loaderElement).style.display = resultsLoader ? 'flex' : 'none';
}

function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this, args = arguments;
    var later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

function hasParameter(param) {
  return window.location.href.indexOf(param) >= 0
}

function getRequest(url, callback) {
  const authenticationObj = {
    token: document.querySelector('#verification_token').value,
    user: document.querySelector('#user_identificator').value
  };

  var xhr = new XMLHttpRequest();
  xhr.open('GET', url);
  xhr.setRequestHeader('token', authenticationObj.token);
  xhr.setRequestHeader('user', authenticationObj.user);
  xhr.onload = function () {
    if (xhr.status === 200) {
      let resObject = [];
      try {
        resObject = JSON.parse(xhr.responseText);
        if (resObject) {
          if (resObject.notification) {
            click5_sitemap_notification(resObject.type, resObject.message, 3500);
            return;
          }
        }
      } catch (e) {

      }
      callback(resObject);
    }
    else {
    }
  };
  xhr.send();
}

function getRequestNoAuth(url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', url);
  xhr.setRequestHeader('token', authenticationObj.token);
  xhr.setRequestHeader('user', authenticationObj.user);
  xhr.onload = function () {
    if (xhr.status === 200) {
      let resObject = [];
      try {
        resObject = JSON.parse(xhr.responseText);
        if (resObject) {
          if (resObject.notification) {
            click5_sitemap_notification(resObject.type, resObject.message, 3500);
            return;
          }
        }
      } catch (e) {

      }
      callback(resObject);
    }
    else {
    }
  };
  xhr.send();
}

function postRequestJSON(url, object, callback) {
  const authenticationObj = {
    token: document.querySelector('#verification_token').value,
    user: document.querySelector('#user_identificator').value
  };

  var xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.setRequestHeader('token', authenticationObj.token);
  xhr.setRequestHeader('user', authenticationObj.user);
  xhr.setRequestHeader('Content-type', 'application/json;charset=UTF-8');
  xhr.onload = function () {
    if (xhr.status === 200) {
      let resObject = [];
      try {
        resObject = JSON.parse(xhr.responseText)
        if (resObject) {
          if (resObject.notification) {
            click5_sitemap_notification(resObject.type, resObject.message, 3500);
            return;
          }
        }
      } catch (e) {

      }
      callback(resObject);
    }
    else {
    }
  };
  xhr.send(JSON.stringify(object));
}

function postRequest(url, params, callback) {
  const authenticationObj = {
    token: document.querySelector('#verification_token').value,
    user: document.querySelector('#user_identificator').value
  };

  var xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('token', authenticationObj.token);
  xhr.setRequestHeader('user', authenticationObj.user);

  //Send the proper header information along with the request
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function () {//Call a function when the state changes.
    if (xhr.readyState == 4 && xhr.status == 200) {
      let resObject = [];
      try {
        resObject = JSON.parse(xhr.responseText);
        if (resObject) {
          if (resObject.notification) {
            click5_sitemap_notification(resObject.type, resObject.message, 3500);
            return;
          }
        }
      } catch (e) {

      }
      callback(resObject);
    }
    else {
    }
  }
  xhr.send(params);
}

const constructListElementBl = (el) => {
  let html = '';

  html += '<span><a href="' + el.url + '" target="_blank">[' + el.post_type + '] ' + el.post_title + '</a></span>';

  html += '<a href="#" class="click5_sitemap_remove_from_bl click5_sitemap_float_right" data-value="' + el.ID + '">Un-Blacklist</a>';

  return html;
}

const constructListElementResults = (el) => {
  let html = '';

  html += '<span><a href="' + el.url + '" target="_blank">[' + el.post_type + '] ' + el.post_title + '</a></span>';

  html += '<a href="#" class="click5_sitemap_addToBlacklist click5_sitemap_float_right" data-value="' + JSON.stringify({ ID: el.ID, post_title: el.post_title, post_type: el.post_type }).replace(new RegExp('"', 'g'), "'") + '">Add to Blacklist</a>';

  return html;
}

const addToBlacklistedSingleItem = (el, type = 'html') => {
  let itemElement = document.createElement('li');
  itemElement.innerHTML = constructListElementBl(el);

  let listElement = document.querySelector('#click5_sitemap_already_blacklisted');
  listElement.append(itemElement);

  //scroll to bottom
  listElement.scrollTop = listElement.scrollHeight;

  if (listElement.childNodes.length) {
    document.getElementById('btnClearBlacklist').style.display = 'initial';
  }

  itemElement.querySelector('a.click5_sitemap_remove_from_bl').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    let idToRemove = parseInt(this.getAttribute("data-value"));



    getRequest((type == 'html' ? c5resturl.wpjson + 'click5_sitemap/API/unblacklist?ID=' : c5resturl.wpjson + 'click5_sitemap/API/get_seo_unblock?ID=') + idToRemove, (data) => {
      if (data !== false) {
        click5_sitemap_notification('success', 'Blacklist saved.', 2000);
        this.parentElement.remove();
        let inputSearch = document.querySelector('#page_search');
        let selectType = document.querySelector('#page_type');
        let hiddenAllTypes = document.querySelector('#all_types');
        searchFunc(inputSearch, selectType, hiddenAllTypes);
      }
      const event = new Event('blacklist_updated');
      document.dispatchEvent(event);
    });
  });
}

const loadBlacklist = (type = 'html') => {
  toggleLoader('loader_blacklisted', true);
  getRequest(type == 'html' ? c5resturl.wpjson + 'click5_sitemap/API/get_blacklisted' : c5resturl.wpjson + 'click5_sitemap/API/get_seo_block_list', (data) => {
    try {
      let items = JSON.parse(data);
      toggleLoader('loader_blacklisted', false);
      items.forEach(el => {
        addToBlacklistedSingleItem(el, type);
      });
    } catch (e) {
      click5_sitemap_notification('error', 'Couldn\'t load blacklist.', 2000);
    }
  });
}


const searchFunc = (inputSearch = document.querySelector('#page_search'), selectType = document.querySelector('#page_type'), hiddenAllTypes = document.querySelector('#all_types'), type = 'html') => {
  let searchQuery = inputSearch.value.trim();
  let searchType = selectType.value.trim();
  let allTypes = hiddenAllTypes.value.trim();

  const url = c5resturl.wpjson + 'click5_sitemap/API/request_pages?search=' + searchQuery + '&type=' + searchType + '&all_types=' + allTypes + '&type_tab=' + type;


  getRequest(url, (data) => {
    let results = document.querySelectorAll('#click5_sitemap_blacklist_container ul#results > li');
    if (results) {
      results.forEach(el => el.remove());
      toggleLoader('loader_results', true);
    }
    let listElement = document.querySelector('#click5_sitemap_blacklist_container ul#results');
    toggleLoader('loader_results', false);

    if (!data) {
      return;
    }

    if (!data.length) {
      return;
    }

    data.forEach(el => {
      let itemElement = document.createElement('li');
      itemElement.innerHTML = constructListElementResults(el);
      listElement.append(itemElement);
    });
    let blacklistContainer = document.querySelector('#click5_sitemap_blacklist_container');

    blacklistContainer.append(listElement);

    let addToBlacklistBtn = document.querySelectorAll('#click5_sitemap_blacklist_container ul#results > li > a.click5_sitemap_addToBlacklist');

    addToBlacklistBtn.forEach(el => {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const dataValue = this.getAttribute("data-value").replace(new RegExp("'", 'g'), '"');

        const newBlItem = JSON.parse(dataValue);
        postRequest(type == 'html' ? c5resturl.wpjson + 'click5_sitemap/API/add_to_blacklisted' : c5resturl.wpjson + 'click5_sitemap/API/seo_block_page', `ID=${newBlItem.ID}&post_title=${newBlItem.post_title}&post_type=${newBlItem.post_type}`, (data) => {
          try {
            addToBlacklistedSingleItem(JSON.parse(data), type);
            this.parentElement.remove();
            click5_sitemap_notification('success', 'Blacklist saved.', 2000);
          } catch (err) {
            click5_sitemap_notification('error', 'Something went wrong.', 2000);
          }
          const event = new Event('blacklist_updated');
          document.dispatchEvent(event);
        });
      });
    });

  })
}