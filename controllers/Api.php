<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
 
class Api extends REST_Controller {
	
	protected $api_kel=0;
	private $minggu_aktif = 1;
    private $tahun_aktif      = 2021;
    private $tanggal        = '2021/1/1';
    private $max_hari     = 2;
	private $p      = [];
	private $g      = [];

    function __construct()
    {
        // Construct the parent class
		parent::__construct();
		ini_set('max_execution_time', 60);
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
		$this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
		
		$this->g = $this->get();
		$this->p = $this->post();
		$this->data->get=$this->g;
		$this->data->post=$this->p;
		$this->data->header=$this->_args;
		$this->data->preference=$this->_Preference_;
    }

	
	public function login_post(){

		$post = $this->post();
		if(empty($post['username']) || empty($post['password'])){
			$this->response(['resultLogin'=>['status'=>false,'kode'=>400,'keterangan'=>'Username dan password wajib diisi']], 400);
			return;
		}
		$result = $this->data->do_login($post['username'], $post['password']);
		$this->response(['resultLogin'=>$result], $result['kode']);
	}

	public function users_post(){
		$result = $this->data->get_users();
		$this->response(['resultUsers'=>$result], $result['kode']);
	}

	public function test_post(){
		$cek=$this->db->where('uri_title','tgl_cli_wa')->get(_TBL_PREFERENCE)->row_array();

		$awal  = date_create($cek['value']);
		$akhir = date_create(); // waktu sekarang
		$diff  = date_diff( $awal, $akhir );

		$result['tahun']= $diff->y;
		$result['bulan'] = $diff->m;
		$result['hari'] = $diff->d;
		$result['jam'] = $diff->h;
		$result['menit'] = $diff->i;
		$result['detik'] = $diff->s;

		$result['status']=1;
		$result['tgl']=$awal;
		if($diff->h>1 || ($diff->h==0 && $diff->i>45)){
			$result['info']='Lanjut';
		}else{
			$result['info']="masih proses";
		}

		if ($result['status']==1)
		{
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response($result, REST_Controller::HTTP_NOT_FOUND);
		}

	}

