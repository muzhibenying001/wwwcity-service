<?php

namespace App\Libs\ES;

use Elasticsearch\ClientBuilder;

class EsClient
{
	static private $instance;
	private $client;

	private function __construct()
	{
		$hosts = ['http://ly:liyong1992@47.95.145.227:9200'];
		$this->client = ClientBuilder::create()
			->setHosts($hosts)
			->build();
	}

	private function __clone()
	{

	}

	static public function getInstance(){
		if (!self::$instance instanceof self){
			self::$instance = new self();
		}
		return self::$instance;
	}



	/**
	 * 添加一些默认参数
	 * @param $params
	 * @return mixed
	 */
	private function paramsSetting($params)
	{

		//忽略400和404错误
		// if (empty($params['client']['ignore'])) {
		// 	$params['client']['ignore'] = [400, 404, 405];
		// }
		//返回数据时顺带code值等
		// if (empty($params['client']['verbose'])) {
		// 	$params['client']['verbose'] = true;
		// }
		if (empty($params['client']['timeout'])) {
			$params['client']['timeout'] = 20;
		}
		if (empty($params['client']['connect_timeout'])) {
			$params['client']['connect_timeout'] = 20;
		}
		// if (empty($params['client']['future'])){
		// 	$params['client']['future'] = 'lazy';
		// }
		return $params;
	}


