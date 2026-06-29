import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import { Chart } from 'chart.js/auto';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';

window.Chart = Chart;
window.Alpine = Alpine;
window.TomSelect = TomSelect;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    // Initialize custom modern select dropdowns
    document.querySelectorAll('select.ubp-control').forEach((el) => {
        new TomSelect(el, {
            maxOptions: null,
            plugins: ['dropdown_input'],
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results">Tidak ada data ditemukan untuk "' + escape(data.input) + '"</div>';
                }
            }
        });
    });

});
// End of file