	public function sms_post()
    {
        $data['msisdn'] = $this->post('msisdn');
        $data['trx_id'] = $this->post('trx_id');
		$data['mo_message'] = urldecode($this->post('mo_message'));
		$data['id_server'] = $this->post('id');
		$data['operator'] = $this->post('operator');
		$data['created_at'] = date('Y-m-d H:i:s');

		$this->load->library('sms');
		$this->sms->post=$this->post;
		$result = $this->sms->set_data($data)->process();
		// $result['status']=0;
		if ($result['status']==1)
		{
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response($result, REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	public function sms_get()
    {
        $data['msisdn'] = $this->get('msisdn');
        $data['trx_id'] = $this->get('trx_id');
		$data['mo_message'] = urldecode($this->get('mo_message'));
		$data['id_server'] = $this->get('id');
		$data['operator'] = $this->get('operator');
		$data['created_at'] = date('Y-m-d H:i:s');

		$this->load->library('sms');
		$this->sms->post=$this->get();
		$result = $this->sms->set_data($data)->process();
		// $result['status']=0;
		if ($result['status']==1)
		{
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response($result, REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	function ebs_new_post(){
		$result = $this->data->save_ebs_new();
		$this->response(['resultEbs_new'=>$result], $result['kode']);
	}

	function list_ebs_new_post(){
		$result = $this->data->get_list_ebs_new();
		$this->response(['resultList_ebs_new'=>$result], $result['kode']);
        }
        public function update_ebs_new_post(){
                $result = $this->data->update_ebs_new();
                $this->response(["resultUpdate_ebs_new"=>$result], $result["kode"]);
        }


        public function ebs_size_respon_post(){
                $result = $this->data->ebs_size_respon();
                $this->response(["resultEbs_size_respon"=>$result], $result["kode"]);
        }

	function ebs_post(){
		$result = $this->data->cek_auth('token_api_ebs');
		if($result['status']){
			$result = $this->data->ebs();
		// $result['status']=0;
		}

		$prefix='resultEbs';
		if ($result['status'])
		{
			$result['kode']=REST_Controller::HTTP_OK;
			$this->response([$prefix=>$result], REST_Controller::HTTP_OK);
		}
		else
		{
			$result['kode']=REST_Controller::HTTP_NOT_FOUND;
			$this->response([$prefix=>['total'=>0,'data'=>[], 'status'=>false]], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function master_kode_get(){
		$result = $this->data->cek_auth('token_api_ebs');
		if($result['status']){
			$result = $this->data->master_kode();
		}
		if ($result['status'])
		{
			$result['kode']=REST_Controller::HTTP_OK;
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$result['kode']=REST_Controller::HTTP_NOT_FOUND;
			$this->response(['total'=>0,'data'=>[], 'status'=>false], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function tgc_post(){
		$result = $this->data->cek_auth('token_api_tgc');
		if($result['status']){
			$result = $this->data->tgc();
		// $result['status']=0;
		}
		if ($result['status'])
		{
			$result['kode']=REST_Controller::HTTP_OK;
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$result['kode']=REST_Controller::HTTP_NOT_FOUND;
			$this->response(['total'=>0,'data'=>[], 'status'=>false], REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	// function skdr_get(){
	// 	$result = $this->data->individual();
	// 	// $result['status']=0;
	// 	if ($result['status'])
	// 	{
	// 		$this->response($result, REST_Controller::HTTP_OK);
	// 	}
	// 	else
	// 	{
	// 		$this->response(['total'=>0,'data'=>[], 'status'=>false], REST_Controller::HTTP_NOT_FOUND);
	// 	}
	// }

	function skdr_param_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_param();
		}
		if ($result)
		{
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response(['total'=>0,'data'=>[], 'status'=>false], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	function provinsi_post(){

		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_propinsi();
		}
		$this->result_api($result,'provinsi');
	}

	function kota_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_kota();
		}
		$this->result_api($result,'kota');
	}

	function kecamatan_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_kecamatan();
		}
		$this->result_api($result,'kecamatan');
	}

	function unit_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_unit();
		}
		$this->result_api($result,'unit');
	}
	function penyakit_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_penyakit();
		}
		$this->result_api($result,'penyakit');
	}

	function agregat_puskesmas_post(){
		$result = $this->data->save_agregat_puskesmas();
		$this->response(['resultAgregatPuskesmas'=>$result], $result['kode']);
	}

	function kerpang_mbg_post(){
		$result = $this->data->get_mbg(1);
		$this->response(['resultKerpang_mbg'=>$result], $result['kode']);
	}

	function lab_mbg_post(){
		$result = $this->data->get_mbg(2);
		$this->response(['resultLab_mbg'=>$result], $result['kode']);
	}

	function slhs_post(){
		$result = $this->data->get_slhs();
		$this->response(['resultSlhs'=>$result], $result['kode']);
	}

	function download_get(){
		$kode = $this->uri->segment(4);
		$type = $this->uri->segment(3);
		if($type=='serti' && $kode){
			$row = $this->db->select('sertifikat')->where('sertifikat', $kode)->get('ewarn_form_slhs')->row_array();
			if($row && $row['sertifikat']){
				$path = FCPATH.'uploads/serti/'.$row['sertifikat'];
				if(file_exists($path)){
					header('Content-Type: application/pdf');
					header('Content-Disposition: inline; filename="'.$kode.'"');
					readfile($path);
					exit;
				}
			}
			$this->response(['status'=>false,'keterangan'=>'File tidak ditemukan'], 404);
		}else{
			$this->response(['status'=>false,'keterangan'=>'Invalid request'], 400);
		}
	}

	function sppg_post(){
		$result = $this->data->get_sppg(1);
		$this->response(['resultSppg'=>$result], $result['kode']);
	}

	function aggregat_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			// if (getUserIP()=='103.126.10.22'){
			// 	die("debug");
			// }
			$result = $this->data->get_aggregat();
		}
		$this->result_api($result,'aggregat');
	}
	function alert_post(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$result = $this->data->get_alert();
		}
		$this->result_api($result,'alert');
	}
	function alert_dki_post(){
		$result = $this->data->cek_auth('token_api_dki');
		if($result['status']){
			$result = $this->data->get_alert(7);
		}
		$this->result_api($result,'alert');
	}

	function result_api($result, $prefix=''){
		$prefix='result'. ucfirst($prefix);
		// $hasil[$prefix]=$result;
		if ($result['status'])
		{
			$result['kode']=REST_Controller::HTTP_OK;
			$this->response([$prefix=>$result], REST_Controller::HTTP_OK);
		}
		else
		{
			$result['kode']=REST_Controller::HTTP_NOT_FOUND;
			$this->response($result, REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function receive_alert_response_post(){
		$prefix='result'. ucfirst('receive_alert_response');
		$result = $this->data->cek_auth('token_api_dki');
		if($result['status']){
			$result = $this->data->save_respon_alert();
		}

		if ($result['status'])
		{
			$result['kode']=REST_Controller::HTTP_OK;
			$this->response([$prefix=>$result], REST_Controller::HTTP_OK);
		}
		else
		{
			$result['kode']=REST_Controller::HTTP_NOT_FOUND;
			$this->response([$prefix=>$result], REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function message_tmp(){
		$message="success";
		$noProses=[0,0,0];
		$x=$this->db->where_in('uri_title',['api_url','api_token','api_last_message_number', 'api_wa', 'api_suffix'])->get(_TBL_PREFERENCE)->result_array();
			
		foreach($x as $row){
			$param[$row['uri_title']] = $row['value'];
		}
		// echo json_encode($this->p['messages'][0], JSON_PRETTY_PRINT);
		if (array_key_exists('messages', $this->p)){
			$row=$this->p['messages'][0];
			$tipe=1;
			if ($row['author']==$param['api_wa']){
				$tipe=2;
			}

			$inst=[];
			$inst['tipe_id']=$tipe;
			$inst['hp_sender']='+'.str_replace($param['api_suffix'],'',$row['chatId']);
			$inst['id_api']=$row['id'];
			$inst['body']=$row['body'];
			$inst['fromMe']=$row['fromMe'];
			$inst['self']=$row['self'];
			$inst['isForwarded']=$row['isForwarded'];
			$inst['author']=$row['author'];
			$inst['chatId']=$row['chatId'];
			// $inst['messageNumber']=$row['messageNumber'];
			$inst['time']=$row['time'];
			$inst['senderName']=$row['senderName'];
			$inst['chatName']=$row['chatName'];
			$inst['created_by']='system';
			$inst['created_at']=date('Y-m-d H:i:s');
			if(in_array($inst['hp_sender'], ['+','+E', '+1', '+5', '+c', '+K', '+T'])){
				$inst['sts_proses']=4;
			}else{
				if ($tipe==1){
					$inst['sts_proses']=-1;
				}else{
					$inst['sts_proses']=1;
				}
			}
			if(isset($row['caption'])){
				$inst['caption']=$row['caption'];
			}
			$this->db->where('id_api', $row['id']);
			$cek = $this->db->select('id')->where_in('sts_proses', [0,1])->get(_TBL_INBOX_WA)->num_rows();
			if($tipe==1){
				if (intval($cek)==0){
					$this->db->insert(_TBL_INBOX_WA, $inst);
					$noProses[0]=1;
				}else{
					$this->db->insert(_TBL_INBOX_WA_DOUBLE, $inst);
					$noProses[1]=1;
				}
			}else{
				$this->db->insert(_TBL_INBOX_WA_REPLY, $inst);
				$noProses[2]=1;
			}

		}
		$this->response(['status'=>true, 'message'=>$message, 'data'=>['success'=>$noProses[0], 'double'=>$noProses[1], 'reply'=>$noProses[2]]], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
	}

	public function message_test_get(){

		$x=$this->db->where_in('uri_title',['api_url','api_token','api_last_message_number', 'api_wa', 'api_suffix'])->get(_TBL_PREFERENCE)->result_array();
			
		foreach($x as $row){
			$param[$row['uri_title']] = $row['value'];
		}
		$rows=$this->db->where('id>=',959152)->where('id<=',961482)->where('sts_proses',0)->get(_TBL_INBOX_WA_HOOK)->result_array();
	die($this->db->last_query());
		foreach($rows as $rok){
			$ros=json_decode($rok['pesan'], true);
			$row=[];
			// if(array_key_exists('messages', $ros)){
			// 	$row=$ros['messages'][0];
			// }
			if($ros){
				$row=$ros[0];
			}	
			// die(json_encode($row, JSON_PRETTY_PRINT));
			if($row){
				$tipe=1;
				if ($row['author']==$param['api_wa']){
					$tipe=2;
				}

				$inst=[];
				$inst['tipe_id']=$tipe;
				$inst['hp_sender']='+'.str_replace($param['api_suffix'],'',$row['chatId']);
				$inst['id_api']=$row['id'];
				$inst['body']=$row['body'];
				$inst['fromMe']=$row['fromMe'];
				$inst['self']=$row['self'];
				$inst['isForwarded']=$row['isForwarded'];
				$inst['author']=$row['author'];
				$inst['chatId']=$row['chatId'];
				// $inst['messageNumber']=$row['messageNumber'];
				$inst['time']=$row['time'];
				$inst['senderName']=$row['senderName'];
				$inst['chatName']=$row['chatName'];
				$inst['created_by']='system';
				$inst['created_at']=date('Y-m-d H:i:s');
				if(in_array($inst['hp_sender'], ['+','+E', '+1', '+5', '+c', '+K', '+T'])){
					$inst['sts_proses']=4;
				}else{
					if ($tipe==1){
						$inst['sts_proses']=-1;
					}else{
						$inst['sts_proses']=1;
					}
				}
				if(isset($row['caption'])){
					$inst['caption']=$row['caption'];
				}
				$this->db->where('id_api', $row['id']);
				$cek = $this->db->select('id')->where_in('sts_proses', [0,1])->get(_TBL_INBOX_WA)->num_rows();
				if($tipe==1){
					if (intval($cek)==0){
						$this->db->insert(_TBL_INBOX_WA, $inst);
						$noProses[0]=1;
					}else{
						$this->db->insert(_TBL_INBOX_WA_DOUBLE, $inst);
						$noProses[1]=1;
					}
				}else{
					$this->db->insert(_TBL_INBOX_WA_REPLY, $inst);
					$noProses[2]=1;
				}
			}
			$this->db->update(_TBL_INBOX_WA_HOOK, ['sts_proses'=>1], ['id'=>$rok['id']]);
		}
		$this->response(['status'=>true, 'message'=>'Selesai'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
	}

	public function message_post()
    {

		$this->message_tmp();
		die();
		$sts_error=false;
		// die();
		// if(array_key_exists('test',$this->p)){
		// 	$this->response(['status'=>true, 'data'=>'berhasil'], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		// 	return true;
		// }
		
		// $this->db->insert(_TBL_OUTBOX_WA_TEST, ['pesan'=>json_encode($this->p), 'id_status'=>10, 'created_at'=>date('Y-m-d H:i:s')]);
		// die($this->db->last_query());
		$message='';
		$noProses=[0,0,0];
		// $this->db->trans_begin();
		if(array_key_exists('messages', $this->p)){
			// Doi::dump($this->p);
			if(count($this->p['messages'])>0){
				$this->db->insert(_TBL_INBOX_WA_HOOK,['pesan'=>json_encode($this->p['messages'], JSON_UNESCAPED_SLASHES), 'created_at'=>date('Y-m-d H:i:s')]);
			}
		}
		// die("debug");
		$cek=$this->db->where('uri_title','sts_cli_wa')->get(_TBL_PREFERENCE)->row_array();
		if(intval($cek['value'])>=1){
			$cek=$this->db->where('uri_title','tgl_cli_wa')->get(_TBL_PREFERENCE)->row_array();

			$awal  = date_create($cek['value']);
			$akhir = date_create(); // waktu sekarang
			$diff  = date_diff( $awal, $akhir );

			if($diff->h>1 || ($diff->h==0 && $diff->i>45)){
				
			}else{
				echo "masih proses \n";
				return false;
			}
		}
		try{
			$this->db->update(_TBL_PREFERENCE, ['value'=>1], ['uri_title'=>'sts_cli_wa']);
			$this->db->update(_TBL_PREFERENCE, ['value'=>date('d-m-Y H:i:s')], ['uri_title'=>'tgl_cli_wa']);
			
			$x=$this->db->where_in('uri_title',['api_url','api_token','api_last_message_number', 'api_wa', 'api_suffix'])->get(_TBL_PREFERENCE)->result_array();
			
			foreach($x as $row){
				$param[$row['uri_title']] = $row['value'];
			}
			$url = $param['api_url'].'messages';

			$params = [
				'token' => $param['api_token'],
				'lastMessageNumber'=>$param['api_last_message_number'],
			];
			$client = new GuzzleHttp\Client();
			$response = $client->request('GET', $url, ['query'=>$params]);
			$data = json_decode($response->getBody()->getContents(), true);

			// $this->db->insert(_TBL_TMP,['isi'=>json_encode($data), 'update_date'=>date('Y-m-d H:i:s')]);
			// $this->db->trans_begin();
			$jmlPesan=0;
			$akhirNo=$param['api_last_message_number'];
			$akhirNo2=$param['api_last_message_number'];
			if (array_key_exists('messages', $data)){
				$jmlPesan = count($data['messages']);
				$jmlx=count($data['messages'])-1;
				if($jmlx>0){
					if(intval($data['messages'][$jmlx]["messageNumber"])>intval($akhirNo)){
						$akhirNo2=$data['messages'][$jmlx]["messageNumber"];
						$this->db->update(_TBL_PREFERENCE, ['value'=>json_encode($data['messages'][$jmlx])], ['uri_title'=>'lastParamMessageNumber']);
					}else{
						$this->db->update(_TBL_PREFERENCE, ['value'=>json_encode($data['messages'][$jmlx])], ['uri_title'=>'lastParamMessageNumberErr']);
					}
				}
			}
			// echo $jmlPesan .'&&'. $akhirNo2.'>'.$akhirNo."\n";
			
			if ($jmlPesan && $akhirNo2>$akhirNo){
				// $this->db->insert(_TBL_TEST_WA,['d_post'=>json_encode($this->post()), 'd_get'=>json_encode($data), 'no_awal'=>$akhirNo, 'no_akhir'=>$akhirNo2, 'jml_pesan'=>$jmlPesan, 'created_at'=>date('Y-m-d H:i:s')]);
				try{
					foreach($data['messages'] as $key=>$row){
						// echo "no ke ".$key."\n";
						// Doi::dump($row);
						$tipe=1;
						if ($row['author']==$param['api_wa']){
							$tipe=2;
						}

						$inst=[];
						// $inst['bot_code']=$bot_code;
						// $inst['petugas_id']=intval($id_petugas);
						// $inst['tmp_lap']=json_encode($penyakit);
						$inst['tipe_id']=$tipe;
						$inst['hp_sender']='+'.str_replace($param['api_suffix'],'',$row['chatId']);
						$inst['id_api']=$row['id'];
						$inst['body']=$row['body'];
						$inst['fromMe']=$row['fromMe'];
						$inst['self']=$row['self'];
						$inst['isForwarded']=$row['isForwarded'];
						$inst['author']=$row['author'];
						$inst['chatId']=$row['chatId'];
						$inst['messageNumber']=$row['messageNumber'];
						$inst['time']=$row['time'];
						$inst['senderName']=$row['senderName'];
						$inst['chatName']=$row['chatName'];
						$inst['created_by']='system';
						$inst['created_at']=date('Y-m-d H:i:s');
						if(in_array($inst['hp_sender'], ['+','+E', '+1', '+5', '+c', '+K', '+T'])){
							$inst['sts_proses']=4;
						}else{
							if ($tipe==1){
								$inst['sts_proses']=-1;
							}else{
								$inst['sts_proses']=1;
							}
						}
						if(isset($row['caption'])){
							$inst['caption']=$row['caption'];
						}
						// Doi::dump($inst);
						// Doi::dump(json_encode,$inst);
						// $this->db->where('hp_sender', $inst['hp_sender']);
						$this->db->where('id_api', $row['id']);
						$cek = $this->db->select('id')->where_in('sts_proses', [0,1])->get(_TBL_INBOX_WA)->num_rows();
						// $cek=0;
						if($tipe==1){
							if (intval($cek)==0){
								$this->db->insert(_TBL_INBOX_WA, $inst);
								// echo $cek."ke ".$key." | "._TBL_INBOX_WA."\n";
								// $id_inbox = $this->db->insert_id();
								// echo $this->db->last_query()."\n";
								++$noProses[0];
							}else{
								$this->db->insert(_TBL_INBOX_WA_DOUBLE, $inst);
								++$noProses[1];
								// echo $cek."ke ".$key." | "._TBL_INBOX_WA_DOUBLE."\n";
								// echo $this->db->last_query()."\n";
								// die($this->db->last_query());
								// $id_inbox = $this->db->insert_id();
							}
						}else{
							$this->db->insert(_TBL_INBOX_WA_REPLY, $inst);
							++$noProses[2];
							// echo $cek."ke ".$key." | "._TBL_INBOX_WA_REPLY."\n";
						}
					}
					$this->db->update(_TBL_PREFERENCE, ['value'=>$akhirNo2], ['uri_title'=>'api_last_message_number']);
					$this->db->update(_TBL_PREFERENCE, ['value'=>date('d-m-Y H:i:s')], ['uri_title'=>'tgl_last_update_wa']);
				}catch(\Exception $e){
					echo "error1\n";
					echo $e->getMessage();
					$err=$e->getMessage().'\n'.json_encode($data).'\n';
					$this->db->update(_TBL_PREFERENCE, ['value'=>date('d-m-Y H:i:s')], ['uri_title'=>'err_wa_inbox']);
					// $this->db->trans_rollback();
				}
			}
		}catch(\Exception $e){
			// echo "error2\n";
			$message = $e->getMessage();
		}
		$this->db->update(_TBL_PREFERENCE, ['value'=>0], ['uri_title'=>'sts_cli_wa']);
		// $this->db->trans_commit();
		$this->response(['status'=>true, 'message'=>$message, 'data'=>['success'=>$noProses[0], 'double'=>$noProses[1], 'reply'=>$noProses[2]]], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
	}

	public function message_qontak_post()
    {
		$this->load->library('waqontak');
		if(array_key_exists('verify_info', $this->p)){
			if($this->p['verify_info']=='This is a verification message. Please respond with 2xx http code.'){
				$ket='Verify Info : Setting Webhook berubah';
			}else{
				$ket='Qontak Verify Info : ';
			}
			$result = $this->waqontak->params(['mode'=>'post', 'note'=>$ket])->datas($this->p)->process('logs');
		}else{
			$result = $this->waqontak->params(['mode'=>'post'])->datas($this->p)->process('inbox');
		}
		if ($result['status'])
		{
			$this->response($result, REST_Controller::HTTP_OK);	
		}
		else
		{
			$this->response($result, REST_Controller::HTTP_NOT_FOUND);
		}
	}
function unit_pelapor_get(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$historis = array(
				2022 => array('Puskesmas'=>10435,'Rumah Sakit'=>593,'Laboratorium'=>0,'B/BKK/LKK'=>0,'Klinik'=>0),
				2023 => array('Puskesmas'=>10489,'Rumah Sakit'=>935,'Laboratorium'=>11,'B/BKK/LKK'=>51,'Klinik'=>0),
				2024 => array('Puskesmas'=>10489,'Rumah Sakit'=>1386,'Laboratorium'=>92,'B/BKK/LKK'=>51,'Klinik'=>0),
				2025 => array('Puskesmas'=>10583,'Rumah Sakit'=>2473,'Laboratorium'=>196,'B/BKK/LKK'=>51,'Klinik'=>131),
			);
			$jumlah_faskes = array('Puskesmas'=>10416,'Rumah Sakit'=>3267,'Laboratorium'=>299,'B/BKK/LKK'=>51,'Klinik'=>13571);
$rows = $this->db->query("
						SELECT
						CASE WHEN id_unit=2 THEN 'Puskesmas'
							WHEN id_unit=1 THEN 'Rumah Sakit'
							WHEN id_unit=4 THEN 'Laboratorium'
							WHEN id_unit=156 THEN 'B/BKK/LKK'
							WHEN id_unit=242 THEN 'Klinik'
							ELSE 'Lainnya' END AS jenis_unit,
						COALESCE(aktif, '') AS aktif,
						COUNT(*) AS jumlah
						FROM ewarn_puskesmas
						GROUP BY jenis_unit, aktif
					")->result_array();
					$data_kini        = array('Puskesmas'=>0,'Rumah Sakit'=>0,'Laboratorium'=>0,'B/BKK/LKK'=>0,'Klinik'=>0);
					$data_kini_aktif  = array('Puskesmas'=>0,'Rumah Sakit'=>0,'Laboratorium'=>0,'B/BKK/LKK'=>0,'Klinik'=>0);
					$data_kini_nonaktif = array('Puskesmas'=>0,'Rumah Sakit'=>0,'Laboratorium'=>0,'B/BKK/LKK'=>0,'Klinik'=>0);
foreach($rows as $r){
						if(!isset($data_kini[$r['jenis_unit']])) continue;
						$data_kini[$r['jenis_unit']] += intval($r['jumlah']);
						if($r['aktif'] === 'Y'){
							$data_kini_aktif[$r['jenis_unit']] += intval($r['jumlah']);
						} elseif($r['aktif'] === 'N'){
							$data_kini_nonaktif[$r['jenis_unit']] += intval($r['jumlah']);
						}
						// aktif='' (null) dihitung di total saja, tidak di aktif/nonaktif
					}
                        $semua = $historis;
			$semua[intval(date('Y'))] = $data_kini;
			ksort($semua);
			$tahun_list = array_keys($semua);
			$total_per_tahun = array();
			foreach($semua as $t=>$j){ $total_per_tahun[$t]=array_sum($j); }
			$tren = array();
			foreach($tahun_list as $i=>$t){
				$kenaikan=null; $persen=null;
				if($i>0){ $prev=$total_per_tahun[$tahun_list[$i-1]]; $kenaikan=$total_per_tahun[$t]-$prev; $persen=$prev>0?round($kenaikan/$prev,6):null; }
				$tren[]=array('tahun'=>$t,'total'=>$total_per_tahun[$t],'kenaikan'=>$kenaikan,'persen'=>$persen);
			}
			$t_awal=$tahun_list[0]; $t_akhir=$tahun_list[count($tahun_list)-1];
			$max_t=$tahun_list[0]; foreach($total_per_tahun as $t=>$v){ if($v>$total_per_tahun[$max_t]) $max_t=$t; }
			$result = array(
				'status'=>true,
				'generated_at'=>date('Y-m-d H:i:s'),
				'tren'=>$tren,
				'ringkasan'=>array(
					'total_kenaikan'=>$total_per_tahun[$t_akhir]-$total_per_tahun[$t_awal],
					'persen_kenaikan'=>$total_per_tahun[$t_awal]>0?round(($total_per_tahun[$t_akhir]-$total_per_tahun[$t_awal])/$total_per_tahun[$t_awal],6):0,
					'rata_rata'=>round(array_sum($total_per_tahun)/count($total_per_tahun)),
					'tahun_tertinggi'=>$max_t,
				),
				'per_jenis'=>$semua,
				'jumlah_faskes'=>$jumlah_faskes,
				'data_terkini'=>$data_kini,
                                'data_terkini_aktif'=>$data_kini_aktif,
				'data_terkini_nonaktif'=>$data_kini_nonaktif,
				'tahun_list'=>$tahun_list,
			);
		}
		$this->result_api($result,'unit_pelapor');
	}

function list_unit_pelapor_get(){
		$result = $this->data->cek_auth();
		if($result['status']){
			$page  = intval($this->get('page'))  ?: 1;
			$limit = intval($this->get('limit')) ?: 500;
			$aktif = $this->get('aktif'); // Y, N, atau kosong = semua
			$offset = ($page - 1) * $limit;

			$this->db->select('
				p.id, p.puskesmas, p.kode_puskesmas,
				ep.propinsi, ek.kota, ed.distrik,
				vp.unit_pelapor,
				p.aktif, p.lat, p.lng,
				p.id_kota, p.id_prop, p.id_distrik,
				p.kelompok, p.id_unit,
				p.create_date, p.update_date
			');
			$this->db->from('ewarn_puskesmas p');
			$this->db->join('ewarn_view_puskesmas vp', 'vp.id = p.id', 'left');
			$this->db->join('ewarn_propinsi ep', 'ep.id = p.id_prop', 'left');
			$this->db->join('ewarn_kota ek', 'ek.id = p.id_kota', 'left');
			$this->db->join('ewarn_distrik ed', 'ed.id = p.id_distrik', 'left');
			if ($aktif === 'Y' || $aktif === 'N') {
				$this->db->where('p.aktif', $aktif);
			}
			$this->db->order_by('ep.propinsi, ek.kota, p.puskesmas');

			$total = $this->db->count_all_results('', false);
			$this->db->limit($limit, $offset);
			$rows = $this->db->get()->result_array();

			$result = array(
				'status'     => true,
				'total'      => $total,
				'page'       => $page,
				'limit'      => $limit,
				'total_page' => ceil($total / $limit),
				'data'       => $rows,
			);
		}
		$this->result_api($result, 'list_unit_pelapor');
	}

    // API ZOONOSIS PE
    public function zoonosis_pe_post() {
        $p = $this->post();
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) {
            $this->response(array('status'=>false,'kode'=>401,'keterangan'=>'Unauthorized'), 401); return;
        }
        $id_penyakit = isset($p['id_penyakit']) ? (int)$p['id_penyakit'] : 0;
        $id_prop     = isset($p['id_prop'])     ? (int)$p['id_prop']     : 0;
        $tgl1        = isset($p['tgl1'])        ? $p['tgl1']             : date('Y-01-01');
        $tgl2        = isset($p['tgl2'])        ? $p['tgl2']             : date('Y-m-d');
        $limit       = isset($p['limit'])       ? min((int)$p['limit'],1000) : 100;
        $offset      = isset($p['offset'])      ? (int)$p['offset']      : 0;
        $tgl1 = $this->db->escape_str($tgl1);
        $tgl2 = $this->db->escape_str($tgl2);
        $nik  = isset($p['nik'])  ? trim($p['nik'])  : '';
        $wp = $id_penyakit ? " AND z.id_penyakit=".intval($id_penyakit) : "";
        $wr = $id_prop     ? " AND z.id_prop=".intval($id_prop)         : "";
        $wn = $nik         ? " AND z.nik='".$this->db->escape_str($nik)."'" : "";
        $q = "SELECT z.id,z.no_pe,z.no_ebs,z.id_penyakit,p.nama_penyakit,z.diagnosa_no,
                     z.tgl_laporan,z.tgl_pe,z.tgl_bergejala,z.nama_pasien,z.nik,
                     z.kelamin,z.umur_thn,z.umur_bln,z.pekerjaan,z.alamat,
                     z.id_prop,pr.propinsi,z.id_kota,k.kota,z.id_puskesmas,pk.puskesmas as nama_puskesmas,
                     z.status_kasus,z.akhir_no,z.tgl_meninggal,
                     z.diperiksa_lab,z.hasil_lab,
                     z.nama_petugas,z.jabatan_petugas,z.telp_petugas,
                     z.gejala,z.ket_lain,z.create_date,z.update_date
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit
              LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop
              LEFT JOIN ewarn_kota k ON k.id=z.id_kota
              LEFT JOIN ewarn_puskesmas pk ON pk.id=z.id_puskesmas
              WHERE z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}'
              {$wp} {$wr} {$wn}
              ORDER BY z.tgl_laporan DESC,z.id DESC
              LIMIT {$limit} OFFSET {$offset}";
        $rows = $this->db->query($q)->result_array();
        $cnt  = $this->db->query("SELECT COUNT(*) as n FROM ewarn_ghs_zoonosis_pe z
              WHERE z.tgl_laporan BETWEEN '{$tgl1}' AND '{$tgl2}' {$wp} {$wr} {$wn}")->row_array();
        $this->response(array('status'=>true,'kode'=>200,'total'=>(int)$cnt['n'],
            'limit'=>$limit,'offset'=>$offset,'data'=>$rows), 200);
    }

    public function zoonosis_pe_detail_post() {
        $p = $this->post();
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) {
            $this->response(array('status'=>false,'kode'=>401,'keterangan'=>'Unauthorized'), 401); return;
        }
        $id    = isset($p['id'])    ? (int)$p['id']    : 0;
        $no_pe = isset($p['no_pe']) ? trim($p['no_pe']) : '';
        $nik   = isset($p['nik'])   ? trim($p['nik'])   : '';
        if (!$id && !$no_pe && !$nik) {
            $this->response(array('status'=>false,'kode'=>400,'keterangan'=>'id, no_pe, atau nik wajib diisi'), 400); return;
        }
        if ($id)         $where = "z.id=".intval($id);
        elseif ($no_pe)  $where = "z.no_pe='".$this->db->escape_str($no_pe)."'";
        else             $where = "z.nik='".$this->db->escape_str($nik)."'";
        $pe = $this->db->query("SELECT z.*,p.nama_penyakit,pr.propinsi,k.kota,pk.puskesmas as nama_puskesmas
              FROM ewarn_ghs_zoonosis_pe z
              LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit
              LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop
              LEFT JOIN ewarn_kota k ON k.id=z.id_kota
              LEFT JOIN ewarn_puskesmas pk ON pk.id=z.id_puskesmas
              WHERE {$where} LIMIT 1")->row_array();
        if (!$pe) {
            $this->response(array('status'=>false,'kode'=>404,'keterangan'=>'Data tidak ditemukan'), 404); return;
        }
        $pe['eav_detail']      = $this->db->where('id_pe',$pe['id'])->get('ghs_zoonosis_pe_detail')->result_array();
        $pe['anggota_serumah'] = $this->db->where('id_pe',$pe['id'])->get('ghs_zoonosis_anggota_serumah')->result_array();
        $pe['kontak_pe']       = $this->db->where('id_pe',$pe['id'])->get('ghs_zoonosis_kontak_pe')->result_array();
        $pe['tim_pe']          = $this->db->where('id_pe',$pe['id'])->get('ghs_zoonosis_tim_pe')->result_array();
        $this->response(array('status'=>true,'kode'=>200,'data'=>$pe), 200);
    }

    public function zoonosis_pe_entry_post() {
        $p = $this->post();
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) {
            $this->response(array('status'=>false,'kode'=>401,'keterangan'=>'Unauthorized'), 401); return;
        }
        $required = array('id_penyakit','tgl_laporan','nama_pasien','id_prop','id_kota');
        foreach ($required as $r) {
            if (empty($p[$r])) {
                $this->response(array('status'=>false,'kode'=>400,'keterangan'=>'Field wajib: '.implode(', ',$required)), 400); return;
            }
        }
        $id_penyakit = (int)$p['id_penyakit'];
        $pm = array(8=>'GHPR',11=>'AVIAN',14=>'ATX',26=>'LEPTO');
        $prefix = isset($pm[$id_penyakit]) ? $pm[$id_penyakit] : 'ZOO';
        $ym = date('Ymd');
        $last = $this->db->query("SELECT no_pe FROM ewarn_ghs_zoonosis_pe WHERE no_pe LIKE '{$prefix}-{$ym}%' ORDER BY no_pe DESC LIMIT 1")->row_array();
        $num = $last ? (int)substr($last['no_pe'],-4)+1 : 1;
        $no_pe = $prefix.'-'.$ym.'-'.str_pad($num,4,'0',STR_PAD_LEFT);
        $allowed = array('id_penyakit','diagnosa_no','tgl_laporan','tgl_pe','tgl_bergejala',
            'nama_pasien','nama_kk','nik','kelamin','umur_thn','umur_bln','tgl_lahir',
            'pekerjaan','alamat','kelurahan','kecamatan','id_prop','id_kota','id_puskesmas',
            'kd_prop_kasus','kd_kota_kasus','status_kasus','akhir_no','gejala','ket_lain',
            'nama_petugas','jabatan_petugas','telp_petugas','no_ebs');
        $data = array('no_pe'=>$no_pe,'create_user'=>'api_nar','create_date'=>date('Y-m-d H:i:s'));
        foreach ($allowed as $f) { if (isset($p[$f]) && $p[$f]!=='') $data[$f]=$p[$f]; }
        $this->db->insert('ghs_zoonosis_pe', $data);
        $id = $this->db->insert_id();
        $this->response(array('status'=>true,'kode'=>200,'keterangan'=>'PE berhasil disimpan','no_pe'=>$no_pe,'id'=>$id), 200);
    }


    public function zoonosis_pe_update_lab_post() {
        $p = $this->post();
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) {
            $this->response(array('status'=>false,'kode'=>401,'keterangan'=>'Unauthorized'), 401); return;
        }
        // Validasi: id atau no_pe wajib
        $id    = isset($p['id'])    ? (int)$p['id']    : 0;
        $no_pe = isset($p['no_pe']) ? trim($p['no_pe']) : '';
        $nik   = isset($p['nik'])   ? trim($p['nik'])   : '';
        if (!$id && !$no_pe && !$nik) {
            $this->response(array('status'=>false,'kode'=>400,'keterangan'=>'id, no_pe, atau nik wajib diisi'), 400); return;
        }
        // Cari PE
        if ($id)        $where = array('id'=>$id);
        elseif ($no_pe) $where = array('no_pe'=>$no_pe);
        else            $where = array('nik'=>$nik);
        $pe = $this->db->get_where('ghs_zoonosis_pe', $where)->row_array();
        if (!$pe) {
            $this->response(array('status'=>false,'kode'=>404,'keterangan'=>'Data PE tidak ditemukan'), 404); return;
        }
        // Update field lab
        $update = array('update_date'=>date('Y-m-d H:i:s'));
        if (isset($p['diperiksa_lab']))  $update['diperiksa_lab']  = (int)$p['diperiksa_lab'];
        if (isset($p['hasil_lab']))      $update['hasil_lab']      = trim($p['hasil_lab']);
        if (isset($p['tgl_hasil_lab']))  $update['tgl_hasil_lab']  = $p['tgl_hasil_lab'];
        if (isset($p['nama_lab']))       $update['nama_lab']       = trim($p['nama_lab']);
        if (isset($p['ket_lab']))        $update['ket_lab']        = trim($p['ket_lab']);
        if (isset($p['jenis_sample']))   $update['jenis_sample']   = trim($p['jenis_sample']);
        if (isset($p['tgl_ambil_sample'])) $update['tgl_ambil_sample'] = $p['tgl_ambil_sample'];
        if (isset($p['tgl_kirim_sample'])) $update['tgl_kirim_sample'] = $p['tgl_kirim_sample'];
        if (isset($p['status_kasus']))   $update['status_kasus']   = (int)$p['status_kasus'];
        if (count($update) <= 1) {
            $this->response(array('status'=>false,'kode'=>400,'keterangan'=>'Tidak ada field lab yang diupdate'), 400); return;
        }
        $this->db->where('id', $pe['id'])->update('ghs_zoonosis_pe', $update);
        // Log update
        $this->db->insert('ghs_zoonosis_log', array(
            'id_pe'      => $pe['id'],
            'aksi'       => 'update_lab_api',
            'keterangan' => 'Update lab dari NAR: '.json_encode($update),
            'create_date'=> date('Y-m-d H:i:s'),
            'create_user'=> 'api_nar',
        ));
        $this->response(array(
            'status'     => true,
            'kode'       => 200,
            'keterangan' => 'Data lab berhasil diupdate',
            'no_pe'      => $pe['no_pe'],
            'id'         => $pe['id'],
            'updated'    => $update,
        ), 200);
    }


    public function fhir_zoonosis_get() {
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'forbidden','diagnostics'=>'Unauthorized'))), 401); return; }
        $id    = $this->get('id');
        $no_pe = $this->get('no_pe');
        $nik   = $this->get('nik');
        if (!$id && !$no_pe && !$nik) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'required','diagnostics'=>'id, no_pe, atau nik wajib'))), 400); return; }
        if ($id) $where = "z.id=".intval($id);
        elseif ($no_pe) $where = "z.no_pe='".$this->db->escape_str($no_pe)."'";
        else $where = "z.nik='".$this->db->escape_str($nik)."'";
        $pe = $this->db->query("SELECT z.*,p.nama_penyakit,pr.propinsi,k.kota,pk.puskesmas FROM ewarn_ghs_zoonosis_pe z LEFT JOIN ewarn_penyakit p ON p.id=z.id_penyakit LEFT JOIN ewarn_propinsi pr ON pr.id=z.id_prop LEFT JOIN ewarn_kota k ON k.id=z.id_kota LEFT JOIN ewarn_puskesmas pk ON pk.id=z.id_puskesmas WHERE {$where} LIMIT 1")->row_array();
        if (!$pe) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'not-found','diagnostics'=>'PE tidak ditemukan'))), 404); return; }
        $status_map = array(0=>'unconfirmed',1=>'provisional',2=>'confirmed',3=>'refuted');
        $verif = isset($status_map[(int)$pe['status_kasus']]) ? $status_map[(int)$pe['status_kasus']] : 'unconfirmed';
        $akhir_map = array(1=>'resolved',2=>'inactive',3=>'active',4=>'active',5=>'active');
        $clinical = isset($akhir_map[(int)$pe['akhir_no']]) ? $akhir_map[(int)$pe['akhir_no']] : 'active';
        $snomed = array(8=>array('system'=>'http://snomed.info/sct','code'=>'14168008','display'=>'Rabies'),11=>array('system'=>'http://snomed.info/sct','code'=>'57386000','display'=>'Avian influenza'),14=>array('system'=>'http://snomed.info/sct','code'=>'409498004','display'=>'Anthrax'),26=>array('system'=>'http://snomed.info/sct','code'=>'58419002','display'=>'Leptospirosis'));
        $dcode = isset($snomed[(int)$pe['id_penyakit']]) ? $snomed[(int)$pe['id_penyakit']] : array('system'=>'http://skdr.kemkes.go.id','code'=>'ZOO','display'=>$pe['nama_penyakit']);
        $bundle = array('resourceType'=>'Bundle','id'=>'pe-'.$pe['id'],'type'=>'collection','timestamp'=>date('c'),'entry'=>array());
        $patient = array('resourceType'=>'Patient','id'=>'patient-'.$pe['id'],'identifier'=>array(array('system'=>'http://skdr.kemkes.go.id/nik','value'=>$pe['nik']?:'-'),array('system'=>'http://skdr.kemkes.go.id/pe','value'=>$pe['no_pe'])),'name'=>array(array('use'=>'official','text'=>$pe['nama_pasien']?:'-')),'gender'=>$pe['kelamin']=='L'?'male':($pe['kelamin']=='P'?'female':'unknown'),'birthDate'=>$pe['tgl_lahir']?:null,'address'=>array(array('text'=>$pe['alamat']?:'-','city'=>$pe['kota']?:'-','state'=>$pe['propinsi']?:'-','country'=>'ID')),'contact'=>array(array('name'=>array('text'=>$pe['kontak_darurat']?:'-'),'telecom'=>array(array('system'=>'phone','value'=>$pe['telp_kontak_darurat']?:'-')))));
        $bundle['entry'][] = array('resource'=>$patient);
        $condition = array('resourceType'=>'Condition','id'=>'cond-'.$pe['id'],'clinicalStatus'=>array('coding'=>array(array('system'=>'http://terminology.hl7.org/CodeSystem/condition-clinical','code'=>$clinical))),'verificationStatus'=>array('coding'=>array(array('system'=>'http://terminology.hl7.org/CodeSystem/condition-ver-status','code'=>$verif))),'code'=>array('coding'=>array($dcode),'text'=>$pe['nama_penyakit']),'subject'=>array('reference'=>'Patient/patient-'.$pe['id']),'onsetDateTime'=>$pe['tgl_bergejala']?:null,'recordedDate'=>$pe['tgl_laporan']?:null,'note'=>array(array('text'=>$pe['gejala']?:'')));
        $bundle['entry'][] = array('resource'=>$condition);
        if ($pe['diperiksa_lab']) {
            $dr = array('resourceType'=>'DiagnosticReport','id'=>'lab-'.$pe['id'],'status'=>$pe['hasil_lab']?'final':'registered','code'=>array('text'=>'Lab Zoonosis'),'subject'=>array('reference'=>'Patient/patient-'.$pe['id']),'effectiveDateTime'=>$pe['tgl_ambil_sample']?:null,'issued'=>$pe['tgl_hasil_lab']?$pe['tgl_hasil_lab'].'T00:00:00+07:00':null,'performer'=>array(array('display'=>$pe['nama_lab']?:'-')),'conclusion'=>$pe['hasil_lab']?:'-');
            $bundle['entry'][] = array('resource'=>$dr);
        }
        if ($pe['nama_rs']||$pe['tgl_masuk_rs']) {
            $enc = array('resourceType'=>'Encounter','id'=>'enc-'.$pe['id'],'status'=>'finished','class'=>array('system'=>'http://terminology.hl7.org/CodeSystem/v3-ActCode','code'=>'IMP','display'=>'inpatient'),'subject'=>array('reference'=>'Patient/patient-'.$pe['id']),'period'=>array('start'=>$pe['tgl_masuk_rs']?:null),'serviceProvider'=>array('display'=>$pe['nama_rs']?:'-'));
            $bundle['entry'][] = array('resource'=>$enc);
        }
        $prov = array('resourceType'=>'Provenance','id'=>'prov-'.$pe['id'],'target'=>array(array('reference'=>'Condition/cond-'.$pe['id'])),'recorded'=>$pe['tgl_pe']?$pe['tgl_pe'].'T00:00:00+07:00':date('c'),'agent'=>array(array('who'=>array('display'=>($pe['nama_petugas']?:'-').($pe['jabatan_petugas']?' ('.$pe['jabatan_petugas'].')':'')))),'location'=>array('display'=>isset($pe['puskesmas'])?$pe['puskesmas']:($pe['kota']?:'-')));
        $bundle['entry'][] = array('resource'=>$prov);
        header('Content-Type: application/fhir+json');
        echo json_encode($bundle, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }

    public function fhir_zoonosis_put() {
        $result = $this->data->cek_auth('token_api_ebs');
        if (!$result['status']) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'forbidden','diagnostics'=>'Unauthorized'))), 401); return; }
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || !isset($body['resourceType']) || $body['resourceType'] !== 'DiagnosticReport') {
            $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'invalid','diagnostics'=>'Body harus FHIR DiagnosticReport'))), 400); return;
        }
        $subject = isset($body['subject']['reference']) ? $body['subject']['reference'] : '';
        preg_match('/patient-(\d+)/', $subject, $m);
        $id_pe = isset($m[1]) ? (int)$m[1] : 0;
        if (!$id_pe) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'required','diagnostics'=>'subject.reference harus patient-{id}'))), 400); return; }
        $pe = $this->db->get_where('ghs_zoonosis_pe', array('id'=>$id_pe))->row_array();
        if (!$pe) { $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'error','code'=>'not-found','diagnostics'=>'PE tidak ditemukan'))), 404); return; }
        $upd = array('diperiksa_lab'=>1,'hasil_lab'=>isset($body['conclusion'])?$body['conclusion']:NULL,'tgl_hasil_lab'=>isset($body['issued'])?substr($body['issued'],0,10):NULL,'nama_lab'=>isset($body['performer'][0]['display'])?$body['performer'][0]['display']:NULL,'update_date'=>date('Y-m-d H:i:s'));
        $this->db->where('id',$id_pe)->update('ghs_zoonosis_pe',$upd);
        $this->response(array('resourceType'=>'OperationOutcome','issue'=>array(array('severity'=>'information','code'=>'informational','diagnostics'=>'Lab berhasil diupdate via FHIR R4'))), 200);
    }

}
