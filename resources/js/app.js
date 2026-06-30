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
        // Detect if this select has a clearable empty-value option (filter selects like "Semua semester")
        const firstOption = el.querySelector('option[value=""]');
        const isClearable = firstOption && !el.hasAttribute('required');

        const plugins = ['dropdown_input'];

        new TomSelect(el, {
            maxOptions: null,
            allowEmptyOption: !!isClearable,
            plugins: plugins,
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results">Tidak ada data ditemukan untuk "' + escape(data.input) + '"</div>';
                }
            }
        });
    });

});
// End of file
