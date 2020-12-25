/*jshint nonew:true, curly:true, noarg:true, forin:true, noempty:true, eqeqeq:true, strict:true, undef:true, bitwise:true, browser:true */
/* global jQuery */

var YII = YII || {};

YII.main = (function ($) {
    'use strict';

    var $navbar = $('.navbar-nav.nav'),
        $modal = $('#myModal'),
        $modalBody = $modal.find('.modal-body'),
        $modalAlertBox = $modal.find('.alert').hide(),
        $modalError = $modalAlertBox.find('.error'),
        $CTALogin = $navbar.find('.login'),
        $CTALogout = $('<li class="logout"><a href="#">Logout</a></li>'),
        authorization = null,
        username = null,
        userID = null;

    /**
     * modifies the XHR object to include the authorization headers.
     *
     * @param {jqXHR} xhr the jQuery XHR object, is automatically passed at call time
     */
    function authorize(xhr) {
        xhr.setRequestHeader('Authorization', 'Basic ' + authorization);
    }

    /**
     * initialise all the events in the page.
     */
    (function init() {
        $navbar.append($CTALogout);
        $CTALogout.hide();

        $navbar.on('click', '.logout a', function (e) {
            e.preventDefault();

            // unset the user info
            authorization = null;
            username = null;
            userID = null;

            // restore the login CTA
            $CTALogout.hide();
            $CTALogin.show();
        });

        $navbar.on('click', '.login a', function (e) {
            e.preventDefault();
        });

        $modalBody.on('submit', '#login-form', function (e) {
            e.preventDefault();

            username = this['loginform-username'].value;
            // we don't care to store the password... sorta
            authorization = btoa(username + ':' + this['loginform-password'].value);

            $.ajax(
                {
                    method: 'GET',
                    url: '/v1/users/search/' + username,
                    dataType: 'json',
                    async: false,
                    beforeSend: authorize,
                    complete: function (xhr, status) {

                        if (status === 'success') {
                            // save the user ID for subsequent calls
                            userID = xhr.responseJSON.id;
                            // set the logout button
                            $CTALogin.hide();
                            $CTALogout.show();
                            // clear the status errors
                            $modalError.html('');
                            $modalAlertBox.hide();
                            // close the modal window
                            $modal.modal('hide');
                        }
                        else {
                            // display the error
                            $modalError.html('<strong>Error</strong>: ' + xhr.statusText);
                            $modalAlertBox.show();
                        }
                    }
                }
            );
        });
    })();

})(jQuery);