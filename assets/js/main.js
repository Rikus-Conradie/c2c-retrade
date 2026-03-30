// Handles all front-end interactions on the site
document.addEventListener('DOMContentLoaded', function() {


    // AUTO HIDE ALERTS
    // Success and error messages disappear after 4 seconds

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 4000);
    });

    // Asks the user to confirm before deleting a listing

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('Are you sure you want to delete this listing? This cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // Filters listing cards in real time as the user types

    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const cards = document.querySelectorAll('.listing-card');
            cards.forEach(function(card) {
                const title = card.querySelector('h3').textContent.toLowerCase();
                if (title.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

});