<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
<section class="content-header">
  <h1><i class="fa fa-camera"></i> OCR Form PE — Scan & Upload</h1>
  <small class="text-muted">Upload foto atau scan form PE fisik untuk dikonversi otomatis</small>
</section>
<section class="content">
<div class="row">
  <div class="col-sm-6">
    <div class="box box-primary">
      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-upload"></i> Upload Foto Form PE</h3></div>
      <div class="box-body">
        <div class="form-group">
          <label>Pilih Penyakit</label>
          <select id="ocr_penyakit" class="form-control">
            <option value="8">GHPR / Rabies</option>
            <option value="11">Avian Flu</option>
            <option value="14">Anthraks</option>
            <option value="26">Leptospirosis</option>
          </select>
        </div>
        <div class="form-group">
          <label>Upload Foto / Scan Form PE</label>
          <div id="drop-zone" style="border:2px dashed #3c8dbc;border-radius:8px;padding:40px;text-align:center;cursor:pointer;background:#f4f6f9;transition:background 0.2s">
            <i class="fa fa-cloud-upload fa-3x text-primary"></i>
            <p style="margin-top:10px;color:#666">Drag & drop foto di sini atau <b>klik untuk pilih file</b></p>
            <small class="text-muted">Format: JPG, PNG, PDF | Maks: 10MB</small>
            <input type="file" id="foto_pe" accept=".jpg,.jpeg,.png,.pdf" style="display:none">
          </div>
        </div>
        <div id="preview-box" style="display:none;margin-top:10px">
          <img id="preview-img" src="" style="max-width:100%;border:1px solid #ddd;border-radius:4px">
        </div>
        <button id="btn-ocr" class="btn btn-primary btn-block" style="margin-top:10px;display:none">
          <i class="fa fa-magic"></i> Proses OCR
        </button>
      </div>
    </div>
    <!-- Raw OCR -->
    <div class="box box-default" id="box-raw" style="display:none">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-file-text-o"></i> Hasil OCR Raw</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">
        <textarea id="raw-text" class="form-control" rows="10" style="font-size:11px;font-family:monospace" readonly></textarea>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <!-- Hasil Parsed -->
    <div class="box box-success" id="box-parsed" style="display:none">
      <div class="box-header with-border" style="background:#27AE60;color:#fff">
        <h3 class="box-title" style="color:#fff"><i class="fa fa-check-circle"></i> Hasil Deteksi Field</h3>
      </div>
      <div class="box-body">
        <div class="alert alert-info" style="font-size:12px">
          <i class="fa fa-info-circle"></i> Periksa dan koreksi field di bawah sebelum lanjut ke form PE.
        </div>
        <div id="parsed-fields"></div>
        <button id="btn-lanjut" class="btn btn-success btn-block" style="margin-top:15px">
          <i class="fa fa-arrow-right"></i> Lanjut ke Form PE dengan Data Ini
        </button>
      </div>
    </div>
    <!-- Tips -->
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Tips untuk Hasil Terbaik</h3></div>
      <div class="box-body" style="font-size:13px">
        <ul style="padding-left:18px">
          <li>Foto dalam kondisi <b>terang</b> dan tidak buram</li>
          <li>Form <b>tidak miring</b> — pastikan horizontal</li>
          <li>Resolusi minimal <b>300 DPI</b> untuk scan</li>
          <li>Tulisan tangan <b>cetak/kapital</b> lebih mudah dibaca</li>
          <li>Hindari bayangan di atas teks</li>
          <li>OCR mungkin tidak 100% akurat — selalu periksa ulang</li>
        </ul>
      </div>
    </div>
  </div>
</div>
</section>
</div>

<script>
var BASE = '<?=base_url()?>';
var selectedFile = null;
var parsedData = {};

