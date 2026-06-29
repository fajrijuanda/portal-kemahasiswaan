<!-- Global Alert Modal for Success/Errors -->
<div class="modal fade" id="globalAlertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 18px;">
            @if(session('status'))
                <div class="mb-3 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #dcfce7; color: #22c55e;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 fw-bold" style="color: #1e293b;">Berhasil</h5>
                <p class="text-muted small mb-4">{{ session('status') }}</p>
                <button type="button" class="btn btn-success fw-bold w-100" data-bs-dismiss="modal" style="border-radius: 10px; padding: 8px 16px;">Tutup</button>
            @elseif($errors->any())
                <div class="mb-3 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #fee2e2; color: #ef4444;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 fw-bold" style="color: #1e293b;">Gagal</h5>
                <p class="text-muted small mb-4">{{ $errors->first() }}</p>
                <button type="button" class="btn btn-danger fw-bold w-100" data-bs-dismiss="modal" style="border-radius: 10px; padding: 8px 16px; background-color: #ef4444; border-color: #ef4444;">Tutup</button>
            @endif
        </div>
    </div>
</div>

@if(session('status') || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var alertModal = new bootstrap.Modal(document.getElementById('globalAlertModal'));
        alertModal.show();
    });
</script>
@endif
