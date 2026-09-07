<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="fa fa-file-text"></i> <?=htmlspecialchars($title)?></h1>
    <ol class="breadcrumb">
      <li><a href="<?=base_url()?>"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?=site_url('zoonosis')?>">Zoonosis</a></li>
      <li><a href="<?=site_url('zoonosis/daftar')?>">Daftar PE</a></li>
      <li class="active">Form PE</li>
    </ol>
  </section>
  <section class="content">

<?php
$is_edit = !empty($pe) && isset($pe['id']);
$v = !empty($pe) ? $pe : array();
function fv($v,$k,$def='') { return isset($v[$k]) ? htmlspecialchars($v[$k]) : $def; }
$warna_map = array('danger'=>'#e74c3c','warning'=>'#e67e22','dark'=>'#2c3e50','info'=>'#2980b9');
$warna_hex = isset($warna_map[$info_p['warna']]) ? $warna_map[$info_p['warna']] : '#2980b9';
?>

    <div class="penyakit-badge" style="background:<?=$warna_hex?>">
      <i class="fa fa-bug"></i> <?=htmlspecialchars($info_p['nama'])?>
    </div>

    <form id="formPE">
    <input type="hidden" name="id" value="<?=$is_edit ? $pe['id'] : ''?>">
    <input type="hidden" name="id_penyakit" value="<?=$id_penyakit?>">

    <?php if(!empty($list_diagnosa)): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-stethoscope"></i> Diagnosa</div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Diagnosa <span class="req">*</span></label>
            <select name="diagnosa_no" class="form-control" required>
              <option value="">-- Pilih Diagnosa --</option>
              <?php foreach($list_diagnosa as $d): ?>
              <option value="<?=$d['id']?>" <?=fv($v,'diagnosa_no')==$d['id']?'selected':''?>>
                <?=htmlspecialchars($d['data'])?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- IDENTITAS PELAPOR -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-user-md"></i> Identitas Pelapor &amp; Laporan</div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>No PE <span class="req">*</span></label>
            <input type="text" name="no_pe" class="form-control" value="<?=fv($v,'no_pe')?>" readonly style="background:#f5f5f5" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Laporan</label>
            <input type="date" name="tgl_laporan" class="form-control" value="<?=fv($v,'tgl_laporan',date('Y-m-d'))?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal PE</label>
            <input type="date" name="tgl_pe" class="form-control" value="<?=fv($v,'tgl_pe')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Link No EBS</label>
            <select name="no_ebs" class="form-control">
              <option value="">-- Tidak Ada --</option>
              <?php foreach($list_ebs as $ebs): ?>
              <option value="<?=htmlspecialchars($ebs['no_ebs'])?>"
                <?=fv($v,'no_ebs')==$ebs['no_ebs']?'selected':''?>>
                <?=htmlspecialchars($ebs['no_ebs'])?> | <?=htmlspecialchars($ebs['tanggal'])?> | <?=htmlspecialchars($ebs['kota'])?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Petugas PE</label>
            <input type="text" name="nama_petugas" class="form-control" value="<?=fv($v,'nama_petugas')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Telp Petugas</label>
            <input type="text" name="telp_petugas" class="form-control" value="<?=fv($v,'telp_petugas')?>">
          </div>
        </div>
      </div>
    </div>

    <!-- WILAYAH -->
    <div class="form-section">
      <?php $has_wilayah = !empty($v["id_prop"]); ?>
      <div class="form-section-title"><i class="fa fa-map-marker"></i> Wilayah Unit Pelapor</div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Provinsi <span class="req">*</span></label>
            <select name="id_prop" id="sel_prop" class="form-control" required <?=$has_wilayah&&!$is_edit?"disabled":""?>>
              <option value="">-- Pilih --</option>
              <?php foreach($list_prop as $pr): ?>
              <option value="<?=$pr['id']?>" <?=fv($v,'id_prop')==$pr['id']?'selected':''?>>
                <?=htmlspecialchars($pr['propinsi'])?>
              </option>
              <?php endforeach; ?>
            </select>
            <?php if($has_wilayah && !$is_edit): ?>
            <input type="hidden" name="id_prop" value="<?=fv($v,'id_prop')?>">
            <?php endif; ?>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kab/Kota <span class="req">*</span></label>
            <select name="id_kota" id="sel_kota" class="form-control" required <?=$has_wilayah&&!$is_edit&&!empty($v["id_kota"])?"disabled":""?>>
              <option value="">-- Pilih Provinsi --</option>
            </select>
            <?php if($has_wilayah && !$is_edit && !empty($v['id_kota'])): ?>
            <input type="hidden" name="id_kota" value="<?=fv($v,'id_kota')?>">
            <?php endif; ?>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kecamatan</label>
            <select name="id_kecamatan" id="sel_kec_unit" class="form-control" <?=$has_wilayah&&!$is_edit&&!empty($v["id_kecamatan"])?"disabled":""?>>
              <option value="">-- Pilih Kab/Kota dulu --</option>
            </select>
            <?php if($has_wilayah && !$is_edit && !empty($v["id_kecamatan"])): echo "<input type=hidden name=id_kecamatan value=" . fv($v,"id_kecamatan") . ">"; endif; ?>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Unit Pelapor</label>
            <select name="id_puskesmas" id="sel_pusk" class="form-control" <?=$has_wilayah&&!$is_edit&&!empty($v["id_puskesmas"])?"disabled":""?>>
              <option value="">-- Pilih Kab/Kota --</option>
            </select>
            <?php if($has_wilayah && !$is_edit && !empty($v["id_puskesmas"])): echo "<input type=hidden name=id_puskesmas value=" . fv($v,"id_puskesmas") . ">"; endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- IDENTITAS PASIEN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-user"></i> Identitas Pasien</div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Pasien <span class="req">*</span></label>
            <input type="text" name="nama_pasien" class="form-control" value="<?=fv($v,'nama_pasien')?>" required>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Nama Orang Tua / KK</label>
            <input type="text" name="nama_kk" class="form-control" value="<?=fv($v,'nama_kk')?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" maxlength="16" value="<?=fv($v,'nik')?>">
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Jenis Kelamin</label>
            <select name="kelamin" class="form-control">
              <option value="L" <?=fv($v,'kelamin')=='L'?'selected':''?>>Laki-laki</option>
              <option value="P" <?=fv($v,'kelamin')=='P'?'selected':''?>>Perempuan</option>
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Umur (Tahun)</label>
            <input type="number" name="umur_thn" class="form-control" min="0" value="<?=fv($v,'umur_thn',0)?>">
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Umur (Bulan)</label>
            <input type="number" name="umur_bln" class="form-control" min="0" max="11" value="<?=fv($v,'umur_bln',0)?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="form-control" value="<?=fv($v,'tgl_lahir')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Pekerjaan</label>
            <select name="pekerjaan" class="form-control">
              <option value="">-- Pilih Pekerjaan --</option>
              <?php foreach(array(
                'petani'=>'Petani',
                'peternakan'=>'Peternakan/Peternak',
                'karyawan'=>'Karyawan/Pekerja Swasta',
                'ibu_rumah_tanggal'=>'Ibu Rumah Tangga',
                'tni'=>'TNI',
                'polri'=>'POLRI',
                'pelajar'=>'Pelajar/Mahasiswa',
                'tukang_ledeng'=>'Tukang/Buruh',
                'nelayan'=>'Nelayan',
                'pedagang'=>'Pedagang',
                'lainnya'=>'Lainnya',
              ) as $val=>$label): ?>
              <option value="<?=$val?>" <?=fv($v,'pekerjaan')==$val?'selected':''?>><?=$label?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Tlp/HP Pasien</label>
            <input type="text" name="telp_pasien" class="form-control" value="<?=fv($v,'telp_pasien')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" value="<?=fv($v,'alamat')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Provinsi Domisili Pasien</label>
            <select name="kd_prop_kasus" id="sel_prop_pasien" class="form-control">
              <option value="">-- Sama dengan Unit Pelapor --</option>
              <?php foreach($list_prop as $pr): ?>
              <option value="<?=$pr['id']?>" <?=fv($v,'kd_prop_kasus')==$pr['id']?'selected':''?>>
                <?=htmlspecialchars($pr['propinsi'])?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kab/Kota Domisili Pasien</label>
            <select name="kd_kota_kasus" id="sel_kota_pasien" class="form-control" onchange="loadKecamatanPasien(this.value, null)">
              <option value="">-- Pilih Provinsi dulu --</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kecamatan Domisili Pasien</label>
            <select name="id_kecamatan_kasus" id="sel_kecamatan_pasien" class="form-control">
              <option value="">-- Pilih Kab/Kota dulu --</option>
            </select>
            <input type="hidden" name="kecamatan" id="hid_kecamatan_nama">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kelurahan/Desa</label>
            <input type="text" name="kelurahan" class="form-control" value="<?=fv($v,'kelurahan')?>">
          </div>
        </div>
      </div>
    </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Alamat Tempat Kerja</label>
            <input type="text" name="alamat_kerja" class="form-control" value="<?=fv($v,'alamat_kerja')?>">
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Saudara Dekat yang Dapat Dihubungi</label>
            <input type="text" name="kontak_darurat" class="form-control" value="<?=fv($v,'kontak_darurat')?>">
          </div>
        </div>
      </div>

    <!-- KLINIS -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-heartbeat"></i> Informasi Klinis</div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Mulai Sakit/Bergejala</label>
            <input type="date" name="tgl_bergejala" class="form-control" value="<?=fv($v,'tgl_bergejala')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Sakit (Berobat)</label>
            <input type="date" name="tgl_sakit" class="form-control" value="<?=fv($v,'tgl_sakit')?>">
          </div>
        </div>
        <?php if(in_array($id_penyakit, array(14,26))): // Anthrax dan Lepto ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Pajanan/Eksposur</label>
            <input type="date" name="tgl_pajanan" class="form-control" value="<?=fv($v,'tgl_pajanan')?>">
            <small class="text-muted">Tanggal kontak/pajanan dengan sumber penularan</small>
          </div>
        </div>
        <?php endif; ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Status Kasus</label>
            <select name="status_kasus" class="form-control">
              <option value="0" <?=fv($v,'status_kasus','0')=='0'?'selected':''?>>Suspek</option>
              <option value="1" <?=fv($v,'status_kasus')=='1'?'selected':''?>>Probable</option>
              <option value="2" <?=fv($v,'status_kasus')=='2'?'selected':''?>>Konfirmasi</option>
              <option value="3" <?=fv($v,'status_kasus')=='3'?'selected':''?>>Discarded</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kondisi Akhir</label>
            <select name="akhir_no" class="form-control">
              <option value="">-- Belum Diketahui --</option>
              <option value="1" <?=fv($v,'akhir_no')=='1'?'selected':''?>>Sembuh</option>
              <option value="2" <?=fv($v,'akhir_no')=='2'?'selected':''?>>Meninggal</option>
              <option value="3" <?=fv($v,'akhir_no')=='3'?'selected':''?>>Dalam Perawatan</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Meninggal</label>
            <input type="date" name="tgl_meninggal" class="form-control" value="<?=fv($v,'tgl_meninggal')?>">
          </div>
        </div>
        <div class="col-sm-9">
          <div class="form-group">
            <label>Gejala</label>
            <input type="text" name="gejala" class="form-control" placeholder="Pisahkan dengan koma" value="<?=fv($v,'gejala')?>">
          </div>
        </div>
      </div>
    </div>

    <!-- KONTAK HEWAN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-paw"></i> Riwayat Kontak Hewan</div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Riwayat Kontak Hewan</label>
            <select name="riwayat_kontak_hewan" class="form-control">
              <option value="">-- Tidak Diketahui --</option>
              <option value="1" <?=fv($v,'riwayat_kontak_hewan')=='1'?'selected':''?>>Ya</option>
              <option value="0" <?=fv($v,'riwayat_kontak_hewan')=='0'?'selected':''?>>Tidak</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Jenis Hewan</label>
            <input type="text" name="jenis_hewan" class="form-control" value="<?=fv($v,'jenis_hewan')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Kontak</label>
            <input type="date" name="tgl_kontak" class="form-control" value="<?=fv($v,'tgl_kontak')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Lokasi Kontak</label>
            <input type="text" name="lokasi_kontak" class="form-control" value="<?=fv($v,'lokasi_kontak')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Riwayat Vaksinasi</label>
            <select name="riwayat_vaksinasi" class="form-control">
              <option value="">-- Tidak Diketahui --</option>
              <option value="1" <?=fv($v,'riwayat_vaksinasi')=='1'?'selected':''?>>Ya</option>
              <option value="0" <?=fv($v,'riwayat_vaksinasi')=='0'?'selected':''?>>Tidak</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Jenis Vaksin</label>
            <input type="text" name="jenis_vaksin" class="form-control" value="<?=fv($v,'jenis_vaksin')?>">
          </div>
        </div>
        <?php if($id_penyakit==11): // Avian Flu only ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Diberikan Oseltamivir?</label>
            <select name="oseltamivir" class="form-control">
              <option value="">-- --</option>
              <option value="1" <?=fv($v,'oseltamivir')=='1'?'selected':''?>>Ya</option>
              <option value="0" <?=fv($v,'oseltamivir')=='0'?'selected':''?>>Tidak</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Pemberian Oseltamivir</label>
            <input type="date" name="tgl_oseltamivir" class="form-control" value="<?=fv($v,'tgl_oseltamivir')?>">
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- LABORATORIUM -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-flask"></i> Pemeriksaan Laboratorium</div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Diperiksa Lab?</label>
            <select name="diperiksa_lab" class="form-control">
              <option value="0" <?=fv($v,'diperiksa_lab','0')=='0'?'selected':''?>>Tidak</option>
              <option value="1" <?=fv($v,'diperiksa_lab')=='1'?'selected':''?>>Ya</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Jenis Spesimen</label>
            <input type="text" name="jenis_sample" class="form-control" value="<?=fv($v,'jenis_sample')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Ambil Spesimen</label>
            <input type="date" name="tgl_ambil_sample" class="form-control" value="<?=fv($v,'tgl_ambil_sample')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Kirim Spesimen</label>
            <input type="date" name="tgl_kirim_sample" class="form-control" value="<?=fv($v,'tgl_kirim_sample')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Hasil Lab</label>
            <input type="date" name="tgl_hasil_lab" class="form-control" value="<?=fv($v,'tgl_hasil_lab')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Laboratorium</label>
            <input type="text" name="nama_lab" class="form-control" value="<?=fv($v,'nama_lab')?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Hasil Lab</label>
            <input type="text" name="hasil_lab" class="form-control" value="<?=fv($v,'hasil_lab')?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Keterangan Lab</label>
            <input type="text" name="ket_lab" class="form-control" value="<?=fv($v,'ket_lab')?>">
          </div>
        </div>
      </div>
    </div>

    <!-- VARIABEL TAMBAHAN PER PENYAKIT -->
    <?php if(!empty($detail)): ?>
    <?php
    // Kelompokkan detail per submodule
    $detail_by_sub = array();
    // Jika edit (id_pe>0) gunakan nilai aktual, jika baru gunakan template (value kosong)
    foreach($detail as $d) {
        $detail_by_sub[$d['submodule']][] = $d;
    }
    ?>
    <?php foreach($detail_by_sub as $submodule => $rows): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-list-alt"></i> <?=htmlspecialchars($submodule)?></div>
      <?php foreach($rows as $i => $d): ?>
      <div class="row" style="margin-bottom:6px">
        <input type="hidden" name="dsub[]" value="<?=htmlspecialchars($d['submodule'])?>">
        <input type="hidden" name="dkey[]" value="<?=htmlspecialchars($d['var_key'])?>">
        <input type="hidden" name="dtype[]" value="<?=htmlspecialchars($d['var_type'])?>">
        <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($d['var_label'])?>">
        <div class="col-sm-4">
          <label class="control-label"><?=htmlspecialchars($d['var_label'])?></label>
        </div>
        <div class="col-sm-7">
          <?php
          // Definisi pilihan untuk field select per var_key
          $select_options = array(
            'dp_lokasi' => array(
              'ekstrimitas_bawah'=>'Ekstrimitas Bawah (Kaki/Tungkai)',
              'ekstrimitas_atas'=>'Ekstrimitas Atas (Tangan/Lengan)',
              'kepala'=>'Kepala/Wajah',
              'leher'=>'Leher',
              'badan'=>'Badan/Perut/Dada',
              'bokong'=>'Bokong',
              'genitalia'=>'Genitalia',
            ),
            'lokasi_gigitan' => array(
              'ekstrimitas_bawah'=>'Ekstrimitas Bawah (Kaki/Tungkai)',
              'ekstrimitas_atas'=>'Ekstrimitas Atas (Tangan/Lengan)',
              'kepala'=>'Kepala/Wajah',
              'leher'=>'Leher',
              'badan'=>'Badan/Perut/Dada',
              'bokong'=>'Bokong',
              'genitalia'=>'Genitalia',
            ),
            'dp_hpr' => array(
              'anjing'=>'Anjing Peliharaan',
              'anjing_liar'=>'Anjing Liar',
              'kucing_peliharaan'=>'Kucing Peliharaan',
              'kucing_liar'=>'Kucing Liar',
              'monyet_liar'=>'Monyet Liar',
              'monye_peliharaan'=>'Monyet Peliharaan',
              'hewan_lainnya'=>'Hewan Lainnya',
            ),
            'dp_satuan_hpr' => array(
              'peliharaan'=>'Peliharaan',
              'peliharaan_yang_dilepas_liarkan'=>'Peliharaan yang Dilepas/Diliarkan',
              'liar'=>'Liar',
            ),
            'dp_kondisi' => array(
              'dalam_observasi'=>'Dalam Observasi',
              'lari_hilang'=>'Lari/Hilang',
              'mati_dibunuh'=>'Mati Dibunuh',
              'mati_sakit'=>'Mati Sakit',
              'lainnya'=>'Lainnya',
            ),
            'tipe_luka' => array(
              'jilatan'=>'Jilatan',
              'cakaran'=>'Cakaran',
              'gigitan_tidak_tembus'=>'Gigitan Tidak Tembus Kulit',
              'gigitan_tembus'=>'Gigitan Tembus Kulit',
            ),
            'riwayat_provokasi' => array(
              'tiba_tiba'=>'Tiba-tiba Menggigit',
              'memegang'=>'Saat Dipegang/Digendong',
              'mengganggu'=>'Saat Diganggu',
              'galak'=>'Hewan Memang Galak',
            ),
            'kondisi_pasca_var' => array(
              'sehat'=>'Sehat',
              'sakit_ringan'=>'Sakit Ringan',
              'sakit_berat'=>'Sakit Berat',
              'meninggal'=>'Meninggal',
            ),
            'dp_pekerjaan' => array(
              'petani'=>'Petani',
              'peternakan'=>'Peternakan/Peternak',
              'karyawan'=>'Karyawan/Pekerja Swasta',
              'ibu_rumah_tanggal'=>'Ibu Rumah Tangga',
              'tni'=>'TNI',
              'polri'=>'POLRI',
              'pelajar'=>'Pelajar/Mahasiswa',
              'tukang_ledeng'=>'Tukang/Buruh',
              'nelayan'=>'Nelayan',
              'pedagang'=>'Pedagang',
              'lainnya'=>'Lainnya',
            ),
            'pekerjaan' => array(
              'petani'=>'Petani',
              'peternakan'=>'Peternakan/Peternak',
              'karyawan'=>'Karyawan/Pekerja Swasta',
              'ibu_rumah_tanggal'=>'Ibu Rumah Tangga',
              'tni'=>'TNI',
              'polri'=>'POLRI',
              'pelajar'=>'Pelajar/Mahasiswa',
              'tukang_ledeng'=>'Tukang/Buruh',
              'nelayan'=>'Nelayan',
              'pedagang'=>'Pedagang',
              'lainnya'=>'Lainnya',
            ),
            // Avian
            'dp_kontak_hewan' => array(
              'hidup_sehat'=>'Hidup Sehat',
              'hidup_sakit'=>'Hidup Sakit',
              'mati'=>'Mati',
              'tidak_diketahui'=>'Tidak Diketahui',
            ),
            'pcr_result' => array(
              'positif'=>'Positif',
              'negatif'=>'Negatif',
              'pending'=>'Pending/Belum Keluar',
            ),
            'dp_asal_unggas' => array(
              'peternakan'=>'Peternakan',
              'lepas_liarkan'=>'Lepas/Diliarkan',
              'liar'=>'Liar',
              'pasar'=>'Pasar',
              'rumah_potong'=>'Rumah Potong Hewan',
            ),
            'dp_mobilitas_hewan' => array(
              'pasar'=>'Pasar',
              'pasar_hewan'=>'Pasar Hewan',
              'peternakan'=>'Peternakan',
              'migrasi'=>'Migrasi',
              'lainnya'=>'Lainnya',
            ),
            // Anthrax
            'tipe_manifestasi' => array(
              'kulit'=>'Anthraks Kulit (Cutaneous)',
              'gastrointestinal'=>'Anthraks Gastrointestinal',
              'paru'=>'Anthraks Paru/Inhalasi',
            ),
            'jenis_kontak_hewan' => array(
              'menyentuh'=>'Menyentuh Hewan',
              'menyembelih'=>'Menyembelih Hewan',
              'mengonsumsi'=>'Mengonsumsi Produk Hewan',
              'bekerja_sekitar'=>'Bekerja di Sekitar Hewan',
              'lainnya'=>'Lainnya',
            ),
            // Avian
            'dp_kontak_hewan' => array(
              'hidup_sehat'=>'Hidup Sehat',
              'hidup_sakit'=>'Hidup Sakit',
              'mati'=>'Mati',
              'tidak_diketahui'=>'Tidak Diketahui',
            ),
            // Anthrax
            'tipe_manifestasi' => array(
              'kulit'=>'Anthraks Kulit',
              'gastrointestinal'=>'Anthraks Gastrointestinal',
              'paru'=>'Anthraks Paru/Inhalasi',
            ),
          );
          ?>
          <?php if($d['var_type']=='boolean'): ?>
          <select name="dval[]" class="form-control input-sm">
            <option value="">-- Pilih --</option>
            <option value="Ya" <?=$d['var_value']=='Ya'?'selected':''?>>Ya</option>
            <option value="Tidak" <?=$d['var_value']=='Tidak'?'selected':''?>>Tidak</option>
          </select>
          <?php elseif($d['var_type']=='date'): ?>
          <input type="date" name="dval[]" class="form-control input-sm" value="<?=htmlspecialchars($d['var_value'])?>">
          <?php elseif($d['var_type']=='select' && isset($select_options[$d['var_key']])): ?>
          <select name="dval[]" class="form-control input-sm">
            <option value="-">-- Pilih --</option>
            <?php foreach($select_options[$d['var_key']] as $val=>$label): ?>
            <option value="<?=$val?>" <?=$d['var_value']==$val?'selected':''?>><?=$label?></option>
            <?php endforeach; ?>
          </select>
          <?php else: ?>
          <input type="text" name="dval[]" class="form-control input-sm" value="<?=htmlspecialchars($d['var_value'])?>" placeholder="<?=htmlspecialchars($d['var_label'])?>">
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-hospital-o"></i> Rawat Inap / RS</div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Nama RS/Klinik yang Merawat</label>
            <input type="text" name="nama_rs" class="form-control" value="<?=fv($v,'nama_rs')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Masuk RS/Klinik</label>
            <input type="date" name="tgl_masuk_rs" class="form-control" value="<?=fv($v,'tgl_masuk_rs')?>">
          </div>
        </div>
      </div>
    </div>
    <!-- KETERANGAN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-sticky-note"></i> Keterangan Lain</div>
      <div class="form-group">
        <textarea name="ket_lain" class="form-control" rows="3" placeholder="Keterangan tambahan..."><?=fv($v,'ket_lain')?></textarea>
      </div>
    </div>

    <div style="margin-bottom:30px">
      <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Simpan PE
      </button>
      <a href="<?=site_url('zoonosis/daftar')?>" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>
    </form>

  </section>
