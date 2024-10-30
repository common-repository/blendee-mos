var _sbnaq = _sbnaq || [];
(function($) {
    
    if (typeof sbnData !== 'undefined') {
        var documentTitle = sbnData.documentTitle || '';
        var siteId = sbnData.siteId || '';
        var catalogId = sbnData.catalogId || '';
        var language = sbnData.language || '';
        var pageType = sbnData.pageType || '';
        var userIdExt = sbnData.userIdExt || '';
        var itemId = sbnData.itemId || '';

        _sbnaq.push(['setDocumentTitle', documentTitle]);
        _sbnaq.push(['setSiteId', siteId]);
        _sbnaq.push(['setCatalogId', catalogId]);

        if (userIdExt) {
            _sbnaq.push(['setSiteUserId', userIdExt]);
        }

        _sbnaq.push(['setLanguage', language]);
        _sbnaq.push(['setPageType', pageType]);

        if (pageType === '102') {
            _sbnaq.push(['trkCategoryView', itemId]);
            _sbnaq.push(['setCategoryId', itemId]);
        } else if (pageType === '103') {
            _sbnaq.push(['trkProductView', itemId]);
            _sbnaq.push(['setProductId', itemId]);
        }

        if (navigator.permissions) {
            navigator.permissions.query({ name: 'geolocation' }).then(function(geoPermission) {
                var geoFlag = geoPermission.state === 'granted';
                _sbnaq.push(['setGeoDevice', geoFlag]);
            });
        }

        if (sbnData.newUserData) {
            var newUserData = sbnData.newUserData;
            _sbnaq.push(['trkUserRegistration', newUserData.userId, newUserData.userEmail]);
        }

        _sbnaq.push(['trkPageView']);
    }
})(jQuery);
