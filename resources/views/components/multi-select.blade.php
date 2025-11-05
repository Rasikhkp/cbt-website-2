@props([
    'name',
    'options', // Expects: [{ value: '1', label: 'Jack' }, { value: '2', label: 'Ann' }]
    'selected' => [], // Expects: ['1', '2']
    'placeholder' => 'Select options'
])

@php
    // We need a unique ID for each component instance on the page
    $componentId = 'multiselect-' . Str::uuid();
@endphp

{{--
This component uses vanilla JavaScript.
It finds its elements by the unique $componentId.
--}}
<div class="relative" id="{{ $componentId }}">

    <div data-multiselect-hidden-inputs>
        @foreach($selected as $value)
            <input type="hidden" name="{{ $name }}[]" value="{{ $value }}">
        @endforeach
    </div>

    <div
        class="flex w-full cursor-pointer flex-wrap items-center gap-2 rounded-md border border-gray-300 bg-white p-2 min-h-[42px]"
        data-multiselect-button
    >
        <div class="flex flex-wrap gap-2" data-multiselect-pill-container></div>

        <span class="text-gray-800" data-multiselect-placeholder>
            {{ $placeholder }}
        </span>

        <svg
            class="ml-auto h-5 w-5 text-gray-400"
            data-multiselect-arrow
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
        >
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </div>

    <div
        class="fixed z-10 w-72 mt-1 rounded-md border border-gray-300 bg-white shadow-lg"
        data-multiselect-dropdown
        style="display: none;"
    >
        <div class="p-2">
            <input
                type="text"
                data-multiselect-search
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Search options..."
            >
        </div>

        <ul class="max-h-60 overflow-y-auto" data-multiselect-options-list>
            </ul>

        <div class="p-2 text-gray-500" data-multiselect-no-results style="display: none;">
            No options found.
        </div>
    </div>
</div>

