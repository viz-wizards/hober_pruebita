document.querySelectorAll('input[type="date"]').forEach((input) => {
    if (!input.value) {
        input.valueAsDate = new Date();
    }
});
