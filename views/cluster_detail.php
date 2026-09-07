<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-object-group"></i> <?=htmlspecialchars($title)?></h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li><a href="<?=site_url('zoonosis/cluster')?>">Cluster</a></li>
      <li class="active"><?=htmlspecialchars($cluster['no_cluster'])?></li>
    </ol>
  </section>
  <section class="content">
<?php
$STATUS_CL = array(0=>'Aktif',1=>'Selesai',2=>'Eskalasi KLB');
$STATUS_PE = array(0=>'Suspek',1=>'Probable',2=>'Konfirmasi',3=>'Discarded');
$AKHIR     = array(1=>'Sembuh',2=>'Meninggal',3=>'Perawatan');
$st = (int)$cluster['status_cluster'];
$st_color = array(0=>'#2980b9',1=>'#27ae60',2=>'#c0392b');
$hex = isset($st_color[$st]) ? $st_color[$st] : '#2980b9';
$p_meta = isset($penyakit[$cluster['id_penyakit']]) ? $penyakit[$cluster['id_penyakit']] : array('nama'=>'Zoonosis','warna'=>'info');
$p_warna = array('danger'=>'#e74c3c','warning'=>'#e67e22','dark'=>'#2c3e50','info'=>'#2980b9');
$p_hex = isset($p_warna[$p_meta['warna']]) ? $p_warna[$p_meta['warna']] : '#2980b9';
?>
<style>
.cl-header{border-radius:10px;padding:16px 20px;color:#fff;margin-bottom:16px}
.kpi-card{background:#fff;border:1px solid #dce3ec;border-radius:8px;padding:14px 16px;text-align:center}
.kpi-card .num{font-size:32px;font-weight:700;line-height:1}
.kpi-card .lbl{font-size:12px;color:#888;margin-top:4px}
.section-box{background:#fff;border:1px solid #dce3ec;border-radius:8px;padding:16px 20px;margin-bottom:14px}
.section-title{font-size:13px;font-weight:700;color:#2c3e50;border-left:4px solid #2E75B6;padding-left:10px;margin-bottom:12px}
.tbl-pe td,.tbl-pe th{font-size:12px;vertical-align:middle}
dl.dl-cl dt{font-size:12px;color:#888;font-weight:600;margin-bottom:2px}
dl.dl-cl dd{font-size:13px;color:#2c3e50;margin-bottom:10px}
</style>

<!-- Header -->
<div class="cl-header" style="background:<?=$p_hex?>">
  <div class="row">
    <div class="col-sm-7">
      <div style="font-size:13px;opacity:.85"><?=htmlspecialchars($p_meta['nama'])?></div>
      <h3 style="margin:4px 0;font-size:22px;font-weight:700"><?=htmlspecialchars($cluster['no_cluster'])?></h3>
      <div style="font-size:14px;margin-top:4px"><?=htmlspecialchars($cluster['nama_cluster'])?></div>
      <div style="font-size:12px;opacity:.85;margin-top:6px">
        <?=htmlspecialchars($cluster['propinsi'])?> / <?=htmlspecialchars($cluster['kota'])?>
        &nbsp;·&nbsp; <?=htmlspecialchars($cluster['tgl_mulai'])?> s/d <?=($cluster['tgl_selesai']?htmlspecialchars($cluster['tgl_selesai']):'sekarang')?>
      </div>
    </div>
    <div class="col-sm-5 text-right">
      <div style="font-size:20px;font-weight:700;opacity:.9"><?=isset($STATUS_CL[$st])?$STATUS_CL[$st]:'-'?></div>
      <?php if($cluster['no_klb']): ?>
      <div style="background:rgba(0,0,0,.2);display:inline-block;padding:3px 10px;border-radius:10px;font-size:12px;margin-top:4px">
        <i class="fa fa-exclamation-triangle"></i> KLB: <?=htmlspecialchars($cluster['no_klb'])?>
      </div>
      <?php endif; ?>
      <div style="margin-top:10px">
        <a href="<?=site_url('zoonosis/cluster_form/'.$cluster['id'])?>" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4)">
          <i class="fa fa-pencil"></i> Edit
        </a>
        <a href="<?=site_url('zoonosis/cluster')?>" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)">
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>

<!-- KPI -->
<div class="row" style="margin-bottom:14px">
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #2980b9">
      <div class="num" style="color:#2980b9"><?=$total_kasus?></div>
      <div class="lbl">Total Kasus</div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #27ae60">
      <div class="num" style="color:#27ae60"><?=$total_konfirm?></div>
      <div class="lbl">Konfirmasi</div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #c0392b">
      <div class="num" style="color:#c0392b"><?=$total_mati?></div>
      <div class="lbl">Meninggal</div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #e67e22">
      <div class="num" style="color:#e67e22"><?=$attack_rate?>%</div>
      <div class="lbl">Attack Rate</div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #8e44ad">
      <div class="num" style="color:#8e44ad"><?=$cfr?>%</div>
      <div class="lbl">CFR</div>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="kpi-card" style="border-top:3px solid #7f8c8d">
      <div class="num" style="color:#7f8c8d"><?=$cluster['jumlah_terpapar']?:'-'?></div>
      <div class="lbl">Pop. Berisiko</div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Info cluster -->
  <div class="col-sm-4">
    <div class="section-box">
      <div class="section-title"><i class="fa fa-info-circle"></i> Detail Cluster</div>
      <dl class="dl-cl dl-horizontal">
        <dt>Sumber Paparan</dt><dd><?=htmlspecialchars($cluster['sumber_paparan']?:'-')?></dd>
        <dt>Lokasi Paparan</dt><dd><?=htmlspecialchars($cluster['lokasi_paparan']?:'-')?></dd>
        <dt>No EBS Terkait</dt><dd><?=htmlspecialchars($cluster['no_ebs']?:'-')?></dd>
        <dt>Dibuat oleh</dt><dd><?=htmlspecialchars($cluster['create_user'])?> · <?=htmlspecialchars(substr($cluster['create_date'],0,10))?></dd>
        <?php if($cluster['keterangan']): ?>
        <dt>Keterangan</dt><dd><?=nl2br(htmlspecialchars($cluster['keterangan']))?></dd>
        <?php endif; ?>
      </dl>
    </div>

    <!-- Kurva epidemi -->
    <div class="section-box">
      <div class="section-title"><i class="fa fa-bar-chart"></i> Kurva Epidemi (per Tgl Sakit)</div>
      <?php if(empty($kurva)): ?>
      <p class="text-muted" style="font-size:12px">Belum ada data kurva epidemi.</p>
      <?php else: ?>
      <canvas id="chartKurva" height="180"></canvas>
      <?php endif; ?>
    </div>
  </div>

  <!-- Daftar PE -->
  <div class="col-sm-8">
    <div class="section-box">
      <div class="section-title">
        <i class="fa fa-users"></i> Daftar Kasus PE terkait Cluster
        <span class="badge" style="background:#2980b9"><?=$total_kasus?></span>
        <button class="btn btn-xs btn-success pull-right" onclick="showLinkModal()">
          <i class="fa fa-link"></i> Link / Unlink PE
        </button>
      </div>
      <div class="table-responsive" style="max-height:420px;overflow-y:auto">
        <table class="table table-bordered table-condensed tbl-pe">
          <thead style="background:#1F3864;color:#fff;position:sticky;top:0">
            <tr><th>No PE</th><th>Nama Pasien</th><th>Tgl Sakit</th><th>Status</th><th>Kondisi</th><th>Lab</th><th>Unit Pelapor</th></tr>
          </thead>
          <tbody>
            <?php if(empty($pe_list)): ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada kasus terhubung ke cluster ini</td></tr>
            <?php else: foreach($pe_list as $i=>$pe): ?>
            <?php $sk=(int)$pe['status_kasus']; $ak=(int)$pe['akhir_no']; ?>
            <tr>
              <td><a href="<?=site_url('zoonosis/detail/'.$pe['id'])?>"><?=htmlspecialchars($pe['no_pe']?:'-')?></a></td>
              <td><?=htmlspecialchars($pe['nama_pasien'])?></td>
              <td><?=htmlspecialchars($pe['tgl_sakit']?:'-')?></td>
              <td><span class="label label-<?=array(0=>'warning',1=>'primary',2=>'success',3=>'default')[$sk]?>"><?=isset($STATUS_PE[$sk])?$STATUS_PE[$sk]:'-'?></span></td>
              <td><?=$ak==2?'<span class="text-danger"><b>Meninggal</b></span>':(isset($AKHIR[$ak])?$AKHIR[$ak]:'-')?></td>
              <td class="text-center"><?=$pe['diperiksa_lab']?'<i class="fa fa-check-circle text-success"></i>':'-'?></td>
              <td><small><?=htmlspecialchars($pe['unit_pelapor']?:'-')?></small></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Link/Unlink PE -->
<div class="modal fade" id="modalLinkPE" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#1F3864;color:#fff">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff"><span>&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-link"></i> Link / Unlink PE ke Cluster <?=htmlspecialchars($cluster['no_cluster'])?></h4>
      </div>
      <div class="modal-body" id="modalLinkBody">
        <div class="text-center"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
    </div>
  </div>
</div>

  </section>
</div>

<script src="<?=base_url('assets/plugins/chartjs/Chart.min.js')?>"></script>
<script>
var BASE='<?=base_url()?>';
var CL_ID=<?=$cluster['id']?>;

<?php if(!empty($kurva)): ?>
var kurvaData = <?=json_encode($kurva)?>;
var ctx=document.getElementById('chartKurva').getContext('2d');
new Chart(ctx,{
  type:'bar',
  data:{
    labels:kurvaData.map(function(r){return r.tgl_sakit;}),
    datasets:[
      {label:'Total',data:kurvaData.map(function(r){return parseInt(r.total);}),backgroundColor:'rgba(52,152,219,0.7)',borderColor:'#2980b9',borderWidth:1},
      {label:'Konfirmasi',data:kurvaData.map(function(r){return parseInt(r.konfirmasi);}),backgroundColor:'rgba(39,174,96,0.7)',borderColor:'#27ae60',borderWidth:1},
      {label:'Meninggal',data:kurvaData.map(function(r){return parseInt(r.meninggal);}),backgroundColor:'rgba(192,57,43,0.7)',borderColor:'#c0392b',borderWidth:1},
    ]
  },
  options:{responsive:true,scales:{yAxes:[{ticks:{beginAtZero:true,precision:0}}]},legend:{position:'bottom',labels:{boxWidth:12,fontSize:10}}}
});
<?php endif; ?>

function showLinkModal() {
  $('#modalLinkBody').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Memuat PE...</div>');
  $('#modalLinkPE').modal('show');
  $.get(BASE+'zoonosis/get_pe_for_cluster/'+CL_ID, function(rows) {
    if(!rows.length){ $('#modalLinkBody').html('<p class="text-muted text-center">Tidak ada PE untuk penyakit ini.</p>'); return; }
    var html='<table class="table table-bordered table-condensed"><thead><tr style="background:#1F3864;color:#fff">'
      +'<th>No PE</th><th>Nama Pasien</th><th>Tgl Sakit</th><th>Status</th><th>Cluster Saat Ini</th><th>Aksi</th></tr></thead><tbody>';
    var STATUS={0:'Suspek',1:'Probable',2:'Konfirmasi',3:'Discarded'};
    $.each(rows,function(i,r){
      var linked = r.id_cluster == CL_ID;
      var cl_info = r.id_cluster ? (linked?'<span class="text-success">Cluster ini</span>':'<span class="text-warning">Cluster lain</span>') : '-';
      var btn = linked
        ? '<button class="btn btn-xs btn-danger" onclick="linkPE('+r.id+\',unlink\')"><i class="fa fa-unlink"></i> Unlink</button>'
        : '<button class="btn btn-xs btn-success" onclick="linkPE('+r.id+\',link\')"><i class="fa fa-link"></i> Link</button>';
      html+='<tr><td>'+r.no_pe+'</td><td>'+r.nama_pasien+'</td><td>'+(r.tgl_sakit||'-')+'</td>'
        +'<td>'+(STATUS[parseInt(r.status_kasus)]||'-')+'</td><td>'+cl_info+'</td><td>'+btn+'</td></tr>';
    });
    html+='</tbody></table>';
    $('#modalLinkBody').html(html);
  },'json');
}

function linkPE(id_pe, action) {
  $.post(BASE+'zoonosis/cluster_link_pe',{id_cluster:CL_ID,id_pe:id_pe,action:action},function(res){
    if(res.status=='ok') { showLinkModal(); setTimeout(function(){location.reload();},1500); }
  },'json');
}
</script>
