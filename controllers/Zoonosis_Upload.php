<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Zoonosis_upload.php
 * Upload & import template Excel PE Zoonosis
 * Menggunakan XlsxParser native (tanpa library eksternal, PHP 5.6 compatible)
 * Route: zoonosis_upload/index, /preview, /proses, /download_template
 */
class Zoonosis_Upload extends BackendController {

    private $PENYAKIT_ZOO;

    private $REQUIRED_FIELDS = array(
        'no_pe','id_prop','id_kota','nama_pasien','kelamin','tgl_sakit','status_kasus'
    );
    private $DATE_FIELDS = array(
        'tgl_laporan','tgl_pe','tgl_lahir','tgl_bergejala','tgl_sakit',
        'tgl_meninggal','tgl_kontak','tgl_oseltamivir','tgl_ambil_sample','tgl_kirim_sample'
    );
    private $INT_FIELDS = array(
        'id_prop','id_kota','id_puskesmas','umur_thn','umur_bln',
        'status_kasus','akhir_no','riwayat_kontak_hewan','riwayat_vaksinasi',
        'oseltamivir','diperiksa_lab'
    );
    private $ALL_FIELDS = array(
        'no_pe','tgl_laporan','tgl_pe','no_ebs','nama_petugas','telp_petugas',
        'id_prop','id_kota','id_puskesmas',
        'nama_pasien','nik','kelamin','umur_thn','umur_bln','tgl_lahir','pekerjaan',
        'alamat','kelurahan','kecamatan',
        'tgl_bergejala','tgl_sakit','status_kasus','akhir_no','tgl_meninggal','gejala',
        'riwayat_kontak_hewan','jenis_hewan','tgl_kontak','lokasi_kontak',
        'riwayat_vaksinasi','jenis_vaksin','oseltamivir','tgl_oseltamivir',
        'diperiksa_lab','jenis_sample','tgl_ambil_sample','tgl_kirim_sample',
        'nama_lab','hasil_lab','ket_lab','ket_lain',
    );

    public function __construct() {
        parent::__construct();
        $this->PENYAKIT_ZOO = array(
            8  => array('nama'=>'Gigitan Hewan Penular Rabies (GHPR)','singkat'=>'GHPR',    'sheet'=>'GHPR'),
            11 => array('nama'=>'Suspek Flu Burung Pada Manusia',      'singkat'=>'Avian Flu','sheet'=>'Avian Flu'),
            14 => array('nama'=>'Suspek Antrax',                       'singkat'=>'Anthrax', 'sheet'=>'Anthrax'),
            26 => array('nama'=>'Suspek Leptospirosis',                'singkat'=>'Lepto',   'sheet'=>'Leptospirosis'),
        );
        $this->load->helper(array('url','file'));
    }

    private function _auth() {
        if (!$this->authentication->is_loggedin()) { redirect(base_url('auth')); exit; }
    }
    private function _user() { return $this->session->userdata('userdata'); }

    // ── Halaman utama upload ─────────────────────────────────────────────────
    public function index() {
        $this->_auth();
        $data = array(
            'title'    => 'Upload Template Excel PE Zoonosis',
            'penyakit' => $this->PENYAKIT_ZOO,
            'user'     => $this->_user(),
        );
        $this->template->build('upload_excel', $data);
    }

    // ── Download template Excel ───────────────────────────────────────────────
    public function download_template() {
        $this->_auth();
        $file = dirname(FCPATH) . '/template_upload/Template_Import_PE_Zoonosis.xlsx';
        if (!file_exists($file)) {
            show_error('File template tidak ditemukan di: ' . $file . '. Hubungi admin untuk mengupload template.');
            return;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_PE_Zoonosis.xlsx"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache');
        readfile($file);
        exit;
    }

    // ── Preview: parse Excel, return JSON ────────────────────────────────────
    public function preview() {
        $this->_auth();

        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] != UPLOAD_ERR_OK) {
            echo json_encode(array('status'=>'error','msg'=>'File tidak ditemukan atau gagal upload.'));
            return;
        }

        $file = $_FILES['file_excel'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            echo json_encode(array('status'=>'error','msg'=>'Hanya file .xlsx yang didukung.'));
            return;
        }

