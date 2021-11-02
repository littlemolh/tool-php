# 微博相关

## Oauth2

### access_token

OAuth2的access_token接口

#### URL
---
https://api.weibo.com/oauth2/access_token

#### HTTP请求方式
POST

#### 请求参数
|               | 必选  | 类型及范围 | 说明                               |
| :-----------: | :---: | :--------: | :--------------------------------- |
|   client_id   | true  |   string   | 申请应用时分配的AppKey。           |
| client_secret | true  |   string   | 申请应用时分配的AppSecret。        |
|  grant_type   | true  |   string   | 请求的类型，填写authorization_code |

grant_type为authorization_code时

|              | 必选  | 类型及范围 | 说明                                       |
| :----------: | :---: | :--------: | :----------------------------------------- |
|     code     | true  |   string   | 调用authorize获得的code值。                |
| redirect_uri | true  |   string   | 回调地址，需需与注册应用里的回调地址一致。 |

#### 返回数据
```json
 {
       "access_token": "ACCESS_TOKEN",
       "expires_in": 1234,
       "remind_in":"798114",
       "uid":"12341234"
 }
```

|  返回值字段  | 字段类型 | 字段说明                                                                                                                                                                                                      |
| :----------: | :------: | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| access_token |  string  | 用户授权的唯一票据，用于调用微博的开放接口，同时也是第三方应用验证微博用户登录的唯一票据，第三方应用应该用该票据和自己应用内的用户建立唯一影射关系，来识别登录状态，不能使用本返回值里的UID字段来做登录识别。 |
|  expires_in  |  string  | access_token的生命周期，单位是秒数。                                                                                                                                                                          |
|  remind_in   |  string  | access_token的生命周期（该参数即将废弃，开发者请使用expires_in）。                                                                                                                                            |
|     uid      |  string  | 授权用户的UID，本字段只是为了方便开发者，减少一次user/show接口调用而返回的，第三方应用不能用此字段作为用户登录状态的识别，只有access_token才是用户授权的唯一票据。                                            |


### get_token_info

查询用户access_token的授权相关信息，包括授权时间，过期时间和scope权限。

#### URL
https://api.weibo.com/oauth2/get_token_info

#### HTTP请求方式
POST

#### 请求参数
access_token：用户授权时生成的access_token。

#### 返回数据

```json
{
      "uid": 1073880650,
      "appkey": 1352222456,
      "scope": null,
      "create_at": 1352267591,
      "expire_in": 157679471
}
```

| 返回值字段 | 字段类型 | 字段说明                                                                     |
| :--------: | :------: | :--------------------------------------------------------------------------- |
|    uid     |  string  | 授权用户的uid。                                                              |
|   appkey   |  string  | access_token所属的应用appkey。                                               |
|   scope    |  string  | 用户授权的scope权限。                                                        |
| create_at  |  string  | access_token的创建时间，从1970年到创建时间的秒数。                           |
| expire_in  |  string  | access_token的剩余时间，单位是秒数，如果返回的时间是负数，代表授权已经过期。 |

如果查询的access_token已经失效，并且已经被覆盖过，则接口会报错，详细见OAuth2.0错误定义。

### revokeoauth2
授权回收接口，帮助开发者主动取消用户的授权。

#### URL
https://api.weibo.com/oauth2/revokeoauth2

#### HTTP请求方式
GET/POST

#### 请求参数
access_token: 用户授权应用的access_token

#### 返回数据
```json
{
    "result":"true"
}
```
#### 使用场景说明
1. 应用下线时，清空所有用户的授权
2. 应用新上线了功能，需要取得用户scope权限，可以回收后重新引导用户授权
3. 开发者调试应用，需要反复调试授权功能
4. 应用内实现类似登出微博帐号的功能



## User

### show

根据用户ID获取用户信息

#### URL
https://api.weibo.com/2/users/show.json

#### 支持格式
JSON

#### HTTP请求方式
GET

