@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-left">الإعدادات</h1>
            @if (session('success'))
                <div class="alert alert-success text-left">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-left">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Settings Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="text-left">الإعدادات العامة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="app_name" class="text-left">اسم التطبيق</label>
                            <input type="text" name="app_name" class="form-control text-left" value="{{ config('settings.app_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="logo" class="text-left">الشعار</label>
                            <input type="file" name="logo" class="form-control">
                            @if(config('settings.logo'))
                                <img src="{{ asset('storage/' . config('settings.logo')) }}" alt="Logo" width="150" class="mt-2">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="phone_numbers" class="text-left">{{ __('main.phone_numbers') }}</label>
                            <div id="phone-numbers-container">
                                @foreach((array) config('settings.phone_numbers', []) as $phone)
                                <div class="input-group mb-2">
                                    <input type="text" name="phone_numbers[{{ $loop->index }}][name]" class="form-control text-left" placeholder="{{ __('main.name_placeholder') }}" value="{{ $phone['name'] ?? '' }}">
                                    <input type="text" name="phone_numbers[{{ $loop->index }}][number]" class="form-control text-left" placeholder="{{ __('main.number_placeholder') }}" value="{{ $phone['number'] ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-danger remove-phone-number" type="button">{{ __('main.remove') }}</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button id="add-phone-number" type="button" class="btn btn-secondary mt-2">{{ __('main.add_phone_number') }}</button>
                        </div>
                        <div class="form-group">
                            <label for="address" class="text-left">{{ __('main.address') }}</label>
                            <textarea name="address" class="form-control text-left">{{ config('settings.address') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="tax_number" class="text-left">الرقم الضريبي (اختياري)</label>
                            <input type="text" name="tax_number" class="form-control text-left" value="{{ config('settings.tax_number') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('main.save_settings') }}</button>
                    </form>
                </div>
            </div>

            <!-- Backup Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="text-left">النسخ الاحتياطية لقاعدة البيانات</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Side: Create and Upload -->
                        <div class="col-md-6">
                            <div class="row">
                                <!-- Create Backup -->
                                <div class="col-md-12 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="text-left text-success">
                                                <i class="fas fa-database me-2"></i>إنشاء نسخة احتياطية
                                            </h6>
                                            <p class="text-muted text-left small">إنشاء نسخة احتياطية جديدة لقاعدة البيانات</p>
                                            <form action="{{ route('settings.backup') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-download me-1"></i>إنشاء نسخة احتياطية
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Upload Backup -->
                                <div class="col-md-12">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <h6 class="text-left text-info">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>رفع ملف نسخة احتياطية
                                            </h6>
                                            <p class="text-muted text-left small">رفع ملف .sql من جهازك</p>
                                            <form action="{{ route('settings.upload-backup') }}" method="POST" enctype="multipart/form-data" id="upload-backup-form">
                                                @csrf
                                                <div class="mb-2">
                                                    <input type="file" name="backup_file" class="form-control form-control-sm" accept=".sql" required>
                                                </div>
                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-upload me-1"></i>رفع الملف
                                                </button>
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>الحد الأقصى: 50 ميجابايت
                                                </small>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side: Available Backups -->
                        <div class="col-md-6">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <h6 class="text-left text-primary">
                                        <i class="fas fa-list me-2"></i>النسخ الاحتياطية المتاحة
                                    </h6>
                                    <div id="backups-list">
                                        <p class="text-muted text-left small">جاري تحميل النسخ الاحتياطية...</p>
                                    </div>
                                    <div id="backups-pagination" class="mt-3" style="display: none;">
                                        <nav aria-label="صفحات النسخ الاحتياطية">
                                            <ul class="pagination pagination-sm justify-content-center" id="pagination-list">
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Restore Warning -->
                    <div class="alert alert-warning mt-3 text-left" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>تحذير:</strong> استعادة قاعدة البيانات ستستبدل البيانات الموجودة بالبيانات من النسخة الاحتياطية المحددة. تأكد من عمل نسخة احتياطية من البيانات الحالية قبل الاستعادة.
                    </div>
                </div>
            </div>

            <!-- Cron Instructions Section -->
            <div class="card">
                <div class="card-header">
                    <h5>إعدادات Cron في cPanel</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-left">تعليمات إعداد Cron Jobs في cPanel:</h6>
                            <div class="alert alert-info text-left" dir="rtl">
                                <h6><strong>خطوات إعداد Cron Jobs:</strong></h6>
                                <ol class="text-left">
                                    <li>ادخل إلى لوحة تحكم cPanel</li>
                                    <li>ابحث عن "Cron Jobs" أو "المهام المجدولة"</li>
                                    <li>اضغط على "Add New Cron Job" أو "إضافة مهمة جديدة"</li>
                                    <li>اختر التكرار المناسب (مثل: يومياً، أسبوعياً)</li>
                                    <li>في حقل "Command" اكتب الأمر التالي:</li>
                                </ol>
                                
                                <div class="bg-light p-3 rounded mt-3">
                                    <code>/usr/local/bin/php /home/username/public_html/artisan schedule:run</code>
                                </div>
                                
                                <div class="mt-3">
                                    <h6><strong>ملاحظات مهمة:</strong></h6>
                                    <ul class="text-left">
                                        <li>استبدل "username" باسم المستخدم الخاص بك</li>
                                        <li>استبدل "public_html" بمسار التطبيق إذا كان مختلفاً</li>
                                        <li>تأكد من أن PHP متاح في المسار المحدد</li>
                                        <li>يُنصح بتشغيل Cron كل يوم للحصول على أفضل أداء</li>
                                    </ul>
                                </div>

                                <div class="mt-3">
                                    <h6><strong>للتحقق من عمل Cron:</strong></h6>
                                    <p class="text-left">يمكنك التحقق من عمل Cron من خلال مراجعة ملفات السجل في cPanel أو إضافة مهمة اختبار بسيطة.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let phoneContainer = document.getElementById('phone-numbers-container');
    let addPhoneBtn = document.getElementById('add-phone-number');
    let index = {{ count((array) config('settings.phone_numbers', [])) }};

    addPhoneBtn.addEventListener('click', function () {
        let newPhoneRow = `
            <div class="input-group mb-2">
                <input type="text" name="phone_numbers[${index}][name]" class="form-control text-right" placeholder="{{ __('main.name_placeholder') }}">
                <input type="text" name="phone_numbers[${index}][number]" class="form-control text-right" placeholder="{{ __('main.number_placeholder') }}">
                <div class="input-group-append">
                    <button class="btn btn-danger remove-phone-number" type="button">{{ __('main.remove') }}</button>
                </div>
            </div>`;
        phoneContainer.insertAdjacentHTML('beforeend', newPhoneRow);
        index++;
    });

    phoneContainer.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-phone-number')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Load backups list
    loadBackups();
    
    // Handle backup file upload
    const uploadForm = document.getElementById('upload-backup-form');
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = uploadForm.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('يرجى اختيار ملف');
            return;
        }
        
        // Check file size (50MB limit)
        if (file.size > 50 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 50 ميجابايت');
            return;
        }
        
        // Check file extension
        if (!file.name.toLowerCase().endsWith('.sql')) {
            alert('يرجى اختيار ملف .sql فقط');
            return;
        }
        
        // Show loading state
        const submitBtn = uploadForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الرفع...';
        submitBtn.disabled = true;
        
        // Create FormData and submit
        const formData = new FormData(uploadForm);
        
        fetch(uploadForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reset form
                uploadForm.reset();
                // Reload backups list
                loadBackups(currentPage);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('خطأ في رفع الملف');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

let currentPage = 1;

function loadBackups(page = 1) {
    currentPage = page;
    fetch(`{{ route("settings.list-backups") }}?page=${page}`)
        .then(response => response.json())
        .then(data => {
            const backupsList = document.getElementById('backups-list');
            const paginationDiv = document.getElementById('backups-pagination');
            
            if (data.backups.length === 0) {
                backupsList.innerHTML = '<p class="text-muted text-right">لا توجد نسخ احتياطية متاحة</p>';
                paginationDiv.style.display = 'none';
                return;
            }

            let html = '';
            data.backups.forEach(backup => {
                const size = formatFileSize(backup.size);
                const date = new Date(backup.created_at).toLocaleString('ar-SA');
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <strong class="text-primary">${backup.filename}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>${date} - 
                                <i class="fas fa-file me-1"></i>${size}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/settings/backup/${backup.filename}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i>تحميل
                            </a>
                            <button onclick="restoreBackup('${backup.filename}')" 
                                    class="btn btn-sm btn-warning">
                                <i class="fas fa-undo me-1"></i>استعادة
                            </button>
                            <button onclick="deleteBackup('${backup.filename}')" 
                                    class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>حذف
                            </button>
                        </div>
                    </div>
                `;
            });
            backupsList.innerHTML = html;
            
            // Show pagination if there are multiple pages
            if (data.last_page > 1) {
                showPagination(data.current_page, data.last_page);
                paginationDiv.style.display = 'block';
            } else {
                paginationDiv.style.display = 'none';
            }
        })
        .catch(error => {
            document.getElementById('backups-list').innerHTML = '<p class="text-danger text-right">خطأ في تحميل النسخ الاحتياطية</p>';
        });
}

function showPagination(currentPage, lastPage) {
    const paginationList = document.getElementById('pagination-list');
    let html = '';
    
    // Previous button
    if (currentPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadBackups(${currentPage - 1})">السابق</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= lastPage; i++) {
        if (i === currentPage) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadBackups(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    if (currentPage < lastPage) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadBackups(${currentPage + 1})">التالي</a></li>`;
    }
    
    paginationList.innerHTML = html;
}

function deleteBackup(filename) {
    if (confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟')) {
        fetch(`/settings/backup/${filename}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadBackups(currentPage); // Reload current page
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('خطأ في حذف النسخة الاحتياطية');
        });
    }
}

function restoreBackup(filename) {
    const warningMessage = `تحذير: استعادة قاعدة البيانات ستستبدل البيانات الموجودة بالبيانات من النسخة الاحتياطية "${filename}".\n\nهذا الإجراء لا يمكن التراجع عنه.\n\nهل أنت متأكد من المتابعة؟`;
    
    if (confirm(warningMessage)) {
        // Show loading state
        const restoreBtn = event.target.closest('.btn');
        const originalText = restoreBtn.innerHTML;
        restoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الاستعادة...';
        restoreBtn.disabled = true;
        
        fetch(`/settings/backup/${filename}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message + '\n\nسيتم إعادة تحميل الصفحة الآن.');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert(data.message);
                // Reset button
                restoreBtn.innerHTML = originalText;
                restoreBtn.disabled = false;
            }
        })
        .catch(error => {
            alert('خطأ في استعادة قاعدة البيانات');
            // Reset button
            restoreBtn.innerHTML = originalText;
            restoreBtn.disabled = false;
        });
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection 