<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Zoonosis extends BackendController {

    private $PENYAKIT_ZOO;

    public function __construct() {
        parent::__construct();
        $this->PENYAKIT_ZOO = array(
            8  => array('nama'=>'GHPR / Rabies (diagnosa_no: 18=GHPR, 31=Rabies)',             'singkat'=>'GHPR',    'warna'=>'danger', 'sheet'=>'GHPR'),
            11 => array('nama'=>'Flu Burung (diagnosa_no: 226=Suspek, 32=Konfirmasi)',          'singkat'=>'Avian Flu','warna'=>'warning','sheet'=>'Avian Flu'),
            14 => array('nama'=>'Anthraks (diagnosa_no: 294)',                                  'singkat'=>'Anthraks', 'warna'=>'dark',   'sheet'=>'Anthrax'),
            26 => array('nama'=>'Leptospirosis (diagnosa_no: 222=Suspek klinis, 24=Konfirmasi)','singkat'=>'Lepto',   'warna'=>'info',   'sheet'=>'Leptospirosis'),
        );
        $this->load->model('Zoonosis_model', 'zm');
        $this->load->helper('url');
    }

    private function _auth() {
        if (!$this->authentication->is_loggedin()) { redirect(base_url('auth')); exit; }
    }
    private function _user()  {
        $u = $this->session->userdata('user_info');
        if (empty($u)) $u = $this->session->userdata('userdata');
        if (empty($u)) {
            // Fallback: ambil langsung dari session
            $u = array(
                'username'    => $this->session->userdata('username'),
                'nama'        => $this->session->userdata('nama_lengkap'),
                'level'       => $this->authentication->get_Info_User('level'),
                'id_place'    => $this->session->userdata('id_place') ?: $this->authentication->get_Info_User('id_place'),
                'kel_place'   => $this->session->userdata('kel_place') ?: $this->authentication->get_Info_User('kel_place'),
            );
        }
        return $u;
    }
    private function _level() { $u = $this->_user(); return isset($u['level']) ? (int)$u['level'] : 0; }

    // DASHBOARD
    public function index() {
        $this->_auth();
        $u  = $this->_user();
        $mw = $this->db->query("SELECT week, week_year FROM ewarn_minggu WHERE week_date>=CURDATE() ORDER BY week_date ASC LIMIT 1")->row_array();
        $data = array(
            'title'        => 'Dashboard Zoonosis',
            'penyakit'     => $this->PENYAKIT_ZOO,
            'list_prop'    => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'minggu_aktif' => $mw ? $mw['week']      : date('W'),
            'tahun_aktif'  => $mw ? $mw['week_year'] : date('Y'),
            'user'         => $u,
            'level'        => $this->_level(),
        );
        $this->template->build('dashboard', $data);
    }

    public function get_dashboard_data() {
        $this->_auth();
        $tgl1    = $this->input->get('tgl1') ?: date('Y-m-d', strtotime('-30 days'));
        $tgl2    = $this->input->get('tgl2') ?: date('Y-m-d');
        $id_prop = (int)$this->input->get('id_prop');
        $id_kota = (int)$this->input->get('id_kota');
        $out = array();
        foreach ($this->PENYAKIT_ZOO as $id_p => $info) {
            $out[$id_p] = $this->zm->get_ringkasan($id_p, $tgl1, $tgl2, $id_prop, $id_kota);
            $out[$id_p]['info'] = $info;
        }
        echo json_encode($out);
    }

    public function get_trend() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $tahun       = (int)$this->input->get('tahun') ?: date('Y');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        echo json_encode($this->zm->get_trend($id_penyakit, $tahun, $id_prop, $id_kota));
    }

    public function get_kota($id_prop=0) {
        $rows = $this->db->query("SELECT id, kota FROM ewarn_kota WHERE id_prop=".intval($id_prop)." AND aktif='Y' ORDER BY kota")->result_array();
        echo json_encode($rows);
    }


    public function get_kecamatan($id_kota=0) {
        $rows = $this->db->query(
            "SELECT id, distrik FROM ewarn_distrik WHERE id_kota=".intval($id_kota)." ORDER BY distrik"
        )->result_array();
        echo json_encode($rows);
    }

    public function get_puskesmas_by_kec($id_kec=0) {
        $rows = $this->db->query(
            "SELECT id, puskesmas FROM ewarn_puskesmas WHERE id_distrik=".intval($id_kec)." AND aktif='Y' ORDER BY puskesmas"
        )->result_array();
        echo json_encode($rows);
    }

    public function get_kota_pasien($id_prop=0) {
        $rows = $this->db->query(
            "SELECT id, kota FROM ewarn_kota WHERE id_prop=".intval($id_prop)." ORDER BY kota"
        )->result_array();
        echo json_encode($rows);
    }

    public function get_puskesmas($id_kota=0) {
        $rows = $this->zm->get_puskesmas($id_kota);
        echo json_encode($rows);
    }

    // DAFTAR PE
    public function daftar() {
        $this->_auth();
        $data = array(
            'title'     => 'Daftar PE Zoonosis',
            'penyakit'  => $this->PENYAKIT_ZOO,
            'list_prop' => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'      => $this->_user(),
            'level'     => $this->_level(),
        );
        $this->template->build('daftar', $data);
    }

    public function get_daftar() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $cari        = $this->input->get('cari');
        echo json_encode($this->zm->get_daftar($id_penyakit, $id_prop, $id_kota, $tgl1, $tgl2, $cari, $this->kel_place, $this->detail_place));
    }

    // FORM PE
    public function form($id_penyakit=0) {
        $this->_auth();
        $id_penyakit = (int)$id_penyakit;
        if (!isset($this->PENYAKIT_ZOO[$id_penyakit])) { redirect('zoonosis'); }
        $data = array(
            'title'       => 'Form PE - '.$this->PENYAKIT_ZOO[$id_penyakit]['nama'],
            'id_penyakit' => $id_penyakit,
            'info_p'      => $this->PENYAKIT_ZOO[$id_penyakit],
            'list_prop'   => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'        => $this->_user(),
            'level'       => $this->_level(),
            'pe'          => array(
                'no_pe'        => $this->_generate_no_pe($id_penyakit),
                'id_prop'      => isset($this->detail_place['id_prop'])      ? $this->detail_place['id_prop']      : '',
                'id_kota'      => isset($this->detail_place['id_kota'])      ? $this->detail_place['id_kota']      : '',
                'id_kecamatan' => isset($this->detail_place['id_distrik'])   ? $this->detail_place['id_distrik']   : '',
                'id_puskesmas' => isset($this->detail_place['id_puskesmas']) ? $this->detail_place['id_puskesmas'] : '',
                'tgl_pe'       => date('Y-m-d'),
                'tgl_laporan'  => date('Y-m-d'),
            ),
            'detail'      => $this->zm->get_eav_template($id_penyakit),
            'anggota'     => array(),
            'kontak_pn'   => array(),
            'list_ebs'    => $this->zm->get_ebs_by_penyakit($id_penyakit, $this->kel_place, $this->detail_place),
            'list_diagnosa' => $this->zm->get_diagnosa_by_penyakit($id_penyakit),
        );
        $this->template->build('form_pe', $data);
    }

    public function form_edit($id=0) {
        $this->_auth();
        $id = (int)$id;
        $pe = $this->zm->get_pe_by_id($id);
        if (!$pe) { redirect('zoonosis/daftar'); }
        $id_penyakit = (int)$pe['id_penyakit'];
        $data = array(
            'title'       => 'Edit PE - '.$this->PENYAKIT_ZOO[$id_penyakit]['nama'],
            'id_penyakit' => $id_penyakit,
            'info_p'      => $this->PENYAKIT_ZOO[$id_penyakit],
            'list_prop'   => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'        => $this->_user(),
            'level'       => $this->_level(),
            'pe'          => $pe,
            'detail'      => $this->zm->get_detail_by_pe($id),
            'list_ebs'    => $this->zm->get_ebs_by_penyakit($id_penyakit, $this->kel_place, $this->detail_place),
            'list_diagnosa' => $this->zm->get_diagnosa_by_penyakit($id_penyakit),
        );
        $this->template->build('form_pe', $data);
    }

    public function simpan() {
        $this->_auth();
        $u  = $this->_user();
        $id = (int)$this->input->post('id');
        $id_penyakit = (int)$this->input->post('id_penyakit');
        $p  = $this->input->post(NULL, TRUE);
        $main = array(
            'id_penyakit'          => $id_penyakit,
            'diagnosa_no'          => $p['diagnosa_no'] !== '' ? (int)$p['diagnosa_no'] : NULL,
            'tgl_pajanan'          => $p['tgl_pajanan']       ?: NULL,
            'alasan_ubah'          => $p['alasan_ubah']             ?: NULL,
            'id_puskesmas'         => (int)$p['id_puskesmas'],
            'id_kecamatan'         => $p['id_kecamatan'] ? (int)$p['id_kecamatan'] : NULL,
            'id_kota'              => (int)$p['id_kota'],
            'id_prop'              => (int)$p['id_prop'],
            'no_pe'                => $p['no_pe'],
            'nama_pasien'          => $p['nama_pasien'],
            'nik'                  => $p['nik'],
            'kelamin'              => $p['kelamin'],
            'umur_thn'             => (int)$p['umur_thn'],
            'umur_bln'             => (int)$p['umur_bln'],
            'tgl_lahir'            => $p['tgl_lahir']         ?: NULL,
            'pekerjaan'            => $p['pekerjaan'],
            'alamat'               => $p['alamat'],
            'kecamatan'            => $p['kecamatan'],
            'kelurahan'            => $p['kelurahan'],
            'tgl_sakit'            => $p['tgl_sakit']         ?: NULL,
            'tgl_bergejala'        => $p['tgl_bergejala']     ?: NULL,
            'tgl_laporan'          => $p['tgl_laporan']       ?: NULL,
            'tgl_pe'               => $p['tgl_pe']            ?: NULL,
            'nama_petugas'         => $p['nama_petugas'],
            'telp_petugas'         => $p['telp_petugas'],
            'status_kasus'         => (int)$p['status_kasus'],
            'akhir_no'             => $p['akhir_no'] !== '' ? (int)$p['akhir_no'] : NULL,
            'tgl_meninggal'        => $p['tgl_meninggal']     ?: NULL,
            'diperiksa_lab'        => (int)$p['diperiksa_lab'],
            'hasil_lab'            => $p['hasil_lab'],
            'jenis_sample'         => $p['jenis_sample'],
            'tgl_ambil_sample'     => $p['tgl_ambil_sample']  ?: NULL,
            'tgl_kirim_sample'     => $p['tgl_kirim_sample']  ?: NULL,
            'nama_lab'             => $p['nama_lab'],
            'ket_lab'              => $p['ket_lab'],
            'ket_lain'             => $p['ket_lain'],
            'riwayat_kontak_hewan' => $p['riwayat_kontak_hewan'] !== '' ? (int)$p['riwayat_kontak_hewan'] : NULL,
            'jenis_hewan'          => $p['jenis_hewan'],
            'tgl_kontak'           => $p['tgl_kontak']        ?: NULL,
            'lokasi_kontak'        => $p['lokasi_kontak'],
            'gejala'               => $p['gejala'],
            'riwayat_vaksinasi'    => $p['riwayat_vaksinasi'] !== '' ? (int)$p['riwayat_vaksinasi'] : NULL,
            'jenis_vaksin'         => $p['jenis_vaksin'],
            'oseltamivir'          => $p['oseltamivir'] !== '' ? (int)$p['oseltamivir'] : NULL,
            'tgl_oseltamivir'      => $p['tgl_oseltamivir']   ?: NULL,
            'kd_prop_kasus'      => $p['kd_prop_kasus'] ? (int)$p['kd_prop_kasus'] : (int)$p['id_prop'],
            'kd_kota_kasus'      => $p['kd_kota_kasus'] ? (int)$p['kd_kota_kasus'] : (int)$p['id_kota'],
            'id_kecamatan_kasus' => $p['id_kecamatan_kasus'] ? (int)$p['id_kecamatan_kasus'] : NULL,
            'no_ebs'               => $p['no_ebs'],
        );
        if ($id > 0) {
            $main['update_user'] = $u['username'];
            $main['update_date'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id)->update('ghs_zoonosis_pe', $main);
        } else {
            $main['create_user'] = $u['username'];
            $this->db->insert('ghs_zoonosis_pe', $main);

            $id = $this->db->insert_id();

        }
        $keys   = isset($p['dkey'])   ? $p['dkey']   : array();
        $labels = isset($p['dlabel']) ? $p['dlabel'] : array();
        $vals   = isset($p['dval'])   ? $p['dval']   : array();
        $types  = isset($p['dtype'])  ? $p['dtype']  : array();
        $subs   = isset($p['dsub'])   ? $p['dsub']   : array();
        if ($keys) {
            $this->db->where('id_pe', $id)->delete('ghs_zoonosis_pe_detail');
            $rows = array();
            foreach ($keys as $i => $k) {
                if ($k === '') continue;
                $rows[] = array(
                    'id_pe'     => $id,
                    'submodule' => isset($subs[$i])   ? $subs[$i]   : '',
                    'var_key'   => $k,
                    'var_label' => isset($labels[$i]) ? $labels[$i] : $k,
                    'var_value' => isset($vals[$i])   ? $vals[$i]   : '',
                    'var_type'  => isset($types[$i])  ? $types[$i]  : 'text',
                );
            }
            if ($rows) $this->db->insert_batch('ghs_zoonosis_pe_detail', $rows);
        }
        // Save anggota serumah
        $anggota = array();
        if (isset($_POST['as_nama'])) {
            foreach ($_POST['as_nama'] as $idx => $nama) {
                if (trim($nama)) $anggota[] = array(
                    'nama'        => trim($nama),
                    'tempat_kerja'=> isset($_POST['as_tempat'][$idx]) ? trim($_POST['as_tempat'][$idx]) : '',
                );
            }
        }
        $this->zm->save_anggota_serumah($id, $anggota);
        // Save kontak pneumonia
        $kontak_pn = array();
        if (isset($_POST['kp_nama'])) {
            foreach ($_POST['kp_nama'] as $idx => $nama) {
                if (trim($nama)) $kontak_pn[] = array(
                    'nama'             => trim($nama),
                    'umur'             => isset($_POST['kp_umur'][$idx]) ? (int)$_POST['kp_umur'][$idx] : NULL,
                    'alamat'           => isset($_POST['kp_alamat'][$idx]) ? trim($_POST['kp_alamat'][$idx]) : '',
                    'hub_penderita'    => isset($_POST['kp_hub'][$idx]) ? trim($_POST['kp_hub'][$idx]) : '',
                    'tgl_kontak_awal'  => isset($_POST['kp_tgl_awal'][$idx]) ? $_POST['kp_tgl_awal'][$idx] : NULL,
                    'tgl_kontak_akhir' => isset($_POST['kp_tgl_akhir'][$idx]) ? $_POST['kp_tgl_akhir'][$idx] : NULL,
                    'status_flu'       => isset($_POST['kp_status'][$idx]) ? trim($_POST['kp_status'][$idx]) : '',
                );
            }
        }
        $this->zm->save_kontak_pneumonia($id, $kontak_pn);
        echo json_encode(array('status'=>'ok', 'id'=>$id));
    }

    // ── EBS BIDIRECTIONAL: Buat EBS dari PE ─────────────────────────────────
    public function buat_ebs($id_pe=0) {
        $this->_auth();
        $id_pe = (int)$id_pe;
        $pe = $this->zm->get_pe_by_id($id_pe);
        if (!$pe) { echo json_encode(array('status'=>'error','msg'=>'PE tidak ditemukan')); return; }

        if (!empty($pe['no_ebs'])) {
            echo json_encode(array('status'=>'exists','no_ebs'=>$pe['no_ebs'],'msg'=>'PE sudah terhubung ke EBS: '.$pe['no_ebs']));
            return;
        }

        $u   = $this->_user();
        $tgl = date('Y-m-d');
        $ym  = date('Ymd');

        $last = $this->db->query(
            "SELECT no_ebs FROM ewarn_form_ebs_new WHERE no_ebs LIKE 'EBS-ZOO-{$ym}%' ORDER BY no_ebs DESC LIMIT 1"
        )->row_array();
        $num    = $last ? (int)substr($last['no_ebs'], -4) + 1 : 1;
        $no_ebs = 'EBS-ZOO-'.$ym.'-'.str_pad($num, 4, '0', STR_PAD_LEFT);

        $diagnosa_no = !empty($pe['diagnosa_no']) ? (int)$pe['diagnosa_no'] : (int)$pe['id_penyakit'];

        // id_unit: 2=Puskesmas (sesuai ewarn_form_ebs_new existing data)
        $id_unit_ebs = 2; // default Puskesmas
        if (!empty($pe['id_puskesmas'])) $id_unit_ebs = 2;

        // Ambil id_distrik dari Puskesmas (ewarn_distrik = kecamatan)
        $pusk = $this->db->query(
            'SELECT id_distrik FROM ewarn_puskesmas WHERE id='.intval($pe['id_puskesmas']).' LIMIT 1'
        )->row_array();
        $id_distrik_ebs = $pusk ? (int)$pusk['id_distrik'] : 0;


        // Build data_pendukung dari EAV PE
        $eav_rows = $this->db->query(
            'SELECT var_key, var_label, var_value FROM ewarn_ghs_zoonosis_pe_detail WHERE id_pe='.intval($id_pe).' ORDER BY id'
        )->result_array();
        // Mapping diagnosa_no ke key data_pendukung EBS
        // Beberapa diagnosa_no berbagi key yang sama
        $dp_key_map = array(
            18  => 18,   // GHPR
            31  => 18,   // Rabies konfirmasi - pakai key GHPR (sama)
            11  => 32,   // Avian Flu (key EBS = 32 Flu Burung Pada Manusia)
            226 => 32,   // Suspek Flu Burung - pakai key EBS 32
            32  => 32,   // Flu Burung Pada Manusia - key EBS 32
            294 => 294,  // Anthraks
            24  => 24,   // Leptospirosis konfirmasi
            222 => 24,   // Suspek Lepto - pakai key Lepto (sama)
        );
        $dp_key = isset($dp_key_map[$diagnosa_no]) ? $dp_key_map[$diagnosa_no] : $diagnosa_no;
        $dp = array('key' => $dp_key);
        foreach ($eav_rows as $eav) {
            if (empty($eav['var_key'])) continue;
            $dp[$eav['var_key']] = array(
                'title' => $eav['var_label'],
                'value' => $eav['var_value'] ?: '',
            );
        }
        // Tambah field pekerjaan dari PE jika ada
        if (!empty($pe['pekerjaan']) && !isset($dp['dp_pekerjaan'])) {
            $dp['dp_pekerjaan'] = array('title'=>'Pekerjaan','value'=>$pe['pekerjaan']);
        }
        $data_pendukung_json = json_encode($dp);

        // Mapping umur individu ke field kelompok umur EBS
        $umur_thn = (int)$pe["umur_thn"];
        $umur_bln = (int)$pe["umur_bln"];
        $is_wanita = ($pe["kelamin"] == "P");
        $sfx = $is_wanita ? "w" : "";
        $umur_fields = array(
            "umur7"=>0,"umur28"=>0,"umur1"=>0,"umur4"=>0,"umur9"=>0,"umur14"=>0,
            "umur19"=>0,"umur44"=>0,"umur54"=>0,"umur69"=>0,"umur70"=>0,
            "umur7w"=>0,"umur28w"=>0,"umur1w"=>0,"umur4w"=>0,"umur9w"=>0,"umur14w"=>0,
            "umur19w"=>0,"umur44w"=>0,"umur54w"=>0,"umur69w"=>0,"umur70w"=>0,
        );
        if ($umur_thn == 0 && $umur_bln == 0)       $umur_fields["umur7".$sfx]  = 1;
        elseif ($umur_thn == 0 && $umur_bln <= 1)   $umur_fields["umur28".$sfx] = 1;
        elseif ($umur_thn == 0)                      $umur_fields["umur1".$sfx]  = 1;
        elseif ($umur_thn <= 4)                      $umur_fields["umur4".$sfx]  = 1;
        elseif ($umur_thn <= 9)                      $umur_fields["umur9".$sfx]  = 1;
        elseif ($umur_thn <= 14)                     $umur_fields["umur14".$sfx] = 1;
        elseif ($umur_thn <= 19)                     $umur_fields["umur19".$sfx] = 1;
        elseif ($umur_thn <= 44)                     $umur_fields["umur44".$sfx] = 1;
        elseif ($umur_thn <= 54)                     $umur_fields["umur54".$sfx] = 1;
        elseif ($umur_thn <= 69)                     $umur_fields["umur69".$sfx] = 1;
        else                                         $umur_fields["umur70".$sfx] = 1;

        // Kronologi/informasi dari identitas pasien
        $info_kronologi = "Nama: ".$pe["nama_pasien"];
        if ($pe["umur_thn"]) $info_kronologi .= ", Umur: ".$pe["umur_thn"]." thn";
        if ($pe["kelamin"])  $info_kronologi .= ", JK: ".($pe["kelamin"]=="L"?"Laki-laki":"Perempuan");
        if ($pe["alamat"])   $info_kronologi .= ", Alamat: ".$pe["alamat"];
        if ($pe["pekerjaan"]) $info_kronologi .= ", Pekerjaan: ".$pe["pekerjaan"];
        if ($pe["ket_lain"]) $info_kronologi .= ". ".$pe["ket_lain"];

        $ebs = array_merge($umur_fields, array(
            'no_ebs'               => $no_ebs,
            'id_unit'              => $id_unit_ebs,
            'id_distrik'           => $id_distrik_ebs,
            'id_puskesmas'         => (int)$pe['id_puskesmas'],
            'diagnosa_no'          => $diagnosa_no,
            'tgl_mulai'            => $pe['tgl_sakit'] ?: ($pe['tgl_kontak'] ?: $tgl),
            'tgl_bergejala'        => $pe['tgl_bergejala'] ?: NULL,
            'tgl_laporan'          => $pe['tgl_laporan'] ?: $tgl,
            'tgl_diketahui'        => $pe['tgl_laporan'] ?: $tgl,
            'tgl_pe'               => $pe['tgl_pe'] ?: ($pe['tgl_laporan'] ?: date('Y-m-d')),
            'tgl_kirim_lab'        => $pe['tgl_kirim_sample'] ?: NULL,
            'tgl_hasil_lab'        => $pe['tgl_hasil_lab'] ?: NULL,
            'tgl_meninggal'        => $pe['tgl_meninggal'] ?: NULL,
            'tgl_datang_faskes'    => $pe['tgl_sakit'] ?: NULL,
            'jml_kasus'            => 1,
            'jml_kematian'         => ($pe['akhir_no']==2) ? 1 : 0,
            'jml_orang_periksa'    => 1,
            'gejala'               => $pe['gejala'] ?: NULL,
            'faktor_resiko'        => $pe['jenis_hewan'] ?: NULL,
            'informasi'            => $info_kronologi,
            'kd_prop_kasus'        => $pe['kd_prop_kasus'] ? (int)$pe['kd_prop_kasus'] : (int)$pe['id_prop'],
            'kd_kota_kasus'        => $pe['kd_kota_kasus'] ? (int)$pe['kd_kota_kasus'] : (int)$pe['id_kota'],
            'nama_pelapor'         => $pe['nama_petugas'] ?: (isset($u['nama_lengkap']) ? $u['nama_lengkap'] : ''),
            'telp_pelapor'         => $pe['telp_petugas'] ?: NULL,
            'create_user'          => isset($u['username']) ? $u['username'] : 'zoonosis',
            'create_date'          => date('Y-m-d H:i:s'),
            'sts_rumor_no'         => 93,
            'verifikasi_no'        => 18,
            'sumber_laporan_no'    => 102,
            'sumber_verifikasi_no' => 102,
            'sts_lab_no'           => !empty($pe['diperiksa_lab']) ? 1 : 0,
            'hasil_lab_no'         => 0,
            'tahun_lap'            => (int)date('Y'),
            'bulan_lap'            => (int)date('m'),
            'data_pendukung'       => $data_pendukung_json,
        ));

        $this->db->insert('ewarn_form_ebs_new', $ebs);
        $ebs_id = $this->db->insert_id();

        if (!$ebs_id) {
            $err = $this->db->error();
            echo json_encode(array('status'=>'error','msg'=>'Gagal insert EBS: '.$err['message']));
            return;
        }

        $this->db->where('id', $id_pe)->update('ghs_zoonosis_pe', array(
            'no_ebs'      => $no_ebs,
            'update_user' => isset($u['username']) ? $u['username'] : 'zoonosis',
            'update_date' => date('Y-m-d H:i:s'),
        ));

        $this->db->insert('ghs_zoonosis_log', array(
            'tabel'      => 'ghs_zoonosis_pe',
            'id_record'  => $id_pe,
            'aksi'       => 'EBS_CREATE',
            'field_ubah' => 'no_ebs',
            'nilai_lama' => NULL,
            'nilai_baru' => $no_ebs,
            'user_id'    => $u['username'],
            'ip_address' => $this->input->ip_address(),
            'alasan'     => 'Auto-create EBS dari PE Zoonosis',
        ));

        echo json_encode(array('status'=>'ok','no_ebs'=>$no_ebs,'ebs_id'=>$ebs_id,'msg'=>'EBS berhasil dibuat: '.$no_ebs));
    }



    // Generate no_pe otomatis: ZOO-YYYYMMDD-XXXX
    private function _generate_no_pe($id_penyakit) {
        $prefix_map = array(8=>'GHPR', 11=>'AVIAN', 14=>'ATX', 26=>'LEPTO');
        $prefix = isset($prefix_map[$id_penyakit]) ? $prefix_map[$id_penyakit] : 'ZOO';
        $tgl = date('Ymd');
        $last = $this->db->query(
            "SELECT no_pe FROM ewarn_ghs_zoonosis_pe
             WHERE no_pe LIKE '{$prefix}-{$tgl}-%'
             ORDER BY no_pe DESC LIMIT 1"
        )->row_array();
        $num = $last ? (int)substr($last['no_pe'], -4) + 1 : 1;
        return $prefix.'-'.$tgl.'-'.str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function hapus($id=0) {
        $this->_auth();
        $id = (int)$id;
        $this->db->where('id', $id)->delete('ghs_zoonosis_pe');
        $this->db->where('id_pe', $id)->delete('ghs_zoonosis_pe_detail');
        redirect('zoonosis/daftar');
    }

    public function detail($id=0) {
        $this->_auth();
        $id = (int)$id;
        $pe = $this->zm->get_pe_by_id($id);
        if (!$pe) { redirect('zoonosis/daftar'); }
        $data = array(
            'title'    => 'Detail PE #'.$pe['no_pe'],
            'pe'       => $pe,
            'detail'   => $this->zm->get_detail_by_pe($id),
            'penyakit' => $this->PENYAKIT_ZOO,
            'user'     => $this->_user(),
            'level'    => $this->_level(),
        );
        $this->template->build('detail_pe', $data);
    }

    // ANALISA
    public function analisa() {
        $this->_auth();
        $mw = $this->db->query("SELECT week, week_year FROM ewarn_minggu WHERE week_date>=CURDATE() ORDER BY week_date ASC LIMIT 1")->row_array();
        $data = array(
            'title'       => 'Analisa Kasus Zoonosis',
            'penyakit'    => $this->PENYAKIT_ZOO,
            'list_prop'   => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'tahun_aktif' => $mw ? $mw['week_year'] : date('Y'),
            'user'        => $this->_user(),
            'level'       => $this->_level(),
        );
        $this->template->build('analisa', $data);
    }

    public function get_analisa_prop() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        echo json_encode($this->zm->get_per_prop($id_penyakit, $tgl1, $tgl2));
    }

    public function get_analisa_status() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $id_prop     = (int)$this->input->get('id_prop');
        echo json_encode($this->zm->get_status_breakdown($id_penyakit, $tgl1, $tgl2, $id_prop));
    }

    public function export_csv() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $rows        = $this->zm->get_daftar($id_penyakit, $id_prop, $id_kota, $tgl1, $tgl2, '', $this->kel_place, $this->detail_place);
        $STATUS = array(0=>'Suspek',1=>'Probable',2=>'Konfirmasi',3=>'Discarded');
        $AKHIR  = array(1=>'Sembuh',2=>'Meninggal',3=>'Dalam Perawatan');
        $pname  = ($id_penyakit && isset($this->PENYAKIT_ZOO[$id_penyakit]))
                  ? $this->PENYAKIT_ZOO[$id_penyakit]['singkat'] : 'Semua';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=pe_zoonosis_'.$pname.'_'.$tgl1.'_'.$tgl2.'.csv');
        $out = fopen('php://output','w');
        fputcsv($out, array('No PE','Penyakit','Propinsi','Kab/Kota','Unit Pelapor',
            'Nama Pasien','NIK','Kelamin','Umur','Alamat','Tgl Sakit','Tgl PE',
            'Status Kasus','Kondisi Akhir','Lab','Hasil Lab','No EBS','Petugas PE'));
        foreach ($rows as $r) {
            $umur = $r['umur_thn'].'thn';
            if ($r['umur_bln']) $umur .= ' '.$r['umur_bln'].'bln';
            fputcsv($out, array(
                $r['no_pe'], $r['nama_penyakit'], $r['propinsi'], $r['kota'],
                $r['unit_pelapor'], $r['nama_pasien'], $r['nik'],
                $r['kelamin']=='L' ? 'Laki-laki' : 'Perempuan', $umur, $r['alamat'],
                $r['tgl_sakit'], $r['tgl_pe'],
                isset($STATUS[$r['status_kasus']]) ? $STATUS[$r['status_kasus']] : '-',
                isset($AKHIR[$r['akhir_no']])      ? $AKHIR[$r['akhir_no']]      : '-',
                $r['diperiksa_lab'] ? 'Ya' : 'Tidak',
                $r['hasil_lab'], $r['no_ebs'], $r['nama_petugas']
            ));
        }
        fclose($out);
    }

    // ================================================================
    // UPLOAD EXCEL
    // ================================================================
    private $ALL_FIELDS = array(
        'no_pe','diagnosa_no','tgl_laporan','tgl_pe','no_ebs','nama_petugas','telp_petugas',
        'id_prop','id_kota','id_puskesmas','id_kecamatan',
        'kd_prop_kasus','kd_kota_kasus','id_kecamatan_kasus',
        'nama_pasien','nama_kk','nik','kelamin','umur_thn','umur_bln','tgl_lahir','pekerjaan','telp_pasien',
        'alamat','alamat_kerja','kontak_darurat','kelurahan','kecamatan',
        'tgl_bergejala','tgl_sakit','tgl_pajanan','status_kasus','akhir_no','tgl_meninggal','gejala',
        'riwayat_kontak_hewan','jenis_hewan','tgl_kontak','lokasi_kontak',
        'riwayat_vaksinasi','jenis_vaksin','oseltamivir','tgl_oseltamivir',
        'nama_rs','tgl_masuk_rs','diperiksa_lab','jenis_sample','tgl_ambil_sample','tgl_kirim_sample','tgl_hasil_lab','tgl_vaksin_influenza',
        'nama_lab','hasil_lab','ket_lab','ket_lain',
    );
    private $DATE_FIELDS_UPL = array(
        'tgl_laporan','tgl_pe','tgl_lahir','tgl_bergejala','tgl_sakit','tgl_pajanan','tgl_masuk_rs',
        'tgl_meninggal','tgl_kontak','tgl_oseltamivir','tgl_ambil_sample','tgl_kirim_sample','tgl_hasil_lab','tgl_vaksin_influenza'
    );
    private $INT_FIELDS_UPL = array(
        'diagnosa_no','id_prop','id_kota','id_puskesmas','id_kecamatan',
        'kd_prop_kasus','kd_kota_kasus','id_kecamatan_kasus',
        'umur_thn','umur_bln','status_kasus','akhir_no',
        'riwayat_kontak_hewan','riwayat_vaksinasi','oseltamivir','diperiksa_lab'
    );

    public function upload_excel() {
        $this->_auth();
        $data = array(
            'title'    => 'Upload Template Excel PE Zoonosis',
            'penyakit' => $this->PENYAKIT_ZOO,
            'user'     => $this->_user(),
        );
        $this->template->build('upload_excel', $data);
    }

    public function download_template() {
        $this->_auth();
        $file = FCPATH . 'template_upload/Template_Import_PE_Zoonosis.xlsx';
        if (!file_exists($file)) {
            show_error('File template tidak ditemukan. Hubungi admin.'); return;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_PE_Zoonosis.xlsx"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache');
        readfile($file); exit;
    }

    public function preview_excel() {
        $this->_auth();
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] != 0) {
            echo json_encode(array('status'=>'error','msg'=>'File tidak ditemukan.')); return;
        }
        $file = $_FILES['file_excel'];
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
            echo json_encode(array('status'=>'error','msg'=>'Hanya file .xlsx.')); return;
        }
        $id_penyakit = (int)$this->input->post('id_penyakit');
        if (!isset($this->PENYAKIT_ZOO[$id_penyakit])) {
            echo json_encode(array('status'=>'error','msg'=>'Pilih jenis penyakit.')); return;
        }
        $tmp = sys_get_temp_dir() . '/skdr_' . uniqid() . '.xlsx';
        if (!move_uploaded_file($file['tmp_name'], $tmp)) {
            echo json_encode(array('status'=>'error','msg'=>'Gagal simpan file sementara.')); return;
        }
        $result = $this->_parse_xlsx($tmp, $id_penyakit);
        @unlink($tmp);
        echo json_encode($result);
    }

    public function proses_excel() {
        $this->_auth();
        $u = $this->_user();
        $rows_json   = $this->input->post('rows_json');
        $id_penyakit = (int)$this->input->post('id_penyakit');
        if (!$rows_json) { echo json_encode(array('status'=>'error','msg'=>'Data kosong.')); return; }
        $rows = json_decode($rows_json, true);
        if (!$rows) { echo json_encode(array('status'=>'error','msg'=>'Format tidak valid.')); return; }
        $inserted = 0; $skipped = 0; $errors = array();
        foreach ($rows as $i => $row) {
            if (!empty($row['no_pe'])) {
                $ex = $this->db->query(
                    "SELECT id FROM ewarn_ghs_zoonosis_pe WHERE no_pe=? AND id_penyakit=? LIMIT 1",
                    array($row['no_pe'], $id_penyakit))->row_array();
                if ($ex) {
                    $skipped++;
                    $errors[] = 'Baris '.($i+1).': No PE duplikat, dilewati.';
                    continue;
                }
            }
            $ins = array('id_penyakit'=>$id_penyakit,'create_user'=>$u['username']);
            foreach ($this->ALL_FIELDS as $f) {
                $val = isset($row[$f]) ? $row[$f] : NULL;
                if ($val===''||$val==='nan') $val=NULL;
                if (in_array($f,$this->INT_FIELDS_UPL)) $ins[$f]=($val!==NULL)?(int)$val:NULL;
                elseif (in_array($f,$this->DATE_FIELDS_UPL)) $ins[$f]=($val&&$val!='0000-00-00')?$val:NULL;
                else $ins[$f]=$val;
            }
            $this->db->insert('ghs_zoonosis_pe',$ins);
            if ($this->db->affected_rows()>0) $inserted++;
            else $errors[]='Baris '.($i+1).': gagal insert.';
        }
        echo json_encode(array('status'=>'ok','inserted'=>$inserted,'skipped'=>$skipped,'errors'=>$errors));
    }

    private function _parse_xlsx($filepath, $id_penyakit) {
        $parser_path = APPPATH . '../_public/modules/zoonosis/helpers/XlsxParser.php';
        if (!file_exists($parser_path)) {
            return array('status'=>'error','msg'=>'XlsxParser tidak ditemukan.');
        }
        require_once $parser_path;
        try {
            $parser      = new XlsxParser($filepath);
            $info_p      = $this->PENYAKIT_ZOO[$id_penyakit];
            $sheet_name  = $info_p['sheet'];
            $field_cells = $parser->get_row_fields($sheet_name, 3);
            if (empty($field_cells)) {
                return array('status'=>'error','msg'=>'Baris 3 tidak ditemukan. Gunakan template resmi SKDR.');
            }
            $field_map = array();
            foreach ($field_cells as $ci => $val) {
                $val = trim($val);
                if ($val && in_array($val, $this->ALL_FIELDS)) $field_map[$ci] = $val;
            }
            if (empty($field_map)) {
                return array('status'=>'error','msg'=>'Field tidak dikenali. Cek baris 3 template.');
            }
            $raw_rows = $parser->get_sheet_rows($sheet_name, 6, 500);
            $rows = array(); $errors = array();
            $req  = array('no_pe','id_prop','id_kota','nama_pasien','kelamin','tgl_sakit','status_kasus');
            foreach ($raw_rows as $ri => $cells) {
                $row_num = $ri + 6;
                $data = array();
                foreach ($field_map as $ci => $field) {
                    $data[$field] = isset($cells[$ci]) ? trim($cells[$ci]) : '';
                }
                $row_err = array();
                foreach ($req as $f) {
                    if (!isset($data[$f])||$data[$f]==='') $row_err[]='Baris '.$row_num.': field "'.$f.'" wajib.';
                }
                if (!empty($data['kelamin'])&&!in_array(strtoupper($data['kelamin']),array('L','P'))) {
                    $row_err[]='Baris '.$row_num.': kelamin harus L/P.';
                }
                if ($row_err) { foreach($row_err as $e) $errors[]=$e; continue; }
                if (isset($data['kelamin'])) $data['kelamin']=strtoupper($data['kelamin']);
                foreach ($this->INT_FIELDS_UPL as $f) {
                    $data[$f]=(isset($data[$f])&&$data[$f]!=='')?(int)$data[$f]:NULL;
                }
                foreach ($this->DATE_FIELDS_UPL as $f) {
                    if (isset($data[$f])&&$data[$f]!=='') {
                        $v=trim($data[$f]);
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/',$v)) $data[$f]=$v;
                        elseif (is_numeric($v)&&$v>1000) $data[$f]=date('Y-m-d',($v-25569)*86400);
                        elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',$v,$m)) $data[$f]=sprintf('%04d-%02d-%02d',$m[3],$m[2],$m[1]);
                        else $data[$f]=NULL;
                    } else { $data[$f]=NULL; }
                }
                if ($id_penyakit!=11) { $data['oseltamivir']=NULL; $data['tgl_oseltamivir']=NULL; }
                $rows[] = $data;
            }
            return array('status'=>'ok','rows'=>$rows,'total'=>count($rows),
                'errors'=>$errors,'id_penyakit'=>$id_penyakit,'penyakit'=>$info_p['nama']);
        } catch (Exception $e) {
            return array('status'=>'error','msg'=>'Gagal baca file: '.$e->getMessage());
        }
    }


    // ================================================================
    // CLUSTER EPIDEMIOLOGI
    // ================================================================

    private function _generate_no_cluster($id_penyakit) {
        $singkat = array(8=>'GHPR',11=>'AVIAN',14=>'ANTHRAX',26=>'LEPTO');
        $kode    = isset($singkat[$id_penyakit]) ? $singkat[$id_penyakit] : 'ZOO';
        $tahun   = date('Y');
        $last    = $this->db->query(
            "SELECT no_cluster FROM ewarn_ghs_zoonosis_cluster
             WHERE id_penyakit=? AND YEAR(create_date)=?
             ORDER BY id DESC LIMIT 1",
            array($id_penyakit, $tahun)
        )->row_array();
        $urut = 1;
        if ($last) {
            $parts = explode('-', $last['no_cluster']);
            $urut  = (int)end($parts) + 1;
        }
        return sprintf('CLU-%s-%s-%03d', $kode, $tahun, $urut);
    }

    // Halaman daftar cluster
    public function cluster() {
        $this->_auth();
        $data = array(
            'title'     => 'Daftar Cluster Zoonosis',
            'penyakit'  => $this->PENYAKIT_ZOO,
            'list_prop' => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'      => $this->_user(),
            'level'     => $this->_level(),
        );
        $this->template->build('cluster_daftar', $data);
    }

    // AJAX: get daftar cluster
    public function get_cluster_list() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $status      = $this->input->get('status');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');

        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);

        $q = "SELECT c.*,
                p.nama_penyakit,
                pr.propinsi,
                k.kota,
                COUNT(pe.id) AS jumlah_kasus,
                SUM(CASE WHEN pe.status_kasus=2 THEN 1 ELSE 0 END) AS konfirmasi,
                SUM(CASE WHEN pe.akhir_no=2     THEN 1 ELSE 0 END) AS meninggal
              FROM ewarn_ghs_zoonosis_cluster c
              LEFT JOIN ewarn_penyakit p   ON p.id  = c.id_penyakit
              LEFT JOIN ewarn_propinsi pr  ON pr.id = c.id_prop
              LEFT JOIN ewarn_kota k       ON k.id  = c.id_kota
              LEFT JOIN ewarn_ghs_zoonosis_pe pe ON pe.id_cluster = c.id
              WHERE c.tgl_mulai BETWEEN '{$tgl1}' AND '{$tgl2}'";
        if ($id_penyakit) $q .= " AND c.id_penyakit=".intval($id_penyakit);
        if ($id_kota)     $q .= " AND c.id_kota=".intval($id_kota);
        elseif ($id_prop) $q .= " AND c.id_prop=".intval($id_prop);
        if ($status !== '' && $status !== null) $q .= " AND c.status_cluster=".intval($status);
        $q .= " GROUP BY c.id ORDER BY c.tgl_mulai DESC LIMIT 500";

        echo json_encode($this->db->query($q)->result_array());
    }

    // Form cluster baru
    public function cluster_form($id=0) {
        $this->_auth();
        $id = (int)$id;
        $cl = array();
        if ($id > 0) {
            $cl = $this->db->query(
                "SELECT c.*, p.nama_penyakit, pr.propinsi, k.kota
                 FROM ewarn_ghs_zoonosis_cluster c
                 LEFT JOIN ewarn_penyakit p  ON p.id  = c.id_penyakit
                 LEFT JOIN ewarn_propinsi pr ON pr.id = c.id_prop
                 LEFT JOIN ewarn_kota k      ON k.id  = c.id_kota
                 WHERE c.id=".intval($id)." LIMIT 1"
            )->row_array();
        }
        $data = array(
            'title'     => $id > 0 ? 'Edit Cluster #'.$cl['no_cluster'] : 'Buat Cluster Baru',
            'penyakit'  => $this->PENYAKIT_ZOO,
            'list_prop' => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'      => $this->_user(),
            'level'     => $this->_level(),
            'cluster'   => $cl,
        );
        $this->template->build('cluster_form', $data);
    }

    // Simpan cluster
    public function cluster_simpan() {
        $this->_auth();
        $u  = $this->_user();
        $id = (int)$this->input->post('id');
        $id_penyakit = (int)$this->input->post('id_penyakit');

        $main = array(
            'id_penyakit'    => $id_penyakit,
            'id_prop'        => (int)$this->input->post('id_prop') ?: NULL,
            'id_kota'        => (int)$this->input->post('id_kota') ?: NULL,
            'id_puskesmas'   => (int)$this->input->post('id_puskesmas') ?: NULL,
            'nama_cluster'   => $this->input->post('nama_cluster'),
            'tgl_mulai'      => $this->input->post('tgl_mulai')    ?: NULL,
            'tgl_selesai'    => $this->input->post('tgl_selesai')  ?: NULL,
            'sumber_paparan' => $this->input->post('sumber_paparan'),
            'lokasi_paparan' => $this->input->post('lokasi_paparan'),
            'jumlah_terpapar'=> $this->input->post('jumlah_terpapar') !== '' ? (int)$this->input->post('jumlah_terpapar') : NULL,
            'status_cluster' => (int)$this->input->post('status_cluster'),
            'no_klb'         => $this->input->post('no_klb'),
            'no_ebs'         => $this->input->post('no_ebs'),
            'keterangan'     => $this->input->post('keterangan'),
        );

        if ($id > 0) {
            $main['update_user'] = $u['username'];
            $main['update_date'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id)->update('ghs_zoonosis_cluster', $main);
            echo json_encode(array('status'=>'ok','id'=>$id,'no_cluster'=>$this->input->post('no_cluster')));
        } else {
            $main['no_cluster']  = $this->_generate_no_cluster($id_penyakit);
            $main['create_user'] = $u['username'];
            $this->db->insert('ghs_zoonosis_cluster', $main);
            $id = $this->db->insert_id();

            echo json_encode(array('status'=>'ok','id'=>$id,'no_cluster'=>$main['no_cluster']));
        }
    }

    // Detail cluster: semua PE terkait + kurva epidemi
    public function cluster_detail($id=0) {
        $this->_auth();
        $id = (int)$id;
        $cl = $this->db->query(
            "SELECT c.*, p.nama_penyakit, pr.propinsi, k.kota
             FROM ewarn_ghs_zoonosis_cluster c
             LEFT JOIN ewarn_penyakit p  ON p.id  = c.id_penyakit
             LEFT JOIN ewarn_propinsi pr ON pr.id = c.id_prop
             LEFT JOIN ewarn_kota k      ON k.id  = c.id_kota
             WHERE c.id=".intval($id)." LIMIT 1"
        )->row_array();
        if (!$cl) { redirect('zoonosis/cluster'); }

        // PE terkait
        $pe_list = $this->db->query(
            "SELECT pe.*, pk.puskesmas AS unit_pelapor
             FROM ewarn_ghs_zoonosis_pe pe
             LEFT JOIN ewarn_puskesmas pk ON pk.id = pe.id_puskesmas
             WHERE pe.id_cluster=".intval($id)."
             ORDER BY pe.tgl_sakit ASC"
        )->result_array();

        // Kurva epidemi: kasus per hari
        $kurva = $this->db->query(
            "SELECT tgl_sakit, COUNT(*) AS total,
                SUM(CASE WHEN status_kasus=2 THEN 1 ELSE 0 END) AS konfirmasi,
                SUM(CASE WHEN akhir_no=2     THEN 1 ELSE 0 END) AS meninggal
             FROM ewarn_ghs_zoonosis_pe
             WHERE id_cluster=".intval($id)." AND tgl_sakit IS NOT NULL
             GROUP BY tgl_sakit ORDER BY tgl_sakit ASC"
        )->result_array();

        // Attack rate & CFR
        $total_kasus    = count($pe_list);
        $total_konfirm  = 0; $total_mati = 0;
        foreach ($pe_list as $pe) {
            if ($pe['status_kasus']==2) $total_konfirm++;
            if ($pe['akhir_no']==2)     $total_mati++;
        }
        $jumlah_terpapar = $cl['jumlah_terpapar'] ?: $total_kasus;
        $attack_rate = $jumlah_terpapar > 0 ? round($total_kasus / $jumlah_terpapar * 100, 2) : 0;
        $cfr         = $total_kasus > 0 ? round($total_mati / $total_kasus * 100, 2) : 0;

        $data = array(
            'title'          => 'Detail Cluster '.$cl['no_cluster'],
            'cluster'        => $cl,
            'pe_list'        => $pe_list,
            'kurva'          => $kurva,
            'total_kasus'    => $total_kasus,
            'total_konfirm'  => $total_konfirm,
            'total_mati'     => $total_mati,
            'attack_rate'    => $attack_rate,
            'cfr'            => $cfr,
            'penyakit'       => $this->PENYAKIT_ZOO,
            'user'           => $this->_user(),
            'level'          => $this->_level(),
        );
        $this->template->build('cluster_detail', $data);
    }

    // Link/unlink PE ke cluster
    public function cluster_link_pe() {
        $this->_auth();
        $id_cluster = (int)$this->input->post('id_cluster');
        $id_pe      = (int)$this->input->post('id_pe');
        $action     = $this->input->post('action'); // 'link' atau 'unlink'

        if ($action === 'link') {
            $this->db->where('id', $id_pe)->update('ghs_zoonosis_pe', array('id_cluster'=>$id_cluster));
        } else {
            $this->db->where('id', $id_pe)->update('ghs_zoonosis_pe', array('id_cluster'=>NULL));
        }
        echo json_encode(array('status'=>'ok'));
    }

    // AJAX: cari PE yang bisa di-link ke cluster ini
    public function get_pe_for_cluster($id_cluster=0) {
        $this->_auth();
        $id_cluster  = (int)$id_cluster;
        $cl = $this->db->query("SELECT id_penyakit, id_kota FROM ewarn_ghs_zoonosis_cluster WHERE id=? LIMIT 1", array($id_cluster))->row_array();
        if (!$cl) { echo json_encode(array()); return; }

        $q = "SELECT pe.id, pe.no_pe, pe.nama_pasien, pe.tgl_sakit, pe.status_kasus,
                pe.id_cluster, pk.puskesmas AS unit_pelapor
              FROM ewarn_ghs_zoonosis_pe pe
              LEFT JOIN ewarn_puskesmas pk ON pk.id = pe.id_puskesmas
              WHERE pe.id_penyakit=".intval($cl['id_penyakit'])."
              ORDER BY pe.tgl_sakit DESC LIMIT 200";
        echo json_encode($this->db->query($q)->result_array());
    }

    // Hapus cluster
    public function cluster_hapus($id=0) {
        $this->_auth();
        $id = (int)$id;
        // Unlink semua PE dulu
        $this->db->where('id_cluster', $id)->update('ghs_zoonosis_pe', array('id_cluster'=>NULL));
        $this->db->where('id', $id)->delete('ghs_zoonosis_cluster');
        redirect('zoonosis/cluster');
    }

}
