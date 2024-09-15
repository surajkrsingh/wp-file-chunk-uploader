// import "bootstrap/scss/bootstrap";
import './scss/dashboard.scss';
import './scss/settings.scss';
import './scss/common.scss';

import './js/Chart.min.js';

window.onload = function () {

    console.log('loaded');

    var pie1 = document.getElementById('bfu-local-pie');
    if (pie1) {

        var config_local = {
            type: 'pie',
            data: FCU_Objects.local_types,
            options: {
                responsive: true,
                legend: false,
                tooltips: {
                    callbacks: {
                        label: 'Test'//sizelabel
                    },
                    backgroundColor: '#F1F1F1',
                    bodyFontColor: '#2A2A2A',
                },
                title: {
                    display: true,
                    position: 'bottom',
                    fontSize: 18,
                    fontStyle: 'normal',
                    text: FCU_Objects.local_types.total
                }
            }
        };

        var ctx = pie1.getContext('2d');
        window.myPieLocal = new Chart(ctx, config_local);
    }
}
