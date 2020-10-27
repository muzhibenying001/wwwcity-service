<?php

namespace App\Libs\MicroService\FinanceMicroService;


use App\Http\Service\BaseService;
use App\Libs\MicroService\AGRequest;


class FinanceMicroService extends BaseService
{
	private $host;

	public function __construct()
	{
		$this->host = env('FinanceMicroService_host');
		if (!$this->host) {
			throw new \Exception('缺少请求host', 5003);
		}
	}

//	快速交易接口
//	快速交易接口
	public function fastTransaction(
		$orderno,
		$orgtype, $orgaccountno,
		$desttype, $destaccountno,
		$money, $content = '', $detail = '',
		$stoptime = 0,
		$callback = '', $typeid = 0, $orgsaccount = '', $destsaccount = '')
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/transaction/fasttransaction',
			[
				'orderno' => $orderno,
				'orgtype' => $orgtype,
				'orgaccountno' => $orgaccountno,
				'desttype' => $desttype,
				'destaccountno' => $destaccountno,
				'money' => $money,
				'content' => $content,
				'detail' => $detail,
				'stoptime' => $stoptime,
				'callback' => $callback,
				'typeid' => $typeid,
				'orgsaccount' => $orgsaccount,
				'destsaccount' => $destsaccount,
			]
		);
	}

	//	开通账户接口
	public function openAccount($atid, $uuid, $ano, $password, $user_type, $name, $memo = '', $mode = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/account/openAccount',
			[
				'atid' => $atid,
				'uuid' => $uuid,
				'ano' => $ano,
				'password' => $password,
				'user_type' => $user_type,
				'name' => $name,
				'memo' => $memo,
				'mode' => $mode
			]
		);
	}

	//	开通账户接口
	public function queryAccount($atid, $ano, $rank = 0, $needSubAccountInfo = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/account/queryAccount',
			[
				'atid' => $atid,
				'ano' => $ano,
				'rank_user_type' => $rank,
				'needSubAccountInfo' => $needSubAccountInfo,
			]
		);
	}

	//	开通子账户接口
	public function devide($atid, $ano, $source_ename, $target_ename, $money, $mode)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/subaccount/devide',
			[
				'atid' => $atid,//货币id
				'ano' => $ano,//小程序用户账户ano
				'source_ename' => $source_ename,//：源账户，填空
				'target_ename' => $target_ename,//：子账户名称，填上面的英文简称
				'money' => $money,//：分配金额，填0
				'mode' => $mode,//：分拆模式，填1
			]
		);
	}

	/**
	 * 批量查询账户余额
	 * @param  [type]  $anos               [description]
	 * @param  integer $needSubAccountInfo [description]
	 * @return [type]                      [description]
	 */
	public function batchQueryAccount($anos, $needSubAccountInfo = 0)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/account/batchQueryAccount',
			[
				'anos' => $anos,
				'needSubAccountInfo' => $needSubAccountInfo,
			]
		);
	}

	/**
	 * 子账户类型分拆
	 * @param $atid
	 * @param $ename
	 * @param $relate_ename
	 * @param $memo
	 * @param $mode
	 * @return mixed
	 */
	public function devideSubAccountType($atid, $ename, $relate_ename, $memo, $mode)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/subaccounttype/devide',
			[
				'atid' => $atid,
				'ename' => $ename,
				'relate_ename' => $relate_ename,
				'memo' => $memo,
				'mode' => $mode,
			]
		);
	}

	/**
	 * 获取额度统计信息
	 * @param int $atid
	 * @return mixed
	 */
	public function getBalanceStat(int $atid)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/balance/getBalanceStat',
			[
				'atid' => $atid,
			]
		);
	}

	/**
	 * 获取货币的余额数量等统计信息
	 * @param int $atid
	 * @param int $user_type
	 * @param string $ignoreAnos
	 * @return mixed
	 */
	public function statByAtype(int $atid, int $user_type = 0, string $ignoreAnos = '')
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/account/statByAtype',
			[
				'atid' => $atid,
				'user_type' => $user_type,
				'ignoreAnos' => $ignoreAnos,
			]
		);
	}

	// 创建货币
	public function createAccountType(
		$typeid, $atName, $account, $info = '',
		$param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '',
		$param6 = '', $param7 = '', $param8 = '', $param9 = '', $param10 = ''
	)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/accountType/create',
			[
				'typeid' => $typeid,
				'atName' => $atName,
				'account' => $account,
				'info' => $info,
				'param1' => $param1, 'param2' => $param2, 'param3' => $param3, 'param4' => $param4,
				'param5' => $param5, 'param6' => $param6, 'param7' => $param7, 'param8' => $param8,
				'param9' => $param9, 'param10' => $param10
			]
		);
	}

	// 货币启用禁用
	public function updateATStatus(int $atid, int $status)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/accountType/updateStatus',
			[
				'atid' => $atid,
				'status' => $status,
			]
		);
	}

	// 最大额度申请
	public function balanceApply(int $atid, $balance, string $reason = '')
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/balance/apply',
			[
				'atid' => $atid,
				'balance' => $balance,
				'reason' => $reason,
			]
		);
	}

	// 最大额度审核
	public function balanceApprove(int $id, int $status, $approve_reason = '')
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/balance/approve',
			[
				'id' => $id,
				'status' => $status,
				'approve_reason' => $approve_reason,
			]
		);
	}

	// 资金发放
	public function cashSend(int $atid, $money, string $reason)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/cashSend/cashSend',
			[
				'atid' => $atid,
				'money' => $money,
				'reason' => $reason,
			]
		);
	}

	// 在线充值
	public function deposit($atid, $ano, $money, $orderNo, $depositReason = '', $destsaccount = '')
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/transaction/deposit',
			[
				'atid' => $atid,
				'ano' => $ano,
				'money' => $money,
				'orderNo' => $orderNo,
				'depositReason' => $depositReason,
				'destsaccount' => $destsaccount,
			]
		);
	}

	#######################
	########汇兑规则接口 开始

	// 汇兑规则查询
	public function searchExchangeRule($atid, $atid2 = 0, $mode = 3, $skip = 0, $limit = 10)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/exchangeRule/search',
			[
				'atid' => $atid,
				'atid2' => $atid2,
				'mode' => $mode,
				'skip' => $skip,
				'limit' => $limit
			]
		);
	}

	// 新增汇兑规则
	public function createExchangeRule($mode, $atid1, $atid2,
	                                   $max_balance1 = 0, $daily_balance1 = 0, $user_balance1 = 0,
	                                   $max_balance2 = 0, $daily_balance2 = 0, $user_balance2 = 0
	)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/exchangeRule/create',
			[
				'mode' => $mode,
				'atid1' => $atid1,
				'atid2' => $atid2,
				'max_balance1' => $max_balance1,
				'daily_balance1' => $daily_balance1,
				'user_balance1' => $user_balance1,
				'max_balance2' => $max_balance2,
				'daily_balance2' => $daily_balance2,
				'user_balance2' => $user_balance2,
			]
		);
	}

	// 汇兑规则详情
	public function detailExchangeRule($id)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/exchangeRule/detail',
			[
				'id' => $id,
			]
		);
	}

	// 修改汇兑规则
	public function updateExchangeRule($id, $max_balance, $daily_balance, $user_balance)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/exchangeRule/update',
			[
				'id' => $id,
				'max_balance' => $max_balance,
				'daily_balance' => $daily_balance,
				'user_balance' => $user_balance,
			]
		);
	}

	// 修改汇兑规则状态
	public function updateStatusExchangeRule($id, $status)
	{
		return AGRequest::getInstance()->post(
			$this->host,
			'/exchangeRule/updateStatus',
			[
				'id' => $id,
				'status' => $status,
			]
		);
	}

	########汇兑规则接口 结束
	#######################

}