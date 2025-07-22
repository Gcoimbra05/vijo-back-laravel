
$(document).ready(function () {
    $.validator.addMethod("validPassword", function(value, element) {
        return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{12,}$/.test(value);
    }, "Password must be at least 12 characters long and contain at least one digit, one uppercase letter, one lowercase letter, and one special character.");

    //fadeout flash message
    $("#status_msg").fadeOut(4000);

    $("#signinForm").validate({
        onfocusout: false,
        errorClass: 'js_error',
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
            }
        },
        messages: {
            email: {
                required: "Please enter email",
                email: "Please enter a valid email address",
            },
            password: {
                required: "Please enter password",
            },
        },

        submitHandler: function(form) {
            form.submit(); 
        },
    });
});