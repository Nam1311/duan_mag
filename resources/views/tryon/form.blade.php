@extends('app')

@section('body')
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Try-On | Fashion Innovation</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{asset('')}}css/tryon.css">
</head>

<body>
  <div id="root">
    <div class="container">
      <header class="header">
        <h1>Virtual Try-On Experience</h1>
        <p>Try on clothes virtually in seconds with our AI-powered technology</p>
        <div class="toggle-container">
          <button id="darkModeToggle" class="toggle-btn">
            <i class="fas fa-moon"></i>
          </button>
        </div>
      </header>
      <a href="{{route('home')}}">Trở về trang chủ</a>
      <form id="tryonForm" method="post" action="{{route('tryon.process')}}" enctype="multipart/form-data"
        class="form-section">
        @csrf

        <div class="card">
          <h2><i class="fas fa-user"></i> Model Image</h2>
          <div id="modelUpload" class="upload-area">
            <i class="fas fa-cloud-upload-alt upload-icon"></i>
            <div class="upload-text">Click or drag an image here to upload</div>
            <div class="upload-hint">Image only • Max size: 10MB</div>
          </div>
          <input type="file" id="modelFileInput" name="person_image" accept="image/*" class="hidden">
          <div id="modelPreview" class="preview-container hidden">
            <img id="modelPreviewImage" src="#" alt="Model Preview" class="preview-image">
            <span id="removeModel" class="remove-btn"><i class="fas fa-times"></i></span>
          </div>

          <div class="form-group">
            <label for="modelType"><i class="fas fa-tshirt"></i> Model Type</label>
            <select id="modelType" name="model_type">
              <option value="">Select model type</option>
              <option value="top">Top Half</option>
              <option value="bottom">Bottom Half</option>
              <option value="full">Full Body</option>
            </select>
          </div>

          <div class="form-group">
            <label for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
            <select id="gender" name="gender">
              <option value="">Select gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="unisex">Unisex</option>
            </select>
          </div>
        </div>

        <div class="card">
          <h2><i class="fas fa-tshirt"></i> Garment Image</h2>
          <div id="garmentUpload" class="upload-area">
            <i class="fas fa-cloud-upload-alt upload-icon"></i>
            <div class="upload-text">Click or drag an image here to upload</div>
            <div class="upload-hint">Image only • Max size: 10MB</div>
          </div>
          <input type="file" id="garmentFileInput" name="cloth_image" accept="image/*" class="hidden">
          <div id="garmentPreview" class="preview-container hidden">
            <img id="garmentPreviewImage" src="#" alt="Garment Preview" class="preview-image">
            <span id="removeGarment" class="remove-btn"><i class="fas fa-times"></i></span>
          </div>

          <div class="form-group">
            <label for="garmentType"><i class="fas fa-tag"></i> Garment Type</label>
            <select id="garmentType" name="garment_type">
              <option value="">Select garment type</option>
              <option value="shirt">Shirt</option>
              <option value="pants">Pants</option>
              <option value="jacket">Jacket</option>
              <option value="dress">Dress</option>
              <option value="tshirt">T-shirt</option>
            </select>
          </div>

          <div class="form-group">
            <label for="style"><i class="fas fa-palette"></i> Style</label>
            <select id="style" name="style">
              <option value="">Select style</option>
              <option value="casual">Casual</option>
              <option value="formal">Formal</option>
              <option value="streetwear">Streetwear</option>
              <option value="traditional">Traditional</option>
              <option value="sports">Sportswear</option>
            </select>
          </div>
        </div>

        <div class="card" style="grid-column: span 1;">
          <h2><i class="fas fa-comment-dots"></i> Special Instructions</h2>
          <div class="form-group">
            <textarea id="instructions" name="instructions"
              placeholder="Enter any specific instructions (e.g., fit preference, color adjustments, etc.)"></textarea>
          </div>
        </div>

        <div class="submit-container">
          <button type="submit" id="submitBtn" class="submit-btn">
            <span class="btn-text">Try On Now</span>
            <i class="fas fa-magic"></i>
          </button>
        </div>
      </form>

      <!-- Result Section (Example) -->
      @if(!empty($resultImage))
      <div class="result-section">
      <h2>Your Virtual Try-On Result</h2>
      <div class="result-container">
        <img
        src="{{ $resultImage }}"
        alt="Virtual Try-On Result" class="result-image">
      </div>
      <p class="result-text">{{ $description }}</p>
      <div class="submit-container">
        <a href="{{ route('tryon.form') }}" class="submit-btn">Try Another Outfit</a>
      </div>
      </div>
    @endif


      <div id="toastContainer" class="toast-container"></div>
    </div>
  </div>

  <script src="{{asset('')}}js/tryon.js"></script>
</body>

@endsection