<script>
    // Define the initialization function in the global scope,
    // but wrap it in a check to prevent re-definition.
    if (typeof initMultiSelect !== 'function') {
        /**
         * @param {Object} config
         * @param {string} config.id - The DOM ID of the component wrapper.
         * @param {string} config.name - The name for the hidden input fields.
         * @param {Array<Object>} config.options - [{value: '', label: ''}]
         * @param {Array<string>} config.selectedValues - ['value1', 'value2']
         * @param {string} config.placeholder - The placeholder text.
         */
        function initMultiSelect(config) {
            // --- State ---
            let open = false;
            let search = '';
            let selectedValues = [...config.selectedValues];
            const allOptions = [...config.options];

            // --- DOM Elements ---
            const el = document.getElementById(config.id);
            if (!el) return;

            const body = document.body;
            const hiddenInputContainer = el.querySelector('[data-multiselect-hidden-inputs]');
            const button = el.querySelector('[data-multiselect-button]');
            const pillContainer = el.querySelector('[data-multiselect-pill-container]');
            const placeholder = el.querySelector('[data-multiselect-placeholder]');
            const arrow = el.querySelector('[data-multiselect-arrow]');
            const dropdown = el.querySelector('[data-multiselect-dropdown]');
            const searchInput = el.querySelector('[data-multiselect-search]');
            const optionsList = el.querySelector('[data-multiselect-options-list]');
            const noResults = el.querySelector('[data-multiselect-no-results]');

            // --- Helper Functions ---
            const getOption = (value) => allOptions.find(opt => opt.value == value);
            const isSelected = (value) => selectedValues.includes(String(value));

            // --- Render Functions ---
            function renderHiddenInputs() {
                hiddenInputContainer.innerHTML = '';
                selectedValues.forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${config.name}[]`;
                    input.value = value;
                    hiddenInputContainer.appendChild(input);
                });
            }

            function renderPills() {
                pillContainer.innerHTML = '';
                selectedValues.map(getOption).forEach(option => {
                    const pill = document.createElement('span');
                    pill.className = 'flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-medium text-gray-800';
                    pill.innerHTML = `
                        <span>${option.label}</span>
                        <button
                            type="button"
                            class="text-gray-500 hover:text-gray-900"
                            title="Remove"
                            data-remove-value="${option.value}"
                        >
                            &times;
                        </button>
                    `;
                    pillContainer.appendChild(pill);
                });
                updatePlaceholder();
            }

            function updatePlaceholder() {
                placeholder.style.display = selectedValues.length === 0 ? 'inline' : 'none';
            }

            function renderOptions() {
                optionsList.innerHTML = '';
                const filtered = allOptions.filter(
                    opt => opt.label.toLowerCase().includes(search.toLowerCase())
                );

                noResults.style.display = filtered.length === 0 ? 'block' : 'none';

                filtered.forEach(option => {
                    const li = document.createElement('li');
                    li.className = 'flex cursor-pointer items-center justify-between p-2 hover:bg-indigo-50';
                    li.dataset.selectValue = option.value;

                    const selected = isSelected(option.value);

                    li.innerHTML = `
                        <span class="font-medium">${option.label}</span>
                        ${selected ? `
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-icon lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
                        ` : ''}
                    `;
                    optionsList.appendChild(li);
                });
            }

            // --- Event Handlers ---
            function openDropdown() {
                // 1. Calculate Bounding Box of the Input Button
                const rect = button.getBoundingClientRect();

                // 2. Apply Fixed Positioning to Dropdown
                // Note: We use the 'z-50' utility to ensure it's on top of nearly everything.
                dropdown.style.cssText = `
                    position: fixed;
                    top: ${rect.bottom + 4}px; /* Input bottom + margin */
                    left: ${rect.left}px;
                    width: ${rect.width}px;
                    display: block;
                    z-index: 50;
                `;

                // 3. Lock Scrolling on Body
                body.classList.add('overflow-hidden');

                // 4. Update State and Focus
                open = true;
                button.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500');
                arrow.classList.add('rotate-180');
                searchInput.focus();
                renderOptions();
            }

            /**
             * Reverts the dropdown to its original hidden, relative state.
             */
            function closeDropdown() {
                // 1. Revert Dropdown Positioning and Visibility
                dropdown.style.cssText = 'display: none;';

                // 2. Unlock Scrolling on Body
                body.classList.remove('overflow-hidden');

                // 3. Update State
                open = false;
                button.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500');
                arrow.classList.remove('rotate-180');
                search = '';
                searchInput.value = '';
            }

            function toggleDropdown() {
                if (open) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            }

            function selectOption(value) {
                if (isSelected(value)) {
                    selectedValues = selectedValues.filter(v => v != value);
                } else {
                    selectedValues.push(String(value));
                }
                // Re-render everything
                renderHiddenInputs();
                renderPills();
                renderOptions(); // To update the checkmark
            }

            // --- Event Listeners ---
            button.addEventListener('click', toggleDropdown);

            searchInput.addEventListener('input', (e) => {
                search = e.target.value;
                renderOptions();
            });

            // Event delegation for option selection
            optionsList.addEventListener('click', (e) => {
                const li = e.target.closest('li[data-select-value]');
                if (li) {
                    selectOption(li.dataset.selectValue);
                    e.stopPropagation()
                }
            });

            // Event delegation for pill removal
            pillContainer.addEventListener('click', (e) => {
                const removeButton = e.target.closest('button[data-remove-value]');
                if (removeButton) {
                    e.stopPropagation(); // Stop the click from toggling the dropdown
                    selectOption(removeButton.dataset.removeValue);
                }
            });

            dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Click outside to close
            document.addEventListener('click', (e) => {
                if (open && !el.contains(e.target)) {
                    closeDropdown();
                }
            });

            window.addEventListener('resize', () => {
                if (open) {
                    // Temporarily close and immediately reopen to recalculate position
                    // This is cleaner than calculating bounds during every resize event.
                    closeDropdown();
                    openDropdown();
                }
            });

            // --- Initialization ---
            renderPills();
            updatePlaceholder();
        }
    }

    // This block calls the initializer function for this specific
    // instance of the component.
    document.addEventListener('DOMContentLoaded', () => {
        initMultiSelect({
            id: '{{ $componentId }}',
            name: '{{ $name }}',
            options: @json($options),
            selectedValues: @json($selected),
            placeholder: '{{ $placeholder }}'
        });
    });
</script>
