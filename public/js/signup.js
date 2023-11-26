//This function get called when the signup hash is clicked.
function signup() {
    $('.img-loading, main, .form-signin, #li-signin').hide();
    $('.form-signup, #li-signup').show();

    //window.location.hash = 'signup';
}

//submit the form to create a user account
$('form.form-signup').submit(function (e) {
    $('.img-loading').show();
    e.preventDefault();
    let username = $('#signup-username').val();
    let dob = $('#signup-dob').val();

    let dobSplit = dob.split("-");
    dob = dobSplit[1] + "/" + dobSplit[2] + "/" + dobSplit[0];

    let today = new Date();
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();

    today = mm + '/' + dd + '/' + yyyy;
    let email = $('#signup-email').val();
    let password = $('#signup-password').val();
    const url = baseUrl_API + '/users';
    console.log(username + " " + dob + " " + today + " " + email + " " + password);
    $.ajax({
        url: url,
        method: 'post',
        dataType: 'json',
        data: {username: username, dob: dob, email: email, password: password, date_created: today}
    }).done(function () {
        $('.img-loading').hide();
        //show a message after a sussessful login
        showMessage('Signup Message',
            'Thanks for signing up. Your account has been created.');
        $('li#li-signin').show();
        $('li#li-signout').hide();
    }).fail(function (jqXHR, textStatus) {
        showMessage('Signup Error', JSON.stringify(jqXHR.responseJSON, null,
            4));
    }).always(function () {
        console.log('Signup has Completed.');
    });
});