
<script>$(function(){ $('body').addClass('sidebar-collapse'); });</script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-list"></i> Daftar PE Zoonosis</h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li class="active">Daftar PE</li>
    </ol>
  </section>
  <section class="content">

    <div class="filter-bar">
      <div class="row">
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Penyakit</label>
          <select id="f_penyakit" class="form-control input-sm">
            <option value="0">-- Semua --</option>
            <?php foreach($penyakit as $id_p => $info): ?>
            <option value="<?=$id_p?>"><?=htmlspecialchars($info['singkat'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Level Wilayah</label>
          <select id="f_level" class="form-control input-sm" onchange="setLevelDaftar()">
            <option value="0">Nasional</option>
            <option value="1">Provinsi</option>
            <option value="2">Kab/Kota</option>
            <option value="3">Kecamatan</option>
            <option value="4">Unit Pelapor</option>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-prop" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Provinsi</label>
          <select id="f_prop" class="form-control input-sm" onchange="loadKotaDaftar()">
            <option value="0">-- Pilih Provinsi --</option>
            <?php foreach($list_prop as $pr): ?>
            <option value="<?=$pr['id']?>"><?=htmlspecialchars($pr['propinsi'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-kota" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Kab/Kota</label>
          <select id="f_kota" class="form-control input-sm" onchange="loadKecDaftar()">
            <option value="0">-- Pilih Kab/Kota --</option>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-kec" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Kecamatan</label>
          <select id="f_kec" class="form-control input-sm" onchange="loadPuskDaftar()">
            <option value="0">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-pusk" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Unit Pelapor</label>
          <select id="f_pusk" class="form-control input-sm">
            <option value="0">-- Pilih Unit Pelapor --</option>
          </select>
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Tahun</label>
          <select id="f_tahun" class="form-control input-sm" onchange="setTahun()">
            <?php for($y=date('Y');$y>=2020;$y--): ?>
            <option value="<?=$y?>" <?=$y==date('Y')?'selected':''?>><?=$y?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Dari Tanggal</label>
          <input type="date" id="f_tgl1" class="form-control input-sm" value="<?=date('Y-01-01')?>">
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Sampai</label>
          <input type="date" id="f_tgl2" class="form-control input-sm" value="<?=date('Y-m-d')?>">
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">&nbsp;</label><br>
          <button class="btn btn-primary btn-sm" onclick="loadDaftar()">
            <i class="fa fa-search"></i> Cari
          </button>
          <button class="btn btn-success btn-sm" onclick="exportCsv()">
            <i class="fa fa-download"></i> CSV
          </button>
          <a href="<?=site_url('zoonosis/download_template')?>" class="btn btn-default btn-sm" title="Download Template Excel untuk import batch PE">
            <i class="fa fa-file-excel-o"></i> Template
          </a>
          <a href="<?=site_url('zoonosis/cluster')?>" class="btn btn-warning btn-sm"><i class="fa fa-object-group"></i> Cluster</a>
          <a href="<?=site_url('zoonosis/upload_excel')?>" class="btn btn-info btn-sm" title="Upload Excel untuk import batch PE">
            <i class="fa fa-upload"></i> Import Excel
          </a>
        </div>
      </div>
      <div class="row" style="margin-top:8px">
        <div class="col-sm-4">
          <input type="text" id="f_cari" class="form-control input-sm" placeholder="Cari nama pasien / No PE / NIK...">
        </div>
        <div class="col-sm-8" style="padding-top:4px">
          <?php foreach($penyakit as $id_p => $info): ?>
          <a href="<?=site_url('zoonosis/zoonosis_ocr')?>" class="btn btn-warning btn-sm" style="margin-right:5px"><i class="fa fa-camera"></i> OCR Scan Form</a>
        <a href="<?=site_url('zoonosis/form/'.$id_p)?>" class="btn btn-xs btn-<?=$info['warna']?>" style="margin-right:4px">
            <i class="fa fa-plus"></i> <?=htmlspecialchars($info['singkat'])?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table"></i> Data PE
          <span class="badge" id="jml-total" style="background:#2980b9">0</span>
        </h3>
      </div>
      <div class="box-body table-responsive" style="padding:0">
        <table class="table table-bordered table-striped tbl-zoo" id="tblDaftar">
          <thead>
            <tr style="background:#2c3e50;color:#fff">
              <th>No PE</th><th>No EBS</th><th>Penyakit</th><th>Provinsi</th><th>Kab/Kota</th>
              <th>Nama Pasien</th><th>Kelamin</th><th>Umur</th>
              <th>Tgl Sakit</th><th>Tgl PE</th><th>Status</th><th>Kondisi</th>
              <th>Lab</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody-daftar">
            
          </tbody>
        </table>
      </div>
    </div>

  </section>
</div>
</div>

<script>
var BASE = '<?=base_url()?>';
var STATUS_LABEL = {0:'Suspek',1:'Probable',2:'Konfirmasi',3:'Discarded'};
var STATUS_CLASS  = {0:'badge-suspek',1:'badge-probable',2:'badge-konfirmasi',3:'badge-discarded'};
var AKHIR_LABEL  = {1:'Sembuh',2:'Meninggal',3:'Perawatan'};

$('#f_prop').change(function() {
    var id = $(this).val();
    $('#f_kota').html('<option value="0">-- Semua --</option>');
    if (!id || id=='0') return;
    $.get(BASE+'zoonosis/get_kota/'+id, function(rows) {
        $.each(rows, function(i,r) {
            $('#f_kota').append('<option value="'+r.id+'">'+r.kota+'</option>');
        });
    },'json');
});

function setLevelDaftar() {
    var lv = parseInt($('#f_level').val());
    $('#wrap-prop').toggle(lv>0);
    $('#wrap-kota').toggle(lv>1);
    $('#wrap-kec').toggle(lv>2);
    $('#wrap-pusk').toggle(lv>3);
    if (lv==0) { $('#f_prop,#f_kota,#f_kec,#f_pusk').val(0); }
    if (lv<2)  { $('#f_kota,#f_kec,#f_pusk').val(0); }
    if (lv<3)  { $('#f_kec,#f_pusk').val(0); }
    if (lv<4)  { $('#f_pusk').val(0); }
}
function loadKotaDaftar() {
    var id = $('#f_prop').val();
    $('#f_kota').html('<option value="0">-- Pilih Kab/Kota --</option>');
    $('#f_kec').html('<option value="0">-- Pilih Kecamatan --</option>');
    $('#f_pusk').html('<option value="0">-- Pilih Unit Pelapor --</option>');
    if (!id||id==0) return;
    $.get(BASE+'zoonosis/get_kota/'+id,function(rows){
        $.each(rows,function(i,r){ $('#f_kota').append('<option value="'+r.id+'">'+r.kota+'</option>'); });
    },'json');
}
function loadKecDaftar() {
    var id = $('#f_kota').val();
    $('#f_kec').html('<option value="0">-- Pilih Kecamatan --</option>');
    $('#f_pusk').html('<option value="0">-- Pilih Unit Pelapor --</option>');
    if (!id||id==0) return;
    $.get(BASE+'zoonosis/get_kecamatan/'+id,function(rows){
        $.each(rows,function(i,r){ $('#f_kec').append('<option value="'+r.id+'">'+r.distrik+'</option>'); });
    },'json');
}
function loadPuskDaftar() {
    var id = $('#f_kec').val();
    $('#f_pusk').html('<option value="0">-- Pilih Unit Pelapor --</option>');
    if (!id||id==0) return;
    $.get(BASE+'zoonosis/get_puskesmas_by_kec/'+id,function(rows){
        $.each(rows,function(i,r){ $('#f_pusk').append('<option value="'+r.id+'">'+r.puskesmas+'</option>'); });
    },'json');
}
function setTahun() {
    var y = $("#f_tahun").val();
    $("#f_tgl1").val(y+"-01-01");
    $("#f_tgl2").val(y+"-12-31");
    loadDaftar();
}
function loadDaftar() {
    if ($.fn.DataTable.isDataTable('#tblDaftar')) {
        $('#tblDaftar').DataTable().ajax.reload(null, false);
    }
}

function exportCsv() {
    var p = '?id_penyakit='+$('#f_penyakit').val()
        +'&id_prop='+$('#f_prop').val()
        +'&id_kota='+$('#f_kota').val()
        +'&tgl1='+$('#f_tgl1').val()
        +'&tgl2='+$('#f_tgl2').val();
    window.location = BASE+'zoonosis/export_csv'+p;
}

function openEbs(no_ebs) {
    $.get(BASE+'zoonosis/get_ebs_id/'+no_ebs, function(id) {
        if (id && id > 0) {
            window.open(BASE+'form-ebs-new/view/'+id, '_blank');
        } else {
            alert('EBS tidak ditemukan: '+no_ebs);
        }
    }, 'json');
}
$(function() {
    // Init DataTables server-side
    var dt = $('#tblDaftar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + 'zoonosis/get_daftar',
            type: 'GET',
            data: function(d) {
                d.id_penyakit = $('#f_penyakit').val();
                d.id_prop     = $('#f_prop').val();
                d.id_kota     = $('#f_kota').val();
                d.id_kec      = $('#f_kec').val() || 0;
                d.id_pusk     = $('#f_pusk').val() || 0;
                d.tgl1        = $('#f_tgl1').val();
                d.tgl2        = $('#f_tgl2').val();
                d.cari        = $('#f_cari').val();
            }
        },
        columns: [
            {data:'no_pe',        render: function(d,t,r){ return d ? '<a href="'+BASE+'zoonosis/detail/'+r.id+'" target="_blank">'+d+'</a>' : '-'; }},
            {data:'no_ebs',       render: function(d,t,r){ return d ? '<a href="javascript:void(0)" onclick="openEbs(\"'+d+'\")" class="text-info">'+d+'</a>' : '-'; }},
            {data:'nama_penyakit',render: function(d){ return d||'-'; }},
            {data:'propinsi',     render: function(d){ return d||'-'; }},
            {data:'kota',         render: function(d){ return d||'-'; }},
            {data:'nama_pasien',  render: function(d){ return d||'-'; }},
            {data:'kelamin',      render: function(d){ return d=='L'?'Laki-laki':(d=='P'?'Perempuan':'-'); }},
            {data:'umur_thn',     render: function(d,t,r){ return (r.umur_thn||0)+' thn '+(r.umur_bln||0)+' bln'; }},
            {data:'tgl_sakit',    render: function(d){ return d||'-'; }},
            {data:'tgl_pe',       render: function(d){ return d||'-'; }},
            {data:'status_kasus', render: function(d){ var s=['Suspek','Probable','Konfirmasi','Discarded']; return '<span class="label label-'+(d==2?'success':d==1?'warning':d==3?'default':'info')+'">'+(s[d]||'-')+'</span>'; }},
            {data:'akhir_no',     render: function(d){ var s=['-','Sembuh','Meninggal','Dirawat RS','Dirawat Klinik','Dirawat Rumah']; return s[d]||'-'; }},
            {data:'diperiksa_lab',render: function(d){ return d==1?'<span class="label label-info">Ya</span>':'Tidak'; }},
            {data:'id',           render: function(d,t,r){ return '<a href="'+BASE+'zoonosis/detail/'+d+'" class="btn btn-xs btn-primary" target="_blank"><i class="fa fa-eye"></i></a> <a href="'+BASE+'zoonosis/edit/'+d+'" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>'; }},
        ],
        paging:    true,
        ordering:  false,
        info:      true,
        searching: false,
        lengthMenu: [[10,25,50,100],[10,25,50,100]],
        pageLength: 25,
        language: {
            lengthMenu:   'Menampilkan _MENU_ data per halaman',
            info:         'Menampilkan _START_ sampai _END_ dari _TOTAL_ records',
            infoEmpty:    'Tidak ada data',
            infoFiltered: '(dari _MAX_ total records)',
            processing:   '<i class="fa fa-spinner fa-spin"></i> Memuat...',
            paginate:     {first:'<<',last:'>>',next:'>',previous:'<'}
        }
    });

    $('#f_penyakit').change(function(){ loadDaftar(); });
    $('#f_prop').change(function(){ loadKotaDaftar(); loadDaftar(); });
    $('#f_kota').change(function(){ loadKecDaftar(); loadDaftar(); });
    $('#f_kec').change(function(){ loadPuskDaftar(); loadDaftar(); });
    $('#f_pusk').change(function(){ loadDaftar(); });
    $('#f_level').change(function(){ setLevelDaftar(); });
});
</script>