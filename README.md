# BilibiliFollowPage
基于handsome主题的B站追番列表独立页面作品<br>
<br>
效果展示：[https://qwq.best/bf.html](https://qwq.best/bf.html "https://qwq.best/bf.html")<br>
使用方法：[https://qwq.best/dev/84.html](https://qwq.best/dev/84.html "https://qwq.best/dev/84.html")<br>

## 介绍
在你的博客里添加一个“追番列表”独立页面，可以输出你在B站的追番列表。<br>
采用缓存机制，保证速度与性能。<br>
缓存图片支持自定义CDN的URL链接，提升加载速度。<br>
缓存图片支持使用Lazyload载入图片，提升加载速度。<br>
支持自定义隐藏番剧，将不想展示出来的动漫隐藏起来。<br>
配置简单方便，教程通俗易懂。<br>

## 说在前面
仅支持Typecho的handsome主题。<br>

## 版本记录
master分支为最新稳定版本，dev分支为开发进度。所有历史版本请见仓库Tag<br>
V2.5.0 - 2019.10.26<br>
V2.0.0 - 2019.10.1<br>
V1.0.0 - 2019.7.13<br>
<br>
V2.5.0更新特性：<br>
1.修复主题在透明模式下番剧标题与描述看不清的问题<br>
2.新增Lazyload功能（可选择性开启）<br>
3.新增自定义图片CDN功能（可选择性开启）<br>
4.修复主题在非盒子模型（宽屏模式）下排版显示问题<br>
5.修复细节BUG，减少报错几率<br>

## 开始使用
项目地址：[https://github.com/AyagawaSeirin/BilibiliFollowPage](https://github.com/AyagawaSeirin/BilibiliFollowPage "https://github.com/AyagawaSeirin/BilibiliFollowPage")<br>
将文件下载至handsome主题目录<br>
> 相对于网站根目录的路径：/usr/themes/handsome/


首先请先确保目录（相对于主题目录）/assets/cache/可写！<br>
新建独立页面，自定义模板选择“B站追番列表”<br>
<br>
利用独立页面的自定义字段设置参数：<br>
BilibiliUid：B站UID，必填参数。<br>
Amout：输出数量，最大100，选填参数，默认值为100。<br>
CacheTime：缓存文件更新时间，选填参数，默认值为86400，单位为秒。<br>
HideMedia：不展示的番剧，选填参数，由media_id组成，用英文逗号隔开。<br>
lazyload：是否开启Lazyload，选填参数，默认不开启，若开启则输入值为1。<br>
cdnurl：选填参数，CDN服务器的URL链接，请将缓存目录的所有图片文件上传至您的CDN服务器，然后设置URL链接，必须是完整（带http的）URL地址，到图片目录。比如：https://cdn.example.com/blog/zwzmoe/handsome/assets/cache/BilibiliFollow<br>

发布页面后即可查看效果。<br>
**原创作品，请尊重原作版权~**<br>
>更新缓存文件方法：删除文件/assets/cache/BilibiliFollow/BilibiliFollow.json（相对于主题目录）

## 技术细节
### 数据来源
数据当然是从B站接口获取的了。<br>
接口限制了来源域名，所以使用浏览器发送请求后会被B站服务器拒接，强行修改`HEADER`的相关参数会被浏览器拦截。<br>
所以无法使用浏览器发送请求，故使用服务器（PHP的cURL方法）来发送请求。<br>
当然假如访问量过大，服务器一直向B站服务器请求，1是会减慢速度，2是高频率的单IP请求可能会被B站服务器发现。所以我采用了缓存策略，大大减少向B站服务器发送请求的次数。<br>
### 缓存策略
缓存文件路径（相对于主题目录）：/assets/cache/BilibiliFollow/BilibiliFollow.json<br>
会将B站接口返回的数据，加上B站UID、数量、缓存时间参数加入数组，再通过Json格式写入缓存文件。<br>
删除缓存文件即可更新缓存，修改自定义字段里的参数内容也会更新缓存。<br>
当然这里的更新缓存不是立刻更新，而是下次访问时更新缓存。<br>
缓存策略大大减少向B站服务器发送请求的次数，同时加快了速度。<br>
番剧图片也会缓存至服务器内，以解决B站图片防盗链问题。第一次更新缓存时会缓存图片所以耗时较长，建议此次访问由站长完成。后面除非是有新的番剧被订阅，否则更新时不会再缓存图片。<br>
图片加载支持自定义CDN地址以及Lazyload功能，提升加载速度。<br>
### 前端美化
由于我不擅长前端设计，做出来的这个页面也就勉强能看，你可以自己制作前端效果，最好是可以分享出来~<br>