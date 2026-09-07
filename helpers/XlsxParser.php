<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * XlsxParser.php
 * Parser XLSX sederhana untuk PHP 5.6
 * Menggunakan ZipArchive + SimpleXML (built-in PHP)
 * Tidak memerlukan library eksternal
 *
 * Cara pakai:
 *   $parser = new XlsxParser('/path/to/file.xlsx');
 *   $rows   = $parser->get_sheet_rows('GHPR', 6); // sheet name, mulai baris ke-6
 *   $fields = $parser->get_row_fields('GHPR', 3);  // ambil field names dari baris 3
 */
class XlsxParser {

    private $zip;
    private $shared_strings = array();
    private $sheets         = array(); // sheet_name => sheet_id
    private $filepath;

    public function __construct($filepath) {
        $this->filepath = $filepath;
        $this->zip = new ZipArchive();
        if ($this->zip->open($filepath) !== TRUE) {
            throw new Exception('Gagal membuka file XLSX: ' . $filepath);
        }
        $this->_load_shared_strings();
        $this->_load_sheet_list();
    }

    public function __destruct() {
        if ($this->zip) {
            $this->zip->close();
        }
    }

    // ── Load shared strings (lookup teks dari index) ───────────────────────
    private function _load_shared_strings() {
        $xml_str = $this->zip->getFromName('xl/sharedStrings.xml');
        if (!$xml_str) return;

        $xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml) return;

        foreach ($xml->si as $si) {
            // Ambil semua teks (bisa multi-run <r><t>)
            $text = '';
            if (isset($si->t)) {
                $text = (string)$si->t;
            } elseif (isset($si->r)) {
                foreach ($si->r as $r) {
                    if (isset($r->t)) $text .= (string)$r->t;
                }
            }
            $this->shared_strings[] = $text;
        }
    }

    // ── Load daftar sheet ──────────────────────────────────────────────────
    private function _load_sheet_list() {
        $xml_str = $this->zip->getFromName('xl/workbook.xml');
        if (!$xml_str) return;

        $xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml) return;

        $ns = $xml->getNamespaces(true);
        $r_ns = isset($ns['r']) ? $ns['r'] : 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

        foreach ($xml->sheets->sheet as $sheet) {
            $attrs = $sheet->attributes($r_ns);
            $rid   = $attrs ? (string)$attrs->id : '';
            $name  = (string)$sheet['name'];
            $this->sheets[$name] = $rid;
        }

        // Load relationships untuk mapping rid -> file
        $rels_str = $this->zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($rels_str) {
            $rels_xml = simplexml_load_string($rels_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $rid_map  = array();
            foreach ($rels_xml->Relationship as $rel) {
                $rid_map[(string)$rel['Id']] = (string)$rel['Target'];
            }
            foreach ($this->sheets as $name => $rid) {
                $target = isset($rid_map[$rid]) ? $rid_map[$rid] : '';
                // target bisa 'worksheets/sheet1.xml' atau '../worksheets/sheet1.xml'
                $target = ltrim($target, '../');
                $this->sheets[$name] = 'xl/' . $target;
            }
        }
    }

    // ── Ambil path sheet ───────────────────────────────────────────────────
    private function _get_sheet_path($sheet_name) {
        // Exact match dulu
        if (isset($this->sheets[$sheet_name])) {
            return $this->sheets[$sheet_name];
        }
        // Case-insensitive
        foreach ($this->sheets as $name => $path) {
            if (strtolower($name) === strtolower($sheet_name)) {
                return $path;
            }
        }
        // Fallback: sheet pertama
        $vals = array_values($this->sheets);
        return isset($vals[0]) ? $vals[0] : null;
    }

    // ── Konversi kolom Excel (A, B, AA, ...) ke index 0-based ─────────────
    private function _col_to_index($col) {
        $col   = strtoupper($col);
        $index = 0;
        $len   = strlen($col);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $index - 1; // 0-based
    }

    // ── Parse cell reference (A1 -> col=0, row=1) ─────────────────────────
    private function _parse_ref($ref) {
        preg_match('/^([A-Z]+)(\d+)$/', strtoupper($ref), $m);
        return array(
            'col' => isset($m[1]) ? $this->_col_to_index($m[1]) : 0,
            'row' => isset($m[2]) ? (int)$m[2] : 0,
        );
    }

    // ── Ambil nilai cell ───────────────────────────────────────────────────
    private function _get_cell_value($cell_xml) {
        $type = (string)$cell_xml['t'];
        $val  = isset($cell_xml->v) ? (string)$cell_xml->v : '';

        if ($type === 's') {
            // Shared string
            $idx = (int)$val;
            return isset($this->shared_strings[$idx]) ? $this->shared_strings[$idx] : '';
        } elseif ($type === 'inlineStr') {
            return isset($cell_xml->is->t) ? (string)$cell_xml->is->t : '';
        } elseif ($type === 'b') {
            return $val === '1' ? 1 : 0;
        } else {
            // Number atau date — kembalikan as-is
            return $val;
        }
    }

    // ── Ambil satu baris sebagai array (0-based index kolom) ──────────────
    private function _parse_row($row_xml, $max_col = 100) {
        $cells = array_fill(0, $max_col, '');
        foreach ($row_xml->c as $cell) {
            $ref  = (string)$cell['r'];
            $info = $this->_parse_ref($ref);
            $ci   = $info['col'];
            if ($ci < $max_col) {
                $cells[$ci] = $this->_get_cell_value($cell);
            }
        }
        return $cells;
    }

    // ── Public: ambil field names dari baris tertentu ──────────────────────
    public function get_row_fields($sheet_name, $row_num) {
        $path = $this->_get_sheet_path($sheet_name);
        if (!$path) return array();

        $xml_str = $this->zip->getFromName($path);
        if (!$xml_str) return array();

        $xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml) return array();

        $ns = $xml->getNamespaces(true);
        $def_ns = isset($ns['']) ? $ns[''] : '';

        foreach ($xml->sheetData->row as $row) {
            $r = (int)$row['r'];
            if ($r == $row_num) {
                return $this->_parse_row($row, 60);
            }
        }
        return array();
    }

    // ── Public: ambil semua baris data (mulai dari start_row) ─────────────
    public function get_sheet_rows($sheet_name, $start_row = 6, $max_rows = 500) {
        $path = $this->_get_sheet_path($sheet_name);
        if (!$path) {
            throw new Exception('Sheet "' . $sheet_name . '" tidak ditemukan. Sheet tersedia: ' . implode(', ', array_keys($this->sheets)));
        }

        $xml_str = $this->zip->getFromName($path);
        if (!$xml_str) {
            throw new Exception('Gagal membaca sheet: ' . $path);
        }

        $xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml) {
            throw new Exception('Gagal parse XML sheet');
        }

        $rows  = array();
        $count = 0;

        foreach ($xml->sheetData->row as $row) {
            $r = (int)$row['r'];
            if ($r < $start_row) continue;
            if ($count >= $max_rows) break;

            $cells = $this->_parse_row($row, 60);

            // Cek apakah baris kosong
            $non_empty = array_filter($cells, function($v) { return $v !== '' && $v !== null; });
            if (empty($non_empty)) continue;

            $rows[] = $cells;
            $count++;
        }

        return $rows;
    }

    // ── Public: get sheet names ────────────────────────────────────────────
    public function get_sheet_names() {
        return array_keys($this->sheets);
    }
}
