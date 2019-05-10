var form = document.getElementById( 'form-form' );
form.addEventListener
(
    'submit',
    function( e )
    {
        e.preventDefault();
        grecaptcha.ready
        (
            function()
            {
                grecaptcha.execute('6LddvqIUAAAAAKWrPSgNJvYN81TH9PtOLXuqgPi7', {action: 'homepage'}).then
                (
                    function( token )
                    {
                        var recaptchaResponse = document.getElementById('recaptchaResponse');
                        recaptchaResponse.value = token;
                        console.log( recaptchaResponse );
                        document.getElementById( 'form-form' ).submit();
                    }
                );
            }
        );
    },
    false
);
