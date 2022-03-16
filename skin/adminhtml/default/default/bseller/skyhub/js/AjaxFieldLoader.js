function renderAttributeOptions(magentoAttributeId, mappingAttributeId, areaToLoad, url) {
    var params = {magentoAttributeId: magentoAttributeId, mappingAttributeId: mappingAttributeId};
    new Ajax.Request(url, {
        method: 'POST',
        parameters: params,
        onSuccess: function (transport) {
            $(areaToLoad).update(transport.responseText);
        },
        onFailure: function () {
            alert('There was an error mapping the attribute options. Please, reload the page and try again.');
            return false;
        }.bind(this)
    });
}