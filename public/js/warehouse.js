//This function shows all warehouses. It gets called when a user clicks on the Warehouses link in the nav bar.
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


//Callback function: display all warehouses; The parameter is an array of warehouse objects.
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

/***********************************************************************************************************
 ******                            Show Orders Within a Specific Warehouse                                 ******
 **********************************************************************************************************/
/* Display orders within a warehouse. It gets called when a user clicks on a warehouse's id in
 * the warehouse list. The parameter is the warehouse's id.
*/
//Display posts made by a user in a modal
function showWarehouseOrdersPreview(id) {
    console.log('preview a warehouse\'s orders');
    const url = baseUrl_API + '/warehouses/' + id + '/orders';
    const name = $("span[data-warehouse='" + id + "']").html();
    console.log(url);
    console.log(name);
    $.ajax({
        url: url,
        headers: {"Authorization": "Bearer " + jwt}
    }).done(function(data){
        displayWarehouseOrdersPreview(name, data);
    }).fail(function(xaXHR) {
        let error = {'Code': jqXHR.status,
            'Status':jqXHR.responseJSON.status};
        showMessage('Error', JSON.stringify(error, null, 4));
    });
}




// Callback function that displays all posts made by a user.
// Parameters: user's name, an array of Post objects
function displayWarehouseOrdersPreview(warehouse, orders) {
    let _html = "<div class='post_preview'>No orders were found.</div>";
    if (orders.length > 0) {
        _html = "<table class='order_preview'>" +
            "<tr>" +
            "<th class='order_preview-orderid'>Order ID</th>" +
            "<th class='order_preview-productid'>Product ID</th>" +
            "<th class='order_preview-productcost'>Cost</th>" +
            "<th class='order_preview-userid'>User ID</th>" +
            "<th class='order_preview-date'>Date Created</th>" +
            "</tr>";

        for (let x in orders) {
            let aOrder = orders[x];
            _html += "<tr>" +
                "<td class='order_preview-orderid'>" + aOrder.id + "</td>" +
                "<td class='order_preview-productid'>" + aOrder.product_id + "</td>" +
                "<td class='order_preview-productcost'>$" + aOrder.cost + "</td>" +
                "<td class='order_preview-userid'>" + aOrder.user_id + "</td>" +
                "<td class='order_preview-date'>" + aOrder.date_created + "</td>" +
                "</tr>"
        }
        _html += "</table>"
    }

    // set modal title and content
    $('#modal-title').html("Orders Within Warehouse " + warehouse);
    $('#modal-button-ok').hide();
    $('#modal-button-close').html('Close').off('click');
    $('#modal-content').html(_html);

    // Display the modal
    $('#modal-center').modal();
}