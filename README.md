`composer require cy01/wwwcity-service`
### 要求
+ php7.2 + 
+ laravel5.5 + 

### 发布配置

`php artisan vendor:publish --provider="Cy\WWWCityService\WWWCityServiceProvider"`

### 在`.env`或`config`中设置密钥

``` json
# 微服务配置
APP_ACCESS_KEY=25822262
APP_ACCESS_SECRET=0b4745d88378eb7aeac039deb21368c4
AREA_HOST=http://area.test.wwwcity.net
```



