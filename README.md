# BilibiliFollowPage
基于handsome主题的B站追番列表独立页面作品<br>
<br>
效果展示：[https://zwz.moe/bf.html](https://zwz.moe/bf.html "https://zwz.moe/bf.html")<br>
使用方法：[https://zwz.moe/dev/84.html](https://zwz.moe/dev/84.html "https://zwz.moe/dev/84.html")<br>

## 说在前面
仅支持Typecho的handsome主题。我没专门学过什么Typecho插件开发和主题开发之类的，这个只是参考handsome主题的豆瓣书单和github项目独立页面源码制成的。
先说明使用方法，再讨论技术细节。<br>
## 开始使用
项目地址：[https://github.com/AyagawaSeirin/BilibiliFollowPage](https://github.com/AyagawaSeirin/BilibiliFollowPage "https://github.com/AyagawaSeirin/BilibiliFollowPage")<br>
将文件下载至handsome主题目录<br>
> 相对于网站根目录的路径：/usr/themes/handsome/


首先请先确保目录（相对于主题目录）/assets/cache/可写！<br>
新建独立页面，自定义模板选择“B站追番列表”<br>
![](https://cdn.pplin.cn/blog/zwzmoe/uploads/2019/07/13/096371324091815.png)

设置自定义参数<br>
BilibiliUid：B站UID，必填参数。<br>
Amout：输出数量，最大100，选填参数，默认值为100。<br>
CahceTime：缓存文件更新时间，选填参数，默认值为86400，单位为秒。<br>
![](https://cdn.pplin.cn/blog/zwzmoe/uploads/2019/07/13/103233264283136.png)

发布页面后即可查看效果。<br>
原创作品，请尊重原作版权~<br>
>更新缓存文件方法：删除文件/assets/cache/BilibiliFollow.json（相对于主题目录）

## 技术细节
### 数据来源
数据当然是从B站接口获取的了。<br>
接口限制了来源域名，所以使用浏览器发送请求后会被B站服务器拒接，强行修改`HEADER`的相关参数会被浏览器拦截。<br>
所以无法使用浏览器发送请求，故使用服务器（PHP的cURL方法）来发送请求。<br>
当然假如访问量过大，服务器一直向B站服务器请求，1是会减慢速度，2是高频率的单IP请求可能会被B站服务器发现。所以我采用了缓存策略，大大减少向B站服务器发送请求的次数。<br>
### 缓存策略
缓存文件路径（相对于主题目录）：/assets/cache/BilibiliFollow.json<br>
会将B站接口返回的数据，加上B站UID、数量、缓存时间参数加入数组，再通过Json格式写入缓存文件。<br>
删除缓存文件即可更新缓存，修改自定义字段里的参数内容也会更新缓存。<br>
当然这里的更新缓存不是立刻更新，而是下次访问时更新缓存。<br>
缓存策略大大减少向B站服务器发送请求的次数，同时加快了速度。<br>
### 前端美化
由于我不擅长前端设计，做出来的这个页面也就勉强能看，你可以自己制作前端效果，最好是可以分享出来~<br>