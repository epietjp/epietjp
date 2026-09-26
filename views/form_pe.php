
<script>$(function(){ $('body').addClass('sidebar-collapse'); });</script>
<style>
.form-section {
    margin-top: 28px;
    margin-bottom: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0;
}
.form-section-title {
    background: #2c3e50;
    color: #fff !important;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 3px 3px 0 0;
    margin-bottom: 12px;
}
.form-section-title b {
    color: #fff;
    font-weight: 700;
}
.form-section > .row,
.form-section > div:not(.form-section-title) {
    padding: 8px 12px;
}
</style>
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
      <div class="form-section-title"><i class="fa fa-stethoscope"></i> <b>Diagnosa</b></div>
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
    <!-- STATUS LAPORAN ANTRAKS -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-flag"></i> <b>A. Status Laporan Antraks</b></div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Status Laporan <span class="req">*</span></label>
            <input type="hidden" name="dkey[]" value="atx_status_laporan">
            <input type="hidden" name="dlabel[]" value="Status Laporan">
            <input type="hidden" name="dsub[]" value="Status Laporan">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_sl=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_status_laporan'){$atx_sl=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="Suspek Antraks" <?=$atx_sl=='Suspek Antraks'?'selected':''?>>Suspek Antraks</option>
              <option value="Probable Antraks" <?=$atx_sl=='Probable Antraks'?'selected':''?>>Probable Antraks</option>
              <option value="Konfirmasi Antraks" <?=$atx_sl=='Konfirmasi Antraks'?'selected':''?>>Konfirmasi Antraks</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Berkaitan dengan Rumor KLB/Wabah?</label>
            <input type="hidden" name="dkey[]" value="atx_rumor_klb">
            <input type="hidden" name="dlabel[]" value="Berkaitan Rumor KLB">
            <input type="hidden" name="dsub[]" value="Status Laporan">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_rumor=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_rumor_klb'){$atx_rumor=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" onchange="toggleAtxRumor(this.value)">
              <option value="">-- Pilih --</option>
              <option value="Ya" <?=$atx_rumor=='Ya'?'selected':''?>>Ya</option>
              <option value="Tidak" <?=$atx_rumor=='Tidak'?'selected':''?>>Tidak</option>
              <option value="Tidak tahu" <?=$atx_rumor=='Tidak tahu'?'selected':''?>>Tidak tahu</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3" id="wrap_atx_rumor" style="display:<?=$atx_rumor=='Ya'?'block':'none'?>">
          <div class="form-group">
            <label>Tanggal Rumor</label>
            <input type="hidden" name="dkey[]" value="atx_tgl_rumor">
            <input type="hidden" name="dlabel[]" value="Tanggal Rumor">
            <input type="hidden" name="dsub[]" value="Status Laporan">
            <input type="hidden" name="dtype[]" value="date">
            <?php $v_tr=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_tgl_rumor'){$v_tr=$ed['var_value'];break;}} ?>
            <input type="date" name="dval[]" class="form-control" value="<?=htmlspecialchars($v_tr)?>">
          </div>
        </div>
        <div class="col-sm-3" id="wrap_atx_lokasi_rumor" style="display:<?=$atx_rumor=='Ya'?'block':'none'?>">
          <div class="form-group">
            <label>Lokasi Rumor</label>
            <input type="hidden" name="dkey[]" value="atx_lokasi_rumor">
            <input type="hidden" name="dlabel[]" value="Lokasi Rumor">
            <input type="hidden" name="dsub[]" value="Status Laporan">
            <input type="hidden" name="dtype[]" value="text">
            <?php $v_lr=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_lokasi_rumor'){$v_lr=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" value="<?=htmlspecialchars($v_lr)?>" placeholder="Kelurahan/Desa, Kecamatan">
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-user-md"></i> <b>B. Identitas Pelapor &amp; Laporan</b></div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>No PE <span class="req">*</span></label>
            <input type="text" name="no_pe" class="form-control" value="<?=fv($v,'no_pe')?>" readonly style="background:#f5f5f5" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Laporan <span class="req">*</span></label>
            <input type="date" name="tgl_laporan" class="form-control" required value="<?=fv($v,'tgl_laporan',date('Y-m-d'))?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal PE <span class="req">*</span></label>
            <input type="date" name="tgl_pe" class="form-control" required value="<?=fv($v,'tgl_pe')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Link No EBS</label>
            <select name="no_ebs" class="form-control">
              <option value="">-- Tidak Ada --</option>
              <?php $existing_no_ebs=fv($v,"no_ebs"); $ebs_keys=array_column($list_ebs,"no_ebs"); if($existing_no_ebs && !in_array($existing_no_ebs,$ebs_keys)): ?>
              <option value="<?=$existing_no_ebs?>" selected><?=$existing_no_ebs?> [EBS Existing]</option>
              <?php endif; ?>
              <?php foreach($list_ebs as $ebs): ?>
              <option value="<?=htmlspecialchars($ebs['no_ebs'])?>"
                <?=fv($v,'no_ebs')==$ebs['no_ebs']?'selected':''?>
                <?=!empty($ebs['sudah_pe'])?'style="color:#e74c3c;font-weight:bold"':''?>>
                <?=!empty($ebs['sudah_pe'])?'[SUDAH PE: '.$ebs['sudah_pe'].'] ':''?><?=htmlspecialchars($ebs['no_ebs'])?> | <?=htmlspecialchars($ebs['tanggal'])?> | <?=htmlspecialchars($ebs['kota'])?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <?php if($id_penyakit==26): ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Berkunjung Pertama ke Fasyankes <small class="text-muted">(Lepto)</small></label>
            <input type="date" name="tgl_fasyankes" class="form-control" value="<?=fv($v,'tgl_fasyankes')?>">
            <small class="text-muted">Tanggal pertama datang ke fasilitas kesehatan</small>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Petugas PE <span class="req">*</span></label>
            <input type="text" name="nama_petugas" class="form-control" value="<?=fv($v,'nama_petugas')?>" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Telp Petugas</label>
            <input type="tel" name="telp_petugas" class="form-control" maxlength="13" pattern="[0-9]{10,13}" inputmode="numeric" placeholder="10-13 digit angka" value="<?=fv($v,'telp_petugas')?>" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Jabatan Petugas</label>
            <select name="jabatan_petugas" class="form-control" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php
              $jab_opts = array('Petugas Surveilans Puskesmas','Dokter','Perawat','Bidan','Epidemiolog','Sanitarian','Lainnya');
              foreach($jab_opts as $jab):
              $sel = fv($v,'jabatan_petugas')==$jab ? 'selected' : '';
              // Cek jika nilai tidak ada di list (free text lama)
              if($jab=='Lainnya' && fv($v,'jabatan_petugas') && !in_array(fv($v,'jabatan_petugas'),$jab_opts)) $sel='selected';
              ?>
              <option value="<?=$jab?>" <?=$sel?>><?=$jab?></option>
              <?php endforeach; ?>
              <?php if(fv($v,'jabatan_petugas') && !in_array(fv($v,'jabatan_petugas'),$jab_opts)): ?>
              <option value="<?=fv($v,'jabatan_petugas')?>" selected><?=fv($v,'jabatan_petugas')?></option>
              <?php endif; ?>
            </select>
            <input type="text" id="jabatan_lainnya" class="form-control" placeholder="Tulis jabatan lainnya"
              style="margin-top:5px;display:<?=(fv($v,'jabatan_petugas')&&!in_array(fv($v,'jabatan_petugas'),array('Petugas Surveilans Puskesmas','Dokter','Perawat','Bidan','Epidemiolog','Sanitarian','Lainnya')))?'block':'none'?>"
              value="<?=(fv($v,'jabatan_petugas')&&!in_array(fv($v,'jabatan_petugas'),array('Petugas Surveilans Puskesmas','Dokter','Perawat','Bidan','Epidemiolog','Sanitarian','Lainnya')))?fv($v,'jabatan_petugas'):''?>"
              oninput="this.previousElementSibling.previousElementSibling.value=this.value">
            <script>
            $('select[name=jabatan_petugas]').on('change',function(){
              if($(this).val()=='Lainnya'){
                $('#jabatan_lainnya').show().focus();
                $(this).val('Lainnya');
              } else {
                $('#jabatan_lainnya').hide().val('');
              }
            });
            // Sync nilai lainnya ke select saat submit
            $('form').on('submit',function(){
              if($('select[name=jabatan_petugas]').val()=='Lainnya' && $('#jabatan_lainnya').val()){
                $('select[name=jabatan_petugas]').append('<option value="'+$('#jabatan_lainnya').val()+'" selected>'+$('#jabatan_lainnya').val()+'</option>').val($('#jabatan_lainnya').val());
              }
            });
            </script>
          </div>
        </div>
      </div>
    </div>

    <!-- WILAYAH -->
    <div class="form-section">
      <?php $has_wilayah = !empty($v["id_prop"]); ?>
      <div class="form-section-title"><i class="fa fa-map-marker"></i> <b>C. Wilayah Unit Pelapor</b></div>
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
            <select name="id_kecamatan" id="sel_kec_unit" class="form-control" required <?=$has_wilayah&&!$is_edit&&!empty($v["id_kecamatan"])?"disabled":""?>>
              <option value="">-- Pilih Kab/Kota dulu --</option>
            </select>
            <?php if($has_wilayah && !$is_edit && !empty($v["id_kecamatan"])): echo "<input type=hidden name=id_kecamatan value=" . fv($v,"id_kecamatan") . ">"; endif; ?>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Unit Pelapor</label>
            <select name="id_puskesmas" id="sel_pusk" class="form-control" required <?=$has_wilayah&&!$is_edit&&!empty($v["id_puskesmas"])?"disabled":""?>>
              <option value="">-- Pilih Kab/Kota --</option>
            </select>
            <?php if($has_wilayah && !$is_edit && !empty($v["id_puskesmas"])): echo "<input type=hidden name=id_puskesmas value=" . fv($v,"id_puskesmas") . ">"; endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- IDENTITAS PASIEN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-user"></i> <b>D. Identitas Pasien</b></div>
      <?php if($id_penyakit != 8): ?>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>No. Epid <span class="req">*</span> <small class="text-muted">(11 digit)</small></label>
            <input type="text" name="no_epid" id="f_no_epid" class="form-control" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" placeholder="Contoh: 36740100001" value="<?=fv($v,'no_epid')?>" required>
            <small class="text-muted">Format: Kode Prov(2)+Kabko(2)+Penyakit(2)+No Kasus(3)+Cek(2)</small>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Pasien <span class="req">*</span></label>
            <input type="text" name="nama_pasien" class="form-control" value="<?=fv($v,'nama_pasien')?>" required>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Orang Tua / KK <span class="req">*</span></label>
            <input type="text" name="nama_ortu" class="form-control" required value="<?=fv($v,'nama_ortu')?>" placeholder="Nama orang tua atau kepala keluarga">
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" placeholder="16 digit angka (0000000000000000 jika tidak ada)" value="<?=fv($v,'nik')?>" required><small class="text-muted">Isi 0000000000000000 jika tidak ada NIK</small>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-2">
          <div class="form-group">
            <label>Jenis Kelamin <span class="req">*</span></label>
            <select name="kelamin" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="L" <?=fv($v,'kelamin')=='L'?'selected':''?>>Laki-laki</option>
              <option value="P" <?=fv($v,'kelamin')=='P'?'selected':''?>>Perempuan</option>
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Tanggal Lahir <span class="req">*</span></label>
            <input type="date" name="tgl_lahir" class="form-control" value="<?=fv($v,'tgl_lahir')?>" required>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Umur (Tahun)</label>
            <input type="number" name="umur_thn" class="form-control" min="0" value="<?=fv($v,'umur_thn',0)?>" required>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Umur (Bulan)</label>
            <input type="number" name="umur_bln" class="form-control" min="0" max="11" value="<?=fv($v,'umur_bln',0)?>">
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Umur (Hari)</label>
            <input type="number" name="umur_hari" class="form-control" value="<?=fv($v,'umur_hari')?>" min="0" max="30" required placeholder="0-30">
          </div>
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Alamat <span class="req">*</span></label>
            <input type="text" name="alamat" class="form-control" required value="<?=fv($v,'alamat')?>">
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
            <select name="kelurahan" id="sel_kelurahan" class="form-control">
              <option value="">-- Pilih dulu Kecamatan --</option>
              <?php if(fv($v,'kelurahan')): ?>
              <option value="<?=htmlspecialchars(fv($v,'kelurahan'))?>" selected><?=htmlspecialchars(fv($v,'kelurahan'))?></option>
              <?php endif; ?>
            </select>
            <script>
            $(function(){
              var id_kec = $('#sel_kecamatan_pasien').val();
              if(id_kec && id_kec > 0) loadDesa(id_kec);
              $('#sel_kecamatan_pasien').on('change', function(){ loadDesa($(this).val()); });
            });
            function loadDesa(id_kec) {
              var cur = '<?=addslashes(fv($v,"kelurahan"))?>';
              $('#sel_kelurahan').html('<option value="">-- Memuat desa... --</option>');
              if(!id_kec||id_kec==0){ $('#sel_kelurahan').html('<option value="">-- Pilih dulu Kecamatan --</option>'); return; }
              $.get(BASE+'zoonosis/get_desa/'+id_kec, function(rows){
                var html='<option value="">-- Pilih Kelurahan/Desa --</option>';
                $.each(rows,function(i,r){ html+='<option value="'+r.desa+'"'+(r.desa==cur?' selected':'')+'>'+r.desa+'</option>'; });
                html+='<option value="__lain__">Lainnya (tulis manual)</option>';
                $('#sel_kelurahan').html(html);
                if(cur) $('#sel_kelurahan').val(cur);
              },'json');
            }
            $('#sel_kelurahan').on('change',function(){
              if($(this).val()=='__lain__'){
                var v=prompt('Tulis nama kelurahan/desa:');
                if(v){ $(this).append('<option value="'+v+'" selected>'+v+'</option>').val(v); }
                else { $(this).val(''); }
              }
            });
            </script>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Alamat Detail <small class="text-muted">(Jalan/RT/RW/Blok/Pemukiman)</small></label>
            <input type="text" name="alamat_detail" class="form-control" placeholder="Contoh: Jl. Mawar No.5 RT 02/RW 03" value="<?=fv($v,'alamat_detail')?>">
          </div>
        </div>
      </div>
    </div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Pekerjaan</label>
            <select name="pekerjaan" class="form-control" <?=$id_penyakit==26?"required":"" ?>>
              <option value="">-- Pilih Pekerjaan --</option>
              <?php
              // Opsi pekerjaan per penyakit sesuai form PE kertas
              $pekerjaan_opts = array(
                8 => array( // GHPR/Rabies
                  'petani'=>'Petani','peternakan'=>'Peternakan/Peternak','veterinarian'=>'Veterinarian',
                  'karyawan'=>'Karyawan/Pekerja Swasta','ibu_rumah_tangga'=>'Ibu Rumah Tangga',
                  'tni'=>'TNI','polri'=>'POLRI','pelajar'=>'Pelajar/Mahasiswa',
                  'tukang'=>'Tukang/Buruh','nelayan'=>'Nelayan','pedagang'=>'Pedagang',
                  'belum_bekerja'=>'Belum/Tidak Bekerja','lainnya'=>'Lainnya',
                ),
                11 => array( // Avian Flu
                  'rs_klinik'=>'RS/Klinik','veterinarian'=>'Veterinarian',
                  'laboratorium'=>'Laboratorium','peternak_unggas'=>'Peternak Unggas',
                  'peternak_babi'=>'Peternak Babi','pasar_unggas'=>'Pasar Unggas/Babi',
                  'belum_bekerja'=>'Belum/Tidak Bekerja','lainnya'=>'Lainnya',
                ),
                14 => array( // Anthraks
                  'peternak_sapi'=>'Peternak Sapi/Kambing/Domba',
                  'pekerja_rph'=>'Pekerja RPH/Pemotongan Hewan',
                  'pekerja_kulit'=>'Pekerja Pengolah Kulit/Wool/Tulang',
                  'veterinarian'=>'Veterinarian',
                  'laboratorium'=>'Laboratorium',
                  'rs_klinik'=>'RS/Klinik',
                  'pasar_hewan'=>'Pasar Hewan',
                  'petani'=>'Petani/Penggarap Lahan',
                  'belum_bekerja'=>'Belum/Tidak Bekerja','lainnya'=>'Lainnya',
                ),
                26 => array( // Leptospirosis
                  'petani_sawah'=>'Petani Sawah/Kebun',
                  'petugas_kebersihan'=>'Petugas Kebersihan/Sanitasi',
                  'pekerja_saluran'=>'Pekerja Saluran Air/Selokan',
                  'peternak'=>'Peternak',
                  'nelayan'=>'Nelayan',
                  'petugas_kesehatan'=>'Petugas Kesehatan',
                  'veterinarian'=>'Veterinarian',
                  'laboratorium'=>'Laboratorium',
                  'militer'=>'Militer/TNI/POLRI',
                  'belum_bekerja'=>'Belum/Tidak Bekerja','lainnya'=>'Lainnya',
                ),
              );
              $opts = isset($pekerjaan_opts[$id_penyakit]) ? $pekerjaan_opts[$id_penyakit] : $pekerjaan_opts[8];
              foreach($opts as $val=>$label): ?>
              <option value="<?=$val?>" <?=fv($v,'pekerjaan')==$val?'selected':''?>><?=$label?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" id="pekerjaan_lainnya" class="form-control" placeholder="Tulis pekerjaan lainnya"
              style="margin-top:5px;display:<?=fv($v,'pekerjaan')=='lainnya'?'block':'none'?>"
              value="">
            <script>
            $('select[name=pekerjaan]').on('change',function(){
              if($(this).val()=='lainnya'){
                $('#pekerjaan_lainnya').show().focus();
              } else {
                $('#pekerjaan_lainnya').hide().val('');
              }
            });
            $('form').on('submit',function(){
              if($('select[name=pekerjaan]').val()=='lainnya' && $('#pekerjaan_lainnya').val()){
                $('select[name=pekerjaan]').append('<option value="'+$('#pekerjaan_lainnya').val()+'" selected>'+$('#pekerjaan_lainnya').val()+'</option>').val($('#pekerjaan_lainnya').val());
              }
            });
            </script>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Tlp/HP Pasien</label>
            <input type="tel" name="telp_pasien" class="form-control" maxlength="13" pattern="[0-9]{10,13}" inputmode="numeric" placeholder="10-13 digit angka" value="<?=fv($v,'telp_pasien')?>" required>
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
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama Saudara Dekat yang Dapat Dihubungi</label>
            <input type="text" name="kontak_darurat" class="form-control" placeholder="Nama kontak darurat" value="<?=fv($v,'kontak_darurat')?>">
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>No Telp Kontak Darurat</label>
            <input type="tel" name="telp_kontak_darurat" class="form-control" maxlength="13" pattern="[0-9]{10,13}" inputmode="numeric" placeholder="10-13 digit angka" value="<?=fv($v,'telp_kontak_darurat')?>">
          </div>
        </div>
      </div>

    <!-- KLINIS -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-heartbeat"></i> <b>E. Informasi Klinis</b></div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Mulai Sakit/Bergejala <span class="req">*</span></label>
            <input type="date" name="tgl_bergejala" class="form-control" required value="<?=fv($v,'tgl_bergejala')?>">
          </div>
        </div>
      </div>
      <?php if($id_penyakit==11): ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <label><b>Gejala dan Tanda Sakit (Avian Influenza)</b> <span class="req">*</span></label>
            <div class="row">
              <?php
              $avian_gejala = array(
                'g_demam'=>'Demam','g_batuk'=>'Batuk','g_pilek'=>'Pilek',
                'g_sakit_tenggorok'=>'Sakit Tenggorok','g_sesak'=>'Sesak Nafas'
              );
              $avian_val = array();
              if(!empty($eav_data)) foreach($eav_data as $ed){
                if(in_array($ed['var_key'], array_keys($avian_gejala))) $avian_val[$ed['var_key']]=$ed['var_value'];
              }
              foreach($avian_gejala as $key=>$label): ?>
              <div class="col-sm-2">
                <div class="checkbox">
                  <label>
                    <input type="hidden" name="dkey[]" value="<?=$key?>">
                    <input type="hidden" name="dlabel[]" value="<?=$label?>">
                    <input type="hidden" name="dsub[]" value="Gejala Avian">
                    <input type="hidden" name="dtype[]" value="boolean">
                    <input type="checkbox" name="dval[]" value="Ya" <?=isset($avian_val[$key])&&$avian_val[$key]=='Ya'?'checked':''?>> <?=$label?>
                  </label>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="row" style="display:none"><div class="col-sm-3"><div class="form-group">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Sakit (Berobat) <span class="req">*</span></label>
            <input type="date" name="tgl_sakit" class="form-control" required value="<?=fv($v,'tgl_sakit')?>">
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
            <select name="status_kasus" class="form-control" required>
              <option value="0" <?=fv($v,'status_kasus','0')=='0'?'selected':''?>>Suspek</option>
              <option value="1" <?=fv($v,'status_kasus')=='1'?'selected':''?>>Probable</option>
              <option value="2" <?=fv($v,'status_kasus')=='2'?'selected':''?>>Konfirmasi</option>
              <option value="3" <?=fv($v,'status_kasus')=='3'?'selected':''?>>Discarded</option>
            </select>
          </div>
        </div>
        <?php if($id_penyakit == 26): ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Definisi Kasus <small class="text-muted">(Lepto)</small></label>
            <select name="definisi_kasus" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="1" <?=fv($v,'definisi_kasus')=='1'?'selected':''?>>Konfirmasi</option>
              <option value="2" <?=fv($v,'definisi_kasus')=='2'?'selected':''?>>Probable</option>
              <option value="3" <?=fv($v,'definisi_kasus')=='3'?'selected':''?>>Suspek</option>
              <option value="4" <?=fv($v,'definisi_kasus')=='4'?'selected':''?>>Tidak Memenuhi Kriteria</option>
            </select>
          </div>
        </div>
        <?php endif; ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Kondisi Akhir</label>
            <select name="akhir_no" class="form-control">
              <option value="">-- Belum Diketahui --</option>
              <option value="1" <?=fv($v,'akhir_no')=='1'?'selected':''?>>Sembuh</option>
              <option value="2" <?=fv($v,'akhir_no')=='2'?'selected':''?>>Meninggal</option>
              <option value="3" <?=fv($v,'akhir_no')=='3'?'selected':''?>>Dirawat RS</option>
              <option value="4" <?=fv($v,'akhir_no')=='4'?'selected':''?>>Dirawat Klinik</option>
              <option value="5" <?=fv($v,'akhir_no')=='5'?'selected':''?>>Dirawat di Rumah</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3" id="wrap_tgl_meninggal" style="display:<?=fv($v,'akhir_no')=='2'?'block':'none'?>">
          <div class="form-group">
            <label>Tanggal Meninggal</label>
            <input type="date" name="tgl_meninggal" class="form-control" value="<?=fv($v,'tgl_meninggal')?>">
          </div>
        </div>
        <script>
        $('select[name=akhir_no]').on('change', function(){
            $('#wrap_tgl_meninggal').toggle($(this).val() == '2');
            if($(this).val() != '2') $('input[name=tgl_meninggal]').val('');
        });
        </script>
        <!-- field gejala free text disembunyikan, digantikan checklist per penyakit -->
        <input type="hidden" name="gejala" value="<?=fv($v,'gejala')?>">
      </div>
    </div>

    <!-- GEJALA INLINE setelah onset (khusus GHPR dan Lepto) -->
    <?php if(in_array($id_penyakit, array(8,26))): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-stethoscope"></i> <b>Gejala &amp; Tanda Sakit</b></div>
      <div class="row">
      <?php
      $gejala_inline = array();
      foreach($detail as $d) {
        if(strpos($d['submodule'],'Gejala')===0) $gejala_inline[] = $d;
      }
      foreach($gejala_inline as $gi):
      ?>
      <div class="col-sm-3" style="padding:4px 15px">
        <label style="font-weight:normal;margin:0;font-size:12px">
          <input type="hidden" name="dsub[]" value="<?=htmlspecialchars($gi['submodule'])?>">
          <input type="hidden" name="dkey[]" value="<?=htmlspecialchars($gi['var_key'])?>">
          <input type="hidden" name="dtype[]" value="<?=htmlspecialchars($gi['var_type'])?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($gi['var_label'])?>">
          <input type="checkbox" name="dval[]" value="Ya" <?=$gi['var_value']=='Ya'?'checked':''?> style="margin-right:4px">
          <?=htmlspecialchars($gi['var_label'])?>
        </label>
      </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>


    <!-- LABORATORIUM -->
    <!-- DIAGNOSIS + RAWAT INAP ANTRAKS -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-stethoscope"></i> <b>Diagnosis & Perawatan Antraks</b></div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Diagnosis Awal <span class="req">*</span></label>
            <input type="hidden" name="dkey[]" value="atx_diagnosis_awal">
            <input type="hidden" name="dlabel[]" value="Diagnosis Awal Antraks">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_da=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_diagnosis_awal'){$atx_da=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" required onchange="toggleDiagLainnya(this,'atx_da_lainnya')">
              <option value="">-- Pilih Diagnosis Awal --</option>
              <option value="Antraks Kulit" <?=$atx_da=='Antraks Kulit'?'selected':''?>>Antraks Kulit (Cutaneous Anthrax)</option>
              <option value="Antraks Saluran Cerna" <?=$atx_da=='Antraks Saluran Cerna'?'selected':''?>>Antraks Saluran Cerna (GI Anthrax)</option>
              <option value="Antraks Saluran Nafas" <?=$atx_da=='Antraks Saluran Nafas'?'selected':''?>>Antraks Saluran Nafas (Inhalational)</option>
              <option value="Antraks Injeksi" <?=$atx_da=='Antraks Injeksi'?'selected':''?>>Antraks Injeksi (Injection Anthrax)</option>
              <option value="Lainnya" <?=$atx_da=='Lainnya'?'selected':''?>>Lainnya</option>
            </select>
            <input type="text" id="atx_da_lainnya" class="form-control" placeholder="Tulis diagnosis awal lainnya"
              style="margin-top:5px;display:<?=$atx_da=='Lainnya'?'block':'none'?>"
              value="<?=$atx_da=='Lainnya'?'':''?>"
              oninput="document.querySelector('select[name=dval[]]').value=this.value">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Diagnosis Akhir <span class="req">*</span></label>
            <input type="hidden" name="dkey[]" value="atx_diagnosis_akhir">
            <input type="hidden" name="dlabel[]" value="Diagnosis Akhir Antraks">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_dk=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_diagnosis_akhir'){$atx_dk=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" required onchange="toggleDiagLainnya(this,'atx_dk_lainnya')">
              <option value="">-- Pilih Diagnosis Akhir --</option>
              <option value="Antraks Kulit" <?=$atx_dk=='Antraks Kulit'?'selected':''?>>Antraks Kulit (Cutaneous Anthrax)</option>
              <option value="Antraks Saluran Cerna" <?=$atx_dk=='Antraks Saluran Cerna'?'selected':''?>>Antraks Saluran Cerna (GI Anthrax)</option>
              <option value="Antraks Saluran Nafas" <?=$atx_dk=='Antraks Saluran Nafas'?'selected':''?>>Antraks Saluran Nafas (Inhalational)</option>
              <option value="Antraks Injeksi" <?=$atx_dk=='Antraks Injeksi'?'selected':''?>>Antraks Injeksi (Injection Anthrax)</option>
              <option value="Lainnya" <?=$atx_dk=='Lainnya'?'selected':''?>>Lainnya</option>
            </select>
            <input type="text" id="atx_dk_lainnya" class="form-control" placeholder="Tulis diagnosis akhir lainnya"
              style="margin-top:5px;display:<?=$atx_dk=='Lainnya'?'block':'none'?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Apakah Dirawat Inap?</label>
            <input type="hidden" name="dkey[]" value="atx_rawat_inap">
            <input type="hidden" name="dlabel[]" value="Dirawat Inap">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_ri=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_rawat_inap'){$atx_ri=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" onchange="toggleAtxRI(this.value)">
              <option value="">-- Pilih --</option>
              <option value="Ya" <?=$atx_ri=='Ya'?'selected':''?>>Ya</option>
              <option value="Tidak" <?=$atx_ri=='Tidak'?'selected':''?>>Tidak</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row" id="wrap_atx_ri" style="display:<?=$atx_ri=='Ya'?'block':'none'?>">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Nama RS/Puskesmas/Klinik</label>
            <input type="hidden" name="dkey[]" value="atx_nama_rs">
            <input type="hidden" name="dlabel[]" value="Nama RS Perawatan">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="text">
            <?php $atx_rs=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_nama_rs'){$atx_rs=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" value="<?=htmlspecialchars($atx_rs)?>" placeholder="Nama fasilitas kesehatan">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Tanggal Masuk Rawat Inap</label>
            <input type="hidden" name="dkey[]" value="atx_tgl_masuk_rs">
            <input type="hidden" name="dlabel[]" value="Tgl Masuk Rawat Inap">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="date">
            <?php $atx_tm=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_tgl_masuk_rs'){$atx_tm=$ed['var_value'];break;}} ?>
            <input type="date" name="dval[]" class="form-control" value="<?=htmlspecialchars($atx_tm)?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Tanggal Keluar Rawat Inap</label>
            <input type="hidden" name="dkey[]" value="atx_tgl_keluar_rs">
            <input type="hidden" name="dlabel[]" value="Tgl Keluar Rawat Inap">
            <input type="hidden" name="dsub[]" value="Klinis Anthraks">
            <input type="hidden" name="dtype[]" value="date">
            <?php $atx_tk=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_tgl_keluar_rs'){$atx_tk=$ed['var_value'];break;}} ?>
            <input type="date" name="dval[]" class="form-control" value="<?=htmlspecialchars($atx_tk)?>">
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>


    <!-- TATA LAKSANA ANTRAKS -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-medkit"></i> <b>F. Tata Laksana Kasus (Anthraks)</b></div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Apakah Diberikan Antibiotik?</label>
            <input type="hidden" name="dkey[]" value="atx_antibiotik">
            <input type="hidden" name="dlabel[]" value="Diberikan Antibiotik">
            <input type="hidden" name="dsub[]" value="Tata Laksana Anthraks">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_ab=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_antibiotik'){$atx_ab=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control" required onchange="$('#wrap_atx_ab').toggle(this.value==='Ya')">
              <option value="">-- Pilih --</option>
              <option value="Ya" <?=$atx_ab=='Ya'?'selected':''?>>Ya</option>
              <option value="Tidak" <?=$atx_ab=='Tidak'?'selected':''?>>Tidak</option>
            </select>
          </div>
        </div>
        <div class="col-sm-4" id="wrap_atx_ab" style="display:<?=$atx_ab=='Ya'?'block':'none'?>">
          <div class="form-group">
            <label>Jenis Antibiotik</label>
            <input type="hidden" name="dkey[]" value="atx_jenis_antibiotik">
            <input type="hidden" name="dlabel[]" value="Jenis Antibiotik">
            <input type="hidden" name="dsub[]" value="Tata Laksana Anthraks">
            <input type="hidden" name="dtype[]" value="text">
            <?php $atx_jab=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_jenis_antibiotik'){$atx_jab=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" value="<?=htmlspecialchars($atx_jab)?>" placeholder="Contoh: Ciprofloxacin, Doxycycline, Amoxicillin">
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-flask"></i> <b>G. Pemeriksaan Laboratorium</b></div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Diperiksa Lab? <span class="req">*</span></label>
            <select name="diperiksa_lab" class="form-control" onchange="toggleLab(this.value)" required>
              <option value="0" <?=fv($v,'diperiksa_lab','0')=='0'?'selected':''?>>Tidak</option>
              <option value="1" <?=fv($v,'diperiksa_lab')=='1'?'selected':''?>>Ya</option>
            </select>
          </div>
        </div>
      </div>
      <div id="wrap_detail_lab" style="display:<?=fv($v,'diperiksa_lab')=='1'?'block':'none'?>">
      <div class="row">
        <div class="col-sm-2">
          <div class="form-group">
            <label>Jenis Pemeriksaan Lab</label>
            <select name="jenis_pemeriksaan_lab" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="Kultur" <?=fv($v,'jenis_pemeriksaan_lab')=='Kultur'?'selected':''?>>Kultur</option>
              <option value="PCR" <?=fv($v,'jenis_pemeriksaan_lab')=='PCR'?'selected':''?>>PCR</option>
              <option value="Serologi" <?=fv($v,'jenis_pemeriksaan_lab')=='Serologi'?'selected':''?>>Serologi</option>
              <option value="Mikroskopis" <?=fv($v,'jenis_pemeriksaan_lab')=='Mikroskopis'?'selected':''?>>Mikroskopis</option>
              <option value="Imunohistokimia" <?=fv($v,'jenis_pemeriksaan_lab')=='Imunohistokimia'?'selected':''?>>Imunohistokimia</option>
              <option value="Lainnya" <?=fv($v,'jenis_pemeriksaan_lab')=='Lainnya'?'selected':''?>>Lainnya</option>
            </select>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Jenis Spesimen</label>
            <select name="jenis_sample" class="form-control">
              <option value="">-- Pilih Jenis Spesimen --</option>
              <option value="Serum Darah" <?=fv($v,'jenis_sample')=='Serum Darah'?'selected':''?>>Serum Darah</option>
              <option value="Whole Blood" <?=fv($v,'jenis_sample')=='Whole Blood'?'selected':''?>>Whole Blood</option>
              <option value="Urine" <?=fv($v,'jenis_sample')=='Urine'?'selected':''?>>Urine</option>
              <option value="Usap Nasofaring" <?=fv($v,'jenis_sample')=='Usap Nasofaring'?'selected':''?>>Usap Nasofaring</option>
              <option value="Usap Tenggorok" <?=fv($v,'jenis_sample')=='Usap Tenggorok'?'selected':''?>>Usap Tenggorok</option>
              <option value="Swab Rektal" <?=fv($v,'jenis_sample')=='Swab Rektal'?'selected':''?>>Swab Rektal</option>
              <option value="Kulit/Lesi" <?=fv($v,'jenis_sample')=='Kulit/Lesi'?'selected':''?>>Kulit/Lesi</option>
              <option value="Jaringan/Eksudat" <?=fv($v,'jenis_sample')=='Jaringan/Eksudat'?'selected':''?>>Jaringan/Eksudat</option>
              <option value="Otak Hewan (GHPR)" <?=fv($v,'jenis_sample')=='Otak Hewan (GHPR)'?'selected':''?>>Otak Hewan (GHPR)</option>
              <option value="Eksudat/Keropeng Lesi" <?=fv($v,'jenis_sample')=='Eksudat/Keropeng Lesi'?'selected':''?>>Eksudat/Keropeng Lesi (Antraks)</option>
              <option value="Darah Vena" <?=fv($v,'jenis_sample')=='Darah Vena'?'selected':''?>>Darah Vena</option>
              <option value="Cairan Pleura" <?=fv($v,'jenis_sample')=='Cairan Pleura'?'selected':''?>>Cairan Pleura</option>
              <option value="Feses" <?=fv($v,'jenis_sample')=='Feses'?'selected':''?>>Feses</option>
              <option value="Lainnya" <?=fv($v,'jenis_sample')=='Lainnya'?'selected':''?>>Lainnya</option>
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Tanggal Ambil Spesimen</label>
            <input type="date" name="tgl_ambil_sample" class="form-control" value="<?=fv($v,'tgl_ambil_sample')?>">
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label>Tanggal Kirim Spesimen</label>
            <input type="date" name="tgl_kirim_sample" class="form-control" value="<?=fv($v,'tgl_kirim_sample')?>">
          </div>
        </div>
        <div class="col-sm-2">
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
      </div><!-- /wrap_detail_lab -->
      <script>
      function toggleLab(val) {
        $('#wrap_detail_lab').toggle(val == '1');
        $('#wrap_spesimen_tambahan').toggle(val == '1');
      }
      </script>
    </div>

    <!-- SPESIMEN TAMBAHAN -->
    <div class="form-section" id="wrap_spesimen_tambahan" style="display:<?=fv($v,'diperiksa_lab','0')=='1'?'block':'none'?>">
      <div class="form-section-title"><i class="fa fa-flask"></i> <b>H. Spesimen Tambahan (Lab)</b></div>
      <div id="tbl-spesimen">
        <div class="row spesimen-row" style="margin-bottom:6px">
          <div class="col-sm-2">
            <input type="hidden" name="dkey[]" value="sp0_jenis">
            <input type="hidden" name="dlabel[]" value="Jenis Spesimen 1">
            <input type="hidden" name="dsub[]" value="Spesimen Lab">
            <input type="hidden" name="dtype[]" value="text">
            <select name="dval[]" class="form-control input-sm">
              <option value="">-- Jenis --</option>
              <option value="serum_darah">Serum Darah</option>
              <option value="urine">Urine</option>
              <option value="usap_nasofaring">Usap Nasofaring</option>
              <option value="usap_tenggorok">Usap Tenggorok</option>
              <option value="kulit_lesi">Kulit/Lesi</option>
              <option value="jaringan">Jaringan/Eksudat</option>
              <option value="otak_hewan">Otak Hewan (GHPR)</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>
          <div class="col-sm-2">
            <input type="hidden" name="dkey[]" value="sp0_nomor">
            <input type="hidden" name="dlabel[]" value="Nomor Spesimen 1">
            <input type="hidden" name="dsub[]" value="Spesimen Lab">
            <input type="hidden" name="dtype[]" value="text">
            <input type="text" name="dval[]" class="form-control input-sm" placeholder="Nomor Spesimen">
          </div>
          <div class="col-sm-2">
            <input type="hidden" name="dkey[]" value="sp0_tgl_ambil">
            <input type="hidden" name="dlabel[]" value="Tgl Ambil 1">
            <input type="hidden" name="dsub[]" value="Spesimen Lab">
            <input type="hidden" name="dtype[]" value="date">
            <input type="date" name="dval[]" class="form-control input-sm" placeholder="Tgl Ambil">
          </div>
          <div class="col-sm-2">
            <input type="hidden" name="dkey[]" value="sp0_tgl_hasil">
            <input type="hidden" name="dlabel[]" value="Tgl Hasil 1">
            <input type="hidden" name="dsub[]" value="Spesimen Lab">
            <input type="hidden" name="dtype[]" value="date">
            <input type="date" name="dval[]" class="form-control input-sm" placeholder="Tgl Hasil">
          </div>
          <div class="col-sm-2">
            <input type="hidden" name="dkey[]" value="sp0_hasil">
            <input type="hidden" name="dlabel[]" value="Hasil 1">
            <input type="hidden" name="dsub[]" value="Spesimen Lab">
            <input type="hidden" name="dtype[]" value="text">
            <input type="text" name="dval[]" class="form-control input-sm" placeholder="Hasil">
          </div>
          <div class="col-sm-2">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.spesimen-row').remove()"><i class="fa fa-times"></i></button>
          </div>
        </div>
      </div>
      <small class="text-muted">Jenis | Nomor | Tgl Ambil | Tgl Hasil | Hasil</small><br>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahSpesimen()"><i class="fa fa-plus"></i> Tambah Spesimen</button>
    </div>

    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-hospital-o"></i> <b>I. Rawat Inap / RS</b></div>
      <small class="text-muted">Nama RS/Klinik | Tanggal Masuk | Keterangan</small>
      <div id="tbl-rawat-inap">
        <div class="row rawat-row" style="margin-bottom:6px">
          <div class="col-sm-5">
            <input type="text" name="rs_nama[]" class="form-control input-sm" placeholder="Nama RS/Klinik" value="<?=fv($v,'nama_rs')?>">
          </div>
          <div class="col-sm-3">
            <input type="date" name="rs_tgl[]" class="form-control input-sm" value="<?=fv($v,'tgl_masuk_rs')?>">
          </div>
          <div class="col-sm-3">
            <input type="text" name="rs_ket[]" class="form-control input-sm" placeholder="Keterangan">
          </div>
          <div class="col-sm-1">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.rawat-row').remove()"><i class="fa fa-times"></i></button>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahRawatInap()"><i class="fa fa-plus"></i> Tambah RS/Klinik</button>
    </div>
    <!-- ANGGOTA SERUMAH -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-users"></i> <b>J. Anggota Serumah</b></div>
      <div class="row" style="margin-bottom:8px">
        <div class="col-sm-3">
          <div class="form-group">
            <label style="font-size:12px">Jumlah Anggota Serumah (orang)</label>
            <input type="number" name="jumlah_anggota_serumah" class="form-control input-sm" min="0" max="30" value="<?=fv($v,'jumlah_anggota_serumah')?>">
          </div>
        </div>
        <?php if(in_array($id_penyakit, array(11,14))): ?>
        <div class="col-sm-9">
          <label style="font-size:12px">Tempat Kerja Anggota Serumah yang Berisiko</label>
          <div class="row">
            <?php
            $tempat_kerja_risiko = array(
              11 => array('rs_klinik'=>'RS/Klinik','lab'=>'Laboratorium','veterinarian'=>'Veterinarian','peternak_unggas'=>'Peternak Unggas','peternak_babi'=>'Peternak Babi','pasar_unggas'=>'Pasar Unggas/Babi'),
              14 => array('rs_klinik'=>'RS/Klinik','lab'=>'Laboratorium','veterinarian'=>'Veterinarian','peternakan'=>'Peternakan Hewan','pasar_hewan'=>'Pasar Hewan'),
            );
            $opts_tk = isset($tempat_kerja_risiko[$id_penyakit]) ? $tempat_kerja_risiko[$id_penyakit] : array();
            $saved_tk = array();
            if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']=='as_tempat_risiko') { $saved_tk=explode(',',$ed['var_value']); break; } }
            foreach($opts_tk as $tk=>$tl):
            ?>
            <div class="col-sm-4" style="margin-bottom:4px">
              <div class="checkbox" style="margin:0">
                <label style="font-size:11px">
                  <input type="checkbox" name="as_tempat_risiko[]" value="<?=$tk?>" <?=in_array($tk,$saved_tk)?'checked':''?>>
                  <?=$tl?>
                </label>
              </div>
            </div>
            <?php endforeach; ?>
            <input type="hidden" name="dkey[]" value="as_tempat_risiko">
            <input type="hidden" name="dlabel[]" value="Tempat kerja anggota serumah berisiko">
            <input type="hidden" name="dsub[]" value="Anggota Serumah">
            <input type="hidden" name="dtype[]" value="text">
            <input type="hidden" name="dval[]" id="as_tempat_risiko_val" value="<?=isset($saved_tk)?implode(',',$saved_tk):''?>">
          </div>
        </div>
        <?php endif; ?>
      </div>
      <div id="tbl-anggota">
        <?php foreach($anggota as $idx => $as): ?>
        <div class="row anggota-row" style="margin-bottom:6px">
          <div class="col-sm-5"><input type="text" name="as_nama[]" class="form-control input-sm" placeholder="Nama" value="<?=htmlspecialchars($as['nama'])?>"></div>
          <div class="col-sm-5"><input type="text" name="as_tempat[]" class="form-control input-sm" placeholder="Tempat Kerja" value="<?=htmlspecialchars($as['tempat_kerja'])?>"></div>
          <div class="col-sm-2"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.anggota-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($anggota)): ?>
        <div class="row anggota-row" style="margin-bottom:6px">
          <div class="col-sm-5"><input type="text" name="as_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-5"><input type="text" name="as_tempat[]" class="form-control input-sm" placeholder="Tempat Kerja"></div>
          <div class="col-sm-2"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.anggota-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
        <?php endif; ?>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahAnggota()"><i class="fa fa-plus"></i> Tambah Anggota</button>
    </div>
    <!-- KONTAK PNEUMONIA (khusus Avian) -->
    <?php if($id_penyakit==11): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-user-md"></i> <b>K. Kontak dengan Penderita Pneumonia</b></div>
      <div id="tbl-kontak-pn">
        <?php foreach($kontak_pn as $idx => $kp): ?>
        <div class="row kontak-pn-row" style="margin-bottom:6px">
          <div class="col-sm-2"><input type="text" name="kp_nama[]" class="form-control input-sm" placeholder="Nama" value="<?=htmlspecialchars($kp['nama'])?>"></div>
          <div class="col-sm-1"><input type="number" name="kp_umur[]" class="form-control input-sm" placeholder="Umur" value="<?=htmlspecialchars($kp['umur'])?>"></div>
          <div class="col-sm-2"><input type="text" name="kp_hub[]" class="form-control input-sm" placeholder="Hub. Penderita" value="<?=htmlspecialchars($kp['hub_penderita'])?>"></div>
          <div class="col-sm-2"><input type="date" name="kp_tgl_awal[]" class="form-control input-sm" value="<?=htmlspecialchars($kp['tgl_kontak_awal'])?>"></div>
          <div class="col-sm-2"><input type="date" name="kp_tgl_akhir[]" class="form-control input-sm" value="<?=htmlspecialchars($kp['tgl_kontak_akhir'])?>"></div>
          <div class="col-sm-2"><input type="text" name="kp_status[]" class="form-control input-sm" placeholder="Status Flu" value="<?=htmlspecialchars($kp['status_flu'])?>"></div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.kontak-pn-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($kontak_pn)): ?>
        <div class="row kontak-pn-row" style="margin-bottom:6px">
          <div class="col-sm-2"><input type="text" name="kp_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-1"><input type="number" name="kp_umur[]" class="form-control input-sm" placeholder="Umur"></div>
          <div class="col-sm-2"><input type="text" name="kp_hub[]" class="form-control input-sm" placeholder="Hub. Penderita"></div>
          <div class="col-sm-2"><input type="date" name="kp_tgl_awal[]" class="form-control input-sm"></div>
          <div class="col-sm-2"><input type="date" name="kp_tgl_akhir[]" class="form-control input-sm"></div>
          <div class="col-sm-2"><input type="text" name="kp_status[]" class="form-control input-sm" placeholder="Status Flu"></div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.kontak-pn-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
        <?php endif; ?>
      </div>
      <small class="text-muted">Nama | Umur | Hub. Penderita | Tgl Kontak Awal | Tgl Kontak Akhir | Status Flu</small><br>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahKontakPN()"><i class="fa fa-plus"></i> Tambah Kontak</button>
    </div>
    <?php endif; ?>
    <!-- KONTAK GEJALA SAMA (Avian - terpisah dari Kontak Pneumonia) -->
    <?php if($id_penyakit==11): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-users"></i> <b>L. Kontak Gejala Sama (Keluarga/Tetangga Bergejala)</b></div>
      <small class="text-muted">Nama | Umur | Alamat | Hubungan | Tgl Kontak | Status Flu Burung</small>
      <div id="tbl-kontak-gs">
        <div class="row kontak-gs-row" style="margin-bottom:6px">
          <div class="col-sm-2"><input type="text" name="kg_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-1"><input type="number" name="kg_umur[]" class="form-control input-sm" placeholder="Umur"></div>
          <div class="col-sm-3"><input type="text" name="kg_alamat[]" class="form-control input-sm" placeholder="Alamat"></div>
          <div class="col-sm-2"><input type="text" name="kg_hub[]" class="form-control input-sm" placeholder="Hubungan"></div>
          <div class="col-sm-2"><input type="date" name="kg_tgl[]" class="form-control input-sm"></div>
          <div class="col-sm-1"><input type="text" name="kg_status[]" class="form-control input-sm" placeholder="Status"></div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.kontak-gs-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahKontakGS()"><i class="fa fa-plus"></i> Tambah</button>
    </div>
    <?php endif; ?>

    <!-- KEBIASAAN RESPONDEN LEPTO -->
    <?php if($id_penyakit==26): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-clipboard"></i> <b>M. Kebiasaan Responden (Faktor Risiko Leptospirosis)</b></div>
      <?php
      $kb_fields = array(
        array('key'=>'kb_aktivitas_air',    'label'=>'A1. Bekerja/beraktivitas di sawah, ladang, kebun', 'sub'=>'A'),
        array('key'=>'kb_renang_sungai',    'label'=>'A2. Berenang/mandi di sungai/danau', 'sub'=>'A'),
        array('key'=>'kb_banjir',           'label'=>'A3. Tinggal/beraktivitas di daerah banjir', 'sub'=>'A'),
        array('key'=>'kb_genangan_air',     'label'=>'A4. Kontak dengan genangan air/lumpur', 'sub'=>'A'),
        array('key'=>'kb_parit_selokan',    'label'=>'A5. Tinggal dekat parit/selokan yang kotor', 'sub'=>'A'),
        array('key'=>'kb_air_tercemar',     'label'=>'A6. Minum/gunakan air yang mungkin tercemar', 'sub'=>'A'),
        array('key'=>'kb_kontak_hewan',     'label'=>'B1. Kontak langsung dengan hewan (tikus/sapi/babi/anjing)', 'sub'=>'B'),
        array('key'=>'kb_apd',              'label'=>'B2. Menggunakan APD (sepatu boot/sarung tangan) saat bekerja', 'sub'=>'B'),
        array('key'=>'kb_cuci_tangan',      'label'=>'C1. Cuci tangan sebelum makan', 'sub'=>'C'),
        array('key'=>'kb_cuci_luka',        'label'=>'C2. Merawat luka/lecet dengan benar', 'sub'=>'C'),
        array('key'=>'kb_makan_sembarangan','label'=>'C3. Makan di tempat yang tidak terlindung', 'sub'=>'C'),
        array('key'=>'kb_minum_mentah',     'label'=>'C4. Minum air mentah/tidak dimasak', 'sub'=>'C'),
        array('key'=>'kb_tikus_rumah',      'label'=>'D1. Ada tikus di dalam rumah/dapur', 'sub'=>'D'),
        array('key'=>'kb_makanan_terbuka',  'label'=>'D2. Menyimpan makanan tidak tertutup/terlindung', 'sub'=>'D'),
        array('key'=>'kb_sampah_terbuka',   'label'=>'D3. Membuang sampah sembarangan di sekitar rumah', 'sub'=>'D'),
        array('key'=>'kb_drainase_buruk',   'label'=>'D4. Drainase/saluran air di sekitar rumah buruk', 'sub'=>'D'),
      );
      $kb_sub_labels = array('A'=>'A. Aktivitas Berhubungan Air', 'B'=>'B. Kontak & APD', 'C'=>'C. Personal Higiene', 'D'=>'D. Ketersediaan Pangan & Sanitasi');
      $kb_sub_cur = '';
      foreach($kb_fields as $kb):
        if($kb['sub'] != $kb_sub_cur):
          if($kb_sub_cur) echo '</div>';
          echo '<div style="margin-bottom:10px"><div style="font-weight:600;font-size:12px;color:#1F4E79;margin:8px 0 4px">'.$kb_sub_labels[$kb['sub']].'</div>';
          $kb_sub_cur = $kb['sub'];
        endif;
        // Ambil nilai EAV yang sudah tersimpan
        $kb_val = '';
        if(!empty($eav_data)) {
          foreach($eav_data as $ed) {
            if($ed['var_key']==$kb['key']) { $kb_val=$ed['var_value']; break; }
          }
        }
      ?>
      <div class="row" style="margin-bottom:4px">
        <div class="col-sm-8" style="font-size:12px;padding-top:6px"><?=htmlspecialchars($kb['label'])?></div>
        <div class="col-sm-4">
          <input type="hidden" name="dkey[]" value="<?=$kb['key']?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($kb['label'])?>">
          <input type="hidden" name="dsub[]" value="Kebiasaan Responden Lepto">
          <input type="hidden" name="dtype[]" value="radio">
          <select name="dval[]" class="form-control input-sm" style="width:150px">
            <option value="">-- Pilih --</option>
            <option value="ya" <?=$kb_val=='ya'?'selected':''?>>Ya</option>
            <option value="tidak" <?=$kb_val=='tidak'?'selected':''?>>Tidak</option>
            <option value="tidak_tahu" <?=$kb_val=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
          </select>
        </div>
      </div>
      <?php endforeach; echo '</div>'; ?>
    </div>
    <?php endif; ?>
    <!-- KONTAK PENYELIDIKAN & TIM PE -->

    <!-- KETERANGAN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-sticky-note"></i> <b>N. Keterangan Lain</b></div>
      <div class="form-group">
        <textarea name="ket_lain" class="form-control" rows="3" placeholder="Keterangan tambahan..."><?=fv($v,'ket_lain')?></textarea>
      </div>
    </div>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-phone"></i> <b>O. Kontak Penyelidikan</b></div>
      <small class="text-muted">Narasumber (pejabat/petugas/dokter) yang dihubungi saat penyelidikan</small>
      <div id="tbl-kontak-pe">
        <div class="row kontak-pe-row" style="margin-bottom:6px">
          <div class="col-sm-4"><input type="text" name="kpe_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-4"><input type="text" name="kpe_jabatan[]" class="form-control input-sm" placeholder="Jabatan/Kantor/Alamat"></div>
          <div class="col-sm-3"><input type="text" name="kpe_telp[]" class="form-control input-sm" placeholder="Telp/HP"></div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.kontak-pe-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahKontakPE()"><i class="fa fa-plus"></i> Tambah</button>
    </div>

    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-users"></i> <b>P. Tim Penyelidikan Epidemiologi</b></div>
      <small class="text-muted">Anggota tim PE yang terlibat dalam penyelidikan</small>
      <div id="tbl-tim-pe">
        <?php for($ti=0;$ti<3;$ti++): ?>
        <div class="row tim-pe-row" style="margin-bottom:6px">
          <div class="col-sm-4"><input type="text" name="tpe_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-4"><input type="text" name="tpe_kantor[]" class="form-control input-sm" placeholder="Kantor/Instansi"></div>
          <div class="col-sm-3"><input type="text" name="tpe_telp[]" class="form-control input-sm" placeholder="Telp/HP"></div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.tim-pe-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
        <?php endfor; ?>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahTimPE()"><i class="fa fa-plus"></i> Tambah</button>
    </div>

    <!-- AVIAN: Kunjungan Wabah + Matriks Kontak Unggas -->
    <?php if($id_penyakit==11): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-map-marker"></i> <b>Q. Riwayat Kunjungan & Kontak Unggas (Avian)</b></div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Kunjungan 14 hari terakhir ke daerah wabah kematian unggas?</label>
            <input type="hidden" name="dkey[]" value="av_kunjungan_wabah">
            <input type="hidden" name="dlabel[]" value="Kunjungan 14 hari ke daerah wabah unggas">
            <input type="hidden" name="dsub[]" value="Riwayat Avian">
            <input type="hidden" name="dtype[]" value="select">
            <?php
            $av_kunjungan = '';
            if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']=='av_kunjungan_wabah') { $av_kunjungan=$ed['var_value']; break; } }
            ?>
            <select name="dval[]" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="pernah" <?=$av_kunjungan=='pernah'?'selected':''?>>Pernah</option>
              <option value="tidak_pernah" <?=$av_kunjungan=='tidak_pernah'?'selected':''?>>Tidak Pernah</option>
              <option value="tidak_jelas" <?=$av_kunjungan=='tidak_jelas'?'selected':''?>>Tidak Jelas</option>
            </select>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label>Keterangan Kunjungan</label>
            <input type="hidden" name="dkey[]" value="av_kunjungan_ket">
            <input type="hidden" name="dlabel[]" value="Keterangan kunjungan daerah wabah unggas">
            <input type="hidden" name="dsub[]" value="Riwayat Avian">
            <input type="hidden" name="dtype[]" value="text">
            <?php
            $av_kunjungan_ket = '';
            if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']=='av_kunjungan_ket') { $av_kunjungan_ket=$ed['var_value']; break; } }
            ?>
            <input type="text" name="dval[]" class="form-control" placeholder="Lokasi, tanggal, keterangan" value="<?=htmlspecialchars($av_kunjungan_ket)?>">
          </div>
        </div>
      </div>
      <!-- Matriks Kontak Unggas -->
      <div class="form-group">
        <label><b>Matriks Kontak Unggas 7 Hari Terakhir</b></label>
        <table class="table table-bordered table-condensed" style="font-size:12px">
          <thead style="background:#2c3e50;color:#fff">
            <tr>
              <th>Jenis Unggas</th>
              <th>Kondisi Sehat</th>
              <th>Kondisi Sakit</th>
              <th>Kondisi Mati</th>
              <th>Jenis Kontak</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $unggas_list = array('ayam'=>'Ayam','bebek'=>'Bebek','puyuh'=>'Puyuh','burung'=>'Burung','babi'=>'Babi');
          $kontak_types = array('tidak_ada'=>'Tidak Ada','tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari');
          foreach($unggas_list as $uk=>$ul):
            $val_sehat = $val_sakit = $val_mati = $val_kontak = '';
            if(!empty($eav_data)) foreach($eav_data as $ed) {
              if($ed['var_key']=='av_ung_'.$uk.'_sehat') $val_sehat=$ed['var_value'];
              if($ed['var_key']=='av_ung_'.$uk.'_sakit') $val_sakit=$ed['var_value'];
              if($ed['var_key']=='av_ung_'.$uk.'_mati')  $val_mati=$ed['var_value'];
              if($ed['var_key']=='av_ung_'.$uk.'_kontak') $val_kontak=$ed['var_value'];
            }
          ?>
          <tr>
            <td><b><?=$ul?></b></td>
            <td>
              <input type="hidden" name="dkey[]" value="av_ung_<?=$uk?>_sehat">
              <input type="hidden" name="dlabel[]" value="Kontak <?=$ul?> Sehat">
              <input type="hidden" name="dsub[]" value="Matriks Unggas">
              <input type="hidden" name="dtype[]" value="select">
              <select name="dval[]" class="form-control input-sm">
                <option value="tidak">Tidak</option>
                <option value="ya" <?=$val_sehat=='ya'?'selected':''?>>Ya</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="dkey[]" value="av_ung_<?=$uk?>_sakit">
              <input type="hidden" name="dlabel[]" value="Kontak <?=$ul?> Sakit">
              <input type="hidden" name="dsub[]" value="Matriks Unggas">
              <input type="hidden" name="dtype[]" value="select">
              <select name="dval[]" class="form-control input-sm">
                <option value="tidak">Tidak</option>
                <option value="ya" <?=$val_sakit=='ya'?'selected':''?>>Ya</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="dkey[]" value="av_ung_<?=$uk?>_mati">
              <input type="hidden" name="dlabel[]" value="Kontak <?=$ul?> Mati">
              <input type="hidden" name="dsub[]" value="Matriks Unggas">
              <input type="hidden" name="dtype[]" value="select">
              <select name="dval[]" class="form-control input-sm">
                <option value="tidak">Tidak</option>
                <option value="ya" <?=$val_mati=='ya'?'selected':''?>>Ya</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="dkey[]" value="av_ung_<?=$uk?>_kontak">
              <input type="hidden" name="dlabel[]" value="Jenis Kontak <?=$ul?>">
              <input type="hidden" name="dsub[]" value="Matriks Unggas">
              <input type="hidden" name="dtype[]" value="select">
              <select name="dval[]" class="form-control input-sm">
                <?php foreach($kontak_types as $kv=>$kl): ?>
                <option value="<?=$kv?>" <?=$val_kontak==$kv?'selected':''?>><?=$kl?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- AVIAN: Pemeriksaan Lingkungan Rumah -->
    <?php if($id_penyakit==11): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-home"></i> <b>R. Pemeriksaan Lingkungan Rumah (Avian)</b></div>
      <div class="row">
        <?php
        $lingk_avian = array(
          'av_lingk_piaraan'    => 'Ada unggas piaraan di rumah (Ayam/Bebek/Burung/dll)',
          'av_lingk_peternakan' => 'Ada peternakan unggas di sekitar rumah (<100m)',
          'av_lingk_pasar'      => 'Ada pasar unggas hidup di sekitar rumah',
        );
        foreach($lingk_avian as $lk=>$ll):
          $lv = '';
          if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']==$lk) { $lv=$ed['var_value']; break; } }
        ?>
        <div class="col-sm-4">
          <div class="form-group">
            <label style="font-size:12px"><?=$ll?></label>
            <input type="hidden" name="dkey[]" value="<?=$lk?>">
            <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($ll)?>">
            <input type="hidden" name="dsub[]" value="Lingkungan Avian">
            <input type="hidden" name="dtype[]" value="select">
            <select name="dval[]" class="form-control input-sm">
              <option value="">-- Pilih --</option>
              <option value="ya" <?=$lv=='ya'?'selected':''?>>Ya</option>
              <option value="tidak" <?=$lv=='tidak'?'selected':''?>>Tidak</option>
              <option value="tidak_tahu" <?=$lv=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
            </select>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="form-group">
        <label style="font-size:12px">Keterangan sumber penularan potensial</label>
        <input type="hidden" name="dkey[]" value="av_lingk_ket">
        <input type="hidden" name="dlabel[]" value="Keterangan lingkungan sumber penularan">
        <input type="hidden" name="dsub[]" value="Lingkungan Avian">
        <input type="hidden" name="dtype[]" value="text">
        <?php
        $av_lingk_ket = '';
        if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']=='av_lingk_ket') { $av_lingk_ket=$ed['var_value']; break; } }
        ?>
        <input type="text" name="dval[]" class="form-control" placeholder="Keterangan tambahan lingkungan" value="<?=htmlspecialchars($av_lingk_ket)?>">
      </div>
    </div>
    <?php endif; ?>

    <!-- ANTHRAKS: Gejala per Tipe Manifestasi -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-stethoscope"></i> <b>S. Gejala per Tipe Manifestasi (Anthraks)</b></div>
      <?php
      $anthrax_gejala = array(
        'Kulit' => array(
          'atx_g_gatal'      => 'Rasa gatal di lokasi kontak',
          'atx_g_vesikel'    => 'Vesikel (gelembung berisi cairan)',
          'atx_g_hemoragik'  => 'Lesi hemoragik',
          'atx_g_eschar'     => 'Eschar (keropeng hitam)',
          'atx_g_sesak_kulit'=> 'Nafas pendek/sesak',
        ),
        'Gastrointestinal' => array(
          'atx_g_mual'       => 'Mual/Muntah',
          'atx_g_sakit_perut'=> 'Sakit perut hebat',
          'atx_g_nafsu'      => 'Tidak nafsu makan',
          'atx_g_konstipasi' => 'Konstipasi',
          'atx_g_gi_berdarah'=> 'Gastroenteritis berdarah',
          'atx_g_hematemesis'=> 'Hematemesis (muntah darah)',
          'atx_g_lemah'      => 'Kelemahan umum',
          'atx_g_demam_gi'   => 'Demam',
          'atx_g_lainnya'    => 'Lain-lain',
        ),
      );
      foreach($anthrax_gejala as $tipe => $gejala_list):
      ?>
      <div style="margin-bottom:10px">
        <div style="font-weight:700;font-size:12px;color:#1F4E79;margin-bottom:6px">Manifestasi <?=$tipe?></div>
        <div class="row">
        <?php foreach($gejala_list as $gk=>$gl):
          $gv = '';
          if(!empty($eav_data)) foreach($eav_data as $ed) { if($ed['var_key']==$gk) { $gv=$ed['var_value']; break; } }
        ?>
          <div class="col-sm-4" style="margin-bottom:6px">
            <label style="font-size:11px;font-weight:normal"><?=$gl?></label>
            <input type="hidden" name="dkey[]" value="<?=$gk?>">
            <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($gl)?>">
            <input type="hidden" name="dsub[]" value="Gejala Anthraks <?=$tipe?>">
            <input type="hidden" name="dtype[]" value="select">
            <select name="dval[]" class="form-control input-sm">
              <option value="">--</option>
              <option value="ya" <?=$gv=='ya'?'selected':''?>>Ya</option>
              <option value="tidak" <?=$gv=='tidak'?'selected':''?>>Tidak</option>
            </select>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ANTHRAKS: Kunjungan 7 hari -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-map-marker"></i> <b>T. Riwayat Kunjungan Daerah Wabah (Anthraks)</b></div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Kunjungan 7 hari terakhir ke daerah wabah kematian hewan?</label>
            <input type="hidden" name="dkey[]" value="atx_kunjungan_wabah">
            <input type="hidden" name="dlabel[]" value="Kunjungan 7 hari ke daerah wabah hewan Anthraks">
            <input type="hidden" name="dsub[]" value="Riwayat Anthraks">
            <input type="hidden" name="dtype[]" value="select">
            <?php $atx_kunjungan=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_kunjungan_wabah'){$atx_kunjungan=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="pernah" <?=$atx_kunjungan=='pernah'?'selected':''?>>Pernah</option>
              <option value="tidak_pernah" <?=$atx_kunjungan=='tidak_pernah'?'selected':''?>>Tidak Pernah</option>
              <option value="tidak_jelas" <?=$atx_kunjungan=='tidak_jelas'?'selected':''?>>Tidak Jelas</option>
            </select>
          </div>
        </div>
        <div class="col-sm-8">
          <div class="form-group">
            <label>Keterangan Kunjungan</label>
            <input type="hidden" name="dkey[]" value="atx_kunjungan_ket">
            <input type="hidden" name="dlabel[]" value="Keterangan kunjungan daerah wabah Anthraks">
            <input type="hidden" name="dsub[]" value="Riwayat Anthraks">
            <input type="hidden" name="dtype[]" value="text">
            <?php $atx_kunjungan_ket=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_kunjungan_ket'){$atx_kunjungan_ket=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" placeholder="Lokasi, tanggal, keterangan" value="<?=htmlspecialchars($atx_kunjungan_ket)?>">
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- LEPTO: Kondisi Lingkungan Rumah -->
    <?php if($id_penyakit==26): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-home"></i> <b>U. Kondisi Lingkungan Rumah (Leptospirosis)</b></div>
      <div class="row">
        <?php
        $lepto_lingk = array(
          'lp_tetangga_sakit'  => 'Ada tetangga/keluarga yang sakit dengan gejala sama',
          'lp_riwayat_banjir'  => 'Riwayat banjir di sekitar rumah',
          'lp_parit_kotor'     => 'Ada parit/selokan kotor di sekitar rumah',
          'lp_ada_tikus'       => 'Ada tikus di dalam/sekitar rumah',
          'lp_hewan_peliharaan'=> 'Ada hewan peliharaan (anjing/sapi/babi/dll)',
          'lp_drainase_buruk'  => 'Drainase/saluran air sekitar rumah buruk',
        );
        foreach($lepto_lingk as $lk=>$ll):
          $lv=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']==$lk){$lv=$ed['var_value'];break;}}
        ?>
        <div class="col-sm-4" style="margin-bottom:8px">
          <label style="font-size:12px"><?=$ll?></label>
          <input type="hidden" name="dkey[]" value="<?=$lk?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($ll)?>">
          <input type="hidden" name="dsub[]" value="Kondisi Lingkungan Lepto">
          <input type="hidden" name="dtype[]" value="select">
          <select name="dval[]" class="form-control input-sm">
            <option value="">-- Pilih --</option>
            <option value="ya" <?=$lv=='ya'?'selected':''?>>Ya</option>
            <option value="tidak" <?=$lv=='tidak'?'selected':''?>>Tidak</option>
            <option value="tidak_tahu" <?=$lv=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
          </select>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label style="font-size:12px">Durasi banjir (hari)</label>
            <input type="hidden" name="dkey[]" value="lp_durasi_banjir">
            <input type="hidden" name="dlabel[]" value="Durasi banjir (hari)">
            <input type="hidden" name="dsub[]" value="Kondisi Lingkungan Lepto">
            <input type="hidden" name="dtype[]" value="text">
            <?php $lp_dur=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='lp_durasi_banjir'){$lp_dur=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control input-sm" placeholder="Contoh: 3 hari" value="<?=htmlspecialchars($lp_dur)?>">
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>



    <!-- LEPTO: Riwayat Kontak Faktor Risiko -->
    <?php if($id_penyakit==26): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-exclamation-triangle"></i> <b>V. Riwayat Kontak Faktor Risiko (Leptospirosis)</b></div>
      <div class="row">
        <?php
        $lepto_risiko = array(
          'lp_rs_hutan_sawah'   => 'Pernah kunjungi hutan/sawah/kebun dalam 2 minggu terakhir',
          'lp_rs_genangan_kerja'=> 'Ada genangan air di tempat kerja',
          'lp_rs_tikus_kerja'   => 'Ada tikus di tempat kerja',
          'lp_rs_kontak_air'    => 'Kontak dengan air/tanah yang mungkin tercemar urin hewan',
        );
        foreach($lepto_risiko as $rk=>$rl):
          $rv=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']==$rk){$rv=$ed['var_value'];break;}}
        ?>
        <div class="col-sm-6" style="margin-bottom:8px">
          <label style="font-size:12px"><?=$rl?></label>
          <input type="hidden" name="dkey[]" value="<?=$rk?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($rl)?>">
          <input type="hidden" name="dsub[]" value="Faktor Risiko Lepto">
          <input type="hidden" name="dtype[]" value="select">
          <select name="dval[]" class="form-control input-sm">
            <option value="">-- Pilih --</option>
            <option value="ya" <?=$rv=='ya'?'selected':''?>>Ya</option>
            <option value="tidak" <?=$rv=='tidak'?'selected':''?>>Tidak</option>
            <option value="tidak_tahu" <?=$rv=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
          </select>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="form-group">
        <label style="font-size:12px">Hewan yang ditemui di tempat kerja/aktivitas</label>
        <input type="hidden" name="dkey[]" value="lp_rs_hewan_kerja">
        <input type="hidden" name="dlabel[]" value="Hewan yang ditemui di tempat kerja">
        <input type="hidden" name="dsub[]" value="Faktor Risiko Lepto">
        <input type="hidden" name="dtype[]" value="text">
        <?php $lp_hw=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='lp_rs_hewan_kerja'){$lp_hw=$ed['var_value'];break;}} ?>
        <input type="text" name="dval[]" class="form-control" placeholder="Contoh: tikus, sapi, babi" value="<?=htmlspecialchars($lp_hw)?>">
      </div>
    </div>
    <?php endif; ?>




    <!-- GHPR: Riwayat Pengobatan -->
    <?php if($id_penyakit==8): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-medkit"></i> <b>W. Riwayat Pengobatan Luka (GHPR)</b></div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Cara Rawat Luka Pertama</label>
            <input type="hidden" name="dkey[]" value="ghpr_cara_rawat_luka">
            <input type="hidden" name="dlabel[]" value="Cara rawat luka pertama">
            <input type="hidden" name="dsub[]" value="Pengobatan GHPR">
            <input type="hidden" name="dtype[]" value="select">
            <?php $ghpr_rawat=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_cara_rawat_luka'){$ghpr_rawat=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="cuci_air" <?=$ghpr_rawat=='cuci_air'?'selected':''?>>Dicuci dengan air mengalir</option>
              <option value="cuci_sabun" <?=$ghpr_rawat=='cuci_sabun'?'selected':''?>>Dicuci dengan sabun</option>
              <option value="antiseptik" <?=$ghpr_rawat=='antiseptik'?'selected':''?>>Diberi antiseptik</option>
              <option value="dibalut" <?=$ghpr_rawat=='dibalut'?'selected':''?>>Dibalut saja</option>
              <option value="tidak_dirawat" <?=$ghpr_rawat=='tidak_dirawat'?'selected':''?>>Tidak dirawat</option>
              <option value="lainnya" <?=$ghpr_rawat=='lainnya'?'selected':''?>>Lainnya</option>
            </select>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Tempat Pengobatan Pertama</label>
            <input type="hidden" name="dkey[]" value="ghpr_tempat_pengobatan">
            <input type="hidden" name="dlabel[]" value="Tempat pengobatan pertama GHPR">
            <input type="hidden" name="dsub[]" value="Pengobatan GHPR">
            <input type="hidden" name="dtype[]" value="text">
            <?php $ghpr_tmpat=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_tempat_pengobatan'){$ghpr_tmpat=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" placeholder="Contoh: Puskesmas, RS, Klinik, Dukun" value="<?=htmlspecialchars($ghpr_tmpat)?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Obat yang Diberikan</label>
            <input type="hidden" name="dkey[]" value="ghpr_obat_diberikan">
            <input type="hidden" name="dlabel[]" value="Obat yang diberikan GHPR">
            <input type="hidden" name="dsub[]" value="Pengobatan GHPR">
            <input type="hidden" name="dtype[]" value="text">
            <?php $ghpr_obat=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_obat_diberikan'){$ghpr_obat=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" placeholder="Nama obat yang diberikan" value="<?=htmlspecialchars($ghpr_obat)?>">
          </div>
        </div>
      </div>
      <!-- Riwayat Kontak Epidemiologis -->
      <div class="row" style="margin-top:10px">
        <div class="col-sm-4">
          <div class="form-group">
            <label>Ada korban gigitan lain dari hewan yang sama?</label>
            <input type="hidden" name="dkey[]" value="ghpr_korban_lain">
            <input type="hidden" name="dlabel[]" value="Korban gigitan lain dari hewan yang sama">
            <input type="hidden" name="dsub[]" value="Epidemiologi GHPR">
            <input type="hidden" name="dtype[]" value="select">
            <?php $ghpr_korban=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_korban_lain'){$ghpr_korban=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="ya" <?=$ghpr_korban=='ya'?'selected':''?>>Ya</option>
              <option value="tidak" <?=$ghpr_korban=='tidak'?'selected':''?>>Tidak</option>
              <option value="tidak_tahu" <?=$ghpr_korban=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
            </select>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Jumlah hewan yang menggigit</label>
            <input type="hidden" name="dkey[]" value="ghpr_jumlah_hewan">
            <input type="hidden" name="dlabel[]" value="Jumlah hewan yang menggigit">
            <input type="hidden" name="dsub[]" value="Epidemiologi GHPR">
            <input type="hidden" name="dtype[]" value="text">
            <?php $ghpr_jml=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_jumlah_hewan'){$ghpr_jml=$ed['var_value'];break;}} ?>
            <input type="text" name="dval[]" class="form-control" placeholder="Jumlah hewan" value="<?=htmlspecialchars($ghpr_jml)?>">
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Kasus hewan penular rabies sebulan terakhir di sekitar?</label>
            <input type="hidden" name="dkey[]" value="ghpr_kasus_hewan_sekitar">
            <input type="hidden" name="dlabel[]" value="Kasus hewan penular rabies sebulan terakhir">
            <input type="hidden" name="dsub[]" value="Epidemiologi GHPR">
            <input type="hidden" name="dtype[]" value="select">
            <?php $ghpr_kasus=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='ghpr_kasus_hewan_sekitar'){$ghpr_kasus=$ed['var_value'];break;}} ?>
            <select name="dval[]" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="ya" <?=$ghpr_kasus=='ya'?'selected':''?>>Ya</option>
              <option value="tidak" <?=$ghpr_kasus=='tidak'?'selected':''?>>Tidak</option>
              <option value="tidak_tahu" <?=$ghpr_kasus=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ANTHRAKS: Pemeriksaan Lingkungan Rumah -->
    <?php if($id_penyakit==14): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-home"></i> <b>X. Pemeriksaan Lingkungan Rumah (Anthraks)</b></div>
      <div class="row">
        <?php
        $lingk_anthrax = array(
          'atx_lingk_piaraan'    => 'Ada hewan piaraan di rumah (kambing/sapi/kuda/dll)',
          'atx_lingk_peternakan' => 'Ada peternakan hewan di sekitar rumah',
          'atx_lingk_pasar'      => 'Ada pasar hewan di sekitar rumah',
        );
        foreach($lingk_anthrax as $lk=>$ll):
          $lv=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']==$lk){$lv=$ed['var_value'];break;}}
        ?>
        <div class="col-sm-4" style="margin-bottom:8px">
          <label style="font-size:12px"><?=$ll?></label>
          <input type="hidden" name="dkey[]" value="<?=$lk?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($ll)?>">
          <input type="hidden" name="dsub[]" value="Lingkungan Anthraks">
          <input type="hidden" name="dtype[]" value="select">
          <select name="dval[]" class="form-control input-sm">
            <option value="">-- Pilih --</option>
            <option value="ya" <?=$lv=='ya'?'selected':''?>>Ya</option>
            <option value="tidak" <?=$lv=='tidak'?'selected':''?>>Tidak</option>
            <option value="tidak_tahu" <?=$lv=='tidak_tahu'?'selected':''?>>Tidak Tahu</option>
          </select>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="form-group">
        <label style="font-size:12px">Keterangan sumber penularan potensial</label>
        <input type="hidden" name="dkey[]" value="atx_lingk_ket">
        <input type="hidden" name="dlabel[]" value="Keterangan lingkungan sumber penularan Anthraks">
        <input type="hidden" name="dsub[]" value="Lingkungan Anthraks">
        <input type="hidden" name="dtype[]" value="text">
        <?php $atx_lket=''; if(!empty($eav_data)) foreach($eav_data as $ed){if($ed['var_key']=='atx_lingk_ket'){$atx_lket=$ed['var_value'];break;}} ?>
        <input type="text" name="dval[]" class="form-control" placeholder="Keterangan tambahan lingkungan" value="<?=htmlspecialchars($atx_lket)?>">
      </div>
    </div>
    <?php endif; ?>

    <!-- KONTAK KASUS LAIN (Lepto + Anthraks) -->
    <?php if(in_array($id_penyakit, array(26,14))): ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-users"></i> <b>Y. Kontak Kasus Lain / Gejala Sama</b></div>
      <small class="text-muted">Nama | Umur | Alamat | Hubungan | Tgl Kontak | Status (suspek/konfirmasi/tidak tahu)</small>
      <div id="tbl-kontak-kasus">
        <div class="row kontak-kasus-row" style="margin-bottom:6px">
          <div class="col-sm-2"><input type="text" name="kk_nama[]" class="form-control input-sm" placeholder="Nama"></div>
          <div class="col-sm-1"><input type="number" name="kk_umur[]" class="form-control input-sm" placeholder="Umur"></div>
          <div class="col-sm-3"><input type="text" name="kk_alamat[]" class="form-control input-sm" placeholder="Alamat"></div>
          <div class="col-sm-2"><input type="text" name="kk_hub[]" class="form-control input-sm" placeholder="Hub. Penderita"></div>
          <div class="col-sm-2"><input type="date" name="kk_tgl[]" class="form-control input-sm"></div>
          <div class="col-sm-1">
            <select name="kk_status[]" class="form-control input-sm">
              <option value="">--</option>
              <option value="suspek">Suspek</option>
              <option value="konfirmasi">Konfirmasi</option>
              <option value="tidak_tahu">Tidak Tahu</option>
            </select>
          </div>
          <div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('.kontak-kasus-row').remove()"><i class="fa fa-times"></i></button></div>
        </div>
      </div>
      <button type="button" class="btn btn-xs btn-default" onclick="tambahKontakKasus()"><i class="fa fa-plus"></i> Tambah</button>
    </div>
    <?php endif; ?>

    <!-- KONTAK HEWAN -->
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-paw"></i> <b>Z. Riwayat Kontak Hewan</b></div>
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Riwayat Kontak Hewan</label>
            <select name="riwayat_kontak_hewan" class="form-control" onchange="toggleKontakHewan(this.value)">
              <option value="">-- Tidak Diketahui --</option>
              <option value="1" <?=fv($v,'riwayat_kontak_hewan')=='1'?'selected':''?>>Ya</option>
              <option value="0" <?=fv($v,'riwayat_kontak_hewan')=='0'?'selected':''?>>Tidak</option>
            </select>
            <script>
            function toggleKontakHewan(val){
                $('#detail-kontak-hewan').toggle(val === '1');
            }
            $(function(){ toggleKontakHewan('<?=fv($v,"riwayat_kontak_hewan")?>'); });
            </script>
          </div>
        </div>
