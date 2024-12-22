document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const adminNav = document.querySelector('.admin-nav');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            adminNav.classList.toggle('active');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.admin-nav') && !e.target.closest('.mobile-menu-toggle')) {
            adminNav.classList.remove('active');
        }
    });

    // Initialize any charts or interactive elements
    const charts = document.querySelectorAll('.chart-container');
    if (charts.length) {
        initializeCharts();
    }
});

function initializeCharts() {
    // Add your chart initialization code here
    // Example using Chart.js
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                // Add your chart data here
            },
            options: {
                // Add your chart options here
            }
        });
    }
} 