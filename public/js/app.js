document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('report-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);

        // Ako želite da provjerite podatke koji se šalju
        for (const [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch('/reports', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        }).then(response => response.json())
          .then(data => {
              console.log(data);
              location.reload();
          }).catch(error => {
              console.error('Error:', error);
        });
    });

    // Add functionality for editing and deleting reports
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            // Implement edit functionality here
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Delete button clicked');
            const id = this.dataset.id;
            if (confirm('Are you sure you want to delete this report?')) {
                fetch(`/reports/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json()).then(data => {
                    console.log(data);
                    location.reload(); // Reloads the page to remove deleted report
                }).catch(error => console.error('Error:', error));
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('datum');
    if (dateInput) { // Provjeri postoji li element
        const today = new Date().toISOString().split('T')[0]; // Format to YYYY-MM-DD
        dateInput.value = today;
    }
});