<!-- jenis_hewan dihapus, gunakan dp_hpr di variabel tambahan -->
      </div>
      <div class="row" id="detail-kontak-hewan" style="display:<?=fv($v,'riwayat_kontak_hewan')=='1'?'block':'none'?>">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tanggal Kontak</label>
            <input type="date" name="tgl_kontak" id="f_tgl_kontak" class="form-control" value="<?=fv($v,'tgl_kontak')?>" onblur="cekTglKontak()">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Lokasi Kontak</label>
            <select name="lokasi_kontak" class="form-control">
              <option value="">-- Pilih Lokasi --</option>
              <option value="Rumah" <?=fv($v,'lokasi_kontak')=='Rumah'?'selected':''?>>Rumah</option>
              <option value="Peternakan" <?=fv($v,'lokasi_kontak')=='Peternakan'?'selected':''?>>Peternakan</option>
              <option value="Pasar Hewan" <?=fv($v,'lokasi_kontak')=='Pasar Hewan'?'selected':''?>>Pasar Hewan</option>
              <option value="Sawah/Kebun" <?=fv($v,'lokasi_kontak')=='Sawah/Kebun'?'selected':''?>>Sawah/Kebun</option>
              <option value="Hutan" <?=fv($v,'lokasi_kontak')=='Hutan'?'selected':''?>>Hutan</option>
              <option value="Sungai/Danau" <?=fv($v,'lokasi_kontak')=='Sungai/Danau'?'selected':''?>>Sungai/Danau</option>
              <option value="Tempat Kerja" <?=fv($v,'lokasi_kontak')=='Tempat Kerja'?'selected':''?>>Tempat Kerja</option>
              <option value="Lainnya" <?=fv($v,'lokasi_kontak')=='Lainnya'?'selected':''?>>Lainnya</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
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
        <div class="col-sm-3" id="detail-vaksinasi">
          <div class="form-group">
            <label>Jenis Vaksin</label>
            <input type="text" name="jenis_vaksin" class="form-control" value="<?=fv($v,'jenis_vaksin')?>">
          </div>
        </div>
        <?php if($id_penyakit==8): // GHPR only ?>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Tgl Vaksinasi Terakhir Hewan</label>
            <input type="date" name="tgl_vaksinasi_hewan" class="form-control" value="<?=fv($v,'tgl_vaksinasi_hewan')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Nama Pemilik Hewan</label>
            <input type="text" name="nama_pemilik_hewan" class="form-control" value="<?=fv($v,'nama_pemilik_hewan')?>">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Alamat Pemilik Hewan</label>
            <input type="text" name="alamat_pemilik_hewan" class="form-control" value="<?=fv($v,'alamat_pemilik_hewan')?>">
          </div>
        </div>
        <?php endif; ?>
        <?php // oseltamivir dipindah ke seksi Klinis ?>
      </div>
      <?php if($id_penyakit==11): ?>
      <div class="row">
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
      </div>
      <?php endif; ?>

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
    <?php $rows = array_filter($rows, function($d){ return !in_array($d['var_key'], array('lokasi_gigitan','dp_satuan_hpr')); }); if(empty($rows)) continue; ?>
    <?php if($submodule=='Gejala Lepto' && $id_penyakit==26) continue; ?>
    <?php if($submodule === 'Gejala GHPR') continue; // Sudah dirender inline di section F ?>
    <?php if($id_penyakit==14 && in_array($submodule, array(
        'Status Laporan','Klinis Anthraks','Riwayat Anthraks',
        'Gejala Anthraks Kulit','Gejala Anthraks Gastrointestinal',
        'Tata Laksana Anthraks','Kontak Anthraks','Timeline Inkubasi Anthraks',
        'Lab Anthraks','Spesimen Anthraks','Lingkungan Anthraks'
    ))) continue; // Sudah dirender di section A,E,F,S,T,X ?>
    <div class="form-section">
      <div class="form-section-title"><i class="fa fa-list-alt"></i> <?=htmlspecialchars($submodule)?></div>
      <?php if(strpos($submodule,'Gejala')===0): ?>
      <div class="row">
      <?php foreach($rows as $i => $d): ?>
        <div class="col-sm-3" style="padding:4px 15px">
          <input type="hidden" name="dsub[]" value="<?=htmlspecialchars($d['submodule'])?>">
          <input type="hidden" name="dkey[]" value="<?=htmlspecialchars($d['var_key'])?>">
          <input type="hidden" name="dtype[]" value="<?=htmlspecialchars($d['var_type'])?>">
          <input type="hidden" name="dlabel[]" value="<?=htmlspecialchars($d['var_label'])?>">
          <label style="font-weight:normal;margin:0">
            <input type="checkbox" name="dval[]" value="Ya" <?=$d['var_value']=='Ya'?'checked':'' ?> style="margin-right:5px">
            <?=htmlspecialchars($d['var_label'])?>
          </label>
        </div>
      <?php endforeach; ?>
      </div>
      <?php else: ?>
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
            /* lokasi_gigitan dihapus - pakai dp_lokasi */
            'dp_kategori_hpr' => array(
              'peliharaan'=>'Peliharaan',
              'liar'=>'Liar',
              'tidak_diketahui'=>'Tidak Diketahui',
            ),
            'dp_sabun' => array(
              'ya_sabun'=>'Ya, dengan sabun',
              'ya_air'=>'Ya, dengan air saja',
              'tidak'=>'Tidak dicuci',
            ),
            'dp_sar' => array(
              'var'=>'VAR saja',
              'var_sar'=>'VAR + SAR',
              'tidak'=>'Tidak diberikan',
            ),
            'riwayat_gigitan_sebelumnya' => array(
              'Ya'=>'Ya',
              'Tidak'=>'Tidak',
              'Tidak Diketahui'=>'Tidak Diketahui',
            ),
            'hewan_dibunuh' => array(
              'Ya'=>'Ya, dibunuh/mati',
              'Tidak'=>'Tidak, masih hidup',
              'Tidak Diketahui'=>'Tidak Diketahui',
            ),
            'vaksin_hewan' => array(
              'Ya'=>'Ya, sudah divaksin',
              'Tidak'=>'Belum divaksin',
              'Tidak Diketahui'=>'Tidak Diketahui',
            ),
            'dp_hpr' => array(
              'anjing'=>'Anjing',
              'kucing'=>'Kucing',
              'kera'=>'Kera/Monyet',
              'kelelawar'=>'Kelelawar',
              'hewan_lainnya'=>'Lainnya',
              // legacy values (data lama)
              'anjing_liar'=>'Anjing Liar (lama)',
              'kucing_peliharaan'=>'Kucing Peliharaan (lama)',
              'kucing_liar'=>'Kucing Liar (lama)',
              'monyet_liar'=>'Monyet Liar (lama)',
              'monye_peliharaan'=>'Monyet Peliharaan (lama)',
            ),
