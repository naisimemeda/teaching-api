### Api 相关测试

## 注册、第三方登陆测试

在多次测试教师和学生登录测试均为正常之后, 测试注册以及第三方登陆发现出现了认证多表失败...
具体表现为因为在生成令牌时， 没有触发相关事件 存储这个令牌所对应的 Provider。

因为注册后返回令牌, 第三方登陆相关的逻辑是引用的 Passport 的 GitHub Issues 中
找到的 [解决方案](https://github.com/laravel/passport/issues/71#issuecomment-330506407)。
在阅读相关代码之后发现是不会触发扩展包 `AccessTokenCreated` 事件, 解决方案为在这个 Trait 类中生成令牌之后
手动存储对应的 Provider。 

这里因为我没有信用卡没办法引入 Heroku 的 Redis 就使用的 DB 存储每个令牌对应的 Provider 在每次请求时都会去查 DB,
这里使用 Redis 的话对于性能方面还是有提升的。

- 再来测试一下相关逻辑教师以及学生不同用户身份， 登陆、 注册、 第三方均以存储令牌对应的用户表， 经过中间件认证之后也没有出现只默认查询 `Auth` 配置文件默认表.


## Line 绑定

这里测试之后发现一个最开始设计的问题, 因为 `axios` 是异步请求操作没办法重定向页面, 直接 Get 跳转也是没有办法设置 `Header` 携带 ` Authorization ` 获得具体绑定的用户 ID , 然后就让前端传过来, 这意味着用户可以恶意传入其他用户 ID 然后通过 Line 绑定.....
 
这里已经改为绑定之前请求后台接口, 获取值为用户 ID 的一个随机 `Key` , 绑定接口使用 `Key`
来获取用户ID

初次绑定时, 平台存在教师已经该 Line ID 会进行个人信息更新, 这里注册多个教师账号进行同一个 Line 绑定时, 没有出现重复绑定的情况, 仅会更新最初绑定的平台账号的个人资料信息

创建多个学生账号进行绑定测试, 均绑定成功

## 学校
#### 教师:
- 申请学校

因为 `School` 模型将字段转为 `boolean` 类型, 并在创建学校时设置为 `false` 确保申请学校后是未审核状态
```php
 protected $casts = [
        'status' => 'boolean', 
 ];
```
并定义多对多关联之后 在中间表写入相应关系, 并给予申请人管理员身份

```php
$school->teacher()->attach(Auth::user(), ['is_admin' => true])
```

这里测试的时候发现因为涉及到多表操作, 但是这里代码当时并没有加入事务, 经过测试之后已经加上相关代码

- 邮箱邀请教师

未注册邮箱会提示用户先进行注册流程, 邮箱邀请链接所附带的 `Key` 也做到了一次性使用之后失效。

经过前面 `Request` 类验证相关参数之后, 会再次验证用户是否重复加入学校, 这里测试之后没有出现重复加入的情况。

这里也有个问题是， 创建的时候并没有指定权限状态为 `false`, 因为数据库字段默认填充 `false`, 但是出于代码严谨考虑这里还是必须指定状态的. 相关代码已经加上


- 普通教师权限验证

因为可以通过更改路由上的 ID 查看未加入的学校, 这里使用 `Policy` 对教师查看学校列表进行验证, 测试之后确认了未加入学校是无法查看的.

当进行邀请教师、 创建学生、 沟通学生操作时均使用 `Request` 验证类, 测试之后并没有出现可以越权操作.



- 聊天

经过测试发现, 获取与学生最近聊天的验证没有完善, 教师可以通过更改路由参数来获取未与自己建立关系的学生信息, 需要完善验证 避免被恶意用户遍历信息 从而爬取平台用户信息, 相关代码已经修正


#### 学生:

- 关注教师

代码方面 学生关注表多对多关联定义正确

```php
public function followersTeachers()
{
   return $this->belongsToMany(Teacher::class, 'followers')
        ->withTimestamps()
        ->orderBy('followers.created_at', 'desc');
}
```

测试学生重复请求关注接口时, 没有出现重复关注的情况. 

获取全部教师列表接口之后, 返回标明是否关注教师字段没有返回错误. 测试后也是正常的

- 聊天

测试过程中没有出现学生可以沟通其他教师的情况, 只能和绑定自己学校的管理员教师对话

### 前端

- 不同身份登录

测试登录成功之后使用 `Vuex` 进行持久化存储用户相关信息没有问题.  并且没有出现身份信息错乱存储的情况

登陆之后只会加入自己的私人频道，没有出现加入他们私人频道, 以及能够正常加入公共频道

- 通知、聊天

通过后台发送广播通知测试能否接受信息,  学生以及教师都能够接受到广播信息。


只有当用户不处于聊天界面时, 才会进行右上角弹出提示, 正常聊天时会自动更新信息, 这里通过多次测试发送聊天信息确认功能没有问题, 

测试了所有聊天信息使用 `Vuex` 能够持久化存储成功 没有出现数据丢失的情况

- 路由

当访问不属于自己身份相关路由时, 会进行跳转, 这里测试没有出现可以跨身份访问路由的情况.

测试时更改路由参数, 访问自己没有权限的相关数据, 只有弹出提示 没有进行页面跳转. 因为代码方面调用 `router.push` 时传入的路由参数错误, 相关代码已经更新



### 管理后台

- 学校审核

后台负责学校审核相关操作正常, 修改学校相关信息.

- 多个列表操作处理

增删改查功能测试之后都保证了正常使用, 因为修改图片字段时 laravel-admin 仅存入相对路径, 就会导致修改图片后 前台图片失效, 相关操作处理已经修改 指定了图片前缀域名

- 发送通知

先测试发送广播通知, 以及通知所有 Line 用户, 发送成功之后 打开前端页面 学生和教师均接受消息成功

因为 Line 用户可以绑定多个账户, 测试这里批量通知 Line 用户时会不会重复接受同一条通知, 
代码相关也进行了批量通知时 去除重复 ID 的操作, 测试之后没有重复通知.

后台进行私人通知时, 前端接受正常 并且右上角弹出, 仅通知了私人频道 没有出现变成公共频道通知

