<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class RmsService extends BaseMicroService
{
	/**
	 * 增加类型
	 * @param $name
	 * @param $ename //英文名
	 * @param int $parentId //上级类型ID
	 * @param array $fields // 扩展字段 {"field":"字段名","name":"字段名称","dataType":"字段类型"}
	 * dataType：datetime，enum，string，numberic，int
	 * @return mixed
	 * @throws \Exception
	 */
	public function typeAdd($name, $ename, $parentId = 0, $fields = []){
		if (empty($name) || empty($ename)){
			throw new \Exception('类型名称不可为空', 5101);
		}
		$data = [
			'name' => $name,
			'ename ' => $ename,
			'parentid' => $parentId
		];
		if (!empty($fields)){
			$data['fields'] = json_encode($fields);
		}
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/add',
			$data
		);
	}

	/**
	 * 类型列表
	 * @param int $parentId //上级类型ID
	 * @param int $skip
	 * @param int $limit
	 * @return mixed
	 */
	public function typeList($parentId = 0, $skip = 0, $limit = 10){
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/list',
			[
				'parentid' => $parentId,
				'skip' => $skip,
				'limit' => $limit
			]
		);
	}

	/**
	 * 类型详情
	 * @param $typeId
	 * @return mixed
	 */
	public function typeGet($typeId){
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/get',
			[
				'typeid' => $typeId
			]
		);
	}

	public function typeListfield($typeId){
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/listfield',
			// '/type/listsamplefield',
			[
				'typeid' => $typeId
			]
		);
	}

	public function typeListsamplefield($typeId){
		return AGRequest::getInstance()->post(
			$this->host,
			 '/type/listsamplefield',
			[
				'typeid' => $typeId
			]
		);
	}

	public function typeFields($typeId){
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/listfield',
			// '/type/listsamplefield',
			[
				'typeid' => $typeId
			]
		);
	}

	/**
	 * 修改类型
	 * @param $id
	 * @param string $name
	 * @param string $ename
	 * @param array $fields // 同添加类型
	 * @return mixed
	 * @throws \Exception
	 */
	public function typeModify($id, $name = '', $ename = '', $fields = []){
		if (empty($name) && empty($ename) && empty($fields)){
			throw new \Exception('无修改内容', 5102);
		}
		$data = ['id' => $id];
		if (!empty($fields)){
			$data['fields'] = json_encode($fields);
		}
		if (!empty($name)){
			$data['name'] = $name;
		}
		if (!empty($ename)){
			$data['ename'] = $ename;
		}
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/modify',
			$data
		);
	}

	/**
	 * 删除类型
	 * @param $typeId
	 * @return mixed
	 */
	public function typeRemove($typeId){
		return AGRequest::getInstance()->post(
			$this->host,
			'/type/remove',
			[
				'typeid' => $typeId
			]
		);
	}

	/**
	 * 新增资源
	 * @param $typeid // 类型ID
	 * @param $name // 资源名称
	 * @param $roleId // 所属角色组ID
	 * @param $areaid // 所在地区ID，对接行政区域微服务
	 * @param int $count // 资源数量
	 * @param string $images // 资源图片
	 * @param int $parentid // 上级资源ID
	 * @param string $owner_uuid // 创建人ID
	 * @param string $address // 资源地址
	 * @param int $latitude // 纬度
	 * @param int $longitude // 经度
	 * @param int $status // 状态 1：正常 2：禁用 3：删除
	 * @param string $tag // 标签
	 * @param int $creationtime
	 * @param int $modifiedtime
	 * @param int $altitude // 海拔
	 * @return mixed
	 * @throws \Exception
	 */
	public function resourceAdd($typeid, $name, $roleId, $areaid, $count = 1, $images = '', $parentid = 0,
	                            $owner_uuid = '', $address = '', $desc = '', $latitude = 0, $longitude = 0, $status = 1, $tag =
	                            [], $creationtime = 0, $modifiedtime = 0, $altitude = 0){
		if (empty($creationtime)){
			$creationtime = time();
		}
		if (empty($typeid) || empty($name) || empty($roleId) || empty($areaid)){
			throw new \Exception('缺少必要参数', 5103);
		}
		$data = [
			'name'=> $name,
			'typeid' => $typeid,
			'roleId' => $roleId,
			'areaid' => $areaid,
			'count' => $count,
			'images' => $images,
			'parentid' => $parentid,
			'address' => $address,
			'desc' => $desc,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'status' => $status,
			'tag' => $tag,
			'creationtime' => $creationtime,
			'owner_uuid' => $owner_uuid,
			'altitude' => $altitude
		];

		if (empty($parentid)){
			unset($data['parentid']);
		}
		if (empty($tag)){
			unset($data['tag']);
		}

		$data_json = json_encode($data);
		return AGRequest::getInstance()->post(
			$this->host,
			'/resource/add',
			[
				'fields' => $data_json
			]
		);
	}

	/**
	 * 查询资源
	 * @param $name
	 * @param $typeid
	 * @param $roleId
	 * @param $areaid
	 * @param $parentid
	 * @param $status
	 * @param $owner_uuid
	 * @param $skip
	 * @param $limit
	 * @return mixed
	 */
	public function resourceList($name, $typeid, $roleId, $areaid, $parentid, $status, $owner_uuid, $skip, $limit){
		// 查询条件，json格式，如[{"field":"tag", "operator":"all", "value":["good"]}]
		$conditions = [];
		if (!empty($name)){
			$conditions[] = [
				'field' => 'name',
				'operator' => 'regex',
				'value' => $name
			];
		}
		if (!empty($roleId)){
			$conditions[] = [
				'field' => 'roleId',
				'operator' => '=',
				'value' => $roleId
			];
		}
		if (!empty($areaid)){
			$conditions[] = [
				'field' => 'areaid',
				'operator' => '=',
				'value' => $areaid
			];
		}
		if (!empty($parentid)){
			$conditions[] = [
				'field' => 'parentid',
				'operator' => '=',
				'value' => $parentid
			];
		}
		if (!empty($status)){
			$conditions[] = [
				'field' => 'status',
				'operator' => '=',
				'value' => (int)$status
			];
		}else{
			$conditions[] = [
				'field' => 'status',
				'operator' => 'in',
				'value' => [1, 2]
			];
		}
		if (!empty($owner_uuid)){
			$conditions[] = [
				'field' => 'owner_uuid',
				'operator' => '=',
				'value' => $owner_uuid
			];
		}
		$data = [
			'conditions' => json_encode($conditions),
			'skip' => $skip,
			'limit' => $limit
		];
		if (!empty($typeid)){
			$data['typeid'] = $typeid;
		}

		// dd($data);

		return AGRequest::getInstance()->post(
			$this->host,
			'/resource/list',
			$data
		);
	}

	/**
	 * 资源详情
	 * @param $uuid
	 * @return mixed
	 * @throws \Exception
	 */
	public function resourceGet($uuid){
		if (empty($uuid)){
			throw new \Exception('缺少资源uuid', 5105);
		}
		$detail = AGRequest::getInstance()->post(
			$this->host,
			'/resource/get',
			[
				'uuid' => $uuid
			]
		);
		if (empty($detail['images'])){
			$detail['images'] = [];
		}else{
			$detail['images'] = explode(',', $detail['images']);
		}
		return $detail;
	}

	/**
	 * 修改资源
	 * @param $uuid
	 * @param $typeid
	 * @param $name
	 * @param $areaid
	 * @param $count
	 * @param $images
	 * @param $parentid
	 * @param $address
	 * @param $latitude
	 * @param $longitude
	 * @param $status
	 * @param $tag
	 * @param $altitude
	 * @return mixed
	 * @throws \Exception
	 */
	public function resourceModify($uuid, $typeid, $name, $areaid, $count, $images, $parentid,
	                               $address, $latitude, $longitude, $status, $tag, $altitude){
		if (empty($uuid)){
			throw new \Exception('缺少资源uuid', 5105);
		}
		$data = [
			'name'=> $name,
			'typeid' => $typeid,
			'areaid' => $areaid,
			'count' => $count,
			'images' => $images,
			'parentid' => $parentid,
			'address' => $address,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'status' => (int)$status,
			'tag' => $tag,
			'modifiedtime' => time(),
			'altitude' => $altitude
		];
		$data_json = json_encode($data);
		// dd($data_json);
		return AGRequest::getInstance()->post(
			$this->host,
			'/resource/modify',
			[
				'uuid' => $uuid,
				'fields' => $data_json
			]
		);
	}

	/**
	 * 删除资源
	 * @param $uuid
	 * @return mixed
	 * @throws \Exception
	 */
	public function resourceRemove($uuid){
		if (empty($uuid)){
			throw new \Exception('缺少资源uuid', 5105);
		}
		return AGRequest::getInstance()->post(
			$this->host,
			'/resource/remove',
			[
				'uuid' => $uuid
			]
		);
	}

}