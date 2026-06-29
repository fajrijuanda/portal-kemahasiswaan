<!-- Global Delete Confirmation Modal -->
<div class="modal fade ubp-record-modal" id="globalDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="globalDeleteForm" method="POST" action="" class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 18px;">
            @csrf
            @method('DELETE')
            <div class="mb-3 d-flex justify-content-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #fee2e2; color: #ef4444;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                </div>
            </div>
            <h5 class="mb-2 fw-bold" style="color: #1e293b;">Konfirmasi Hapus</h5>
            <p class="text-muted small mb-4" id="globalDeleteModalMessage">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px; padding: 8px 16px;">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold" id="confirmDeleteBtn" style="border-radius: 10px; padding: 8px 16px; background-color: #ef4444; border-color: #ef4444;" onclick="this.disabled=true; this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Menghapus...'; this.form.submit();">Ya, Hapus Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function triggerDeleteModal(actionUrl, message) {
        document.getElementById('globalDeleteForm').action = actionUrl;
        if(message) {
            document.getElementById('globalDeleteModalMessage').textContent = message;
        } else {
            document.getElementById('globalDeleteModalMessage').textContent = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
        }
        var modal = new bootstrap.Modal(document.getElementById('globalDeleteModal'));
        modal.show();
    }
</script>
