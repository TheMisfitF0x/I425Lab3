/***********************************************************************************************************
 ******                            Show Products                                                         ******
 **********************************************************************************************************/
//This function shows all products. It gets called when a user clicks on the Products link in the nav bar.

// Pagination, sorting, and limiting are disabled
function showOrders (offset = 0) {
    console.log('show all orders');
    // const url = baseUrl_API + '/messages';

    //if the selection list exists, retrieve the selected option value; otherwise, set a default value.
    let limit = ($("#order-limit-select").length) ? $('#order-limit-select option:checked').val() : 5;
    let sort = ($("#order-sort-select").length) ? $('#order-sort-select option:checked').val() : "id:asc";
    //construct the request url
    const url = baseUrl_API + '/orders?limit=' + limit + "&offset=" + offset + "&sort=" + sort;
    //define AXIOS request
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            displayOrders(response.data);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}

//Callback function: display all posts; The parameter is a promise returned by axios request.
function displayOrders (response) {
    //console.log(response);
    let _html;
    _html =
        "<div class='content-row content-row-header'>" +
        "<div class='order-id'>Order ID</></div>" +
        "<div class='order-warehouse_id'>Warehouse ID</></div>" +
        "<div class='order-product_id'>Product ID</></div>" +
        "<div class='order-cost'>Cost</></div>" +
        "<div class='order-user_id'>User ID</></div>" +
        "<div class='order-date'>Date Created</></div>" +
        "</div>";
    let orders = response.data;
    orders.forEach(function(order, x){
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += "<div class='" + cssClass + "'>" +
            "<div class='order-id'>" + order.id + "</div>" +
            "<div class='order-warehouse_id'>" + order.warehouse_id + "</div>" +
            "<div class='order-product_id'>" + order.product_id + "</div>" +
            "<div class='order-cost'>$" + order.cost + "</div>" +
            "<div class='order-user_id'>" + order.user_id + "</div>" +
            "<div class='order-date'>" + order.date_created + "</div>" +
            "</div>" +
            "<div class='container order-detail' id='order-detail-" + order.id + "' style='display: none'></div>";
    });

    //Add a div block for pagination links and selection lists for limiting and sorting courses
    _html += "<div class='content-row course-pagination'><div>";
//pagination
    _html += paginateOrders(response);
//items per page
    _html += limitOrders(response);
//sorting
    _html += sortOrders(response);
//end the div block
    _html += "</div></div>";

    //Finally, update the page
    updateMain('Orders', 'All Orders', _html);

}


/***************************************************************************
 *************************
 ********* This function handles errors occurred by an
 AXIOS request. **********
 ****************************************************************************
 ***********************/
function handleAxiosError(error) {
    let errMessage;
    if (error.response) {
// The request was made and the server responded with a status code of 4xx or 5xx
        errMessage = {"Code": error.response.status, "Status":
            error.response.data.status};
    } else if (error.request) {
// The request was made but no response was received
        errMessage = {"Code": error.request.status, "Status":
            error.request.data.status};
    } else {
// Something happened in setting up the request that triggered an error
        errMessage = JSON.stringify(error.message, null, 4);
    }
    showMessage('Error', errMessage);
}

/*******************************************************************************
 *********************
 ********* Paginating, sorting, and limiting orders
 **********
 ********************************************************************************
 *******************/
//paginate all messages
function paginateOrders(response) {
    //calculate the total number of pages
    let limit = response.limit;
    let totalCount = response.totalCount;
    let totalPages = Math.ceil(totalCount / limit);
    //determine the current page showing
    let offset = response.offset;
    let currentPage = offset / limit + 1;
    //retrieve the array of links from response json
    let links = response.links;
    //convert an array of links to JSON document. Keys are "self", "prev","next", "first", "last"; values are offsets.
    let pages = {};
    //extract offset from each link and store it in pages
    links.forEach(function (link) {
        let href = link.href;
        let offset = href.substr(href.indexOf('offset') + 7);pages[link.rel] = offset;
    });
    if (!pages.hasOwnProperty('prev')) {
        pages.prev = pages.self;
    }
    if (!pages.hasOwnProperty('next')) {
        pages.next = pages.self;
    }
    //generate HTML code for links
    let _html = `Showing Page ${currentPage} of ${totalPages}&nbsp;&nbsp;&nbsp;&nbsp;
        <a href='#order' title="first page"
        onclick='showOrders(${pages.first})'> << </a>
        <a href='#order' title="previous page"
        onclick='showOrders(${pages.prev})'> < </a>
        <a href='#order' title="next page"
        onclick='showOrders(${pages.next})'> > </a>
        <a href='#order' title="last page"
        onclick='showOrders(${pages.last})'> >> </a>`;
    return _html;
}

//limit messages
function limitOrders(response) {
//define an array of courses per page options
    let ordersPerPageOptions = [5, 10, 20];
//create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='order-limit-select' onChange='showOrders()'>`;
    ordersPerPageOptions.forEach(function (option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;
    });
    _html += "</select>";
    return _html;
}

//sort messages
function sortOrders(response) {
//create selection list for sorting
    let sort = response.sort;
//sort field and direction: convert json to a string then remove {, }, and "
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");
    console.log(sortString);
//define a JSON containing sort options
    let sortOptions = {
        "id:asc": "First Order ID -> Last Order ID",
        "id:desc": "Last Order ID -> First Order ID",
        "warehouse_id:asc": "First Warehouse ID -> Last Warehouse ID",
        "warehouse_id:desc": "Last Warehouse ID -> First Warehouse ID",
        "product_id:asc": "First Product ID -> Last Product ID",
        "product_id:desc": "Last Product ID -> First Product ID",
    };
//create the selection list
    let _html = "&nbsp;&nbsp;&nbsp;&nbsp; Sort by: <select id='order-sort-select'" + "onChange='showOrders()'>";
    for (let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value='${option}'>${sortOptions[option]}</option>`;
    }
    _html += "</select>";
    return _html;
}