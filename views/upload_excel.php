<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-upload"></i> Upload Template Excel PE Zoonosis</h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li class="active">Upload Excel</li>
    </ol>
  </section>
  <section class="content">

<style>
.upload-box{border:2px dashed #2E75B6;border-radius:10px;padding:30px;text-align:center;background:#f0f6ff;cursor:pointer;transition:all .2s}
.upload-box:hover,.upload-box.dragover{background:#daeaf5;border-color:#1F3864}
.upload-box i{font-size:3em;color:#2E75B6;margin-bottom:10px}
.step-badge{display:inline-block;width:28px;height:28px;border-radius:50%;background:#2E75B6;color:#fff;font-weight:700;font-size:13px;line-height:28px;text-align:center;margin-right:8px}
.preview-row-ok{background:#f0fff4}.preview-row-err{background:#fff0f0}
.tbl-preview td,.tbl-preview th{font-size:12px;white-space:nowrap;padding:4px 8px!important}
.badge-wajib{background:#B71C1C;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px}
.err-list li{font-size:13px;color:#B71C1C;margin-bottom:3px}
</style>

<div class="row">
  <!-- Panel kiri: upload form -->
  <div class="col-sm-5">

    <!-- Download template -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-download"></i> Langkah 1 — Download Template</h3>
      </div>
      <div class="box-body">
        <p style="font-size:13px">Download template Excel resmi, isi data PE, lalu upload kembali.</p>
        <a href="<?=site_url('zoonosis/download_template')?>" class="btn btn-success btn-block">
          <i class="fa fa-file-excel-o"></i> Download Template Excel PE Zoonosis
        </a>
        <div style="margin-top:12px;font-size:12px;color:#666">
          <b>Petunjuk singkat:</b><br>
          • Pilih sheet sesuai penyakit (GHPR / Anthrax / Leptospirosis / Avian Flu)<br>
          • Isi data mulai baris 5 ke bawah<br>
          • Kolom <span class="badge-wajib">WAJIB</span> harus diisi<br>
          • Format tanggal: <b>YYYY-MM-DD</b><br>
          • Maks <b>500 baris</b> per file
        </div>
      </div>
    </div>

    <!-- Upload form -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-upload"></i> Langkah 2 — Upload File</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          <label style="font-size:13px">Jenis Penyakit <span class="badge-wajib">WAJIB</span></label>
          <select id="sel_penyakit" class="form-control">
            <option value="">-- Pilih Penyakit --</option>
            <?php foreach($penyakit as $id_p => $info): ?>
            <option value="<?=$id_p?>"><?=htmlspecialchars($info['nama'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label style="font-size:13px">File Excel (.xlsx)</label>
          <div class="upload-box" id="dropZone" onclick="document.getElementById('file_excel').click()">
            <i class="fa fa-file-excel-o"></i>
            <div id="dropText">Klik atau drag & drop file Excel di sini</div>
            <div id="fileName" style="margin-top:8px;font-size:13px;color:#1F3864;font-weight:700;display:none"></div>
          </div>
          <input type="file" id="file_excel" accept=".xlsx,.xls" style="display:none">
        </div>
        <button class="btn btn-primary btn-block" id="btnPreview" onclick="doPreview()" disabled>
          <i class="fa fa-eye"></i> Preview Data
        </button>
      </div>
    </div>

  </div>

  <!-- Panel kanan: hasil preview -->
  <div class="col-sm-7">
    <div class="box box-success" id="boxPreview" style="display:none">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> Langkah 3 — Preview &amp; Konfirmasi</h3>
      </div>
      <div class="box-body" id="previewBody">
        <!-- diisi via JS -->
      </div>
      <div class="box-footer" id="previewFooter" style="display:none">
        <button class="btn btn-success" id="btnSimpan" onclick="doSimpan()">
          <i class="fa fa-save"></i> Simpan ke Database
        </button>
        <button class="btn btn-default" onclick="resetForm()">
          <i class="fa fa-times"></i> Batal
        </button>
        <span id="statusSimpan" style="margin-left:12px;font-size:13px"></span>
      </div>
    </div>

    <!-- Hasil simpan -->
    <div id="boxHasil" style="display:none">
      <div class="alert alert-success" id="alertHasil"></div>
    </div>
  </div>
</div>

  </section>
</div>

<script>
var BASE = '<?=base_url()?>';
var parsedRows = [];
var parsedPenyakit = 0;

// Drag & drop
var dz = document.getElementById('dropZone');
dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('dragover'); });
dz.addEventListener('dragleave', function(){ dz.classList.remove('dragover'); });
dz.addEventListener('drop', function(e){
  e.preventDefault(); dz.classList.remove('dragover');
  var f = e.dataTransfer.files[0];
  if(f){ document.getElementById('file_excel').files = e.dataTransfer.files; onFileChange(f.name); }
});
document.getElementById('file_excel').addEventListener('change', function(){
  if(this.files[0]) onFileChange(this.files[0].name);
});

function onFileChange(name) {
  document.getElementById('fileName').textContent = '📄 ' + name;
  document.getElementById('fileName').style.display = 'block';
  document.getElementById('dropText').style.display = 'none';
  checkReady();
}

document.getElementById('sel_penyakit').addEventListener('change', checkReady);

function checkReady() {
  var ok = document.getElementById('sel_penyakit').value && document.getElementById('file_excel').files[0];
  document.getElementById('btnPreview').disabled = !ok;
}

function doPreview() {
  var fd = new FormData();
  fd.append('file_excel', document.getElementById('file_excel').files[0]);
  fd.append('id_penyakit', document.getElementById('sel_penyakit').value);
  document.getElementById('btnPreview').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Membaca file...';
  document.getElementById('btnPreview').disabled = true;

  $.ajax({
    url: BASE+'zoonosis/preview_excel',
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    success: function(res) {
      document.getElementById('btnPreview').innerHTML = '<i class="fa fa-eye"></i> Preview Data';
      document.getElementById('btnPreview').disabled = false;
      if(res.status === 'error') {
        alert('Error: ' + res.msg);
        return;
      }
      renderPreview(res);
    },
    error: function(){ alert('Gagal menghubungi server.'); document.getElementById('btnPreview').innerHTML='<i class="fa fa-eye"></i> Preview Data'; document.getElementById('btnPreview').disabled=false; }
  });
}

function renderPreview(res) {
  parsedRows     = res.rows;
  parsedPenyakit = res.id_penyakit;
  document.getElementById('boxPreview').style.display = 'block';

  var html = '';

  // Summary
  html += '<div class="row" style="margin-bottom:12px">'
    + '<div class="col-sm-4"><div class="info-box" style="min-height:60px"><span class="info-box-icon bg-green" style="font-size:24px;line-height:60px"><i class="fa fa-check"></i></span>'
    + '<div class="info-box-content"><span class="info-box-text" style="font-size:12px">Data Valid</span><span class="info-box-number">'+res.total+'</span></div></div></div>'
    + '<div class="col-sm-4"><div class="info-box" style="min-height:60px"><span class="info-box-icon bg-red" style="font-size:24px;line-height:60px"><i class="fa fa-exclamation"></i></span>'
    + '<div class="info-box-content"><span class="info-box-text" style="font-size:12px">Error</span><span class="info-box-number">'+(res.errors?res.errors.length:0)+'</span></div></div></div>'
    + '<div class="col-sm-4"><div class="info-box" style="min-height:60px"><span class="info-box-icon bg-blue" style="font-size:24px;line-height:60px"><i class="fa fa-bug"></i></span>'
    + '<div class="info-box-content"><span class="info-box-text" style="font-size:12px">Penyakit</span><span class="info-box-number" style="font-size:14px">'+res.penyakit+'</span></div></div></div>'
    + '</div>';

  // Error list
  if(res.errors && res.errors.length) {
    html += '<div class="alert alert-warning"><b><i class="fa fa-warning"></i> Baris berikut tidak diimport (ada error):</b><ul class="err-list" style="margin-top:8px">';
    res.errors.forEach(function(e){ html += '<li>'+e+'</li>'; });
    html += '</ul></div>';
  }

  if(!res.rows || !res.rows.length) {
    html += '<div class="alert alert-danger">Tidak ada data valid yang bisa diimport.</div>';
    document.getElementById('previewBody').innerHTML = html;
    document.getElementById('previewFooter').style.display = 'none';
    return;
  }

  // Tabel preview (ambil 20 kolom penting)
  var SHOW_COLS = ['no_pe','nama_pasien','kelamin','umur_thn','tgl_sakit','status_kasus','akhir_no','gejala','diperiksa_lab','hasil_lab'];
  html += '<div class="table-responsive" style="max-height:340px;overflow-y:auto">'
    + '<table class="table table-bordered table-condensed tbl-preview"><thead><tr style="background:#1F3864;color:#fff">'
    + '<th>#</th>';
  SHOW_COLS.forEach(function(c){ html += '<th>'+c+'</th>'; });
  html += '</tr></thead><tbody>';
  res.rows.forEach(function(row, i){
    html += '<tr class="preview-row-ok"><td class="text-center">'+(i+1)+'</td>';
    SHOW_COLS.forEach(function(c){ html += '<td>'+(row[c]!==null&&row[c]!==undefined?row[c]:'-')+'</td>'; });
    html += '</tr>';
  });
  html += '</tbody></table></div>';
  html += '<small class="text-muted">Menampilkan '+res.rows.length+' baris valid. Klik Simpan untuk menyimpan ke database.</small>';

  document.getElementById('previewBody').innerHTML = html;
  document.getElementById('previewFooter').style.display = 'block';
  document.getElementById('boxHasil').style.display = 'none';
}

function doSimpan() {
  if(!parsedRows.length){ alert('Tidak ada data.'); return; }
  if(!confirm('Simpan '+parsedRows.length+' data PE ke database?')) return;
  document.getElementById('btnSimpan').disabled = true;
  document.getElementById('statusSimpan').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

  $.post(BASE+'zoonosis/proses_excel', {
    rows_json:    JSON.stringify(parsedRows),
    id_penyakit:  parsedPenyakit,
  }, function(res){
    document.getElementById('btnSimpan').disabled = false;
    document.getElementById('statusSimpan').innerHTML = '';
    if(res.status === 'ok') {
      var msg = '<b><i class="fa fa-check-circle"></i> Import selesai!</b><br>'
        + 'Berhasil disimpan: <b>'+res.inserted+'</b> data PE.<br>';
      if(res.skipped) msg += 'Dilewati (duplikat): '+res.skipped+'.<br>';
      if(res.errors && res.errors.length) {
        msg += 'Error: <ul>';
        res.errors.forEach(function(e){ msg += '<li>'+e+'</li>'; });
        msg += '</ul>';
      }
      msg += '<a href="'+BASE+'zoonosis/daftar" class="btn btn-sm btn-primary" style="margin-top:8px"><i class="fa fa-list"></i> Lihat Daftar PE</a>';
      document.getElementById('alertHasil').innerHTML = msg;
      document.getElementById('boxHasil').style.display = 'block';
      document.getElementById('boxPreview').style.display = 'none';
      resetForm();
    } else {
      alert('Error: ' + res.msg);
    }
  }, 'json').fail(function(){ alert('Gagal menyimpan.'); document.getElementById('btnSimpan').disabled=false; });
}

function resetForm() {
  document.getElementById('file_excel').value = '';
  document.getElementById('fileName').style.display = 'none';
  document.getElementById('dropText').style.display = 'block';
  document.getElementById('btnPreview').disabled = true;
  parsedRows = []; parsedPenyakit = 0;
}
</script>
