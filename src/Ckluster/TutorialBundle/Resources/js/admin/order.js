function OrderManager(element, nextUrl, errorUrl) {
    this.id = $('div.order-item-id', $(element)).text();
    this.nextUrl = nextUrl.replace('_id_', this.id);
    this.errorUrl = errorUrl.replace('_id_', this.id);
    this.element = element;
}

OrderManager.prototype._reportStage = function(url) {
    var that = this;

    $.get(
        url,
        function(status) {
            $('span.order-item-status-text', $(that.element)).text(status);
        }
    )
};

OrderManager.prototype.reportNextStage = function() {
    this._reportStage(this.nextUrl);
};

OrderManager.prototype.reportError = function() {
    this._reportStage(this.errorUrl);
};

function report(link, status) {
    var manager = $(link).parentsUntil('div.order-item').parent()[0].orderManager;
    if (status == 'next') {
        manager.reportNextStage();
    } else {
        manager.reportError();
    }
    return false;
}

window['__reportStage'] = report;

window['OrderManager'] = OrderManager;

