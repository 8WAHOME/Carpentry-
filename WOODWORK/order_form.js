function validateForm() {
    // Basic client-side validation
    var form = document.getElementById('orderForm');
    var fields = form.querySelectorAll('input, textarea');

    for (var i = 0; i < fields.length; i++) {
        if (fields[i].value.trim() === '') {
            alert('Please fill in all fields.');
            return;
        }
    }

    // If all fields are filled, submit the form
    form.submit();
}
