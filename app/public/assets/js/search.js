document.addEventListener('DOMContentLoaded', function() {
    
    // Select the filters and the result container
    const filters = document.querySelectorAll('#tutor-filters select');
    const resultsContainer = document.getElementById('tutor-results');

    // Add event listener to all dropdowns
    filters.forEach(filter => {
        filter.addEventListener('change', fetchTutors);
    });

    function fetchTutors() {
        const subject = document.querySelector('select[name="subject"]').value;
        const minPrice = document.querySelector('select[name="min_price"]').value;
        const maxPrice = document.querySelector('select[name="max_price"]').value;

        const url = `/tutors?ajax=1&subject=${encodeURIComponent(subject)}&min_price=${minPrice}&max_price=${maxPrice}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderTutors(data);
            })
            .catch(error => console.error('Error:', error));
    }

    function renderTutors(tutors) {
        resultsContainer.innerHTML = ''; // Clear current list

        if (tutors.length === 0) {
            resultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-warning text-center">No tutors found.</div></div>';
            return;
        }

        // Loop through data and build HTML cards
        tutors.forEach(tutor => {
            const html = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">${escapeHtml(tutor.first_name)} ${escapeHtml(tutor.last_name)}</h5>
                            <small class="text-muted">${escapeHtml(tutor.subject)}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-truncate">${escapeHtml(tutor.bio)}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">€${tutor.hourly_rate}/hr</span>
                                <span class="badge bg-info text-dark">${tutor.experience_years} Years Exp.</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="/book?tutor_id=${tutor.profile_id}" class="btn btn-primary w-100">Book Lesson</a>
                        </div>
                    </div>
                </div>
            `;
            resultsContainer.innerHTML += html;
        });
    }

    // Helper to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});