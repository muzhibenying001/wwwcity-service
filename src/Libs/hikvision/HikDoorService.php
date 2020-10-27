<?php

namespace App\Libs\hikvision;

use GuzzleHttp\Client;

class HikDoorService
{
	/**
	 * 对接海康门禁管理系统
	 */

	/**
	 * 获取根区域信息
	 * https://open.hikvision.com/docs/8d516e2582e1417aa144081108347f75#b8deecfc
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getRootRegion(){
		return self::requestHik('/api/resource/v1/regions/root', ['treeCode' => 0]);
	}

	/**
	 * 注册小区--批量添加小区-楼栋-单元
	 * https://open.hikvision.com/docs/8d516e2582e1417aa144081108347f75#e21ca7e1
	 * @param $regions
	 * @return mixed
	 * @throws Exception
	 */
	public function addRegion($regions){
		if (empty($regions)){
			throw new \Exception('区域信息为空', 3001);
		}
		return self::requestHik('/api/resource/v1/region/batch/add', $regions);
	}

	/**
	 * 生成小区级别的组织
	 * https://open.hikvision.com/docs/58b00f249a0342efbed14b6bb48bcc14#de5c0817
	 * @param $code
	 * @param $name
	 * @return mixed
	 * @throws Exception
	 */
	public function addCommunityOrg($code, $name){
		$orgs[] = [
			'orgIndexCode' => $code,
			'orgName' => $name,
			'parentIndexCode' => 'root000000' // 根组织
		];
		return self::requestHik('/api/resource/v1/org/batch/add', ['object' => $orgs]);
	}

	/**
	 * 注册用户身份
	 * https://open.hikvision.com/docs/f67c6f8b13de4621853583daa058327b#b6a07b38
	 * @param $uuid
	 * @param $name
	 * @param $gender
	 * @param $communityCode
	 * @param $idCard
	 * @return mixed
	 * @throws Exception
	 */
	public function registerUser($uuid, $name, $gender, $communityCode, $idCard, $face = ''){
		$data = [
			'personId' => $uuid,
			'personName' => $name,
			'gender' => $gender,
			'orgIndexCode' => $communityCode,
			'certificateType' => '111',
			'certificateNo' => $idCard
		];
		if (!empty($face)){
			$data['faces'] = [[
				'faceData' => $face
			]];
		}
		return self::requestHik('/api/resource/v2/person/single/add', $data);
	}

	/**
	 * 添加用户钥匙 -- 人脸
	 * 添加人脸后对于已授权的权限要执行一下修改权限，将卡片添加进去
	 * https://open.hikvision.com/docs/f67c6f8b13de4621853583daa058327b#ae3a260f
	 * @param $uuid
	 * @param $face
	 * @return mixed
	 * @throws Exception
	 */
	public function addFace($uuid, $face){
		$data = [
			'personId' => $uuid,
			'faceData' => $face
		];
		// 修改权限
		return self::requestHik('/api/resource/v1/face/single/add', $data);
	}

	public function modifyFace(){

	}

	/**
	 * 添加用户钥匙 -- 卡片
	 * 添加卡片后对于已授权的权限要执行一下修改权限，将卡片添加进去
	 * https://open.hikvision.com/docs/cd03aa4347c342c1a6bb9e220a867bc6#d75b749c
	 * @param $uuid
	 * @param $cardNo
	 * @param $communityCode
	 * @param $startDate
	 * @param $endDate
	 * @return mixed
	 * @throws Exception
	 */
	public function addCard($uuid, $cardNo, $communityCode, $startDate = 0, $endDate = 0){
		if (empty($startDate)){
			$startDate = date('Y-m-d', time());
		}
		if (empty($endDate)){
			$endDate = '2037-12-30';
		}

		$data = [
			'startDate' => $startDate, // yyyy-MM-dd
			'endDate' => $endDate,
			'cardList' => [
				'cardNo' => $cardNo,
				'personId' => $uuid,
				'orgIndexCode' => $communityCode,
				'cardType' => '1'
			]
		];
		return self::requestHik('/api/cis/v1/card/bindings', $data);
	}

	/**
	 * 退卡， 卡的权限自动失效
	 * https://open.hikvision.com/docs/cd03aa4347c342c1a6bb9e220a867bc6#f9a4b36a
	 * @param $uuid
	 * @param $cardNo
	 * @return mixed
	 * @throws Exception
	 */
	public function deleteCard($uuid, $cardNo){
		$data = [
			'cardNumber' => $cardNo,
			'personId' => $uuid
		];
		return self::requestHik('/api/cis/v1/card/deletion', $data);
	}

