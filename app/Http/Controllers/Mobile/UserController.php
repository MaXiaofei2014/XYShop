<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Good\CouponUser;
use App\Models\Good\Order;
use App\Models\Good\OrderGood;
use App\Models\Good\ReturnGood;
use App\Models\User\Consume;
use App\Models\User\SignLog;
use App\Models\User\User;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    // 用户中心
    public function getCenter()
    {
        $pos_id = 'center';
        $title = '用户中心';
        $info = User::where('id',session('member')->id)->first();
        // 查这个用户今天签没签到
        $sign = SignLog::where('user_id',session('member')->id)->where('type',1)->where('signtime',date('Y-m-d 00:00:00'))->orderBy('id','desc')->first();
        return view(cache('config')['theme'].'.user.center',compact('pos_id','title','info','sign'));
    }
    // 修改个人信息
    public function getUserinfo()
    {
        $pos_id = 'center';
        $title = '修改个人信息';
        $info = User::findOrFail(session('member')->id);
        return view(cache('config')['theme'].'.user.userinfo',compact('pos_id','title','info'));
    }
    public function postUserinfo(Request $req)
    {
        $validator = Validator::make($req->input(), [
          'data.nickname' => 'required|max:255',
          'data.phone' => 'required|digits:11',
          'data.email' => 'required|email',
        ]);
        $attrs = array(
          'data.nickname' => '昵称',
          'data.phone' => '手机号',
          'data.email' => '邮箱',
        );
        $validator->setAttributeNames($attrs);
        if ($validator->fails()) {
            // 如果有错误，提示第一条
            return back()->with('message',$validator->errors()->all()[0]);
        }
        $data = $req->input('data');
        User::where('id',session('member')->id)->update($data);
        return redirect(url('center'))->with('message','修改个人信息成功！');
    }
    // 修改密码
    public function getPasswd()
    {
        $pos_id = 'center';
        $title = '修改密码';
        return view(cache('config')['theme'].'.user.passwd',compact('pos_id','title'));
    }
    public function postPasswd(Request $req)
    {
        $validator = Validator::make($req->input(), [
          'passwd' => 'required|min:6|max:15|confirmed',
          'passwd_confirmation' => 'required|min:6|max:15',
        ]);
        $attrs = array(
          'passwd' => '新密码',
          'passwd_confirmation' => '新密码',
        );
        $validator->setAttributeNames($attrs);
        if ($validator->fails()) {
            // 如果有错误，提示第一条
            return back()->with('message',$validator->errors()->all()[0]);
        }
        User::where('id',session('member')->id)->update(['password'=>encrypt($req->passwd)]);
        return redirect(url('center'))->with('message','修改密码成功！');
    }
    // 消费记录
    public function getConsume()
    {
        $pos_id = 'center';
        $title = '消费记录';
        $consume = Consume::where('user_id',session('member')->id)->orderBy('id','desc')->paginate(20);
        return view(cache('config')['theme'].'.user.consume',compact('pos_id','title','consume'));
    }
    // 优惠券
    public function getCoupon()
    {
        $pos_id = 'center';
        $title = '消费记录';
        $list = CouponUser::with('coupon')->where('user_id',session('member')->id)->orderBy('id','desc')->paginate(20);
        return view(cache('config')['theme'].'.user.coupon',compact('pos_id','title','list'));
    }
}