// Drag & drop
var dz = document.getElementById('drop-zone');
dz.addEventListener('click', function(){ document.getElementById('foto_pe').click(); });
dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.style.background='#d9edf7'; });
dz.addEventListener('dragleave', function(){ dz.style.background='#f4f6f9'; });
dz.addEventListener('drop', function(e){
    e.preventDefault(); dz.style.background='#f4f6f9';
    if(e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
});
document.getElementById('foto_pe').addEventListener('change', function(){
    if(this.files.length) handleFile(this.files[0]);
});

function handleFile(file){
    selectedFile = file;
    var ext = file.name.split('.').pop().toLowerCase();
    if(ext !== 'pdf'){
        var reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-box').style.display='block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('preview-box').style.display='none';
    }
    dz.innerHTML = '<i class="fa fa-file-image-o fa-2x text-success"></i><p style="margin-top:8px;color:#27ae60"><b>'+file.name+'</b> ('+Math.round(file.size/1024)+' KB)</p>';
    document.getElementById('btn-ocr').style.display='block';
}

// Proses OCR
document.getElementById('btn-ocr').addEventListener('click', function(){
    if(!selectedFile){ alert('Pilih file dulu'); return; }
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses OCR...';

    var fd = new FormData();
    fd.append('foto_pe', selectedFile);
    fd.append('id_penyakit', document.getElementById('ocr_penyakit').value);

    fetch(BASE+'zoonosis/zoonosis_ocr/proses', { method:'POST', body:fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-magic"></i> Proses OCR';
        if(d.status !== 'ok'){
            alert('Error: ' + d.msg); return;
        }
        // Tampilkan preview hasil OCR
        if(d.preview) document.getElementById('preview-img').src = d.preview;
        document.getElementById('preview-box').style.display='block';

        // Raw text
        document.getElementById('raw-text').value = d.raw_text;
        document.getElementById('box-raw').style.display='block';

        // Parsed fields
        parsedData = d.parsed;
        renderParsed(d.parsed);
        document.getElementById('box-parsed').style.display='block';
    })
    .catch(function(e){ btn.disabled=false; btn.innerHTML='<i class="fa fa-magic"></i> Proses OCR'; alert('Error: '+e); });
});

var fieldLabels = {
    'nama_pasien':'Nama Pasien','nik':'NIK','umur':'Umur (Tahun)',
    'jenis_kelamin':'Jenis Kelamin','alamat':'Alamat',
    'tgl_bergejala':'Tgl Mulai Sakit','tgl_laporan':'Tgl Laporan',
    'tgl_pe':'Tgl PE','nama_petugas':'Nama Petugas','no_telp':'Telp/HP',
    'dp_tanggal':'Tgl Gigitan','dp_lokasi':'Lokasi Gigitan',
    'dp_hpr':'Jenis Hewan HPR','dp_sabun':'Cuci Sabun','dp_sar':'Pemberian VAR'
};

function renderParsed(data){
    var html = '';
    var count = Object.keys(data).length;
    if(count === 0){
        html = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Tidak ada field yang berhasil dideteksi. Coba foto yang lebih jelas.</div>';
    } else {
        html += '<p class="text-success"><i class="fa fa-check"></i> <b>'+count+' field</b> berhasil dideteksi:</p>';
        html += '<div style="max-height:400px;overflow-y:auto">';
        $.each(data, function(k,v){
            var label = fieldLabels[k] || k;
            html += '<div class="form-group" style="margin-bottom:8px">';
            html += '<label style="font-size:12px;color:#666">'+label+'</label>';
            html += '<input type="text" class="form-control input-sm ocr-field
            html += '</div>';
        });
        html += '</div>';
    }
    document.getElementById('parsed-fields').innerHTML = html;
}

// Lanjut ke form PE
document.getElementById('btn-lanjut').addEventListener('click', function(){
    // Kumpulkan nilai terkini dari input
    var data = {};
    $('.ocr-field').each(function(){ data[$(this).data('key')] = $(this).val(); });
    var penyakit = document.getElementById('ocr_penyakit').value;
    // Encode ke URL params dan redirect ke form PE baru
    var params = 'id_penyakit='+penyakit;
    $.each(data, function(k,v){ if(v) params += '&'+k+'='+encodeURIComponent(v); });
    window.location.href = BASE+'zoonosis/form/'+penyakit+'?'+params;
});
</script>
