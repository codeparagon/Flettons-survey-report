@extends('layouts.app')

@section('title', 'Survey Media Upload')

@push('styles')
<style>
    .media-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        color: #1A202C;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #C1EC4A;
    }

    .upload-zone {
        border: 3px dashed #D1D5DB;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #F9FAFB;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .upload-zone:hover {
        border-color: #C1EC4A;
        background: #F0F9FF;
    }

    .upload-zone.dragover {
        border-color: #C1EC4A;
        background: #E0F2FE;
        transform: scale(1.02);
    }

    .upload-zone.uploading {
        border-color: #3B82F6;
        background: #EFF6FF;
    }

    .upload-icon {
        font-size: 48px;
        color: #9CA3AF;
        margin-bottom: 16px;
    }

    .upload-zone:hover .upload-icon {
        color: #C1EC4A;
    }

    .upload-text {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .upload-subtext {
        color: #6B7280;
        font-size: 14px;
    }

    .file-list {
        margin-top: 20px;
    }

    .file-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .file-item:hover {
        border-color: #C1EC4A;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .file-icon {
        font-size: 24px;
        color: #C1EC4A;
        margin-right: 15px;
        width: 30px;
        text-align: center;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 600;
        color: #1A202C;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-size {
        font-size: 12px;
        color: #6B7280;
    }

    .file-status {
        margin-left: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress-container {
        width: 100%;
        height: 12px;
        background: #E5E7EB;
        border-radius: 6px;
        overflow: hidden;
        margin: 8px 0;
        position: relative;
        border: 1px solid #D1D5DB;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #C1EC4A 0%, #B0D93F 100%);
        border-radius: 5px;
        transition: width 0.3s ease;
        width: 0%;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-text {
        font-size: 12px;
        color: #6B7280;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-uploading {
        background: #EFF6FF;
        color: #1D4ED8;
    }

    .status-completed {
        background: #F0FDF4;
        color: #166534;
    }

    .status-error {
        background: #FEF2F2;
        color: #DC2626;
    }

    .status-cancelled {
        background: #FEF3C7;
        color: #D97706;
    }

    .file-remove {
        background: #FEE2E2;
        color: #DC2626;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-remove:hover {
        background: #FECACA;
        transform: scale(1.1);
    }

    .section-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .section-card {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 20px;
    }

    .section-label {
        font-weight: 600;
        color: #1A202C;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label i {
        color: #C1EC4A;
    }

    .upload-btn {
        background: #C1EC4A;
        color: #1A202C;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
        width: 100%;
    }

    .upload-btn:hover {
        background: #B0D93F;
        transform: translateY(-2px);
    }

    .upload-btn:disabled {
        background: #9CA3AF;
        cursor: not-allowed;
        transform: none;
    }

    .hidden {
        display: none;
    }

    .success-message {
        background: #F0FDF4;
        color: #166534;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #BBF7D0;
    }

    .error-message {
        background: #FEF2F2;
        color: #DC2626;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #FECACA;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                    <li class="breadcrumb-item active">Media Upload</li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Survey Media Upload</h2>
                    <p class="text-muted mb-0">Property: {{ $survey->property_address }}</p>
                </div>
                <a href="{{ route('surveyor.surveys.show', $survey) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Survey
                </a>
            </div>

            <!-- Messages -->
            <div id="messages"></div>

            <!-- Video Upload Section -->
            <div class="media-section">
                <h3 class="section-title">
                    <i class="fas fa-video"></i> Video Uploads
                </h3>
                
                <div class="upload-zone" id="video-upload-zone">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Drop video files here or click to select</div>
                    <div class="upload-subtext">MP4, MOV, AVI files up to 100MB each</div>
                    <input type="file" id="video-input" multiple accept="video/*" class="hidden">
                </div>
                
                <div class="file-list" id="video-file-list"></div>
            </div>

            <!-- Exterior Images Section -->
            <div class="media-section">
                <h3 class="section-title">
                    <i class="fas fa-home"></i> Exterior Images
                </h3>
                
                <div class="section-grid">
                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-home"></i> Roof
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="roof">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>

                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-home"></i> Chimney
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="chimney">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>

                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-home"></i> Walls
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="walls">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>

                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-home"></i> Doors
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="doors">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>
                </div>
            </div>

            <!-- Interior Images Section -->
            <div class="media-section">
                <h3 class="section-title">
                    <i class="fas fa-couch"></i> Interior Images
                </h3>
                
                <div class="section-grid">
                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-couch"></i> Floor
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="floor">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>

                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-couch"></i> Windows
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="windows">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>

                    <div class="section-card">
                        <div class="section-label">
                            <i class="fas fa-couch"></i> Utilities
                        </div>
                        <div class="upload-zone section-upload-zone" data-section="utilities">
                            <div class="upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="upload-text">Drop images here</div>
                            <div class="upload-subtext">JPG, PNG files up to 10MB each</div>
                            <input type="file" multiple accept="image/*" class="hidden">
                        </div>
                        <div class="file-list"></div>
                    </div>
                </div>
            </div>

            <!-- Upload All Button -->
            <div class="text-center">
                <button class="upload-btn" id="upload-all-btn" disabled>
                    <i class="fas fa-cloud-upload-alt"></i> Upload All Media
                </button>
            </div>
        </div>
    </div>
</div>

<script>
class MediaUploader {
    constructor() {
        this.uploadQueue = [];
        this.uploading = false;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Video upload zone
        const videoZone = document.getElementById('video-upload-zone');
        const videoInput = document.getElementById('video-input');
        
        videoZone.addEventListener('click', () => videoInput.click());
        videoZone.addEventListener('dragover', this.handleDragOver.bind(this));
        videoZone.addEventListener('dragleave', this.handleDragLeave.bind(this));
        videoZone.addEventListener('drop', (e) => this.handleVideoDrop(e));
        videoInput.addEventListener('change', (e) => this.handleVideoSelect(e));

        // Section upload zones
        document.querySelectorAll('.section-upload-zone').forEach(zone => {
            const input = zone.querySelector('input[type="file"]');
            
            zone.addEventListener('click', () => input.click());
            zone.addEventListener('dragover', this.handleDragOver.bind(this));
            zone.addEventListener('dragleave', this.handleDragLeave.bind(this));
            zone.addEventListener('drop', (e) => this.handleImageDrop(e, zone));
            input.addEventListener('change', (e) => this.handleImageSelect(e, zone));
        });

        // Upload all button
        document.getElementById('upload-all-btn').addEventListener('click', () => this.uploadAll());

        // Load existing media files
        this.loadExistingMedia();
    }

    async loadExistingMedia() {
        try {
            const response = await fetch(`{{ route('surveyor.survey.media.list', $survey) }}`);
            const data = await response.json();
            
            if (data.success && data.media_files.length > 0) {
                // Display existing media files
                data.media_files.forEach(mediaFile => {
                    this.displayExistingMedia(mediaFile);
                });
            }
        } catch (error) {
            console.error('Error loading existing media:', error);
        }
    }

    displayExistingMedia(mediaFile) {
        const container = mediaFile.type === 'video' 
            ? document.getElementById('video-file-list')
            : document.querySelector(`[data-section="${mediaFile.section}"] .file-list`);
        
        if (!container) return;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.dataset.fileId = mediaFile.id;
        fileItem.dataset.type = mediaFile.type;
        fileItem.dataset.section = mediaFile.section || '';
        
        const icon = mediaFile.type === 'video' ? 'fas fa-video' : 'fas fa-image';
        const size = this.formatFileSize(mediaFile.file_size);
        
        fileItem.innerHTML = `
            <div class="file-icon">
                <i class="${icon}"></i>
            </div>
            <div class="file-info">
                <div class="file-name">${mediaFile.original_name}</div>
                <div class="file-size">${size}</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 100%"></div>
                </div>
                <div class="progress-text">Upload complete</div>
            </div>
            <div class="file-status">
                <span class="status-badge status-completed">Uploaded</span>
                <button type="button" class="file-remove" onclick="mediaUploader.removeFile('${mediaFile.id}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.appendChild(fileItem);
    }

    handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('dragover');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('dragover');
    }

    handleVideoDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        this.processVideoFiles(files);
    }

    handleVideoSelect(e) {
        const files = Array.from(e.target.files);
        this.processVideoFiles(files);
    }

    handleImageDrop(e, zone) {
        e.preventDefault();
        zone.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        const section = zone.dataset.section;
        this.processImageFiles(files, section, zone);
    }

    handleImageSelect(e, zone) {
        const files = Array.from(e.target.files);
        const section = zone.dataset.section;
        this.processImageFiles(files, section, zone);
    }

    processVideoFiles(files) {
        files.forEach(file => {
            if (file.type.startsWith('video/')) {
                // Get the actual DOM element
                const container = document.getElementById('video-file-list');
                // Auto-upload immediately
                this.uploadFileImmediately(file, 'video', null, container);
            }
        });
    }

    processImageFiles(files, section, zone) {
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                // Find the file-list in the parent section-card
                const sectionCard = zone.closest('.section-card');
                const fileList = sectionCard.querySelector('.file-list');
                // Auto-upload immediately
                this.uploadFileImmediately(file, 'image', section, fileList);
            }
        });
    }

    async uploadFileImmediately(file, type, section, container) {
        // Check if the same file is currently being processed (uploading or pending)
        const existingItem = this.uploadQueue.find(item => 
            item.file.name === file.name && 
            item.file.size === file.size && 
            item.type === type && 
            item.section === section &&
            (item.status === 'uploading' || item.status === 'pending')
        );
        
        if (existingItem) {
            this.showMessage(`File "${file.name}" is already being processed`, 'error');
            return;
        }
        
        const fileId = Date.now() + Math.random();
        
        // Create file item first
        const fileItem = this.createFileItem(file, fileId, type, section);
        container.appendChild(fileItem);
        
        // Add to upload queue
        const queueItem = {
            id: fileId,
            file: file,
            type: type,
            section: section,
            element: fileItem,
            status: 'pending'
        };
        this.uploadQueue.push(queueItem);
        
        // Start upload immediately
        await this.uploadFile(queueItem);
    }

    createFileItem(file, fileId, type, section) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.dataset.fileId = fileId;
        fileItem.dataset.type = type;
        fileItem.dataset.section = section || '';
        
        const icon = type === 'video' ? 'fas fa-video' : 'fas fa-image';
        const size = this.formatFileSize(file.size);
        
        fileItem.innerHTML = `
            <div class="file-icon">
                <i class="${icon}"></i>
            </div>
            <div class="file-info">
                <div class="file-name">${file.name}</div>
                <div class="file-size">${size}</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress-text">Preparing upload...</div>
            </div>
            <div class="file-status">
                <span class="status-badge status-uploading">Preparing</span>
                <button type="button" class="file-remove" onclick="mediaUploader.removeFile('${fileId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        return fileItem;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    async removeFile(fileId) {
        const fileItem = document.querySelector(`[data-file-id="${fileId}"]`);
        if (!fileItem) return;

        const type = fileItem.dataset.type;
        const section = fileItem.dataset.section || null;
        const item = this.uploadQueue.find(item => item.id === fileId);

        // If file is currently uploading, cancel the upload
        if (item && item.status === 'uploading') {
            item.status = 'cancelled';
            this.updateFileStatus(item, 'cancelled', 'Cancelled');
            this.showMessage(`Upload cancelled for ${item.file.name}`, 'success');
        }
        // If file is uploaded, delete from server first
        else if (item && item.status === 'completed' && item.uploadResponse) {
            const deleted = await this.deleteFile(item.uploadResponse.id, type, section);
            if (!deleted) {
                this.showMessage('Failed to delete file from server', 'error');
                return;
            }
        }

        // Remove from DOM
        fileItem.remove();

        // Remove from queue completely
        this.uploadQueue = this.uploadQueue.filter(item => item.id !== fileId);
        this.updateUploadButton();
        
        // Clear the file input to allow re-selection of the same file
        this.clearFileInput(type, section);
        
        this.showMessage('File removed successfully', 'success');
    }

    clearFileInput(type, section) {
        if (type === 'video') {
            const videoInput = document.getElementById('video-upload');
            if (videoInput) {
                videoInput.value = '';
            }
        } else if (type === 'image' && section) {
            const imageInput = document.querySelector(`input[data-section="${section}"]`);
            if (imageInput) {
                imageInput.value = '';
            }
        }
    }

    updateUploadButton() {
        const uploadBtn = document.getElementById('upload-all-btn');
        const hasFiles = this.uploadQueue.length > 0;
        
        uploadBtn.disabled = !hasFiles || this.uploading;
        uploadBtn.innerHTML = this.uploading 
            ? '<i class="fas fa-spinner fa-spin"></i> Uploading...' 
            : `<i class="fas fa-cloud-upload-alt"></i> Upload All Media (${this.uploadQueue.length})`;
    }

    async uploadAll() {
        if (this.uploading || this.uploadQueue.length === 0) return;

        this.uploading = true;
        this.updateUploadButton();
        this.showMessage('Starting upload process...', 'success');

        for (const item of this.uploadQueue) {
            if (item.status === 'pending') {
                await this.uploadFile(item);
            }
        }

        this.uploading = false;
        this.updateUploadButton();
        this.showMessage('All files uploaded successfully!', 'success');
    }

    async uploadFile(item) {
        const { file, element, type, section } = item;
        
        // Update status to uploading
        this.updateFileStatus(item, 'uploading', 'Uploading...');
        this.showProgress(item, 0);
        
        try {
            // Create FormData for upload
            const formData = new FormData();
            formData.append('files[]', file);
            formData.append('type', type);
            if (section) {
                formData.append('section', section);
            }

            // Simulate progress before actual upload
            await this.simulateProgress(item);
            
            // Upload to backend
            const response = await this.uploadToBackend(formData);
            
            if (response.success) {
                // Update status to completed
                this.updateFileStatus(item, 'completed', 'Uploaded');
                this.showProgress(item, 100);
                this.showMessage(`${file.name} uploaded successfully!`, 'success');
                
                // Store upload response for future use
                item.uploadResponse = response.uploaded_files[0];
                
                // Clear the file input to allow re-selection of the same file
                this.clearFileInput(item.type, item.section);
            } else {
                throw new Error(response.message || 'Upload failed');
            }
            
        } catch (error) {
            // Update status to error
            this.updateFileStatus(item, 'error', 'Failed');
            this.showMessage(`Failed to upload ${file.name}: ${error.message}`, 'error');
        }
    }

    showProgress(item, percentage) {
        const { element } = item;
        const progressBar = element.querySelector('.progress-bar');
        const progressText = element.querySelector('.progress-text');
        
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }
        
        if (progressText) {
            if (percentage === 100) {
                progressText.textContent = 'Upload complete';
            } else if (percentage === 0) {
                progressText.textContent = 'Preparing upload...';
            } else {
                progressText.textContent = `Uploading... ${percentage}%`;
            }
        }
    }

    async simulateProgress(item) {
        // Simulate upload progress
        for (let progress = 10; progress <= 90; progress += 10) {
            // Check if upload was cancelled
            if (item.status === 'cancelled') {
                throw new Error('Upload cancelled');
            }
            
            await new Promise(resolve => setTimeout(resolve, 300));
            this.showProgress(item, progress);
        }
    }

    async uploadToBackend(formData) {
        const response = await fetch(`{{ route('surveyor.survey.media.upload', $survey) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    async deleteFile(fileId, type, section = null) {
        try {
            const response = await fetch(`{{ route('surveyor.survey.media.delete', $survey) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    media_id: fileId,
                    type: type,
                    section: section
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            return result.success;
        } catch (error) {
            console.error('Delete error:', error);
            return false;
        }
    }

    async simulateUpload(item) {
        const { file, element } = item;
        const progressBar = element.querySelector('.progress-bar');
        const progressText = element.querySelector('.progress-text');
        
        // Simulate upload progress
        for (let progress = 0; progress <= 100; progress += 10) {
            await new Promise(resolve => setTimeout(resolve, 200));
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `Uploading... ${progress}%`;
        }

        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    updateFileStatus(item, status, text) {
        const { element } = item;
        const statusBadge = element.querySelector('.status-badge');
        const progressText = element.querySelector('.progress-text');
        
        // Update status badge
        statusBadge.className = `status-badge status-${status}`;
        statusBadge.textContent = text;
        
        // Update progress text
        if (status === 'completed') {
            progressText.textContent = 'Upload complete';
        } else if (status === 'error') {
            progressText.textContent = 'Upload failed';
        }
        
        // Update item status
        item.status = status;
    }

    showMessage(message, type) {
        const messagesContainer = document.getElementById('messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `${type}-message`;
        messageDiv.textContent = message;
        
        messagesContainer.appendChild(messageDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

// Initialize the media uploader
const mediaUploader = new MediaUploader();
</script>
@endsection