	/**
	 * 创建索引
	 * @param $index 索引名称 建议：项目+日期(年月)
	 * @param $body 索引参数 可空
	 * @return array|callable
	 */
	public function createIndex($index, $body = '')
	{
		//判断索引是否已存在
		$currentIndex = $this->getIndexs([$index]);
		if ($currentIndex['code'] == 'SUCCESS') {
			return ['code' => 'FAIL', 'msg' => '索引已存在'];
		}
		$params = [
			'index' => $index,
			'body' => $body
		];
		$params = $this->paramsSetting($params);
		$result = $this->client->indices()->create($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status']];
		// }
	}

	/**
	 * 获取索引配置
	 * @param $indexs ['index1', 'index2', ...]
	 * @return array|callable
	 */
	public function getIndexs($indexs)
	{
		$params = [
			'index' => $indexs
		];
		$params = $this->paramsSetting($params);
		$result = $this->client->indices()->getSettings($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}

	/**
	 * 修改索引配置
	 * @param $index
	 * @param string $body
	 * @return array|callable
	 */
	public function updateIndex($index, $settings)
	{
		//判断索引是否已存在
		$currentIndex = $this->getIndexs([$index]);
		if ($currentIndex['status'] != 200) {
			return ['code' => 'FAIL', 'msg' => '索引不存在'];
		}
		$body = '';
		if (count($settings) > 0){
			$body = ['settings' => $settings];
		}else{
			return ['code' => 'FAIL', 'msg' => '未提供修改信息！'];
		}
		$params = [
			'index' => $index,
			'body' => $body
		];
		$params = $this->paramsSetting($params);

		$result = $this->client->indices()->putSettings($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}

	/**
	 * 删除索引
	 * @param $index
	 * @return array|callable
	 */
	public function deleteIndex($index)
	{
		$params = [
			'index' => $index
		];
		$params = $this->paramsSetting($params);
		// 忽略400和404 405错误
		$params['client']['ignore'] = [400, 404, 405];
		$result = $this->client->indices()->delete($params);
		return $result;

		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}

	/**
	 * 索引文档
	 * @param $index
	 * @param $id 可不传递，会自动生成
	 * @param $body ['field1' => '1', 'field2' => '2', ...]
	 * @return array|callable
	 */
	public function indexSingleDoc($index, $body, $id = '')
	{

		$params = [
			'index' => $index,
			'body' => $body
		];
		if (!empty($id)){
			$params['id'] = $id;
		}

		$params = $this->paramsSetting($params);
		// dd(json_encode($params));
		$result = $this->client->index($params);
		// dd(json_encode($result));
		return $result;

		/***
		 * $result
		 *
		 * {"_index":"door-open--2003","_type":"openLog","_id":"tzwiFnIB9sh1uA9Q5_vx","_version":1,"result":"created","_shards":{"total":2,"successful":1,"failed":0},"_seq_no":1,"_primary_term":1}
		 */

		// if ($result['status'] == 201){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }elseif ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es更新成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}


	/**
	 * 批量索引文档
	 * @param $index
	 * @param $docs [['id' => '', 'body' => ['field1' => '1', 'field2' => '2', ...]], ['id' => '', 'body' => ['field1' => '1', 'field2' => '2', ...]], ...]
	 * @return array
	 */
	public function indexMultiDoc($index, $docs)
	{
		$params = ['body' => []];
		foreach ($docs as $doc) {
			if (empty($doc['id'])){
				$params['body'][] = [
					'create' => [
						'_index' => $index,
					]
				];
			}else{
				$params['body'][] = [
					'create' => [
						'_index' => $index,
						'_id' => $doc['id']
					]
				];
			}
			$params['body'][] = $doc['body'];
			if (count($params['body']) % 1000 == 0) {
				// dd(json_encode($params));
				$params = $this->paramsSetting($params);
				$responses = $this->client->bulk($params);
				if ($responses['errors']){

				}
				$params = ['body' => []];
				unset($responses);
			}
		}
		if (!empty($params['body'])) {
			// dd(json_encode($params));
			$params = $this->paramsSetting($params);
			$responses = $this->client->bulk($params);
			if ($responses['errors']){

			}
		}
		return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => '索引文档成功！'];
	}

	/**
	 * 获取文档 参数不可为空
	 * @param $index
	 * @param $type
	 * @param $id
	 * @return array|callable
	 */
	public function getDoc($index, $type, $id)
	{
		$params = [
			'index' => $index,
			'type' => $type,
			'id' => $id
		];
		$params = $this->paramsSetting($params);
		$result = $this->client->get($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}

	/**
	 * 更新文档
	 * https://www.elastic.co/guide/cn/elasticsearch/php/current/_updating_documents.html#_script_更新
	 * @param $title
	 * @param $type
	 * @param $id
	 * @param $body
	 *  部分更新 $body = ['doc' => ['field1' => 'new value', 'field2' => 'new value', 'field3' => 'new value']]
	 *  script更新 $body = ['script' => 'ctx._source.counter += count', 'params' => ['count' => 4]]
	 *  upsert更新 $body = ['script' => 'ctx._source.counter += count', 'params' => ['count' => 4], 'upsert' => ['counter' => 1]]
	 * @return array|callable
	 */
	public function updateDoc($index, $type, $id, $body)
	{
		$params = [
			'index' => $index,
			'type' => $type,
			'id' => $id,
			'body' => $body
		];
		$params = $this->paramsSetting($params);
		$result = $this->client->update($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}

	/**
	 * 删除文档
	 * @param $title
	 * @param $type
	 * @param $id
	 * @return array|callable
	 */
	public function deleteDoc($index, $type, $id)
	{
		$params = [
			'index' => $index,
			'type' => $type,
			'id' => $id
		];
		$params = $this->paramsSetting($params);
		$result = $this->client->delete($params);
		return $result;
		// if ($result['status'] == 200){
		// 	return ['code' => 'SUCCESS', 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => 'FAIL', 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}


	//查询
	public function search($index, $body)
	{
		$params = [
			'index' => $index,
			'body' => $body
		];
		$params = $this->paramsSetting($params);
		// dd(json_encode($params));
		$result = $this->client->search($params);
		// dd(json_encode($result));
		return $result;
		//
		// if ($result['status'] == 200){
		// 	return ['code' => true, 'msg' => 'es执行成功！', 'content' => $result['body']];
		// }else{
		// 	return ['code' => false, 'msg' => '错误码：'.$result['status'].' 错误信息：'.$result['body']['error']['reason']];
		// }
	}
}