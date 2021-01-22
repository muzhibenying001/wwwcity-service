<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;
use Illuminate\Support\Arr;

class RmsMicro extends BaseMicroService
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
    public function typeAdd($name, $ename, $parentId = 0, $fields = [])
    {
        if (empty($name) || empty($ename)) {
            throw new \Exception('类型名称不可为空', 5101);
        }
        $data = [
            'name' => $name,
            'ename ' => $ename,
            'parentid' => $parentId
        ];
        if (!empty($fields)) {
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
    public function typeList($parentId = 0, $skip = 0, $limit = 10)
    {
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
    public function typeGet($typeId)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/type/get',
            [
                'typeid' => $typeId
            ]
        );
    }

    public function typeListfield($typeId, $recursion = 1)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/type/listfield',
            // '/type/listsamplefield',
            [
                'typeid' => $typeId,
                'recursion' => $recursion
            ]
        );
    }

    public function typeListsamplefield($typeId)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/type/listsamplefield',
            [
                'typeid' => $typeId
            ]
        );
    }

    public function typeFields($typeId)
    {
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
    public function typeModify($id, $name = '', $ename = '', $fields = [])
    {
        if (empty($name) && empty($ename) && empty($fields)) {
            throw new \Exception('无修改内容', 5102);
        }
        $data = ['id' => $id];
        if (!empty($fields)) {
            $data['fields'] = json_encode($fields);
        }
        if (!empty($name)) {
            $data['name'] = $name;
        }
        if (!empty($ename)) {
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
    public function typeRemove($typeId)
    {
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
     * @param $fields 资源属性
     * @param int $creationtime
     * @param int $modifiedtime
     * @return mixed
     * @throws \Exception
     */
    public function resourceAdd($data)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/resource/add',
            [
                'fields' => json_encode($data)
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
    public function resourceList($data)
    {
        $this->isSet($data, 'typeid');

        $data = Arr::add($data, 'conditions', []);
        $data = Arr::add($data, 'skip', '');
        $data = Arr::add($data, 'limit', '');
        $data = Arr::add($data, 'fields', '');
        $data = Arr::add($data, 'typeLevel', '');

        $data['conditions'] = json_encode($data['conditions']);

        return AGRequest::getInstance()->post($this->host, '/resource/list', $data);
    }

    /**
     * 资源详情
     * @param $uuid
     * @return mixed
     * @throws \Exception
     */
    public function resourceGet($uuid)
    {
        if (empty($uuid)) {
            throw new \Exception('缺少资源uuid', 5105);
        }
        $detail = AGRequest::getInstance()->post(
            $this->host,
            '/resource/get',
            [
                'uuid' => $uuid
            ]
        );
        if (empty($detail['images'])) {
            $detail['images'] = [];
        } else {
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
    public function resourceModify($uuid, $data)
    {
        if (empty($uuid)) {
            throw new \Exception('缺少资源uuid', 5105);
        }

        return AGRequest::getInstance()->post(
            $this->host,
            '/resource/modify',
            [
                'uuid' => $uuid,
                'fields' => json_encode($data)
            ]
        );
    }

    /**
     * 删除资源
     * @param $uuid
     * @return mixed
     * @throws \Exception
     */
    public function resourceRemove($uuid)
    {
        if (empty($uuid)) {
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