	/**
	 * 获取门禁设备列表
	 * https://open.hikvision.com/docs/2367840dc2e946759cf05b9698870314#be146ecc
	 * @param $communityCode
	 * @param int $time
	 * @param int $pageNo
	 * @param int $pageSize
	 * @return mixed
	 * @throws Exception
	 */
	public function getDoors($communityCode, $time = 0, $pageNo = 1, $pageSize = 20){
		$data = [
			'regionIndexCodes' => $communityCode,
			'isSubRegion' => true,
			'pageNo' => $pageNo,
			'pageSize' => $pageSize
		];
		if ($time > 0){
			$data['expressions'] = [
				'key' => 'createTime',
				'operator' => '>=',
				'values' => [$time]
			];
		}
		return self::requestHik('/api/resource/v2/acsDevice/search', $data);
	}

	/**
	 * 查询门禁设备在线状态
	 * https://open.hikvision.com/docs/2367840dc2e946759cf05b9698870314#d4b33c66
	 * @param $communityCode
	 * @param int $pageNo
	 * @param int $pageSize
	 * @return mixed
	 * @throws Exception
	 */
	public function getDoorStatus($communityCode, $pageNo = 1, $pageSize = 20){
		$data = [
			'regionId' => $communityCode,
			'includeSubNode' => 1,
			'pageNo' => $pageNo,
			'pageSize' => $pageSize
		];
		return self::requestHik('/api/nms/v1/online/acs_device/get', $data);
	}

	// 门禁授权三部曲：添加权限配置 -> 创建下载任务 -> 开始任务

	/**
	 * 添加权限配置
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#b474b6a1
	 * @param $uuids
	 * @param $doors
	 * @param int $startTime
	 * @param int $endTime
	 * @return mixed
	 * @throws Exception
	 */
	public function authorization($uuids, $doors, $hasCard, $hasFace, $startTime = 0, $endTime = 0){
		if (empty($uuids) || empty($doors)){
			throw new \Exception('用户信息和门禁信息都必传', 3004);
		}
		if (empty($startTime) && empty($endTime)){
			//永久授权
		}elseif (!empty($startTime) && !empty($endTime)){
			// 有效期间
			$data['startTime'] = gmdate(DATE_ATOM, $startTime);
			$data['endTime'] = gmdate(DATE_ATOM, $endTime);
		}else{
			throw new \Exception('所传时间无效', 3005);
		}
		$data['personDatas'] = [[
			'indexCodes' => $uuids,
			'personDataType' => 'person'
		]];
		$resourceInfos = [];
		foreach ($doors as $door){
			$resourceInfos[] = [
				'resourceIndexCode' => $door,
				'resourceType' => 'acsDevice'
			];
		}
		$data['resourceInfos'] = $resourceInfos;
		$result = self::requestHik('/api/acps/v1/auth_config/add', $data);
		if ($result){
			if ($hasFace){
				// 已上传人脸
				// 创建任务 4:人脸 5：卡片+人脸
				if ($hasCard){
					$result2 = self::createTask(5);
				}else{
					$result2 = self::createTask(4);
				}
			}else{
				// 创建任务 1：卡片
				if ($hasCard){
					$result2 = self::createTask(1);
				}else{
					throw new \Exception('无需同步到海康', 2201);
				}
			}
			if ($result2){
				// 任务添加待执行设备
				$result3 = self::addToTask($result2['taskId'], $resourceInfos);
				if ($result3){
					// 开始任务
					self::startTask($result2['taskId']);
				}
			}
		}

		return 'success';
	}

	/**
	 * 创建下载任务
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#c4c37753
	 * @return mixed
	 * @throws Exception
	 */
	public function createTask($taskType){
		return self::requestHik('/api/acps/v1/download/configuration/task/add', ['taskType' => $taskType]);
	}

	/**
	 * 任务添加待执行设备
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#cc977c04
	 * @param $taskId
	 * @param $resourceInfos
	 * @return mixed
	 */
	public function addToTask($taskId, $resourceInfos){
		return self::requestHik('/api/acps/v1/download/configuration/data/add', ['taskId' => $taskId, 'resourceInfos' => $resourceInfos]);
	}

	/**
	 * 开始任务
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#b969dda3
	 * @param $taskId
	 * @return mixed
	 * @throws Exception
	 */
	public function startTask($taskId){
		return self::requestHik('/api/acps/v1/authDownload/task/start', ['taskId' => $taskId]);
	}

	/**
	 * 权限下载情况
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#dad1a642
	 * @param $taskId
	 * @param $doorCode
	 * @return mixed
	 */
	public function getDownloadDetail($taskId, $doorCode){
		$data = [
			'taskId' => $taskId,
			'resourceInfo' => [
				'resourceIndexCode' => $doorCode,
				'resourceType' => 'acsDevice'
			],
			'pageNo' => 1,
			'pageSize' => 1000
		];
		return self::requestHik('/api/acps/v2/download_record/person/detail/search', $data);

	}

