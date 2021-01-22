<?php

namespace Cy\WWWCityService\MicroService;

use Cy\WWWCityService\Libs\MicroService\AGRequest;
use Cy\WWWCityService\Libs\MicroService\BaseMicroService;

class UserMicro extends BaseMicroService
{
    //token验证接口
    public function userToken($token)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/token',
            ['token' => $token]
        );
    }

    //登录接口
    public function login($account, $ts, $sign)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/login',
            [
                'account' => $account,
                'ts' => $ts,
                'sign' => $sign,
            ]
        );
    }

    //通过account获取用户
    public function getByAccount($account)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/getByAccount',
            [
                'account' => $account,
            ]
        );
    }

    //关键字搜索用户
    public function searchByKey($key)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/searchByKey',
            [
                'key' => $key,
            ]
        );
    }

    //检查用户是否存在
    public function checkRegister($mobile)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/checkRegister',
            [
                'key' => $mobile,
            ]
        );
    }

    // 新增用户
    public function add($data)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/add', $data
        );
    }

    /**
     * 修改用户微服务信息
     * @param  [type] $id       [description]
     * @param  [type] $nickname [description]
     * @param  [type] $address  [description]
     * @param  [type] $idcard   [description]
     * @return [type]           [description]
     */
    public function modify($id, $nickname, $address)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/modify',
            [
                'id' => $id,
                'nickname' => $nickname,
                'address' => $address,
            ]
        );
    }

    /**
     * 获取详情
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get($id)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/get',
            [
                'id' => $id,
            ]
        );
    }


    /**
     * 修改密码
     * @param $id
     * @param $password
     * @param $oldpassword
     * @return mixed
     */
    public function modifyPassword($id, $oldpassword, $password)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/user/modifyPassword',
            [
                'id' => $id,
                'password' => $password,
                'oldpassword' => $oldpassword,
            ]
        );
    }

    /**
     * 获取人脸数据
     * @param $id
     * @param $image
     * @return mixed
     */
    public function getFace($id)
    {
        return AGRequest::getInstance()->post(
            $this->host,
            '/face/get',
            [
                'id' => $id
            ]
        );
    }

    /**
     * 获取身份证照片
     * @param $id
     * @return mixed
     */
    public function getIdCardPhoto($id)
    {

        return AGRequest::getInstance()->post($this->host, '/user/getIDCardImage', [
            'id' => $id
        ]);
    }

    /**
     * 跟据身份证号查询
     * @param $id_card
     * @return mixed
     */
    public function getByIdCard($id_card)
    {
        return AGRequest::getInstance()->post($this->host, '/user/getByIdCard', [
            'idcard' => $id_card
        ]);
    }

    /**
     * 判断用户是否实名认证
     */
    public function isAuth($id)
    {
        return AGRequest::getInstance()->post($this->host, '/user/isAuth', [
            'id' => $id
        ]);
    }

    /**
     * 身份证图片识别
     * @param $image
     * @param $cardside
     * @return mixed
     */
    public function idCardOCR($image, $cardside)
    {
        return AGRequest::getInstance()->post($this->host, '/user/idCardOCR', [
            'image' => $image,
            'cardside' => $cardside
        ]);
    }

    /**
     * 上传身份证图片
     * @param $id
     * @param $image
     * @param string $cardside
     * @param int $isforce
     * @return mixed
     */
    public function uploadIDCard($id, $image, $cardside = 'FRONT', $isforce = 0)
    {
        return AGRequest::getInstance()->post($this->host, '/user/uploadIDCard', [
            'id' => $id,
            'image' => $image,
            'cardside' => $cardside,
            'isforce' => $isforce
        ]);
    }

    /**
     * 通过身份证图片保存用户
     * @param $image
     * @return mixed
     */
    public function saveIDCard($image)
    {
        return AGRequest::getInstance()->post($this->host, '/user/saveIDCard', [
            'image' => $image,
        ]);
    }

    /**
     * 删除用户
     * @param $id
     * @return mixed
     */
    public function del($id)
    {
        return AGRequest::getInstance()->post($this->host, '/user/del', [
            'id' => $id,
        ]);
    }

    /**
     * 修改手机号
     */
    public function modifyMobileForce($id, $mobile)
    {
        return AGRequest::getInstance()->post($this->host, '/user/modifyMobileForce', [
            'id' => $id,
            'mobile' => $mobile,
        ]);
    }

    /**
     * 上传人脸照片
     * @param $id
     * @param $image
     * @param int $isCompareIDCard
     * @return mixed
     */
    public function faceUpload($id, $image, $isCompareIDCard = 1)
    {
        return AGRequest::getInstance()->post($this->host, '/face/upload', [
            'id' => $id,
            'image' => $image,
            'isCompareIDCard' => $isCompareIDCard,
        ]);
    }

    /**
     * 人脸识别
     * @param $id
     * @param $image
     * @return mixed
     */
    public function faceCheck($id, $image)
    {
        return AGRequest::getInstance()->post($this->host, '/face/check', [
            'id' => $id,
            'image' => $image,
        ]);
    }

    /**
     * 车辆驾驶证识别
     * @param $data
     * @return mixed
     */
    public function ocrDrivingLicense($data)
    {
        $this->isSet($data, 'image');
        return AGRequest::getInstance()->post($this->host, '/ocr/driving/license', $data);
    }

    /**
     * 车辆行驶证识别
     * @param $data
     * @return mixed
     */
    public function ocrVehicleLicense($data)
    {
        $this->isSet($data, ['image', 'side']);
        return AGRequest::getInstance()->post($this->host, '/ocr/vehicle/license', $data);
    }
}
