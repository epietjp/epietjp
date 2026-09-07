<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-file-text-o"></i> <?=htmlspecialchars($title)?></h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li><a href="<?=site_url('zoonosis/daftar')?>">Daftar PE</a></li>
      <li class="active">Detail</li>
    </ol>
  </section>
  <section class="content">

<?php
$id_p  = $pe['id_penyakit'];
$warna = array(8=>'#e74c3c',11=>'#e67e22',14=>'#2c3e50',26=>'#2980b9');
$hex   = isset($warna[$id_p]) ? $warna[$id_p] : '#2980b9';
$STATUS = array(0=>'Suspek',1=>'Probable',2=>'Konfirmasi',3=>'Discarded');
$AKHIR  = array(1=>'Sembuh',2=>'Meninggal',3=>'Dalam Perawatan');
$p_nama = isset($penyakit[$id_p]) ? $penyakit[$id_p]['nama'] : 'Zoonosis';
?>
    <div class="pe-header" style="background:<?=$hex?>">
      <div class="row">
        <div class="col-sm-8">
          <h4 style="margin:0 0 4px"><i class="fa fa-bug"></i> <?=htmlspecialchars($p_nama)?></h4>
          <div style="font-size:1.3em;font-weight:700"><?=htmlspecialchars($pe['nama_pasien'])?></div>
          <div style="opacity:.85;font-size:0.88em;margin-top:4px">
            No PE: <b><?=htmlspecialchars($pe['no_pe'])?></b> &nbsp;|&nbsp;
            <?=htmlspecialchars($pe['propinsi'])?> / <?=htmlspecialchars($pe['kota'])?>
          </div>
        </div>
        <div class="col-sm-4 text-right">
          <?php $sk = (int)$pe['status_kasus']; ?>
          <div style="font-size:1.8em;font-weight:700;opacity:.9">
            <?=isset($STATUS[$sk]) ? $STATUS[$sk] : '-'?>
          </div>
          <?php if($pe['akhir_no']==2): ?>
          <div style="background:rgba(0,0,0,.3);display:inline-block;padding:3px 10px;border-radius:12px;font-size:0.85em">
            <i class="fa fa-exclamation-triangle"></i> MENINGGAL
          </div>
          <?php endif; ?>
          <div style="margin-top:8px">
            <a href="<?=site_url('zoonosis/edit/'.$pe['id'])?>" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4)">
              <i class="fa fa-pencil"></i> Edit
            </a>
            <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)">
              <i class="fa fa-arrow-left"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="section-box">
          <div class="section-box-title"><i class="fa fa-user"></i> Identitas Pasien</div>
          <dl class="dl-pe dl-horizontal">
            <dt>Nama Orang Tua/KK</dt><dd><?=htmlspecialchars($pe['nama_kk'] ?: '-')?></dd>
            <dt>NIK</dt><dd><?=htmlspecialchars($pe['nik'] ?: '-')?></dd>
            <dt>Jenis Kelamin</dt><dd><?=$pe['kelamin']=='L'?'Laki-laki':'Perempuan'?></dd>
            <dt>Umur</dt><dd><?=$pe['umur_thn']?> thn <?=$pe['umur_bln']?> bln</dd>
            <dt>Pekerjaan</dt><dd><?=htmlspecialchars($pe['pekerjaan'] ?: '-')?></dd>
            <dt>Tlp/HP Pasien</dt><dd><?=htmlspecialchars($pe['telp_pasien'] ?: '-')?></dd>
            <dt>Alamat</dt><dd><?=htmlspecialchars($pe['alamat'] ?: '-')?></dd>
            <dt>Kelurahan</dt><dd><?=htmlspecialchars($pe['kelurahan'] ?: '-')?></dd>
            <dt>Kecamatan</dt><dd><?=htmlspecialchars($pe['kecamatan'] ?: '-')?></dd>
            <dt>Kab/Kota Domisili</dt><dd><?php
              if(!empty($pe['kd_kota_kasus'])) {
                $kota_dom = $this->db->query('SELECT kota FROM ewarn_kota WHERE id='.(int)$pe['kd_kota_kasus'])->row_array();
                echo htmlspecialchars(isset($kota_dom['kota']) ? $kota_dom['kota'] : '-');
              } else { echo htmlspecialchars(isset($pe['kota']) ? $pe['kota'] : '-'); }
            ?></dd>
            <dt>Provinsi Domisili</dt><dd><?php
              if(!empty($pe['kd_prop_kasus'])) {
                $prop_dom = $this->db->query('SELECT propinsi FROM ewarn_propinsi WHERE id='.(int)$pe['kd_prop_kasus'])->row_array();
                echo htmlspecialchars(isset($prop_dom['propinsi']) ? $prop_dom['propinsi'] : '-');
              } else { echo htmlspecialchars(isset($pe['propinsi']) ? $pe['propinsi'] : '-'); }
            ?></dd>
            <dt>Alamat Tempat Kerja</dt><dd><?=htmlspecialchars($pe['alamat_kerja'] ?: '-')?></dd>
            <dt>Saudara Dekat</dt><dd><?=htmlspecialchars($pe['kontak_darurat'] ?: '-')?></dd>
          </dl>
        </div>
        <div class="section-box">
          <div class="section-box-title"><i class="fa fa-heartbeat"></i> Klinis</div>
          <dl class="dl-pe dl-horizontal">
            <dt>Tgl Bergejala</dt><dd><?=htmlspecialchars($pe['tgl_bergejala'] ?: '-')?></dd>
            <dt>Tgl Sakit</dt><dd><?=htmlspecialchars($pe['tgl_sakit'] ?: '-')?></dd>
            <dt>Gejala</dt><dd><?=htmlspecialchars($pe['gejala'] ?: '-')?></dd>
            <dt>Status Kasus</dt>
            <dd><b><?=isset($STATUS[$pe['status_kasus']]) ? $STATUS[$pe['status_kasus']] : '-'?></b></dd>
            <dt>Kondisi Akhir</dt>
            <dd><?=isset($AKHIR[$pe['akhir_no']]) ? $AKHIR[$pe['akhir_no']] : '-'?></dd>
            <dt>Tgl Meninggal</dt><dd><?=htmlspecialchars($pe['tgl_meninggal'] ?: '-')?></dd>
          </dl>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="section-box">
          <div class="section-box-title"><i class="fa fa-user-md"></i> Pelaporan &amp; PE</div>
          <dl class="dl-pe dl-horizontal">
            <dt>No PE</dt><dd><?=htmlspecialchars($pe['no_pe'] ?: '-')?></dd>
            <dt>No EBS</dt><dd>
              <?php if(!empty($pe['no_ebs'])): ?>
                <span class="label label-success"><?=htmlspecialchars($pe['no_ebs'])?></span>
              <?php else: ?>
                <span class="text-muted">-</span>
                <button type="button" id="btn-buat-ebs" class="btn btn-xs btn-info" style="margin-left:8px"
                  data-id="<?=$pe['id']?>" onclick="buatEBS(this)">
                  <i class="fa fa-plus-circle"></i> Buat EBS dari PE ini
                </button>
              <?php endif; ?>
            </dd>
            <dt>Unit Pelapor</dt><dd><?=htmlspecialchars($pe['unit_pelapor'] ?: '-')?></dd>
            <dt>Kab/Kota</dt><dd><?=htmlspecialchars($pe['kota'] ?: '-')?></dd>
            <dt>Provinsi</dt><dd><?=htmlspecialchars($pe['propinsi'] ?: '-')?></dd>
            <dt>Tgl Laporan</dt><dd><?=htmlspecialchars($pe['tgl_laporan'] ?: '-')?></dd>
            <dt>Tgl PE</dt><dd><?=htmlspecialchars($pe['tgl_pe'] ?: '-')?></dd>
            <dt>Petugas PE</dt><dd><?=htmlspecialchars($pe['nama_petugas'] ?: '-')?>
              <?php if($pe['telp_petugas']): ?> <small class="text-muted">(<?=htmlspecialchars($pe['telp_petugas'])?>)</small><?php endif; ?>
            </dd>
          </dl>
        </div>
        <div class="section-box">
          <div class="section-box-title"><i class="fa fa-paw"></i> Riwayat Hewan &amp; Vaksinasi</div>
          <dl class="dl-pe dl-horizontal">
            <dt>Kontak Hewan</dt>
            <dd><?=$pe['riwayat_kontak_hewan']===NULL?'-':($pe['riwayat_kontak_hewan']?'Ya':'Tidak')?></dd>
            <dt>Jenis Hewan</dt><dd><?=htmlspecialchars($pe['jenis_hewan'] ?: '-')?></dd>
            <dt>Tgl Kontak</dt><dd><?=htmlspecialchars($pe['tgl_kontak'] ?: '-')?></dd>
            <dt>Lokasi Kontak</dt><dd><?=htmlspecialchars($pe['lokasi_kontak'] ?: '-')?></dd>
            <dt>Vaksinasi</dt>
            <dd><?=$pe['riwayat_vaksinasi']===NULL?'-':($pe['riwayat_vaksinasi']?'Ya':'Tidak')?></dd>
            <dt>Jenis Vaksin</dt><dd><?=htmlspecialchars($pe['jenis_vaksin'] ?: '-')?></dd>
            <?php if($id_p==11): ?>
            <dt>Oseltamivir</dt>
            <dd><?=$pe['oseltamivir']===NULL?'-':($pe['oseltamivir']?'Ya - '.$pe['tgl_oseltamivir']:'Tidak')?></dd>
            <?php endif; ?>
          </dl>
        </div>
        <div class="section-box">
          <div class="section-box-title"><i class="fa fa-flask"></i> Laboratorium</div>
          <dl class="dl-pe dl-horizontal">
            <dt>Diperiksa Lab</dt><dd><?=$pe['diperiksa_lab']?'Ya':'Tidak'?></dd>
            <dt>Jenis Spesimen</dt><dd><?=htmlspecialchars($pe['jenis_sample'] ?: '-')?></dd>
            <dt>Tgl Ambil</dt><dd><?=htmlspecialchars($pe['tgl_ambil_sample'] ?: '-')?></dd>
            <dt>Tgl Kirim</dt><dd><?=htmlspecialchars($pe['tgl_kirim_sample'] ?: '-')?></dd>
            <dt>Nama Lab</dt><dd><?=htmlspecialchars($pe['nama_lab'] ?: '-')?></dd>
            <dt>Hasil Lab</dt><dd><b><?=htmlspecialchars($pe['hasil_lab'] ?: '-')?></b></dd>
            <dt>Ket Lab</dt><dd><?=htmlspecialchars($pe['ket_lab'] ?: '-')?></dd>
            <dt>Nama RS/Klinik</dt><dd><?=htmlspecialchars($pe['nama_rs'] ?: '-')?></dd>
            <dt>Tgl Masuk RS</dt><dd><?=htmlspecialchars($pe['tgl_masuk_rs'] ?: '-')?></dd>
          </dl>
        </div>
      </div>
    </div>

    <?php if(!empty($detail)): ?>
    <div class="section-box">
      <div class="section-box-title"><i class="fa fa-list-alt"></i> Variabel Tambahan GHS</div>
      <?php
      $by_sub = array();
      foreach($detail as $d) {
          $sub = $d['submodule'] ?: 'Lainnya';
          $by_sub[$sub][] = $d;
      }
      ?>
      <?php foreach($by_sub as $sub => $rows): ?>
      <div style="margin-bottom:10px">
        <div style="font-size:0.82em;font-weight:700;color:#7f8c8d;margin-bottom:6px;text-transform:uppercase"><?=htmlspecialchars($sub)?></div>
        <table class="table table-bordered table-condensed tbl-det">
          <thead><tr><th>Variabel</th><th>Nilai</th></tr></thead>
          <tbody>
            <?php foreach($rows as $d): ?>
            <tr>
              <td><?=htmlspecialchars($d['var_label'])?></td>
              <td><?=htmlspecialchars($d['var_value'] ?: '-')?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if($pe['ket_lain']): ?>
    <div class="section-box">
      <div class="section-box-title"><i class="fa fa-sticky-note"></i> Keterangan Lain</div>
      <p style="font-size:0.9em"><?=nl2br(htmlspecialchars($pe['ket_lain']))?></p>
    </div>
    <?php endif; ?>

    <div style="margin-bottom:30px">
      <a href="<?=site_url('zoonosis/edit/'.$pe['id'])?>" class="btn btn-warning">
        <i class="fa fa-pencil"></i> Edit
      </a>
      <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </a>
      <a href="<?=site_url('zoonosis/hapus/'.$pe['id'])?>" class="btn btn-danger pull-right"
         onclick="return confirm('Hapus data PE ini secara permanen?')">
        <i class="fa fa-trash"></i> Hapus
      </a>
    </div>

  </section>
</div>
<script>
var BASE = "<?=base_url()?>";
function buatEBS(btn) {
    if (!confirm("Buat laporan EBS baru dari data PE ini?")) return;
    var id_pe = $(btn).data("id");
    $(btn).prop("disabled", true).html("<i class=\"fa fa-spinner fa-spin\"></i> Memproses...");
    $.post(BASE+"zoonosis/buat_ebs/"+id_pe, function(res) {
        if (res.status == "ok") {
            alert("Berhasil! " + res.msg);
            location.reload();
        } else if (res.status == "exists") {
            alert("PE sudah terhubung ke EBS: " + res.no_ebs);
            location.reload();
        } else {
            alert("Gagal: " + res.msg);
            $(btn).prop("disabled", false).html("<i class=\"fa fa-plus-circle\"></i> Buat EBS dari PE ini");
        }
    }, "json").fail(function(){
        alert("Error server.");
        $(btn).prop("disabled", false).html("<i class=\"fa fa-plus-circle\"></i> Buat EBS dari PE ini");
    });
}
</script>
