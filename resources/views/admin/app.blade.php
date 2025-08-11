<!DOCTYPE html>
<html lang="vi">

<head>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang chủ')</title>
    <link rel="stylesheet" href="{{ asset('/css/admin/header.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/home.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/baocao.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/caidat.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/hotro.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/khuyenmai.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/quanlyhinhanh.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/quanlykhachhang.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/quanlykho.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/quanlynguoidung.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/quanlytintuc.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/admin/binhluan.css') }}">
     <link rel="stylesheet" href="{{ asset('/css/admin/orders.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic-all/ckeditor.js"></script>


    {{-- --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:wght@100..900&family=Oxanium:wght@200..800&display=swap"
        rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="aindex-body">
        <div class="aindex-container expanded">
            @if (session('success'))
                <div id="toast-success" class="custom-toast">
                    <div class="toast-content">
                        <div class="toast-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="toast-message">{{ session('success') }}</div>
                        <button class="toast-close" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="toast-progress"></div>
                </div>

                <style>
                    .custom-toast {
                        position: fixed;
                        top: 24px;
                        right: 24px;
                        background: #fff;
                        color: #1f2937;
                        padding: 0;
                        border-radius: 12px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                        font-size: 14px;
                        animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                        z-index: 9999;
                        width: 350px;
                        overflow: hidden;
                        border: 1px solid #e5e7eb;
                        transition: all 0.3s ease;
                    }

                    .custom-toast:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
                    }

                    .toast-content {
                        display: flex;
                        align-items: center;
                        padding: 16px;
                        position: relative;
                    }

                    .toast-icon {
                        width: 24px;
                        height: 24px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #10b981;
                        border-radius: 50%;
                        color: white;
                        flex-shrink: 0;
                        margin-right: 12px;
                        padding: 4px;
                    }

                    .toast-icon svg {
                        width: 16px;
                        height: 16px;
                    }

                    .toast-message {
                        flex: 1;
                        line-height: 1.5;
                        padding-right: 8px;
                    }

                    .toast-close {
                        background: none;
                        border: none;
                        color: #9ca3af;
                        cursor: pointer;
                        padding: 4px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: color 0.2s ease;
                    }

                    .toast-close:hover {
                        color: #6b7280;
                    }

                    .toast-close svg {
                        width: 14px;
                        height: 14px;
                    }

                    .toast-progress {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        height: 4px;
                        width: 100%;
                        background-color: #e5e7eb;
                    }

                    .toast-progress::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        height: 100%;
                        width: 100%;
                        background-color: #10b981;
                        animation: progress 3s linear forwards;
                    }

                    @keyframes slideInRight {
                        from {
                            opacity: 0;
                            transform: translateX(calc(100% + 24px));
                        }

                        to {
                            opacity: 1;
                            transform: translateX(0);
                        }
                    }

                    @keyframes slideOutRight {
                        to {
                            opacity: 0;
                            transform: translateX(calc(100% + 24px));
                        }
                    }

                    @keyframes progress {
                        from {
                            width: 100%;
                        }

                        to {
                            width: 0%;
                        }
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toast = document.getElementById('toast-success');
                        if (toast) {
                            // Auto dismiss after 5 seconds
                            const dismissTimeout = setTimeout(() => {
                                dismissToast();
                            }, 5000);

                            // Close button click handler
                            const closeButton = toast.querySelector('.toast-close');
                            closeButton.addEventListener('click', () => {
                                clearTimeout(dismissTimeout);
                                dismissToast();
                            });

                            // Dismiss function with animation
                            function dismissToast() {
                                toast.style.animation = 'slideOutRight 0.5s ease forwards';
                                toast.addEventListener('animationend', () => {
                                    toast.remove();
                                }, {
                                    once: true
                                });
                            }

                            // Pause progress bar on hover
                            toast.addEventListener('mouseenter', () => {
                                toast.querySelector('.toast-progress').style.animationPlayState = 'paused';
                            });

                            toast.addEventListener('mouseleave', () => {
                                toast.querySelector('.toast-progress').style.animationPlayState = 'running';
                            });
                        }
                    });
                </script>
            @endif

            @if (session('error'))
                <div id="toast-error" class="custom-toast">
                    <div class="toast-content">
                        <div class="toast-icon-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="toast-message">{{ session('error') }}</div>
                        <button class="toast-close" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="toast-progress"></div>
                </div>

                <style>
                    .custom-toast {
                        position: fixed;
                        top: 24px;
                        right: 24px;
                        background: #fff;
                        color: #1f2937;
                        padding: 0;
                        border-radius: 12px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                        font-size: 14px;
                        animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                        z-index: 9999;
                        width: 350px;
                        overflow: hidden;
                        border: 1px solid #e5e7eb;
                        transition: all 0.3s ease;
                    }

                    .custom-toast:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
                    }

                    .toast-content {
                        display: flex;
                        align-items: center;
                        padding: 16px;
                        position: relative;
                    }

                    .toast-icon-error {
                        width: 24px;
                        height: 24px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #ff0000;
                        border-radius: 50%;
                        color: white;
                        flex-shrink: 0;
                        margin-right: 12px;
                        padding: 4px;
                    }

                    .toast-icon-error svg {
                        width: 16px;
                        height: 16px;
                    }

                    .toast-message {
                        flex: 1;
                        line-height: 1.5;
                        padding-right: 8px;
                    }

                    .toast-close {
                        background: none;
                        border: none;
                        color: #9ca3af;
                        cursor: pointer;
                        padding: 4px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: color 0.2s ease;
                    }

                    .toast-close:hover {
                        color: #6b7280;
                    }

                    .toast-close svg {
                        width: 14px;
                        height: 14px;
                    }

                    .toast-progress {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        height: 4px;
                        width: 100%;
                        background-color: #e5e7eb;
                    }

                    .toast-progress::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        height: 100%;
                        width: 100%;
                        background-color: #ff0000;
                        animation: progress 3s linear forwards;
                    }

                    @keyframes slideInRight {
                        from {
                            opacity: 0;
                            transform: translateX(calc(100% + 24px));
                        }

                        to {
                            opacity: 1;
                            transform: translateX(0);
                        }
                    }

                    @keyframes slideOutRight {
                        to {
                            opacity: 0;
                            transform: translateX(calc(100% + 24px));
                        }
                    }

                    @keyframes progress {
                        from {
                            width: 100%;
                        }

                        to {
                            width: 0%;
                        }
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toast = document.getElementById('toast-error');
                        if (toast) {
                            // Auto dismiss after 5 seconds
                            const dismissTimeout = setTimeout(() => {
                                dismissToast();
                            }, 5000);

                            // Close button click handler
                            const closeButton = toast.querySelector('.toast-close');
                            closeButton.addEventListener('click', () => {
                                clearTimeout(dismissTimeout);
                                dismissToast();
                            });

                            // Dismiss function with animation
                            function dismissToast() {
                                toast.style.animation = 'slideOutRight 0.5s ease forwards';
                                toast.addEventListener('animationend', () => {
                                    toast.remove();
                                }, {
                                    once: true
                                });
                            }

                            // Pause progress bar on hover
                            toast.addEventListener('mouseenter', () => {
                                toast.querySelector('.toast-progress').style.animationPlayState = 'paused';
                            });

                            toast.addEventListener('mouseleave', () => {
                                toast.querySelector('.toast-progress').style.animationPlayState = 'running';
                            });
                        }
                    });
                </script>
            @endif

            @include('admin.header')

            @yield('admin.body')

            {{-- @include('admin.footer') --}}

        </div>
    </div>
    @stack('scripts')
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#adnews-newsContent'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'link', 'blockQuote', 'code', '|',
                            'bulletedList', 'numberedList', '|',
                            'insertTable', 'mediaEmbed', '|',
                            'imageUpload', '|',
                            'undo', 'redo'
                        ]
                    },
                    simpleUpload: {
                        uploadUrl: '/api/upload-image',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    },
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            }
                        ]
                    }
                })
                .catch(error => {
                    console.error('There was a problem initializing the editor.', error);
                });
        });
    </script>
</body>

</html>
