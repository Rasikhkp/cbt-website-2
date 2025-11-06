<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('teacher.questions.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                Questions</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Import Question') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-5xl py-12 mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - Upload Section -->
        <div class="bg-white h-fit rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-800">Upload File</h2>
            </div>
            <p class="text-sm text-gray-500 mb-6">Drag & drop a file or choose one from your computer</p>

            <div id="dropzone" class="transition-all border-2 relative cursor-pointer hover:bg-gray-100 border-dashed border-gray-300 rounded p-12 text-center mb-4">
                <div class="w-full h-full absolute inset-0"></div>
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p id="drag-text" class="text-gray-700 font-medium mb-1">Drag & drop file here</p>
                <p id="drag-small-text" class="text-sm text-gray-500 mb-1">or click to choose file</p>
                <p class="text-xs text-gray-400">Format: .xlsx, .csv • Max: 10MB</p>
                <input id="file-input" type="file" class="hidden" />
            </div>

            <div class="flex justify-end mb-4">
                <x-primary-button disabled id="upload-btn" onclick="uploadFile()">Upload File</x-primary-button>
            </div>

            <div id="error-container" class="flex flex-col gap-2"></div>
        </div>

        <!-- Right Column - Top Section -->
        <div class="space-y-6">
            <!-- Template File -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Template File</h2>
                </div>
                <p class="text-sm text-gray-500 mb-4">Download the template for the correct format</p>

                <a href="{{ route('download.template') }}" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium flex items-center justify-center gap-2 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Template
                </a>
            </div>

            <!-- Data Format -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Data Format</h2>
                </div>
                <p class="text-sm text-gray-500 mb-4">Data format for importing data</p>

                <div class="mt-3 py-2 px-4 rounded border border-gray-200 font-medium bg-gray-100">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Type (Required)</span>
                        <span class="text-sm text-gray-500">[mcq, short, long]</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Question Text (Required)</span>
                        <span class="text-sm text-gray-500">Text</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Explanation (Required for short and long type)</span>
                        <span class="text-sm text-gray-500">Text</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Points (Required)</span>
                        <span class="text-sm text-gray-500">Number</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Difficulty (Optional, default to medium)</span>
                        <span class="text-sm text-gray-500">[easy, medium, hard]</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Tags (Optional)</span>
                        <span class="text-sm text-gray-500">Text</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">Option A - F (Required for mcq type)</span>
                        <span class="text-sm text-gray-500">Text</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropzone = document.querySelector("#dropzone")
        const dragText = dropzone.querySelector("#drag-text")
        const dragSmallText = dropzone.querySelector("#drag-small-text")
        const fileInput = dropzone.querySelector('#file-input')
        const uploadBtn = document.querySelector('#upload-btn')
        const errorContainer = document.querySelector('#error-container')
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content

        let isFileHovering = false
        let selectedFile;

        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault()

            if (!isFileHovering) {
                isFileHovering = true
                dragText.innerText = "Drop file here"
                dropzone.classList.add('bg-gray-100')
            }
        })

        dropzone.addEventListener('drop', (event) => {
            event.preventDefault()
            selectedFile = event.dataTransfer.files[0]

            isFileHovering = false
            dragText.innerText = selectedFile.name
            dragSmallText.innerText = fileSizeFormat(selectedFile.size)
            dropzone.classList.remove('bg-gray-100')
            uploadBtn.disabled = false
        })

        dropzone.addEventListener('dragleave', () => {
            console.log('leave')
            if (isFileHovering) {
                isFileHovering = false
                dragText.innerText = "Drag & drop file here"
                dropzone.classList.remove('bg-gray-100')
            }
        })

        dropzone.addEventListener('click', () => {
            fileInput.click()
        })

        fileInput.addEventListener('change', () => {
            selectedFile = event.target.files[0]

            if (selectedFile) {
                dragText.innerText = selectedFile.name
                dragSmallText.innerText = fileSizeFormat(selectedFile.size)
                uploadBtn.disabled = false
            }
        })
        async function uploadFile() {
            errorContainer.innerHTML = ''
            const formData = new FormData()
            formData.append('file', selectedFile)

            try {
                const res = await fetch('/upload-file', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    method: 'POST',
                    body: formData
                })

                // Try to parse JSON no matter what
                const data = await res.json().catch(() => null)

                // Handle success
                if (res.ok) {
                    console.log('✅ Success:', data)
                    showToast({
                      title: "Success!",
                      message: "Your questions was imported successfully.",
                      type: "success"
                    });

                    return
                }

                // Handle validation errors (422)
                if (res.status === 422) {
                    console.warn('⚠️ Validation Errors:', data.errors)
                    // Example: show messages per row
                    data.errors.forEach(err => {
                        console.log(`Row ${err.row} - ${err.attribute}: ${err.errors.join(', ')}`)
                        const errorElement = document.createElement('div')
                        errorElement.className = "py-3 px-4 flex gap-2 border border-red-500 rounded text-red-500"
                        errorElement.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert-icon lucide-circle-alert"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                               Row ${err.row}: ${err.errors.join(', ')}
                            `
                        errorContainer.appendChild(errorElement)
                    })

                    return
                }

                // Handle other errors
                console.error('❌ Error response:', data)
            } catch (error) {
                console.error('🔥 Fetch failed:', error)
            }
        }

        function handleImagePreview(input, isOptionImage) {
            const file = input.files[0]
            const img = input.nextElementSibling
            const imgPreview = input.parentElement
            const addImageBtn = imgPreview.parentElement.querySelector('.add-image-btn')

            if (file) {
                const reader = new FileReader()
                reader.onload = e => {
                    img.src = e.target.result
                    imgPreview.classList.remove('hidden')
                    if (isOptionImage) {
                        addImageBtn.classList.add('hidden')
                    }
                }

                reader.readAsDataURL(file)
            }
        }
    </script>
</x-app-layout>
