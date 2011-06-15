function ProductCRUD(tableId, total, path, editPath, count) {
    this.count = count ? count : 10;
    this.total = total;
    this.path = path;
    this.editPath = editPath;
    this.page = 1;
    this.table = $(tableId.substr(0, 1) === '#' ? tableId : '#' + tableId);

    var that = this;
    this.table[0].setPage = function (page) {
        that.setPage(page);
    };

    var totalPages = this.getTotalPages();
    var tdLinks = $('<td>', { colspan: '4' });
    for (var i = 1; i <= totalPages; i++) {
        tdLinks.append($('<a>', { href: 'javascript:void(0)', onclick: 'javascript:__setPage(this, ' + i + ')' }).text(i.toString()));
    }
    
    $('tfoot', this.table).html('').append(tdLinks);
}

ProductCRUD.prototype.fillTable = function(products) {
    $('tbody', this.table).html('');
    var that = this;
    if (products.length === 0) {
        $('tbody', this.table).html('<tr><td colspan="4">No data</td></tr>');
        return;
    }
    
    $.each(products, function(index, value) {
        var id = value.id;
        $('<tr>')
            .append($('<td>').text(value.name))
            .append($('<td>').text(value.description))
            .append($('<td>').text(value.cost))
            .append(
                $('<td>')
                    .append($('<a>').attr('href', that.editPath.replace('_id_', id)).text('Edit'))
            )
            .appendTo($('tbody', that.table));
    });
};

ProductCRUD.prototype.getTotalPages = function() {
    return this.total / this.count + (this.total % this.count == 0 ? 0 : 1);
};

ProductCRUD.prototype.setPage = function(page) {
    if (page <= 0 || page > this.getTotalPages()) {
        return;
    }

    this.page = page;
    this.getData();
};

ProductCRUD.prototype.getData = function() {
    var offset = this.count * (this.page - 1);
    var getPath = this.path.replace('_offset_', offset.toString()).replace('_count_', this.count.toString());
    var that = this;
    $.get(
      getPath,
      function (products) {
          that.fillTable(products);
      }

    );
};

function setPage(link, page) {
    $(link).parentsUntil('table').parent()[0].setPage(page);
    return false;
}

window['ProductCRUD'] = ProductCRUD;
window['__setPage'] = setPage;
