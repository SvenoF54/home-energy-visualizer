/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

const emColor = getComputedStyle(document.documentElement).getPropertyValue('--em-color').trim();
const emOverZeroColor = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color').trim();
const addSavingsColor = getComputedStyle(document.documentElement).getPropertyValue('--add-savings-color').trim();
const emOverZeroColor2 = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color2').trim();
const addSavingsColor2 = getComputedStyle(document.documentElement).getPropertyValue('--add-savings-color2').trim();

const savingsColor = getComputedStyle(document.documentElement).getPropertyValue('--savings-color').trim();
const savingsColor2 = getComputedStyle(document.documentElement).getPropertyValue('--savings-color2').trim();

const feedInColor = getComputedStyle(document.documentElement).getPropertyValue('--feed-in-color').trim();
const feedInColor2 = getComputedStyle(document.documentElement).getPropertyValue('--feed-in-color2').trim();

const pm1Color = getComputedStyle(document.documentElement).getPropertyValue('--pm1-color').trim();
const pm2Color = getComputedStyle(document.documentElement).getPropertyValue('--pm2-color').trim();
const pm3Color = getComputedStyle(document.documentElement).getPropertyValue('--pm3-color').trim();
const pmTotalColor = getComputedStyle(document.documentElement).getPropertyValue('--pm-total-color').trim();

const line1Color = getComputedStyle(document.documentElement).getPropertyValue('--line1-color').trim();
const line2Color = getComputedStyle(document.documentElement).getPropertyValue('--line2-color').trim();
const lineZeroColor = getComputedStyle(document.documentElement).getPropertyValue('--line-zero-color').trim();

const autarkyColor = getComputedStyle(document.documentElement).getPropertyValue('--autarky-color').trim();
const autarkyColor2 = getComputedStyle(document.documentElement).getPropertyValue('--autarky-color2').trim();
const selfConsumptionColor = getComputedStyle(document.documentElement).getPropertyValue('--self-consumption-color').trim();
const selfConsumptionColor2 = getComputedStyle(document.documentElement).getPropertyValue('--self-consumption-color2').trim();

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

    function fetchAlertData() {
        $.ajax({
            url: URL_PREFIX + 'api/get-hasAlert.php',
            method: 'GET',
            dataType: 'json', // Falls die Antwort JSON ist
            success: function(response) {
                $("#naviAlert, #naviMobileAlert").toggleClass("d-none", !response.hasAlert);
                $("#naviAppTitle, #naviMobileAppTitle").toggleClass("d-none", response.hasAlert);
            }

        });
    }

    // Add time for alerting
    setInterval(fetchAlertData, 10000);
});