</div>
</div>

<script>
var BASE = '<?=base_url()?>';
var initProp = '<?=fv($v,"id_prop")?>';
var initKota = '<?=fv($v,"id_kota")?>';
var initPropPasien  = '<?=fv($v,"kd_prop_kasus")?>';
var initKotaPasien  = '<?=fv($v,"kd_kota_kasus")?>';
var initKecPasien   = '<?=fv($v,"id_kecamatan_kasus")?>';
var initKecNama     = '<?=fv($v,"kecamatan")?>';

// Cascade Kab/Kota Pasien
function loadKotaPasien(id_prop, selected) {
    $('#sel_kota_pasien').html('<option value="">-- Pilih Kab/Kota --</option>');
    $('#sel_kecamatan_pasien').html('<option value="">-- Pilih Kab/Kota dulu --</option>');
    if (!id_prop) return;
    $.get(BASE+'zoonosis/get_kota_pasien/'+id_prop, function(rows) {
        $.each(rows, function(i,r) {
            var sel = (selected && selected==r.id) ? ' selected' : '';
            $('#sel_kota_pasien').append('<option value="'+r.id+'"'+sel+'>'+r.kota+'</option>');
        });
        if (selected) { loadKecamatanPasien($('#sel_kota_pasien').val(), initKecPasien); }
    }, 'json');
}

