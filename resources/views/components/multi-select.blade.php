@props([
    'name' => 'items',
    'options' => [],
    'selected' => [],
    'placeholder' => 'Search...'
])

<div class="multi-select-container relative" data-name="{{ $name }}">
    <!-- Hidden inputs (initial selected values) -->
    <div class="hidden-inputs">
        @foreach($selected as $value)
            <input type="hidden" name="{{ $name }}[]" value="{{ $value }}">
        @endforeach
    </div>

    <!-- Visible input / tags -->
    <div class="input-box border border-gray-300 rounded-lg px-2 py-1 flex flex-wrap gap-1 items-center cursor-text" tabindex="0">
        {{-- Pre-render tags for initial selected values --}}
        @foreach($selected as $value)
            @php
                $label = collect($options)->firstWhere('value', $value)['label'] ?? $value;
            @endphp
            <span class="tag flex items-center bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm" data-value="{{ $value }}">
                <span class="tag-label">{{ $label }}</span>
                <button type="button" class="ml-1 text-blue-500 remove-tag" aria-label="remove">&times;</button>
            </span>
        @endforeach

        <input
            type="text"
            class="search-input flex-1 border-none outline-none py-1 focus:ring-0"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
        >
    </div>

    <!-- Dropdown -->
    <div class="dropdown fixed mt-1 w-52 bg-white border rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto hidden">
        @foreach($options as $option)
            <div class="dropdown-item px-3 py-2 cursor-pointer hover:bg-blue-100 flex justify-between items-center"
                 data-value="{{ $option['value'] }}"
                 data-label="{{ $option['label'] }}">
                <span class="item-label">{{ $option['label'] }}</span>
                <!-- checkmark placeholder -->
                <span class="selected-check" aria-hidden="true" style="display: none;"><i data-lucide="check" class="text-green-500 w-5 h-5"></i></span>
            </div>
        @endforeach
    </div>
</div>

