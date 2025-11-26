@extends('layouts.survey-mock')

@section('title', 'Media Files')

@section('content')
    <div class="survey-media-mock-content">
        <!-- Integrated Header Bar -->
        <div class="survey-media-mock-header-bar">
            <div class="survey-media-mock-header-left">
                <span
                    class="survey-media-mock-address">{{ $survey->full_address ?? '123, Sample Street, Kent DA9 9ZT' }}</span>
            </div>
            <div class="survey-media-mock-header-right">
                <div class="survey-media-mock-jobref">
                    <span class="survey-media-mock-jobref-label">Job Reference</span>
                    <span class="survey-media-mock-jobref-value">{{ $survey->job_reference ?? '12SE39DT-SH' }}</span>
                </div>
                <div class="survey-media-mock-header-icons">
                    <button type="button" class="survey-media-mock-icon-btn" title="Settings">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <button type="button" class="survey-media-mock-icon-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="survey-media-mock-icon-btn" id="survey-media-mock-profile-btn"
                        title="Profile">
                        <i class="fas fa-user"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="survey-media-mock-body">
            <!-- Back Button -->
            <a href="{{ route('surveyor.surveys.data.mock', $survey) }}" class="survey-media-mock-back-btn">
                <i class="fas fa-chevron-left"></i>
            </a>

            <!-- Main Title -->
            <h1 class="survey-media-mock-title" id="media-title">Automatic Mode</h1>

            <!-- Upload Section (Default View) -->
            <div class="survey-media-mock-upload-section" id="upload-section">
                <!-- Drag and Drop Box -->
                <div class="survey-media-mock-upload-box" id="upload-box">
                    <div class="survey-media-mock-upload-content">
                        <p class="survey-media-mock-upload-text">
                            Drag and Drop Your<br>
                            Videos and Photos or<br>
                            <strong>Upload</strong>
                        </p>
                    </div>
                    <input type="file" id="media-file-input" multiple accept="image/*,video/*" style="display: none;">
                </div>

                <!-- File Queue (Shown after files are selected, before upload) -->
                <div class="survey-media-mock-file-queue" id="file-queue" style="display: none;">
                    <div class="survey-media-mock-file-queue-header">
                        <h3 class="survey-media-mock-file-queue-title">Selected Files</h3>
                        <button type="button" class="survey-media-mock-clear-queue-btn" id="clear-queue-btn">Clear
                            All</button>
                    </div>
                    <div class="survey-media-mock-file-queue-list" id="file-queue-list">
                        <!-- Files will be added here -->
                    </div>
                </div>

                <!-- Upload Files Button -->
                <div class="survey-media-mock-upload-actions">
                    <button type="button" class="survey-media-mock-upload-btn" id="upload-files-btn"
                        style="display: none;">
                        Upload Files
                    </button>
                </div>
            </div>

            <!-- Upload Progress Section (Hidden by default) -->
            <div class="survey-media-mock-progress-section" id="progress-section" style="display: none;">
                <h2 class="survey-media-mock-progress-title">Files Uploading for Transcription...</h2>
                <div class="survey-media-mock-progress-list" id="progress-list">
                    <!-- Progress items will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Poppins Font Family Throughout - Exclude Font Awesome icons */
        .survey-media-mock-content {
            font-family: 'Poppins', sans-serif;
            min-height: calc(100vh - 60px);
            background: #FFFFFF;
        }

        /* Ensure Font Awesome icons use correct font */
        .survey-media-mock-content i,
        .survey-media-mock-content i.fas,
        .survey-media-mock-content i.far,
        .survey-media-mock-content i.fab {
            font-family: "Font Awesome 5 Free", "Font Awesome 5 Pro", "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome" !important;
            font-weight: 900 !important;
            display: inline-block !important;
            font-style: normal !important;
        }

        /* Header Bar */
        .survey-media-mock-header-bar {
            background: #FFFFFF;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }

        .survey-media-mock-header-left {
            display: flex;
            align-items: center;
        }

        .survey-media-mock-address {
            font-size: 16px;
            color: #1A202C;
        }

        .survey-media-mock-header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .survey-media-mock-jobref {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .survey-media-mock-jobref-label {
            font-size: 12px;
            color: #94A3B8;
            text-transform: uppercase;
        }

        .survey-media-mock-jobref-value {
            font-size: 16px;
            color: #1A202C;
        }

        .survey-media-mock-header-icons {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .survey-media-mock-icon-btn {
            background: none;
            border: none;
            color: #64748B;
            font-size: 18px;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .survey-media-mock-icon-btn:hover {
            background: #F1F5F9;
            color: #475569;
        }

        .survey-media-mock-icon-btn i {
            font-size: 16px;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Main Body */
        .survey-media-mock-body {
            padding: 2rem 1.5rem;
            background: #FFFFFF;
            min-height: calc(100vh - 140px);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Back Button */
        .survey-media-mock-back-btn {
            position: absolute;
            top: 2rem;
            left: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            background: #C1EC4A;
            color: #1A202C;
            text-decoration: none;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .survey-media-mock-back-btn:hover {
            background: #A8D043;
            color: #1A202C;
        }

        .survey-media-mock-back-btn i {
            font-size: 16px;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Main Title */
        .survey-media-mock-title {
            font-size: 36px;
            color: #1A202C;
            margin: 0 0 3rem 0;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.02em;
        }

        /* Upload Section */
        .survey-media-mock-upload-section {
            width: 100%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        /* Drag and Drop Box */
        .survey-media-mock-upload-box {
            width: 100%;
            max-width: 600px;
            min-height: 300px;
            background: #E0F2FE;
            border: 1px solid rgba(148, 163, 184, 0.4);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .survey-media-mock-upload-box:hover {
            border-color: #C1EC4A;
            background: #DBEAFE;
        }

        .survey-media-mock-upload-box.dragover {
            border-color: #C1EC4A;
            background: #BFDBFE;
            border-width: 2px;
        }

        .survey-media-mock-upload-content {
            text-align: center;
            padding: 2rem;
        }

        .survey-media-mock-upload-text {
            font-size: 16px;
            color: #1A202C;
            line-height: 1.8;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-upload-text strong {
            color: #1A202C;
            font-size: 16px;
        }

        /* File Queue */
        .survey-media-mock-file-queue {
            width: 100%;
            max-width: 600px;
            margin-top: 1.5rem;
        }

        .survey-media-mock-file-queue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .survey-media-mock-file-queue-title {
            font-size: 18px;
            color: #1A202C;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-clear-queue-btn {
            padding: 0.5rem 1rem;
            background: transparent;
            color: #EF4444;
            border: 1px solid #EF4444;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-clear-queue-btn:hover {
            background: #EF4444;
            color: #FFFFFF;
        }

        .survey-media-mock-file-queue-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .survey-media-mock-file-queue-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #FFFFFF;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .survey-media-mock-file-queue-item:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-color: rgba(148, 163, 184, 0.3);
        }

        .survey-media-mock-file-queue-item-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }

        .survey-media-mock-file-queue-item-icon {
            font-size: 20px;
            color: #64748B;
            flex-shrink: 0;
        }

        .survey-media-mock-file-queue-item-details {
            flex: 1;
            min-width: 0;
        }

        .survey-media-mock-file-queue-item-name {
            font-size: 15px;
            color: #1A202C;
            margin: 0 0 0.25rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-file-queue-item-size {
            font-size: 13px;
            color: #64748B;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-file-queue-item-remove {
            background: transparent;
            border: none;
            color: #EF4444;
            font-size: 18px;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .survey-media-mock-file-queue-item-remove:hover {
            background: #FEE2E2;
            color: #DC2626;
        }

        .survey-media-mock-file-queue-item-remove i {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Upload Actions */
        .survey-media-mock-upload-actions {
            display: flex;
            justify-content: center;
        }

        .survey-media-mock-upload-btn {
            padding: 0.875rem 2rem;
            background: #1E293B;
            color: #FFFFFF;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-upload-btn:hover {
            background: #0F172A;
        }

        /* Progress Section */
        .survey-media-mock-progress-section {
            width: 100%;
            max-width: 800px;
        }

        .survey-media-mock-progress-title {
            font-size: 28px;
            color: #1A202C;
            margin: 0 0 2rem 0;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.02em;
        }

        .survey-media-mock-progress-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .survey-media-mock-progress-item {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: #FFFFFF;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .survey-media-mock-progress-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #C1EC4A 0%, #A8D043 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .survey-media-mock-progress-item.uploading::before {
            opacity: 1;
        }

        .survey-media-mock-progress-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12), 0 2px 6px rgba(0, 0, 0, 0.08);
            border-color: rgba(148, 163, 184, 0.25);
            transform: translateY(-1px);
        }

        .survey-media-mock-progress-item.upload-error {
            border-color: #EF4444;
            background: #FEF2F2;
        }

        .survey-media-mock-progress-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .survey-media-mock-progress-item-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }

        .survey-media-mock-progress-item-icon {
            font-size: 20px;
            color: #64748B;
            flex-shrink: 0;
        }

        .survey-media-mock-progress-item-icon i {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .survey-media-mock-progress-item-details {
            flex: 1;
            min-width: 0;
        }

        .survey-media-mock-progress-item-name {
            font-size: 15px;
            color: #1A202C;
            margin: 0 0 0.25rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-progress-item-status {
            font-size: 13px;
            color: #64748B;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .survey-media-mock-progress-item-status.uploading {
            color: #475569;
        }

        .survey-media-mock-progress-item-status.completed {
            color: #10B981;
        }

        .survey-media-mock-progress-item-status.error {
            color: #EF4444;
        }

        .survey-media-mock-progress-bar-wrapper {
            width: 100%;
            height: 8px;
            background: #E2E8F0;
            border-radius: 100px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .survey-media-mock-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #475569 0%, #334155 100%);
            border-radius: 100px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .survey-media-mock-progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    transparent 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .survey-media-mock-progress-percentage {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            font-size: 13px;
            color: #475569;
            font-family: 'Poppins', sans-serif;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.125rem 0.5rem;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .survey-media-mock-progress-item.upload-error .survey-media-mock-progress-bar {
            background: linear-gradient(90deg, #EF4444 0%, #DC2626 100%);
        }

        .survey-media-mock-progress-item.upload-error .survey-media-mock-progress-percentage {
            color: #EF4444;
        }

        @media (max-width: 768px) {
            .survey-media-mock-body {
                padding: 1.5rem 1rem;
            }

            .survey-media-mock-title {
                font-size: 24px;
                margin-bottom: 2rem;
            }

            .survey-media-mock-upload-box {
                min-height: 250px;
            }

            .survey-media-mock-upload-text {
                font-size: 16px;
            }

            .survey-media-mock-header-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .survey-media-mock-header-right {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const surveyId = {{ $survey->id }};
            const uploadUrl = "{{ route('aws.transcription') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            let uploadQueue = [];
            let uploadInProgress = false;
            let activeUploads = [];

            // File input change handler
            $('#media-file-input').on('change', function(e) {
                const files = Array.from(e.target.files);
                handleFiles(files);
            });

            // Click on upload box to trigger file input (only when no files in queue)
            $('#upload-box').on('click', function(e) {
                // Don't trigger if clicking on a child element that has its own handler
                if ($(e.target).closest('.survey-media-mock-upload-content').length) {
                    if (uploadQueue.length === 0) {
                        $('#media-file-input').click();
                    }
                }
            });

            // Drag and drop handlers
            $('#upload-box').on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $('#upload-box').on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $('#upload-box').on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');

                const files = Array.from(e.originalEvent.dataTransfer.files);
                handleFiles(files);
            });

            // Handle selected files
            function handleFiles(files) {
                if (files.length === 0) return;

                // Filter for images and videos only
                const mediaFiles = files.filter(file => {
                    return file.type.startsWith('image/') || file.type.startsWith('video/');
                });

                if (mediaFiles.length === 0) {
                    alert('Please select image or video files only.');
                    return;
                }

                // Validate file sizes (100MB max)
                const maxSize = 100 * 1024 * 1024; // 100MB in bytes
                const invalidFiles = mediaFiles.filter(file => file.size > maxSize);

                if (invalidFiles.length > 0) {
                    alert(
                        `Some files exceed the maximum size of 100MB:\n${invalidFiles.map(f => f.name).join('\n')}`
                        );
                    return;
                }

                // Add files to queue (append to existing queue)
                const newFiles = mediaFiles.map((file, index) => ({
                    file: file,
                    name: file.name,
                    type: file.type.startsWith('video/') ? 'video' : 'image',
                    size: file.size,
                    progress: 0,
                    status: 'pending',
                    index: uploadQueue.length + index,
                    id: 'file_' + Date.now() + '_' + index
                }));

                uploadQueue = uploadQueue.concat(newFiles);

                // Update file queue display
                updateFileQueue();
            }

            // Update file queue display
            function updateFileQueue() {
                if (uploadQueue.length === 0) {
                    $('#file-queue').hide();
                    $('#upload-files-btn').hide();
                    return;
                }

                $('#file-queue').show();
                $('#upload-files-btn').show();

                const $queueList = $('#file-queue-list');
                $queueList.empty();

                uploadQueue.forEach((item, index) => {
                    const fileIcon = item.type === 'video' ? 'fa-video' : 'fa-image';
                    const fileSize = formatFileSize(item.size);

                    const queueItem = $('<div>')
                        .addClass('survey-media-mock-file-queue-item')
                        .attr('data-file-id', item.id)
                        .html(`
                    <div class="survey-media-mock-file-queue-item-info">
                        <i class="fas ${fileIcon} survey-media-mock-file-queue-item-icon"></i>
                        <div class="survey-media-mock-file-queue-item-details">
                            <div class="survey-media-mock-file-queue-item-name">${item.name}</div>
                            <div class="survey-media-mock-file-queue-item-size">${fileSize}</div>
                        </div>
                    </div>
                    <button type="button" class="survey-media-mock-file-queue-item-remove" data-file-id="${item.id}">
                        <i class="fas fa-times"></i>
                    </button>
                `);
                    $queueList.append(queueItem);
                });

                // Attach remove handlers
                $('.survey-media-mock-file-queue-item-remove').on('click', function() {
                    const fileId = $(this).data('file-id');
                    removeFileFromQueue(fileId);
                });
            }

            // Remove file from queue
            function removeFileFromQueue(fileId) {
                uploadQueue = uploadQueue.filter(item => item.id !== fileId);
                updateFileQueue();
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Clear all files from queue
            $('#clear-queue-btn').on('click', function() {
                uploadQueue = [];
                updateFileQueue();
            });

            // Start real S3 uploads
            function startUpload() {
                if (uploadInProgress || uploadQueue.length === 0) return;

                uploadInProgress = true;
                activeUploads = [];

                // Hide upload section and show progress section
                $('#upload-section').hide();
                $('#progress-section').show();

                // Update title based on file types
                const hasVideos = uploadQueue.some(item => item.type === 'video');
                $('#media-title').text(hasVideos ? 'Files Uploading for Transcription...' : 'Files Uploading...');

                // Clear previous progress list
                $('#progress-list').empty();

                // Create progress items with actual file names
                uploadQueue.forEach((item, index) => {
                    const fileIcon = item.type === 'video' ? 'fa-video' : 'fa-image';
                    const fileSize = formatFileSize(item.size);

                    const progressItem = $('<div>')
                        .addClass('survey-media-mock-progress-item')
                        .addClass('uploading')
                        .attr('data-index', index)
                        .attr('data-file-id', item.id)
                        .attr('data-file-name', item.name)
                        .html(`
                    <div class="survey-media-mock-progress-item-header">
                        <div class="survey-media-mock-progress-item-info">
                            <i class="fas ${fileIcon} survey-media-mock-progress-item-icon"></i>
                            <div class="survey-media-mock-progress-item-details">
                                <div class="survey-media-mock-progress-item-name">${item.name}</div>
                                <div class="survey-media-mock-progress-item-status uploading">${fileSize} • Uploading...</div>
                            </div>
                        </div>
                    </div>
                    <div class="survey-media-mock-progress-bar-wrapper">
                        <div class="survey-media-mock-progress-bar" style="width: 0%">
                            <span class="survey-media-mock-progress-percentage">0%</span>
                        </div>
                    </div>
                `);
                    $('#progress-list').append(progressItem);
                });

                // Upload all files simultaneously
                uploadQueue.forEach((item, index) => {
                    uploadFileToS3(item, index);
                });
            }

            // Start upload when button is clicked
            $('#upload-files-btn').on('click', function() {
                if (uploadQueue.length > 0 && !uploadInProgress) {
                    startUpload();
                }
            });

            // Upload single file to S3 via Laravel API
            function uploadFileToS3(item, index) {
                const formData = new FormData();

                // Append file
                formData.append('files[]', item.file);

                // ✅ Append survey ID so Laravel receives it
                formData.append('survey_id', surveyId);

                const xhr = new XMLHttpRequest();
                activeUploads.push(xhr);

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        item.progress = percentComplete;
                        updateProgressBar(index, percentComplete);
                    }
                });

                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);

                            if (response.success && response.uploaded_files?.length > 0) {
                                let uploadedFile = response.uploaded_files.find(
                                    f => f.original_name === item.name
                                ) || response.uploaded_files[0];

                                if (uploadedFile) {
                                    item.status = 'completed';

                                    updateProgressBar(index, 100, 'completed');

                                    const progressItem = $(
                                        `.survey-media-mock-progress-item[data-index="${index}"]`);
                                    progressItem.removeClass('uploading').addClass('completed');

                                    // Attach metadata
                                    progressItem.attr('data-media-id', uploadedFile.id);
                                    progressItem.attr('data-s3-url', uploadedFile.s3_url);
                                    progressItem.attr('data-s3-path', uploadedFile.s3_path);
                                    progressItem.attr('data-needs-transcription', uploadedFile
                                        .needs_transcription || false);
                                    progressItem.attr('data-file-type', uploadedFile.type);

                                    progressItem.find('.survey-media-mock-progress-item-status')
                                        .removeClass('uploading')
                                        .addClass('completed')
                                        .text('Uploaded successfully');
                                }
                            } else {
                                item.status = 'error';
                                showUploadError(index, response.message || 'Upload failed');
                            }
                        } catch (e) {
                            item.status = 'error';
                            showUploadError(index, 'Failed to parse server response');
                        }
                    } else {
                        let errorMessage = 'Upload failed';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch (e) {
                            errorMessage = `Server error (${xhr.status})`;
                        }
                        showUploadError(index, errorMessage);
                    }

                    checkAllUploadsComplete();
                });

                xhr.addEventListener('error', function() {
                    item.status = 'error';
                    showUploadError(index, 'Network error during upload');
                    checkAllUploadsComplete();
                });

                xhr.addEventListener('abort', function() {
                    item.status = 'cancelled';
                    checkAllUploadsComplete();
                });

                xhr.open('POST', uploadUrl, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }


            // Update progress bar UI
            function updateProgressBar(index, progress, status = 'uploading') {
                const progressItem = $(`.survey-media-mock-progress-item[data-index="${index}"]`);
                const progressBar = progressItem.find('.survey-media-mock-progress-bar');
                const progressPercentage = progressItem.find('.survey-media-mock-progress-percentage');
                const statusText = progressItem.find('.survey-media-mock-progress-item-status');

                const progressValue = Math.min(progress, 100);
                progressBar.css('width', progressValue + '%');
                progressPercentage.text(Math.round(progressValue) + '%');

                if (status === 'uploading') {
                    const item = uploadQueue[index];
                    if (item) {
                        const fileSize = formatFileSize(item.size);
                        statusText.text(`${fileSize} • Uploading... ${Math.round(progressValue)}%`);
                    }
                }
            }

            // Show upload error
            function showUploadError(index, message) {
                const progressItem = $(`.survey-media-mock-progress-item[data-index="${index}"]`);
                progressItem.removeClass('uploading').addClass('upload-error');

                const statusText = progressItem.find('.survey-media-mock-progress-item-status');
                statusText.removeClass('uploading').addClass('error').text('Upload failed');

                // Optionally show error message
                console.error(`Upload error for file ${index}:`, message);
            }

            // Check if all uploads are complete
            function checkAllUploadsComplete() {
                const allComplete = uploadQueue.every(item =>
                    item.status === 'completed' || item.status === 'error' || item.status === 'cancelled'
                );

                if (allComplete) {
                    uploadInProgress = false;

                    const successCount = uploadQueue.filter(item => item.status === 'completed').length;
                    const errorCount = uploadQueue.filter(item => item.status === 'error').length;

                    // Show success message
                    if (successCount > 0) {
                        const message = errorCount > 0 ?
                            `${successCount} file(s) uploaded successfully. ${errorCount} file(s) failed.` :
                            `All ${successCount} file(s) uploaded successfully!`;

                        setTimeout(() => {
                            alert(message);
                            resetUploadView();
                        }, 1000);
                    } else {
                        setTimeout(() => {
                            alert('Upload failed. Please try again.');
                            resetUploadView();
                        }, 1000);
                    }
                }
            }

            // Reset upload view
            function resetUploadView() {
                // Cancel any active uploads
                activeUploads.forEach(xhr => {
                    if (xhr.readyState !== XMLHttpRequest.DONE) {
                        xhr.abort();
                    }
                });

                uploadQueue = [];
                uploadInProgress = false;
                activeUploads = [];

                // Show upload section and hide progress section
                $('#progress-section').hide();
                $('#upload-section').show();
                $('#file-queue').hide();
                $('#upload-files-btn').hide();
                $('#media-title').text('Automatic Mode');

                // Clear file input
                $('#media-file-input').val('');
            }

            // Cleanup on page unload
            $(window).on('beforeunload', function() {
                activeUploads.forEach(xhr => {
                    if (xhr.readyState !== XMLHttpRequest.DONE) {
                        xhr.abort();
                    }
                });
            });
        });
    </script>
@endpush
