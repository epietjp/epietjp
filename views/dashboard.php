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
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Dari Tanggal</label>
          <input type="date" id="f_tgl1" class="form-control input-sm" value="<?=date('Y-01-01')?>">
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Sampai</label>
          <input type="date" id="f_tgl2" class="form-control input-sm" value="<?=date('Y-m-d')?>">
        </div>
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">Level</label>
          <div>
            <label class="radio-inline" style="font-size:0.85em"><input type="radio" name="f_level" value="0" checked onchange="setLevel()"> Nasional</label>
            <label class="radio-inline" style="font-size:0.85em"><input type="radio" name="f_level" value="1" onchange="setLevel()"> Provinsi</label>
            <label class="radio-inline" style="font-size:0.85em"><input type="radio" name="f_level" value="2" onchange="setLevel()"> Kab/Kota</label>
            <label class="radio-inline" style="font-size:0.85em"><input type="radio" name="f_level" value="3" onchange="setLevel()"> Kecamatan</label>
            <label class="radio-inline" style="font-size:0.85em"><input type="radio" name="f_level" value="4" onchange="setLevel()"> Unit Pelapor</label>
          </div>
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
        <div class="col-sm-2">
          <label style="font-size:0.82em;margin-bottom:3px">&nbsp;</label><br>
          <button class="btn btn-primary btn-sm" onclick="loadDashboard()">
            <i class="fa fa-search"></i> Load Data
          </button>
          <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default btn-sm">
            <i class="fa fa-list"></i> Daftar PE
          </a>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="section-title"><i class="fa fa-stethoscope"></i> Ringkasan Kasus per Penyakit</div>
    <div class="row" id="kpi-row">
      <div class="col-sm-3">
        <div class="zoo-card card-ghpr">
          <div class="zoo-num" id="kpi-8-total">-</div>
          <div class="zoo-label">Gigitan Hewan Penular Rabies (GHPR)</div>
          <div class="zoo-sub"><span class="zoo-badge">Konfirmasi: <b id="kpi-8-kon">-</b></span> <span class="zoo-badge">Meninggal: <b id="kpi-8-mati">-</b></span> <span class="zoo-badge">CFR: <b id="kpi-8-cfr">-</b></span></div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-8-minggu">-</b></div>
          <i class="fa fa-paw zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-avian">
          <div class="zoo-num" id="kpi-11-total">-</div>
          <div class="zoo-label">Suspek Flu Burung Pada Manusia</div>
          <div class="zoo-sub"><span class="zoo-badge">Konfirmasi: <b id="kpi-11-kon">-</b></span> <span class="zoo-badge">Meninggal: <b id="kpi-11-mati">-</b></span> <span class="zoo-badge">CFR: <b id="kpi-11-cfr">-</b></span></div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-11-minggu">-</b></div>
          <i class="fa fa-dove zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-anthrax">
          <div class="zoo-num" id="kpi-14-total">-</div>
          <div class="zoo-label">Suspek Antrax</div>
          <div class="zoo-sub"><span class="zoo-badge">Konfirmasi: <b id="kpi-14-kon">-</b></span> <span class="zoo-badge">Meninggal: <b id="kpi-14-mati">-</b></span> <span class="zoo-badge">CFR: <b id="kpi-14-cfr">-</b></span></div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-14-minggu">-</b></div>
          <i class="fa fa-biohazard zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-lepto">
          <div class="zoo-num" id="kpi-26-total">-</div>
          <div class="zoo-label">Suspek Leptospirosis</div>
          <div class="zoo-sub"><span class="zoo-badge">Konfirmasi: <b id="kpi-26-kon">-</b></span> <span class="zoo-badge">Meninggal: <b id="kpi-26-mati">-</b></span> <span class="zoo-badge">CFR: <b id="kpi-26-cfr">-</b></span></div>
          <div style="font-size:0.8em;opacity:0.9;margin-top:6px"><i class="fa fa-calendar-o"></i> <b id="kpi-26-minggu">-</b></div>
          <i class="fa fa-tint zoo-icon"></i>
        </div>
      </div>
    </div>

    <!-- Trend Chart -->
    <div class="row">
      <div class="col-sm-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-line-chart"></i> Trend Mingguan</h3>
            <div class="box-tools pull-right">
              <select id="f_trend_p" class="form-control input-sm" style="width:160px;display:inline-block">
                <option value="8">GHPR</option>
                <option value="11">Avian Flu</option>
                <option value="14">Anthrax</option>
                <option value="26">Leptospirosis</option>
              </select>
              <select id="f_trend_tahun" class="form-control input-sm" style="width:80px;display:inline-block">
                <?php for($y=date('Y');$y>=2020;$y--): ?>
                <option value="<?=$y?>" <?=$y==date('Y')?'selected':''?>><?=$y?></option>
                <?php endfor; ?>
              </select>
              <button class="btn btn-xs btn-default" onclick="loadTrend()"><i class="fa fa-refresh"></i></button>
            </div>
          </div>
          <div class="box-body">
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
            <a href="<?=site_url('zoonosis/analisa')?>" class="btn btn-default btn-block" style="margin-top:6px">
              <i class="fa fa-bar-chart"></i> Analisa &amp; Grafik
            </a>
          </div>
        </div>
      </div>
    </div>

  </section>
</div>
</div>

<script src="<?=base_url('assets/plugins/chartjs/Chart.min.js')?>"></script>
<script>
var BASE = '<?=base_url()?>';
var chartTrend = null;

function setLevel() {
    var lv = parseInt($('input[name=f_level]:checked').val());
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
    $.get(BASE+'zoonosis/get_dashboard_data', {tgl1:tgl1,tgl2:tgl2,id_prop:id_prop,id_kota:id_kota,id_kec:id_kec,id_pusk:id_pusk}, function(d) {
        $.each(d, function(id_p, row) {
            $('#kpi-'+id_p+'-total').text(row.total || 0);
            $('#kpi-'+id_p+'-kon').text(row.konfirmasi || 0);
            $('#kpi-'+id_p+'-mati').text(row.meninggal || 0);
            $('#kpi-'+id_p+'-cfr').text(row.cfr ? row.cfr+'%' : '0%');
            $('#kpi-'+id_p+'-minggu').text('Minggu '+row.minggu_no+': '+(row.minggu_ini||0)+' kasus');
        });
    }, 'json');
}

function loadTrend() {
    var id_p  = $('#f_trend_p').val();
    var tahun = $('#f_trend_tahun').val();
    var id_prop = $('#f_prop').val();
    var id_kota = $('#f_kota').val() || 0;
    $.get(BASE+'zoonosis/get_trend', {id_penyakit:id_p,tahun:tahun,id_prop:id_prop,id_kota:id_kota}, function(rows) {
        var labels = [], tot = [], kon = [], mati = [];
        $.each(rows, function(i,r) {
            labels.push('M'+r.minggu);
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
                    {label:'Total',data:tot,backgroundColor:'rgba(52,152,219,0.7)',borderColor:'#2980b9',borderWidth:1},
                    {label:'Konfirmasi',data:kon,backgroundColor:'rgba(231,76,60,0.7)',borderColor:'#c0392b',borderWidth:1},
                    {label:'Meninggal',data:mati,backgroundColor:'rgba(44,62,80,0.7)',borderColor:'#2c3e50',borderWidth:1}
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

$(function() {
    loadDashboard();
    loadTrend();
    $('#f_trend_p, #f_trend_tahun').change(loadTrend);
});
</script>