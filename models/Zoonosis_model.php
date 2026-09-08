<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Zoonosis_model extends CI_Model {

    private function _where_wilayah(&$q, $id_prop=0, $id_kota=0, $alias='z') {
        if ($id_kota)  $q .= " AND {$alias}.id_kota=".intval($id_kota);
        elseif ($id_prop) $q .= " AND {$alias}.id_prop=".intval($id_prop);
    }

    // Ringkasan per penyakit untuk dashboard
    public function get_ringkasan($id_penyakit, $tgl1, $tgl2, $id_prop=0, $id_kota=0) {
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $q = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status_kasus=2 THEN 1 ELSE 0 END) AS konfirmasi,
                SUM(CASE WHEN akhir_no=2     THEN 1 ELSE 0 END) AS meninggal,
                SUM(CASE WHEN diperiksa_lab=1 THEN 1 ELSE 0 END) AS diperiksa_lab
              FROM ewarn_ghs_zoonosis_pe z
              WHERE z.id_penyakit=".intval($id_penyakit)."
                AND z.tgl_sakit BETWEEN '{$tgl1}' AND '{$tgl2}'";
        $this->_where_wilayah($q, $id_prop, $id_kota);
        return $this->db->query($q)->row_array();
    }

    // Trend mingguan per penyakit
    public function get_trend($id_penyakit, $tahun, $id_prop=0, $id_kota=0) {
        $tahun = intval($tahun);
        $q = "SELECT
                m.week AS minggu,
                COUNT(z.id) AS total,
                SUM(CASE WHEN z.status_kasus=2 THEN 1 ELSE 0 END) AS konfirmasi,
                SUM(CASE WHEN z.akhir_no=2     THEN 1 ELSE 0 END) AS meninggal
              FROM ewarn_minggu m
              LEFT JOIN ewarn_ghs_zoonosis_pe z
                ON z.id_penyakit=".intval($id_penyakit)."
                AND z.tgl_sakit BETWEEN m.week_date AND DATE_ADD(m.week_date, INTERVAL 6 DAY)";
        if ($id_kota)       $q .= " AND z.id_kota=".intval($id_kota);
        elseif ($id_prop)   $q .= " AND z.id_prop=".intval($id_prop);
        $q .= " WHERE m.week_year={$tahun}
              GROUP BY m.week ORDER BY m.week ASC";
        return $this->db->query($q)->result_array();
    }

    // Daftar PE untuk tabel
    public function get_daftar($id_penyakit=0, $id_prop=0, $id_kota=0, $tgl1='', $tgl2='', $cari='', $kel_place=0, $detail_place=array()) {
        $tgl1 = $this->db->escape_str($tgl1 ?: date('Y-01-01'));
        $tgl2 = $this->db->escape_str($tgl2 ?: date('Y-m-d'));
        $q = "SELECT z.*,
                p.nama_penyakit,
                pr.propinsi,
                k.kota,
                pk.puskesmas AS unit_pelapor
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_penyakit p  ON p.id = z.id_penyakit
              LEFT JOIN ewarn_propinsi pr ON pr.id = z.id_prop
              LEFT JOIN ewarn_kota k      ON k.id  = z.id_kota
              LEFT JOIN ewarn_puskesmas pk ON pk.id = z.id_puskesmas
              WHERE COALESCE(z.tgl_laporan, z.tgl_sakit, z.tgl_pe, z.create_date) BETWEEN '{$tgl1}' AND '{$tgl2}'";
        if ($id_penyakit) $q .= " AND z.id_penyakit=".intval($id_penyakit);
        // Filter wilayah berdasarkan level user
        if ($kel_place == 2 && !empty($detail_place['id_kota'])) {
            $q .= " AND z.id_kota=".intval($detail_place['id_kota']);
        } elseif ($kel_place == 1 && !empty($detail_place['id_prop'])) {
            $q .= " AND z.id_prop=".intval($detail_place['id_prop']);
        } elseif (in_array($kel_place, array(3,4,5,6)) && !empty($detail_place["id_puskesmas"])) {
            $q .= " AND z.id_puskesmas=".intval($detail_place['id_puskesmas']);
        }
        if ($cari) {
            $c = $this->db->escape_str($cari);
            $q .= " AND (z.nama_pasien LIKE '%{$c}%' OR z.no_pe LIKE '%{$c}%' OR z.nik LIKE '%{$c}%')";
        }
        $q .= " ORDER BY z.tgl_sakit DESC, z.id DESC LIMIT 5000";
        return $this->db->query($q)->result_array();
    }

    // Ambil satu PE by id
    public function get_pe_by_id($id) {
        $q = "SELECT z.*,
                p.nama_penyakit,
                pr.propinsi,
                k.kota,
                pk.puskesmas AS unit_pelapor
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_penyakit p   ON p.id  = z.id_penyakit
              LEFT JOIN ewarn_propinsi pr  ON pr.id = z.id_prop
              LEFT JOIN ewarn_kota k       ON k.id  = z.id_kota
              LEFT JOIN ewarn_puskesmas pk ON pk.id = z.id_puskesmas
              WHERE z.id=".intval($id)." LIMIT 1";
        return $this->db->query($q)->row_array();
    }

    // Detail EAV per PE
    public function get_detail_by_pe($id_pe) {
        return $this->db->query(
            "SELECT * FROM ewarn_ghs_zoonosis_pe_detail WHERE id_pe=".intval($id_pe)." ORDER BY submodule, id"
        )->result_array();
    }

    // EBS terkait penyakit (untuk dropdown link no_ebs)
    public function get_ebs_by_penyakit($id_penyakit, $kel_place=0, $detail_place=array()) {
        $diagnosa_map = array(
            8  => array(18, 31),
            11 => array(226, 32),
            14 => array(294),
            26 => array(222, 24),
        );
        if (!isset($diagnosa_map[$id_penyakit])) return array();
        $ids = implode(',', $diagnosa_map[$id_penyakit]);
        $q = "SELECT e.no_ebs,
                     d.data AS diagnosa,
                     e.tgl_laporan AS tanggal,
                     pr.propinsi,
                     k.kota,
                     pk.puskesmas AS unit
              FROM ewarn_form_ebs_new e
              LEFT JOIN ewarn_data_combo d  ON d.id = e.diagnosa_no
              LEFT JOIN ewarn_distrik dist  ON dist.id = e.id_distrik
              LEFT JOIN ewarn_kota k        ON k.id = dist.id_kota
              LEFT JOIN ewarn_propinsi pr   ON pr.id = k.id_prop
              LEFT JOIN ewarn_puskesmas pk  ON pk.id = e.id_puskesmas
              WHERE e.diagnosa_no IN ({$ids})";
        if ($kel_place == 2 && !empty($detail_place['id_kota'])) {
            $q .= " AND dist.id_kota=".intval($detail_place['id_kota']);
        } elseif ($kel_place == 1 && !empty($detail_place['id_prop'])) {
            $q .= " AND k.id_prop=".intval($detail_place['id_prop']);
        } elseif (in_array($kel_place, array(3,4,5,6)) && !empty($detail_place["id_puskesmas"])) {
            $q .= " AND e.id_puskesmas=".intval($detail_place['id_puskesmas']);
        }
        $q .= " ORDER BY e.tgl_laporan DESC LIMIT 200";
        return $this->db->query($q)->result_array();
    }

    // Analisa per provinsi
    public function get_per_prop($id_penyakit, $tgl1, $tgl2) {
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $q = "SELECT pr.propinsi,
                COUNT(z.id) AS total,
                SUM(CASE WHEN z.status_kasus=2 THEN 1 ELSE 0 END) AS konfirmasi,
                SUM(CASE WHEN z.akhir_no=2     THEN 1 ELSE 0 END) AS meninggal,
                SUM(CASE WHEN z.diperiksa_lab=1 THEN 1 ELSE 0 END) AS diperiksa_lab
              FROM ewarn_propinsi pr
              LEFT JOIN ewarn_ghs_zoonosis_pe z
                ON z.id_prop=pr.id
                AND z.id_penyakit=".intval($id_penyakit)."
                AND z.tgl_sakit BETWEEN '{$tgl1}' AND '{$tgl2}'
              WHERE pr.aktif='Y'
              GROUP BY pr.id, pr.propinsi
              ORDER BY total DESC";
        return $this->db->query($q)->result_array();
    }

    // Breakdown status kasus
    public function get_status_breakdown($id_penyakit, $tgl1, $tgl2, $id_prop=0) {
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $q = "SELECT status_kasus, COUNT(*) AS total
              FROM ewarn_ghs_zoonosis_pe z
              WHERE z.id_penyakit=".intval($id_penyakit)."
                AND z.tgl_sakit BETWEEN '{$tgl1}' AND '{$tgl2}'";
        if ($id_prop) $q .= " AND z.id_prop=".intval($id_prop);
        $q .= " GROUP BY status_kasus ORDER BY status_kasus";
        return $this->db->query($q)->result_array();
    }

    // Dropdown puskesmas by kota
    public function get_puskesmas($id_kota) {
        return $this->db->query(
            "SELECT id, puskesmas FROM ewarn_puskesmas WHERE id_distrik IN
             (SELECT id FROM ewarn_distrik WHERE id_kota=".intval($id_kota).")
             AND aktif='Y' ORDER BY puskesmas"
        )->result_array();
    }

    // Load EAV template per penyakit (id_pe=0) untuk form PE baru
    public function get_eav_template($id_penyakit) {
        $submodule_map = array(
            8  => array('Gejala GHPR','Tata Laksana GHPR'),
            11 => array('Gejala Avian','Data Pendukung Avian','Klinis Avian','Identitas Avian'),
            14 => array('Gejala Anthraks','Data Pendukung Anthrax','Klinis Anthrax'),
            26 => array('Gejala Lepto','Data Pendukung Lepto','Klinis Lepto'),
        );
        if (!isset($submodule_map[$id_penyakit])) return array();
        $subs = $submodule_map[$id_penyakit];
        $in = implode("','", array_map(array($this->db,'escape_str'), $subs));
        return $this->db->query(
            "SELECT submodule, var_key, var_label, var_type, var_value
             FROM ewarn_ghs_zoonosis_pe_detail
             WHERE id_pe=0 AND submodule IN ('{$in}')
             ORDER BY id"
        )->result_array();
    }

    // Dropdown diagnosa_no per id_penyakit
    public function get_diagnosa_by_penyakit($id_penyakit) {
        $diagnosa_map = array(
            8  => array(18,31),
            11 => array(226, 32),   // Suspek Flu Burung + Flu Burung Pada Manusia
            14 => array(294),          // Anthraks
            26 => array(222,24),
        );
        if (!isset($diagnosa_map[$id_penyakit])) return array();
        $ids = implode(',', $diagnosa_map[$id_penyakit]);
        return $this->db->query(
            "SELECT id, data FROM ewarn_data_combo WHERE id IN ({$ids}) ORDER BY id"
        )->result_array();
    }
}