// Cascade Kecamatan Pasien
function loadKecamatanPasien(id_kota, selected) {
    $('#sel_kecamatan_pasien').html('<option value="">-- Pilih Kecamatan --</option>');
    if (!id_kota) return;
    $.get(BASE+'zoonosis/get_kecamatan/'+id_kota, function(rows) {
        $.each(rows, function(i,r) {
            var sel = (selected && selected==r.id) ? ' selected' : '';
            $('#sel_kecamatan_pasien').append('<option value="'+r.id+'"'+sel+'>'+r.distrik+'</option>');
        });
        $('#sel_kecamatan_pasien').change(function(){
            $('#hid_kecamatan_nama').val($('#sel_kecamatan_pasien option:selected').text());
        });
    }, 'json');
}

$('#sel_prop_pasien').change(function() {
    loadKotaPasien($(this).val(), null);
});
$('#sel_kota_pasien').change(function() {
    loadKecamatanPasien($(this).val(), null);
});

// Init saat edit
if (initPropPasien) {
    $('#sel_prop_pasien').val(initPropPasien);
    loadKotaPasien(initPropPasien, initKotaPasien);
}
if (initKecNama) { $('#hid_kecamatan_nama').val(initKecNama); }
var initPusk = '<?=fv($v,"id_puskesmas")?>';
var initKec  = '<?=fv($v,"id_kecamatan")?>';

