function showToast(title, message, type = 'info') {
    const icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-exclamation-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info: '<i class="fas fa-info-circle"></i>'
    };

    toastr[type](message, title, { icon: icons[type] });
}

function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10); // Random number between 0 and 9
    const num2 = Math.floor(Math.random() * 10);
    const operator = ['+', '-', '*'][Math.floor(Math.random() * 3)]; // Random operator: +, -, *
    const expression = `${num1} ${operator} ${num2}`;
    return { expression, answer: eval(expression) }; // Evaluate the expression to get the answer
}
const { expression, answer } = generateCaptcha();

function signup() {
    var username = $("#signup-username").val();
    var password = $("#signup-password").val();
    var cpassword = $("#signup-confirm-password").val();
    var captcha = $("#signup-captcha").val();
    console.log('clicked signup, posting data!');

    if (!username || !password || !cpassword || !email || !captcha || !$('#tos').prop('checked')) {
        showToast('Error', 'Please fill in all required fields and accept the Terms of Service.', 'error');
        return;
    }

    if (password !== confirmPassword) {
        showToast('Error', 'Password and Confirm Password do not match.', 'error');
        return;
    }

    if (captcha !== String(answer)) {
        showToast('Error', 'Incorrect CAPTCHA.', 'error');
        return;
    }

    $.post('/signup',{username, password},  function (response) {
        data = $.parseJSON(response);
        if (data.status == 'success') {
            showToast('Success!', data.message, 'success');
            setTimeout(() => {
                window.localation.href = '/dashboard';
            }, 5000);
        } else if (data.status == 'error') {
            showToast('Error', data.message, 'error');
        }
    })



}
$(window).on('load', function () {
    console.log(expression);
});