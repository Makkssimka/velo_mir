function doesFileExist(urlToFile) {
  var xhr = new XMLHttpRequest();
  xhr.open('HEAD', urlToFile, false);
  xhr.send();
   
  if (xhr.status == "404") {
      return false;
  } else {
      return true;
  }
}

function reloadRobotsTxt (callback = undefined) {

  document.getElementById('click5_sitemap_robots_txt_container').style.display = 'none';
  toggleLoader('loader_status_robots', true);
  getRequest(c5resturl.wpjson + 'click5_sitemap/API/print_robots_txt', (data) => {

      document.getElementById('click5_sitemap_robots_txt_container').innerHTML = data;
      document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
      toggleLoader('loader_status_robots', false);
      if (callback !== undefined) {
        callback();
      }

      Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
      };

      //if(Object.size(data) == 0){

        var robots_file = c5homeurl.home + '/robots.txt?t='+jQuery.now();
        if (doesFileExist(robots_file)) {
          //console.log('exist');

          jQuery.get(robots_file, function(data) {

            document.getElementById('click5_sitemap_robots_txt_container').innerHTML = '<a href="'+c5homeurl.home+'/robots.txt" target="_blank" rel="nofollow">'+c5homeurl.home+'/robots.txt</a><textarea rows="7" disabled="disabled" style="margin-top: 15px; resize: none;">'+data+'</textarea>';

            document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
          }, 'text');
  
          toggleLoader('loader_status_robots', false);
    
        } else {
          //console.log('not exist');
    
          document.getElementById('click5_sitemap_robots_txt_container').innerHTML = 'robots.txt not generated yet.';
          document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
          toggleLoader('loader_status_robots', false);
        }
      //}

  });

}

function reloadSitemapsLinks(callback = undefined) {
  document.querySelectorAll('#click5_sitemap_url_container a.click5_sitemap_urls').forEach(el => {
    el.remove();
  })
  toggleLoader('loader_status_sitemap', true);
  
  getRequest(c5resturl.wpjson + 'click5_sitemap/API/print_sitemap_urls', (data) => {
    toggleLoader('loader_status_sitemap', false);
    let parsedData = data;
    if (parsedData.length) {
      try {
        document.querySelector('p.sitemap_not_gen').remove();

        //console.log('xml generated');
      } catch (e) {
        
      }
      parsedData.forEach(el => {
        document.getElementById('click5_sitemap_url_container').innerHTML += '<a href="' + el + '" style="display: block; width: 100%;" target="_blank" class="click5_sitemap_urls">' + el + '</a>';
      });
      if (callback !== undefined) {
        callback();
      }
    } else {
      //console.log('xml not generated');

      document.getElementById('click5_sitemap_url_container').innerHTML = '<p class="sitemap_not_gen" style="width: 100%;">sitemap.xml not generated yet.</p>';
      
    }
  });
}

const checkSetting = (settings, setting_name) => {
  let result = undefined
  settings.forEach(setting => {
    if (setting.name == setting_name) {
      result = setting.value;
      return;
    }
  });
  return result;
}

const reGenerateButton = () => {
  document.getElementById('click5-ajax-loader').style.display = 'inline-block';

      let settings = [];
      document.querySelectorAll('#ajaxable select').forEach(el => {
        settings.push({name: el.getAttribute('name'), value: el.value });
      });
      document.querySelectorAll('#ajaxable input[type="checkbox"]').forEach(el => {
        settings.push({ name: el.getAttribute('name'), value: el.checked });
      });

      postRequestJSON(c5resturl.wpjson + 'click5_sitemap/API/generate_manual', {options: settings}, (data) => {
        document.getElementById('click5-ajax-loader').style.display = 'none';
        let enabledXML = checkSetting(settings, "click5_sitemap_seo_sitemap_xml");
        let enabledRobots = checkSetting(settings, "click5_sitemap_seo_robots_txt");

        //console.log(enabledXML);

        //if (enabledXML) {
          reloadSitemapsLinks(() => {
            if (!enabledRobots) {
              click5_sitemap_notification('success', 'Sitemap XML updated.', 2000);
            }

            jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', false);
          });
        //}
        
        //if (enabledRobots) {
          reloadRobotsTxt(() => {
            if (enabledXML) {
              click5_sitemap_notification('success', 'Sitemap XML & robots.txt updated.', 2000);
            } else {
              click5_sitemap_notification('success', 'robots.txt updated.', 2000);
            }
            jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', false);
          });

          

        //}
      })
}

(() => {
  document.addEventListener("DOMContentLoaded", function (event) {
    if (!hasParameter('&tab=seo')) {
      return;
    }

    toggleLoader('loader_results', true);
    toggleLoader('loader_blacklisted', true);

    let inputSearch = document.querySelector('#page_search');
    let selectType = document.querySelector('#page_type');
    let hiddenAllTypes = document.querySelector('#all_types');

    document.getElementById('btnClearBlacklist').addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (confirm('Are you sure you want delete all blacklisted pages?')) {
        getRequest(c5resturl.wpjson + 'click5_sitemap/API/get_seo_clear', (data) => {
          if (data == true) {
            document.querySelectorAll('#click5_sitemap_already_blacklisted > li').forEach(el => {
              el.remove();
            });
            loadBlacklist('seo');
            searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
            this.style.display = 'none';
            click5_sitemap_notification('success', 'Blacklist saved.', 2000);
            reGenerateButton();
          } else {
            click5_sitemap_notification('error', 'Something went wrong.', 2000);
          }
        });
      }

    });

    loadBlacklist('seo');

    document.addEventListener('blacklist_updated', function(e) {
      //console.log('blacklist_updated');
      reGenerateButton();
    })
    //loadSeoBlockedList();

    searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');


    selectType.addEventListener('change', function (e) {

      searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
    });
    inputSearch.addEventListener('input', debounce(function (e) {
      searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
    }, 300));

    document.getElementById('generate_btn').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      reGenerateButton();
    });

    document.querySelectorAll('#ajaxable select').forEach(el => {
      el.addEventListener('change', debounce(function(e) {
        reGenerateButton();
      }, 300));
    });
    document.querySelectorAll('#ajaxable input[type="checkbox"]').forEach(el => {
      el.addEventListener('change', debounce(function(e) {
        reGenerateButton();

        jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', true);

      }, 300));
    });
  });
})();