$('#sel_prop').change(function() {
    var id = $(this).val();
    $('#sel_kota').html('<option value="">-- Pilih --</option>');
    $('#sel_pusk').html('<option value="">-- Pilih Kab/Kota --</option>');
    if (!id) return;
    $.get(BASE+'zoonosis/get_kota/'+id, function(rows) {
        $.each(rows, function(i,r) {
            var sel = (r.id==initKota) ? ' selected' : '';
            $('#sel_kota').append('<option value="'+r.id+'"'+sel+'>'+r.kota+'</option>');
        });
        if (initKota) { $('#sel_kota').trigger('change'); }
    },'json');
});

$('#sel_kota').change(function() {
    var id = $(this).val();
    $('#sel_kec_unit').html('<option value="">-- Pilih Kecamatan --</option>');
    $('#sel_pusk').html('<option value="">-- Pilih --</option>');
    if (!id) return;
    // Load kecamatan unit pelapor
    $.get(BASE+'zoonosis/get_kecamatan/'+id, function(rows) {
        $.each(rows, function(i,r) {
            var sel = (r.id==initKec) ? ' selected' : '';
            $('#sel_kec_unit').append('<option value="'+r.id+'"'+sel+'>'+r.distrik+'</option>');
        });
        if (initKec) {
            // Load puskesmas langsung tanpa trigger (kecamatan mungkin disabled)
            $.get(BASE+'zoonosis/get_puskesmas_by_kec/'+initKec, function(rows) {
                if (!rows) return;
                $.each(rows, function(i,r) {
                    var sel = (r.id==initPusk) ? ' selected' : '';
                    $('#sel_pusk').append('<option value="'+r.id+'"'+sel+'>'+r.puskesmas+'</option>');
                });
            },'json');
        }
    },'json');
});

