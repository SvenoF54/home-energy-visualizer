/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {
    $('.selectorYear1').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#year1').val($(this).attr('href').slice(1));
        $('#date-range-form').submit();
    });

    $('.selectorYear2').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#year2').val($(this).attr('href').slice(1));
        $('#date-range-form').submit();
    });

    $('#line1').change(function() {
        $('#date-range-form').submit();
    });

    $('#line2').change(function() {
        $('#date-range-form').submit();
    });
});
