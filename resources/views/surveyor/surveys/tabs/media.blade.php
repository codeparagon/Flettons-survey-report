{{-- Media Tab Content - Integrated with Existing Media System --}}
<div class="survey-tab-content">
    {{-- Content Header - Sophisticated Design --}}
    <div class="survey-content-header">
        <h2 class="survey-tab-title">Media Uploads</h2>
    </div>
    
    <div class="survey-media-container">
        {{-- Link to Full Media Page --}}
        <div class="survey-media-link-section">
            <p class="survey-media-description">
                Use the full media management page to upload and organize photos, videos, and documents for this survey.
            </p>
            <a href="{{ route('surveyor.survey.media', $survey) }}" class="survey-media-link-btn">
                <i class="fas fa-images"></i> Open Media Manager
            </a>
        </div>
        
        {{-- Quick Upload Section --}}
        <div class="survey-quick-upload">
            <h4 class="survey-section-subtitle">Quick Upload</h4>
            <div class="survey-upload-zone" id="quick-upload-zone">
                <div class="survey-upload-content">
                    <i class="fas fa-cloud-upload-alt survey-upload-icon"></i>
                    <p class="survey-upload-text">Drag & drop files here or click to browse</p>
                    <p class="survey-upload-subtext">Images, Videos, Documents (Max 100MB per file)</p>
                </div>
                <input type="file" id="quick-upload-input" multiple accept="image/*,video/*,.pdf,.doc,.docx" style="display: none;">
            </div>
            
            <div class="survey-uploaded-files" id="uploaded-files-list"></div>
        </div>
    </div>
</div>

@push('styles')
<style>
.survey-media-container {
    margin-top: 1.5rem;
}

.survey-media-link-section {
    background: #F9FAFB;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    margin-bottom: 2rem;
    text-align: center;
}

.survey-media-description {
    color: #6B7280;
    font-size: 1.125rem;
    margin-bottom: 1rem;
}

.survey-media-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2.25rem;
    font-size: 1.125rem;
    font-weight: 600;
    background: #C1EC4A;
    color: #1A202C;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.survey-media-link-btn:hover {
    background: #B0D93F;
    transform: translateY(-1px);
    text-decoration: none;
    color: #1A202C;
}

.survey-section-subtitle {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1A202C;
    margin-bottom: 1rem;
}

.survey-upload-zone {
    border: 2px dashed #D1D5DB;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #FFFFFF;
    cursor: pointer;
    transition: all 0.2s ease;
}

.survey-upload-zone:hover {
    border-color: #C1EC4A;
    background: #F9FAFB;
}

.survey-upload-zone.dragover {
    border-color: #C1EC4A;
    background: #F0FDF4;
}

.survey-upload-content {
    pointer-events: none;
}

.survey-upload-icon {
    font-size: 3rem;
    color: #9CA3AF;
    margin-bottom: 1rem;
}

.survey-upload-zone:hover .survey-upload-icon {
    color: #C1EC4A;
}

.survey-upload-text {
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.survey-upload-subtext {
    font-size: 1rem;
    color: #6B7280;
}

.survey-uploaded-files {
    margin-top: 1.5rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.survey-file-item {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 1rem;
    position: relative;
}

.survey-file-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.survey-file-name {
    font-size: 1rem;
    font-weight: 500;
    color: #1A202C;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.survey-file-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(239, 68, 68, 0.9);
    color: #FFFFFF;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('quick-upload-zone');
    const uploadInput = document.getElementById('quick-upload-input');
    const filesList = document.getElementById('uploaded-files-list');
    
    if (uploadZone && uploadInput) {
        // Click to upload
        uploadZone.addEventListener('click', function(e) {
            if (!e.target.closest('.survey-file-remove')) {
                uploadInput.click();
            }
        });
        
        // Drag and drop
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        
        uploadInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });
        
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    uploadFile(file);
                } else {
                    alert('Only image files are supported in quick upload. Use Media Manager for other file types.');
                }
            });
        }
        
        function uploadFile(file) {
            const formData = new FormData();
            formData.append('files[]', file);
            formData.append('type', 'image');
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const fileItem = document.createElement('div');
                fileItem.className = 'survey-file-item';
                fileItem.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="survey-file-name">${file.name}</div>
                    <button type="button" class="survey-file-remove" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                filesList.appendChild(fileItem);
                
                // Upload to server
                fetch('{{ route("surveyor.survey.media.upload", $survey) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('File uploaded:', data);
                    } else {
                        alert('Upload failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Upload failed. Please try again.');
                });
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endpush
