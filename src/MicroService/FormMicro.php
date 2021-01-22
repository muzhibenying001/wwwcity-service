<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;
use Illuminate\Support\Arr;

class FormMicro extends BaseMicroService
{
    # 获取表单详情
    public function getFormType($data)
    {
        $this->isSet($data, 'id');

        return AGRequest::getInstance()->post($this->host, '/form/getFormType', $data);
    }


    # 创建表单类型
    public function createFormType($data)
    {
        $this->isSet($data, ['id', 'name', 'loop_type', 'status']);

        $data = Arr::add($data, 'starttime', '');
        $data = Arr::add($data, 'stoptime', '');
        $data = Arr::add($data, 'memo', '');
        $data = Arr::add($data, 'explain', '');

        return AGRequest::getInstance()->post($this->host, '/formDesign/createFormType', $data);
    }

    # 添加表单项
    public function createItem($data)
    {
        $this->isSet($data, ['form_type_id', 'name', 'type']);

        $data = Arr::add($data, 'form_key', '');
        $data = Arr::add($data, 'memo', '');
        $data = Arr::add($data, 'weight', '');
        $data = Arr::add($data, 'default_value', '');
        $data = Arr::add($data, 'enable_null', '');
        $data = Arr::add($data, 'isOnly', '');
        $data = Arr::add($data, 'option', '');

        return AGRequest::getInstance()->post($this->host, '/formDesign/createItem', $data);
    }

    # 批量添加表单项
    public function createBatchItem($data)
    {
        $this->isSet($data, ['form_type_id', 'items']);

        return AGRequest::getInstance()->post($this->host, '/formDesign/createBatchItem', $data);
    }

    # 修改表单项
    public function modifyItem($data)
    {
        $this->isSet($data, 'item_id');

        $data = Arr::add($data, 'name', '');
        $data = Arr::add($data, 'form_key', '');
        $data = Arr::add($data, 'memo', '');
        $data = Arr::add($data, 'weight', '');
        $data = Arr::add($data, 'default_value', '');
        $data = Arr::add($data, 'enable_null', '');
        $data = Arr::add($data, 'isOnly', '');
        $data = Arr::add($data, 'option', '');
        $data = Arr::add($data, 'score_type', '');
        $data = Arr::add($data, 'score ', '');
        $data = Arr::add($data, 'answer ', '');

        return AGRequest::getInstance()->post($this->host, '/formDesign/modifyItem', $data);
    }

    # 修改表单项状态
    public function modifyItemStatus($data)
    {
        $this->isSet($data, ['item_id', 'status']);

        return AGRequest::getInstance()->post($this->host, '/formDesign/modifyItemStatus', $data);
    }

    # 获取表单项列表
    public function searchFormItem($data)
    {
        $this->isSet($data, 'formTypeId');

        $data = Arr::add($data, 'status', '');

        return AGRequest::getInstance()->post($this->host, '/form/searchFormItem', $data);
    }

    # 创建表单分组
    public function createFormTypesGroup($data)
    {
        $this->isSet($data, ['name', 'form_type_ids', 'loop_type']);

        $data = Arr::add($data, 'starttime', '');
        $data = Arr::add($data, 'status', 1);
        $data = Arr::add($data, 'stoptime', '');
        $data = Arr::add($data, 'memo', '');
        $data = Arr::add($data, 'explain', '');

        return AGRequest::getInstance()->post($this->host, '/formDesign/createFormTypesGroup', $data);
    }

    # 修改表单分组
    public function modifyFormTypesGroup($data)
    {
        $this->isSet($data, 'group_id');

        $data = Arr::add($data, 'name', '');
        $data = Arr::add($data, 'form_type_ids', '');
        $data = Arr::add($data, 'starttime', '');
        $data = Arr::add($data, 'stoptime', '');
        $data = Arr::add($data, 'memo', '');
        $data = Arr::add($data, 'explain', '');

        return AGRequest::getInstance()->post($this->host, '/formDesign/createFormTypesGroup', $data);
    }

    # 获取表单分组列表
    public function searchFormTypeGroup($data)
    {
        $data = Arr::add($data, 'name', '');
        $data = Arr::add($data, 'loop_type', '');
        $data = Arr::add($data, 'status', '');
        $data = Arr::add($data, 'skip', '');
        $data = Arr::add($data, 'limit', '');

        return AGRequest::getInstance()->post($this->host, '/form/searchFormTypeGroup', $data);
    }

    # 提交表单填报内容
    public function answer($data)
    {
        $this->isSet($data, ['tid', 'token', 'answer']);

        return AGRequest::getInstance()->post($this->host, '/answer/answer', $data);
    }

    # 获取身份令牌
    public function create($data)
    {
        $this->isSet($data, 'entity_code_1');

        $data = Arr::add($data, 'entity_code_2', '');
        $data = Arr::add($data, 'entity_code_3', '');
        $data = Arr::add($data, 'entity_code_4', '');
        $data = Arr::add($data, 'entity_code_5', '');

        return AGRequest::getInstance()->post($this->host, '/userToken/create', $data);
    }
}
