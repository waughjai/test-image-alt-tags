var form=document.getElementById("form-form");form.addEventListener("submit",function(e){e.preventDefault(),grecaptcha.ready(function(){grecaptcha.execute("6LddvqIUAAAAAKWrPSgNJvYN81TH9PtOLXuqgPi7",{action:"homepage"}).then(function(e){var t=document.getElementById("recaptchaResponse");t.value=e,console.log(t),document.getElementById("form-form").submit()})})},!1);
