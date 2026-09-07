<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-bar-chart"></i> Analisa Kasus Zoonosis</h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li class="active">Analisa</li>
    </ol>
  </section>
  <section class="content">

    <div class="filter-bar">
      <div class="row">
        <div class="col-sm-3">
          <label style="font-size:0.82em;margin-bottom:3px">Dari Tanggal</label>
          <input type="date" id="f_tgl1" class="form-control input-sm" value="<?=date('Y-01-01')?>">
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
          <button class="btn btn-primary btn-sm" onclick="loadAnalisa()">
            <i class="fa fa-search"></i> Tampilkan
          </button>
        </div>
      </div>
    </div>

    <!-- Tab per penyakit -->
    <div class="tab-penyakit">
      <ul class="nav nav-tabs" id="tabPenyakit">
        <?php $first=true; foreach($penyakit as $id_p => $info): ?>
        <li class="<?=$first?'active':''?>">
          <a href="#tab-<?=$id_p?>" data-toggle="tab" data-id="<?=$id_p?>">
            <i class="fa fa-bug"></i> <?=htmlspecialchars($info['singkat'])?>
          </a>
        </li>
        <?php $first=false; endforeach; ?>
      </ul>

      <div class="tab-content" style="background:#fff;border:1px solid #ddd;border-top:none;padding:16px;border-radius:0 0 6px 6px">
        <?php $first=true; foreach($penyakit as $id_p => $info): ?>
        <div class="tab-pane <?=$first?'active':''?>" id="tab-<?=$id_p?>">
          <div class="row">
            <!-- Grafik status -->
            <div class="col-sm-4">
              <h5 style="font-size:0.9em;font-weight:700;color:#2c3e50">Status Kasus</h5>
              <canvas id="chartStatus<?=$id_p?>" height="200"></canvas>
            </div>
            <!-- Tabel per provinsi -->
            <div class="col-sm-8">
              <h5 style="font-size:0.9em;font-weight:700;color:#2c3e50">Distribusi per Provinsi</h5>
              <div class="table-responsive" style="max-height:320px;overflow-y:auto">
                <table class="table table-bordered table-condensed tbl-prop">
                  <thead style="background:#2c3e50;color:#fff;position:sticky;top:0">
                    <tr><th>Provinsi</th><th>Total</th><th>Konfirmasi</th><th>Meninggal</th><th>Lab</th><th>Distribusi</th></tr>
                  </thead>
                  <tbody id="tbody-prop-<?=$id_p?>">
                    <tr><td colspan="6" class="text-center text-muted">Klik Tampilkan</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php $first=false; endforeach; ?>
      </div>
    </div>

  </section>
</div>
</div>

<script src="<?=base_url('assets/plugins/chartjs/Chart.min.js')?>"></script>
<script>
var BASE = '<?=base_url()?>';
var charts = {};
var STATUS_LABELS = ['Suspek','Probable','Konfirmasi','Discarded'];
var STATUS_COLORS = ['#f39c12','#8e44ad','#27ae60','#95a5a6'];

function loadAnalisa() {
    var tgl1   = $('#f_tgl1').val();
    var tgl2   = $('#f_tgl2').val();
    var id_prop = $('#f_prop').val();
    var ids = [<?php echo implode(',', array_keys($penyakit))?>];
    $.each(ids, function(i, id_p) {
        // Status breakdown (pie)
        $.get(BASE+'zoonosis/get_analisa_status', {id_penyakit:id_p, tgl1:tgl1, tgl2:tgl2, id_prop:id_prop}, function(rows) {
            var vals = [0,0,0,0];
            $.each(rows, function(i,r) { vals[parseInt(r.status_kasus)] = parseInt(r.total); });
            var ctx = document.getElementById('chartStatus'+id_p).getContext('2d');
            if (charts['s'+id_p]) charts['s'+id_p].destroy();
            charts['s'+id_p] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: STATUS_LABELS,
                    datasets: [{data: vals, backgroundColor: STATUS_COLORS, borderWidth:1}]
                },
                options: {
                    responsive:true,
                    legend:{position:'bottom',labels:{boxWidth:12,fontSize:11}},
                    cutoutPercentage:55
                }
            });
        },'json');

        // Per provinsi (tabel)
        $.get(BASE+'zoonosis/get_analisa_prop', {id_penyakit:id_p, tgl1:tgl1, tgl2:tgl2}, function(rows) {
            var maxTotal = 0;
            $.each(rows, function(i,r){ if (parseInt(r.total)>maxTotal) maxTotal=parseInt(r.total); });
            var html = '';
            if (!rows.length) {
                html = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>';
            } else {
                $.each(rows, function(i,r) {
                    var tot = parseInt(r.total)||0;
                    var pct = maxTotal ? Math.round(tot/maxTotal*100) : 0;
                    var kon = parseInt(r.konfirmasi)||0;
                    var mat = parseInt(r.meninggal)||0;
                    var lab = parseInt(r.diperiksa_lab)||0;
                    html += '<tr>'
                        +'<td>'+r.propinsi+'</td>'
                        +'<td class="text-center"><b>'+tot+'</b></td>'
                        +'<td class="text-center"><span class="text-success">'+kon+'</span></td>'
                        +'<td class="text-center">'+(mat>0?'<span class="text-danger"><b>'+mat+'</b></span>':0)+'</td>'
                        +'<td class="text-center">'+lab+'</td>'
                        +'<td><span class="bar-prop" style="width:'+pct+'%"></span> '+pct+'%</td>'
                        +'</tr>';
                });
            }
            $('#tbody-prop-'+id_p).html(html);
        },'json');
    });
}

$(function() { loadAnalisa(); });
</script>