// Thanks for doing your part to liber(IE)t the world from IE6!
// Here's to the "Support but Discourage" approach :)
// Original copy written by Jonathan Howard.
// jon@StaringIsPolite.com // twitter.com/staringispolite
//
// GNU LGPL License v3
// SevenUp 0.3 is released into the wild under a GNU LGPL v3
//
// Browser sniffing technique lovingly adapted from http://www.thefutureoftheweb.com/
// Simple CSS Lightbox technique adapted equally lovingly from http://www.emanueleferonato.com/
// Go read their blogs :)

// Constructor technique advocated by Doug Crockford (of LSlint, JSON) in his recent Google tech talk.
var sevenUp = function() {
  // Define 'private vars' here.
	var osSupportsUpgrade = /(Windows NT 5.1|Windows NT 6.0|Windows NT 6.1|)/i.test(navigator.userAgent); // XP, Vista, Win7
  var options = {  // Change these to fit your color scheme via the 'options' arg for test().
    enableClosing: true,
    enableQuitBuggingMe: true,
    overlayColor: "#000000",  
    lightboxColor: "#ffffff",
    borderColor: "#6699ff",
    downloadLink: osSupportsUpgrade ? 
                  "http://www.microsoft.com/windows/internet-explorer" :
                  "http://getfirefox.com",
    overrideLightbox: false,
    lightboxHTML: null,
    showToAllBrowsers: false,
    usePlugin: false
  };
  function mergeInOptions(newOptions) {
    if (newOptions) {
      for (var i in options) {
        if (newOptions[i] !== undefined) {
          options[i] = newOptions[i];
        }
      }
    }
  }
  function isCookieSet() {
    if (document.cookie.length > 0) {
      var i = document.cookie.indexOf("sevenup=");
      return (i != -1);
    }
    return false;
  }
  
  // Return object literal and public methods here.
  return {
    // Hate to define CSS this way, but trying to keep to one file.
    // I'll keep it as pretty as possible.
    overlayCSS: function() {
      return "display: block; position: absolute; top: 0%; left: 0%;" +
      "width: 100%; height: 100%; background-color: " + options.overlayColor + "; " +
      "filter: alpha(opacity: 80); z-index:1001;";
    },
    lightboxCSS: function() {
      return "display: block; position: absolute; top: 25%; left: 25%; width: 50%; " +
      "padding: 16px; border: 8px solid " + options.borderColor + "; " +
      "background-color:" + options.lightboxColor + "; " +
      "z-index:1002; overflow: hidden;";
    },
    lightboxContents: function() {
      var html = options.lightboxHTML;
      if (!html) {
        html =
        "<div style='width: 100%; height: 95%'>" +
          "<h2 style='text-align: center;'>Your web browser is outdated and unsupported</h2>" +
          "<div class='upgrade_msg' style='text-align: center;'>" +
            "You can easily upgrade to the latest version at<br> " +
            "<a style='color: #0000EE' href='" + options.downloadLink + "'>" +
              options.downloadLink +
            "</a>" +
          "</div>" +
          "<h3 style='margin-top: 40px'>Why should I upgrade?</h3>" +
          "<ul>" +
            "<li><b>Websites load faster</b>, often double the speed of this older version</li>" +
            "<li><b>Websites look better</b>, so you see sites they way they were intended</li>" +
            "<li><b>Tabs</b> let you view multiple sites in one window</li>" +
            "<li><b>Safer browsing</b> with phishing protection</li>" +
          "</ul>" +
        "</div>";
        if (options.enableClosing) {
          html += "<div style='font-size: 11px; text-align: right;'>";
          html += options.enableQuitBuggingMe ?
          ("<a href='#' onclick='sevenUp.quitBuggingMe();' " +
              "style='color: #0000EE'>" +
              "Quit bugging me" +
          "</a>") :
          ("<a href='#' onclick='sevenUp.close();' " +
              "style='color: #0000EE'>" +
              "close" +
            "</a>");
          html += "</div>";
        }
      }
      return html;
    },
    test: function(newOptions, callback) {
      mergeInOptions(newOptions);
  	  if (!isCookieSet()) {
  	    // Write layer into the document.
  	    var layerHTML = "<div id='sevenUpCallbackSignal'></div>";
        if (options.overrideLightbox) {
          layerHTML += options.lightboxHTML;
        } else {
          layerHTML += "<div id='sevenUpOverlay' style='" + this.overlayCSS() + "'>" +
  	        "</div>" +
            "<div id='sevenUpLightbox' style='" + this.lightboxCSS() + "'>" +
              this.lightboxContents() +
            "</div>";
        }
        if (options.showToAllBrowsers !== true) {
          layerHTML = "<!--[if lt IE 7]>" + layerHTML + "<![endif]-->";
        }
        var layer = document.createElement('div');
        layer.innerHTML = layerHTML;
  	    document.body.appendChild(layer);
        // Fire callback.
        // I don't like this hack but IE6 seems to restrict dynamically created <script> tags to <head> only, 
        // and I don't see a way to add conditional comments around the script tag or its contents.
        // So for now, we write a 'signal' div inside the CC. If anyone has a better way please let me know.
        if (callback && document.getElementById('sevenUpCallbackSignal')) {
          callback(options);
        }
  	  }  
  	},
    quitBuggingMe: function() {
      var exp = new Date();
      exp.setTime(exp.getTime()+(7*24*3600000));
      document.cookie = "sevenup=dontbugme; expires="+exp.toUTCString();
      this.close();
    },
    close: function() {
      var overlay = document.getElementById('sevenUpOverlay');
      var lightbox = document.getElementById('sevenUpLightbox');
      if (overlay) { overlay.style.display = 'none'; }
      if (lightbox) { lightbox.style.display = 'none'; }
    },
    plugin: {}
  };
}();

