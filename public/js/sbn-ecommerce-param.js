(function($) {
    if (typeof sbnData !== 'undefined') {
        var currency = sbnData.currency || '';
        var newCartItems = sbnData.currentCartProducts || [];
        //var _sbnaq = _sbnaq || [];

        _sbnaq.push(['setCurrencyCode', currency]);

        if (sbnData.orderDetail !=null && sbnData.orderDetail.lines && sbnData.orderDetail.lines.length>0) {
            var orderDetail = sbnData.orderDetail || {};
            var orderProcessed = localStorage.getItem('lastProcessedOrderId');

            if (orderDetail.orderId && orderDetail.orderId !== orderProcessed && orderDetail.lines.length > 0) {
                orderDetail.lines.forEach(function(item) {
                    _sbnaq.push([
                        'trkProductSale',
                        orderDetail.orderId,
                        item.productId,
                        item.quantity,
                        item.coupon,
                        item.price,
                        item.priceWithTax,
                        item.deliveryPrice,
                        item.currency,
                        item.dateTime
                    ]);
                });
                localStorage.setItem('lastProcessedOrderId', orderDetail.orderId);
                localStorage.setItem('cartItems', JSON.stringify([]));
            }
        } else {
            var oldCartItems = [];
            if (localStorage.getItem('cartItems')) {
                oldCartItems = JSON.parse(localStorage.getItem('cartItems'));
            }
            if (newCartItems.length > 0) {
                _sbnaq.push(["setCtxParamProductIds", newCartItems.map(el => el.productId).join(',')]);
                _sbnaq.push(["setCtxParamProductQuantities", newCartItems.map(el => el.quantity).join(',')]);
            }
            var addedItems = newCartItems.filter(function(newItem) {
                return !oldCartItems.map(oldItem => oldItem.productId).includes(newItem.productId);
            });
            var removedItems = oldCartItems.filter(function(oldItem) {
                return !newCartItems.map(newItem => newItem.productId).includes(oldItem.productId);
            });
            addedItems.forEach(function(item) {
                _sbnaq.push(['trkProductBasketAdd', item.productId]);
            });
            removedItems.forEach(function(item) {
                _sbnaq.push(['trkProductBasketRemove', item.productId]);
            });
            localStorage.setItem('cartItems', JSON.stringify(newCartItems));
        }
    }
})(jQuery);
