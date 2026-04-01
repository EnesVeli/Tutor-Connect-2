document.addEventListener('DOMContentLoaded', function() {
    console.log("Tutor Connect: Assets Loaded.");

    const bookingForm = document.querySelector('form[action="/book/payment"]');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const timeSelect = document.querySelector('select[name="time"]');
            if (timeSelect && timeSelect.value === "") {
                e.preventDefault();
                alert("Please select a time slot.");
            }
        });
    }
});