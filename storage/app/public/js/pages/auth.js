$(document).ready(function () {
    // Success Alert
    setTimeout(function () {
        $("#success-alert")
            .fadeTo(2000, 500)
            .slideUp(500, function () {
                $("#success-alert").slideUp(500);
            });
    }, 5000); // 5000 milidetik (5 detik) delay

    // Danger Alert
    setTimeout(function () {
        $("#danger-alert")
            .fadeTo(2000, 500)
            .slideUp(500, function () {
                $("#danger-alert").slideUp(500);
            });
    }, 5000); // 5000 milidetik (5 detik) delay
});
