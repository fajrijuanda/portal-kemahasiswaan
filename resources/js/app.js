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

    document.querySelectorAll('[data-rich-editor]').forEach((editor) => {
        const area = editor.querySelector('[data-editor-area]');
        const input = editor.querySelector('[data-editor-input]');
        const form = editor.closest('form');

        if (!area || !input || !form) {
            return;
        }

        const sync = () => {
            input.value = area.innerHTML.trim();
            area.classList.toggle('is-empty', area.textContent.trim().length === 0);
        };

        editor.querySelectorAll('[data-editor-command]').forEach((button) => {
            button.addEventListener('click', () => {
                area.focus();
                document.execCommand(button.dataset.editorCommand, false, button.dataset.editorValue || null);
                sync();
            });
        });

        const blockSelect = editor.querySelector('[data-editor-block]');
        if (blockSelect) {
            blockSelect.addEventListener('change', () => {
                area.focus();
                document.execCommand('formatBlock', false, blockSelect.value);
                sync();
                blockSelect.value = 'P';
            });
        }

        const linkButton = editor.querySelector('[data-editor-link]');
        if (linkButton) {
            linkButton.addEventListener('click', () => {
                area.focus();
                const url = window.prompt('Masukkan URL link');
                if (url) {
                    document.execCommand('createLink', false, url);
                    area.querySelectorAll('a').forEach((link) => {
                        link.setAttribute('target', '_blank');
                        link.setAttribute('rel', 'noreferrer');
                    });
                    sync();
                }
            });
        }

        area.addEventListener('input', sync);
        area.addEventListener('blur', sync);
        form.addEventListener('submit', sync);
        sync();
    });
});
// End of file
