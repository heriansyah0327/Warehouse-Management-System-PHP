        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.app-layout -->

<!-- Modal konfirmasi hapus (dipakai halaman yang punya tombol data-delete-url) -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h3>Hapus Data</h3>
        <p id="deleteMessage">Apakah kamu yakin ingin menghapus data ini?</p>
        <form id="deleteForm" method="POST" action="">
            <div class="modal-actions">
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                <button type="button" class="btn btn-outline" id="deleteCancel">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= $base ?>assets/js/script.js"></script>
</body>
</html>
