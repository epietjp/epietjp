<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-object-group"></i> Daftar Cluster Zoonosis</h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li class="active">Cluster</li>
    </ol>
  </section>
  <section class="content">
<style>
.filter-bar{background:#f8f9fa;border-radius:8px;padding:14px 16px;margin-bottom:14px}
.tbl-cl td,.tbl-cl th{font-size:13px;vertical-align:middle}
.badge-aktif{background:#2980b9}.badge-selesai{background:#27ae60}.badge-klb{background:#c0392b}
.stat-pill{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;color:#fff;margin-right:3px}
</style>

<div class="filter-bar">
  <div class="row">
    <div class="col-sm-2">
      <label style="font-size:12px">Penyakit</label>
      <select id="f_penyakit" class="form-control input-sm">
        <option value="0">-- Semua --</option>
        <?php foreach($penyakit as $id_p=>$info): ?>
        <option value="<?=$id_p?>"><?=htmlspecialchars($info['singkat'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-2">
      <label style="font-size:12px">Provinsi</label>
      <select id="f_prop" class="form-control input-sm">
        <option value="0">-- Semua --</option>
        <?php foreach($list_prop as $pr): ?>
        <option value="<?=$pr['id']?>"><?=htmlspecialchars($pr['propinsi'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-2">
      <label style="font-size:12px">Kab/Kota</label>
      <select id="f_kota" class="form-control input-sm">
        <option value="0">-- Semua --</option>
      </select>
    </div>
    <div class="col-sm-2">
      <label style="font-size:12px">Status</label>
      <select id="f_status" class="form-control input-sm">
        <option value="">-- Semua --</option>
        <option value="0">Aktif</option>
        <option value="1">Selesai</option>
        <option value="2">Eskalasi KLB</option>
      </select>
    </div>
    <div class="col-sm-2">
      <label style="font-size:12px">Dari Tgl Mulai</label>
      <input type="date" id="f_tgl1" class="form-control input-sm" value="<?=date('Y-01-01')?>">
    </div>
    <div class="col-sm-2">
      <label style="font-size:12px">Sampai</label>
      <input type="date" id="f_tgl2" class="form-control input-sm" value="<?=date('Y-m-d')?>">
    </div>
  </div>
  <div class="row" style="margin-top:8px">
    <div class="col-sm-12">
      <button class="btn btn-primary btn-sm" onclick="loadList()"><i class="fa fa-search"></i> Cari</button>
      <a href="<?=site_url('zoonosis/cluster_form')?>" class="btn btn-success btn-sm">
        <i class="fa fa-plus"></i> Buat Cluster Baru
      </a>
    </div>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-table"></i> Data Cluster
      <span class="badge" id="jml-cl" style="background:#2980b9">0</span>
    </h3>
  </div>
  <div class="box-body table-responsive" style="padding:0">
    <table class="table table-bordered table-striped tbl-cl">
      <thead>
        <tr style="background:#1F3864;color:#fff">
          <th>No Cluster</th><th>Penyakit</th><th>Wilayah</th><th>Nama Kejadian</th>
          <th>Tgl Mulai</th><th>Tgl Selesai</th><th>Kasus</th><th>Konfirmasi</th>
          <th>Meninggal</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tbody-cl">
        <tr><td colspan="11" class="text-center text-muted">Klik Cari untuk memuat data</td></tr>
      </tbody>
    </table>
  </div>
</div>

  </section>
</div>
<script>
var BASE='<?=base_url()?>';
var STATUS_LABEL={0:'Aktif',1:'Selesai',2:'Eskalasi KLB'};
var STATUS_CLASS={0:'badge-aktif',1:'badge-selesai',2:'badge-klb'};

$('#f_prop').change(function(){
  var id=$(this).val();
  $('#f_kota').html('<option value="0">-- Semua --</option>');
  if(!id||id=='0') return;
  $.get(BASE+'zoonosis/get_kota/'+id,function(rows){
    $.each(rows,function(i,r){ $('#f_kota').append('<option value="'+r.id+'">'+r.kota+'</option>'); });
  },'json');
});

function loadList(){
  var p={id_penyakit:$('#f_penyakit').val(),id_prop:$('#f_prop').val(),
    id_kota:$('#f_kota').val(),status:$('#f_status').val(),
    tgl1:$('#f_tgl1').val(),tgl2:$('#f_tgl2').val()};
  $('#tbody-cl').html('<tr><td colspan="11" class="text-center"><i class="fa fa-spinner fa-spin"></i></td></tr>');
  $.get(BASE+'zoonosis/get_cluster_list',p,function(rows){
    $('#jml-cl').text(rows.length);
    if(!rows.length){$('#tbody-cl').html('<tr><td colspan="11" class="text-center text-muted">Tidak ada data</td></tr>');return;}
    var html='';
    $.each(rows,function(i,r){
      var st=parseInt(r.status_cluster);
      var badge='<span class="badge '+(STATUS_CLASS[st]||'')+'">'+STATUS_LABEL[st]+'</span>';
      html+='<tr>'
        +'<td><b>'+r.no_cluster+'</b></td>'
        +'<td><small>'+r.nama_penyakit+'</small></td>'
        +'<td><small>'+(r.propinsi||'-')+' / '+(r.kota||'-')+'</small></td>'
        +'<td>'+(r.nama_cluster||'-')+'</td>'
        +'<td>'+(r.tgl_mulai||'-')+'</td>'
        +'<td>'+(r.tgl_selesai||'-')+'</td>'
        +'<td class="text-center"><b>'+r.jumlah_kasus+'</b></td>'
        +'<td class="text-center"><span class="text-success">'+r.konfirmasi+'</span></td>'
        +'<td class="text-center">'+(r.meninggal>0?'<span class="text-danger"><b>'+r.meninggal+'</b></span>':0)+'</td>'
        +'<td class="text-center">'+badge+(r.no_klb?'<br><small>'+r.no_klb+'</small>':'')+'</td>'
        +'<td style="white-space:nowrap">'
        +'<a href="'+BASE+'zoonosis/cluster_detail/'+r.id+'" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> '
        +'<a href="'+BASE+'zoonosis/cluster_form/'+r.id+'" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a> '
        +'<a href="'+BASE+'zoonosis/cluster_hapus/'+r.id+'" class="btn btn-xs btn-danger" onclick="return confirm(\'Hapus cluster ini?\')"><i class="fa fa-trash"></i></a>'
        +'</td></tr>';
    });
    $('#tbody-cl').html(html);
  },'json');
}
$(function(){loadList();});
</script>