/* dp_satuan_hpr dihapus - duplikasi dengan dp_hpr */
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
            // Antraks baru
            'atx_status_laporan' => array('Suspek Antraks'=>'Suspek Antraks','Probable Antraks'=>'Probable Antraks','Konfirmasi Antraks'=>'Konfirmasi Antraks'),
            'atx_rumor_klb'      => array('Ya'=>'Ya','Tidak'=>'Tidak','Tidak tahu'=>'Tidak tahu'),
            'atx_rawat_inap'     => array('Ya'=>'Ya','Tidak'=>'Tidak'),
            'atx_antibiotik'     => array('Ya'=>'Ya','Tidak'=>'Tidak'),
            'atx_diagnosis_awal' => array(
                'Antraks Kulit (Cutaneous Anthrax)'=>'Antraks Kulit (Cutaneous Anthrax)',
                'Antraks Saluran Cerna (Gastrointestinal Anthrax)'=>'Antraks Saluran Cerna (Gastrointestinal Anthrax)',
                'Antraks Saluran Nafas (Inhalational Anthrax)'=>'Antraks Saluran Nafas (Inhalational Anthrax)',
                'Antraks Injeksi (Injection Anthrax)'=>'Antraks Injeksi (Injection Anthrax)',
                'Lainnya'=>'Lainnya',
            ),
            'atx_diagnosis_akhir' => array(
                'Antraks Kulit (Cutaneous Anthrax)'=>'Antraks Kulit (Cutaneous Anthrax)',
                'Antraks Saluran Cerna (Gastrointestinal Anthrax)'=>'Antraks Saluran Cerna (Gastrointestinal Anthrax)',
                'Antraks Saluran Nafas (Inhalational Anthrax)'=>'Antraks Saluran Nafas (Inhalational Anthrax)',
                'Antraks Injeksi (Injection Anthrax)'=>'Antraks Injeksi (Injection Anthrax)',
                'Lainnya'=>'Lainnya',
            ),
            // Matriks Kontak Anthraks
            'ant_status_kontak' => array('tidak'=>'Tidak','suspek'=>'Suspek','probable'=>'Probable','konfirmasi'=>'Konfirmasi','tidak_tahu'=>'Tidak Tahu'),
            'ant_tipe_manifestasi' => array('kulit'=>'Anthraks Kulit (Cutaneous)','gi'=>'Anthraks Gastrointestinal','paru'=>'Anthraks Paru/Inhalasi'),
            'ant_kambing_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'ant_sapi_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'ant_kuda_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'ant_kambing_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            'ant_sapi_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            'ant_kuda_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            // Matriks Kontak Avian
            'av_ayam_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'av_bebek_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'av_puyuh_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'av_babi_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'av_lain_kondisi' => array('Sehat'=>'Sehat','Sakit'=>'Sakit','Mati'=>'Mati','Tidak Kontak'=>'Tidak Kontak'),
            'av_ayam_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            'av_bebek_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            'av_puyuh_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            'av_babi_kontak' => array('tidak_erat'=>'Kontak Tidak Erat','erat'=>'Kontak Erat','sehari_hari'=>'Kontak Sehari-hari'),
            // Lab Lepto
            'lepto_foto_paru' => array('normal'=>'Normal','infiltrat'=>'Infiltrat','tidak_diperiksa'=>'Tidak Diperiksa'),
            'kb_makanan_terbuka' => array(
              'lemari_tertutup'=>'Disimpan di lemari/tempat tertutup',
              'meja_tertutup'=>'Di meja tapi tertutup rapat',
              'meja_terbuka'=>'Di meja terbuka/tidak tertutup',
              'lantai_terbuka'=>'Di lantai/tempat terbuka',
            ),
            'kb_makanan_siap_saji' => array(
              'lemari_tertutup'=>'Disimpan di lemari/tempat tertutup',
              'meja_tertutup'=>'Di meja tapi tertutup rapat',
              'meja_terbuka'=>'Di meja terbuka/tidak tertutup',
              'lantai_terbuka'=>'Di lantai/tempat terbuka',
            ),
            'kb_rawat_luka' => array('plester_kedap'=>'Dibersihkan & Ditutup Plester Kedap Air','plester_biasa'=>'Dibersihkan & Ditutup Plester Biasa','dibersihkan'=>'Dibersihkan Saja (Tanpa Ditutup)','tidak_dirawat'=>'Tidak Dirawat'),
            'lepto_urinalisis' => array(
              'Proteinuria'=>'Proteinuria',
              'Hematuria'=>'Hematuria',
              'Proteinuria dan Hematuria'=>'Proteinuria dan Hematuria',
              'Normal'=>'Normal',
              'Tidak Dilakukan'=>'Tidak Dilakukan',
            ),
            'lepto_rdt' => array(
              'Positif'=>'Positif',
              'Negatif'=>'Negatif',
              'Pending'=>'Pending/Belum Keluar',
              'Tidak Dilakukan'=>'Tidak Dilakukan',
            ),
            'lepto_mat' => array(
              'Positif'=>'Positif',
              'Negatif'=>'Negatif',
              'Pending'=>'Pending/Belum Keluar',
              'Tidak Dilakukan'=>'Tidak Dilakukan',
            ),
            'lepto_pcr' => array(
              'Positif'=>'Positif',
              'Negatif'=>'Negatif',
              'Pending'=>'Pending/Belum Keluar',
              'Tidak Dilakukan'=>'Tidak Dilakukan',
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
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>



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
    // Validasi sebelum submit
    var tgl_pe = $('input[name=tgl_pe]').val();
    var tgl_laporan = $('input[name=tgl_laporan]').val();
    var nik = $('input[name=nik]').val();
    var umur_thn = parseInt($('input[name=umur_thn]').val()) || 0;
    var umur_bln = parseInt($('input[name=umur_bln]').val()) || 0;
    var errors = [];

    if (!tgl_pe) { errors.push('Tanggal PE wajib diisi'); }
    if (tgl_pe && tgl_laporan && tgl_pe > tgl_laporan) {
        errors.push('Tanggal PE tidak boleh lebih dari Tanggal Laporan');
    }
    // Validasi No Epid (wajib untuk non-GHPR)
    var no_epid = $('#f_no_epid').val();
    if($('#f_no_epid').length && (!no_epid || no_epid.length !== 11 || !/^[0-9]+$/.test(no_epid))) {
        errors.push('No. Epid wajib diisi 11 digit angka untuk penyakit ini.');
    }
    if (nik && (nik.length !== 16 || !/^[0-9]+$/.test(nik))) {
        errors.push('NIK harus 16 digit angka (isi 0000000000000000 jika tidak ada NIK)');
    }
    // Validasi gejala minimal 1 (hanya jika ada checkbox gejala di form)
    var totalGejala = $('input[name="dval[]"][type="checkbox"]').length;
    var gejalaChecked = $('input[name="dval[]"]:checked').length;
    if (totalGejala > 0 && gejalaChecked === 0) {
        errors.push('Gejala dan Tanda Sakit wajib diisi - pilih minimal 1 gejala.');
    }
    if (umur_thn > 100) { errors.push('Umur (tahun) tidak boleh lebih dari 100'); }

    // Validasi No HP
    var telp_pasien = $('input[name=telp_pasien]').val();
    var telp_petugas = $('input[name=telp_petugas]').val();
    if (telp_pasien && (!/^[0-9]{10,13}$/.test(telp_pasien))) {
        errors.push('No HP Pasien harus numeric 10-13 digit');
    }
    if (telp_petugas && (!/^[0-9]{10,13}$/.test(telp_petugas))) {
        errors.push('No HP Petugas harus numeric 10-13 digit');
    }
    if (umur_bln < 0 || umur_bln > 11) { errors.push('Umur (bulan) harus antara 0-11'); }
    // Validasi urutan tanggal kasus
    var tgl_bergejala = $('input[name=tgl_bergejala]').val();
    var tgl_masuk_rs  = $('input[name=tgl_masuk_rs]').val();
    var tgl_meninggal = $('input[name=tgl_meninggal]').val();
    if (tgl_bergejala && tgl_masuk_rs && tgl_bergejala > tgl_masuk_rs) errors.push('Tanggal bergejala tidak boleh lebih dari tanggal masuk RS');
    if (tgl_bergejala && tgl_meninggal && tgl_bergejala > tgl_meninggal) errors.push('Tanggal bergejala tidak boleh lebih dari tanggal meninggal');
    if (tgl_masuk_rs && tgl_meninggal && tgl_masuk_rs > tgl_meninggal) errors.push('Tanggal masuk RS tidak boleh lebih dari tanggal meninggal');
    var tgl_ambil = $('input[name=tgl_ambil_sample]').val();
    var tgl_kirim = $('input[name=tgl_kirim_sample]').val();
    var tgl_hasil = $('input[name=tgl_hasil_lab]').val();
    if (tgl_ambil && tgl_kirim && tgl_ambil > tgl_kirim) errors.push('Tanggal ambil spesimen tidak boleh lebih dari tanggal kirim');
    if (tgl_kirim && tgl_hasil && tgl_kirim > tgl_hasil) errors.push('Tanggal kirim tidak boleh lebih dari tanggal hasil lab');

    if (errors.length > 0) {
        alert('Validasi gagal:\n\n' + errors.join('\n'));
        return;
    }

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
// Validasi tanggal real-time saat blur
function cekUrutan() {
    var tgl_bergejala  = $('#tgl_bergejala').val();
    var tgl_sakit      = $('#tgl_sakit').val();
    var tgl_laporan    = $('#tgl_laporan').val();
    var tgl_pe         = $('#tgl_pe').val();
    var tgl_masuk_rs   = $('#tgl_masuk_rs').val();
    var tgl_meninggal  = $('#tgl_meninggal').val();
    var tgl_ambil      = $('input[name=tgl_ambil_sample]').val();
    var tgl_kirim      = $('input[name=tgl_kirim_sample]').val();
    var tgl_hasil      = $('input[name=tgl_hasil_lab]').val();
    var warns = [];

    // Hapus warning lama
    $('.warn-tgl').remove();

    function addWarn(selector, msg) {
        $(selector).after('<small class="warn-tgl text-danger"><i class="fa fa-exclamation-triangle"></i> '+msg+'</small>');
    }

    if (tgl_pe && tgl_laporan && tgl_pe > tgl_laporan)
        addWarn('#tgl_pe', 'Tgl PE tidak boleh lebih dari Tgl Laporan');
    if (tgl_bergejala && tgl_masuk_rs && tgl_bergejala > tgl_masuk_rs)
        addWarn('#tgl_bergejala', 'Tgl Bergejala tidak boleh setelah Tgl Masuk RS');
    if (tgl_masuk_rs && tgl_meninggal && tgl_masuk_rs > tgl_meninggal)
        addWarn('#tgl_masuk_rs', 'Tgl Masuk RS tidak boleh setelah Tgl Meninggal');
    if (tgl_bergejala && tgl_meninggal && tgl_bergejala > tgl_meninggal)
        addWarn('#tgl_bergejala', 'Tgl Bergejala tidak boleh setelah Tgl Meninggal');
    if (tgl_ambil && tgl_kirim && tgl_ambil > tgl_kirim)
        addWarn('input[name=tgl_ambil_sample]', 'Tgl Ambil tidak boleh setelah Tgl Kirim');
    if (tgl_kirim && tgl_hasil && tgl_kirim > tgl_hasil)
        addWarn('input[name=tgl_kirim_sample]', 'Tgl Kirim tidak boleh setelah Tgl Hasil');
}

// Pasang event blur ke semua field tanggal
// Auto-calc umur dari tgl_lahir
$('input[name=tgl_lahir]').on('change', function(){
    var tgl = $(this).val();
    if(!tgl) return;
    var lahir = new Date(tgl);
    var today = new Date();
    var thn = today.getFullYear() - lahir.getFullYear();
    var bln = today.getMonth() - lahir.getMonth();
    var hari = today.getDate() - lahir.getDate();
    if(hari < 0) { bln--; hari += new Date(today.getFullYear(), today.getMonth(), 0).getDate(); }
    if(bln < 0) { thn--; bln += 12; }
    $('input[name=umur_thn]').val(thn >= 0 ? thn : 0);
    $('input[name=umur_bln]').val(bln >= 0 ? bln : 0);
    $('input[name=umur_hari]').val(hari >= 0 ? hari : 0);
});

$(document).on('change', 'input[name="tgl_bergejala"], input[name="tgl_sakit"], input[name="tgl_laporan"], input[name="tgl_pe"], input[name="tgl_masuk_rs"], input[name="tgl_meninggal"], input[name="tgl_ambil_sample"], input[name="tgl_kirim_sample"], input[name="tgl_hasil_lab"]', function(){
    cekUrutan();
});

// Filter input hanya angka untuk NIK dan telp
function onlyNumbers(e) {
    var k = e.which || e.keyCode;
    // Allow: backspace, delete, tab, escape, enter, arrow keys
    if (k==8||k==9||k==13||k==27||k==46||(k>=35&&k<=40)) return true;
    // Allow: ctrl+A, ctrl+C, ctrl+V, ctrl+X
    if (e.ctrlKey && (k==65||k==67||k==86||k==88)) return true;
    // Block non-numeric
    if (k<48||k>57) { e.preventDefault(); return false; }
    return true;
}
$(function(){
    // Apply ke NIK dan semua field telp
    $('input[name="nik"], input[name="telp_pasien"], input[name="telp_petugas"], input[name="telp_kontak_darurat"]')
        .on('keypress', onlyNumbers)
        .on('paste', function(e){
            var text = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            if (!/^[0-9]+$/.test(text)) { e.preventDefault(); }
        });
});

// Validasi umur real-time
function cekUmur() {
    var thn = parseInt($('input[name=umur_thn]').val()) || 0;
    var bln = parseInt($('input[name=umur_bln]').val()) || 0;
    var hari = parseInt($('input[name=umur_hari]').val()) || 0;
    $('.warn-umur').remove();
    if (thn > 100) {
        $('input[name=umur_thn]').after('<small class="warn-umur text-danger"><i class="fa fa-exclamation-triangle"></i> Umur tahun tidak boleh lebih dari 100</small>');
    }
    if (bln > 11) {
        $('input[name=umur_bln]').after('<small class="warn-umur text-danger"><i class="fa fa-exclamation-triangle"></i> Umur bulan harus 0-11</small>');
    }
    if (hari > 30) {
        $('input[name=umur_hari]').after('<small class="warn-umur text-danger"><i class="fa fa-exclamation-triangle"></i> Umur hari harus 0-30</small>');
    }
}
$(document).on('change', 'input[name=umur_thn], input[name=umur_bln], input[name=umur_hari]', cekUmur);

// Set max date = today untuk semua input date
$(function(){
    var today = new Date().toISOString().split('T')[0];
    $('input[type=date]').attr('max', today);

    // Skip logic: detail kontak hewan hanya tampil jika Ya
    function toggleKontakHewan() {
        var val = $('select[name=riwayat_kontak_hewan]').val();
        if (val === '0') {
            $('#detail-kontak-hewan').hide();
        } else {
            $('#detail-kontak-hewan').show();
        }
    }
    $('select[name=riwayat_kontak_hewan]').on('change', toggleKontakHewan);
    toggleKontakHewan();

    // Skip logic: detail riwayat vaksinasi hanya tampil jika riwayat_vaksinasi = Ya
    function toggleVaksinasi() {
        var val = $('select[name=riwayat_vaksinasi]').val();
        if (val === '1') {
            $('#detail-vaksinasi').show();
        } else {
            $('#detail-vaksinasi').hide();
        }
    }
    $('select[name=riwayat_vaksinasi]').on('change', toggleVaksinasi);
    toggleVaksinasi();
    // Validasi tanggal kontak hewan
    window.cekTglKontak = function(){
        var tgl_kontak = $('#f_tgl_kontak').val();
        var tgl_bergejala = $('input[name=tgl_bergejala]').val();
        var tgl_sakit = $('input[name=tgl_sakit]').val();
        if(tgl_kontak && tgl_bergejala && tgl_kontak > tgl_bergejala){
            $('#f_tgl_kontak').css('border-color','#e74c3c');
            alert('Tanggal kontak hewan harus lebih awal dari tanggal mulai bergejala');
        } else if(tgl_kontak && tgl_sakit && tgl_kontak > tgl_sakit){
            $('#f_tgl_kontak').css('border-color','#e74c3c');
            alert('Tanggal kontak hewan harus lebih awal dari tanggal mulai berobat');
        } else {
            $('#f_tgl_kontak').css('border-color','');
        }
    };
});

function toggleDiagLainnya(sel, inputId){
    $('#'+inputId).toggle($(sel).val()==='Lainnya');
}
function tambahRawatInap() {
    $('#tbl-rawat-inap').append('<div class="row rawat-row" style="margin-bottom:6px"><div class="col-sm-5"><input type="text" name="rs_nama[]" class="form-control input-sm" placeholder="Nama RS/Klinik"></div><div class="col-sm-3"><input type="date" name="rs_tgl[]" class="form-control input-sm"></div><div class="col-sm-3"><input type="text" name="rs_ket[]" class="form-control input-sm" placeholder="Keterangan"></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.rawat-row\').remove()"><i class="fa fa-times"></i></button></div></div>');
}
function tambahSpesimen() {
    var idx = $('#tbl-spesimen .spesimen-row').length;
    var opts = '<option value="">-- Jenis --</option><option value="serum_darah">Serum Darah</option><option value="urine">Urine</option><option value="usap_nasofaring">Usap Nasofaring</option><option value="usap_tenggorok">Usap Tenggorok</option><option value="kulit_lesi">Kulit/Lesi</option><option value="jaringan">Jaringan/Eksudat</option><option value="otak_hewan">Otak Hewan (GHPR)</option><option value="lainnya">Lainnya</option>';
    var row = '<div class="row spesimen-row" style="margin-bottom:6px">'
        + '<div class="col-sm-2"><input type="hidden" name="dkey[]" value="sp'+idx+'_jenis"><input type="hidden" name="dlabel[]" value="Jenis Spesimen '+(idx+1)+'"><input type="hidden" name="dsub[]" value="Spesimen Lab"><input type="hidden" name="dtype[]" value="text"><select name="dval[]" class="form-control input-sm">'+opts+'</select></div>'
        + '<div class="col-sm-2"><input type="hidden" name="dkey[]" value="sp'+idx+'_nomor"><input type="hidden" name="dlabel[]" value="Nomor Spesimen '+(idx+1)+'"><input type="hidden" name="dsub[]" value="Spesimen Lab"><input type="hidden" name="dtype[]" value="text"><input type="text" name="dval[]" class="form-control input-sm" placeholder="Nomor"></div>'
        + '<div class="col-sm-2"><input type="hidden" name="dkey[]" value="sp'+idx+'_tgl_ambil"><input type="hidden" name="dlabel[]" value="Tgl Ambil '+(idx+1)+'"><input type="hidden" name="dsub[]" value="Spesimen Lab"><input type="hidden" name="dtype[]" value="date"><input type="date" name="dval[]" class="form-control input-sm"></div>'
        + '<div class="col-sm-2"><input type="hidden" name="dkey[]" value="sp'+idx+'_tgl_hasil"><input type="hidden" name="dlabel[]" value="Tgl Hasil '+(idx+1)+'"><input type="hidden" name="dsub[]" value="Spesimen Lab"><input type="hidden" name="dtype[]" value="date"><input type="date" name="dval[]" class="form-control input-sm"></div>'
        + '<div class="col-sm-2"><input type="hidden" name="dkey[]" value="sp'+idx+'_hasil"><input type="hidden" name="dlabel[]" value="Hasil '+(idx+1)+'"><input type="hidden" name="dsub[]" value="Spesimen Lab"><input type="hidden" name="dtype[]" value="text"><input type="text" name="dval[]" class="form-control input-sm" placeholder="Hasil"></div>'
        + '<div class="col-sm-2"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.spesimen-row\').remove()"><i class="fa fa-times"></i></button></div>'
        + '</div>';
    $('#tbl-spesimen').append(row);
}
function tambahAnggota() {
    $("#tbl-anggota").append('<div class="row anggota-row" style="margin-bottom:6px"><div class="col-sm-5"><input type="text" name="as_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-5"><input type="text" name="as_tempat[]" class="form-control input-sm" placeholder="Tempat Kerja"></div><div class="col-sm-2"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\".anggota-row\").remove()"><i class="fa fa-times"></i></button></div></div>');
}
// Update hidden field for checklist tempat risiko
$(document).on('change', 'input[name="as_tempat_risiko[]"]', function(){
    var vals = [];
    $('input[name="as_tempat_risiko[]"]:checked').each(function(){ vals.push($(this).val()); });
    $('#as_tempat_risiko_val').val(vals.join(','));
});
function tambahKontakKasus() {
    var opts = '<option value="">--</option><option value="suspek">Suspek</option><option value="konfirmasi">Konfirmasi</option><option value="tidak_tahu">Tidak Tahu</option>';
    $('#tbl-kontak-kasus').append('<div class="row kontak-kasus-row" style="margin-bottom:6px"><div class="col-sm-2"><input type="text" name="kk_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-1"><input type="number" name="kk_umur[]" class="form-control input-sm" placeholder="Umur"></div><div class="col-sm-3"><input type="text" name="kk_alamat[]" class="form-control input-sm" placeholder="Alamat"></div><div class="col-sm-2"><input type="text" name="kk_hub[]" class="form-control input-sm" placeholder="Hub. Penderita"></div><div class="col-sm-2"><input type="date" name="kk_tgl[]" class="form-control input-sm"></div><div class="col-sm-1"><select name="kk_status[]" class="form-control input-sm">'+opts+'</select></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.kontak-kasus-row\').remove()"><i class="fa fa-times"></i></button></div></div>');
}
function tambahKontakGS() {
    $('#tbl-kontak-gs').append('<div class="row kontak-gs-row" style="margin-bottom:6px"><div class="col-sm-2"><input type="text" name="kg_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-1"><input type="number" name="kg_umur[]" class="form-control input-sm" placeholder="Umur"></div><div class="col-sm-3"><input type="text" name="kg_alamat[]" class="form-control input-sm" placeholder="Alamat"></div><div class="col-sm-2"><input type="text" name="kg_hub[]" class="form-control input-sm" placeholder="Hubungan"></div><div class="col-sm-2"><input type="date" name="kg_tgl[]" class="form-control input-sm"></div><div class="col-sm-1"><input type="text" name="kg_status[]" class="form-control input-sm" placeholder="Status"></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.kontak-gs-row\').remove()"><i class="fa fa-times"></i></button></div></div>');
}
function tambahKontakPE() {
    $('#tbl-kontak-pe').append('<div class="row kontak-pe-row" style="margin-bottom:6px"><div class="col-sm-4"><input type="text" name="kpe_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-4"><input type="text" name="kpe_jabatan[]" class="form-control input-sm" placeholder="Jabatan/Kantor/Alamat"></div><div class="col-sm-3"><input type="text" name="kpe_telp[]" class="form-control input-sm" placeholder="Telp/HP"></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.kontak-pe-row\').remove()"><i class="fa fa-times"></i></button></div></div>');
}
function tambahTimPE() {
    $('#tbl-tim-pe').append('<div class="row tim-pe-row" style="margin-bottom:6px"><div class="col-sm-4"><input type="text" name="tpe_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-4"><input type="text" name="tpe_kantor[]" class="form-control input-sm" placeholder="Kantor/Instansi"></div><div class="col-sm-3"><input type="text" name="tpe_telp[]" class="form-control input-sm" placeholder="Telp/HP"></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'.tim-pe-row\').remove()"><i class="fa fa-times"></i></button></div></div>');
}
function tambahKontakPN() {
    $("#tbl-kontak-pn").append('<div class="row kontak-pn-row" style="margin-bottom:6px"><div class="col-sm-2"><input type="text" name="kp_nama[]" class="form-control input-sm" placeholder="Nama"></div><div class="col-sm-1"><input type="number" name="kp_umur[]" class="form-control input-sm" placeholder="Umur"></div><div class="col-sm-2"><input type="text" name="kp_hub[]" class="form-control input-sm" placeholder="Hub."></div><div class="col-sm-2"><input type="date" name="kp_tgl_awal[]" class="form-control input-sm"></div><div class="col-sm-2"><input type="date" name="kp_tgl_akhir[]" class="form-control input-sm"></div><div class="col-sm-2"><input type="text" name="kp_status[]" class="form-control input-sm" placeholder="Status"></div><div class="col-sm-1"><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\".kontak-pn-row\").remove()"><i class="fa fa-times"></i></button></div></div>');
}
</script>
