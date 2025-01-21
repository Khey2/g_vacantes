/* ------------------------------------------------------------------------------
 *
 *  # Google Visualization - bars
 *
 *  Google Visualization bar chart demonstration
 *
 * ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------

var GoogleBarBasic = function() {


    //
    // Setup module components
    //

    // Bar chart
    var _googleBarBasic = function() {
        if (typeof google == 'undefined') {
            console.warn('Warning - Google Charts library is not loaded.');
            return;
        }

        // Initialize chart
        google.charts.load('current', {
            callback: function () {

                // Draw chart
                drawBar();

                // Resize on sidebar width change
                $(document).on('click', '.sidebar-control', drawBar);

                // Resize on window resize
                var resizeBarBasic;
                $(window).on('resize', function() {
                    clearTimeout(resizeBarBasic);
                    resizeBarBasic = setTimeout(function () {
                        drawBar();
                    }, 200);
                });
            },
            packages: ['corechart']
        });

        // Chart settings
        function drawBar() {

            // Define charts element
            var bar_chart_element = document.getElementById('google-bar');

            // Data
            var data = google.visualization.arrayToDataTable([
                ['Area', 'resultado', { role: 'annotation' }, { role: "style" } ],
				['0 a 3 meses', ceroatres, ceroatres, "#3366CC"],
                ['4 a 6 meses', tresaseis, tresaseis, "#3366CC"],
                ['7 a 12 meses', seisadoce, seisadoce, "#3366CC"],
                ['mas de 12 meses', masdedoce, masdedoce, "#3366CC"]
            ]);


            // Options
            var options_bar = {
                fontName: 'Roboto',
                height: 400,
                fontSize: 12,
                chartArea: {
                    left: '5%',
                    width: '94%',
                    height: 350
                },
                tooltip: {
                    textStyle: {
                        fontName: 'Roboto',
                        fontSize: 13
                    }
                },
                vAxis: {
                    gridlines:{
                        color: '#e5e5e5',
                        count: 10
                    },
                    minValue: 0
                },
                legend: {
                    position: 'top',
                    alignment: 'center',
                    textStyle: {
                        fontSize: 12
                    }
                }
            };

            // Draw chart
            var bar = new google.visualization.BarChart(bar_chart_element);
            bar.draw(data, options_bar);

        }
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _googleBarBasic();
        }
    }
}();


// Initialize module
// ------------------------------

GoogleBarBasic.init();


