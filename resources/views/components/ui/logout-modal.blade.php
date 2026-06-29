<div class="modal fade ubp-record-modal" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" action="{{ route('logout', absolute: false) }}" class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 18px;">
            @csrf
            <div class="mb-3 d-flex justify-content-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #fee2e2; color: #ef4444;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <path d="M16 17l5-5-5-5"/>
                        <path d="M21 12H9"/>
                    </svg>
                </div>
            </div>
            <h5 class="mb-2 fw-bold" style="color: #1e293b;">Konfirmasi Logout</h5>
            <p class="text-muted small mb-4">Apakah Anda yakin ingin keluar dari akun ini?</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px; padding: 8px 16px;">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold" style="border-radius: 10px; padding: 8px 16px; background-color: #ef4444; border-color: #ef4444;" onclick="this.disabled=true; this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Logout...'; this.form.submit();">Ya, Logout</button>
            </div>
        </form>
    </div>
</div>