	/**
	 * 删除权限 -- 仅使用此接口的删除权限功能
	 * https://open.hikvision.com/docs/0c85dcc42e8645dfa8f736ebb37018a0#b95f0c75
	 * @param $outUserId
	 * @param $outDoorId
	 * @param $taskType
	 * @return mixed
	 */
	public function deleteAuthorization($outUserId, $outDoorId, $taskType){
		$data = [
			'resourceInfo' => [
				'resourceIndexCode' => $outDoorId,
				'resourceType' => 'acsDevice'
			],
			'personInfo' => [
				'personId' => $outUserId,
				'operatorType' => 2
			],
			'taskType' => $taskType // 1:卡片 4：人脸  5：卡片+人脸
		];
		return self::requestHik('/api/acps/v1/authDownload/task/simpleDownload', $data);
	}

	/**
	 * 修改权限
	 * 删除卡权限
	 * 追加卡权限
	 * 修改有效期
	 * @param $outUserId
	 * @param $outDoorId
	 * @param string $deleteCardNo
	 * @param string $addCardNo
	 * @param int $startTime
	 * @param int $endTime
	 * @return mixed
	 */
	public function modifyAuthorization($outUserId, $outDoorId, $startTime = 0, $endTime = 0, $deleteCardNo = '',
	                                    $addCardNo	= ''){
		$personInfo = [
			'personId' => $outUserId,
			'operatorType' => 1
		];
		$cards = [];
		if (!empty($deleteCardNo)){
			$cards[] = [
				'card' => $deleteCardNo,
				'status' => 2
			];
		}
		if (!empty($addCardNo)){
			$cards[] = [
				'card' => $addCardNo,
				'status' => 0
			];
		}
		if (!empty($cards)){
			$personInfo['cards'] = $cards;
		}

		if (!empty($startTime) && !empty($endTime)){
			$personInfo['startTime'] = $startTime;
			$personInfo['endTime'] = $endTime;
		}

		$data = [
			'resourceInfo' => [
				'resourceIndexCode' => $outDoorId,
				'resourceType' => 'acsDevice'
			],
			'personInfo' => $personInfo,
			'taskType' => 1 // 1:卡片 4：人脸  5：卡片+人脸
		];

		return self::requestHik('/api/acps/v1/authDownload/task/simpleDownload', $data);
	}



	/**
	 * 远程开门
	 * https://open.hikvision.com/docs/2367840dc2e946759cf05b9698870314#a3b66788
	 * @param $door
	 * @return mixed
	 * @throws Exception
	 */
	public function openDoor($door){
		$data = [
			'doorIndexCodes' => [
				$door
			],
			'controlType' => 2
		];
		return self::requestHik('/api/acs/v1/door/doControl', $data);
	}


	/**
	 * 开门日志, 每日获取一次，获取昨日的，保存在ES中
	 * https://open.hikvision.com/docs/2367840dc2e946759cf05b9698870314#a1ff98ef
	 * @param $startTime
	 * @param $endTime
	 * @param int $pageNo
	 * @param int $pageSize
	 * @return mixed
	 * @throws Exception
	 */
	public function getOpenLog($startTime, $endTime, $pageNo = 1, $pageSize = 1000){
		$data = [
			'pageNo' => $pageNo,
			'pageSize' => $pageSize,
			'startTime' => gmdate(DATE_ATOM, $startTime),
			'endTime' => gmdate(DATE_ATOM, $endTime),
			'receiveStartTime' => gmdate(DATE_ATOM, $startTime),
			'receiveEndTime' => gmdate(DATE_ATOM, $endTime),
			// 'doorRegionIndexCodes' => $regions,
			'sort' => 'eventTime',
			'order' => 'asc'
		];
		return self::requestHik('/api/acs/v2/door/events', $data);
	}

	/**
	 * 获取门禁事件的图片
	 * https://open.hikvision.com/docs/2367840dc2e946759cf05b9698870314#d894dc1b
	 * @param $svrIndexCode
	 * @param $picUri
	 * @return mixed
	 * @throws Exception
	 */
	public function getLogImage($svrIndexCode, $picUri){
		$data = [
			'svrIndexCode' => $svrIndexCode,
			'picUri' => $picUri
		];
		return self::requestHik('/api/acs/v1/event/pictures', $data);
	}


	/**
	 * 请求海康
	 * @param $api
	 * @param $data
	 * @param string $method
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function requestHik($api, $data, $method = 'POST'){
		return (new HikCarService())->requestHik($api, $data, $method);
	}
}