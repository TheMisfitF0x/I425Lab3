//This function shows all users. It gets called when a user clicks on the Users link in the nav bar.
function showWarehouses() {
    console.log('show all the warehouses');
    const url = baseUrl_API + '/warehouses';
    $.ajax({
        url: url,
        headers: {'Authorization': `Bearer ${jwt}`}
    }).done(function (data) {
        displayWarehouses(data);
    }).fail(function (jqXHR, textStatus) {
        let error = {'code': jqXHR.staus,
            'status':jqXHR.responseJson.status};
        showMessage('Error', JSON.stringify(error, null, 4));
    });
}


//Callback function: display all users; The parameter is an array of user objects.
function displayWarehouses(warehouses) {
    let _html;
    _html = `<div class='content-row content-row-header'>
        <div class='warehouse-id'>ID</div>
        <div class='warehouse-location'>Location</div>
        <div class='warehouse-leasenum'>Lease Number</div>
        <div class='warehouse-sqft'>Square Footage</div>
        <div class='warehouse-monthlycost'>Monthly Cost</div>
        </div>`;
    for (let x in warehouses) {
        let warehouse = warehouses[x];
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += `<div id='content-row-${warehouse.id}' class='${cssClass}'>
            <div class='warehouse-id'>
                <span class='list-key' data-warehouse='${warehouse.id}' 
                     onclick=showWarehouseOrdersPreview('${warehouse.id}') 
                     title='Get orders made for the warehouse'>${warehouse.id}
                </span>
            </div>
            <div class='warehouse-location'>${warehouse.location}</div>
            <div class='warehouse-leasenum'>${warehouse.lease_num}</div>
            <div class='warehouse-sqft'>${warehouse.sqft}</div>
            <div class='warehouse-monthlycost'>$${warehouse.monthly_cost}</div>            
            </div>`;
    }
    //Finally, update the page
    updateMain('Warehouses', 'All Warehouses', _html);
}