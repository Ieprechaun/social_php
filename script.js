document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const postDiv = button.parentElement;
            const editForm = postDiv.querySelector('.edit-form');
            editForm.style.display = 'block';
        });
    });
});
