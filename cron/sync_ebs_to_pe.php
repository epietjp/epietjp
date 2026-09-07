<?php
$db = new mysqli('192.168.203.21','skdr_db','q(e?9gU38g','skdr_db_new');
$db->set_charset('utf8');
$db->query('SET SESSION wait_timeout=300');
$db->query('SET SESSION interactive_timeout=300');
$penyakit_map = array(18=>8,31=>8,226=>11,32=>11,294=>14,222=>26,24=>26);
function get_umur_kelamin($row){$m=array('umur7'=>0,'umur28'=>0,'umur1'=>0,'umur4'=>2,'umur9'=>7,'umur14'=>12,'umur19'=>17,'umur44'=>32,'umur54'=>47,'umur69'=>62,'umur70'=>71,'umur7w'=>0,'umur28w'=>0,'umur1w'=>0,'umur4w'=>2,'umur9w'=>7,'umur14w'=>12,'umur19w'=>17,'umur44w'=>32,'umur54w'=>47,'umur69w'=>62,'umur70w'=>71);foreach($m as $f=>$u){if(!empty($row[$f])&&$row[$f]>0){return array($u,strpos($f,'w')!==false?'P':'L');}}return array(NULL,NULL);}
$sql="SELECT e.*,d.id_kota as dist_id_kota,k.id_prop as kota_id_prop,pk.id_distrik as pusk_id_distrik FROM ewarn_form_ebs_new e LEFT JOIN ewarn_distrik d ON d.id=e.id_distrik LEFT JOIN ewarn_kota k ON k.id=d.id_kota LEFT JOIN ewarn_puskesmas pk ON pk.id=e.id_puskesmas LEFT JOIN ewarn_ghs_zoonosis_pe pe2 ON pe2.no_ebs=e.no_ebs WHERE e.create_date >= DATE_SUB(NOW(), INTERVAL 48 HOUR) AND e.diagnosa_no IN(18,31,226,32,294,222,24) AND pe2.id IS NULL ORDER BY e.id";
$result=$db->query($sql);
if(!$result){file_put_contents('/web-data/skdr/webdir/logs/sync_ebs_pe.log',date('Y-m-d H:i:s')." ERROR: ".$db->error."\n",FILE_APPEND);exit;}
$inserted=0;$errors=0;
$db->begin_transaction();
while($row=$result->fetch_assoc()){
    $idp=isset($penyakit_map[$row['diagnosa_no']])?$penyakit_map[$row['diagnosa_no']]:NULL;
    if(!$idp)continue;
    list($umur,$kel)=get_umur_kelamin($row);
    $ip=$row['kota_id_prop']?(int)$row['kota_id_prop']:NULL;
    $ik=$row['dist_id_kota']?(int)$row['dist_id_kota']:NULL;
    $ipk=$row['id_puskesmas']?(int)$row['id_puskesmas']:NULL;
    $ikec=$row['pusk_id_distrik']?(int)$row['pusk_id_distrik']:NULL;
    $kpp=$row['kd_prop_kasus']?(int)$row['kd_prop_kasus']:$ip;
    $kkp=$row['kd_kota_kasus']?(int)$row['kd_kota_kasus']:$ik;
    $ne=$db->real_escape_string($row['no_ebs']);
    $dn=(int)$row['diagnosa_no'];
    $tl=$row['tgl_laporan']?:($row['create_date']?date('Y-m-d',strtotime($row['create_date'])):NULL);
    $tp=$row['tgl_pe']?:NULL;
    $tsr=$row['tgl_bergejala']?:($row['tgl_mulai']?:NULL);
    $ts=($tsr&&$tsr!='0000-00-00'&&$tsr!='1970-01-01')?$tsr:NULL;
    $tb=($row['tgl_bergejala']&&$row['tgl_bergejala']!='0000-00-00')?$row['tgl_bergejala']:NULL;
    $tm=$row['tgl_meninggal']?:NULL;
    $td=$row['tgl_datang_faskes']?:NULL;
    $ta=$row['tgl_ambil_hasil_lab']?:($row['tgl_kirim_lab']?:NULL);
    $tk=$row['tgl_kirim_lab']?:NULL;
    $th=$row['tgl_hasil_lab']?:NULL;
    $np=$row['nama_pelapor']?$db->real_escape_string($row['nama_pelapor']):NULL;
    $tp2=$row['telp_pelapor']?$db->real_escape_string($row['telp_pelapor']):NULL;
    $g=$row['gejala']?$db->real_escape_string($row['gejala']):NULL;
    $kl=$row['informasi']?$db->real_escape_string($row['informasi']):NULL;
    $fr=$row['faktor_resiko']?$db->real_escape_string($row['faktor_resiko']):NULL;
    $jk=$row['jml_kasus']?(int)$row['jml_kasus']:1;
    $ak=$row['jml_kematian']>0?2:NULL;
    $cd=$row['create_date']?:date('Y-m-d H:i:s');
    $f=function($v){return $v!==NULL?"'$v'":'NULL';};
    $fi=function($v){return $v!==NULL?"$v":'NULL';};
    $ins="INSERT IGNORE INTO ewarn_ghs_zoonosis_pe(id_penyakit,diagnosa_no,no_ebs,id_prop,id_kota,id_puskesmas,id_kecamatan,kd_prop_kasus,kd_kota_kasus,tgl_laporan,tgl_pe,tgl_sakit,tgl_bergejala,tgl_meninggal,tgl_datang_faskes,tgl_masuk_rs,tgl_ambil_sample,tgl_kirim_sample,tgl_hasil_lab,umur_thn,kelamin,nama_petugas,telp_petugas,gejala,ket_lain,faktor_resiko,jml_kasus,akhir_no,status_kasus,create_user,create_date)VALUES({$fi($idp)},{$fi($dn)},{$f($ne)},{$fi($ip)},{$fi($ik)},{$fi($ipk)},{$fi($ikec)},{$fi($kpp)},{$fi($kkp)},{$f($tl)},{$f($tp)},{$f($ts)},{$f($tb)},{$f($tm)},{$f($td)},{$f($td)},{$f($ta)},{$f($tk)},{$f($th)},{$fi($umur)},{$f($kel)},{$f($np)},{$f($tp2)},{$f($g)},{$f($kl)},{$f($fr)},{$fi($jk)},{$fi($ak)},0,'backfill_ebs','$cd')";
    if($db->query($ins))$inserted++;else $errors++;
}
$db->commit();
$log=date('Y-m-d H:i:s')." Sync EBS->PE: inserted=$inserted errors=$errors\n";
echo $log;
file_put_contents('/web-data/skdr/webdir/logs/sync_ebs_pe.log',$log,FILE_APPEND);
