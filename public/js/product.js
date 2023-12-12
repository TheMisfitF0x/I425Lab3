/***********************************************************************************************************
 ******                            Show Products                                                         ******
 **********************************************************************************************************/
//This function shows all products. It gets called when a user clicks on the Products link in the nav bar.

// Pagination, sorting, and limiting are disabled
function showProducts (offset = 0) {
    console.log('show all products');
    // const url = baseUrl_API + '/messages';

    //if the selection list exists, retrieve the selected option value; otherwise, set a default value.
    let limit = ($("#product-limit-select").length) ? $('#product-limit-select option:checked').val() : 5;
    let sort = ($("#product-sort-select").length) ? $('#product-sort-select option:checked').val() : "id:asc";
    //construct the request url
    const url = baseUrl_API + '/products?limit=' + limit + "&offset=" + offset + "&sort=" + sort;
    //define AXIOS request
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            displayProducts(response.data);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}

//Callback function: display all posts; The parameter is a promise returned by axios request.
function displayProducts (response) {
    //console.log(response);
    let _html;
    _html =
        "<div class='content-row content-row-header'>" +
        "<div class='product-id'>Product ID</></div>" +
        "<div class='product-name'>Product Name</></div>" +
        "<div class='product-desc'>Description</div>" +
        "<div class='product-weight'>Weight</div>" +
        "<div class='product-count'>Count</div>" +
        "<div class='product-cost'>Cost</div>" +
        "<div class='product-warehouse-id'>Warehouse ID</div>" +
        "</div>";
    let products = response.data;
    products.forEach(function(product, x){
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += "<div class='" + cssClass + "'>" +
            "<div class='product-id'>" + product.id + "</div>" +
            "<div class='product-name'>" + product.product_name + "</div>" +
            "<div class='product-desc'>" + product.product_desc + "</div>" +
            "<div class='product-weight'>" + product.product_weight + "</div>" +
            "<div class='product-count'>" + product.product_count + "</div>" +
            "<div class='product-cost'>$" + product.product_cost + "</div>" +
            "<div class='product-warehouse-id'>" + product.warehouse_id + "</div>" +
            "</div>" +
            "<div class='container product-detail' id='product-detail-" + product.product_id + "' style='display: none'></div>";
    });

    //Add a div block for pagination links and selection lists for limiting and sorting courses
    _html += "<div class='content-row course-pagination'><div>";
//pagination
    _html += paginateProducts(response);
//items per page
    _html += limitProducts(response);
//sorting
    _html += sortProducts(response);
//end the div block
    _html += "</div></div>";

    //Finally, update the page
    updateMain('Products', 'All Products', _html);

}


// /***********************************************************************************************************
//  ******                            Show Comments made for a message                                   ******
//  **********************************************************************************************************/
// /* Display all comments. It get called when a user clicks on a message's id number in
//  * the message list. The parameter is the message id number.
// */
// function showComments(number) {
//     console.log('get a message\'s all comments');
//     let url = baseUrl_API + '/messages/' + number + '/comments';
//     axios({
//         method: 'get',
//         url: url,
//         cache: true,
//         headers: {"Authorization": "Bearer " + jwt}
//     })
//         .then(function (response) {
// //console.log(response.data);
//             displayComments(number, response);
//         })
//         .catch(function (error) {
//             handleAxiosError(error);
//         });
// }


// // Callback function that displays all details of a course.
// // Parameters: course number, a promise
// function displayComments(number, response) {
//     let _html = "<div class='content-row content-row-header'>Comments</div>";
//     let comments = response.data;
//     //console.log(number);
//     //console.log(comments);
//     comments.forEach(function(comment, x){
//         _html +=
//             "<div class='post-detail-row'><div class='post-detail-label'>Comment ID</div><div class='post-detail-field'>" + comment.id + "</div></div>" +
//             "<div class='post-detail-row'><div class='post-detail-label'>Comment Body</div><div class='post-detail-field'>" + comment.body + "</div></div>" +
//             "<div class='post-detail-row'><div class='post-detail-label'>Create Time</div><div class='post-detail-field'>" + comment.created_at + "</div></div>";
//     });
//
//     $('#post-detail-' + number).html(_html);
//     $("[id^='post-detail-']").each(function(){   //hide the visible one
//         $(this).not("[id*='" + number + "']").hide();
//     });
//
//     $('#post-detail-' + number).toggle();
// }

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
 ********* Paginating, sorting, and limiting products
 **********
 ********************************************************************************
 *******************/
//paginate all messages
function paginateProducts(response) {
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
        <a href='#product' title="first page"
        onclick='showProducts(${pages.first})'> << </a>
        <a href='#product' title="previous page"
        onclick='showProducts(${pages.prev})'> < </a>
        <a href='#product' title="next page"
        onclick='showProducts(${pages.next})'> > </a>
        <a href='#product' title="last page"
        onclick='showProducts(${pages.last})'> >> </a>`;
    return _html;
}

//limit messages
function limitProducts(response) {
//define an array of courses per page options
    let productsPerPageOptions = [5, 10, 20];
//create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='product-limit-select' onChange='showProducts()'>`;
    productsPerPageOptions.forEach(function (option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;
    });
    _html += "</select>";
    return _html;
}

//sort messages
function sortProducts(response) {
//create selection list for sorting
    let sort = response.sort;
//sort field and direction: convert json to a string then remove {, }, and "
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");
    console.log(sortString);
//define a JSON containing sort options
    let sortOptions = {
        "id:asc": "First Product ID -> Last Product ID",
        "id:desc": "Last Product ID -> First Product ID",
        "product_name:asc": "Product name A -> Z",
        "product_name:desc": "Product name Z -> A"
    };
//create the selection list
    let _html = "&nbsp;&nbsp;&nbsp;&nbsp; Sort by: <select id='product-sort-select'" + "onChange='showProducts()'>";
    for (let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value='${option}'>${sortOptions[option]}</option>`;
    }
    _html += "</select>";
    return _html;
}

