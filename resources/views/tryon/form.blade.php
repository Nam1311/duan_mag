@extends('app')

@section('body')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Reset và base styling */
.tryon-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Section */
.tryon-header {
    text-align: center;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
    color: white;
    padding: 60px 40px;
    border-radius: 16px;
    position: relative;
    overflow: hidden;
}

.tryon-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>') repeat;
}

.tryon-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}

.tryon-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.back-home-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 12px 24px;
    border-radius: 30px;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.back-home-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

/* Form Layout */
.tryon-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

.form-card {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.form-card h3 {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 2px solid #dee2e6;
}

.form-card h3 i {
    color: #495057;
    font-size: 1.1rem;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #adb5bd;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #ffffff;
    margin-bottom: 20px;
}

.upload-area:hover {
    border-color: #495057;
    background: #f8f9fa;
}

.upload-area.drag-over {
    border-color: #212529;
    background: #e9ecef;
}

.upload-icon {
    font-size: 2.5rem;
    color: #6c757d;
    margin-bottom: 12px;
}

.upload-text {
    font-size: 1rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
}

.upload-hint {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Preview */
.preview-container {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.preview-image {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #dee2e6;
}

.remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

/* Form Groups */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    font-size: 0.875rem;
}

.form-group label i {
    color: #6c757d;
    font-size: 0.875rem;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
    transition: all 0.3s ease;
    color: #495057;
}

.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #495057;
    box-shadow: 0 0 0 3px rgba(73, 80, 87, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

/* Instructions Card */
.instructions-card {
    grid-column: 1 / -1;
}

/* Submit Section */
.submit-section {
    text-align: center;
    margin: 40px 0;
}

.submit-btn {
    background: linear-gradient(135deg, #212529 0%, #495057 100%);
    color: white;
    border: none;
    padding: 16px 48px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(33, 37, 41, 0.3);
    background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.submit-btn.loading {
    pointer-events: none;
}

.submit-btn.loading .btn-text {
    opacity: 0;
}

.submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid transparent;
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Result Section */
.result-section {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 40px;
    margin-top: 40px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    position: relative;
}

.result-section h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #212529;
    margin: 0;
    flex: 1;
}

.close-result-btn {
    background: #dc3545;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
    position: absolute;
    right: 0;
    top: -10px;
}

.close-result-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

.result-container {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.result-image-container {
    text-align: center;
}

.result-image {
    max-width: 400px;
    width: 100%;
    height: auto;
    border-radius: 12px;
    border: 2px solid #dee2e6;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.secondary-btn {
    background: #6c757d !important;
    margin-left: 16px;
}

.secondary-btn:hover {
    background: #5a6268 !important;
}

.result-text {
    font-size: 1rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Utility Classes */
.hidden {
    display: none !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .tryon-container {
        padding: 0 16px;
        margin: 20px auto;
    }
    
    .tryon-header {
        padding: 40px 20px;
    }
    
    .tryon-header h1 {
        font-size: 2rem;
    }
    
    .tryon-form {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-card {
        padding: 20px;
    }
    
    .upload-area {
        padding: 30px 15px;
    }
    
    .result-container {
        flex-direction: column;
        align-items: center;
    }
    
    .result-image {
        max-width: 300px;
    }
    
    .result-header {
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }
    
    .close-result-btn {
        position: relative;
        top: 0;
        right: 0;
    }
    
    .submit-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }
    
    .secondary-btn {
        margin-left: 0 !important;
    }
}

/* Loading States */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 16px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    border: 1px solid #dee2e6;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #495057;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

.loading-text {
    font-size: 1.1rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
}

.loading-subtext {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>

<div class="tryon-container">
    <!-- Header -->
    <div class="tryon-header">
        <h1><i class="fas fa-magic"></i> Thử Đồ Ảo</h1>
        <p>Trải nghiệm công nghệ AI tiên tiến để thử đồ ảo trong vài giây</p>
        <a href="{{ route('home') }}" class="back-home-btn">
            <i class="fas fa-home"></i>
            Trở về trang chủ
        </a>
    </div>

    <!-- Form -->
    <form id="tryonForm" class="tryon-form" enctype="multipart/form-data">
        @csrf
        
        <!-- Model Upload Card -->
        <div class="form-card">
            <h3><i class="fas fa-user"></i> Ảnh Người Mẫu</h3>
            
            <div id="modelUpload" class="upload-area">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <div class="upload-text">Nhấp hoặc kéo ảnh vào đây</div>
                <div class="upload-hint">Chỉ ảnh • Tối đa: 10MB</div>
            </div>
            <input type="file" id="modelFileInput" name="person_image" accept="image/*" class="hidden">
            
            <div id="modelPreview" class="preview-container hidden">
                <img id="modelPreviewImage" src="#" alt="Model Preview" class="preview-image">
                <span id="removeModel" class="remove-btn"><i class="fas fa-times"></i></span>
            </div>

            <!-- Hidden field for model type - always full body -->
            <input type="hidden" id="modelType" name="model_type" value="full">

            <div class="form-group">
                <label for="gender"><i class="fas fa-venus-mars"></i> Giới Tính</label>
                <select id="gender" name="gender">
                    <option value="">Chọn giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>
        </div>

        <!-- Garment Upload Card -->
        <div class="form-card">
            <h3><i class="fas fa-tshirt"></i> Ảnh Trang Phục</h3>
            
            <div id="garmentUpload" class="upload-area">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <div class="upload-text">Nhấp hoặc kéo ảnh vào đây</div>
                <div class="upload-hint">Chỉ ảnh • Tối đa: 10MB</div>
            </div>
            <input type="file" id="garmentFileInput" name="cloth_image" accept="image/*" class="hidden">
            
            <div id="garmentPreview" class="preview-container hidden">
                <img id="garmentPreviewImage" src="#" alt="Garment Preview" class="preview-image">
                <span id="removeGarment" class="remove-btn"><i class="fas fa-times"></i></span>
            </div>

            <div class="form-group">
                <label for="garmentType"><i class="fas fa-tag"></i> Loại Trang Phục</label>
                <select id="garmentType" name="garment_type">
                    <option value="">Chọn loại trang phục</option>
                    <option value="shirt">Áo sơ mi</option>
                    <option value="pants">Quần</option>
                    <option value="jacket">Áo khoác</option>
                    <option value="dress">Váy</option>
                    <option value="tshirt">Áo thun</option>
                </select>
            </div>

            <div class="form-group">
                <label for="style"><i class="fas fa-palette"></i> Phong Cách</label>
                <select id="style" name="style">
                    <option value="">Chọn phong cách</option>
                    <option value="casual">Casual</option>
                    <option value="formal">Trang trọng</option>
                    <option value="streetwear">Đường phố</option>
                    <option value="traditional">Truyền thống</option>
                    <option value="sports">Thể thao</option>
                </select>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="form-card instructions-card">
            <h3><i class="fas fa-comment-dots"></i> Hướng Dẫn Đặc Biệt</h3>
            <div class="form-group">
                <textarea id="instructions" name="instructions" 
                    placeholder="Nhập các yêu cầu đặc biệt (ví dụ: độ vừa vặn, điều chỉnh màu sắc, v.v.)"></textarea>
            </div>
        </div>
    </form>

    <!-- Submit Section -->
    <div class="submit-section">
        <button type="button" id="submitBtn" class="submit-btn">
            <span class="btn-text">Thử Đồ Ngay</span>
            <i class="fas fa-magic"></i>
        </button>
    </div>

    <!-- Result Section -->
    @if(!empty($resultImage))
    <div class="result-section">
        <div class="result-header">
            <h2><i class="fas fa-sparkles"></i> Kết Quả Thử Đồ Ảo</h2>
            <button class="close-result-btn" onclick="closeResult()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="result-container">
            <div class="result-image-container">
                <img src="{{ $resultImage }}" alt="Virtual Try-On Result" class="result-image">
            </div>
        </div>
        
        @if(!empty($description))
        <p class="result-text">{{ $description }}</p>
        @endif
        
        <div class="submit-section">
            <a href="{{ route('tryon.form') }}" class="submit-btn">
                <i class="fas fa-redo"></i>
                Thử Trang Phục Khác
            </a>
            <a href="{{ route('home') }}" class="submit-btn secondary-btn">
                <i class="fas fa-home"></i>
                Về Trang Chủ
            </a>
        </div>
    </div>
    @endif

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay hidden">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Đang xử lý thử đồ ảo...</div>
            <div class="loading-subtext">Quá trình này có thể mất 30-60 giây</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload functionality
    function setupUpload(uploadAreaId, fileInputId, previewId, previewImageId, removeBtnId) {
        const uploadArea = document.getElementById(uploadAreaId);
        const fileInput = document.getElementById(fileInputId);
        const preview = document.getElementById(previewId);
        const previewImage = document.getElementById(previewImageId);
        const removeBtn = document.getElementById(removeBtnId);

        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0], uploadArea, preview, previewImage);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0], uploadArea, preview, previewImage);
            }
        });

        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            preview.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        });

        function handleFileSelect(file, uploadArea, preview, previewImage) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    uploadArea.classList.add('hidden');
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    }

    // Setup uploads
    setupUpload('modelUpload', 'modelFileInput', 'modelPreview', 'modelPreviewImage', 'removeModel');
    setupUpload('garmentUpload', 'garmentFileInput', 'garmentPreview', 'garmentPreviewImage', 'removeGarment');

    // Check for auto-populate data from product detail page
    checkForAutoPopulate();

    function checkForAutoPopulate() {
        try {
            const tryOnData = localStorage.getItem('tryOnProductData');
            if (tryOnData) {
                const data = JSON.parse(tryOnData);
                
                // Auto-populate garment image with product image
                if (data.currentImage) {
                    populateGarmentImage(data.currentImage);
                }
                
                // Clear the data after use
                localStorage.removeItem('tryOnProductData');
            }
        } catch (error) {
            console.error('Error loading auto-populate data:', error);
        }
    }

    function populateGarmentImage(imageUrl) {
        // Convert the image URL to a blob and create a file
        fetch(imageUrl)
            .then(response => response.blob())
            .then(blob => {
                // Create a file from the blob
                const fileName = 'product-image.jpg';
                const file = new File([blob], fileName, { type: blob.type });
                
                // Create a new FileList with the file
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('garmentFileInput').files = dt.files;
                
                // Show preview
                const garmentPreviewImage = document.getElementById('garmentPreviewImage');
                const garmentUpload = document.getElementById('garmentUpload');
                const garmentPreview = document.getElementById('garmentPreview');
                
                garmentPreviewImage.src = imageUrl;
                garmentUpload.classList.add('hidden');
                garmentPreview.classList.remove('hidden');
                
                console.log('Auto-populated garment image from product detail');
            })
            .catch(error => {
                console.error('Error loading product image:', error);
            });
    }

    // Form submission with AJAX
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('tryonForm');
    const loadingOverlay = document.getElementById('loadingOverlay');

    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Validation
        const personImage = document.getElementById('modelFileInput').files[0];
        const clothImage = document.getElementById('garmentFileInput').files[0];
        
        if (!personImage || !clothImage) {
            alert('Vui lòng tải lên cả ảnh người mẫu và ảnh trang phục');
            return;
        }

        // Show loading
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        loadingOverlay.classList.remove('hidden');

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('person_image', personImage);
        formData.append('cloth_image', clothImage);
        formData.append('model_type', document.getElementById('modelType').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('garment_type', document.getElementById('garmentType').value);
        formData.append('style', document.getElementById('style').value);
        formData.append('instructions', document.getElementById('instructions').value);

        try {
            const response = await fetch('{{ route("tryon.process") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const result = await response.json();
                
                if (result.success) {
                    // Redirect to show result
                    window.location.href = result.redirect || window.location.href;
                } else {
                    throw new Error(result.message || 'Có lỗi xảy ra khi xử lý');
                }
            } else {
                throw new Error('Lỗi kết nối đến server');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra: ' + error.message);
        } finally {
            // Hide loading
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            loadingOverlay.classList.add('hidden');
        }
    });

    // Function to close result section
    window.closeResult = function() {
        const resultSection = document.querySelector('.result-section');
        if (resultSection) {
            resultSection.style.transform = 'translateY(-20px)';
            resultSection.style.opacity = '0';
            setTimeout(() => {
                resultSection.remove();
                // Clear URL parameters if any
                if (window.history && window.history.pushState) {
                    window.history.pushState({}, document.title, window.location.pathname);
                }
            }, 300);
        }
    };
});
</script>

@endsection

