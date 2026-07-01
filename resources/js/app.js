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
        const blockSelect = editor.querySelector('[data-editor-block]');
        const imageInput = editor.querySelector('[data-editor-image-input]');
        const imageButton = editor.querySelector('[data-editor-image-trigger]');
        const stateButtons = Array.from(editor.querySelectorAll('[data-editor-state]'));
        const imageLimit = 2 * 1024 * 1024;
        let savedRange = null;

        if (!area || !input || !form) {
            return;
        }

        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));

        const selectionNode = () => {
            const selection = window.getSelection();

            return selection && selection.rangeCount > 0 ? selection.anchorNode : null;
        };

        const isNodeInsideArea = (node) => {
            if (!node) {
                return false;
            }

            const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentNode;

            return element === area || area.contains(element);
        };

        const saveSelection = () => {
            const selection = window.getSelection();

            if (selection && selection.rangeCount > 0 && isNodeInsideArea(selection.anchorNode)) {
                savedRange = selection.getRangeAt(0).cloneRange();
            }
        };

        const restoreSelection = () => {
            if (!savedRange) {
                return;
            }

            const selection = window.getSelection();
            const startNode = savedRange.startContainer;

            if (!selection || !isNodeInsideArea(startNode)) {
                return;
            }

            selection.removeAllRanges();
            selection.addRange(savedRange);
        };

        const normalizeLinks = () => {
            area.querySelectorAll('a').forEach((link) => {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noreferrer');
            });
        };

        const sync = () => {
            normalizeLinks();
            input.value = area.innerHTML.trim();
            area.classList.toggle('is-empty', area.textContent.trim().length === 0 && !area.querySelector('img'));
        };

        const getSelectedBlockName = () => {
            let node = selectionNode();
            let fallback = null;
            const blockNames = ['P', 'H2', 'H3', 'H4', 'PRE', 'LI', 'DIV'];

            if (!isNodeInsideArea(node)) {
                return 'P';
            }

            node = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;

            while (node && node !== area) {
                const name = node.nodeName.toUpperCase();

                if (name === 'BLOCKQUOTE') {
                    return 'BLOCKQUOTE';
                }

                if (!fallback && blockNames.includes(name)) {
                    fallback = name === 'LI' || name === 'DIV' ? 'P' : name;
                }

                node = node.parentElement;
            }

            return fallback || 'P';
        };

        const updateToolbar = () => {
            stateButtons.forEach((button) => {
                const state = button.dataset.editorState;
                let isActive = false;

                if (state === 'formatBlock') {
                    isActive = getSelectedBlockName() === (button.dataset.editorValue || '').toUpperCase();
                } else {
                    try {
                        isActive = document.queryCommandState(state);
                    } catch (error) {
                        isActive = false;
                    }
                }

                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            if (blockSelect) {
                const blockName = getSelectedBlockName();

                if (Array.from(blockSelect.options).some((option) => option.value === blockName)) {
                    blockSelect.value = blockName;
                }
            }
        };

        const runCommand = (command, value = null) => {
            area.focus();
            restoreSelection();

            try {
                document.execCommand('styleWithCSS', false, false);
                document.execCommand(command, false, value);
            } catch (error) {
                return;
            }

            sync();
            saveSelection();
            updateToolbar();
        };

        const insertHtml = (html) => {
            area.focus();
            restoreSelection();

            try {
                document.execCommand('insertHTML', false, html);
            } catch (error) {
                return;
            }

            sync();
            saveSelection();
            updateToolbar();
        };

        const insertImageFile = (file) => {
            if (!file.type.startsWith('image/')) {
                window.alert('File harus berupa gambar.');

                return;
            }

            if (file.size > imageLimit) {
                window.alert('Ukuran gambar maksimal 2 MB.');

                return;
            }

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                const alt = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ').trim() || 'Gambar berita';
                insertHtml(`<p><img src="${reader.result}" alt="${escapeHtml(alt)}"></p><p><br></p>`);
            });
            reader.readAsDataURL(file);
        };

        const insertImageFiles = (files) => {
            const imageFiles = Array.from(files || []).filter((file) => file.type.startsWith('image/'));

            if (imageFiles.length === 0) {
                return false;
            }

            imageFiles.forEach(insertImageFile);

            return true;
        };

        const placeCaretFromPoint = (event) => {
            let range = null;

            if (document.caretRangeFromPoint) {
                range = document.caretRangeFromPoint(event.clientX, event.clientY);
            } else if (document.caretPositionFromPoint) {
                const position = document.caretPositionFromPoint(event.clientX, event.clientY);

                if (position) {
                    range = document.createRange();
                    range.setStart(position.offsetNode, position.offset);
                    range.collapse(true);
                }
            }

            if (!range || !isNodeInsideArea(range.startContainer)) {
                return;
            }

            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            saveSelection();
        };

        editor.querySelectorAll('.ubp-doc-toolbar button').forEach((button) => {
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
                saveSelection();
            });
        });

        editor.querySelectorAll('[data-editor-command]').forEach((button) => {
            button.addEventListener('click', () => {
                runCommand(button.dataset.editorCommand, button.dataset.editorValue || null);
            });
        });

        if (blockSelect) {
            blockSelect.addEventListener('mousedown', saveSelection);
            blockSelect.addEventListener('change', () => {
                runCommand('formatBlock', blockSelect.value);
            });
        }

        const linkButton = editor.querySelector('[data-editor-link]');
        if (linkButton) {
            linkButton.addEventListener('click', () => {
                area.focus();
                restoreSelection();

                const url = window.prompt('Masukkan URL link');

                if (!url) {
                    return;
                }

                const selection = window.getSelection();

                if (selection && selection.isCollapsed) {
                    const label = window.prompt('Teks link') || url;
                    insertHtml(`<a href="${escapeHtml(url)}" target="_blank" rel="noreferrer">${escapeHtml(label)}</a>`);

                    return;
                }

                runCommand('createLink', url);
                normalizeLinks();
                sync();
                updateToolbar();
            });
        }

        if (imageButton && imageInput) {
            imageButton.addEventListener('click', () => {
                area.focus();
                saveSelection();
                imageInput.click();
            });

            imageInput.addEventListener('change', () => {
                restoreSelection();
                insertImageFiles(imageInput.files);
                imageInput.value = '';
            });
        }

        area.addEventListener('paste', (event) => {
            if (event.clipboardData && insertImageFiles(event.clipboardData.files)) {
                event.preventDefault();
            }
        });

        area.addEventListener('dragover', (event) => {
            const hasImage = Array.from(event.dataTransfer?.items || []).some((item) => item.type.startsWith('image/'));

            if (hasImage) {
                event.preventDefault();
                area.classList.add('is-drag-over');
            }
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('is-drag-over');
        });

        area.addEventListener('drop', (event) => {
            area.classList.remove('is-drag-over');

            if (!event.dataTransfer || !insertImageFiles(event.dataTransfer.files)) {
                return;
            }

            event.preventDefault();
            placeCaretFromPoint(event);
        });

        area.addEventListener('input', () => {
            sync();
            saveSelection();
            updateToolbar();
        });
        area.addEventListener('keyup', () => {
            saveSelection();
            updateToolbar();
        });
        area.addEventListener('mouseup', () => {
            saveSelection();
            updateToolbar();
        });
        area.addEventListener('focus', () => {
            saveSelection();
            updateToolbar();
        });
        area.addEventListener('blur', sync);
        document.addEventListener('selectionchange', () => {
            if (isNodeInsideArea(selectionNode())) {
                saveSelection();
                updateToolbar();
            }
        });
        form.addEventListener('submit', sync);
        sync();
        updateToolbar();
    });
});
// End of file
