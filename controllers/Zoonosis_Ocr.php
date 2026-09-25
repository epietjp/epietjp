<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Zoonosis_Ocr extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    private function _auth(){
        if(!$this->authentication->is_loggedin()){
            redirect(base_url('auth')); exit;
        }
    }

    public function index(){
        $this->_auth();
        $data = array('title' => 'OCR Form PE - Scan & Upload');
        $this->load->view('templates/header_simple', $data);
        $this->load->view('ocr/upload', $data);
        $this->load->view('templates/footer_simple');
    }

    public function proses(){
        $this->_auth();
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

        $img_src = imagecreatefromstring(file_get_contents($img_file));
        if($img_src){
            imagefilter($img_src, IMG_FILTER_GRAYSCALE);
            imagefilter($img_src, IMG_FILTER_CONTRAST, -30);
            imagefilter($img_src, IMG_FILTER_SHARPEN);
            $processed = $tmp_dir . uniqid('proc_') . '.png';
            imagepng($img_src, $processed);
            imagedestroy($img_src);
            $img_file = $processed;
        }

        $output_base = $tmp_dir . uniqid('ocr_');
        exec("tesseract {$img_file} {$output_base} -l ind+eng 2>&1", $ocr_out, $ocr_ret);
        $txt_file = $output_base . '.txt';
        if(!file_exists($txt_file)){
            echo json_encode(array('status'=>'error','msg'=>'OCR gagal')); die();
        }
        $raw_text = file_get_contents($txt_file);
        $parsed   = $this->_parse_ocr($raw_text);
        $preview  = 'data:image/png;base64,' . base64_encode(file_get_contents($img_file));

        @unlink($tmp_file); @unlink($img_file); @unlink($txt_file);
        if(isset($processed)) @unlink($processed);

        echo json_encode(array('status'=>'ok','raw_text'=>$raw_text,'parsed'=>$parsed,'preview'=>$preview));
    }

    private function _parse_ocr($text){
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
            'no_telp'       => array('/telp?\s*[:\|]\s*([0-9\-\+\s]{8,15})/i'),
            'dp_tanggal'    => array('/tgl[\s\.]*gigitan\s*[:\|]\s*([0-9\-\/]+)/i'),
            'dp_lokasi'     => array('/lokasi\s*gigitan\s*[:\|]\s*(.+)/i'),
            'dp_hpr'        => array('/jenis\s*hewan\s*[:\|]\s*(anjing|kucing|monyet)/i'),
        );
        foreach($patterns as $field => $regexes){
            foreach($regexes as $regex){
                foreach($lines as $line){
                    if(preg_match($regex, $line, $m)){
                        $val = isset($m[1]) ? trim($m[1]) : '';
                        if($val){
                            if(strpos($field,'tgl')!==false) $val=$this->_normalize_date($val);
                            if($field==='jenis_kelamin'){
                                $v=strtolower($val);
                                $val=in_array($v,array('l','laki','laki-laki'))?'L':'P';
                            }
                            $result[$field]=$val; break 2;
                        }
                    }
                }
            }
        }
        return $result;
    }

    private function _normalize_date($str){
        $str=trim($str);
        if(preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/',$str,$m))
            return sprintf('%04d-%02d-%02d',$m[3],$m[2],$m[1]);
        return $str;
    }
}
