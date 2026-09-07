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
        <div class="col-sm-3">
          <label style="font-size:0.82em;margin-bottom:3px">Dari Tanggal</label>
          <input type="date" id="f_tgl1" class="form-control input-sm"
            value="<?=date('Y-m-d', strtotime('-30 days'))?>">
        </div>
        <div class="col-sm-3">
          <label style="font-size:0.82em;margin-bottom:3px">Sampai Tanggal</label>
          <input type="date" id="f_tgl2" class="form-control input-sm" value="<?=date('Y-m-d')?>">
        </div>
        <div class="col-sm-3">
          <label style="font-size:0.82em;margin-bottom:3px">Provinsi</label>
          <select id="f_prop" class="form-control input-sm">
            <option value="0">-- Semua Provinsi --</option>
            <?php foreach($list_prop as $pr): ?>
            <option value="<?=$pr['id']?>"><?=htmlspecialchars($pr['propinsi'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-3">
          <label style="font-size:0.82em;margin-bottom:3px">&nbsp;</label><br>
          <button class="btn btn-primary btn-zoo" onclick="loadDashboard()">
            <i class="fa fa-search"></i> Tampilkan
          </button>
          <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default btn-zoo">
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
          <div class="zoo-sub">Konfirmasi: <b id="kpi-8-kon">-</b> &bull; Meninggal: <b id="kpi-8-mati">-</b></div>
          <i class="fa fa-paw zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-avian">
          <div class="zoo-num" id="kpi-11-total">-</div>
          <div class="zoo-label">Suspek Flu Burung Pada Manusia</div>
          <div class="zoo-sub">Konfirmasi: <b id="kpi-11-kon">-</b> &bull; Meninggal: <b id="kpi-11-mati">-</b></div>
          <i class="fa fa-dove zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-anthrax">
          <div class="zoo-num" id="kpi-14-total">-</div>
          <div class="zoo-label">Suspek Antrax</div>
          <div class="zoo-sub">Konfirmasi: <b id="kpi-14-kon">-</b> &bull; Meninggal: <b id="kpi-14-mati">-</b></div>
          <i class="fa fa-biohazard zoo-icon"></i>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="zoo-card card-lepto">
          <div class="zoo-num" id="kpi-26-total">-</div>
          <div class="zoo-label">Suspek Leptospirosis</div>
          <div class="zoo-sub">Konfirmasi: <b id="kpi-26-kon">-</b> &bull; Meninggal: <b id="kpi-26-mati">-</b></div>
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

function loadDashboard() {
    var tgl1 = $('#f_tgl1').val();
    var tgl2 = $('#f_tgl2').val();
    var id_prop = $('#f_prop').val();
    $.get(BASE+'zoonosis/get_dashboard_data', {tgl1:tgl1,tgl2:tgl2,id_prop:id_prop}, function(d) {
        $.each(d, function(id_p, row) {
            $('#kpi-'+id_p+'-total').text(row.total || 0);
            $('#kpi-'+id_p+'-kon').text(row.konfirmasi || 0);
            $('#kpi-'+id_p+'-mati').text(row.meninggal || 0);
        });
    }, 'json');
}

function loadTrend() {
    var id_p  = $('#f_trend_p').val();
    var tahun = $('#f_trend_tahun').val();
    var id_prop = $('#f_prop').val();
    $.get(BASE+'zoonosis/get_trend', {id_penyakit:id_p,tahun:tahun,id_prop:id_prop}, function(rows) {
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