        $id_penyakit = (int)$this->input->post('id_penyakit');
        if (!isset($this->PENYAKIT_ZOO[$id_penyakit])) {
            echo json_encode(array('status'=>'error','msg'=>'Pilih jenis penyakit terlebih dahulu.'));
            return;
        }

        // Simpan ke tmp
        $tmp_dir  = sys_get_temp_dir();
        $tmp_file = $tmp_dir . '/skdr_upload_' . uniqid() . '.xlsx';
        if (!move_uploaded_file($file['tmp_name'], $tmp_file)) {
            echo json_encode(array('status'=>'error','msg'=>'Gagal menyimpan file sementara.'));
            return;
        }

        $result = $this->_parse_xlsx($tmp_file, $id_penyakit);
        @unlink($tmp_file);

        echo json_encode($result);
    }

    // ── Proses simpan ke DB ───────────────────────────────────────────────────
    public function proses() {
        $this->_auth();
        $u = $this->_user();

        $rows_json   = $this->input->post('rows_json');
        $id_penyakit = (int)$this->input->post('id_penyakit');

        if (!$rows_json) {
            echo json_encode(array('status'=>'error','msg'=>'Data kosong.'));
            return;
        }

        $rows = json_decode($rows_json, true);
        if (!$rows || !is_array($rows)) {
            echo json_encode(array('status'=>'error','msg'=>'Format data tidak valid.'));
            return;
        }

        $inserted = 0;
        $skipped  = 0;
        $errors   = array();

        foreach ($rows as $i => $row) {
            // Cek duplikat no_pe
            if (!empty($row['no_pe'])) {
                $exists = $this->db->query(
                    "SELECT id FROM ghs_zoonosis_pe WHERE no_pe=? AND id_penyakit=? LIMIT 1",
                    array($row['no_pe'], $id_penyakit)
                )->row_array();
                if ($exists) {
                    $skipped++;
                    $errors[] = 'Baris ' . ($i+1) . ': No PE "' . $row['no_pe'] . '" sudah ada, dilewati.';
                    continue;
                }
            }

            // Build insert array
            $insert = array(
                'id_penyakit' => $id_penyakit,
                'create_user' => $u['username'],
            );

            foreach ($this->ALL_FIELDS as $f) {
                $val = isset($row[$f]) ? $row[$f] : NULL;
                if ($val === '' || $val === 'nan' || $val === 'None' || $val === false) $val = NULL;

                if (in_array($f, $this->INT_FIELDS)) {
                    $insert[$f] = ($val !== NULL && $val !== '') ? (int)$val : NULL;
                } elseif (in_array($f, $this->DATE_FIELDS)) {
                    $insert[$f] = ($val && $val !== '0000-00-00') ? $this->_clean_date($val) : NULL;
                } else {
                    $insert[$f] = $val;
                }
            }

            $this->db->insert('ghs_zoonosis_pe', $insert);
            if ($this->db->affected_rows() > 0) {
                $inserted++;
            } else {
                $errors[] = 'Baris ' . ($i+1) . ': gagal insert (' . $this->db->error()['message'] . ').';
            }
        }

        echo json_encode(array(
            'status'   => 'ok',
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ));
    }

    // ── Core: parse XLSX dengan native ZipArchive + SimpleXML ────────────────
    private function _parse_xlsx($filepath, $id_penyakit) {
        // Load XlsxParser
        $parser_path = APPPATH . '../_public/modules/zoonosis/helpers/XlsxParser.php';
        if (!file_exists($parser_path)) {
            return array('status'=>'error','msg'=>'XlsxParser tidak ditemukan di: ' . $parser_path);
        }
        require_once $parser_path;

        try {
            $parser     = new XlsxParser($filepath);
            $info_p     = $this->PENYAKIT_ZOO[$id_penyakit];
            $sheet_name = $info_p['sheet'];

            // Ambil field names dari baris 3
            $field_cells = $parser->get_row_fields($sheet_name, 3);
            if (empty($field_cells)) {
                return array('status'=>'error','msg'=>'Baris 3 (nama field) tidak ditemukan. Pastikan menggunakan template resmi SKDR.');
            }

            // Build field map: col_index => field_name
            $field_map = array();
            foreach ($field_cells as $ci => $val) {
                $val = trim($val);
                if ($val && in_array($val, $this->ALL_FIELDS)) {
                    $field_map[$ci] = $val;
                }
            }

            if (empty($field_map)) {
                return array('status'=>'error','msg'=>'Field header tidak dikenali. Pastikan menggunakan template resmi SKDR (cek baris 3).');
            }

            // Ambil data mulai baris 6 (baris 5 = contoh, skip)
            $raw_rows = $parser->get_sheet_rows($sheet_name, 6, 500);

            $rows   = array();
            $errors = array();

            foreach ($raw_rows as $ri => $cells) {
                $row_num = $ri + 6;
                $data    = array();

                foreach ($field_map as $ci => $field) {
                    $data[$field] = isset($cells[$ci]) ? trim($cells[$ci]) : '';
                }

                // Validasi
                $row_errors = $this->_validate_row($data, $row_num, $id_penyakit);
                if ($row_errors) {
                    foreach ($row_errors as $e) $errors[] = $e;
                    continue;
                }

                // Normalize
                $data = $this->_normalize_row($data, $id_penyakit);
                $rows[] = $data;
            }

            return array(
                'status'      => 'ok',
                'rows'        => $rows,
                'total'       => count($rows),
                'errors'      => $errors,
                'id_penyakit' => $id_penyakit,
                'penyakit'    => $info_p['nama'],
                'sheet_names' => $parser->get_sheet_names(),
            );

        } catch (Exception $e) {
            return array('status'=>'error','msg'=>'Gagal membaca file: ' . $e->getMessage());
        }
    }

    // ── Validasi satu baris ───────────────────────────────────────────────────
    private function _validate_row($data, $row_num, $id_penyakit) {
        $errors = array();
        $prefix = 'Baris ' . $row_num . ': ';

        foreach ($this->REQUIRED_FIELDS as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                $errors[] = $prefix . 'Field "' . $f . '" wajib diisi.';
            }
        }

        if (!empty($data['kelamin']) && !in_array(strtoupper($data['kelamin']), array('L','P'))) {
            $errors[] = $prefix . 'Kelamin harus L atau P, bukan "' . $data['kelamin'] . '".';
        }

        if (isset($data['status_kasus']) && $data['status_kasus'] !== '') {
            if (!in_array((int)$data['status_kasus'], array(0,1,2,3))) {
                $errors[] = $prefix . 'Status kasus harus 0/1/2/3.';
            }
        }

        foreach ($this->DATE_FIELDS as $f) {
            if (!empty($data[$f])) {
                $clean = $this->_clean_date($data[$f]);
                if ($clean && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $clean)) {
                    $errors[] = $prefix . 'Format tanggal "' . $f . '" tidak valid: "' . $data[$f] . '". Gunakan YYYY-MM-DD.';
                }
            }
        }

        return $errors;
    }

    // ── Normalisasi satu baris ────────────────────────────────────────────────
    private function _normalize_row($data, $id_penyakit) {
        if (isset($data['kelamin'])) $data['kelamin'] = strtoupper(trim($data['kelamin']));

        foreach ($this->INT_FIELDS as $f) {
            $data[$f] = (isset($data[$f]) && $data[$f] !== '') ? (int)$data[$f] : NULL;
        }

        foreach ($this->DATE_FIELDS as $f) {
            if (isset($data[$f]) && $data[$f] !== '') {
                $data[$f] = $this->_clean_date($data[$f]);
            } else {
                $data[$f] = NULL;
            }
        }

        // Non-Avian: hapus oseltamivir
        if ($id_penyakit != 11) {
            $data['oseltamivir']     = NULL;
            $data['tgl_oseltamivir'] = NULL;
        }

        return $data;
    }

    // ── Bersihkan format tanggal ──────────────────────────────────────────────
    private function _clean_date($val) {
        if (!$val || $val === '') return NULL;
        $val = trim($val);
        // Sudah YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;
        // Excel serial date (angka)
        if (is_numeric($val) && $val > 1000) {
            $unix = ($val - 25569) * 86400;
            return date('Y-m-d', $unix);
        }
        // DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        // MM/DD/YYYY
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
        }
        return NULL;
    }
}