#### 是否需要登录
是
关于登录授权，参见[如何登录授权](https://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6%E8%AF%B4%E6%98%8E)

#### 访问授权限制
访问级别：普通接口
频次限制：是
关于频次限制，参见[接口访问权限说明](https://open.weibo.com/wiki/Rate-limiting)

#### 请求参数
|     必选     | 类型及范围 | 说明   |
| :----------: | :--------: | :----- |
| access_token |    true    | string | 采用OAuth授权方式为必填参数，OAuth授权后获得。 |
|     uid      |   false    | int64  | 需要查询的用户ID。                             |
| screen_name  |   false    | string | 需要查询的用户昵称。                           |

#### 注意事项
参数uid与screen_name二者必选其一，且只能选其一；
接口升级后，对未授权本应用的uid，将无法获取其个人简介、认证原因、粉丝数、关注数、微博数及最近一条微博内容。

#### 调用样例及调试工具
[API测试工具](http://open.weibo.com/tools/console?uri=users/show&httpmethod=GET&key1=uid&value1=1904178193)

返回结果
```json
{
    "id": 1404376560,
    "screen_name": "zaku",
    "name": "zaku",
    "province": "11",
    "city": "5",
    "location": "北京 朝阳区",
    "description": "人生五十年，乃如梦如幻；有生斯有死，壮士复何憾。",
    "url": "http://blog.sina.com.cn/zaku",
    "profile_image_url": "http://tp1.sinaimg.cn/1404376560/50/0/1",
    "domain": "zaku",
    "gender": "m",
    "followers_count": 1204,
    "friends_count": 447,
    "statuses_count": 2908,
    "favourites_count": 0,
    "created_at": "Fri Aug 28 00:00:00 +0800 2009",
    "following": false,
    "allow_all_act_msg": false,
    "geo_enabled": true,
    "verified": false,
    "status": {
        "created_at": "Tue May 24 18:04:53 +0800 2011",
        "id": 11142488790,
        "text": "我的相机到了。",
        "source": "<a href="http://weibo.com" rel="nofollow">新浪微博</a>",
        "favorited": false,
        "truncated": false,
        "in_reply_to_status_id": "",
        "in_reply_to_user_id": "",
        "in_reply_to_screen_name": "",
        "geo": null,
        "mid": "5610221544300749636",
        "annotations": [],
        "reposts_count": 5,
        "comments_count": 8
    },
    "allow_all_comment": true,
    "avatar_large": "http://tp1.sinaimg.cn/1404376560/180/0/1",
    "verified_reason": "",
    "follow_me": false,
    "online_status": 0,
    "bi_followers_count": 215
}
```

关于错误返回值与错误代码，参见[错误代码说明](https://open.weibo.com/wiki/Error_code)

#### 返回字段说明

|     返回值字段     | 字段类型 | 字段说明                                                       |
| :----------------: | :------: | :------------------------------------------------------------- |
|         id         |  int64   | 用户UID                                                        |
|       idstr        |  string  | 字符串型的用户UID                                              |
|    screen_name     |  string  | 用户昵称                                                       |
|        name        |  string  | 友好显示名称                                                   |
|      province      |   int    | 用户所在省级ID                                                 |
|        city        |   int    | 用户所在城市ID                                                 |
|      location      |  string  | 用户所在地                                                     |
|    description     |  string  | 用户个人描述                                                   |
|        url         |  string  | 用户博客地址                                                   |
| profile_image_url  |  string  | 用户头像地址（中图），50×50像素                                |
|    profile_url     |  string  | 用户的微博统一URL地址                                          |
|       domain       |  string  | 用户的个性化域名                                               |
|       weihao       |  string  | 用户的微号                                                     |
|       gender       |  string  | 性别，m：男、f：女、n：未知                                    |
|  followers_count   |   int    | 粉丝数                                                         |
|   friends_count    |   int    | 关注数                                                         |
|   statuses_count   |   int    | 微博数                                                         |
|  favourites_count  |   int    | 收藏数                                                         |
|     created_at     |  string  | 用户创建（注册）时间                                           |
|     following      | boolean  | 暂未支持                                                       |
| allow_all_act_msg  | boolean  | 是否允许所有人给我发私信，true：是，false：否                  |
|    geo_enabled     | boolean  | 是否允许标识用户的地理位置，true：是，false：否                |
|      verified      | boolean  | 是否是微博认证用户，即加V用户，true：是，false：否             |
|   verified_type    |   int    | 暂未支持                                                       |
|       remark       |  string  | 用户备注信息，只有在查询用户关系时才返回此字段                 |
|       status       |  object  | 用户的最近一条微博信息字段 详细                                |
| allow_all_comment  | boolean  | 是否允许所有人对我的微博进行评论，true：是，false：否          |
|    avatar_large    |  string  | 用户头像地址（大图），180×180像素                              |
|     avatar_hd      |  string  | 用户头像地址（高清），高清头像原图                             |
|  verified_reason   |  string  | 认证原因                                                       |
|     follow_me      | boolean  | 该用户是否关注当前登录用户，true：是，false：否                |
|   online_status    |   int    | 用户的在线状态，0：不在线、1：在线                             |
| bi_followers_count |   int    | 用户的互粉数                                                   |
|        lang        |  string  | 用户当前的语言版本，zh-cn：简体中文，zh-tw：繁体中文，en：英语 |

#### 其他
无

#### 相关问题
无


到 [帮助中心](https://weibo.com/newlogin?tabtype=weibo&gid=102803&url=https%3A%2F%2Fweibo.com%2Ffaq%2Fq%2F2117) 查看更多问题或提问

### domain_show

通过个性化域名获取用户资料以及用户最新的一条微博

#### URL
https://api.weibo.com/2/users/domain_show.json

#### 支持格式
JSON

#### HTTP请求方式
GET

#### 是否需要登录
是
关于登录授权，参见 如何登录授权

#### 访问授权限制
访问级别：普通接口
频次限制：是
关于频次限制，参见 接口访问权限说明

#### 请求参数
|     必选     | 类型及范围 | 说明   |
| :----------: | :--------: | :----- |
| access_token |    true    | string | 采用OAuth授权方式为必填参数，OAuth授权后获得。 |
|    domain    |    true    | string | 需要查询的个性化域名。                         |

#### 注意事项
接口升级后，对未授权本应用的uid，将无法获取其个人简介、认证原因、粉丝数、关注数、微博数及最近一条微博内容。

#### 调用样例及调试工具
[API测试工具](http://open.weibo.com/tools/console?uri=users/domain_show&httpmethod=GET&key1=domain&value1=openapi)

####返回结果
```json
{
    "id": 1404376560,
    "screen_name": "zaku",
    "name": "zaku",
    "province": "11",
    "city": "5",
    "location": "北京 朝阳区",
    "description": "人生五十年，乃如梦如幻；有生斯有死，壮士复何憾。",
    "url": "http://blog.sina.com.cn/zaku",
    "profile_image_url": "http://tp1.sinaimg.cn/1404376560/50/0/1",
    "domain": "zaku",
    "gender": "m",
    "followers_count": 1204,
    "friends_count": 447,
    "statuses_count": 2908,
    "favourites_count": 0,
    "created_at": "Fri Aug 28 00:00:00 +0800 2009",
    "following": false,
    "allow_all_act_msg": false,
    "geo_enabled": true,
    "verified": false,
    "status": {
        "created_at": "Tue May 24 18:04:53 +0800 2011",
        "id": 11142488790,
        "text": "我的相机到了。",
        "source": "<a href="http://weibo.com" rel="nofollow">新浪微博</a>",
        "favorited": false,
        "truncated": false,
        "in_reply_to_status_id": "",
        "in_reply_to_user_id": "",
        "in_reply_to_screen_name": "",
        "geo": null,
        "mid": "5610221544300749636",
        "annotations": [],
        "reposts_count": 5,
        "comments_count": 8
    },
    "allow_all_comment": true,
    "avatar_large": "http://tp1.sinaimg.cn/1404376560/180/0/1",
    "verified_reason": "",
    "follow_me": false,
    "online_status": 0,
    "bi_followers_count": 215
}
```
关于错误返回值与错误代码，参见[错误代码说明](https://open.weibo.com/wiki/Error_code)

#### 返回字段说明
|     返回值字段     | 字段类型 | 字段说明                                                       |
| :----------------: | :------: | :------------------------------------------------------------- |
|         id         |  int64   | 用户UID                                                        |
|       idstr        |  string  | 字符串型的用户UID                                              |
|    screen_name     |  string  | 用户昵称                                                       |
|        name        |  string  | 友好显示名称                                                   |
|      province      |   int    | 用户所在省级ID                                                 |
|        city        |   int    | 用户所在城市ID                                                 |
|      location      |  string  | 用户所在地                                                     |
|    description     |  string  | 用户个人描述                                                   |
|        url         |  string  | 用户博客地址                                                   |
| profile_image_url  |  string  | 用户头像地址（中图），50×50像素                                |
|    profile_url     |  string  | 用户的微博统一URL地址                                          |
|       domain       |  string  | 用户的个性化域名                                               |
|       weihao       |  string  | 用户的微号                                                     |
|       gender       |  string  | 性别，m：男、f：女、n：未知                                    |
|  followers_count   |   int    | 粉丝数                                                         |
|   friends_count    |   int    | 关注数                                                         |
|   statuses_count   |   int    | 微博数                                                         |
|  favourites_count  |   int    | 收藏数                                                         |
|     created_at     |  string  | 用户创建（注册）时间                                           |
|     following      | boolean  | 暂未支持                                                       |
| allow_all_act_msg  | boolean  | 是否允许所有人给我发私信，true：是，false：否                  |
|    geo_enabled     | boolean  | 是否允许标识用户的地理位置，true：是，false：否                |
|      verified      | boolean  | 是否是微博认证用户，即加V用户，true：是，false：否             |
|   verified_type    |   int    | 暂未支持                                                       |
|       remark       |  string  | 用户备注信息，只有在查询用户关系时才返回此字段                 |
|       status       |  object  | 用户的最近一条微博信息字段 详细                                |
| allow_all_comment  | boolean  | 是否允许所有人对我的微博进行评论，true：是，false：否          |
|    avatar_large    |  string  | 用户头像地址（大图），180×180像素                              |
|     avatar_hd      |  string  | 用户头像地址（高清），高清头像原图                             |
|  verified_reason   |  string  | 认证原因                                                       |
|     follow_me      | boolean  | 该用户是否关注当前登录用户，true：是，false：否                |
|   online_status    |   int    | 用户的在线状态，0：不在线、1：在线                             |
| bi_followers_count |   int    | 用户的互粉数                                                   |
|        lang        |  string  | 用户当前的语言版本，zh-cn：简体中文，zh-tw：繁体中文，en：英语 |
#### 其他
无

#### 相关问题
无


到 [帮助中心](https://weibo.com/newlogin?tabtype=weibo&gid=102803&url=https%3A%2F%2Fweibo.com%2Ffaq%2Fq%2F2117) 查看更多问题或提问
