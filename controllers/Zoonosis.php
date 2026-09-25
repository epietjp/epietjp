<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Zoonosis extends BackendController {

    private $PENYAKIT_ZOO;

    public function __construct() {
        parent::__construct();
        $this->PENYAKIT_ZOO = array(
            8  => array('nama'=>'GHPR / Rabies',             'singkat'=>'GHPR',    'warna'=>'danger', 'sheet'=>'GHPR'),
            11 => array('nama'=>'Avian Influenza / Flu Burung',          'singkat'=>'Avian Flu','warna'=>'warning','sheet'=>'Avian Flu'),
            14 => array('nama'=>'Anthraks',                                  'singkat'=>'Anthraks', 'warna'=>'dark',   'sheet'=>'Anthrax'),
            26 => array('nama'=>'Leptospirosis','singkat'=>'Lepto',   'warna'=>'info',   'sheet'=>'Leptospirosis'),
        );
        $this->load->model('Zoonosis_model', 'zm');
        $this->load->helper('url');
        // Override remap - jangan remap ke BackendController
        unset($this->remap['edit']);
        unset($this->remap['view']);
        unset($this->remap['add']);
        unset($this->remap['delete']);
        // Bypass privilege check untuk semua fungsi zoonosis
        $this->modul_by_pass = array('zoonosis');
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
        // Minggu aktif
        $minggu = $this->db->query(
            "SELECT week, week_year, week_date, DATE_SUB(week_date, INTERVAL 6 DAY) as week_start
             FROM ewarn_minggu
             WHERE week_date >= CURDATE()
             ORDER BY week_date ASC LIMIT 1"
        )->row_array();

        $out = array();
        foreach ($this->PENYAKIT_ZOO as $id_p => $info) {
            $out[$id_p] = $this->zm->get_ringkasan($id_p, $tgl1, $tgl2, $id_prop, $id_kota);
            $out[$id_p]['info'] = $info;
            // Kasus minggu aktif
            if ($minggu) {
                $week_start = $minggu['week_start'];
                $week_end   = $minggu['week_date'];
                $qm = "SELECT COUNT(*) AS n FROM ewarn_ghs_zoonosis_pe
                     WHERE id_penyakit=".intval($id_p)."
                     AND tgl_laporan BETWEEN '{$week_start}' AND '{$week_end}'";
                if ($id_kota)     $qm .= " AND id_kota=".intval($id_kota);
                elseif ($id_prop) $qm .= " AND id_prop=".intval($id_prop);
                $r = $this->db->query($qm)->row_array();
                $out[$id_p]['minggu_ini'] = (int)$r['n'];
                $out[$id_p]['minggu_no']  = $minggu['week'];
            }
        }
        echo json_encode($out);
    }

    public function get_trend() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $diagnosa_no = (int)$this->input->get('diagnosa_no');
        $tahun       = (int)$this->input->get('tahun') ?: date('Y');
        $mode        = $this->input->get('mode') === 'bulan' ? 'bulan' : 'minggu';
        $bulan       = (int)$this->input->get('bulan');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        echo json_encode($this->zm->get_trend($id_penyakit, $tahun, $id_prop, $id_kota, $diagnosa_no, $mode, $bulan));
    }

    public function get_desa($id_kec=0) {
        $id_kec = (int)$id_kec;
        $kec = $this->db->query("SELECT distrik FROM ewarn_distrik WHERE id=".intval($id_kec))->row_array();
        if (!$kec) { echo json_encode(array()); return; }
        $nama_kec = $this->db->escape_str($kec['distrik']);
        $rows = $this->db->query("SELECT DISTINCT `COL 5` as desa FROM wilayah_desa WHERE `COL 4`='{$nama_kec}' AND `COL 5`!='' AND `COL 5`!='village' ORDER BY `COL 5`")->result_array();
        echo json_encode($rows);
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
    public function cluster() {
        $this->_auth();
        $data = array(
            'title'     => 'Cluster Epidemiologi Zoonosis',
            'penyakit'  => $this->PENYAKIT_ZOO,
            'list_prop' => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'      => $this->_user(),
            'level'     => $this->_level(),
        );
        $this->template->build('cluster_daftar', $data);
    }

    public function cluster_form($id=0) {
        $this->_auth();
        $id = (int)$id;
        $cluster = array();
        if ($id) {
            $cluster = $this->db->query("SELECT c.*, p.propinsi, k.kota FROM ewarn_ghs_zoonosis_cluster c LEFT JOIN ewarn_propinsi p ON p.id=c.id_prop LEFT JOIN ewarn_kota k ON k.id=c.id_kota WHERE c.id=".intval($id))->row_array();
        }
        $data = array(
            'title'     => $id ? 'Edit Cluster '.$cluster['no_cluster'] : 'Form Cluster Zoonosis',
            'penyakit'  => $this->PENYAKIT_ZOO,
            'list_prop' => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'      => $this->_user(),
            'level'     => $this->_level(),
            'id'        => $id,
            'cluster'   => $cluster,
        );
        $this->template->build('cluster_form', $data);
    }

    public function cluster_detail($id=0) {
        $this->_auth();
        $id = (int)$id;
        $cluster = $this->db->query("
            SELECT c.*, p.propinsi, k.kota, py.nama_penyakit,
                COUNT(cp.id_pe) as jumlah_kasus,
                SUM(CASE WHEN pe.status_kasus=2 THEN 1 ELSE 0 END) as konfirmasi,
                SUM(CASE WHEN pe.akhir_no=2 THEN 1 ELSE 0 END) as meninggal
            FROM ewarn_ghs_zoonosis_cluster c
            LEFT JOIN ewarn_propinsi p ON p.id=c.id_prop
            LEFT JOIN ewarn_kota k ON k.id=c.id_kota
            LEFT JOIN ewarn_penyakit py ON py.id=c.id_penyakit
            LEFT JOIN ewarn_ghs_zoonosis_cluster_pe cp ON cp.id_cluster=c.id
            LEFT JOIN ewarn_ghs_zoonosis_pe pe ON pe.id=cp.id_pe
            WHERE c.id=".intval($id)."
            GROUP BY c.id
        ")->row_array();
        if (!$cluster) { redirect('zoonosis/cluster'); }
        // Kurva epidemi
        $kurva = $this->db->query("
            SELECT DATE(COALESCE(pe.tgl_sakit,pe.tgl_bergejala,pe.tgl_laporan)) as tgl_sakit,
                COUNT(*) as total,
                SUM(CASE WHEN pe.diagnosa_no IN (31,32,24) THEN 1 ELSE 0 END) as konfirmasi,
                SUM(CASE WHEN pe.akhir_no=2 THEN 1 ELSE 0 END) as meninggal
            FROM ewarn_ghs_zoonosis_cluster_pe cp
            JOIN ewarn_ghs_zoonosis_pe pe ON pe.id=cp.id_pe
            WHERE cp.id_cluster=".intval($id)."
            AND COALESCE(pe.tgl_sakit,pe.tgl_bergejala,pe.tgl_laporan) IS NOT NULL
            GROUP BY DATE(COALESCE(pe.tgl_sakit,pe.tgl_bergejala,pe.tgl_laporan))
            ORDER BY tgl_sakit ASC
        ")->result_array();

        // Daftar PE terkait
        $pe_cluster = $this->db->query("
            SELECT pe.id, pe.no_pe, pe.nama_pasien, pe.tgl_sakit, pe.tgl_laporan,
                pe.status_kasus, pe.akhir_no, pe.diagnosa_no,
                pr.propinsi, k.kota
            FROM ewarn_ghs_zoonosis_cluster_pe cp
            JOIN ewarn_ghs_zoonosis_pe pe ON pe.id=cp.id_pe
            LEFT JOIN ewarn_propinsi pr ON pr.id=pe.id_prop
            LEFT JOIN ewarn_kota k ON k.id=pe.id_kota
            WHERE cp.id_cluster=".intval($id)."
            ORDER BY pe.tgl_sakit ASC
        ")->result_array();

        $data = array(
            'title'      => 'Detail Cluster '.$cluster['no_cluster'],
            'cluster'    => $cluster,
            'kurva'      => $kurva,
            'pe_list'    => $pe_cluster,
            'penyakit'   => $this->PENYAKIT_ZOO,
            'list_prop'  => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'       => $this->_user(),
            'level'      => $this->_level(),
            'id'         => $id,
        );
        $this->template->build('cluster_detail', $data);
    }

    public function get_pe_for_cluster($id_cluster=0) {
        $this->_auth();
        $id_cluster = (int)$id_cluster;
        $cl = $this->db->query("SELECT id_penyakit FROM ewarn_ghs_zoonosis_cluster WHERE id=".intval($id_cluster))->row_array();
        $id_penyakit = $cl ? (int)$cl['id_penyakit'] : 0;
        $q = "SELECT z.id, z.no_pe, z.no_ebs, z.nama_pasien, z.tgl_laporan, z.tgl_pe,
                z.status_kasus, z.akhir_no, pr.propinsi, k.kota,
                cp.id_cluster
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop
              LEFT JOIN ewarn_kota k ON k.id=z.id_kota
              LEFT JOIN ewarn_ghs_zoonosis_cluster_pe cp ON cp.id_pe=z.id AND cp.id_cluster=".intval($id_cluster)."
              WHERE z.id_penyakit=".intval($id_penyakit)."
              ORDER BY z.tgl_laporan DESC LIMIT 500";
        echo json_encode($this->db->query($q)->result_array());
    }

    public function cluster_link_pe() {
        $this->_auth();
        $id_cluster = (int)$this->input->post('id_cluster');
        $id_pe      = (int)$this->input->post('id_pe');
        $action     = $this->input->post('action');
        if ($action == 'link') {
            $exists = $this->db->query("SELECT id FROM ewarn_ghs_zoonosis_cluster_pe WHERE id_cluster={$id_cluster} AND id_pe={$id_pe}")->row_array();
            if (!$exists) {
                $this->db->insert('ewarn_ghs_zoonosis_cluster_pe', array('id_cluster'=>$id_cluster,'id_pe'=>$id_pe));
            }
        } else {
            $this->db->where('id_cluster',$id_cluster)->where('id_pe',$id_pe)->delete('ewarn_ghs_zoonosis_cluster_pe');
        }
        echo json_encode(array('status'=>'ok'));
    }


    public function get_cluster_list() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $status      = $this->input->get('status');
        $q = "SELECT c.*, p.propinsi, k.kota, py.nama_penyakit,
                COUNT(cp.id_pe) as jumlah_kasus,
                SUM(CASE WHEN pe.status_kasus=2 THEN 1 ELSE 0 END) as konfirmasi,
                SUM(CASE WHEN pe.akhir_no=2 THEN 1 ELSE 0 END) as meninggal
              FROM ewarn_ghs_zoonosis_cluster c
              LEFT JOIN ewarn_propinsi p ON p.id=c.id_prop
              LEFT JOIN ewarn_kota k ON k.id=c.id_kota
              LEFT JOIN ewarn_penyakit py ON py.id=c.id_penyakit
              LEFT JOIN ewarn_ghs_zoonosis_cluster_pe cp ON cp.id_cluster=c.id
              LEFT JOIN ewarn_ghs_zoonosis_pe pe ON pe.id=cp.id_pe
              WHERE 1=1";
        if ($id_penyakit) $q .= " AND c.id_penyakit=".intval($id_penyakit);
        if ($id_prop)     $q .= " AND c.id_prop=".intval($id_prop);
        if ($id_kota)     $q .= " AND c.id_kota=".intval($id_kota);
        if ($status!=='') $q .= " AND c.status_cluster=".intval($status);
        $q .= " GROUP BY c.id ORDER BY c.create_date DESC LIMIT 200";
        echo json_encode($this->db->query($q)->result_array());
    }

    public function cluster_hapus($id=0) {
        $this->_auth();
        $id = (int)$id;
        $this->db->where('id', $id)->delete('ewarn_ghs_zoonosis_cluster');
        echo json_encode(array('status'=>'ok','id'=>$id,'redirect'=>site_url('zoonosis/cluster')));
    }

    public function cluster_simpan() {
        $this->_auth();
        $u  = $this->_user();
        $p  = $this->input->post(NULL, TRUE);
        $id = isset($p['id']) ? (int)$p['id'] : 0;
        $id_penyakit = (int)$p['id_penyakit'];

        // Generate no_cluster
        if (empty($p['no_cluster'])) {
            $singkat = isset($this->PENYAKIT_ZOO[$id_penyakit]) ? $this->PENYAKIT_ZOO[$id_penyakit]['singkat'] : 'ZOO';
            $tahun   = date('Y');
            $last    = $this->db->query("SELECT no_cluster FROM ewarn_ghs_zoonosis_cluster WHERE no_cluster LIKE 'CLU-{$singkat}-{$tahun}-%' ORDER BY id DESC LIMIT 1")->row_array();
            $num     = $last ? (int)substr($last['no_cluster'], -4) + 1 : 1;
            $no_cluster = 'CLU-'.$singkat.'-'.$tahun.'-'.str_pad($num, 4, '0', STR_PAD_LEFT);
        } else {
            $no_cluster = $p['no_cluster'];
        }

        $data = array(
            'no_cluster'          => $no_cluster,
            'id_penyakit'         => $id_penyakit,
            'id_prop'             => $p['id_prop'] ? (int)$p['id_prop'] : NULL,
            'id_kota'             => $p['id_kota'] ? (int)$p['id_kota'] : NULL,
            'nama_cluster'        => $p['nama_cluster'] ?: NULL,
            'tgl_mulai'           => $p['tgl_mulai'] ?: NULL,
            'tgl_selesai'         => $p['tgl_selesai'] ?: NULL,
            'sumber_paparan'      => $p['sumber_paparan'] ?: NULL,
            'lokasi_paparan'      => $p['lokasi_paparan'] ?: NULL,
            'jumlah_terpapar'     => $p['jumlah_terpapar'] ? (int)$p['jumlah_terpapar'] : NULL,
            'status_cluster'      => (int)$p['status_cluster'],
            'no_ebs'              => isset($p['no_ebs']) && is_array($p['no_ebs']) ? implode(',', $p['no_ebs']) : (isset($p['no_ebs']) ? $p['no_ebs'] : NULL),
            'no_klb'              => $p['no_klb'] ?: NULL,
            'keterangan'          => $p['keterangan'] ?: NULL,
            'update_user'         => $u['username'],
            'update_date'         => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', $id)->update('ewarn_ghs_zoonosis_cluster', $data);
        } else {
            $data['create_user'] = $u['username'];
            $this->db->insert('ewarn_ghs_zoonosis_cluster', $data);
            $id = $this->db->insert_id();
        }

        // Simpan PE ke cluster
        $pe_ids = isset($p['pe_ids']) ? $p['pe_ids'] : array();
        if (!empty($pe_ids)) {
            $this->db->where('id_cluster', $id)->delete('ewarn_ghs_zoonosis_cluster_pe');
            foreach ($pe_ids as $pe_id) {
                $this->db->insert('ewarn_ghs_zoonosis_cluster_pe', array('id_cluster'=>$id,'id_pe'=>(int)$pe_id));
            }
        }
        echo json_encode(array('status'=>'ok','id'=>$id,'no_cluster'=>$no_cluster));
    }

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
        $start  = (int)$this->input->get('start');
        $length = (int)$this->input->get('length') ?: 25;
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $id_kec      = (int)$this->input->get('id_kec');
        $id_pusk     = (int)$this->input->get('id_pusk');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $cari        = $this->input->get('cari');
        $start  = (int)$this->input->get('start');
        $length = (int)$this->input->get('length') ?: 25;
        $draw   = (int)$this->input->get('draw');
        $result = $this->zm->get_daftar($id_penyakit, $id_prop, $id_kota, $id_kec, $id_pusk, $tgl1, $tgl2, $cari, $this->kel_place, $this->detail_place, $length, $start);
        echo json_encode(array('draw'=>$draw,'recordsTotal'=>$result['total'],'recordsFiltered'=>$result['total'],'data'=>$result['data']));
    }

    // FORM PE
    public function form($id_penyakit=0) {
        $this->_auth();
        $id_penyakit = (int)$id_penyakit;
        if (!isset($this->PENYAKIT_ZOO[$id_penyakit])) { redirect('zoonosis'); }

        // OCR pre-fill: ambil dari GET params jika ada
        $ocr_fields = array('nama_pasien','nik','umur','jenis_kelamin','alamat',
            'tgl_bergejala','tgl_laporan','tgl_pe','nama_petugas','no_telp',
            'dp_tanggal','dp_lokasi','dp_hpr','dp_sabun','dp_sar');
        $ocr_data = array();
        foreach($ocr_fields as $f){
            $v = $this->input->get($f);
            if($v !== FALSE && $v !== '') $ocr_data[$f] = $v;
        }
        $from_ocr = !empty($ocr_data);

        $data = array(
            'title'       => 'Form PE - '.$this->PENYAKIT_ZOO[$id_penyakit]['nama'].($from_ocr?' [OCR]':''),
            'id_penyakit' => $id_penyakit,
            'info_p'      => $this->PENYAKIT_ZOO[$id_penyakit],
            'list_prop'   => $this->db->query("SELECT id, propinsi FROM ewarn_propinsi WHERE aktif='Y' ORDER BY propinsi")->result_array(),
            'user'        => $this->_user(),
            'level'       => $this->_level(),
            'from_ocr'    => $from_ocr,
            'ocr_data'    => $ocr_data,
            'pe'          => array_merge(array(
                'no_pe'        => $this->_generate_no_pe($id_penyakit),
                'id_prop'      => isset($this->detail_place['id_prop'])      ? $this->detail_place['id_prop']      : '',
                'id_kota'      => isset($this->detail_place['id_kota'])      ? $this->detail_place['id_kota']      : '',
                'id_kecamatan' => isset($this->detail_place['id_distrik'])   ? $this->detail_place['id_distrik']   : '',
                'id_puskesmas' => isset($this->detail_place['id_puskesmas']) ? $this->detail_place['id_puskesmas'] : '',
                'tgl_pe'       => date('Y-m-d'),
                'tgl_laporan'  => date('Y-m-d'),
            ), $ocr_data),
            'detail'      => $this->zm->get_eav_template($id_penyakit),
            'anggota'     => array(),
            'kontak_pn'   => array(),
            'kontak_pe'   => array(),
            'tim_pe'      => array(),
            'kontak_kasus'=> array(),
            'kontak_gs'   => array(),
            'list_ebs'    => $this->zm->get_ebs_by_penyakit($id_penyakit, $this->kel_place, $this->detail_place),
            'list_diagnosa' => $this->zm->get_diagnosa_by_penyakit($id_penyakit),
        );
        $this->template->build('form_pe', $data);
    }

    public function edit($id=0) {
        $this->form_edit($id);
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
            'anggota'     => $this->zm->get_anggota_serumah($id),
            'kontak_pn'   => $this->zm->get_kontak_pneumonia($id),
            'kontak_pe'   => $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_pe')->result_array(),
            'tim_pe'      => $this->db->where('id_pe',$id)->get('ghs_zoonosis_tim_pe')->result_array(),
            'kontak_kasus'=> $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_kasus')->result_array(),
            'kontak_gs'   => $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_gejala')->result_array(),
        );
        $this->template->build('form_pe', $data);
    }

    public function simpan() {
        $this->_auth();
        $u  = $this->_user();
        $id = (int)$this->input->post('id');
        $id_penyakit = (int)$this->input->post('id_penyakit');
        $p  = $this->input->post(NULL, TRUE);
        // Validasi server-side
        $errors = array();
        if (empty($p["tgl_pe"])) $errors[] = "Tanggal PE wajib diisi";
        if (!empty($p["tgl_pe"]) && !empty($p["tgl_laporan"]) && $p["tgl_pe"] > $p["tgl_laporan"]) $errors[] = "Tanggal PE tidak boleh lebih dari Tanggal Laporan";
        if (!empty($p["nik"]) && (strlen($p["nik"]) !== 16 || !ctype_digit($p["nik"]))) $errors[] = "NIK harus 16 digit angka";
        if (isset($p["umur_thn"]) && (int)$p["umur_thn"] > 100) $errors[] = "Umur tahun tidak boleh lebih dari 100";
        if (isset($p["umur_bln"]) && ((int)$p["umur_bln"] < 0 || (int)$p["umur_bln"] > 11)) $errors[] = "Umur bulan harus antara 0-11";
        if (!empty($errors)) { echo json_encode(array("status"=>"error","message"=>implode("; ",$errors))); return; }
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
            'no_epid'              => isset($p['no_epid']) && $p['no_epid'] ? $p['no_epid'] : NULL,
            'kelamin'              => $p['kelamin'],
            'umur_thn'             => (int)$p['umur_thn'],
            'umur_bln'             => (int)$p['umur_bln'],
            'tgl_lahir'            => $p['tgl_lahir']         ?: NULL,
            'pekerjaan'            => $p['pekerjaan'],
            'alamat'               => $p['alamat'],
            'alamat_detail'        => isset($p['alamat_detail']) ? $p['alamat_detail'] : '',
            'kecamatan'            => $p['kecamatan'],
            'kelurahan'            => $p['kelurahan'],
            'tgl_sakit'            => $p['tgl_sakit']         ?: NULL,
            'tgl_bergejala'        => $p['tgl_bergejala']     ?: NULL,
            'tgl_laporan'          => $p['tgl_laporan']       ?: NULL,
            'tgl_pe'               => $p['tgl_pe']            ?: NULL,
            'nama_petugas'         => $p['nama_petugas'],
            'jabatan_petugas'      => isset($p['jabatan_petugas']) ? $p['jabatan_petugas'] : NULL,
            'telp_petugas'         => $p['telp_petugas'],
            'status_kasus'         => (int)$p['status_kasus'],
            'definisi_kasus'       => isset($p['definisi_kasus']) && $p['definisi_kasus']!=='' ? (int)$p['definisi_kasus'] : NULL,
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
            'tgl_vaksinasi_hewan'  => isset($p['tgl_vaksinasi_hewan'])  && $p['tgl_vaksinasi_hewan']  ? $p['tgl_vaksinasi_hewan']  : NULL,
            'nama_pemilik_hewan'   => isset($p['nama_pemilik_hewan'])   ? $p['nama_pemilik_hewan']   : NULL,
            'alamat_pemilik_hewan' => isset($p['alamat_pemilik_hewan']) ? $p['alamat_pemilik_hewan'] : NULL,
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

        // Save Rawat Inap repeatable via EAV
        if (isset($_POST['rs_nama'])) {
            // Hapus data rawat inap lama dari EAV
            $this->db->where('id_pe', $id)->where('submodule', 'Rawat Inap RS')->delete('ghs_zoonosis_pe_detail');
            $rs_rows = array();
            foreach ($_POST['rs_nama'] as $ri => $rsnm) {
                if (!trim($rsnm)) continue;
                $rs_rows[] = array('id_pe'=>$id,'submodule'=>'Rawat Inap RS','var_key'=>'rs'.$ri.'_nama','var_label'=>'Nama RS '.($ri+1),'var_value'=>trim($rsnm),'var_type'=>'text');
                $rs_rows[] = array('id_pe'=>$id,'submodule'=>'Rawat Inap RS','var_key'=>'rs'.$ri.'_tgl','var_label'=>'Tgl Masuk RS '.($ri+1),'var_value'=>isset($_POST['rs_tgl'][$ri])?$_POST['rs_tgl'][$ri]:'','var_type'=>'date');
                $rs_rows[] = array('id_pe'=>$id,'submodule'=>'Rawat Inap RS','var_key'=>'rs'.$ri.'_ket','var_label'=>'Keterangan RS '.($ri+1),'var_value'=>isset($_POST['rs_ket'][$ri])?trim($_POST['rs_ket'][$ri]):'','var_type'=>'text');
            }
            if ($rs_rows) $this->db->insert_batch('ghs_zoonosis_pe_detail', $rs_rows);
        }

        // Save Kontak Penyelidikan
        $kontak_pe = array();
        if (isset($_POST['kpe_nama'])) {
            foreach ($_POST['kpe_nama'] as $idx => $nama) {
                if (trim($nama)) $kontak_pe[] = array(
                    'nama'    => trim($nama),
                    'jabatan' => isset($_POST['kpe_jabatan'][$idx]) ? trim($_POST['kpe_jabatan'][$idx]) : '',
                    'telp'    => isset($_POST['kpe_telp'][$idx])    ? trim($_POST['kpe_telp'][$idx])    : '',
                );
            }
        }
        $this->zm->save_kontak_pe($id, $kontak_pe);

        // Save Kontak Kasus Lain (Lepto + Anthraks)
        $kontak_kasus = array();
        if (isset($_POST['kk_nama'])) {
            foreach ($_POST['kk_nama'] as $idx => $nama) {
                if (trim($nama)) $kontak_kasus[] = array(
                    'id_pe'        => $id,
                    'nama'         => trim($nama),
                    'umur'         => isset($_POST['kk_umur'][$idx]) ? (int)$_POST['kk_umur'][$idx] : NULL,
                    'alamat'       => isset($_POST['kk_alamat'][$idx]) ? trim($_POST['kk_alamat'][$idx]) : '',
                    'hub_penderita'=> isset($_POST['kk_hub'][$idx])    ? trim($_POST['kk_hub'][$idx])    : '',
                    'tgl_kontak'   => isset($_POST['kk_tgl'][$idx])    ? $_POST['kk_tgl'][$idx]          : NULL,
                    'status'       => isset($_POST['kk_status'][$idx]) ? trim($_POST['kk_status'][$idx]) : '',
                );
            }
        }
        $this->db->where('id_pe', $id)->delete('ghs_zoonosis_kontak_kasus');
        if ($kontak_kasus) $this->db->insert_batch('ghs_zoonosis_kontak_kasus', $kontak_kasus);

        // Save Kontak Gejala Sama (Avian)
        $kontak_gs = array();
        if (isset($_POST['kg_nama'])) {
            foreach ($_POST['kg_nama'] as $idx => $nama) {
                if (trim($nama)) $kontak_gs[] = array(
                    'id_pe'        => $id,
                    'nama'         => trim($nama),
                    'umur'         => isset($_POST['kg_umur'][$idx]) ? (int)$_POST['kg_umur'][$idx] : NULL,
                    'alamat'       => isset($_POST['kg_alamat'][$idx]) ? trim($_POST['kg_alamat'][$idx]) : '',
                    'hub_penderita'=> isset($_POST['kg_hub'][$idx])    ? trim($_POST['kg_hub'][$idx])    : '',
                    'tgl_kontak'   => isset($_POST['kg_tgl'][$idx])    ? $_POST['kg_tgl'][$idx]          : NULL,
                    'status'       => isset($_POST['kg_status'][$idx]) ? trim($_POST['kg_status'][$idx]) : '',
                );
            }
        }
        $this->db->where('id_pe', $id)->delete('ghs_zoonosis_kontak_gejala');
        if ($kontak_gs) $this->db->insert_batch('ghs_zoonosis_kontak_gejala', $kontak_gs);

        // Save Tim PE
        $tim_pe = array();
        if (isset($_POST['tpe_nama'])) {
            foreach ($_POST['tpe_nama'] as $idx => $nama) {
                if (trim($nama)) $tim_pe[] = array(
                    'nama'   => trim($nama),
                    'kantor' => isset($_POST['tpe_kantor'][$idx]) ? trim($_POST['tpe_kantor'][$idx]) : '',
                    'telp'   => isset($_POST['tpe_telp'][$idx])   ? trim($_POST['tpe_telp'][$idx])   : '',
                );
            }
        }
        $this->zm->save_tim_pe($id, $tim_pe);

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
            'title'      => 'Detail PE #'.$pe['no_pe'],
            'pe'         => $pe,
            'detail'     => $this->zm->get_detail_by_pe($id),
            'penyakit'   => $this->PENYAKIT_ZOO,
            'user'       => $this->_user(),
            'level'      => $this->_level(),
            'anggota'    => $this->zm->get_anggota_serumah($id),
            'kontak_pn'  => $this->zm->get_kontak_pneumonia($id),
            'kontak_pe'  => $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_pe')->result_array(),
            'tim_pe'     => $this->db->where('id_pe',$id)->get('ghs_zoonosis_tim_pe')->result_array(),
            'kontak_kasus' => $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_kasus')->result_array(),
            'kontak_gs'  => $this->db->where('id_pe',$id)->get('ghs_zoonosis_kontak_gejala')->result_array(),
            'rawat_inap' => $this->db->where('id_pe',$id)->where('submodule','Rawat Inap RS')->get('ghs_zoonosis_pe_detail')->result_array(),
            'spesimen'   => $this->db->where('id_pe',$id)->where('submodule','Spesimen Lab')->get('ghs_zoonosis_pe_detail')->result_array(),
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
        $id_prop     = (int)$this->input->get('id_prop');
        echo json_encode($this->zm->get_per_prop($id_penyakit, $tgl1, $tgl2, $id_prop));
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
        'no_pe','diagnosa_no','tgl_laporan','tgl_pe','no_ebs','nama_petugas','jabatan_petugas','telp_petugas',
        'id_prop','id_kota','id_puskesmas','id_kecamatan',
        'kd_prop_kasus','kd_kota_kasus','id_kecamatan_kasus',
        'nama_pasien','nama_kk','nik','no_epid','kelamin','umur_thn','umur_bln','umur_hari','tgl_lahir','pekerjaan','telp_pasien',
        'alamat','alamat_detail','alamat_kerja','kontak_darurat','tgl_fasyankes','nama_ortu','umur_hari','alamat_kerja_detail','telp_kontak_darurat','kelurahan','kecamatan',
        'tgl_bergejala','tgl_sakit','tgl_pajanan','status_kasus','akhir_no','tgl_meninggal','gejala',
        'riwayat_kontak_hewan','jenis_hewan','tgl_kontak','lokasi_kontak',
        'riwayat_vaksinasi','jenis_vaksin','tgl_vaksinasi_hewan','nama_pemilik_hewan','alamat_pemilik_hewan','oseltamivir','tgl_oseltamivir',
        'nama_rs','tgl_masuk_rs','jumlah_anggota_serumah','diperiksa_lab','jenis_sample','tgl_ambil_sample','tgl_kirim_sample','tgl_hasil_lab','tgl_vaksin_influenza',
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


    public function search_ebs() {
        $this->_auth();
        $q       = $this->input->get('q');
        $id_prop = (int)$this->input->get('id_prop');
        $id_kota = (int)$this->input->get('id_kota');
        if (!$id_prop && !$id_kota && strlen($q) < 3) { echo json_encode(array()); return; }
        $sql = "SELECT e.no_ebs, d.data as diagnosa, k.kota,
             pe.no_pe as sudah_pe, pe.id as id_pe
             FROM ewarn_form_ebs_new e
             LEFT JOIN ewarn_data_combo d ON d.id=e.diagnosa_no
             LEFT JOIN ewarn_distrik dist ON dist.id=e.id_distrik
             LEFT JOIN ewarn_kota k ON k.id=dist.id_kota
             LEFT JOIN ewarn_propinsi pr ON pr.id=k.id_prop
             LEFT JOIN ewarn_ghs_zoonosis_pe pe ON pe.no_ebs=e.no_ebs
             WHERE e.diagnosa_no IN(18,31,226,32,294,222,24)";
        if (strlen($q) >= 2) {
            $ql = $this->db->escape_like_str($q);
            $sql .= " AND e.no_ebs LIKE '%{$ql}%'";
        }
        if ($id_kota)     $sql .= " AND k.id=".intval($id_kota);
        elseif ($id_prop) $sql .= " AND pr.id=".intval($id_prop);
        $sql .= " ORDER BY e.no_ebs DESC LIMIT 30";
        $rows = $this->db->query($sql)->result_array();
        echo json_encode($rows);
    }




    public function get_lepto_faktor() {
        $this->_auth();
        $tahun   = (int)$this->input->get('tahun') ?: date('Y');
        $id_prop = (int)$this->input->get('id_prop');
        $id_kota = (int)$this->input->get('id_kota');

        $wil = "";
        if ($id_kota)     $wil = " AND z.id_kota=".intval($id_kota);
        elseif ($id_prop) $wil = " AND z.id_prop=".intval($id_prop);

        $base = " FROM ewarn_ghs_zoonosis_pe z WHERE z.id_penyakit=26 AND YEAR(z.tgl_laporan)={$tahun}{$wil}";

        // KPI
        $kpi = $this->db->query("SELECT COUNT(*) as total,
            SUM(CASE WHEN z.diagnosa_no IN(222,24) THEN 1 ELSE 0 END) as suspek,
            SUM(CASE WHEN z.diagnosa_no=24 THEN 1 ELSE 0 END) as konfirmasi,
            SUM(CASE WHEN z.akhir_no=2 THEN 1 ELSE 0 END) as meninggal
            {$base}")->row_array();

        // Distribusi usia
        $usia = $this->db->query("SELECT
            CASE
                WHEN z.umur_thn < 5 THEN '<5 tahun'
                WHEN z.umur_thn BETWEEN 5 AND 9 THEN '5-9 tahun'
                WHEN z.umur_thn BETWEEN 10 AND 14 THEN '10-14 tahun'
                WHEN z.umur_thn BETWEEN 15 AND 19 THEN '15-19 tahun'
                WHEN z.umur_thn BETWEEN 20 AND 35 THEN '20-35 tahun'
                WHEN z.umur_thn BETWEEN 36 AND 45 THEN '36-45 tahun'
                WHEN z.umur_thn BETWEEN 46 AND 64 THEN '46-64 tahun'
                ELSE '>65 tahun'
            END as kat_usia,
            COUNT(*) as n
            {$base} AND z.umur_thn IS NOT NULL
            GROUP BY kat_usia ORDER BY MIN(z.umur_thn)")->result_array();

        // Distribusi kelamin
        $kelamin = $this->db->query("SELECT
            CASE z.kelamin WHEN 'L' THEN 'Laki-laki' WHEN 'P' THEN 'Perempuan' ELSE 'Tidak Diketahui' END as kelamin,
            COUNT(*) as n
            {$base} GROUP BY z.kelamin")->result_array();

        // Distribusi pekerjaan (top 8)
        $pekerjaan = $this->db->query("SELECT pekerjaan, COUNT(*) as n
            {$base} AND pekerjaan IS NOT NULL AND pekerjaan!=''
            GROUP BY pekerjaan ORDER BY n DESC LIMIT 8")->result_array();

        echo json_encode(array(
            'kpi'      => $kpi,
            'usia'     => $usia,
            'kelamin'  => $kelamin,
            'pekerjaan'=> $pekerjaan,
        ));
    }

    public function get_rabies_faktor() {
        $this->_auth();
        $tahun = (int)$this->input->get('tahun') ?: date('Y');
        $id_prop = (int)$this->input->get('id_prop');

        $wil = $id_prop ? " AND id_prop_mapped=".intval($id_prop) : "";

        // KPI
        $kpi = $this->db->query("SELECT
            COUNT(*) as total,
            ROUND(AVG(gigitan_onset),1) as avg_inkubasi,
            ROUND(AVG(onset_kematian),1) as avg_onset_mat,
            SUM(CASE WHEN cuci_luka IN ('Iya','Ya','iya','ya') THEN 1 ELSE 0 END) as cuci_luka_ya,
            SUM(CASE WHEN vaksinasi_1 IN ('Iya','Ya','iya','ya') THEN 1 ELSE 0 END) as var_d1,
            SUM(CASE WHEN vaksinasi_2 IN ('Iya','Ya','iya','ya') THEN 1 ELSE 0 END) as var_d2,
            SUM(CASE WHEN vaksinasi_3 IN ('Iya','Ya','iya','ya') THEN 1 ELSE 0 END) as var_d3,
            SUM(CASE WHEN vaksinasi_4 IN ('Iya','Ya','iya','ya') THEN 1 ELSE 0 END) as var_d4
            FROM ewarn_rabies_surveilans WHERE tahun={$tahun}")->row_array();

        // HPR
        $hpr = $this->db->query("SELECT hpr, COUNT(*) as n FROM ewarn_rabies_surveilans
            WHERE tahun={$tahun} AND hpr IS NOT NULL AND hpr!=''
            GROUP BY hpr ORDER BY n DESC")->result_array();

        // Kondisi HPR
        $kondisi = $this->db->query("SELECT kondisi_hpr, COUNT(*) as n FROM ewarn_rabies_surveilans
            WHERE tahun={$tahun} AND kondisi_hpr IS NOT NULL AND kondisi_hpr!=''
            GROUP BY kondisi_hpr ORDER BY n DESC")->result_array();

        // Lokasi gigitan
        $lokasi = $this->db->query("SELECT lokasi_gigitan, COUNT(*) as n FROM ewarn_rabies_surveilans
            WHERE tahun={$tahun} AND lokasi_gigitan IS NOT NULL AND lokasi_gigitan!=''
            GROUP BY lokasi_gigitan ORDER BY n DESC LIMIT 10")->result_array();

        // Per bulan
        $trend = $this->db->query("SELECT bulan, COUNT(*) as n FROM ewarn_rabies_surveilans
            WHERE tahun={$tahun} GROUP BY bulan ORDER BY bulan ASC")->result_array();

        echo json_encode(array(
            'kpi' => $kpi,
            'hpr' => $hpr,
            'kondisi' => $kondisi,
            'lokasi' => $lokasi,
            'trend' => $trend,
        ));
    }

    public function get_map_data() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $diagnosa_no = (int)$this->input->get('diagnosa_no');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $level       = (int)$this->input->get('level') ?: 1;
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $dn_sql = $diagnosa_no ? " AND z.diagnosa_no=".intval($diagnosa_no) : "";

        if ($level == 4) {
            // Per Unit Pelapor - koordinat dari kecamatan
            $q = "SELECT pk.id, pk.puskesmas as nama, d.lat, d.lng, COUNT(z.id) as n
                  FROM ewarn_puskesmas pk
                  JOIN ewarn_distrik d ON d.id = pk.id_distrik
                  JOIN ewarn_kota k ON k.id = d.id_kota
                  LEFT JOIN ewarn_ghs_zoonosis_pe z ON z.id_puskesmas=pk.id AND z.id_penyakit=".intval($id_penyakit)." AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'{$dn_sql}
                  WHERE ".($id_kota?"k.id=".intval($id_kota):"k.id_prop=".intval($id_prop))." AND pk.aktif='Y' GROUP BY pk.id";
            $rows = $this->db->query($q)->result_array();
            $out = array();
            foreach ($rows as $r) { $out[] = array("id"=>$r["id"],"nama"=>$r["nama"],"lat"=>(float)$r["lat"],"lng"=>(float)$r["lng"],"n"=>(int)$r["n"]); }
            echo json_encode($out);
            return;
        } else 
        if ($level == 3) {
            // Per Kecamatan — pakai koordinat lat/lng dari ewarn_distrik
            $q = "SELECT d.id, d.distrik as nama, d.lat, d.lng,
                         COUNT(z.id) as n
                  FROM ewarn_distrik d
                  LEFT JOIN ewarn_ghs_zoonosis_pe z
                    ON z.id_kecamatan = d.id
                    AND z.id_penyakit=".intval($id_penyakit)."
                    AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'{$dn_sql}
                  WHERE d.id_kota=".intval($id_kota)."
                  GROUP BY d.id";
            $rows = $this->db->query($q)->result_array();
            $out = array();
            foreach ($rows as $r) {
                if ($r['n'] > 0 || true) {
                    $out[] = array(
                        'id'   => $r['id'],
                        'nama' => $r['nama'],
                        'lat'  => (float)$r['lat'],
                        'lng'  => (float)$r['lng'],
                        'n'    => (int)$r['n'],
                    );
                }
            }
            echo json_encode($out);
            return;
        } elseif ($level == 2) {
            // Per Kab/Kota
            $q = "SELECT CONCAT(LEFT(k.kode_depdagri,2),'.',RIGHT(k.kode_depdagri,2)) as kode,
                         k.kota as nama, COUNT(*) as n
                  FROM ewarn_ghs_zoonosis_pe z
                  JOIN ewarn_kota k ON k.id=z.id_kota
                  WHERE z.id_penyakit=".intval($id_penyakit)."
                  AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'{$dn_sql}";
            if ($id_prop) $q .= " AND z.id_prop=".intval($id_prop);
            $q .= " GROUP BY z.id_kota";
        } else {
            // Per Provinsi
            $q = "SELECT LPAD(p.kode_depdagri,2,'0') as kode,
                         p.id as id_prop, p.propinsi as nama, COUNT(*) as n
                  FROM ewarn_ghs_zoonosis_pe z
                  JOIN ewarn_propinsi p ON p.id=z.id_prop
                  WHERE z.id_penyakit=".intval($id_penyakit)."
                  AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'{$dn_sql}
                  GROUP BY z.id_prop";
            $rows = $this->db->query($q)->result_array();
            // Ambil EBS stats per provinsi
            if ($rows) {
                $ids = implode(',', array_column($rows, 'id_prop'));
                $tahun = date('Y');
                // Minggu aktif
                $mg = $this->db->query("SELECT week, DATE_SUB(week_date, INTERVAL 6 DAY) as w_start, week_date as w_end FROM ewarn_minggu WHERE week_date >= CURDATE() ORDER BY week_date ASC LIMIT 1")->row_array();
                $w_start = $mg ? $mg['w_start'] : date('Y-m-d', strtotime('monday this week'));
                $w_end   = $mg ? $mg['w_end']   : date('Y-m-d');
                $minggu_no = $mg ? $mg['week'] : '-';
                // EBS total (new + old)
                $ebs_total_new = array(); $tmp = $this->db->query("SELECT id_prop as id_wil, COUNT(id) as jml FROM ewarn_view_form_ebs_new WHERE id_prop IN ($ids) AND diagnosa_no IN(18,31,226,32,294,222,24) GROUP BY id_prop")->result_array();
                foreach ($tmp as $r) $ebs_total_new[$r['id_wil']] = (int)$r['jml'];
                $ebs_total_old = array(); $tmp2 = $this->db->query("SELECT id_prop as id_wil, COUNT(id) as jml FROM ewarn_view_form_ebs WHERE id_prop IN ($ids) AND diagnosa_no IN(18,31,226,32,294,222,24) AND create_date < '2026-03-29' GROUP BY id_prop")->result_array();
                foreach ($tmp2 as $r) $ebs_total_old[$r['id_wil']] = (int)$r['jml'];
                // EBS tahun ini
                $ebs_yr_new = array(); $tmp3 = $this->db->query("SELECT id_prop as id_wil, COUNT(id) as jml FROM ewarn_view_form_ebs_new WHERE id_prop IN ($ids) AND diagnosa_no IN(18,31,226,32,294,222,24) AND tahun=$tahun GROUP BY id_prop")->result_array();
                foreach ($tmp3 as $r) $ebs_yr_new[$r['id_wil']] = (int)$r['jml'];
                $ebs_yr_old = array(); $tmp4 = $this->db->query("SELECT id_prop as id_wil, COUNT(id) as jml FROM ewarn_view_form_ebs WHERE id_prop IN ($ids) AND diagnosa_no IN(18,31,226,32,294,222,24) AND tahun=$tahun AND create_date < '2026-03-29' GROUP BY id_prop")->result_array();
                foreach ($tmp4 as $r) $ebs_yr_old[$r['id_wil']] = (int)$r['jml'];
                // EBS minggu aktif (realtime)
                $ebs_mg = array(); $tmp5 = $this->db->query("SELECT id_prop as id_wil, COUNT(id) as jml FROM ewarn_view_form_ebs_new WHERE id_prop IN ($ids) AND diagnosa_no IN(18,31,226,32,294,222,24) AND tgl_laporan BETWEEN '$w_start' AND '$w_end 23:59:59' GROUP BY id_prop")->result_array();
                foreach ($tmp5 as $r) $ebs_mg[$r['id_wil']] = (int)$r['jml'];

                $out = array();
                foreach ($rows as $r) {
                    $ip = $r['id_prop'];
                    $out[$r['kode']] = array(
                        'n'         => (int)$r['n'],
                        'nama'      => $r['nama'],
                        'ebs_total' => isset($ebs_total_new[$ip])?$ebs_total_new[$ip]:0 + isset($ebs_total_old[$ip])?$ebs_total_old[$ip]:0,
                        'ebs_yr'    => isset($ebs_yr_new[$ip])?$ebs_yr_new[$ip]:0 + isset($ebs_yr_old[$ip])?$ebs_yr_old[$ip]:0,
                        'ebs_mg'    => isset($ebs_mg[$ip])?$ebs_mg[$ip]:0,
                        'minggu_no' => $minggu_no,
                    );
                }
                echo json_encode($out);
                return;
            }
        }
        $rows = $this->db->query($q)->result_array();
        $out = array();
        foreach ($rows as $r) {
            $out[$r['kode']] = array('n'=>(int)$r['n'], 'nama'=>$r['nama']);
        }
        echo json_encode($out);
    }

    public function get_ebs_id($no_ebs="") {
        $this->_auth();
        $no = $this->db->escape_str($no_ebs);
        $r = $this->db->query("SELECT id FROM ewarn_form_ebs_new WHERE no_ebs='$no' LIMIT 1")->row_array();
        echo json_encode($r ? $r["id"] : 0);
    }

    public function get_pe_by_ebs($no_ebs="") {
        $this->_auth();
        $no = $this->db->escape_str($no_ebs);
        $r = $this->db->query("SELECT id, no_pe FROM ewarn_ghs_zoonosis_pe WHERE no_ebs='$no' LIMIT 1")->row_array();
        echo json_encode($r ?: null);
    }

    public function get_disease_summary() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $tgl1 = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2 = $this->input->get('tgl2') ?: date('Y-m-d');
        $id_prop = (int)$this->input->get('id_prop');
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $tahun = date('Y');
        $where_wil = $id_prop ? " AND z.id_prop=".intval($id_prop) : "";

        // Ringkasan
        $q = "SELECT COUNT(*) as total,
                SUM(CASE WHEN akhir_no=2 THEN 1 ELSE 0 END) as meninggal,
                SUM(CASE WHEN diperiksa_lab=1 THEN 1 ELSE 0 END) as lab,
                SUM(CASE WHEN status_kasus=1 THEN 1 ELSE 0 END) as suspek,
                SUM(CASE WHEN status_kasus=2 THEN 1 ELSE 0 END) as konfirmasi,
                ROUND(SUM(CASE WHEN akhir_no=2 THEN 1 ELSE 0 END)/NULLIF(COUNT(*),0)*100,1) as cfr
              FROM ewarn_ghs_zoonosis_pe z
              WHERE z.id_penyakit=".intval($id_penyakit)."
              AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}' {$where_wil}";
        $summary = $this->db->query($q)->row_array();

        // Top 5 provinsi
        $q2 = "SELECT pr.propinsi as nama, COUNT(*) as n,
                 SUM(CASE WHEN akhir_no=2 THEN 1 ELSE 0 END) as meninggal
               FROM ewarn_ghs_zoonosis_pe z
               JOIN ewarn_propinsi pr ON pr.id=z.id_prop
               WHERE z.id_penyakit=".intval($id_penyakit)."
               AND z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}' {$where_wil}
               GROUP BY z.id_prop ORDER BY n DESC LIMIT 5";
        $top_prop = $this->db->query($q2)->result_array();

        // Trend 8 minggu terakhir
        $q3 = "SELECT m.week as minggu, COUNT(z.id) as n
               FROM ewarn_minggu m
               LEFT JOIN ewarn_ghs_zoonosis_pe z
                 ON z.id_penyakit=".intval($id_penyakit)."
                 AND z.tgl_laporan BETWEEN DATE_SUB(m.week_date, INTERVAL 6 DAY) AND m.week_date
                 {$where_wil}
               WHERE m.week_year={$tahun}
               AND m.week_date <= CURDATE()
               GROUP BY m.week ORDER BY m.week DESC LIMIT 8";
        $trend = array_reverse($this->db->query($q3)->result_array());

        echo json_encode(array(
            'summary'  => $summary,
            'top_prop' => $top_prop,
            'trend'    => $trend,
        ));
    }



    public function get_timeliness() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $dari        = $this->input->get('dari') ?: date('Y-01-01');
        $sampai      = $this->input->get('sampai') ?: date('Y-m-d');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');

        $wil = "";
        if ($id_kota)     $wil = " AND id_kota=".intval($id_kota);
        elseif ($id_prop) $wil = " AND id_prop=".intval($id_prop);
        $pwil = $id_penyakit ? " AND id_penyakit=".intval($id_penyakit) : "";

        // T1+T3: Query cepat tanpa JOIN
        $q = "SELECT
            COUNT(*) as total,
            SUM(CASE WHEN tgl_bergejala > '2000-01-01' THEN 1 ELSE 0 END) as t1_valid,
            SUM(CASE WHEN tgl_bergejala > '2000-01-01' AND DATEDIFF(tgl_laporan, tgl_bergejala) <= 7 THEN 1 ELSE 0 END) as t1_ok,
            SUM(CASE WHEN tgl_bergejala > '2000-01-01' AND DATEDIFF(tgl_laporan, tgl_bergejala) > 7 THEN 1 ELSE 0 END) as t1_late,
            ROUND(AVG(CASE WHEN tgl_bergejala > '2000-01-01' THEN DATEDIFF(tgl_laporan, tgl_bergejala) END),1) as t1_avg,
            SUM(CASE WHEN tgl_pe > '2000-01-01' AND tgl_pe >= tgl_laporan THEN 1 ELSE 0 END) as t3_valid,
            SUM(CASE WHEN tgl_pe > '2000-01-01' AND tgl_pe >= tgl_laporan AND DATEDIFF(tgl_pe, tgl_laporan) <= 7 THEN 1 ELSE 0 END) as t3_ok,
            SUM(CASE WHEN tgl_pe > '2000-01-01' AND tgl_pe >= tgl_laporan AND DATEDIFF(tgl_pe, tgl_laporan) BETWEEN 8 AND 14 THEN 1 ELSE 0 END) as t3_8_14,
            SUM(CASE WHEN tgl_pe > '2000-01-01' AND tgl_pe >= tgl_laporan AND DATEDIFF(tgl_pe, tgl_laporan) > 14 THEN 1 ELSE 0 END) as t3_gt14,
            ROUND(AVG(CASE WHEN tgl_pe > '2000-01-01' AND tgl_pe >= tgl_laporan THEN DATEDIFF(tgl_pe, tgl_laporan) END),1) as t3_avg
            FROM ewarn_ghs_zoonosis_pe
            WHERE tgl_laporan BETWEEN '{$dari}' AND '{$sampai}'
            AND tgl_laporan > '2000-01-01'
            {$wil}{$pwil}";

        $total = $this->db->query($q)->row_array();

        // Per penyakit — T1, T2 (via subquery), T3
        $per_p = $this->db->query("SELECT p.nama_penyakit, COUNT(*) as n,
            SUM(CASE WHEN z.tgl_bergejala > '2000-01-01' THEN 1 ELSE 0 END) as n_t1,
            SUM(CASE WHEN z.tgl_bergejala > '2000-01-01' AND DATEDIFF(z.tgl_laporan, z.tgl_bergejala) <= 7 THEN 1 ELSE 0 END) as t1_ok,
            SUM(CASE WHEN z.no_ebs IS NOT NULL AND z.no_ebs != '' THEN 1 ELSE 0 END) as n_t2,
            SUM(CASE WHEN z.tgl_pe > '2000-01-01' AND z.tgl_pe >= z.tgl_laporan THEN 1 ELSE 0 END) as n_t3,
            SUM(CASE WHEN z.tgl_pe > '2000-01-01' AND z.tgl_pe >= z.tgl_laporan AND DATEDIFF(z.tgl_pe, z.tgl_laporan) <= 7 THEN 1 ELSE 0 END) as t3_ok
            FROM ewarn_ghs_zoonosis_pe z
            LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit
            WHERE z.tgl_laporan BETWEEN '{$dari}' AND '{$sampai}'
            AND z.tgl_laporan > '2000-01-01'
            {$wil}
            GROUP BY z.id_penyakit ORDER BY n DESC")->result_array();

        // T2: Query terpisah dengan JOIN - hanya ambil data dengan no_ebs
        $t2 = $this->db->query("SELECT
            COUNT(*) as t2_valid,
            SUM(CASE WHEN ABS(DATEDIFF(e.create_date, z.tgl_laporan)) <= 1 THEN 1 ELSE 0 END) as t2_ok,
            SUM(CASE WHEN ABS(DATEDIFF(e.create_date, z.tgl_laporan)) > 1 THEN 1 ELSE 0 END) as t2_late,
            ROUND(AVG(ABS(DATEDIFF(e.create_date, z.tgl_laporan))),1) as t2_avg
            FROM ewarn_ghs_zoonosis_pe z
            INNER JOIN ewarn_form_ebs_new e ON e.no_ebs=z.no_ebs
            WHERE z.tgl_laporan BETWEEN '{$dari}' AND '{$sampai}'
            AND z.tgl_laporan > '2000-01-01'
            AND z.no_ebs IS NOT NULL AND z.no_ebs != ''
            {$wil}{$pwil}")->row_array();
        $total = array_merge($total, $t2 ? $t2 : array('t2_valid'=>0,'t2_ok'=>0,'t2_late'=>0,'t2_avg'=>0));

        echo json_encode(array(
            'total'   => $total,
            'per_p'   => $per_p,
            'dari'    => $dari,
            'sampai'  => $sampai,
            'note'    => array(
                't1' => 'Onset ke Deteksi: tgl_bergejala -> tgl_laporan. Data valid: '.$total['t1_valid'].' dari '.$total['total'].' PE (tgl_bergejala harus terisi)',
                't2' => 'Deteksi ke Notifikasi: tgl_laporan -> EBS create_date. Data valid: '.$total['t2_valid'].' dari '.$total['total'].' PE (harus ada no_ebs)',
                't3' => 'Notifikasi ke Respon PE: tgl_laporan -> tgl_pe. Data valid: '.$total['t3_valid'].' dari '.$total['total'].' PE (tgl_pe harus terisi, backfill umumnya kosong)',
            ),
        ));
    }


    public function ocr(){
        $this->_auth();
        $data = array(
            'title'   => 'OCR Form PE - Scan & Upload',
            'user'    => $this->_user(),
            'level'   => $this->_level(),
        );
        $this->template->build('ocr/upload', $data);
    }

    public function ocr_proses(){
        @ini_set('display_errors',1); @error_reporting(E_ALL);
        header('Content-Type: application/json');
        if(!isset($_FILES['foto_pe']) || $_FILES['foto_pe']['error'] !== 0){
            echo json_encode(array('status'=>'error','msg'=>'File tidak valid')); die();
        }
        $file = $_FILES['foto_pe'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext, array('jpg','jpeg','png','pdf'))){
            echo json_encode(array('status'=>'error','msg'=>'Format tidak didukung')); die();
        }
        $tmp_dir = '/tmp/ocr_pe/';
        if(!is_dir($tmp_dir)) mkdir($tmp_dir, 0755, true);
        $tmp_file = $tmp_dir . uniqid('pe_') . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $tmp_file);

        $img_file = $tmp_file;
        if($ext === 'pdf'){
            $img_file = str_replace('.pdf', '.png', $tmp_file);
            exec("convert -density 200 {$tmp_file}[0] -quality 90 {$img_file} 2>&1", $out, $ret);
            if($ret !== 0 || !file_exists($img_file)){
                echo json_encode(array('status'=>'error','msg'=>'Gagal konversi PDF')); die();
            }
        }

        // Pre-process image
        $img_info = @getimagesize($img_file);
        if($img_info){
            $img_src = null;
            if($img_info[2]==IMAGETYPE_JPEG) $img_src=imagecreatefromjpeg($img_file);
            elseif($img_info[2]==IMAGETYPE_PNG) $img_src=imagecreatefrompng($img_file);
            if($img_src){
                imagefilter($img_src, IMG_FILTER_GRAYSCALE);
                imagefilter($img_src, IMG_FILTER_CONTRAST, -30);
                $processed = $tmp_dir . uniqid('proc_') . '.png';
                imagepng($img_src, $processed);
                imagedestroy($img_src);
                $img_file = $processed;
            }
        }

        $output_base = $tmp_dir . uniqid('ocr_');
        exec("tesseract {$img_file} {$output_base} -l ind+eng 2>&1", $ocr_out, $ocr_ret);
        $txt_file = $output_base . '.txt';
        if(!file_exists($txt_file)){
            echo json_encode(array('status'=>'error','msg'=>'OCR gagal: '.implode(' ',$ocr_out))); die();
        }
        $raw_text = file_get_contents($txt_file);
        $parsed   = $this->_ocr_parse($raw_text);
        $preview  = 'data:image/png;base64,' . base64_encode(file_get_contents($img_file));
        @unlink($tmp_file); @unlink($img_file); @unlink($txt_file);
        if(isset($processed)) @unlink($processed);
        echo json_encode(array('status'=>'ok','raw_text'=>$raw_text,'parsed'=>$parsed,'preview'=>$preview));
    }

    private function _ocr_parse($text){
        $result = array();
        $lines  = explode("\n", $text);
        $patterns = array(
            'nama_pasien'   => array('/nama\s*[:\|]\s*(.+)/i'),
            'nik'           => array('/nik\s*[:\|]\s*([0-9]{10,16})/i'),
            'umur'          => array('/umur\s*[:\|]\s*([0-9]+)/i'),
            'jenis_kelamin' => array('/jenis\s*kelamin\s*[:\|]\s*(laki|perempuan|l|p)/i'),
            'alamat'        => array('/alamat\s*[:\|]\s*(.+)/i'),
            'tgl_bergejala' => array('/tgl[\s\.]*mulai\s*sakit\s*[:\|]\s*([0-9\-\/]+)/i'),
            'tgl_laporan'   => array('/tgl[\s\.]*laporan\s*[:\|]\s*([0-9\-\/]+)/i'),
            'tgl_pe'        => array('/tgl[\s\.]*pe\s*[:\|]\s*([0-9\-\/]+)/i'),
            'nama_petugas'  => array('/nama\s*petugas\s*[:\|]\s*(.+)/i'),
            'dp_tanggal'    => array('/tgl[\s\.]*gigitan\s*[:\|]\s*([0-9\-\/]+)/i'),
            'dp_lokasi'     => array('/lokasi\s*gigitan\s*[:\|]\s*(.+)/i'),
        );
        foreach($patterns as $field => $regexes){
            foreach($regexes as $regex){
                foreach($lines as $line){
                    if(preg_match($regex, $line, $m)){
                        $val = isset($m[1]) ? trim($m[1]) : '';
                        if($val){
                            if(strpos($field,'tgl')!==false){
                                if(preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/',$val,$dm))
                                    $val=sprintf('%04d-%02d-%02d',$dm[3],$dm[2],$dm[1]);
                            }
                            $result[$field]=$val; break 2;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function get_alert_summary() {
        $this->_auth();
        $id_prop = (int)$this->input->get('id_prop');
        $id_kota = (int)$this->input->get('id_kota');

        // Filter wilayah dari ewarn_ghs_zoonosis_pe
        $wil = "";
        if ($id_kota)     $wil = " AND z.id_kota=".intval($id_kota);
        elseif ($id_prop) $wil = " AND z.id_prop=".intval($id_prop);

        // Total EBS Zoonosis per periode langsung dari ewarn_form_ebs_new
        // ewarn_form_ebs_new pakai id_distrik (kab/kota) dan id_unit (puskesmas)
        // id_prop tidak ada langsung, pakai subquery via ewarn_kota
        $wil_ebs = "";
        if ($id_kota)     $wil_ebs = " AND id_distrik=".intval($id_kota);
        elseif ($id_prop) $wil_ebs = " AND id_distrik IN (SELECT id FROM ewarn_kota WHERE id_propinsi=".intval($id_prop).")";

        $q = "SELECT
            SUM(CASE WHEN create_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as minggu_ini,
            SUM(CASE WHEN create_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as bulan_ini,
            SUM(CASE WHEN YEAR(create_date) = YEAR(NOW()) THEN 1 ELSE 0 END) as tahun_ini,
            SUM(CASE WHEN create_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND jml_kematian>0 THEN 1 ELSE 0 END) as meninggal_minggu,
            SUM(CASE WHEN create_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND jml_kematian>0 THEN 1 ELSE 0 END) as meninggal_bulan
            FROM ewarn_form_ebs_new
            WHERE diagnosa_no IN (18,31,32,226,294,24,222)
            {$wil_ebs}";

        $total = $this->db->query($q)->row_array();

        // Per penyakit bulan ini - pakai ewarn_diagnosa bukan ewarn_penyakit
        $penyakit_map = array(18=>'GHPR/Rabies',31=>'Rabies Konfirmasi',32=>'Flu Burung Manusia',226=>'Suspek Flu Burung',294=>'Anthraks',24=>'Leptospirosis',222=>'Suspek Leptospirosis');
        $per_penyakit = $this->db->query("SELECT p.nama_penyakit, COUNT(*) as n
            FROM ewarn_form_ebs_new e
            JOIN ewarn_ghs_zoonosis_pe z ON z.no_ebs=e.no_ebs
            JOIN ewarn_penyakit p ON p.id=z.id_penyakit
            WHERE e.create_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            {$wil_ebs}
            GROUP BY z.id_penyakit ORDER BY n DESC")->result_array();

        echo json_encode(array(
            'total'       => $total,
            'per_penyakit'=> $per_penyakit,
        ));
    }

    public function get_alert_ebs() {
        $this->_auth();
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $window_jam  = 72; // 72 jam terakhir

        $where_penyakit = $id_penyakit ? " AND z.id_penyakit=".intval($id_penyakit) : "";
        $where_prop     = $id_prop     ? " AND z.id_prop=".intval($id_prop)          : "";

        // PE yang punya no_ebs dan EBS-nya dibuat dalam window_jam terakhir
        $q = "SELECT z.id, z.no_pe, z.no_ebs, z.tgl_pe, z.tgl_laporan,
                     p.nama_penyakit, pr.propinsi, k.kota,
                     e.create_date as ebs_create_date,
                     TIMESTAMPDIFF(HOUR, e.create_date, NOW()) as jam_lalu,
                     COALESCE(d.lat, k.lat) as lat,
                     COALESCE(d.lng, k.lng) as lng
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit
              LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop
              LEFT JOIN ewarn_kota k ON k.id=z.id_kota
              LEFT JOIN ewarn_distrik d ON d.id=z.id_kecamatan
              LEFT JOIN ewarn_form_ebs_new e ON e.no_ebs=z.no_ebs
              WHERE z.no_ebs IS NOT NULL
              AND z.no_ebs != ''
              AND e.create_date >= DATE_SUB(NOW(), INTERVAL {$window_jam} HOUR)
              {$where_penyakit} {$where_prop}
              ORDER BY e.create_date DESC
              LIMIT 50";

        $rows = $this->db->query($q)->result_array();
        echo json_encode($rows);
    }

    public function export_dashboard_csv() {
        $this->_auth();
        $this->_export_dashboard('csv');
    }
    public function export_dashboard_xls() {
        $this->_auth();
        $this->_export_dashboard('xls');
    }
    private function _export_dashboard($fmt='csv') {
        $id_penyakit = (int)$this->input->get('id_penyakit');
        $id_prop     = (int)$this->input->get('id_prop');
        $id_kota     = (int)$this->input->get('id_kota');
        $id_kec      = (int)$this->input->get('id_kec');
        $id_pusk     = (int)$this->input->get('id_pusk');
        $tgl1        = $this->input->get('tgl1') ?: date('Y-01-01');
        $tgl2        = $this->input->get('tgl2') ?: date('Y-m-d');
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);

        $where = "z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'";
        if ($id_penyakit) $where .= " AND z.id_penyakit=".intval($id_penyakit);
        if ($id_prop)     $where .= " AND z.id_prop=".intval($id_prop);
        if ($id_kota)     $where .= " AND z.id_kota=".intval($id_kota);
        if ($id_kec)      $where .= " AND z.id_kecamatan=".intval($id_kec);
        if ($id_pusk)     $where .= " AND z.id_puskesmas=".intval($id_pusk);

        $rows = $this->db->query("
            SELECT z.no_pe, p.nama_penyakit, pr.propinsi, k.kota,
                   pk.puskesmas as unit_pelapor,
                   z.tgl_laporan, z.tgl_pe, z.tgl_bergejala,
                   z.nama_pasien, z.nik, z.kelamin,
                   z.umur_thn, z.umur_bln, z.pekerjaan,
                   CASE z.status_kasus WHEN 0 THEN 'Suspek' WHEN 1 THEN 'Probable' WHEN 2 THEN 'Konfirmasi' WHEN 3 THEN 'Discarded' END as status_kasus,
                   CASE z.akhir_no WHEN 1 THEN 'Sembuh' WHEN 2 THEN 'Meninggal' WHEN 3 THEN 'Dirawat RS' WHEN 4 THEN 'Dirawat Klinik' WHEN 5 THEN 'Dirawat Rumah' END as akhir_no,
                   CASE z.diperiksa_lab WHEN 1 THEN 'Ya' ELSE 'Tidak' END as diperiksa_lab,
                   z.hasil_lab, z.nama_petugas, z.no_ebs
            FROM ewarn_ghs_zoonosis_pe z
            LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit
            LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop
            LEFT JOIN ewarn_kota k ON k.id=z.id_kota
            LEFT JOIN ewarn_puskesmas pk ON pk.id=z.id_puskesmas
            WHERE {$where}
            ORDER BY z.tgl_laporan DESC, z.id DESC
            LIMIT 10000
        ")->result_array();

        $headers = array('No PE','Penyakit','Provinsi','Kab/Kota','Unit Pelapor',
            'Tgl Laporan','Tgl PE','Tgl Bergejala','Nama Pasien','NIK','Kelamin',
            'Umur (Thn)','Umur (Bln)','Pekerjaan','Status Kasus','Kondisi Akhir',
            'Diperiksa Lab','Hasil Lab','Petugas PE','No EBS');

        $fname = 'pe_zoonosis_'.$tgl1.'_'.$tgl2;

        if ($fmt === 'xls') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$fname.'.xls"');
            echo '<table border="1">';
            echo '<tr>'; foreach($headers as $h) echo '<th>'.htmlspecialchars($h).'</th>'; echo '</tr>';
            foreach($rows as $r) {
                echo '<tr>';
                foreach($headers as $i=>$h) {
                    $keys = array('no_pe','nama_penyakit','propinsi','kota','unit_pelapor','tgl_laporan','tgl_pe','tgl_bergejala','nama_pasien','nik','kelamin','umur_thn','umur_bln','pekerjaan','status_kasus','akhir_no','diperiksa_lab','hasil_lab','nama_petugas','no_ebs');
                    echo '<td>'.htmlspecialchars($r[$keys[$i]]?:'-').'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$fname.'.csv"');
            $out = fopen('php://output','w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($out, $headers);
            foreach($rows as $r) {
                fputcsv($out, array($r['no_pe'],$r['nama_penyakit'],$r['propinsi'],$r['kota'],$r['unit_pelapor'],$r['tgl_laporan'],$r['tgl_pe'],$r['tgl_bergejala'],$r['nama_pasien'],$r['nik'],$r['kelamin'],$r['umur_thn'],$r['umur_bln'],$r['pekerjaan'],$r['status_kasus'],$r['akhir_no'],$r['diperiksa_lab'],$r['hasil_lab'],$r['nama_petugas'],$r['no_ebs']));
            }
            fclose($out);
        }
    }

}
