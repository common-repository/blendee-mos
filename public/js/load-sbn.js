(function() {
	//var _sbnaq = _sbnaq || [];

    var u = 'https://' + trackingUrl + '/';
    _sbnaq.push(['setTrackerUrl', u]);
    var g = document.createElement('script');
    var s = document.getElementsByTagName('script')[0];
    g.type = 'text/javascript';
    g.defer = true;
    g.async = true;
    g.src = u + 'sbn.js';
    s.parentNode.insertBefore(g, s);
})();
