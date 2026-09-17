<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data extends MX_Model {
	var $post=[];
	var $get=[];
	var $header=[];
	var $preference=[];
	var $data_Checks=[];
	var $limit=10;
	private $secret_key = '8f32a7b9c1d2e4f6a9b4e3c7d8a1f9e0c3b5a6d7e8f9a0b1c2d3e4f5a6b7c8d9';
    private $secret_iv  = 'f7a1c2d3e4f5b6a7c8d9e0f1a2b3c4d5';
	public function __construct()
    {
		parent::__construct();
	}

	function get_data_field(){
		$this->data_Checks[]=array('field'=>'id', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'propinsi', 'title'=>'Propinsi', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'kota', 'title'=>'Kota', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'distrik', 'title'=>'Distrik', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'kel_unit', 'title'=>'Id Unit', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'unit', 'title'=>'Unit', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'kode_depdagri_prop', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'kode_depdagri_kota', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'kode_depdagri_distrik', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'sts_rumor', 'title'=>'Status Rumor', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'sumber_laporan', 'title'=>'Sumber Laporan', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'sts_klb', 'title'=>'sts_klb', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'diagnosa', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'verifikasi', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'no_ebs', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'sumber_verifikasi', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'hasil_lab', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'penyakit', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur7', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur28', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur1', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur4', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur9', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur14', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur19', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur44', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur54', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur69', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'umur70', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'pria', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'wanita', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'populasi', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'tgl_mulai', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'tgl_akhir', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'tgl_diketahui', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'tgl_ditanggulangi', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'tgl_laporan', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'nama_pelapor', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'telp_pelapor', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'jml_kasus', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'jml_kematian', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'sts_lab_no', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'informasi', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'kronologi', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'tindakan', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'saran', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'respon24', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'formw1', 'title'=>'', 'rule'=>false);
		$this->data_Checks[]=array('field'=>'klb', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'lat', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'lng', 'title'=>'', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'tgl_laporan', 'title'=>'Tanggal Laporan', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'informasi', 'title'=>'Informasi', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'create_date', 'title'=>'Tanggal Entri', 'rule'=>true);
		$this->data_Checks[]=array('field'=>'update_date', 'title'=>'Terakhir Update', 'rule'=>true);
	}

	function get_param(){
		$kel='propinsi';
		if (isset($this->post['kel'])){
			$kel=$this->post['kel'];
		}
		$result=[];
		switch ($kel){
			case 'propinsi':
				if(isset($this->post['id'])){
					$this->db->where('id', intval($this->post['id']));
				}
				if(isset($this->post['kode_depdagri'])){
					$this->db->where('kode_depdagri', $this->post['kode_depdagri']);
				}
				$result=$this->db->select('id, kode_depdagri, kode, propinsi as name')->where('aktif','Y')->get(_TBL_PROPINSI)->result_array();
				break;
			case 'kota':
				if(isset($this->post['id'])){
					$this->db->where('id', intval($this->post['id']));
				}
				if(isset($this->post['id_prop'])){
					$this->db->where('id_prop', intval($this->post['id_prop']));
				}
				if(isset($this->post['kode_depdagri'])){
					$this->db->where('kode_depdagri', $this->post['kode_depdagri']);
				}
				if(isset($this->post['kode_depdagri_prop'])){
					$this->db->where('kode_depdagri_prop', $this->post['kode_depdagri_prop']);
				}
				$result=$this->db->select('id, kode_depdagri, kode_depdagri_prop, kota as name')->where('aktif','Y')->order_by('id_prop, kota')->get(_TBL_VIEW_KOTA)->result_array();
				break;
			case 'distrik':
				if(isset($this->post['id'])){
					$this->db->where('id', intval($this->post['id']));
				}
				if(isset($this->post['id_kota'])){
					$this->db->where('id_kota', intval($this->post['id_kota']));
				}
				if(isset($this->post['id_prop'])){
					$this->db->where('id_prop', intval($this->post['id_prop']));
				}
				if(isset($this->post['kode_depdagri'])){
					$this->db->where('kode_depdagri', $this->post['kode_depdagri']);
				}
				if(isset($this->post['kode_depdagri_prop'])){
					$this->db->where('kode_depdagri_prop', $this->post['kode_depdagri_prop']);
				}
				if(isset($this->post['kode_depdagri_kota'])){
					$this->db->where('kode_depdagri_kota', $this->post['kode_depdagri_kota']);
				}
				$result=$this->db->select('id, kode_depdagri, kode_depdagri_kota, kode_depdagri_prop, distrik as name')->where('aktif','Y')->order_by('id_kota, distrik')->get(_TBL_VIEW_DISTRIK)->result_array();
				break;
			case 'unit':
				$result=$this->db->select('id, data as name')->where('kelompok', 'sumber_data')->where('aktif','Y')->get(_TBL_DATA_COMBO)->result_array();
				break;
		}
		return $result;
	}

	function get_data_filter_ebs(){
		// if (isset($this->get['unit_pelapor'])){
		// 	$this->db->where('id_unit', intval($this->get['unit_pelapor']));
		// }
		if (isset($this->post['tahun'])){
			$this->db->where('tahun', intval($this->post['tahun']));
		}
		if (isset($this->post['tgl_buat_awal']) && isset($this->post['tgl_buat_akhir'])){
			if (!empty($this->post['tgl_buat_awal']) && !empty($this->post['tgl_buat_akhir'])){
				$this->db->where('tanggal >=', $this->post['tgl_buat_awal']);
				$this->db->where('tanggal <=', $this->post['tgl_buat_akhir']);
			}
		}elseif (isset($this->post['tgl_buat_awal'])){
			if (!empty($this->post['tgl_buat_awal'])){
				$this->db->where('tanggal', $this->post['tgl_buat_awal']);
			}
		}

		if (isset($this->post['tgl_laporan_awal']) && isset($this->post['tgl_laporan_akhir'])){
			if (!empty($this->post['tgl_laporan_awal']) && !empty($this->post['tgl_laporan_akhir'])){
				$this->db->where('tgl_laporan >=', $this->post['tgl_laporan_awal']);
				$this->db->where('tgl_laporan <=', $this->post['tgl_laporan_akhir']);
			}
		}elseif (isset($this->post['tgl_laporan_awal'])){
			if (!empty($this->post['tgl_laporan_awal'])){
				$this->db->where('tgl_laporan>=', $this->post['tgl_laporan_awal']);
			}
		}

		
		if (isset($this->post['kode_prop'])){
			if (!empty($this->post['kode_prop'])){
				$this->db->where('kode_depdagri_prop', intval($this->post['kode_prop']));
			}
		}
		if (isset($this->post['kode_kota'])){
			if (!empty($this->post['kode_kota'])){
				$this->db->where('kode_depdagri_kota', intval($this->post['kode_kota']));
			}
		}
		if (isset($this->post['kode_kecamatan'])){
			if (!empty($this->post['kode_kecamatan'])){
				$this->db->where('kode_depdagri_distrik', intval($this->post['kode_kecamatan']));
			}
		}
	}

	function ebs(){
		// $fields=['id', 'prop_code as prop_id', 'propinsi', 'city_code as kota_id', 'kota', 'distrik_code as distrik_id', 'distrik', 'puskesmas as unit_pelapor', 'tgl_laporan', 'minggu', 'nama_pasien', 'no_epid', 'alamat', 'lat', 'lng', 'kelamin', 'tgl_sakit', 'diag_awal', 'diag_akhir','create_date', 'update_date'];
		// $fields=['propinsi', 'kota', 'lokasi', 'sts_rumor', 'diagnosa', 'verifikasi', 'jml_kasus', 'jml_kematian', 'kronologi', 'tindakan', 'klb', 'lat', 'lng'];

		$this->get_data_field();
		if (isset($this->post['fields'])){
			if (!empty($this->post['fields'])){
				$fields=explode(',', $this->post['fields']);
			}
		}else{
			$fields=[];
		}

		if(count($fields)==0){
			foreach($this->data_Checks as $x){
				if ($x['rule']){
					$fields[]=$x['field'];
				}
			}
		}
		if (isset($this->post['limit'])){
			if (intval($this->post['limit'])>0){
				$this->limit=$this->post['limit'];
			}
		}
		$page = (isset($this->post['page']))?intval($this->post['page']):1;
		if($page<1){$page=1;}

		$this->get_data_filter_ebs();
		$rows = $this->db->get(_TBL_VIEW_FORM_EBS)->num_rows();
		$result['paging']['total']=$rows;
		$result['paging']['page']=$page;
		$result['paging']['limit']=$this->limit;
		$x=$rows%$this->limit;
		$tpage=0;
		if ($x>0){
			$tpage=1;
		}
		--$page;
		$result['paging']['total_page']=intval($rows/$this->limit)+$tpage;

		$this->get_data_filter_ebs();
		$this->db->limit($this->limit, ($page*$this->limit));
		
		$this->db->order_by('tgl_laporan desc');

		$rows=$this->db->select(implode(',',$fields))->get(_TBL_VIEW_FORM_EBS)->result_array();
		$result['data']=$rows;
		$result['status']=true;
		// die($this->db->last_query());
		// $result['sql']=$this->db->last_query();
		return $result;
	}

	function get_data_filter_tgc(){
		
		if (isset($this->post['unit'])){
			$this->db->where('unit', $this->post['unit']);
		}
		if (isset($this->post['golongan'])){
			$this->db->where('golongan', $this->post['golongan']);
		}
		if (isset($this->post['jabatan'])){
			$this->db->where('jabatan', $this->post['jabatan']);
		}
		if (isset($this->post['gender'])){
			$this->db->where('gender', $this->post['gender_id']);
		}
		
		if (isset($this->post['kode_prop'])){
			if (!empty($this->post['kode_prop'])){
				$this->db->where('id_prop', $this->post['kode_prop']);
			}
		}
		if (isset($this->post['kode_kota'])){
			if (!empty($this->post['kode_kota'])){
				$this->db->where('id_kota', $this->post['kode_kota']);
			}
		}
	}

	function tgc(){
		$this->data_Checks=[];
		$this->data_Checks=['unit'=>'Instansi', 'satuan_kerja'=>'Satuan Kerja', 'nik'=>'Nik', 'nama'=>'Nama', 'golongan'=>'Pangkat/Golongan', 'jabatan'=>'Jabatan / Profesi', 'sk'=>'Sk', 'sertifikat'=>'Sertifikat Pelatihan', 'pendidikan'=>'Pendidikan Terakhir', 'tahun_tugas'=>'Tahun Penugasan', 'email'=>'Alamat Email', 'hp'=>'Handphone', 'gender'=>'Gender','aktif'=>'Status Keanggotaan', 'lat'=>'Latitude', 'lng'=>'Longitude'];


		if (isset($this->post['limit'])){
			if (intval($this->post['limit'])>0){
				$this->limit=$this->post['limit'];
			}
		}
		$page = (isset($this->post['page']))?intval($this->post['page']):1;
		if($page<1){$page=1;}

		$this->get_data_filter_ebs();
		$fields=array_keys($this->data_Checks);
		// $this->db->select(implode(",",$fields));
		$rows = $this->db->get(_TBL_VIEW_TGC)->num_rows();
		$result['paging']['total']=$rows;
		$result['paging']['page']=$page;
		$result['paging']['limit']=intval($this->limit);
		$x=$rows%$this->limit;
		$tpage=0;
		if ($x>0){
			$tpage=1;
		}
		--$page;
		$result['paging']['total_page']=intval($rows/$this->limit)+$tpage;

		$this->get_data_filter_ebs();
		$this->db->limit($this->limit, ($page*$this->limit));
		
		$this->db->order_by('create_date');

		$rows=$this->db->select(implode(',',$fields))->get(_TBL_VIEW_TGC)->result_array();
		$result['data']=$rows;
		$result['status']=true;
		// die($this->db->last_query());
		// $result['sql']=$this->db->last_query();
		return $result;
	}

	function master_kode(){
		$sts=true;
		$result=[];
		if (isset($this->get['tipe'])){
			if($this->get['tipe']=='unit'){
				$this->db->select('id, data')->where('kelompok', 'sumber_data');
				$rows = $this->db->get(_TBL_DATA_COMBO)->result_array();
			}
			elseif($this->get['tipe']=='golongan'){
				$this->db->select('id, data')->where('kelompok', 'gol');
				$rows = $this->db->get(_TBL_DATA_COMBO)->result_array();
			}
			elseif($this->get['tipe']=='profesi'){
				$this->db->select('id, data')->where('kelompok', 'profesi');
				$rows = $this->db->get(_TBL_DATA_COMBO)->result_array();
			}elseif($this->get['tipe']=='gender'){
				$rows = [['id'=>'','data'=>''],['id'=>'L','data'=>'Pria'], ['id'=>'P','data'=>'Wanita']];
			}
		}else{
			$sts=false;
			$rows=[];
		}
		$x=[];
		sort($rows);
		if($rows){
			foreach($rows as $row){
				$x[]=['id'=>$row['id'],'nama'=>$row['data']];
			}
		}
		$result['data']=$x;
		$result['status']=$sts;
		return $result;
	}
	
	function saveSms($data=[]){
		try{
			$rows = $this->db->where('hp', $data['no_hp'])->get(_TBL_VIEW_PETUGAS_PUSKESMAS)->row();
			$result['nama']='';
			$result['puskesmas']='';
			if($data['sts_data']==0){
				$result['pesan'] = 'Format laporan salah, data minggu tidak ada!';
				$result['sts']=0;
			}elseif ($rows){
				$data['petugas_no']=$rows->id;
				$data['puskesmas_no']=$rows->id_puskesmas;
				$result['puskesmas']=$rows->puskesmas;
				$result['pesan']='hi %s, Laporan minggu %s-%s unt %s sudah kami terima dengan ID:%s, %s';
				$result['sts']=1;
				$result['nama']=$rows->petugas;
			}else{
				$result['pesan']='No HP Anda belum terdaftar di database kami';
				$result['sts']=0;
				$result['sts_data']=0;
				$data['sts_data']==0;
			}
			// $data['pesan_balik']=$result['pesan'];
			$this->db->insert(_TBL_INBOX_NEW, $data);
			$id=$this->db->insert_id();
			$result['id']=$id;
			$upd=[];
			$upd['pesan_balik']=$mo_message=sprintf($result['pesan'], $result['nama'], $data['minggu'], $data['tahun'], $result['puskesmas'], $result['id'], 'Surveillance KEMENKES RI');
			$this->db->update(_TBL_INBOX_NEW, $upd, ['id'=>$id]);
		}catch(Exception $e){
			$result['pesan']=$e->getMessage();
			$result['id']=0;
		}
		return $result;
	}

	function set_Minggu_Aktif(){
		$thn=date('Y');
		$tgl=date('Y-m-d');
		
		$mulai = new DateTime(date('Y-m-d'));
		$week = $mulai->format("W");
		$minggu= $week;
		$x='';
		$rows = $this->db
					->select('*')
					->from('minggu')
					->where('week_date >=',$tgl)
					->order_by('week_date', 'asc')
					->limit(1)
					->get()->row();
					
		// $x=$this->ci->db->last_query();
		$arr=array();
		if (!$rows){
			$thn=date('Y')-1;
			$rows = $this->db
						->select('*')
						->from('minggu')
						->where('week_date < substring(now(),1,10)')
						->where('week_year', $thn)
						->order_by('week_date', 'desc')
						->limit(1)
						->get()->row();
			// $x=$this->ci->db->last_query();
		}
		
		$rows = (array) $rows;
		
		if (count($rows)==0){
			$rows=array('minggu'=>$minggu, 'tahun'=>$thn, 'tanggal'=>$tgl, 'query'=>$x);
		}else{
			$rows=array('minggu'=>$rows['week'], 'tahun'=>$rows['week_year'], 'tanggal'=>$rows['week_date'], 'query'=>$x);
		}
		
		return $rows;
	}

	protected function _detect_method()
    {
        // Declare a variable to store the method
        $method = NULL;

        // Determine whether the 'enable_emulate_request' setting is enabled
        if ($this->config->item('enable_emulate_request') === TRUE)
        {
            $method = $this->input->post('_method');
            if ($method === NULL)
            {
                $method = $this->input->server('HTTP_X_HTTP_METHOD_OVERRIDE');
            }

            $method = strtolower($method);
        }

        if (empty($method))
        {
            // Get the request method as a lowercase string
            $method = $this->input->method();
        }

        return in_array($method, $this->allowed_http_methods) && method_exists($this, '_parse_' . $method) ? $method : 'get';
    }

	function cek_auth($tk='token_api'){
		
		$token=$this->preference[$tk];//'ufMnCmdj0ON7tyDz9mCxf2blLYzSrVIQM3DSVeMs';
		$hasil['status']=false;
		$hasil['keterangan']='Authentication failed';
		$key='';
		if(array_key_exists('User-Key', $this->header) || array_key_exists('User-Id', $this->header)){
			if(array_key_exists('User-Key', $this->header)){
				$key=$this->header['User-Key'];
			}
			if(array_key_exists('User-Id', $this->header)){
				$key=$this->header['User-Id'];
			}
			if($key==$token){
				$hasil['status']=true;
				$hasil['keterangan']='success';
			}
		}
		$ip=get_ip();

		if($hasil['status']==true && intval($this->preference['sts_ip_whitelist'])){
			$hasil=$this->cekwhitelist($ip);
		}
		if($hasil['status']==false){
			$this->db->insert(
					'ewarn_api_logs_dki', [
					'method' => $this->uri->uri_string(),
					'api_key' => $key,
					'ip_address' => $ip,
					'status' => $hasil['keterangan'],
					'created_at' => date('Y-m-d H:i:s')
					]);
		}
		// $hasil['ip_address']=$ip;
		// if($this->input->ip_address()=='192.168.203.2'){
		// 	die ($this->db->last_query());
		// }
		return $hasil;
	}

	function cekwhitelist($ip){
		$hasil['status']=true;
		$hasil['keterangan']='berhasil';
		$whitelist=$this->db->get(_TBL_IP_WHITELIST)->result_array();
		if(!empty($whitelist)){
			foreach($whitelist as $x){
				if(trim($x['ip_address'])==$ip){
					$ada=true;
					break;
				}
			}
			if(!$ada){
				$hasil['status']=false;
				$hasil['keterangan']='Authentication failed, your IP address is not registered';
			}
		}
		return $hasil;
	}

	function get_penyakit(){
		
		// doi::dump($this->preference);
		$rows=$this->db->select('id as uniq_id, code as kode, kode_sms, nama_penyakit as nama')->where('aktif','Y')->get(_TBL_PENYAKIT)->result_array();

		$result['status']=true;
		$result['data']=$rows;
		return $result;
	}
	function get_propinsi(){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri', $this->post['kode_prop']);
		}
		$rows = $this->db->get(_TBL_PROPINSI)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));
		
		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri', $this->post['kode_prop']);
		}
		$rows=$this->db->select('id as uniq_id, kode_depdagri as kode_prop, propinsi')->where('aktif','Y')->get(_TBL_PROPINSI)->result_array();

		$result['data']=$rows;
		$result['status']=true;
		return $result;
	}
	function get_kota(){

		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri', $this->post['kode_kota']);
		}

		$rows = $this->db->select('id')->get(_TBL_VIEW_KOTA)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri', $this->post['kode_kota']);
		}
		$rows=$this->db->select('id as uniq_id, kode_depdagri_prop as kode_prop, kode_depdagri as kode_kota, propinsi, kota')->where('aktif','Y')->get(_TBL_VIEW_KOTA)->result_array();
		$result['data']=$rows;
		$result['status']=true;
		return $result;
	}
	function get_kecamatan(){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri_kota', $this->post['kode_kota']);
		}
		if(isset($this->post['kode_kecamatan'])){
			$this->db->where('kode_depdagri', $this->post['kode_kecamatan']);
		}

		$rows = $this->db->select('id')->where('aktif','Y')->get(_TBL_VIEW_DISTRIK)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri_kota', $this->post['kode_kota']);
		}
		if(isset($this->post['kode_kecamatan'])){
			$this->db->where('kode_depdagri', $this->post['kode_kecamatan']);
		}
		$rows=$this->db->select('id as uniq_id,kode_depdagri_prop as kode_prop, kode_depdagri_kota as kode_kota,  kode_depdagri as kode_kecamatan, propinsi, kota, distrik as kecamatan')->where('aktif','Y')->get(_TBL_VIEW_DISTRIK)->result_array();
		$result['data']=$rows;
		$result['status']=true;
		return $result;
	}
	function get_unit(){

		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			$this->db->where('id_unit', intval($this->post['kode_unit']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri_kota', $this->post['kode_kota']);
		}
		if(isset($this->post['kode_kecamatan'])){
			$this->db->where('kode_depdagri_distrik', $this->post['kode_kecamatan']);
		}

		$rows = $this->db->select('id')->where('aktif','Y')->get(_TBL_VIEW_PUSKESMAS)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			$this->db->where('id_unit', intval($this->post['kode_unit']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri_kota', $this->post['kode_kota']);
		}
		if(isset($this->post['kode_kecamatan'])){
			$this->db->where('kode_depdagri_distrik', $this->post['kode_kecamatan']);
		}
		$rows=$this->db->select('id as uniq_id,kode_depdagri_prop as kode_prop, kode_depdagri_kota as kode_kota, kode_depdagri_distrik as kode_kecamatan, id_unit unit_id, unit_pelapor as unit, kode_puskesmas as kode, propinsi, kota, distrik as kecamatan, puskesmas as nama_unit, alamat')->where('aktif','Y')->get(_TBL_VIEW_PUSKESMAS)->result_array();
		$result['data']=$rows;
		$result['status']=true;
		return $result;
	}

	function get_aggregat(){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			if(!empty($this->post['kode_unit'])){
				$this->db->where('kode_puskesmas', $this->post['kode_unit']);
			}
		}
		if(isset($this->post['kategori_unit_id'])){
			if(!empty($this->post['kategori_unit_id'])){
				$this->db->where('id_unit', intval($this->post['kategori_unit_id']));
			}
		}
		if(isset($this->post['tahun'])){
			$this->db->where('year', $this->post['tahun']);
		}else{
			$this->db->where('year', date('Y'));
		}
		if(isset($this->post['minggu'])){
			$this->db->where('minggu', $this->post['minggu']);
		}else{
			$minggu=date('W');
			$this->db->where('minggu', date('W'));
		}

		$rows = $this->db->select('id')->get(_TBL_VIEW_DATAREPORT)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			if(!empty($this->post['kode_unit'])){
				$this->db->where('kode_puskesmas', $this->post['kode_unit']);
			}
		}
		if(isset($this->post['unit_id'])){
			if(!empty($this->post['unit_id'])){
				$this->db->where('id_puskesmas', intval($this->post['unit_id']));
			}
		}
		if(isset($this->post['tahun'])){
			$this->db->where('year', $this->post['tahun']);
		}else{
			$this->db->where('year', date('Y'));
		}
		if(isset($this->post['minggu'])){
			$this->db->where('minggu', $this->post['minggu']);
		}else{
			$minggu=date('W');
			$this->db->where('minggu', date('W'));
		}
		$this->db->order_by('minggu');

		$rows=$this->db->select('id as uniq_id, id_unit kategori_unit_id, unit_pelapor kategori_unit_pelapor, id_puskesmas unit_id, kode_puskesmas kode_unit, puskesmas as unit, nama_petugas as petugas, year as tahun, minggu, sts_tepat, sts_simpan as tipe_input')->get(_TBL_VIEW_DATAREPORT)->result_array();
		$x=[];
		// if (getUserIP()=='103.126.10.22'){
		// 	die($this->db->last_query());
		// }
		$arr_detail=[];
		foreach($rows as $row){
			$x[]=$row['uniq_id'];
		}
		if($x){
			$rows_detail=$this->db->where_in('id_datareport', $x)->get(_TBL_VIEW_DATAREPORT_DETAIL)->result_array();
			foreach($rows_detail as $row){
				if(isset($this->post['status_kasus'])){
					if(intval($this->post['status_kasus'])){
						if(intval($row['nilai'])){
							$arr_detail[$row['id_datareport']][$row['kode_sms']]=['penyakit'=>$row['nama_penyakit'], 'kasus'=>$row['nilai']];
						}
					}else{
						$arr_detail[$row['id_datareport']][$row['kode_sms']]=['penyakit'=>$row['nama_penyakit'], 'kasus'=>$row['nilai']];
					}
				}else{
					$arr_detail[$row['id_datareport']][$row['kode_sms']]=['penyakit'=>$row['nama_penyakit'], 'kasus'=>$row['nilai']];
				}
			}

			foreach($rows as &$row){
				if(array_key_exists($row['uniq_id'], $arr_detail)){
					$row['detail']=$arr_detail[$row['uniq_id']];
				}
			}
			unset($row);
		}
		$result['data']=$rows;
		$result['status']=true;
		return $result;
	}

	function get_alert($id_prop=0){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			$this->db->where('kode_puskesmas', $this->post['kode_unit']);
		}

		if(isset($this->post['unit_id'])){
			$this->db->where('id_place', $this->post['unit_id']);
		}
		if(isset($this->post['kategori_unit_id'])){
			$this->db->where('id_unit', $this->post['kategori_unit_id']);
		}
		
		if(isset($this->post['tahun'])){
			$tahun=$this->post['tahun'];
			$this->db->where('tahun', $this->post['tahun']);
		}else{
			$tahun=date('Y');
			$this->db->where('tahun', date('Y'));
		}
		if(isset($this->post['minggu'])){
			$minggu=$this->post['minggu'];
			$this->db->where('minggu', $this->post['minggu']);
		}else{
			$minggu=date('W');
			$this->db->where('minggu', date('W'));
		}
		if($id_prop>0){
			$this->db->where('id_prop', $id_prop);
			if($id_prop==7){
				$this->db->where('id_unit', 1);
			}
		}

		$rows = $this->db->select('id')->get(_TBL_VIEW_ALERT)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['id'])){
			$this->db->where('id', intval($this->post['id']));
		}
		if(isset($this->post['kode_unit'])){
			$this->db->where('kode_puskesmas', $this->post['kode_unit']);
		}

		if(isset($this->post['unit_id'])){
			$this->db->where('id_place', $this->post['unit_id']);
		}
		if(isset($this->post['kategori_unit_id'])){
			$this->db->where('id_unit', $this->post['kategori_unit_id']);
		}
		
		if(isset($this->post['tahun'])){
			$tahun=$this->post['tahun'];
			$this->db->where('tahun', $this->post['tahun']);
		}else{
			$tahun=date('Y');
			$this->db->where('tahun', date('Y'));
		}
		if(isset($this->post['minggu'])){
			$minggu=$this->post['minggu'];
			$this->db->where('minggu', $this->post['minggu']);
		}else{
			$minggu=date('W');
			$this->db->where('minggu', date('W'));
		}
		if($id_prop>0){
			$this->db->where('id_prop', $id_prop);
			if($id_prop==7){
				$this->db->where('id_unit', 1);
			}
		}
		$this->db->order_by('minggu');
		$rows=$this->db->select('id as uniq_id, id_unit kategori_unit_id, unit_pelapor kategori_unit_pelapor, id_place unit_id , kode_puskesmas kode_unit, propinsi as provinsi, kota, distrik as kecamatan, puskesmas as unit, lokasi as alamat, lat, lng, nama_penyakit, code as kode_penyakit, tahun, minggu, nilai as kasus, sts_verifikasi, note, tgl_verif as tgl_verifikasi, temuan, klb, respon24')->get(_TBL_VIEW_ALERT)->result_array();
		
		if ($id_prop==7){
			foreach($rows as &$row){
				$plain_text = $row['uniq_id'];
				$encrypted = $this->encrypt_text($plain_text);
				$row['uniq_id']=$encrypted;
			}	
			unset($row);
		}	

		// $result['sql']=$this->db->last_query();
		// die($this->db->last_query());
		$result['status']=true;
		$result['data']=$rows;
		$result['tahun']=$tahun;
		$result['minggu']=$minggu;

		return $result;
	}

	function get_mbg($mode=1){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}
		
		if(isset($this->post['tahun'])){
			$tahun=$this->post['tahun'];
			$this->db->where('tahun', $this->post['tahun']);
		}else{
			$tahun=date('Y');
			$this->db->where('tahun', date('Y'));
		}
		if(isset($this->post['bulan'])){
			$this->db->where('bulan', $this->post['bulan']);
		}
		if(isset($this->post['no_mbg'])){
			$this->db->where('no_mbg', $this->post['no_mbg']);
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_kota', $this->post['kode_kota']);
		}

		$rows = $this->db->select('id')->get(_TBL_VIEW_MBG)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['tahun'])){
			$tahun=$this->post['tahun'];
			$this->db->where('tahun', $this->post['tahun']);
		}else{
			$tahun=date('Y');
			$this->db->where('tahun', date('Y'));
		}
		if(isset($this->post['bulan'])){
			$this->db->where('bulan', $this->post['bulan']);
		}
		if(isset($this->post['no_mbg'])){
			$this->db->where('no_mbg', $this->post['no_mbg']);
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_depdagri_prop', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_depdagri_kota', $this->post['kode_kota']);
		}
		$this->db->order_by('tahun, bulan');
		if($mode==1){	
			$rows=$this->db->select('id, id id_uniq, no_mbg, create_date tanggal_pelaporan, hp_pelapor, email_pelapor, tgl_kejadian tanggal_kejadian, tgl_makan tanggal_makan, tgl_gejala tanggal_gejala, kode_depdagri_prop kode_prop, kode_depdagri_kota kode_kota, kode_depdagri_distrik kode_kecamatan, propinsi provinsi, kota, distrik kecamatan,, desa, sekolah, jml_kasus jumlah_kasus, jml_mati jumlah_kematian, jml_rawatinap jumlah_rawat_inap, jml_rawatjalan jumlah_rawat_jalan, jml_sembuh jumlah_sembuh, jml_pria jumlah_pria, jml_wanita jumlah_wanita, usia4 usia_4, usia59 usia_5_9, usia1014 usia_10_14, usia1519 usia_15_19, usia20 usia_20, jml_sekolah jumlah_sekolah, kloter_anter kloter_pengantaran, \'\' list_gejala, tanda_gejala, inku_pendek inkubasi_terpendek, inku_panjang inkubasi_terpanjang, inku_rata inkubasi_rata, inku_median inkubasi_median, karbohidrat, protein1 protein_1, protein2 protein_2, sayur, buah, faskes faskes_penanganan_kasus, diag_banding diagnosa_banding , nama_sppg, yayasan_sppg, \'\' info_sekolah, bumil ibu_hamil, busui ibu_menyusui, balita, case when serti_sppg=0 then \'ya\' else \'tidak\' end serti_sppg, if(hasil_ikl=1, \'Memenuhi syarat\', \'Tidak memenuhi syarat\') hasil_ikl, create_date created_at, update_date updated_at')->get(_TBL_VIEW_MBG)->result_array();
		}else{
			$rows=$this->db->select('id, id id_uniq, no_mbg, tgl_ambil_sample, tgl_kirim_sample, tgl_keluar_hasil, nama_lab, \'\' sample_lab, create_date created_at, update_date updated_at')->get(_TBL_VIEW_MBG)->result_array();
		}
		
		$arr_id=[];
		foreach($rows as &$row){
			$arr_id[]=$row['id'];
			$plain_text = $row['id'];
			$encrypted = $this->encrypt_text($plain_text);
			$row['id_uniq']=$encrypted;
		}	
		unset($row);
		$arr_sppg=[];
		$arr_gejala=[];
		if($mode==1){	
			$sppg_rows=$this->db->where_in('mbg_id', $arr_id)->order_by('mbg_id')->get(_TBL_MBG_SPPG)->result_array();
			$gejala_rows=$this->db->where('kelompok', 'gejala_mbg')->get(_TBL_DATA_COMBO)->result_array();
			foreach($gejala_rows as $gr){
				$arr_gejala[$gr['id']]=$gr['data'];
			}
			foreach($sppg_rows as $sppg){
				$arr_sppg[$sppg['mbg_id']][]=['nama_sekolah'=>$sppg['sekolah'], 'jml_siswa'=>$sppg['jml_siswa'], 'jml_porsi'=>$sppg['jml_porsi'], 'sts_uks'=>$sppg['uks'], 'sts_uji_organoleptik'=>$sppg['sk'], 'sarana_ctps'=>$sppg['kepemilikan']];
			}
		}else{
			$sample_lab_rows=$this->db->where('kelompok', 'sample_lab')->get(_TBL_DATA_COMBO)->result_array();
			foreach($sample_lab_rows as $gr){
				$arr_gejala[$gr['id']]=$gr['data'];
			}
			$sppg_rows=$this->db->where_in('mbg_id', $arr_id)->order_by('mbg_id')->get(_TBL_MBG_LAB)->result_array();
			foreach($sppg_rows as $sppg){
				$kategori=$sppg['kategori_id'];
				if(array_key_exists(intval($sppg['kategori_id']), $arr_gejala)){
					$kategori=$arr_gejala[intval($sppg['kategori_id'])];
				}
				$arr_sppg[$sppg['mbg_id']][]=['kategori'=>$kategori, 'sample'=>$sppg['sample'], 'sumber'=>$sppg['sumber'], 'hasil_mikrobiologi_positif'=>$sppg['hasil_mikrobiologi_positif'], 'hasil_mikrobiologi_negatif'=>$sppg['hasil_mikrobiologi_negatif'], 'hasil_kimia_positif'=>$sppg['hasil_kimia_positif'], 'hasil_kimia_negatif'=>$sppg['hasil_kimia_negatif']];
			}
		}
		foreach($rows as &$row){
			if($mode==1){
				if(array_key_exists($row['id'], $arr_sppg)){
					$row['info_sekolah']=$arr_sppg[$row['id']];
				}else{
					$row['info_sekolah']=[];
				}
				$gejala=json_decode($row['tanda_gejala'],true);
				foreach($gejala as &$g){
					if(array_key_exists(intval($g['gejala']), $arr_gejala)){
						$g['gejala']=$arr_gejala[$g['gejala']];
					}
				}
				unset($g);
				unset($row['tanda_gejala']);
				$row['list_gejala']=$gejala;
			}else{
				if(array_key_exists($row['id'], $arr_sppg)){
					$row['sample_lab']=$arr_sppg[$row['id']];
				}else{
					$row['sample_lab']=[];
				}
			}
			unset($row['id']);
		}	
		unset($row);

		$result['status']=true;
		$result['data']=$rows;

		return $result;
	}

	function get_sppg($mode=1){
		$page        = (isset($this->post['page'])) ? intval($this->post['page']) : 1;
		if (isset($this->post['limit'])){
            if (intval($this->post['limit']>0)){
			    $this->limit=$this->post['limit'];
		    }
		}
        if ($page < 1) {$page = 1;}
		
		
		if(isset($this->post['status_slhs'])){
			$this->db->where('slhs', intval($this->post['status_slhs']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_prov', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_kab', $this->post['kode_kota']);
		}

		$rows = $this->db->select('id')->get(_TBL_VIEW_SPPG)->num_rows();
		$result['paging']['total'] = $rows;
		$result['paging']['page']  = $page;
		$result['paging']['limit'] = intval($this->limit);
		$x                         = $rows % $this->limit;
		$tpage                     = 0;
		if ($x > 0) {
			$tpage = 1;
		}
		--$page;
		$result['paging']['total_page'] = intval($rows / $this->limit) + $tpage;
		$this->db->limit($this->limit, ($page*$this->limit));

		if(isset($this->post['status_slhs'])){
			$this->db->where('slhs', intval($this->post['status_slhs']));
		}
		if(isset($this->post['kode_prop'])){
			$this->db->where('kode_prov', $this->post['kode_prop']);
		}
		if(isset($this->post['kode_kota'])){
			$this->db->where('kode_kab', $this->post['kode_kota']);
		}
		// $this->db->order_by('tahun, bulan');
		$rows=$this->db->select('kode_sppg,uuid id_sppg,kode_prov,provinsi,kode_kab,kabupaten,kode_kec,kecamatan,kode_kel,kelurahan,nama_sppg,alamat_sppg,ka_sppg,tanggal_operasional,jenis_sppg,jenis_sppg,slhs status_slhs,sertifikat, create_date created_at, update_date updated_at')->get(_TBL_VIEW_SPPG)->result_array();
		
		// $arr_id=[];
		foreach($rows as &$row){
			if($row['sertifikat']){
				$row['sertifikat'] = base_url('api/download/serti/'.$row['sertifikat']);
			}
		}	
		unset($row);

		$result['status']=true;
		$result['data']=$rows;

		return $result;
	}
	function save_respon_alert(){

		// $headerServer=$this->input->request_headers();
		$upd=[];
		// $upd['created_at'] = date('Y-m-d H:i:s');	
		// $upd['datas'] = json_encode($this->post,JSON_UNESCAPED_SLASHES);	
		// $upd['ip'] = get_ip();	
		// $upd['agen'] = json_encode($headerServer,JSON_UNESCAPED_SLASHES);

		// $this->db->insert(_TBL_TEMPORARY,$upd);

		$data=$this->post;
		$alert_id = $this->decrypt_text($data['uniq_id']);
		// $this->post['uniq_id']=$alert_id;
		$result['data']=$this->post;
		$result['status']=true;
		$result['keterangan']='Proses penyimpanan data alert berhasil dengan id '.$alert_id;
		$result['tanggal']=date('d-m-Y H:i:s');

		
		// $alert_id=intval($data['uniq_id']);
		// $pesan="seseorang mengirimkan data respon alert dengan id ".$alert_id.":\n\n";
		// $pesan.=$upd['datas']."\n\n";
		// $pesan.=" Tanggal : ".$upd['created_at'];

		// $inst=[];
		// $inst['id_inbox']=$alert_id;
		// $inst['tipe_id']=2;
		// $inst['id_petugas']=0;
		// $inst['pesan']=$pesan;
		// $inst['hp']='+628176690091';
		// $inst['id_status']=0;
		// $inst['id_prioritas']=1;
		// $inst['created_by']='sistem';
		// $inst['tgl_masuk']=date('Y-m-d H:i:s');
		// $this->db->insert(_TBL_OUTBOX_WA, $inst);
		// $inst['id_inbox']=$alert_id;
		// $inst['tipe_id']=2;
		// $inst['id_petugas']=0;
		// $inst['pesan']=$pesan;
		// $inst['hp']='+628569999357';
		// $inst['id_status']=0;
		// $inst['id_prioritas']=1;
		// $inst['created_by']='sistem';
		// $inst['tgl_masuk']=date('Y-m-d H:i:s');
		// $this->db->insert(_TBL_OUTBOX_WA, $inst);
		
		// return $result;
		$result['status']=false;
		$result['keterangan']='gagal memproses data alert';

		// $alert_id=intval($alert_id);
		if(intval($alert_id)){
			$rows = $this->db->where('id', $alert_id)->get(_TBL_LAP_ALERT)->row_array();
			if($rows){
				// $result['keterangan']='Data berhasil disimpan';
				
				$upd=[];
				$upd['klb'] = $data["klb"]?$data["klb"]:null;
				$upd['id_criteria'] = $data["kriteria"]?$data["kriteria"]:null;
				$upd['note'] = $data["Rencana_tindak_lanjut"]?$data["Rencana_tindak_lanjut"]:null;
				// $upd['petugas'] = $data["petugas"]?$data["petugas"]:null;
				$upd['jml_kematian'] = $data["jml_kematian"]?$data["jml_kematian"]:null;
				$upd['gejala'] = $data["gejala"]?$data["gejala"]:null;
				$upd['kronologi'] = $data["kronologi"]?$data["kronologi"]:null;
				$upd['tindakan'] = $data["tindakan"]?$data["tindakan"]:null;
				$upd['spesimen_diperiksa'] = $data["spesimen_diperiksa"]?$data["spesimen_diperiksa"]:null;
				$upd['hasil_pemeriksaan'] = $data["hasil_pemeriksaan"]?$data["hasil_pemeriksaan"]:null;
				$upd['temuan'] = $data["deskripsi_kejadian"]?$data["deskripsi_kejadian"]:null;
				$upd['update_date'] = date('Y-m-d H:i:s');	
				$upd['update_user'] = 'api';	
				$upd['jenis_verif'] = $data["jenis_verif"]?$data["jenis_verif"]:null;	
				$upd['sts_laporan_ebs'] = $data["sts_laporan_ebs"]?$data["sts_laporan_ebs"]:null;	
				$upd['no_ebs'] = $data["no_ebs"]?$data["no_ebs"]:null;	
					
				$upd['tgl_verif'] = $data["tgl_verifikasi"]?$data["tgl_verifikasi"]:null;
				if(date('Y-m-d') <= date('Y-m-d', strtotime($data['create_date_alert']." +1 day"))){
					$respon24 = 1;
				}
				$upd['sts_verifikasi'] = 1;
				$upd['respon24'] = $respon24;

				$this->db->where('id', $rows["id"]);
				$this->db->update(_TBL_LAP_ALERT,$upd);
				$result['keterangan']='Proses update respon data alert berhasil dengan id '.$alert_id;
				$result['status']=true;
			}else{
				$result['status']=false;
				$result['keterangan']='Data alert tidak ditemukan';
			}
		}else{
			$result['status']=false;
			$result['keterangan']='Parameter id alert '.$alert_id.' tidak ditemukan';
		}
		return $result;
	}

	private function encrypt_text($string)
    {
        $key = hash('sha256', $this->secret_key);
        $iv  = substr(hash('sha256', $this->secret_iv), 0, 16);

        $output = openssl_encrypt($string, "AES-256-CBC", $key, 0, $iv);
        return base64_encode($output);
    }

    // Fungsi decrypt
    private function decrypt_text($string)
    {
        $key = hash('sha256', $this->secret_key);
        $iv  = substr(hash('sha256', $this->secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($string), "AES-256-CBC", $key, 0, $iv);
    }
}
/* End of file app_login_model.php */
/* Location: ./application/models/app_login_model.php */
