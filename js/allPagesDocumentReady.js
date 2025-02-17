/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

// Documen-Ready for all pages
$(document).ready(function() {
    // Add tooltips
    $('[data-bs-toggle="tooltip"]').each(function() {
        new bootstrap.Tooltip(this);
    });

    // Add popovers
    var popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    var popoverList = [...popoverTriggerList].map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            html: true,
            trigger: 'focus',
            content: function() {
                // Get content from the added div
                var contentId = popoverTriggerEl.getAttribute('data-bs-content');
                var content = document.querySelector(contentId);
                var heading = $(content).children(".popover-heading").html();
                var body = $(content).children(".popover-body").html();
                return `<div class="popover-heading">${heading}</div><div class="popover-body">${body}</div>`;
            }
        });
    });
});
