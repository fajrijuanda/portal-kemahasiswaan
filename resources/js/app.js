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

    const cursorRing = document.querySelector('.ubp-cursor-ring');
    const canUseCursorRing = cursorRing
        && window.matchMedia('(hover: hover)').matches
        && window.matchMedia('(pointer: fine)').matches
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (canUseCursorRing) {
        window.addEventListener('mousemove', (event) => {
            cursorRing.classList.add('is-visible');
            cursorRing.style.transform = `translate3d(${event.clientX}px, ${event.clientY}px, 0) translate(-50%, -50%)`;
        }, {passive: true});

        window.addEventListener('mouseout', () => cursorRing.classList.remove('is-visible'), {passive: true});

        document.querySelectorAll('a, button, input, select, textarea, [role="button"], .ts-control').forEach((el) => {
            el.addEventListener('mouseenter', () => cursorRing.classList.add('is-active'));
            el.addEventListener('mouseleave', () => cursorRing.classList.remove('is-active'));
        });
    }
});
// End of file
