<?php

/**
 * Created by PhpStorm.
 * User: zyw
 * Date: 2019/9/5
 * Time: 4:46 PM
 */

namespace App\Libs\MicroService\OrgMicroService;


use App\Http\Service\BaseService;
use App\Libs\MicroService\AGRequest;

class OrgMicroService extends BaseService
{
	private $host;

	public function __construct()
	{
		$this->host = env('OrgMicroService_host');
		if (!$this->host) {
			throw new \Exception('缺少请求host', 5003);
		}
	}

	//组织类型列表
	public function orgTypeList($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'orgtype/search',
			[
				'sign' => $sign,
				'type' => $type,
			]
		);
	}


	//组织节点列表
	public function nodeList($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/familyorg/search',
			[
				'appid' => '',
				'family_id' => '1',
				'parentid' => '1',
				'skip' => '',
				'limit' => '',
				'sign' => $sign,
				'type' => $type,
			]
		);
	}

	//组织节点详情
	public function nodeDetail($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/familyorg/get',
			[   'uuid' => '1',
				'family_id' => '1',
				'sign' => $sign,
				'type' => $type,
			]
		);
	}

	//创建组织节点
	public function nodeCreate($name, $orgType)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/org/add',
			[
				'name' => $name,
				'latitude' => '',
				'longitude' => '',
				'head' => '',
				'area_id' => '',
				'area_name' => '',
				'org_type' => $orgType
			]
		);
	}

	//修改组织节点
	public function nodeUpdate($uuid, $name)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/org/modify',
			[
				'id' => $uuid,
				'name' => $name,
				// 'latitude' => '',
				// 'longitude' => '',
				// 'head' => '',
				// 'area_id' => '',
				// 'area_name' => '',
				// 'org_type' => '',
				// 'status' => ''
			]
		);
	}

	//删除组织节点
	public function nodeDelete($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/org/remove',
			[
				'id' => '',
				'sign' => $sign,
				'type' => $type,
			]
		);
	}

	//绑定组织节点和框架 typeid=51公益架构
	public function nodeBindFrame($orgName, $orgUuid, $orgType)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/familyorg/bind',
			[
				'org_uuid' => $orgUuid,
				'typeid' => 52,
				'name' => $orgName,
				'org_type' => $orgType,
				'status' => 1
			]
		);
	}

	//解绑组织节点和框架
	public function nodeUnbindFrame($mobile, $content, $sign, $type = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/familyorg/unbind',
			[
				'uuid' => '1',
				'family_id' => '1',
				'sign' => $sign,
				'type' => $type,
			]
		);
	}











}