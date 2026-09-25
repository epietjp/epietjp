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
      <div class="box-header with-border"><h3 class="box-title">Upload Foto Form PE</h3></div>
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
          <input type="file" id="foto_pe" name="foto_pe" accept=".jpg,.jpeg,.png,.pdf" class="form-control" style="padding:6px">
          <small class="text-muted">Format: JPG, PNG, PDF | Maks: 10MB</small>
        </div>
        <div id="preview-box" style="display:none;margin-top:10px">
          <img id="preview-img" src="" style="max-width:100%;border:1px solid #ddd;border-radius:4px">
        </div>
        <button id="btn-ocr" class="btn btn-primary btn-block" style="margin-top:10px;display:none">
          <i class="fa fa-magic"></i> Proses OCR
        </button>
      </div>
    </div>
    <div class="box box-default" id="box-raw" style="display:none">
      <div class="box-header with-border"><h3 class="box-title">Hasil OCR Raw Text</h3></div>
      <div class="box-body">
        <textarea id="raw-text" class="form-control" rows="8" style="font-size:11px;font-family:monospace" readonly></textarea>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="box box-success" id="box-parsed" style="display:none">
      <div class="box-header with-border" style="background:#27AE60;color:#fff">
        <h3 class="box-title" style="color:#fff"><i class="fa fa-check-circle"></i> Hasil Deteksi Field</h3>
      </div>
      <div class="box-body">
        <div class="alert alert-info" style="font-size:12px">
          <i class="fa fa-info-circle"></i> Periksa dan koreksi field sebelum lanjut ke form PE.
        </div>
        <div id="parsed-fields"></div>
        <button id="btn-lanjut" class="btn btn-success btn-block" style="margin-top:15px">
          <i class="fa fa-arrow-right"></i> Lanjut ke Form PE
        </button>
      </div>
    </div>
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Tips</h3></div>
      <div class="box-body" style="font-size:13px">
        <ul>
          <li>Foto terang dan tidak buram</li>
          <li>Form tidak miring (horizontal)</li>
          <li>Resolusi minimal 300 DPI</li>
          <li>Tulisan tangan cetak/kapital</li>
          <li>Selalu periksa ulang hasil OCR</li>
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

$(document).ready(function(){

    $('#foto_pe').on('change', function(){
        if(this.files && this.files.length){
            selectedFile = this.files[0];
            var ext = selectedFile.name.split('.').pop().toLowerCase();
            if(ext !== 'pdf'){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#preview-img').attr('src', e.target.result);
                    $('#preview-box').show();
                };
                reader.readAsDataURL(selectedFile);
            }
            $('#btn-ocr').show();
        }
    });

    $('#btn-ocr').on('click', function(){
        if(!selectedFile){ alert('Pilih file dulu'); return; }
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses OCR...');

        var fd = new FormData();
        fd.append('foto_pe', selectedFile);
        fd.append('id_penyakit', $('#ocr_penyakit').val());

        $.ajax({
            url: BASE + 'zoonosis/ocr_proses',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(d){
                btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Proses OCR');
                if(d.status !== 'ok'){ alert('Error: ' + d.msg); return; }
                if(d.preview) $('#preview-img').attr('src', d.preview).parent().show();
                $('#raw-text').val(d.raw_text);
                $('#box-raw').show();
                renderParsed(d.parsed);
                $('#box-parsed').show();
            },
            error: function(xhr, status, err){
                btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Proses OCR');
                alert('Error: ' + status + ' | ' + err + '\nResponse: ' + xhr.responseText.substring(0,200));
            }
        });
    });

    $('#btn-lanjut').on('click', function(){
        var data = {};
        $('.ocr-field').each(function(){ data[$(this).data('key')] = $(this).val(); });
        var penyakit = $('#ocr_penyakit').val();
        var params = '';
        $.each(data, function(k,v){ if(v) params += '&' + k + '=' + encodeURIComponent(v); });
        window.location.href = BASE + 'zoonosis/form/' + penyakit + '?' + params.substring(1);
    });

});

var fieldLabels = {
    'nama_pasien':'Nama Pasien','nik':'NIK','umur':'Umur (Tahun)',
    'jenis_kelamin':'Jenis Kelamin','alamat':'Alamat',
    'tgl_bergejala':'Tgl Mulai Sakit','tgl_laporan':'Tgl Laporan',
    'tgl_pe':'Tgl PE','nama_petugas':'Nama Petugas',
    'dp_tanggal':'Tgl Gigitan','dp_lokasi':'Lokasi Gigitan'
};

function renderParsed(data){
    var keys = Object.keys(data);
    var html = '';
    if(keys.length === 0){
        html = '<div class="alert alert-warning">Tidak ada field yang berhasil dideteksi. Coba foto yang lebih jelas.</div>';
    } else {
        html += '<p class="text-success"><b>' + keys.length + ' field</b> berhasil dideteksi:</p>';
        $.each(data, function(k, v){
            var label = fieldLabels[k] || k;
            html += '<div class="form-group" style="margin-bottom:6px">';
            html += '<label style="font-size:11px;color:#666">' + label + '</label>';
            html += '<input type="text" class="form-control input-sm ocr-field" data-key="' + k + '" value="' + v + '">';
            html += '</div>';
        });
    }
    $('#parsed-fields').html(html);
}
</script>
