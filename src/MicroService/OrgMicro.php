<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class OrgMicro extends BaseMicroService
{
    /*
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
    */

    //组织节点列表
    public function nodeList($appId, $familyId, $parentId, $skip = 0, $limit=20)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/familyorg/search',
            [
                'appid' => $appId,
                'family_id' => $familyId,
                'parentid' => $parentId,
                'skip' => $skip,
                'limit' => $limit
            ]
        );
    }

    //组织节点详情
    public function nodeDetail($familyId, $uuid)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/familyorg/get',
            ['uuid' => '1',
                'family_id' => $familyId,
                'uuid' => $uuid
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

    /*
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
    */

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

    /*
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
    */


    //员工列表
    public function employeeList($familyId, $orgId, $levelId, $positionUuid, $skip = 0, $limit=200)
    {
        $data = [];
        if($familyId){
            $data['familyid'] = $familyId;
        }
        if($orgId){
            $data['orgid'] = $orgId;
        }
        if($levelId){
            $data['levelid'] = $levelId;
        }
        if($positionUuid){
            $data['position_uuid'] = $positionUuid;
        }
        if($familyId){
            $data['familyid'] = $familyId;
        }
        $data['skip'] = $skip;
        $data['limit'] = $limit;
        return AGRequest::getInstance()->post(
            $this->host,
            '/employee/search',
            $data
        );
    }
    //员工列表
    public function employeeGet($id)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/employee/get',
            [
                'id' => $id
            ]
        );
    }


    //员工列表
    public function employeeHire($userId, $realName, $mobile, $internalEmail, $gender, $education,
                                 $school, $married, $political, $birthday, $idCard, $hireType, $memo, $hiredDate, $onDate)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/employee/hire',
            [
                'userid' => $userId,
                'realname' => $realName,
                'mobile' => $mobile,
                'internal_email' => $internalEmail,
                'gender' => $gender,
                'education' => $education,
                'school' => $school,
                'married' => $married,
                'political' => $political,
                'birthday' => $birthday,
                'idcard' => $idCard,
                'hire_type' => $hireType,
                'memo' => $memo,
                'hiredate' => $hiredDate,
                'ondate' => $onDate,
                'limit' => $gender,
                'limit' => $gender
            ]
        );
    }
    //员工列表
    public function employeeFire($id, $fireDate)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/employee/fire',
            [
                'id' => $id,
                'firedate' => $fireDate
            ]
        );
    }
}
