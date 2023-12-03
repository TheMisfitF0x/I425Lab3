var oldHash = '';
var baseUrl_API = "http://localhost:80/projects/WarehouseAPI/api"; // you need to fill this variable with your own api url

$(function () {
    //Handle hashchange event; when a click is clicked, invoke an appropriate function
    window.addEventListener('hashchange', function (event) {
        let hash = location.hash.substr(1);  //need to remove the # symbol at the beginning.
        oldHash = event.oldURL.substr(event.oldURL.indexOf('#') + 1);

        if ($("a[href='#" + hash + "'").hasClass('disabled')) {
            showMessage('Signin Error', 'Access is not permitted. Please <a href="index.php#signin">sign in</a> to explore the site.');
            return;
        }

        //set active link
        $('li.nav-item.active').removeClass('active');
        $('li.nav-item#li-' + hash).addClass('active');

        //call appropriate function depending on the hash
        switch (hash) {
            case 'home':
                home();
                break;
            case 'user':
                showUsers();
                break;
            case 'post':
                showPosts();
                break;
            case 'admin':
                showAllPosts();
                break;
            case 'signin':
                signin();
                break;
            case 'signup':
                signup();
                break;
            case 'signout':
                signout();
                break;
            case 'message':
                break;
            default:
                home();
        }
    });
    if(jwt == '') {
        //display homepage content and set the hash to 'home'
        home();
        window.location.hash = 'home';
    }
});

// This function sets the content of the homepage.
function home() {
    let _html =
        `<p>This application allows employees and clientele of ACME inc. to access and update the information of products and outgoing orders within our database. 
        ACME personnel also have access to users, warehouses, and employee information.</p>
        
        <p>No access will be provided prior to sign-in. Sign-up is available for those lacking an ACME inc. account. 
        If you are an employee of ACME inc. and do not have permissions to access data you believe you should be able to, please contact your ACME inc. supervisor</p>
        
        <p>Please click on the "Sign in" link to sign in and explore the site. If you don't already have an account, please sign up and create a new account.</p>`;

    // Update the section heading, sub heading, and content
    updateMain('Home', 'Welcome to MyChatter Application', _html);
}

// This function updates main section content.
function updateMain(main_heading, sub_heading, section_content) {
    $('main').show();  //show main section
    $('.form-signup, .form-signin').hide(); //hide the sign-in and sign-up forms

    //update section content
    $('div#main-heading').html(main_heading);
    $('div#sub-heading').html(sub_heading);
    $('div#section-content').html(section_content);
}