
<script>$(function(){ $('body').addClass('sidebar-collapse'); });</script>
<style>
.zoo-card {
    border-radius:12px;
    padding:20px 18px 14px;
    color:#fff;
    position:relative;
    overflow:hidden;
    margin-bottom:16px;
    box-shadow:0 4px 15px rgba(0,0,0,0.15);
    min-height:130px;
}
.card-ghpr   { background:linear-gradient(135deg,#e74c3c,#c0392b); }
.card-avian  { background:linear-gradient(135deg,#3498db,#1a5276); }
.card-anthrax{ background:linear-gradient(135deg,#e67e22,#b7770d); }
.card-lepto  { background:linear-gradient(135deg,#27ae60,#1a5e38); }
.zoo-num {
    font-size:2.8em;
    font-weight:700;
    line-height:1;
    letter-spacing:-1px;
    margin-bottom:4px;
}
.zoo-label {
    font-size:0.82em;
    font-weight:600;
    opacity:0.92;
    margin-bottom:8px;
    line-height:1.3;
}
.zoo-sub {
    font-size:0.78em;
    opacity:0.85;
    border-top:1px solid rgba(255,255,255,0.3);
    padding-top:7px;
    margin-top:4px;
}
.zoo-sub b { font-weight:700; }
.zoo-icon {
    position:absolute;
    right:14px;
    top:14px;
    font-size:3.2em;
    opacity:0.18;
}
.zoo-badge {
    display:inline-block;
    background:rgba(255,255,255,0.22);
    border-radius:20px;
    padding:1px 8px;
    font-size:0.78em;
    margin-top:4px;
    font-weight:600;
}
.section-title {
    font-size:1.05em;
    font-weight:700;
    color:#2c3e50;
    margin:18px 0 12px;
    padding-bottom:6px;
    border-bottom:2px solid #e0e0e0;
}
.filter-bar {
    background:#f8f9fa;
    border-radius:8px;
    padding:14px 16px;
    margin-bottom:18px;
    border:1px solid #e0e0e0;
}
.box { border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
.box-header { border-radius:10px 10px 0 0; }
#map-zoo{height:380px;border-radius:6px;border:1px solid #ddd;}
.map-legend{background:white;padding:8px 12px;border-radius:4px;font-size:11px;line-height:2;box-shadow:0 1px 4px rgba(0,0,0,.2);}
.map-legend i{width:14px;height:14px;display:inline-block;margin-right:5px;border-radius:2px;vertical-align:middle;}
</style>
<style>
.content { padding-left: 10px !important; padding-right: 10px !important; }
.content { padding: 8px 10px !important; }
</style>
<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-bug"></i> Dashboard Zoonosis
      <small>Rabies &bull; Avian Flu &bull; Anthrax &bull; Leptospirosis</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li class="active">Zoonosis</li>
    </ol>
  </section>
  <section class="content">

    <!-- Filter -->
    <div class="filter-bar">
      <div class="row">
        <div class="col-sm-1" style="min-width:120px">
          <label style="font-size:0.82em;margin-bottom:3px">Dari Tanggal</label>
          <input type="date" id="f_tgl1" class="form-control input-sm" value="<?=date('Y-01-01')?>">
        </div>
        <div class="col-sm-1" style="min-width:120px">
          <label style="font-size:0.82em;margin-bottom:3px">Sampai</label>
          <input type="date" id="f_tgl2" class="form-control input-sm" value="<?=date('Y-m-d')?>">
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Level</label>
          <select name="f_level" id="f_level_sel" class="form-control" onchange="setLevel()">
            <option value="0">Nasional</option>
            <option value="1">Provinsi</option>
            <option value="2">Kab/Kota</option>
            <option value="3">Kecamatan</option>
            <option value="4">Unit Pelapor</option>
          </select>
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Penyakit</label>
          <select id="f_penyakit" class="form-control" onchange="syncPenyakit()">
            <option value="0">Semua Penyakit</option>
            <optgroup label="GHPR / Rabies">
              <option value="8">Semua GHPR+Rabies</option>
              <option value="8_18">-- GHPR (Suspek)</option>
              <option value="8_31">-- Rabies (Konfirmasi)</option>
            </optgroup>
            <optgroup label="Avian Flu">
              <option value="11">Semua Avian Flu</option>
              <option value="11_226">-- Suspek Flu Burung</option>
              <option value="11_32">-- Flu Burung Pd Manusia</option>
            </optgroup>
            <optgroup label="Anthraks">
              <option value="14">Anthraks</option>
            </optgroup>
            <optgroup label="Leptospirosis">
              <option value="26">Semua Leptospirosis</option>
              <option value="26_222">-- Suspek Lepto</option>
              <option value="26_24">-- Leptospirosis</option>
            </optgroup>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-prop" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Provinsi</label>
          <select id="f_prop" class="form-control input-sm" onchange="loadKota()">
            <option value="0">-- Pilih Provinsi --</option>
            <?php foreach($list_prop as $pr): ?>
            <option value="<?=$pr['id']?>"><?=htmlspecialchars($pr['propinsi'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-kota" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Kab/Kota</label>
          <select id="f_kota" class="form-control input-sm" onchange="loadKec()">
            <option value="0">-- Pilih Kab/Kota --</option>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-kec" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Kecamatan</label>
          <select id="f_kec" class="form-control input-sm" onchange="loadPusk()">
            <option value="0">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div class="col-sm-2" id="wrap-pusk" style="display:none">
          <label style="font-size:0.82em;margin-bottom:3px">Unit Pelapor</label>
          <select id="f_pusk" class="form-control input-sm">
            <option value="0">-- Pilih Unit Pelapor --</option>
          </select>
        </div>
        <div class="col-sm-4">
          <label style="font-size:0.82em;margin-bottom:3px">&nbsp;</label><br>
          <button class="btn btn-primary btn-sm" onclick="loadDashboard()">
            <i class="fa fa-search"></i> Load Data
          </button>
          <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default btn-sm">
            <i class="fa fa-list"></i> Daftar PE
          </a>
          <a href="<?=site_url('zoonosis/cluster')?>" class="btn btn-warning btn-sm">
            <i class="fa fa-object-group"></i> Cluster
          </a>
          <button class="btn btn-success btn-sm" onclick="exportDashboard('csv')" title="Export CSV">
            <i class="fa fa-file-text-o"></i> CSV
          </button>
          <button class="btn btn-info btn-sm" onclick="exportDashboard('xls')" title="Export Excel">
            <i class="fa fa-file-excel-o"></i> XLS
          </button>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="section-title"><i class="fa fa-stethoscope"></i> Ringkasan Kasus per Penyakit</div>
    <div class="row" id="kpi-row">
      <div class="col-sm-3">
        <div class="zoo-card card-ghpr" onclick="window.location='<?=site_url('zoonosis/analisa')?>#tab-8'" style="cursor:pointer" title="Klik untuk lihat analisa">
          <div class="zoo-num" id="kpi-8-total">-</div>
          <div class="zoo-label">GHPR / Rabies</div>
          <div class="zoo-sub" id="kpi-8-sub">-</div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-8-minggu">-</b></div>
          <div id="kpi-8-breakdown" style="margin-top:5px;font-size:0.78em;line-height:1.8"></div>
          <i class="fa fa-paw zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-avian" onclick="window.location='<?=site_url('zoonosis/analisa')?>#tab-11'" style="cursor:pointer" title="Klik untuk lihat analisa">
          <div class="zoo-num" id="kpi-11-total">-</div>
          <div class="zoo-label">Avian Flu</div>
          <div class="zoo-sub" id="kpi-11-sub">-</div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-11-minggu">-</b></div>
          <div id="kpi-11-breakdown" style="margin-top:5px;font-size:0.78em;line-height:1.8"></div>
          <i class="fa fa-dove zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-anthrax" onclick="window.location='<?=site_url('zoonosis/analisa')?>#tab-14'" style="cursor:pointer" title="Klik untuk lihat analisa">
          <div class="zoo-num" id="kpi-14-total">-</div>
          <div class="zoo-label">Suspek Antrax</div>
          <div class="zoo-sub" id="kpi-14-sub">-</div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-14-minggu">-</b></div>
          <div id="kpi-14-breakdown" style="margin-top:5px;font-size:0.78em;line-height:1.8"></div>
          <i class="fa fa-biohazard zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-lepto" onclick="window.location='<?=site_url('zoonosis/analisa')?>#tab-26'" style="cursor:pointer" title="Klik untuk lihat analisa">
          <div class="zoo-num" id="kpi-26-total">-</div>
          <div class="zoo-label">Leptospirosis</div>
          <div class="zoo-sub" id="kpi-26-sub">-</div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-26-minggu">-</b></div>
          <div id="kpi-26-breakdown" style="margin-top:5px;font-size:0.78em;line-height:1.8"></div>
          <i class="fa fa-tint zoo-icon"></i>
        </div>
      </div>
    </div>

    <!-- Trend Chart + Input PE -->
    <div class="row">
      <div class="col-sm-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-line-chart"></i> Trend</h3>
          </div>
          <div class="box-body">
            <div style="margin-bottom:8px;display:flex;flex-wrap:wrap;gap:4px;align-items:center">
              <select id="f_trend_p" class="form-control input-sm" style="width:180px">
                <optgroup label="GHPR / Rabies">
                  <option value="8">Semua GHPR+Rabies</option>
                  <option value="8_18">-- GHPR (Suspek)</option>
                  <option value="8_31">-- Rabies (Konfirmasi)</option>
                </optgroup>
                <optgroup label="Avian Flu">
                  <option value="11">Semua Avian Flu</option>
                  <option value="11_226">-- Suspek Flu Burung</option>
                  <option value="11_32">-- Flu Burung Pd Manusia</option>
                </optgroup>
                <optgroup label="Anthraks">
                  <option value="14">Anthraks</option>
                </optgroup>
                <optgroup label="Leptospirosis">
                  <option value="26">Semua Leptospirosis</option>
                  <option value="26_222">-- Suspek Lepto</option>
                  <option value="26_24">-- Leptospirosis</option>
                </optgroup>
              </select>
              <select id="f_trend_mode" class="form-control input-sm" style="width:100px" onchange="toggleTrendMode()">
                <option value="minggu">Mingguan</option>
                <option value="bulan">Bulanan</option>
              </select>
              <select id="f_trend_tahun" class="form-control input-sm" style="width:75px">
                <?php for($y=date('Y');$y>=2020;$y--): ?>
                <option value="<?=$y?>" <?=$y==date('Y')?'selected':''?>><?=$y?></option>
                <?php endfor; ?>
              </select>
              <select id="f_trend_bulan" class="form-control input-sm" style="width:100px;display:none">
                <option value="0">Semua Bulan</option>
                <option value="1">Januari</option><option value="2">Februari</option>
                <option value="3">Maret</option><option value="4">April</option>
                <option value="5">Mei</option><option value="6">Juni</option>
                <option value="7">Juli</option><option value="8">Agustus</option>
                <option value="9">September</option><option value="10">Oktober</option>
                <option value="11">November</option><option value="12">Desember</option>
              </select>
              <button class="btn btn-xs btn-primary" onclick="loadTrend()"><i class="fa fa-refresh"></i> Tampilkan</button>
            </div>
            <div id="trend-wilayah-info" style="font-size:0.95em;font-weight:bold;color:#2980b9;margin-bottom:6px;display:none">
              <i class="fa fa-map-marker"></i> <span id="trend-wilayah-label"></span>
            </div>
            <canvas id="chartTrend" height="120"></canvas>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-square"></i> Input PE Baru</h3>
          </div>
          <div class="box-body" style="padding:10px">
            <?php
            $btn_colors = array(8=>'btn-danger',11=>'btn-warning',14=>'btn-default',26=>'btn-info');
            foreach($penyakit as $id_p => $info):
            ?>
            <a href="<?=site_url('zoonosis/form/'.$id_p)?>"
               class="btn <?=isset($btn_colors[$id_p])?$btn_colors[$id_p]:'btn-default'?> btn-block"
               style="margin-bottom:8px;text-align:left">
              <i class="fa fa-plus"></i> <?=htmlspecialchars($info['nama'])?>
            </a>
            <?php endforeach; ?>
            <hr style="margin:10px 0">
            <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default btn-block">
              <i class="fa fa-list"></i> Lihat Semua Daftar PE
            </a>
            <hr style="margin:10px 0">
            <a href="<?=site_url('zoonosis/cluster_form')?>" class="btn btn-warning btn-block" style="margin-bottom:6px">
              <i class="fa fa-plus"></i> Input Cluster Baru
            </a>
            <a href="<?=site_url('zoonosis/cluster')?>" class="btn btn-default btn-block">
              <i class="fa fa-object-group"></i> Lihat Semua Daftar Cluster
            </a>
            <a href="<?=site_url('zoonosis/analisa')?>" class="btn btn-default btn-block" style="margin-top:6px">
              <i class="fa fa-bar-chart"></i> Analisa &amp; Grafik
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- MAP PETA SEBARAN -->
    <div class="section-title"><i class="fa fa-map"></i> Peta Sebaran Kasus</div>
    <div style="margin-bottom:8px;display:flex;align-items:center;flex-wrap:wrap;gap:4px">
      <select id="f_map_penyakit" class="form-control input-sm" style="width:200px">
        <optgroup label="GHPR / Rabies">
          <option value="8">Semua GHPR+Rabies</option>
          <option value="8_18">-- GHPR (Suspek)</option>
          <option value="8_31">-- Rabies (Konfirmasi)</option>
        </optgroup>
        <optgroup label="Avian Flu">
          <option value="11">Semua Avian Flu</option>
          <option value="11_226">-- Suspek Flu Burung</option>
          <option value="11_32">-- Flu Burung Pd Manusia</option>
        </optgroup>
        <optgroup label="Anthraks">
          <option value="14">Anthraks</option>
        </optgroup>
        <optgroup label="Leptospirosis">
          <option value="26">Semua Leptospirosis</option>
          <option value="26_222">-- Suspek Lepto</option>
          <option value="26_24">-- Leptospirosis</option>
        </optgroup>
      </select>
      <select id="f_map_level" class="form-control input-sm" style="width:130px" onchange="onMapLevelChange()">
        <option value="1">Provinsi</option>
        <option value="2">Kab/Kota</option>
        <option value="3">Kecamatan</option>
        <option value="4">Unit Pelapor</option>
      </select>
      <select id="f_map_prop" class="form-control input-sm" style="width:160px;display:none" onchange="loadMapKota()">
        <option value="0">-- Pilih Provinsi --</option>
        <?php foreach($list_prop as $pr): ?>
        <option value="<?=$pr['id']?>"><?=htmlspecialchars($pr['propinsi'])?></option>
        <?php endforeach; ?>
      </select>
      <select id="f_map_kota" class="form-control input-sm" style="width:160px;display:none">
        <option value="0">-- Pilih Kab/Kota --</option>
      </select>
      <button class="btn btn-sm btn-primary" onclick="loadMap()">
        <i class="fa fa-map-marker"></i> Tampilkan
      </button>
    </div>
    <div class="row">
      <div class="col-sm-12"><div id="map-zoo" style="height:420px"></div></div>
    </div>
    <div class="row" style="margin-top:14px">
      <div class="col-sm-12">
        <div style="font-weight:bold;margin-bottom:8px;font-size:13px;color:#2c3e50"><i class="fa fa-bar-chart"></i> Top 10 Wilayah</div>
        <div id="map-zoo-legend"></div>
      </div>
    </div>
    <br>



  </section>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var BASE = '<?=base_url()?>';
var chartTrend = null;

function setLevel() {
    var lv = parseInt($('#f_level_sel').val());
    $('#wrap-prop').toggle(lv>0);
    $('#wrap-kota').toggle(lv>1);
    $('#wrap-kec').toggle(lv>2);
    $('#wrap-pusk').toggle(lv>3);
    if (lv==0) { $('#f_prop,#f_kota,#f_kec,#f_pusk').val(0); }
    if (lv<2)  { $('#f_kota,#f_kec,#f_pusk').val(0); }
    if (lv<3)  { $('#f_kec,#f_pusk').val(0); }
    if (lv<4)  { $('#f_pusk').val(0); }
}
function loadKec() {
    var id_kota = $('#f_kota').val();
    $('#f_kec').html('<option value="0">-- Pilih Kecamatan --</option>');
    $('#f_pusk').html('<option value="0">-- Pilih Unit Pelapor --</option>');
    if (!id_kota || id_kota==0) return;
    $.get(BASE+'zoonosis/get_kecamatan/'+id_kota, function(rows) {
        $.each(rows, function(i,r) {
            $('#f_kec').append('<option value="'+r.id+'">'+r.distrik+'</option>');
        });
    },'json');
}
function loadPusk() {
    var id_kec = $('#f_kec').val();
    $('#f_pusk').html('<option value="0">-- Pilih Unit Pelapor --</option>');
    if (!id_kec || id_kec==0) return;
    $.get(BASE+'zoonosis/get_puskesmas_by_kec/'+id_kec, function(rows) {
        $.each(rows, function(i,r) {
            $('#f_pusk').append('<option value="'+r.id+'">'+r.puskesmas+'</option>');
        });
    },'json');
}
function loadKota() {
    var id_prop = $('#f_prop').val();
    $('#f_kota').html('<option value="0">-- Pilih Kab/Kota --</option>');
    if (!id_prop || id_prop==0) return;
    $.get(BASE+'zoonosis/get_kota/'+id_prop, function(rows) {
        $.each(rows, function(i,r) {
            $('#f_kota').append('<option value="'+r.id+'">'+r.kota+'</option>');
        });
    },'json');
}
function loadDashboard() {
    var tgl1 = $('#f_tgl1').val();
    var tgl2 = $('#f_tgl2').val();
    var id_prop = $('#f_prop').val();
    var id_kota = $('#f_kota').val() || 0;
    var id_kec  = $('#f_kec').val() || 0;
    var id_pusk = $('#f_pusk').val() || 0;
    var raw_p = $('#f_penyakit').val() || '0';
    var parts_p = raw_p.split('_');
    var id_penyakit_filter = parts_p[0];
    var diagnosa_filter = parts_p.length > 1 ? parts_p[1] : 0;
    $.get(BASE+'zoonosis/get_dashboard_data', {tgl1:tgl1,tgl2:tgl2,id_prop:id_prop,id_kota:id_kota,id_kec:id_kec,id_pusk:id_pusk,id_penyakit:id_penyakit_filter,diagnosa_no:diagnosa_filter}, function(d) {
    loadTrend(); // Auto reload trend setelah load dashboard
        $.each(d, function(id_p, row) {
            $('#kpi-'+id_p+'-total').text(row.total || 0);
            // Build badge dari breakdown
            var DNAME = {18:'GHPR',31:'Rabies',226:'Suspek Flu Burung',32:'Flu Burung Pd Manusia',222:'Suspek Lepto',24:'Leptospirosis',294:'Anthraks'};
            if (row.breakdown && row.breakdown.length > 0) {
                var bparts = [];
                $.each(row.breakdown, function(i,b){
                    bparts.push((DNAME[b.diagnosa_no]||'Diagnosa '+b.diagnosa_no)+': <b>'+b.total+'</b>');
                });
                bparts.push('Meninggal: <b>'+(row.meninggal||0)+'</b>');
                bparts.push('CFR: <b>'+(row.cfr?row.cfr+'%':'0%')+'</b>');
                $('#kpi-'+id_p+'-sub').html(bparts.join(' | '));
            } else {
                $('#kpi-'+id_p+'-sub').html('Meninggal: <b>'+(row.meninggal||0)+'</b> | CFR: <b>'+(row.cfr?row.cfr+'%':'0%')+'</b>');
            }
            $('#kpi-'+id_p+'-minggu').text('Minggu '+row.minggu_no+': '+(row.minggu_ini||0)+' kasus');
            // Breakdown per diagnosa
            if (row.breakdown && row.breakdown.length >= 1) {
                var DLABEL = {18:'GHPR',31:'Rabies',226:'Suspek Flu Burung',32:'Flu Burung Pd Manusia',222:'Suspek Lepto',24:'Leptospirosis',294:'Anthraks'};
                var bhtml = '';
                $.each(row.breakdown, function(i,b){
                    bhtml += '<span class="zoo-badge" style="background:rgba(255,255,255,0.15);margin-top:3px">'
                        +(DLABEL[b.diagnosa_no]||'Diagnosa '+b.diagnosa_no)+': <b>'+b.total+'</b>'
                        +' | Meninggal: <b>'+b.meninggal+'</b>'
                        +' | CFR: <b>'+(b.cfr||0)+'%</b></span> ';
                });
                $('#kpi-'+id_p+'-breakdown').html(bhtml);
            }
        });
    }, 'json');
}

function toggleTrendMode(){
    var mode = $('#f_trend_mode').val();
    if(mode === 'bulan'){
        $('#f_trend_bulan').show();
    } else {
        $('#f_trend_bulan').hide().val('0');
    }
}

function loadTrend() {
    var raw = $('#f_trend_p').val();
    var parts = raw.split('_');
    var id_p = parts[0];
    var dn = parts.length > 1 ? parts[1] : 0;
    var tahun = $('#f_trend_tahun').val();
    var mode = $('#f_trend_mode').val() || 'minggu';
    var bulan = $('#f_trend_bulan').val() || 0;
    var id_prop = $('#f_prop').val();
    var id_kota = $('#f_kota').val() || 0;
    // Tampilkan info wilayah aktif
    var wilayah_label = '';
    var sel_kota = $('#f_kota option:selected').text();
    var sel_prop = $('#f_prop option:selected').text();
    if(id_kota && id_kota!='0' && sel_kota.indexOf('Pilih')<0) wilayah_label = sel_kota;
    else if(id_prop && id_prop!='0' && sel_prop.indexOf('Pilih')<0) wilayah_label = sel_prop;
    if(wilayah_label){ $('#trend-wilayah-info').show(); $('#trend-wilayah-label').text('Wilayah: '+wilayah_label); }
    else { $('#trend-wilayah-info').hide(); }
    $.get(BASE+'zoonosis/get_trend', {id_penyakit:id_p,diagnosa_no:dn,tahun:tahun,mode:mode,bulan:bulan,id_prop:id_prop,id_kota:id_kota}, function(rows) {
        var labels = [], tot = [], kon = [], mati = [];
        $.each(rows, function(i,r) {
            var mode_aktif = $('#f_trend_mode').val() || 'minggu';
            labels.push(mode_aktif==='bulan' ? (r.bulan_label||'Bln '+r.bulan) : 'M'+r.minggu);
            tot.push(r.total || 0);
            kon.push(r.konfirmasi || 0);
            mati.push(r.meninggal || 0);
        });
        var ctx = document.getElementById('chartTrend').getContext('2d');
        if (chartTrend) chartTrend.destroy();
        chartTrend = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {label:'Total',type:'bar',data:tot,backgroundColor:'rgba(52,152,219,0.6)',borderColor:'#2980b9',borderWidth:1},
                    {label:'Konfirmasi',type:'bar',data:kon,backgroundColor:'rgba(231,76,60,0.6)',borderColor:'#c0392b',borderWidth:1},
                    {label:'Meninggal',type:'line',data:mati,borderColor:'#e74c3c',backgroundColor:'rgba(231,76,60,0.15)',borderWidth:2,pointBackgroundColor:'#e74c3c',pointRadius:4,pointHoverRadius:6,fill:true}
                ]
            },
            options: {
                responsive:true,
                scales:{yAxes:[{ticks:{beginAtZero:true,precision:0}}]},
                legend:{position:'bottom',labels:{boxWidth:12,fontSize:11}}
            }
        });
    }, 'json');
}

var _mapZoo = null;
function onMapLevelChange() {
    var lv = parseInt($('#f_map_level').val());
    $('#f_map_prop').toggle(lv >= 2);
    $('#f_map_kota').toggle(lv >= 3);
}
function loadMapKota() {
    var id = $('#f_map_prop').val();
    $('#f_map_kota').html('<option value="0">-- Pilih Kab/Kota --</option>');
    if (!id || id==0) return;
    $.get(BASE+'zoonosis/get_kota/'+id, function(rows) {
        $.each(rows, function(i,r){ $('#f_map_kota').append('<option value="'+r.id+'">'+r.kota+'</option>'); });
    },'json');
}
function loadAlertOverlay(map_obj) {
    if (!map_obj) return;
    var raw_map = $("#f_map_penyakit").val(); var parts_map = raw_map.split("_"); var id_p = parts_map[0]; var dn_map = parts_map.length>1?parts_map[1]:0;
    var id_prop = $("#f_prop").val() || 0;
    $.get(BASE+'zoonosis/get_alert_ebs', {id_penyakit:id_p, id_prop:id_prop, window_jam:72}, function(rows) {
        if (!rows || !rows.length) return;
        $.each(rows, function(i, r) {
            if (!r.lat || !r.lng) return;
            var m = L.circleMarker([parseFloat(r.lat), parseFloat(r.lng)], {
                radius: 10, color:'#f39c12', fillColor:'#f1c40f',
                fillOpacity:0.95, weight:3
            }).addTo(map_obj);
            m.bindPopup('<b style="color:#e74c3c"><i class="fa fa-bell"></i> ALERT EBS AKTIF</b><br>'
                +'<b>No PE:</b> '+r.no_pe+'<br>'
                +'<b>No EBS:</b> '+r.no_ebs+'<br>'
                +'<b>Penyakit:</b> '+(r.nama_penyakit||'-')+'<br>'
                +'<b>Wilayah:</b> '+(r.propinsi||'-')+' / '+(r.kota||'-')+'<br>'
                +'<b>EBS dibuat:</b> '+r.ebs_create_date+' ('+r.jam_lalu+' jam lalu)<br>'
                +'<a href="'+BASE+'zoonosis/detail/'+r.id+'" target="_blank" class="btn btn-xs btn-danger">Detail PE</a>');
        });
    }, 'json');
}

function loadMap() {
    var raw_map = $("#f_map_penyakit").val();
    var parts_map = raw_map.split("_");
    var id_p = parts_map[0];
    var dn_map = parts_map.length > 1 ? parts_map[1] : 0;
    var tgl1 = $("#f_tgl1").val();
    var tgl2 = $("#f_tgl2").val();
    var id_prop = $("#f_prop").val() || 0;

    var map_level = parseInt($('#f_map_level').val()) || 1;
    var map_prop  = $('#f_map_prop').val() || id_prop;
    var map_kota  = $('#f_map_kota').val() || 0;
    $.ajax({url:BASE+"zoonosis/get_map_data", data:{id_penyakit:id_p,diagnosa_no:dn_map||0,tgl1:tgl1,tgl2:tgl2,level:map_level,id_prop:map_prop,id_kota:map_kota}, dataType:"json",
    success:function(data){
        // Level kecamatan/unit pelapor - bubble marker
        if (map_level == 3 || map_level == 4) {
            if (_mapZoo) { _mapZoo.remove(); _mapZoo = null; }
            _mapZoo = L.map("map-zoo", {scrollWheelZoom:false}).setView([-2.5, 118], 4);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution:"(c) OSM", maxZoom:15, opacity:0.4
            }).addTo(_mapZoo);
            var maxV = 0;
            data.forEach(function(r){ if(r.n > maxV) maxV = r.n; });
            var markers = [];
            data.forEach(function(r) {
                if (!r.lat || !r.lng) return;
                var rad = r.n > 0 ? Math.max(5, Math.min(25, r.n/Math.max(maxV,1)*25)) : 4;
                var m = L.circleMarker([r.lat, r.lng], {
                    radius: rad,
                    fillColor: r.n > 0 ? "#800026" : "#bdbdbd",
                    color: "#fff", weight: 1, fillOpacity: 0.85
                }).bindTooltip("<b>"+r.nama+"</b><br>"+r.n+" kasus", {sticky:true});
                m.addTo(_mapZoo);
                markers.push(m);
            });
            if (markers.length) {
                var grp = L.featureGroup(markers);
                try { _mapZoo.fitBounds(grp.getBounds(), {padding:[10,10]}); } catch(e) {}
            loadAlertOverlay(_mapZoo);
            }
            // Top 10 horizontal bar chart
            var sorted = data.slice().sort(function(a,b){return b.n-a.n;}).slice(0,10);
            var maxN = sorted.length > 0 ? sorted[0].n : 1;
            var tbl = '<table class="table table-condensed table-bordered" style="font-size:11px;margin:0"><thead><tr style="background:#2c3e50;color:#fff"><th>#</th><th>Wilayah</th><th>Kasus</th></tr></thead><tbody>';
            var bars = '<div><div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;font-weight:bold;font-size:11px;background:#2c3e50;color:#fff;padding:4px 6px;border-radius:3px"><div style="width:130px">Wilayah</div><div style="flex:1"></div><div style="width:40px;text-align:right">Kasus</div></div>';
            sorted.forEach(function(d,i){
                if(d.n<=0) return;
                var pct = Math.round(d.n/maxN*100);
                var clr = d.n>5000?'#800026':d.n>3000?'#BD0026':d.n>1000?'#E31A1C':d.n>300?'#FC4E2A':'#FD8D3C';
                tbl += '<tr><td>'+(i+1)+'</td><td>'+d.nama+'</td><td><b>'+d.n+'</b></td></tr>';
                bars += '<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px">'
                    + '<div style="width:130px;font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="'+d.nama+'">'+d.nama+'</div>'
                    + '<div style="flex:1;background:#eee;border-radius:3px;height:14px">'
                    + '<div style="width:'+pct+'%;background:'+clr+';height:100%;border-radius:3px"></div></div>'
                    + '<div style="width:40px;text-align:right;font-size:11px;font-weight:bold">'+d.n+'</div>'
                    + '</div>';
            });
            tbl += '</tbody></table>';
            bars += '</div>';
            var html = '<div class="row"><div class="col-sm-5">'+tbl+'</div><div class="col-sm-7" style="padding-top:4px">'+bars+'</div></div>';
            $("#map-zoo-legend").html(html);
            return; // stop - jangan lanjut ke choropleth
        }
        if (_mapZoo) { try { _mapZoo.off(); _mapZoo.remove(); } catch(e){} _mapZoo = null; }
        _mapZoo = L.map("map-zoo", {scrollWheelZoom:false}).setView([-2.5, 118], 4);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:"(c) OSM", maxZoom:10, opacity:0.4
        }).addTo(_mapZoo);

        var lookup = {}, maxV = 0;
        $.each(data, function(k, v) {
            var nm = (v.nama||k).toUpperCase()
                .replace(/^KAB\. /,"").replace(/^KABUPATEN /,"")
                .replace(/PROP\. |PROVINSI |^DI |^DKI /g,"").trim();
            lookup[nm] = v.n;
            if (v.n > maxV) maxV = v.n;
        });
        var _nameMap = {"IRIAN JAYA TIMUR":"PAPUA","IRIAN JAYA BARAT":"PAPUA BARAT","DKI JAKARTA":"JAKARTA","D.I. ACEH":"ACEH","BANGKA-BELITUNG":"BANGKA BELITUNG","NUSATENGGARA BARAT":"NUSA TENGGARA BARAT","NUSATENGGARA TIMUR":"NUSA TENGGARA TIMUR"};
        function getClr(n) {
            if (!n||n==0) return "#f5f5f5";
            if (n>5000) return "#800026";
            if (n>3000) return "#BD0026";
            if (n>1000) return "#E31A1C";
            if (n>300)  return "#FC4E2A";
            return "#FD8D3C";
        }
        function matchJml(nm) {
            var k=nm.toUpperCase().trim();
            if (_nameMap[k]) k=_nameMap[k];
            k=k.replace(/PROP\. |PROVINSI |^DI |^DKI /g,"").trim();
            var best=0;
            Object.keys(lookup).forEach(function(lk){
                var lk2=lk.replace(/^DI |^DKI /g,"").trim();
                if (k==lk2||k==lk||k.indexOf(lk2)>=0||lk2.indexOf(k)>=0) best=lookup[lk];
            });
            return best;
        }
        var geoLv = map_level==1 ? 0 : (map_level==3 ? 2 : 1);
        var geoId = map_level==3 ? map_kota : (map_level==2 ? map_prop : 0);
        fetch(BASE+"buletin/geojson?level="+geoLv+"&id_prop="+map_prop+"&id_kota="+geoId)
        .then(function(r){return r.json();})
        .then(function(geo){
            var gl=L.geoJSON(geo,{
                style:function(f){var nm=f.properties.nama||"";return{fillColor:getClr(matchJml(nm)),weight:1,color:"#fff",fillOpacity:0.8};},
                onEachFeature:function(f,layer){
                    var nm=f.properties.nama||"";
                    layer.bindTooltip("<strong>"+nm+"</strong><br>"+matchJml(nm)+" kasus",{sticky:true});
                    layer.on("mouseover",function(e){e.target.setStyle({weight:2,color:"#333"});});
                    layer.on("mouseout",function(e){e.target.setStyle({weight:1,color:"#fff"});});
                }
            }).addTo(_mapZoo);
            try{if(gl.getBounds().isValid())_mapZoo.fitBounds(gl.getBounds(),{padding:[5,5]});}catch(e){}
            loadAlertOverlay(_mapZoo);
            var legC=L.control({position:"bottomright"});
            legC.onAdd=function(){
                var d=L.DomUtil.create("div","map-legend");
                [["#800026","> 5.000"],["#BD0026","3.001 - 5.000"],["#E31A1C","1.001 - 3.000"],["#FC4E2A","301 - 1.000"],["#FD8D3C","1 - 300"],["#f5f5f5","Tidak Ada"]].forEach(function(c){
                    d.innerHTML+="<i style='background:"+c[0]+"'></i>"+c[1]+"<br>";
                });
                return d;
            };
            legC.addTo(_mapZoo);
            var sorted=Object.keys(data).map(function(k){return{nm:data[k].nama||k,n:data[k].n};}).sort(function(a,b){return b.n-a.n;}).slice(0,10);
            var maxN2=sorted.length>0?sorted[0].n:1;
            var tbl2='<table class="table table-condensed table-bordered" style="font-size:11px;margin:0"><thead><tr style="background:#2c3e50;color:#fff"><th>#</th><th>Wilayah</th><th>Kasus</th></tr></thead><tbody>';
            var bars2='<div><div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;font-weight:bold;font-size:11px;background:#2c3e50;color:#fff;padding:4px 6px;border-radius:3px"><div style="width:130px">Wilayah</div><div style="flex:1"></div><div style="width:40px;text-align:right">Kasus</div></div>';
            sorted.forEach(function(d,i){
                if(d.n<=0) return;
                var pct=Math.round(d.n/maxN2*100);
                var clr=d.n>5000?'#800026':d.n>3000?'#BD0026':d.n>1000?'#E31A1C':d.n>300?'#FC4E2A':'#FD8D3C';
                tbl2+='<tr><td>'+(i+1)+'</td><td>'+d.nm+'</td><td><b>'+d.n+'</b></td></tr>';
                bars2+='<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px">'
                    +'<div style="width:130px;font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="'+d.nm+'">'+d.nm+'</div>'
                    +'<div style="flex:1;background:#eee;border-radius:3px;height:14px">'
                    +'<div style="width:'+pct+'%;background:'+clr+';height:100%;border-radius:3px"></div></div>'
                    +'<div style="width:40px;text-align:right;font-size:11px;font-weight:bold">'+d.n+'</div>'
                    +'</div>';
            });
            tbl2+='</tbody></table>';
            bars2+='</div>';
            var html2='<div class="row"><div class="col-sm-5">'+tbl2+'</div><div class="col-sm-7" style="padding-top:4px">'+bars2+'</div></div>';
            $("#map-zoo-legend").html(html2);
        });
    },
    error:function(xhr,st,err){ console.log("MapData error:",st,err,xhr.responseText.substr(0,200)); }
    });
}

function exportDashboard(fmt) {
    var tgl1   = $('#f_tgl1').val();
    var tgl2   = $('#f_tgl2').val();
    var id_prop = $('#f_prop').val() || 0;
    var id_kota = $('#f_kota').val() || 0;
    var id_kec  = $('#f_kec').val()  || 0;
    var id_pusk = $('#f_pusk').val() || 0;
    var id_penyakit = $('#f_penyakit').val() || 0;
    var url = BASE + 'zoonosis/export_dashboard_' + fmt
        + '?tgl1=' + tgl1 + '&tgl2=' + tgl2
        + '&id_prop=' + id_prop + '&id_kota=' + id_kota
        + '&id_kec=' + id_kec + '&id_pusk=' + id_pusk
        + '&id_penyakit=' + id_penyakit;
    window.open(url, '_blank');
}

$(function() {
    loadDashboard();
    loadTrend();
    $('#f_trend_p, #f_trend_tahun').change(loadTrend);
    // Default: tampilkan peta GHPR level provinsi
    $('#f_map_penyakit').val('8');
    $('#f_map_level').val('1');
    loadMap();
});
// Sinkron filter penyakit utama ke trend dan peta (bind langsung)
function syncPenyakit(){
    var val = $('#f_penyakit').val() || '0';
    var target = val === '0' ? '8' : val;
    if($('#f_trend_p option[value="'+target+'"]').length){
        $('#f_trend_p').val(target);
    } else {
        $('#f_trend_p').val(target.split('_')[0]);
    }
    if($('#f_map_penyakit option[value="'+target+'"]').length){
        $('#f_map_penyakit').val(target);
    } else {
        $('#f_map_penyakit').val(target.split('_')[0]);
    }
    // Auto reload trend dan peta
    loadTrend();
    loadMap();
}
</script>