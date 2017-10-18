# 微信网页授权中心


「微信网页授权中心」类似用户登录授权中心，用于统一签发微信授权成功后的用户信息。

## 说明

本系统是基于轻量级的 [Lumen](https://lumen.laravel-china.org) 框架实现的。通过 白名单列表 + 访问签名验证 的方式来保证调用的合法性。微信授权成功后会下发一个访问令牌，接入方拿到令牌后向授权中心索要令牌对应微信用户的数据信息  
- Tips: 接入授权中心后，在本地也可以通过 Web 开发者工具进行需要网页授权的微信开发，不会遇到 `redirect_url 参数错误` 的问题

## 安装&&准备

首先跟正常 [Lumen](https://lumen.laravel-china.org) 代码处理一样，你需要先 `composer update` 将框架的核心文件准备好。  
接着你需要将 `.env.example` 文件拷贝一份到 `.env` 根据你的环境情况配置一下里面的内容。  

系统提供了 `artisan` 自定义命令 `whitelist:make` 和 `system:install`  

数据库准备：  
安装 `php artisan system:install`  

管理授权白名单：  
创建白名单用户 `php artisan whitelist:make create you_system_name`  
移除白名单用户 `php artisan whitelist:make remove you_system_name`  
***创建白名单用户成功后，你需要将生成的白名单授权码保存下来，在发起授权的时候需要被用于签名***  

## 发起授权

假设你的授权中心地址为：https://wxauth.example.com
接入授权中心你需要在授权中心的白名单中。你需要跳转页面的形式去发起授权申请，授权的跳转地址为 https://wxauth.example.com/auth


在向授权中心发起授权申请的时候，你需要提供以下几个参数  
`system`,`timestamp`,`nonce`,`signature`,`redirect_url`  
他们分别是  

- `system`: 系统标识「与授权中心白名单存储数据一致」
- `timestamp`: 时间戳
- `nonce`: 随机字符串
- `signature`: 签名密文「加密规则见下文」
- `redirect_url`: 授权成功后的回调地址「请进行 URL 转码」

授权成功后，授权中心将在 `redirect_url` 的后面带上拥有授权用户信息访问权限的 `access_token` 访问令牌，你需要使用拿到的访问令牌去向授权中心索要刚授权成功的用户数据。  
获取用户授权数据的接口地址为 https://wxauth.example.com/userinfo

你需要用 `GET` 的方式去调用对应的接口，接口需要进行签名验证，除签名必要参数以外你还需要提供刚拿到的 `access_token` 访问令牌。签名通过且令牌有效的情况下你可以得到用户的授权数据信息。  
这里对返回数据做了筛选，授权中心默认仅返回 `openid`,`unionid`,`nickname`,`sex`,`province`,`city`,`country`,`headimgurl` 的数据

## 签名加密

签名需要用到 `system`,`token`,`timestamp`,`nonce` 四个参数，其中 `token` 为系统新增白名单用户时生成的授权码。
对四个参数进行字典序排序后以 URL-encode 的格式拼接成字符串（PHP 里面可以使用 `http_build_query` 函数），然后对字符串进行 `sha1` 加密生成 `signature`

## 联系

Email: i@joosie.cn