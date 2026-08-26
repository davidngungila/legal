import SignaturePad from 'signature_pad';

class SignaturePadManager {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.getElementById(container) : container;
        if (!this.container) return;

        this.name = options.name || 'signature';
        this.required = options.required || false;
        this.existingPath = options.existingPath || '';
        this.canvasWidth = options.canvasWidth || 400;
        this.canvasHeight = options.canvasHeight || 150;

        this.signaturePad = null;
        this.canvas = null;
        this.hiddenInput = null;
        this.activeTab = 'draw';
        this.hasDrawn = false;
        this.hasUploaded = false;

        this.init();
    }

    init() {
        this.createHTML();
        this.setupCanvas();
        this.setupUpload();
        this.setupDeviceDetection();
        this.setupEventListeners();

        if (this.existingPath) {
            this.showExistingSignature();
        }
    }

    createHTML() {
        const wrapper = document.createElement('div');
        wrapper.className = 'signature-pad-wrapper';
        wrapper.id = `${this.name}_wrapper`;

        wrapper.innerHTML = `
            <div class="signature-pad-container border border-gray-300 rounded-lg overflow-hidden">
                <div class="signature-pad-tabs flex bg-gray-50 border-b border-gray-200">
                    <button type="button" class="signature-tab active" data-tab="draw">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Draw
                    </button>
                    <button type="button" class="signature-tab" data-tab="upload">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload
                    </button>
                    <button type="button" class="signature-tab" data-tab="device" id="${this.name}_device_tab" style="display:none;">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Device
                    </button>
                </div>

                <div class="signature-pad-body p-3">
                    <div class="signature-tab-content" data-content="draw" style="display:block;">
                        <div class="relative bg-white rounded border border-gray-200">
                            <canvas id="${this.name}_canvas" width="${this.canvasWidth}" height="${this.canvasHeight}"></canvas>
                            <div class="absolute bottom-2 right-2 flex space-x-1">
                                <button type="button" class="sig-btn sig-btn-undo" onclick="window.signaturePads['${this.name}'].undo()" title="Undo">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4 4M3 10l4-4"/></svg>
                                </button>
                                <button type="button" class="sig-btn sig-btn-clear" onclick="window.signaturePads['${this.name}'].clearCanvas()" title="Clear">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Draw your signature above, or use a connected USB signature pad device</p>
                    </div>

                    <div class="signature-tab-content" data-content="upload" style="display:none;">
                        <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors cursor-pointer" id="${this.name}_upload_area">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400">JPG, PNG (max 5MB)</p>
                            <input type="file" id="${this.name}_file_input" accept=".jpg,.jpeg,.png" class="hidden">
                        </div>
                        <div class="upload-preview mt-3 hidden" id="${this.name}_preview">
                            <img id="${this.name}_preview_img" class="max-h-32 mx-auto rounded border border-gray-200" alt="Signature preview">
                            <button type="button" class="text-xs text-red-500 hover:text-red-700 mt-1" onclick="window.signaturePads['${this.name}'].removeUpload()">Remove</button>
                        </div>
                    </div>

                    <div class="signature-tab-content" data-content="device" style="display:none;">
                        <div class="text-center py-8" id="${this.name}_device_status">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-600 mb-2">Connect a USB signature pad device</p>
                            <p class="text-xs text-gray-400 mb-3">Compatible with Wacom STU, Topaz SigLite, and other HID-compatible devices</p>
                            <button type="button" class="sig-btn-primary" onclick="window.signaturePads['${this.name}'].connectDevice()">
                                Detect Device
                            </button>
                            <p class="text-xs text-gray-400 mt-2" id="${this.name}_device_msg"></p>
                        </div>
                        <div class="hidden text-center py-8" id="${this.name}_device_active">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse inline-block mr-2"></div>
                            <span class="text-sm text-green-600 font-medium">Device connected - Sign on the pad</span>
                            <p class="text-xs text-gray-400 mt-1" id="${this.name}_device_info"></p>
                        </div>
                    </div>
                </div>

                <div class="signature-pad-footer flex items-center justify-between p-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center text-xs text-gray-500" id="${this.name}_status">
                        ${this.existingPath ? '<span class="text-green-600">Signature on file</span>' : 'No signature yet'}
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" class="sig-btn-secondary" onclick="window.signaturePads['${this.name}'].clearAll()">Clear All</button>
                        <button type="button" class="sig-btn-primary" onclick="window.signaturePads['${this.name}'].applySignature()">Apply Signature</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="${this.name}" id="${this.name}_hidden" value="">
        `;

        this.container.appendChild(wrapper);
    }

    setupCanvas() {
        this.canvas = this.container.querySelector(`#${this.name}_canvas`);
        if (!this.canvas) return;

        this.signaturePad = new SignaturePad(this.canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: '#1f2937',
            velocityFilterWeight: 0.7,
            minWidth: 0.5,
            maxWidth: 2.5,
        });

        window.signaturePads = window.signaturePads || {};
        window.signaturePads[this.name] = this;

        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    resizeCanvas() {
        if (!this.canvas) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        this.canvas.width = this.canvas.offsetWidth * ratio;
        this.canvas.height = this.canvas.offsetHeight * ratio;
        this.canvas.getContext('2d').scale(ratio, ratio);
        this.signaturePad.clear();
    }

    setupUpload() {
        const fileInput = this.container.querySelector(`#${this.name}_file_input`);
        const uploadArea = this.container.querySelector(`#${this.name}_upload_area`);

        if (!fileInput || !uploadArea) return;

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
            const files = e.dataTransfer.files;
            if (files.length) this.handleFileUpload(files[0]);
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) this.handleFileUpload(e.target.files[0]);
        });
    }

    handleFileUpload(file) {
        if (!file.type.match(/^image\/(jpeg|png)$/)) {
            this.showStatus('Please upload a JPG or PNG image', 'error');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            this.showStatus('File size must be less than 5MB', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = this.container.querySelector(`#${this.name}_preview`);
            const previewImg = this.container.querySelector(`#${this.name}_preview_img`);
            const uploadArea = this.container.querySelector(`#${this.name}_upload_area`);

            if (previewImg) previewImg.src = e.target.result;
            if (preview) preview.classList.remove('hidden');
            if (uploadArea) uploadArea.classList.add('hidden');

            this.hasUploaded = true;
            this.uploadedData = e.target.result;
            this.showStatus('Signature image loaded - click Apply', 'success');
        };
        reader.readAsDataURL(file);
    }

    removeUpload() {
        const preview = this.container.querySelector(`#${this.name}_preview`);
        const uploadArea = this.container.querySelector(`#${this.name}_upload_area`);
        const fileInput = this.container.querySelector(`#${this.name}_file_input`);

        if (preview) preview.classList.add('hidden');
        if (uploadArea) uploadArea.classList.remove('hidden');
        if (fileInput) fileInput.value = '';

        this.hasUploaded = false;
        this.uploadedData = null;
        this.showStatus('No signature yet', '');
    }

    setupDeviceDetection() {
        if (typeof navigator.usb === 'undefined' && typeof navigator.hid === 'undefined') return;

        const deviceTab = this.container.querySelector(`#${this.name}_device_tab`);
        if (deviceTab) deviceTab.style.display = '';

        if (typeof navigator.hid !== 'undefined') {
            navigator.hid.addEventListener('connect', () => {
                this.showStatus('Signature device detected', 'success');
            });
            navigator.hid.addEventListener('disconnect', () => {
                this.showStatus('Signature device disconnected', 'warning');
            });
        }
    }

    async connectDevice() {
        const msgEl = this.container.querySelector(`#${this.name}_device_msg`);
        const statusEl = this.container.querySelector(`#${this.name}_device_status`);
        const activeEl = this.container.querySelector(`#${this.name}_device_active`);
        const infoEl = this.container.querySelector(`#${this.name}_device_info`);

        if (typeof navigator.hid === 'undefined') {
            if (msgEl) msgEl.textContent = 'WebHID is not supported in this browser. Use Chrome or Edge.';
            return;
        }

        try {
            const devices = await navigator.hid.requestDevice({
                filters: [
                    { vendorId: 0x056A },
                    { vendorId: 0x0403 },
                    { vendorId: 0x0874 },
                    { vendorId: 0x0627 },
                ]
            });

            if (devices && devices.length > 0) {
                const device = devices[0];
                if (msgEl) msgEl.textContent = '';
                if (statusEl) statusEl.classList.add('hidden');
                if (activeEl) activeEl.classList.remove('hidden');
                if (infoEl) infoEl.textContent = `${device.productName || 'Signature Device'} connected`;

                this.hidDevice = device;
                this.setupHIDListeners(device);
                this.showStatus('USB device connected - sign on the pad', 'success');
            }
        } catch (err) {
            if (msgEl) msgEl.textContent = err.message || 'No device found or access denied';
        }
    }

    setupHIDListeners(device) {
        device.addEventListener('inputreport', (event) => {
            const data = new Uint8Array(event.data.buffer);
            this.processHIDData(data, event.reportId);
        });
    }

    processHIDData(data, reportId) {
        if (data.length < 6) return;

        const penX = (data[2] | (data[3] << 8)) & 0x7FF;
        const penY = (data[4] | (data[5] << 8)) & 0x7FF;
        const pressure = (data[6] | (data[7] << 8)) & 0x3FF;
        const isDown = (data[1] & 0x20) !== 0;

        const canvasRect = this.canvas.getBoundingClientRect();
        const x = (penX / 2047) * canvasRect.width;
        const y = (penY / 2047) * canvasRect.height;
        const normalizedPressure = pressure / 1023;

        if (isDown) {
            const point = {
                x: x,
                y: y,
                pressure: normalizedPressure,
            };

            if (!this.hidDrawing) {
                this.signaturePad._beginNewPoint(point.x, point.y, point.pressure);
                this.hidDrawing = true;
            } else {
                this.signaturePad._addPoint(point);
            }
        } else if (this.hidDrawing) {
            this.signaturePad._endStroke();
            this.hidDrawing = false;
        }
    }

    setupEventListeners() {
        if (this.signaturePad) {
            this.signaturePad.addEventListener('beginStroke', () => {
                this.hasDrawn = true;
            });
        }

        const tabs = this.container.querySelectorAll('.signature-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.dataset.tab;
                this.switchTab(tabName);
            });
        });
    }

    switchTab(tabName) {
        this.activeTab = tabName;

        this.container.querySelectorAll('.signature-tab').forEach(t => t.classList.remove('active'));
        this.container.querySelector(`.signature-tab[data-tab="${tabName}"]`)?.classList.add('active');

        this.container.querySelectorAll('.signature-tab-content').forEach(c => c.style.display = 'none');
        this.container.querySelector(`.signature-tab-content[data-content="${tabName}"]`).style.display = '';
    }

    showExistingSignature() {
        if (!this.existingPath) return;
        const preview = this.container.querySelector(`#${this.name}_preview`);
        const previewImg = this.container.querySelector(`#${this.name}_preview_img`);
        const uploadArea = this.container.querySelector(`#${this.name}_upload_area`);

        if (previewImg) previewImg.src = `/storage/${this.existingPath}`;
        if (preview) preview.classList.remove('hidden');
        if (uploadArea) uploadArea.classList.add('hidden');

        this.hiddenInput = this.container.querySelector(`#${this.name}_hidden`);
        if (this.hiddenInput) this.hiddenInput.value = this.existingPath;
    }

    clearCanvas() {
        if (this.signaturePad) {
            this.signaturePad.clear();
            this.hasDrawn = false;
        }
    }

    clearAll() {
        this.clearCanvas();
        this.removeUpload();
        this.hasDrawn = false;
        this.hasUploaded = false;

        this.hiddenInput = this.container.querySelector(`#${this.name}_hidden`);
        if (this.hiddenInput) this.hiddenInput.value = '';

        this.showStatus('No signature yet', '');
    }

    undo() {
        if (this.signaturePad) {
            const data = this.signaturePad.toData();
            if (data.length > 0) {
                data.pop();
                this.signaturePad.fromData(data);
            }
        }
    }

    applySignature() {
        let signatureData = null;

        if (this.activeTab === 'draw' && this.hasDrawn && !this.signaturePad.isEmpty()) {
            signatureData = this.signaturePad.toDataURL('image/png');
        } else if (this.activeTab === 'upload' && this.hasUploaded && this.uploadedData) {
            signatureData = this.uploadedData;
        } else if (this.activeTab === 'device' && !this.signaturePad.isEmpty()) {
            signatureData = this.signaturePad.toDataURL('image/png');
        }

        if (!signatureData) {
            this.showStatus('Please provide a signature first', 'error');
            return false;
        }

        this.hiddenInput = this.container.querySelector(`#${this.name}_hidden`);
        if (this.hiddenInput) this.hiddenInput.value = signatureData;

        this.showStatus('Signature applied', 'success');
        return true;
    }

    hasSignature() {
        return (this.activeTab === 'draw' && this.hasDrawn && !this.signaturePad.isEmpty()) ||
               (this.activeTab === 'upload' && this.hasUploaded) ||
               (this.activeTab === 'device' && this.hasDrawn);
    }

    getSignatureData() {
        if (!this.signaturePad.isEmpty()) {
            return this.signaturePad.toDataURL('image/png');
        }
        return this.uploadedData || null;
    }

    showStatus(message, type) {
        const statusEl = this.container.querySelector(`#${this.name}_status`);
        if (!statusEl) return;

        statusEl.textContent = message;
        statusEl.className = 'flex items-center text-xs ';

        switch (type) {
            case 'success':
                statusEl.classList.add('text-green-600');
                break;
            case 'error':
                statusEl.classList.add('text-red-500');
                break;
            case 'warning':
                statusEl.classList.add('text-yellow-600');
                break;
            default:
                statusEl.classList.add('text-gray-500');
        }
    }

    destroy() {
        if (this.signaturePad) {
            this.signaturePad.off();
        }
        delete window.signaturePads?.[this.name];
    }
}

window.SignaturePadManager = SignaturePadManager;

export default SignaturePadManager;
