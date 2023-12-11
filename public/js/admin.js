/***********************************************************************************************************
 ******                            Show All Products for Admin                                        ******
 **********************************************************************************************************/

//This function gets called when the Admin link in the nav bar is clicked. It shows all the records of products
function showAllProducts() {
    console.log('Show all products for admin.');
    //if the selection list exists, retrieve the selected option value; otherwise, set a default value.
    let limit = ($("#product-limit-select").length) ? $('#product-limit-select option:checked').val() : 5;
    let sort = ($("#product-sort-select").length) ? $('#product-sort-select option:checked').val() : "id:asc";
    //construct the request url
    const url = baseUrl_API + '/products?limit=' + limit + "&offset=" + offset + "&sort=" + sort;
    fetch(url, {
        method: 'GET',
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(checkFetch)
        .then(response => response.json())
        .then(products => displayAllProducts(products.data))
        .catch(err => showMessage("Errors", err)) //display errors
}


//Callback function that shows all the messages. The parameter is an array of messages.
// The first parameter is an array of messages and second parameter is the subheading, defaults to null.
function displayAllProducts(products, subheading=null) {
    console.log("display all message for the editing purpose")

    // search box and the row of headings
    let _html = `<div style='text-align: right; margin-bottom: 3px'>
            <input id='search-term' placeholder='Enter search terms'> 
            <button id='btn-product-search' onclick='searchProducts()'>Search</button></div>
            <div class='content-row content-row-header'>
            <div class='product-id'>Product ID</></div>
            <div class='product-name'>Product Name</></div>
            <div class='product-desc'>Description</div>
            <div class='product-weight'>Weight</div>
            <div class='product-count'>Count</div>
            <div class='product-warehouse-id'>Warehouse ID</div>
            </div>`;  //end the row

    // content rows
    for (let x in products) {
        let product = products[x];
        _html += `<div class='content-row'>
            <div class='product-id'>${product.id}</div>
            <div class='product-name' id='product-edit-name-${product.id}'>${product.product_name}</div> 
            <div class='product-desc' id='post-edit-desc-${product.id}'>${product.product_desc}</div>
            <div class='product-weight' id='post-edit-weight-${product.id}'>${product.product_weight}</div> 
            <div class='product-count' id='product-edit-count-${product.id}'>${post.product_count}</div>
            <div class='product-warehouse-id' id='product-edit-warehouse_id-${product.id}'>${post.warehouse_id}</div>`;

        _html += `<div class='list-edit'><button id='btn-product-edit-${product.id}' onclick=editProduct('${product.id}') class='btn-light'> Edit </button></div>
            <div class='list-update'><button id='btn-product-update-${product.id}' onclick=updateProduct('${product.id}') class='btn-light btn-update' style='display:none'> Update </button></div>
            <div class='list-delete'><button id='btn-product-delete-${product.id}' onclick=deleteProduct('${product.id}') class='btn-light'>Delete</button></div>
            <div class='list-cancel'><button id='btn-product-cancel-${product.id}' onclick=cancelUpdateProduct('${product.id}') class='btn-light btn-cancel' style='display:none'>Cancel</button></div>`

        _html += '</div>';  //end the row
    }

    //the row of element for adding a new message

    _html += `<div class='content-row' id='product-add-row' style='display: none'> 
            <div class='product-id product-editable' id='product-new-product_id' contenteditable='true' content="Product ID"></div>
            <div class='product-name product-editable' id='product-new-product_name' contenteditable='true'></div>
            
            
            <div class='post-image post-editable' id='post-new-image_url' contenteditable='true'></div>
            <div class='list-update'><button id='btn-add-post-insert' onclick='addPost()' class='btn-light btn-update'> Insert </button></div>
            <div class='list-cancel'><button id='btn-add-post-cancel' onclick='cancelAddPost()' class='btn-light btn-cancel'>Cancel</button></div>
            </div>`;  //end the row

    // add new message button
    _html += `<div class='content-row post-add-button-row'><div class='post-add-button' onclick='showAddRow()'>+ ADD A NEW MESSAGE</div></div>`;

    //Finally, update the page
    subheading = (subheading == null) ? 'All Messages' : subheading;
    updateMain('Messages', subheading, _html);
}

/***********************************************************************************************************
 ******                            Search Messages                                                    ******
 **********************************************************************************************************/
function searchPosts() {
    console.log('searching for messages');
    let term = $("#search-term").val();
//console.log(term);
    const url = baseUrl_API + "/messages?q=" + term;
    let subheading = '';
//console.log(url);
    if (term == '') {
        subheading = "All Messages";
    } else if (isNaN(term)) {
        subheading = "Messages Containing '" + term + "'"
    } else {
        subheading = "Messages whose ID is having" + term;
    }
//send the request
    fetch(url, {
        method: 'GET',
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(checkFetch)
        .then(response => response.json())
        .then(posts => displayAllPosts(posts))
        .catch(err => showMessage("Errors", err)) //display errors
}


/***********************************************************************************************************
 ******                            Edit a Message                                                     ******
 **********************************************************************************************************/

// This function gets called when a user clicks on the Edit button to make items editable
function editPost(id) {
    //Reset all items
    resetPost();

    //select all divs whose ids begin with 'post' and end with the current id and make them editable
    // $("div[id^='post-edit'][id$='" + id + "']").each(function () {
    //     $(this).attr('contenteditable', true).addClass('post-editable');
    // });

    $("div#post-edit-body-" + id).attr('contenteditable', true).addClass('post-editable');
    $("div#post-edit-image_url-" + id).attr('contenteditable', true).addClass('post-editable');
    $("div#post-edit-created_at-" + id).attr('contenteditable', true).addClass('post-editable');
    $("div#post-edit-updated_at-" + id).attr('contenteditable', true).addClass('post-editable');

    $("button#btn-post-edit-" + id + ", button#btn-post-delete-" + id).hide();
    $("button#btn-post-update-" + id + ", button#btn-post-cancel-" + id).show();
    $("div#post-add-row").hide();
}

//This function gets called when the user clicks on the Update button to update a message record
function updatePost(id) {
    console.log('update the message whose id is ' + id);
    let data = {};
    data['body'] = $("div#post-edit-body-" + id).html();
    data['image_url'] = $("div#post-edit-image_url-" + id).html();
    data['created_at'] = $("div#post-edit-created_at-" + id).html();
    data['updated_at'] = $("div#post-edit-updated_at-" + id).html();
    console.log(data);
    const url = baseUrl_API + "/messages/" + id;
    console.log(url);
    fetch(url, {
        method: 'PATCH',
        headers: {
            "Authorization": "Bearer " + jwt,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(checkFetch)
        .then(() => resetPost())
        .catch(error => showMessage("Errors", error))
}


//This function gets called when the user clicks on the Cancel button to cancel updating a message
function cancelUpdatePost(id) {
    showAllPosts();
}

/***********************************************************************************************************
 ******                            Delete a Message                                                   ******
 **********************************************************************************************************/

// This function confirms deletion of a message. It gets called when a user clicks on the Delete button.
function deletePost(id) {
    $('#modal-button-ok').html("Delete").show().off('click').click(function () {
        removePost(id);
    });
    $('#modal-button-close').html('Cancel').show().off('click');
    $('#modal-title').html("Warning:");
    $('#modal-content').html('Are you sure you want to delete the message?');

    // Display the modal
    $('#modal-center').modal();
}

// Callback function that removes a message from the system. It gets called by the deletePost function.
function removePost(id) {
    console.log('remove the message whose id is ' + id);
    let url = baseUrl_API + "/messages/" + id;
    fetch(url, {
        method: 'DElETE',
        headers: {"Authorization": "Bearer " + jwt,},
    })
        .then(checkFetch)
        .then(() => showAllPosts())
        .catch(error => showMessage("Errors", error))
}


/***********************************************************************************************************
 ******                            Add a Message                                                      ******
 **********************************************************************************************************/
//This function shows the row containing editable fields to accept user inputs.
// It gets called when a user clicks on the Add New Student link
function showAddRow() {
    resetPost(); //Reset all items
    $('div#post-add-row').show();
}

//This function inserts a new message. It gets called when a user clicks on the Insert button.
function addPost() {
    console.log('Add a new message');
    let data = {};
    $("div[id^='post-new-']").each(function () {
        let field = $(this).attr('id').substr(9);
        let value = $(this).html();
        data[field] = value;
    });
// data['user_id'] = $("div#post-new-user_id").html();
// data['body'] = $("div#post-new-body").html();
// data['image_url'] = $("div#post-new-image_url").html();
    console.log(data);
    const url = baseUrl_API + "/messages";
    console.log(url);
    fetch(url, {
        method: 'POST',
        headers: {
            "Authorization": "Bearer " + jwt,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(checkFetch)
        .then(() => showAllPosts())
        .catch(err => showMessage("Errors", err))
}



// This function cancels adding a new message. It gets called when a user clicks on the Cancel button.
function cancelAddPost() {
    $('#post-add-row').hide();
}

/***********************************************************************************************************
 ******                            Check Fetch for Errors                                             ******
 **********************************************************************************************************/
/* This function checks fetch request for error. When an error is detected, throws an Error to be caught
 * and handled by the catch block. If there is no error detetced, returns the promise.
 * Need to use async and await to retrieve JSON object when an error has occurred.
 */
let checkFetch = async function (response) {
    if (!response.ok) {
        await response.json()  //need to use await so Javascipt will until promise settles and returns its result
            .then(result => {
                throw Error(JSON.stringify(result, null, 4));
            });
    }
    return response;
}


/***********************************************************************************************************
 ******                            Reset post section                                                 ******
 **********************************************************************************************************/
//Reset post section: remove editable features, hide update and cancel buttons, and display edit and delete buttons
function resetPost() {
    // Remove the editable feature from all divs
    $("div[id^='post-edit-']").each(function () {
        $(this).removeAttr('contenteditable').removeClass('post-editable');
    });

    // Hide all the update and cancel buttons and display all the edit and delete buttons
    $("button[id^='btn-post-']").each(function () {
        const id = $(this).attr('id');
        if (id.indexOf('update') >= 0 || id.indexOf('cancel') >= 0) {
            $(this).hide();
        } else if (id.indexOf('edit') >= 0 || id.indexOf('delete') >= 0) {
            $(this).show();
        }
    });
}