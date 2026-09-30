@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const errors = @json($errors->all());
            const html = `
                <div style="text-align:left; font-size:14px; line-height:1.5">
                    <div style="margin-bottom:8px; font-weight:600">Gagal menyimpan. Periksa kembali:</div>
                    <ul style="margin:0; padding-left:18px">
                        ${errors.map(e => `<li>${e}</li>`).join('')}
                    </ul>
                </div>
            `;
            Swal.fire({ icon:'error', title:'Validasi Gagal', html, confirmButtonText:'Oke' });
        });
    </script>
@endif

@if (session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({ icon:'success', title:'Berhasil', text:@json(session('status')), confirmButtonText:'Oke' });
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({ icon:'error', title:'Gagal', text:@json(session('error')), confirmButtonText:'Oke' });
        });
    </script>
@endif