<script>
(function () {
    function initMultiSelect(container) {
        const name = container.dataset.name;
        const inputBox = container.querySelector('.input-box');
        const searchInput = container.querySelector('.search-input');
        const dropdown = container.querySelector('.dropdown');
        const hiddenInputs = container.querySelector('.hidden-inputs');
        const dropdownItems = Array.from(dropdown.querySelectorAll('.dropdown-item'));

        let focusedIndex = -1; // --- added for keyboard navigation ---

        function getSelectedValues() {
            return Array.from(hiddenInputs.querySelectorAll('input[type="hidden"]')).map(i => i.value);
        }

        function showDropdown() {
            dropdown.classList.remove('hidden');
        }
        function hideDropdown() {
            dropdown.classList.add('hidden');
            clearFocus(); // --- added
        }

        function updateDropdownFilter() {
            const q = searchInput.value.trim().toLowerCase();
            let anyVisible = false;
            dropdownItems.forEach(item => {
                const label = (item.dataset.label || '').toLowerCase();
                const matches = label.indexOf(q) !== -1;
                item.style.display = matches ? 'flex' : 'none';
                if (matches) anyVisible = true;
            });

            let noRow = dropdown.querySelector('.no-results');
            if (!anyVisible) {
                if (!noRow) {
                    noRow = document.createElement('div');
                    noRow.className = 'no-results px-3 py-2 text-gray-500 text-sm';
                    noRow.textContent = 'No results found.';
                    dropdown.appendChild(noRow);
                }
            } else {
                if (noRow) noRow.remove();
            }

            focusedIndex = -1; // --- reset focus index ---
        }

        function syncDropdownSelection() {
            const selected = getSelectedValues();
            dropdownItems.forEach(item => {
                const checkSpan = item.querySelector('.selected-check');
                if (selected.includes(item.dataset.value)) {
                    checkSpan.style.display = '';
                } else {
                    checkSpan.style.display = 'none';
                }
            });
        }

        function createTag(value, label) {
            if (getSelectedValues().includes(value)) return;

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name + '[]';
            hidden.value = value;
            hiddenInputs.appendChild(hidden);

            const tag = document.createElement('span');
            tag.className = 'tag flex items-center bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm';
            tag.dataset.value = value;
            tag.innerHTML = `<span class="tag-label">${escapeHtml(label)}</span>
                             <button type="button" class="ml-1 text-blue-500 remove-tag" aria-label="remove">&times;</button>`;
            inputBox.insertBefore(tag, searchInput);

            syncDropdownSelection();
        }

        function removeTagByValue(value) {
            const tag = inputBox.querySelector(`.tag[data-value="${cssEscape(value)}"]`);
            if (tag) tag.remove();
            const hidden = hiddenInputs.querySelector(`input[type="hidden"][value="${cssEscape(value)}"]`);
            if (hidden) hidden.remove();
            syncDropdownSelection();
        }

        function toggleValue(value, label) {
            if (getSelectedValues().includes(value)) {
                removeTagByValue(value);
            } else {
                createTag(value, label);
            }
        }

        // --- new helper functions for focus management ---
        function visibleItems() {
            return dropdownItems.filter(i => i.style.display !== 'none');
        }

        function clearFocus() {
            dropdownItems.forEach(i => i.classList.remove('bg-blue-100'));
            focusedIndex = -1;
        }

        function moveFocus(dir) {
            const vis = visibleItems();
            if (vis.length === 0) return;

            if (focusedIndex === -1) {
                focusedIndex = dir === 1 ? 0 : vis.length - 1;
            } else {
                focusedIndex = (focusedIndex + dir + vis.length) % vis.length;
            }

            vis.forEach(i => i.classList.remove('bg-blue-100'));
            vis[focusedIndex].classList.add('bg-blue-100');
            vis[focusedIndex].scrollIntoView({ block: 'nearest' });
        }

        function selectFocused() {
            const vis = visibleItems();
            if (focusedIndex >= 0 && focusedIndex < vis.length) {
                const item = vis[focusedIndex];
                toggleValue(item.dataset.value, item.dataset.label);
                searchInput.focus();
                updateDropdownFilter();
            }
        }

        // --- events ---
        inputBox.addEventListener('click', function (e) {
            if (e.target === inputBox) {
                searchInput.focus();
            }
            showDropdown();
            updateDropdownFilter();
        });

        searchInput.addEventListener('input', function () {
            updateDropdownFilter();
            showDropdown();
        });

        dropdown.addEventListener('click', function (e) {
            const item = e.target.closest('.dropdown-item');
            if (!item) return;
            toggleValue(item.dataset.value, item.dataset.label);
            searchInput.focus();
            updateDropdownFilter();
        });

        inputBox.addEventListener('click', function (e) {
            const rem = e.target.closest('.remove-tag');
            if (!rem) return;
            const tag = rem.closest('.tag');
            if (!tag) return;
            removeTagByValue(tag.dataset.value);
            searchInput.focus();
        });

        document.addEventListener('click', function (e) {
            if (!container.contains(e.target)) {
                hideDropdown();
            }
        });

        // --- keyboard controls ---
        searchInput.addEventListener('keydown', function (e) {
            const vis = visibleItems();
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                showDropdown();
                moveFocus(1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                showDropdown();
                moveFocus(-1);
            } else if (e.key === 'Enter') {
                if (focusedIndex >= 0 && vis.length) {
                    e.preventDefault();
                    const oldIndex = focusedIndex; // preserve index
                    selectFocused();
                    // reapply focus to the same position (after update)
                    const newVis = visibleItems();
                    if (newVis.length) {
                        focusedIndex = Math.min(oldIndex, newVis.length - 1);
                        newVis.forEach(i => i.classList.remove('bg-blue-100'));
                        newVis[focusedIndex].classList.add('bg-blue-100');
                        newVis[focusedIndex].scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Escape') {
                hideDropdown();
                searchInput.blur();
            } else if (e.key === 'Backspace' && searchInput.value === '') {
                const tags = inputBox.querySelectorAll('.tag');
                if (tags.length) {
                    const last = tags[tags.length - 1];
                    removeTagByValue(last.dataset.value);
                }
            }
        });

        syncDropdownSelection();
    }

    function cssEscape(s) {
        return s.replace(/(["'\\])/g, '\\$1');
    }
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.multi-select-container').forEach(initMultiSelect);
    });
})();
</script>
