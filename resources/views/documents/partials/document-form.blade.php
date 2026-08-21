<form id="documentForm" class="space-y-4" enctype="multipart/form-data">
    <input type="hidden" name="document_id" id="documentId">
    <input type="hidden" name="replace_file" id="replaceFile" value="false">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="documentTitle" required maxlength="255"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter document title">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" id="documentDescription" rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter document description"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label>
        <select name="document_type" id="documentType" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Select Document Type</option>
            @foreach($document_types as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <input type="text" name="category" id="documentCategory" maxlength="100"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g., HR, Finance, Operations, Legal">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Version <span class="text-red-500">*</span></label>
            <input type="text" name="version" id="documentVersion" required maxlength="20" value="1.0"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., 1.0, 2.1, 3.0">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" id="documentStatus" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
            <input type="date" name="effective_date" id="documentEffectiveDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
            <input type="date" name="expiry_date" id="documentExpiryDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_required" id="documentIsRequired" value="1"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Required Document</span>
            </label>
        </div>
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_public" id="documentIsPublic" value="1" checked
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Public (Visible to Employees)</span>
            </label>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
        <div class="flex flex-wrap gap-2" id="tagsContainer">
            <input type="text" id="tagInput" placeholder="Add tag, press Enter"
                class="flex-1 min-w-[150px] px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <div id="tagsList" class="flex flex-wrap gap-2"></div>
        </div>
        <input type="hidden" name="tags" id="tagsInput">
        <p class="text-xs text-gray-500 mt-1">Press Enter to add tags (e.g., HR, Policy, Compliance)</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="fileDropZone">
            <input type="file" name="file" id="documentFile" accept=".pdf,.doc,.docx" class="hidden">
            <i data-feather="upload-cloud" class="w-10 h-10 text-gray-400 mx-auto mb-2"></i>
            <p class="text-gray-600 mb-2">Drag & drop a file here, or click to browse</p>
            <p class="text-sm text-gray-400">PDF, DOC, DOCX up to 10MB</p>
            <button type="button" onclick="document.getElementById('documentFile').click()"
                class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Choose File
            </button>
        </div>
        <div id="selectedFileInfo" class="hidden mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i data-feather="file" class="w-5 h-5 text-indigo-600" id="fileIcon"></i>
                <div>
                    <p class="font-medium text-gray-900" id="fileName"></p>
                    <p class="text-sm text-gray-500" id="fileSize"></p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="previewSelectedFile()" id="previewFileBtn"
                    class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors flex items-center space-x-1.5">
                    <i data-feather="eye" class="w-4 h-4"></i>
                    <span>Preview</span>
                </button>
                <button type="button" onclick="clearFile()" class="text-red-600 hover:text-red-800">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div id="existingFileInfo" class="hidden mt-3 p-3 bg-blue-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i data-feather="file" class="w-5 h-5 text-blue-600"></i>
                <div>
                    <p class="font-medium text-gray-900">Current File: <span id="existingFileName"></span></p>
                    <p class="text-sm text-gray-500" id="existingFileSize"></p>
                </div>
            </div>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" id="replaceFileCheckbox" onchange="toggleReplaceFile()"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Replace with new file</span>
            </label>
        </div>
    </div>

    <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <button type="button" onclick="closeDocumentModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
        <button type="button" onclick="previewDocument()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center space-x-2">
            <i data-feather="eye" class="w-4 h-4"></i>
            <span>Preview</span>
        </button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Document</button>
    </div>
</form>