// Load puskesmas saat kecamatan dipilih
$('#sel_kec_unit').change(function() {
    var id_kec = $(this).val();
    $('#sel_pusk').html('<option value="">-- Pilih --</option>');
    if (!id_kec) return;
    $.get(BASE+'zoonosis/get_puskesmas_by_kec/'+id_kec, function(rows) {
        if (!rows) return;
        $.each(rows, function(i,r) {
            var sel = (r.id==initPusk) ? ' selected' : '';
            $('#sel_pusk').append('<option value="'+r.id+'"'+sel+'>'+r.puskesmas+'</option>');
        });
    },'json');
});

// tambah endpoint get_puskesmas ke controller jika belum ada
// sementara panggil via zm model
function tambahRow() {
    var html = '<div class="row detail-row" style="margin-bottom:6px">'
        +'<input type="hidden" name="dsub[]" value="">'
        +'<input type="hidden" name="dkey[]" value="var_'+Date.now()+'">'
        +'<input type="hidden" name="dtype[]" value="text">'
        +'<div class="col-sm-3"><input type="text" name="dlabel[]" class="form-control input-sm" placeholder="Label/Pertanyaan"></div>'
        +'<div class="col-sm-8"><input type="text" name="dval[]" class="form-control input-sm" placeholder="Nilai"></div>'
        +'<div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.detail-row\').remove()"><i class="fa fa-times"></i></button></div>'
        +'</div>';
    $('#detail-rows').append(html);
}

$('#formPE').submit(function(e) {
    e.preventDefault();
    var fd = $(this).serialize();
    $.post(BASE+'zoonosis/simpan', fd, function(res) {
        if (res.status=='ok') {
            alert('Data PE berhasil disimpan.');
            window.location = BASE+'zoonosis/detail/'+res.id;
        } else {
            alert('Gagal menyimpan: '+JSON.stringify(res));
        }
    },'json').fail(function(){ alert('Error server. Cek log.'); });
});

$(function() {
    if (initProp) { $('#sel_prop').val(initProp).trigger('change'); }
});
</script>
