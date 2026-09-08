<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-object-group"></i> <?=htmlspecialchars($title)?></h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li><a href="<?=site_url('zoonosis/cluster')?>">Cluster</a></li>
      <li class="active">Form</li>
    </ol>
  </section>
  <section class="content">
<?php
$is_edit = !empty($cluster) && isset($cluster['id']);
$v = $is_edit ? $cluster : array();
function fvc($v,$k,$def='') { return isset($v[$k]) ? htmlspecialchars($v[$k]) : $def; }
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
/* Select2 fix - text visibility */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #2980b9 !important;
    border-color: #2471a3 !important;
    color: #fff !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    margin-right: 5px;
}
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ccc;
    min-height: 34px;
}
.select2-container--default .select2-results__option {
    color: #333 !important;
}

.form-section{background:#fff;border:1px solid #dce3ec;border-radius:8px;padding:18px 20px;margin-bottom:14px}
.form-section-title{font-size:14px;font-weight:700;color:#2c3e50;border-left:4px solid #2E75B6;padding-left:10px;margin-bottom:14px}
label{font-size:13px;font-weight:600;color:#555}
.form-control{font-size:13px}
.req{color:#e74c3c}
</style>

<form id="formCluster">
<input type="hidden" name="id" value="<?=$is_edit?$v['id']:''?>">
<?php if($is_edit): ?>
<input type="hidden" name="no_cluster" value="<?=fvc($v,'no_cluster')?>">
<?php endif; ?>

<?php if($is_edit): ?>
<div class="alert alert-info" style="padding:10px 16px;font-size:13px">
  <i class="fa fa-tag"></i> No Cluster: <b><?=fvc($v,'no_cluster')?></b>
</div>
<?php endif; ?>

<div class="form-section">
  <div class="form-section-title"><i class="fa fa-info-circle"></i> Informasi Dasar Cluster</div>
  <div class="row">
    <div class="col-sm-4">
      <div class="form-group">
        <label>Jenis Penyakit <span class="req">*</span></label>
        <select name="id_penyakit" id="sel_penyakit" class="form-control" required <?=$is_edit?'disabled':''?>>
          <option value="">-- Pilih --</option>
          <?php foreach($penyakit as $id_p=>$info): ?>
          <option value="<?=$id_p?>" <?=fvc($v,'id_penyakit')==$id_p?'selected':''?>>
            <?=htmlspecialchars($info['nama'])?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php if($is_edit): ?><input type="hidden" name="id_penyakit" value="<?=fvc($v,'id_penyakit')?>"><?php endif; ?>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="form-group">
        <label>Nama / Deskripsi Kejadian <span class="req">*</span></label>
        <input type="text" name="nama_cluster" class="form-control" required
          placeholder="contoh: KLB GHPR Desa Sukamaju Juli 2026"
          value="<?=fvc($v,'nama_cluster')?>">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3">
      <div class="form-group">
        <label>Tanggal Mulai <span class="req">*</span></label>
        <input type="date" name="tgl_mulai" class="form-control" required value="<?=fvc($v,'tgl_mulai',date('Y-m-d'))?>">
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label>Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" class="form-control" value="<?=fvc($v,'tgl_selesai')?>">
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label>Status Cluster</label>
        <select name="status_cluster" class="form-control">
          <option value="0" <?=fvc($v,'status_cluster','0')=='0'?'selected':''?>>Aktif (Investigasi)</option>
          <option value="1" <?=fvc($v,'status_cluster')=='1'?'selected':''?>>Selesai</option>
          <option value="2" <?=fvc($v,'status_cluster')=='2'?'selected':''?>>Eskalasi KLB</option>
        </select>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label>No KLB <small class="text-muted">(jika eskalasi)</small></label>
        <input type="text" name="no_klb" class="form-control" value="<?=fvc($v,'no_klb')?>">
      </div>
    </div>
  </div>
</div>

<div class="form-section">
  <div class="form-section-title"><i class="fa fa-map-marker"></i> Wilayah Kejadian</div>
  <div class="row">
    <div class="col-sm-4">
      <div class="form-group">
        <label>Provinsi</label>
        <select name="id_prop" id="sel_prop_cl" class="form-control">
          <option value="">-- Pilih --</option>
          <?php foreach($list_prop as $pr): ?>
          <option value="<?=$pr['id']?>" <?=fvc($v,'id_prop')==$pr['id']?'selected':''?>>
            <?=htmlspecialchars($pr['propinsi'])?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <label>Kab/Kota</label>
        <select name="id_kota" id="sel_kota_cl" class="form-control">
          <option value="">-- Pilih Provinsi --</option>
        </select>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <label>Unit Pelapor</label>
        <select name="id_puskesmas" id="sel_pusk_cl" class="form-control">
          <option value="">-- Pilih Kab/Kota --</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        <label>Lokasi Paparan Bersama</label>
        <input type="text" name="lokasi_paparan" class="form-control"
          placeholder="Tempat / lokasi spesifik kejadian bersama"
          value="<?=fvc($v,'lokasi_paparan')?>">
      </div>
    </div>
  </div>
</div>

<div class="form-section">
  <div class="form-section-title"><i class="fa fa-paw"></i> Hubungan Epidemiologis</div>
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
        <label>Sumber / Dugaan Paparan Bersama</label>
        <input type="text" name="sumber_paparan" class="form-control"
          placeholder="misal: Anjing liar di RT 003, Unggas di pasar X"
          value="<?=fvc($v,'sumber_paparan')?>">
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label>Estimasi Populasi Berisiko</label>
        <input type="number" name="jumlah_terpapar" class="form-control" min="0"
          placeholder="Jumlah orang yang terpapar"
          value="<?=fvc($v,'jumlah_terpapar')?>">
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label>Link No EBS <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
        <select name="no_ebs[]" id="sel-ebs" class="form-control" multiple style="width:100%">
          <?php if(!empty($v['no_ebs'])): ?>
          <?php foreach(explode(',', $v['no_ebs']) as $ebs_no): ?>
          <?php $ebs_no = trim($ebs_no); if(!$ebs_no) continue; ?>
          <option value="<?=htmlspecialchars($ebs_no)?>" selected><?=htmlspecialchars($ebs_no)?></option>
          <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label>Keterangan Tambahan</label>
    <textarea name="keterangan" class="form-control" rows="3"
      placeholder="Deskripsi hubungan epidemiologis, hasil investigasi, rencana tindak lanjut..."><?=fvc($v,'keterangan')?></textarea>
  </div>
</div>

<div style="margin-bottom:30px">
  <button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i> <?=$is_edit?'Update Cluster':'Simpan Cluster'?>
  </button>
  <a href="<?=site_url('zoonosis/cluster')?>" class="btn btn-default">
    <i class="fa fa-arrow-left"></i> Kembali
  </a>
</div>
</form>

  </section>
</div>
<script>
var BASE='<?=base_url()?>';
var initProp='<?=fvc($v,"id_prop")?>',initKota='<?=fvc($v,"id_kota")?>',initPusk='<?=fvc($v,"id_puskesmas")?>';

$('#sel_prop_cl').change(function(){
  var id=$(this).val();
  $('#sel_kota_cl').html('<option value="">-- Pilih --</option>');
  $('#sel_pusk_cl').html('<option value="">-- Pilih Kab/Kota --</option>');
  if(!id) return;
  $.get(BASE+'zoonosis/get_kota/'+id,function(rows){
    $.each(rows,function(i,r){
      var sel=r.id==initKota?' selected':'';
      $('#sel_kota_cl').append('<option value="'+r.id+'"'+sel+'>'+r.kota+'</option>');
    });
    if(initKota) $('#sel_kota_cl').trigger('change');
  },'json');
});

$('#sel_kota_cl').change(function(){
  var id=$(this).val();
  $('#sel_pusk_cl').html('<option value="">-- Pilih --</option>');
  if(!id) return;
  $.get(BASE+'zoonosis/get_puskesmas/'+id,function(rows){
    if(!rows) return;
    $.each(rows,function(i,r){
      var sel=r.id==initPusk?' selected':'';
      $('#sel_pusk_cl').append('<option value="'+r.id+'"'+sel+'>'+r.puskesmas+'</option>');
    });
  },'json');
});

$('#formCluster').submit(function(e){
  e.preventDefault();
  $.post(BASE+'zoonosis/cluster_simpan', $(this).serialize(), function(res){
    if(res.status=='ok'){
      alert('Cluster '+res.no_cluster+' berhasil disimpan.');
      window.location=BASE+'zoonosis/cluster_detail/'+res.id;
    } else { alert('Gagal: '+JSON.stringify(res)); }
  },'json').fail(function(){ alert('Error server.'); });
});

$(function(){
  if(initProp) $('#sel_prop_cl').val(initProp).trigger('change');
});
</script>

<script>
$(function() {
$.getScript("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js", function() {
    $("#sel-ebs").select2({
        tags: true,
        tokenSeparators: [','],
        placeholder: 'Klik untuk pilih EBS dari wilayah...',
        ajax: {
            url: BASE+'zoonosis/search_ebs',
            dataType: 'json',
            delay: 300,
            data: function(p) { return {q: p.term, id_prop: $('#sel_prop_cl').val(), id_kota: $('#sel_kota_cl').val()}; },
            processResults: function(d) {
                return {results: $.map(d, function(r) {
                    return {id: r.no_ebs, text: r.no_ebs+' | '+r.diagnosa+' | '+(r.kota||'-')};
                })};
            },
            cache: true
        },
        minimumInputLength: 0
    });
}); // end getScript
});
</script>
